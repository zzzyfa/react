<?php
/**
 * Created by PhpStorm.
 * User: alvin
 * Date: 29/09/2017
 * Time: 4:00 PM
 */

namespace Althea\Cms\Observer;

use Althea\Cms\Model\Banner;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use MSP\APIEnhancer\Api\TagInterface;

class BannerLoadAfterObserver implements ObserverInterface {

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
		/* @var Banner $banner */
		$banner = $observer->getEvent()->getBanner();

		$this->_tag->addTags(['althea_cms_banner', sprintf('althea_cms_banner_%s', $banner->getId())]);
	}

}