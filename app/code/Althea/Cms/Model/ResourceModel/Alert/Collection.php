<?php
/**
 * Created by PhpStorm.
 * User: alvin
 * Date: 07/08/2017
 * Time: 3:07 PM
 */

namespace Althea\Cms\Model\ResourceModel\Alert;

use Althea\Cms\Api\Data\AlertInterface;
use Althea\Cms\Model\ResourceModel\AbstractCollection;

class Collection extends AbstractCollection {

	/**
	 * @var string
	 */
	protected $_idFieldName = 'alert_id';

	/**
	 * Name prefix of events that are dispatched by model
	 *
	 * @var string
	 */
	protected $_eventPrefix = 'althea_cms_alert_collection';

	/**
	 * Name of event parameter
	 *
	 * @var string
	 */
	protected $_eventObject = 'alert_collection';

	/**
	 * Perform operations after collection load
	 *
	 * @return $this
	 */
	protected function _afterLoad()
	{
		$entityMetadata = $this->metadataPool->getMetadata(AlertInterface::class);

		$this->performAfterLoad('althea_cms_alert_store', $entityMetadata->getLinkField());

		return parent::_afterLoad();
	}

	/**
	 * Define resource model
	 *
	 * @return void
	 */
	protected function _construct()
	{
		$this->_init('Althea\Cms\Model\Alert', 'Althea\Cms\Model\ResourceModel\Alert');
		$this->_map['fields']['store'] = 'store_table.store_id';
	}

	/**
	 * Returns pairs alert_id - title
	 *
	 * @return array
	 */
	public function toOptionArray()
	{
		return $this->_toOptionArray('alert_id', 'title');
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
		$entityMetadata = $this->metadataPool->getMetadata(AlertInterface::class);

		$this->joinStoreRelationTable('althea_cms_alert_store', $entityMetadata->getLinkField());
	}

}