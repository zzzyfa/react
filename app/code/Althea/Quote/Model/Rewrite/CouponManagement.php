<?php
/**
 * Created by PhpStorm.
 * User: alvin
 * Date: 17/07/2017
 * Time: 5:06 PM
 */

namespace Althea\Quote\Model\Rewrite;

use Magento\Framework\Event\Manager;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;

class CouponManagement extends \Magento\Quote\Model\CouponManagement {

	private $eventManager;

	public function __construct(\Magento\Quote\Api\CartRepositoryInterface $quoteRepository, Manager $eventManager)
	{
		$this->eventManager = $eventManager;

		parent::__construct($quoteRepository);
	}

	public function set($cartId, $couponCode)
	{
		/** @var  \Magento\Quote\Model\Quote $quote */
		$quote = $this->quoteRepository->getActive($cartId);
		if (!$quote->getItemsCount()) {
			throw new NoSuchEntityException(__('Cart %1 doesn\'t contain products', $cartId));
		}
		$quote->getShippingAddress()->setCollectShippingRates(true);

		try {
			$quote->setCouponCode($couponCode);
			$this->quoteRepository->save($quote->collectTotals());

			// althea: dispatch custom coupon message event
			$this->eventManager->dispatch('coupon_apply_after', [
				'coupon_code' => $couponCode,
				'quote'       => $quote,
			]);
		} catch (\Exception $e) {
			throw new CouldNotSaveException(__('Could not apply coupon code'));
		}

		if ($quote->getCouponCode() != $couponCode) {

			if ($msg = $quote->getCouponErrorMessage()) { // althea: handle custom coupon message

				throw new CouldNotSaveException(__($msg));
			} else {

				throw new NoSuchEntityException(__('Coupon code is not valid'));
			}
		}
		return true;
	}

}