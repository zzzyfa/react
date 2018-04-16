<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Rma
 */


namespace Amasty\Rma\Block\Adminhtml\Order\Edit\Tab;

use Magento\Customer\Api\AccountManagementInterface;
use Magento\Backend\Block\Widget\Tab\TabInterface;

class Rma extends \Magento\Framework\View\Element\Text\ListText implements TabInterface
{
    /**
     * @var \Magento\Framework\AuthorizationInterface
     */
    protected $authorization;

    /**
     * Rma constructor.
     *
     * @param \Magento\Framework\View\Element\Context   $context
     * @param \Magento\Framework\AuthorizationInterface $authorization
     * @param array                                     $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Context $context,

        \Magento\Framework\AuthorizationInterface $authorization,

        array $data = []
    ) {

        parent::__construct($context, $data);

        $this->authorization = $authorization;
    }

    /**
     * Return Tab label
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabLabel()
    {
        return __('RMA');
    }

    /**
     * Return Tab title
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabTitle()
    {
        return __('RMA');
    }

    /**
     * Can show tab in tabs
     *
     * @return boolean
     */
    public function canShowTab()
    {
        return $this->authorization->isAllowed('Amasty_Rma::requests');
    }

    /**
     * Tab is hidden
     *
     * @return boolean
     */
    public function isHidden()
    {
        return false;
    }
}
