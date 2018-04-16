<?php
/**
 * Created by PhpStorm.
 * User: alvin
 * Date: 11/08/2017
 * Time: 4:04 PM
 */

namespace Althea\Cms\Api;

interface AlertRepositoryInterface {

	/**
	 * Save alert.
	 *
	 * @param \Althea\Cms\Api\Data\AlertInterface $alert
	 * @return \Althea\Cms\Api\Data\AlertInterface
	 * @throws \Magento\Framework\Exception\LocalizedException
	 */
	public function save(Data\AlertInterface $alert);

	/**
	 * Retrieve alert.
	 *
	 * @param string $alertId
	 * @return \Althea\Cms\Api\Data\AlertInterface
	 * @throws \Magento\Framework\Exception\LocalizedException
	 */
	public function getById($alertId);

	/**
	 * Retrieve store-specific alert.
	 *
	 * @param string $alertId
	 * @param int    $storeId
	 * @return \Althea\Cms\Api\Data\AlertInterface
	 * @throws \Magento\Framework\Exception\LocalizedException
	 */
	public function getStoreSpecificById($alertId, $storeId);

	/**
	 * Retrieve store-specific new sign up alert.
	 *
	 * @param int $storeId
	 * @return \Althea\Cms\Api\Data\AlertInterface
	 * @throws \Magento\Framework\Exception\LocalizedException
	 */
	public function getStoreSpecificNewSignUp($storeId);

	/**
	 * Retrieve alerts matching the specified criteria.
	 *
	 * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
	 * @return \Althea\Cms\Api\Data\AlertSearchResultsInterface
	 * @throws \Magento\Framework\Exception\LocalizedException
	 */
	public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria);

	/**
	 * Delete alert.
	 *
	 * @param \Althea\Cms\Api\Data\AlertInterface $alert
	 * @return bool true on success
	 * @throws \Magento\Framework\Exception\LocalizedException
	 */
	public function delete(Data\AlertInterface $alert);

	/**
	 * Delete alert by ID.
	 *
	 * @param int $alertId
	 * @return bool true on success
	 * @throws \Magento\Framework\Exception\NoSuchEntityException
	 * @throws \Magento\Framework\Exception\LocalizedException
	 */
	public function deleteById($alertId);

}