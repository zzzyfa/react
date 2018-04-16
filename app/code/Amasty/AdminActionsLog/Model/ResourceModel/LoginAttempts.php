<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2017 Amasty (https://www.amasty.com)
 * @package Amasty_AdminActionsLog
 */


namespace Amasty\AdminActionsLog\Model\ResourceModel;

class LoginAttempts extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    protected function _construct()
    {
        $this->_init('amasty_audit_login_attempts', 'id');
    }
}
