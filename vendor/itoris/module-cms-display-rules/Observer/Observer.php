<?php
/**
 * ITORIS
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the ITORIS's Magento Extensions License Agreement
 * which is available through the world-wide-web at this URL:
 * http://www.itoris.com/magento-extensions-license.html
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to sales@itoris.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade the extensions to newer
 * versions in the future. If you wish to customize the extension for your
 * needs please refer to the license agreement or contact sales@itoris.com for more information.
 *
 * @category   ITORIS
 * @package    ITORIS_M2_CMS_DISPLAY_RULES
 * @copyright  Copyright (c) 2016 ITORIS INC. (http://www.itoris.com)
 * @license    http://www.itoris.com/magento-extensions-license.html  Commercial License
 */
namespace Itoris\CmsDisplayRules\Observer;
class Observer extends AbstractObserver
{
    protected $_objectManager;
    public function execute(\Magento\Framework\Event\Observer $observer)
    {   /** @var  $layout \Magento\Framework\View\Layout */
        $layout=$observer->getLayout();
        if($observer->getFullActionName()=='cms_block_edit' && $this->getDataHelper()->isEnabled()){
            $layout->getUpdate()->addHandle('cms_block_edit_itoris');
        }
        if($observer->getFullActionName()=='cms_page_edit' && $this->getDataHelper()->isEnabled()){
            $layout->getUpdate()->addHandle('cms_itoris_page_edit');
        }

    }
}