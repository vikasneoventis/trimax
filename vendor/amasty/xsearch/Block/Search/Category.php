<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Xsearch
 */


namespace Amasty\Xsearch\Block\Search;

class Category extends AbstractSearch
{
    const CATEGORY_BLOCK_TYPE = 'category';

    /**
     * @return string
     */
    public function getBlockType()
    {
        return self::CATEGORY_BLOCK_TYPE;
    }

    /**
     * @inheritdoc
     */
    protected function prepareCollection()
    {
        $collection = $this->getSearchCollection()
            ->addNameToResult()
            ->addAttributeToSelect('*')
            ->addUrlRewriteToResult()
            ->addIsActiveFilter()
            ->addSearchFilter($this->getQuery()->getQueryText())
            ->setPageSize($this->getLimit());
        $collection->load();
        return parent::prepareCollection();
    }

    /**
     * @param \Magento\Framework\Model\AbstractModel $item
     * @return bool
     */
    public function showDescription(\Magento\Framework\Model\AbstractModel $item)
    {
        return $this->stringUtils->strlen($item->getDescription()) > 0;
    }

    /**
     * @param \Magento\Framework\Model\AbstractModel $category
     * @return string
     */
    public function getDescription(\Magento\Framework\Model\AbstractModel $category)
    {
        $descLength = $this->getDescLength();
        $descStripped = $this->stripTags($category->getDescription(), null, true);
        $text = $this->stringUtils->strlen($descStripped) > $descLength ?
            $this->stringUtils->substr($descStripped, 0, $this->getDescLength()) . '...'
            : $descStripped;
        return $this->highlight($text);
    }
}
