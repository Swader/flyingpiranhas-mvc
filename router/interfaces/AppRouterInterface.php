<?php

namespace flyingpiranhas\mvc\router\interfaces;

/**
 * Router used by FP App
 * should implement this interface
 *
 * @category       router
 * @package        flyingpiranhas.mvc
 * @license        BSD License
 * @version        0.01
 * @since          2012-09-07
 * @author         Ivan Pintar
 */
interface AppRouterInterface extends RouterInterface
{

    /**
     * @return string
     */
    public function getModule();

}