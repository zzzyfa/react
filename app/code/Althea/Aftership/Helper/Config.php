<?php
/**
 * Created by PhpStorm.
 * User: alvin
 * Date: 05/12/2017
 * Time: 5:15 PM
 */

namespace Althea\Aftership\Helper;

use Magento\Store\Model\ScopeInterface;
use Mrmonsters\Aftership\Helper\Config as AftershipConfig;

class Config extends AftershipConfig {

	const XML_PATH_MESSAGES_TRACKING_DOMAIN        = 'aftership_options/messages/tracking_domain';
	const XML_PATH_MESSAGES_WEBHOOK_KEY            = 'aftership_options/messages/webhook_key';
	const XML_PATH_MESSAGES_WEBHOOK_SALT           = 'aftership_options/messages/webhook_salt';
	const XML_PATH_CUSTOM_TRACKERS_GENERAL_ENABLED = 'customtrackers/general/enabled';
	const XML_PATH_COURIER_SLUG                    = 'customtrackers/%s/courier_slug';
	const XML_PATH_COURIER_TITLE                   = 'customtrackers/%s/title';

	public function getExtensionTrackingDomain($storeId = null)
	{
		return $this->scopeConfig->getValue(self::XML_PATH_MESSAGES_TRACKING_DOMAIN, ScopeInterface::SCOPE_STORES, $storeId);
	}

	public function getExtensionWebhookKey($storeId = null)
	{
		return $this->scopeConfig->getValue(self::XML_PATH_MESSAGES_WEBHOOK_KEY, ScopeInterface::SCOPE_STORES, $storeId);
	}

	public function getExtensionWebhookSalt($storeId = null)
	{
		return $this->scopeConfig->getValue(self::XML_PATH_MESSAGES_WEBHOOK_SALT, ScopeInterface::SCOPE_STORES, $storeId);
	}

	public function getCustomTrackersEnabled($storeId = null)
	{
		return $this->scopeConfig->getValue(self::XML_PATH_CUSTOM_TRACKERS_GENERAL_ENABLED, ScopeInterface::SCOPE_STORES, $storeId);
	}

	public function getExtensionCourierSlug($tracker, $storeId = null)
	{
		return $this->scopeConfig->getValue(sprintf(self::XML_PATH_COURIER_SLUG, $tracker), ScopeInterface::SCOPE_STORES, $storeId);
	}

	public function getExtensionCourierTitle($tracker, $storeId = null)
	{
		return $this->scopeConfig->getValue(sprintf(self::XML_PATH_COURIER_TITLE, $tracker), ScopeInterface::SCOPE_STORES, $storeId);
	}

}