<?php
namespace Aitoc\AbandonedCartAlertsPro\Helper;

class Statistic extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var \Magento\Quote\Model\ResourceModel\Quote\CollectionFactory
     */
    private $quoteCollection;

    /**
     * @var \Magento\Sales\Model\ResourceModel\Order\CollectionFactory
     */
    private $orderCollection;

    /**
     * @var \Aitoc\AbandonedCartAlertsPro\Model\ResourceModel\Statistic\CollectionFactory
     */
    private $statisticCollection;

    /**
     * @var \Magento\Directory\Model\CurrencyFactory
     */
    private $currencyFactory;

    /**
     * Class constructor
     *
     * @param \Magento\Quote\Model\ResourceModel\Quote\CollectionFactory $quoteCollection
     * @param \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollection
     * @param \Aitoc\AbandonedCartAlertsPro\Model\ResourceModel\Statistic\CollectionFactory $statisticCollection
     * @param \Magento\Directory\Model\CurrencyFactory $currencyFactory
     */
    public function __construct(
        \Magento\Quote\Model\ResourceModel\Quote\CollectionFactory $quoteCollection,
        \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollection,
        \Aitoc\AbandonedCartAlertsPro\Model\ResourceModel\Statistic\CollectionFactory $statisticCollection,
        \Magento\Directory\Model\CurrencyFactory $currencyFactory
    ) {
        $this->quoteCollection = $quoteCollection;
        $this->orderCollection = $orderCollection;
        $this->statisticCollection = $statisticCollection;
        $this->currencyFactory = $currencyFactory;
    }

    /**
     * Get total number of placed order
     *
     * @return int
     */
    public function getTotalOrdersPlaced()
    {
        return (int) $this->orderCollection->create()
            ->getSize();
    }

    /**
     * Get total number of completed orders
     *
     * @return int
     */
    public function getTotalOrdersCompleted()
    {
        return (int) $this->orderCollection->create()
            ->addFieldToFilter('status', 'complete')
            ->getSize();
    }

    /**
     * Get completed orders value by currencies
     *
     * @return string
     */
    public function getCompletedOrdersValue()
    {
        $currencies = $this->getCurrencies();

        $value = [];
        foreach ($currencies as $currency) {
            $orders = $this->orderCollection->create()
                ->addFieldToFilter('status', 'complete')
                ->addFieldToFilter('order_currency_code', $currency);

            $amount = 0;
            foreach ($orders as $order) {
                $amount += $order->getGrandTotal();
            }

            $value[] = $currency . ' ' . $amount;
        }

        return implode(', ', $value);
    }

    /**
     * Get order completion rate
     *
     * @return int
     */
    public function getOrderCompletionRate()
    {
        $rate = 0;
        if ($this->getTotalOrdersPlaced() > 0) {
            $rate = (int) round($this->getTotalOrdersCompleted() * (100 / $this->getTotalOrdersPlaced()), 2);
        }
        return $rate;
    }

    /**
     * Get total number of recovered carts
     *
     * @return int
     */
    public function getRecoveredCartsQty()
    {
        return (int) $this->statisticCollection->create()
            ->getSize();
    }

    /**
     * Get total value of recovered carts
     *
     * @return string
     */
    public function getRecoveredCartsValue()
    {
        $currencies = $this->getCurrencies();

        $value = [];
        foreach ($currencies as $currency) {
            $statistics = $this->statisticCollection->create()
                ->addFieldToFilter('order_currency_code', $currency);

            $amount = 0;
            foreach ($statistics as $statistic) {
                $amount += $statistic->getOrderGrandTotal();
            }

            $value[] = $currency . ' ' . $amount;
        }

        return implode(', ', $value);
    }

    /**
     * Get all allowed currencies
     *
     * @return array
     */
    public function getCurrencies()
    {
        return $this->currencyFactory->create()->getConfigAllowCurrencies();
    }
}
