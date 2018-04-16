<?php
/**
 * Created by PhpStorm.
 * User: alvin
 * Date: 03/08/2017
 * Time: 12:26 PM
 */

namespace Althea\Nexmo\Model;

use Althea\Nexmo\Api\VerificationApiInterface;
use Althea\Nexmo\Exception\InternalErrorException;
use Althea\Nexmo\Exception\RequestNotCanceledException;
use Althea\Nexmo\Helper\Verification;
use Althea\Nexmo\Logger\NexmoLogger;
use libphonenumber\NumberParseException;
use libphonenumber\PhoneNumberFormat;
use libphonenumber\PhoneNumberUtil;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Directory\Model\CountryFactory;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\State\InvalidTransitionException;
use Magento\Store\Model\StoreManagerInterface;

class VerificationApi implements VerificationApiInterface {

	protected $customerRepository;
	protected $storeManager;
	protected $verificationHelper;
	protected $countryFactory;
	protected $customLogger;

	/**
	 * VerificationApi constructor.
	 *
	 * @param $storeManager
	 */
	public function __construct(
		CustomerRepositoryInterface $customerRepository,
		StoreManagerInterface $storeManager,
		Verification $verificationHelper,
		CountryFactory $countryFactory,
		NexmoLogger $customLogger
	)
	{
		$this->customerRepository = $customerRepository;
		$this->storeManager       = $storeManager;
		$this->verificationHelper = $verificationHelper;
		$this->countryFactory     = $countryFactory;
		$this->customLogger       = $customLogger;
	}

	public function request($phoneNo, $countryCode, $customerId)
	{
		$exceptionData = array(
			'customerId'  => $customerId,
			'phoneNo'     => $phoneNo,
			'countryCode' => $countryCode,
		);
		$customer      = $this->customerRepository->getById($customerId);

		$this->storeManager->setCurrentStore($customer->getStoreId());

		$formatter   = PhoneNumberUtil::getInstance();
		$intlPhoneNo = null;

		try {

			$instance    = $formatter->parse($phoneNo, $countryCode);
			$intlPhoneNo = $formatter->format($instance, PhoneNumberFormat::E164);

			if (!$formatter->isValidNumber($instance)) { // check if number is valid

				$country = $this->countryFactory->create();

				$country->loadByCode($countryCode);

				throw new InputException(__("Invalid %country phone number.", $country->getName()));
			} else if ($this->verificationHelper->isCustomerVerified($customerId)) { // check customer verification status

				throw new InvalidTransitionException(__("Customer has already been verified."));
			} else if ($this->verificationHelper->isPhoneNumberInUse($intlPhoneNo)) { // check if phone number is in use

				throw new InvalidTransitionException(__("Phone number is already in use."));
			}
		} catch (NumberParseException $e) {

			$exceptionData['exception'] = $e->getMessage();

			$this->customLogger->debug(json_encode($exceptionData));

			throw new InputException(__("Invalid phone number."));
		}

		try {

			$requestId = $this->verificationHelper->request($customerId, $intlPhoneNo, $countryCode);

			return $requestId;
		} catch (RequestNotCanceledException $e) {

			$exceptionData['exception'] = $e->getMessage();

			$this->customLogger->debug(json_encode($exceptionData));

			throw new InvalidTransitionException(__($e->getDefaultErrorMessage()));
		} catch (InternalErrorException $e) {

			$exceptionData['exception'] = $e->getMessage();

			$this->customLogger->debug(json_encode($exceptionData));

			throw new InvalidTransitionException(__("SMS verification server under maintenance. Please try again later."));
		} catch (\Exception $e) {

			$exceptionData['exception'] = $e->getMessage();

			$this->customLogger->debug(json_encode($exceptionData));

			throw new InvalidTransitionException(__("Failed to process request. Please try again later."));
		}
	}

	public function verify($requestId, $code, $customerId)
	{
		$exceptionData = [
			'requestId'  => $requestId,
			'code'       => $code,
			'customerId' => $customerId,
		];

		try {

			$eventId = $this->verificationHelper->validate($customerId, $requestId, $code);

			return $eventId;
		} catch (\Exception $e) {

			$exceptionData['exception'] = $e->getMessage();

			$this->customLogger->debug(json_encode($exceptionData));

			throw new InvalidTransitionException(__("Failed to process request. Please try again later."));
		}
	}

}