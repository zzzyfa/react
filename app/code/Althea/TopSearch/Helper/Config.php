<?php
/**
 * Created by PhpStorm.
 * User: alvin
 * Date: 02/10/2017
 * Time: 5:52 PM
 */

namespace Althea\TopSearch\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\ScopeInterface;

class Config extends AbstractHelper {

	const XML_PATH_GENERAL_ENABLED      = 'topsearch/general/is_enabled';
	const XML_PATH_GENERAL_SEARCH_TERMS = 'topsearch/general/search_terms';

	/**
	 * @param null $storeId
	 * @return mixed
	 */
	public function getGeneralEnabled($storeId = null)
	{
		return $this->scopeConfig->getValue(self::XML_PATH_GENERAL_ENABLED, ScopeInterface::SCOPE_STORE, $storeId);
	}

	/**
	 * @param null $storeId
	 * @return mixed
	 */
	public function getGeneralSearchTerms($storeId = null)
	{
		return $this->scopeConfig->getValue(self::XML_PATH_GENERAL_SEARCH_TERMS, ScopeInterface::SCOPE_STORE, $storeId);
	}

}