<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Rma
 */


namespace Amasty\Rma\Block\Request;

use Magento\Framework\ObjectManagerInterface;
use Magento\Sales\Model\Order\Address\Renderer as AddressRenderer;

class Export extends \Magento\Framework\View\Element\Template
{
    /**
     * @var ObjectManagerInterface
     */
    protected $objectManager;
    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;
    /**
     * @var \Amasty\Rma\Helper\Data
     */
    protected $helper;
    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;
    /**
     * @var \Magento\Theme\Block\Html\Header\Logo
     */
    protected $logo;
    /**
     * @var AddressRenderer
     */
    protected $addressRenderer;

    /**
     * History constructor.
     *
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param ObjectManagerInterface                           $objectManager
     * @param \Magento\Theme\Block\Html\Header\Logo            $logo
     * @param \Magento\Framework\Registry                      $registry
     * @param \Amasty\Rma\Helper\Data                          $helper
     * @param AddressRenderer                                  $addressRenderer
     * @param array                                            $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,

        ObjectManagerInterface $objectManager,
        \Magento\Theme\Block\Html\Header\Logo $logo,
        \Magento\Framework\Registry $registry,
        \Amasty\Rma\Helper\Data $helper,
        AddressRenderer $addressRenderer,

        array $data = []
    ) {
        parent::__construct($context, $data);

        $this->registry = $registry;
        $this->helper = $helper;
        $this->logo = $logo;
        $this->addressRenderer = $addressRenderer;
        $this->objectManager = $objectManager;

        /** @var \Amasty\Rma\Model\ResourceModel\Item\Collection $items */
        $items = $this->objectManager->create(
            '\Amasty\Rma\Model\ResourceModel\Item\Collection'
        );

        $items->addFilter('request_id', $this->getRmaRequest()->getId());

        $this->setData('items', $items);

        $this->pageConfig->getTitle()->set(__('Export RMA'));
    }

    /**
     * @return string
     */
    public function getLogoSrc()
    {
        return $this->logo->getLogoSrc();
    }

    /**
     * @return string
     */
    public function getLogoAlt()
    {
        return $this->logo->getLogoAlt();
    }

    /**
     * @return \Amasty\Rma\Model\Request
     */
    public function getRmaRequest()
    {
        return $this->registry->registry('amrma_request');
    }

    /**
     * @return string
     */
    public function getReturnAddress()
    {
        return $this->helper->getReturnAddress();
    }

    public function getCustomerAddress()
    {
        $request = $this->getRmaRequest();
        
        /** @var \Magento\Sales\Model\Order $order */
        $order = $this->objectManager->create('\Magento\Sales\Model\Order');

        $order->load($request->getData('order_id'));
        
        return $this->addressRenderer->format($order->getShippingAddress(), 'html');
    }
}
