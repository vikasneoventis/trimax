<?php
/**
 * @author Evince Team
 * @copyright Copyright (c) 2018 Evince (http://evincemage.com/)
 */

namespace Evincemage\Homepage\Block\Customer;

use Magento\Customer\Model\Context;

class Account extends \Magento\Framework\View\Element\Html\Link
{
    protected $httpContext;

    protected $_customerUrl;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\App\Http\Context $httpContext,
        \Magento\Customer\Model\Url $customerUrl,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->httpContext = $httpContext;
        $this->_customerUrl = $customerUrl;
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

}