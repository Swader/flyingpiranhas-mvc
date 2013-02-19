<?php

    namespace flyingpiranhas\mvc\views\head\interfaces;

    use flyingpiranhas\mvc\views\head\exceptions\HeadException;

    /**
     * @category       views
     * @package        flyingpiranhas.mvc
     * @license        Apache-2.0
     * @version        0.01
     * @since          2013-02-12
     * @author         Bruno Škvorc <bruno@skvorc.me>
     */
    interface HeadInterface
    {
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
        public function render();

        /**
         * Renders the script elements
         *
         * @return void
         */
        public function renderScript();

        /**
         * Renders the inline scripts
         *
         * @return void
         */
        public function renderInlineScript();

        /**
         * Returns the defined title
         *
         * @return string
         */
        public function getTitle();

        /**
         * Sets the title to the given value
         *
         * @param string $sTitle
         *
         * @return HeadInterface
         * @throws HeadException
         */
        public function setTitle($sTitle);

        /**
         * Prepends a string with the given separator to the current title
         *
         * @param string $sTitle
         * @param string $sSeparator DEF: ' | '
         *
         * @return HeadInterface
         */
        public function prependTitle($sTitle, $sSeparator = ' - ');

        /**
         * Appends a string with the given separator to the current title
         *
         * @param string $sTitle
         * @param string $sSeparator DEF: ' | '
         *
         * @return HeadInterface
         */
        public function appendTitle($sTitle, $sSeparator = ' - ');

        /**
         * Sets the title to an empty string
         *
         * @return HeadInterface
         */
        public function removeTitle();

        /**
         * Renders the head title
         *
         * @return void
         */
        public function renderTitle();

        /**
         * Sets the head base tag
         *
         * @param string $sHref
         * @param string $sTarget DEF: ''
         *
         * @return HeadInterface
         * @throws HeadException
         */
        public function setBase($sHref = '', $sTarget = '');

        /**
         * Deletes the head base
         *
         * @return HeadInterface
         */
        public function removeBase();

        /**
         * Returns the defined base
         *
         * @return array
         */
        public function getBase();

        /**
         * Renders the head base
         *
         * @return void
         */
        public function renderBase();

        /**
         * Removes all meta names and sets the given one as the only meta name
         *
         * @param string          $sName
         * @param string          $sContent
         * @param array           $aAttributes DEF: array()
         * @param string|int|null $sKey        DEF: null; the key under which the meta name will be set
         *
         * @return HeadInterface
         */
        public function setMetaName($sName, $sContent, $aAttributes = array(), $sKey = null);

        /**
         * The meta name with the given key will be overriden if it exists
         *
         * @param string|int $sKey
         * @param string     $sName
         * @param string     $sContent
         * @param array      $aAttributes    DEF: array()
         * @param string     $sKeyNotFound   DEF: 'APPEND'; VALUES: 'APPEND'|'PREPEND'|'IGNORE'; if key is not found the meta name will be appended, prepended or not set at all
         *
         * @return HeadInterface
         * @throws HeadException
         */
        public function overrideMetaName($sKey, $sName, $sContent, $aAttributes = array(), $sKeyNotFound = 'APPEND');

        /**
         * Prepends a meta name
         *
         * @param string          $sName
         * @param string          $sContent
         * @param array           $aAttributes DEF: array()
         * @param string|int|null $sKey        DEF: null - if a key is given, it will remove the meta name with the same key before prepending
         *
         * @return HeadInterface
         */
        public function prependMetaName($sName, $sContent, $aAttributes = array(), $sKey = null);

        /**
         * Appends a meta name
         *
         * @param string          $sName
         * @param string          $sContent
         * @param array           $aAttributes DEF: array()
         * @param string|int|null $sKey        DEF: null - if a key is given, it will remove the meta name with the same key before appending
         *
         * @return HeadInterface
         */
        public function appendMetaName($sName, $sContent, $aAttributes = array(), $sKey = null);

        /**
         * Removes the meta name with the given key, or all meta names if no key given
         *
         * @param int|string|null $sKey DEF: null
         *
         * @return HeadInterface
         */
        public function removeMetaName($sKey = null);

        /**
         * Renders the meta name elements
         *
         * @return void
         */
        public function renderMetaName();

        /**
         * Returns defined meta names
         *
         * @return array
         */
        public function getMetaNames();

        /**
         * Returns defined meta http equivs
         *
         * @return array
         */
        public function getMetaHttpEquivs();


        /**
         * @param  string          $sHttpEquivName
         * @param   string         $sContent
         * @param array            $aAttributes DEF: array()
         * @param string|int|null  $sKey        DEF: null; the key under which the meta http-equiv will be set
         *
         * @return HeadInterface
         */
        public function setMetaHttpEquiv($sHttpEquivName, $sContent, $aAttributes = array(), $sKey = null);

        /**
         * The meta http-equiv with the given key will be overriden if it exists
         *
         * @param string|int $sKey
         * @param string     $sHttpEquiv
         * @param string     $sContent
         * @param array      $aAttributes DEF: array()
         * @param string     $sNotFound   DEF: 'APPEND'; VALUES: 'APPEND'|'PREPEND'|'IGNORE'; if key is not found the meta http-equiv will be appended, prepended or not set at all
         *
         * @return HeadInterface
         * @throws HeadException
         */
        public function overrideMetaHttpEquiv($sKey, $sHttpEquiv, $sContent, $aAttributes = array(), $sNotFound = 'APPEND');

        /**
         * Prepends a meta http-equiv
         *
         * @param string          $sHttpEquiv
         * @param string          $sContent
         * @param array           $aAttributes DEF: array()
         * @param string|int|null $sKey        DEF: null - if a key is given, it will remove the meta http-equiv with the same key before prepending
         *
         * @return HeadInterface
         */
        public function prependMetaHttpEquiv($sHttpEquiv, $sContent, $aAttributes = array(), $sKey = null);

        /**
         * Appends a meta http-equiv
         *
         * @param string          $sHttpEquiv
         * @param string          $sContent
         * @param array           $aAttributes DEF: array()
         * @param string|int|null $sKey        DEF: null - if a key is given, it will remove the meta http-equiv with the same key before appending
         *
         * @return HeadInterface
         */
        public function appendMetaHttpEquiv($sHttpEquiv, $sContent, $aAttributes = array(), $sKey = null);

        /**
         * Removes the meta http-equiv with the given key, or all meta http-equivs if no key given
         *
         * @param int|string|null $sKey DEF: null
         *
         * @return HeadInterface
         */
        public function removeMetaHttpEquiv($sKey = null);

        /**
         * Renders the meta http-equiv elements
         *
         * @return void
         */
        public function renderMetaHttpEquiv();

        /**
         * Returns the defined meta properties
         *
         * @return array
         */
        public function getMetaProperties();

        /**


        /**
         * Removes all meta properties and sets the given one as the only meta property
         *
         * @param string          $sProperty
         * @param string          $sContent
         * @param array           $aAttributes DEF: array()
         * @param string|int|null $sKey        DEF: null; the key under which the meta property will be set
         *
         * @return HeadInterface
         */
        public function setMetaProperty($sProperty, $sContent, $aAttributes = array(), $sKey = null);

        /**
         * The meta property with the given key will be overriden if it exists
         *
         * @param string|int $sKey
         * @param string     $sProperty
         * @param string     $sContent
         * @param array      $aAttributes DEF: array()
         * @param string     $sNotFound   DEF: 'APPEND'; VALUES: 'APPEND'|'PREPEND'|'IGNORE'; if key is not found the meta property will be appended, prepended or not set at all
         *
         * @return HeadInterface
         * @throws HeadException
         */
        public function overrideMetaProperty($sKey, $sProperty, $sContent, $aAttributes = array(), $sNotFound = 'APPEND');

        /**
         * Prepends a meta property
         *
         * @param string          $sProperty
         * @param string          $sContent
         * @param array           $aAttributes DEF: array()
         * @param string|int|null $sKey        DEF: null - if a key is given, it will remove the meta property with the same key before prepending
         *
         * @return HeadInterface
         */
        public function prependMetaProperty($sProperty, $sContent, $aAttributes = array(), $sKey = null);

        /**
         * Appends a meta property
         *
         * @param string          $sProperty
         * @param string          $sContent
         * @param array           $aAttributes DEF: array()
         * @param string|int|null $sKey        DEF: null - if a key is given, it will remove the meta property with the same key before apending
         *
         * @return HeadInterface
         */
        public function appendMetaProperty($sProperty, $sContent, $aAttributes = array(), $sKey = null);

        /**
         * Removes the meta property with the given key, or all meta properties if no key given
         *
         * @param int|string|null $sKey DEF: null
         *
         * @return HeadInterface
         */
        public function removeMetaProperty($sKey = null);

        /**
         * Renders the meta property elements
         *
         * @return void
         */
        public function renderMetaProperty();

        /**
         * @return array
         */
        public function getLinks();


        /**
         * Removes all links and sets the given one as the only link
         *
         * @param string          $sHref
         * @param array           $aAttributes DEF: array()
         * @param string|int|null $sKey        DEF: null; the key under which the link will be set
         *
         * @return HeadInterface
         */
        public function setLink($sHref, $aAttributes = array(), $sKey = null);

        /**
         * The link with the given key will be overriden if it exists
         *
         * @param string|int $sKey
         * @param string     $sHref
         * @param array      $aAttributes DEF: array()
         * @param string     $sNotFound   DEF: 'APPEND'; VALUES: 'APPEND'|'PREPEND'|'IGNORE'; if key is not found the link will be appended, prepended or not set at all
         *
         * @return HeadInterface
         * @throws HeadException
         */
        public function overrideLink($sKey, $sHref, $aAttributes = array(), $sNotFound = 'APPEND');

        /**
         * Prepends a link
         *
         * @param string          $sHref
         * @param array           $aAttributes DEF: array()
         * @param string|int|null $sKey        DEF: null - if a key is given, it will remove the link with the same key before prepending
         *
         * @return HeadInterface
         */
        public function prependLink($sHref, $aAttributes = array(), $sKey = null);

        /**
         * Appends a link
         *
         * @param string          $sHref
         * @param array           $aAttributes DEF: array()
         * @param string|int|null $sKey        DEF: null - if a key is given, it will remove the link with the same key before appending
         *
         * @return HeadInterface
         */
        public function appendLink($sHref, $aAttributes = array(), $sKey = null);

        /**
         * Removes the link with the given key, or all links if no key given
         *
         * @param int|string|null $sKey DEF: null
         *
         * @return HeadInterface
         */
        public function removeLink($sKey = null);

        /**
         * Renders the link elements
         *
         * @return void
         */
        public function renderLink();

        /**
         * Removes all stylesheet links and sets the given one as the only stylesheet link
         *
         * @param string          $sHref
         * @param bool            $bMinify     DEF: true
         * @param array           $aAttributes DEF: array()
         * @param string|int|null $sKey        DEF: null; the key under which the stylesheet link will be set
         *
         * @return HeadInterface
         */
        public function setStylesheet($sHref, $bMinify = true, $aAttributes = array(), $sKey = null);

        /**
         * The stylesheet link with the given key will be overriden if it exists
         *
         * @param string|int $sKey
         * @param string     $sHref
         * @param bool       $bMinify     DEF: true
         * @param array      $aAttributes DEF: array()
         * @param string     $sNotFound   DEF: 'APPEND'; VALUES: 'APPEND'|'PREPEND'|'IGNORE'; if key is not found the stylesheet link will be appended, prepended or not set at all
         *
         * @return HeadInterface
         * @throws HeadException
         */
        public function overrideStylesheet($sKey, $sHref, $bMinify = true, $aAttributes = array(), $sNotFound = 'APPEND');

        /**
         * Prepends a stylesheet link
         *
         * @param string          $sHref
         * @param bool            $bMinify     DEF: true
         * @param array           $aAttributes DEF: array()
         * @param string|int|null $sKey        DEF: null - if a key is given, it will remove the stylesheet link with the same key before prepending
         *
         * @return HeadInterface
         */
        public function prependStylesheet($sHref, $bMinify = true, $aAttributes = array(), $sKey = null);

        /**
         * Appends a stylesheet link
         *
         * @param string          $sHref
         * @param bool            $bMinify     DEF: true
         * @param array           $aAttributes DEF: array()
         * @param string|int|null $sKey        DEF: null - if a key is given, it will remove the stylesheet link with the same key before appending
         *
         * @return HeadInterface
         */
        public function appendStylesheet($sHref, $bMinify = true, $aAttributes = array(), $sKey = null);

        /**
         * Removes the stylesheet with the given key, or all stylesheets if no key given
         *
         * @param int|string|null $sKey DEF: null
         *
         * @return HeadInterface
         */
        public function removeStylesheet($sKey = null);

        /**
         * Renders the stylesheet link elements
         *
         * @return void
         */
        public function renderStylesheet();

        /**
         * Returns defined stylesheets
         *
         * @return array
         */
        public function getStylesheets();

        /**
         * @return array
         */
        public function getScripts();

        /**
         * @return array
         */
        public function getInlineStyles();

        /**
         * Renders the inline styles
         *
         * @return void
         */
        public function renderInlineStyle();

        /**
         * @return array
         */
        public function getInlineScripts();

        /**
         * Set the directory where the minified scripts and stylesheets will be stored.
         * This directory is always relative to the public directory
         *
         * @param string $sMinifyDir
         *
         * @return HeadInterface
         * @throws HeadException
         */
        public function setMinifyDir($sMinifyDir);

        /**
         * Set the public directory
         *
         * @param string $sPublicDir
         *
         * @return HeadInterface
         * @throws HeadException
         */
        public function setPublicDir($sPublicDir);

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
         * @return HeadInterface
         * @throws HeadException
         */
        public function addMeta($sPrependOrAppend, $sType, $sTypeValue, $sContent, $aAttributes = array(), $sKey = null);

        /**
         * Adds (prepends or appends) an inline style
         *
         * @param string          $sPrependOrAppend VALUES: 'PREPEND'|'APPEND'; defines the position of the element
         * @param string          $sCssString
         * @param array           $aAttributes      DEF: array()
         * @param string|int|null $sKey             DEF: null
         *
         * @throws HeadException
         * @return HeadInterface
         */
        public function addInlineStyle($sPrependOrAppend, $sCssString, $aAttributes = array(), $sKey = null);

        /**
         * Removes all styles and sets the given one as the only style
         *
         * @param string          $sCssString
         * @param array           $aAttributes DEF: array()
         * @param string|int|null $sKey        DEF: null; the key under which the style will be set
         *
         * @return HeadInterface
         */
        public function setInlineStyle($sCssString, $aAttributes = array(), $sKey = null);

        /**
         * The style with the given key will be overriden if it exists
         *
         * @param string|int $sKey
         * @param string     $sCssString
         * @param array      $aAttributes DEF: array()
         * @param string     $sNotFound   DEF: 'APPEND'; VALUES: 'APPEND'|'PREPEND'|'IGNORE'; if key is not found the style will be appended, prepended or not set at all
         *
         * @return HeadInterface
         * @throws HeadException
         */
        public function overrideInlineStyle($sKey, $sCssString, $aAttributes = array(), $sNotFound = 'APPEND');

        /**
         * Prepends an inline style
         *
         * @param string          $sCssString
         * @param array           $aAttributes DEF: array()
         * @param string|int|null $sKey        DEF: null - if a key is given, it will remove the style with the same key before prepending
         *
         * @return HeadInterface
         */
        public function prependInlineStyle($sCssString, $aAttributes = array(), $sKey = null);

        /**
         * Appends an inline style
         *
         * @param string          $sCssString
         * @param array           $aAttributes DEF: array()
         * @param string|int|null $sKey        DEF: null - if a key is given, it will remove the style with the same key before prepending
         *
         * @return HeadInterface
         */
        public function appendInlineStyle($sCssString, $aAttributes = array(), $sKey = null);

        /**
         * Removes the style with the given key, or all styles if no key given
         *
         * @param int|string|null $sKey DEF: null
         *
         * @return HeadInterface
         */
        public function removeInlineStyle($sKey = null);

        /**
         * Removes all scripts and sets the given one as the only script
         *
         * @param string          $sScriptString
         * @param string          $sType       DEF: 'text/javascript'
         * @param array           $aAttributes DEF: array()
         * @param string|int|null $sKey        DEF: null; the key under which the script will be set
         *
         * @return HeadInterface
         */
        public function setInlineScript($sScriptString, $sType = 'text/javascript', $aAttributes = array(), $sKey = null);

        /**
         * The script with the given key will be overriden if it exists
         *
         * @param string|int $sKey
         * @param string     $sScriptString
         * @param string     $sType       DEF: 'text/javascript'
         * @param array      $aAttributes DEF: array()
         * @param string     $sNotFound   DEF: 'APPEND'; VALUES: 'APPEND'|'PREPEND'|'IGNORE'; if key is not found the script will be appended, prepended or not set at all
         *
         * @return HeadInterface
         * @throws HeadException
         */
        public function overrideInlineScript($sKey, $sScriptString, $sType = 'text/javascript', $aAttributes = array(), $sNotFound = 'APPEND');

        /**
         * Prepends an inline script
         *
         * @param string          $sScriptString
         * @param string          $sType       DEF: 'text/javascript'
         * @param array           $aAttributes DEF: array()
         * @param string|int|null $sKey        DEF: null - if a key is given, it will remove the script with the same key before prepending
         *
         * @return HeadInterface
         */
        public function prependInlineScript($sScriptString, $sType = 'text/javascript', $aAttributes = array(), $sKey = null);

        /**
         * Appends an inline script
         *
         * @param string          $sScriptString
         * @param string          $sType       DEF: 'text/javascript'
         * @param array           $aAttributes DEF: array()
         * @param string|int|null $sKey        DEF: null - if a key is given, it will remove the script with the same key before prepending
         *
         * @return HeadInterface
         */
        public function appendInlineScript($sScriptString, $sType = 'text/javascript', $aAttributes = array(), $sKey = null);

        /**
         * Removes the style with the given key, or all styles if no key given
         *
         * @param int|string|null $sKey DEF: null
         *
         * @return HeadInterface
         */
        public function removeInlineScript($sKey = null);

        /**
         * Removes all scripts and sets the given one as the only script
         *
         * @param string          $sSrc
         * @param bool            $bMinify     DEF: true
         * @param string          $sType       DEF: 'text/javascript'
         * @param array           $aAttributes DEF: array()
         * @param string|int|null $sKey        DEF: null; the key under which the script will be set
         *
         * @return HeadInterface
         */
        public function setScript($sSrc, $bMinify = true, $sType = 'text/javascript', $aAttributes = array(), $sKey = null);

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
         * @return HeadInterface
         * @throws HeadException
         */
        public function overrideScript($sKey, $sSrc, $bMinify = true, $sType = 'text/javascript', $aAttributes = array(), $sNotFound = 'APPEND');

        /**
         * Prepends a script
         *
         * @param string          $sSrc
         * @param bool            $bMinify     DEF: true
         * @param string          $sType       DEF: 'text/javascript'
         * @param array           $aAttributes DEF: array()
         * @param string|int|null $sKey        DEF: null - if a key is given, it will remove the script with the same key before prepending
         *
         * @return HeadInterface
         */
        public function prependScript($sSrc, $bMinify = true, $sType = 'text/javascript', $aAttributes = array(), $sKey = null);

        /**
         * Appends a script
         *
         * @param string          $sSrc
         * @param bool            $bMinify     DEF: true
         * @param string          $sType       DEF: 'text/javascript'
         * @param array           $aAttributes DEF: array()
         * @param string|int|null $sKey        DEF: null - if a key is given, it will remove the script with the same key before appending
         *
         * @return HeadInterface
         */
        public function appendScript($sSrc, $bMinify = true, $sType = 'text/javascript', $aAttributes = array(), $sKey = null);

        /**
         * Removes the script with the given key, or all scripts if no key given
         *
         * @param int|string|null $sKey DEF: null
         *
         * @return HeadInterface
         */
        public function removeScript($sKey = null);

        /**
         * @return HeadInterface
         */
        public function clearAll();

        /**
         * Renders all three meta types
         *
         * @return void
         */
        public function renderAllMeta();
    }
