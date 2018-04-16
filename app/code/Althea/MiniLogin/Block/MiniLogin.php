<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Althea\MiniLogin\Block;

use Magento\Customer\Model\Context;

class MiniLogin extends \Magento\Framework\View\Element\Template
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
     * @return string
     */
    public function getHref()
    {
        return $this->isLoggedIn()
            ? $this->_customerUrl->getLogoutUrl()
            : $this->_customerUrl->getLoginUrl();
    }

    /**
     * Returns popup config
     *
     * @return array
     */
    public function getConfig()
    {
        return [
            'customerRegisterUrl' => $this->getCustomerRegisterUrl(),
            'customerForgotPasswordUrl' => $this->getCustomerForgotPasswordUrl(),
            'baseUrl' => $this->getBaseUrl()
        ];
    }
    /**
     * Return base url.
     *
     * @return string
     */
    public function getBaseUrl()
    {
        return $this->_storeManager->getStore()->getBaseUrl();
    }

    /**
     * Get customer register url
     *
     * @return string
     */
    public function getCustomerRegisterUrl()
    {
        return $this->getUrl('customer/account/create');
    }

    /**
     * Get customer forgot password url
     *
     * @return string
     */
    public function getCustomerForgotPasswordUrl()
    {
        return $this->getUrl('customer/account/forgotpassword');
    }

    /**
     * Get forgot password action
     * @return string
     */
    public function getForgotPasswordActionUrl(){
        return $this->getUrl('customer/account/forgotpasswordpost');
    }

    /**
     * Get customer account url
     * @return string
     */
    public function getCustomerAccountUrl(){
        return $this->getUrl('customer/account/');
    }

    /**
     * get wishlist url
     * @return string
     */
    public function getWishlistUrl(){
        return $this->getUrl('wishlist/');
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

    /**
     * Retrieve username for form field
     *
     * @return string
     */
    public function getUsername()
    {
        if (-1 === $this->_username) {
            $this->_username = $this->_customerSession->getUsername(true);
        }
        return $this->_username;
    }

    /**
     * Check if autocomplete is disabled on storefront
     *
     * @return bool
     */
    public function isAutocompleteDisabled()
    {
        return (bool)!$this->_scopeConfig->getValue(
            \Magento\Customer\Model\Form::XML_PATH_ENABLE_AUTOCOMPLETE,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    public function getCustomerName(){
        if($this->_customerSession->getData('customer_id'))
        {
            $customer = $this->customerRepository->getById($this->_customerSession->getData('customer_id'));
            return $customer->getFirstname();
        }
        else
        {
            return null;
        }
    }

	/**
	 * Retrieve form posting url
	 *
	 * @return string
	 */
	public function getPostActionUrl()
	{
		return $this->_customerUrl->getLoginPostUrl();
	}

}