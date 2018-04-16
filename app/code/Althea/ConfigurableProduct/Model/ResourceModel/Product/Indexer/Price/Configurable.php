<?php
/**
 * Created by PhpStorm.
 * User: alvin
 * Date: 20/12/2017
 * Time: 6:36 PM
 */

namespace Althea\ConfigurableProduct\Model\ResourceModel\Product\Indexer\Price;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Store\Api\StoreResolverInterface;
use Magento\Store\Model\Store;

class Configurable extends \Magento\ConfigurableProduct\Model\ResourceModel\Product\Indexer\Price\Configurable {

	protected $_storeResolver;

	/**
	 * @inheritDoc
	 */
	public function __construct(
		\Magento\Framework\Model\ResourceModel\Db\Context $context,
		\Magento\Framework\Indexer\Table\StrategyInterface $tableStrategy,
		\Magento\Eav\Model\Config $eavConfig,
		\Magento\Framework\Event\ManagerInterface $eventManager,
		\Magento\Framework\Module\Manager $moduleManager,
		string $connectionName = null,
		StoreResolverInterface $storeResolver = null
	)
	{
		$this->_storeResolver = $storeResolver ?: \Magento\Framework\App\ObjectManager::getInstance()->get(StoreResolverInterface::class);

		parent::__construct($context, $tableStrategy, $eavConfig, $eventManager, $moduleManager, $connectionName, $storeResolver);
	}

	/**
	 * @inheritDoc
	 */
	protected function prepareFinalPriceDataForType($entityIds, $type)
	{
		$this->_prepareDefaultFinalPriceTable();
		$metadata = $this->getMetadataPool()->getMetadata(\Magento\Catalog\Api\Data\ProductInterface::class);
		$connection = $this->getConnection();
		$select = $connection->select()->from(
			['e' => $this->getTable('catalog_product_entity')],
			['entity_id']
		)->join(
			['cg' => $this->getTable('customer_group')],
			'',
			['customer_group_id']
		)->join(
			['cw' => $this->getTable('store_website')],
			'',
			['website_id']
		)->join(
			['cwd' => $this->_getWebsiteDateTable()],
			'cw.website_id = cwd.website_id',
			[]
		)->join(
			['csg' => $this->getTable('store_group')],
			'csg.website_id = cw.website_id AND cw.default_group_id = csg.group_id',
			[]
		)->join(
			['cs' => $this->getTable('store')],
			'csg.default_store_id = cs.store_id AND cs.store_id != 0',
			[]
		)->join(
			['pw' => $this->getTable('catalog_product_website')],
			'pw.product_id = e.entity_id AND pw.website_id = cw.website_id',
			[]
		)->joinLeft(
			['tp' => $this->_getTierPriceIndexTable()],
			'tp.entity_id = e.entity_id AND tp.website_id = cw.website_id' .
			' AND tp.customer_group_id = cg.customer_group_id',
			[]
		);

		if ($type !== null) {
			$select->where('e.type_id = ?', $type);
		}

		// add enable products limitation
		$statusCond = $connection->quoteInto(
			'=?',
			\Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED
		);
		$this->_addAttributeToSelect(
			$select,
			'status',
			'e.' . $metadata->getLinkField(),
			'cs.store_id',
			$statusCond,
			true
		);
		if ($this->moduleManager->isEnabled('Magento_Tax')) {
			$taxClassId = $this->_addAttributeToSelect(
				$select,
				'tax_class_id',
				'e.' . $metadata->getLinkField(),
				'cs.store_id'
			);
		} else {
			$taxClassId = new \Zend_Db_Expr('0');
		}
		$select->columns(['tax_class_id' => $taxClassId]);

		$price = $this->_addAttributeToSelect(
			$select,
			'price',
			'e.' . $metadata->getLinkField(),
			'cs.store_id'
		);
		$specialPrice = $this->_addAttributeToSelect(
			$select,
			'special_price',
			'e.' . $metadata->getLinkField(),
			'cs.store_id'
		);
		$specialFrom = $this->_addAttributeToSelect(
			$select,
			'special_from_date',
			'e.' . $metadata->getLinkField(),
			'cs.store_id'
		);
		$specialTo = $this->_addAttributeToSelect(
			$select,
			'special_to_date',
			'e.' . $metadata->getLinkField(),
			'cs.store_id'
		);
		$currentDate = $connection->getDatePartSql('cwd.website_date');

		$specialFromDate = $connection->getDatePartSql($specialFrom);
		$specialToDate = $connection->getDatePartSql($specialTo);

		$specialFromUse = $connection->getCheckSql("{$specialFromDate} <= {$currentDate}", '1', '0');
		$specialToUse = $connection->getCheckSql("{$specialToDate} >= {$currentDate}", '1', '0');
		$specialFromHas = $connection->getCheckSql("{$specialFrom} IS NULL", '1', "{$specialFromUse}");
		$specialToHas = $connection->getCheckSql("{$specialTo} IS NULL", '1', "{$specialToUse}");

		// althea:
		// - ignore special_price of configurable parent product
		// - special_price will be based on configurable child product
		$configurableType = \Magento\ConfigurableProduct\Model\Product\Type\Configurable::TYPE_CODE;
		$finalPrice       = $connection->getCheckSql(
			sprintf(
				"e.type_id != '%s' AND %s > 0 AND %s > 0 AND %s < %s",
				$configurableType,
				$specialFromHas,
				$specialToHas,
				$specialPrice,
				$price
			),
			$specialPrice,
			$price
		);

		$select->columns(
			[
				'orig_price' => $connection->getIfNullSql($price, 0),
				'price' => $connection->getIfNullSql($finalPrice, 0),
				'min_price' => $connection->getIfNullSql($finalPrice, 0),
				'max_price' => $connection->getIfNullSql($finalPrice, 0),
				'tier_price' => new \Zend_Db_Expr('tp.min_price'),
				'base_tier' => new \Zend_Db_Expr('tp.min_price'),
			]
		);

		if ($entityIds !== null) {
			$select->where('e.entity_id IN(?)', $entityIds);
		}

		/**
		 * Add additional external limitation
		 */
		$this->_eventManager->dispatch(
			'prepare_catalog_product_index_select',
			[
				'select' => $select,
				'entity_field' => new \Zend_Db_Expr('e.entity_id'),
				'website_field' => new \Zend_Db_Expr('cw.website_id'),
				'store_field' => new \Zend_Db_Expr('cs.store_id')
			]
		);

		$query = $select->insertFromSelect($this->_getDefaultFinalPriceTable(), [], false);
		$connection->query($query);
		return $this;
	}

