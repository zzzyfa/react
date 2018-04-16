<?php
/**
 * Created by PhpStorm.
 * User: alvin
 * Date: 01/04/2018
 * Time: 7:23 PM
 */

namespace Althea\Catalog\Console\Command;

use Magento\Framework\Model\ResourceModel\Iterator;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SyncBrandProductsCommand extends Command {

	protected $_appState;
	protected $_brandCollectionFactory;
	protected $_productCollectionFactory;
	protected $_categoryFactory;
	protected $_productFactory;
	protected $_storeManager;
	protected $_resourceIterator;

	/**
	 * @inheritDoc
	 */
	public function __construct(
		\Magento\Framework\App\State $appState,
		\TemplateMonster\ShopByBrand\Model\ResourceModel\Brand\CollectionFactory $brandCollectionFactory,
		\Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
		\Magento\Catalog\Model\CategoryFactory $categoryFactory,
		\Magento\Catalog\Model\ProductFactory $productFactory,
		\Magento\Store\Model\StoreManager $storeManager,
		Iterator $iterator,
		string $name = null
	)
	{
		$this->_appState                 = $appState;
		$this->_brandCollectionFactory   = $brandCollectionFactory;
		$this->_productCollectionFactory = $productCollectionFactory;
		$this->_categoryFactory          = $categoryFactory;
		$this->_productFactory           = $productFactory;
		$this->_storeManager             = $storeManager;
		$this->_resourceIterator         = $iterator;

		parent::__construct($name);
	}

	/**
	 * @inheritDoc
	 */
	protected function configure()
	{
		$this->setName('althea:catalog:sync-brand');
		$this->setDescription('[only ran after data migration] sync category brand products to shop by brand.');
	}

	/**
	 * @inheritDoc
	 */
	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$this->_appState->setAreaCode('adminhtml');

		$collection = $this->_brandCollectionFactory->create()
		                                            ->addFieldToFilter('main_table.status', ['eq' => 1]);
		$progress   = new ProgressBar($output, $collection->count());

		$progress->start();
		$this->_resourceIterator->walk($collection->getSelect(), [[$this, 'getCategoryProducts']], [
			'output'   => $output,
			'progress' => $progress,
		]);
		$progress->finish();
		$output->writeln('<info>Done syncing brands.</info>');
	}

	public function getCategoryProducts($args)
	{
		$row = $args['row'];
		/* @var ProgressBar $progress */
		$progress = $args['progress'];
		/* @var OutputInterface $output */
		$output     = $args['output'];
		$category   = $this->_categoryFactory->create()
		                                     ->load($row['brand_id']);
		$collection = $this->_productCollectionFactory->create()
		                                              ->addCategoryFilter($category);

		$this->_resourceIterator->walk($collection->getSelect(), [[$this, 'syncBrand']], [
			'output'   => $output,
			'brand_id' => $row['brand_id'],
		]);

		$progress->advance();
	}

	public function syncBrand($args)
	{
		$row = $args['row'];
		/* @var OutputInterface $output */
		$output  = $args['output'];
		$brandId = $args['brand_id'];

		try {

			$product    = $this->_productFactory->create();
			$connection = $product->getResource()->getConnection();

			$connection->insert('catalog_product_entity_int', [
				'attribute_id' => 221,
				'store_id'     => 0,
				'entity_id'    => $row['entity_id'],
				'value'        => $brandId,
			]);

			$catalog = $this->_productFactory->create()
			                                 ->load($row['entity_id']);

			foreach ($catalog->getStoreIds() as $storeId) {

				$connection->insert('tm_brand_product', [
					'brand_id'   => $brandId,
					'product_id' => $row['entity_id'],
					'store_id'   => $storeId,
				]);
			}
		} catch (\Exception $e) {

			$output->writeln(sprintf("<error>%s</error>", $e->getMessage()));
		}
	}

}