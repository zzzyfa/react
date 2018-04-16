<?php
/**
 * Created by PhpStorm.
 * User: alvin
 * Date: 07/09/2017
 * Time: 3:11 PM
 */

namespace Althea\PaymentFilter\Controller\Adminhtml;

use Magento\Backend\App\Action;

abstract class Rule extends Action {

	/**
	 * Authorization level of a basic admin session
	 *
	 * @see _isAllowed()
	 */
	const ADMIN_RESOURCE = 'Althea_PaymentFilter::althea_paymentfilter_rules';

	/**
	 * Core registry
	 *
	 * @var \Magento\Framework\Registry
	 */
	protected $_coreRegistry;

	/**
	 * @param \Magento\Backend\App\Action\Context $context
	 * @param \Magento\Framework\Registry         $coreRegistry
	 */
	public function __construct(\Magento\Backend\App\Action\Context $context, \Magento\Framework\Registry $coreRegistry)
	{
		$this->_coreRegistry = $coreRegistry;
		parent::__construct($context);
	}

	/**
	 * Init page
	 *
	 * @param \Magento\Backend\Model\View\Result\Page $resultPage
	 * @return \Magento\Backend\Model\View\Result\Page
	 */
	protected function initPage($resultPage)
	{
		$resultPage->setActiveMenu('Althea_PaymentFilter::althea_paymentfilter_rules')
		           ->addBreadcrumb(__('Payment Filter'), __('Payment Filter'))
		           ->addBreadcrumb(__('Rules'), __('Rules'));

		return $resultPage;
	}
}