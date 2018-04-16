<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Rma
 */

namespace Amasty\Rma\Model;

use Magento\Framework\Filesystem;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\UrlInterface;
use Magento\Framework\App\Filesystem\DirectoryList;

class File extends AbstractModel
{
    const UPLOAD_DIR = 'amasty/rma/uploads';

    /**
     * @var UrlInterface
     */
    protected $urlBuilder;

    /**
     * @var Filesystem
     */
    protected $filesystem;

    /**
     * @param \Magento\Framework\Model\Context                                            $context
     * @param \Magento\Framework\Registry                                                 $registry
     * @param ResourceModel\File|\Magento\Framework\Model\ResourceModel\AbstractResource  $resource
     * @param ResourceModel\File\Collection|\Magento\Framework\Data\Collection\AbstractDb $resourceCollection
     * @param UrlInterface                                                                $urlBuilder
     * @param Filesystem                                                                  $filesystem
     * @param array                                                                       $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Amasty\Rma\Model\ResourceModel\File $resource = null,
        \Amasty\Rma\Model\ResourceModel\File\Collection $resourceCollection = null,

        UrlInterface $urlBuilder,
        Filesystem $filesystem,

        array $data = []
    ) {
        $this->urlBuilder = $urlBuilder;
        $this->filesystem = $filesystem;

        parent::__construct($context, $registry, $resource, $resourceCollection);
    }

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Amasty\Rma\Model\ResourceModel\File');
    }

    public function getUploadDir()
    {
        $path = $this->filesystem->getDirectoryRead(
            DirectoryList::MEDIA
        )->getAbsolutePath(
            self::UPLOAD_DIR
        );

        return $path;
    }

    public function getHref()
    {
        $url = $this->urlBuilder->getUrl('amasty_rma/request/download', [
            'id' => $this->getId()
        ]);

        return $url;
    }

    public function getRelativeFilePath()
    {
        return self::UPLOAD_DIR . '/' . $this->getData('file');
    }
}
