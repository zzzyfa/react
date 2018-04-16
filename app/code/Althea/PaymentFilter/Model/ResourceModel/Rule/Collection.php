<?php
/**
 * Created by PhpStorm.
 * User: alvin
 * Date: 07/09/2017
 * Time: 2:51 PM
 */

namespace Althea\PaymentFilter\Model\ResourceModel\Rule;

use Althea\Cms\Model\ResourceModel\AbstractCollection;
use Althea\PaymentFilter\Api\Data\RuleInterface;
use Magento\Store\Model\Store;

class Collection extends AbstractCollection {

	/**
	 * @var string
	 */
	protected $_idFieldName = 'rule_id';

	/**
	 * Perform operations after collection load
	 *
	 * @return $this
	 */
	protected function _afterLoad()
	{
		$entityMetadata = $this->metadataPool->getMetadata(RuleInterface::class);

		$this->performAfterLoad('althea_paymentfilter_rule_store', $entityMetadata->getLinkField());

		return parent::_afterLoad();
	}

	/**
	 * Define resource model
	 *
	 * @return void
	 */
	protected function _construct()
	{
		$this->_init('Althea\PaymentFilter\Model\Rule', 'Althea\PaymentFilter\Model\ResourceModel\Rule');
		$this->_map['fields']['store'] = 'store_table.store_id';
	}

	/**
	 * Returns pairs rule_id - title
	 *
	 * @return array
	 */
	public function toOptionArray()
	{
		return $this->_toOptionArray('rule_id', 'name');
	}

	/**
	 * Add filter by store
	 *
	 * @param int|array|\Magento\Store\Model\Store $store
	 * @param bool                                 $withAdmin
	 * @return $this
	 */
	public function addStoreFilter($store, $withAdmin = true)
	{
		$this->performAddStoreFilter($store, $withAdmin);

		return $this;
	}

	/**
	 * Join store relation table if there is store filter
	 *
	 * @return void
	 */
	protected function _renderFiltersBefore()
	{
		$entityMetadata = $this->metadataPool->getMetadata(RuleInterface::class);

		$this->joinStoreRelationTable('althea_paymentfilter_rule_store', $entityMetadata->getLinkField());
	}

}