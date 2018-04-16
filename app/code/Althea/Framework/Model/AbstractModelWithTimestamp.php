<?php
/**
 * Created by PhpStorm.
 * User: alvin
 * Date: 02/08/2017
 * Time: 12:50 PM
 */

namespace Althea\Framework\Model;

use Magento\Framework\Model\AbstractModel;

abstract class AbstractModelWithTimestamp extends AbstractModel {

	public function beforeSave()
	{
		parent::beforeSave();

		$dateTime    = new \DateTime();
		$currentTime = $dateTime->format('Y-m-d H:i:s');

		if ((!$this->getId() || $this->isObjectNew()) && !$this->getCreatedAt()) {

			$this->setCreatedAt($currentTime);
		}

		$this->setUpdatedAt($currentTime);

		return $this;
	}

}