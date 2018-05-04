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
class AbstractObserver implements \Magento\Framework\Event\ObserverInterface
{
    protected $_objectManager;
    protected $_request;
    protected $_helper;
    public function getObjectManager(){
        if($this->_objectManager)
            return $this->_objectManager;
        return $this->_objectManager=\Magento\Framework\App\ObjectManager::getInstance();
    }
    public function getRequest(){
        if(!$this->_request){
            $this->_request = $this->getObjectManager()->get('Magento\Framework\App\RequestInterface');
        }
        if(!$this->_request)
            $this->_request = $this->getObjectManager()->create('Magento\Framework\App\RequestInterface');
        return $this->_request;
    }
    /** @return \Itoris\CmsDisplayRules\Helper\Data */
    public function getDataHelper(){
        if(!$this->_helper){
            $this->_helper=$this->getObjectManager()->create('Itoris\CmsDisplayRules\Helper\Data');
        }
        return $this->_helper;
    }
    /** @return \Magento\Framework\Message\ManagerInterface */
    public function getMessageManager(){
        return $this->getObjectManager()->get('Magento\Framework\Message\ManagerInterface');
    }
    public function execute(\Magento\Framework\Event\Observer $observer)
    {

    }
}