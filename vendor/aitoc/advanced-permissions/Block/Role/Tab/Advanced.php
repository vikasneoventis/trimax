<?php
/**
 * Copyright © 2018 Aitoc. All rights reserved.
 */
namespace Aitoc\AdvancedPermissions\Block\Role\Tab;

use Magento\Backend\Block\Widget\Tab\TabInterface;

class Advanced extends \Magento\Backend\Block\Widget\Form\Generic implements TabInterface
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
     * @var \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory
     */
    protected $categories;

    /**
     * Advanced constructor.
     *
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\App\Action\Context   $contextManager
     * @param \Magento\Framework\Registry             $registry
     * @param \Magento\Framework\Data\FormFactory     $formFactory
     * @param \Magento\Store\Model\System\Store       $systemStore
     * @param \Aitoc\AdvancedPermissions\Helper\Data  $helper
     * @param \Aitoc\AdvancedPermissions\Model\Role   $roleGen
     * @param \Aitoc\AdvancedPermissions\Model\Stores $stores
     * @param \Magento\Store\Model\Group              $group
     * @param \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $categories,
     * @param array                                   $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\App\Action\Context $contextManager,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Store\Model\System\Store $systemStore,
        \Aitoc\AdvancedPermissions\Helper\Data $helper,
        \Aitoc\AdvancedPermissions\Model\Role $roleGen,
        \Aitoc\AdvancedPermissions\Model\Stores $stores,
        \Magento\Store\Model\Group $group,
        \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $categories,
        array $data = []
    ) {
        parent::__construct($context, $registry, $formFactory, $data);
        $this->objectManager = $contextManager->getObjectManager();
        $this->systemStore   = $systemStore;
        $this->helper        = $helper;
        $this->role          = $roleGen;
        $this->stores        = $stores;
        $this->group         = $group;
        $this->categories    = $categories;
    }

    /**
     * Get tab label
     *
     * @return \Magento\Framework\Phrase
     */
    public function getTabLabel()
    {
        return __('Advanced Permissions: Access');
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
     * Prepare form
     *
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function _prepareForm()
    {
        $role    = $this->_coreRegistry->registry('current_role');
        $roleAdv = $this->objectManager->create('Aitoc\AdvancedPermissions\Model\Role')->loadOriginal($role->getId());
        $form    = $this->_formFactory->create();
        $limit   = 0;
        if ($roleAdv) {
            $limit = $roleAdv->getScope();
        }
        $fieldset = $form->addFieldset('base_fieldset', ['legend' => __('Advanced Permissions')]);
        $fieldset->addField(
            'access_scope',
            'hidden',
            ['name' => 'access_scope', 'id' => 'access_scope', 'value' => 'store_view']
        );
        $fieldset->addField(
            'radio_limits_one',
            'radio',
            [
                'after_element_html' => __("Disabled"),
                'required'           => true,
                'name'               => 'radio_limits',
                'checked'            => ($limit == 0) ? "checked" : "",
                'class'              => 'admin__control-radio',
                'value'              => '0'
            ]
        );
        $fieldset->addField(
            'radio_limits_two',
            'radio',
            [
                'after_element_html' => __("Limit Access by Store View/Category"),
                'required'           => true,
                'name'               => 'radio_limits',
                'checked'            => ($limit == 1) ? "checked" : "",
                'class'              => 'admin__control-radio',
                'value'              => "1"
            ]
        );
        $fieldset->addField(
            'radio_limits_three',
            'radio',
            [
                'after_element_html' => __("Limit Access by Website"),
                'required'           => true,
                'name'               => 'radio_limits',
                'checked'            => ($limit == 2) ? "checked" : "",
                'class'              => 'admin__control-radio',
                'value'              => "2"
            ]
        );

        $fieldsetLS = $form->addFieldset('limits_store', ['legend' => __('Limit Access by Store View/Category')]);

        $fieldsetLS->addType('text_category_ids', 'Aitoc\AdvancedPermissions\Block\Product\Form\Category');
        $fieldsetLS->addType('store_tree', 'Aitoc\AdvancedPermissions\Block\Role\Form\StoreTree');
        $results    = [];
        $stores     = [];
        $websiteIds = [];
        $view       = 0;
        $websites   = $this->systemStore->getStoreValuesAllForForm();

        if ($roleAdv) {
            $results    = $this->getAllowedStoreViewIds($roleAdv->getId());
            $stores     = $this->getAllowedStoreIds($roleAdv->getId());
            $websiteIds = explode(",", $roleAdv->getWebsiteId());
            $view       = $roleAdv->getViewAll();
        }
        
        $rootCategories = $this->categories->create();
        $rootCategories->addRootLevelFilter()->load();
        
        foreach ($websites as $values) {
            $fieldsetLS->addField(
                'select_stores' . $values['value'],
                'store_tree',
                [
                    'label'    => __($values['label']),
                    'required' => true,
                    'values'   => $values['scopes'],
                    'results'  => $results,
                    'parents'  => $stores
                ]
            );
            
            if (!empty($values['scopes'])) {
                for ($index=0; $index<count($values['scopes']); $index++) {
                    $store = $values['scopes'][$index];
                    $item  = $this->group->load($store['value']);
                    $field = $fieldsetLS->addField(
                        'category_ids' . $item->getId(),
                        'text_category_ids',
                        [
                            'label'    => __('Categories for ' . $item->getName()),
                            'required' => false,
                            'class'    => 'category_ids',
                            'style'    => (!in_array($item->getId(), $stores)) ? 'display:none' : '',
                            'name'     => 'category_ids' . $item->getId(),
                            'root_ids' => $rootCategories->getAllIds(),
                            'show_id'  => $item->getRootCategoryId()
                        ]
                    );
                    $renderer = $this->getLayout()->createBlock(
                        'Aitoc\AdvancedPermissions\Block\Product\Form\Renderer\Fieldset\Element'
                    );
                    $field->setRenderer($renderer);
                }
            }
        }
        $fieldsetLW = $form->addFieldset('limits_website', ['legend' => __('Limit Access by Store Website')]);

        $field = $fieldsetLW->addField(
            'select_websites',
            'multiselect',
            [
                'label'    => __('Website'),
                'required' => true,
                'name'     => 'websites[]',
                'values'   => $this->systemStore->getWebsiteValuesAllForForm()
            ]
        );

        $renderer = $this->getLayout()->createBlock(
            'Magento\Backend\Block\Store\Switcher\Form\Renderer\Fieldset\Element'
        );

        $field->setRenderer($renderer);

        $values                       = [];
        $values['radio_limits_one']   = 0;
        $values['radio_limits_two']   = 1;
        $values['radio_limits_three'] = 2;
        if ($stores) {
            foreach ($stores as $item) {
                $values['category_ids' . $item] = $this->getCategoryIds($roleAdv->getId(), $item);
            }
        }

        if (!$roleAdv->getWebsiteId()) {
            $websiteIds = array_map(function ($x) {
                return $x['value'];
            }, $this->systemStore->getWebsiteValuesAllForForm());
        }
        
        $values['select_websites'] = $websiteIds;
        $form->setValues($values);

        $this->setForm($form);

        return parent::_prepareForm();
    }

    /**
     * Get websites
     *
     * @return \Magento\Store\Model\Website[]
     */
    public function getWebsites()
    {
        $websites = $this->_storeManager->getWebsites();
        if ($websiteIds = $this->getWebsiteIds()) {
            foreach (array_keys($websites) as $websiteId) {
                if (!in_array($websiteId, $websiteIds)) {
                    unset($websites[$websiteId]);
                }
            }
        }

        return $websites;
    }

    /**
     * Get Stores
     *
     * @param $id
     *
     * @return array
     */
    public function getAllowedStoreViewIds($id)
    {
        $codes  = [];
        $stores = $this->stores->getCollection()->setRoleFilter($id);
        if ($stores) {
            foreach ($stores as $item) {
                $array = explode(",", $item->getStoreViewIds());
                if (count($codes) > 0) {
                    $codes = array_merge($codes, $array);
                } else {
                    $codes = $array;
                }
            }
        }

        return $codes;
    }

    /**
     * Get allowed stores of role
     *
     * @param $id
     *
     * @return array
     */
    public function getAllowedStoreIds($id)
    {
        $elements = [];
        $stores   = $this->stores->getCollection()->setRoleFilter($id);
        foreach ($stores as $item) {
            $elements[] = $item->getStoreId();
        }

        return $elements;
    }

    /**
     * * Get allowed collection of role
     *
     * @param $id
     * @param $store
     *
     * @return array
     */
    public function getCategoryIds($id, $store)
    {
        $categorys = [];
        $stores    = $this->stores->getCollection()->setRoleFilter($id);
        foreach ($stores as $item) {
            if ($item->getStoreId() == $store) {
                $categorys = explode(",", $item->getCategoryIds());
            }
        }

        return $categorys;
    }
}
