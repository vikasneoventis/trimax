<?php
/**
 * Copyright © 2016 Aitoc. All rights reserved.
 */

namespace Aitoc\PreOrders\Model\Product;

class Type extends \Magento\Catalog\Model\Product\Type
{
    const TYPE_CONFIGURABLE = 'configurable';

    const TYPE_DOWNLOADABLE = 'downloadable';

    const TYPE_GROUPED = 'grouped';
}
