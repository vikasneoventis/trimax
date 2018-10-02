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
    /**
     * @var \Magento\Framework\App\Http\Context
     */
    protected $httpContext;

    /**
     * @var \Magento\Customer\Model\Url
     */
    protected $_customerUrl;

    /**
     * @var \Magento\Customer\Model\SessionFactory
     */
    protected $customerSession;

    /**
     * @var CustomerRepositoryInterface
     */
    protected $customerRepository;

    /**
     * @var \Magento\Customer\Helper\View
     */
    protected $_helperView;

    /**
     * @var \Magento\Framework\Data\Helper\PostHelper
     */
    protected $_postDataHelper;

    /**
     * Account constructor.
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\App\Http\Context $httpContext
     * @param \Magento\Customer\Model\Url $customerUrl
     * @param \Magento\Customer\Model\SessionFactory $customerSession
     * @param CustomerRepositoryInterface $customerRepository
     * @param \Magento\Customer\Helper\View $helperView
     * @param \Magento\Framework\Data\Helper\PostHelper $postDataHelper
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\App\Http\Context $httpContext,
        \Magento\Customer\Model\Url $customerUrl,
        \Magento\Customer\Model\SessionFactory $customerSession,
        CustomerRepositoryInterface $customerRepository,
        \Magento\Customer\Helper\View $helperView,
        \Magento\Framework\Data\Helper\PostHelper $postDataHelper,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->httpContext = $httpContext;
        $this->_customerUrl = $customerUrl;
        $this->customerSession = $customerSession;
        $this->customerRepository = $customerRepository;
        $this->_helperView = $helperView;
        $this->_postDataHelper = $postDataHelper;
    }

    /**
     * @return mixed|null
     */
    public function isLoggedIn()
    {
        return $this->httpContext->getValue(Context::CONTEXT_AUTH);
    }

    /**
     * @return string
     */
    public function getLoginUrl()
    {
        return $this->_customerUrl->getLoginUrl();
    }

    /**
     * @return string
     */
    public function getRegisterUrl()
    {
        return $this->_customerUrl->getRegisterUrl();
    }

    /**
     * @return string
     */
    public function getDashboardUrl()
    {
        return $this->_customerUrl->getDashboardUrl();
    }

    /**
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getCustomerName()
    {
        $customerId = $this->customerSession->create()->getCustomerId();
        if($customerId){
            $customer = $this->customerRepository->getById($customerId);
            return $this->_helperView->getCustomerName($customer);
        }
    }

    /**
     * @return string
     */
    public function getLogoutUrl()
    {
        return $this->_customerUrl->getLogoutUrl();
    }

    /**
     * @return bool|string
     */
    public function getPostParams()
    {
        $params = $this->_postDataHelper->getPostData($this->getLogoutUrl());

        if($params){
            return sprintf(" data-post='%s'", $params);
        }

        return false;
    }

}