<?php

namespace flyingpiranhas\mvc\views;

use flyingpiranhas\common\http\interfaces\ContentInterface;
use flyingpiranhas\mvc\views\head\interfaces\HeadInterface;
use flyingpiranhas\mvc\views\interfaces\ViewInterface;

/**
 * A View object is responsible for rendering content.
 * It holds a reference to a shared Head object, to the current ModuleMind,
 * as well as any other Views that are to be rendered as fragments.
 *
 * @category       views
 * @package        flyingpiranhas.mvc
 * @license        BSD License
 * @version        0.01
 * @since          2012-09-07
 * @author         Ivan Pintar
 */
class View implements ViewInterface, ContentInterface
{

    /** @var array */
    protected $sResponseHeader = array('Content-type: text/html; charset=uft-8');

    /** @var HeadInterface */
    protected $oHead;

    /** @var string */
    protected $sLayout = '';

    /** @var array */
    protected $aLayoutsIncludePath = array();

    /** @var string|null */
    protected $sView = null;

    /** @var array */
    protected $aViewData = array();

    /** @var array */
    protected $aViewsIncludePath = array();

    /** @var bool */
    protected $bPreRenderDone = false;

    /** @var array */
    protected $aFragments = array();

    /** @var array */
    protected $aFragmentsIncludePath = array();

    /**
     * Sets the view properties and fragments.
     *
     * @param array  $aViewData  array of view properties that are accessible in the templates as public properties, and rendering properties like view, leyout, head
     * @param string $sView
     * @param array  $aFragments array of fragments
     */
    public function __construct(array $aViewData = array(), $sView = null, array $aFragments = array())
    {
        if ($sView !== null) {
            $this->setView($sView);
        }
        if ($aFragments) {
            $this->aFragments = $aFragments;
        }
        if ($aViewData) {
            $this->setViewData($aViewData);
        }
    }

    /**
     * @return array
     */
    public function getResponseHeaders()
    {
        return $this->sResponseHeader;
    }

    /**
     * @return HeadInterface
     */
    public function getHead()
    {
        return $this->oHead;
    }

    /**
     * @param HeadInterface $oHead
     *
     * @return View
     */
    public function setHead(HeadInterface $oHead)
    {
        $this->oHead = $oHead;
        /** @var $oFragment ViewInterface */
        foreach ($this->aFragments as $oFragment) {
            $oFragment->setHead($oHead);
        }

        return $this;
    }

    /**
     * @param string $sLayout
     *
     * @return View
     */
    public function setLayout($sLayout)
    {
        $this->sLayout = $sLayout;
        return $this;
    }

    /**
     * @return array
     */
    public function getLayoutsIncludePath()
    {
        return $this->aLayoutsIncludePath;
    }

    /**
     * @param array $aLayoutsIncludePath
     *
     * @return View
     */
    public function setLayoutsIncludePath(array $aLayoutsIncludePath)
    {
        $this->aLayoutsIncludePath = $aLayoutsIncludePath;
        return $this;
    }

    /**
     * Renders the first layout template
     * on the include path with the name of $this->sLayout
     */
    private function renderLayout()
    {
        if (is_readable($this->sLayout)) {
            include $this->sLayout;
        } else {
            foreach ($this->aLayoutsIncludePath as $sDir) {
                if (is_readable($sDir . '/' . (rtrim($this->sLayout, '.php')) . '.php')) {
                    $sDir = ($sDir) ? $sDir : '.';
                    include $sDir . '/' . (rtrim($this->sLayout, '.php')) . '.php';
                    break;
                }
            }
        }
    }

    /**
     * @return string
     */
    public function getView()
    {
        return $this->sView;
    }

    /**
     * @param string $sView
     *
     * @return View
     */
    public function setView($sView)
    {
        $this->sView = $sView;
        return $this;
    }

    /**
     * @return string
     */
    public function getContents()
    {
        ob_start();
        $this->render();
        return ob_get_clean();
    }

    /**
     * @return array
     */
    public function getViewsIncludePath()
    {
        return $this->aViewsIncludePath;
    }

    /**
     * @param array $aViewsIncludePath
     *
     * @return View
     */
    public function setViewsIncludePath(array $aViewsIncludePath)
    {
        $this->aViewsIncludePath = $aViewsIncludePath;
        return $this;
    }

    /**
     * preRender() for this View and all contained Views
     * is triggered before any rendering is done.
     * This is where head params can be set, and other presentation logic can be done.
     *
     * @return View
     */
    public function preRender()
    {
        return $this;
    }

