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
use Magento\Framework\Message\ManagerInterface;
use Magento\Customer\Model\Metadata\ElementFactory;
use Bss\CustomerApproval\Helper\Data;
use Magento\Framework\Exception\EmailNotConfirmedException;
use Magento\Framework\Exception\AuthenticationException;

class LoginObserver implements ObserverInterface
{
    /**
     * @var Magento\Framework\Message\ManagerInterface
     */
    protected $messageManager;
    /**
     * @var Magento\Customer\Model\Metadata\ElementFactory
     */
    protected $metadataElement;
    /**
     * @var Bss\CustomerApproval\Helper\Data
     */
    protected $helper;

    /**
     * LoginObserver constructor.
     * @param ManagerInterface $messageManager
     * @param ElementFactory $metadataElement
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     * @param \Magento\Framework\App\Request\Http $request
     * @param Data $helper
     */
    public function __construct(
        ManagerInterface $messageManager,
        ElementFactory $metadataElement,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Magento\Framework\App\Request\Http $request,
        Data $helper
    ) {
        $this->resultJsonFactory = $resultJsonFactory;
        $this->messageManager = $messageManager;
        $this->request = $request;
        $this->metadataElement = $metadataElement;
        $this->helper = $helper;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     * @throws EmailNotConfirmedException
     * @return mixed
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if ($this->helper->isEnable()) {
            $orderValue = $observer->getModel()->getData('activasion_status');
            if ($orderValue) {
                $attributes = $this->helper->getAttribute();
                foreach ($attributes as $attribute) {
                    if ($attribute->getAttributeCode() == 'activasion_status') {
                        $metadataElement = $this->metadataElement->create($attribute, $orderValue, 'customer');
                        $value = $metadataElement->outputValue(
                            \Magento\Eav\Model\AttributeDataFactory::OUTPUT_FORMAT_HTML
                        );
                    }
                }
                if ($value == "Pending") {
                    $message = $this->helper->getPendingMess();
                    if ($this->request->isAjax()) {
                        throw new EmailNotConfirmedException(__($message));
                    }
                }
                if ($value == "Disapproved") {
                    $message = $this->helper->getDisapproveMess();
                    if ($this->request->isAjax()) {
                        throw new EmailNotConfirmedException(__($message));
                    }
                }
            }
        }
    }
}
