<?php

namespace Aitoc\ProductUnitsAndQuantities\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Aitoc\ProductUnitsAndQuantities\Api\Data\GeneralInterface as UnitsAndQuantitiesModel;
use Magento\Framework\App\Config\ScopeConfigInterface;

class Data extends AbstractHelper
{
    protected $fields = ['replace_qty', 'qty_type', 'use_quantities', 'start_qty', 'qty_increment', 'end_qty', 'allow_units', 'price_per', 'price_per_divider'];
    protected $scopeConfig;
    private $fieldsSortOrder = 0;
    private $containerData = [];



    private $generalModel;

    public function __construct(
        ScopeConfigInterface $scopeConfig,
        UnitsAndQuantitiesModel $generalModel
    ) {
        $this->generalModel = $generalModel;
        $this->scopeConfig  = $scopeConfig;
        $this->initContainerData();
    }

    public function getFields()
    {
        return $this->fields;
    }

    public function getContainerData()
    {
        return $this->containerData;
    }

    public function getValueFromConfig($field)
    {
        $field = 'product_units_and_quantities/general_settings/' . $field;
        $value = $this->scopeConfig->getValue($field, \Magento\Store\Model\ScopeInterface::SCOPE_STORE);

        return $value;
    }

    public function getProductParams($id, $mode = null)
    {
        $productData = [];
        $fields = $this->getFields();
        if ($mode) {
            $productData = ['mode' => $mode];
        }

        if ($this->generalModel->load($id, 'product_id')->getData()) {
            $productOriginalData = $this->generalModel->load($id, 'product_id')->getData();

            foreach ($fields as $field) {
                if ($productOriginalData[$field] === 'use_config') {
                    $productData[$field] = $this->getValueFromConfig($field);
                } else {
                    $productData[$field] = $productOriginalData[$field];
                }
            }

            $this->generalModel->load($id, 'product_id')->unsetData();
        } else {
            foreach ($fields as $field) {
                $productData[$field] = $this->getValueFromConfig($field);
            }
        }

        return $productData;
    }

    private function initContainerData()
    {
        $this->addContainerData(
            'replace_qty',
            'Replace Qty',
            0,
            0,
            'text',
            'select',
            ['options' => [
                ['label' => 'Off', 'value' => 0],
                ['label' => 'Dropdown', 'value' => 1],
                ['label' => 'Slider', 'value' => 2],
                ['label' => 'Plus Minus', 'value' => 3],
                ['label' => 'Arrows', 'value' => 4]
            ]]
        );

        $this->addContainerData(
            'qty_type',
            'QTY Field Value',
            0,
            0,
            'text',
            'select',
            [
                'component' => 'Aitoc_ProductUnitsAndQuantities/js/form/product/field/qty_type',
                'options' => [
                    ['label' => 'Static', 'value' => 0],
                    ['label' => 'Dynamic', 'value' => 1]
                ]]
        );

        $this->addContainerData(
            'use_quantities',
            'Use Quantities',
            '1,2,5,7,10'
        );

        $this->addContainerData(
            'start_qty',
            'Set a starting QTY value.',
            '1',
            0,
            'text',
            'input',
            [
                'additionalClasses' => 'validate-use-quantities',
                'notice' => 'Set a starting QTY value.',
                'validation' => ['required-entry' => true],
            ]
        );

        $this->addContainerData(
            'qty_increment',
            'Qty Increment',
            '2',
            0,
            'text',
            'input',
            [
                'notice' => 'Add a QTY increment amount.',
                'validation' => ['required-entry' => true],
            ]
        );

        $this->addContainerData(
            'end_qty',
            'Final QTY Value',
            '2',
            0,
            'text',
            'input',
            [
                'notice' => 'Introduce a final QTY value.',
                'validation' => ['required-entry' => true],
            ]
        );

        $this->addContainerData(
            'allow_units',
            'Allow Units',
            0,
            0,
            'text',
            'select',
            [
                'options' => [
                    ['label' => 'No', 'value' => 0],
                    ['label' => 'Yes', 'value' => 1]
                ]
            ]
        );

        $this->addContainerData(
            'price_per',
            'Price per',
            'Item'
        );

        $this->addContainerData(
            'price_per_divider',
            'Price Per Divider',
            '/'
        );
    }

    private function addContainerData($name, $label, $defaultValue, $isRequired = 0, $dataType = 'text', $formElement = 'input', $additionalConfig = [])
    {
        $elementConfig = [
            'visible' => 1,
            'notice' => '',
            'label' => $label,
            'code' => $name,
            'source' => 'product-units-and-quantities',
            'globalScope' => '',
            'sortOrder' => $this->fieldsSortOrder,
            'componentType' => 'field',
            'required' => $isRequired,
            'default' => $defaultValue,
            'dataType' => $dataType,
            'formElement' => $formElement,
        ];

        $elementConfig = array_merge($elementConfig, $additionalConfig);

        $this->containerData['container_' . $name] = [
            'arguments' => [
                'data' => [
                    'config' => [
                        'formElement' => 'container',
                        'componentType' => 'container',
                        'breakLine' => '',
                        'label' => $label,
                        'required' => 0,
                        'sortOrder' => $this->fieldsSortOrder,
                        'component' => 'Magento_Ui/js/form/components/group',
                        'dataScope' => ''
                    ]
                ]
            ],
            'children' => [
                $name => [
                    'arguments' => [
                        'data' => [
                            'config' => $elementConfig
                        ]
                    ]
                ]
            ]
        ];
        $this->fieldsSortOrder += 10;
    }
}
