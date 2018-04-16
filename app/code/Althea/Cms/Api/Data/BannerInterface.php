<?php
/**
 * Created by PhpStorm.
 * User: alvin
 * Date: 07/08/2017
 * Time: 3:28 PM
 */

namespace Althea\Cms\Api\Data;

interface BannerInterface {

	/**#@+
	 * Constants for keys of data array. Identical to the name of the getter in snake case
	 */
	const BANNER_ID  = 'banner_id';
	const IDENTIFIER = 'identifier';
	const TITLE      = 'title';
	const CONTENT    = 'content';
	const CREATED_AT = 'created_at';
	const UPDATED_AT = 'updated_at';
	const IS_ACTIVE  = 'is_active';
	/**#@-*/

	/**
	 * Get ID
	 *
	 * @return int|null
	 */
	public function getId();

	/**
	 * Get identifier
	 *
	 * @return string
	 */
	public function getIdentifier();

	/**
	 * Get title
	 *
	 * @return string|null
	 */
	public function getTitle();

	/**
	 * Get content
	 *
	 * @return string|null
	 */
	public function getContent();

	/**
	 * Get created at
	 *
	 * @return string|null
	 */
	public function getCreatedAt();

	/**
	 * Get updated at
	 *
	 * @return string|null
	 */
	public function getUpdatedAt();

	/**
	 * Is active
	 *
	 * @return bool|null
	 */
	public function isActive();

	/**
	 * Set ID
	 *
	 * @param int $id
	 * @return BannerInterface
	 */
	public function setId($id);

	/**
	 * Set identifier
	 *
	 * @param string $identifier
	 * @return BannerInterface
	 */
	public function setIdentifier($identifier);

	/**
	 * Set title
	 *
	 * @param string $title
	 * @return BannerInterface
	 */
	public function setTitle($title);

	/**
	 * Set content
	 *
	 * @param string $content
	 * @return BannerInterface
	 */
	public function setContent($content);

	/**
	 * Set creation time
	 *
	 * @param string $createdAt
	 * @return BannerInterface
	 */
	public function setCreatedAt($createdAt);

	/**
	 * Set update time
	 *
	 * @param string $updatedAt
	 * @return BannerInterface
	 */
	public function setUpdatedAt($updatedAt);

	/**
	 * Set is active
	 *
	 * @param bool|int $isActive
	 * @return BannerInterface
	 */
	public function setIsActive($isActive);

}