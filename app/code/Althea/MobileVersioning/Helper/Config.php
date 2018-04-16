<?php
/**
 * Created by PhpStorm.
 * User: manadirmahi
 * Date: 04/10/2017
 * Time: 2:48 PM
 */

namespace Althea\MobileVersioning\Helper;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Helper\AbstractHelper;

class Config extends AbstractHelper {

	const XML_PATH_ANDROID_VERSION  = 'mobileversioning/general/android_version';
	const XML_PATH_ANDROID_PRIORITY = 'mobileversioning/general/android_priority';
	const XML_PATH_ANDROID_MESSAGE  = 'mobileversioning/general/android_message';
	const XML_PATH_IOS_VERSION      = 'mobileversioning/general/ios_version';
	const XML_PATH_IOS_PRIORITY     = 'mobileversioning/general/ios_priority';
	const XML_PATH_IOS_MESSAGE      = 'mobileversioning/general/ios_message';

	/**
	 * @param $storeId
	 * @return mixed
	 */
	public function getAndVer($storeId)
	{
		return $this->scopeConfig->getValue(self::XML_PATH_ANDROID_VERSION, ScopeConfigInterface::SCOPE_TYPE_DEFAULT, $storeId);
	}

	/**
	 * @param  $storeId
	 * @return mixed
	 */
	public function getAndPrio($storeId)
	{
		return $this->scopeConfig->getValue(self::XML_PATH_ANDROID_PRIORITY, ScopeConfigInterface::SCOPE_TYPE_DEFAULT, $storeId);
	}

	/**
	 * @param $storeId
	 * @return \Magento\Framework\Phrase
	 */

	public function getAndMsg($storeId)
	{
		return $this->scopeConfig->getValue(self::XML_PATH_ANDROID_MESSAGE, ScopeConfigInterface::SCOPE_TYPE_DEFAULT, $storeId);
	}

	/**
	 * @param  $storeId
	 * @return mixed
	 */
	public function getIosVer($storeId)
	{
		return $this->scopeConfig->getValue(self::XML_PATH_IOS_VERSION, ScopeConfigInterface::SCOPE_TYPE_DEFAULT, $storeId);
	}

	/**
	 * @param  $storeId
	 * @return mixed
	 */
	public function getIosPrio($storeId)
	{
		return $this->scopeConfig->getValue(self::XML_PATH_IOS_PRIORITY, ScopeConfigInterface::SCOPE_TYPE_DEFAULT, $storeId);
	}

	/**
	 * @param $storeId
	 * @return \Magento\Framework\Phrase
	 */
	public function getIosMsg($storeId)
	{
		return $this->scopeConfig->getValue(self::XML_PATH_IOS_MESSAGE, ScopeConfigInterface::SCOPE_TYPE_DEFAULT, $storeId);
	}

}