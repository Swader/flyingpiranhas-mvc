<?php

namespace flyingpiranhas\mvc\views\minify\interfaces;

/**
 * Any minifier that will be used by the Head object
 * should implement this interface.
 *
 * @category       views
 * @package        flyingpiranhas.mvc
 * @license        BSD License
 * @version        0.01
 * @since          2012-09-07
 * @author         Ivan Pintar
 */
interface MinifyInterface
{

    /**
     * @param string $sString
     *
     * @return string
     */
    public function minify(&$sString);
}