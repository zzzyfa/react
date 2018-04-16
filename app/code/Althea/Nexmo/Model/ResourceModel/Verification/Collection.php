<?php
/**
 * Created by PhpStorm.
 * User: alvin
 * Date: 24/07/2017
 * Time: 4:43 PM
 */

namespace Althea\Nexmo\Model\ResourceModel\Verification;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\VersionControl\Collection {

	protected function _construct()
	{
		$this->_init('Althea\Nexmo\Model\Verification', 'Althea\Nexmo\Model\ResourceModel\Verification');
	}

}