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
use Bss\CustomerApproval\Helper\Data;
use Magento\Customer\Model\Metadata\ElementFactory;
use Bss\CustomerApproval\Helper\Email;

class SaveObserver implements ObserverInterface
{
    /**
     * @var Bss\CustomerApproval\Helper\Data
     */
    protected $helper;
    /**
     * @var Magento\Customer\Model\Metadata\ElementFactory
     */
    protected $metadataElement;
    /**
     * @var Bss\CustomerApproval\Helper\Email
     */
    protected $emailHelper;

    /**
     * SaveObserver constructor.
     * @param Data $helper
     * @param ElementFactory $metadataElement
     * @param Email $emailHelper
     */
    public function __construct(
        Data $helper,
        ElementFactory $metadataElement,
        Email $emailHelper
    ) {
        $this->helper = $helper;
        $this->metadataElement = $metadataElement;
        $this->emailHelper = $emailHelper;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     * @return mixed
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if ($this->helper->isEnable()) {
            if ($this->helper->isEnableCustomerEmail()) {
                $customer = $observer->getCustomer();
                $status = $customer->getData('activasion_status');
                $attributes = $this->helper->getAttribute();
                foreach ($attributes as $attribute) {
                    if ($attribute->getAttributeCode() == 'activasion_status') {
                        $metadataElement = $this->metadataElement->create($attribute, $status, 'customer');
                        $value = $metadataElement->outputValue(
                            \Magento\Eav\Model\AttributeDataFactory::OUTPUT_FORMAT_HTML
                        );
                    }
                }
                $customerEmail = $customer->getEmail();
                $customerName = $customer->getName();
                if ($value == "Approved") {
                    $emailTemplate = $this->helper->getCustomerApproveEmailTemplate();
                    $this->emailHelper->sendEmail($customerEmail, $emailTemplate, $customerName);
                } elseif ($value == "Disapproved") {
                    $emailTemplate = $this->helper->getCustomerDisapproveEmailTemplate();
                    $this->emailHelper->sendEmail($customerEmail, $emailTemplate, $customerName);
                }
            }
        }
    }
}
