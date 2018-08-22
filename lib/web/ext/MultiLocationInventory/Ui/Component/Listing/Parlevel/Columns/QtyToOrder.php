<?php
/**
 * Copyright © 2017 Aitoc. All rights reserved.
 */
namespace Aitoc\MultiLocationInventory\Ui\Component\Listing\Parlevel\Columns;

class QtyToOrder extends \Magento\Ui\Component\Listing\Columns\Column
{
    /**
     * Column name
     */
    const NAME = 'qty_to_order';

    /**
     * {@inheritdoc}
     * @deprecated
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            $fieldName = $this->getData('name');
            foreach ($dataSource['data']['items'] as & $item) {
                $qty = (int)$item['qty'];
                $safetyStock = (int)$item['safety_stock'];
                $qtyToOrder = $safetyStock - $qty;
                $item[$fieldName] = $qtyToOrder;
            }
        }

        return $dataSource;
    }
}
