<?php
/**
 * Copyright © 2016 Aitoc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Aitoc\PreOrders\Model;

class ConfigurableAttributeData extends \Magento\ConfigurableProduct\Model\ConfigurableAttributeData
{
    const ATTR_PREORDER = "preorder";

    const ATTR_PREORDER_DESCRIPT = "preorderdescript";

    /**
     * @var Product\Type
     */
    protected $catalogProductType;

    /**
     * @var \Magento\CatalogInventory\Api\StockRegistryInterface
     */
    protected $stockRegistry;

    /**
     * ConfigurableAttributeData constructor.
     * @param Product\Type $catalogProductType
     * @param \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry
     */
    public function __construct(
        \Aitoc\PreOrders\Model\Product\Type $catalogProductType,
        \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry
    ) {
        $this->catalogProductType = $catalogProductType;
        $this->stockRegistry = $stockRegistry;
    }

    /**
     * get attributes with pre-order
     *
     * @param \Magento\Catalog\Model\Product $product
     * @param array $options
     * @return array
     */
    public function getAttributesData(\Magento\Catalog\Model\Product $product, array $options = [])
    {
        $defaultValues = [];
        $attributes = [];
        foreach ($product->getTypeInstance()->getConfigurableAttributes($product) as $attribute) {
            $attributeOptionsData = $this->getAttributeOptionsData($attribute, $options);
            if ($attributeOptionsData) {
                $productAttribute = $attribute->getProductAttribute();
                $attributeId = $productAttribute->getId();
                $attributes[$attributeId] = [
                    'id' => $attributeId,
                    'code' => $productAttribute->getAttributeCode(),
                    'label' => $attribute->getLabel(),
                    'options' => $attributeOptionsData,
                ];
                $defaultValues[$attributeId] = $this->getAttributeConfigValue($attributeId, $product);
            }
        }
        $preorderAttr = [self::ATTR_PREORDER, self::ATTR_PREORDER_DESCRIPT];
        $options = $this->preorderOptions($product);
        foreach ($preorderAttr as $key => $attr) {
            $result = $this->findAttr($attr, $product);
            if ($result) {
                $attributeId = $result->getId();
                $attributes[$attributeId] =
                    [
                        'id' => $attributeId,
                        'code' => $result->getAttributeCode(),
                        'options' => $options[$key]
                    ];
            }
        }

        return ['attributes' => $attributes, 'defaultValues' => $defaultValues];

    }

    /**
     * Find atrribute
     *
     * @param $attr
     * @param $product
     * @return null
     **/
    protected function findAttr($attr, $product)
    {
        $attributes = $product->getAttributes();
        if ($attributes[$attr]) {
            return $attributes[$attr];
        }
        return null;
    }

     /**
     * Get pre-order options
     *
     * @param $product
     * @return array
     **/
    protected function preorderOptions($product)
    {
        $attributeOptionsPreOrder = [];
        $attributeOptionsDescript = [];
        $associatedProducts = $this->catalogProductType->factory($product)->getUsedProducts($product);
        $valuesPreorder = [];
        $valuesPreorderDescript = [];

        foreach ($associatedProducts as $value) {
            $preorder = $value->getPreorder();
            if (!$value->getPreorder()) {
                $preorder = 'Add to Cart';
            } else {
                $preorder = 'Pre-Order';
            }
            if (!isset($valuesPreorder[$preorder])) {
                $valuesPreorder[$preorder] = [];
                $valuesPreorder[$preorder] = ["products" => []];
            }

            $valuesPreorder[$preorder]['products'][] = $value->getId();
            $newProduct = \Magento\Framework\App\ObjectManager::getInstance()
                ->get('Aitoc\PreOrders\Model\Product')->load($value->getId());
            $preorderdescript = $newProduct->getPreorderdescript();

            $stockItem = $newProduct->getStockData();
            if (!$newProduct->getPreorderdescript()) {
                $item = $this->stockRegistry->getStockItem($value->getId(), $value->getStore()->getWebsiteId());
                if ($item->getData('is_in_stock')) {
                    $preorderdescript = __("In stock");
                } else {
                    $preorderdescript = __("Pre-Order");
                }
            }

            if (!isset($valuesPreorderDescript[(string)$preorderdescript])) {
                $valuesPreorderDescript[(string)$preorderdescript] = [];
                $valuesPreorderDescript[(string)$preorderdescript] = ["products" => []];
            }
            $valuesPreorderDescript[(string)$preorderdescript]['products'][] = $value->getId();
        }

        $inc = 0;
        foreach ($valuesPreorder as $key => $element) {
            $attributeOptionsPreOrder[] = ['id' => $inc, 'label' => (string)__($key), 'products' => $element['products']];
            $inc++;
        }
        $inc = 0;
        foreach ($valuesPreorderDescript as $key => $element) {
            $attributeOptionsDescript[] = ['id' => $inc, 'label' => (string)__($key), 'products' => $element['products']];
            $inc++;
        }

        return [$attributeOptionsPreOrder, $attributeOptionsDescript];
    }
}
