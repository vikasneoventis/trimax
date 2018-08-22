<?php
/**
 * Copyright © 2016 Aitoc. All rights reserved.
 */

namespace Aitoc\PreOrders\Plugin\Block\Product\View\Type;

class Bundle
{
    const SELECTION = "selections";

    /**
     * @var \Aitoc\PreOrders\Model\Product
     */
    protected $_product;

    /**
     * Bundle constructor.
     * @param \Aitoc\PreOrders\Model\Product $product
     */
    public function __construct(
        \Aitoc\PreOrders\Model\Product $product
    ) {
        $this->_product = $product;
    }

    /**
     * @param \Magento\Bundle\Block\Catalog\Product\View\Type\Bundle $block
     * @param $text
     * @return string
     * @throws \Zend_Json_Exception
     */
    public function afterGetJsonConfig(\Magento\Bundle\Block\Catalog\Product\View\Type\Bundle $block, $text)
    {
        $config = \Zend_Json::decode($text);

        foreach ($config as $keyConfig => $options) {
            if (is_array($options)) {
                foreach ($options as $keyOption => $selections) {
                    if (is_array($selections)) {
                        foreach ($selections as $keySelect => $selection) {
                            if ($keySelect == self::SELECTION) {
                                if (is_array($selection)) {
                                    foreach ($selection as $keyElement => $element) {
                                        $product = $this->_product->load($element['optionId']);
                                        if ($product->getListPreorder()) {
                                            $config[$keyConfig][$keyOption][$keySelect][$keyElement]['name'] .= " " . __('Pre-Order');
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
        
        return \Zend_Json::encode($config);
    }
}
