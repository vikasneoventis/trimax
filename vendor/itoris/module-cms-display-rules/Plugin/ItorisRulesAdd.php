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


class ItorisRulesAdd extends Plugin
{
    protected $colection;
   public function aroundGetData($subject, \Closure $proceed){
       $returnResult = $proceed();
       $blockId = $this->_request->getParam('block_id');
       $dataFormated=[];
       $dataFormated['itoris_cms_display_rules']=[];
       if($blockId && $this->dataHelper->isEnabled()){
           $this->collection = $this->_objectManager->create('Itoris\CmsDisplayRules\Model\ResourceModel\Block\Collection');
           $this->collection->getSelect()->where("main_table.block_id={$blockId}");
           $data = $this->collection->getData();
           if($data) {
               foreach ($data as $value) {
                   if($value['group_id']) {
                       $returnResult[$blockId]['itoris_cms_display_rules[groups]'] = explode(',', $value['group_id']);
                   }
                   $returnResult[$blockId]['itoris_cms_display_rules[starting]'] = $value['start_date'];
                   $returnResult[$blockId]['itoris_cms_display_rules[ending]'] = $value['finish_date'];
                   $returnResult[$blockId]['itoris_cms_display_rules[another_cms]'] = $value['another_cms'];


               }

           }
       }
       return $returnResult;
   }
}