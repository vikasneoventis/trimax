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
namespace Bss\CustomerApproval\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\App\ResponseFactory;
use Magento\Framework\UrlInterface;
use Magento\Customer\Api\AccountManagementInterface;
use Bss\CustomerApproval\Helper\Data;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\Translate\Inline\StateInterface;
use Magento\Framework\Escaper;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Framework\Exception\InputException;

class UpgradeStatusObserver implements ObserverInterface
{
    /**
     * @var Magento\Customer\Api\CustomerRepositoryInterface
     */
    protected $customerRepository;
    /**
     * @var Magento\Framework\App\ResponseFactory
     */
    protected $responseFactory;
    /**
     * @var Magento\Framework\UrlInterface
     */
    protected $url;
    /**
     * @var Magento\Customer\Api\AccountManagementInterface
     */
    protected $accountManagement;
    /**
     * @var Magento\Customer\Model\Session
     */
    protected $helper;
    /**
     * @var Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;
    /**
     * @var Magento\Framework\Translate\Inline\StateInterface
     */
    protected $inlineTranslation;
    /**
     * @var Magento\Framework\Escaper
     */
    protected $escaper;
    /**
     * @var Magento\Framework\Mail\Template\TransportBuilder
     */
    protected $transportBuilder;

    /**
     * UpgradeStatusObserver constructor.
     * @param CustomerRepositoryInterface $customerRepository
     * @param ResponseFactory $responseFactory
     * @param UrlInterface $url
     * @param AccountManagementInterface $accountManagement
     * @param Data $helper
     * @param ScopeConfigInterface $scopeConfig
     * @param StateInterface $inlineTranslation
     * @param Escaper $escaper
     * @param TransportBuilder $transportBuilder
     */
    public function __construct(
        CustomerRepositoryInterface $customerRepository,
        ResponseFactory $responseFactory,
        UrlInterface $url,
        AccountManagementInterface $accountManagement,
        Data $helper,
        ScopeConfigInterface $scopeConfig,
        StateInterface $inlineTranslation,
        Escaper $escaper,
        TransportBuilder $transportBuilder
    ) {
        $this->customerRepository = $customerRepository;
        $this->responseFactory = $responseFactory;
        $this->url = $url;
        $this->accountManagement = $accountManagement;
        $this->helper = $helper;
        $this->scopeConfig = $scopeConfig;
        $this->inlineTranslation = $inlineTranslation;
        $this->escaper = $escaper;
        $this->transportBuilder = $transportBuilder;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     * @return mixed
     * @throws InputException
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if ($this->helper->isEnable()) {
            $customer = $observer->getCustomer();
            $customerEmail = $customer->getEmail();
            $emailTemplate = $this->helper->getAdminEmailTemplate();
            $confirmationStatus = $this->accountManagement->getConfirmationStatus($customer->getId());
            if ($this->helper->isEnableAdminEmail()) {
                $this->sendEmail($customerEmail, $emailTemplate);
            }
            if ($this->helper->isAutoApproval()) {
                $value = $this->helper->updateCustomerApprovedStatus();
                $customer->setCustomAttribute("activasion_status", $value);
                $this->customerRepository->save($customer);
            } elseif ($confirmationStatus == AccountManagementInterface::ACCOUNT_CONFIRMATION_REQUIRED) {
                $value = $this->helper->updateCustomerPendingStatus();
                $customer->setCustomAttribute("activasion_status", $value);
                $this->customerRepository->save($customer);
                $logintUrl = $this->url->getUrl('*/*/login');
                return $this->responseFactory->create()->setRedirect($logintUrl)->sendResponse();
            } else {
                $value = $this->helper->updateCustomerPendingStatus();
                $customer->setCustomAttribute("activasion_status", $value);
                $this->customerRepository->save($customer);
                $message = $this->helper->getPendingMess();
                throw new InputException(__($message));
            }
        }
    }

    /**
     * @param string $customerEmail
     * @param string $emailTemplate
     * @return mixed
     */
    protected function sendEmail($customerEmail, $emailTemplate)
    {
        try {
            $recipients = $this->helper->getAdminEmail();
            $recipients = str_replace(' ', '', $recipients);
            $recipients = (explode(',', $recipients));
            $email = $this->helper->getAdminEmailSender();
            $emailValue = 'trans_email/ident_'.$email.'/email';
            $emailName = 'trans_email/ident_'.$email.'/name';
            $emailSender = $this->scopeConfig->getValue($emailValue, ScopeInterface::SCOPE_STORE);
            $emailNameSender = $this->scopeConfig->getValue($emailName, ScopeInterface::SCOPE_STORE);
            $this->inlineTranslation->suspend();
            $sender = [
                'name' => $this->escaper->escapeHtml($emailNameSender),
                'email' => $this->escaper->escapeHtml($emailSender),
                ];
            $transport = $this->transportBuilder
                ->setTemplateIdentifier($emailTemplate)
                ->setTemplateOptions(
                    [
                    'area' => \Magento\Framework\App\Area::AREA_FRONTEND,
                    'store' => \Magento\Store\Model\Store::DEFAULT_STORE_ID,
                    ]
                )
            ->setTemplateVars([
                    'varEmail'  => $customerEmail,
                ])
            ->setFrom($sender)
            ->addTo($recipients)
            ->getTransport();
            $transport->sendMessage();
            $this->inlineTranslation->resume();
        } catch (\Exception $e) {
            //do nothing
        }
    }
}
