<?php
namespace Aitoc\AbandonedCartAlertsPro\Model\Plugin\Customer;

class AccountManagement
{
    /**
     * @var \Magento\Quote\Model\QuoteFactory
     */
    private $quote;

    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    private $request;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    private $checkoutSession;

    /**
     * @var \Magento\Framework\App\Route\ConfigInterface
     */
    private $routeConfig;

    /**
     * @var \Aitoc\AbandonedCartAlertsPro\Helper\Data
     */
    private $helper;
    
    /**
     * Class constructor
     *
     * @param \Magento\Quote\Model\QuoteFactory $quoteFactory
     * @param \Magento\Framework\App\RequestInterface $request
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magento\Framework\App\Route\ConfigInterface $routeConfig
     * @param \Aitoc\AbandonedCartAlertsPro\Helper\Data $helper
     */
    public function __construct(
        \Magento\Quote\Model\QuoteFactory $quoteFactory,
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Framework\App\Route\ConfigInterface $routeConfig,
        \Aitoc\AbandonedCartAlertsPro\Helper\Data $helper
    ) {
        $this->quote = $quoteFactory;
        $this->request = $request;
        $this->checkoutSession = $checkoutSession;
        $this->routeConfig = $routeConfig;
        $this->helper = $helper;
    }

    /**
     * Save guest email
     *
     * @param \Magento\Customer\Model\AccountManagement $accountManagement
     * @param callable $proceed
     * @param $email
     * @return mixed
     */
    public function aroundIsEmailAvailable(
        \Magento\Customer\Model\AccountManagement $accountManagement,
        \Closure $proceed,
        $email
    ) {
        $result = $proceed($email);

        $checkoutFrontName = $this->routeConfig->getRouteFrontName('checkout', 'frontend');
        $referer = $this->request->getServer('HTTP_REFERER');
        $url = $this->helper->getPathUrl($referer);
        if (strpos($url, '/' . $checkoutFrontName . '/') !== false) {
            $quoteId = $this->checkoutSession->getQuoteId();
            if ($quoteId) {
                $quote = $this->quote->create()->load($quoteId);
                $quote->setCustomerEmail($email)->save();
            }
        }

        return $result;
    }
}
