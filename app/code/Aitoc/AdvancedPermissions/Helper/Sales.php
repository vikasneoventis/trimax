<?php
/**
 * Copyright © 2016 Aitoc. All rights reserved.
 */
namespace Aitoc\AdvancedPermissions\Helper;

use Magento\Framework\App\Helper\Context;
use Magento\Framework\App\ObjectManager;
use Magento\Sales\Model\ResourceModel\Order\Invoice as InvoiceResource;
use Magento\Sales\Model\ResourceModel\Order\Shipment as ShipmentResource;
use Magento\Sales\Model\ResourceModel\Order as OrderResource;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Magento\Framework\DB\Select;

/**
 *
 */
class Sales extends Data
{
    /**
     * alias for sales_shipment_item, sales_invoice_item tables
     *
     * @var string
     */
    protected $itemAlias = 'entity_item';

    /**
     * Is current model instace is Order, Invoice or Shipment
     *
     * @param AbstractResource $resourceModel
     *
     * @return bool
     */
    public function isSalesResource(AbstractResource $resourceModel)
    {
        return $resourceModel instanceof OrderResource
        || $resourceModel instanceof InvoiceResource
        || $resourceModel instanceof ShipmentResource;
    }


    /**
     * Add filter to Order, Invoice and Shipment collections,
     * sub-admin should see entity with allowed products only
     *
     * @param AbstractCollection $collection
     *
     * @return AbstractCollection
     */
    public function addAllowedProductFilter(AbstractCollection &$collection)
    {
        /**
         * With method getResource we can process collection like sales_invoice and sales_invoice_grid,
         * because he have same resource model but different collection model Same with shipment
         */
        if (!$this->isSalesResource($collection->getResource())
            || array_key_exists($this->itemAlias, $collection->getSelect()->getPart(Select::FROM))
        ) {
            return $collection;
        }
        /** @var \Magento\Framework\DB\Select $select */
        $select = $collection->getSelect();
        $where  = $select->getPart(Select::WHERE);
        
        $columns = $collection->getSelect()->getPart(Select::COLUMNS);
        if ($columns[0][0] == 'main_table') {
            foreach ($where as $part) {
                if (false === strpos($part, 'main_table')) {
                    return $collection;
                }
            }
        }
        
        if (!$this->isAdvancedPermissionEnabled()) {
            return $collection;
        } else {
            $select->setPart(Select::WHERE, $where);
            $allowedStoreIds = $this->getAllowedStoreIds();
            $allowedWebsites = $this->getAllowedWebsiteIds();
            if (count($allowedStoreIds)) {
                $select->where('main_table.store_id IN (?)', $allowedStoreIds);
            }
            if (count($allowedWebsites)) {
                $this->joinEntityItem($select, $collection)
                    ->joinLeft(
                        [
                            'pw' => ObjectManager::getInstance()->get('Magento\Catalog\Model\ResourceModel\Product')
                                ->getProductWebsiteTable()
                        ],
                        $this->itemAlias . '.product_id = pw.product_id',
                        null
                    );
                $select->where('pw.website_id IN (?)', $allowedWebsites);
            }

            if ($this->getScope() == \Aitoc\AdvancedPermissions\Helper\Data::SCOPE_STORE) {
                $allowedCategorys = $this->getCategoryIds();
                if (count($allowedCategorys)) {
                    $this->joinEntityItem($select, $collection)->joinLeft(
                        ['category' => $collection->getTable('catalog_category_product')],
                        'category.product_id = ' . $this->itemAlias . '.product_id',
                        null
                    );
                    $select->where('category.category_id IN (?)', $allowedCategorys);
                }
            }
        }
        $select->distinct(true);

        return $collection;
    }

    /**
     * Join item table
     *
     * @param Select             $select
     * @param AbstractCollection $collection
     *
     * @return Select
     */
    protected function joinEntityItem(Select $select, AbstractCollection $collection)
    {
        if (array_key_exists($this->itemAlias, $select->getPart(Select::FROM))
            || !$this->isSalesResource($collection->getResource())
        ) {
            return $select;
        }

        // OrderResource
        $table = $collection->getTable('sales_order_item');
        $field = ".order_id";
        if ($collection->getResource() instanceof InvoiceResource) {
            $table = $collection->getTable('sales_invoice_item');
            $field = ".parent_id";
        } elseif ($collection->getResource() instanceof ShipmentResource) {
            $table = $collection->getTable('sales_shipment_item');
            $field = ".parent_id";
        }

        return $select->joinLeft(
            [$this->itemAlias => $table],
            'main_table.entity_id = ' . $this->itemAlias . $field,
            null
        );
    }
}
