<?php

namespace Aitoc\ProductUnitsAndQuantities\Observer;

use Aitoc\ProductUnitsAndQuantities\Helper\Data;
use Aitoc\ProductUnitsAndQuantities\Api\Data\GeneralInterface as UnitsAndQuantitiesModel;
use Aitoc\ProductUnitsAndQuantities\Model\Order as ProductUnitsAndQuantitiesOrderModel;
use Magento\Framework\Event\ObserverInterface;
use Magento\Sales\Model\Order;

class SaveOrderObserver implements ObserverInterface
{
    private $generalModel;
    private $productUnitsAndQuantitiesOrderModel;
    private $orderModel;
    private $helper;

    public function __construct(
        UnitsAndQuantitiesModel $generalModel,
        Data $helper,
        ProductUnitsAndQuantitiesOrderModel $productUnitsAndQuantitiesOrderModel,
        Order $orderModel
    ) {
        $this->generalModel = $generalModel;
        $this->productUnitsAndQuantitiesOrderModel = $productUnitsAndQuantitiesOrderModel;
        $this->orderModel = $orderModel;
        $this->helper = $helper;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $orderIds = $observer->getData('order_ids');

        foreach ($orderIds as $orderId) {
            $this->orderModel->load($orderId);
            $orderItems = $this->orderModel->getAllItems();

            foreach ($orderItems as $orderItem) {
                $orderItemId = $orderItem->getId();

                $productId = $orderItem->getData('product_id');
                $productData = $this->helper->getProductParams($productId);

                $this->productUnitsAndQuantitiesOrderModel->setOrderItemId($orderItemId);
                $this->productUnitsAndQuantitiesOrderModel->setPricePer($productData['price_per']);
                $this->productUnitsAndQuantitiesOrderModel->setPricePerDivider($productData['price_per_divider']);

                $this->productUnitsAndQuantitiesOrderModel->save();
                $this->productUnitsAndQuantitiesOrderModel->unsetData();
            }

            $this->orderModel->unsetData();
        }

        return $this;
    }
}
