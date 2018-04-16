<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Rma
 */


namespace Amasty\Rma\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\UrlInterface;
use Magento\Sales\Model\Order;

class ProcessLayoutRenderElement implements ObserverInterface
{
    /**
     * @var \Amasty\Rma\Helper\Data
     */
    protected $helper;
    /**
     * @var UrlInterface
     */
    protected $urlBuilder;

    /**
     * ProcessLayoutRenderElement constructor.
     *
     * @param \Amasty\Rma\Helper\Data $helper
     * @param UrlInterface            $urlBuilder
     */
    public function __construct(
        \Amasty\Rma\Helper\Data $helper,
        UrlInterface $urlBuilder
    ) {
        $this->helper = $helper;
        $this->urlBuilder = $urlBuilder;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $event = $observer->getEvent();
        /** @var \Magento\Framework\View\Layout $layout */
        $layout = $event->getLayout();
        $name = $event->getElementName();

        if (in_array($name, ['sales.order.history', 'customer_account_dashboard_top'])) {

            /** @var \Magento\Sales\Block\Order\History $block */
            $block = $layout->getBlock($name);

            /** @var \Magento\Sales\Model\ResourceModel\Order\Collection $orders */
            $orders = $block->getOrders();

            $transport = $event->getTransport();
            $output = $transport->getData('output');

            $dom = new \DOMDocument();
            $dom->loadHTML('<?xml encoding="utf-8" ?>' . $output);
            $domx = new \DOMXPath($dom);

            $thead = $domx->evaluate("//table[@id='my-orders-table']/thead/tr");
            if ($thead && $thead->item(0)) {
                $thead->item(0)->appendChild($dom->createElement('th', '&nbsp;'));

                $entries = $domx->evaluate("//table[@id='my-orders-table']/tbody/*");

                $i = 0;
                /** @var Order $order */
                foreach ($orders as $order) {
                    $td = $dom->createElement('td');

                    if ($this->helper->canCreateRma($order)) {
                        $a = $dom->createElement('a', __('Return'));
                        $a->setAttribute(
                            'href', $this->urlBuilder->getUrl(
                            'amasty_rma/request/new',
                            ['order_id' => $order->getId()]
                        ));
                        $td->appendChild($a);
                    }

                    $entries->item($i++)->appendChild($td);
                }

                $output = $dom->saveHTML();

                $transport->setData('output', $output);
            }
        }
    }
}
