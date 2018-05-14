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
namespace Bss\CustomerApproval\Plugin;

use Bss\CustomerApproval\Helper\Data;
use Magento\Framework\App\Action\Context;
use Magento\Customer\Model\Metadata\ElementFactory;
use Magento\Framework\App\Response\Http as responseHttp;
use Magento\Customer\Api\CustomerRepositoryInterface;

class LoginPost
{
    /**
     * @var Data
     */
    protected $helper;
    /**
     * @var Magento\Framework\App\Action\Context
     */
    protected $context;
    /**
     * @var ElementFactory
     */
    protected $metadataElement;
    /**
     * @var ManagerInterface
     */
    protected $messageManager;
    /**
     * @var CustomerRepositoryInterface
     */
    protected $customerInterface;
    /**
     * @var UrlInterface
     */
    protected $url;
    /**
     * @var responseHttp
     */
    protected $response;

    /**
     * LoginPost constructor.
     * @param Data $helper
     * @param Context $context
     * @param ElementFactory $metadataElement
     * @param responseHttp $response
     * @param CustomerRepositoryInterface $customerInterface
     */
    public function __construct(
        Data $helper,
        Context $context,
        ElementFactory $metadataElement,
        responseHttp $response,
        CustomerRepositoryInterface $customerInterface
    ) {
        $this->helper = $helper;
        $this->_request = $context->getRequest();
        $this->metadataElement = $metadataElement;
        $this->messageManager = $context->getMessageManager();
        $this->url = $context->getUrl();
        $this->response = $response;
        $this->customerInterface = $customerInterface;
    }

    /**
     * @param \Magento\Customer\Controller\Account\LoginPost $subject
     * @param \Closure $proceed
     * @return $this|mixed
     */
    public function aroundExecute(\Magento\Customer\Controller\Account\LoginPost $subject, \Closure $proceed)
    {
        if ($this->helper->isEnable()) {
            $login =  $this->_request->getPost('login');
            $email = $login['username'];
            try {
                $customer = $this->customerInterface->get($email);
                $customerAttr = $customer->getCustomAttribute('activasion_status');
                if ($customerAttr) {
                    $customerValue = $customerAttr->getValue();
                    $attributes = $this->helper->getAttribute();
                    foreach ($attributes as $attribute) {
                        if ($attribute->getAttributeCode() == 'activasion_status') {
                            $metadataElement = $this->metadataElement->create($attribute, $customerValue, 'customer');
                            $value = $metadataElement->outputValue(
                                \Magento\Eav\Model\AttributeDataFactory::OUTPUT_FORMAT_HTML
                            );
                        }
                    }
                    if ($value == "Pending") {
                        $message = $this->helper->getPendingMess();
                        $loginUrl = $this->url->getUrl('customer/account/login');
                        $this->messageManager->addErrorMessage($message);
                        return $this->response->setRedirect($loginUrl);
                    } elseif ($value == "Disapproved") {
                        $message = $this->helper->getDisapproveMess();
                        $loginUrl = $this->url->getUrl('customer/account/login');
                        $this->messageManager->addErrorMessage($message);
                        return $this->response->setRedirect($loginUrl);
                    }
                }
                return $proceed();
            } catch (\Exception $e) {
                return $proceed();
            }
        }
        return $proceed();
    }
}
