<?php

namespace flyingpiranhas\mvc\views;

use flyingpiranhas\mvc\views\exceptions\JsonException;
use flyingpiranhas\common\http\interfaces\ContentInterface;

/**
 * Description of Json
 *
 * @category       views
 * @package        flyingpiranhas.mvc
 * @license        Apache-2.0
 * @version        0.01
 * @since          2012-09-07
 * @author         Ivan Pintar
 */
class Json implements ContentInterface
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

    public function render()
    {
        echo $this->getContents();
    }

    /**
     * @return array
     */
    public function getResponseHeaders()
    {
        return $this->sResponseHeader;
    }

}