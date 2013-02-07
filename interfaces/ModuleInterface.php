<?php

namespace flyingpiranhas\mvc\interfaces;

use flyingpiranhas\common\http\Params;

/**
 * @category       interfaces
 * @package        flyingpiranhas.mvc
 * @license        Apache-2.0
 * @version        0.01
 * @since          2012-09-07
 * @author         Ivan Pintar
 */
interface ModuleInterface
{

    /**
     * @return AppInterface
     */
    public function getApp();

    /**
     * @param string $sAction
     * @param string $sControllerName
     * @param Params $aParams
     *
     * @return mixed
     */
    public function findView($sAction, $sControllerName = null, Params $aParams = null);

    /**
     * @param string $sModuleName
     *
     * @return ModuleInterface
     */
    public function setModuleName($sModuleName);

    /**
     * @param string $sModuleNamespace
     *
     * @return ModuleInterface
     */
    public function setModuleNamespace($sModuleNamespace);

    /**
     * @param string $sModuleDir
     *
     * @return ModuleInterface
     */
    public function setModuleDir($sModuleDir);

    /**
     * @param array $aModuleSettings
     *
     * @return ModuleInterface
     */
    public function setModuleSettings(array $aModuleSettings);

    /**
     * @return ModuleInterface
     */
    public function preDispatch();

    /**
     * @return ModuleInterface
     */
    public function work();

}