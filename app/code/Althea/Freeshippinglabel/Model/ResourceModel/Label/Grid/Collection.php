<?php
/**
 * Created by PhpStorm.
 * User: alvin
 * Date: 28/12/2017
 * Time: 4:41 PM
 */

namespace Althea\Freeshippinglabel\Model\ResourceModel\Label\Grid;

use Magento\Framework\Data\Collection\Db\FetchStrategyInterface as FetchStrategy;
use Magento\Framework\Data\Collection\EntityFactoryInterface as EntityFactory;
use Magento\Framework\Event\ManagerInterface as EventManager;
use Psr\Log\LoggerInterface as Logger;

class Collection extends \Magento\Framework\View\Element\UiComponent\DataProvider\SearchResult {

	/**
	 * Collection constructor.
	 *
	 * @param EntityFactory $entityFactory
	 * @param Logger        $logger
	 * @param FetchStrategy $fetchStrategy
	 * @param EventManager  $eventManager
	 * @param string        $mainTable
	 * @param string        $resourceModel
	 *
	 * @throws \Magento\Framework\Exception\LocalizedException
	 */
	public function __construct(
		EntityFactory $entityFactory,
		Logger $logger,
		FetchStrategy $fetchStrategy,
		EventManager $eventManager,
		$mainTable = 'aw_fslabel_label',
		$resourceModel = '\Aheadworks\Freeshippinglabel\Model\ResourceModel\Label'
	)
	{
		parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $mainTable, $resourceModel);
	}

}