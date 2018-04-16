<?php
/**
 * Created by PhpStorm.
 * User: alvin
 * Date: 12/03/2018
 * Time: 2:59 PM
 */

namespace Althea\Checkout\Controller\Mobile;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\ResponseInterface;

class Free extends Action {

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