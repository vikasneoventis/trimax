<?php
/**
 * Copyright © 2016 Aitoc. All rights reserved.
 */
namespace Aitoc\CheckoutFieldsManager\Plugin\Checkout\Controller\Cart;

class Index
{
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    private $scope;

    /**
     * @var \Magento\Framework\UrlInterface
     */
    private $url;

    /**
     * @var \Magento\Framework\App\Response\Http
     */
    private $response;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    private $checkoutSession;

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    private $resultPageFactory;

    /**
     * Index constructor.
     *
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scope
     * @param \Magento\Framework\UrlInterface                    $url
     * @param \Magento\Framework\App\Response\Http               $response
     * @param \Magento\Checkout\Model\Session                    $checkoutSession
     * @param \Magento\Framework\View\Result\PageFactory         $resultPageFactory
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scope,
        \Magento\Framework\UrlInterface $url,
        \Magento\Framework\App\Response\Http $response,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
    ) {
        $this->scope = $scope;
        $this->url = $url;
        $this->response = $response;
        $this->checkoutSession = $checkoutSession;
        $this->resultPageFactory = $resultPageFactory;
    }

    /**
     * @param \Magento\Checkout\Controller\Cart\Index $object
     *
     * @return void
     */
    public function beforeExecute(\Magento\Checkout\Controller\Cart\Index $object)
    {
        $status = $this->scope->getValue('checkoutfieldsmanager/general/disable_cart');
        if ($status) {
            $quote = $this->checkoutSession->getQuote();
            $redirectUrl = $this->url->getUrl('checkout');
            /** if no items in checkout, redirect by "continue shopping" link */
            if (!$quote->hasItems() || $quote->getHasError() || !$quote->validateMinimumAmount()) {
                $redirectUrl = $this->resultPageFactory->create()
                    ->getLayout()
                    ->getBlock('checkout.cart')
                    ->getContinueShoppingUrl();
            }
            $this->response->setRedirect($redirectUrl);
        }
    }
}
