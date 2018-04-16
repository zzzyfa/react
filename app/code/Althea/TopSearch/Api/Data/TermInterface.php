<?php
/**
 * Created by PhpStorm.
 * User: alvin
 * Date: 02/10/2017
 * Time: 6:49 PM
 */

namespace Althea\TopSearch\Api\Data;

interface TermInterface {

	/**#@+
	 * Constants for keys of data array. Identical to the name of the getter in snake case
	 */
	const TERMS    = 'terms';
	const STORE_ID = 'store_id';
	/**#@-*/

	/**
	 * Get search terms
	 *
	 * @return array
	 */
	public function getTerms();

	/**
	 * Get store ID
	 *
	 * @return int
	 */
	public function getStoreId();

	/**
	 * Set search terms
	 *
	 * @param array $terms
	 * @return TermInterface
	 */
	public function setTerms($terms);

	/**
	 * Set store ID
	 *
	 * @param int $storeId
	 * @return TermInterface
	 */
	public function setStoreId($storeId);

}