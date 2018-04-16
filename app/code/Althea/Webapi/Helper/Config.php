<?php
/**
 * Created by PhpStorm.
 * User: alvin
 * Date: 14/08/2017
 * Time: 6:38 PM
 */

namespace Althea\Webapi\Helper;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Helper\AbstractHelper;

class Config extends AbstractHelper {

	const XML_PATH_GENERAL_ENABLE      = 'althea_webapi/general/is_enabled';
	const XML_PATH_GENERAL_MSG_CONTENT = 'althea_webapi/general/msg_content';

	/**
	 * Get module status
	 *
	 * @param null $storeId
	 * @return mixed
	 */
	public function getGeneralEnable($storeId = null)
	{
		return $this->scopeConfig->getValue(self::XML_PATH_GENERAL_ENABLE, ScopeConfigInterface::SCOPE_TYPE_DEFAULT, $storeId);
	}

	/**
	 * Get message content
	 *
	 * @param null $storeId
	 * @return mixed
	 */
	public function getMsgContent($storeId = null)
	{
		return $this->scopeConfig->getValue(self::XML_PATH_GENERAL_MSG_CONTENT, ScopeConfigInterface::SCOPE_TYPE_DEFAULT, $storeId);
	}

}