<?php
/**
 * Copyright © 2018 Aitoc. All rights reserved.
 */

namespace Aitoc\AdvancedPermissions\Ui\DataProvider\AddFilterHelper;

/**
 * Class StoreIdsAddFilterHelper
 */
class StoreIdsAddFilterHelper extends BaseAddFilterHelper
{
    const FIELD_STORE_ID = 'store_id';

    /**
     * @inheritdoc
     */
    protected function getApplicableDatasourceNames()
    {
        return [
            "sales_order_grid_data_source",
            "cms_page_listing_data_source",
            "cms_block_listing_data_source",
            self::CUSTOMER_LISTING_DATA_SOURCE
        ];
    }

    /**
     * @inheritdoc
     */
    public function getBindedFieldId()
    {
        return self::FIELD_STORE_ID;
    }

    /**
     * @inheritdoc
     */
    protected function getStoredAllowedFieldIds()
    {
        return $this->helper->getAllowedStoreViewIds();
    }

    /**
     * @return string
     */
    protected function getFilterFieldName()
    {
        return $this->getBindedFieldId();
    }
}
