<?php
/**
 * Created by PhpStorm.
 * User: alvin
 * Date: 18/07/2017
 * Time: 4:46 PM
 */

namespace Althea\OneSignal\Block;

use Althea\OneSignal\Helper\Config;
use Magento\Customer\Model\Session;
use Magento\Framework\View\Element\Template;

class WebPushNotification extends Template {

	protected $configHelper;
	protected $customerSession;

	public function __construct(Template\Context $context, array $data = [], Config $configHelper, Session $customerSession)
	{
		$this->configHelper    = $configHelper;
		$this->customerSession = $customerSession;

		parent::__construct($context, $data);
	}

	/**
	 * Get module status
	 *
	 * @return mixed
	 */
	public function getIsEnabled()
	{
		return $this->configHelper->getGenerableEnable();
	}

	/**
	 * Get application ID
	 *
	 * @return mixed
	 */
	public function getAppId()
	{
		return $this->configHelper->getAppId();
	}

	/**
	 * Get 'auto prompt subscription for HTTPS' option
	 *
	 * @return mixed
	 */
	public function getIsAutoRegister()
	{
		return $this->configHelper->isAutoRegister();
	}

	/**
	 * Get OneSignal subdomain
	 *
	 * @return mixed
	 */
	public function getSubdomain()
	{
		return $this->configHelper->getSubdomain();
	}

	/**
	 * Get safari web ID
	 *
	 * @return mixed
	 */
	public function getSafariWebId()
	{
		return $this->configHelper->getSafariWebId();
	}

	/**
	 * Get 'display notification button' option
	 *
	 * @return mixed
	 */
	public function getIsShowNotifyButton()
	{
		return $this->configHelper->isShowNotifyButton();
	}

	/**
	 * Get OneSignal user tags
	 *
	 * @return string
	 */
	public function getCurrentTags()
	{
		$tags = [
			'email'    => '',
			'store_id' => '',
		];

		if ($this->customerSession->isLoggedIn()) {

			$customer         = $this->customerSession->getCustomer();
			$tags['email']    = $customer->getEmail();
			$tags['store_id'] = $customer->getStoreId();
		}

		return json_encode($tags);
	}

}