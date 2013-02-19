<?php

namespace flyingpiranhas\mvc\errorHandling;

use flyingpiranhas\common\http\interfaces\RequestInterface;
use flyingpiranhas\common\errorHandling\ErrorHandler as BaseErrorHandler;
use Exception;

/**
 * The framework's original miniature error helper class.
 *
 * Usage:
 *
 * To display an error and die the application, call
 * errorReporter::show($code[, $message]) where $code is the error code of the error you wish to display. It is an arbitrary value, but has to be provided.
 * If you provide $message, it overrides the error/errorReporter default
 *
 * To set a different view to be used, use
 * errorReporter::setView($view) where $view is the name of a php file in library/errorReporter/views/
 *
 * @category       errorHandling
 * @package        flyingpiranhas.mvc
 * @license        Apache-2.0
 * @version        0.01
 * @since          2012-09-07
 * @author         Bruno Škvorc <bruno@skvorc.me>
 * @author         Ivan Pintar
 */
class ErrorHandler extends BaseErrorHandler
{

    /** @var array */
    public static $aCommonExceptions = array();

    /** @var Exception */
    private $oException;

    /** @var string */
    private $sAppEnv = '';

    /** @var string */
    private $sView = 'default';

    /** @var string */
    private $sLayout = 'layout';

    /** @var string */
    private $sErrorViewDir = '';

    /** @var RequestInterface */
    private $oRequest;

    /**
     * @param RequestInterface $oRequest
     * @param string           $sAppEnv
     * @param string           $sErrorViewsDir
     */
    public function __construct(RequestInterface $oRequest, $sAppEnv, $sErrorViewsDir)
    {
        $this->oRequest = $oRequest;
        $this->sAppEnv = $sAppEnv;
        $this->sErrorViewDir = $sErrorViewsDir;
    }

    /**
     * @param string $sAppEnv
     *
     * @return ErrorHandler
     */
    public function setAppEnv($sAppEnv)
    {
        $this->sAppEnv = $sAppEnv;
        return $this;
    }

    /**
     * @param string $sErrorViewDir
     *
     * @return ErrorHandler
     */
    public function setErrorViewDir($sErrorViewDir)
    {
        $this->sErrorViewDir = $sErrorViewDir;
        return $this;
    }

    /**
     * Sets the view to be used to display the error. Views are very simple layouts specific to this errorReporter.
     * Views need to exist as php files in the directory set in config.ini (dirs.errorViews), default is application_dir/views/errors,
     * If a view with a name identical to the given error code exists, that view will be used.
     *
     * @param string $sView
     *
     * @return ErrorHandler
     */
    public function setView($sView)
    {
        $this->sView = $sView;
        return $this;
    }

    /**
     * @param string $sLayout
     *
     * @return ErrorHandler
     */
    public function setLayout($sLayout)
    {
        $this->sLayout = $sLayout;
        return $this;
    }

    /**
     * Displays the error and exits the application
     */
    public function show()
    {
        ob_end_clean();

        if (!headers_sent()) {
            header('HTTP/1.1 404 Not Found');
        }

        $sLayout = $this->sErrorViewDir . '/' . $this->sLayout . '.php';
        if (is_readable($sLayout)) {
            $this->renderLayout();
        } else {
            $this->renderView();
        }
        exit();
    }

    /**
     * The method sent to PHP's set_exception_handler()
     *
     * @param Exception $oException
     *
     * @return void
     */
    public function handleExceptions(Exception $oException)
    {
        $this->oException = $oException;
        $this->show();
    }

    /**  */
    private function renderLayout()
    {
        include $this->sErrorViewDir . '/' . $this->sLayout . '.php';
    }

    /**  */
    private function renderView()
    {
        $sView = $this->sView;
        if (is_readable($this->sErrorViewDir . '/' . $this->oException->getCode() . '.php')) {
            $sView = $this->oException->getCode();
        }
        $sFullPath = $this->sErrorViewDir . '/' . $sView . '.php';
        $e = $this->oException;
        if (is_readable($sFullPath)) {
            include $sFullPath;
        } else {
            exit(
            'An error has occured, but you have no default error view template to display it.
            Please create the following file: '.$sFullPath.'<br />For more information,
            see the mvc.ErrorHandler documentation online.');
        }
    }
}

