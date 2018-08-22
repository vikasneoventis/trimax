<?php
/**
 * Copyright © 2016 Aitoc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Aitoc\CheckoutFieldsManager\Api\Data;

interface OrderCustomerDataSearchResultInterface extends \Magento\Framework\Api\SearchResultsInterface
{
    /**
     * Gets collection items.
     *
     * @return \Aitoc\CheckoutFieldsManager\Api\Data\OrderCustomerDataInterface[] Array of collection items.
     */
    public function getItems();

    /**
     * Set collection items.
     *
     * @param \Aitoc\CheckoutFieldsManager\Api\Data\OrderCustomerDataInterface[] $items
     *
     * @return $this
     */
    public function setItems(array $items);
}
