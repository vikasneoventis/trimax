<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2018 Amasty (https://www.amasty.com)
 * @package Amasty_Xsearch
 */


namespace Amasty\Xsearch\Model\ResourceModel\Page\Fulltext;

use Zend_Db_Expr;

class Collection extends \Magento\Cms\Model\ResourceModel\Page\Collection
{
    /** @var string */
    private $queryText;

    protected $storeId;

    protected $weights = [
       'title' => 3,
       'content' => 2
    ];

    public function addSearchFilter($query)
    {
        $this->queryText = trim($this->queryText . ' ' . $query);

        return $this;
    }

    public function getStoreId()
    {
        if ($this->storeId === null) {
            $this->setStoreId($this->storeManager->getStore()->getId());
        }

        return $this->storeId;
    }

    public function setStoreId($storeId)
    {
        if ($storeId instanceof \Magento\Store\Model\Store) {
            $storeId = $storeId->getId();
        }
        $this->storeId = (int)$storeId;

        return $this;
    }

    protected function getFulltextIndexColumns($collection, $indexTable)
    {
        $indexes = $collection->getConnection()->getIndexList($indexTable);
        foreach ($indexes as $index) {
            if (strtoupper($index['INDEX_TYPE']) == 'FULLTEXT') {
                return $index['COLUMNS_LIST'];
            }
        }

        return [];
    }

    protected function _renderFiltersBefore()
    {
        $columns = $this->getFulltextIndexColumns($this, $this->getMainTable());

        $this->getSelect()
            ->where(
                'MATCH(' . implode(',', $columns) . ') AGAINST(?)',
                $this->queryText
            )->order(
                new Zend_Db_Expr(
                    $this->getConnection()->quoteInto(
                        'MATCH(' . implode(',', $columns) . ') AGAINST(?)',
                        $this->queryText
                    ) . ' desc'
                )
            );

        parent::_renderFiltersBefore();
    }
}
