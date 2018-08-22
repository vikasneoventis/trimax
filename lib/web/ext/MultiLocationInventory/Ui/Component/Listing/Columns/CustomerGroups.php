<?php
/**
 * Copyright © 2017 Aitoc. All rights reserved.
 */
namespace Aitoc\MultiLocationInventory\Ui\Component\Listing\Columns;

use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Customer\Model\ResourceModel\Group\CollectionFactory as CustomerGroupCollectionFactory;

class CustomerGroups extends \Magento\Ui\Component\Listing\Columns\Column
{
    /**
     * Column name
     */
    const NAME = 'customer_group_ids';

    protected $customerGroupCollectionFactory;

    /**
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param CustomerGroupCollectionFactory $customerGroupCollectionFactory
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        CustomerGroupCollectionFactory $customerGroupCollectionFactory,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $uiComponentFactory, $components, $data);
        $this->customerGroupCollectionFactory = $customerGroupCollectionFactory;
    }

    /**
     * {@inheritdoc}
     * @deprecated
     */
    public function prepareDataSource(array $dataSource)
    {
        $customerGroupNames = [];
        $customerGroupCollection = $this->customerGroupCollectionFactory->create();
        foreach ($customerGroupCollection->getItems() as $customerGroupItem) {
            $customerGroupNames[$customerGroupItem->getId()] = $customerGroupItem->getCustomerGroupCode();
        }
        if (isset($dataSource['data']['items'])) {
            $fieldName = $this->getData('name');
            foreach ($dataSource['data']['items'] as & $item) {
                $customerGroups = [];
                if (array_key_exists($fieldName, $item)) {
                    foreach ($item[$fieldName] as $groupId) {
                        if (!isset($customerGroupNames[$groupId])) {
                            continue;
                        }
                        $customerGroups[] = $customerGroupNames[$groupId];
                    }
                }
                $item[$fieldName] = implode('<br/>', $customerGroups);
            }
        }

        return $dataSource;
    }
}
