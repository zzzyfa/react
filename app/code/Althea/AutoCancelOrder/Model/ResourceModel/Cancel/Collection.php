<?php

namespace Althea\AutoCancelOrder\Model\ResourceModel\Cancel;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    protected function _construct()
    {
        $this->_init(
            'Althea\AutoCancelOrder\Model\Cancel',
            'Althea\AutoCancelOrder\Model\ResourceModel\Cancel'
        );
    }
}