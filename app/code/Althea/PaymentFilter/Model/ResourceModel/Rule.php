<?php
/**
 * Created by PhpStorm.
 * User: alvin
 * Date: 07/09/2017
 * Time: 2:06 PM
 */

namespace Althea\PaymentFilter\Model\ResourceModel;

use Althea\PaymentFilter\Api\Data\RuleInterface;
use Magento\Framework\DB\Select;
use Magento\Framework\EntityManager\EntityManager;
use Magento\Framework\EntityManager\MetadataPool;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\ResourceModel\Db\Context;
use Magento\Rule\Model\ResourceModel\AbstractResource;
use Magento\Store\Model\Store;

class Rule extends AbstractResource {

	/**
	 * @var EntityManager
	 */
	protected $entityManager;

	/**
	 * @var MetadataPool
	 */
	protected $metadataPool;

	/**
	 * @inheritDoc
	 */
	public function __construct(
		Context $context,
		EntityManager $entityManager,
		MetadataPool $metadataPool,
		$connectionName = null
	)
	{
		$this->entityManager = $entityManager;
		$this->metadataPool = $metadataPool;

		parent::__construct($context, $connectionName);
	}

	/**
	 * Initialize resource model
	 *
	 * @return void
	 */
	protected function _construct()
	{
		$this->_init('althea_paymentfilter_rule', 'rule_id');
	}

	/**
	 * @inheritDoc
	 */
	public function getConnection()
	{
		return $this->metadataPool->getMetadata(RuleInterface::class)->getEntityConnection();
	}

	/**
	 * @param AbstractModel $object
	 * @param mixed         $value
	 * @param null          $field
	 * @return bool|int|string
	 * @throws LocalizedException
	 * @throws \Exception
	 */
	private function getRuleId(AbstractModel $object, $value, $field = null)
	{
		$entityMetadata = $this->metadataPool->getMetadata(RuleInterface::class);
		$field          = $entityMetadata->getIdentifierField();

		if (!$field) {

			$field = $entityMetadata->getIdentifierField();
		}

		$entityId = $value;

		if ($field != $entityMetadata->getIdentifierField() || $object->getStoreId()) {

			$select = $this->_getLoadSelect($field, $value, $object);

			$select->reset(Select::COLUMNS)
			       ->columns($this->getMainTable() . '.' . $entityMetadata->getIdentifierField())
			       ->limit(1);

			$result   = $this->getConnection()->fetchCol($select);
			$entityId = count($result) ? $result[0] : false;
		}

		return $entityId;
	}

	/**
	 * Load an object
	 *
	 * @param \Althea\PaymentFilter\Model\Rule|AbstractModel $object
	 * @param mixed                                          $value
	 * @param string                                         $field field to load by (defaults to model id)
	 * @return $this
	 */
	public function load(AbstractModel $object, $value, $field = null)
	{
		$ruleId = $this->getRuleId($object, $value, $field);

		if ($ruleId) {

			$this->entityManager->load($object, $ruleId);
		}

		return $this;
	}

	/**
	 * Retrieve select object for load object data
	 *
	 * @param string                                 $field
	 * @param mixed                                  $value
	 * @param \Magento\Cms\Model\Block|AbstractModel $object
	 * @return Select
	 */
	protected function _getLoadSelect($field, $value, $object)
	{
		$entityMetadata = $this->metadataPool->getMetadata(RuleInterface::class);
		$linkField      = $entityMetadata->getLinkField();
		$select         = parent::_getLoadSelect($field, $value, $object);

		if ($object->getStoreId()) {

			$stores = [(int)$object->getStoreId(), Store::DEFAULT_STORE_ID];

			$select->join(
				['aprs' => $this->getTable('althea_paymentfilter_rule_store')],
				$this->getMainTable() . '.' . $linkField . ' = aprs.' . $linkField,
				['store_id']
			)
			       ->where('status = ?', 1)
			       ->where('aprs.store_id in (?)', $stores)
			       ->order('store_id DESC')
			       ->limit(1);
		}

		return $select;
	}

	/**
	 * Get store ids to which specified item is assigned
	 *
	 * @param int $id
	 * @return array
	 */
	public function lookupStoreIds($id)
	{
		$connection     = $this->getConnection();
		$entityMetadata = $this->metadataPool->getMetadata(RuleInterface::class);
		$linkField      = $entityMetadata->getLinkField();
		$select         = $connection->select()
		                             ->from(['aprs' => $this->getTable('althea_paymentfilter_rule_store')], 'store_id')
		                             ->join(
			                             ['apr' => $this->getMainTable()],
			                             'aprs.' . $linkField . ' = apr.' . $linkField,
			                             []
		                             )
		                             ->where('apr.' . $entityMetadata->getIdentifierField() . ' = :rule_id');

		return $connection->fetchCol($select, ['rule_id' => (int)$id]);
	}

	/**
	 * @inheritDoc
	 */
	public function save(\Magento\Framework\Model\AbstractModel $object)
	{
		$this->entityManager->save($object);

		return $this;
	}

	/**
	 * @inheritDoc
	 */
	public function delete(\Magento\Framework\Model\AbstractModel $object)
	{
		$this->entityManager->delete($object);

		return $this;
	}

}