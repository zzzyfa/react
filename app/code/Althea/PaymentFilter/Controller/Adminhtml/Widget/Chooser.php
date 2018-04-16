<?php
/**
 * Created by PhpStorm.
 * User: alvin
 * Date: 13/09/2017
 * Time: 1:05 PM
 */

namespace Althea\PaymentFilter\Controller\Adminhtml\Widget;

use Althea\PaymentFilter\Controller\Adminhtml\Widget;

class Chooser extends Widget {

	/**
	 * Prepare block for chooser
	 *
	 * @return void
	 */
	public function execute()
	{
		$request = $this->getRequest();

		switch ($request->getParam('attribute')) {

			case 'customer_group':
				$block = $this->_view->getLayout()->createBlock(
					'Althea\PaymentFilter\Block\Adminhtml\Widget\Chooser\Customer\Group',
					'paymentfilter_widget_chooser_customer_group',
					['data' => ['js_form_object' => $request->getParam('form')]]
				);
				break;

			case 'customer_id':
				$block = $this->_view->getLayout()->createBlock(
					'Althea\PaymentFilter\Block\Adminhtml\Widget\Chooser\Customer',
					'paymentfilter_widget_chooser_customer',
					['data' => ['js_form_object' => $request->getParam('form')]]
				);
				break;

			default:
				$block = false;
				break;
		}

		if ($block) {

			$this->getResponse()->setBody($block->toHtml());
		}
	}

}