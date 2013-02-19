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
interface AppInterface
{

    /**
     * @return string
     */
    public function getProjectDir();

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
     * @param string $sAppEnv
     *
     * @return AppInterface
     */
    public function setAppEnv($sAppEnv);

    /**
     * @param string $sProjectDir
     *
     * @return AppInterface
     */
    public function setProjectDir($sProjectDir);

    /**
     * @param array $aAppSettings
     *
     * @return AppInterface
     */
    public function setAppSettings(array $aAppSettings);

}
