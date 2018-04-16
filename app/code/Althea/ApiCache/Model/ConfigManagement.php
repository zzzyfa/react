<?php
/**
 * Created by PhpStorm.
 * User: alvin
 * Date: 04/10/2017
 * Time: 6:34 PM
 */

namespace Althea\ApiCache\Model;

class ConfigManagement extends AbstractConfigManagement {

	/**
	 * @inheritDoc
	 */
	public function isCacheEnabled()
	{
		return $this->state->isEnabled(CacheType::TYPE_IDENTIFIER);
	}

}