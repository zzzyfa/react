<?php
/**
 * Created by PhpStorm.
 * User: alvin
 * Date: 24/07/2017
 * Time: 3:32 PM
 */

namespace Althea\Nexmo\Helper;

use Althea\Nexmo\Exception\InternalErrorException;
use Althea\Nexmo\Exception\RequestNotCanceledException;
use Althea\Nexmo\Logger\NexmoLogger;
use Althea\Nexmo\Model\VerificationFactory;
use GuzzleHttp\Client;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Customer\Model\CustomerFactory;
use Magento\Store\Model\StoreManagerInterface;

class Verification extends AbstractHelper {

	const BRAND_NAME = 'Althea';
	const EXPIRY_MIN = 60;
	const EXPIRY_MAX = 3600;

	protected $configHelper;
	protected $storeManager;
	protected $verificationFactory;
	protected $customerFactory;
	protected $customLogger;

	public function __construct(
		Context $context,
		Config $configHelper,
		StoreManagerInterface $storeManager,
		VerificationFactory $verificationFactory,
		CustomerFactory $customerFactory,
		NexmoLogger $customLogger
	)
	{
		$this->configHelper        = $configHelper;
		$this->storeManager        = $storeManager;
		$this->verificationFactory = $verificationFactory;
		$this->customerFactory     = $customerFactory;
		$this->customLogger        = $customLogger;

		parent::__construct($context);
	}

	protected function _initQuery()
	{
		return [
			'api_key'    => $this->configHelper->getApiKey(),
			'api_secret' => $this->configHelper->getApiSecret(),
		];
	}

	/**
	 * Check if phone number is already in use
	 *
	 * @param $phoneNo
	 * @return bool
	 */
	public function isPhoneNumberInUse($phoneNo)
	{
		$websiteId    = $this->storeManager->getWebsite()
		                                   ->getId();
		$verification = $this->verificationFactory->create();

		$verification->loadActiveByPhoneNo($phoneNo, $websiteId);

		return (!is_null($verification->getId()));
	}

	/**
	 * Check if customer is already verified
	 *
	 * @param $customerId
	 * @return bool
	 */
	public function isCustomerVerified($customerId)
	{
		$verification = $this->verificationFactory->create();

		$verification->loadActiveByCustomer($customerId);

		return (!is_null($verification->getId()));
	}

	/**
	 * Create Nexmo verification request
	 *
	 * @param $customerId
	 * @param $phoneNo
	 * @param $countryCode
	 * @return mixed
	 */
	public function request($customerId, $phoneNo, $countryCode)
	{
		$this->cancel($customerId, $phoneNo);

		// send request
		$client             = new Client();
		$query              = $this->_initQuery();
		$query['country']   = $countryCode;
		$query['sender_id'] = self::BRAND_NAME; // may varies due to country regulations
		$query['brand']     = self::BRAND_NAME;
		$query['number']    = $phoneNo;
		$expiry             = $this->configHelper->getPinExpiry();
		$exceptionData      = [
			'customerId'  => $customerId,
			'phoneNo'     => $phoneNo,
			'countryCode' => $countryCode,
		];

		if ($expiry
			&& is_numeric($expiry)
			&& $expiry >= self::EXPIRY_MIN
			&& $expiry <= self::EXPIRY_MAX
		) {

			$query['pin_expiry'] = $expiry;
		}

		$response = $client->request('GET', 'https://api.nexmo.com/verify/json', array('query' => $query));
		$result   = json_decode((string)$response->getBody(), true);

		if (empty($result)) {

			$exceptionData['exception'] = 'Empty response returned from Nexmo Verify.';

			$this->customLogger->debug(json_encode($exceptionData));

			throw new \Exception();
		} else if (in_array($result['status'], array('7', '10', '15'))) {

			$exceptionData['exception'] = $result['error_text'];

			$this->customLogger->debug(json_encode($exceptionData));

			throw new \Exception(__($result['error_text']));
		} else if ($result['status'] == '5') {

			$exceptionData['exception'] = $result['error_text'];

			$this->customLogger->debug(json_encode($exceptionData));

			throw new InternalErrorException(__("SMS verification server under maintenance. Please try again later."));
		} else if (!empty($result['error_text'])) {

			$exceptionData['exception'] = $result['error_text'];

			$this->customLogger->debug(json_encode($exceptionData));

			throw new \Exception();
		} else if (empty($result['request_id'])) {

			$exceptionData['exception'] = 'No verification request ID returned from Nexmo Verify.';

			$this->customLogger->debug(json_encode($exceptionData));

			throw new \Exception();
		}

		$customer = $this->customerFactory->create();

		$customer->load($customerId);

		// dispatch verification created event
		$this->_eventManager->dispatch('althea_nexmo_verification_register_after', [
			'customer_id'  => $customerId,
			'website_id'   => $customer->getData('website_id'),
			'phone_number' => $phoneNo,
			'request_id'   => $result['request_id'],
		]);

		return $result['request_id'];
	}

