<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Rma
 */


namespace Amasty\Rma\Model;

use Amasty\Rma\Controller\Router;

/**
 * this class used in DI
 */
class UrlBuilder extends \Magento\Framework\Url
{
    /**
     * @var string
     */
    static private $BASE_URL;

    /**
     * @return string
     */
    public function getUrlPrefix()
    {
        if (self::$BASE_URL === null) {
            if ($url = $this->_scopeConfig->getValue(Router::ROUTE_XPATH)) {
                self::$BASE_URL = trim($url, '/ ');
            } else {
                self::$BASE_URL = Router::RMA_URL_SYSTEM_ROUTE;
            }
        }

        return self::$BASE_URL;
    }

    /**
     * @param string|null $routePath
     * @param array|null $routeParams
     *
     * @return string
     */
    public function getUrl($routePath = null, $routeParams = null)
    {
        if (is_string($routePath) && strpos($routePath, Router::RMA_URL_SYSTEM_ROUTE) !== false) {
            $routePath = str_replace(Router::RMA_URL_SYSTEM_ROUTE, $this->getUrlPrefix(), $routePath);
        }

        return parent::getUrl($routePath, $routeParams);
    }
}
