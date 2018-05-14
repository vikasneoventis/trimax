<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Xsearch
 */


namespace Amasty\Xsearch\Block\Search;

class Page extends AbstractSearch
{
    const CATEGORY_BLOCK_PAGE = 'page';

    /**
     * @return string
     */
    public function getBlockType()
    {
        return self::CATEGORY_BLOCK_PAGE;
    }

    /**
     * @inheritdoc
     */
    protected function prepareCollection()
    {
        $collection = $this->getSearchCollection()
            ->addSearchFilter($this->getQuery()->getQueryText())
            ->addStoreFilter($this->_storeManager->getStore())
            ->addFieldToFilter('is_active', 1)
            ->setPageSize($this->getLimit());
        $collection->load();
        return parent::prepareCollection();
    }

    /**
     * @param \Magento\Framework\Model\AbstractModel $item
     * @return string
     */
    public function getName(\Magento\Framework\Model\AbstractModel $item)
    {
        return $this->generateName($item->getTitle());
    }

    /**
     * @param \Magento\Framework\Model\AbstractModel $page
     * @return bool
     */
    public function showDescription(\Magento\Framework\Model\AbstractModel $page)
    {
        return $this->stringUtils->strlen($page->getContent()) > 0;
    }

    /**
     * @param \Magento\Framework\Model\AbstractModel $page
     * @return string
     */
    public function getDescription(\Magento\Framework\Model\AbstractModel $page)
    {
        $descStripped = $this->stripTags($page->getContent(), null, true);

        $text =
            $this->stringUtils->strlen($descStripped) > $this->getDescLength() ?
            $this->stringUtils->substr($descStripped, 0, $this->getDescLength()) . '...'
            : $descStripped;

        return $this->highlight($text);
    }
}
