<?php
/**
 * Created by PhpStorm.
 * User: alvin
 * Date: 27/12/2017
 * Time: 3:49 PM
 */

namespace Althea\Freeshippinglabel\Model;

use Althea\Freeshippinglabel\Api\Data\LabelInterface;

class Label extends \Aheadworks\Freeshippinglabel\Model\Label implements LabelInterface {

	/**
	 * @inheritDoc
	 */
	public function getMinItemQty()
	{
		return $this->getData(self::MIN_ITEM_QTY);
	}

	/**
	 * @inheritDoc
	 */
	public function setMinItemQty($qty)
	{
		return $this->setData(self::MIN_ITEM_QTY, $qty);
	}

	/**
	 * @inheritDoc
	 */
	public function getIdentifier()
	{
		return $this->getData(self::IDENTIFIER);
	}

	/**
	 * @inheritDoc
	 */
	public function setIdentifier($identifier)
	{
		return $this->setData(self::IDENTIFIER, $identifier);
	}

}