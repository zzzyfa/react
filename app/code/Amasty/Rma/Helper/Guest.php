<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Rma
 */

namespace Amasty\Rma\Helper;

use Magento\Framework\App as App;
use Magento\Framework\ObjectManagerInterface;
use Magento\Sales\Model\Order;
use Magento\Store\Model\ScopeInterface;

class Guest extends \Magento\Sales\Helper\Guest
{
    /**
     * @var ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * @param App\Helper\Context                                     $context
     * @param \Magento\Store\Model\StoreManagerInterface             $storeManager
     * @param \Magento\Framework\Registry                            $coreRegistry
     * @param \Magento\Framework\Stdlib\CookieManagerInterface       $cookieManager
     * @param \Magento\Framework\Stdlib\Cookie\CookieMetadataFactory $cookieMetadataFactory
     * @param \Magento\Framework\Message\ManagerInterface            $messageManager
     * @param \Magento\Sales\Model\OrderFactory                      $orderFactory
     * @param ObjectManagerInterface                                 $objectManager
     * @param \Amasty\Rma\Injection\CustomerSession                  $customerSession
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        App\Helper\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\Stdlib\CookieManagerInterface $cookieManager,
        \Magento\Framework\Stdlib\Cookie\CookieMetadataFactory $cookieMetadataFactory,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Magento\Sales\Model\OrderFactory $orderFactory,

        ObjectManagerInterface $objectManager,
        \Amasty\Rma\Injection\CustomerSession $customerSession
    ) {
        $this->objectManager = $objectManager;

        $resultRedirectFactory = $this->objectManager->create(
            '\Magento\Framework\Controller\Result\RedirectFactory', 
            ['instanceName' => '\Amasty\Rma\Injection\Redirect']
        );
        
        parent::__construct(
            $context,
            $storeManager,
            $coreRegistry,
            $customerSession,
            $cookieManager,
            $cookieMetadataFactory,
            $messageManager,
            $orderFactory,
            $resultRedirectFactory
        );
    }
    
    public function isGuestEnabled()
    {
        return $this->scopeConfig->isSetFlag(
            'amrma/general/guest', ScopeInterface::SCOPE_STORE
        );
    }

    public function authorizeOrder(Order $order)
    {
        $value = base64_encode($order->getProtectCode() . ':' . $order->getIncrementId());
        
        $metadata = $this->cookieMetadataFactory->createPublicCookieMetadata()
            ->setPath(self::COOKIE_PATH)
            ->setHttpOnly(true);
        
        $this->cookieManager->setPublicCookie(self::COOKIE_NAME, $value, $metadata);
    }
}
