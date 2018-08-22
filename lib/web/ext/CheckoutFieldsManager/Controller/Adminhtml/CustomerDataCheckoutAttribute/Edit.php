<?php
/**
 *
 * Copyright © 2016 Aitoc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Aitoc\CheckoutFieldsManager\Controller\Adminhtml\CustomerDataCheckoutAttribute;

class Edit extends \Aitoc\CheckoutFieldsManager\Controller\Adminhtml\CustomerDataCheckoutAttribute
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Aitoc_CheckoutFieldsManager::actions_edit';

    /**
     * Edit order address form
     *
     * @return \Magento\Backend\Model\View\Result\Page|\Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        return $this->getResult();
    }
}
