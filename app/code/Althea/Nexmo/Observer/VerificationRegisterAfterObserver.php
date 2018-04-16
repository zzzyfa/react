<?php
/**
 * Created by PhpStorm.
 * User: alvin
 * Date: 25/07/2017
 * Time: 4:56 PM
 */

namespace Althea\Nexmo\Observer;

use Althea\Nexmo\Model\VerificationFactory;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class VerificationRegisterAfterObserver implements ObserverInterface {

	protected $verificationFactory;

	public function __construct(VerificationFactory $verificationFactory)
	{
		$this->verificationFactory = $verificationFactory;
	}

	public function execute(\Magento\Framework\Event\Observer $observer)
	{
		$customerId   = $observer->getData('customer_id');
		$websiteId    = $observer->getData('website_id');
		$phoneNo      = $observer->getData('phone_number');
		$requestId    = $observer->getData('request_id');
		$verification = $this->verificationFactory->create();

		$verification->loadByCustomer($customerId);

		if (!$customerId || !$phoneNo || !$requestId) {

			return false;
		}

		$verification->addData([
			'customer_id'  => $customerId,
			'website_id'   => $websiteId,
			'phone_number' => $phoneNo,
			'request_id'   => $requestId,
		])->save();
	}

}