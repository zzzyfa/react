<?php
/**
 * Created by PhpStorm.
 * User: alvin
 * Date: 28/12/2017
 * Time: 6:42 PM
 */

namespace Althea\Freeshippinglabel\Controller\Adminhtml\Settings;

use Magento\Backend\App\Action;

class NewAction extends Action {

	/**
	 * @var \Magento\Backend\Model\View\Result\ForwardFactory
	 */
	protected $_resultForwardFactory;

	/**
	 * @inheritDoc
	 */
	public function __construct(
		\Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory,
		Action\Context $context
	)
	{
		$this->_resultForwardFactory = $resultForwardFactory;

		parent::__construct($context);
	}

	/**
	 * @inheritDoc
	 */
	public function execute()
	{
		/** @var \Magento\Framework\Controller\Result\Forward $resultForward */
		$resultForward = $this->_resultForwardFactory->create();

		return $resultForward->forward('edit');
	}

}