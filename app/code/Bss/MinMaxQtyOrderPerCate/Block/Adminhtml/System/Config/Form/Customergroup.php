<?php
/**
* BSS Commerce Co.
*
* NOTICE OF LICENSE
*
* This source file is subject to the EULA
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://bsscommerce.com/Bss-Commerce-License.txt
*
* =================================================================
*                 MAGENTO EDITION USAGE NOTICE
* =================================================================
* This package designed for Magento COMMUNITY edition
* BSS Commerce does not guarantee correct work of this extension
* on any other Magento edition except Magento COMMUNITY edition.
* BSS Commerce does not provide extension support in case of
* incorrect edition usage.
* =================================================================
*
* @category   BSS
* @package    Bss_MinMaxQtyOrderPerCate
* @author     Extension Team
* @copyright  Copyright (c) 2014-2016 BSS Commerce Co. ( http://bsscommerce.com )
* @license    http://bsscommerce.com/Bss-Commerce-License.txt
*/

namespace Bss\MinMaxQtyOrderPerCate\Block\Adminhtml\System\Config\Form;
class Customergroup extends \Magento\Framework\View\Element\Html\Select {
   /**
     * methodList
     *
     * @var array
     */
    protected $groupfactory;
 
 
    public function __construct(
    \Magento\Framework\View\Element\Context $context, \Magento\Customer\Model\GroupFactory $groupfactory, array $data = []
    ) {
        parent::__construct($context, $data);
        $this->groupfactory = $groupfactory;
    }  
    /**
     * Returns countries array
     *
     * @return array
     */ 
     /**
     * Render block HTML
     *
     * @return string
     */
     public function toOptionArray()
    {
        if (!$this->getOptions()) {
            $customerGroupCollection = $this->groupfactory->create()->getCollection();
            $cOptions[] = [
                'label' => 'Please Select a Customer Group',
                'value' => ''
            ];
            foreach ($customerGroupCollection as $customerGroup) {
                     $cOptions[] = [
                            'label' => htmlspecialchars($customerGroup->getCustomerGroupCode(), ENT_QUOTES),
                            'value' => $customerGroup->getCustomerGroupId()
                    ];
            }
        }
        return $cOptions;
    }
    
    public function _toHtml() {

        $options =  $this->toOptionArray();
        foreach ($options as $option) {
            $this->addOption($option['value'], $option['label']);
        }
        return parent::_toHtml();
    }
    /**
     * Sets name for input element
     *
     * @param string $value
     * @return $this
     */
    public function setInputName($value) {
        return $this->setName($value);
    }
}