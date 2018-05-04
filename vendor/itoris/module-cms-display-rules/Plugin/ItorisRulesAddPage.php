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


class ItorisRulesAddPage extends Plugin
{
    protected $colection;
    public function aroundGetData($subject, \Closure $proceed){
        $returnResult = $proceed();
        $pageId = $this->_request->getParam('page_id');
        if($pageId && $this->dataHelper->isEnabled()){
            $this->collection = $this->_objectManager->create('Itoris\CmsDisplayRules\Model\ResourceModel\Page\Collection');
            $this->collection->getSelect()->where("main_table.page_id={$pageId}");
            $data = $this->collection->getData();
            if($data) {
                foreach ($data as $value) {
                    if($value['group_id']) {
                        $returnResult[$pageId]['itoris_cms_display_rules[groups]'] = explode(',', $value['group_id']);
                    }
                    $returnResult[$pageId]['itoris_cms_display_rules[starting]'] = $value['start_date'];
                    $returnResult[$pageId]['itoris_cms_display_rules[ending]'] = $value['finish_date'];
                    $returnResult[$pageId]['itoris_cms_display_rules[another_cms]'] = $value['another_cms'];


                }
            }
        }
        return $returnResult;
    }
}