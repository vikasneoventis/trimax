<?php

namespace Aitoc\PreOrders\Plugin\Model\Product;

use Aitoc\PreOrders\Helper\Data as HelperData;
use Magento\Catalog\Model\Product;
use Magento\CatalogInventory\Model\Stock\StockItemRepository;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable as ConfigurableOriginal;
use Magento\CatalogInventory\Model\Stock\Status;

class Configurable
{
    private $helperData;

    private $product;

    private $requiredAttributeIds;

    protected $_stockItemRepository;

    /**
     * Configurable constructor.
     *
     * @param HelperData          $helperData
     * @param StockItemRepository $stockItemRepository
     */
    public function __construct(
        HelperData $helperData,
        StockItemRepository $stockItemRepository
    ) {
        $this->helperData           = $helperData;
        $this->_stockItemRepository = $stockItemRepository;
    }

    /**
     * @param         $subject
     * @param Product $product
     * @param null    $requiredAttributeIds
     */
    public function beforeGetSalableUsedProducts($subject, Product $product, $requiredAttributeIds = null)
    {
        $this->product = $product;
        $this->requiredAttributeIds = $requiredAttributeIds;
    }

    /**
     * @param ConfigurableOriginal $subject
     * @param                      $result
     *
     * Add out of stock and preorder simple products to other simple products
     *
     * @return array
     */
    public function afterGetSalableUsedProducts(ConfigurableOriginal $subject, $result)
    {
        $usedProducts = $subject->getUsedProducts($this->product, $this->requiredAttributeIds);

        $usedSalableProducts = array_filter($usedProducts, function (Product $product) {
            $stockStatus = $this->_stockItemRepository->get(
                $product->getId(),
                $product->getStore()->getWebsiteId()
            );

            if (!$product->isSalable()) {
                return false;
            }

            if ($stockStatus->getData('is_in_stock') == Status::STATUS_IN_STOCK) {
                return false;
            }

            $test = $this->helperData->isPreOrder($stockStatus);

            return true;
        });

        return array_merge($result, $usedSalableProducts);
    }
}