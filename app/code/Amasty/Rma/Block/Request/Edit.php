<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Rma
 */


namespace Amasty\Rma\Block\Request;

use Magento\Catalog\Model\Product\Type as ProductType;
use Magento\Sales\Model\Order\Address;
use Magento\Store\Model\ScopeInterface;
use Magento\Sales\Model\Order\Address\Renderer as AddressRenderer;

class Edit extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;
    /**
     * @var \Amasty\Rma\Helper\Data
     */
    protected $helper;
    /**
     * @var \Amasty\Rma\Model\Item
     */
    protected $rmaItem;
    /**
     * @var AddressRenderer
     */
    protected $addressRenderer;
    /**
     * @var \Magento\Framework\App\Response\RedirectInterface
     */
    protected $redirect;
    /**
     * @var \Amasty\Rma\Model\Request
     */
    protected $rmaRequest;

    /**
     * History constructor.
     *
     * @param \Magento\Framework\View\Element\Template\Context  $context
     * @param \Magento\Framework\Registry                       $registry
     * @param \Amasty\Rma\Helper\Data                           $helper
     * @param \Amasty\Rma\Model\Item                            $rmaItem
     * @param AddressRenderer                                   $addressRenderer
     * @param \Magento\Framework\App\Response\RedirectInterface $redirect
     * @param \Amasty\Rma\Model\Request                         $rmaRequest
     * @param array                                             $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,

        \Magento\Framework\Registry $registry,
        \Amasty\Rma\Helper\Data $helper,
        \Amasty\Rma\Model\Item $rmaItem,
        AddressRenderer $addressRenderer,
        \Magento\Framework\App\Response\RedirectInterface $redirect,
        \Amasty\Rma\Model\Request $rmaRequest,

        array $data = []
    ) {
        parent::__construct($context, $data);

        $this->registry = $registry;
        $this->helper = $helper;
        $this->rmaItem = $rmaItem;
        $this->addressRenderer = $addressRenderer;
        $this->redirect = $redirect;
        $this->rmaRequest = $rmaRequest;

        $items = $this->rmaItem->getOrderItems(
            $this->getOrder()->getId(), 
            true
        );

        $this->addData([
            'conditions'  => $this->helper->getConditions(),
            'resolutions' => $this->helper->getResolutions(),
            'reasons'     => $this->helper->getReasons(),

            'items'       => $items
        ]);
    }

    /**
     * @return \Magento\Sales\Model\Order
     */
    public function getOrder()
    {
        return $this->registry->registry('current_order');
    }

    /**
     * @param Address $address
     *
     * @return null|string
     */
    public function renderAddress(Address $address)
    {
        return $this->addressRenderer->format($address, 'html');
    }
    
    public function getJsonConfig()
    {
        $items = $this->getData('items');
        
        $config = [];

        /** @var \Magento\Sales\Model\Order\Item $item */
        foreach ($items as $item) {

            $config[$item->getId()] = [
                'id'   => $item->getId(),
                'type' => $item->getProductType(),
                'qty'  => $item->getData('available_qty')
            ];
            if ($item->getHasChildren()) {
                /** @var \Magento\Sales\Model\Order\Item $childrenItem */
                foreach ($item->getChildrenItems() as $childrenItem) {
                    $config[$item->getId()]['child'][$childrenItem->getId()] = [
                        'id'   => $childrenItem->getId(),
                        'type' => $childrenItem->getProductType(),
                        'qty'  => $childrenItem->getData('available_qty')
                    ];
                }
            }
        }
        
        return \Zend_Json::encode($config);
    }

    /**
     * @return array
     */
    public function getBundles()
    {
        $result = [];

        /** @var \Magento\Sales\Model\Order\Item $item */
        foreach($this->getData('items') as $item) {
            if ($item->getProductType() == ProductType::TYPE_BUNDLE) {
                $result[$item->getId()] = $item;
            }
        }
        
        return $result;
    }

    /**
     * @return array
     */
    public function getSelectItems()
    {
        if (!$this->hasData('select_items')) {
            $result = [];

            /** @var \Magento\Sales\Model\Order\Item $item */
            foreach ($this->getData('items') as $item) {
                $result[$item->getId()] = $item;
            }
            
            $this->setData('select_items', $result);
        }
        
        return $this->getData('select_items');
    }

    /**
     * @return \Magento\Sales\Model\Order\Item
     */
    public function getFirstItemId()
    {
        $selectItems = $this->getSelectItems();
        
        return reset($selectItems);
    }

    /**
     * @return string
     */
    public function getBackUrl()
    {
        return $this->redirect->getRefererUrl();
    }

    /**
     * @return bool
     */
    public function getIsEnablePerItem()
    {
        return $this->_scopeConfig->isSetFlag(
            'amrma/general/enable_per_item', ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @return string
     */
    public function getExtraTitle()
    {
        return $this->_scopeConfig->getValue(
            'amrma/extra/title', ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @return \Amasty\Rma\Model\Request
     */
    public function getRmaRequest()
    {
        return $this->rmaRequest;
    }
}
