<?php
/**
 * Created by PhpStorm.
 * User: alvin
 * Date: 06/12/2017
 * Time: 6:49 PM
 */

namespace Althea\Aftership\Model;

use Althea\Aftership\Api\Data\CheckpointDataInterface;
use Magento\Framework\Model\AbstractModel;

class CheckpointData extends AbstractModel implements CheckpointDataInterface {

	/**
	 * @inheritDoc
	 */
	public function getSlug()
	{
		return $this->getData(self::SLUG);
	}

	/**
	 * @inheritDoc
	 */
	public function getLocation()
	{
		return $this->getData(self::LOCATION);
	}

	/**
	 * @inheritDoc
	 */
	public function getMessage()
	{
		return $this->getData(self::MESSAGE);
	}

	/**
	 * @inheritDoc
	 */
	public function getTag()
	{
		return $this->getData(self::TAG);
	}

	/**
	 * @inheritDoc
	 */
	public function getCheckpointTime()
	{
		return $this->getData(self::CHECKPOINT_TIME);
	}

	/**
	 * @inheritDoc
	 */
	public function setSlug($slug)
	{
		return $this->setData(self::SLUG, $slug);
	}

	/**
	 * @inheritDoc
	 */
	public function setLocation($location)
	{
		return $this->setData(self::LOCATION, $location);
	}

	/**
	 * @inheritDoc
	 */
	public function setMessage($message)
	{
		return $this->setData(self::MESSAGE, $message);
	}

	/**
	 * @inheritDoc
	 */
	public function setTag($tag)
	{
		return $this->setData(self::TAG, $tag);
	}

	/**
	 * @inheritDoc
	 */
	public function setCheckpointTime($time)
	{
		return $this->setData(self::CHECKPOINT_TIME, $time);
	}

}