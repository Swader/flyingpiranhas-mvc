<?php

namespace flyingpiranhas\mvc\views;

use flyingpiranhas\mvc\views\interfaces\ViewInterface;
use flyingpiranhas\mvc\views\head\interfaces\HeadInterface;
use flyingpiranhas\mvc\views\exceptions\JsonException;

/**
 * Description of Json
 *
 * @category       views
 * @package        flyingpiranhas.mvc
 * @license        BSD License
 * @version        0.01
 * @since          2012-09-07
 * @author         Ivan Pintar
 */
class Json implements ViewInterface
{

    /** @var array */
    protected $sResponseHeader = array('Content-type: text/json; charset=uft-8');

    /** @var array */
    protected $aViewData = array();

    /**
     * @return string
     * @throws JsonException
     */
    public function getContents()
    {
        $sContents = json_encode($this->aViewData);
        if (!$sContents) {
            throw new JsonException('Failed to create json from view data');
        }

        return $sContents;
    }

    /**
     * @return array
     */
    public function getLayoutsIncludePath()
    {
        return array();
    }

    /**
     * @return bool
     */
    public function getView()
    {
        return false;
    }

    /**
     * @return array
     */
    public function getViewsIncludePath()
    {
        return array();
    }

    public function preRenderAll()
    {
    }

    public function preRender()
    {

    }

    public function render()
    {
        $this->preRender();
        echo $this->getContents();
    }

    /**
     * @param string $sLayout
     *
     * @return void
     * @throws JsonException
     */
    public function setLayout($sLayout)
    {
        throw new JsonException('Can not set layout on json output');
    }

    /**
     * @param array $aLayoutsIncludePath
     *
     * @return void
     * @throws JsonException
     */
    public function setLayoutsIncludePath(array $aLayoutsIncludePath)
    {
        throw new JsonException('Can not set layout on json output');
    }

    /**
     * @param string $sView
     *
     * @return void
     * @throws JsonException
     */
    public function setView($sView)
    {
        throw new JsonException('Can not set view on json output');
    }

    /**
     * @param array $aViewsIncludePath
     *
     * @return void
     * @throws JsonException
     */
    public function setViewsIncludePath(array $aViewsIncludePath)
    {
        throw new JsonException('Can not set view on json output');
    }

    /**
     * @return array
     */
    public function getResponseHeaders()
    {
        return $this->sResponseHeader;
    }

    /**
     * @return void
     * @throws JsonException
     */
    public function getHead()
    {
        throw new JsonException('Can not set head on json output');
    }

    /**
     * @param HeadInterface $oHead
     *
     * @return void
     * @throws JsonException
     */
    public function setHead(HeadInterface $oHead)
    {
        throw new JsonException('Can not set head on json output');
    }
}