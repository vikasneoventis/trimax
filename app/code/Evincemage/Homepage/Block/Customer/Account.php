<?php
/**
 * @author Evince Team
 * @copyright Copyright (c) 2018 Evince (http://evincemage.com/)
 */

namespace Evincemage\Homepage\Block\Customer;

use Magento\Customer\Model\Context;
use Magento\Customer\Api\CustomerRepositoryInterface;

class Account extends \Magento\Framework\View\Element\Html\Link
{
    protected $httpContext;

    protected $_customerUrl;

    protected $customerSession;

    protected $customerRepository;

    protected $_helperView;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\App\Http\Context $httpContext,
        \Magento\Customer\Model\Url $customerUrl,
        \Magento\Customer\Model\SessionFactory $customerSession,
        CustomerRepositoryInterface $customerRepository,
        \Magento\Customer\Helper\View $helperView,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->httpContext = $httpContext;
        $this->_customerUrl = $customerUrl;
        $this->customerSession = $customerSession;
        $this->customerRepository = $customerRepository;
        $this->_helperView = $helperView;
    }

    public function isLoggedIn()
    {
        return $this->httpContext->getValue(Context::CONTEXT_AUTH);
    }

    public function getLoginUrl()
    {
        return $this->_customerUrl->getLoginUrl();
    }

    public function getRegisterUrl()
    {
        return $this->_customerUrl->getRegisterUrl();
    }

    public function getDashboardUrl()
    {
        return $this->_customerUrl->getDashboardUrl();
    }

    public function getCustomerName()
    {
        $customerId = $this->customerSession->create()->getCustomerId();
        if($customerId){
            $customer = $this->customerRepository->getById($customerId);
            return $this->_helperView->getCustomerName($customer);
        }
    }

}