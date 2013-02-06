<?php

namespace flyingpiranhas\mvc;

use flyingpiranhas\common\dependencyInjection\DIContainer;

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

    /** @var DIContainer */
    protected $oDIContainer;

    /** @var string */
    protected $sAppEnv = 'production';

    /** @var string */
    protected $sProjectDir = '';

    /**
     * @param string $sProjectDir
     * @param string $sAppEnv
     */
    public function __construct($sProjectDir, $sAppEnv = 'production')
    {
        $this->sProjectDir = $sProjectDir;
        $this->sAppEnv = $sAppEnv;

        $this->oDIContainer = new DIContainer();
        $this->oDIContainer->registerInstance(
            $this->oDIContainer,
            'flyingpiranhas\\common\\dependencyInjection\\interfaces\\DIContainerInterface'
        );
    }

    /**  */
    public function run()
    {
        $this->registerMvcDependencies();
        $this->runCustomInit();

        /** @var $oMind App */
        $oMind = $this->oDIContainer->resolve('flyingpiranhas\\mvc\\interfaces\\AppInterface');
        $oMind
            ->setAppEnv($this->sAppEnv)
            ->setProjectDir($this->sProjectDir);

        $oMind->work();
    }

    /**  */
    protected function registerMvcDependencies()
    {
        $aErrorHandlerParams = array(
            'sAppEnv' => $this->sAppEnv,
            'sErrorViewsDir' => $this->sProjectDir . '/application/views/errors'
        );

        $aHeadParams = array(
            'sPublicDir' => $this->sProjectDir . '/public',
            'sMinifyDir' => 'minify',
            'oJsMinifier' => 'jsMinifier',
            'oCssMinifier' => 'cssMinifier',
        );

        $this->oDIContainer->registerClass(
            'SessionHandler',
            'SessionHandlerInterface'
        );

        $this->oDIContainer->registerClass(
            'flyingpiranhas\\common\\session\\Session',
            'flyingpiranhas\\common\\session\\interfaces\\SessionInterface'
        );

        $this->oDIContainer->registerClass(
            'flyingpiranhas\\mvc\\router\\AppRouter',
            'flyingpiranhas\\mvc\\router\\interfaces\\AppRouterInterface'
        );

        $this->oDIContainer->registerClass(
            'flyingpiranhas\\mvc\\router\\ModuleRouter',
            'flyingpiranhas\\mvc\\router\\interfaces\\ModuleRouterInterface',
            DIContainer::NEW_INSTANCE
        );

        $this->oDIContainer->registerClass(
            'flyingpiranhas\\common\\http\\Request',
            'flyingpiranhas\\common\\http\\interfaces\\RequestInterface'
        );

        $this->oDIContainer->registerClass(
            'flyingpiranhas\\common\\http\\Response',
            'flyingpiranhas\\common\\http\\interfaces\\ResponseInterface'
        );

        $this->oDIContainer->registerClass(
            'flyingpiranhas\\mvc\\errorHandling\\ErrorHandler',
            'flyingpiranhas\\common\\errorHandling\\interfaces\\ErrorHandlerInterface',
            DIContainer::SHARED_INSTANCE,
            $aErrorHandlerParams
        );

        $this->oDIContainer->registerClass(
            'flyingpiranhas\\common\\cache\\ArrayCache',
            'flyingpiranhas\\common\\cache\\interfaces\\CacheInterface'
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

        $this->oDIContainer->registerClass(
            'flyingpiranhas\\mvc\\App',
            'flyingpiranhas\\mvc\\interfaces\\AppInterface'
        );
    }

    /**  */
    protected function runCustomInit()
    {

    }

}
