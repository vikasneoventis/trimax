<?php
/**
 * Copyright © 2016 Aitoc. All rights reserved.
 */

namespace Aitoc\CheckoutFieldsManager\Model\ResourceModel\QuoteCustomerData;

/**
 * @SuppressWarnings(PHPMD.CamelCasePropertyName)
 */
class Collection extends \Magento\Sales\Model\ResourceModel\Collection\AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = 'value_id';

    /**
     * Resource initialization
     *
     * @SuppressWarnings(PHPMD.CamelCaseMethodName)
     */
    protected function _construct()
    {
        $this->_init(
            'Aitoc\CheckoutFieldsManager\Model\QuoteCustomerData',
            'Aitoc\CheckoutFieldsManager\Model\ResourceModel\QuoteCustomerData'
        );
    }

    /**
     * add filters by quoteId and checkout step (address type)
     * and join attribute_code
     * Used for adding customAttributes to quote address
     *
     * @param int    $quoteId
     * @param string $type : shipping|billing
     *
     * @return $this
     */
    public function prepareForCustomAttributesLoad($quoteId, $type)
    {
        $this
            ->join(
                ['add' => $this->getTable('aitoc_checkout_eav_attribute')],
                'add.attribute_id = main_table.attribute_id',
                ''
            )
            ->join(
                ['eav' => $this->getTable('eav_attribute')],
                'eav.attribute_id = main_table.attribute_id',
                'attribute_code'
            )
            ->addFieldToFilter('quote_id', $quoteId)
            ->addFieldToFilter('add.checkout_step', $type);

        return $this;
    }
}
