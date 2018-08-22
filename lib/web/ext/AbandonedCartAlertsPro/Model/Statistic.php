<?php
namespace Aitoc\AbandonedCartAlertsPro\Model;

class Statistic extends \Magento\Framework\Model\AbstractModel
{
    /**
     * @var \Aitoc\AbandonedCartAlertsPro\Model\StatisticFactory
     */
    public $statisticFactory;

    /**
     * Class constructor
     *
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Aitoc\AbandonedCartAlertsPro\Model\StatisticFactory $statisticFactory
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Aitoc\AbandonedCartAlertsPro\Model\StatisticFactory $statisticFactory,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->statisticFactory = $statisticFactory;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Init resource model
     *
     * @return void
     */
    public function _construct()
    {
        $this->_init('Aitoc\AbandonedCartAlertsPro\Model\ResourceModel\Statistic');
    }

    /**
     * Save statistic
     *
     * @param $alert
     * @param $quote
     * @return mixed
     */
    public function saveStatistic($alert, $quote)
    {
        $now = new \DateTime();
        $data = [
            'recovered_at' => $now->format(\Magento\Framework\Stdlib\DateTime::DATETIME_PHP_FORMAT),
            'alert_id' => $alert->getId(),
            'campaign_id' => $alert->getCampaignId(),
            'quote_id' => $quote->getId(),
            'quote_grand_total' => $quote->getGrandTotal()
        ];

        $statistic = $this->statisticFactory->create()
            ->addData($data)
            ->save();

        return $statistic;
    }
}
