<?php

namespace flyingpiranhas\mvc\controller\abstracts;

use flyingpiranhas\common\http\interfaces\RequestInterface;
use flyingpiranhas\mvc\interfaces\ModuleInterface;
use BadMethodCallException;
use flyingpiranhas\common\http\Request;
use flyingpiranhas\common\http\interfaces\ResponseInterface;
use flyingpiranhas\common\session\interfaces\SessionInterface;
use flyingpiranhas\mvc\controller\exceptions\ControllerException;
use flyingpiranhas\mvc\controller\interfaces\ControllerInterface;
use flyingpiranhas\mvc\views\interfaces\ViewInterface;
use flyingpiranhas\mvc\views\head\interfaces\HeadInterface;

/**
 * The ControllerAbstract object is a base for all controllers in the application.
 * It holds references to the ModuleMind, Session, Request and Response objects.
 *
 * @category       controller
 * @package        flyingpiranhas.mvc
 * @license        Apache-2.0
 * @version        0.01
 * @since          2012-09-07
 * @author         Ivan Pintar
 */
abstract class ControllerAbstract implements ControllerInterface
{

    /** @var string */
    protected $sName = '';

    /** @var RequestInterface */
    private $oRequest;

    /** @var SessionInterface */
    private $oSession;

    /** @var ResponseInterface */
    private $oResponse;

    /** @var HeadInterface */
    private $oHead;

    /** @var string */
    private $sViewsDir;

    /** @var ModuleInterface */
    private $oModule;

    /**
     * @return SessionInterface
     */
    protected function getSession()
    {
        return $this->oSession;
    }

    /**
     * @return RequestInterface
     */
    protected function getRequest()
    {
        return $this->oRequest;
    }

    /**
     * @return ResponseInterface
     */
    protected function getResponse()
    {
        return $this->oResponse;
    }

    /**
     * @return HeadInterface
     */
    protected function getHead()
    {
        return $this->oHead;
    }

    /**
     * @return string
     */
    public function getViewsDir()
    {
        $aClassName = explode('\\', get_class($this));
        return ($this->sViewsDir) ? $this->sViewsDir : array_pop($aClassName);
    }

    /**
     * @return ModuleInterface
     */
    protected function getModule()
    {
        return $this->oModule;
    }

    /**
     * @dependency
     *
     * @param RequestInterface $oRequest
     *
     * @return ControllerAbstract
     */
    public function setRequest(RequestInterface $oRequest)
    {
        $this->oRequest = $oRequest;
        return $this;
    }

    /**
     * @dependency
     *
     * @param ResponseInterface $oResponse
     *
     * @return ControllerAbstract
     */
    public function setResponse(ResponseInterface $oResponse)
    {
        $this->oResponse = $oResponse;
        return $this;
    }

    /**
     * @dependency
     *
     * @param SessionInterface $oSession
     *
     * @return ControllerAbstract
     */
    public function setSession(SessionInterface $oSession)
    {
        $this->oSession = $oSession;
        return $this;
    }

    /**
     * @dependency
     *
     * @param HeadInterface $oHead
     *
     * @return ControllerAbstract
     */
    public function setHead(HeadInterface $oHead)
    {
        $this->oHead = $oHead;
        return $this;
    }

    /**
     * @dependency
     *
     * @param ModuleInterface $oModule
     *
     * @return ControllerAbstract
     */
    public function setModule(ModuleInterface $oModule)
    {
        $this->oModule = $oModule;
        return $this;
    }

    /**
     * Override this method to execute operations in the controller preDispatch,
     * right before the action is triggered
     */
    public function preDispatch()
    {

    }

    /**
     * Override this method to execute operations in the controller postDispatch,
     * right after the action is executed
     */
    public function postDispatch()
    {

    }

