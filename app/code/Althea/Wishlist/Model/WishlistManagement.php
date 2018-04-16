<?php
/**
 * Created by PhpStorm.
 * User: alvin
 * Date: 20/02/2018
 * Time: 4:36 PM
 */

namespace Althea\Wishlist\Model;

use Magento\Framework\Exception\InputException;

class WishlistManagement extends \Ipragmatech\Ipwishlist\Model\WishlistManagement {

	/**
	 * @inheritDoc
	 */
	public function getWishlistForCustomer($customerId)
	{
		if (empty($customerId) || !isset($customerId) || $customerId == "") {
			throw new InputException(__('Id required'));
		} else {

			// althea:
			// - add product status filter
			$collection = $this->_wishlistCollectionFactory->create()
			                                               ->addCustomerIdFilter($customerId)
			                                               ->addProductStatusFilter(\Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED);

			$wishlistData = [];
			foreach ($collection as $item) {
				$productInfo = $item->getProduct()->toArray();
				$data = [
					"wishlist_item_id" => $item->getWishlistItemId(),
					"wishlist_id" => $item->getWishlistId(),
					"product_id" => $item->getProductId(),
					"store_id" => $item->getStoreId(),
					"added_at" => $item->getAddedAt(),
					"description" => $item->getDescription(),
					"qty" => round($item->getQty()),
					"product" => $productInfo
				];
				$wishlistData[] = $data;
			}
			return $wishlistData;
		}
	}

}