    /**
     * @return View
     */
    public final function preRenderAll()
    {
        if (!$this->bPreRenderDone) {
            $this->preRender();

            /** @var $oFragment ViewInterface */
            foreach ($this->aFragments as $oFragment) {
                $oFragment->preRenderAll();
            }
        }
        $this->bPreRenderDone = true;
        return $this;
    }

    /**
     * Renders the view with the layout.
     * If a layout is used, the layout template should call renderView(),
     * where the view should be rendered.
     */
    public function render()
    {
        $this->preRenderAll();
        if ($this->sLayout) {
            $this->renderLayout();
        } else {
            $this->renderView();
        }
    }

    /**
     * Renders the first view template
     * on the include path with the name of $this->sView
     */
    protected function renderView()
    {
        if (is_readable($this->sView)) {
            include $this->sView;
        } else {
            foreach ($this->aViewsIncludePath as $sDir) {
                $sDir = ($sDir) ? $sDir : '.';
                if (is_readable($sDir . '/' . (rtrim($this->sView, '.php')) . '.php')) {
                    include $sDir . '/' . (rtrim($this->sView, '.php')) . '.php';
                    break;
                }
            }
        }
    }

    /**
     * @return array
     */
    public function getFragments()
    {
        return $this->aFragments;
    }

    /**
     * @param string $sName
     * @param array  $aParams
     *
     * @return View
     */
    public function getFragment($sName, array $aParams = array())
    {
        if (isset($this->aFragments[$sName])) {
            $oFragment = $this->aFragments[$sName];
        } else {
            $aParams = $aParams ? $aParams : $this->aViewData;
            $oFragment = new View($aParams, $sName);
        }

        if (!$oFragment->getViewsIncludePath()) {
            $oFragment->setViewsIncludePath($this->aFragmentsIncludePath);
        }
        if (!$oFragment->getFragmentsIncludePath()) {
            $oFragment->setFragmentsIncludePath($this->aFragmentsIncludePath);
        }

        return $oFragment;
    }

    /**
     * @param array $aFragments
     *
     * @return View
     */
    public function addFragments(array $aFragments)
    {
        foreach ($aFragments as $sName => $oFragment) {
            $this->addFragment($sName, $oFragment);
        }
        return $this;
    }

    /**
     * @param string $sName
     * @param View   $oFragment
     *
     * @return View
     */
    public function addFragment($sName, View $oFragment)
    {
        $this->aFragments[$sName] = $oFragment;
        return $this;
    }

    /**
     * @return array
     */
    public function getFragmentsIncludePath()
    {
        return $this->aFragmentsIncludePath;
    }

    /**
     * @param array $aFragmentsIncludePath
     *
     * @return View
     */
    public function setFragmentsIncludePath(array $aFragmentsIncludePath)
    {
        $this->aFragmentsIncludePath = $aFragmentsIncludePath;
        return $this;
    }

    /**
     * Calls the render method on the fragment View
     * stored with the given name
     *
     * @param string $sName
     * @param array  $aParams
     */
    protected function renderFragment($sName, array $aParams = array())
    {
        $this->getFragment($sName, $aParams)->render();
    }

    /**
     * @param string $sName
     *
     * @return mixed
     */
    public function __get($sName)
    {
        return (isset($this->aViewData[$sName])) ? $this->aViewData[$sName] : null;
    }

    /**
     * Sets multiple properties.
     * If a setter exists for a property it will use that.
     * So properties that are used when rendering, like view, layout, head and similar are "reserved".
     * Other properties are view data and are saved into the $aViewProperties array.
     *
     * @param array $aViewData
     * @param bool  $bOverwrite
     *
     * @return View
     */
    public function setViewData(array $aViewData, $bOverwrite = false)
    {
        if ($bOverwrite) {
            $this->aViewData = $aViewData;
        } else {
            foreach ($aViewData as $sKey => $mVal) {
                $this->addViewData($sKey, $mVal, $bOverwrite);
            }
        }
        return $this;
    }

    /**
     * @param string $sKey
     * @param mixed  $mValue
     * @param bool   $bOverwrite
     *
     * @return View
     */
    public function addViewData($sKey, $mValue, $bOverwrite = false)
    {
        if (!$bOverwrite && isset($this->aViewData[$sKey])) {
            return $this;
        }

        $this->aViewData[$sKey] = $mValue;
        return $this;
    }

    /**
     * @return array
     */
    public function getViewData()
    {
        return $this->aViewData;
    }

}