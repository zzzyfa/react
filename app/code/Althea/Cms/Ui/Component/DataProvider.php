<?php
/**
 * Created by PhpStorm.
 * User: alvin
 * Date: 07/08/2017
 * Time: 4:30 PM
 */

namespace Althea\Cms\Ui\Component;

use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\Search\SearchCriteriaBuilder;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\View\Element\UiComponent\DataProvider\Reporting;

class DataProvider extends \Magento\Framework\View\Element\UiComponent\DataProvider\DataProvider {

	/**
	 * @param string                $name
	 * @param string                $primaryFieldName
	 * @param string                $requestFieldName
	 * @param Reporting             $reporting
	 * @param SearchCriteriaBuilder $searchCriteriaBuilder
	 * @param RequestInterface      $request
	 * @param FilterBuilder         $filterBuilder
	 * @param array                 $meta
	 * @param array                 $data
	 */
	public function __construct(
		$name,
		$primaryFieldName,
		$requestFieldName,
		Reporting $reporting,
		SearchCriteriaBuilder $searchCriteriaBuilder,
		RequestInterface $request,
		FilterBuilder $filterBuilder,
		array $meta = [],
		array $data = []
	)
	{
		parent::__construct(
			$name,
			$primaryFieldName,
			$requestFieldName,
			$reporting,
			$searchCriteriaBuilder,
			$request,
			$filterBuilder,
			$meta,
			$data
		);
	}

}