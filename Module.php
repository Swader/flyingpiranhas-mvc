<?php

namespace flyingpiranhas\mvc;

use flyingpiranhas\common\cache\interfaces\CacheInterface;
use flyingpiranhas\common\config\Config;
use flyingpiranhas\common\config\interfaces\ConfigRootInterface;
use flyingpiranhas\mvc\controller\interfaces\ControllerInterface;
use flyingpiranhas\mvc\interfaces\ModuleInterface;
use flyingpiranhas\mvc\router\interfaces\ModuleRouterInterface;
use flyingpiranhas\mvc\views\interfaces\ViewInterface;
use flyingpiranhas\common\http\Params;

/**
 * @category       mvc
 * @package        flyingpiranhas.mvc
 * @license        BSD License
 * @version        0.01
 * @since          2012-09-07
 * @author         Ivan Pintar
 */
class Module implements ModuleInterface
{

    /** @var string */
    protected $sRoutesIniPath = 'config/Routes.ini';

    /** @var string */
    protected $sModuleName = '';

    /** @var string */
    protected $sModuleNamespace = '';

    /** @var string */
    protected $sModuleDir = '';

    /** @var string */
    protected $sControllerNamespace = 'controllers';

    /** @var string */
    protected $sDefaultController = 'Index';

    /** @var string */
    protected $sDefaultAction = 'index';

    /** @var string */
    protected $sViewsDir = 'views/scripts';

    /** @var string */
    protected $sLayoutsDir = 'views/layouts';

    /** @var string */
    protected $sViewFragmentsDir = 'views/fragments';

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

    /** @var App */
    protected $oApp;

    /** @var CacheInterface */
    protected $oCache;

    /** @var ConfigRootInterface */
    protected $oConfig;

    /** @var ModuleRouterInterface */
    protected $oRouter;

    /**
     * @return App
     */
    public final function getApp()
    {
        return $this->oApp;
    }

    /**
     * @return CacheInterface
     */
    public final function getCache()
    {
        return $this->oCache;
    }

    /**
     * @return Config
     */
    public final function getConfig()
    {
        return $this->oConfig;
    }

    /**
     * @return ModuleRouterInterface
     */
    public final function getRouter()
    {
        return $this->oRouter;
    }

    /**
     * @dependency
     *
     * @param App $oApp
     *
     * @return Module
     */
    public final function setApp(App $oApp)
    {
        $this->oApp = $oApp;
        return $this;
    }

    /**
     * @dependency
     *
     * @param CacheInterface $oCache
     *
     * @return Module
     */
    public final function setCache(CacheInterface $oCache)
    {
        $this->oCache = $oCache;
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
     * Initializes the router,
     * fills it with the required info from the config object.
     */
    private function initRouter()
    {
        // setup the default module/controller/action
        $aMcaDefaults = array(
            'module' => $this->sModuleName,
            'controller' => $this->sDefaultController,
            'action' => $this->sDefaultAction
        );

        // init router
        $this->oRouter->setDefaults($aMcaDefaults);

        // add routes from the Routes.xml
        $sRoutesIniPath = $this->oApp->getProjectDir() . '/' . $this->sModuleDir . '/' . $this->sRoutesIniPath;

        if (is_readable($sRoutesIniPath)) {
            if ($this->oCache->exists($sRoutesIniPath)) {
                $this->oRouter->addRoutes($this->oCache->get($sRoutesIniPath));
            } else {
                $this->oRouter->addRoutes($sRoutesIniPath);
                $this->oCache->set($sRoutesIniPath, $this->oRouter->getRoutes());
            }
        }
    }

    /**
     * Creates a controller object for the given controller name.
     * Sets the required info and returns it.
     *
     * @param string $sControllerName
     *
     * @return ControllerInterface
     */
    public final function createController($sControllerName)
    {
        $sProjectDir = $this->oApp->getProjectDir();
        $sModuleDir = $sProjectDir . '/' . $this->sModuleDir;
        $sControllerClass = $this->sModuleNamespace . '\\' . $this->sControllerNamespace . '\\' . ucfirst($sControllerName);

        $oHead = $this->oApp->getDIContainer()->resolve('flyingpiranhas\\mvc\\views\\head\\interfaces\\HeadInterface');
        $oController = $this->oApp->getDIContainer()->resolve($sControllerClass);
        $oController->setViewSettings(
            array(
                 'oHead' => $oHead,
                 'aLayoutsIncludePath' => array(
                     $sModuleDir . '/' . $this->sLayoutsDir,
                     $sProjectDir . '/' . $this->oApp->getLayoutsDir(),
                 ),
                 'aViewsIncludePath' => array(
                     $sModuleDir . '/' . $this->sViewsDir . '/' . lcfirst($oController->getViewsDir()),
                     $sModuleDir . '/' . $this->sViewsDir,
                     $sProjectDir . '/' . $this->oApp->getViewsDir(),
                 ),
                 'aFragmentsIncludePath' => array(
                     $sModuleDir . '/' . $this->sViewFragmentsDir,
                     $sProjectDir . '/' . $this->oApp->getViewFragmentsDir(),
                 ),
            )
        );

        return $oController;
    }

    /**
     * @param string       $sAction
     * @param string|null  $sControllerName
     * @param Params|null  $aParams
     *
     * @return ViewInterface
     */
    public final function createView($sAction, $sControllerName = null, Params $aParams = null)
    {
        $sControllerName = ($sControllerName) ? $sControllerName : $this->oRouter->getController();

        $oController = $this->createController($sControllerName);

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
        $oView = $this->createView($this->oRouter->getAction(), $this->oRouter->getController());

        // render
        $this->oApp->getResponse()->setContent($oView);
        $this->oApp->getResponse()->send();
    }

    /**
     * This is called by the App right after a module is first created.
     * Initializes the Config and Router and registers dbAdapter classes
     * and calls the user defined preDispatch()
     */
    public final function initModule()
    {
        // init components
        $this->initRouter();
    }

    /**
     * Override this method to execute operations right after the module mind is created.
     */
    public function preDispatch()
    {

    }


}