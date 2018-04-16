<?php
/**
 * Created by PhpStorm.
 * User: alvin
 * Date: 08/09/2017
 * Time: 3:59 PM
 */

namespace Althea\PaymentFilter\Model\Rule\Condition;

use Magento\Rule\Model\Condition\AbstractCondition;
use Magento\Rule\Model\Condition\Context;

class Customer extends AbstractCondition {

	/**
	 * Adminhtml data
	 *
	 * @var \Magento\Backend\Helper\Data
	 */
	protected $_backendData;

	/**
	 * @inheritDoc
	 */
	public function __construct(
		Context $context,
		\Magento\Backend\Helper\Data $backendData,
		array $data = []
	)
	{
		$this->_backendData = $backendData;

		parent::__construct($context, $data);
	}

	/**
	 * @inheritDoc
	 */
	public function loadAttributeOptions()
	{
		$attributes = [
			'customer_group' => __('Customer Groups'),
			'customer_id'    => __('Customers'),
		];

		$this->setAttributeOption($attributes);

		return $this;
	}

	/**
	 * @inheritDoc
	 */
	public function getValueAfterElementHtml()
	{
		$html = '';

		switch ($this->getAttribute()) {

			case 'customer_group':
			case 'customer_id':
				$image = $this->_assetRepo->getUrl('images/rule_chooser_trigger.gif');
				break;
		}

		if (!empty($image)) {

			$html = '<a href="javascript:void(0)" class="rule-chooser-trigger"><img src="' .
				$image .
				'" alt="" class="v-middle rule-chooser-trigger" title="' .
				__(
					'Open Chooser'
				) . '" /></a>';
		}

		return $html;
	}

	/**
	 * Retrieve value element chooser URL
	 *
	 * @return string
	 */
	public function getValueElementChooserUrl()
	{
		$url = false;

		switch ($this->getAttribute()) {

			case 'customer_group':
			case 'customer_id':
				$url = 'althea_paymentfilter/widget/chooser/attribute/' . $this->getAttribute();

				if ($this->getJsFormObject()) {

					$url .= '/form/' . $this->getJsFormObject();
				}
				break;
			default:
				break;
		}

		return $url !== false ? $this->_backendData->getUrl($url) : '';
	}

	/**
	 * Retrieve Explicit Apply
	 *
	 * @return bool
	 * @SuppressWarnings(PHPMD.BooleanGetMethodName)
	 */
	public function getExplicitApply()
	{
		switch ($this->getAttribute()) {

			case 'customer_group':
			case 'customer_id':
				return true;
			default:
				break;
		}

		return false;
	}

}