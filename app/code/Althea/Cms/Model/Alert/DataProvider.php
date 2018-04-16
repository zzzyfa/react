<?php
/**
 * Created by PhpStorm.
 * User: alvin
 * Date: 08/08/2017
 * Time: 10:37 AM
 */

namespace Althea\Cms\Model\Alert;

use Althea\Cms\Model\ResourceModel\Alert\CollectionFactory;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Ui\DataProvider\AbstractDataProvider;

class DataProvider extends AbstractDataProvider {

	/**
	 * @var \Magento\Cms\Model\ResourceModel\Block\Collection
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
	 * @param CollectionFactory      $alertCollectionFactory
	 * @param DataPersistorInterface $dataPersistor
	 * @param array                  $meta
	 * @param array                  $data
	 */
	public function __construct(
		$name,
		$primaryFieldName,
		$requestFieldName,
		CollectionFactory $alertCollectionFactory,
		DataPersistorInterface $dataPersistor,
		array $meta = [],
		array $data = []
	)
	{
		$this->collection    = $alertCollectionFactory->create();
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

		/** @var \Althea\Cms\Model\Alert $alert */
		foreach ($items as $alert) {

			$this->loadedData[$alert->getId()] = $alert->getData();
		}

		$data = $this->dataPersistor->get('cms_alert');

		if (!empty($data)) {

			$alert = $this->collection->getNewEmptyItem();

			$alert->setData($data);
			$this->loadedData[$alert->getId()] = $alert->getData();
			$this->dataPersistor->clear('cms_alert');
		}

		return $this->loadedData;
	}

}