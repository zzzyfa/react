<?php
/**
 * Created by PhpStorm.
 * User: alvin
 * Date: 06/03/2018
 * Time: 12:13 PM
 */

namespace Althea\SocialLogin\Setup;

use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\UpgradeSchemaInterface;

class UpgradeSchema implements UpgradeSchemaInterface {

	/**
	 * @inheritDoc
	 */
	public function upgrade(SchemaSetupInterface $setup, ModuleContextInterface $context)
	{
		if (version_compare($context->getVersion(), '1.0.1', '<')) {

			$this->_dropUniqueIndex($setup);
		}
	}

	protected function _dropUniqueIndex(SchemaSetupInterface $setup)
	{
		$setup->getConnection()
		      ->dropIndex(
			      $setup->getTable('social_login_provider'),
			      $setup->getIdxName('social_login_provider', ['provider_code', 'provider_id'])
		      );

		$setup->getConnection()
		      ->addIndex(
			      $setup->getTable('social_login_provider'),
			      $setup->getIdxName('social_login_provider', ['provider_code', 'provider_id']),
			      ['provider_code', 'provider_id']
		      );
	}

}