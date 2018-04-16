<?php
/**
 * Created by PhpStorm.
 * User: alvin
 * Date: 14/12/2017
 * Time: 3:38 PM
 */

namespace Althea\Catalog\Api\Data;

interface CategoryTreeInterface extends \Magento\Catalog\Api\Data\CategoryTreeInterface {

	/**
	 * @return \Althea\Catalog\Api\Data\CategoryTreeInterface[]
	 */
	public function getChildrenData();

	/**
	 * @param \Althea\Catalog\Api\Data\CategoryTreeInterface[] $childrenData
	 * @return $this
	 */
	public function setChildrenData(array $childrenData = null);

	/**
	 * Retrieve existing extension attributes object or create a new one.
	 *
	 * @return \Magento\Catalog\Api\Data\CategoryExtensionInterface|null
	 */
	public function getExtensionAttributes();

	/**
	 * Set an extension attributes object.
	 *
	 * @param \Magento\Catalog\Api\Data\CategoryExtensionInterface $extensionAttributes
	 * @return $this
	 */
	public function setExtensionAttributes(\Magento\Catalog\Api\Data\CategoryExtensionInterface $extensionAttributes);

}