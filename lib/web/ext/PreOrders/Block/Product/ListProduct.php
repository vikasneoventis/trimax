<?php
/**
 * Copyright © 2016 Aitoc. All rights reserved.
 */
namespace Aitoc\PreOrders\Block\Product;

use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Catalog\Model\Category;
use Magento\Catalog\Model\Product;
use Magento\Eav\Model\Entity\Collection\AbstractCollection;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\DataObject\IdentityInterface;

class ListProduct extends \Magento\Catalog\Block\Product\ListProduct
{
    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_manager;

    /**
     * @var \Aitoc\PreOrders\Model\Product\Type
     */
    protected $_catalogProductType;

    /**
     * @var \Aitoc\PreOrders\Model\Product
     */
    protected $_product;

    /**
     * @var \Magento\Framework\Json\EncoderInterface
     */
    protected $_jsonEncoder;

    /**
     * @var \Aitoc\PreOrders\Helper\Data
     */
    protected $_helper;

    /**
     * ListProduct constructor.
     * @param \Magento\Catalog\Block\Product\Context $context
     * @param \Magento\Framework\Data\Helper\PostHelper $postDataHelper
     * @param \Magento\Catalog\Model\Layer\Resolver $layerResolver
     * @param CategoryRepositoryInterface $categoryRepository
     * @param \Magento\Framework\Url\Helper\Data $urlHelper
     * @param \Magento\Framework\ObjectManagerInterface $manager
     * @param \Aitoc\PreOrders\Model\Product\Type $catalogProductType
     * @param \Aitoc\PreOrders\Model\Product $product
     * @param \Magento\Framework\Json\EncoderInterface $jsonEncoder
     * @param \Aitoc\PreOrders\Helper\Data $helper
     * @param array $data
     */
    public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        \Magento\Framework\Data\Helper\PostHelper $postDataHelper,
        \Magento\Catalog\Model\Layer\Resolver $layerResolver,
        CategoryRepositoryInterface $categoryRepository,
        \Magento\Framework\Url\Helper\Data $urlHelper,
        \Magento\Framework\ObjectManagerInterface $manager,
        \Aitoc\PreOrders\Model\Product\Type $catalogProductType,
        \Aitoc\PreOrders\Model\Product $product,
        \Magento\Framework\Json\EncoderInterface $jsonEncoder,
        \Aitoc\PreOrders\Helper\Data $helper,
        array $data = []
    ) {
        $this->_manager = $manager;
        $this->_catalogProductType = $catalogProductType;
        $this->_product = $product;
        $this->_jsonEncoder = $jsonEncoder;
        $this->_helper = $helper;

        parent::__construct(
            $context,
            $postDataHelper,
            $layerResolver,
            $categoryRepository,
            $urlHelper,
            $data
        );
    }

    /**
     * Check product is preordered
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

    /**
     * Get json data from product collection with attribute preorder
     *
     * @return string
     */
    public function getJsonConfigPreorder()
    {
        $list = [];
        $_products = $this->getLoadedProductCollection();
        foreach ($_products as $item) {
            $text = ($this->isPreOrder($item->getId())) ? __('Pre-Order') : __('Add to Cart');
            $list[$item->getId()] = $text;
        }

        return $this->_jsonEncoder->encode($list);
    }
}
