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
namespace Bss\CustomerApproval\Helper;

use Magento\Eav\Model\Config;
use Magento\Framework\App\Helper\Context;
use Magento\Customer\Api\CustomerMetadataInterface;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var Magento\Eav\Model\Config
     */
    protected $eavConfig;
    /**
     * @var Magento\Customer\Api\CustomerMetadataInterface
     */
    protected $customerMetaData;

    /**
     * Data constructor.
     * @param Context $context
     * @param Config $eavConfig
     * @param CustomerMetadataInterface $customerMetaData
     */
    public function __construct(
        Context $context,
        Config $eavConfig,
        CustomerMetadataInterface $customerMetaData
    ) {
        parent::__construct($context);
        $this->eavConfig = $eavConfig;
        $this->customerMetaData = $customerMetaData;
    }

    /**
     * Get Enable|Disable
     * @return bool
     */
    public function isEnable()
    {
        return $this->scopeConfig->isSetFlag(
            'customer_approval/general/enable',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @return true|false
     */
    public function isEnableAdminEmail()
    {
        return $this->scopeConfig->isSetFlag(
            'customer_approval/admin_notification/admin_notification_enable',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @return true|false
     */
    public function isEnableCustomerEmail()
    {
        return $this->scopeConfig->isSetFlag(
            'customer_approval/email_setting/customer_email_enable',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @return true|false
     */
    public function isAutoApproval()
    {
        return $this->scopeConfig->isSetFlag(
            'customer_approval/general/auto_approval',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @return $pendingMess
     */
    public function getPendingMess()
    {
        $pendingMess= $this->scopeConfig->getValue(
            'customer_approval/general/pending_message',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        return $pendingMess;
    }

    /**
     * @return $emailTemplate
     */
    public function getAdminEmailTemplate()
    {
        $emailTemplate= $this->scopeConfig->getValue(
            'customer_approval/admin_notification/admin_email_templates',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        return $emailTemplate;
    }

    /**
     * @return $customerApproveEmailTemplate
     */
    public function getCustomerApproveEmailTemplate()
    {
        $customerApproveEmailTemplate= $this->scopeConfig->getValue(
            'customer_approval/email_setting/customer_approve_templates',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        return $customerApproveEmailTemplate;
    }

    /**
     * @return $customerDisapproveEmailTemplate
     */
    public function getCustomerDisapproveEmailTemplate()
    {
        $customerDisapproveEmailTemplate= $this->scopeConfig->getValue(
            'customer_approval/email_setting/customer_disapprove_templates',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        return $customerDisapproveEmailTemplate;
    }

    /**
     * @return $emailSender
     */
    public function getAdminEmailSender()
    {
        $emailSender= $this->scopeConfig->getValue(
            'customer_approval/admin_notification/admin_email_sender',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        return $emailSender;
    }

    /**
     * @return $emailAdmin
     */
    public function getAdminEmail()
    {
        $emailAdmin= $this->scopeConfig->getValue(
            'customer_approval/admin_notification/admin_recipeints',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        return $emailAdmin;
    }

    /**
     * @return $customerEmailSender
     */
    public function getCustomerEmailSender()
    {
        $customerEmailSender= $this->scopeConfig->getValue(
            'customer_approval/email_setting/customer_email_sender',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        return $customerEmailSender;
    }

    /**
     * @return $pendingMess
     */
    public function getDisapproveMess()
    {
        $pendingMess= $this->scopeConfig->getValue(
            'customer_approval/general/disapprove_message',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        return $pendingMess;
    }

    /**
     * Set Customer Pending Status
     * @return $value
     */
    public function updateCustomerPendingStatus()
    {
        $attribute = $this->eavConfig->getAttribute('customer', 'activasion_status');
        $options = $attribute->getSource()->getAllOptions();
        foreach ($options as $option) {
            if ($option['label'] == 'Pending') {
                $value = $option['value'];
            }
        }
        return $value;
    }

    /**
     * Set Customer Approved Status
     * @return $value
     */
    public function updateCustomerApprovedStatus()
    {
        $attribute = $this->eavConfig->getAttribute('customer', 'activasion_status');
        $options = $attribute->getSource()->getAllOptions();
        foreach ($options as $option) {
            if ($option['label'] == 'Approved') {
                $value = $option['value'];
            }
        }
        return $value;
    }

    /**
     * Set Customer Disapproved Status
     * @return $value
     */
    public function updateCustomerDisapprovedStatus()
    {
        $attribute = $this->eavConfig->getAttribute('customer', 'activasion_status');
        $options = $attribute->getSource()->getAllOptions();
        foreach ($options as $option) {
            if ($option['label'] == 'Disapproved') {
                $value = $option['value'];
            }
        }
        return $value;
    }

    /**
     * Get All Customer Attribute
     * @return array
     */
    public function getAttribute()
    {
        return $this->customerMetaData->getAllAttributesMetadata('customer');
    }
}
