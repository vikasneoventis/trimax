/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

var config = {
    config: {
        mixins: {
            'Magento_Swatches/js/swatch-renderer': {
                'Aitoc_PreOrders/js/swatch-renderer-mixin': true
            }
        }
    },
    map: {
        '*': {
            preorder: 'Aitoc_PreOrders/js/preorder',
            catalogAddToCart: "Aitoc_PreOrders/js/catalog-add-to-cart",
            "Magento_Catalog/js/catalog-add-to-cart": "Aitoc_PreOrders/js/catalog-add-to-cart"
        }
    }
};
