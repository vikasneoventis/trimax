<?php
namespace Aitoc\AbandonedCartAlertsPro\Model;

class Campaign extends \Magento\Framework\Model\AbstractModel
{

    /**
     * @var \Magento\Quote\Model\ResourceModel\Quote\CollectionFactory
     */
    private $quoteCollection;

    /**
     * @var \Aitoc\AbandonedCartAlertsPro\Model\Alert
     */
    private $alert;

    /**
     * @var \Aitoc\AbandonedCartAlertsPro\Helper\Stoplist
     */
    private $stoplistHelper;

    /**
     * @var \Magento\SalesRule\Model\RuleFactory
     */
    private $ruleFactory;

    /**
     * @var \Magento\SalesRule\Model\CouponFactory
     */
    private $couponFactory;

    /**
     * @var \Magento\SalesRule\Model\Coupon\Massgenerator
     */
    private $couponMassgenerator;

    /**
     * @var \Magento\Customer\Model\CustomerFactory
     */
    private $customerFactory;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var \Aitoc\AbandonedCartAlertsPro\Helper\Data
     */
    private $helper;

    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Aitoc\AbandonedCartAlertsPro\Model\Alert $alert,
        \Aitoc\AbandonedCartAlertsPro\Helper\Stoplist $stoplistHelper,
        \Magento\Quote\Model\ResourceModel\Quote\CollectionFactory $quoteCollectionFactory,
        \Magento\SalesRule\Model\RuleFactory $ruleFactory,
        \Magento\SalesRule\Model\CouponFactory $couponFactory,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Aitoc\AbandonedCartAlertsPro\Helper\Data $helper,
        \Magento\SalesRule\Model\Coupon\Massgenerator $couponMassgenerator,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        $data = []
    ) {
        $this->quoteCollection = $quoteCollectionFactory;
        $this->alert = $alert;
        $this->stoplistHelper = $stoplistHelper;
        $this->ruleFactory = $ruleFactory;
        $this->couponFactory = $couponFactory;
        $this->couponMassgenerator = $couponMassgenerator;
        $this->customerFactory = $customerFactory;
        $this->helper = $helper;
        $this->scopeConfig = $scopeConfig;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Init resource model
     *
     * @return void
     */
    public function _construct()
    {
        $this->_init('Aitoc\AbandonedCartAlertsPro\Model\ResourceModel\Campaign');
    }

    /**
     * Add quote alerts
     *
     * @return $this
     */
    public function addQuoteAlerts()
    {
        $campaign = $this->getData();
        $quotes = $this->getUnprocessedQuotes();
        foreach ($quotes->getItems() as $quote) {
            $quoteProducts = $this->getQuoteProducts($quote, $campaign['exclude_from_alert']);
            if (!$quoteProducts) {
                continue;
            }
            $customerEmail = $quote->getCustomerEmail();
            if (!$this->stoplistHelper->isEmailInStoplist($customerEmail)) {
                $customer = $this->customerFactory->create()
                    ->setWebsiteId($quote->getStore()->getWebsiteId())
                    ->loadByEmail($customerEmail);
                $data = [
                    'store_id' => $quote->getStoreId(),
                    'alert_type' => 'quote',
                    'alert_type_id' => $quote->getId(),
                    'campaign_id' => $campaign['campaign_id'],
                    'customer_id' => $quote->getCustomerId(),
                    'customer_email' => $quote->getCustomerEmail(),
                    'customer_firstname' => $quote->getCustomerFirstname(),
                    'customer_middlename' => $quote->getCustomerMillename(),
                    'customer_lastname' => $quote->getCustomerLastname(),
                    'products' => serialize($quoteProducts),
                    'code' => md5(uniqid()),
                    'sales_rule_id' => $this->getSalesRuleId($customer, $campaign)
                ];
                $this->alert->addData($data)->save();
                $this->alert->unsetData();
            }
        }

        return $this;
    }

    /**
     * Add order alerts
     *
     * @return $this
     */
    public function addOrderAlerts()
    {
        return $this;
    }

    /**
     * Get unprocessed quotes
     *
     * @param array $conditions
     *
     * @return mixed
     */
    public function getUnprocessedQuotes()
    {
        $quotes = $this->quoteCollection->create()
            ->setPageSize(20)
            ->setCurPage(1)
            ->addOrder('main_table.created_at', \Magento\Framework\Data\Collection::SORT_ORDER_DESC);

        if ($this->getProcessedQuotes()) {
            $quotes->addFieldToFilter('main_table.entity_id', ['nin' => $this->getProcessedQuotes()]);
        }
        $abandonedPeriod = $this->scopeConfig->getValue(
            'abandoned_cart_alerts_pro/general_settings/abandoned_period',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        // minimal abandoned period - 1 hour
        $abandonedPeriod = min($abandonedPeriod, 1);
        $quotes->addFieldToFilter('main_table.is_active', 1);
        $quotes->addFieldToFilter('main_table.customer_email', ['notnull' => true]);
        $quotes->getSelect()
            ->where(
                'main_table.created_at < NOW() - INTERVAL ? HOUR',
                $abandonedPeriod
            );

        return $quotes->load();
    }

    /**
     * Get list of processed quotes
     *
     * @return array
     */
    public function getProcessedQuotes()
    {
        $readAdapter = $this->getResource()->getConnection();
        $processedQuoteIds = $readAdapter->select()
            ->from(
                ['reminder' => $this->getResource()->getTable('aitoc_abandoned_cart_alerts_pro_alert')],
                ['alert_type_id']
            )
            ->where('alert_type = ?', 'quote')
            ->query()
            ->fetchAll(\Zend_Db::FETCH_COLUMN);

        return $processedQuoteIds;
    }

    /**
     * Get list of quote products
     *
     * @param $order
     *
     * @return array
     */
    public function getQuoteProducts($quote, $checkAvailability)
    {
        $products = [];
        $items = $quote->getAllItems();
        foreach ($items as $item) {
            $productItem = $item->getProduct();
            if ($checkAvailability && $productItem->getIsSalable() === false) {
                continue;
            }
            if (!$item->getParentItemId()) {
                $product['id'] = $item->getId();
                $product['name'] = $item->getName();
                $product['url'] = $productItem->getProductUrl();
                $products[] = $product;
            }
        }

        return $products;
    }

    /**
     * Get sales rule id
     *
     * @param $customer
     * @param $campaign
     *
     * @return mixed
     */
    public function getSalesRuleId($customer, $campaign)
    {
        return $this->generateIndividualRule($customer, $campaign)->getId();
    }

    /**
     * Generate individual sales rule
     *
     * @param $customer
     * @param $campaign
     *
     * @return \Magento\Framework\Object
     */
    public function generateIndividualRule($customer, $campaign)
    {
        if ($customer->getId()) {
            $ruleData = [
                'name'                => __('Individual sales rule for %1', $customer->getEmail()),
                'is_active'           => 1,
                'simple_action'       => 'by_percent',
                'discount_amount'     => $campaign['discount_amount'],
                'coupon_type'         => 2,
                'use_auto_generation' => 1,
                'website_ids'         => [$customer->getWebsiteId()],
                'customer_group_ids'  => $this->helper->getCustomerGroups()
            ];

            $rule = $this->ruleFactory->create()
                ->addData($ruleData)
                ->save();

            $code = $this->couponMassgenerator
                ->setLength(12)
                ->generateCode();
            $couponData = [
                'rule_id'            => $rule->getId(),
                'code'               => $code,
                'usage_limit'        => 1,
                'usage_per_customer' => 1,
                'is_primary'         => 1,
                'type'               => \Magento\SalesRule\Helper\Coupon::COUPON_TYPE_SPECIFIC_AUTOGENERATED
            ];

            $coupon = $this->couponFactory->create()
                ->addData($couponData)
                ->save();
        } else {
            $rule = new \Magento\Framework\DataObject();
        }

        return $rule;
    }
}
