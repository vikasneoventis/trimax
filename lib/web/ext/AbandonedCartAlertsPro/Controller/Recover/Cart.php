<?php
namespace Aitoc\AbandonedCartAlertsPro\Controller\Recover;

class Cart extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Aitoc\AbandonedCartAlertsPro\Model\AlertFactory
     */
    private $alertFactory;

    /**
     * @var \Aitoc\AbandonedCartAlertsPro\Model\Statistic
     */
    private $statistic;

    /**
     * @var \Magento\Quote\Model\QuoteFactory
     */
    private $quoteFactory;

    /**
     * @var \Magento\Quote\Model\QuoteRepository
     */
    private $quoteRepository;

    /**
     * @var \Magento\Customer\Model\Session
     */
    private $customerSession;

    /**
     * @var \Magento\Customer\Model\CustomerFactory
     */
    private $customerFactory;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    private $cartSession;

    /**
     * @var \Magento\Framework\Stdlib\CookieManagerInterface
     */
    private $cookieManager;

    /**
     * @var \Magento\Framework\Stdlib\Cookie\CookieMetadataFactory
     */
    private $cookieMetadataFactory;
    /**
     * Class constructor
     *
     * @param \Aitoc\AbandonedCartAlertsPro\Model\AlertFactory $alertFactory
     * @param \Aitoc\AbandonedCartAlertsPro\Model\Statistic $statistic
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Customer\Model\CustomerFactory $customerFactory
     * @param \Magento\Quote\Model\QuoteFactory $quoteFactory
     * @param \Magento\Quote\Model\QuoteRepository $quoteRepository
     * @param \Magento\Checkout\Model\Session $cartSession
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\Stdlib\CookieManagerInterface $cookieManager
     * @param \Magento\Framework\Stdlib\Cookie\CookieMetadataFactory $cookieMetadataFactory
     */
    public function __construct(
        \Aitoc\AbandonedCartAlertsPro\Model\AlertFactory $alertFactory,
        \Aitoc\AbandonedCartAlertsPro\Model\Statistic $statistic,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Quote\Model\QuoteFactory $quoteFactory,
        \Magento\Quote\Model\QuoteRepository $quoteRepository,
        \Magento\Checkout\Model\Session $cartSession,
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Stdlib\CookieManagerInterface $cookieManager,
        \Magento\Framework\Stdlib\Cookie\CookieMetadataFactory $cookieMetadataFactory
    ) {
        $this->alertFactory = $alertFactory;
        $this->statistic = $statistic;
        $this->quoteFactory = $quoteFactory;
        $this->quoteRepository = $quoteRepository;
        $this->customerSession = $customerSession;
        $this->customerFactory = $customerFactory;
        $this->cartSession = $cartSession;
        $this->cookieManager = $cookieManager;
        $this->cookieMetadataFactory = $cookieMetadataFactory;
        parent::__construct($context);
    }

    /**
     * Recover abandoned cart
     *
     * @return \Magento\Framework\App\ResponseInterface
     */
    public function execute()
    {
        $code = $this->getRequest()->getParam('code');
        $alertId = $this->getRequest()->getParam('id');

        if ($code) {
            $alert = $this->alertFactory->create()->load($alertId);
            if ($code == $alert->getCode()) {
                $quote = $this->quoteFactory->create()->load($alert->getAlertTypeId());
                if (!$this->customerSession->isLoggedIn()) {
                    $customer = $this->customerFactory->create()
                        ->load($alert->getCustomerId());
                    if ($customer->getId()) {
                        $this->customerSession->loginById($customer->getId());
                    }
                }
                $this->cartSession->replaceQuote($quote);
                if ($this->statistic->load($alertId, 'alert_id')->getStatisticId() === null) {
                    $this->statistic->saveStatistic($alert, $quote);
                }
            }
        }

        $publicCookieMetadata = $this->cookieMetadataFactory->createPublicCookieMetadata()
            ->setDuration(\Magento\Framework\App\PageCache\Version::COOKIE_PERIOD)
            ->setPath('/')
            ->setHttpOnly(false);
        $this->cookieManager->setPublicCookie(
            \Magento\Framework\App\PageCache\Version::COOKIE_NAME,
            md5(rand() . time()),
            $publicCookieMetadata
        );

        return $this->_redirect('checkout/cart');
    }
}
