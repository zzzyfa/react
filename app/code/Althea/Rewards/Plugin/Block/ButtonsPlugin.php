<?php
/**
 * Created by PhpStorm.
 * User: alvin
 * Date: 05/02/2018
 * Time: 4:05 PM
 */

namespace Althea\Rewards\Plugin\Block;

class ButtonsPlugin {

	public function aroundIsActive(\Mirasvit\Rewards\Block\Buttons $subject, \Closure $proceed)
	{
		if (!$subject->rewardsConfig->getDisplayOptionsIsShowReferralShare()) {

			return false;
		}

		return $proceed();
	}

}