<?php

namespace flyingpiranhas\mvc;

use flyingpiranhas\mvc\interfaces\ModuleInterface;
use flyingpiranhas\mvc\interfaces\ModuleBootstrapperInterface;
use flyingpiranhas\mvc\exceptions\MvcException;
use Exception;
use flyingpiranhas\common\config\ConfigRoot;
use flyingpiranhas\common\dependencyInjection\interfaces\DIContainerInterface;

/**
 * The bootstrapper registers the dependencies that the mvc uses.
 * To register other dependencies, extend this bootstrapper in the application
 * and override the runCustomInit() method
 *
 * @category       mvc
 * @package        flyingpiranhas.mvc
 * @license        Apache-2.0
 * @version        0.01
 * @since          2012-09-07
 * @author         Ivan Pintar
 */
class ModuleBootstrapper implements ModuleBootstrapperInterface
{

    /** @var ModuleInterface */
    protected $oModule;

    /** @var string */
    protected $sAppEnv = 'production';

    /** @var DIContainerInterface */
    protected $oDIContainer;

    /** @var string */
    protected $sModuleName = '';

    /** @var string */
    protected $sModuleNamespace = '';

    /** @var string */
    protected $sModuleDir = '';

    /**
     * all paths are relative to the module dir
     *
     * @var array
     */
    protected $aModuleSettings = array(
        'routesIniPath' => 'config/Routes.ini',

        'viewsDir' => 'views/scripts',
        'layoutsDir' => 'views/layouts',
        'viewFragmentsDir' => 'views/fragments',

        'controllerNamespace' => 'controllers',
        'defaultController' => 'Index',
        'defaultAction' => 'index',
    );

    /**
     * @param string               $sModuleName
     * @param string               $sModuleNamespace
     * @param string               $sModuleDir
     * @param string               $sAppEnv
     * @param DIContainerInterface $oDIContainer
     * @param string               $sModuleConfigPath
     */
    public function __construct($sModuleName,
                                $sModuleNamespace,
                                $sModuleDir,
                                $sAppEnv,
                                DIContainerInterface $oDIContainer,
                                $sModuleConfigPath = 'config/Config.ini')
    {
        $this->sModuleName = $sModuleName;
        $this->sModuleNamespace = $sModuleNamespace;
        $this->sModuleDir = $sModuleDir;
        $this->oDIContainer = $oDIContainer;
        $this->sAppEnv = $sAppEnv;

        $this->parseConfig($sModuleConfigPath);
    }

    /**
     * @return ModuleInterface
     * @throws MvcException
     */
    public final function findModule()
    {
        if (!$this->oModule) {
            $this->runCustomInit();

            /** @var $oModule ModuleInterface */
            $oModule = null;
            try {
                $sModuleClass = $this->sModuleNamespace . '\\Module';
                $oModule = $this->oDIContainer->resolve($sModuleClass);
            } catch (Exception $oException) {
                $oModule = $this->oDIContainer->resolve('\\flyingpiranhas\\mvc\\Module');
            }

            if (!($oModule instanceof ModuleInterface)) {
                throw new MvcException('The module has to implement \\flyingpiranhas\\mvc\\interfaces\\ModuleInterface');
            }

            $oModule
                ->setModuleDir($this->sModuleDir)
                ->setModuleName($this->sModuleName)
                ->setModuleNamespace($this->sModuleNamespace)
                ->setModuleSettings($this->aModuleSettings)
                ->initModule()
                ->preDispatch();
            $this->oModule = $oModule;
        }
        return $this->oModule;
    }

    /**  */
    public function run()
    {
        $this->findModule()->work();
    }

    /**  */
    protected function runCustomInit()
    {

    }

    /**  */
    private final function parseConfig($sModuleConfigPath)
    {
        $sModuleConfigPath = $this->sModuleDir . '/' . $sModuleConfigPath;

        if (is_readable($sModuleConfigPath)) {
            $oConfig = new ConfigRoot($this->sAppEnv, $sModuleConfigPath);

            foreach (array_keys($this->aModuleSettings) as $sKey) {
                if (isset($oConfig->module->{$sKey})) {
                    $this->aModuleSettings[$sKey] = $oConfig->module->{$sKey};
                }
            }
        }
    }

}
