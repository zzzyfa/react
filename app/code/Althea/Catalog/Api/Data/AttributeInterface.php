<?php
/**
 * Created by PhpStorm.
 * User: alvin
 * Date: 14/03/2018
 * Time: 4:18 PM
 */

namespace Althea\Catalog\Api\Data;

interface AttributeInterface extends \Magento\Framework\Api\AttributeInterface {

	const FRONTEND_LABEL = 'frontend_label';

	/**
	 * Get attribute frontend label
	 *
	 * @return string
	 */
	public function getFrontendLabel();

	/**
	 * Set attribute frontend label
	 *
	 * @param string $label
	 * @return $this
	 */
	public function setFrontendLabel($label);

}