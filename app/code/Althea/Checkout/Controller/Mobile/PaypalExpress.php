<?php
/**
 * Created by PhpStorm.
 * User: alvin
 * Date: 27/10/2017
 * Time: 6:14 PM
 */

namespace Althea\Checkout\Controller\Mobile;

use Magento\Framework\App\Action\Action;

class PaypalExpress extends Action {

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