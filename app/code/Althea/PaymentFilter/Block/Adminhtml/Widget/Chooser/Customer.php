<?php
/**
 * Created by PhpStorm.
 * User: alvin
 * Date: 13/09/2017
 * Time: 12:26 PM
 */

namespace Althea\PaymentFilter\Block\Adminhtml\Widget\Chooser;

use Magento\Backend\Block\Widget\Grid\Extended;

class Customer extends Extended {

	/**
	 * @var \Magento\Customer\Model\ResourceModel\Customer\CollectionFactory
	 */
	protected $_collection;

	/**
	 * @var \Magento\Customer\Model\ResourceModel\Customer\Collection
	 */
	protected $_collectionInstance;

	/**
	 * @var \Magento\Customer\Model\ResourceModel\Group\CollectionFactory
	 */
	protected $_customerGroupCollection;

	/**
	 * @var \Magento\Customer\Model\ResourceModel\Group\Collection
	 */
	protected $_customerGroupCollectionInstance;

	/**
	 * @var \Magento\Store\Model\System\Store
	 */
	protected $_store;

	/**
	 * @param \Magento\Backend\Block\Template\Context                          $context
	 * @param \Magento\Backend\Helper\Data                                     $backendHelper
	 * @param \Magento\Customer\Model\ResourceModel\Customer\CollectionFactory $collection
	 * @param \Magento\Customer\Model\ResourceModel\Group\CollectionFactory    $customerGroupCollection
	 * @param \Magento\Store\Model\System\Store                                $store
	 * @param array                                                            $data
	 */
	public function __construct(
		\Magento\Backend\Block\Template\Context $context,
		\Magento\Backend\Helper\Data $backendHelper,
		\Magento\Customer\Model\ResourceModel\Customer\CollectionFactory $collection,
		\Magento\Customer\Model\ResourceModel\Group\CollectionFactory $customerGroupCollection,
		\Magento\Store\Model\System\Store $store,
		array $data = []
	)
	{
		$this->_collection              = $collection;
		$this->_customerGroupCollection = $customerGroupCollection;
		$this->_store                   = $store;

		parent::__construct($context, $backendHelper, $data);
	}

	/**
	 * @inheritDoc
	 */
	protected function _construct()
	{
		parent::_construct(); // TODO: Change the autogenerated stub

		if ($this->getRequest()->getParam('current_grid_id')) {

			$this->setId($this->getRequest()->getParam('current_grid_id'));
		} else {

			$this->setId('customersChooserGrid_' . $this->getId());
		}

		$form = $this->getJsFormObject();

		$this->setRowClickCallback("{$form}.chooserGridRowClick.bind({$form})");
		$this->setCheckboxCheckCallback("{$form}.chooserGridCheckboxCheck.bind({$form})");
		$this->setRowInitCallback("{$form}.chooserGridRowInit.bind({$form})");
		$this->setDefaultSort('entity_id');
		$this->setUseAjax(true);

		if ($this->getRequest()->getParam('collapse')) {

			$this->setIsCollapsed(true);
		}
	}

	/**
	 * @inheritDoc
	 */
	protected function _prepareCollection()
	{
		$collection = $this->_getCollectionInstance();

		$collection->addNameToSelect();
		$collection->addAttributeToSelect('group_id');
		$this->setCollection($collection);

		return parent::_prepareCollection();
	}

	/**
	 * Get customer resource collection instance
	 *
	 * @return \Magento\Customer\Model\ResourceModel\Customer\Collection
	 */
	protected function _getCollectionInstance()
	{
		if (!$this->_collectionInstance) {

			$this->_collectionInstance = $this->_collection->create();
		}

		return $this->_collectionInstance;
	}

	protected function _prepareColumns()
	{
		$this->addColumn('in_selected', [
			'header_css_class' => 'a-center',
			'type'             => 'checkbox',
			'name'             => 'in_selected',
			'values'           => $this->_getSelectedItems(),
			'align'            => 'center',
			'index'            => 'entity_id',
			'use_index'        => true,
		]);

		$this->addColumn('name', [
			'header' => __('Name'),
			'index'  => 'name',
		]);

		$this->addColumn('email', [
			'header' => __('Email'),
			'index'  => 'email',
		]);

		$groups = $this->_getCustomerGroupCollectionInstance()
		               ->addFieldToFilter('customer_group_id', array('gt' => 0))
		               ->load()
		               ->toOptionHash();

		$this->addColumn('group', array(
			'header'  => __('Group'),
			'width'   => '100',
			'index'   => 'group_id',
			'type'    => 'options',
			'options' => $groups,
		));

		if (!$this->_storeManager->isSingleStoreMode()) {

			$this->addColumn('website_id', array(
				'header'  => __('Website'),
				'align'   => 'center',
				'width'   => '80px',
				'type'    => 'options',
				'options' => $this->_store->getWebsiteOptionHash(true),
				'index'   => 'website_id',
			));
		}
	}

	/**
	 * Get customer group resource collection instance
	 *
	 * @return \Magento\Customer\Model\ResourceModel\Group\Collection
	 */
	protected function _getCustomerGroupCollectionInstance()
	{
		if (!$this->_customerGroupCollectionInstance) {

			$this->_customerGroupCollectionInstance = $this->_customerGroupCollection->create();
		}

		return $this->_customerGroupCollectionInstance;
	}

	/**
	 * @inheritDoc
	 */
	public function getGridUrl()
	{
		return $this->getUrl('althea_paymentfilter/widget/chooser', [
			'_current'        => true,
			'current_grid_id' => $this->getId(),
			'collapse'        => null,
		]);
	}

	protected function _getSelectedItems()
	{
		$products = $this->getRequest()->getPost('selected', []);

		return $products;
	}

	/**
	 * @inheritDoc
	 */
	protected function _addColumnFilterToCollection($column)
	{
		// Set custom filter for in product flag
		if ($column->getId() == 'in_selected') {

			$selected = $this->_getSelectedItems();

			if (empty($selected)) {

				$selected = '';
			}

			if ($column->getFilter()->getValue()) {

				$this->getCollection()
				     ->addFieldToFilter('entity_id', ['in' => $selected]);
			} else {

				$this->getCollection()
				     ->addFieldToFilter('entity_id', ['nin' => $selected]);
			}
		} else {

			parent::_addColumnFilterToCollection($column);
		}

		return $this;
	}

}