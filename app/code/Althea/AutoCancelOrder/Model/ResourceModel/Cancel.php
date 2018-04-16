<?php

namespace Althea\AutoCancelOrder\Model\ResourceModel;

class Cancel extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    protected function _construct()
    {
        $this->_init('althea_autocancelorder', 'id');
    }
}