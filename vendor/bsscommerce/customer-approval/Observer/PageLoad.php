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

class PageLoad implements ObserverInterface
{
    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;
    /**
     * @var \Magento\Customer\Api\CustomerRepositoryInterface
     */
    protected $customerRepositoryInterface;
    /**
     * @var \Bss\CustomerApproval\Helper\Data
     */
    protected $helper;
    /**
     * @var \Magento\Customer\Model\Metadata\ElementFactory
     */
    protected $metadataElement;

    /**
     * PageLoad constructor.
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Customer\Api\CustomerRepositoryInterface $customerRepositoryInterface
     * @param \Bss\CustomerApproval\Helper\Data $helper
     * @param \Magento\Customer\Model\Metadata\ElementFactory $metadataElement
     */
    public function __construct(
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepositoryInterface,
        \Bss\CustomerApproval\Helper\Data $helper,
        \Magento\Customer\Model\Metadata\ElementFactory $metadataElement
    ) {
        $this->customerRepositoryInterface = $customerRepositoryInterface;
        $this->customerSession = $customerSession;
        $this->helper = $helper;
        $this->metadataElement = $metadataElement;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     * @return mixed
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if ($this->helper->isEnable()) {
            try {
                $customerId = $this->customerSession->getCustomerId();
                $customer = $this->customerRepositoryInterface->getById($customerId);
                $customerAttr = $customer->getCustomAttribute('activasion_status');
                if ($customerAttr) {
                    $customerValue = $customerAttr->getValue();
                    $attributes = $this->helper->getAttribute();
                    foreach ($attributes as $attribute) {
                        if ($attribute->getAttributeCode() == 'activasion_status') {
                            $metadataElement = $this->metadataElement->create(
                                $attribute,
                                $customerValue,
                                'customer'
                            );
                            $value = $metadataElement->outputValue(
                                \Magento\Eav\Model\AttributeDataFactory::OUTPUT_FORMAT_HTML
                            );
                        }
                    }
                }
                if ($value == "Disapproved") {
                    $this->customerSession->logout();
                }
            } catch (\Exception $e) {
                // Do nothing
            }
        }
    }
}
