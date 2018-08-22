<?php
/**
 * Copyright © 2016 Aitoc. All rights reserved.
 */

namespace Aitoc\PreOrders\Model\ResourceModel\Preorder\Customer;

class Collection extends \Magento\Customer\Model\ResourceModel\Customer\Collection
{
    /**
     * join productalert pre-order data to customer collection
     *
     * @param int $productId
     * @param int $websiteId
     * @return $this
     */
    public function join($productId, $websiteId)
    {
        $this->getSelect()->join(
            ['alert' => $this->getTable('product_alert_preorder')],
            'alert.customer_id=e.entity_id',
            ['alert_preorder_id', 'add_date', 'send_date', 'send_count', 'status']
        );

        $this->getSelect()->where('alert.product_id=?', $productId);
        if ($websiteId) {
            $this->getSelect()->where('alert.website_id=?', $websiteId);
        }
        $this->_setIdFieldName('alert_preorder_id');
        $this->addAttributeToSelect('*');

        return $this;
    }
}
