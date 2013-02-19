<?php

namespace flyingpiranhas\mvc;

use flyingpiranhas\mvc\interfaces\ModuleInterface;
use flyingpiranhas\mvc\router\ModuleRouter;
use flyingpiranhas\common\dependencyInjection\DIContainer;
use flyingpiranhas\common\http\interfaces\RequestInterface;
use flyingpiranhas\common\http\interfaces\ResponseInterface;
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
     * @throws MvcException
     */
    public final function run()
    {
        $this->initRouter();
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
            ->setModuleSettings($this->aModuleSettings);
        $oModule->work();
    }

    /**  */
    protected function initRouter()
    {
        /** @var $oRequest RequestInterface */
        $oRequest = $this->oDIContainer->resolve('flyingpiranhas\\common\\http\\interfaces\\RequestInterface');

        /** @var $oResponse ResponseInterface */
        $oResponse = $this->oDIContainer->resolve('flyingpiranhas\\common\\http\\interfaces\\ResponseInterface');

        // setup the default controller/action
        $aMcaDefaults = array(
            'module' => $this->sModuleName,
            'controller' => $this->aModuleSettings['defaultController'],
            'action' => $this->aModuleSettings['defaultAction']
        );

        // add routes from the Routes.ini
        $sRoutesIniPath = $this->sModuleDir . '/' . trim($this->aModuleSettings['routesIniPath'], '.ini') . '.ini';

        $oClosure = function () use ($oRequest, $oResponse, $aMcaDefaults, $sRoutesIniPath) {
            $oRouter = new ModuleRouter($oRequest, $oResponse);
            $oRouter->setDefaults($aMcaDefaults);

            if (is_readable($sRoutesIniPath)) {
                $oRouter->addRoutes($sRoutesIniPath);
            }
            return $oRouter;
        };

        $this->oDIContainer->registerClosure(
            $oClosure,
            'flyingpiranhas\\mvc\\router\\interfaces\\ModuleRouterInterface',
            DIContainer::SHARED_INSTANCE
        );
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
