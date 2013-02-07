<?php

namespace flyingpiranhas\mvc\views\interfaces;

use flyingpiranhas\mvc\views\head\interfaces\HeadInterface;

/**
 * All Views (fragments and custom View extensions)
 * should implement this interface
 *
 * @category       views
 * @package        flyingpiranhas.mvc
 * @license        Apache-2.0
 * @version        0.01
 * @since          2012-09-07
 * @author         Ivan Pintar
 */
interface ViewInterface
{

    /**
     * @return array
     */
    public function getResponseHeaders();

    /**
     * @return HeadInterface
     */
    public function getHead();


    /**
     * @param HeadInterface $oHead
     *
     * @return ViewInterface
     */
    public function setHead(HeadInterface $oHead);

    /**
     * @param string $sLayout
     *
     * @return ViewInterface
     */
    public function setLayout($sLayout);

    /**
     * @return array
     */
    public function getLayoutsIncludePath();

    /**
     * @param array $aLayoutsIncludePath
     *
     * @return ViewInterface
     */
    public function setLayoutsIncludePath(array $aLayoutsIncludePath);

    /**
     * @return string
     */
    public function getView();

    /**
     * @param string $sView
     *
     * @return ViewInterface
     */
    public function setView($sView);

    /**
     * @return array
     */
    public function getViewsIncludePath();

    /**
     * @param array $aViewsIncludePath
     *
     * @return ViewInterface
     */
    public function setViewsIncludePath(array $aViewsIncludePath);

    /**
     * @return array
     */
    public function getFragmentsIncludePath();

    /**
     * @param array $aFragmentsIncludePath
     *
     * @return ViewInterface
     */
    public function setFragmentsIncludePath(array $aFragmentsIncludePath);

    /**
     * @return string
     */
    public function getContents();

    public function preRender();

    public function preRenderAll();

    /**
     * Renders the view with the layout.
     * If a layout is used, the layout template should call renderView(),
     * where the view should be rendered.
     */
    public function render();

}