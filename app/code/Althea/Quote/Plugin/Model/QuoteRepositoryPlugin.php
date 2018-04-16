<?php
/**
 * Created by PhpStorm.
 * User: alvin
 * Date: 20/02/2018
 * Time: 11:28 AM
 */

namespace Althea\Quote\Plugin\Model;

class QuoteRepositoryPlugin {

	protected $_extensionAttributeFactory;

	/**
	 * QuoteRepositoryPlugin constructor.
	 *
	 * @param \Magento\Quote\Api\Data\CartExtensionFactory $extensionAttributeFactory
	 */
	public function __construct(\Magento\Quote\Api\Data\CartExtensionFactory $extensionAttributeFactory)
	{
		$this->_extensionAttributeFactory = $extensionAttributeFactory;
	}

	public function aroundGetForCustomer(\Magento\Quote\Model\QuoteRepository $subject, \Closure $proceed, $customerId, array $sharedStoreIds = [])
	{
		/* @var \Magento\Quote\Api\Data\CartInterface $result */
		$result        = $proceed($customerId, $sharedStoreIds);
		$extAttributes = $result->getExtensionAttributes();

		if (!$extAttributes) {

			$extAttributes = $this->_extensionAttributeFactory->create();
		}

		$extAttributes->setHasError($result->getHasError() === true);
		$extAttributes->setErrorTexts($this->_getQuoteErrorTexts($result));
		$result->setExtensionAttributes($extAttributes);

		return $result;
	}

	protected function _getQuoteErrorTexts(\Magento\Quote\Model\Quote $quote)
	{
		$errors = [];

		/* @var \Magento\Framework\Message\AbstractMessage $val */
		foreach ($quote->getErrors() as $key => $val) {

			if (!array_key_exists($val->getIdentifier(), $errors)) {

				$errors[$val->getIdentifier()] = $val->getText();
			}
		}

		return $errors;
	}

}