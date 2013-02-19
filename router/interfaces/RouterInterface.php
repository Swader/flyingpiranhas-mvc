<?php

namespace flyingpiranhas\mvc\router\interfaces;

/**
 *
 * @author pinetree
 */
interface RouterInterface
{

    /**
     * @return RouterInterface
     */
    public function parseRequest();

    /**
     * @param array $aMcaDefaults
     *
     * @return RouterInterface
     */
    public function setDefaults(array $aMcaDefaults);

    /**
     * @param array
     *
     * @return RouterInterface
     */
    public function addRoutes($aRoutes);

    /**
     * @return array;
     */
    public function getRoutes();

}