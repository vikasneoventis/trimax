<?php
namespace Aitoc\AbandonedCartAlertsPro\Block\Adminhtml\Statistic;

class Index extends \Magento\Backend\Block\Template
{
    /**
     * @var \Aitoc\AbandonedCartAlertsPro\Helper\Statistic
     */
    private $statisticHelper;

    /**
     * @var string
     */
    public $_template = 'statistic/index.phtml';

    /**
     * Class constructor
     *
     * @param \Aitoc\AbandonedCartAlertsPro\Helper\Statistic $statisticHelper
     * @param \Magento\Backend\Block\Widget\Context $context
     * @param array $data
     */
    public function __construct(
        \Aitoc\AbandonedCartAlertsPro\Helper\Statistic $statisticHelper,
        \Magento\Backend\Block\Widget\Context $context,
        array $data = []
    ) {
        $this->statisticHelper = $statisticHelper;
        parent::__construct($context, $data);
    }

    /**
     * Prepare layout
     *
     * @return void
     */
    public function _prepareLayout()
    {
        parent::_prepareLayout();

        $data = [
            'total_orders_placed' => $this->statisticHelper->getTotalOrdersPlaced(),
            'total_orders_completed' => $this->statisticHelper->getTotalOrdersCompleted(),
            'order_completion_rate' => $this->statisticHelper->getOrderCompletionRate(),
            'completed_orders_value' => $this->statisticHelper->getCompletedOrdersValue(),
            'recovered_carts_qty' => $this->statisticHelper->getRecoveredCartsQty(),
            'recovered_carts_value' => $this->statisticHelper->getRecoveredCartsValue(),
        ];

        $this->addData($data);
    }
}
