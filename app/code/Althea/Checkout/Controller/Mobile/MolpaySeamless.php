<?php
/**
 * Created by PhpStorm.
 * User: alvin
 * Date: 25/10/2017
 * Time: 5:10 PM
 */

namespace Althea\Checkout\Controller\Mobile;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\ResponseInterface;

class MolpaySeamless extends Action {

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