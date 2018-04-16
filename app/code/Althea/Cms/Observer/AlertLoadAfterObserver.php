<?php
/**
 * Created by PhpStorm.
 * User: alvin
 * Date: 29/09/2017
 * Time: 4:29 PM
 */

namespace Althea\Cms\Observer;

use Althea\Cms\Model\Alert;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use MSP\APIEnhancer\Api\TagInterface;

class AlertLoadAfterObserver implements ObserverInterface {

	protected $_tag;

	/**
	 * BannerLoadAfterObserver constructor.
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
		/* @var Alert $alert */
		$alert = $observer->getEvent()->getAlert();

		$this->_tag->addTags(['althea_cms_alert', sprintf('althea_cms_alert_%s', $alert->getId())]);
	}

}