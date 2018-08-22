<?php
namespace Aitoc\AbandonedCartAlertsPro\Controller\Alert;

class Unsubscribe extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Aitoc\AbandonedCartAlertsPro\Model\AlertFactory
     */
    private $alertFactory;

    /**
     * @var \Aitoc\AbandonedCartAlertsPro\Model\StoplistFactory
     */
    private $stoplistFactory;

    /**
     * Class constructor
     *
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Aitoc\AbandonedCartAlertsPro\Model\AlertFactory $alertFactory
     * @param \Aitoc\AbandonedCartAlertsPro\Model\StoplistFactory $stoplistFactory
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Aitoc\AbandonedCartAlertsPro\Model\AlertFactory $alertFactory,
        \Aitoc\AbandonedCartAlertsPro\Model\StoplistFactory $stoplistFactory
    ) {
        $this->alertFactory = $alertFactory;
        $this->stoplistFactory = $stoplistFactory;
        parent::__construct($context);
    }

    /**
     * Unsubscribe customer
     *
     * @return \Magento\Framework\App\ResponseInterface
     */
    public function execute()
    {
        $alertId = $this->getRequest()->getParam('id');
        $code = $this->getRequest()->getParam('code');
        $alert = $this->alertFactory->create()
            ->load($alertId);
        if ($alert->getCode() == $code) {
            try {
                $this->stoplistFactory->create()->addToStoplist($alert->getCustomerEmail());
                $this->messageManager->addSuccess(__('You have been successfully unsubscribed.'));
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addException($e, $e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addException($e, __('Something went wrong while unsubscribing you.'));
            }
        }

        return $this->_redirect('customer/account');
    }
}
