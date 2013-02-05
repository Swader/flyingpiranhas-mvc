<?php

namespace flyingpiranhas\mvc;

use flyingpiranhas\common\cache\interfaces\CacheInterface;
use flyingpiranhas\common\dependencyInjection\interfaces\DIContainerInterface;
use flyingpiranhas\common\errorHandling\interfaces\ErrorHandlerInterface;
use flyingpiranhas\common\session\interfaces\SessionInterface;
use flyingpiranhas\common\http\interfaces\RequestInterface;
use flyingpiranhas\common\http\interfaces\ResponseInterface;
use flyingpiranhas\mvc\exceptions\MvcException;
use flyingpiranhas\mvc\interfaces\ModuleInterface;
use flyingpiranhas\mvc\router\interfaces\AppRouterInterface;
use Exception;

/**
 * @category       mvc
 * @package        flyingpiranhas.mvc
 * @license        BSD License
 * @version        0.01
 * @since          2012-09-07
 * @author         Ivan Pintar
 */
class App
{

    /** @var string */
    protected $sAppEnv = 'production';

    /** @var string */
    protected $sRoutesIniPath = 'application/config/Routes.ini';

    /** @var string */
    protected $sProjectDir = '';

    /** @var string */
    protected $sModulesDir = 'application/modules';

    /** @var string */
    protected $sViewsDir = 'application/views/scripts';

    /** @var string */
    protected $sLayoutsDir = 'application/views/layouts';

    /** @var string */
    protected $sViewFragmentsDir = 'application/views/fragments';

    /** @var string */
    protected $sDefaultModule = 'home';

    /** @var array */
    protected $aModuleNamespaces = array();

    /** @var CacheInterface */
    protected $oCache;

    /** @var SessionInterface */
    protected $oSession;

    /** @var RequestInterface */
    protected $oRequest;

    /** @var ResponseInterface */
    protected $oResponse;

    /** @var ErrorHandlerInterface */
    protected $oErrorHandler;

    /** @var AppRouterInterface */
    protected $oRouter;

    /** @var DIContainerInterface */
    protected $oDIContainer;

    /** @var array */
    protected $aModules = array();

    /**
     * @return string
     */
    public function getAppEnv()
    {
        return $this->sAppEnv;
    }

