<?php
/**
 * MailChimp Magento Component
 *
 * @category Ebizmarts
 * @package MailChimp
 * @author Ebizmarts Team <info@ebizmarts.com>
 * @copyright Ebizmarts (http://ebizmarts.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @date: 12/1/17 2:21 PM
 * @file: SaveAfter.php
 */

namespace Ebizmarts\MailChimp\Observer\Adminhtml\Customer;

class SaveAfter implements \Magento\Framework\Event\ObserverInterface
{
    /**
     * @var \Ebizmarts\MailChimp\Helper\Data
     */
    protected $helper;
    /**
     * @var \Magento\Newsletter\Model\SubscriberFactory
     */
    protected $subscriberFactory;
    /**
     * @var \Ebizmarts\MailChimp\Model\MailChimpInterestGroupFactory
     */
    protected $interestGroupFactory;

    /**
     * SaveAfter constructor.
     * @param \Ebizmarts\MailChimp\Helper\Data $helper
     * @param \Magento\Newsletter\Model\SubscriberFactory $subscriberFactory
     * @param \Ebizmarts\MailChimp\Model\MailChimpInterestGroupFactory $interestGroupFactory
     */
    public function __construct(
        \Ebizmarts\MailChimp\Helper\Data $helper,
        \Magento\Newsletter\Model\SubscriberFactory $subscriberFactory,
        \Ebizmarts\MailChimp\Model\MailChimpInterestGroupFactory $interestGroupFactory
    )
    {
        $this->helper               = $helper;
        $this->subscriberFactory    = $subscriberFactory;
        $this->interestGroupFactory = $interestGroupFactory;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $customer = $observer->getCustomer();
        $request  = $observer->getEvent()->getRequest();
        $allParams = $request->getParams();
        if(isset($allParams['customer']['interestgroup'])) {
            $params = ['group' => $allParams['customer']['interestgroup']];
            foreach ($params['group'] as $index => $ig) {
                if (is_array($ig)) {
                    foreach ($ig as $i => $v) {
                        if ($v == 1) {
                            $params['group'][$index][$i] = $i;
                        }
                    }
                }
            }
            $subscriber = $this->subscriberFactory->create();
            $interestGroup = $this->interestGroupFactory->create();
            try {
                $subscriber->loadByEmail($customer->getEmail());
                if ($subscriber->getEmail() == $customer->getEmail()) {
                    $interestGroup->getBySubscriberIdStoreId($subscriber->getSubscriberId(), $subscriber->getStoreId());
                    $interestGroup->setGroupdata(serialize($params));
                    $interestGroup->setSubscriberId($subscriber->getSubscriberId());
                    $interestGroup->setStoreId($subscriber->getStoreId());
                    $interestGroup->setUpdatedAt($this->helper->getGmtDate());
                    $interestGroup->getResource()->save($interestGroup);
                    $listId = $this->helper->getGeneralList($subscriber->getStoreId());
                    $this->_updateSubscriber($listId, $subscriber->getId(), $this->helper->getGmtDate(), null, 1);
                } else {
                    $this->subscriberFactory->create()->subscribe($customer->getEmail());
                    $subscriber->loadByEmail($customer->getEmail());
                    $interestGroup->getBySubscriberIdStoreId($subscriber->getSubscriberId(), $subscriber->getStoreId());
                    $interestGroup->setGroupdata(serialize($params));
                    $interestGroup->setSubscriberId($subscriber->getSubscriberId());
                    $interestGroup->setStoreId($subscriber->getStoreId());
                    $interestGroup->setUpdatedAt($this->helper->getGmtDate());
                    $interestGroup->getResource()->save($interestGroup);
                }

            } catch (\Exception $e) {

            }
        }
    }
    protected function _updateSubscriber($listId, $entityId, $sync_delta = null, $sync_error=null, $sync_modified=null)
    {
        $this->helper->saveEcommerceData($listId, $entityId, \Ebizmarts\MailChimp\Helper\Data::IS_SUBSCRIBER ,
            $sync_delta, $sync_error, $sync_modified);
    }

}