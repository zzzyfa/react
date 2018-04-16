<?php
/**
 * Created by PhpStorm.
 * User: alvin
 * Date: 26/10/2017
 * Time: 11:11 AM
 */

namespace Althea\Checkout\Controller\Mobile;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\ResponseInterface;

class AdyenOneClick extends Action {

	/**
	 * @inheritDoc
	 */
	public function execute()
	{
		$this->_view->loadLayout();
		$this->_view->getLayout()->initMessages();
		$this->_view->renderLayout();
	}

}