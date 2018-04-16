<?php
/**
 * Created by PhpStorm.
 * User: alvin
 * Date: 02/10/2017
 * Time: 5:48 PM
 */

namespace Althea\TopSearch\Block\System\Config\Form\Field;

class Terms extends \Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray {

	/**
	 * Grid columns
	 *
	 * @var array
	 */
	protected $_columns = [];

	/**
	 * Enable the "Add after" button or not
	 *
	 * @var bool
	 */
	protected $_addAfter = true;

	/**
	 * Label of add button
	 *
	 * @var string
	 */
	protected $_addButtonLabel;

	/**
	 * Check if columns are defined, set template
	 *
	 * @return void
	 */
	protected function _construct()
	{
		parent::_construct();
		$this->_addButtonLabel = __('Add');
	}

	/**
	 * Prepare to render
	 *
	 * @return void
	 */
	protected function _prepareToRender()
	{
		$this->addColumn('term', array('label' => __('Term')));
		$this->addColumn('position', array('label' => __('Position')));

		$this->_addAfter       = false;
		$this->_addButtonLabel = __('Add');
	}

	/**
	 * Render array cell for prototypeJS template
	 *
	 * @param string $columnName
	 * @return string
	 * @throws \Exception
	 */
	public function renderCellTemplate($columnName)
	{
		if ($columnName == "active") {
			$this->_columns[$columnName]['class'] = 'input-text required-entry validate-number';
			$this->_columns[$columnName]['style'] = 'width:50px';
		}

		return parent::renderCellTemplate($columnName);
	}

}