<?php
/**
 * Created by PhpStorm.
 * User: alvin
 * Date: 02/10/2017
 * Time: 6:29 PM
 */

namespace Althea\TopSearch\Model;

use Althea\TopSearch\Api\TermRepositoryInterface;
use Althea\TopSearch\Model\TermFactory;

class TermRepository implements TermRepositoryInterface {

	protected $_termFactory;

	/**
	 * TermRepository constructor.
	 *
	 * @param TermFactory $termFactory
	 */
	public function __construct(TermFactory $termFactory)
	{
		$this->_termFactory = $termFactory;
	}

	/**
	 * @inheritDoc
	 */
	public function getTermsByStore($storeId)
	{
		/* @var Term $term */
		$term = $this->_termFactory->create();

		return $term->loadByStoreId($storeId);
	}

}