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
class CmsSaveAfter extends AbstractObserver
{
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $modelObject = $observer->getObject();
        $id = (int)$modelObject->getId();
        if ($id && $this->getDataHelper()->isEnabled()) {
            $data =  $this->getRequest()->getParam('itoris_cms_display_rules');
            if (is_array($data)) {
                if ($modelObject instanceof \Magento\Cms\Model\Page) {
                    $cmsModel = $this->getObjectManager()->create('Itoris\CmsDisplayRules\Model\Page');
                    $cmsModel->setPageId($id);
                    $cmsMode = 'page';
                } elseif ($modelObject instanceof  \Magento\Cms\Model\Block) {
                    $cmsModel = $this->getObjectManager()->create('Itoris\CmsDisplayRules\Model\Block');
                    $cmsModel->setBlockId($id);
                    $cmsMode = 'block';
                }
                if (isset($cmsModel)) {
                    if (!empty($data['ending']) && !empty($data['starting'])) {
                        $start = $this->getDataHelper()->getDate($data['starting']);
                        $end = $this->getDataHelper()->getDate($data['ending']);
                        if ($end->compareDate($start) !== -1) {
                            $cmsModel->setStartDate($data['starting']);
                            $cmsModel->setFinishDate($data['ending']);
                            $cmsModel->setAnotherCms($data['another_cms']);
                            $cmsModel->save();
                        } else {
                            $this->getMessageManager()->addErrorMessage(__('Ending on date must be greater than starting on date'));
                            return;
                        }
                    } else {
                        $cmsModel->setStartDate($data['starting']);
                        $cmsModel->setFinishDate($data['ending']);
                        $cmsModel->setAnotherCms($data['another_cms']);
                        $cmsModel->save();
                    }
                    /** @var  $resource \Magento\Framework\App\ResourceConnection */
                    $resource = $this->getDataHelper()->getResourceConnection();
                    $connection = $resource->getConnection();
                    if ($cmsMode == 'page') {
                        $tableGroup = $resource->getTableName('itoris_cms_display_rules_page_group');
                        $valueUserGroup = $data['groups'];
                        $connection->query("delete from {$tableGroup} where page_id={$id}");
                        foreach ($valueUserGroup as $group) {
                            if ($group != 'all') {
                                $connection->query("insert into {$tableGroup} (page_id, group_id) values ({$id}, {$group})");
                            }
                        }
                    } elseif ($cmsMode == 'block') {
                        $tableGroup = $resource->getTableName('itoris_cms_display_rules_block_group');
                        $valueUserGroup = $data['groups'];
                        $connection->query("delete from {$tableGroup} where block_id={$id}");
                        foreach ($valueUserGroup as $group) {
                            if ($group != 'all') {
                                $connection->query("insert into {$tableGroup} (block_id, group_id) values ({$id}, {$group})");
                            }
                        }
                    }
                }
            }

        }

    }
}