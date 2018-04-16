<?php
/**
 * Created by PhpStorm.
 * User: alvin
 * Date: 17/07/2017
 * Time: 11:25 AM
 */

namespace Althea\Checkout\Controller\Rewrite\Cart;

class CouponPost extends \Magento\Checkout\Controller\Cart\CouponPost {

	private $eventManager;

	public function __construct(
		\Magento\Framework\App\Action\Context $context,
		\Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
		\Magento\Checkout\Model\Session $checkoutSession,
		\Magento\Store\Model\StoreManagerInterface $storeManager,
		\Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator,
		\Magento\Checkout\Model\Cart $cart,
		\Magento\SalesRule\Model\CouponFactory $couponFactory,
		\Magento\Quote\Api\CartRepositoryInterface $quoteRepository,
		\Magento\Framework\Event\Manager $eventManager
	)
	{
		$this->eventManager = $eventManager;

		parent::__construct($context, $scopeConfig, $checkoutSession, $storeManager, $formKeyValidator, $cart, $couponFactory, $quoteRepository);
	}

	public function execute()
	{
		$couponCode = $this->getRequest()->getParam('remove') == 1
			? ''
			: trim($this->getRequest()->getParam('coupon_code'));

		$cartQuote = $this->cart->getQuote();
		$oldCouponCode = $cartQuote->getCouponCode();

		$codeLength = strlen($couponCode);
		if (!$codeLength && !strlen($oldCouponCode)) {
			return $this->_goBack();
		}

		try {
			$isCodeLengthValid = $codeLength && $codeLength <= \Magento\Checkout\Helper\Cart::COUPON_CODE_MAX_LENGTH;

			$itemsCount = $cartQuote->getItemsCount();
			if ($itemsCount) {
				$cartQuote->getShippingAddress()->setCollectShippingRates(true);
				$cartQuote->setCouponCode($isCodeLengthValid ? $couponCode : '')->collectTotals();
				$this->quoteRepository->save($cartQuote);
			}

			// althea: dispatch custom coupon message event
			$this->eventManager->dispatch('coupon_apply_after', [
				'coupon_code' => $couponCode,
				'quote'       => $cartQuote,
			]);

			if ($codeLength) {
				$escaper = $this->_objectManager->get('Magento\Framework\Escaper');
				if (!$itemsCount) {
					if ($isCodeLengthValid) {
						$coupon = $this->couponFactory->create();
						$coupon->load($couponCode, 'code');
						if ($coupon->getId()) {
							$this->_checkoutSession->getQuote()->setCouponCode($couponCode)->save();
							$this->messageManager->addSuccess(
								__(
									'You used coupon code "%1".',
									$escaper->escapeHtml($couponCode)
								)
							);
						} else {
							$this->messageManager->addError(
								__(
									'The coupon code "%1" is not valid.',
									$escaper->escapeHtml($couponCode)
								)
							);
						}
					} else {
						$this->messageManager->addError(
							__(
								'The coupon code "%1" is not valid.',
								$escaper->escapeHtml($couponCode)
							)
						);
					}
				} else {
					if ($isCodeLengthValid && $couponCode == $cartQuote->getCouponCode()) {
						$this->messageManager->addSuccess(
							__(
								'You used coupon code "%1".',
								$escaper->escapeHtml($couponCode)
							)
						);
					} else if ($msg = $cartQuote->getCouponErrorMessage()) { // althea: handle custom coupon message

						$this->messageManager->addError($msg);
					} else {
						$this->messageManager->addError(
							__(
								'The coupon code "%1" is not valid.',
								$escaper->escapeHtml($couponCode)
							)
						);
						$this->cart->save();
					}
				}
			} else {
				$this->messageManager->addSuccess(__('You canceled the coupon code.'));
			}
		} catch (\Magento\Framework\Exception\LocalizedException $e) {
			$this->messageManager->addError($e->getMessage());
		} catch (\Exception $e) {
			$this->messageManager->addError(__('We cannot apply the coupon code.'));
			$this->_objectManager->get('Psr\Log\LoggerInterface')->critical($e);
		}

		return $this->_goBack();
	}

}