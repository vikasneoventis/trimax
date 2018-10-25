<?php
/**
 * Copyright © 2018 Aitoc. All rights reserved.
 */

namespace Aitoc\AdvancedPermissions\Ui\DataProvider\AddFilterHelper;

use Aitoc\AdvancedPermissions\Helper\Data as AdvancedPermissionHelper;
use Magento\Framework\Api\Filter;
use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\Search\SearchCriteria;

/**
 * Class BaseAddFilterHelper
 */
abstract class BaseAddFilterHelper
{
    const CUSTOMER_LISTING_DATA_SOURCE = 'customer_listing_data_source';

    /**
     * @return string
     */
    abstract public function getBindedFieldId();

    /**
     * @return string[]
     */
    abstract protected function getApplicableDatasourceNames();

    /**
     * @return array
     */
    abstract protected function getStoredAllowedFieldIds();

    /**
     * @return string
     */
    abstract protected function getFilterFieldName();

    /**
     * @var AdvancedPermissionHelper
     */
    protected $helper;

    /**
     * BaseAddFilterHelper constructor.
     * @param AdvancedPermissionHelper $advancedPermissionHelper
     */
    public function __construct(AdvancedPermissionHelper $advancedPermissionHelper)
    {
        $this->helper = $advancedPermissionHelper;
    }

    /**
     * @param SearchCriteria $searchCriteria
     * @param FilterBuilder $filterBuilder
     * @param string $dataProviderName
     * @return Filter|null
     */
    public function getFilterIfRequired(SearchCriteria $searchCriteria, FilterBuilder $filterBuilder, $dataProviderName)
    {
        if (!$this->isFilterRequired($dataProviderName)) {
            return null;
        }

        $filteredFields = $this->getFilteredFields($searchCriteria);

        if ($this->hasBindedIdField($filteredFields)) {
            return null;
        }

        $allowedFieldIds = $this->getAllowedFieldIds();

        return $this->createFieldFilter($filterBuilder, $allowedFieldIds);
    }

    /**
     * @param FilterBuilder $filterBuilder
     * @param $allowedStoreIds
     * @return Filter
     */
    private function createFieldFilter(FilterBuilder $filterBuilder, $allowedStoreIds)
    {
        return $filterBuilder->setField($this->getFilterFieldName())
            ->setValue($allowedStoreIds)
            ->setConditionType('in')
            ->create();
    }

    /**
     * @return array
     */
    public function getAllowedFieldIds()
    {
        $allowedStoreIds = $this->getStoredAllowedFieldIds();

        if (!$this->helper->isViewAll()) {
            $allowedStoreIds[] = 0;
        }

        return $allowedStoreIds;
    }

    /**
     * @param string $dataProviderName
     * @return bool
     */
    private function isFilterRequired($dataProviderName)
    {
        return $this->isBindedFieldRelatedDatasource($dataProviderName)
            && !$this->isCustomerListingWithShowAllEnabled($dataProviderName);
    }

    /**
     * @param $dataProviderName
     * @return bool
     */
    private function isBindedFieldRelatedDatasource($dataProviderName)
    {
        return in_array($dataProviderName, $this->getApplicableDatasourceNames());
    }

    /**
     * @param SearchCriteria $searchCriteria
     * @return string[]
     */
    private function getFilteredFields(SearchCriteria $searchCriteria)
    {
        $fields = [];

        foreach ($searchCriteria->getFilterGroups() as $key => $item) {
            foreach ($item->getFilters() as $filter) {
                $fields[] = $filter->getField();
            }
        }

        return $fields;
    }

    /**
     * @param string[] $fields
     * @return bool
     */
    private function hasBindedIdField($fields)
    {
        return in_array($this->getBindedFieldId(), $fields);
    }

    /**
     * @param $dataProviderName
     * @return bool
     */
    private function isCustomerListingWithShowAllEnabled($dataProviderName)
    {
        return ($dataProviderName == self::CUSTOMER_LISTING_DATA_SOURCE)
            && $this->helper->getRole()->getShowAllCustomers();
    }
}
