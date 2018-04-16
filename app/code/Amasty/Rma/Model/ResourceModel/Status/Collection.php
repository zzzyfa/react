<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Rma
 */


namespace Amasty\Rma\Model\ResourceModel\Status;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    protected $_idFieldName = 'id';

    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            'Amasty\Rma\Model\Status', 'Amasty\Rma\Model\ResourceModel\Status'
        );
    }

    public function addLabel($storeId = 0)
    {
        $this->getSelect()
            ->joinLeft(
                ['label' => $this->getTable('amasty_amrma_status_label')],
                'main_table.id = label.status_id and label.store_id = '
                . (int)$storeId,
                ['label.label']
            )
        ;

        return $this;
    }

    public function sortByOrder($direction = self::SORT_ORDER_ASC)
    {
        if (!in_array($direction, [self::SORT_ORDER_ASC, self::SORT_ORDER_DESC])) {
            $direction = self::SORT_ORDER_ASC;
        }

        $this->getSelect()->order("ifnull(priority, 9999) $direction");
        $this->getSelect()->order("status_id " . self::SORT_ORDER_ASC);

        return $this;
    }

    public function toOptionArray()
    {
        return $this->_toOptionArray(null, 'label');
    }
}
