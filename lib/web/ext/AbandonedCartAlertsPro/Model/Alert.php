<?php
namespace Aitoc\AbandonedCartAlertsPro\Model;

class Alert extends \Magento\Framework\Model\AbstractModel
{
    /**
     * Alert statuses
     */
    const STATUS_PENDING = 'pending';

    const STATUS_SENT = 'sent';

    const STATUS_FAILED = 'failed';

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    private $date;

    /**
     * @var \Magento\Framework\Mail\Template\TransportBuilder
     */
    private $transportBuilder;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var \Aitoc\AbandonedCartAlertsPro\Model\CampaignFactory
     */
    private $campaignFactory;

    /**
     * @var \Magento\Framework\UrlInterface
     */
    private $urlBuilder;

    /**
     * @var \Magento\SalesRule\Model\RuleFactory
     */
    private $salesRule;

    /**
     * Class constructor
     *
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $date
     * @param \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param CampaignFactory $campaignFactory
     * @param \Magento\Framework\UrlInterface $urlBuilder
     * @param \Magento\SalesRule\Model\RuleFactory $salesRule
     * @param \Magento\Framework\Model\ResourceModel\AbstractResource $resource
     * @param \Magento\Framework\Data\Collection\AbstractDb $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        \Magento\Framework\Mail\Template\TransportBuilder $transportBuilder,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Aitoc\AbandonedCartAlertsPro\Model\CampaignFactory $campaignFactory,
        \Magento\Framework\UrlInterface $urlBuilder,
        \Magento\SalesRule\Model\RuleFactory $salesRule,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        $data = []
    ) {
        $this->date = $date;
        $this->transportBuilder = $transportBuilder;
        $this->storeManager = $storeManager;
        $this->campaignFactory = $campaignFactory;
        $this->urlBuilder = $urlBuilder;
        $this->salesRule = $salesRule;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Init resource model
     *
     * @return void
     */
    public function _construct()
    {
        $this->_init('Aitoc\AbandonedCartAlertsPro\Model\ResourceModel\Alert');
    }

    /**
     * Send alerts
     *
     * @return $this
     */
    public function sendAlerts()
    {
        $alertData = $this->getData();
        $campaign = $this->loadCampaignById($alertData['campaign_id']);
        $alertSenderContact = $campaign->getSender();
        $alertEmailTemplateId = $campaign->getTemplateId();
        $coupon = new \Magento\Framework\DataObject();
        $products = unserialize($alertData['products']);
        $quoteProducts = $this->getProductsHtml($products);
        if ($alertData['sales_rule_id']) {
            $salesRule = $this->salesRule->create()
                ->load($alertData['sales_rule_id']);

            if ($salesRule->getPrimaryCoupon()->getCode()) {
                $coupon
                    ->addData([
                        'coupon_code' => $salesRule->getPrimaryCoupon()->getCode(),
                        'discount_amount' => (int) $campaign->getDiscountAmount() . '%',
                        'expiry_days' => $campaign->getExpiryDays()
                    ]);
            }
        }

        $transport = $this->transportBuilder
            ->setTemplateIdentifier(
                $alertEmailTemplateId
            )
            ->setTemplateOptions(
                [
                    'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
                    'store' => $this->storeManager->getStore()->getStoreId()
                ]
            )
            ->setFrom(
                $alertSenderContact
            )
            ->setTemplateVars(
                [
                    'customer_name' => $alertData['customer_firstname'],
                    'quote_products' => $quoteProducts,
                    'real_quote' => false,
                    'alert_code' => $alertData['code'],
                    'alert_id' => $alertData['alert_id'],
                    'coupon' => $coupon
                ]
            )
            ->addTo(
                $this->getCustomerEmail(),
                $this->getCustomerFirstname()
            )
            ->getTransport();

        try {
            $transport->sendMessage();
        } catch (\Magento\Framework\Exception\MailException $e) {
            $this->setStatus(self::STATUS_FAILED)
                ->save();
            $this->_logger->critical($e);
            return $this;
        }

        $timestamp = $this->date->gmtDate();
        $this->setStatus(self::STATUS_SENT)
            ->setSentAt($timestamp)
            ->save();

        return $this;
    }

    /**
     * Get html links of products
     *
     * @param array $products
     * @return string
     */
    public function getProductsHtml($products)
    {
        $orderProducts = '';
        if (is_array($products)) {
            foreach ($products as $product) {
                $orderProducts .= '<a href="' . htmlspecialchars($product['url']);
                $orderProducts .= '">' . htmlspecialchars($product['name']) . '</a><br/>';
            }
        }

        return $orderProducts;
    }

    /**
     * Load campaign by id
     *
     * @param $campaignId
     * @return object
     */
    public function loadCampaignById($campaignId)
    {
        return $this->campaignFactory->create()->load($campaignId);
    }
}
