<?php
/**
 * Created by PhpStorm.
 * User: alvin
 * Date: 07/09/2017
 * Time: 4:27 PM
 */

namespace Althea\PaymentFilter\Ui\Component\Listing\Column;

use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;

class RuleActions extends Column {

	/**
	 * Url path
	 */
	const URL_PATH_EDIT    = 'althea_paymentfilter/rule/edit';
	const URL_PATH_DELETE  = 'althea_paymentfilter/rule/delete';
	const URL_PATH_DETAILS = 'althea_paymentfilter/rule/details';

	/**
	 * @var UrlInterface
	 */
	protected $urlBuilder;

	/**
	 * Constructor
	 *
	 * @param ContextInterface   $context
	 * @param UiComponentFactory $uiComponentFactory
	 * @param UrlInterface       $urlBuilder
	 * @param array              $components
	 * @param array              $data
	 */
	public function __construct(
		ContextInterface $context,
		UiComponentFactory $uiComponentFactory,
		UrlInterface $urlBuilder,
		array $components = [],
		array $data = []
	)
	{
		$this->urlBuilder = $urlBuilder;

		parent::__construct($context, $uiComponentFactory, $components, $data);
	}
	
	/**
	 * Prepare Data Source
	 *
	 * @param array $dataSource
	 * @return array
	 */
	public function prepareDataSource(array $dataSource)
	{
		if (isset($dataSource['data']['items'])) {

			foreach ($dataSource['data']['items'] as & $item) {

				if (isset($item['rule_id'])) {

					$item[$this->getData('name')] = [
						'edit'   => [
							'href'  => $this->urlBuilder->getUrl(static::URL_PATH_EDIT, [
								'rule_id' => $item['rule_id'],
							]),
							'label' => __('Edit'),
						],
						'delete' => [
							'href'    => $this->urlBuilder->getUrl(static::URL_PATH_DELETE, [
								'rule_id' => $item['rule_id'],
							]),
							'label'   => __('Delete'),
							'confirm' => [
								'title'   => __('Delete "${ $.$data.title }"'),
								'message' => __('Are you sure you wan\'t to delete a "${ $.$data.title }" record?'),
							],
						],
					];
				}
			}
		}

		return $dataSource;
	}

}