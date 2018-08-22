<?php
/**
 * Copyright © 2016 Aitoc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Aitoc\CheckoutFieldsManager\Api;

interface OrderCustomerDataRepositoryInterface
{
    /**
     * Lists order status history comments that match specified search criteria.
     *
     * @param \Magento\Framework\Api\SearchCriteria $searchCriteria The search criteria.
     *
     * @return \Aitoc\CheckoutFieldsManager\Api\Data\OrderCustomerDataSearchResultInterface Order custom fields search results interface.
     */
    public function getList(\Magento\Framework\Api\SearchCriteria $searchCriteria);
}
