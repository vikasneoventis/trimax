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

namespace Itoris\CmsDisplayRules\Helper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper {

	protected $alias = 'cms_display';
	protected $_responseFactory;

	protected $_date;
	const SCOPE_TYPE_STORES = 'store';
	const XML_PATH_MODULE_ENABLED = 'itoris_cmsdisplayrules/general/enabled';
	const XML_PATH_LICENSE = 'itoris_core/installed/Itoris_CmsDisplayRules';
	const XML_PATH_DEFAULT_TIMEZONE = 'general/locale/timezone';
	public function __construct(
		\Magento\Framework\App\Helper\Context $context,
		\Magento\Framework\ObjectManagerInterface $objectManager,
		\Magento\Framework\Locale\ResolverInterface $localeResolver,
		\Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezoneInterface,
		\Magento\Framework\Stdlib\DateTime\DateTime $dateTime,
		\Magento\Framework\App\ResponseFactory $responseFactory,
		\Magento\Framework\Registry $registry


	){
		$this->_responseFactory = $responseFactory;
		$this->_timezoneInterface = $timezoneInterface;
		$this->_objectManager = $objectManager;
		/** @var  \Magento\Backend\App\ConfigInterface $_backendConfig */
		$this->_backendConfig = $this->_objectManager->create('Magento\Backend\App\ConfigInterface');
		/** @var \Magento\Framework\App\Config\ScopeConfigInterface $_scopeConfig */
		$this->_scopeConfig = $this->_objectManager->create('Magento\Framework\App\Config\ScopeConfigInterface');
		$this->_date = $dateTime;
		$this->_request = $this->_objectManager->get('Magento\Framework\App\RequestInterface');
		$this->_localeResolver = $localeResolver;
		$this->registry = $registry;
		parent::__construct($context);
	}
	/** @return \Magento\Framework\App\Config\ScopeConfigInterface */
	public function getScopeConfig(){
		return $this->_scopeConfig;
	}
	public function isDisabledForStore($storeId = null){
		if($storeId == null){
			$storeId = $this->getStoreManager()->getStore()->getId();
		}


		return !(bool)$this->_scopeConfig->getValue(self::XML_PATH_MODULE_ENABLED, self::SCOPE_TYPE_STORES, $storeId);
	}
	public function isDisabledBackendForStore($storeId = null){
		return !(bool)$this->_scopeConfig->getValue(self::XML_PATH_MODULE_ENABLED, self::SCOPE_TYPE_STORES, $storeId);
	}
	public function isEnabled(){
		return !$this->isDisabledForStore();
	}
	public function isEnabledBackend($storeId){
		return !$this->isDisabledBackendForStore((int)$storeId);
	}
	/**
	 * @return \Magento\Store\Model\StoreManagerInterface
	 */
	public function getStoreManager(){
		return $this->_objectManager->create('Magento\Store\Model\StoreManagerInterface');
	}
	public function getRequest()
	{
		return $this->_request;
	}

	/**
	 * Get store id by parameter from the request
	 *
	 * @return int
	 */
	public function getStoreId() {
		if ($this->_request->getParam('store')) {
			return $this->getStoreManager()->getStore($this->_request->getParam('store'))->getId();
		}
		return 0;
	}
	/**
	 * @return \Magento\Backend\App\ConfigInterface|mixed
	 */
	public function getBackendConfig(){
		return $this->_backendConfig;
	}
	/**
	 * @return \Magento\Framework\ObjectManagerInterface
	 */
	public function getObjectManager(){
		return $this->_objectManager;
	}
	/**
	 * Create connection adapter instance
	 * @return \Magento\Framework\App\ResourceConnection
	 */
	public function getResourceConnection(){
		return $this->_objectManager->get('Magento\Framework\App\ResourceConnection');
	}


	public function getAlias() {
		return $this->alias;
	}

