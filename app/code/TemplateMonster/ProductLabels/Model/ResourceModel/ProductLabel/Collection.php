<?php

/**
 *
 * Copyright © 2015 TemplateMonster. All rights reserved.
 * See COPYING.txt for license details.
 *
 */

namespace TemplateMonster\ProductLabels\Model\ResourceModel\ProductLabel;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{

    protected $_idFieldName = 'smart_label_id';

    protected function _construct()
    {
        $this->_init(
            'TemplateMonster\ProductLabels\Model\ProductLabel',
            'TemplateMonster\ProductLabels\Model\ResourceModel\ProductLabel');
    }

    /**
     * Find product attribute in conditions or actions
     *
     * @param string $attributeCode
     * @return $this
     */
    public function addAttributeInConditionFilter($attributeCode)
    {
        $match = sprintf('%%%s%%', substr(serialize(['attribute' => $attributeCode]), 5, -1));
        $field = $this->_getMappedField('conditions_serialized');
        $cCond = $this->_getConditionSql($field, ['like' => $match]);
        $aCond = $this->_getConditionSql($field, ['like' => $match]);

        $this->getSelect()->where(sprintf('(%s OR %s)', $cCond, $aCond), null, \Magento\Framework\DB\Select::TYPE_CONDITION);

        return $this;
    }
}
