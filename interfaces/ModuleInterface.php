<?php

namespace flyingpiranhas\mvc\interfaces;

/**
 * @category       interfaces
 * @package        flyingpiranhas.mvc
 * @license        BSD License
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

    public function initModule();

    public function preDispatch();

    public function work();

}