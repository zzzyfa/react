<?php
/**
 * Created by PhpStorm.
 * User: alvin
 * Date: 09/03/2018
 * Time: 6:21 PM
 */

namespace Althea\Rewardpoints\Plugin\Model;

class OrderRepositoryPlugin {

	protected $_orderExtensionFactory;

	/**
	 * OrderRepositoryPlugin constructor.
	 *
	 * @param \Magento\Sales\Api\Data\OrderExtensionFactory $orderExtensionFactory
	 */
	public function __construct(\Magento\Sales\Api\Data\OrderExtensionFactory $orderExtensionFactory)
	{
		$this->_orderExtensionFactory = $orderExtensionFactory;
	}

	public function aroundGetList(\Magento\Sales\Model\OrderRepository $subject, \Closure $proceed, \Magento\Framework\Api\SearchCriteria $searchCriteria)
	{
		/* @var \Magento\Sales\Api\Data\OrderSearchResultInterface $result */
		$result = $proceed($searchCriteria);

		foreach ($result->getItems() as $item) {

			$extensionAttributes = $item->getExtensionAttributes();

			if ($extensionAttributes === null) {

				$extensionAttributes = $this->_orderExtensionFactory->create();
			}

			$rewardPointsDiscount = ($discount = $item->getData('rewardpoints_discount')) ? $discount : 0;

			$extensionAttributes->setRewardpointsDiscount($rewardPointsDiscount);
			$item->setExtensionAttributes($extensionAttributes);
		}

		return $result;
	}

}