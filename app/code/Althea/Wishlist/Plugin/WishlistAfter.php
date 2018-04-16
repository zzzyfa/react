<?php
/**
 * Created by PhpStorm.
 * User: alvin
 * Date: 03/10/2017
 * Time: 2:32 PM
 */

namespace Althea\Wishlist\Plugin;

use Ipragmatech\Ipwishlist\Api\WishlistManagementInterface;
use MSP\APIEnhancer\Api\TagInterface;

class WishlistAfter {

	protected $_tag;

	/**
	 * WishlistAfter constructor.
	 *
	 * @param $_tag
	 */
	public function __construct(TagInterface $_tag)
	{
		$this->_tag = $_tag;
	}

	public function afterGetWishlistForCustomer(WishlistManagementInterface $subject, array $result)
	{
		$tags = ['wishlist_item'];

		foreach ($result as $item) {

			$tags[] = sprintf('wishlist_item_%s', $item['wishlist_item_id']);
		}

		$this->_tag->addTags($tags);

		return $result;
	}

}