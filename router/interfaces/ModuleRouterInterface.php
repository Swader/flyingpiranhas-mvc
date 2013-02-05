<?php

namespace flyingpiranhas\mvc\router\interfaces;

/**
 * Routers used by FP Modules
 * should implement this interface
 *
 * @author pinetree
 */
interface ModuleRouterInterface extends RouterInterface
{

    /**
     * @return string
     */
    public function getController();

    /**
     * @return string
     */
    public function getAction();

}