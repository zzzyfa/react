<?php
/**
 * Created by PhpStorm.
 * User: alvin
 * Date: 06/12/2017
 * Time: 1:57 PM
 */

namespace Althea\Aftership\Block\Adminhtml\Order\View;

use Althea\Aftership\Block\TrackTrait;
use Althea\Aftership\Helper\Config;
use Althea\Aftership\Helper\Data;
use Magento\Backend\Block\Template;
use Magento\Framework\Registry;
use Mrmonsters\Aftership\Model\ResourceModel\Track\CollectionFactory;

class Track extends Template {

	use TrackTrait;

	/**
	 * @inheritDoc
	 */
	public function __construct(
		Registry $coreRegistry,
		CollectionFactory $trackCollectionFactory,
		Data $aftershipHelper,
		Config $configHelper,
		Template\Context $context,
		array $data = []
	)
	{
		$this->_coreRegistry           = $coreRegistry;
		$this->_trackCollectionFactory = $trackCollectionFactory;
		$this->_aftershipHelper        = $aftershipHelper;
		$this->_configHelper           = $configHelper;

		parent::__construct($context, $data);
	}

}