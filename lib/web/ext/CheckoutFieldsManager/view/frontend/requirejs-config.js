/**
 * Copyright © 2016 Aitoc. All rights reserved.
 */

var config = {
    config: {
        mixins: {
            'Magento_Checkout/js/view/billing-address': {  // Target module
                'Aitoc_CheckoutFieldsManager/js/view/billing-address-mixin': true  // Extender module
            },
            'Magento_Checkout/js/view/payment/default': {  // Target module
                'Aitoc_CheckoutFieldsManager/js/view/payment/default-mixin': true  // Extender module
            },
            'Magento_OfflinePayments/js/view/payment/method-renderer/purchaseorder-method': {  // Target module
                'Aitoc_CheckoutFieldsManager/js/view/payment/purchaseorder-method-mixin': true  // Extender module
            },
            'Magento_Checkout/js/view/shipping': {
                'Aitoc_CheckoutFieldsManager/js/view/shipping-mixin': true
            }
        }
    },
    map: {
        '*': {
            'Magento_Checkout/js/view/summary/item/details': 'Aitoc_CheckoutFieldsManager/js/view/summary/item/details',
            'Magento_Checkout/template/billing-address/details.html': 'Aitoc_CheckoutFieldsManager/template/billing-address/details.html',
            'Magento_Checkout/template/shipping-address/address-renderer/default.html': 'Aitoc_CheckoutFieldsManager/template/shipping-address/address-renderer/default.html',
          }
      }
  };
