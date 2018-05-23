<?php
/**
 * @author Evince Team
 * @copyright Copyright (c) 2018 Evince (http://evincemage.com/)
 */

namespace Evincemage\Topmenu\Block;

use Magento\Customer\Model\Context;

class Downloadcategory extends \Magento\Framework\View\Element\Template
{
    protected $httpContext;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\App\Http\Context $httpContext,
        array $data = []
    ) {
        parent::__construct($context,$data);
        $this->httpContext = $httpContext;
    }

    public function isLoggedIn()
    {
        return $this->httpContext->getValue(Context::CONTEXT_AUTH);
    }
}