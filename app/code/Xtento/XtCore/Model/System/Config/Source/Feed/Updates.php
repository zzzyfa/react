<?php

/**
 * Product:       Xtento_XtCore (2.0.7)
 * ID:            pla2jYgPEb1bu8ncBMlGtw6aKM5PQ018LXPJPkLn5XM=
 * Packaged:      2017-06-01T10:43:07+00:00
 * Last Modified: 2015-07-05T13:56:08+00:00
 * File:          app/code/Xtento/XtCore/Model/System/Config/Source/Feed/Updates.php
 * Copyright:     Copyright (c) 2017 XTENTO GmbH & Co. KG <info@xtento.com> / All rights reserved.
 */

namespace Xtento\XtCore\Model\System\Config\Source\Feed;

class Updates implements \Magento\Framework\Option\ArrayInterface
{
    const TYPE_NEW_RELEASE = 'NEW_RELEASE';
    const TYPE_UPDATED = 'UPDATE';
    const TYPE_PROMOTIONS = 'PROMOTIONS';
    const TYPE_OTHERINFO = 'OTHER_INFO';

    /**
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['value' => self::TYPE_UPDATED, 'label' => __('Updates for installed extensions')],
            ['value' => self::TYPE_NEW_RELEASE, 'label' => __('New Extensions')],
            ['value' => self::TYPE_PROMOTIONS, 'label' => __('Discounts/Promotions')],
            ['value' => self::TYPE_OTHERINFO, 'label' => __('Other information')]
        ];
    }
}
