<?php
/**
 * Created by PhpStorm.
 * User: alvin
 * Date: 11/08/2017
 * Time: 4:00 PM
 */

namespace Althea\Cms\Api\Data;

interface AlertInterface {

	/**#@+
	 * Constants for keys of data array. Identical to the name of the getter in snake case
	 */
	const ALERT_ID   = 'alert_id';
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
	 * @return AlertInterface
	 */
	public function setId($id);

	/**
	 * Set identifier
	 *
	 * @param string $identifier
	 * @return AlertInterface
	 */
	public function setIdentifier($identifier);

	/**
	 * Set title
	 *
	 * @param string $title
	 * @return AlertInterface
	 */
	public function setTitle($title);

	/**
	 * Set content
	 *
	 * @param string $content
	 * @return AlertInterface
	 */
	public function setContent($content);

	/**
	 * Set creation time
	 *
	 * @param string $createdAt
	 * @return AlertInterface
	 */
	public function setCreatedAt($createdAt);

	/**
	 * Set update time
	 *
	 * @param string $updatedAt
	 * @return AlertInterface
	 */
	public function setUpdatedAt($updatedAt);

	/**
	 * Set is active
	 *
	 * @param bool|int $isActive
	 * @return AlertInterface
	 */
	public function setIsActive($isActive);

}