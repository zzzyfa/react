<?php
/**
 * Created by PhpStorm.
 * User: alvin
 * Date: 13/09/2017
 * Time: 6:04 PM
 */

namespace Althea\PaymentFilter\Block\Adminhtml\Widget\Chooser\Customer;

use Magento\Backend\Block\Widget\Grid\Extended;

class Group extends Extended {

	/**
	 * @var \Magento\Customer\Model\ResourceModel\Group\CollectionFactory
	 */
	protected $_customerGroupCollection;

	/**
	 * @var \Magento\Customer\Model\ResourceModel\Group\Collection
	 */
	protected $_customerGroupCollectionInstance;

	/**
	 * @inheritDoc
	 */
	public function __construct(
		\Magento\Backend\Block\Template\Context $context,
		\Magento\Backend\Helper\Data $backendHelper,
		\Magento\Customer\Model\ResourceModel\Group\CollectionFactory $collectionFactory,
		\Magento\Customer\Model\ResourceModel\Group\Collection $collection,
		array $data = []
	)
	{
		$this->_customerGroupCollection         = $collectionFactory;
		$this->_customerGroupCollectionInstance = $collection;

		parent::__construct($context, $backendHelper, $data);
	}

	/**
	 * @inheritDoc
	 */
	protected function _construct()
	{
		parent::_construct();

		if ($this->getRequest()->getParam('current_grid_id')) {

			$this->setId($this->getRequest()->getParam('current_grid_id'));
		} else {

			$this->setId('customerGroupChooserGrid_' . $this->getId());
		}

		$form = $this->getJsFormObject();

		$this->setRowClickCallback("{$form}.chooserGridRowClick.bind({$form})");
		$this->setCheckboxCheckCallback("{$form}.chooserGridCheckboxCheck.bind({$form})");
		$this->setRowInitCallback("{$form}.chooserGridRowInit.bind({$form})");
		$this->setDefaultSort('customer_group_id');
		$this->setUseAjax(true);

		if ($this->getRequest()->getParam('collapse')) {

			$this->setIsCollapsed(true);
		}
	}

	protected function _prepareCollection()
	{
		$collection = $this->_getCustomerGroupCollectionInstance();

		$this->setCollection($collection);

		return parent::_prepareCollection();
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

	protected function _prepareColumns()
	{
		$this->addColumn('in_selected', [
			'header_css_class' => 'a-center',
			'type'             => 'checkbox',
			'name'             => 'in_selected',
			'values'           => $this->_getSelectedItems(),
			'align'            => 'center',
			'index'            => 'customer_group_id',
			'use_index'        => true,
		]);

		$this->addColumn('customer_group_code', [
			'header' => 'Group',
			'index'  => 'customer_group_code',
		]);
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
		$selecteds = $this->getRequest()->getPost('selected', array());

		return $selecteds;
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
				     ->addFieldToFilter('customer_group_id', ['in' => $selected]);
			} else {

				$this->getCollection()
				     ->addFieldToFilter('customer_group_id', ['nin' => $selected]);
			}
		} else {

			parent::_addColumnFilterToCollection($column);
		}

		return $this;
	}

}