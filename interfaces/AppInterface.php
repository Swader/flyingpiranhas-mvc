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
interface AppInterface
{

    /**
     * @param string $sAppEnv
     *
     * @return mixed
     */
    public function setAppEnv($sAppEnv);

    /**
     * @param string $sProjectDir
     *
     * @return mixed
     */
    public function setProjectDir($sProjectDir);

}
