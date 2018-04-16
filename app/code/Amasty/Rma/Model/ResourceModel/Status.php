<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Rma
 */


namespace Amasty\Rma\Model\ResourceModel;

use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Status extends AbstractDb
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('amasty_amrma_status', 'id');
    }

    public function getStoreLabels($statusId)
    {
        $select = $this->getConnection()->select()
            ->from(
                $this->getTable('amasty_amrma_status_label'),
                ['store_id', 'label']
            )
            ->where('status_id = :status_id');

        return $this->getConnection()->fetchPairs($select, [':status_id' => $statusId]);
    }

    public function getStoreTemplates($statusId)
    {
        $select = $this->getConnection()->select()
            ->from($this->getTable('amasty_amrma_status_template'), ['store_id', 'template'])
            ->where('status_id = :status_id');

        return $this->getConnection()->fetchPairs($select, [':status_id' => $statusId]);
    }

    protected function _afterSave(AbstractModel $object)
    {
        $this->saveStoreLabels($object->getId(), $object->getStoreLabels());
        $this->saveStoreTemplates($object->getId(), $object->getStoreTemplates());
    }

    public function saveStoreTemplates($statusId, $templates)
    {
        $deleteByStoreIds = [];
        $table   = $this->getTable('amasty_amrma_status_template');
        $adapter = $this->getConnection();

        $data    = [];
        foreach ($templates as $storeId => $template) {
            if ($template) {
                $data[] = [
                    'status_id' => $statusId,
                    'store_id'  => $storeId,
                    'template'  => $template
                ];
            } else {
                $deleteByStoreIds[] = $storeId;
            }
        }

        $adapter->beginTransaction();

        try {
            if (!empty($data)) {
                $adapter->insertOnDuplicate(
                    $table,
                    $data,
                    ['template']
                );
            }

            if (!empty($deleteByStoreIds)) {
                $adapter->delete($table, [
                    'status_id=?'     => $statusId,
                    'store_id IN (?)' => $deleteByStoreIds
                ]);
            }
        } catch (\Exception $e) {
            $adapter->rollback();
            throw $e;
        }

        $adapter->commit();

        return $this;
    }

    public function saveStoreLabels($statusId, $labels)
    {
        $deleteByStoreIds = [];
        $table   = $this->getTable('amasty_amrma_status_label');
        $adapter = $this->getConnection();

        $data    = [];
        foreach ($labels as $storeId => $label) {
            if ($label) {
                $data[] = ['status_id' => $statusId, 'store_id' => $storeId, 'label' => $label];
            } else {
                $deleteByStoreIds[] = $storeId;
            }
        }

        $adapter->beginTransaction();

        try {
            if (!empty($data)) {
                $adapter->insertOnDuplicate(
                    $table,
                    $data,
                    ['label']
                );
            }

            if (!empty($deleteByStoreIds)) {
                $adapter->delete($table, array(
                    'status_id=?'       => $statusId,
                    'store_id IN (?)' => $deleteByStoreIds
                ));
            }
        } catch (\Exception $e) {
            $adapter->rollback();
            throw $e;
        }

        $adapter->commit();

        return $this;
    }
}
