<?php
namespace Aitoc\AbandonedCartAlertsPro\Model;

class Cron
{
    /**
     * @var ResourceModel\Campaign\CollectionFactory
     */
    private $campaignCollection;

    /**
     * @var ResourceModel\Alert\CollectionFactory
     */
    private $alertCollection;

    /**
     * Class constructor
     *
     * @param ResourceModel\Campaign\CollectionFactory $campaignCollection
     * @param ResourceModel\Alert\CollectionFactory $alertCollection
     */
    public function __construct(
        \Aitoc\AbandonedCartAlertsPro\Model\ResourceModel\Campaign\CollectionFactory $campaignCollection,
        \Aitoc\AbandonedCartAlertsPro\Model\ResourceModel\Alert\CollectionFactory $alertCollection
    ) {
        $this->campaignCollection = $campaignCollection;
        $this->alertCollection = $alertCollection;
    }

    /**
     * Generate al for quoteserts
     *
     * @return $this
     */
    public function generateQuoteAlerts()
    {
        $collection = $this->campaignCollection->create()
            ->addFieldToFilter('status', 'enabled')
            ->load();

        $collection->walk('addQuoteAlerts');

        return $this;
    }

    /**
     * Generate alerts for orders
     *
     * @return $this
     */
    public function generateOrderAlerts()
    {
        return $this;
    }

    /**
     * Process generated alerts
     *
     * @return $this
     */
    public function processAlerts()
    {
        $collection = $this->alertCollection->create()
            ->setPageSize(20)
            ->setCurPage(1)
            ->addFieldToFilter('main_table.status', 'pending')
            ->join(
                ['campaign' => 'aitoc_abandoned_cart_alerts_pro_campaign'],
                'main_table.campaign_id = campaign.campaign_id',
                ['send_interval'=>'campaign.send_interval']
            );
        $collection->getSelect()
            ->where(
                'main_table.created_at < NOW() - INTERVAL send_interval HOUR'
            );
        $collection->load();
        $collection->walk('sendAlerts', [20]);

        return $this;
    }
}
