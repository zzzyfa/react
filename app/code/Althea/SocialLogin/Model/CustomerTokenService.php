<?php
/**
 * Created by PhpStorm.
 * User: alvin
 * Date: 06/03/2018
 * Time: 2:13 PM
 */

namespace Althea\SocialLogin\Model;

use Althea\SocialLogin\Api\CustomerTokenServiceInterface;
use Magento\Framework\Exception\AuthenticationException;

class CustomerTokenService implements CustomerTokenServiceInterface {

	protected $_providerCollection;
	protected $_accountManagement;
	protected $_tokenFactory;

	/**
	 * CustomerTokenService constructor.
	 *
	 * @param \TemplateMonster\SocialLogin\Model\ResourceModel\Provider\Collection $collection
	 * @param \TemplateMonster\SocialLogin\Model\AccountManagement                 $accountManagement
	 * @param \Magento\Integration\Model\Oauth\TokenFactory                        $tokenFactory
	 */
	public function __construct(
		\TemplateMonster\SocialLogin\Model\ResourceModel\Provider\Collection $collection,
		\TemplateMonster\SocialLogin\Model\AccountManagement $accountManagement,
		\Magento\Integration\Model\Oauth\TokenFactory $tokenFactory
	)
	{
		$this->_providerCollection = $collection;
		$this->_accountManagement  = $accountManagement;
		$this->_tokenFactory       = $tokenFactory;
	}

	public function createSocialCustomerAccessToken($code, $accessToken)
	{
		try {

			$provider = $this->_providerCollection->getItemById($code);
			$data     = $provider->getUserData(['access_token' => $accessToken]);
			$customer = $this->_accountManagement->authenticateByOAuth($data);

			return $this->_tokenFactory->create()
			                           ->createCustomerToken($customer->getId())
			                           ->getToken();
		} catch (\TemplateMonster\SocialLogin\Model\Exception $e) {

			throw new AuthenticationException(
				__('You did not sign in correctly or your account is temporarily disabled.')
			);
		} catch (\Exception $e) {

			throw new AuthenticationException(
				__('You did not sign in correctly or your account is temporarily disabled.')
			);
		}
	}

}