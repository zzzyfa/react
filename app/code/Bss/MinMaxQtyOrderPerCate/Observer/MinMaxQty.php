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

namespace Bss\MinMaxQtyOrderPerCate\Observer;

use \Magento\Framework\Event\Observer;
use \Magento\Framework\Event\ObserverInterface;
use Magento\Customer\Model\Session as CustomerSession;

class MinMaxQty implements ObserverInterface
{
    protected $customerSession;

    public function __construct(
         CustomerSession $customerSession,
        \Magento\Framework\App\Request\Http $request,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        \Bss\MinMaxQtyOrderPerCate\Helper\Data $minmaxHelper,
        \Magento\Checkout\Model\Cart $cart,
        \Magento\Catalog\Model\CategoryFactory $categoryFactory
    ) {
        $this->customerSession = $customerSession;
        $this->_request = $request;
        $this->_messageManager = $messageManager;
        $this->minmaxHelper = $minmaxHelper;
        $this->cart = $cart;
        $this->categoryFactory = $categoryFactory;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if($this->minmaxHelper->getConfig('enable')) {

            $cartItems = $this->cart->getQuote()->getAllVisibleItems();

            $i =1;
            $cates_qty = [];
            $cates_name = [];
            foreach ($cartItems as $item){

                $cates = $item->getProduct()->getCategoryIds();
                $cates_name[$item->getName()] = $cates;

                foreach ($cates as  $cate) {
                    if (is_array($cates_qty) and !empty($cates_qty[$cate]) ) {
                        $cates_qty[$cate] += $item->getQty();
                    }else{
                        $cates_qty[$cate] = $item->getQty();
                    }
                }
                $i++;
            }

            $customer = $this->customerSession->getCustomerGroupId();
            $_qty = $this->minmaxHelper->OrderQty($cates_qty, $customer);

            if (!empty($_qty)) { 
                if ($this->_request->getFullActionName() === 'checkout_cart_index') {
                
                    if(!empty($_qty['min_qty'])) {
                        
                        foreach ($_qty['min_qty'] as $catid => $qtylimit) {
                            $product_names = [];
                            foreach ($cates_name as $prt_name => $catids) {
                                if (in_array($catid,$catids)) {
                                    $product_names[] = $prt_name;
                                }
                            }
                            $product_name = implode(',',$product_names);
                            $cate_name = $this->categoryFactory->create()->load($catid)->getName();
                            $message = str_replace("{{category_name}}",$cate_name,$this->minmaxHelper->getConfig('mess_err_min'));
                            $message = str_replace("{{qty_limit}}",$qtylimit,$message);
                            $message = str_replace("{{product_name}}",$product_name,$message);
                            // $message = "The min quantity allowed for purchase at category ".$cate_name." is ".$qtylimit.' [ Product Name : '.$product_name.' ]';
                            $this->_messageManager->addError($message); 
                        }

                    }
                    if(!empty($_qty['max_qty'])) {

                         foreach ($_qty['max_qty'] as $catid => $qtylimit) {
                            $product_names = [];
                            foreach ($cates_name as $prt_name => $catids) {
                                if (in_array($catid,$catids)) {
                                    $product_names[] = $prt_name;
                                }
                            }
                            $product_name = implode(',',$product_names);
                            $cate_name = $this->categoryFactory->create()->load($catid)->getName();
                            $message = str_replace("{{category_name}}",$cate_name,$this->minmaxHelper->getConfig('mess_err_max'));
                            $message = str_replace("{{qty_limit}}",$qtylimit,$message);
                            $message = str_replace("{{product_name}}",$product_name,$message);          

                            // $message = "The max quantity allowed for purchase at category ".$cate_name." is ".$qtylimit.' [ Product Name : '.$product_name.' ]';
                            $this->_messageManager->addError($message); 
                        }

                    }
                }
                $this->cart->getQuote()->setHasError(true);
            }
        }
    }
}