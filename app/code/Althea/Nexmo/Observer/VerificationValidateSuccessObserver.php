<?php
/**
 * Created by PhpStorm.
 * User: alvin
 * Date: 25/07/2017
 * Time: 4:59 PM
 */

namespace Althea\Nexmo\Observer;

use Althea\Nexmo\Model\Verification;
use Althea\Nexmo\Model\VerificationFactory;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class VerificationValidateSuccessObserver implements ObserverInterface {

	protected $verificationFactory;

	public function __construct(VerificationFactory $verificationFactory)
	{
		$this->verificationFactory = $verificationFactory;
	}

	public function execute(\Magento\Framework\Event\Observer $observer)
	{
		$customerId   = $observer->getData('customer_id');
		$verification = $this->verificationFactory->create();

		$verification->loadByCustomer($customerId);

		if (!$verification->getId()) {

			return false;
		}

		$verification->setStatus(Verification::STATUS_VERIFIED)
		             ->save();
	}

}