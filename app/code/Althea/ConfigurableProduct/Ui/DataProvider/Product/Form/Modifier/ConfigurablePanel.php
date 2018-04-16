<?php
/**
 * Created by PhpStorm.
 * User: alvin
 * Date: 06/02/2018
 * Time: 6:43 PM
 */

namespace Althea\ConfigurableProduct\Ui\DataProvider\Product\Form\Modifier;

class ConfigurablePanel extends \Magento\ConfigurableProduct\Ui\DataProvider\Product\Form\Modifier\ConfigurablePanel {

	/**
	 * @inheritDoc
	 */
	protected function getGrid()
	{
		$result = parent::getGrid();

		// althea:
		// - content of $result['children'] comes from getRows()
		// - getRows() already called in getRows() @ Magestore/InventorySuccess/Ui/DataProvider/Product/Form/Modifier/ConfigurablePanel.php
		// - to fix illegal offset type exception
		unset($result['children']);

		return $result;
	}

}