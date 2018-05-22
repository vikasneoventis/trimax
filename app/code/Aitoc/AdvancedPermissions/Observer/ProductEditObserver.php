<?php
/**
 * Copyright © 2016 Aitoc. All rights reserved.
 */
namespace Aitoc\AdvancedPermissions\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer as EventObserver;

class ProductEditObserver implements ObserverInterface
{
    /**
     * @var \Aitoc\AdvancedPermissions\Helper\Data
     */
    protected $helper;
    
    /**
     * @var \Magento\Framework\App\ResponseFactory
     */
    protected $responseFactory;
    
    /**
     * @var \Magento\Framework\UrlInterface
     */
    protected $url;
    
    /**
     * InventoryObserver constructor.
     *
     * @param \Aitoc\AdvancedPermissions\Helper\Data $helper
     * @param \Magento\Framework\App\ResponseFactory $responseFactory
     * @param \Magento\Framework\UrlInterface $url
     */
    public function __construct(
        \Aitoc\AdvancedPermissions\Helper\Data $helper,
        \Magento\Framework\App\ResponseFactory $responseFactory,
        \Magento\Framework\UrlInterface $url
    ) {
        $this->helper = $helper;
        $this->responseFactory = $responseFactory;
        $this->url = $url;
    }

    /**
     * @param EventObserver $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if (!$this->helper->isAdvancedPermissionEnabled()) {
            return;
        }
        
        $product            = $observer->getProduct();
        $productCategoryIds = $product->getCategoryIds();
        $roleCategoryIds    = $this->helper->getCategoryIds();
        $allowedStoreIds    = $this->helper->getAllowedStoreIds();
        
        if (array_intersect($allowedStoreIds, $product->getStoreIds())) {
            if (!count($roleCategoryIds) ||
                array_intersect($roleCategoryIds, $productCategoryIds) ||
                $this->helper->getRole()->getAllowNullCategory()
            ) {
                return;
            }
        }
        
        $redirectUrl = $this->url->getUrl('admin/dashboard/index');
        $this->responseFactory->create()->setRedirect($redirectUrl)->sendResponse();
        die();
    }
}
