<?php
/**
 * Created by PhpStorm.
 * User: alvin
 * Date: 04/01/2018
 * Time: 11:52 AM
 */

namespace Althea\Directory\Setup;

use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Rinvex\Country\CountryLoaderException;

class InstallData implements InstallDataInterface {

	protected $_countryCollectionFactory;
	protected $_regionCollectionFactory;
	protected $_resourceIterator;

	/**
	 * InstallData constructor.
	 *
	 * @param \Magento\Directory\Model\ResourceModel\Country\CollectionFactory $countryCollectionFactory
	 * @param \Magento\Directory\Model\ResourceModel\Region\CollectionFactory  $regionCollectionFactory
	 * @param \Magento\Framework\Model\ResourceModel\Iterator                  $iterator
	 */
	public function __construct(
		\Magento\Directory\Model\ResourceModel\Country\CollectionFactory $countryCollectionFactory,
		\Magento\Directory\Model\ResourceModel\Region\CollectionFactory $regionCollectionFactory,
		\Magento\Framework\Model\ResourceModel\Iterator $iterator
	)
	{
		$this->_countryCollectionFactory = $countryCollectionFactory;
		$this->_regionCollectionFactory  = $regionCollectionFactory;
		$this->_resourceIterator         = $iterator;
	}

	/**
	 * @inheritDoc
	 */
	public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
	{
		$countryCollection = $this->_countryCollectionFactory->create();

		$this->_resourceIterator->walk($countryCollection->getSelect(), [[$this, 'importRegions']], ['setup' => $setup]);
	}

	public function importRegions($args)
	{
		/* @var ModuleDataSetupInterface $setup */
		$setup        = $args['setup'];
		$row          = $args['row'];
		$countryCode  = $row['country_id'];
		$regionsCount = $this->_regionCollectionFactory->create()
		                                               ->addFieldToFilter('country_id', ['eq' => $countryCode])
		                                               ->getSize();

		try {

			$country = country(strtolower($countryCode));

			if ($regionsCount > 0 || !$country->getDivisions()) {

				return;
			}

			foreach ($country->getDivisions() as $key => $val) {

				$bind = [
					'country_id'   => $countryCode,
					'code'         => $key,
					'default_name' => $val['name'],
				];

				$setup->getConnection()->insert($setup->getTable('directory_country_region'), $bind);

				$regionId = $setup->getConnection()->lastInsertId($setup->getTable('directory_country_region'));
				$bind     = [
					'locale'    => 'en_US',
					'region_id' => $regionId,
					'name'      => $val['name'],
				];

				$setup->getConnection()->insert($setup->getTable('directory_country_region_name'), $bind);
			}
		} catch (CountryLoaderException $e) {

			return;
		}
	}

}