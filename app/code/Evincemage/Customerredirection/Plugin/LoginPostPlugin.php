<?php

namespace Evincemage\Customerredirection\Plugin;

class LoginPostPlugin
{
    protected $helper;

    public function __construct(
        \Evincemage\Customerredirection\Helper\Data $helper
    ) {
        
        $this->helper   = $helper;  
    }

    /**
     * Change redirect after login to home instead of dashboard.
     *
     * @param \Magento\Customer\Controller\Account $subject
     * @param \Magento\Framework\Controller\Result\Redirect $result
     */
    public function afterExecute(
        \Magento\Customer\Controller\Account\LoginPost $subject,
        $result)
    {
       
        if($this->helper->RedirectSetting() == 0){
            if(trim($this->helper->CustomerRedirect())){
               $result->setPath(trim($this->helper->CustomerRedirect())); // Change this to what you want
                return $result;  
            }
        }
         return $result;
        
    }

}