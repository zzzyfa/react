<?php
/**
 * Created by PhpStorm.
 * User: alvin
 * Date: 06/03/2018
 * Time: 11:05 AM
 */

namespace Althea\SocialLogin\Model\ResourceModel;

use TemplateMonster\SocialLogin\Model\OAuthToken as Token;

class OAuthToken extends \TemplateMonster\SocialLogin\Model\ResourceModel\OAuthToken {

	protected $_storeManager;

	/**
	 * @inheritDoc
	 */
	public function __construct(
		\Magento\Store\Model\StoreManager $storeManager,
		\Magento\Framework\Model\ResourceModel\Db\Context $context,
		string $connectionName = null
	)
	{
		$this->_storeManager = $storeManager;

		parent::__construct($context, $connectionName);
	}

	/**
	 * @inheritDoc
	 */
	public function getByProvider(Token $token, $code, $id)
	{
		$select = $this
			->getConnection()
			->select()
			->from($this->getMainTable())
			->where('provider_code = ?', $code)
			->where('provider_id = ?', $id)
			->where('website_id = ?', $this->_storeManager->getStore()->getWebsiteId())
			->limit(1);
		$data = $this->getConnection()->fetchRow($select);

		if (false !== $data) {
			$token->setData($data);
		}

		return $this;
	}

}