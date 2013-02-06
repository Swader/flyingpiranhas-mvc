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
    public function initModule();

    /**
     * @return ModuleInterface
     */
    public function preDispatch();

    /**
     * @return ModuleInterface
     */
    public function work();

}