	/**
	 * Validate Nexmo verification code
	 *
	 * @param $customerId
	 * @param $requestId
	 * @param $code
	 * @return mixed
	 */
	public function validate($customerId, $requestId, $code)
	{
		if (empty($code)) {

			throw new \Exception(__("Invalid verification code."));
		} else if (empty($requestId)) {

			throw new \Exception(__("Invalid verification request ID."));
		}

		// send request
		$client              = new Client();
		$query               = $this->_initQuery();
		$query['code']       = $code;
		$query['request_id'] = $requestId;
		$response            = $client->request('GET', 'https://api.nexmo.com/verify/check/json', array('query' => $query));
		$result              = json_decode((string)$response->getBody(), true);
		$exceptionData       = [
			'customerId' => $customerId,
			'requestId'  => $requestId,
			'code'       => $code,
		];

		if (empty($result)) {

			$exceptionData['exception'] = 'Empty response returned from Nexmo Verify.';

			$this->customLogger->debug(json_encode($exceptionData));

			throw new \Exception('');
		} else if (in_array($result['status'], array('16', '17'))) {

			$exceptionData['exception'] = $result['error_text'];

			$this->customLogger->debug(json_encode($exceptionData));

			throw new \Exception($result['error_text']);
		} else if (!empty($result['error_text'])) {

			$exceptionData['exception'] = $result['error_text'];

			$this->customLogger->debug(json_encode($exceptionData));

			throw new \Exception('');
		} else if (empty($result['event_id'])) {

			$exceptionData['exception'] = 'No validation event ID returned from Nexmo Verify.';

			$this->customLogger->debug(json_encode($exceptionData));

			throw new \Exception('');
		}

		// dispatch verification success event
		$this->_eventManager->dispatch('althea_nexmo_verification_validate_success', array(
			'customer_id' => $customerId,
		));

		return $result['event_id'];
	}

	/**
	 * Cancel on-going verification request
	 *
	 * @param $customerId
	 * @param $phoneNo
	 */
	public function cancel($customerId, $phoneNo)
	{
		$verification = $this->verificationFactory->create();

		$verification->loadByCustomerAndPhoneNo($customerId, $phoneNo);

		if ($verification->getId() && $verification->getData('request_id')) {

			// send request
			$client              = new Client();
			$query               = $this->_initQuery();
			$query['cmd']        = 'cancel';
			$query['request_id'] = $verification->getData('request_id');
			$response            = $client->request('GET', 'https://api.nexmo.com/verify/control/json', array('query' => $query));
			$result              = json_decode((string)$response->getBody(), true);
			$exceptionData       = [
				'customerId' => $customerId,
				'phoneNo'    => $phoneNo,
			];

			// 19 - nexmo verification request cannot be canceled
			if ($result['status'] == '19' && !empty($result['error_text'])) {

				$exceptionData['exception'] = $result['error_text'];

				$this->customLogger->debug(json_encode($exceptionData));

				throw new RequestNotCanceledException(__($result['error_text']), null, $this->configHelper);
			}
		}
	}

}