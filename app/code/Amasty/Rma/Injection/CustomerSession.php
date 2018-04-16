<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Rma
 */


namespace Amasty\Rma\Injection;

class CustomerSession extends \Magento\Customer\Model\Session
{
    public function isLoggedIn()
    {
        return false;
    }
}
