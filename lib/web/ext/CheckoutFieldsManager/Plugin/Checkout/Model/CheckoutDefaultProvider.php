<?php
/**
 * Copyright © 2016 Aitoc. All rights reserved.
 */
namespace Aitoc\CheckoutFieldsManager\Plugin\Checkout\Model;

class CheckoutDefaultProvider
{
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scope;

    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $urlBuilder;

    /**
     * CheckoutDefaultProvider constructor.
     *
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scope
     * @param \Magento\Framework\UrlInterface                    $urlBuilder
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scope,
        \Magento\Framework\UrlInterface $urlBuilder
    ) {
        $this->urlBuilder = $urlBuilder;
        $this->scope      = $scope;
    }

    /**
     * @param \Magento\Checkout\Model\DefaultConfigProvider $object
     * @param                                               $config
     *
     * @return mixed
     */
    public function afterGetConfig(\Magento\Checkout\Model\DefaultConfigProvider $object, $config)
    {
        $status = $this->scope->getValue('checkoutfieldsmanager/general/read_cart_in_checkout');
        $config['additional_cfm'] =
            [
                'read_cart_in_checkout' => $status,
                'updateItemQtyUrl'      => $this->getUpdateItemQtyUrl(),
                'removeItemUrl'         => $this->getRemoveItemUrl(),
                'updateConfig'          => $this->getUpdateConfig()
            ];

        return $config;
    }

    /**
     * Get update cart item url
     *
     * @return string
     * @codeCoverageIgnore
     */
    public function getUpdateItemQtyUrl()
    {
        return $this->getUrl('checkout/sidebar/updateItemQty');
    }

    /**
     * Get remove cart item url
     *
     * @return string
     * @codeCoverageIgnore
     */
    public function getRemoveItemUrl()
    {
        return $this->getUrl('checkout/sidebar/removeItem');
    }

    /**
     * Get update cart item url
     *
     * @return string
     */
    public function getUpdateConfig()
    {
        return $this->getUrl('aitoccheckoutfieldsmanager/updatecart/index');
    }

    /**
     * @param       $route
     * @param array $params
     *
     * @return string
     */
    protected function getUrl($route, $params = [])
    {
        return $this->urlBuilder->getUrl($route, $params);
    }
}
