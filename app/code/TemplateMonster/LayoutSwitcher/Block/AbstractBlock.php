<?php

namespace TemplateMonster\LayoutSwitcher\Block;

use TemplateMonster\LayoutSwitcher\Helper\Data as LayoutSwitcherHelper;
use Magento\Framework\View\Element\Template;

/**
 * Frontend abstract block.
 *
 * @package TemplateMonster\LayoutSwitcher\Block
 */
class AbstractBlock extends Template
{
    /**
     * @var LayoutSwitcherHelper
     */
    protected $_helper;

    /**
     * @var bool
     */
    protected $_livedemoMode;

    /**
     * AbstractBlock constructor.
     *
     * @param LayoutSwitcherHelper $helper
     * @param Template\Context     $context
     * @param bool                 $livedemoMode
     * @param array                $data
     */
    public function __construct(
        LayoutSwitcherHelper $helper,
        Template\Context $context,
        $livedemoMode = false,
        array $data = []
    )
    {
        $this->_helper = $helper;
        $this->_livedemoMode = (bool) $livedemoMode;
        parent::__construct($context, $data);
    }

    /**
     * @inheritdoc
     */
    protected function _toHtml()
    {
        if (!$this->_helper->isEnabled() || !$this->_livedemoMode) {
            return '';
        }

        return parent::_toHtml();
    }
}