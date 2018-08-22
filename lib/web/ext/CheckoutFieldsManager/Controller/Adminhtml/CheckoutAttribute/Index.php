<?php
/**
 *
 * Copyright © 2016 Aitoc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Aitoc\CheckoutFieldsManager\Controller\Adminhtml\CheckoutAttribute;

use Magento\Backend\App\Action;

class Index extends \Aitoc\CheckoutFieldsManager\Controller\Adminhtml\CheckoutAttribute
{
    /**
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        $resultPage = $this->createActionPage();
        $resultPage->addContent(
            $resultPage->getLayout()
                ->createBlock('Aitoc\CheckoutFieldsManager\Block\Adminhtml\CheckoutAttribute')
        );
        return $resultPage;
    }
}
