<?php
/**
 * Created by PhpStorm.
 * User: alvin
 * Date: 02/10/2017
 * Time: 6:52 PM
 */

namespace Althea\TopSearch\Model;

use Althea\TopSearch\Api\Data\TermInterface;
use Althea\TopSearch\Helper\Config;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\Context;

class Term extends AbstractModel implements TermInterface {

	/**
	 * Prefix of model events names
	 *
	 * @var string
	 */
	protected $_eventPrefix = 'althea_topsearch_term';

	/**
	 * Parameter name in event
	 *
	 * In observe method you can use $observer->getEvent()->getObject() in this case
	 *
	 * @var string
	 */
	protected $_eventObject = 'term';

	protected $_configHelper;

	/**
	 * @inheritDoc
	 */
	public function __construct(
		Config $configHelper,
		Context $context,
		\Magento\Framework\Registry $registry,
		\Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
		\Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
		array $data = []
	)
	{
		$this->_configHelper = $configHelper;

		parent::__construct($context, $registry, $resource, $resourceCollection, $data);
	}


	/**
	 * @inheritDoc
	 */
	public function getTerms()
	{
		return $this->getData(self::TERMS);
	}

	/**
	 * @inheritDoc
	 */
	public function getStoreId()
	{
		return $this->getData(self::STORE_ID);
	}

	/**
	 * /**
	 * @inheritDoc
	 */
	public function setTerms($terms)
	{
		return $this->setData(self::TERMS, $terms);
	}

	/**
	 * @inheritDoc
	 */
	public function setStoreId($storeId)
	{
		return $this->setData(self::STORE_ID, $storeId);
	}

	/**
	 * Load object data by store ID
	 *
	 * @param integer $storeId
	 * @return $this
	 */
	public function loadByStoreId($storeId)
	{
		$result = [];

		$this->setStoreId($storeId);

		if ($this->_configHelper->getGeneralEnabled($storeId)
			&& $terms = $this->_configHelper->getGeneralSearchTerms($storeId)
		) {

			$termsArray = unserialize($terms);

			foreach ($termsArray as $item) {

				$result[intval($item['position'])] = $item['term'];
			}

			ksort($result, SORT_ASC);
		}

		$this->setTerms($result);
		$this->_afterLoad();

		return $this;
	}

}