	public function getScopeData() {
		if ($this->getStoreId()) {
			return array(
				'scope'    => 'store',
				'scope_id' => $this->getStoreId(),
			);
		} elseif ($this->getWebsiteId()) {
			return array(
				'scope'    => 'website',
				'scope_id' => $this->getWebsiteId(),
			);
		} else {
			return array(
				'scope'    => 'default',
				'scope_id' => 0
			);
		}
	}
	public function customerGroup($selectedGroupId) {
		/** @var  $customer \Magento\Customer\Model\Session */
		$customer = $this->getObjectManager()->get('Magento\Customer\Model\Session');
		$customerId = $customer->getCustomerGroupId();
		$customerId = (string)($customerId);
		$allowedGroups = array();
		if (is_array($selectedGroupId)) {
			foreach ($selectedGroupId as $key => $value) {
				$groupId = isset($value['group_id']) ? $value['group_id'] : $value;
				if ($groupId !== null) {
					$allowedGroups[] = $groupId;
				}
			}

		} else {
			$allowedGroups = explode(',', $selectedGroupId);
		}
		if (is_null($selectedGroupId) || empty($allowedGroups)) {
			return null;
		} else {
			if (in_array($customerId, $allowedGroups)) {
				return true;
			} else {
				return false;
			}
		}
	}
 public function compareCustom($curentDate,$compareDate,$dateCode='start'){
		if($dateCode=='start'){
			if(strtotime($compareDate) < strtotime($curentDate)){
				return 1;
			}elseif(strtotime($compareDate) == strtotime($curentDate)){
					return 1;
			}else{
				return -1;
			}
		}elseif($dateCode=='finish'){
			if(strtotime($compareDate) > strtotime($curentDate)){
				return - 1;
			}elseif(strtotime($compareDate) == strtotime($curentDate)){
				return -1;
			}else{
				return 1;
			}
		}
 }
	public function isVisibleByRestrictionDate($startDate, $endDate) {
		/** @var  $currentDate \Zend_Date */
		$currentDate = new \Zend_Date(null,null,$this->_localeResolver->getLocale());
			if ($timezone =$this->getStoreManager()->getStore()->getConfig(self::XML_PATH_DEFAULT_TIMEZONE)) {
				$currentDate->setTimezone($timezone);
		}
		$start = ($startDate == '' ||  $startDate==null)? '' : $this->getDate($startDate);
		/** @var  $end \Zend_Date */
		$end =$endDate=($endDate == '' ||  $endDate==null)  ? '' : $this->getDate($endDate);
		if (!empty($startDate) && !empty($endDate)) {
			if ($this->compareCustom($currentDate->toString('y-M-d'),$start->toString('y-M-d')) !== 1 && $this->compareCustom($currentDate->toString('y-M-d'),$end->toString('y-M-d'),'finish') !== -1) {
				return false;
			}elseif($this->compareCustom($currentDate->toString('y-M-d'),$start->toString('y-M-d')) !== 1){
				return false;

			} elseif($this->compareCustom($currentDate->toString('y-M-d'),$end->toString('y-M-d'),'finish') !== -1){
				 return false;
			}else {
				return true;
			}
		} elseif (!empty($startDate) && empty($endDate)) {
			if ($this->compareCustom($currentDate->toString('y-M-d'),$start->toString('y-M-d')) !== 1) {
				return false;
			} else {
				return true;
			}
		} elseif (empty($startDate) && !empty($endDate))  {
			if ($this->compareCustom($currentDate->toString('y-M-d'),$end->toString('y-M-d'),'finish')  !== -1) {
				return false;
			} else {
				return true;
			}
		} else {
			return true;
		}
	}

	public function getDate($dateOrigValue) {
		$dateOrig = new \Zend_Date($dateOrigValue, \Zend_Date::ISO_8601);
		$dateWithTimezone = new \Zend_Date($dateOrig, \Zend_Date::ISO_8601);
		$currentTimezone = $this->_timezoneInterface->date()->getTimezone();
		if ($dateWithTimezone->getTimezone() != $currentTimezone->getName()) {
			$dateWithTimezone->setTimezone($currentTimezone->getName());
			$dateWithTimezone->setYear($dateOrig->getYear());
			$dateWithTimezone->setMonth($dateOrig->getMonth());
			$dateWithTimezone->setDay($dateOrig->getDay());
		}
		return $dateWithTimezone;
	}
	public function getResponseFactory(){
		return $this->_responseFactory;
	}
	public function getUrl($route, $params = []){
		return $this->_getUrl($route, $params);
	}

}
