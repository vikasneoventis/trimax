<?php
/**
 * Copyright © 2016 Aitoc. All rights reserved.
 */
namespace Aitoc\PreOrders\Ui\DataProvider;

use Magento\Framework\Api\Search\SearchCriteriaInterface;

class Reporting extends \Magento\Framework\View\Element\UiComponent\DataProvider\Reporting
{
    /**
     * Reporting constructor.
     * @param \Magento\Framework\View\Element\UiComponent\DataProvider\CollectionFactory $collectionFactory
     * @param FilterPool $filterPool
     */
    public function __construct(
        \Magento\Framework\View\Element\UiComponent\DataProvider\CollectionFactory $collectionFactory,
        FilterPool $filterPool
    ) {
        $this->collectionFactory = $collectionFactory;
        $this->filterPool = $filterPool;
    }

    /**
     * @param SearchCriteriaInterface $searchCriteria
     * @return \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
     * @throws \Exception
     */
    public function search(SearchCriteriaInterface $searchCriteria)
    {
        $collection = $this->collectionFactory->getReport($searchCriteria->getRequestName());
        $tblSalesOrder = $collection->getMainTable();
        if ($tblSalesOrder == 'sales_order_grid') {
            $values = $collection->getConnection()->describeTable($tblSalesOrder);
            $keys = array_keys($values);
            if (isset($keys['status'])) {
                unset($keys['status']);
            }
            if (isset($keys['status_preorder'])) {
                unset($keys['status_preorder']);
            }
            $collection->removeAllFieldsFromSelect()->addFieldToSelect($keys)->addFieldToSelect(new \Zend_Db_Expr('IF(status<>status_preorder,status_preorder,status)'), 'status');
        }
        $collection->setPageSize($searchCriteria->getPageSize());
        $collection->setCurPage($searchCriteria->getCurrentPage());
        $this->filterPool->applyFilters($collection, $searchCriteria);
        foreach ($searchCriteria->getSortOrders() as $sortOrder) {
            if ($sortOrder->getField() == 'status') {
                $sortOrder->setField('status_preorder');
            }
            if ($sortOrder->getField()) {
                $collection->setOrder($sortOrder->getField(), $sortOrder->getDirection());
            }
        }
        return $collection;
    }
}
