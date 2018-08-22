<?php
/**
 * Copyright © 2016 Aitoc. All rights reserved.
 */

namespace Aitoc\PreOrders\Plugin\Controller\Ajax;

use Magento\Catalog\Block\Product\Context;
use Magento\Catalog\Model\Product;

class Media
{
    /**
     * @var \Magento\Framework\Json\Decoder
     */
    protected $_decode;

    /**
     * @var \Magento\Catalog\Model\ProductFactory
     */
    protected $_factory;

    /**
     * @var \Magento\Swatches\Helper\Data
     */
    protected $swatchHelper;

    /**
     * @var
     */
    protected $_object;

    /**
     * Media constructor.
     * @param Context $context
     * @param \Magento\Framework\Json\Decoder $decode
     * @param \Magento\Catalog\Model\ProductFactory $productModelFactory
     * @param \Magento\Swatches\Helper\Data $swatchHelper
     */
    public function __construct(
        Context $context,
        \Magento\Framework\Json\Decoder $decode,
        \Magento\Catalog\Model\ProductFactory $productModelFactory,
        \Magento\Swatches\Helper\Data $swatchHelper
    ) {
        $this->_decode = $decode;
        $this->_factory = $productModelFactory;
        $this->swatchHelper = $swatchHelper;
    }

    /**
     * @param \Magento\Swatches\Controller\Ajax\Media $object
     * @param $text
     * @return mixed
     */
    public function afterExecute(\Magento\Swatches\Controller\Ajax\Media $object, $text)
    {
        $this->_object = $object;
        $productMedia = [];
        $productMedia = $text->getJson();
        $productId = (int)$this->_object->getRequest()->getParam('product_id');
        $attributes = (array)$this->_object->getRequest()->getParam('attributes');
        $currentConfigurable = $this->_factory->create()->load($productId);
        $product = $this->getProductVariationWithMedia($currentConfigurable, $attributes);
        $productMedia['product'] = $product->getId();
        $text->setData($productMedia);

        return $text;
    }

    /**
     * @param Product $currentConfigurable
     * @param array $attributes
     * @return bool|\Magento\Catalog\Api\Data\ProductInterface|null
     */
    protected function getProductVariationWithMedia(Product $currentConfigurable, array $attributes)
    {
        $product = null;
        $layeredAttributes = [];
        $configurableAttributes = $this->swatchHelper->getAttributesFromConfigurable($currentConfigurable);
        if ($configurableAttributes) {
            $layeredAttributes = $this->getLayeredAttributesIfExists($configurableAttributes);
        }
        $resultAttributes = array_merge($layeredAttributes, $attributes);

        $product = $this->swatchHelper->loadVariationByFallback($currentConfigurable, $resultAttributes);
        if (!$product || (!$product->getImage() || $product->getImage() == 'no_selection')) {
            $product = $this->swatchHelper->loadFirstVariationWithImage(
                $currentConfigurable,
                $resultAttributes
            );
        }
        return $product;
    }

    /**
     * @param array $configurableAttributes
     * @return array
     */
    protected function getLayeredAttributesIfExists(array $configurableAttributes)
    {
        $layeredAttributes = [];

        foreach ($configurableAttributes as $attribute) {
            if ($urlAdditional = (array)$this->_object->getRequest()->getParam('additional')) {
                if (array_key_exists($attribute['attribute_code'], $urlAdditional)) {
                    $layeredAttributes[$attribute['attribute_code']] = $urlAdditional[$attribute['attribute_code']];
                }
            }
        }
        return $layeredAttributes;
    }
}
