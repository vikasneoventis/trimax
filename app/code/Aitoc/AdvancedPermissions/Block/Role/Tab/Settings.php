<?php
/**
 * Copyright © 2016 Aitoc. All rights reserved.
 */
namespace Aitoc\AdvancedPermissions\Block\Role\Tab;

class Settings extends \Magento\Backend\Block\Widget\Form\Generic implements \Magento\Backend\Block\Widget\Tab\TabInterface
{

    /**
     * @var \Magento\Store\Model\System\Store
     */
    protected $systemStore;

    /**
     * @var \Aitoc\AdvancedPermissions\Helper\Data
     */
    protected $helper;

    /**
     * @var  \Aitoc\AdvancedPermissions\Model\Role
     */
    protected $role;

    /**
     * @var \Aitoc\AdvancedPermissions\Model\Stores
     */
    protected $stores;

    /**
     * @var \Magento\Store\Model\Group
     */
    protected $group;

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * @var
     */
    protected $roleManager;

    /**
     * Settings constructor.
     *
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry             $registry
     * @param \Magento\Framework\App\Action\Context   $contextManager
     * @param \Magento\Framework\Data\FormFactory     $formFactory
     * @param \Magento\Store\Model\System\Store       $systemStore
     * @param \Aitoc\AdvancedPermissions\Helper\Data  $helper
     * @param \Aitoc\AdvancedPermissions\Model\Role   $roleGen
     * @param \Aitoc\AdvancedPermissions\Model\Stores $stores
     * @param \Magento\Store\Model\Group              $group
     * @param array                                   $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\App\Action\Context $contextManager,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Store\Model\System\Store $systemStore,
        \Aitoc\AdvancedPermissions\Helper\Data $helper,
        \Aitoc\AdvancedPermissions\Model\Role $roleGen,
        \Aitoc\AdvancedPermissions\Model\Stores $stores,
        \Magento\Store\Model\Group $group,
        array $data = []
    ) {
        parent::__construct($context, $registry, $formFactory, $data);
        $this->_objectManager = $contextManager->getObjectManager();
        $this->systemStore   = $systemStore;
        $this->helper        = $helper;
        $this->role          = $roleGen;
        $this->stores        = $stores;
        $this->group         = $group;
    }

    /**
     * Get tab label
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabLabel()
    {
        return __('Advanced Permissions: Settings');
    }

    /**
     * Get tab title
     *
     * @return string
     */
    public function getTabTitle()
    {
        return $this->getTabLabel();
    }

    /**
     * Whether tab is available
     *
     * @return bool
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * Whether tab is visible
     *
     * @return bool
     */
    public function isHidden()
    {
        return false;
    }

    /**
     * Is single store
     *
     * @return bool
     */
    public function isSingleStoreMode()
    {
        return $this->_storeManager->isSingleStoreMode();
    }

    /**
     * Get role from registry
     *
     * @return mixed
     */
    public function getRole()
    {
        if (!$this->roleManager) {
            $role               = $this->_coreRegistry->registry('current_role');
            $this->roleManager =
                $this->_objectManager->create('Aitoc\AdvancedPermissions\Model\Role')->loadOriginal($role->getId());
        }

        return $this->roleManager;
    }

    /**
     * get suffix
     *
     * @return string
     */
    public function getSuffix()
    {
        return 'setting';
    }

    /**
     * Get field from role
     *
     * @param $field
     *
     * @return mixed
     */
    public function getFieldValue($field)
    {
        return $this->getRole()->getData($field);
    }

    /**
     * Get field from Global
     *
     * @param $field
     *
     * @return int
     */
    public function getFieldValueUseConfig($field)
    {
        if ($this->getRole()->hasData($field)) {
            return $this->getRole()->getData($field);
        }

        return 1;
    }
}
