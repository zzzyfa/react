<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Rma
 */


namespace Amasty\Rma\Controller;

class Router implements \Magento\Framework\App\RouterInterface
{
    const ROUTE_XPATH = 'amrma/general/route';

    const RMA_URL_SYSTEM_ROUTE = 'amasty_rma';

    /**
     * @var \Magento\Framework\App\ActionFactory
     */
    private $actionFactory;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * Router constructor.
     *
     * @param \Magento\Framework\App\ActionFactory               $actionFactory
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        \Magento\Framework\App\ActionFactory $actionFactory,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
    ) {
        $this->actionFactory = $actionFactory;
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * @param \Magento\Framework\App\RequestInterface $request
     * @return \Magento\Framework\App\ActionInterface|false
     */
    public function match(\Magento\Framework\App\RequestInterface $request)
    {
        $identifier = explode(DIRECTORY_SEPARATOR, trim($request->getPathInfo(), DIRECTORY_SEPARATOR));
        $compareUrl = $this->getPathUrlFromSetting();

        if (isset($identifier[0]) && ($compareUrl == $identifier[0])) {
            $newPathInfo = str_replace($compareUrl, self::RMA_URL_SYSTEM_ROUTE, $request->getPathInfo());
            $request->setPathInfo($newPathInfo);

            return $this->actionFactory->create('Magento\Framework\App\Action\Forward', ['request' => $request]);
        }

        return false;
    }

    /**
     * @return string
     */
    protected function getPathUrlFromSetting()
    {
        return trim($this->scopeConfig->getValue(self::ROUTE_XPATH), DIRECTORY_SEPARATOR) ?: "rma";
    }
}
