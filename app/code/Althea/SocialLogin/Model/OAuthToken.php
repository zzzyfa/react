<?php
/**
 * Created by PhpStorm.
 * User: alvin
 * Date: 06/03/2018
 * Time: 11:12 AM
 */

namespace Althea\SocialLogin\Model;

use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Registry;
use Magento\Framework\Stdlib\DateTime\DateTimeFactory;

class OAuthToken extends \TemplateMonster\SocialLogin\Model\OAuthToken {

	protected $_storeManager;

	/**
	 * @inheritDoc
	 */
	public function __construct(
		\Magento\Store\Model\StoreManager $storeManager,
		DateTimeFactory $dateFactory,
		Context $context,
		Registry $registry,
		AbstractResource $resource = null,
		AbstractDb $resourceCollection = null,
		array $data = []
	)
	{
		$this->_storeManager = $storeManager;

		parent::__construct($dateFactory, $context, $registry, $resource, $resourceCollection, $data);
	}

	/**
	 * @inheritDoc
	 */
	public function beforeSave()
	{
		$this->setData('website_id', $this->_storeManager->getStore()->getWebsiteId());

		return parent::beforeSave();
	}

}