<?php
/**
 * Copyright © 2017 Magento. All rights reserved.
 */
namespace Aitoc\MultiLocationInventory\Ui\Component\Listing\Columns\Supplier;

use Magento\Framework\Data\OptionSourceInterface;
use Aitoc\MultiLocationInventory\Model\ResourceModel\Supplier\CollectionFactory as SupplierCollectionFactory;

/**
 * Supplier Options for Parlevel Grid
 */
class Options implements OptionSourceInterface
{
    /**
     * All Suppliers value
     */
    const ALL_SUPPLIERS = '0';

    /**
     *
     * @var SupplierCollectionFactory $supplierCollectionFactory
     */
    protected $supplierCollectionFactory;

    /**
     * @var array
     */
    protected $options;

    /**
     * Constructor
     *
     * @param SupplierCollectionFactory $supplierCollectionFactory
     */
    public function __construct(SupplierCollectionFactory $supplierCollectionFactory)
    {
        $this->supplierCollectionFactory = $supplierCollectionFactory;
    }

    /**
     * Get options
     *
     * @return array
     */
    public function toOptionArray()
    {
        if ($this->options !== null) {
            return $this->options;
        }

        $this->options['All Suppliers']['label'] = __('All Suppliers');
        $this->options['All Suppliers']['value'] = self::ALL_SUPPLIERS;

        $supplierCollection = $this->supplierCollectionFactory->create();

        foreach ($supplierCollection->getItems() as $supplierItem) {
            $this->options[$supplierItem->getId()]['label'] = __($supplierItem->getTitle());
            $this->options[$supplierItem->getId()]['value'] = $supplierItem->getId();
        }


        return $this->options;
    }
}
