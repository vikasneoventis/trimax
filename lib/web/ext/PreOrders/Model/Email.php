<?php
/**
 * Copyright © 2016 Aitoc. All rights reserved.
 */
namespace Aitoc\PreOrders\Model;

class Email extends \Magento\ProductAlert\Model\Email
{

    const AITOC_PREORDER_TEMPLATE = 'preorder/preorder_alert/email_preorder_template';

    /**
     * @var
     */
    protected $_preorderBlock;

    /**
     * @var array
     */
    protected $_preorderProducts = [];

    /**
     * Get block for alert with pre-orders
     *
     * @return \Magento\Framework\View\Element\AbstractBlock
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _getPreorderBlock()
    {
        if (is_null($this->_preorderBlock)) {
            $this->_preorderBlock = $this->_productAlertData->createBlock('Aitoc\PreOrders\Block\Email\Stock');
        }

        return $this->_preorderBlock;
    }

    /**
     * Add product with pre-order
     *
     * @param \Magento\Catalog\Model\Product $product
     * @return $this
     */
    public function addPreorderProduct(\Magento\Catalog\Model\Product $product)
    {
        $this->_preorderProducts[$product->getId()] = $product;

        return $this;
    }

    /**
     * Send E-mail
     *
     * @return bool
     * @throws \Exception
     */
    public function send()
    {
        if ($this->_website === null || $this->_customer === null) {
            return false;
        }
        if ($this->_type == 'price' && count($this->_priceProducts) == 0 || $this->_type == 'stock' && count($this->_stockProducts) == 0 || $this->_type == 'preorder' && count($this->_preorderProducts) == 0) {
            return false;
        }
        if (!$this->_website->getDefaultGroup() || !$this->_website->getDefaultGroup()->getDefaultStore()) {
            return false;
        }
        if ($this->_customer->getStoreId() > 0) {
            $store = $this->_storeManager->getStore($this->_customer->getStoreId());
        } else {
            $store = $this->_website->getDefaultStore();
        }
        $storeId = $store->getId();

        if ($this->_type == 'price' && !$this->_scopeConfig->getValue(\Magento\ProductAlert\Model\Email::XML_PATH_EMAIL_PRICE_TEMPLATE, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId)) {
            return false;
        } elseif ($this->_type == 'stock' && !$this->_scopeConfig->getValue(\Magento\ProductAlert\Model\Email::XML_PATH_EMAIL_STOCK_TEMPLATE, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId)) {
            return false;
        } elseif ($this->_type == 'preorder' && !$this->_scopeConfig->getValue(self::AITOC_PREORDER_TEMPLATE, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId)) {
            return false;
        }
        if ($this->_type != 'price' && $this->_type != 'stock' && $this->_type != 'preorder') {
            return false;
        }

        $this->_appEmulation->startEnvironmentEmulation($storeId);

        if ($this->_type == 'price') {
            $this->_getPriceBlock()->setStore($store)->reset();
            foreach ($this->_priceProducts as $product) {
                $product->setCustomerGroupId($this->_customer->getGroupId());
                $this->_getPriceBlock()->addProduct($product);
            }
            $block = $this->_getPriceBlock();
            $templateId = $this->_scopeConfig->getValue(\Magento\ProductAlert\Model\Email::XML_PATH_EMAIL_PRICE_TEMPLATE, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
        } elseif ($this->_type == 'stock') {
            $this->_getStockBlock()->setStore($store)->reset();
            foreach ($this->_stockProducts as $product) {
                $product->setCustomerGroupId($this->_customer->getGroupId());
                $this->_getStockBlock()->addProduct($product);
            }
            $block = $this->_getStockBlock();
            $templateId = $this->_scopeConfig->getValue(\Magento\ProductAlert\Model\Email::XML_PATH_EMAIL_STOCK_TEMPLATE, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
        } else {
            $this->_getPreorderBlock()->setStore($store)->reset();
            foreach ($this->_preorderProducts as $product) {
                $product->setCustomerGroupId($this->_customer->getGroupId());
                $this->_getPreorderBlock()->addProduct($product);
            }
            $block = $this->_getPreorderBlock();
            $templateId = $this->_scopeConfig->getValue(self::AITOC_PREORDER_TEMPLATE, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId);
        }

        $alertGrid = $this->_appState->emulateAreaCode(\Magento\Framework\App\Area::AREA_FRONTEND, [$block, 'toHtml']);
        $this->_appEmulation->stopEnvironmentEmulation();

        $transport = $this->_transportBuilder
            ->setTemplateIdentifier($templateId)
            ->setTemplateOptions(['area' => \Magento\Framework\App\Area::AREA_FRONTEND, 'store' => $storeId])
            ->setTemplateVars(['customerName' => $this->_customerHelper->getCustomerName($this->_customer), 'alertGrid' => $alertGrid,])
            ->setFrom($this->_scopeConfig->getValue(self::XML_PATH_EMAIL_IDENTITY, \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $storeId))
            ->addTo($this->_customer->getEmail(), $this->_customerHelper->getCustomerName($this->_customer))->getTransport();
        $transport->sendMessage();

        return true;
    }
}
