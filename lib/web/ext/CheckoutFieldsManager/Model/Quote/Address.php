<?php
/**
 * Copyright © 2017 Aitoc. All rights reserved.
 */

namespace Aitoc\CheckoutFieldsManager\Model\Quote;

use Magento\Customer\Api\AddressMetadataInterface;
use Magento\Customer\Api\Data\AddressInterfaceFactory;
use Magento\Customer\Api\Data\RegionInterfaceFactory;
use Magento\Quote\Api\Data\AddressInterface;
use Aitoc\CheckoutFieldsManager\Model\ResourceModel\Attribute\CollectionFactory;

class Address extends \Magento\Quote\Model\Quote\Address
{
    protected $collectionFactoryAttributes;
    
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Api\ExtensionAttributesFactory $extensionFactory,
        \Magento\Framework\Api\AttributeValueFactory $customAttributeFactory,
        \Magento\Directory\Helper\Data $directoryData,
        \Magento\Eav\Model\Config $eavConfig,
        \Magento\Customer\Model\Address\Config $addressConfig,
        \Magento\Directory\Model\RegionFactory $regionFactory,
        \Magento\Directory\Model\CountryFactory $countryFactory,
        AddressMetadataInterface $metadataService,
        AddressInterfaceFactory $addressDataFactory,
        RegionInterfaceFactory $regionDataFactory,
        \Magento\Framework\Api\DataObjectHelper $dataObjectHelper,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Quote\Model\Quote\Address\ItemFactory $addressItemFactory,
        \Magento\Quote\Model\ResourceModel\Quote\Address\Item\CollectionFactory $itemCollectionFactory,
        \Magento\Quote\Model\Quote\Address\RateFactory $addressRateFactory,
        \Magento\Quote\Model\Quote\Address\RateCollectorInterfaceFactory $rateCollector,
        \Magento\Quote\Model\ResourceModel\Quote\Address\Rate\CollectionFactory $rateCollectionFactory,
        \Magento\Quote\Model\Quote\Address\RateRequestFactory $rateRequestFactory,
        \Magento\Quote\Model\Quote\Address\Total\CollectorFactory $totalCollectorFactory,
        \Magento\Quote\Model\Quote\Address\TotalFactory $addressTotalFactory,
        \Magento\Framework\DataObject\Copy $objectCopyService,
        \Magento\Shipping\Model\CarrierFactoryInterface $carrierFactory,
        \Magento\Quote\Model\Quote\Address\Validator $validator,
        \Magento\Customer\Model\Address\Mapper $addressMapper,
        \Magento\Quote\Model\Quote\Address\CustomAttributeListInterface $attributeList,
        \Magento\Quote\Model\Quote\TotalsCollector $totalsCollector,
        \Magento\Quote\Model\Quote\TotalsReader $totalsReader,
        CollectionFactory $collectionFactoryAttributes,
        $resource = null,
        $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $registry,
            $extensionFactory,
            $customAttributeFactory,
            $directoryData,
            $eavConfig,
            $addressConfig,
            $regionFactory,
            $countryFactory,
            $metadataService,
            $addressDataFactory,
            $regionDataFactory,
            $dataObjectHelper,
            $scopeConfig,
            $addressItemFactory,
            $itemCollectionFactory,
            $addressRateFactory,
            $rateCollector,
            $rateCollectionFactory,
            $rateRequestFactory,
            $totalCollectorFactory,
            $addressTotalFactory,
            $objectCopyService,
            $carrierFactory,
            $validator,
            $addressMapper,
            $attributeList,
            $totalsCollector,
            $totalsReader,
            $resource,
            $resourceCollection,
            $data
        );
        $this->collectionFactoryAttributes = $collectionFactoryAttributes;
    }

    protected function getCustomAttributesCodes()
    {
        $attributesCollection = $this->collectionFactoryAttributes->create();
        $attributesCollection->getItems();
        return $attributesCollection->getColumnValues('attribute_code');
    }
}