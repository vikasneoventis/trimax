<?php
/**
 * Copyright © 2016 Aitoc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Aitoc\PreOrders\Model\Config\Source;

use Magento\Framework\Data\ValueSourceInterface;

/**
 * Class Backstok
 *
 * @package Aitoc\PreOrders\Model\Config\Source
 */
class Backstok implements ValueSourceInterface
{

    /**
     * @var \Magento\Framework\Registry
     */
    protected $coreRegistry;

    /**
     * Backstok constructor.
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
            return (int) $product->getData($name);
        }

        return 0;
    }
}