    /**
     * @return string
     */
    public function getProjectDir()
    {
        return $this->sProjectDir;
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
    public function getLayoutsDir()
    {
        return $this->sLayoutsDir;
    }

    /**
     * @return string
     */
    public function getViewFragmentsDir()
    {
        return $this->sViewFragmentsDir;
    }

    /**
     * @return CacheInterface
     */
    public final function getCache()
    {
        return $this->oCache;
    }

    /**
     * @return SessionInterface
     */
    public final function getSession()
    {
        return $this->oSession;
    }

    /**
     * @return RequestInterface
     */
    public final function getRequest()
    {
        return $this->oRequest;
    }

    /**
     * @return ResponseInterface
     */
    public final function getResponse()
    {
        return $this->oResponse;
    }

    /**
     * @return ErrorHandlerInterface
     */
    public final function getErrorHandler()
    {
        return $this->oErrorHandler;
    }

    /**
     * @return AppRouterInterface
     */
    public final function getRouter()
    {
        return $this->oRouter;
    }

    /**
     * @return DIContainerInterface
     */
    public final function getDIContainer()
    {
        return $this->oDIContainer;
    }

    /**
     * @param string $sAppEnv
     *
     * @return App
     */
    public function setAppEnv($sAppEnv)
    {
        $this->sAppEnv = $sAppEnv;
        return $this;
    }

    /**
     * @param string $sRoutesIniPath
     *
     * @return App
     */
    public function setRoutesIniPath($sRoutesIniPath)
    {
        $this->sRoutesIniPath = $sRoutesIniPath;
        return $this;
    }

    /**
     * @param string $sProjectDir
     *
     * @return App
     */
    public function setProjectDir($sProjectDir)
    {
        $this->sProjectDir = $sProjectDir;
        return $this;
    }

    /**
     * @param array $aModuleNamespaces
     *
     * @return App
     */
    public function setModuleNamespaces(array $aModuleNamespaces)
    {
        $this->aModuleNamespaces = $aModuleNamespaces;
        return $this;
    }

    /**
     * @dependency
     *
     * @param CacheInterface $oCache
     *
     * @return App
     */
    public final function setCache(CacheInterface $oCache)
    {
        $this->oCache = $oCache;
        return $this;
    }

    /**
     * @dependency
     *
     * @param SessionInterface $oSession
     *
     * @return App
     */
    public final function setSession(SessionInterface $oSession)
    {
        $this->oSession = $oSession;
        return $this;
    }

    /**
     * @dependency
     *
     * @param RequestInterface $oRequest
     *
     * @return App
     */
    public final function setRequest(RequestInterface $oRequest)
    {
        $this->oRequest = $oRequest;
        return $this;
    }

    /**
     * @dependency
     *
     * @param ResponseInterface $oResponse
     *
     * @return App
     */
    public final function setResponse(ResponseInterface $oResponse)
    {
        $this->oResponse = $oResponse;
        return $this;
    }

    /**
     * @dependency
     *
     * @param ErrorHandlerInterface $oErrorHandler
     *
     * @return App
     */
    public final function setErrorHandler(ErrorHandlerInterface $oErrorHandler)
    {
        $this->oErrorHandler = $oErrorHandler;
        return $this;
    }

    /**
     * @dependency
     *
     * @param AppRouterInterface $oRouter
     *
     * @return App
     */
    public final function setRouter(AppRouterInterface $oRouter)
    {
        $this->oRouter = $oRouter;
        return $this;
    }

    /**
     * @dependency
     *
     * @param DIContainerInterface $oDIContainer
     *
     * @return App
     */
    public final function setDIContainer(DIContainerInterface $oDIContainer)
    {
        $this->oDIContainer = $oDIContainer;
        $this->oDIContainer->registerInstance($this, __CLASS__);
        return $this;
    }

    /**
     * Initializes the router,
     * fills it with the required info from the config object.
     */
    protected final function initRouter()
    {
        $this->oRouter->setDefaults(
            array(
                 'module' => $this->sDefaultModule
            )
        );

        // add routes from the Routes.xml
        $sRoutesIniPath = $this->sProjectDir . '/' . $this->sRoutesIniPath;
        if (is_readable($sRoutesIniPath)) {
            if ($this->oCache->exists($sRoutesIniPath)) {
                $this->oRouter->addRoutes($this->oCache->get($sRoutesIniPath));
            } else {
                $this->oRouter->addRoutes($sRoutesIniPath);
                $this->oCache->set($sRoutesIniPath, $this->oRouter->getRoutes());
            }
        }

        return $this;
    }

    /**
     * Creates a Module for the given module name,
     * fills it with the required info and returns.
     *
     * @param string $sModuleName
     *
     * @return Module
     * @throws MvcException
     */
    public final function findModule($sModuleName)
    {
        if (!isset($this->aModules[$sModuleName])) {
            $sModuleNamespace = (isset($this->aModuleNamespaces[$sModuleName])) ? $this->aModuleNamespaces[$sModuleName] : $sModuleName;
            $sModuleDir = $this->sModulesDir . '/' . str_replace('\\', '/', $sModuleNamespace);

            /** @var $oModule ModuleInterface */
            $oModule = null;
            try {
                $sModuleClass = $sModuleNamespace . '\\Module';
                $oModule = $this->oDIContainer->resolve($sModuleClass);
            } catch (Exception $oException) {
                $oModule = $this->oDIContainer->resolve('\\flyingpiranhas\\mvc\\Module');
            }

            if (!($oModule instanceof ModuleInterface)) {
                throw new MvcException('The custom module has to implement \\flyingpiranhas\\mvc\\interfaces\\ModuleInterface');
            }

            $oModule
                ->setModuleDir($sModuleDir)
                ->setModuleName($sModuleName)
                ->setModuleNamespace($sModuleNamespace);

            $oModule->initModule();
            $oModule->preDispatch();
            $this->aModules[$sModuleName] = $oModule;
        }

        return $this->aModules[$sModuleName];
    }

    /**
     * Override this method to execute operations before the routing starts
     */
    protected function preDispatch()
    {

    }

    /**
     * Override this method to execute operations on post dispatch
     */
    protected function postDispatch()
    {

    }

    /**
     * Called internally to initialize the components when processing a request
     */
    private function initApp()
    {
        // init components
        $this->oErrorHandler->register();

        $this->initRouter();

        // start session
        $this->oSession->registerAndStart();
    }

    /**
     * Parses the request to find out the module.
     * It then creates a Module and calls its work() method
     */
    public final function work()
    {
        $this->initApp();
        $this->preDispatch();

        $this->oRouter->parseRequest();
        $oModule = $this->findModule($this->oRouter->getModule());
        $oModule->work();

        $this->postDispatch();
    }

}