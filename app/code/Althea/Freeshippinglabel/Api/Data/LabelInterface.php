<?php
/**
 * Created by PhpStorm.
 * User: alvin
 * Date: 02/01/2018
 * Time: 12:43 PM
 */

namespace Althea\Freeshippinglabel\Api\Data;

interface LabelInterface extends \Aheadworks\Freeshippinglabel\Api\Data\LabelInterface {

	const MIN_ITEM_QTY = 'min_item_qty';
	const IDENTIFIER   = 'identifier';

	/**
	 * @return int
	 */
	public function getMinItemQty();

	/**
	 * @param int $qty
	 *
	 * @return $this
	 */
	public function setMinItemQty($qty);

	/**
	 * @return string
	 */
	public function getIdentifier();

	/**
	 * @param string $identifier
	 *
	 * @return $this
	 */
	public function setIdentifier($identifier);

}