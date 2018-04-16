<?php
/**
 * Created by PhpStorm.
 * User: alvin
 * Date: 13/09/2017
 * Time: 1:04 PM
 */

namespace Althea\PaymentFilter\Controller\Adminhtml;

use Magento\Backend\App\Action;

abstract class Widget extends Action {

	/**
	 * Authorization level of a basic admin session
	 *
	 * @see _isAllowed()
	 */
	const ADMIN_RESOURCE = 'Althea_PaymentFilter::althea_paymentfilter_rules';

}