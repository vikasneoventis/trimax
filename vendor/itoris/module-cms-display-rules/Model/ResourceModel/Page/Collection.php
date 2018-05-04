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

namespace Itoris\CmsDisplayRules\Model\ResourceModel\Page;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection {

	protected $groupTable = 'itoris_cms_display_rules_page_group';

	protected function _construct() {
		$this->_init('Itoris\CmsDisplayRules\Model\Page','Itoris\CmsDisplayRules\Model\ResourceModel\Page');
		$this->groupTable = $this->getTable('itoris_cms_display_rules_page_group');
	}

	protected function _initSelect() {
		parent::_initSelect();
		$this->getSelect()->joinLeft(
			array('group' => $this->groupTable),
			'group.page_id = main_table.page_id',
			array('group_id' => 'group_concat(distinct group.group_id)')
		)->group('main_table.page_id');

		return $this;
	}

	public function addGroupFilter($groupId) {
		$this->_select->having("group_id IS NULL OR FIND_IN_SET('" . intval($groupId) . "', group_id)");
		return $this;
	}
}
