<?php

namespace TemplateMonster\ThemeOptions\Model\Config\Source;

class SocialPosition implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => 'left',  'label' => __('Left')],
            ['value' => 'right', 'label' => __('Right')],
            ['value' => 'center',  'label' => __('Center')]
        ];
    }
}

