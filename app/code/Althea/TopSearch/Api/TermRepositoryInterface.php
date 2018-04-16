<?php
/**
 * Created by PhpStorm.
 * User: alvin
 * Date: 02/10/2017
 * Time: 6:24 PM
 */

namespace Althea\TopSearch\Api;

interface TermRepositoryInterface {

	/**
	 * Get top search terms by store
	 *
	 * @param string $storeId
	 * @return \Althea\TopSearch\Api\Data\TermInterface
	 */
	public function getTermsByStore($storeId);

}