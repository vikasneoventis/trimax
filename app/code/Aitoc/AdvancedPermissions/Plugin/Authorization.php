<?php
/**
 * Copyright © 2016 Aitoc. All rights reserved.
 */
namespace Aitoc\AdvancedPermissions\Plugin;

class Authorization
{
    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $request;

    /**
     * @var \Aitoc\AdvancedPermissions\Helper\Data
     */
    protected $helper;

    /**
     * @var \Aitoc\AdvancedPermissions\Model\Permissions
     */
    protected $permissionsModel;

    /**
     * @param \Magento\Backend\App\Action\Context          $context
     * @param \Aitoc\AdvancedPermissions\Helper\Data       $helper
     * @param \Aitoc\AdvancedPermissions\Model\Permissions $permissionsModel
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Aitoc\AdvancedPermissions\Helper\Data $helper,
        \Aitoc\AdvancedPermissions\Model\Permissions $permissionsModel
    ) {
        $this->request          = $context->getRequest();
        $this->helper           = $helper;
        $this->permissionsModel = $permissionsModel;
    }

    /**
     * Disallow for direct link.
     *
     * @param \Magento\Framework\Authorization $object
     * @param \Closure                         $proceed
     * @param string                           $resource
     * @param null                             $privilege
     *
     * @return bool
     */
    public function aroundIsAllowed(
        \Magento\Framework\Authorization $object,
        \Closure $proceed,
        $resource,
        $privilege = null
    ) {
        return $this->permissionsModel->isAllowedResource($resource, $proceed($resource, $privilege));
    }
}
