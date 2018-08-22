<?php
/**
 * Copyright © 2016 Aitoc. All rights reserved.
 */

namespace Aitoc\CheckoutFieldsManager\Model;

use Magento\Eav\Model\Entity\Attribute\AbstractAttribute;
use Aitoc\CheckoutFieldsManager\Model\ResourceModel\Form\Attribute\CollectionFactory as FormCollectionFactory;

/**
 * Attribute Metadata data provider class
 */
class AttributeMetadataDataProvider
{
    /**
     * @var \Magento\Eav\Model\Config
     */
    private $eavConfig;

    /**
     * @var FormCollectionFactory
     */
    private $attrFormCollectionFactory;

    /**
     * @var \Magento\Store\Model\StoreManager
     */
    private $storeManager;

    /**
     * Initialize data provider with data source
     *
     * @param \Magento\Eav\Model\Config $eavConfig
     * @param FormCollectionFactory $attrFormCollectionFactory
     * @param \Magento\Store\Model\StoreManager $storeManager
     */
    public function __construct(
        \Magento\Eav\Model\Config $eavConfig,
        FormCollectionFactory $attrFormCollectionFactory,
        \Magento\Store\Model\StoreManager $storeManager
    ) {
        $this->eavConfig                 = $eavConfig;
        $this->attrFormCollectionFactory = $attrFormCollectionFactory;
        $this->storeManager              = $storeManager;
    }

    /**
     * Get attribute model for a given entity type and code
     *
     * @param string $entityType
     * @param string $attributeCode
     * @return false|AbstractAttribute
     */
    public function getAttribute($entityType, $attributeCode)
    {
        return $this->eavConfig->getAttribute($entityType, $attributeCode);
    }

    /**
     * Load collection with filters applied
     *
     * @param string $entityType
     * @param string $displayArea
     * @param string $checkoutStep
     * @return \Aitoc\CheckoutFieldsManager\Model\ResourceModel\Form\Attribute\Collection
     */
    public function loadAttributesCollection($entityType = null, $displayArea = null, $checkoutStep = null)
    {
        $attributesFormCollection = $this->attrFormCollectionFactory->create();
        $attributesFormCollection->setStore($this->storeManager->getStore())
            ->addFieldToFilter('ca.is_visible', 1)
            ->addFieldToFilter('ca.display_area', ['neq' => ''])
            ->setSortOrder();
        if ($entityType) {
            $attributesFormCollection->setEntityType($entityType);
        }
        if ($displayArea) {
            $attributesFormCollection->addDisplayAreaFilter($displayArea);
        }
        if ($checkoutStep) {
            $attributesFormCollection->addCheckoutStepFilter($checkoutStep);
        }

        return $attributesFormCollection;
    }
}
