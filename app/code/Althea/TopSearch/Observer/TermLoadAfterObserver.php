<?php
/**
 * Created by PhpStorm.
 * User: alvin
 * Date: 03/10/2017
 * Time: 11:54 AM
 */

namespace Althea\TopSearch\Observer;

use Althea\TopSearch\Model\Term;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use MSP\APIEnhancer\Api\TagInterface;

class TermLoadAfterObserver implements ObserverInterface {

	protected $_tag;

	/**
	 * TermConfigGetAfterObserver constructor.
	 *
	 * @param $_tag
	 */
	public function __construct(TagInterface $tag)
	{
		$this->_tag = $tag;
	}

	/**
	 * @inheritDoc
	 */
	public function execute(\Magento\Framework\Event\Observer $observer)
	{
		/* @var Term $term */
		$term = $observer->getEvent()->getTerm();

		$this->_tag->addTags(['althea_topsearch_term', sprintf('althea_topsearch_term_store_%s', $term->getStoreId())]);
	}

}