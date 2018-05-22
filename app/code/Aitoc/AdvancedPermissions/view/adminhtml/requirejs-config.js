/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

var config = {
    config: {
        mixins: {
            'Magento_Ui/js/form/element/abstract': {  // Target module
                'Aitoc_AdvancedPermissions/js/abstract-mixin': true  // Extender module
            }
        }
    },
    map: {
        '*': {
            rolesTree: 'Magento_User/js/roles-tree',
            editInventoryFields: 'Aitoc_AdvancedPermissions/js/edit',
            'aitapTreeSuggest': 'Aitoc_AdvancedPermissions/js/custom-tree-suggest'
        }
    }
};
