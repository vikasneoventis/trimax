<?php
/**
 * Copyright © 2016 Aitoc. All rights reserved.
 */

namespace Aitoc\PreOrders\Observer;

use Magento\Framework\Event\ObserverInterface;

class AfterSaveProduct implements ObserverInterface
{
    /**
     * @var \Magento\Sales\Model\ResourceModel\Order\Collection
     */
    protected $_collection;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scope;

    /**
     * @var \Magento\Customer\Model\ResourceModel\CustomerRepository
     */
    protected $_customer;

    /**
     * AfterSaveProduct constructor.
     * @param \Magento\Sales\Model\ResourceModel\Order\Collection $collection
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scope
     * @param \Magento\Customer\Model\ResourceModel\CustomerRepository $customer
     */
    public function __construct(
        \Magento\Sales\Model\ResourceModel\Order\Collection $collection,
        \Magento\Framework\App\Config\ScopeConfigInterface $scope,
        \Magento\Customer\Model\ResourceModel\CustomerRepository $customer
    ) {
        $this->_collection = $collection;
        $this->_scope = $scope;
        $this->_customer = $customer;

    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $event = $observer->getEvent();
        $product = $event->getProduct();
        $preorderStatus = 2;
        $needAlert = false;
        $item = $product->getStockData();
        if (!array_key_exists('is_in_stock', $item) || !array_key_exists('backorders', $item))
            return;
        if ($item['is_in_stock'] && $item['backorders'] < 30) {
            $preorderStatus = 0;
            $statuses[] = \Aitoc\PreOrders\Model\Order::STATE_PENDING_PREORDER;
            $statuses[] = \Aitoc\PreOrders\Model\Order::STATE_PROCESSING_PREORDER;
        } else {
            if ($item['backorders'] >= 30) {
                $preorderStatus = 1;
                $needAlert = true;
                $statuses[] = \Aitoc\PreOrders\Model\Order::STATE_PROCESSING;
                $statuses[] = \Aitoc\PreOrders\Model\Order::STATE_PENDING;
            }
        }
        if ($preorderStatus < 2) {
            $this->_collection->addFieldToFilter('status_preorder', ['in' => $statuses]);

            $prealias = 'main_table';

            $this->_collection->getSelect()->join(
                [
                    'oi' => $this->_collection
                        ->getTable('sales_order_item')
                ],
                'oi.order_id = ' . $prealias . '.entity_id',
                ['oi.product_id', 'oi.sku']
            )
                ->where('oi.product_id = ?', $product->getId())
                ->group($prealias . '.entity_id');
            foreach ($this->_collection->getItems() as $order) {
                if ($needAlert && $order->getData('customer_id')
                    && $this->_scope->getValue(
                        'preorder/preorder_alert/send_email'
                    )
                ) {
                    $model = \Magento\Framework\App\ObjectManager::getInstance()->get('Aitoc\PreOrders\Model\Preorder')
                        ->setCustomerId($order->getData('customer_id'))
                        ->setProductId($product->getId())
                        ->setWebsiteId(
                            \Magento\Framework\App\ObjectManager::getInstance()->get(
                                'Magento\Store\Model\StoreManagerInterface'
                            )
                                ->getStore()
                                ->getWebsiteId()
                        );
                    $model->save();
                }
                $this->setStatuses($order);
                $order->save();
            }
        }
    }

    /**
     * Set statuses for order
     *
     * @param $order
     */
    public function setStatuses($order)
    {
        list($orderStatusNew, $orderStatusPreorderNew) = $order->changeStatuses();
        $order->setData("status", $orderStatusNew);
        $order->setData("status_preorder", $orderStatusPreorderNew);
    }
}
