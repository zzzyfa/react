<?php
namespace TemplateMonster\Megamenu\Block\Adminhtml\Category\Tab;

use TemplateMonster\Megamenu\Helper\Data;

class Configurator extends \Magento\Backend\Block\Template
{
    const CONFIGURATOR_TEMPLATE = 'TemplateMonster_Megamenu::category/tab/configurator.phtml';

    const FILED_NAME = 'mm_configurator';

    protected $_staticBlocksSource;

    protected $registry;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Catalog\Model\Category\Attribute\Source\Page $staticBlocksSource,
        \Magento\Framework\Registry $registry,
        array $data = []
    )
    {
        parent::__construct($context, $data);
        $this->registry = $registry;
        $this->_staticBlocksSource = $staticBlocksSource;
    }

    public function getTemplate()
    {
        return self::CONFIGURATOR_TEMPLATE;
    }

    public function toHtml()
    {
        return $this->_toHtml();
    }

    /**
     * Althea
     * This is for preventing crash when it is used with JSON.parse
     * @param $string
     * @return mixed
     */
    public function removeSemicolon($string)
    {
        return str_replace('\'', '', $string);
    }

    public function getStaticBlocksJson()
    {
        $blocks = $this->_staticBlocksSource->getAllOptions();
        $removedSemicolonBlocks = [];

        foreach ($blocks as $block) {
            $rsb = ['value' => $block['value'], 'label' => $this->removeSemicolon($block['label'])];
            array_push($removedSemicolonBlocks, $rsb);
        }

        array_shift($removedSemicolonBlocks);
        return addslashes(\Zend_Json::encode($removedSemicolonBlocks));
    }

    public function getFieldName()
    {
        return self::FILED_NAME;
    }

    public function getCategory()
    {
        return $this->registry->registry('category');
    }

    public function getConfiguredValue()
    {
        return $this->removeSemicolon($this->getCategory()->getMmConfigurator());
    }

    public function getCategoryLevel()
    {
        return $this->getCategory()->getLevel();
    }
}