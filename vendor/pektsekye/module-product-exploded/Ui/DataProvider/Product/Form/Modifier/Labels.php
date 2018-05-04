<?php

namespace Pektsekye\ProductExploded\Ui\DataProvider\Product\Form\Modifier;

use Magento\Ui\Component\Container;
use Magento\Ui\Component\Form\Fieldset;
use Magento\GroupedProduct\Model\Product\Type\Grouped as GroupedProductType;

class Labels extends \Magento\Catalog\Ui\DataProvider\Product\Form\Modifier\AbstractModifier
{

    const PRODUCT_TYPE_GROUPED = 'grouped';
    
    const GROUP_NAME = 'pe_image';
    const GROUP_SCOPE = 'data.product';
    const GROUP_PREVIOUS_NAME = 'block_gallery';
    const SORT_ORDER = 25;

    const CONTAINER_HEADER_NAME = 'container_header';


    protected $meta = [];
   
    
    protected $locator;


    public function __construct(
        \Magento\Catalog\Model\Locator\LocatorInterface $locator
    ) {
        $this->locator = $locator;
    }



    public function modifyData(array $data)
    {
      return $data;
    }



    public function modifyMeta(array $meta)
    {
        $this->meta = $meta;
        
        if ($this->locator->getProduct()->getTypeId() === GroupedProductType::TYPE_CODE) {
          $this->createCustomOptionsPanel();
        }
        
        return $this->meta;
    }



    protected function createCustomOptionsPanel()
    {
        $this->meta = array_replace_recursive(
            $this->meta,
            [
                static::GROUP_NAME => [
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'label' => __('Exploded View Labels'),
                                'componentType' => Fieldset::NAME,
                                'dataScope' => static::GROUP_SCOPE,
                                'collapsible' => true,
                                'sortOrder' => $this->getNextGroupSortOrder(
                                    $this->meta,
                                    static::GROUP_PREVIOUS_NAME,
                                    static::SORT_ORDER
                                ),
                            ],
                        ],
                    ],
                    'children' => [
                        static::CONTAINER_HEADER_NAME => $this->getHeaderContainerConfig(10),

                    ]
                ]
            ]
        );

        return $this;
    }


    protected function getHeaderContainerConfig($sortOrder)
    {
        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'label' => null,
                        'formElement' => Container::NAME,
                        'componentType' => Container::NAME,
                        'template' => "Pektsekye_ProductExploded/form/components/labels_js",                         
                        'sortOrder' => $sortOrder,
                        'content' => '',
                    ],
                ],
            ],
        ];
    } 

}
