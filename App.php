<?php

namespace flyingpiranhas\mvc;

use flyingpiranhas\mvc\interfaces\AppInterface;
use flyingpiranhas\common\dependencyInjection\interfaces\DIContainerInterface;
use flyingpiranhas\mvc\exceptions\MvcException;
use flyingpiranhas\mvc\router\interfaces\AppRouterInterface;
use flyingpiranhas\mvc\interfaces\ModuleBootstrapperInterface;
use flyingpiranhas\mvc\interfaces\ModuleInterface;
use Exception;

/**
 * @category       mvc
 * @package        flyingpiranhas.mvc
 * @license        Apache-2.0
 * @version        0.01
 * @since          2012-09-07
 * @author         Ivan Pintar
 */
class App implements AppInterface
{

    /** @var string */
    protected $sProjectDir = '';

    /** @var string */
    protected $sAppEnv = '';

    /** @var string */
    protected $sModulesDir = '';

    /** @var string */
    protected $sViewsDir = '';

    /** @var string */
    protected $sLayoutsDir = '';

    /** @var string */
    protected $sViewFragmentsDir = '';

    /** @var string */
    protected $sDefaultModule = '';

    /** @var array */
    protected $aModuleNamespaces = array();

    /** @var array */
    protected $aModuleConfigPaths = array();

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
     * @param array $aAppSettings
     *
     * @return App
     */
    public function setAppSettings(array $aAppSettings)
    {
        if (!$this->sModulesDir) $this->sModulesDir = $aAppSettings['modulesDir'];
        if (!$this->sViewsDir) $this->sViewsDir = $aAppSettings['viewsDir'];
        if (!$this->sLayoutsDir) $this->sLayoutsDir = $aAppSettings['layoutsDir'];
        if (!$this->sViewFragmentsDir) $this->sViewFragmentsDir = $aAppSettings['viewFragmentsDir'];
        if (!$this->sDefaultModule) $this->sDefaultModule = $aAppSettings['defaultModule'];
        if (!$this->aModuleNamespaces) $this->aModuleNamespaces = $aAppSettings['moduleNamespaces'];
        if (!$this->aModuleConfigPaths) $this->aModuleConfigPaths = $aAppSettings['moduleConfigPaths'];

        return $this;
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
     * Creates a Module for the given module name,
     * fills it with the required info and returns.
     *
     * @param string $sModuleName
     *
     * @return ModuleInterface
     * @throws MvcException
     */
    public final function findModule($sModuleName)
    {
        if (!isset($this->aModules[$sModuleName])) {
            $sModuleNamespace =
                (isset($this->aModuleNamespaces[$sModuleName]))
                    ? $this->aModuleNamespaces[$sModuleName]
                    : $sModuleName;

            $sModuleConfigPath =
                (isset($this->aModuleConfigPaths[$sModuleName]))
                    ? $this->aModuleConfigPaths[$sModuleName]
                    : 'config/Config.ini';

            $sModuleDir = $this->sProjectDir . '/' . $this->sModulesDir . '/' . str_replace('\\', '/', $sModuleNamespace);

            $aBootstrapperParams = array(
                'sModuleName' => $sModuleName,
                'sModuleNamespace' => $sModuleNamespace,
                'sModuleDir' => $sModuleDir,
                'sAppEnv' => $this->sAppEnv,
                'sModuleConfigPath' => $sModuleConfigPath
            );

            /** @var $oModuleBootstrapper ModuleBootstrapperInterface */
            $oModuleBootstrapper = null;
            try {
                $sModuleClass = $sModuleNamespace . '\\Bootstrapper';
                $oModuleBootstrapper = $this->oDIContainer->resolve($sModuleClass, $aBootstrapperParams);
            } catch (Exception $oException) {
                $oModuleBootstrapper = $this->oDIContainer->resolve('\\flyingpiranhas\\mvc\\ModuleBootstrapper', $aBootstrapperParams);
            }


            if (!($oModuleBootstrapper instanceof ModuleBootstrapperInterface)) {
                throw new MvcException('The module bootstrapper has to implement \\flyingpiranhas\\mvc\\interfaces\\ModuleBootstrapperInterface');
            }

            $this->aModules[$sModuleName] = $oModuleBootstrapper->findModule();
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
     * Parses the request to find out the module.
     * It then creates a Module and calls its work() method
     */
    public final function work()
    {
        $this->preDispatch();

        $this->oRouter->parseRequest();
        $oModule = $this->findModule($this->oRouter->getModule());
        $oModule->work();

        $this->postDispatch();
    }

}