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

namespace Itoris\CmsDisplayRules\Ui\Component\Listing\Column\Cms;
use Magento\Framework\Data\OptionSourceInterface;

class AbstractOption implements OptionSourceInterface
{
    protected $_objectManager;
    protected $_request;
    protected $_escaper;
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
    protected function getEscaper(){
        if(!$this->_escaper){
            $this->_escaper = $this->getObjectManager()->get('Magento\Framework\Escaper');
        }
        if(!$this->_escaper){
            $this->_escaper = $this->getObjectManager()->create('Magento\Framework\Escaper');
        }
        return $this->_escaper;
    }
    /**
     * Escape html entities
     *
     * @param string|array $data
     * @param array|null $allowedTags
     * @return string
     */
    public function escapeHtml($data, $allowedTags = null)
    {
        return $this->getEscaper()->escapeHtml($data, $allowedTags);
    }
    public function toOptionArray()
    {

    }
}