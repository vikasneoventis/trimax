<?php
/**
 * Copyright © 2016 Aitoc. All rights reserved.
 */
namespace Aitoc\AdvancedPermissions\Ui\DataProvider;

class DataProvider extends \Magento\Framework\View\Element\UiComponent\DataProvider\DataProvider
{
    /**
     * @var \Aitoc\AdvancedPermissions\Helper\Data
     */
    protected $helper;

    protected $names;

    /**
     * DataProvider constructor.
     *
     * @param string                                                             $name
     * @param string                                                             $primaryFieldName
     * @param string                                                             $requestFieldName
     * @param \Magento\Framework\View\Element\UiComponent\DataProvider\Reporting $reporting
     * @param \Magento\Framework\Api\Search\SearchCriteriaBuilder                $searchCriteriaBuilder
     * @param \Magento\Framework\App\RequestInterface                            $request
     * @param \Magento\Framework\Api\FilterBuilder                               $filterBuilder
     * @param \Aitoc\AdvancedPermissions\Helper\Data                             $helper
     * @param array                                                              $meta
     * @param array                                                              $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        \Magento\Framework\View\Element\UiComponent\DataProvider\Reporting $reporting,
        \Magento\Framework\Api\Search\SearchCriteriaBuilder $searchCriteriaBuilder,
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Framework\Api\FilterBuilder $filterBuilder,
        \Aitoc\AdvancedPermissions\Helper\Data $helper,
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
        $this->helper          = $helper;
        /**
         * Names sources for Grid with Stores
         */
        $this->names           = [
            "sales_order_grid_data_source",
            "cms_page_listing_data_source",
            "cms_block_listing_data_source",
            "customer_listing_data_source"
        ];
        
        /**
         * Names sources for Grid with Websites
         */
        $this->namesForWebsite = [];
    }

    /**
     * Returns search criteria
     *
     * @return \Magento\Framework\Api\Search\SearchCriteria
     */
    public function getSearchCriteria()
    {
        if (!$this->searchCriteria) {
            $this->searchCriteria = $this->searchCriteriaBuilder->create();
            $this->searchCriteria->setRequestName($this->name);
            if ($this->helper->isAdvancedPermissionEnabled()) {
                if (in_array($this->name, $this->names)) {
                    // do nothing ae2617#1
                    if ($this->name != 'customer_listing_data_source' ||
                        !$this->helper->getRole()->getShowAllCustomers()
                    ) {
                        $fields = [];
                        foreach ($this->searchCriteria->getFilterGroups() as $key => $item) {
                            foreach ($item->getFilters() as $filter) {
                                $fields[] = $filter->getField();
                            }
                        }
                        if (!in_array("store_id", $fields)) {
                            $allowsIds = $this->helper->getAllowedStoreViewIds();
                            if (!$this->helper->isViewAll()) {
                                $allowsIds[] = 0;
                            }
                            $this->addFilter(
                                $this->filterBuilder->setField("store_id")
                                    ->setValue($allowsIds)
                                    ->setConditionType('in')
                                    ->create()
                            );
                            $this->searchCriteria = $this->searchCriteriaBuilder->create();
                            $this->searchCriteria->setRequestName($this->name);
                        }
                    }
                }

                if (in_array($this->name, $this->namesForWebsite)) {
                    $fields = [];
                    foreach ($this->searchCriteria->getFilterGroups() as $key => $item) {
                        foreach ($item->getFilters() as $filter) {
                            $fields[] = $filter->getField();
                        }
                    }

                    // If role doesn't have permissions to view all customers
                    $r = $this->helper->getRole()->getShowAllCustomers();
                    if (!$r) {
                        if (!in_array("website_id", $fields)) {
                            $websites = $this->helper->getAllowedWebsiteIds();
                            if (!$this->helper->isViewAll()) {
                                $websites[] = 0;
                            }
                            $this->addFilter(
                                $this->filterBuilder->setField("main_table.website_id")
                                    ->setValue($websites)
                                    ->setConditionType('in')
                                    ->create()
                            );
                            $this->searchCriteria = $this->searchCriteriaBuilder->create();
                            $this->searchCriteria->setRequestName($this->name);
                        }
                    }
                }
            }
        }
        return $this->searchCriteria;
    }
}
