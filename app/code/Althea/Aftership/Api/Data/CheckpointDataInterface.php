<?php
/**
 * Created by PhpStorm.
 * User: alvin
 * Date: 06/12/2017
 * Time: 6:15 PM
 */

namespace Althea\Aftership\Api\Data;

interface CheckpointDataInterface {

	const SLUG            = 'slug';
	const LOCATION        = 'location';
	const MESSAGE         = 'message';
	const TAG             = 'tag';
	const CHECKPOINT_TIME = 'checkpoint_time';

	/**
	 * @return string|null
	 */
	public function getSlug();

	/**
	 * @return string|null
	 */
	public function getLocation();

	/**
	 * @return string|null
	 */
	public function getMessage();

	/**
	 * @return string|null
	 */
	public function getTag();

	/**
	 * @return string|null
	 */
	public function getCheckpointTime();

	/**
	 * @param string $slug
	 *
	 * @return CheckpointDataInterface
	 */
	public function setSlug($slug);

	/**
	 * @param string $location
	 *
	 * @return CheckpointDataInterface
	 */
	public function setLocation($location);

	/**
	 * @param string $message
	 *
	 * @return CheckpointDataInterface
	 */
	public function setMessage($message);

	/**
	 * @param string $tag
	 *
	 * @return CheckpointDataInterface
	 */
	public function setTag($tag);

	/**
	 * @param string $time
	 *
	 * @return CheckpointDataInterface
	 */
	public function setCheckpointTime($time);

}