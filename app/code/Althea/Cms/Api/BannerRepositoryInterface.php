<?php
/**
 * Created by PhpStorm.
 * User: alvin
 * Date: 07/08/2017
 * Time: 3:28 PM
 */

namespace Althea\Cms\Api;

interface BannerRepositoryInterface {

	/**
	 * Save banner.
	 *
	 * @param \Althea\Cms\Api\Data\BannerInterface $banner
	 * @return \Althea\Cms\Api\Data\BannerInterface
	 * @throws \Magento\Framework\Exception\LocalizedException
	 */
	public function save(Data\BannerInterface $banner);

	/**
	 * Retrieve banner.
	 *
	 * @param string $bannerId
	 * @return \Althea\Cms\Api\Data\BannerInterface
	 * @throws \Magento\Framework\Exception\LocalizedException
	 */
	public function getById($bannerId);

	/**
	 * Retrieve banner w/ formatted content.
	 *
	 * @param string $bannerId
	 * @return \Althea\Cms\Api\Data\BannerInterface
	 * @throws \Magento\Framework\Exception\LocalizedException
	 */
	public function getFormattedById($bannerId);

	/**
	 * Retrieve banners matching the specified criteria.
	 *
	 * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
	 * @return \Althea\Cms\Api\Data\BannerSearchResultsInterface
	 * @throws \Magento\Framework\Exception\LocalizedException
	 */
	public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria);

	/**
	 * Delete banner.
	 *
	 * @param \Althea\Cms\Api\Data\BannerInterface $banner
	 * @return bool true on success
	 * @throws \Magento\Framework\Exception\LocalizedException
	 */
	public function delete(Data\BannerInterface $banner);

	/**
	 * Delete banner by ID.
	 *
	 * @param int $bannerId
	 * @return bool true on success
	 * @throws \Magento\Framework\Exception\NoSuchEntityException
	 * @throws \Magento\Framework\Exception\LocalizedException
	 */
	public function deleteById($bannerId);

}