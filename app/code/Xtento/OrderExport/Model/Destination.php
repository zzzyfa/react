<?php

/**
 * Product:       Xtento_OrderExport (2.2.8)
 * ID:            pla2jYgPEb1bu8ncBMlGtw6aKM5PQ018LXPJPkLn5XM=
 * Packaged:      2017-06-01T10:42:36+00:00
 * Last Modified: 2016-04-16T14:10:22+00:00
 * File:          app/code/Xtento/OrderExport/Model/Destination.php
 * Copyright:     Copyright (c) 2017 XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

namespace Xtento\OrderExport\Model;

/**
 * Class Destination
 * Destination model containing information about "destinations" where exported files can be saved on
 * @package Xtento\OrderExport\Model
 */
class Destination extends \Magento\Framework\Model\AbstractModel
{
    /**
     * Destination Types
     */
    const TYPE_LOCAL = 'local';
    const TYPE_FTP = 'ftp';
    const TYPE_SFTP = 'sftp';
    const TYPE_HTTP = 'http';
    const TYPE_EMAIL = 'email';
    const TYPE_WEBSERVICE = 'webservice';
    const TYPE_CUSTOM = 'custom';

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $date;

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * @var DestinationFactory
     */
    protected $destinationFactory;

    /**
     * @var ResourceModel\Profile\CollectionFactory
     */
    protected $profileCollectionFactory;

    /**
     * Destination constructor.
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $date
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param DestinationFactory $destinationFactory
     * @param ResourceModel\Profile\CollectionFactory $profileCollectionFactory
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource|null $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb|null $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        DestinationFactory $destinationFactory,
        \Xtento\OrderExport\Model\ResourceModel\Profile\CollectionFactory $profileCollectionFactory,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
        $this->date = $date;
        $this->objectManager = $objectManager;
        $this->destinationFactory = $destinationFactory;
        $this->profileCollectionFactory = $profileCollectionFactory;
    }

    /**
     * Constructor
     */
    protected function _construct()
    {
        $this->_init('Xtento\OrderExport\Model\ResourceModel\Destination');
        $this->_collectionName = 'Xtento\OrderExport\Model\ResourceModel\Destination\Collection';
    }

    /**
     * Return destination types
     *
     * @return array
     */
    public function getTypes()
    {
        $values = [];
        $values[self::TYPE_LOCAL] = __('Local Directory');
        $values[self::TYPE_FTP] = __('FTP Server');
        $values[self::TYPE_SFTP] = __('SFTP Server');
        $values[self::TYPE_HTTP] = __('HTTP Server');
        $values[self::TYPE_EMAIL] = __('E-Mail Recipient(s)');
        $values[self::TYPE_WEBSERVICE] = __('Webservice/API');
        $values[self::TYPE_CUSTOM] = __('Custom Class');
        return $values;
    }

    /**
     * Set last result message for this destination
     *
     * @param $message
     * @return $this
     */
    public function setLastResultMessage($message)
    {
        $this->setData(
            'last_result_message',
            date('c', $this->date->timestamp()) . ": " . $message
        );
        return $this;
    }

    /**
     * Save files on destinations
     *
     * @param $generatedFiles
     * @return array
     */
    public function saveFiles($generatedFiles)
    {
        $destinationClass = $this->objectManager->create(
            '\Xtento\OrderExport\Model\Destination\\' . ucfirst($this->getData('type'))
        );
        if ($destinationClass !== false) {
            $destinationClass->setDestination($this);
            return $destinationClass->saveFiles($generatedFiles);
        }
        return [];
    }

    /**
     * Retrieve profiles which are using this destination.
     *
     * @return array
     */
    public function getProfileUsage()
    {
        $profileUsage = [];
        $profileCollection = $this->profileCollectionFactory->create();
        $profileCollection->addFieldToFilter('destination_ids', ['neq' => '']);
        $profileCollection->getSelect()->order('entity ASC');
        foreach ($profileCollection as $profile) {
            $destinationIds = explode("&", $profile->getData('destination_ids'));
            if (in_array($this->getId(), $destinationIds)) {
                $profileUsage[] = $profile;
            }
        }
        return $profileUsage;
    }

    /**
     * Overwrite ID when importing destinations.
     *
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function saveWithId()
    {
        // First check if the ID we've set exists as a model right now.
        $realId = $this->getId();
        $idExists = $this->destinationFactory->create()->setId(null)->load($realId)->getId();

        // Perform the regular saving routine as if it's a new model
        if (!$idExists) {
            $this->setId(null);
        }
        $this->save();

        // Update the new model we created with the original ID.
        if (!$idExists) {
            $write = $this->getResource()->getConnection();
            $write->update(
                $this->getResource()->getMainTable(),
                [$this->getResource()->getIdFieldName() => $realId],
                ["`{$this->getResource()->getIdFieldName()}` = {$this->getId()}"]
            );
            #$write->commit();
        }

        return $this;
    }

    /**
     * Fix bad user input for specific configuration values when requested by the module
     *
     * @return mixed|string
     */
    public function getHostname()
    {
        $hostname = $this->getData('hostname');
        $hostname = str_replace(['ftp://', 'http://'], '', $hostname);
        $hostname = trim($hostname);
        return $hostname;
    }

    /**
     * Fix bad user input for specific configuration values when requested by the module
     *
     * @return mixed|string
     */
    public function getPort()
    {
        $port = $this->getData('port');
        $port = preg_replace('/[^0-9]/', '', $port);
        return $port;
    }

    /**
     * Fix bad user input for specific configuration values when requested by the module
     *
     * @return mixed|string
     */
    public function getTimeout()
    {
        $timeout = $this->getData('timeout');
        $timeout = preg_replace('/[^0-9]/', '', $timeout);
        return $timeout;
    }
}