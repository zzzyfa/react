<?php
/**
 * Created by PhpStorm.
 * User: alvin
 * Date: 26/01/2018
 * Time: 4:39 PM
 */

namespace Althea\Aftership\Model;

use Althea\Aftership\Helper\Config;

class Track extends \Mrmonsters\Aftership\Model\Track {

	protected $_configHelper;

	/**
	 * @inheritDoc
	 */
	public function __construct(
		Config $config,
		\Magento\Framework\Model\Context $context,
		\Magento\Framework\Registry $registry,
		\Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
		\Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
		array $data = []
	)
	{
		$this->_configHelper = $config;

		parent::__construct($context, $registry, $resource, $resourceCollection, $data);
	}

	/**
	 * @inheritDoc
	 */
	public function getShipCompCode()
	{
		if ($this->_configHelper->getCustomTrackersEnabled()) {

			return $this->_configHelper->getExtensionCourierSlug($this->getData(self::SHIP_COMP_CODE));
		}

		return parent::getShipCompCode(); // TODO: Change the autogenerated stub
	}

}