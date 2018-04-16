<?php
/**
 * Created by PhpStorm.
 * User: alvin
 * Date: 29/09/2017
 * Time: 4:43 PM
 */

namespace Althea\Cms\Observer;

use Althea\Cms\Model\ResourceModel\Alert\Collection;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use MSP\APIEnhancer\Api\TagInterface;

class AlertCollectionLoadAfterObserver implements ObserverInterface {

	protected $_tag;

	/**
	 * AlertCollectionLoadAfterObserver constructor.
	 *
	 * @param $_tag
	 */
	public function __construct(TagInterface $_tag)
	{
		$this->_tag = $_tag;
	}

	/**
	 * @inheritDoc
	 */
	public function execute(\Magento\Framework\Event\Observer $observer)
	{
		/* @var Collection $collection */
		$collection = $observer->getEvent()->getAlertCollection();
		$tags       = ['althea_cms_alert'];
		$ids        = array_keys($collection->getItems());

		foreach ($ids as $id) {

			$tags[] = sprintf('althea_cms_alert_%s', $id);
		}

		$this->_tag->addTags($tags);
	}

}