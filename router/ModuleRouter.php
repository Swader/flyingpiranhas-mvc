<?php

namespace flyingpiranhas\mvc\router;

use flyingpiranhas\mvc\router\abstracts\RouterAbstract;
use flyingpiranhas\mvc\router\interfaces\ModuleRouterInterface;
use flyingpiranhas\common\http\Request;

/**
 * The ModuleRouter object parses the request
 * and sets the corresponding controller and action.
 * It holds references to the Request and Response objects
 *
 * Additionally, it parses the parameters into the Params object that the Request holds.
 *
 * The module, controller, action and params are mapped in the url like this:
 *      http://domain/module/controller/action/param/value/param2/value2
 *
 * This can be overriden by defining routes in the Routes.xml
 *
 * @category       router
 * @package        flyingpiranhas.mvc
 * @license        Apache-2.0
 * @version        0.01
 * @since          2012-09-07
 * @author         Ivan Pintar
 */
class ModuleRouter extends RouterAbstract implements ModuleRouterInterface
{

    /**
     * @return string
     */
    public function getController()
    {
        return $this->sController;
    }

    /**
     * @return string
     */
    public function getAction()
    {
        return $this->sAction;
    }

    /**
     * Set the default controller and action.
     *
     * @param array $aDefaults
     *
     * @return ModuleRouter
     */
    public function setDefaults(array $aDefaults)
    {
        parent::setDefaults($aDefaults);
        return $this;
    }

    /**
     * Routes the url to the mca defined by the url itself
     * Returns false on failure
     *
     * @param string $sUrl
     *
     * @return bool
     */
    protected function route($sUrl)
    {
        $sUrl = trim($sUrl, '/');

        $bReturn = true;
        $aUrlParts = (!empty($sUrl)) ? explode('/', $sUrl) : array();

        $sController = $this->aMcaDefaults['controller'];
        $sAction = $this->aMcaDefaults['action'];

        switch (count($aUrlParts)) {
            case 0:
            case 1:
                unset($aUrlParts[0]);
                break;
            case 2:
                $sController = $aUrlParts[1];
                unset($aUrlParts[0]);
                unset($aUrlParts[1]);
                break;

            default:
                $sController = $aUrlParts[1];
                $sAction = $aUrlParts[2];
                unset($aUrlParts[0]);
                unset($aUrlParts[1]);
                unset($aUrlParts[2]);
                break;
        }

        $this->sAction = trim($sAction);
        $this->sController = trim(ucfirst($sController));

        $this->buildGet(array_values($aUrlParts));

        return $bReturn;
    }

    /**
     * Builds the $_GET params from the url string
     * where everything after the module/controller/action is defined as /paramName/paramValue
     * unless otherwise defined by a custom route
     *
     * @param array $aUrlParts
     *
     * @return ModuleRouter
     */
    private function buildGet(array $aUrlParts = array())
    {
        $aParams = $this->oRequest->getParams()[Request::PARAM_TYPES_GET];

        for ($iIndex = 0; $iIndex < count($aUrlParts); $iIndex += 2) {
            if (isset($aUrlParts[$iIndex])) {
                $sKey = $aUrlParts[$iIndex];
                $sValue = (isset($aUrlParts[$iIndex + 1])) ? $aUrlParts[$iIndex + 1] : '';
                if (!isset($aParams->$sKey)) {
                    $aParams->$sKey = $sValue;
                }
            }
        }

        return $this;
    }

}