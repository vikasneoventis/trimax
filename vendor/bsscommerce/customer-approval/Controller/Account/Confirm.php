<?php
/**
 * BSS Commerce Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://bsscommerce.com/Bss-Commerce-License.txt
 *
 * =================================================================
 *
 * MAGENTO EDITION USAGE NOTICE
 * =================================================================
 * This package designed for Magento COMMUNITY edition
 * BSS Commerce does not guarantee correct work of this extension
 * on any other Magento edition except Magento COMMUNITY edition.
 * BSS Commerce does not provide extension support in case of
 * incorrect edition usage.
 * =================================================================
 *
 * @category   BSS
 * @package    Bss_CustomerApproval
 * @author     Extension Team
 * @copyright  Copyright (c) 2015-2016 BSS Commerce Co. ( http://bsscommerce.com )
 * @license    http://bsscommerce.com/Bss-Commerce-License.txt
 */
namespace Bss\CustomerApproval\Controller\Account;

use Magento\Framework\App\Action\Context;
use Magento\Customer\Model\Url;
use Magento\Store\Model\ScopeInterface;
use Magento\Customer\Model\Session;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Customer\Api\AccountManagementInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Helper\Address;
use Magento\Framework\UrlFactory;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\ResultFactory;
use Bss\CustomerApproval\Helper\Data;
use Magento\Customer\Model\Metadata\ElementFactory;
use Magento\Framework\Exception\StateException;

class Confirm extends \Magento\Customer\Controller\Account\Confirm
{
    /**
     * @var Magento\Framework\App\Action\Context
     */
    protected $context;
    /**
     * @var Magento\Customer\Model\Session
     */
    protected $customerSession;
    /**
     * @var Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;
    /**
     * @var Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;
    /**
     * @var Magento\Customer\Api\AccountManagementInterface
     */
    protected $customerAccountManagement;
    /**
     * @var Magento\Customer\Api\CustomerRepositoryInterface
     */
    protected $customerRepository;
    /**
     * @var Magento\Customer\Helper\Address
     */
    protected $addressHelper;
    /**
     * @var Magento\Framework\UrlFactory
     */
    protected $urlFactory;
    /**
     * @var Magento\Framework\Controller\ResultFactory
     */
    protected $resultFactory;
    /**
     * @var Bss\CustomerApproval\Helper\Data
     */
    protected $helper;
    /**
     * @var ElementFactory
     */
    protected $metadataElement;

    /**
     * Confirm constructor.
     * @param Context $context
     * @param Session $customerSession
     * @param ScopeConfigInterface $scopeConfig
     * @param StoreManagerInterface $storeManager
     * @param AccountManagementInterface $customerAccountManagement
     * @param CustomerRepositoryInterface $customerRepository
     * @param Address $addressHelper
     * @param UrlFactory $urlFactory
     * @param Redirect $resultFactory
     * @param Data $helper
     * @param ElementFactory $metadataElement
     */
    public function __construct(
        Context $context,
        Session $customerSession,
        ScopeConfigInterface $scopeConfig,
        StoreManagerInterface $storeManager,
        AccountManagementInterface $customerAccountManagement,
        CustomerRepositoryInterface $customerRepository,
        Address $addressHelper,
        UrlFactory $urlFactory,
        Redirect $resultFactory,
        Data $helper,
        ElementFactory $metadataElement
    ) {
        parent::__construct(
            $context,
            $customerSession,
            $scopeConfig,
            $storeManager,
            $customerAccountManagement,
            $customerRepository,
            $addressHelper,
            $urlFactory
        );
        $this->helper = $helper;
        $this->metadataElement = $metadataElement;
    }

    /**
     * @return Redirect
     */
    public function execute()
    {
        /** @var \Magento\Framework\Controller\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);

        if ($this->session->isLoggedIn()) {
            $resultRedirect->setPath('*/*/');
            return $resultRedirect;
        }
        try {
            $customerId = $this->getRequest()->getParam('id', false);
            $key = $this->getRequest()->getParam('key', false);
            if (empty($customerId) || empty($key)) {
                throw new \Exception(__('Bad request.'));
            }

            // log in and send greeting email
            $customerEmail = $this->customerRepository->getById($customerId)->getEmail();
            $customer = $this->customerAccountManagement->activate($customerEmail, $key);
            $customerStatus = $this->customerRepository->getById($customerId)->getCustomAttribute('activasion_status');
            $value = $this->getValue($customerStatus);
            if ($value == "Pending") {
                $message = $this->helper->getPendingMess();
                $this->messageManager->addErrorMessage($message);
                $url = $this->urlModel->getUrl('customer/account/login', ['_secure' => true]);
                return $resultRedirect->setUrl($this->_redirect->error($url));
            } else {
                $this->session->setCustomerDataAsLoggedIn($customer);
                $this->messageManager->addSuccessMessage($this->getSuccessMessage());
                $resultRedirect->setUrl($this->getSuccessRedirect());
                return $resultRedirect;
            }
        } catch (StateException $e) {
            $this->messageManager->addExceptionMessage($e, __('This confirmation key is invalid or has expired.'));
        } catch (\Exception $e) {
            $this->messageManager->addExceptionMessage($e, __('There was an error confirming the account'));
        }

        $url = $this->urlModel->getUrl('*/*/index', ['_secure' => true]);
        return $resultRedirect->setUrl($this->_redirect->error($url));
    }

    /**
     * @param array $customerStatus
     * @return mixed
     */

    protected function getValue($customerStatus)
    {
        if ($customerStatus) {
            $customerValue = $customerStatus->getValue();
            $attributes = $this->helper->getAttribute();
            foreach ($attributes as $attribute) {
                if ($attribute->getAttributeCode() == 'activasion_status') {
                    $metadataElement = $this->metadataElement->create($attribute, $customerValue, 'customer');
                    $value = $metadataElement->outputValue(\Magento\Eav\Model\AttributeDataFactory::OUTPUT_FORMAT_HTML);
                }
            }
            return $value;
        }
    }
}
