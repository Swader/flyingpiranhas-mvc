<?php

namespace flyingpiranhas\mvc\router\abstracts;

use flyingpiranhas\common\http\interfaces\RequestInterface;
use flyingpiranhas\common\http\interfaces\ResponseInterface;
use flyingpiranhas\mvc\router\exceptions\RouterException;
use flyingpiranhas\mvc\router\Redirect;
use flyingpiranhas\mvc\router\Route;

/**
 * The RouterAbstract is the base class for both the AppRouter and the ModuleRouter.
 * It holds references to the Request and Response objects.
 *
 * @category       router
 * @package        flyingpiranhas.mvc
 * @license        Apache-2.0
 * @version        0.01
 * @since          2012-09-07
 * @author         Ivan Pintar
 */
abstract class RouterAbstract
{

    /** @var array */
    protected $aRoutes = array();

    /** @var string */
    protected $sModule = '';

    /** @var string */
    protected $sController = '';

    /** @var string */
    protected $sAction = '';

    /** @var array */
    protected $aMcaDefaults = array('module' => 'home', 'controller' => 'Index', 'action' => 'index');

    /**
     * @var RequestInterface
     */
    protected $oRequest;

    /**
     * @var ResponseInterface
     */
    protected $oResponse;

    /**
     * @param RequestInterface  $oRequest
     * @param ResponseInterface $oResponse
     */
    public final function __construct(RequestInterface $oRequest, ResponseInterface $oResponse)
    {
        $this->oRequest = $oRequest;
        $this->oResponse = $oResponse;
    }

    /**
     * @return array
     */
    public function getRoutes()
    {
        return array_merge($this->aRoutes);
    }

    /**
     * @param Route $oRoute
     *
     * @return RouterAbstract
     * @throws RouterException
     */
    public function addRoute(Route $oRoute)
    {
        if (isset($this->aRoutes[$oRoute->getFrom()])) {
            throw new RouterException('Route for: ' . $oRoute->getFrom() . ' alredy set');
        }

        $this->aRoutes[$oRoute->getFrom()] = $oRoute;

        return $this;
    }

    /**
     * Adds multiple routes from an array or a SimpleXMLElement build from the Routes.xml
     *
     * @param array|string $mRoutes
     *
     * @return RouterAbstract
     * @throws RouterException
     */
    public function addRoutes($mRoutes)
    {
        if (is_array($mRoutes)) {
            foreach ($mRoutes as $sName => $oRoute) {
                $this->addRoute($oRoute);
            }
        } else if (is_string($mRoutes)) {
            $this->buildRoutesFromIni($mRoutes);
        } else {
            throw new RouterException('The given routes are invalid');
        }

        return $this;
    }

    /**
     * Sets the default module, controller and action
     *
     * @param array $aDefaults
     *
     * @return RouterAbstract
     */
    public function setDefaults(array $aDefaults)
    {
        $this->aMcaDefaults = $aDefaults;
        return $this;
    }

    /**
     * Parses the request uri and routes to the appropriate module, controller and action
     *
     * @return RouterAbstract
     * @throws RouterException
     */
    public function parseRequest()
    {
        $aUrlParts = parse_url($this->oRequest->getServer()->REQUEST_URI);
        $sPath = trim($aUrlParts['path'], '/');
        $sQueryString = (isset($aUrlParts['query'])) ? $aUrlParts['query'] : '';

        // redirect if needed
        $this->redirect($sPath, $sQueryString);

        // check routes
        if (!$this->reroute($sPath)) {
            if (!$this->route($sPath)) {
                throw new RouterException('Could not route the requested url', 404);
            }
        }
        return $this;
    }

    /**
     * Redirects a given uri to a uri defined in $this->aRoutes
     *
     * @param string $sUrl
     * @param string $sQueryString
     *
     * @return boolean
     */
    protected function redirect($sUrl, $sQueryString)
    {
        /** @var $oRedirect Redirect */
        foreach ($this->aRoutes as $oRedirect) {
            if ($oRedirect instanceof Redirect && $oRedirect->urlMatches($sUrl)) {
                $sNewUrl = $oRedirect->getNewUrl($sUrl) . (($sQueryString) ? '?' . $sQueryString : '');
                $this->oResponse->redirect($sNewUrl, $oRedirect->getHeader());
            }
        }

        return false;
    }

    /**
     * Reroutes a url according to the route settings
     * Returns false if there is no routing set up for the given url
     *
     * @param string $sUrl
     *
     * @return bool
     */
    protected function reroute($sUrl)
    {
        $sUrl = trim($sUrl, '/');

        /** @var $oRoute Route */
        foreach ($this->aRoutes as $oRoute) {
            if (!($oRoute instanceof Redirect) && $oRoute->urlMatches($sUrl)) {
                return $this->route($oRoute->getNewUrl($sUrl));
            }
        }

        return $this->route($sUrl);
    }

    /**
     * The AppRouter and ModuleRouter should override this function
     * to implement application and module-level routing
     *
     * @param string $sUrl
     */
    protected abstract function route($sUrl);

    /**
     * Builds the routes array from the SimpleXMLElement built from Routes.xml
     *
     * @param string $sIniPath
     *
     * @return RouterAbstract
     * @throws RouterException
     */
    protected function buildRoutesFromIni($sIniPath)
    {
        $aIniArray = parse_ini_file($sIniPath, true);
        if (!$aIniArray) {
            throw new RouterException("Invalid ini file provided");
        }

        $aRoutes = array();
        foreach ($aIniArray as $sRouteType => &$mIniRow) {
            foreach ($mIniRow as $sRowKey => &$sRowValue) {
                if (!in_array($sRouteType, array('routes', 'redirects'))) {
                    throw new RouterException('Unsupported route type: ' . $sRouteType . '. Please use "routes" or "redirects"');
                }

                $this->parseIniRow($sRowKey, $sRowValue, $aRoutes[$sRouteType]);
            }
        }

        foreach ($aRoutes as $sType => $aRoute) {
            foreach ($aRoute as $sName => $aValues) {
                if ($sType == 'redirects') {
                    $oRoute = new Redirect($sName, $aValues);
                } else {
                    $oRoute = new Route($sName, $aValues);
                }
                $this->addRoute($oRoute);
            }

        }

        return $this;
    }

    /**
     * @param string $sKey
     * @param string $sValue
     * @param array  $aRoutes
     *
     * @return RouterAbstract
     */
    private function parseIniRow($sKey, $sValue, &$aRoutes)
    {
        $aKey = explode('.', $sKey);
        if (count($aKey) <= 1) {
            $aRoutes[$sKey] = $sValue;
            return $this;
        }

        $sSettingKey = $aKey[0];
        if (!isset($aRoutes[$sSettingKey])) {
            $aRoutes[$sSettingKey] = array();
        }
        unset($aKey[0]);

        $sKey = implode('.', $aKey);
        $this->parseIniRow($sKey, $sValue, $aRoutes[$sSettingKey]);

        return $this;
    }

}