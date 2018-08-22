<?php
/**
 * Copyright © 2018 Aitoc. All rights reserved.
 */

namespace Aitoc\ProductUnitsAndQuantities\Block;

use Aitoc\ProductUnitsAndQuantities\Helper\Data as UnitsAndQuantitiesHelper;
use Aitoc\ProductUnitsAndQuantities\Api\Data\GeneralInterface as UnitsAndQuantitiesModel;
use Aitoc\ProductUnitsAndQuantities\Model\Order as OrderModel;
use Magento\Framework\View\Element\Template\Context;
use Magento\Sales\Model\Order\ItemRepository as OrderItemRepository;
use Magento\Catalog\Model\ProductRepository;

class Render extends \Magento\Framework\View\Element\Template
{
    /** @var UnitsAndQuantitiesModel */
    private $generalModel;

    /** @var OrderModel */
    private $productUnitsAndQuantitiesModel;

    /** @var UnitsAndQuantitiesHelper */
    private $helper;

    /** @var OrderItemRepository */
    private $orderItemRepository;

    /** @var ProductRepository */
    private $productRepository;

    /**
     * Render constructor.
     * @param Context $context
     * @param UnitsAndQuantitiesHelper $helper
     * @param UnitsAndQuantitiesModel $generalModel
     * @param OrderItemRepository $orderItemRepository
     * @param OrderModel $productUnitsAndQuantitiesModel
     * @param array $data
     */
    public function __construct(
        Context $context,
        UnitsAndQuantitiesHelper $helper,
        UnitsAndQuantitiesModel $generalModel,
        OrderItemRepository $orderItemRepository,
        OrderModel $productUnitsAndQuantitiesModel,
        ProductRepository $productRepository,
        array $data = [])
    {
        $this->generalModel = $generalModel;
        $this->helper = $helper;
        $this->orderItemRepository = $orderItemRepository;
        $this->productUnitsAndQuantitiesModel = $productUnitsAndQuantitiesModel;
        $this->productRepository = $productRepository;
        parent::__construct($context, $data);
    }

    /**
     * Render json params for 'productunitsandquantities' js object
     *
     * @param $mode
     * @param int|null $productId
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function renderProductParams($mode, $productId = null)
    {
        if (!$productId) {
            $params = $this->getRequest()->getParams();
            if (isset($params['product_id'])) {
                $productId = $params['product_id'];
            } else {
                $productId = $params['id'];
            }

            $product = $this->productRepository->getById($productId);

            if (in_array($product->getTypeId(), ['bundle'])) {
                return json_encode(['mode' => 'empty']);
            }
            $productUnitsData = $this->helper->getProductParams($productId, $mode);

            if ($product->getTypeId() == 'grouped') {
                $productUnitsData['mode'] = 'grouped_view';
            }
            return json_encode($productUnitsData);

        }

        return json_encode($this->helper->getProductParams($productId, $mode));
    }

    /**
     * @param string $mode
     * @param int $orderItemId
     * @return string
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function renderProductParamsForOrder($mode, $orderItemId)
    {
        /** @var \Magento\Sales\Model\Order\Item $orderItem */
        $orderItem = $this->orderItemRepository->get($orderItemId);
        $productId = $orderItem->getData('product_id');

        $productParams = $this->helper->getProductParams($productId, $mode);
        // TODO: load from repository
        $orderItemParams = $this->productUnitsAndQuantitiesModel->load($orderItemId, 'order_item_id')->getData();

        if ($orderItemParams) {
            $productParams['price_per'] = $orderItemParams['price_per'];
            $productParams['price_per_divider'] = $orderItemParams['price_per_divider'];
        }

        return json_encode($productParams);
    }
}
