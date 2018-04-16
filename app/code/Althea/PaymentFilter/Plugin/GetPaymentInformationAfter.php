<?php
/**
 * Created by PhpStorm.
 * User: alvin
 * Date: 21/09/2017
 * Time: 3:24 PM
 */

namespace Althea\PaymentFilter\Plugin;

use Althea\PaymentFilter\Helper\Config;
use Magento\Checkout\Api\Data\PaymentDetailsExtensionFactory;
use Magento\Checkout\Api\Data\PaymentDetailsInterface;
use Magento\Checkout\Api\PaymentInformationManagementInterface;
use Magento\Framework\App\ObjectManager;
use \Magento\Payment\Model\Checks\SpecificationFactory;
use Magento\Payment\Model\Method\AbstractMethod;
use Magento\Payment\Model\MethodList;
use Magento\Quote\Api\CartRepositoryInterface;

class GetPaymentInformationAfter extends MethodList {

	protected $_configHelper;
	protected $_quoteRepository;
	protected $_extensionAttributeFactory;

	/**
	 * @inheritDoc
	 */
	public function __construct(
		\Magento\Payment\Helper\Data $paymentHelper,
		SpecificationFactory $specificationFactory,
		Config $configHelper,
		CartRepositoryInterface $quoteRepository,
		PaymentDetailsExtensionFactory $extensionAttributeFactory
	)
	{
		$this->_configHelper              = $configHelper;
		$this->_quoteRepository           = $quoteRepository;
		$this->_extensionAttributeFactory = $extensionAttributeFactory;

		parent::__construct($paymentHelper, $specificationFactory);
	}

	/**
	 * althea: include disabled payment methods in payment information
	 *
	 * @param PaymentInformationManagementInterface $management
	 * @param callable                              $proceed
	 * @param                                       $cartId
	 * @return PaymentDetailsInterface
	 */
	public function aroundGetPaymentInformation(PaymentInformationManagementInterface $management, callable $proceed, $cartId)
	{
		/* @var PaymentDetailsInterface $result */
		$result = $proceed($cartId);

		if (!$this->_configHelper->getGeneralEnabled() || !$this->_configHelper->getGeneralShowDisabledMethods()) {

			return $result;
		}

		$quote                 = $this->_quoteRepository->get($cartId);
		$store                 = $quote ? $quote->getStoreId() : null;
		$methodList            = ObjectManager::getInstance()->get(\Magento\Payment\Api\PaymentMethodListInterface::class);
		$methodInstanceFactory = ObjectManager::getInstance()->get(\Magento\Payment\Model\Method\InstanceFactory::class);
		$disabledMethods       = [];

		foreach ($methodList->getActiveList($store) as $method) {

			/* @var AbstractMethod $methodInstance */
			$methodInstance = $methodInstanceFactory->create($method);

			// althea: show only disabled and filtered payment method
			if ((!$methodInstance->isAvailable($quote) && $methodInstance->getData('is_filtered')) || !$this->_canUseMethod($methodInstance, $quote)) {

				$disabledMethods[] = $methodInstance;
			}
		}

		$extAttributes = $result->getExtensionAttributes();

		if (!$extAttributes) {

			$extAttributes = $this->_extensionAttributeFactory->create();
		}

		$extAttributes->setDisabledPaymentMethods($disabledMethods);
		$result->setExtensionAttributes($extAttributes);

		return $result;
	}

}