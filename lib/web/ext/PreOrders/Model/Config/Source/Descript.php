<?php
/**
 * Copyright © 2016 Aitoc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Aitoc\PreOrders\Model\Config\Source;

use Magento\Framework\Data\ValueSourceInterface;

/**
 * Class Descript
 *
 * @package Aitoc\PreOrders\Model\Config\Source
 */
class Descript implements ValueSourceInterface
{

    /**
     * @var \Magento\Framework\Registry
     */
    protected $coreRegistry;

    /**
     * Descript constructor.
     *
     * @param \Magento\Framework\Registry $coreRegistry
     */
    public function __construct(\Magento\Framework\Registry $coreRegistry)
    {
        $this->coreRegistry = $coreRegistry;
    }

    /**
     * {@inheritdoc}
     */
    public function getValue($name)
    {
        $product = $this->coreRegistry->registry('current_product');

        if ($product->getId()) {
            return $product->getData($name);
        }

        return 0;
    }
}
