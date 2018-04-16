<?php
/**
 * Created by PhpStorm.
 * User: alvin
 * Date: 19/02/2018
 * Time: 6:06 PM
 */

namespace Althea\Quote\Model;

use Magento\Framework\Exception\CouldNotSaveException;

class QuoteManagement extends \Magento\Quote\Model\QuoteManagement {

	/**
	 * @inheritDoc
	 */
	public function createEmptyCartForCustomer($customerId)
	{
		$storeId = $this->storeManager->getStore()->getStoreId();
		$quote = $this->createCustomerCart($customerId, $storeId);

		try {

			// althea:
			// - https://github.com/magento/magento2/issues/2991
			$quote->getShippingAddress()->setCollectShippingRates(true);

			$this->quoteRepository->save($quote);
		} catch (\Exception $e) {
			throw new CouldNotSaveException(__('Cannot create quote'));
		}
		return $quote->getId();
	}

}