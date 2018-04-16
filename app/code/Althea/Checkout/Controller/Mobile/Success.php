<?php
/**
 * Created by PhpStorm.
 * User: alvin
 * Date: 18/01/2018
 * Time: 3:49 PM
 */

namespace Althea\Checkout\Controller\Mobile;

use Althea\Checkout\Helper\Mobile;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Exception\NotFoundException;

class Success extends Action {

	protected $_mobileCheckoutHelper;

	/**
	 * @inheritDoc
	 */
	public function __construct(Context $context, Mobile $mobile)
	{
		parent::__construct($context);

		$this->_mobileCheckoutHelper = $mobile;
	}

	/**
	 * @inheritDoc
	 */
	public function execute()
	{
		if (!$this->_mobileCheckoutHelper->isMobileCheckout()) {

			throw new NotFoundException(__('Page not found.'));
		}

		$this->_mobileCheckoutHelper->clearMobileCheckoutSession();
		$this->_view->loadLayout();
		$this->_view->getLayout()->initMessages();
		$this->_view->renderLayout();
	}

}