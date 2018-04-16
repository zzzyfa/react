<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Rma
 */


namespace Amasty\Rma\Model\ResourceModel\Request;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    protected $_idFieldName = 'id';

    protected $_map = ['fields' => [
        'store_id' => 'main_table.store_id'
    ]];

    protected $filterMappings = [
        'customer_name' => 'CONCAT(customer_firstname, " ", customer_lastname)'
    ];

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            'Amasty\Rma\Model\Request', 'Amasty\Rma\Model\ResourceModel\Request'
        );
    }

    public function addStatusName($storeId = 0)
    {
        $this->getSelect()
            ->joinLeft(
                ['label' => $this->getTable('amasty_amrma_status_label')],
                'main_table.status_id = label.status_id and label.store_id = '
                . (int)$storeId,
                ['status_name' => 'label.label']
            )
        ;

        return $this;
    }

    public function addCustomerName()
    {
        $this->getSelect()
            ->columns($this->filterMappings)
        ;

        return $this;
    }

    protected function _translateCondition($field, $condition)
    {
        if (isset($this->filterMappings[$field])) {
            return $this->_getConditionSql(
                $this->filterMappings[$field], $condition
            );
        }
        else
        {
            return parent::_translateCondition($field, $condition);
        }
    }
}
