<?php
/**
 * Created by PhpStorm.
 * User: alvin
 * Date: 09/08/2017
 * Time: 3:05 PM
 */

namespace Althea\Cms\Controller\Adminhtml;

use Magento\Backend\App\Action;

abstract class Alert extends Action {

	/**
	 * Authorization level of a basic admin session
	 *
	 * @see _isAllowed()
	 */
	const ADMIN_RESOURCE = 'Althea_Cms::alert';

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
		$resultPage->setActiveMenu('Althea_Cms::althea_alert')
		           ->addBreadcrumb(__('CMS'), __('CMS'))
		           ->addBreadcrumb(__('Alerts'), __('Alerts'));

		return $resultPage;
	}

}