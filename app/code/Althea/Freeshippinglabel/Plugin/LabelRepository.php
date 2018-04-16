<?php
/**
 * Created by PhpStorm.
 * User: alvin
 * Date: 02/01/2018
 * Time: 12:21 PM
 */

namespace Althea\Freeshippinglabel\Plugin;

use Althea\Freeshippinglabel\Api\Data\LabelInterface;
use Magento\Framework\Exception\LocalizedException;

class LabelRepository {

	protected $_collectionFactory;
	protected $_labelFactory;
	protected $_entityManager;

	/**
	 * LabelRepository constructor.
	 *
	 * @param \Aheadworks\Freeshippinglabel\Model\ResourceModel\Label\CollectionFactory $collectionFactory
	 * @param \Althea\Freeshippinglabel\Model\LabelFactory                              $labelFactory
	 * @param \Magento\Framework\EntityManager\EntityManager                            $entityManager
	 */
	public function __construct(
		\Aheadworks\Freeshippinglabel\Model\ResourceModel\Label\CollectionFactory $collectionFactory,
		\Althea\Freeshippinglabel\Model\LabelFactory $labelFactory,
		\Magento\Framework\EntityManager\EntityManager $entityManager
	)
	{
		$this->_collectionFactory = $collectionFactory;
		$this->_labelFactory      = $labelFactory;
		$this->_entityManager     = $entityManager;
	}

	public function aroundSave(\Aheadworks\Freeshippinglabel\Model\LabelRepository $subject, \Closure $proceed, LabelInterface $label)
	{
		if ($label->hasData(LabelInterface::IDENTIFIER)
			&& !$this->_isUniqueIdentifier($label)
		) {

			throw new LocalizedException(__('Duplicated identifier.'));
		}

		/** @var \Althea\Freeshippinglabel\Model\Label $labelModel */
		$labelModel = $this->_labelFactory->create();

		if ($labelId = $label->getId()) {

			$this->_entityManager->load($labelModel, $labelId);
		}

		$labelModel->addData($label->getData(), LabelInterface::class);

		$this->_entityManager->save($labelModel, $this->_extractCustomData($label)); // exception caught in controller

		return $label;
	}

	protected function _isUniqueIdentifier(LabelInterface $label)
	{
		$collection = $this->_collectionFactory->create()
		                                       ->addFieldToFilter('identifier', ['eq' => $label->getIdentifier()]);

		if ($id = $label->getId()) {

			$collection->getSelect()
			           ->where('main_table.id <> ?', $id);
		}

		if ($collection->count() > 0) {

			return false;
		}

		return true;
	}

	protected function _extractCustomData(LabelInterface $label)
	{
		$customData = [];

		foreach ($label->getData() as $key => $val) {

			switch ($key) {

				case LabelInterface::MIN_ITEM_QTY:
					$customData[LabelInterface::MIN_ITEM_QTY] = $label->getMinItemQty();
					break;
				case LabelInterface::IDENTIFIER:
					$customData[LabelInterface::IDENTIFIER] = $label->getIdentifier();
					break;
			}
		}

		return $customData;
	}

}