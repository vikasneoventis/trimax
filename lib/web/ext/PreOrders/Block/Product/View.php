<?php
/**
 * Copyright © 2016 Aitoc. All rights reserved.
 */
namespace Aitoc\PreOrders\Block\Product;

use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Product;

class View extends \Magento\Catalog\Block\Product\View
{
    /**
     * @var \Aitoc\PreOrders\Helper\Data
     */
    protected $_helper;

    /**
     * View constructor.
     * @param \Magento\Catalog\Block\Product\Context $context
     * @param \Magento\Framework\Url\EncoderInterface $urlEncoder
     * @param \Magento\Framework\Json\EncoderInterface $jsonEncoder
     * @param \Magento\Framework\Stdlib\StringUtils $string
     * @param \Magento\Catalog\Helper\Product $productHelper
     * @param \Magento\Catalog\Model\ProductTypes\ConfigInterface $productTypeConfig
     * @param \Magento\Framework\Locale\FormatInterface $localeFormat
     * @param \Magento\Customer\Model\Session $customerSession
     * @param ProductRepositoryInterface $productRepository
     * @param \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency
     * @param \Aitoc\PreOrders\Helper\Data $helper
     * @param array $data
     */
    public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        \Magento\Framework\Url\EncoderInterface $urlEncoder,
        \Magento\Framework\Json\EncoderInterface $jsonEncoder,
        \Magento\Framework\Stdlib\StringUtils $string,
        \Magento\Catalog\Helper\Product $productHelper,
        \Magento\Catalog\Model\ProductTypes\ConfigInterface $productTypeConfig,
        \Magento\Framework\Locale\FormatInterface $localeFormat,
        \Magento\Customer\Model\Session $customerSession,
        ProductRepositoryInterface $productRepository,
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrency,
        \Aitoc\PreOrders\Helper\Data $helper,
        array $data = []
    ) {
        $this->_helper = $helper;

        parent::__construct(
            $context,
            $urlEncoder,
            $jsonEncoder,
            $string,
            $productHelper,
            $productTypeConfig,
            $localeFormat,
            $customerSession,
            $productRepository,
            $priceCurrency,
            $data
        );
    }

    /**
     * Get json data with preorder
     *
     * @return string
     */
    public function getJsonConfigPreorder()
    {
        $product = $this->getProduct();
        $list = [];
        $cart = __('Add to Cart');
        if ($product->getTypeId() == \Aitoc\PreOrders\Model\Product\Type::TYPE_BUNDLE) {
            $cart = __('Customize and Add to Cart');
        }
        $text = ($this->isPreOrder($product->getId())) ? __('Pre-Order') : $cart;
        $list[$product->getId()] = $text;

        return $this->_jsonEncoder->encode($list);
    }

    /**
     *  Get json data with preordertext
     *
     * @return string
     */
    public function getJsonConfigPreorderText()
    {
        $product = $this->getProduct();

        $stock = "";
        if ($this->_helper->getStockItem($product)) {
            $stock = __('In stock');
        } else {
            $stock = __('Out of stock');
        }
        $list = [];
        $text = ($this->isPreOrder($product->getId())) ? (($product->getPreorderdescript()) ? $product->getPreorderdescript() : __('Pre-Order')) : $stock;
        $list[$product->getId()] = $text;

        return $this->_jsonEncoder->encode($list);
    }

    public function getJsonConfigChange()
    {
        $product = $this->getProduct();
        return (in_array($product->getTypeId(), [\Aitoc\PreOrders\Model\Product\Type::TYPE_SIMPLE, \Aitoc\PreOrders\Model\Product\Type::TYPE_DOWNLOADABLE])) ? 1 : 0;
    }

    /**
     * Check product is preorder
     *
     * @param $id
     * @return int
     */
    protected function isPreOrder($id)
    {
        $product = \Magento\Framework\App\ObjectManager::getInstance()->get('Aitoc\PreOrders\Model\Product')->load($id);
        $preorder = 0;
        if ($product->getListPreorder()) {
            if (!$this->_helper->getStockItem($product)) {
                if ($this->_helper->isBackstockPreorderAllowed($product)) {
                    $preorder = 1;
                }
            } else {
                $preorder = 1;
            }
        }

        return $preorder;
    }
}
