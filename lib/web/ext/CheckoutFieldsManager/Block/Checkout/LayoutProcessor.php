<?php
/**
 * Copyright © 2016 Aitoc. All rights reserved.
 */

namespace Aitoc\CheckoutFieldsManager\Block\Checkout;

use Magento\Framework\Api\CustomAttributesDataInterface;
use Magento\Customer\Api\CustomerRepositoryInterface as CustomerRepository;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Checkout\Block\Checkout\LayoutProcessorInterface;
use Aitoc\CheckoutFieldsManager\Model\AttributeMetadataDataProvider;
use Magento\Ui\Component\Form\AttributeMapper;
use Magento\Checkout\Block\Checkout\AttributeMerger;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Psr\Log\LoggerInterface;

class LayoutProcessor implements LayoutProcessorInterface
{
    /**
     * @var AttributeMetadataDataProvider
     */
    private $attributeMetadataDataProvider;

    /**
     * @var AttributeMapper
     */
    protected $attributeMapper;

    /**
     * @var AttributeMerger
     */
    protected $merger;

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var CustomerRepository
     */
    private $customerRepository;

    /**
     * @var CustomerSession
     */
    private $customerSession;

    /**
     * @param AttributeMetadataDataProvider $attributeMetadataDataProvider
     * @param AttributeMapper               $attributeMapper
     * @param AttributeMerger               $merger
     * @param ScopeConfigInterface          $scopeConfig
     * @param LoggerInterface               $logger
     * @param CustomerRepository            $customerRepository
     * @param CustomerSession               $customerSession
     */
    public function __construct(
        AttributeMetadataDataProvider $attributeMetadataDataProvider,
        AttributeMapper $attributeMapper,
        AttributeMerger $merger,
        ScopeConfigInterface $scopeConfig,
        LoggerInterface $logger,
        CustomerRepository $customerRepository,
        CustomerSession $customerSession
    ) {
        $this->attributeMetadataDataProvider = $attributeMetadataDataProvider;
        $this->attributeMapper               = $attributeMapper;
        $this->merger                        = $merger;
        $this->scopeConfig                   = $scopeConfig;
        $this->logger                        = $logger;
        $this->customerRepository            = $customerRepository;
        $this->customerSession               = $customerSession;
    }

    /**
     * Inject additional checkout fields to main layout
     *
     * @param array $jsLayout
     *
     * @return array
     */

    public function process($jsLayout)
    {
        $elementsSet = $this->getElements();
        foreach ($elementsSet as $displayArea => $elements) {
            $fields = $this->getFields($jsLayout, $displayArea);
            $keys   = array_keys($fields);
            foreach ($elements as $code => $items) {
                if (!in_array($code, $keys)
                    && isset($items['formElement'])
                    && $this->scopeConfig->getValue(
                        'checkoutfieldsmanager/components/' . $items['formElement'] . '/model'
                    )
                ) {
                    $fields[$code] = [
                        'component' => $this->scopeConfig->getValue(
                            'checkoutfieldsmanager/components/' . $items['formElement'] . '/component'
                        ),
                        'config' =>
                            [
                                'template' => $this->scopeConfig->getValue(
                                    'checkoutfieldsmanager/components/' . $items['formElement'] . '/component_config/template'
                                ),
                                'elementTmpl' => $this->scopeConfig->getValue(
                                    'checkoutfieldsmanager/components/' . $items['formElement'] . '/component_config/element_tmpl'
                                )
                            ]
                    ];
                }
            }

            /**
             * Merger merging old and new fields (with replace by attribute code) and preparing for KnockoutJS fields
             */
            $fields   = $this->merger->merge(
                $elements,
                'checkoutProvider',
                $this->getDataPrefix($displayArea),
                $fields
            );
            $jsLayout = $this->setFields($jsLayout, $fields, $displayArea);
        }

        return $jsLayout;
    }

