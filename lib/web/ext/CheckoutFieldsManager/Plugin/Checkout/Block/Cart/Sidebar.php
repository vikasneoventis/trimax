<?php
/**
 * Copyright © 2016 Aitoc. All rights reserved.
 */
namespace Aitoc\CheckoutFieldsManager\Plugin\Checkout\Block\Cart;

class Sidebar
{
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scope;

    /**
     * Sidebar constructor.
     *
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scope
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scope
    ) {
        $this->scope      = $scope;
    }

    /**
     * @param \Magento\Checkout\Block\Cart\Sidebar $object
     * @param                                      $config
     *
     * @return mixed
     */
    public function afterGetConfig(\Magento\Checkout\Block\Cart\Sidebar $object, $config)
    {
        $status = $this->scope->getValue('checkoutfieldsmanager/general/disable_cart');
        $config['additional_cfm'] = ['disable_cart' => $status];

        return $config;
    }
}
