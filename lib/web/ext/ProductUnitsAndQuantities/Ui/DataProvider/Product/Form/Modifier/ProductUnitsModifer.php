<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Aitoc\ProductUnitsAndQuantities\Ui\DataProvider\Product\Form\Modifier;

use Aitoc\ProductUnitsAndQuantities\Helper\Data as UnitsAndQuantitiesHelper;
use Aitoc\ProductUnitsAndQuantities\Api\Data\GeneralInterface as UnitsAndQuantitiesModel;
use Magento\Catalog\Model\Locator\LocatorInterface;
use Magento\Catalog\Ui\DataProvider\Product\Form\Modifier\AbstractModifier;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Stdlib\ArrayManager;
use Magento\Ui\Component\Form\Element\Checkbox;
use Magento\Ui\Component\Form\Field;

/**
 * Class AitocFields
 */
class ProductUnitsModifer extends AbstractModifier
{
    const FIELD_MESSAGE_AVAILABLE = 'replace_qty';

    const USE_CONFIG = 'use_config';

    private $generalModel;

    private $fields;

    private $helper;

    private $containerData;

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var LocatorInterface
     */
    protected $locator;

    /**
     * @var ArrayManager
     */
    protected $arrayManager;

    /**
     * @param LocatorInterface $locator
     * @param ArrayManager $arrayManager
     * @param ScopeConfigInterface $scopeConfig
     * @param UnitsAndQuantitiesModel $generalModel
     * @param UnitsAndQuantitiesHelper $helper
     */
    public function __construct(
        LocatorInterface $locator,
        ArrayManager $arrayManager,
        ScopeConfigInterface $scopeConfig,
        UnitsAndQuantitiesModel $generalModel,
        UnitsAndQuantitiesHelper $helper
    )
    {
        $this->locator = $locator;
        $this->arrayManager = $arrayManager;
        $this->scopeConfig = $scopeConfig;
        $this->generalModel = $generalModel;
        $this->fields = $helper->getFields();
        $this->containerData = $helper->getContainerData();
    }

    public function getFields()
    {
        return $this->fields;
    }

    public function getUseConfig()
    {
        return static::USE_CONFIG;
    }

    public function getContainerData($attributeCode = null)
    {
        if ($attributeCode) {
            return $this->containerData['container_' . $attributeCode];
        } else {
            return $this->containerData;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function modifyData(array $data)
    {
        $productType = $this->locator->getProduct()->getData('type_id');

        if ($productType == 'bundle' || $productType == 'grouped') {
            return $data;
        }

        $modelId = $this->locator->getProduct()->getId();
        $item = $this->generalModel->load($modelId, 'product_id');
        $useConfigParams = explode(',', $item->getUseConfigParams());

        foreach ($this->getFields() as $field) {

            if (!$item->getData($field)) {
                $data[$modelId][static::DATA_SOURCE_DEFAULT][$this->getUseConfig() . '_' . $field] = '1';
            } else {
                if (in_array($field, $useConfigParams)) {
                    $data[$modelId][static::DATA_SOURCE_DEFAULT][$field] = $this->getValueFromConfig($field);
                    $data[$modelId][static::DATA_SOURCE_DEFAULT][$this->getUseConfig() . '_' . $field] = '1';
                } else {
                    $data[$modelId][static::DATA_SOURCE_DEFAULT][$field] = $item->getData($field);
                }
            }
        }

        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function modifyMeta(array $meta)
    {
        $productType = $this->locator->getProduct()->getData('type_id');

        if ($productType == 'bundle' || $productType == 'grouped') {
            return $meta;
        }

        $meta['product-units-and-quantities']['arguments']['data']['config'] = [
            'componentType' => 'fieldset',
            'label' => __('Product Units And Quantities'),
            'collapsible' => true,
            'dataScope' => 'data.product',
            'sortOrder' => 100
        ];
        foreach ($this->getFields() as $attributeCode) {
            if (array_key_exists('container_' . $attributeCode, $this->containerData)) {
                $meta['product-units-and-quantities']['children']['container_'
                . $attributeCode] = $this->getContainerData($attributeCode);
            }
            $meta = $this->customizeProductUnitsField($meta, $attributeCode);
        }

        return $meta;
    }

    /**
     * Customization of allow gift message field
     *
     * @param array $meta
     *
     * @return array
     */
    protected function customizeProductUnitsField(array $meta, $attributeCode)
    {
        $groupCode = $this->getGroupCodeByField($meta, 'container_' . $attributeCode);

        if (!$groupCode) {
            return $meta;
        }

        $containerPath = $this->arrayManager->findPath(
            'container_' . $attributeCode,
            $meta,
            null,
            'children'
        );
        $fieldPath = $this->arrayManager->findPath($attributeCode, $meta, null, 'children');
        $groupConfig = $this->arrayManager->get($containerPath, $meta);
        $fieldConfig = $this->arrayManager->get($fieldPath, $meta);

        $meta = $this->arrayManager->merge(
            $containerPath,
            $meta,
            [
                'arguments' => [
                    'data' => [
                        'config' => [
                            'formElement' => 'container',
                            'componentType' => 'container',
                            'component' => 'Magento_Ui/js/form/components/group',
                            'label' => $groupConfig['arguments']['data']['config']['label'],
                            'breakLine' => false,
                            'sortOrder' => $fieldConfig['arguments']['data']['config']['sortOrder'],
                            'dataScope' => '',
                        ],
                    ],
                ],
            ]
        );

        $meta = $this->arrayManager->merge(
            $containerPath,
            $meta,
            [
                'children' => [
                    $attributeCode => [
                        'arguments' => [
                            'data' => [
                                'config' => [
                                    'dataScope' => $attributeCode,
                                    'componentType' => Field::NAME,
                                    'value' => $this->getValueFromConfig($attributeCode),
                                ],
                            ],
                        ],
                    ],
                    'use_config_' . $attributeCode => [
                        'arguments' => [
                            'data' => [
                                'config' => [
                                    'dataType' => 'number',
                                    'formElement' => Checkbox::NAME,
                                    'componentType' => Field::NAME,
                                    'description' => __('Use Config Settings'),
                                    'dataScope' => $this->getUseConfig() . '_' . $attributeCode,
                                    'valueMap' => [
                                        'false' => '0',
                                        'true' => '1',
                                    ],
                                    'exports' => [
                                        'checked' => 'ns = ${ $.ns }, index = ' . $attributeCode . ' :disabled',
                                    ]
                                ],
                            ],
                        ],
                    ],
                ],
            ]
        );

        return $meta;
    }

    /**
     * Get config value data
     *
     * @return string|null
     */
    public function getValueFromConfig($field)
    {
        $field = 'product_units_and_quantities/general_settings/' . $field;
        $value = $this->scopeConfig->getValue($field, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);

        return $value;
    }
}
