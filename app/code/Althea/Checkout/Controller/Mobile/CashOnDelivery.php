<?php
/**
 * Created by PhpStorm.
 * User: alvin
 * Date: 21/02/2018
 * Time: 3:53 PM
 */

namespace Althea\Checkout\Controller\Mobile;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\ResponseInterface;

class CashOnDelivery extends Action {

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