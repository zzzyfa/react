<?php
/**
 * Created by PhpStorm.
 * User: alvin
 * Date: 11/08/2017
 * Time: 4:08 PM
 */

namespace Althea\Cms\Model\ResourceModel;

use Althea\Cms\Api\Data\AlertInterface;
use Magento\Framework\DB\Select;
use Magento\Framework\EntityManager\EntityManager;
use Magento\Framework\EntityManager\MetadataPool;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Model\ResourceModel\Db\Context;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManagerInterface;

class Alert extends AbstractDb {

	/**
	 * Store manager
	 *
	 * @var StoreManagerInterface
	 */
	protected $_storeManager;

	/**
	 * @var EntityManager
	 */
	protected $entityManager;

	/**
	 * @var MetadataPool
	 */
	protected $metadataPool;

	/**
	 * @param Context               $context
	 * @param StoreManagerInterface $storeManager
	 * @param EntityManager         $entityManager
	 * @param MetadataPool          $metadataPool
	 * @param string                $connectionName
	 */
	public function __construct(
		Context $context,
		StoreManagerInterface $storeManager,
		EntityManager $entityManager,
		MetadataPool $metadataPool,
		$connectionName = null
	)
	{
		$this->_storeManager = $storeManager;
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
		$this->_init('althea_cms_alert', 'alert_id');
	}

	/**
	 * @inheritDoc
	 */
	public function getConnection()
	{
		return $this->metadataPool->getMetadata(AlertInterface::class)->getEntityConnection();
	}

	/**
	 * Perform operations before object save
	 *
	 * @param AbstractModel $object
	 * @return $this
	 * @throws LocalizedException
	 */
	protected function _beforeSave(AbstractModel $object)
	{
		if (!$this->getIsUniqueAlertToStores($object)) {

			throw new LocalizedException(
				__('An alert identifier with the same properties already exists in the selected store.')
			);
		}

		return $this;
	}

	/**
	 * @param AbstractModel $object
	 * @param mixed         $value
	 * @param null          $field
	 * @return bool|int|string
	 * @throws LocalizedException
	 * @throws \Exception
	 */
	private function getAlertId(AbstractModel $object, $value, $field = null)
	{
		$entityMetadata = $this->metadataPool->getMetadata(AlertInterface::class);

		if (!is_numeric($value) && $field === null) {

			$field = 'identifier';
		} elseif (!$field) {

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
	 * @param \Althea\Cms\Model\Alert|AbstractModel $object
	 * @param mixed                                 $value
	 * @param string                                $field field to load by (defaults to model id)
	 * @return $this
	 */
	public function load(AbstractModel $object, $value, $field = null)
	{
		$alertId = $this->getAlertId($object, $value, $field);

		if ($alertId) {

			$this->entityManager->load($object, $alertId);
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
		$entityMetadata = $this->metadataPool->getMetadata(AlertInterface::class);
		$linkField      = $entityMetadata->getLinkField();
		$select         = parent::_getLoadSelect($field, $value, $object);

		if ($object->getStoreId()) {

			$stores = [(int)$object->getStoreId(), Store::DEFAULT_STORE_ID];

			$select->join(
				['acas' => $this->getTable('althea_cms_alert_store')],
				$this->getMainTable() . '.' . $linkField . ' = acas.' . $linkField,
				['store_id']
			)
			       ->where('is_active = ?', 1)
			       ->where('acas.store_id in (?)', $stores)
			       ->order('store_id DESC')
			       ->limit(1);
		}

		return $select;
	}

	/**
	 * Check for unique of identifier of alert to selected store(s).
	 *
	 * @param AbstractModel $object
	 * @return bool
	 * @SuppressWarnings(PHPMD.BooleanGetMethodName)
	 */
	public function getIsUniqueAlertToStores(AbstractModel $object)
	{
		$entityMetadata = $this->metadataPool->getMetadata(AlertInterface::class);
		$linkField      = $entityMetadata->getLinkField();

		if ($this->_storeManager->hasSingleStore()) {

			$stores = [Store::DEFAULT_STORE_ID];
		} else {

			$stores = (array)$object->getData('stores');
		}

		$select = $this->getConnection()->select()
		               ->from(['aca' => $this->getMainTable()])
		               ->join(
			               ['acas' => $this->getTable('althea_cms_alert_store')],
			               'aca.' . $linkField . ' = acas.' . $linkField,
			               []
		               )
		               ->where('aca.identifier = ?', $object->getData('identifier'))
		               ->where('acas.store_id IN (?)', $stores);

		if ($object->getId()) {

			$select->where('aca.' . $entityMetadata->getIdentifierField() . ' <> ?', $object->getId());
		}

		if ($this->getConnection()->fetchRow($select)) {

			return false;
		}

		return true;
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
		$entityMetadata = $this->metadataPool->getMetadata(AlertInterface::class);
		$linkField      = $entityMetadata->getLinkField();
		$select         = $connection->select()
		                             ->from(['acas' => $this->getTable('althea_cms_alert_store')], 'store_id')
		                             ->join(
			                             ['aca' => $this->getMainTable()],
			                             'acas.' . $linkField . ' = aca.' . $linkField,
			                             []
		                             )
		                             ->where('aca.' . $entityMetadata->getIdentifierField() . ' = :alert_id');

		return $connection->fetchCol($select, ['alert_id' => (int)$id]);
	}

	/**
	 * @param AbstractModel $object
	 * @return $this
	 * @throws \Exception
	 */
	public function save(AbstractModel $object)
	{
		$this->entityManager->save($object);

		return $this;
	}

	/**
	 * @inheritDoc
	 */
	public function delete(AbstractModel $object)
	{
		$this->entityManager->delete($object);

		return $this;
	}

}