<?php
namespace Aitoc\AbandonedCartAlertsPro\Controller\Adminhtml\Campaign;

class Edit extends \Aitoc\AbandonedCartAlertsPro\Controller\Adminhtml\Campaign
{
    /**
     * Edit campaign page
     *
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        $campaignId = (int) $this->getRequest()->getParam('id');
        $currentCampaign = $this->campaign->load($campaignId);
        $this->registry->register('aitocabandonedcart_campaign_page', $currentCampaign);
        $resultPage = $this->resultPageFactory->create();
        $resultPage->getConfig()->getTitle()->prepend(__('Edit Campaign'));

        return $resultPage;
    }
}
