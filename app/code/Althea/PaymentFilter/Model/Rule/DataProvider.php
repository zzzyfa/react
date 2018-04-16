<?php
/**
 * Created by PhpStorm.
 * User: alvin
 * Date: 07/09/2017
 * Time: 5:54 PM
 */

namespace Althea\PaymentFilter\Model\Rule;

use Althea\PaymentFilter\Model\ResourceModel\Rule\CollectionFactory;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Ui\DataProvider\AbstractDataProvider;

class DataProvider extends AbstractDataProvider {

	/**
	 * @var \Althea\PaymentFilter\Model\ResourceModel\Rule\Collection
	 */
	protected $collection;

	/**
	 * @var DataPersistorInterface
	 */
	protected $dataPersistor;

	/**
	 * @var array
	 */
	protected $loadedData;

	/**
	 * Constructor
	 *
	 * @param string                 $name
	 * @param string                 $primaryFieldName
	 * @param string                 $requestFieldName
	 * @param CollectionFactory      $ruleCollectionFactory
	 * @param DataPersistorInterface $dataPersistor
	 * @param array                  $meta
	 * @param array                  $data
	 */
	public function __construct(
		$name,
		$primaryFieldName,
		$requestFieldName,
		CollectionFactory $ruleCollectionFactory,
		DataPersistorInterface $dataPersistor,
		array $meta = [],
		array $data = []
	)
	{
		$this->collection    = $ruleCollectionFactory->create();
		$this->dataPersistor = $dataPersistor;

		parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
	}

	/**
	 * Get data
	 *
	 * @return array
	 */
	public function getData()
	{
		if (isset($this->loadedData)) {

			return $this->loadedData;
		}

		$items = $this->collection->getItems();

		/** @var \Althea\PaymentFilter\Model\Rule $rule */
		foreach ($items as $rule) {

			$this->loadedData[$rule->getId()] = $rule->getData();
		}

		$data = $this->dataPersistor->get('paymentfilter_rule');

		if (!empty($data)) {

			$rule = $this->collection->getNewEmptyItem();

			$rule->setData($data);
			$this->loadedData[$rule->getId()] = $rule->getData();
			$this->dataPersistor->clear('paymentfilter_rule');
		}

		return $this->loadedData;
	}

}