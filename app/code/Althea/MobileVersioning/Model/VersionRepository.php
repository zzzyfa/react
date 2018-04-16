<?php
/**
 * Created by PhpStorm.
 * User: manadirmahi
 * Date: 29/09/2017
 * Time: 6:29 PM
 */

namespace Althea\MobileVersioning\Model;

use Althea\MobileVersioning\Api\VersionRepositoryInterface;
use Althea\MobileVersioning\Helper\Config;

class VersionRepository implements VersionRepositoryInterface {

	const ANDROID = 'android';
	const IOS     = 'ios';

	/**
	 * @var VersionFactory
	 */
	protected $versionFactory;

	/**
	 * @var Config
	 */
	protected $config;

	/**
	 * @var array
	 */
	protected $_platforms;

	/**
	 * VersionRepository constructor.
	 *
	 * @param VersionFactory $versionFactory
	 * @param Config         $config
	 */
	public function __construct(VersionFactory $versionFactory, Config $config)
	{
		$this->versionFactory = $versionFactory;
		$this->config         = $config;
		$this->_platforms     = [self::ANDROID, self::IOS];
	}

	/**
	 * @param int    $storeId
	 * @param string $platform
	 * @return \Althea\MobileVersioning\Api\Data\VersionInterface
	 */
	public function getVersion($storeId, $platform)
	{
		$version = $this->versionFactory->create();

		switch (strtolower($platform)) {

			case self::ANDROID:
				$version->setPlatform(self::ANDROID);
				$version->setVersion($this->config->getAndVer($storeId));
				$version->setPriority($this->config->getAndPrio($storeId));
				$version->setMessage($this->config->getAndMsg($storeId));
				break;
			case self::IOS:
				$version->setPlatform(self::IOS);
				$version->setVersion($this->config->getIosVer($storeId));
				$version->setPriority($this->config->getIosPrio($storeId));
				$version->setMessage($this->config->getIosMsg($storeId));
				break;
		}

		return $version;
	}

	/**
	 * @inheritDoc
	 */
	public function getVersions($storeId)
	{
		$versions = [];

		foreach ($this->_platforms as $platform) {

			$versions[] = $this->getVersion($storeId, $platform);
		}

		return $versions;
	}

}