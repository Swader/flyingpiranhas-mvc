<?php

namespace flyingpiranhas\mvc\interfaces;

use flyingpiranhas\common\dependencyInjection\interfaces\DIContainerInterface;
use flyingpiranhas\mvc\exceptions\MvcException;

/**
 * @category       interfaces
 * @package        flyingpiranhas.mvc
 * @license        Apache-2.0
 * @version        0.01
 * @since          2012-09-07
 * @author         Ivan Pintar
 */
interface ModuleBootstrapperInterface
{

    /**
     * @param                      $sModuleName
     * @param                      $sModuleNamespace
     * @param                      $sModuleDir
     * @param                      $sAppEnv
     * @param DIContainerInterface $oDIContainer
     * @param string               $sModuleConfigPath
     */
    public function __construct($sModuleName,
                                $sModuleNamespace,
                                $sModuleDir,
                                $sAppEnv,
                                DIContainerInterface $oDIContainer,
                                $sModuleConfigPath = 'config/Config.ini');

    /**
     * @return ModuleInterface
     * @throws MvcException
     */
    public function findModule();

    /**  */
    public function run();

}
