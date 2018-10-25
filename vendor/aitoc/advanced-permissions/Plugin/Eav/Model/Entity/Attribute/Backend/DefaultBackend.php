<?php
/**
 * Copyright © 2018 Aitoc. All rights reserved.
 */
namespace Aitoc\AdvancedPermissions\Plugin\Eav\Model\Entity\Attribute\Backend;

use Aitoc\AdvancedPermissions\Helper\Data as PermissionHelper;
use Magento\Eav\Model\Entity\Attribute\Backend\DefaultBackend as EavDefault;

/**
 * Plugin for @see EavDefault
 * name = save_eav_value
 */
class DefaultBackend
{
    /**
     * @var PermissionHelper
     */
    private $helper;

    /**
     * @param PermissionHelper  $helper
     */
    public function __construct(
        PermissionHelper $helper
    ) {
        $this->helper = $helper;
    }

    /**
     * Delete values for restricted attributes
     *
     * @param EavDefault                    $object
     * @param \Closure                      $closure
     * @param \Magento\Framework\DataObject $entity
     *
     * @return EavDefault
     */
    public function aroundBeforeSave(EavDefault $object, \Closure $closure, $entity)
    {
        $result = $closure($entity);
        
        if (!$this->helper->isAdvancedPermissionEnabled()) {
            return $result;
        }

        if ($this->helper->isAttributeRestricted($object->getAttribute()) && !$entity->isObjectNew()) {
            $code = $object->getAttribute()->getAttributeCode();
            $attr = ['type_id'];

            if (!in_array($code, $attr)) {
                $entity->unsetData($code);
            }
        }

        return $result;
    }
}
