<?php
/**
 * Created by PhpStorm.
 * User: manadirmahi
 * Date: 17/10/2017
 * Time: 11:05 AM
 */

namespace Althea\ShopByBrand\Model\ResourceModel;

use Althea\ShopByBrand\Api\Data\FollowerInterface;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Model\ResourceModel\Db\Context;
use Magento\Framework\EntityManager\EntityManager;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\EntityManager\MetadataPool;

class Follower extends AbstractDb {

	/**
	 * @var EntityManager
	 */
	protected $entityManager;

	/**
	 * @var MetadataPool
	 */
	protected $metadataPool;

	/**
	 * Follower constructor.
	 *
	 * @param Context       $context
	 * @param EntityManager $entityManager
	 * @param MetadataPool  $metadataPool
	 * @param null          $connectionName
	 */
	public function __construct(
		Context $context,
		EntityManager $entityManager,
		MetadataPool $metadataPool,
		$connectionName = null
	)
	{
		$this->entityManager = $entityManager;
		$this->metadataPool  = $metadataPool;
		parent::__construct($context, $connectionName);
	}

	/**
	 * Initialize resource model
	 *
	 * @return void
	 */
	protected function _construct()
	{
		$this->_init('tm_brand_followers', 'follower_id');
	}

	/**
	 * @inheritDoc
	 */
	public function getConnection()
	{
		return $this->metadataPool->getMetadata(FollowerInterface::class)->getEntityConnection();
	}

	public function load(\Magento\Framework\Model\AbstractModel $follower, $followerId, $field = null)
	{
		$this->entityManager->load($follower, $followerId);

		return $this;
	}

}
