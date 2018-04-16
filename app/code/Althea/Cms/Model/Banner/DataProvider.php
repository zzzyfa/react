<?php
/**
 * Created by PhpStorm.
 * User: alvin
 * Date: 08/08/2017
 * Time: 10:37 AM
 */

namespace Althea\Cms\Model\Banner;

use Althea\Cms\Model\ResourceModel\Banner\CollectionFactory;
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
	 * @param CollectionFactory      $bannerCollectionFactory
	 * @param DataPersistorInterface $dataPersistor
	 * @param array                  $meta
	 * @param array                  $data
	 */
	public function __construct(
		$name,
		$primaryFieldName,
		$requestFieldName,
		CollectionFactory $bannerCollectionFactory,
		DataPersistorInterface $dataPersistor,
		array $meta = [],
		array $data = []
	)
	{
		$this->collection    = $bannerCollectionFactory->create();
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

		/** @var \Althea\Cms\Model\Banner $banner */
		foreach ($items as $banner) {

			$this->loadedData[$banner->getId()] = $banner->getData();
		}

		$data = $this->dataPersistor->get('cms_banner');

		if (!empty($data)) {

			$banner = $this->collection->getNewEmptyItem();

			$banner->setData($data);
			$this->loadedData[$banner->getId()] = $banner->getData();
			$this->dataPersistor->clear('cms_banner');
		}

		return $this->loadedData;
	}

}