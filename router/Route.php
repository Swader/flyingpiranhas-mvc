<?php

namespace flyingpiranhas\mvc\router;

use flyingpiranhas\common\traits\ProtectedPropertySetter;


/**
 * Description of Route
 *
 * @category       router
 * @package        flyingpiranhas.mvc
 * @license        BSD License
 * @version        0.01
 * @since          2012-09-07
 * @author         Ivan Pintar
 */
class Route
{

    use ProtectedPropertySetter;

    /** @var string */
    protected $sName = '';

    /** @var string */
    protected $sFrom = '';

    /** @var string */
    protected $sTo = 'home/index/index';

    /**
     * @param string $sName
     * @param array  $aProperties
     */
    public function __construct($sName, array $aProperties = array())
    {
        $this->sName = $sName;
        $this->setProperties($aProperties);
    }

    /**
     * @param string $sName
     *
     * @return Route
     */
    protected function setName($sName)
    {
        $this->sName = $sName;
        return $this;
    }

    /**
     * @param string $sFrom
     *
     * @return Route
     */
    protected function setFrom($sFrom)
    {
        $this->sFrom = '/^' . str_replace('/', '\\/', trim($sFrom, '/')) . '$/';
        return $this;
    }

    /**
     * @param string $sTo
     *
     * @return Route
     */
    protected function setTo($sTo)
    {
        $this->sTo = trim($sTo, '/');
        return $this;
    }

    /**
     * @param string $sUrl
     *
     * @return bool
     */
    public function urlMatches($sUrl)
    {
        $sUrl = trim($sUrl, '/');
        return (bool)preg_match($this->sFrom, $sUrl);
    }

    /**
     * @param string $sUrl
     *
     * @return string
     */
    public function getNewUrl($sUrl)
    {
        $sUrl = trim($sUrl, '/');
        return '/' . preg_replace($this->sFrom, $this->sTo, $sUrl);
    }

}