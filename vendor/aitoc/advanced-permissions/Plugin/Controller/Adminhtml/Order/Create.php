<?php
/**
 * Copyright © 2018 Aitoc. All rights reserved.
 */
namespace Aitoc\AdvancedPermissions\Plugin\Controller\Adminhtml\Order;

class Create
{
    /**
     * @var \Aitoc\AdvancedPermissions\Helper\Data
     */
    protected $helper;
    
    /**
     * Quote session object
     *
     * @var \Magento\Backend\Model\Session\Quote
     */
    protected $_session;

    /**
     * @param \Aitoc\AdvancedPermissions\Helper\Data $helper
     */
    public function __construct(
        \Aitoc\AdvancedPermissions\Helper\Data $helper,
        \Magento\Backend\Model\Session\Quote $quoteSession
    ) {
        $this->helper = $helper;
        $this->_session = $quoteSession;
    }
    
    /**
     * Create Order
     *
     * @param \Magento\Sales\Controller\Adminhtml\Order\Create $object
     *
     * @return void
     */
    public function beforeExecute(
        \Magento\Sales\Controller\Adminhtml\Order\Create $object
    ) {
        if ($this->helper->isAdvancedPermissionEnabled()) {
            $ids = $this->helper->getAllowedStoreViewIds();
            if (!empty($ids[0])) {
                $this->_session->setStoreId($ids[0]);
            }
        }
    }
}
