<?php
/**
 * Copyright © 2016 Aitoc. All rights reserved.
 */

namespace Aitoc\PreOrders\Model\Stock;

use Magento\Catalog\Model\Product;

class Item extends \Magento\CatalogInventory\Model\Stock\Item
{

    /**
     * @var \Aitoc\PreOrders\Helper\Data
     */
    protected $_helper;

    /**
     * @var \Magento\CatalogInventory\Api\StockConfigurationInterface
     */
    protected $_stockConfiguration;

    /**
     * Item constructor.
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Api\ExtensionAttributesFactory $extensionFactory
     * @param \Magento\Framework\Api\AttributeValueFactory $customAttributeFactory
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\CatalogInventory\Api\StockConfigurationInterface $stockConfiguration
     * @param \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry
     * @param \Magento\CatalogInventory\Api\StockItemRepositoryInterface $stockItemRepository
     * @param \Aitoc\PreOrders\Helper\Data $helper
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource|null $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb|null $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Api\ExtensionAttributesFactory $extensionFactory,
        \Magento\Framework\Api\AttributeValueFactory $customAttributeFactory,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\CatalogInventory\Api\StockConfigurationInterface $stockConfiguration,
        \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry,
        \Magento\CatalogInventory\Api\StockItemRepositoryInterface $stockItemRepository,
        \Aitoc\PreOrders\Helper\Data $helper,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct(
            $context,
            $registry,
            $extensionFactory,
            $customAttributeFactory,
            $customerSession,
            $storeManager,
            $stockConfiguration,
            $stockRegistry,
            $stockItemRepository,
            $resource,
            $resourceCollection,
            $data
        );
        $this->_stockConfiguration = $stockConfiguration;
        $this->_helper = $helper;
    }

    /**
     * get product from entity_id
     *
     * @return Object
     */
    public function getProduct()
    {
        $collection = \Magento\Framework\App\ObjectManager::getInstance()
            ->get('Magento\Catalog\Model\ResourceModel\Product\Collection')
            ->addAttributeToFilter('entity_id', ['eq' => $this->getProductId()]);
        $product = null;
        foreach ($collection as $item) {
            $product = $item;
        }

        return $product;
    }

    /**
     * @param null $qty
     * @return bool
     */
    public function verifyStock($qty = null)
    {
        if ($qty === null) {
            $qty = $this->getQty();
        }

        if (($this->getBackorders() == \Magento\CatalogInventory\Model\Stock::BACKORDERS_NO
                || $this->_helper->isPreOrder($this))
            && $qty <= $this->getMinQty()
        ) {
            return false;
        }
        return true;
    }

    /**
     * @param null $qty
     * @return bool
     */
    public function verifyNotification($qty = null)
    {
        if ($qty === null) {
            $qty = $this->getQty();
        }
        return (float)$qty < $this->getNotifyStockQty();
    }


    /**
     * Override parent. Retrieve Stock Availability
     *
     * @return bool|int
     */
    public function getIsInStock()
    {
        $stock = parent::getIsInStock();
        if (!$stock && $this->_helper->isBackstockPreorderAllowed($this->getProduct())) {
            $stock = true;
        }
        return $stock;
    }

    /**
     * Override parent. Check quantity
     *
     * @param   decimal $qty
     * @exception Mage_Core_Exception
     * @return  bool
     */
    public function checkQty($qty)
    {
        $qty = parent::checkQty($qty);
        if (!$qty && $this->_helper->isBackstockPreorderAllowed($this->getProduct())) {
            $qty = true;
        }
        return $qty;
    }

    /**
     * @return $this
     */
    public function beforeSave()
    {
        $typeId = $this->getTypeId();
        if ($productTypeId = $this->getProductTypeId()) {
            $typeId = $productTypeId;
        }
        $isQty = $this->_stockConfiguration->isQty($typeId);
        if ($isQty) {
            if (!$this->verifyStock()) {
                if ($this->getBackorders() == \Aitoc\PreOrders\Model\SourceBackorders::BACKORDERS_YES_PREORDERS_ZERO) {
                    $preorder = 1;
                }
                $this->setIsInStock(false)
                    ->setStockStatusChangedAutomaticallyFlag(true);
            } elseif ($this->getBackorders() == \Aitoc\PreOrders\Model\SourceBackorders::BACKORDERS_YES_PREORDERS_ZERO) {
                $preorder = 0;
            }
            if (isset($preorder)) {
                $product = $this->getProduct();
                $preorderProduct = $product->getPreorder();
                if (!isset($preorderProduct) || $preorderProduct != $preorder) {
                    $product->setStoreId(0)->setPreorder($preorder);
                    $product->getResource()->saveAttribute($product, 'preorder');
                }
            }
            $this->setLowStockDate(null);
            if ($this->verifyNotification()) {
                $date = new \DateTime;
                $this->setLowStockDate($date->format(\Magento\Framework\Stdlib\DateTime::DATETIME_PHP_FORMAT));
            }
            $this->setStockStatusChangedAutomatically(0);
            if ($this->hasStockStatusChangedAutomaticallyFlag()) {
                $this->setStockStatusChangedAutomatically((int)$this->getStockStatusChangedAutomaticallyFlag());
            }
        } else {
            $this->setQty(0);
        }

        return parent::beforeSave();
    }
}
