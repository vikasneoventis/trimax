<?php
/**
 * Copyright © 2016 Aitoc. All rights reserved.
 */
namespace Aitoc\OrdersExportImport\Block\Adminhtml\Profile\Grid\Renderer\Action;

use Magento\Store\Api\StoreResolverInterface;

/**
 * Class UrlBuilder
 *
 * @package Aitoc\OrdersExportImport\Block\Adminhtml\Profile\Grid\Renderer\Action
 */
class UrlBuilder
{
    /**
     * @var \Magento\Framework\UrlInterface
     */
    public $frontendUrlBuilder;

    /**
     * @param \Magento\Framework\UrlInterface $frontendUrlBuilder
     */
    public function __construct(\Magento\Framework\UrlInterface $frontendUrlBuilder)
    {
        $this->frontendUrlBuilder = $frontendUrlBuilder;
    }

    /**
     * Get action url
     *
     * @param string $routePath
     * @param string $scope
     * @param string $store
     * @return string
     */
    public function getUrl($routePath, $scope, $store)
    {
        $this->frontendUrlBuilder->setScope($scope);
        $href = $this->frontendUrlBuilder->getUrl(
            $routePath,
            [
                '_current' => false,
                '_query' => [StoreResolverInterface::PARAM_NAME => $store]
            ]
        );

        return $href;
    }
}
