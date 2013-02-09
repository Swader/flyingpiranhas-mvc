<?php

namespace flyingpiranhas\mvc;

use flyingpiranhas\mvc\controller\interfaces\ControllerInterface;
use flyingpiranhas\mvc\interfaces\ModuleInterface;
use flyingpiranhas\mvc\router\interfaces\ModuleRouterInterface;
use flyingpiranhas\mvc\views\interfaces\ViewInterface;
use flyingpiranhas\common\http\Params;
use flyingpiranhas\common\dependencyInjection\interfaces\DIContainerInterface;
use flyingpiranhas\mvc\interfaces\AppInterface;

/**
 * @category       mvc
 * @package        flyingpiranhas.mvc
 * @license        Apache-2.0
 * @version        0.01
 * @since          2012-09-07
 * @author         Ivan Pintar
 */
class Module implements ModuleInterface
{

    /** @var string */
    protected $sModuleName = '';

    /** @var string */
    protected $sModuleNamespace = '';

    /** @var string */
    protected $sModuleDir = '';

    /** @var string */
    protected $sControllerNamespace = '';

    /** @var string */
    protected $sDefaultController = '';

    /** @var string */
    protected $sDefaultAction = '';

    /** @var string */
    protected $sViewsDir = '';

    /** @var string */
    protected $sLayoutsDir = '';

    /** @var string */
    protected $sViewFragmentsDir = '';

    /** @var AppInterface */
    protected $oApp;

    /** @var DIContainerInterface */
    protected $oDIContainer;

    /** @var ModuleRouterInterface */
    protected $oRouter;

    /**
     * @return ModuleRouterInterface
     */
    public final function getRouter()
    {
        return $this->oRouter;
    }

    /**
     * @return string
     */
    public function getModuleDir()
    {
        return $this->sModuleDir;
    }

    /**
     * @return string
     */
    public function getLayoutsDir()
    {
        return $this->sLayoutsDir;
    }

    /**
     * @return string
     */
    public function getViewsDir()
    {
        return $this->sViewsDir;
    }

    /**
     * @return string
     */
    public function getViewFragmentsDir()
    {
        return $this->sViewFragmentsDir;
    }

    /**
     * @return AppInterface
     */
    public final function getApp()
    {
        return $this->oApp;
    }

    /**
     * @param string $sModuleName
     *
     * @return Module
     */
    public final function setModuleName($sModuleName)
    {
        $this->sModuleName = $sModuleName;
        return $this;
    }

    /**
     * @param string $sModuleNamespace
     *
     * @return Module
     */
    public final function setModuleNamespace($sModuleNamespace)
    {
        $this->sModuleNamespace = $sModuleNamespace;
        return $this;
    }

    /**
     * @param string $sModuleDir
     *
     * @return Module
     */
    public final function setModuleDir($sModuleDir)
    {
        $this->sModuleDir = $sModuleDir;
        return $this;
    }

    /**
     * @param array $aModuleSettings
     *
     * @return App
     */
    public function setModuleSettings(array $aModuleSettings)
    {
        if (!$this->sControllerNamespace) $this->sControllerNamespace = $aModuleSettings['controllerNamespace'];
        if (!$this->sDefaultController) $this->sDefaultController = $aModuleSettings['defaultController'];
        if (!$this->sDefaultAction) $this->sDefaultAction = $aModuleSettings['defaultAction'];
        if (!$this->sViewsDir) $this->sViewsDir = $aModuleSettings['viewsDir'];
        if (!$this->sLayoutsDir) $this->sLayoutsDir = $aModuleSettings['layoutsDir'];
        if (!$this->sViewFragmentsDir) $this->sViewFragmentsDir = $aModuleSettings['viewFragmentsDir'];

        return $this;
    }

    /**
     * @dependency
     *
     * @param DIContainerInterface $oDIContainer
     *
     * @return Module
     */
    public final function setDIContainer(DIContainerInterface $oDIContainer)
    {
        $this->oDIContainer = $oDIContainer;
        return $this;
    }

    /**
     * @dependency
     *
     * @param ModuleRouterInterface $oRouter
     *
     * @return Module
     */
    public final function setRouter(ModuleRouterInterface $oRouter)
    {
        $this->oRouter = $oRouter;
        return $this;
    }

    /**
     * @dependency
     *
     * @param AppInterface $oApp
     *
     * @return Module
     */
    public final function setApp(AppInterface $oApp)
    {
        $this->oApp = $oApp;
        return $this;
    }

    /**
     * Creates a controller object for the given controller name.
     * Sets the required info and returns it.
     *
     * @param string $sControllerName
     *
     * @return ControllerInterface
     */
    private final function findController($sControllerName)
    {
        $sControllerClass = $this->sModuleNamespace . '\\' . $this->sControllerNamespace . '\\' . ucfirst($sControllerName);
        $oController = $this->oDIContainer->resolve($sControllerClass, array('oModule' => $this));
        return $oController;
    }

    /**
     * @param string       $sAction
     * @param string|null  $sControllerName
     * @param Params|null  $aParams
     *
     * @return ViewInterface
     */
    public final function findView($sAction, $sControllerName, Params $aParams = null)
    {
        $oController = $this->findController($sControllerName);
        return $oController->runAction($sAction, $aParams);
    }

    /**
     * Parses the request to find out the controller and action name.
     * Calls the processActionView() on the controller to get the ViewModel.
     * Calls the preRender() and render() on the ViewModel.
     */
    public final function work()
    {
        // parse the request uri
        $this->oRouter->parseRequest();

        // dispatch
        $oView = $this->findView($this->oRouter->getAction(), $this->oRouter->getController());

        // render
        $oResponse = $this->oDIContainer->resolve('flyingpiranhas\\common\\http\\interfaces\\ResponseInterface');
        $oResponse->setContent($oView);
        $oResponse->send();
    }

    /**
     * Override this method to execute operations right after the module mind is created.
     */
    public function preDispatch()
    {

    }


}