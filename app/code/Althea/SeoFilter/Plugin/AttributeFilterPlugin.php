<?php
/**
 * Created by PhpStorm.
 * User: alvin
 * Date: 21/03/2018
 * Time: 6:21 PM
 */

namespace Althea\SeoFilter\Plugin;

use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Framework\UrlInterface;
use Magento\Theme\Block\Html\Pager as HtmlPager;
use Mirasvit\SeoFilter\Api\Config\ConfigInterface as Config;
use Mirasvit\SeoFilter\Api\Service\FriendlyUrlServiceInterface;
use Mirasvit\SeoFilter\Helper\Url as UrlHelper;

class AttributeFilterPlugin extends \Mirasvit\SeoFilter\Plugin\AttributeFilterPlugin {

	protected $_moduleManager;

	/**
	 * @inheritDoc
	 */
	public function __construct(
		\Magento\Framework\Module\Manager $manager,
		HtmlPager $htmlPagerBlock,
		CategoryRepositoryInterface $categoryRepository,
		FriendlyUrlServiceInterface $friendlyUrlService,
		UrlHelper $urlHelper,
		UrlInterface $url,
		Config $config
	)
	{
		$this->_moduleManager = $manager;

		parent::__construct($htmlPagerBlock, $categoryRepository, $friendlyUrlService, $urlHelper, $url, $config);
	}

	/**
	 * althea:
	 * - avoid reverting url if elasticsuite is enabled
	 * - elasticsuite is used for multiselect filtering in category page
	 *
	 * @inheritDoc
	 */
	public function afterGetUrl(\Magento\Catalog\Model\Layer\Filter\Item $item, $result = null)
	{
		if ($this->_moduleManager->isEnabled('Althea_CatalogSearch')) {

			return $result;
		}

		return parent::afterGetUrl($item);
	}

}