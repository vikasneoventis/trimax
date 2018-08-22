<?php
namespace Aitoc\AbandonedCartAlertsPro\Controller\Adminhtml\Campaign;

class Save extends \Magento\Backend\App\Action
{
    /**
     * @var \Aitoc\AbandonedCartAlertsPro\Model\Campaign
     */
    private $campaign;

    /**
     * Class constructor
     *
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Aitoc\AbandonedCartAlertsPro\Model\Campaign $campaign
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Aitoc\AbandonedCartAlertsPro\Model\Campaign $campaign
    ) {
        $this->campaign = $campaign;
        parent::__construct($context);
    }

    /**
     * Save campaign
     *
     * @return \Magento\Framework\App\ResponseInterface
     */
    public function execute()
    {
        $data = $this->getRequest()->getPostValue();
        $request = $this->getRequest();
        $campaignId = $data['campaign_id'];

        if ($data) {
            if ($campaignId) {
                $campaign = $this->campaign->load($campaignId);
                $campaign->setData($data);

                try {
                    $campaign->save();
                    $this->messageManager->addSuccess(__('You saved the campaign.'));
                } catch (\Exception $e) {
                    $this->messageManager->addError($e, __('Something went wrong while saving campaign.'));
                }

                $this->_getSession()->setFormData($data);

                if ($request->getParam('back') == 'edit') {
                    return $this->_redirect(
                        '*/*/edit/',
                        [
                            'id' => $campaignId,
                            'back' => $request->getParam('active_tab')
                        ]
                    );
                }
            }
        }

        return $this->_redirect('*/*/');
    }
}