    /**
     * Triggers the preDispatch, the action specified by the request uri or route
     * and the postDispatch, before sending the returned ViewModel to the buildViewModel method.
     *
     * @param string $sActionName the action method name
     * @param array  $aGetParams  the action method params. If not provided, the GET params of the Request will be used
     *
     * @return ViewInterface
     * @throws ControllerException
     * @throws BadMethodCallException
     */
    public function runAction($sActionName, array $aGetParams = null)
    {
        $this->preDispatch();

        $sAction = $sActionName . 'Action';

        if ($aGetParams === null) {
            $aGetParams = $this->getRequest()->getParams()[Request::PARAM_TYPES_GET];
        }

        if (!method_exists($this, $sAction)) {
            throw new BadMethodCallException('Action ' . $sActionName . ' does not exist in ' . get_class($this));
        }

        $rFunctionReference = new \ReflectionMethod($this, $sAction);
        $aFunctionParams = $rFunctionReference->getParameters();

        $aParams = array();

        foreach ($aFunctionParams as $oActionParam) {
            $mParam = null;
            if (!$oActionParam->isOptional() && !isset($aGetParams[$oActionParam->name])) {
                throw new ControllerException('Parameter ' . $oActionParam->name . ' is required for this page', 404);
            } else if (isset($aGetParams[$oActionParam->name])) {
                $mParam = $aGetParams[$oActionParam->name];
            } else if ($oActionParam->isOptional()) {
                $mParam = $oActionParam->getDefaultValue();
            }
            $aParams[] = $mParam;
        }

        $oView = call_user_func_array(array($this, $sAction), $aParams);

        if ($oView instanceof ViewInterface) {
            $aLayoutsIncludePath = array(
                $this->oModule->getModuleDir() . '/' . $this->oModule->getLayoutsDir(),
                $this->oModule->getApp()->getProjectDir() . '/' . $this->oModule->getApp()->getLayoutsDir(),
            );
            $aViewsIncludePath = array(
                $this->oModule->getModuleDir() . '/' . $this->oModule->getViewsDir() . '/' . lcfirst($this->getViewsDir()),
                $this->oModule->getModuleDir() . '/' . $this->oModule->getViewsDir(),
                $this->oModule->getApp()->getProjectDir() . '/' . $this->oModule->getApp()->getViewsDir(),
            );
            $aFragmentsIncludePath = array(
                $this->oModule->getModuleDir() . '/' . $this->oModule->getViewFragmentsDir(),
                $this->oModule->getApp()->getProjectDir() . '/' . $this->oModule->getApp()->getViewFragmentsDir(),
            );

            if (!$oView->getHead()) $oView->setHead($this->oHead);
            if (!$oView->getLayoutsIncludePath()) $oView->setLayoutsIncludePath($aLayoutsIncludePath);
            if (!$oView->getViewsIncludePath()) $oView->setViewsIncludePath($aViewsIncludePath);
            if (!$oView->getFragmentsIncludePath()) $oView->setFragmentsIncludePath($aFragmentsIncludePath);
            if ($oView->getView() === null) $oView->setView($sActionName);
        }

        $this->postDispatch();
        return $oView;
    }

    /**
     * @return string
     */
    protected function getRequestMethod()
    {
        return $this->getRequest()->getServer()['REQUEST_METHOD'];
    }

    /**
     * @return bool
     */
    protected function requestIsGet()
    {
        return ($this->getRequestMethod() == Request::REQUEST_METHOD_GET);
    }

    /**
     * @return bool
     */
    protected function requestIsPost()
    {
        return ($this->getRequestMethod() == Request::REQUEST_METHOD_POST);
    }

    /**
     * @return bool
     */
    protected function requestIsPut()
    {
        return ($this->getRequestMethod() == Request::REQUEST_METHOD_PUT);
    }

    /**
     * @return bool
     */
    protected function requestIsDelete()
    {
        return ($this->getRequestMethod() == Request::REQUEST_METHOD_DELETE);
    }

    /**
     * Returns a single parameter. If no parameter is present, the default value will be returned.
     * If a ctype is given, it will check the value against that type, and use the default if there is no match.
     * If a method is given, it will limit the search to the params with that method,
     * otherwise the param is looked for in the GET, POST and FILES params, in that order.
     *
     * @param string      $sName
     * @param mixed       $mDefault
     * @param string|null $sCtype
     * @param string      $sMethod
     *
     * @return mixed
     * @throws ControllerException
     */
    protected function getParam($sName, $mDefault = null, $sCtype = null, $sMethod = null)
    {
        $mValue = $mDefault;
        if ($sMethod) {
            $aParams = $this->getRequest()->getParams($sMethod);
            $mValue = (isset($aParams->$sName)) ? $aParams->$sName : $mValue;
        } else {
            $aParams = $this->getRequest()->getParams();
            foreach ($aParams as $aSubParams) {
                if (isset($aSubParams->$sName)) {
                    $mValue = $aSubParams->$sName;
                    break;
                }
            }
        }

        if ($sCtype) {
            $sFunction = 'ctype_' . $sCtype;
            if (function_exists($sFunction)) {
                $mValue = ($sFunction($mValue)) ? $mValue : $mDefault;
            } else {
                throw new ControllerException('Ctype function ' . $sFunction . '() not recognized.');
            }
        }

        return $mValue;
    }

    /**
     * Returns a single GET parameter. If no parameter is present, the default value will be returned.
     * If a ctype is given, it will check the value against that type, and use the default if there is no match.
     *
     * @param string      $sName
     * @param mixed       $mDefault
     * @param string|null $sCtype
     *
     * @return mixed
     */
    protected function getGetParam($sName, $mDefault = null, $sCtype = null)
    {
        return $this->getParam($sName, $mDefault, $sCtype, Request::PARAM_TYPES_GET);
    }

    /**
     * Returns a single POST parameter. If no parameter is present, the default value will be returned.
     * If a ctype is given, it will check the value against that type, and use the default if there is no match.
     *
     * @param string      $sName
     * @param mixed       $mDefault
     * @param string|null $sCtype
     *
     * @return mixed
     */
    protected function getPostParam($sName, $mDefault = null, $sCtype = null)
    {
        return $this->getParam($sName, $mDefault, $sCtype, Request::PARAM_TYPES_POST);
    }

    /**
     * Returns a single FILES parameter. If no parameter is present, the default value will be returned.
     * If a ctype is given, it will check the value against that type, and use the default if there is no match.
     *
     * @param string      $sName
     * @param mixed       $mDefault
     * @param string|null $sCtype
     *
     * @return mixed
     */
    protected function getFileParam($sName, $mDefault = null, $sCtype = null)
    {
        return $this->getParam($sName, $mDefault, $sCtype, Request::PARAM_TYPES_FILES);
    }

}
