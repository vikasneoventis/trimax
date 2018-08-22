<?php
/**
 * Copyright © 2016 Aitoc. All rights reserved.
 */

namespace Aitoc\PreOrders\Block\Email;

class Stock extends \Magento\ProductAlert\Block\Email\Stock
{
    /**
     * @var string
     */
    protected $_template = 'Aitoc_PreOrders::email/preorder.phtml';

    /**
     * Retrieve unsubscribe url for product
     *
     * @param int $productId
     * @return string
     */
    public function getProductUnsubscribeUrl($productId)
    {
        return '';
    }

    /**
     * Retrieve unsubscribe url for all products
     *
     * @return string
     */
    public function getUnsubscribeUrl()
    {
        return '';
    }
}