    /**
     * Load Attributes and convert to form elements
     *
     * @return array
     */
    protected function getElements()
    {
        /** @var \Aitoc\CheckoutFieldsManager\Model\Attribute $attributes */
        $attributes  = $this->attributeMetadataDataProvider->loadAttributesCollection();
        $elementsSet = [];
        foreach ($attributes as $attribute) {
            $element = $this->attributeMapper->map($attribute);

            if (isset($element['label'])) {
                $element['label'] = __($element['label']);
            }
            if ($element['dataType'] == 'boolean') {
                $element['formElement'] = 'select';
            }
            if (in_array($element['dataType'], ['radiobutton', 'select', 'multiselect'])
                && count($element['options']) < 2
            ) {
                continue;
            }
            $elementsSet[$attribute->getDisplayArea()][$attribute->getAttributeCode()] = $element;
        }

        return $elementsSet;
    }

    /**
     * @param array  $jsLayout
     * @param string $displayArea
     *
     * @return null
     */
    protected function getFields($jsLayout, $displayArea)
    {
        $keys = $this->getKeyPath($displayArea);
        foreach ($keys as $key) {
            if (is_array($jsLayout) && array_key_exists($key, $jsLayout)) {
                $jsLayout = $jsLayout[$key];
            } else {
                return [];
            }
        }

        return $jsLayout;
    }

    /**
     * Get full checkout layout path
     *
     * @param string $displayArea
     *
     * @return array
     */
    protected function getKeyPath($displayArea)
    {
        if ($displayArea == 'shipping_address' && $this->customerSession->isLoggedIn()) {
            $addresses = $this->customerRepository->getById($this->customerSession->getCustomerId())->getAddresses();
            if ($addresses != null && count($addresses)) {
                /**
                 * The additional shipping address fields should be displayed in the other area
                 * if the customer is logged in and have addresses.
                 */
                return [
                    'components',
                    'checkout',
                    'children',
                    'steps',
                    'children',
                    'shipping-step',
                    'children',
                    'shippingAddress',
                    'children',
                    'address-list-additional-addresses',
                    'children'
                ];
            }
        }

        $path = $this->scopeConfig->getValue('checkoutfieldsmanager/checkout_field_path/' . $displayArea);
        if (!$path) {
            $this->logger->warning(
                sprintf(
                    "Aitoc Checkout Fields Manager: Can't find checkout key path for Display Area Code \"%1\$s\"\n" .
                    "The fields with that Area will not be shown.\n" .
                    "Check default value for store config value \"checkoutfieldsmanager/checkout_field_path/%1\$s\"",
                    $displayArea
                )
            );

            return [];
        }

        return explode('>', $path);
    }

    /**
     * Initialize adding fields to layout
     *
     * @param array $jsLayout
     * @param array $fields
     * @param array $formCode
     *
     * @return array
     */
    protected function setFields($jsLayout, $fields, $formCode)
    {
        $keys = $this->getKeyPath($formCode);
        if ($keys) {
            $this->findAndSetField($jsLayout, $fields, $keys);
        }

        return $jsLayout;
    }

    /**
     * Recursively adding fields to layout by the full path
     *
     * @param array $jsLayout
     * @param array $fields
     * @param array $keys
     */
    private function findAndSetField(&$jsLayout, $fields, &$keys)
    {
        $key = current($keys);
        if ($key === false) {
            $jsLayout = $fields;
            return;
        }
        if (!array_key_exists($key, $jsLayout)) {
            $jsLayout[$key] = [];
        }
        next($keys);
        $result = &$jsLayout[$key];

        $this->findAndSetField($result, $fields, $keys);
    }

    /**
     * With prefix custom_attributes, the data will saved by JS in shipping or billing address entity
     * note: billing address have unique name ('billingAddress'+paymentCode), resolved by adding js.
     *
     * @param string $area
     *
     * @return string
     */
    protected function getDataPrefix($area)
    {
        return (strpos($area, 'shipping') !== false ? 'shippingAddress.' : 'billingAddress.')
        . CustomAttributesDataInterface::CUSTOM_ATTRIBUTES;
    }
}
