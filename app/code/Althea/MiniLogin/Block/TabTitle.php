<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Althea\MiniLogin\Block;

use Magento\Customer\Model\Context;

class TabTitle extends \Magento\Framework\View\Element\Template
{
    /**
     * Customer session
     *
     * @var \Magento\Framework\App\Http\Context
     */
    protected $httpContext;
    /**
     * @var \Magento\Customer\Model\Url
     */
    protected $_customerUrl;

    /**
     * @var Session
     */
    protected $_customerSession;

    /**
     * @var int
     */
    private $_username = -1;

    /**
     * @var \Magento\Customer\Api\CustomerRepositoryInterface
     */
    protected $customerRepository;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Customer\Model\Url $customerUrl
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\App\Http\Context $httpContext,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Customer\Model\Url $customerUrl,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        \Magento\Customer\CustomerData\JsLayoutDataProviderPoolInterface $jsLayoutDataProvider,
        array $data = [])
    {
        $this->httpContext = $httpContext;
        $this->customerRepository = $customerRepository;
        parent::__construct($context,$data);
        $this->_customerSession = $customerSession;
        $this->_customerUrl = $customerUrl;
    }

    /**
     * Is logged in
     *
     * @return bool
     */
    public function isLoggedIn()
    {
        return $this->httpContext->getValue(\Magento\Customer\Model\Context::CONTEXT_AUTH);
    }

    public function getTabTitle(){
        return $this->isLoggedIn() ? $this->getCustomerName() : __('LOGIN / SIGN UP');
    }

    public function getCustomerName(){
        if($this->_customerSession->getData('customer_id'))
        {
            $customer = $this->customerRepository->getById($this->_customerSession->getData('customer_id'));
            $firstname = $customer->getFirstname();

            if(strlen($firstname) > 12){
                $firstname = substr($firstname, 0, 12);
            }

            return $firstname;
        }
        else
        {
            return null;
        }
    }
}