<?php
/**
 * Created by PhpStorm.
 * User: alvin
 * Date: 08/08/2017
 * Time: 9:56 AM
 */

namespace Althea\Cms\Controller\Adminhtml\Banner;

use Althea\Cms\Controller\Adminhtml\Banner;

class NewAction extends Banner {

	/**
	 * @var \Magento\Backend\Model\View\Result\ForwardFactory
	 */
	protected $resultForwardFactory;

	/**
	 * @param \Magento\Backend\App\Action\Context               $context
	 * @param \Magento\Framework\Registry                       $coreRegistry
	 * @param \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory
	 */
	public function __construct(
		\Magento\Backend\App\Action\Context $context,
		\Magento\Framework\Registry $coreRegistry,
		\Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory
	)
	{
		$this->resultForwardFactory = $resultForwardFactory;

		parent::__construct($context, $coreRegistry);
	}

	/**
	 * Create new CMS banner
	 *
	 * @return \Magento\Framework\Controller\ResultInterface
	 */
	public function execute()
	{
		/** @var \Magento\Framework\Controller\Result\Forward $resultForward */
		$resultForward = $this->resultForwardFactory->create();

		return $resultForward->forward('edit');
	}

}