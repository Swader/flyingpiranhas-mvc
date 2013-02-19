<?php

namespace flyingpiranhas\mvc\router;

use flyingpiranhas\mvc\router\abstracts\RouterAbstract;
use flyingpiranhas\mvc\router\interfaces\AppRouterInterface;

/**
 * The AppRouter object parses the request
 * and sets the corresponding module.
 * It holds references to the Request and Response objects
 *
 * @category       router
 * @package        flyingpiranhas.mvc
 * @license        Apache-2.0
 * @version        0.01
 * @since          2012-09-07
 * @author         Ivan Pintar
 */
class AppRouter extends RouterAbstract implements AppRouterInterface
{

    /**
     * @return string
     */
    public function getModule()
    {
        return $this->sModule;
    }

    /**
     * Routes the url to the mca defined by the url itself
     * Returns false on failure
     *
     * @param string $sUrl
     *
     * @return bool
     */
    protected function route($sUrl)
    {
        $sUrl = trim($sUrl, '/');

        $bReturn = true;
        $aUrlParts = (!empty($sUrl)) ? explode('/', $sUrl) : array();

        $sModule = $this->aMcaDefaults['module'];

        switch (count($aUrlParts)) {
            case 0:
                break;

            case 1:
                $sModule = $aUrlParts[0];
                unset($aUrlParts[0]);
                break;

            case 2:
                $sModule = $aUrlParts[0];
                unset($aUrlParts[0]);
                unset($aUrlParts[1]);
                break;

            default:
                $sModule = $aUrlParts[0];
                unset($aUrlParts[0]);
                unset($aUrlParts[1]);
                unset($aUrlParts[2]);
                break;
        }

        $this->sModule = trim($sModule);

        return $bReturn;
    }

}