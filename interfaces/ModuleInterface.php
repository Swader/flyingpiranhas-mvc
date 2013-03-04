<?php

namespace flyingpiranhas\mvc\interfaces;

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
     * @return string
     */
    public function getModuleDir();

    /**
     * @return string
     */
    public function getLayoutsDir();

    /**
     * @return string
     */
    public function getViewsDir();

    /**
     * @return string
     */
    public function getViewFragmentsDir();

    /**
     * @return AppInterface
     */
    public function getApp();

    /**
     * @param string $sAction
     * @param string $sControllerName
     * @param array  $aParams
     *
     * @return mixed
     */
    public function findView($sAction, $sControllerName, array $aParams = null);

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
    public function work();

}