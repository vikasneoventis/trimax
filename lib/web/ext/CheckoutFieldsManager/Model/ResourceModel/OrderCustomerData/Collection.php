<?php
/**
 * Copyright © 2016 Aitoc. All rights reserved.
 */

namespace Aitoc\CheckoutFieldsManager\Model\ResourceModel\OrderCustomerData;

/**
 * Customers collection.
 *
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
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            'Aitoc\CheckoutFieldsManager\Model\OrderCustomerData',
            'Aitoc\CheckoutFieldsManager\Model\ResourceModel\OrderCustomerData'
        );
    }

    /**
     * Update Customer Order Checkout Data by order id
     *
     * @param int   $orderId    : Integer
     * @param array $insertData : Data for update
     *
     * @return boolean : Result of removing old data and inserting new data
     */
    public function updateCustomerDataCheckoutByOrderId($orderId, $insertData)
    {
        $resultInsert = $resultDelete = false;
        if (is_int($orderId) && is_array($insertData)) {
            $table        = $this->getMainTable();
            $where        = ['order_id = ?' => $orderId];
            $resultDelete = $this->getConnection()->delete($table, $where);
            $resultInsert = $this->getConnection()->insertMultiple($table, $insertData);
        }

        return $resultInsert && $resultDelete;
    }

    /**
     *  Get Aitoc Checkout Additional Fields for order id
     *
     * @param int|null   $orderId
     * @param bool|false $allAttributes
     *
     * @return array
     */
    public function getAitocCheckoutfieldsByOrderId($orderId = null, $allAttributes = false)
    {
        $connection = $this->_resource->getConnection();
        $select     = $this->getSelectByOrderId($orderId, $allAttributes);

        $checkoutFieldsData = $connection->fetchAll($select);

        $optionIds = array_column($checkoutFieldsData, 'increment_id');
        if ($optionIds) {
            $selectOptions = $connection->select()
                ->from(
                    ['main_table' => 'eav_attribute_option'],
                    [
                        'option_id'    => 'main_table.option_id',
                        'attribute_id' => 'main_table.attribute_id',
                        'value'        => 'eaov.value',
                    ]
                )->joinLeft(
                    ['eaov' => 'eav_attribute_option_value'],
                    'eaov.option_id = main_table.option_id',
                    []
                )->where('attribute_id IN (' . join(',', $optionIds) . ')')
                ->group('eaov.option_id');
            $options = $connection->fetchAll($selectOptions);

            $optionsByAttributeId = [];
            foreach ($options as $option) {
                if (isset($option['attribute_id'])
                    && is_scalar($option['attribute_id'])
                ) {
                    if (!isset($optionsByAttributeId[$option['attribute_id']])) {
                        $optionsByAttributeId[$option['attribute_id']] = [];
                    }
                    $optionsByAttributeId[$option['attribute_id']][$option['option_id']] = $option['value'];
                }
            }
            if ($optionsByAttributeId && is_array($checkoutFieldsData)) {
                foreach ($checkoutFieldsData as $k => $index) {
                    if (isset($optionsByAttributeId[$checkoutFieldsData[$k]['increment_id']])) {
                        foreach ($optionsByAttributeId[$checkoutFieldsData[$k]['increment_id']] as $key => $value) {
                            $checkoutFieldsData[$k]['options'][] =
                                [
                                    'label' => $optionsByAttributeId[(int)$checkoutFieldsData[$k]['increment_id']][$key],
                                    'value' => $key
                                ];
                        }
                    }
                }
            }
        }


        return $checkoutFieldsData;
    }

    /**
     *  Get Aitoc Checkout Additional Fields select for order
     *
     * @param int|null   $orderId
     * @param bool|false $allAttributes
     *
     * @return array
     */
    public function getSelectByOrderId($orderId = null, $allAttributes = false)
    {
        $storeIds   = [0];
        $connection = $this->_resource->getConnection();
        $condition  = '( aceas.attribute_id = av.attribute_id)';
        if ($orderId) {
            $condition  = '( aceas.attribute_id = av.attribute_id AND av.order_id = ' . $orderId . ')';
            $storeIds[] = \Magento\Framework\App\ObjectManager::getInstance()
                ->get('Magento\Sales\Model\Order')
                ->load($orderId)
                ->getStoreId();
        }

        $storeIdsString = join(',', $storeIds);

        $fromData = [
            'field_name'   => 'ea.frontend_label',
            'type'         => 'ea.frontend_input',
            'label'        => 'ea.frontend_label',
            'increment_id' => 'aceas.attribute_id',
            'option_id'    => 'eaov.option_id',
            'field_value'  => $connection->getIfNullSql('av.value', 'ea.default_value'),
            'value'        => 'av.value',
        ];

        $select = $connection->select()->from(
            ['aceas' => 'aitoc_checkout_eav_attribute_store'],
            $fromData
        );

        if ($allAttributes) {
            $select->joinLeft(['av' => 'aitoc_sales_order_value'], $condition, []);
        } else {
            $select->joinInner(['av' => 'aitoc_sales_order_value'], $condition, []);
        }

        $select->joinLeft(['ea' => 'eav_attribute'], 'aceas.attribute_id = ea.attribute_id', [])
            ->joinLeft(
                ['eaov' => 'eav_attribute_option_value'],
                '( eaov.option_id = av.value AND eaov.store_id IN (' . $storeIdsString . '))',
                []
            )->joinLeft(['acea' => 'aitoc_checkout_eav_attribute'], '( acea.attribute_id = aceas.attribute_id)', [])
            ->where('aceas.store_id IN (' . $storeIdsString . ')')
            ->where('ea.frontend_input <> ?', 'label')
            ->where('acea.display_area IS NOT NULL')
            ->group('ea.attribute_id');

        return $select;
    }
}
