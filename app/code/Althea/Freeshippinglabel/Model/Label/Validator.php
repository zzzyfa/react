<?php
/**
 * Created by PhpStorm.
 * User: alvin
 * Date: 03/01/2018
 * Time: 12:58 PM
 */

namespace Althea\Freeshippinglabel\Model\Label;

use Aheadworks\Freeshippinglabel\Model\Source\ContentType;
use Althea\Freeshippinglabel\Api\Data\LabelInterface;

class Validator extends \Aheadworks\Freeshippinglabel\Model\Label\Validator {

	/**
	 * @inheritDoc
	 */
	public function isValid($label)
	{
		$this->_clearMessages();

		if (!$this->_isContentDataValid($label)) {

			return false;
		}

		return empty($this->getMessages());
	}

	/**
	 * althea: remove validator for all store views
	 *
	 * @param LabelInterface $label
	 *
	 * @return bool
	 */
	protected function _isContentDataValid(LabelInterface $label)
	{
		$uniqueContentData = [
			ContentType::EMPTY_CART     => [],
			ContentType::NOT_EMPTY_CART => [],
			ContentType::GOAL_REACHED   => [],
		];

		if ($label->getContent()) {

			foreach ($label->getContent() as $contentItem) {

				$contentType = $contentItem->getContentType();

				if (!in_array($contentItem->getStoreId(), $uniqueContentData[$contentType])) {

					$uniqueContentData[$contentType][] = $contentItem->getStoreId();
				} else {

					$this->_addMessages(['Duplicated store view in label content found.']);

					return false;
				}

				if (!\Zend_Validate::is($contentItem->getMessage(), 'NotEmpty')) {

					$this->_addMessages(['Content message can not be empty.']);

					return false;
				}
			}
		}

		return true;
	}

}