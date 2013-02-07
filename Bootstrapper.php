<?php

namespace flyingpiranhas\mvc;

use SessionHandler;
use flyingpiranhas\mvc\router\AppRouter;
use flyingpiranhas\common\config\ConfigRoot;
use flyingpiranhas\common\dependencyInjection\DIContainer;
use flyingpiranhas\common\session\Session;
use flyingpiranhas\common\http\Response;
use flyingpiranhas\common\http\Request;
use flyingpiranhas\mvc\errorHandling\ErrorHandler;
use flyingpiranhas\common\http\interfaces\RequestInterface;
use flyingpiranhas\common\http\interfaces\ResponseInterface;

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
class Bootstrapper
{

    /**
     * all paths are relative to the project dir,
     * minify path is relative to the public dir
     *
     * @var array
     */
    protected $aAppSettings = array(
        'routesIniPath' => 'application/config/Routes.ini',

        'moduleNamespaces' => array(),
        'moduleConfigPaths' => array(),

        'modulesDir' => 'application/modules',
        'publicDir' => 'public',
        'minifyDir' => 'minify',

        'viewsDir' => 'application/views/scripts',
        'layoutsDir' => 'application/views/layouts',
        'viewFragmentsDir' => 'application/views/fragments',
        'errorViewsDir' => 'application/views/errors',

        'defaultModule' => 'home'
    );

    /** @var DIContainer */
    protected $oDIContainer;

    /** @var string */
    protected $sAppEnv = 'production';

    /** @var string */
    protected $sProjectDir = '';

    /**
     * @param string $sProjectDir
     * @param string $sAppConfigPath
     * @param string $sAppEnv
     */
    public function __construct($sProjectDir,
                                $sAppEnv = 'production',
                                $sAppConfigPath = 'application/config/Config.ini')
    {
        $this->sProjectDir = $sProjectDir;
        $this->sAppEnv = $sAppEnv;

        $this->parseConfig($sAppConfigPath);

        $this->oDIContainer = new DIContainer();
        $this->oDIContainer->registerInstance(
            $this->oDIContainer,
            'flyingpiranhas\\common\\dependencyInjection\\interfaces\\DIContainerInterface'
        );
    }

    /**  */
    public function run()
    {
        $this->initRequestResponse();
        $this->initErrorHandler();
        $this->initSession();
        $this->initRouter();
        $this->initView();
        $this->initApp();
        $this->runCustomInit();

        /** @var $oApp App */
        $oApp = $this->oDIContainer->resolve('flyingpiranhas\\mvc\\interfaces\\AppInterface');
        $oApp
            ->setAppEnv($this->sAppEnv)
            ->setProjectDir($this->sProjectDir)
            ->setAppSettings($this->aAppSettings);

        $oApp->work();
    }

    /**  */
    protected function initRequestResponse()
    {
        $this->oDIContainer->registerInstance(
            new Request(),
            'flyingpiranhas\\common\\http\\interfaces\\RequestInterface'
        );

        $this->oDIContainer->registerInstance(
            new Response(),
            'flyingpiranhas\\common\\http\\interfaces\\ResponseInterface'
        );
    }

    /**  */
    protected function initSession()
    {
        $oSession = new Session(new SessionHandler());

        $this->oDIContainer->registerInstance(
            $oSession,
            'flyingpiranhas\\common\\session\\interfaces\\SessionInterface'
        );

        $oSession->registerAndStart();
    }

    /**  */
    protected function initRouter()
    {
        /** @var $oRequest RequestInterface */
        $oRequest = $this->oDIContainer->resolve('flyingpiranhas\\common\\http\\interfaces\\RequestInterface');

        /** @var $oResponse ResponseInterface */
        $oResponse = $this->oDIContainer->resolve('flyingpiranhas\\common\\http\\interfaces\\ResponseInterface');

        // setup the default module
        $aMcaDefaults = array(
            'module' => $this->aAppSettings['defaultModule']
        );

        // add routes from the Routes.ini
        $sRoutesIniPath = $this->sProjectDir . '/' . trim($this->aAppSettings['routesIniPath'], '.ini') . '.ini';


        $oClosure = function () use ($oRequest, $oResponse, $aMcaDefaults, $sRoutesIniPath) {
            $oRouter = new AppRouter($oRequest, $oResponse);
            $oRouter->setDefaults($aMcaDefaults);

            if (is_readable($sRoutesIniPath)) {
                $oRouter->setDefaults($sRoutesIniPath);
            }
            return $oRouter;
        };

        $this->oDIContainer->registerClosure(
            $oClosure,
            'flyingpiranhas\\mvc\\router\\interfaces\\AppRouterInterface',
            DIContainer::NEW_INSTANCE
        );
    }

    /**  */
    protected function initView()
    {
        $aHeadParams = array(
            'sPublicDir' => $this->sProjectDir . '/' . $this->aAppSettings['publicDir'],
            'sMinifyDir' => $this->aAppSettings['minifyDir'],
            'oJsMinifier' => 'jsMinifier',
            'oCssMinifier' => 'cssMinifier',
        );

        $this->oDIContainer->registerClass(
            'flyingpiranhas\\mvc\\views\\minify\\MinifyJs',
            'jsMinifier'
        );

        $this->oDIContainer->registerClass(
            'flyingpiranhas\\mvc\\views\\minify\\MinifyCss',
            'cssMinifier'
        );

        $this->oDIContainer->registerClass(
            'flyingpiranhas\\mvc\\views\\head\\Head',
            'flyingpiranhas\\mvc\\views\\head\\interfaces\\HeadInterface',
            DIContainer::SHARED_INSTANCE,
            $aHeadParams
        );
    }

    /**  */
    protected function initApp()
    {
        $this->oDIContainer->registerClass(
            'flyingpiranhas\\mvc\\App',
            'flyingpiranhas\\mvc\\interfaces\\AppInterface',
            DIContainer::SHARED_INSTANCE
        );
    }

    /**  */
    protected function initErrorHandler()
    {
        $sErrorViewsDir = $this->sProjectDir . '/' . $this->aAppSettings['errorViewsDir'];

        /** @var $oRequest RequestInterface */
        $oRequest = $this->oDIContainer->resolve('flyingpiranhas\\common\\http\\interfaces\\RequestInterface');

        $oErrorHandler = new ErrorHandler($oRequest, $this->sAppEnv, $sErrorViewsDir);
        $oErrorHandler->register();

        $this->oDIContainer->registerInstance(
            $oErrorHandler,
            'flyingpiranhas\\common\\errorHandling\\interfaces\\ErrorHandlerInterface'
        );
    }

    /**  */
    protected function runCustomInit()
    {

    }

    /**  */
    private function parseConfig($sAppConfigPath)
    {
        $sAppConfigPath = $this->sProjectDir . '/' . $sAppConfigPath;

        if (is_readable($sAppConfigPath)) {
            $oConfig = new ConfigRoot($this->sAppEnv, $sAppConfigPath);

            foreach (array_keys($this->aAppSettings) as $sKey) {
                if (isset($oConfig->app->{$sKey})) {
                    $this->aAppSettings[$sKey] = $oConfig->app->{$sKey};
                }
            }
        }
    }
}
