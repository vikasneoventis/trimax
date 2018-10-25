<?php
/**
 * Copyright © 2018 Aitoc. All rights reserved.
 */

namespace Aitoc\AdvancedPermissions\Ui\DataProvider;

use Aitoc\AdvancedPermissions\Helper\Data as AdvancedPermissionHelper;
use Aitoc\AdvancedPermissions\Ui\DataProvider\AddFilterHelper\BaseAddFilterHelper;
use Aitoc\AdvancedPermissions\Ui\DataProvider\AddFilterHelper\StoreIdsAddFilterHelper;
use Magento\Framework\Api\Filter;
use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\Search\FilterGroup;
use Magento\Framework\Api\Search\SearchCriteria;
use Magento\Framework\Api\Search\SearchCriteriaBuilder;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\View\Element\UiComponent\DataProvider\DataProvider as CoreDataProvider;
use Magento\Framework\View\Element\UiComponent\DataProvider\Reporting;

/**
 * Class DataProvider
 */
class DataProvider extends CoreDataProvider
{
    /**
     * @var AdvancedPermissionHelper
     */
    private $helper;

    /**
     * @var StoreIdsAddFilterHelper
     */
    private $storeIdsAddFilterHelper;

    /**
     * DataProvider constructor.
     *
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param Reporting $reporting
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param RequestInterface $request
     * @param FilterBuilder $filterBuilder
     * @param AdvancedPermissionHelper $helper
     * @param StoreIdsAddFilterHelper $storeIdsAddFilterHelper
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        Reporting $reporting,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        RequestInterface $request,
        FilterBuilder $filterBuilder,
        AdvancedPermissionHelper $helper,
        StoreIdsAddFilterHelper $storeIdsAddFilterHelper,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct(
            $name,
            $primaryFieldName,
            $requestFieldName,
            $reporting,
            $searchCriteriaBuilder,
            $request,
            $filterBuilder,
            $meta,
            $data
        );

        $this->helper = $helper;
        $this->storeIdsAddFilterHelper = $storeIdsAddFilterHelper;
    }

    /**
     * Returns search criteria
     *
     * @return SearchCriteria
     */
    public function getSearchCriteria()
    {
        if (!$this->searchCriteria) {
            $searchCriteria = parent::getSearchCriteria();

            if ($this->helper->isAdvancedPermissionEnabled()) {
                $this->addAdvancedPermissionFiltersIfRequired($searchCriteria);
            }

            $this->searchCriteria = $searchCriteria;
        }
        return $this->searchCriteria;
    }

    /**
     * @param SearchCriteria $searchCriteria
     */
    private function addAdvancedPermissionFiltersIfRequired(SearchCriteria $searchCriteria)
    {
        $this->addStoreFilterIfRequired($searchCriteria);
    }

    /**
     * @param SearchCriteria $searchCriteria
     */
    private function addStoreFilterIfRequired(SearchCriteria $searchCriteria)
    {
        $this->addFilterIfRequired($searchCriteria, $this->storeIdsAddFilterHelper);
    }

    /**
     * @param SearchCriteria $searchCriteria
     * @param BaseAddFilterHelper $filterHelper
     */
    private function addFilterIfRequired(SearchCriteria $searchCriteria, BaseAddFilterHelper $filterHelper)
    {
        $filter = $filterHelper->getFilterIfRequired(
            $searchCriteria,
            $this->filterBuilder,
            $this->name
        );

        if (!$filter) {
            return;
        }

        $this->addFilter($filter);
        $searchCriteriaBuilder = $this->searchCriteriaBuilder;

        $this->reinitCriteriaBuilderBySearchCriteria($searchCriteriaBuilder, $searchCriteria);
        $searchCriteria = $searchCriteriaBuilder->create();
        $searchCriteria->setRequestName($this->name);
        $this->searchCriteria = $searchCriteria;
    }

    /**
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param SearchCriteria $searchCriteria
     */
    private function reinitCriteriaBuilderBySearchCriteria(
        SearchCriteriaBuilder $searchCriteriaBuilder,
        SearchCriteria $searchCriteria
    ) {
        $pageSize = $searchCriteria->getPageSize();
        $currentPage = $searchCriteria->getCurrentPage();

        $searchCriteriaBuilder
            ->setCurrentPage($currentPage)
            ->setPageSize($pageSize);

        $sortOrders = $searchCriteria->getSortOrders();
        $this->addSortOrders($searchCriteriaBuilder, $sortOrders);

        $filterGroups = $searchCriteria->getFilterGroups();
        $this->addFiltersBySearchCriteriaFilterGroupsIfAllowed($filterGroups);
    }

    /**
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param SortOrder[]|null $searchCriteriaSortOrders
     */
    private function addSortOrders(SearchCriteriaBuilder $searchCriteriaBuilder, $searchCriteriaSortOrders)
    {
        foreach ($searchCriteriaSortOrders as $searchCriteriaSortOrder) {
            $this->addsearchCriteriaBuilderSortOrderBySearchCriteriaSortOrder(
                $searchCriteriaBuilder,
                $searchCriteriaSortOrder
            );
        }
    }

    /**
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param SortOrder $searchCriteriaSortOrder
     */
    private function addsearchCriteriaBuilderSortOrderBySearchCriteriaSortOrder(
        SearchCriteriaBuilder $searchCriteriaBuilder,
        SortOrder $searchCriteriaSortOrder
    ) {
        $field = $searchCriteriaSortOrder->getField();
        $direction = $searchCriteriaSortOrder->getDirection();

        $searchCriteriaBuilder->addSortOrder($field, $direction);
    }

    /**
     * @param FilterGroup[] $filterGroups
     */
    private function addFiltersBySearchCriteriaFilterGroupsIfAllowed($filterGroups)
    {
        $restrictionField = $this->storeIdsAddFilterHelper->getBindedFieldId();
        $allowedFieldValues = $this->storeIdsAddFilterHelper->getAllowedFieldIds();

        foreach ($filterGroups as $filterGroup) {
            $this->addFilterByFilterGroupIfAllowed($filterGroup, $restrictionField, $allowedFieldValues);
        }
    }

    /**
     * @param FilterGroup $filterGroup
     * @param string $restrictionFieldName
     * @param array $allowedFieldValues
     */
    private function addFilterByFilterGroupIfAllowed(
        FilterGroup $filterGroup,
        $restrictionFieldName,
        $allowedFieldValues
    ) {
        $filter = $this->filterGroupToFilter($filterGroup);

        if (!$filter) {
            return;
        }

        if (!$this->isAllowedFilter($filter, $restrictionFieldName, $allowedFieldValues)) {
            return;
        }

        $this->addFilter($filter);
    }

    /**
     * @param Filter $filter
     * @param string $restrictionField
     * @param array $allowedFieldValues
     * @return bool
     */
    private function isAllowedFilter(Filter $filter, $restrictionField, $allowedFieldValues)
    {
        if ($filter->getField() !== $restrictionField) {
            return true;
        }

        if (in_array($filter->getValue(), $allowedFieldValues)) {
            return true;
        }

        return false;
    }

    /**
     * @param FilterGroup $filterGroup
     * @return Filter
     */
    private function filterGroupToFilter(FilterGroup $filterGroup)
    {
        $filters = $filterGroup->getFilters();

        return $filters ? $filters[0] : null;
    }
}
