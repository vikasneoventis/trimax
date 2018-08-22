<?php
/**
 * Copyright © 2016 Aitoc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Aitoc\CheckoutFieldsManager\Api;

interface OrderCustomFieldsInterface
{
    /**
     * Lists comments for a specified order.
     *
     * @param int $id The order ID.
     *
     * @return \Aitoc\CheckoutFieldsManager\Api\Data\OrderCustomerDataSearchResultInterface Custom fields results.
     */
    public function getList($id);
}
