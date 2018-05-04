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

 namespace Itoris\CmsDisplayRules\Model;

class Block extends  \Magento\Catalog\Model\AbstractModel {
	protected $_objectManager;
	protected function _construct() {
		$this->_init('Itoris\CmsDisplayRules\Model\ResourceModel\Block');
	}

	protected function _afterLoad() {
		if ($this->getId()) {
			$this->_objectManager=\Magento\Framework\App\ObjectManager::getInstance();
			$resource = $this->_objectManager->create('Magento\Framework\App\ResourceConnection');
			$connection = $resource->getConnection();
			$groupTable = $resource->getTableName('itoris_cms_display_rules_block_group');
			$selectedGroupId = $connection->fetchAll("select group_id from {$groupTable} where block_id={$this->getId()}");
			$groupIds = array();
			foreach ($selectedGroupId as $groupId) {
				$groupIds[] = $groupId['group_id'];
			}
			$this->setGroupId($groupIds);
		}
		return parent::_afterLoad();
	}
}
