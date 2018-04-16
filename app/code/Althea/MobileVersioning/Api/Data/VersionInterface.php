<?php
/**
 * Created by PhpStorm.
 * User: manadirmahi
 * Date: 09/10/2017
 * Time: 5:10 PM
 */

namespace Althea\MobileVersioning\Api\Data;

interface VersionInterface {

	const PLATFORM = 'platform';
	const VERSION  = 'number';
	const PRIORITY = 'priority';
	const MESSAGE  = 'message';

	/**
	 * Get Platform
	 *
	 * @return string
	 *
	 */
	public function getPlatform();

	/**
	 * Get version number
	 *
	 * @return string
	 */
	public function getVersion();

	/**
	 * Get update priority number
	 *
	 * @return int
	 */
	public function getPriority();

	/**
	 * Get Update message
	 *
	 * @return string
	 */
	public function getMessage();

	/**
	 * @param string $platform
	 * @return VersionInterface
	 */
	public function setPlatform($platform);

	/**
	 * @param string $versionNumber
	 * @return VersionInterface
	 */
	public function setVersion($versionNumber);

	/**
	 * @param int $priority
	 * @return VersionInterface
	 */
	public function setPriority($priority);

	/**
	 * @param string $message
	 * @return VersionInterface
	 */
	public function setMessage($message);

}