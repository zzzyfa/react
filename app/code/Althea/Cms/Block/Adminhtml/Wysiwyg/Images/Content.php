<?php
/**
 * Created by PhpStorm.
 * User: alvin
 * Date: 08/08/2017
 * Time: 11:30 AM
 */

namespace Althea\Cms\Block\Adminhtml\Wysiwyg\Images;

class Content extends \Magento\Cms\Block\Adminhtml\Wysiwyg\Images\Content {

	/**
	 * New directory action target URL
	 *
	 * @return string
	 */
	public function getOnInsertUrl()
	{
		return $this->getUrl('althea_cms/*/onInsert');
	}

}