<?php
/**
 * Copyright © 2016 Aitoc. All rights reserved.
 */
namespace Aitoc\PreOrders\Helper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var array
     */
    protected $_productBackstockCache = [];

    /**
     * @var
     */
    protected $_validController;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $_logger;

    /**
     * @var \Aitoc\PreOrders\Model\Product
     */
    protected $_product;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $_registry;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scope;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \Magento\Framework\App\Request\Http
     */
    protected $_http;

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_manager;

    /**
     * @var \Magento\CatalogInventory\Api\StockRegistryInterface
     */
    protected $stockRegistry;

    /**
     * Data constructor.
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Aitoc\PreOrders\Model\Product $product
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scope
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\App\Request\Http $request
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry
     * @param \Magento\Framework\ObjectManagerInterface $manager
     */
    public function __construct(
        \Psr\Log\LoggerInterface $logger,
        \Aitoc\PreOrders\Model\Product $product,
        \Magento\Framework\App\Config\ScopeConfigInterface $scope,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\App\Request\Http $request,
        \Magento\Framework\Registry $registry,
        \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry,
        \Magento\Framework\ObjectManagerInterface $manager
    ) {
        $this->_logger = $logger;
        $this->_product = $product;
        $this->_registry = $registry;
        $this->_scope = $scope;
        $this->_storeManager = $storeManager;
        $this->_http = $request;
        $this->_manager = $manager;
        $this->stockRegistry = $stockRegistry;
    }


    /**
     * get Order
     * @param $item
     * @return null
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getOrder($item)
    {
        try {
            return $item->getOrder();
        } catch (\Exception $e) {
            throw new \Magento\Framework\Exception\LocalizedException(__($e->getMessage()), $e);
        }

    }

    /**
     * Initialize product from id or sku
     *
     * @param $item
     * @param null $sku
     * @return \Aitoc\PreOrders\Model\Product
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function initProduct($item, $sku = null)
    {
        $product = $this->_product;
        $order = $this->getOrder($item);

        if ($order) {
            $product->setStoreId($order->getStoreId());

            if (!$this->_registry->registry('aitoc_order_refund_store_id')) {
                $this->_registry->registry('aitoc_order_refund_store_id', $order->getStoreId());
            }
        }

        $itemData = $item->getData();
        $productId = $sku ? $product->getIdBySku($sku) : $itemData['product_id'];
        $product->load($productId);
        return $product;
    }


    /**
     * Get status for item of order
     *
     * @param $_items
     * @param int $ispending
     * @return int
     */
    public function isHaveReg($_items, $ispending = 0)
    {
        $haveregular = 0;
        $havepreorder = 0;
        $alldownloadable = 1;
        $preorderdownloadable = 0;
        foreach ($_items as $_item) {
            $isshiped = $this->_isShipped($_item);

            if (!$isshiped) {
                switch ($_item->getProductType()) {
                    case \Aitoc\PreOrders\Model\Product\Type::TYPE_GROUPED:
                        $alldownloadable = 0;
                        $_product = $this->initProduct($_item);
                        if (!$_product->getListPreorder()) {
                            $haveregular = 1;
                        } else {
                            $havepreorder = 1;
                        }
                        break;
                    case \Aitoc\PreOrders\Model\Product\Type::TYPE_BUNDLE:
                        $alldownloadable = 0;

                        if ($this->bundleHaveReg($_item)) {
                            $haveregular = 1;
                        }
                        break;
                    case \Aitoc\PreOrders\Model\Product\Type::TYPE_VIRTUAL:
                        if ($ispending) {
                            $alldownloadable = 0;
                            $haveregular = 1;
                        }
                        break;
                    case \Aitoc\PreOrders\Model\Product\Type::TYPE_DOWNLOADABLE:
                        $_product = $this->initProduct($_item);
                        if (!$_product->getListPreorder()) {
                            if ($ispending) {
                                $haveregular = 1;
                            }
                        } else {
                            $havepreorder = 1;
                            $preorderdownloadable = 1;
                        }
                        break;
                    case \Aitoc\PreOrders\Model\Product\Type::TYPE_SIMPLE:
                        $alldownloadable = 0;
                        $_product = $this->initProduct($_item);
                        if (!$_product->getListPreorder() || ($ispending && $_product->isLastInStockProduct())) {
                            $haveregular = 1;
                        } else {
                            $havepreorder = 1;
                        }
                        break;
                }
            }
        }
        if ($havepreorder && $this->_scope->getValue('preorder/general/status_change') == 1) {
            $haveregular = 0;
        }
        if ($ispending == 0) {
            if (($alldownloadable == 1) && ($preorderdownloadable == 1)) {
                $haveregular = -1;
            } elseif (($alldownloadable == 1) && ($preorderdownloadable == 0)) {
                $haveregular = -2;
            }
        }
        return $haveregular;

    }


    /**
     * Check is Shipped
     *
     * @param $_item
     * @return bool
     */
    protected function _isShipped($_item)
    {
        return $_item->getQtyShipped() === $_item->getQtyOrdered();
    }

    /**
     * Check is bundle item on order
     *
     * @param $_item
     * @return int
     */
    public function bundleHaveReg($_item)
    {
        $haveregular = 0;
        $havePreorderInBundle = 0;
        $bundleItems = $_item->getChildrenItems();
        foreach ($bundleItems as $bundleItem) {
            $original_product = $this->initProduct($bundleItem);
            if ($original_product->getPreorder()) {
                $havePreorderInBundle = 1;
            }
        }
        if ($havePreorderInBundle == 0) {
            $haveregular = 1;
        }
        return $haveregular;
    }


    /**
     * Check status for order from items
     *
     * @param $order
     * @return int
     */
    public function isHavePreorder($order)
    {
        if ($order) {
            $items = $order->getItemsCollection();
        } else {
            $this->_logger->debug('There is no order coming in \Aitoc\PreOrders\Helper\Data::isHavePreorder() method');
            return $havepreorder = 0;
        }
        $havepreorder = 0;
        foreach ($items as $item) {
            $noparent_item = 0;
            if (!$item->getParentItemId()) {
                $noparent_item = 1;
            }

            switch ($item->getProductType()) {
                case \Aitoc\PreOrders\Model\Product\Type::TYPE_GROUPED:
                    $product = $this->initProduct($item);
                    $preorder = $_product->getListPreorder();
                    if ($product->getListPreorder()) {
                        $havepreorder = 1;
                    }
                    break;
                case \Aitoc\PreOrders\Model\Product\Type::TYPE_CONFIGURABLE:
                    $productOptions = unserialize($item->getData('product_options'));
                    $originalProduct = $this->initProduct($item, $productOptions['simple_sku']);
                    if ($originalProduct->getListPreorder()) {
                        $havepreorder = 1;
                    }
                    break;
                case \Aitoc\PreOrders\Model\Product\Type::TYPE_BUNDLE:
                    if ($this->bundleHaveReg($item) != '1') {
                        $havepreorder = 1;
                    }

                    break;
                case \Aitoc\PreOrders\Model\Product\Type::TYPE_DOWNLOADABLE:
                    if ($noparent_item) {
                        $product = $this->initProduct($item);
                        $preorder = $product->getListPreorder();
                        if ($preorder) {
                            $havepreorder = 1;
                        }
                    }
                    break;
                case \Aitoc\PreOrders\Model\Product\Type::TYPE_SIMPLE:
                    $product = $this->initProduct($item);
                    $preorder = $product->getListPreorder();
                    if ($preorder) {
                        $havepreorder = 1;
                    }
                    break;
            }
        }

        return $havepreorder;

    }

    /**
     * Get value from config
     *
     * @return bool
     */
    public function isMixedCartAllowed()
    {
        return !(bool)$this->_scope->getValue('preorder/general/deny_mixing_products');
    }

    /**
     * Get backstock for preorder
     *
     * @param $product
     * @return bool
     */
    public function isBackstockPreorderAllowed($product)
    {
        $backstock = false;
        if (!is_object($product)) {
            if (isset($this->_productBackstockCache[$product])) {
                $product = $this->_productBackstockCache[$product];
            } else {
                $product = $this->_product->load($product);
            }
        }
        if (is_null($product->getBackstockPreorders())) {
            $isPreorder = $product
                ->getResource()
                ->getAttributeRawValue(
                    $product->getId(),
                    'backstock_preorders',
                    $this->_storeManager->getStore()
                );
            $product->setData('backstock_preorders', (bool)$isPreorder);
        }
        if ($product->getBackstockPreorders() == 0) {
            $backstock = (bool)$this->_scope->getValue('preorder/general/backstock_preorders');
        } elseif ($product->getBackstockPreorders() == 2) {
            $backstock = true;
        }
        if (!isset($this->_productBackstockCache[$product->getId()])) {
            $this->_productBackstockCache[$product->getId()] = $product;
        }
        $item = $this->stockRegistry->getStockItem(
            $product->getId(),
            $product->getStore()->getWebsiteId()
        );
        $out_of_stock = !$item->getData('is_in_stock');
        $stockLoader = $this->_manager->create('\Aitoc\PreOrders\Model\StockLoader');
        $stockLoader->applyStockToProduct($product);
        if ($this->isPreOrder($item, $backstock) && $out_of_stock && $this->_allowToReplaceStock()) {
            return true;
        }
        return false;
    }


    /**
     * get stock ite from product
     *
     * @param $product
     * @return mixed
     */
    public function getStockItem($product)
    {
        $item = $this->stockRegistry->getStockItem(
            $product->getId(),
            $product->getStore()->getWebsiteId()
        );
        return $item->getData('is_in_stock');
    }

    /**
     * Validate if current page is backend and if it load product for edit - then our validation should fail
     *
     * @return bool
     */
    protected function _allowToReplaceStock()
    {
        if (is_null($this->_validController)) {
            $this->_validController = true;
            $module = $this->_http->getModuleName();
            $controller = $this->_http->getControllerName();
            $notAllowedModules = ['admin'];
            $notAllowedControllers = ['catalog_product'];

            if (in_array($module, $notAllowedModules) && in_array($controller, $notAllowedControllers)) {
                $this->_validController = false;
            }
        }
        return $this->_validController;
    }

    /**
     * Get preorder for out-stock
     *
     * @param $item
     * @return bool
     */
    public function isPreOrder($item, $backstock = null)
    {
        if (!isset($item)) {
            $confValue = $this->_scope->getValue('cataloginventory/item_options/backorders');
            $qty = 0;
            $minQty = 0;
            return false;
        } else {
            $confValue = $item->getBackorders();
            $qty = $item->getQty();
            $minQty = $item->getMinQty();
        }
        if ($minQty == null) {
            $minQty = 0;
        }
        if ($qty == null) {
            $qty = 0;
        }

        if ($item->getTypeId() != \Aitoc\PreOrders\Model\Product\Type::TYPE_DOWNLOADABLE) {
            if ($item->getData("is_in_stock")) {
                return (\Aitoc\PreOrders\Model\SourceBackorders::BACKORDERS_YES_PREORDERS == $confValue)
                || (\Aitoc\PreOrders\Model\SourceBackorders::BACKORDERS_YES_PREORDERS_ZERO == $confValue
                    && $qty <= $minQty
                    && $item->getTypeId() != \Aitoc\PreOrders\Model\Product\Type::TYPE_BUNDLE
                    && $item->getTypeId() != \Aitoc\PreOrders\Model\Product\Type::TYPE_CONFIGURABLE
                    && $item->getTypeId() != \Aitoc\PreOrders\Model\Product\Type::TYPE_GROUPED);
            } else {
                return (\Aitoc\PreOrders\Model\SourceBackorders::BACKORDERS_YES_PREORDERS == $confValue
                && ($backstock === null || $backstock))
                || (\Aitoc\PreOrders\Model\SourceBackorders::BACKORDERS_YES_PREORDERS_ZERO == $confValue
                    && $item->getTypeId() != \Aitoc\PreOrders\Model\Product\Type::TYPE_BUNDLE
                    && $item->getTypeId() != \Aitoc\PreOrders\Model\Product\Type::TYPE_CONFIGURABLE
                    && $item->getTypeId() != \Aitoc\PreOrders\Model\Product\Type::TYPE_GROUPED);
            }
        } else {
            if ($item->getData("is_in_stock")) {
                return (\Aitoc\PreOrders\Model\SourceBackorders::BACKORDERS_YES_PREORDERS == $confValue)
                || (\Aitoc\PreOrders\Model\SourceBackorders::BACKORDERS_YES_PREORDERS_ZERO == $confValue
                    && $qty <= $minQty
                    && $qty > 0
                    && $item->getTypeId() != \Aitoc\PreOrders\Model\Product\Type::TYPE_BUNDLE
                    && $item->getTypeId() != \Aitoc\PreOrders\Model\Product\Type::TYPE_CONFIGURABLE
                    && $item->getTypeId() != \Aitoc\PreOrders\Model\Product\Type::TYPE_GROUPED);
            } else {
                return (\Aitoc\PreOrders\Model\SourceBackorders::BACKORDERS_YES_PREORDERS == $confValue
                    && ($backstock === null || $backstock))
                || (\Aitoc\PreOrders\Model\SourceBackorders::BACKORDERS_YES_PREORDERS_ZERO == $confValue
                    && $qty <= $minQty
                    && $item->getTypeId() != \Aitoc\PreOrders\Model\Product\Type::TYPE_BUNDLE
                    && $item->getTypeId() != \Aitoc\PreOrders\Model\Product\Type::TYPE_CONFIGURABLE
                    && $item->getTypeId() != \Aitoc\PreOrders\Model\Product\Type::TYPE_GROUPED);
            }
        }
    }

    /**
     * Validate for items collection
     *
     * @param $quote
     * @return bool
     */
    public function _validateQuoteItems($quote)
    {
        $quoteHavePreOrdered = false;
        $quoteHaveSimple = false;

        foreach ($quote->getItemsCollection() as $item_id => $item) {
            if ($item->getProduct()->getTypeId() != \Aitoc\PreOrders\Model\Product\Type::TYPE_CONFIGURABLE) {
                $product = \Magento\Framework\App\ObjectManager::getInstance()->get('Aitoc\PreOrders\Model\Product')->load($item->getProduct()->getId());
                if ($product->getListPreorder()) {
                    $quoteHavePreOrdered = true;
                } else {
                    $quoteHaveSimple = true;
                }
            }
        }
        return $quoteHavePreOrdered && $quoteHaveSimple;
    }
}
