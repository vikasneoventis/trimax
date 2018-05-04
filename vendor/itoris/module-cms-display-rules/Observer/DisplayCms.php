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


class DisplayCms extends AbstractObserver
{
    protected $rewriteBlockIds = array();
    protected $_objectManager;
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $model = $observer->getObject();
        if ($this->getDataHelper()->isEnabled()) {

            if ($model instanceof \Magento\Cms\Model\Page) {
                    $id = (int)$model->getId();
                    $customModel = $this->getObjectManager()->create('Itoris\CmsDisplayRules\Model\Page')->load($id);
                    $cmsModel =$this->getObjectManager()->create('Magento\Cms\Model\ResourceModel\Page\Collection');
                    $idType = 'page_id';

            } elseif ($model instanceof \Magento\Cms\Model\Block) {

                    $id = (int)$model->getId();
                    $customModel = $this->getObjectManager()->create('Itoris\CmsDisplayRules\Model\Block')->load($id);
                    $cmsModel = $this->getObjectManager()->create('Magento\Cms\Model\Block');
                    $idType = 'block_id';

            }


            if (isset($customModel)) {
                if ($this->correctSetting($customModel)) {
                    $idOtherCms = (int)$customModel->getAnotherCms();
                    if ($idOtherCms != 0) {
                        if ($idType == 'page_id') {
                            $cmsModel->addFieldToFilter($idType, array('eq' => $idOtherCms));
                            foreach ($cmsModel as $curModel) {
                                $redirectUrl = $this->getDataHelper()->getUrl($curModel->getIdentifier());
                            }
                            $response = $this->getDataHelper()->getResponseFactory()->create()->setRedirect("$redirectUrl");
                            $response->sendHeaders();
                            $exitFunc = create_function('','exit;'); $exitFunc();
                        } elseif ($idType == 'block_id') {
                            if (!in_array($idOtherCms, $this->rewriteBlockIds)) {
                                $this->rewriteBlockIds[] = $idOtherCms;
                                $cmsModel->load($idOtherCms);
                                $model->setContent($cmsModel->getContent());
                            } else {
                                $model->setContent('');
                            }
                            $this->rewriteBlockIds = array();
                        }
                    } else {
                        if ($idType == 'page_id') {
                            if($this->getRequest()->getFullActionName()=='cms_index_index'){
                                $model->setId(null);
                            }else {
                                $model->setContent('');
                                $model->setContent(__('There was no ' . $model->getTitle() . ' CMS page configured or found'));
                                $model->setContentHeading('');
                            }
                        } elseif ($idType == 'block_id') {
                            $model->setContent('');
                        }
                    }
                }
            }
        }

    }
    protected function correctSetting($cmsModel) {
        if($this->getDataHelper()->isVisibleByRestrictionDate($cmsModel->getStartDate(), $cmsModel->getFinishDate())==true && $this->getDataHelper()->customerGroup($cmsModel->getGroupId())===false){
            return true;
        }
        return false;
    }
}