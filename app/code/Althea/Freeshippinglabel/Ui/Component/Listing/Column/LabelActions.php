<?php
/**
 * Created by PhpStorm.
 * User: alvin
 * Date: 28/12/2017
 * Time: 4:31 PM
 */

namespace Althea\Freeshippinglabel\Ui\Component\Listing\Column;

use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;

class LabelActions extends Column {

	/**
	 * Url path
	 */
	const URL_PATH_EDIT    = 'aw_fslabel_admin/settings/edit';
	const URL_PATH_DELETE  = 'aw_fslabel_admin/settings/delete';
	const URL_PATH_DETAILS = 'aw_fslabel_admin/settings/details';

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

				if (isset($item['id'])) {

					$item[$this->getData('name')] = [
						'edit'   => [
							'href'  => $this->urlBuilder->getUrl(static::URL_PATH_EDIT, [
								'id' => $item['id'],
							]),
							'label' => __('Edit'),
						],
						'delete' => [
							'href'    => $this->urlBuilder->getUrl(static::URL_PATH_DELETE, [
								'id' => $item['id'],
							]),
							'label'   => __('Delete'),
							'confirm' => [
								'title'   => __('Delete label "${ $.$data.identifier }"'),
								'message' => __('Are you sure you wan\'t to delete label "${ $.$data.identifier }" record?'),
							],
						],
					];
				}
			}
		}

		return $dataSource;
	}

}