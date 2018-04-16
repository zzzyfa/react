<?php
/**
 * Created by PhpStorm.
 * User: manadirmahi
 * Date: 09/10/2017
 * Time: 5:09 PM
 */

namespace Althea\MobileVersioning\Model;

use Althea\MobileVersioning\Api\Data\VersionInterface;
use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Model\AbstractModel;

class Version extends AbstractModel implements VersionInterface, IdentityInterface {

	/**
	 * Versioning cache tag
	 */
	const CACHE_TAG = 'althea_mobileversioning_version';

	/**
	 * @var string
	 */
	protected $_cacheTag = 'althea_mobileversioning_version';

	/**
	 * @var string
	 */
	protected $_eventPrefix = 'althea_mobileversioning_version';

	/**
	 * Get identities
	 *
	 * @return array
	 */
	public function getIdentities()
	{
		return [self::CACHE_TAG];
	}

	/**
	 * Retrieve platform
	 *
	 * @return string
	 */
	public function getPlatform()
	{
		return $this->getData(self::PLATFORM);
	}

	/**
	 * Get Version Number
	 *
	 * @return string
	 */
	public function getVersion()
	{
		return $this->getData(self::VERSION);
	}

	/**
	 * Get priority number
	 *
	 * @return int
	 */
	public function getPriority()
	{
		return $this->getData(self::PRIORITY);
	}

	/**
	 * Get update message
	 *
	 * @return string
	 */
	public function getMessage()
	{
		return $this->getData(self::MESSAGE);
	}

	/**
	 * @param string $platform
	 *
	 * @return VersionInterface
	 */
	public function setPlatform($platform)
	{
		return $this->setData(self::PLATFORM, $platform);
	}

	/**
	 * @param string $versionnumber
	 *
	 * @return VersionInterface
	 */
	public function setVersion($versionnumber)
	{
		return $this->setData(self::VERSION, $versionnumber);
	}

	/**
	 * @param int $priority
	 *
	 * @return VersionInterface
	 */
	public function setPriority($priority)
	{
		return $this->setData(self::PRIORITY, $priority);
	}

	/**
	 * @param string $message
	 *
	 * @return VersionInterface
	 */
	public function setMessage($message)
	{
		return $this->setData(self::MESSAGE, $message);
	}

}