<?php
/**
 * Created by PhpStorm.
 * User: alvin
 * Date: 11/08/2017
 * Time: 4:06 PM
 */

namespace Althea\Cms\Model;

use Althea\Cms\Api\Data\AlertInterface;
use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Model\AbstractModel;

class Alert extends AbstractModel implements AlertInterface, IdentityInterface {

	/**
	 * Alert cache tag
	 */
	const CACHE_TAG = 'althea_cms_alert';

	/**#@+
	 * Alert's statuses
	 */
	const STATUS_ENABLED  = 1;
	const STATUS_DISABLED = 0;

	/**#@-*/
	/**
	 * @var string
	 */
	protected $_cacheTag = 'althea_cms_alert';

	/**
	 * Prefix of model events names
	 *
	 * @var string
	 */
	protected $_eventPrefix = 'althea_cms_alert';

	/**
	 * Parameter name in event
	 *
	 * In observe method you can use $observer->getEvent()->getObject() in this case
	 *
	 * @var string
	 */
	protected $_eventObject = 'alert';

	/**
	 * @return void
	 */
	protected function _construct()
	{
		$this->_init('Althea\Cms\Model\ResourceModel\Alert');
	}

	/**
	 * Get identities
	 *
	 * @return array
	 */
	public function getIdentities()
	{
		return [self::CACHE_TAG . '_' . $this->getId(), self::CACHE_TAG . '_' . $this->getIdentifier()];
	}

	/**
	 * Retrieve alert id
	 *
	 * @return int
	 */
	public function getId()
	{
		return $this->getData(self::ALERT_ID);
	}

	/**
	 * Retrieve alert identifier
	 *
	 * @return string
	 */
	public function getIdentifier()
	{
		return $this->getData(self::IDENTIFIER);
	}

	/**
	 * Get title
	 *
	 * @return string|null
	 */
	public function getTitle()
	{
		return $this->getData(self::TITLE);
	}

	/**
	 * Get content
	 *
	 * @return string|null
	 */
	public function getContent()
	{
		return $this->getData(self::CONTENT);
	}

	/**
	 * Get created at
	 *
	 * @return string|null
	 */
	public function getCreatedAt()
	{
		return $this->getData(self::CREATED_AT);
	}

	/**
	 * Get updated at
	 *
	 * @return string|null
	 */
	public function getUpdatedAt()
	{
		return $this->getData(self::UPDATED_AT);
	}

	/**
	 * Get is active
	 *
	 * @return string|null
	 */
	public function isActive()
	{
		return (bool)$this->getData(self::IS_ACTIVE);
	}

	/**
	 * Set ID
	 *
	 * @param int $id
	 * @return AlertInterface
	 */
	public function setId($id)
	{
		return $this->setData(self::ALERT_ID, $id);
	}

	/**
	 * Set identifier
	 *
	 * @param string $identifier
	 * @return AlertInterface
	 */
	public function setIdentifier($identifier)
	{
		return $this->setData(self::IDENTIFIER, $identifier);
	}

	/**
	 * Set title
	 *
	 * @param string $title
	 * @return AlertInterface
	 */
	public function setTitle($title)
	{
		return $this->setData(self::TITLE, $title);
	}

	/**
	 * Set content
	 *
	 * @param string $content
	 * @return AlertInterface
	 */
	public function setContent($content)
	{
		return $this->setData(self::CONTENT, $content);
	}

	/**
	 * Set created at
	 *
	 * @param string $createdAt
	 * @return AlertInterface
	 */
	public function setCreatedAt($createdAt)
	{
		return $this->setData(self::getCreatedAt(), $createdAt);
	}

	/**
	 * Set updated at
	 *
	 * @param string $updatedAt
	 * @return AlertInterface
	 */
	public function setUpdatedAt($updatedAt)
	{
		return $this->setData(self::UPDATED_AT, $updatedAt);
	}

	/**
	 * Set ID
	 *
	 * @param bool|int $isActive
	 * @return AlertInterface
	 */
	public function setIsActive($isActive)
	{
		return $this->setData(self::IS_ACTIVE, $isActive);
	}

	/**
	 * Receive alert store ids
	 *
	 * @return int[]
	 */
	public function getStores()
	{
		return $this->hasData('stores') ? $this->getData('stores') : $this->getData('store_id');
	}

	/**
	 * Prepare alert's statuses.
	 *
	 * @return array
	 */
	public function getAvailableStatuses()
	{
		return [
			self::STATUS_ENABLED  => __('Enabled'),
			self::STATUS_DISABLED => __('Disabled'),
		];
	}

	/**
	 * @inheritDoc
	 */
	public function beforeSave()
	{
		$content = $this->getContent();

		if (is_array($content)) {

			$this->setContent(json_encode($content));
		} else if (is_string($content)) {

			$formatted = json_decode($content, true);

			if (!$formatted || JSON_ERROR_NONE !== json_last_error()) {

				throw new LocalizedException(__(json_last_error_msg()));
			}
		}

		return parent::beforeSave();
	}

}