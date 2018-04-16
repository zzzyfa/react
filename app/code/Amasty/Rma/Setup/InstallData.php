<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Rma
 */


namespace Amasty\Rma\Setup;

use Amasty\Base\Helper\Deploy as DeployHelper;
use Amasty\Rma\Model\Status;
use Amasty\Rma\Model\Status\Template as StatusTemplate;
use Magento\Framework\App\TemplateTypesInterface;
use Magento\Framework\Json\EncoderInterface;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

class InstallData implements InstallDataInterface
{
    /**
     * @var ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * @var EncoderInterface
     */
    protected $jsonEncoder;

    /**
     * @var DeployHelper
     */
    protected $deployHelper;

    /**
     * Init
     *
     * @param EncoderInterface       $jsonEncoder
     * @param ObjectManagerInterface $objectManager
     * @param DeployHelper           $deployHelper
     */
    public function __construct(
        EncoderInterface $jsonEncoder,
        ObjectManagerInterface $objectManager,
        DeployHelper $deployHelper
    ) {
        $this->objectManager = $objectManager;
        $this->jsonEncoder = $jsonEncoder;
        $this->deployHelper = $deployHelper;
    }

    /**
     * {@inheritdoc}
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $this->addStatuses();
        $this->addTemplate();
        $this->deployHelper->deployFolder(dirname(__DIR__) . '/pub');
    }

    protected function addTemplate()
    {
        $templateCode = StatusTemplate::TEMPLATE_CODE;

        /** @var \Magento\Email\Model\Template $template */
        $template = $this->objectManager->create('\Magento\Email\Model\Template');

        $template
            ->setForcedArea($templateCode)
            ->loadDefault($templateCode)
            ->setData('orig_template_code', $templateCode)
            ->setData(
                'template_variables',
                $this->jsonEncoder->encode($template->getVariablesOptionArray(true))
            )
            ->setData('template_code', 'Amasty: RMA')
            ->setTemplateType(TemplateTypesInterface::TYPE_HTML)
            ->setId(NULL)
            ->save()
        ;

        /** @var Status $status */
        $status = $this->objectManager
            ->create('\Amasty\Rma\Model\Status')
            ->load('pending', 'status_key')
        ;

        $this->objectManager
            ->create('\Amasty\Rma\Model\Status\Template')
            ->setData([
                'status_id' => $status->getId(),
                'store_id' => 0,
                'template' => $template->getId()
            ])
            ->save();
        ;
    }

    protected function addStatuses()
    {
        $statuses = [
            [
                'code'  => 'pending',
                'label' => 'NEW'
            ],
            [
                'allow_print_label' => true,
                'label'             => 'Processing'
            ],
            [
                'label' => 'Product Shipped'
            ],
            [
                'label' => 'Product Received'
            ],
            [
                'label' => 'Completed'
            ],
        ];

        foreach ($statuses as $priority => $data) {
            /** @var Status $status */
            $status = $this->objectManager
                ->create('\Amasty\Rma\Model\Status')
                ->setData([
                    'is_active' => true,
                    'priority' => $priority,
                    'status_key' => isset($data['code']) ? $data['code'] : false,
                    'allow_print_label' => isset($data['allow_print_label']) ? $data['allow_print_label'] : false
                ])
                ->save();
            ;

            $this->objectManager
                ->create('\Amasty\Rma\Model\Status\Label')
                ->setData([
                    'status_id' => $status->getId(),
                    'store_id' => 0,
                    'label' => $data['label']
                ])
                ->save();
            ;
        }
    }
}