	/**
	 * @inheritDoc
	 */
	protected function _applyConfigurableOption()
	{
		$metadata = $this->getMetadataPool()->getMetadata(ProductInterface::class);
		$connection = $this->getConnection();
		$coaTable = $this->_getConfigurableOptionAggregateTable();
		$copTable = $this->_getConfigurableOptionPriceTable();

		$this->_prepareConfigurableOptionAggregateTable();
		$this->_prepareConfigurableOptionPriceTable();

		$statusAttribute = $this->_getAttribute(ProductInterface::STATUS);
		$linkField = $metadata->getLinkField();

		$select = $connection->select()->from(
			['i' => $this->_getDefaultFinalPriceTable()],
			[]
		)->join(
			['e' => $this->getTable('catalog_product_entity')],
			'e.entity_id = i.entity_id',
			['parent_id' => 'e.entity_id']
		)->join(
			['l' => $this->getTable('catalog_product_super_link')],
			'l.parent_id = e.' . $linkField,
			['product_id']
		)->columns(
			['customer_group_id', 'website_id'],
			'i'
		)->join(
			['le' => $this->getTable('catalog_product_entity')],
			'le.entity_id = l.product_id',
			[]
		)->where(
			'le.required_options=0'
		)->joinLeft(
			['status_global_attr' => $statusAttribute->getBackendTable()],
			"status_global_attr.{$linkField} = le.{$linkField}"
			. ' AND status_global_attr.attribute_id  = ' . (int)$statusAttribute->getAttributeId()
			. ' AND status_global_attr.store_id  = ' . Store::DEFAULT_STORE_ID,
			[]
		)->joinLeft(
			['status_attr' => $statusAttribute->getBackendTable()],
			"status_attr.{$linkField} = le.{$linkField}"
			. ' AND status_attr.attribute_id  = ' . (int)$statusAttribute->getAttributeId()
			. ' AND status_attr.store_id  = ' . $this->_storeResolver->getCurrentStoreId(), // althea: bypass private access to storeResolver
			[]
		)->where(
			'IFNULL(status_attr.value, status_global_attr.value) = ?', Status::STATUS_ENABLED
		)->group(
			['e.entity_id', 'i.customer_group_id', 'i.website_id', 'l.product_id']
		);

		// althea:
		// - https://github.com/magento/magento2/issues/7367
		// - temp. fix price data is not website specific
		$select->join(
			['sg' => $this->getTable('store_group')],
			'sg.website_id = i.website_id',
			[]
		);

		// althea:
		// - use special_price instead of price if special_price exists and is lower than price
		// - special_from_date and special_to_date filter is not applied
		$price           = $this->_addAttributeToSelect($select, 'price', 'le.' . $linkField, 'sg.default_store_id', null, true);
		$specialPrice    = $this->_addAttributeToSelect($select, 'special_price', 'le.' . $linkField, 'sg.default_store_id', null, true);
		$finalPrice      = $connection->getCheckSql(
			sprintf(
				"%s < %s",
				$specialPrice,
				$price
			),
			$specialPrice,
			$price
		);
		$tierPriceColumn = $connection->getIfNullSql('MIN(i.tier_price)', 'NULL');

		$select->columns(
			['price' => $connection->getIfNullSql($finalPrice, 0), 'tier_price' => $tierPriceColumn]
		);

		$query = $select->insertFromSelect($coaTable);
		$connection->query($query);

		$select = $connection->select()->from(
			[$coaTable],
			[
				'parent_id',
				'customer_group_id',
				'website_id',
				'MIN(price)',
				'MAX(price)',
				'MIN(tier_price)',
			]
		)->group(
			['parent_id', 'customer_group_id', 'website_id']
		);

		$query = $select->insertFromSelect($copTable);
		$connection->query($query);

		$table = ['i' => $this->_getDefaultFinalPriceTable()];
		$select = $connection->select()->join(
			['io' => $copTable],
			'i.entity_id = io.entity_id AND i.customer_group_id = io.customer_group_id' .
			' AND i.website_id = io.website_id',
			[]
		);
		$select->columns(
			[
				'min_price' => new \Zend_Db_Expr('i.min_price - i.orig_price + io.min_price'),
				'max_price' => new \Zend_Db_Expr('i.max_price - i.orig_price + io.max_price'),
				'tier_price' => 'io.tier_price',
			]
		);

		$query = $select->crossUpdateFromSelect($table);
		$connection->query($query);

		$connection->delete($coaTable);
		$connection->delete($copTable);

		return $this;
	}

}