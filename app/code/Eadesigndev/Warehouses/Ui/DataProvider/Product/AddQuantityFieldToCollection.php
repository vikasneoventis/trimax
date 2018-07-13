<?php
/**
 * @author Evince Team
 * @copyright Copyright © 2018 Evince (http://evincemage.com/)
 */

namespace Eadesigndev\Warehouses\Ui\DataProvider\Product;

use Magento\Framework\Data\Collection;
use Magento\Ui\DataProvider\AddFieldToCollectionInterface;

class AddQuantityFieldToCollection extends \Magento\CatalogInventory\Ui\DataProvider\Product\AddQuantityFieldToCollection
{
    public function addField(Collection $collection, $field, $alias = null)
    {
        $collection->joinField(
            'qty',
            'warehouseinventory_stock_item',
            'qty',
            'product_id=entity_id',
            '{{table}}.stock_id=1',
            'left'
        );
    }
}