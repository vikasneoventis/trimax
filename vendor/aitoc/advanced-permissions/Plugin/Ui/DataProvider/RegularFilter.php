<?php
/**
 * Copyright © 2018 Aitoc. All rights reserved.
 */

namespace Aitoc\AdvancedPermissions\Plugin\Ui\DataProvider;

class RegularFilter
{

    /**
     * @var \Aitoc\AdvancedPermissions\Helper\Data
     */
    private $helper;

    /**
     * Product constructor.
     *
     * @param \Aitoc\AdvancedPermissions\Helper\Data $helper
     */
    public function __construct(
        \Aitoc\AdvancedPermissions\Helper\Data $helper
    ) {
        $this->helper = $helper;
    }

    /**
     * Apply regular filters like collection filters
     *
     * @param AbstractDb $collection
     * @param Filter     $filter
     *
     * @return void
     */
    public function beforeApply(
        \Magento\Framework\View\Element\UiComponent\DataProvider\RegularFilter $object,
        $collection,
        $filter
    ) {
        $value      = $filter->getValue();
        $typeFilter = $filter->getConditionType();
        if ($filter->getField() == 'store_id') {
            $allowsIds = $this->helper->getAllowedStoreViewIds();
            if (!is_array($filter->getValue())) {
                if (in_array((int)$filter->getValue(), $allowsIds)) {
                    $value = $filter->getValue();
                } elseif (!$filter->getValue()) {
                    $allowsIds[] = 0;
                    $value       = $allowsIds;
                    $typeFilter  = "in";
                } else {
                    $value = "-1";
                }
            }
        }
        if ($filter->getField() == 'website_id') {
            $websites = $this->helper->getAllowedWebsiteIds();

            if (!is_array($filter->getValue())) {
                if (in_array((int)$filter->getValue(), $websites)) {
                    $value = $filter->getValue();
                } elseif (!$filter->getValue()) {
                    $websites[] = 0;
                    $value      = $websites;
                    $typeFilter = "in";
                } else {
                    $value = "-1";
                }
            }
        }
        $filter->setConditionType($typeFilter);
        $filter->setValue($value);
    }
}
