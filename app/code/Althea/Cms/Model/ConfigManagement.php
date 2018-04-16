<?php
/**
 * Created by PhpStorm.
 * User: alvin
 * Date: 04/10/2017
 * Time: 5:12 PM
 */

namespace Althea\Cms\Model;

use Althea\ApiCache\Model\AbstractConfigManagement;

class ConfigManagement extends AbstractConfigManagement {

	/**
	 * @inheritDoc
	 */
	public function isCacheEnabled()
	{
		return $this->state->isEnabled(CacheType::TYPE_IDENTIFIER);
	}

}