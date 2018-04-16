<?php

namespace TemplateMonster\SocialLogin\Model;

use \Magento\Framework\Json\Helper\Data;
use Magento\Framework\HTTP\ZendClient;
use Magento\Integration\Model\ResourceModel\Oauth\Token\CollectionFactory as TokenCollectionFactory;
use Magento\Integration\Model\Oauth\TokenFactory as TokenModelFactory;

use TemplateMonster\SocialLogin\Api\SocialLoginTokenProviderInterface;
/**
 * class for OAuth providers.
 */
class SocialLoginTokenProvider implements SocialLoginTokenProviderInterface
{
    /**
     * Token Model
     *
     * @var TokenModelFactory
     * @var Oauth2CollectionFactory;
     * @var JsonHelper
     */
    private $tokenModelFactory;
    private $jsonHelper;
    
    /**
     * Token Collection Factory
     *
     * @var TokenCollectionFactory
     */
    private $tokenModelCollectionFactory;
    
    /**
     * Initialize service
     *
     * @param TokenModelFactory $tokenModelFactory
     */
    public function __construct(TokenModelFactory $tokenModelFactory,
    TokenCollectionFactory $tokenModelCollectionFactory,
    \Magento\Framework\Json\Helper\Data $jsonHelper)
    {
        $this->tokenModelCollectionFactory = $tokenModelCollectionFactory;
        $this->tokenModelFactory = $tokenModelFactory;
        $this->jsonHelper = $jsonHelper;
    }
    
    protected function requestEmailGoogle($google_access_token)
    {
        $httpClient = new ZendClient();
        
        $httpClient->setUri('https://www.googleapis.com/oauth2/v1/userinfo');
        $httpClient->setParameterGet(array(
            'alt' => 'json',
            'access_token' => $google_access_token
        ));
        
        return $httpClient->request('GET')->getBody();
    }
    
    protected function requestEmailFacebook($fb_access_token)
    {
        $httpClient = new ZendClient();
        
        $httpClient->setUri('https://graph.facebook.com/me');
        $httpClient->setParameterPost(array(
            'fields' => 'email',
            'access_token' => $fb_access_token
        ));
        
        return $httpClient->request('POST')->getBody();
    }
    
    /**
     * Get user data.
     *
     * @api
     *
     * @param string $id
     *
     * @return string
     */
    public function getTokenforFacebook($fb_access_token)
    {
        $reponseBody = $this->requestEmailFacebook($fb_access_token);
        
        return $this->provideToken($reponseBody);
    }
    
     /**
     * Get user data.
     *
     * @api
     *
     * @param string $id
     *
     * @return string
     */
    public function getTokenforGoogle($google_access_token)
    {
        $reponseBody = $this->requestEmailGoogle($google_access_token);
        
        return $this->provideToken($reponseBody);
    }
    
    protected function provideToken($reponseBody)
    {
        $decodedData = $this->jsonHelper->jsonDecode($reponseBody);

		if(!array_key_exists('email',$decodedData)) {
			return "-1";
		}
	
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $customerModel = $objectManager->create('Magento\Customer\Model\Customer');
        $customerModel->setWebsiteId(1);
        
        // Getting Cusotomer Number by email.
        $customerModel->loadByEmail($decodedData['email']);
        $customerId    = $customerModel->getId();
       
       	$tokenKey = "";
        if ($customerId > 0) {
        
			$resource = $objectManager->get('Magento\Framework\App\ResourceConnection');
			$connection = $resource->getConnection();
            
            $tokenCollection = $this->tokenModelCollectionFactory->create()
            ->addFilter('customer_id', $customerId)
            ->addFieldToFilter('created_at',array('lteq'=>'date_add(now() , interval -1 day)'))
            ->setOrder('created_at','DESC');         
            
            foreach ($tokenCollection as $token) {
                if($tokenCollection->getSize() > 0 ) {
            		if(!array_key_exists('token',$token)) {
            			$tokenKey = $token['token'];
                		break;
                	}
            	}
            }
            
            // Crate New.
            if(strlen($tokenKey) <= 0) {
            	$tokenKey = $this->tokenModelFactory->create()->createCustomerToken($customerId)->getToken();
            }
        }
        return $tokenKey;
    }
}
