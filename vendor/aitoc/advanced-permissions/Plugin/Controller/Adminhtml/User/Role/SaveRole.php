<?php
/**
 * Copyright © 2018 Aitoc. All rights reserved.
 */
namespace Aitoc\AdvancedPermissions\Plugin\Controller\Adminhtml\User\Role;

use Magento\Framework\Controller\ResultFactory;

class SaveRole
{
    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $request;

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * @var \Aitoc\AdvancedPermissions\Model\Role
     */
    protected $role;

    /**
     * @var \Aitoc\AdvancedPermissions\Model\Stores
     */
    protected $stores;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $coreRegistry;

    /**
     * Backend auth session
     *
     * @var \Magento\Backend\Model\Auth\Session
     */
    protected $authSession;

    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $messageManager;

    /**
     * @var \Magento\Framework\Controller\ResultFactory
     */
    protected $resultFactory;

    /**
     * SaveRole constructor.
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Aitoc\AdvancedPermissions\Model\Role $roleAdv
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Aitoc\AdvancedPermissions\Model\Stores $storesAdv
     * @param \Magento\Backend\Model\Auth\Session $authSession
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Aitoc\AdvancedPermissions\Model\Role $roleAdv,
        \Magento\Framework\Registry $coreRegistry,
        \Aitoc\AdvancedPermissions\Model\Stores $storesAdv,
        \Magento\Backend\Model\Auth\Session $authSession
    ) {
        $this->request = $context->getRequest();
        $this->objectManager = $context->getObjectManager();
        $this->coreRegistry = $coreRegistry;
        $this->role = $roleAdv;
        $this->stores = $storesAdv;
        $this->authSession = $authSession;
        $this->messageManager = $context->getMessageManager();
        $this->resultFactory = $context->getResultFactory();

    }

    /**
     * @param \Magento\User\Controller\Adminhtml\User\Role\SaveRole $object
     * @param \Closure $proceed
     */
    public function afterExecute(
        \Magento\User\Controller\Adminhtml\User\Role\SaveRole $object,
        $result
    ) {
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $request = $this->request;
        $params = $request->getParams();
        $rid = $this->coreRegistry->registry('current_role')->getId();
        try {
            $this->validateUser();
            if (isset($params['radio_limits'])) {
                $radioLimits = $params['radio_limits'];
                $roleAdvanced = $this->objectManager->create('Aitoc\AdvancedPermissions\Model\Role')->loadOriginal($rid);
                $roleAdvancedId = (int)$roleAdvanced->getId();
                $webSiteId = 0;
                $originalId = (int)$rid;
                $stores = $this->stores->getCollection()->setRoleFilter($roleAdvancedId);
                $scope = 0;
                if ($radioLimits == \Aitoc\AdvancedPermissions\Helper\Data::SCOPE_STORE) {
                    if (!$roleAdvancedId) {
                        $roleAdvanced = $this->objectManager->create('Aitoc\AdvancedPermissions\Model\Role');
                    }
                    $scope = \Aitoc\AdvancedPermissions\Helper\Data::SCOPE_STORE;
                }
                if ($radioLimits == \Aitoc\AdvancedPermissions\Helper\Data::SCOPE_WEBSITE) {
                    $websites = isset($params['websites']) ? $params['websites'] : [0];
                    $webSiteId = implode(",", $websites);
                }
                $this->fullSave($roleAdvanced, $stores, (int)$radioLimits, $webSiteId, $originalId, $params, $scope);
            }

            return $result;
        } catch (\Magento\Framework\Exception\AuthenticationException $e) {
            $arguments = $rid ? ['rid' => $rid] : [];

            return $resultRedirect->setPath('*/*/editrole', $arguments);
        }


    }

    /**
     * @param $roleAdvancedId
     * @param $stores
     * @param $radioLimits
     * @param $webSiteId
     * @param $originalId
     * @param null $params
     * @param int $scope
     */
    public function fullSave(
        $roleAdvanced,
        $stores,
        $radioLimits,
        $webSiteId,
        $originalId,
        $params = null,
        $scope = 0
    ) {
        $this->removeUnAvailableStoresForRoleAdvancedId($roleAdvanced, $stores);
        $this->saveSettings($roleAdvanced, $params['setting']);
        $this->setParamsForRoleAdvancedAndSave($roleAdvanced, $radioLimits, $webSiteId, $originalId, $params, $scope);
    }

    /**
     * Remove unavailable stores from the list
     *
     * @param integer $roleAdvancedId
     * @param array $stores
     *
     * @return null
     */
    private function removeUnAvailableStoresForRoleAdvancedId($roleAdvancedId, $stores)
    {
        if ($roleAdvancedId) {
            foreach ($stores as $store) {
                $store->delete();
            }
        }

        return null;
    }

    /**
     *
     * Set parameters for advanced role and save
     *
     * @param $roleAdvanced
     * @param $radioLimits
     * @param $webSiteId
     * @param $originalId
     * @param null $params
     * @param int $scope
     */
    private function setParamsForRoleAdvancedAndSave(
        $roleAdvanced,
        $radioLimits,
        $webSiteId,
        $originalId,
        $params = null,
        $scope = 0
    ) {
        $roleAdvanced->setScope($radioLimits);
        $roleAdvanced->setWebsiteId($webSiteId);
        $roleAdvanced->setOriginalId($originalId);
        $this->saveModel($roleAdvanced);
        if ($scope == \Aitoc\AdvancedPermissions\Helper\Data::SCOPE_STORE) {
            $this->saveStores($roleAdvanced->getId(), $params);
        }
    }

    /**
     * Save fields from settings
     *
     * @param $role
     * @param $params
     */
    public function saveSettings($role, $params)
    {
        $data = $role->getOptions();
        foreach ($data as $value) {
            $use = 0;
            $scope = 0;
            if (isset($params['use_config_' . $value])) {
                $use = $params['use_config_' . $value];
            }
            $role->setData('use_config_' . $value, $use);
            if (isset($params[$value])) {
                $role->setData($value, $params[$value]);
            }
        }
    }

    /**
     * Save Model
     *
     * @param $model
     */
    public function saveModel($model)
    {

        try {
            $model->save();
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            $this->messageManager->addError($e->getMessage());
        } catch (\Exception $e) {
            $this->messageManager->addError(__('An error occurred while saving this role.'));
        }
    }

    /**
     * Save Stores
     *
     * @param $roleId
     * @param $params
     */
    public function saveStores($roleId, $params)
    {
        if (isset($params['store'])) {
            $newStores = $params['store'];
            foreach ($newStores as $el) {
                $storeNew = $this->objectManager->create('Aitoc\AdvancedPermissions\Model\Stores');
                $storeNew->setStoreId($el);
                if (isset($params['storesview'])) {
                    if (isset($params['storesview'][$el])) {
                        $storeNew->setStoreViewIds(implode(",", $params['storesview'][$el]));
                    }
                }
                if (isset($params['category_ids' . $el])) {
                    $storeNew->setCategoryIds(implode(",", $params['category_ids' . $el]));
                }
                $storeNew->setAdvancedId($roleId);
                $storeNew->save();
            }
        }
    }

    /**
     * Validate current user password
     *
     * @return $this
     * @throws UserLockedException
     * @throws \Magento\Framework\Exception\AuthenticationException
     */
    protected function validateUser()
    {
        $password = $this->request->getParam(
            \Magento\User\Block\Role\Tab\Info::IDENTITY_VERIFICATION_PASSWORD_FIELD
        );
        $user = $this->authSession->getUser();
        $user->performIdentityCheck($password);

        return $this;
    }
}
