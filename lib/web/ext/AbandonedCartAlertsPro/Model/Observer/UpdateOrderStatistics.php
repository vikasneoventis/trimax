<?php
namespace Aitoc\AbandonedCartAlertsPro\Model\Observer;

use Magento\Framework\Event\ObserverInterface;

class UpdateOrderStatistics implements ObserverInterface
{
    /**
     * @var \Aitoc\AbandonedCartAlertsPro\Model\StatisticFactory
     */
    public $statisticFactory;

    /**
     * Class constructor
     *
     * @param StatisticFactory $statisticFactory
     */
    public function __construct(
        \Aitoc\AbandonedCartAlertsPro\Model\StatisticFactory $statisticFactory
    ) {
        $this->statisticFactory = $statisticFactory;
    }

    /**
     * Update order statistics
     *
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $order = $observer->getEvent()->getOrder();
        $statistic = $this->statisticFactory->create()
            ->load($order->getQuoteId(), 'quote_id');
        if ($statistic->getId()) {
            $data = [
                'order_id' => $order->getId(),
                'order_grand_total' => $order->getGrandTotal(),
                'order_created_at' => $order->getCreatedAt(),
                'order_currency_code' => $order->getOrderCurrencyCode()
            ];
            $statistic->addData($data)
                ->save();
        }
    }
}
