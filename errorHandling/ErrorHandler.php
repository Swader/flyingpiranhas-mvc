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
    private $sErrorViewDir = '';

    /**
     * @var RequestInterface
     */
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
     * @param string $sSlug
     *
     * @return ErrorHandler
     */
    public function setView($sSlug)
    {
        $this->sView = $sSlug;
        return $this;
    }

    /**
     * Displays the error and dies the application
     */
    public function show()
    {
        ob_end_clean();

        if (!$this->sView || is_readable($this->sErrorViewDir . '/' . $this->oException->getCode() . '.php')) {
            $this->setView($this->oException->getCode());
        }
        if (!headers_sent()) {
            header('HTTP/1.1 404 Not Found');
        }

        include_once $this->sErrorViewDir . '/layout.php';
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

}

