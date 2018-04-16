<?php
/**
 * BSS Commerce Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://bsscommerce.com/Bss-Commerce-License.txt
 *
 * =================================================================
 *                 MAGENTO EDITION USAGE NOTICE
 * =================================================================
 * This package designed for Magento COMMUNITY edition
 * BSS Commerce does not guarantee correct work of this extension
 * on any other Magento edition except Magento COMMUNITY edition.
 * BSS Commerce does not provide extension support in case of
 * incorrect edition usage.
 * =================================================================
 *
 * @category   BSS
 * @package    Bss_ShoppingCartRulePerStoreView
 * @author     Extension Team
 * @copyright  Copyright (c) 2016-2017 BSS Commerce Co. ( http://bsscommerce.com )
 * @license    http://bsscommerce.com/Bss-Commerce-License.txt
 */
namespace Bss\ShoppingCartRulePerStoreView\Plugin\ResourceModel;

use Bss\ShoppingCartRulePerStoreView\Model\ResourceModel\Rule;

class ReadHandler
{
    /**
     * @var Rule
     */
    public $ruleResource;

    /**
     * @param Rule $ruleResource
     */
    public function __construct(
        Rule $ruleResource
    ) {
        $this->ruleResource = $ruleResource;
    }

    /**
     * @param string $subject
     * @param array $result
     */
    public function afterExecute($subject, $result)
    {
        $linkField = 'rule_id';
        $entityId = $result[$linkField];
        $result['store_ids'] = $this->ruleResource->getStoreIds($entityId);
        
        return $result;
    }
}
