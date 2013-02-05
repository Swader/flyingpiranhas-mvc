<?php

namespace flyingpiranhas\mvc\router;

use flyingpiranhas\mvc\router\Route;

/**
 * Description of Redirect
 *
 * @category       router
 * @package        flyingpiranhas.mvc
 * @license        BSD License
 * @version        0.01
 * @since          2012-09-07
 * @author         Ivan Pintar
 */
class Redirect extends Route
{

    /** @var int */
    private $iHeader = 302;

    /**
     * @return int
     */
    public function getHeader()
    {
        return $this->iHeader;
    }

    /**
     * @param int $iHeader
     *
     * @return Redirect
     */
    protected function setHeader($iHeader)
    {
        $this->iHeader = (int)$iHeader;
        return $this;
    }

}