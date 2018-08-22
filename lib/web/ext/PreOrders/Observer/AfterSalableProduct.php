<?php
/**
 * Copyright © 2016 Aitoc. All rights reserved.
 */
namespace Aitoc\PreOrders\Observer;

class AfterSalableProduct implements \Magento\Framework\Event\ObserverInterface
{
    /**
     * @var \Aitoc\PreOrders\Helper\Data
     */
    protected $_helper;

    /**
     * @var \Magento\ConfigurableProduct\Model\Product\Type\Configurable
     */
    protected $_configurable;

    /**
     * AfterSalableProduct constructor.
     *
     * @param \Aitoc\PreOrders\Helper\Data                                 $helper
     * @param \Magento\ConfigurableProduct\Model\Product\Type\Configurable $configurable
     */
    public function __construct(
        \Aitoc\PreOrders\Helper\Data $helper,
        \Magento\ConfigurableProduct\Model\Product\Type\Configurable $configurable
    ) {
        $this->_helper       = $helper;
        $this->_configurable = $configurable;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     *
     * @return bool
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $event   = $observer->getEvent();
        $product = $event->getProduct();

        if ($event->getSalable()) {
            $salable = $event->getSalable();
        } else {
            $salable = new \Magento\Framework\DataObject(
                [
                    'product'    => $product,
                    'is_salable' => $product->isAvailable()
                ]
            );
        }
        if ($salable->getIsSalable()) {
            return true;
        }
        if ($this->_helper->isBackstockPreorderAllowed($product)) {
            $salable->setIsSalable(true);

            return true;
        }

        if ($product->getTypeId() == \Aitoc\PreOrders\Model\Product\Type::TYPE_CONFIGURABLE && $product->isInStock()) {
            //$conf = $this->_configurable->setProduct($product);
            //$simple_collection = $conf->getUsedProductCollection()->addAttributeToSelect('*')->addFilterByRequiredOptions();

            $simple_collection = $this->_configurable
                ->getUsedProductCollection($product)
                ->addAttributeToSelect('*')
                ->addFilterByRequiredOptions();

            foreach ($simple_collection as $simple) {
                if ($this->_helper->isBackstockPreorderAllowed($simple)) {
                    $salable->setIsSalable(true);

                    return true;
                }
            }
        }
        if ($product->getTypeId() == \Aitoc\PreOrders\Model\Product\Type::TYPE_GROUPED && $product->isInStock()) {
            $associatedProducts = $product->getTypeInstance(true)->getAssociatedProducts($product);
            foreach ($associatedProducts as $simple) {
                if ($this->_helper->isBackstockPreorderAllowed($simple)) {
                    $salable->setIsSalable(true);

                    return true;
                }
            }
        }
    }
}
