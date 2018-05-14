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
namespace Bss\CustomerApproval\Controller\Adminhtml\Index;

use Magento\Backend\App\Action\Context;
use Magento\Customer\Model\ResourceModel\Customer\CollectionFactory;
use Magento\Eav\Model\Entity\Collection\AbstractCollection;
use Magento\Ui\Component\MassAction\Filter;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Framework\Controller\ResultFactory;
use Bss\CustomerApproval\Helper\Data;
use Bss\CustomerApproval\Helper\Email;

class MassDisapproved extends \Magento\Customer\Controller\Adminhtml\Index\AbstractMassAction
{
    /**
     * @var CustomerRepositoryInterface
     */
    protected $customerRepository;
    /**
     * @var Bss\CustomerApproval\Helper\Data
     */
    protected $helper;
    /**
     * @var Bss\CustomerApproval\Helper\Email
     */
    protected $emailHelper;

    /**
     * MassDisapproved constructor.
     * @param Context $context
     * @param Filter $filter
     * @param CollectionFactory $collectionFactory
     * @param CustomerRepositoryInterface $customerRepository
     * @param Data $helper
     * @param Email $emailHelper
     */
    public function __construct(
        Context $context,
        Filter $filter,
        CollectionFactory $collectionFactory,
        CustomerRepositoryInterface $customerRepository,
        Data $helper,
        Email $emailHelper
    ) {
        parent::__construct($context, $filter, $collectionFactory);
        $this->customerRepository = $customerRepository;
        $this->helper = $helper;
        $this->emailHelper = $emailHelper;
    }

    /**
     * @param AbstractCollection $collection
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    protected function massAction(AbstractCollection $collection)
    {
        $customersUpdated = 0;
        $collection = $this->filter->getCollection($this->collectionFactory->create());
        foreach ($collection->getAllIds() as $customerId) {
                // Verify customer exists
            $customer = $this->customerRepository->getById($customerId);
            $value = $this->helper->updateCustomerDisapprovedStatus();
            $customer->setCustomAttribute("activasion_status", $value);
            $this->saveAttribute($customer);
            $customersUpdated++;
        }
        if ($customersUpdated) {
            $this->messageManager->addSuccessMessage(__('A total of %1 record(s) were updated.', $customersUpdated));
        }
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $resultRedirect->setPath($this->getComponentRefererUrl());

        return $resultRedirect;
    }

    protected function saveAttribute($customer)
    {
        return $this->customerRepository->save($customer);
    }
}
