<?php
/**
 * Created by PhpStorm.
 * User: alvin
 * Date: 27/10/2017
 * Time: 3:15 PM
 */

namespace Althea\Checkout\Block;

use Magento\Checkout\Model\Session;

class Onepage extends \Magento\Checkout\Block\Onepage {

	protected $_checkoutSession;

	/**
	 * @inheritDoc
	 */
	public function __construct(
		\Magento\Framework\View\Element\Template\Context $context,
		\Magento\Framework\Data\Form\FormKey $formKey,
		\Magento\Checkout\Model\CompositeConfigProvider $configProvider,
		Session $checkoutSession,
		array $layoutProcessors = [],
		array $data = []
	)
	{
		parent::__construct($context, $formKey, $configProvider, $layoutProcessors, $data);

		$this->_checkoutSession = $checkoutSession;
	}

	public function getCheckoutSession()
	{
		return $this->_checkoutSession;
	}

}