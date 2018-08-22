/**
 * Copyright © 2016 Aitoc. All rights reserved.
 */
define(
    [
        'ko',
        'underscore',
        'Magento_Checkout/js/model/quote'
    ],
    function (
        ko,
        _,
        quote
    ) {
        'use strict';

        var mixin = _.extend({
            initObservable: function () {
                this._super();

                quote.shippingAddress.subscribe(function (newAddress) {
                    if (newAddress != null && this.source.get('shippingAddress.custom_attributes')) {
                        newAddress.customAttributes = this.source.get('shippingAddress.custom_attributes');
                    }
                }, this);

                return this;
            },

            /**
             * @return {Boolean}
             */
            validateShippingInformation: function () {
               var pred = this._super();
                if (this.source.get('shippingAddress.custom_attributes')) {
                    this.source.set('params.invalid', false);
                    this.source.trigger('shippingAddress.custom_attributes.data.validate');
                }

                if (this.source.get('params.invalid'))
                {
                    return false;
                }
                return pred;
            }

        });

        return function (target) {
            return target.extend(mixin);
        };
    }
);
