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
namespace Itoris\CmsDisplayRules\Ui\Component\FormFiledCustom;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Ui\Component\Form\Element\MultiSelect;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
class GroupPageCustom extends MultiSelect
{
    /**
     * @var StoreManagerInterface
     */
    private $storeManager;
    protected $objectManager;
    protected $helper;
    public function getObjectManager(){
        if($this->objectManager)
            return $this->objectManager;
        return $this->objectManager=\Magento\Framework\App\ObjectManager::getInstance();
    }
    /** @return \Itoris\CmsDisplayRules\Helper\Data */
    public function getDataHelper(){
        if(!$this->helper){
            $this->helper=$this->getObjectManager()->create('Itoris\CmsDisplayRules\Helper\Data');
        }
        return $this->helper;
    }
    /**
     * @inheritdoc
     */
    public function prepare()
    {
        $resource = $this->getDataHelper()->getResourceConnection();
        $connection = $resource->getConnection();
        $config = $this->getData('config');
        if($this->getDataHelper()->getRequest()->getParam('page_id')) {
            $pageId=(int)$this->getDataHelper()->getRequest()->getParam('page_id');
            $data = $connection->fetchOne("SELECT GROUP_CONCAT(DISTINCT group_id  ORDER BY  group_id ASC SEPARATOR ',')
                                       as groups FROM {$resource->getTableName('itoris_cms_display_rules_page_group')}
                                       as icdrpg WHERE
                                       icdrpg.page_id=$pageId
                                       GROUP BY page_id ");
            if (is_string($data)) {
                $config['default'] = $data;
                $this->_data['config'] = $config;
            }
        }
        parent::prepare();
    }

}
