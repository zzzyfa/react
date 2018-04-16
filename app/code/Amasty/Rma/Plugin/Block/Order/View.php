<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Rma
 */


namespace Amasty\Rma\Plugin\Block\Order;

use Magento\Backend\Model\UrlInterface;

class View
{
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;
    /**
     * @var \Amasty\Rma\Helper\Data
     */
    protected $helper;
    /**
     * @var UrlInterface
     */
    protected $urlBuilder;

    /**
     * View constructor.
     *
     * @param UrlInterface            $urlBuilder
     * @param \Amasty\Rma\Helper\Data $helper
     */
    public function __construct(
        UrlInterface $urlBuilder,
        \Amasty\Rma\Helper\Data $helper
    )
    {
        $this->helper = $helper;
        $this->urlBuilder = $urlBuilder;
    }

    public function beforeSetLayout(
        \Magento\Sales\Block\Adminhtml\Order\View $subject,
        \Magento\Framework\View\LayoutInterface $layout
    ) {
        $order = $subject->getOrder();

        if (!$this->helper->canCreateRma($order))
            return;
        
        $url = $this->urlBuilder->getUrl('amasty_rma/request/create', [
            'order_id' => $order->getId(),
        ]);

        $subject->addButton(
            'amasty_rma',
            [
                'label' => __('Create RMA'),
                'class' => 'edit secondary',
                'onclick' => "window.open('$url')",
            ]
        );
    }
}
