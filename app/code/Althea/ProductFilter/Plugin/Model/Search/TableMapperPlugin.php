<?php
/**
 * Created by PhpStorm.
 * User: alvin
 * Date: 09/03/2018
 * Time: 9:56 AM
 */

namespace Althea\ProductFilter\Plugin\Model\Search;

class TableMapperPlugin {

	protected $_storeManager;
	protected $_catalogSession;

	/**
	 * TableMapperPlugin constructor.
	 *
	 * @param \Magento\Store\Model\StoreManager $storeManager
	 * @param \Magento\Catalog\Model\Session    $session
	 */
	public function __construct(\Magento\Store\Model\StoreManager $storeManager, \Magento\Catalog\Model\Session $session)
	{
		$this->_storeManager = $storeManager;
		$this->_catalogSession = $session;
	}

	public function aroundAddTables(\Magento\CatalogSearch\Model\Search\TableMapper $subject, \Closure $proceed, \Magento\Framework\DB\Select $select, \Magento\Framework\Search\RequestInterface $request)
	{
		$result = $proceed($select, $request);

		if ($this->_catalogSession->getIsBestSellers()) {

			$result->joinLeft(
				['bestsellers' => 'sales_bestsellers_aggregated_yearly'],
				sprintf('search_index.entity_id = bestsellers.product_id AND bestsellers.store_id = %s', $this->_storeManager->getStore()->getId()),
				[]
			);
			$this->_catalogSession->unsIsBestSellers();
		}

		$abc = (string)$select;

		return $result;
	}

}