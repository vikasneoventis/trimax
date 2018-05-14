<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Xsearch
 */


namespace Amasty\Xsearch\Helper;

use Amasty\Xsearch\Block\Search\Category;
use Amasty\Xsearch\Block\Search\Page;
use Amasty\Xsearch\Block\Search\Popular;
use Amasty\Xsearch\Block\Search\Product;
use Amasty\Xsearch\Block\Search\Recent;
use Magento\Store\Model\ScopeInterface;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    const MODULE_NAME = 'amasty_xsearch/';
    const XML_PATH_TEMPLATE_CATEGORY_POSITION = 'category/position';
    const XML_PATH_TEMPLATE_PRODUCT_POSITION = 'product/position';
    const XML_PATH_TEMPLATE_PAGE_POSITION = 'page/position';
    const XML_PATH_TEMPLATE_POPULAR_SEARCHES_POSITION = 'popular_searches/position';
    const XML_PATH_TEMPLATE_RECENT_SEARCHES_POSITION = 'recent_searches/position';

    const XML_PATH_TEMPLATE_CATEGORY_ENABLED = 'category/enabled';
    const XML_PATH_TEMPLATE_PRODUCT_ENABLED = 'product/enabled';
    const XML_PATH_TEMPLATE_PAGE_ENABLED = 'page/enabled';
    const XML_PATH_TEMPLATE_POPULAR_SEARCHES_ENABLED = 'popular_searches/enabled';
    const XML_PATH_TEMPLATE_RECENT_SEARCHES_ENABLED = 'recent_searches/enabled';
    const XML_PATH_IS_SINGLE_PRODUCT_REDIRECT = 'product/redirect_single_product';

    /**
     * @var \Magento\Catalog\Model\Config
     */
    private $configAttribute;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\Attribute\Collection
     */
    private $collection;

    /**
     * @var \Magento\Search\Helper\Data
     */
    private $searchHelper;

    public function __construct(
        \Magento\Catalog\Model\Config $configAttribute,
        \Magento\Catalog\Model\ResourceModel\Product\Attribute\Collection $collection,
        \Magento\Search\Helper\Data $searchHelper,
        \Magento\Framework\App\Helper\Context $context
    ) {
        parent::__construct($context);
        $this->configAttribute = $configAttribute;
        $this->collection = $collection;
        $this->searchHelper = $searchHelper;
    }

    public function getModuleConfig($path)
    {
        return $this->scopeConfig->getValue(self::MODULE_NAME . $path, ScopeInterface::SCOPE_STORE);
    }

    public function highlight($text, $query)
    {
        if ($query) {
            preg_match_all('~\w+~', $query, $matches);

            if ($matches) {
                $re = '/(' . implode('|', $matches[0]) . ')/iu';
                $text = preg_replace($re, '<span class="amasty-xsearch-highlight">$0</span>', $text);
            }
        }

        return $text;
    }

    protected function _pushItem($position, $block, &$html)
    {
        $position = $this->getModuleConfig($position);
        while (isset($html[$position])) {
            $position++;
        }
        $currentHtml = $block->toHtml();

        $this->replaceVariables($currentHtml);
        $html[$position] = $currentHtml;
    }

    protected function replaceVariables(&$currentHtml)
    {
        $currentHtml = preg_replace('@\{{(.+?)\}}@', '', $currentHtml);
    }

    public function getBlocksHtml(\Magento\Framework\View\Layout $layout)
    {
        $result = [];
        $html = [];

        if ($this->getModuleConfig(self::XML_PATH_TEMPLATE_PRODUCT_ENABLED)) {
            /** @var Product $productsBlock */
            $productsBlock = $layout->createBlock(Product::class, 'amasty.xsearch.product');
            $productsBlock->prepareCollection();

            $this->_pushItem(
                self::XML_PATH_TEMPLATE_PRODUCT_POSITION,
                $productsBlock,
                $html
            );

            if ($this->isSingleProductRedirect()
                && $productsBlock->getLoadedProductCollection()->getSize() == 1
            ) {
                $result['redirect_url'] = $productsBlock->getLoadedProductCollection()->getFirstItem()->getProductUrl();
                $result['html'] = implode('', $html);

                return $result;
            }
        }

        if ($this->getModuleConfig(self::XML_PATH_TEMPLATE_CATEGORY_ENABLED)) {
            $this->_pushItem(
                self::XML_PATH_TEMPLATE_CATEGORY_POSITION,
                $layout->createBlock(Category::class, 'amasty.xsearch.category'),
                $html
            );
        }

        if ($this->getModuleConfig(self::XML_PATH_TEMPLATE_PAGE_ENABLED)) {
            $this->_pushItem(
                self::XML_PATH_TEMPLATE_PAGE_POSITION,
                $layout->createBlock(Page::class, 'amasty.xsearch.page'),
                $html
            );
        }

        if ($this->getModuleConfig(self::XML_PATH_TEMPLATE_POPULAR_SEARCHES_ENABLED)) {
            $this->_pushItem(
                self::XML_PATH_TEMPLATE_POPULAR_SEARCHES_POSITION,
                $layout->createBlock(Popular::class, 'amasty.xsearch.search.popular'),
                $html
            );
        }

        if ($this->getModuleConfig(self::XML_PATH_TEMPLATE_RECENT_SEARCHES_ENABLED)) {
            $this->_pushItem(
                self::XML_PATH_TEMPLATE_RECENT_SEARCHES_POSITION,
                $layout->createBlock(Recent::class, 'amasty.xsearch.search.recent'),
                $html
            );
        }

        ksort($html);
        $result['html'] = implode('', $html);

        return $result;
    }

    /**
     * @param string $requiredData
     * @return array
     */
    public function getProductAttributes($requiredData = '')
    {
        if ($requiredData == 'is_searchable') {
            $attributeNames = [];
            foreach ($this->collection->addIsSearchableFilter()->getItems() as $attribute) {
                $attributeNames[] = $attribute->getAttributeCode();
            }

            return $attributeNames;
        } else {
            return $this->collection->getItems();
        }
    }

    public function isSingleProductRedirect()
    {
        return $this->getModuleConfig(self::XML_PATH_IS_SINGLE_PRODUCT_REDIRECT);
    }

    /**
     * @param string $query
     * @return string
     */
    public function getResultUrl($query = null)
    {
        return $this->searchHelper->getResultUrl($query);
    }
}
