<?php

/**
 * Product:       Xtento_XtCore (2.0.7)
 * ID:            pla2jYgPEb1bu8ncBMlGtw6aKM5PQ018LXPJPkLn5XM=
 * Packaged:      2017-06-01T10:43:07+00:00
 * Last Modified: 2016-04-06T11:32:54+00:00
 * File:          app/code/Xtento/XtCore/Model/ResourceModel/Config.php
 * Copyright:     Copyright (c) 2017 XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

namespace Xtento\XtCore\Model\ResourceModel;

class Config extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    protected static $configTablesCreated = null;

    protected function _construct()
    {
        $this->_init('xtento_xtcore_config_data', 'config_id');
    }

    /**
     * Save config value
     *
     * @param $path
     * @param $value
     *
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function saveConfig($path, $value)
    {
        if (!$this->getConfigTablesCreated()) {
            return $this;
        }

        $writeAdapter = $this->getConnection();
        $select = $writeAdapter->select()
            ->from($this->getMainTable())
            ->where('path=?', $path);
        $row = $writeAdapter->fetchRow($select);

        $newData = [
            'path' => $path,
            'value' => $value
        ];

        if ($row) {
            $whereCondition = $writeAdapter->quoteInto($this->getIdFieldName() . '=?', $row[$this->getIdFieldName()]);
            $writeAdapter->update($this->getMainTable(), $newData, $whereCondition);
        } else {
            $writeAdapter->insert($this->getMainTable(), $newData);
        }
        return $this;
    }

    /**
     * Delete config value
     *
     * @param $path
     *
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteConfig($path)
    {
        if (!$this->getConfigTablesCreated()) {
            return $this;
        }

        $writeAdapter = $this->getConnection();
        $writeAdapter->delete(
            $this->getMainTable(),
            [
                $writeAdapter->quoteInto('path=?', $path)
            ]
        );
        return $this;
    }

    /**
     * Get config value
     *
     * @param $path
     *
     * @return null
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getConfigValue($path)
    {
        if (!$this->getConfigTablesCreated()) {
            return null;
        }

        $readAdapter = $this->getConnection();
        $select = $readAdapter->select()
            ->from($this->getMainTable())
            ->where('path=?', $path);
        $row = $readAdapter->fetchRow($select);

        if ($row) {
            return $row['value'];
        } else {
            return null;
        }
    }

    protected function getConfigTablesCreated()
    {
        // Check if DB table(s) have been created.
        if (self::$configTablesCreated !== null) {
            return self::$configTablesCreated;
        } else {
            try {
                self::$configTablesCreated = ($this->getConnection()->showTableStatus($this->getMainTable()) !== false);
            } catch (\Exception $e) {
                return false;
            }
            return self::$configTablesCreated;
        }
    }
}
