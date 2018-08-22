<?php
/**
 * Copyright © 2016 Aitoc. All rights reserved.
 */

namespace Aitoc\CheckoutFieldsManager\Ui\Component\Form\Element;

class Text extends \Magento\Ui\Component\Form\Element\AbstractElement
{
    const NAME = 'label';

    /**
     * Get component name
     *
     * @return string
     */
    public function getComponentName()
    {
        return static::NAME;
    }
}
