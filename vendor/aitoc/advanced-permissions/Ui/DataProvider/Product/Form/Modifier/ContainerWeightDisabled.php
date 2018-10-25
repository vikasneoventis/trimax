<?php
/**
 * Copyright © 2018 Aitoc. All rights reserved.
 */

namespace Aitoc\AdvancedPermissions\Ui\DataProvider\Product\Form\Modifier;

use Magento\Ui\DataProvider\Modifier\ModifierInterface;

/**
 * Class ContainerWeightDisabled
 */
class ContainerWeightDisabled implements ModifierInterface
{
    public function modifyData(array $data)
    {
        return $data;
    }

    public function modifyMeta(array $meta)
    {
        $meta['product-details']['children']['container_weight']['arguments']['data']['config']['component']
            = 'Aitoc_AdvancedPermissions/js/Ui/form/components/container-weight-group';

        return $meta;
    }
}
