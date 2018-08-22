<?php
namespace Aitoc\AbandonedCartAlertsPro\Controller\Adminhtml;

class Campaign extends \Magento\Backend\App\Action
{
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    public $resultPageFactory;

    /**
     * @var \Aitoc\AbandonedCartAlertsPro\Model\Campaign
     */
    public $campaign;

    /**
     * @var \Magento\Framework\Registry
     */
    public $registry;

    /**
     * Class constructor
     *
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Aitoc\AbandonedCartAlertsPro\Model\Campaign $campaign
     * @param \Magento\Framework\Registry $registry
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Aitoc\AbandonedCartAlertsPro\Model\Campaign $campaign,
        \Magento\Framework\Registry $registry
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->campaign = $campaign;
        $this->registry = $registry;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();

        return $resultPage;
    }
}
