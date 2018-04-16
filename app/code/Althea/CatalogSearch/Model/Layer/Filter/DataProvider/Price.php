<?php
/**
 * Created by PhpStorm.
 * User: alvin
 * Date: 23/03/2018
 * Time: 3:46 PM
 */

namespace Althea\CatalogSearch\Model\Layer\Filter\DataProvider;

use Magento\Catalog\Model\Layer;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Registry;

class Price extends \Magento\Catalog\Model\Layer\Filter\DataProvider\Price {

	/**
	 * @inheritDoc
	 */
	public function getResetValue()
	{
		$priorIntervals = $this->getPriorIntervals();

		if ($priorIntervals) {

			return implode(',', $priorIntervals);
		}

		return null;
	}

	/**
	 * @inheritDoc
	 */
	public function getAdditionalRequestData()
	{
		$result = '';
		$appliedInterval = $this->getInterval();
		if ($appliedInterval) {
			foreach ($appliedInterval as $item) {
				$result .= ',' . $item[0] . '-' . $item[1];
			}
			$priorIntervals = $this->getResetValue();
			if ($priorIntervals) {
				$result .= ',' . $priorIntervals;
			}
		}

		return $result;
	}

}