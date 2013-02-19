<?php

    namespace flyingpiranhas\mvc\views\head;

    use flyingpiranhas\common\utils\Validator;
    use flyingpiranhas\mvc\views\head\exceptions\HeadException;
    use flyingpiranhas\mvc\views\head\interfaces\HeadInterface;
    use flyingpiranhas\mvc\views\minify\interfaces\MinifyInterface;

    /**
     * The Head object provides methods to manipulate the info
     * which is rendered in the &lt;head&gt; element of a page.
     * All ViewModels should share a single Head object, to allow
     * for appending necessary scripts, or styles and other info.
     *
     * @category       views
     * @package        flyingpiranhas.mvc
     * @license        Apache-2.0
     * @version        0.01
     * @since          2012-09-07
     * @author         Ivan Pintar
     */
    class Head implements HeadInterface
    {

        /** @var string */
        private $sTitle = '';
        /** @var array */
        private $aBase = array();

        /** @var array */
        private $aMetaNames = array();

        /** @var array */
        private $aMetaHttpEquivs = array();

        /** @var array */
        private $aMetaProperties = array();

        /** @var array */
        private $aLinks = array();

        /** @var array */
        private $aStylesheets = array();

        /** @var array */
        private $aScripts = array();

        /** @var array */
        private $aInlineStyles = array();

        /** @var array */
        private $aInlineScripts = array();

        /** @var string */
        private $sMinifyDir = '';

        /** @var string */
        private $sPublicDir = '';

        /** @var MinifyInterface */
        private $oJsMinifier;

        /** @var MinifyInterface */
        private $oCssMinifier;

        /** @var array */
        protected $aRendered = array(
            'title'          => false,
            'base'           => false,
            'links'          => false,
            'meta-name'      => false,
            'meta-httpequiv' => false,
            'meta-prop'      => false,
            'styles'         => false,
            'inline-styles'  => false,
            'scripts'        => false,
            'inline-scripts' => false,
        );

        /**
         * @param MinifyInterface $oJsMinifier
         * @param MinifyInterface $oCssMinifier
         * @param string          $sPublicDir
         * @param string          $sMinifyDir
         */
        public function __construct(MinifyInterface $oJsMinifier, MinifyInterface $oCssMinifier, $sPublicDir, $sMinifyDir)
        {
            $this->oJsMinifier  = $oJsMinifier;
            $this->oCssMinifier = $oCssMinifier;
            $this->sPublicDir   = $sPublicDir;
            $this->sMinifyDir   = $sMinifyDir;
        }

        /**
         * Echoes the full head content, excluding <head> tags.
         * Executes the following methods in order:
         *
         * $this->renderTitle();
         * $this->renderBase();
         * $this->renderMetaName();
         * $this->renderMetaProperty();
         * $this->renderMetaHttpEquiv();
         * $this->renderLink();
         * $this->renderStylesheet();
         * $this->renderInlineStyle();
         * $this->renderScript();
         * $this->renderInlineScript();
         *
         * @return void
         */
        public function render()
        {
            $this->renderTitle();
            $this->renderBase();
            $this->renderMetaName();
            $this->renderMetaProperty();
            $this->renderMetaHttpEquiv();
            $this->renderLink();
            $this->renderStylesheet();
            $this->renderInlineStyle();
            $this->renderScript();
            $this->renderInlineScript();
        }

        /**
         * Renders all meta
         *
         * @return void
         */
        public function renderAllMeta()
        {
            $this->renderMetaName();
            $this->renderMetaProperty();
            $this->renderMetaHttpEquiv();
        }

        /**
         * Renders the inline scripts
         *
         * @return void
         */
        public function renderInlineScript()
        {
            if ($this->aRendered['inline-scripts'] === false) {
                $sInlineScriptElements = "";
                foreach ($this->aInlineScripts as $aInlineScript) {
                    $sInlineScriptElements .= PHP_EOL . '<script type="' . $aInlineScript['type'] . '"';
                    foreach ($aInlineScript['attributes'] as $sKey => $sValue) {
                        $sInlineScriptElements .= ' ' . $sKey . '="' . $sValue . '"';
                    }
                    $sInlineScriptElements .= '>';
                    $sInlineScriptElements .= $aInlineScript['scriptString'];
                    $sInlineScriptElements .= '</script>';
                }
                echo $sInlineScriptElements;
                $this->aRendered['inline-scripts'] = true;
            }
        }

        /**
         * Returns the defined title
         *
         * @return string
         */
        public function getTitle()
        {
            return $this->sTitle;
        }

        /**
         * Sets the title to the given value
         *
         * @param string $sTitle
         *
         * @return Head
         * @throws HeadException
         */
        public function setTitle($sTitle)
        {
            if (!is_string($sTitle)) {
                throw new HeadException('Invalid title');
            }
            $this->sTitle = trim($sTitle);
            return $this;
        }

        /**
         * Prepends a string with the given separator to the current title
         *
         * @param string $sTitle
         * @param string $sSeparator DEF: ' | '
         *
         * @return Head
         */
        public function prependTitle($sTitle, $sSeparator = ' | ')
        {
            if (empty($this->sTitle)) {
                $this->setTitle($sTitle);
            } else {
                $this->setTitle($sTitle . $sSeparator . $this->sTitle);
            }
            return $this;
        }

        /**
         * Appends a string with the given separator to the current title
         *
         * @param string $sTitle
         * @param string $sSeparator DEF: ' | '
         *
         * @return Head
         */
        public function appendTitle($sTitle, $sSeparator = ' | ')
        {
            if (empty($this->sTitle)) {
                $this->setTitle($sTitle);
            } else {
                $this->setTitle($this->sTitle . $sSeparator . $sTitle);
            }
            return $this;
        }

        /**
         * Sets the title to an empty string
         *
         * @return Head
         */
        public function removeTitle()
        {
            $this->sTitle = '';
            return $this;
        }

        /**
         * Renders the head title
         *
         * @return void
         */
        public function renderTitle()
        {
            if ($this->aRendered['title'] === false) {
                if (!empty($this->sTitle)) {
                    echo PHP_EOL . "<title>{$this->sTitle}</title>";
                    $this->aRendered['title'] = true;
                }
            }
        }

        /**
         * Returns the defined base
         *
         * @return array
         */
        public function getBase()
        {
            return $this->aBase;
        }

        /**
         * Sets the head base tag
         *
         * @param string $sHref
         * @param string $sTarget DEF: ''
         *
         * @return Head
         * @throws HeadException
         */
        public function setBase($sHref = '', $sTarget = '')
        {
            if (!is_string($sHref) || !is_string($sTarget)) {
                throw new HeadException('Invalid href and/or target');
            }
            $this->aBase = array(
                "href"   => trim($sHref),
                "target" => trim($sTarget)
            );
            return $this;
        }

        /**
         * Deletes the head base
         *
         * @return Head
         */
        public function removeBase()
        {
            $this->aBase = array();
            return $this;
        }

        /**
         * Renders the head base
         *
         * @return void
         */
        public function renderBase()
        {
            if ($this->aRendered['base'] === false) {
                if (Validator::full('href', $this->aBase) || Validator::full('target', $this->aBase)) {
                    echo PHP_EOL
                        . '<base ';
                    if (Validator::full('href', $this->aBase)) {
                        echo ' href="' . $this->aBase['href'] . '" ';
                    }
                    if (Validator::full('target', $this->aBase)) {
                        echo 'target="' . $this->aBase['target'] . '"';
                    }
                    echo '>';
                }
                $this->aRendered['base'] = true;
            }
        }

        /**
         * Returns defined meta names
         *
         * @return array
         */
        public function getMetaNames()
        {
            return $this->aMetaNames;
        }

        /**
         * Removes all meta names and sets the given one as the only meta name
         *
         * @param string          $sName
         * @param string          $sContent
         * @param array           $aAttributes DEF: array()
         * @param string|int|null $sKey        DEF: null; the key under which the meta name will be set
         *
         * @return Head
         */
        public function setMetaName($sName, $sContent, $aAttributes = array(), $sKey = null)
        {
            // set the meta name as the only array element
            $this->removeMetaName();
            $this->appendMetaName($sName, $sContent, $aAttributes, $sKey);
            return $this;
        }

        /**
         * The meta name with the given key will be overriden if it exists
         *
         * @param string|int $sKey
         * @param string     $sName
         * @param string     $sContent
         * @param array      $aAttributes    DEF: array()
         * @param string     $sKeyNotFound   DEF: 'APPEND'; VALUES: 'APPEND'|'PREPEND'|'IGNORE'; if key is not found the meta name will be appended, prepended or not set at all
         *
         * @return Head
         * @throws HeadException
         */
        public function overrideMetaName($sKey, $sName, $sContent, $aAttributes = array(), $sKeyNotFound = 'APPEND')
        {
            // check input
            if (!is_string($sName)) {
                throw new HeadException('Invalid name');
            }
            if (!is_string($sContent)) {
                throw new HeadException('Invalid content');
            }
            if (!is_array($aAttributes)) {
                throw new HeadException('Invalid attriutes');
            }

            // override style or append, prepend or ignore
            if (isset($this->aMetaNames[$sKey])) {
                $this->aMetaNames[$sKey] = array(
                    'name'       => $sName,
                    'content'    => $sContent,
                    'attributes' => $aAttributes
                );
            } else {
                switch ($sKeyNotFound) {
                    case 'PREPEND':
                        $this->prependMetaName($sName, $sContent, $aAttributes, $sKey);
                        break;
                    case 'APPEND':
                        $this->appendMetaName($sName, $sContent, $aAttributes, $sKey);
                        break;
                    default:
                        break;
                }
            }

            return $this;
        }

        /**
         * Prepends a meta name
         *
         * @param string          $sName
         * @param string          $sContent
         * @param array           $aAttributes DEF: array()
         * @param string|int|null $sKey        DEF: null - if a key is given, it will remove the meta name with the same key before prepending
         *
         * @return Head
         */
        public function prependMetaName($sName, $sContent, $aAttributes = array(), $sKey = null)
        {
            $this->addMeta('PREPEND', 'name', $sName, $sContent, $aAttributes, $sKey);
            return $this;
        }

        /**
         * Appends a meta name
         *
         * @param string          $sName
         * @param string          $sContent
         * @param array           $aAttributes DEF: array()
         * @param string|int|null $sKey        DEF: null - if a key is given, it will remove the meta name with the same key before appending
         *
         * @return Head
         */
        public function appendMetaName($sName, $sContent, $aAttributes = array(), $sKey = null)
        {
            $this->addMeta('APPEND', 'name', $sName, $sContent, $aAttributes, $sKey);
            return $this;
        }

        /**
         * Removes the meta name with the given key, or all meta names if no key given
         *
         * @param int|string|null $sKey DEF: null
         *
         * @return Head
         */
        public function removeMetaName($sKey = null)
        {
            if ($sKey !== null) {
                if (isset($this->aMetaNames[$sKey])) {
                    unset($this->aMetaNames[$sKey]);
                }
            } else {
                $this->aMetaNames = array();
            }
            return $this;
        }

        /**
         * Renders the meta name elements
         *
         * @return void
         */
        public function renderMetaName()
        {
            if ($this->aRendered['meta-name'] === false) {
                $sMetaNameElements = '';
                foreach ($this->aMetaNames as $sMetaName) {
                    $sMetaNameElements .= PHP_EOL . '<meta name="' . $sMetaName['name'] . '" content="' . $sMetaName['content'] . '"';
                    foreach ($sMetaName['attributes'] as $sKey => $sValue) {
                        $sMetaNameElements .= ' ' . $sKey . '="' . $sValue . '"';
                    }
                    $sMetaNameElements .= ' />';
                }
                echo $sMetaNameElements;
                $this->aRendered['meta-name'] = true;
            }
        }

        /**
         * Returns defined meta http equivs
         *
         * @return array
         */
        public function getMetaHttpEquivs()
        {
            return $this->aMetaHttpEquivs;
        }

        /**
         * Removes all meta http-equivs and sets the given one as the only meta http-equiv
         *
         * @param string          $sHttpEquivName
         * @param string          $sContent
         * @param array           $aAttributes DEF: array()
         * @param string|int|null $sKey        DEF: null; the key under which the meta http-equiv will be set
         *
         * @return Head
         */
        public function setMetaHttpEquiv($sHttpEquivName, $sContent, $aAttributes = array(), $sKey = null)
        {
            // set the meta name as the only array element
            $this->removeMetaHttpEquiv();
            $this->appendMetaHttpEquiv($sHttpEquivName, $sContent, $aAttributes, $sKey);
            return $this;
        }

        /**
         * The meta http-equiv with the given key will be overriden if it exists
         *
         * @param string|int $sKey
         * @param string     $sHttpEquiv
         * @param string     $sContent
         * @param array      $aAttributes DEF: array()
         * @param string     $sNotFound   DEF: 'APPEND'; VALUES: 'APPEND'|'PREPEND'|'IGNORE'; if key is not found the meta http-equiv will be appended, prepended or not set at all
         *
         * @return Head
         * @throws HeadException
         */
        public function overrideMetaHttpEquiv($sKey, $sHttpEquiv, $sContent, $aAttributes = array(), $sNotFound = 'APPEND')
        {
            // check input
            if (!is_string($sHttpEquiv)) {
                throw new HeadException('Invalid http-equiv');
            }
            if (!is_string($sContent)) {
                throw new HeadException('Invalid content');
            }
            if (!is_array($aAttributes)) {
                throw new HeadException('Invalid attriutes');
            }

            // override style or append, prepend or ignore
            if (isset($this->aMetaHttpEquivs[$sKey])) {
                $this->aMetaHttpEquivs[$sKey] = array(
                    'httpEquiv'  => $sHttpEquiv,
                    'content'    => $sContent,
                    'attributes' => $aAttributes
                );
            } else {
                switch ($sNotFound) {
                    case 'PREPEND':
                        $this->prependMetaHttpEquiv($sHttpEquiv, $sContent, $aAttributes, $sKey);
                        break;
                    case 'APPEND':
                        $this->appendMetaHttpEquiv($sHttpEquiv, $sContent, $aAttributes, $sKey);
                        break;
                    default:
                        break;
                }
            }

            return $this;
        }

        /**
         * Prepends a meta http-equiv
         *
         * @param string          $sHttpEquiv
         * @param string          $sContent
         * @param array           $aAttributes DEF: array()
         * @param string|int|null $sKey        DEF: null - if a key is given, it will remove the meta http-equiv with the same key before prepending
         *
         * @return Head
         */
        public function prependMetaHttpEquiv($sHttpEquiv, $sContent, $aAttributes = array(), $sKey = null)
        {
            $this->addMeta('PREPEND', 'httpEquiv', $sHttpEquiv, $sContent, $aAttributes, $sKey);
            return $this;
        }

        /**
         * Appends a meta http-equiv
         *
         * @param string          $sHttpEquiv
         * @param string          $sContent
         * @param array           $aAttributes DEF: array()
         * @param string|int|null $sKey        DEF: null - if a key is given, it will remove the meta http-equiv with the same key before appending
         *
         * @return Head
         */
        public function appendMetaHttpEquiv($sHttpEquiv, $sContent, $aAttributes = array(), $sKey = null)
        {
            $this->addMeta('APPEND', 'httpEquiv', $sHttpEquiv, $sContent, $aAttributes, $sKey);
            return $this;
        }

        /**
         * Removes the meta http-equiv with the given key, or all meta http-equivs if no key given
         *
         * @param int|string|null $sKey DEF: null
         *
         * @return Head
         */
        public function removeMetaHttpEquiv($sKey = null)
        {
            if ($sKey !== null) {
                if (isset($this->aMetaHttpEquivs[$sKey])) {
                    unset($this->aMetaHttpEquivs[$sKey]);
                }
            } else {
                $this->aMetaHttpEquivs = array();
            }
            return $this;
        }

        /**
         * Renders the meta http-equiv elements
         *
         * @return void
         */
        public function renderMetaHttpEquiv()
        {
            if ($this->aRendered['meta-httpequiv'] === false) {
                $sMetaHttpEquivElements = '';
                foreach ($this->aMetaHttpEquivs as $sHttpEquiv) {
                    $sMetaHttpEquivElements .= PHP_EOL . '<meta http-equiv="' . $sHttpEquiv['httpEquiv'] . '" content="' . $sHttpEquiv['content'] . '"';
                    foreach ($sHttpEquiv['attributes'] as $sKey => $sValue) {
                        $sMetaHttpEquivElements .= ' ' . $sKey . '="' . $sValue . '"';
                    }
                    $sMetaHttpEquivElements .= ' />';
                }
                echo $sMetaHttpEquivElements;
                $this->aRendered['meta-httpequiv'] = true;
            }
        }

        /**
         * Returns the defined meta properties
         *
         * @return array
         */
        public function getMetaProperties()
        {
            return $this->aMetaProperties;
        }

        /**
         * Removes all meta properties and sets the given one as the only meta property
         *
         * @param string          $sProperty
         * @param string          $sContent
         * @param array           $aAttributes DEF: array()
         * @param string|int|null $sKey        DEF: null; the key under which the meta property will be set
         *
         * @return Head
         */
        public function setMetaProperty($sProperty, $sContent, $aAttributes = array(), $sKey = null)
        {
            // set the meta name as the only array element
            $this->removeMetaProperty();
            $this->appendMetaProperty($sProperty, $sContent, $aAttributes, $sKey);
            return $this;
        }

        /**
         * The meta property with the given key will be overriden if it exists
         *
         * @param string|int $sKey
         * @param string     $sProperty
         * @param string     $sContent
         * @param array      $aAttributes DEF: array()
         * @param string     $sNotFound   DEF: 'APPEND'; VALUES: 'APPEND'|'PREPEND'|'IGNORE'; if key is not found the meta property will be appended, prepended or not set at all
         *
         * @return Head
         * @throws HeadException
         */
        public function overrideMetaProperty($sKey, $sProperty, $sContent, $aAttributes = array(), $sNotFound = 'APPEND')
        {
            // check input
            if (!is_string($sProperty)) {
                throw new HeadException('Invalid property');
            }
            if (!is_string($sContent)) {
                throw new HeadException('Invalid content');
            }
            if (!is_array($aAttributes)) {
                throw new HeadException('Invalid attriutes');
            }

            // override style or append, prepend or ignore
            if (isset($this->aMetaNames[$sKey])) {
                $this->aMetaNames[$sKey] = array(
                    'property'   => $sProperty,
                    'content'    => $sContent,
                    'attributes' => $aAttributes
                );
            } else {
                switch ($sNotFound) {
                    case 'PREPEND':
                        $this->prependMetaProperty($sProperty, $sContent, $aAttributes, $sKey);
                        break;
                    case 'APPEND':
                        $this->appendMetaProperty($sProperty, $sContent, $aAttributes, $sKey);
                        break;
                    default:
                        break;
                }
            }

            return $this;
        }

        /**
         * Prepends a meta property
         *
         * @param string          $sProperty
         * @param string          $sContent
         * @param array           $aAttributes DEF: array()
         * @param string|int|null $sKey        DEF: null - if a key is given, it will remove the meta property with the same key before prepending
         *
         * @return Head
         */
        public function prependMetaProperty($sProperty, $sContent, $aAttributes = array(), $sKey = null)
        {
            $this->addMeta('PREPEND', 'property', $sProperty, $sContent, $aAttributes, $sKey);
            return $this;
        }

        /**
         * Appends a meta property
         *
         * @param string          $sProperty
         * @param string          $sContent
         * @param array           $aAttributes DEF: array()
         * @param string|int|null $sKey        DEF: null - if a key is given, it will remove the meta property with the same key before apending
         *
         * @return Head
         */
        public function appendMetaProperty($sProperty, $sContent, $aAttributes = array(), $sKey = null)
        {
            $this->addMeta('APPEND', 'property', $sProperty, $sContent, $aAttributes, $sKey);
            return $this;
        }

        /**
         * Removes the meta property with the given key, or all meta properties if no key given
         *
         * @param int|string|null $sKey DEF: null
         *
         * @return Head
         */
        public function removeMetaProperty($sKey = null)
        {
            if ($sKey !== null) {
                if (isset($this->aMetaProperties[$sKey])) {
                    unset($this->aMetaProperties[$sKey]);
                }
            } else {
                $this->aMetaProperties = array();
            }
            return $this;
        }

        /**
         * Renders the meta property elements
         *
         * @return void
         */
        public function renderMetaProperty()
        {
            if ($this->aRendered['meta-prop'] === false) {
                $sMetaPropertyElements = '';
                foreach ($this->aMetaProperties as $sMetaProperty) {
                    $sMetaPropertyElements .= PHP_EOL . '<meta property="' . $sMetaProperty['property'] . '" content="' . $sMetaProperty['content'] . '"';
                    foreach ($sMetaProperty['attributes'] as $sKey => $sValue) {
                        $sMetaPropertyElements .= ' ' . $sKey . '="' . $sValue . '"';
                    }
                    $sMetaPropertyElements .= ' />';
                }
                echo $sMetaPropertyElements;
                $this->aRendered['meta-prop'] = true;
            }
        }

        /**
         * Returns the defined links for head
         *
         * @return array
         */
        public function getLinks()
        {
            return $this->aLinks;
        }

        /**
         * Adds (prepends or appends) a link element
         *
         * @param string          $sPrependOrAppend VALUES: 'PREPEND'|'APPEND'; defines the position of the element
         * @param string          $sHref
         * @param array           $aAttributes      DEF: array()
         * @param string|int|null $sKey             DEF: null
         *
         * @return Head
         * @throws HeadException
         */
        function addLink($sPrependOrAppend, $sHref, $aAttributes = array(), $sKey = null)
        {
            // check href
            $sHref     = trim($sHref, '/');
            $sLocation = $this->sPublicDir . '/' . $sHref;
            if (preg_match('/^(http)|(https):\/\//', $sHref)) {
                $sLocation = $sHref;
            } else {
                $sHref = '/' . $sHref;
            }

            if (!is_readable($sLocation)) {
                throw new HeadException('Link not readable');
            }

            // prepare link to add
            if ($sKey !== null) {
                $this->removeLink($sKey);
                $aNewLink = array(
                    $sKey => array(
                        'href'       => $sHref,
                        'attributes' => $aAttributes
                    )
                );
            } else {
                $aNewLink = array(
                    array(
                        'href'       => $sHref,
                        'attributes' => $aAttributes
                    )
                );
            }

            if ($sPrependOrAppend == 'PREPEND') {
                // prepend link
                $this->aLinks = array_merge($aNewLink, $this->aLinks);
            } else {
                // append link
                $this->aLinks = array_merge($this->aLinks, $aNewLink);
            }
            return $this;
        }

        /**
         * Removes all links and sets the given one as the only link
         *
         * @param string          $sHref
         * @param array           $aAttributes DEF: array()
         * @param string|int|null $sKey        DEF: null; the key under which the link will be set
         *
         * @return Head
         */
        public function setLink($sHref, $aAttributes = array(), $sKey = null)
        {
            // set the link as the only array element
            $this->removeLink();
            $this->appendLink($sHref, $aAttributes, $sKey);
            return $this;
        }

        /**
         * The link with the given key will be overriden if it exists
         *
         * @param string|int $sKey
         * @param string     $sHref
         * @param array      $aAttributes DEF: array()
         * @param string     $sNotFound   DEF: 'APPEND'; VALUES: 'APPEND'|'PREPEND'|'IGNORE'; if key is not found the link will be appended, prepended or not set at all
         *
         * @return Head
         * @throws HeadException
         */
        public function overrideLink($sKey, $sHref, $aAttributes = array(), $sNotFound = 'APPEND')
        {
            // check href
            $sHref     = trim($sHref, '/');
            $sLocation = $this->sPublicDir . '/' . $sHref;
            if (preg_match('/^(http)|(https):\/\//', $sHref)) {
                $sLocation = $sHref;
            } else {
                $sHref = '/' . $sHref;
            }

            if (!is_readable($sLocation)) {
                throw new HeadException('Link not readable');
            }

            // override style or append, prepend or ignore
            if (isset($this->aLinks[$sKey])) {
                $this->aLinks[$sKey] = array(
                    'href'       => $sHref,
                    'attributes' => $aAttributes
                );
            } else {
                switch ($sNotFound) {
                    case 'PREPEND':
                        $this->prependLink($sHref, $aAttributes, $sKey);
                        break;
                    case 'APPEND':
                        $this->appendLink($sHref, $aAttributes, $sKey);
                        break;
                    default:
                        break;
                }
            }

            return $this;
        }

        /**
         * Prepends a link
         *
         * @param string          $sHref
         * @param array           $aAttributes DEF: array()
         * @param string|int|null $sKey        DEF: null - if a key is given, it will remove the link with the same key before prepending
         *
         * @return Head
         */
        public function prependLink($sHref, $aAttributes = array(), $sKey = null)
        {
            $this->addLink('PREPEND', $sHref, $aAttributes, $sKey);
            return $this;
        }

        /**
         * Appends a link
         *
         * @param string          $sHref
         * @param array           $aAttributes DEF: array()
         * @param string|int|null $sKey        DEF: null - if a key is given, it will remove the link with the same key before appending
         *
         * @return Head
         */
        public function appendLink($sHref, $aAttributes = array(), $sKey = null)
        {
            $this->addLink('APPEND', $sHref, $aAttributes, $sKey);
            return $this;
        }

        /**
         * Removes the link with the given key, or all links if no key given
         *
         * @param int|string|null $sKey DEF: null
         *
         * @return Head
         */
        public function removeLink($sKey = null)
        {
            if ($sKey !== null) {
                if (isset($this->aLinks[$sKey])) {
                    unset($this->aLinks[$sKey]);
                }
            } else {
                $this->aLinks = array();
            }
            return $this;
        }

        /**
         * Renders the link elements
         *
         * @return void
         */
        public function renderLink()
        {
            if ($this->aRendered['links'] === false) {
                $sLinkElements = "";
                foreach ($this->aLinks as $aLink) {
                    $sLinkElements .= PHP_EOL . '<link href="' . $aLink['href'] . '"';
                    foreach ($aLink['attributes'] as $sKey => $sValue) {
                        $sLinkElements .= ' ' . $sKey . '="' . $sValue . '"';
                    }
                    $sLinkElements .= '/>';
                }
                echo $sLinkElements;
                $this->aRendered['links'] = true;
            }
        }


        /**
         * Returns defined stylesheets
         *
         * @return array
         */
        public function getStylesheets()
        {
            return $this->aStylesheets;
        }

        /**
         * Adds (prepends or appends) a stylesheet link
         *
         * @param string          $sPrependOrAppend VALUES: 'PREPEND'|'APPEND'; defines the position of the element
         * @param string          $sHref
         * @param bool            $bMinify          DEF: true
         * @param array           $aAttributes      DEF: array()
         * @param string|int|null $sKey             DEF: null
         *
         * @return Head
         * @throws HeadException
         */
        private function addStylesheet($sPrependOrAppend, $sHref, $bMinify = true, $aAttributes = array(), $sKey = null)
        {
            // check href
            $sHref     = trim($sHref, '/');
            $sLocation = $this->sPublicDir . '/' . $sHref;
            if (preg_match('/^(http)|(https):\/\//', $sHref)) {
                $bMinify   = false;
                $sLocation = $sHref;
            } else {
                $sHref = '/' . $sHref;
            }

            if (!is_readable($sLocation)) {
                throw new HeadException('Stylesheet not readable at ' . $sLocation);
            }

            // prepare link to add
            if ($sKey !== null) {
                $this->removeStylesheet($sKey);
                $aNewStylesheet = array(
                    $sKey => array(
                        'href'       => $sHref,
                        'minify'     => $bMinify,
                        'attributes' => $aAttributes,
                        'location'   => $sLocation
                    )
                );
            } else {
                $aNewStylesheet = array(
                    array(
                        'href'       => $sHref,
                        'minify'     => $bMinify,
                        'attributes' => $aAttributes,
                        'location'   => $sLocation
                    )
                );
            }

            if ($sPrependOrAppend == 'PREPEND') {
                // prepend link
                $this->aStylesheets = array_merge($aNewStylesheet, $this->aStylesheets);
            } else {
                // append link
                $this->aStylesheets = array_merge($this->aStylesheets, $aNewStylesheet);
            }
            return $this;
        }

        /**
         * Removes all stylesheet links and sets the given one as the only stylesheet link
         *
         * @param string          $sHref
         * @param bool            $bMinify     DEF: true
         * @param array           $aAttributes DEF: array()
         * @param string|int|null $sKey        DEF: null; the key under which the stylesheet link will be set
         *
         * @return Head
         */
        public function setStylesheet($sHref, $bMinify = true, $aAttributes = array(), $sKey = null)
        {
            // set the link as the only array element
            $this->removeStylesheet();
            $this->appendStylesheet($sHref, $bMinify, $aAttributes, $sKey);
            return $this;
        }

        /**
         * The stylesheet link with the given key will be overriden if it exists
         *
         * @param string|int $sKey
         * @param string     $sHref
         * @param bool       $bMinify     DEF: true
         * @param array      $aAttributes DEF: array()
         * @param string     $sNotFound   DEF: 'APPEND'; VALUES: 'APPEND'|'PREPEND'|'IGNORE'; if key is not found the stylesheet link will be appended, prepended or not set at all
         *
         * @return Head
         * @throws HeadException
         */
        public function overrideStylesheet($sKey, $sHref, $bMinify = true, $aAttributes = array(), $sNotFound = 'APPEND')
        {
            // check href
            $sHref     = trim($sHref, '/');
            $sLocation = $this->sPublicDir . '/' . $sHref;
            if (preg_match('/^(http)|(https):\/\//', $sHref)) {
                $bMinify   = false;
                $sLocation = $sHref;
            } else {
                $sHref = '/' . $sHref;
            }

            if (!is_readable($sLocation)) {
                throw new HeadException('Stylesheet not readable');
            }

            // override stylesheet link or append, prepend or ignore
            if (isset($this->aStylesheets[$sKey])) {
                $this->aStylesheets[$sKey] = array(
                    'href'       => $sHref,
                    'minify'     => $bMinify,
                    'attributes' => $aAttributes,
                    'location'   => $sLocation
                );
            } else {
                switch ($sNotFound) {
                    case 'PREPEND':
                        $this->prependStylesheet($sHref, $bMinify, $aAttributes, $sKey);
                        break;
                    case 'APPEND':
                        $this->appendStylesheet($sHref, $bMinify, $aAttributes, $sKey);
                        break;
                    default:
                        break;
                }
            }

            return $this;
        }

        /**
         * Prepends a stylesheet link
         *
         * @param string          $sHref
         * @param bool            $bMinify     DEF: true
         * @param array           $aAttributes DEF: array()
         * @param string|int|null $sKey        DEF: null - if a key is given, it will remove the stylesheet link with the same key before prepending
         *
         * @return Head
         */
        public function prependStylesheet($sHref, $bMinify = true, $aAttributes = array(), $sKey = null)
        {
            $this->addStylesheet('PREPEND', $sHref, $bMinify, $aAttributes, $sKey);
            return $this;
        }

        /**
         * Appends a stylesheet link
         *
         * @param string          $sHref
         * @param bool            $bMinify     DEF: true
         * @param array           $aAttributes DEF: array()
         * @param string|int|null $sKey        DEF: null - if a key is given, it will remove the stylesheet link with the same key before appending
         *
         * @return Head
         */
        public function appendStylesheet($sHref, $bMinify = true, $aAttributes = array(), $sKey = null)
        {
            $this->addStylesheet('APPEND', $sHref, $bMinify, $aAttributes, $sKey);
            return $this;
        }

        /**
         * Removes the stylesheet with the given key, or all stylesheets if no key given
         *
         * @param int|string|null $sKey DEF: null
         *
         * @return Head
         */
        public function removeStylesheet($sKey = null)
        {
            if ($sKey !== null) {
                if (isset($this->aStylesheets[$sKey])) {
                    unset($this->aStylesheets[$sKey]);
                }
            } else {
                $this->aStylesheets = array();
            }
            return $this;
        }

        /**
         * Renders the stylesheet link elements
         *
         * @return void
         */
        public function renderStylesheet()
        {
            $aToRender = array();
            $aToMinify = array();
            foreach ($this->aStylesheets as $aStylesheet) {
                if (!$aStylesheet['minify']) {
                    // minifiy previously prepared scripts
                    if (!empty($aToMinify)) {
                        $aToRender[] = $this->minifyCss($aToMinify);
                        $aToMinify   = array();
                    }
                    $sStylesheetElement = PHP_EOL . '<link href="' . $aStylesheet['href'] . '" rel="stylesheet"';
                    foreach ($aStylesheet['attributes'] as $sKey => $sValue) {
                        $sStylesheetElement .= ' ' . $sKey . '="' . $sValue . '"';
                    }
                    $sStylesheetElement .= '/>';
                    $aToRender[] = $sStylesheetElement;
                } else {
                    $aFileInfo   = pathinfo($aStylesheet['location']);
                    $aToMinify[] = array(
                        'extension' => 'css',
                        'filename'  => $aFileInfo['filename'],
                        'location'  => $aStylesheet['location']
                    );
                }
            }

            // minifiy previously prepared scripts
            if (!empty($aToMinify)) {
                $aToRender[] = $this->minifyCss($aToMinify);
            }

            $sStylesheetElements = "";
            foreach ($aToRender as $sStylesheetElement) {
                $sStylesheetElements .= $sStylesheetElement;
            }
            echo $sStylesheetElements;
        }

        /**
         * @return array
         */
        public function getScripts()
        {
            return $this->aScripts;
        }

        /**
         * Adds (prepends or appends) a script
         *
         * @param string          $sPrependOrAppend VALUES: 'PREPEND'|'APPEND'; defines the position of the element
         * @param string          $sSrc
         * @param bool            $bMinify          DEF: true
         * @param string          $sType            DEF: 'text/javascript'
         * @param array           $aAttributes      DEF: array()
         * @param string|int|null $sKey             DEF: null
         *
         * @return Head
         * @throws HeadException
         */
        private function addScript($sPrependOrAppend, $sSrc, $bMinify = true, $sType = 'text/javascript', $aAttributes = array(), $sKey = null)
        {
            // check src
            $sSrc      = trim($sSrc, '/');
            $sLocation = $this->sPublicDir . '/' . $sSrc;
            if (preg_match('/^(http)|(https):\/\//', $sSrc)) {
                $bMinify   = false;
                $sLocation = $sSrc;
            } else {
                $sSrc = '/' . $sSrc;
            }

            if (!is_readable($sLocation)) {
                throw new HeadException('Script not readable at ' . $sLocation);
            }

            // prepare link to add
            $bMinify = (!preg_match('/(\.js)$/', $sSrc)) ? false : $bMinify;
            if ($sKey !== null) {
                $this->removeStylesheet($sKey);
                $aNewScript = array(
                    $sKey => array(
                        'src'        => $sSrc,
                        'type'       => $sType,
                        'minify'     => $bMinify,
                        'attributes' => $aAttributes,
                        'location'   => $sLocation
                    )
                );
            } else {
                $aNewScript = array(
                    array(
                        'src'        => $sSrc,
                        'type'       => $sType,
                        'minify'     => $bMinify,
                        'attributes' => $aAttributes,
                        'location'   => $sLocation
                    )
                );
            }

            if ($sPrependOrAppend == 'PREPEND') {
                // prepend link
                $this->aScripts = array_merge($aNewScript, $this->aScripts);
            } else {
                // append link
                $this->aScripts = array_merge($this->aScripts, $aNewScript);
            }
            return $this;
        }

        /**
         * Removes all scripts and sets the given one as the only script
         *
         * @param string          $sSrc
         * @param bool            $bMinify     DEF: true
         * @param string          $sType       DEF: 'text/javascript'
         * @param array           $aAttributes DEF: array()
         * @param string|int|null $sKey        DEF: null; the key under which the script will be set
         *
         * @return Head
         */
        public function setScript($sSrc, $bMinify = true, $sType = 'text/javascript', $aAttributes = array(), $sKey = null)
        {
            // set the script as the only array element
            $this->removeScript();
            $this->appendScript($sSrc, $bMinify, $sType, $aAttributes, $sKey);
            return $this;
        }

        /**
         * The script with the given key will be overriden if it exists
         *
         * @param string|int $sKey
         * @param string     $sSrc
         * @param bool       $bMinify     DEF: true
         * @param string     $sType       DEF: 'text/javascript'
         * @param array      $aAttributes DEF: array()
         * @param string     $sNotFound   DEF: 'APPEND'; VALUES: 'APPEND'|'PREPEND'|'IGNORE'; if key is not found the script will be appended, prepended or not set at all
         *
         * @return Head
         * @throws HeadException
         */
        public function overrideScript($sKey, $sSrc, $bMinify = true, $sType = 'text/javascript', $aAttributes = array(), $sNotFound = 'APPEND')
        {
            // check src
            $sSrc      = trim($sSrc, '/');
            $sLocation = $this->sPublicDir . '/' . $sSrc;
            if (preg_match('/^(http)|(https):\/\//', $sSrc)) {
                $bMinify   = false;
                $sLocation = $sSrc;
            } else {
                $sSrc = '/' . $sSrc;
            }

            if (!is_readable($sLocation)) {
                throw new HeadException('Stylesheet not readable');
            }

            // override script or append, prepend or ignore
            $bMinify = (!preg_match('/(\.js)$/', $sSrc)) ? false : $bMinify;
            if (isset($this->aScripts[$sKey])) {
                $this->aScripts[$sKey] = array(
                    'src'        => $sSrc,
                    'type'       => $sType,
                    'minify'     => $bMinify,
                    'attributes' => $aAttributes,
                    'location'   => $sLocation
                );
            } else {
                switch ($sNotFound) {
                    case 'PREPEND':
                        $this->prependScript($sSrc, $sType, $bMinify, $aAttributes, $sKey);
                        break;
                    case 'APPEND':
                        $this->appendScript($sSrc, $sType, $bMinify, $aAttributes, $sKey);
                        break;
                    default:
                        break;
                }
            }

            return $this;
        }

        /**
         * Prepends a script
         *
         * @param string          $sSrc
         * @param bool            $bMinify     DEF: true
         * @param string          $sType       DEF: 'text/javascript'
         * @param array           $aAttributes DEF: array()
         * @param string|int|null $sKey        DEF: null - if a key is given, it will remove the script with the same key before prepending
         *
         * @return Head
         */
        public function prependScript($sSrc, $bMinify = true, $sType = 'text/javascript', $aAttributes = array(), $sKey = null)
        {
            $this->addScript('PREPEND', $sSrc, $bMinify, $sType, $aAttributes, $sKey);
            return $this;
        }

        /**
         * Appends a script
         *
         * @param string          $sSrc
         * @param bool            $bMinify     DEF: true
         * @param string          $sType       DEF: 'text/javascript'
         * @param array           $aAttributes DEF: array()
         * @param string|int|null $sKey        DEF: null - if a key is given, it will remove the script with the same key before appending
         *
         * @return Head
         */
        public function appendScript($sSrc, $bMinify = true, $sType = 'text/javascript', $aAttributes = array(), $sKey = null)
        {
            $this->addScript('APPEND', $sSrc, $bMinify, $sType, $aAttributes, $sKey);
            return $this;
        }

        /**
         * Removes the script with the given key, or all scripts if no key given
         *
         * @param int|string|null $sKey DEF: null
         *
         * @return Head
         */
        public function removeScript($sKey = null)
        {
            if ($sKey !== null) {
                if (isset($this->aScripts[$sKey])) {
                    unset($this->aScripts[$sKey]);
                }
            } else {
                $this->aScripts = array();
            }
            return $this;
        }

        /**
         * Renders the script elements
         *
         * @return void
         */
        public function renderScript()
        {
            if ($this->aRendered['scripts'] === false) {
                $aToRender = array();
                $aToMinify = array();
                foreach ($this->aScripts as $aScript) {
                    if (!$aScript['minify']) {
                        // minify previously prepared scripts
                        if (!empty($aToMinify)) {
                            $aToRender[] = $this->minifyJavaScript($aToMinify);
                            $aToMinify   = array();
                        }
                        $sScriptElement = PHP_EOL . '<script src="' . $aScript['src'] . '" type="' . $aScript['type'] . '"';
                        foreach ($aScript['attributes'] as $sKey => $sValue) {
                            $sScriptElement .= ' ' . $sKey . '="' . $sValue . '"';
                        }
                        $sScriptElement .= '></script>';
                        $aToRender[] = $sScriptElement;
                    } else {
                        $aFileInfo   = pathinfo($aScript['location']);
                        $aToMinify[] = array(
                            'extension' => 'js',
                            'filename'  => $aFileInfo['filename'],
                            'location'  => $aScript['location']
                        );
                    }
                }

                // minify previously prepared scripts
                if (!empty($aToMinify)) {
                    $aToRender[] = $this->minifyJavaScript($aToMinify);
                }

                $sScriptElements = "";
                foreach ($aToRender as $sScriptElement) {
                    $sScriptElements .= $sScriptElement;
                }
                echo $sScriptElements;
                $this->aRendered['scripts'] = true;
            }
        }

        /**
         * @return array
         */
        public function getInlineStyles()
        {
            return $this->aInlineStyles;
        }

        /**
         * @return array
         */
        public function getInlineScripts()
        {
            return $this->aInlineScripts;
        }

        /**
         * Set the directory where the minified scripts and stylesheets will be stored.
         * This directory is always relative to the public directory
         *
         * @param string $sMinifyDir
         *
         * @return Head
         * @throws HeadException
         */
        public function setMinifyDir($sMinifyDir)
        {
            $this->sMinifyDir = $sMinifyDir;
            return $this;
        }

        /**
         * Set the public directory
         *
         * @param string $sPublicDir
         *
         * @return Head
         * @throws HeadException
         */
        public function setPublicDir($sPublicDir)
        {
            if (!is_dir($sPublicDir)) {
                throw new HeadException('Public dir is not a directory');
            }
            $this->sPublicDir = $sPublicDir;
            return $this;
        }


        /**
         * Adds (prepends or appends) a meta attribute
         *
         * @param string          $sPrependOrAppend VALUES: 'PREPEND'|'APPEND'; defines the position of the element
         * @param string          $sType            VALUES: 'name'|'httpEquiv'|'property'; defines the key of the meta element
         * @param string          $sTypeValue
         * @param string          $sContent
         * @param array           $aAttributes      DEF: array()
         * @param string|int|null $sKey             DEF: null
         *
         * @return Head
         * @throws HeadException
         */
        public function addMeta($sPrependOrAppend, $sType, $sTypeValue, $sContent, $aAttributes = array(), $sKey = null)
        {
            // check input
            if (!is_string($sType)) {
                throw new HeadException('Invalid type');
            }
            if (!is_string($sTypeValue)) {
                throw new HeadException('Invalid type value');
            }
            if (!is_string($sContent)) {
                throw new HeadException('Invalid content');
            }
            if (!is_array($aAttributes)) {
                throw new HeadException('Invalid attributes');
            }

            // prepare meta name to prepend
            if ($sKey !== null) {
                $this->removeMetaName($sKey);
                $aNewMetaName = array(
                    $sKey => array(
                        $sType       => $sTypeValue,
                        'content'    => $sContent,
                        'attributes' => $aAttributes
                    )
                );
            } else {
                $aNewMetaName = array(
                    array(
                        $sType       => $sTypeValue,
                        'content'    => $sContent,
                        'attributes' => $aAttributes
                    )
                );
            }

            $aMetaValues = 'aMeta' . ucfirst(str_replace('property', 'propertie', $sType)) . 's';
            if ($sPrependOrAppend == 'PREPEND') {
                // prepend style
                $this->$aMetaValues = array_merge($aNewMetaName, $this->$aMetaValues);
            } else {
                // append style
                $this->$aMetaValues = array_merge($this->$aMetaValues, $aNewMetaName);
            }
            return $this;
        }


        /**
         * Adds (prepends or appends) an inline style
         *
         * @param string          $sPrependOrAppend VALUES: 'PREPEND'|'APPEND'; defines the position of the element
         * @param string          $sCssString
         * @param array           $aAttributes      DEF: array()
         * @param string|int|null $sKey             DEF: null
         *
         * @throws HeadException
         * @return Head
         */
        public function addInlineStyle($sPrependOrAppend, $sCssString, $aAttributes = array(), $sKey = null)
        {
            // check input
            if (!is_string($sCssString)) {
                throw new HeadException('Invalid css string');
            }
            if (!is_array($aAttributes)) {
                throw new HeadException('Invalid attriutes');
            }

            // prepare style to prepend
            if ($sKey !== null) {
                $this->removeInlineStyle($sKey);
                $aNewStyle = array(
                    $sKey => array(
                        'cssString'  => $sCssString,
                        'attributes' => $aAttributes
                    )
                );
            } else {
                $aNewStyle = array(
                    array(
                        'cssString'  => $sCssString,
                        'attributes' => $aAttributes
                    )
                );
            }

            if ($sPrependOrAppend == 'PREPEND') {
                // prepend style
                $this->aInlineStyles = array_merge($aNewStyle, $this->aInlineStyles);
            } else {
                // append style
                $this->aInlineStyles = array_merge($this->aInlineStyles, $aNewStyle);
            }
            return $this;
        }

        /**
         * Removes all styles and sets the given one as the only style
         *
         * @param string          $sCssString
         * @param array           $aAttributes DEF: array()
         * @param string|int|null $sKey        DEF: null; the key under which the style will be set
         *
         * @return Head
         */
        public function setInlineStyle($sCssString, $aAttributes = array(), $sKey = null)
        {
            // set the inline style as the only array element
            $this->removeInlineStyle();
            $this->appendInlineStyle($sCssString, $aAttributes, $sKey);
            return $this;
        }

        /**
         * The style with the given key will be overriden if it exists
         *
         * @param string|int $sKey
         * @param string     $sCssString
         * @param array      $aAttributes DEF: array()
         * @param string     $sNotFound   DEF: 'APPEND'; VALUES: 'APPEND'|'PREPEND'|'IGNORE'; if key is not found the style will be appended, prepended or not set at all
         *
         * @return Head
         * @throws HeadException
         */
        public function overrideInlineStyle($sKey, $sCssString, $aAttributes = array(), $sNotFound = 'APPEND')
        {
            // check input
            if (!is_string($sCssString)) {
                throw new HeadException('Invalid css string');
            }
            if (!is_array($aAttributes)) {
                throw new HeadException('Invalid attriutes');
            }

            // override style or append, prepend or ignore
            if (isset($this->aInlineStyles[$sKey])) {
                $this->aInlineStyles[$sKey] = array(
                    'cssString'  => $sCssString,
                    'attributes' => $aAttributes
                );
            } else {
                switch ($sNotFound) {
                    case 'PREPEND':
                        $this->prependInlineStyle($sCssString, $aAttributes, $sKey);
                        break;
                    case 'APPEND':
                        $this->appendInlineStyle($sCssString, $aAttributes, $sKey);
                        break;
                    default:
                        break;
                }
            }

            return $this;
        }

        /**
         * Prepends an inline style
         *
         * @param string          $sCssString
         * @param array           $aAttributes DEF: array()
         * @param string|int|null $sKey        DEF: null - if a key is given, it will remove the style with the same key before prepending
         *
         * @return Head
         */
        public function prependInlineStyle($sCssString, $aAttributes = array(), $sKey = null)
        {
            $this->addInlineStyle('PREPEND', $sCssString, $aAttributes, $sKey);
            return $this;
        }

        /**
         * Appends an inline style
         *
         * @param string          $sCssString
         * @param array           $aAttributes DEF: array()
         * @param string|int|null $sKey        DEF: null - if a key is given, it will remove the style with the same key before prepending
         *
         * @return Head
         */
        public function appendInlineStyle($sCssString, $aAttributes = array(), $sKey = null)
        {
            $this->addInlineStyle('APPEND', $sCssString, $aAttributes, $sKey);
            return $this;
        }

        /**
         * Removes the style with the given key, or all styles if no key given
         *
         * @param int|string|null $sKey DEF: null
         *
         * @return Head
         */
        public function removeInlineStyle($sKey = null)
        {
            if ($sKey !== null) {
                if (isset($this->aInlineStyles[$sKey])) {
                    unset($this->aInlineStyles[$sKey]);
                }
            } else {
                $this->aInlineStyles = array();
            }
            return $this;
        }

        /**
         * Renders the inline styles
         *
         * @return void
         */
        public function renderInlineStyle()
        {
            $sInlineStyle = '';
            foreach ($this->aInlineStyles as $aInlineStyle) {
                $sInlineStyle .= PHP_EOL . '<style type="text/css"';
                foreach ($aInlineStyle['attributes'] as $sKey => $sValue) {
                    $sInlineStyle .= ' ' . $sKey . '="' . $sValue . '"';
                }
                $sInlineStyle .= '>';
                $sInlineStyle .= $aInlineStyle['cssString'];
                $sInlineStyle .= '</style>';
            }
            echo $sInlineStyle;
        }

        /**
         * Adds (prepends or appends) an inline script
         *
         * @param string          $sPrependOrAppend VALUES: 'PREPEND'|'APPEND'; defines the position of the element
         * @param string          $sScriptString
         * @param string          $sType
         * @param array           $aAttributes      DEF: array()
         * @param string|int|null $sKey             DEF: null
         *
         * @return Head
         * @throws HeadException
         */
        private function addInlineScript($sPrependOrAppend, $sScriptString, $sType, $aAttributes = array(), $sKey = null)
        {
            // check input
            if (!is_string($sScriptString)) {
                throw new HeadException('Invalid script string');
            }
            if (!is_string($sType)) {
                throw new HeadException('Invalid type');
            }
            if (!is_array($aAttributes)) {
                throw new HeadException('Invalid attriutes');
            }

            // prepare script to add
            if ($sKey !== null) {
                $this->removeInlineScript($sKey);
                $aNewScript = array(
                    $sKey => array(
                        'scriptString' => $sScriptString,
                        'type'         => $sType,
                        'attributes'   => $aAttributes
                    )
                );
            } else {
                $aNewScript = array(
                    array(
                        'scriptString' => $sScriptString,
                        'type'         => $sType,
                        'attributes'   => $aAttributes
                    )
                );
            }

            if ($sPrependOrAppend == 'PREPEND') {
                // prepend script
                $this->aInlineScripts = array_merge($aNewScript, $this->aInlineScripts);
            } else {
                // append script
                $this->aInlineScripts = array_merge($this->aInlineScripts, $aNewScript);
            }
            return $this;
        }

        /**
         * Removes all scripts and sets the given one as the only script
         *
         * @param string          $sScriptString
         * @param string          $sType       DEF: 'text/javascript'
         * @param array           $aAttributes DEF: array()
         * @param string|int|null $sKey        DEF: null; the key under which the script will be set
         *
         * @return Head
         */
        public function setInlineScript($sScriptString, $sType = 'text/javascript', $aAttributes = array(), $sKey = null)
        {
            // set the inline script as the only array element
            $this->removeInlineScript();
            $this->appendInlineScript($sScriptString, $sType, $aAttributes, $sKey);
            return $this;
        }

        /**
         * The script with the given key will be overriden if it exists
         *
         * @param string|int $sKey
         * @param string     $sScriptString
         * @param string     $sType       DEF: 'text/javascript'
         * @param array      $aAttributes DEF: array()
         * @param string     $sNotFound   DEF: 'APPEND'; VALUES: 'APPEND'|'PREPEND'|'IGNORE'; if key is not found the script will be appended, prepended or not set at all
         *
         * @return Head
         * @throws HeadException
         */
        public function overrideInlineScript($sKey, $sScriptString, $sType = 'text/javascript', $aAttributes = array(), $sNotFound = 'APPEND')
        {
            // check input
            if (!is_string($sScriptString)) {
                throw new HeadException('Invalid script string');
            }
            if (!is_string($sType)) {
                throw new HeadException('Invalid type');
            }
            if (!is_array($aAttributes)) {
                throw new HeadException('Invalid attriutes');
            }

            // override style or append, prepend or ignore
            if (isset($this->aInlineScripts[$sKey])) {
                $this->aInlineScripts[$sKey] = array(
                    'scriptString' => $sScriptString,
                    'type'         => $sType,
                    'attributes'   => $aAttributes
                );
            } else {
                switch ($sNotFound) {
                    case 'PREPEND':
                        $this->prependInlineScript($sScriptString, $sType, $aAttributes, $sKey);
                        break;
                    case 'APPEND':
                        $this->appendInlineScript($sScriptString, $sType, $aAttributes, $sKey);
                        break;
                    default:
                        break;
                }
            }

            return $this;
        }

        /**
         * Prepends an inline script
         *
         * @param string          $sScriptString
         * @param string          $sType       DEF: 'text/javascript'
         * @param array           $aAttributes DEF: array()
         * @param string|int|null $sKey        DEF: null - if a key is given, it will remove the script with the same key before prepending
         *
         * @return Head
         */
        public function prependInlineScript($sScriptString, $sType = 'text/javascript', $aAttributes = array(), $sKey = null)
        {
            $this->addInlineScript('PREPEND', $sScriptString, $sType, $aAttributes, $sKey);
            return $this;
        }

        /**
         * Appends an inline script
         *
         * @param string          $sScriptString
         * @param string          $sType       DEF: 'text/javascript'
         * @param array           $aAttributes DEF: array()
         * @param string|int|null $sKey        DEF: null - if a key is given, it will remove the script with the same key before prepending
         *
         * @return Head
         */
        public function appendInlineScript($sScriptString, $sType = 'text/javascript', $aAttributes = array(), $sKey = null)
        {
            $this->addInlineScript('APPEND', $sScriptString, $sType, $aAttributes, $sKey);
            return $this;
        }

        /**
         * Removes the style with the given key, or all styles if no key given
         *
         * @param int|string|null $sKey DEF: null
         *
         * @return Head
         */
        public function removeInlineScript($sKey = null)
        {
            if ($sKey !== null) {
                if (isset($this->aInlineScripts[$sKey])) {
                    unset($this->aInlineScripts[$sKey]);
                }
            } else {
                $this->aInlineScripts = array();
            }
            return $this;
        }

        /**
         * @return Head
         */
        public function clearAll()
        {
            $this->removeTitle()
                ->removeBase()
                ->removeMetaName()
                ->removeMetaHttpEquiv()
                ->removeMetaProperty()
                ->removeInlineStyle()
                ->removeInlineScript()
                ->removeLink()
                ->removeStylesheet()
                ->removeScript();
            return $this;
        }

        public function renderScripts()
        {
            $this->renderScript();
            $this->renderInlineScript();
        }

        /**
         * Checks if the minify folder is writable by apache
         *
         * @throws exceptions\HeadException
         */
        private function checkMinifyDir()
        {
            $sMinifyFolder = $this->sPublicDir . '/' . $this->sMinifyDir;
            if (!is_writable($sMinifyFolder)) {
                throw new HeadException(
                    'Minify folder is not writable.
                    Give apache permission to write into the following folder: ' . $sMinifyFolder
                );
            }
        }

        /**
         * @param array $aAssets
         *
         * @return string
         * @throws HeadException
         */
        private function minifyJavaScript(array $aAssets)
        {

            $this->checkMinifyDir();

            $aCombinedAsset = array(
                'name'    => '',
                'content' => ''
            );

            $iMostRecentlyChangedJs = 0;
            foreach ($aAssets as $aAsset) {
                $iLastChanged = filemtime($aAsset['location']);

                if ($aAsset['extension'] == "js") {
                    $aCombinedAsset['name'] .= rtrim($aAsset['filename'], '.js');
                    if ($iLastChanged > $iMostRecentlyChangedJs) {
                        $iMostRecentlyChangedJs = $iLastChanged;
                    }
                } else {
                    throw new HeadException('The only supported file types are JS');
                }
            }

            $jsMinifiedFilename = md5($aCombinedAsset['name']);
            $jsMinifiedLocation = $this->sPublicDir . '/' . $this->sMinifyDir . '/' . $jsMinifiedFilename . ".js";
            if (!file_exists($jsMinifiedLocation) || filemtime($jsMinifiedLocation) < $iMostRecentlyChangedJs) {
                foreach ($aAssets as $aAsset) {
                    $aCombinedAsset['content'] .= file_get_contents($aAsset['location']) . "\n";
                }

                $aCombinedAsset['content'] = $this->oJsMinifier->minify($aCombinedAsset['content']);

                if (file_exists($jsMinifiedLocation)) {
                    unlink($jsMinifiedLocation);
                }
                if (!empty($aCombinedAsset['content'])) {
                    file_put_contents($jsMinifiedLocation, $aCombinedAsset['content']);
                }
            }

            return PHP_EOL . '<script src="/' . $this->sMinifyDir . '/' . $jsMinifiedFilename . '.js" type="text/javascript"></script>';
        }

        /**
         * @param array  $aAssets
         * @param string $sMedia
         *
         * @return string
         * @throws HeadException
         */
        private function minifyCss(array $aAssets, $sMedia = '')
        {

            $this->checkMinifyDir();

            $aCombinedAsset          = array(
                'name'    => '',
                'content' => ''
            );
            $iMostRecentlyChangedCss = 0;

            foreach ($aAssets as $aAsset) {
                $iLastChanged = filemtime($aAsset['location']);

                if ($aAsset['extension'] == "css") {
                    $aCombinedAsset['name'] .= rtrim($aAsset['filename'], '.css');
                    if ($iLastChanged > $iMostRecentlyChangedCss) {
                        $iMostRecentlyChangedCss = $iLastChanged;
                    }
                } else {
                    throw new HeadException('The only supported file types are CSS');
                }
            }

            $cssMinifiedFilename = md5($aCombinedAsset['name']);
            $cssMinifiedLocation = $this->sPublicDir . '/' . $this->sMinifyDir . '/' . $cssMinifiedFilename . ".css";
            if (!file_exists($cssMinifiedLocation) || filemtime($cssMinifiedLocation) < $iMostRecentlyChangedCss) {
                foreach ($aAssets as $aAsset) {
                    $aCombinedAsset['content'] .= file_get_contents($aAsset['location']) . "\n";
                }

                $aCombinedAsset['content'] = $this->oCssMinifier->minify($aCombinedAsset['content']);

                if (file_exists($cssMinifiedLocation)) {
                    unlink($cssMinifiedLocation);
                }
                if (!empty($aCombinedAsset['content'])) {
                    file_put_contents($cssMinifiedLocation, $aCombinedAsset['content']);
                }
            }

            return PHP_EOL . '<link href="/' . $this->sMinifyDir . '/' . $cssMinifiedFilename . '.css" rel="stylesheet"' . ((!empty($sMedia)) ? ' media="' . $sMedia . '"' : '') . '/>';
        }

    }
