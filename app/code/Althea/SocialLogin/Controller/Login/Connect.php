<?php
/**
 * Created by PhpStorm.
 * User: alvin
 * Date: 01/04/2018
 * Time: 8:29 AM
 */

namespace Althea\SocialLogin\Controller\Login;

use TemplateMonster\SocialLogin\Model\Exception;

class Connect extends \TemplateMonster\SocialLogin\Controller\Login\Connect {

	/**
	 * @var \Magento\Framework\Stdlib\Cookie\CookieMetadataFactory
	 */
	protected $_cookieMetadataFactory;

	/**
	 * @var \Magento\Framework\Stdlib\Cookie\PhpCookieManager
	 */
	protected $_cookieMetadataManager;

	/**
	 * Retrieve cookie manager
	 *
	 * @deprecated
	 * @return \Magento\Framework\Stdlib\Cookie\PhpCookieManager
	 */
	private function getCookieManager()
	{
		if (!$this->_cookieMetadataManager) {

			$this->_cookieMetadataManager = \Magento\Framework\App\ObjectManager::getInstance()->get(
				\Magento\Framework\Stdlib\Cookie\PhpCookieManager::class
			);
		}

		return $this->_cookieMetadataManager;
	}

	/**
	 * Retrieve cookie metadata factory
	 *
	 * @deprecated
	 * @return \Magento\Framework\Stdlib\Cookie\CookieMetadataFactory
	 */
	private function getCookieMetadataFactory()
	{
		if (!$this->_cookieMetadataFactory) {

			$this->_cookieMetadataFactory = \Magento\Framework\App\ObjectManager::getInstance()->get(
				\Magento\Framework\Stdlib\Cookie\CookieMetadataFactory::class
			);
		}

		return $this->_cookieMetadataFactory;
	}

	/**
	 * @inheritDoc
	 */
	public function execute()
	{
		$code = $this->getRequest()->getParam('provider');

		try {
			$provider = $this->_collection->getItemById($code);

			$token = $provider->getAccessToken();
			$data = $provider->getUserData($token);

			$customer = $this->_accountManagement->authenticateByOAuth($data);
			$this->_customerSession->setCustomerDataAsLoggedIn($customer);
			$this->_customerSession->regenerateId();

			// althea:
			// - invalidate cookie
			// - otherwise social login customer session is outdated
			// - based on Magento/Customer/Controller/Account/LoginPost.php
			if ($this->getCookieManager()->getCookie('mage-cache-sessid')) {
				$metadata = $this->getCookieMetadataFactory()->createCookieMetadata();
				$metadata->setPath('/');
				$this->getCookieManager()->deleteCookie('mage-cache-sessid', $metadata);
			}

			$this->messageManager->addSuccess(__('You have been successfully logged in.'));
		} catch (Exception $e) {
			$this->messageManager->addError(__($e->getMessage()));
		} catch (\Exception $e) {
			$this->messageManager->addError(__('There is an error occurred while trying to login.'));
		}

		if (!$this->_response->isRedirect()) {
			$redirect = $this->resultRedirectFactory->create();
			$redirect->setPath('/');

			return $redirect;
		}
	}

}