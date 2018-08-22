<?php
namespace Aitoc\AbandonedCartAlertsPro\Model\ResourceModel;

class Alert extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Resource initialization
     *
     * @return void
     */
    public function _construct()
    {
        $this->_init('aitoc_abandoned_cart_alerts_pro_alert', 'alert_id');
    }
}
