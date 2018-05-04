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
namespace Itoris\CmsDisplayRules\Plugin;

class Plugin
{
    /** @var \Magento\Framework\ObjectManagerInterface $_objectManager */
    protected $_objectManager;
    /** @var \Magento\Framework\Registry $registry */
    protected $registry;
    /** @var  \Magento\Framework\App\RequestInterface $_request */
    protected $_request;
    /** @var \Itoris\CmsDisplayRules\Helper\Data $dataHelper */
    protected $dataHelper;

    public function __construct(
        \Itoris\CmsDisplayRules\Helper\Data $dataHelper,
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Framework\ObjectManagerInterface $objectManagerInterface,
        \Magento\Framework\Registry $registry
    ){
        $this->dataHelper = $dataHelper;
        $this->_objectManager = $objectManagerInterface;
        $this->registry = $registry;
        $this->_request = $request;
    }

    /**
     * @return bool
     */
    public function isEnabled(){
        return $this->dataHelper->isEnabled();
    }
}