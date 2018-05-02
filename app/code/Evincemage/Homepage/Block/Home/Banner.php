<?php

/**
 * @copyright Copyright (c) 2018 www.evincemage.com
 */

namespace Evincemage\Homepage\Block\Home;

use \Magento\Framework\View\Element\Template;

class Banner extends Template {

    /**
     * Retrieve form action url and set "secure" param to avoid confirm
     * message when we submit form from secure page to unsecure
     *
     * @return string
     */
    protected $_product;
    protected $_dataHelper;
    protected $_storeManager;

    public function __construct(
    \Magento\Framework\View\Element\Template\Context $context, \Magento\Framework\Registry $registry, \Magento\Store\Model\StoreManagerInterface $storeManager, \Evincemage\Homepage\Helper\Data $dataHelper, array $data = []
    ) {
        $this->_coreRegistry = $registry;
        $this->_storeManager = $storeManager;
        $this->_dataHelper = $dataHelper;
        parent::__construct($context, $data);
    }

    public function _prepareLayout() {
        return parent::_prepareLayout();
    }

    public function getBannerImage() {
        return $this->_dataHelper->getBannerImage();
    }

    public function getBannerCaption() {
        return $this->_dataHelper->getBannerCaption();
    }

    public function getBannerDirUrl() {
        return $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA).'homepage';
    }
}
