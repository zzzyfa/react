<?php
/**
 * Created by PhpStorm.
 * User: alvin
 * Date: 03/04/2018
 * Time: 10:06 AM
 */

namespace Althea\Catalog\Console\Command;

use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\Image\Cache as ImageCache;
use Magento\Catalog\Model\Product\Image\CacheFactory as ImageCacheFactory;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory as ProductCollectionFactory;
use Magento\Framework\App\State as AppState;
use Magento\Framework\Exception\NoSuchEntityException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ProductImagesResizeCommand extends Command {

	/**
	 * Indexer name option
	 */
	const INPUT_KEY_PRODUCT_IDS = 'id';

	/**
	 * @var AppState
	 */
	protected $appState;

	/**
	 * @var \Magento\Catalog\Api\ProductRepositoryInterface
	 */
	protected $productRepository;

	/**
	 * @var ImageCacheFactory
	 */
	protected $imageCacheFactory;

	/**
	 * @param AppState                                        $appState
	 * @param \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
	 * @param ImageCacheFactory                               $imageCacheFactory
	 */
	public function __construct(
		AppState $appState,
		\Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
		ImageCacheFactory $imageCacheFactory
	)
	{
		$this->appState          = $appState;
		$this->productRepository = $productRepository;
		$this->imageCacheFactory = $imageCacheFactory;
		parent::__construct();
	}

	/**
	 * {@inheritdoc}
	 */
	protected function configure()
	{
		$options = [
			new InputOption(
				self::INPUT_KEY_PRODUCT_IDS,
				null,
				InputOption::VALUE_REQUIRED,
				'Product ID'
			),
		];

		$this->setName('althea:catalog:resize-product-images')
		     ->setDescription('Creates resized product images by product ID(s)')
		     ->setDefinition($options);
	}

	/**
	 * {@inheritdoc}
	 */
	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$this->appState->setAreaCode('catalog');

		$productIds = [];

		// althea:
		// - generate resized product images by product ids
		if ($input->getOption(self::INPUT_KEY_PRODUCT_IDS)) {
			$productIds = explode(",", $input->getOption(self::INPUT_KEY_PRODUCT_IDS));
			$productIds = array_filter(array_map('trim', $productIds), 'strlen');
		}

		if (!count($productIds)) {
			$output->writeln("<info>No product images to resize</info>");

			// we must have an exit code higher than zero to indicate something was wrong
			return \Magento\Framework\Console\Cli::RETURN_SUCCESS;
		}

		try {
			foreach ($productIds as $productId) {
				try {
					/** @var Product $product */
					$product = $this->productRepository->getById($productId);
				} catch (NoSuchEntityException $e) {
					continue;
				}

				/** @var ImageCache $imageCache */
				$imageCache = $this->imageCacheFactory->create();
				$imageCache->generate($product);

				$output->writeln(sprintf("Resized images generated for product ID %s", $productId));
			}
		} catch (\Exception $e) {
			$output->writeln("<error>{$e->getMessage()}</error>");

			// we must have an exit code higher than zero to indicate something was wrong
			return \Magento\Framework\Console\Cli::RETURN_FAILURE;
		}

		$output->write("\n");
		$output->writeln("<info>Product images resized successfully</info>");
	}

}