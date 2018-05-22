define(
    [
        'jquery',
        'underscore',
    ],
    function ($,_) {
        'use strict';

        var mixin = _.extend({
            defaults       : {
                globalEnabled: window.GlobalEnabled,
                levelScope   : window.LevelScope
            },
            initialize: function () {
                this._super();
                if (!this.globalEnabled) {
                    $('[name="product[weight]"]').attr("disabled", "disabled");
                    $('[name="product[product_has_weight]"]').attr("disabled", "disabled");
                }
                return this;
            },
            setInitialValue: function () {
                this._super();
                if (typeof this.globalEnabled !== 'undefined') {
                    if (!this.globalEnabled && ( this.scopeLabel == '[GLOBAL]' || this.parentScope == 'data.product.stock_data')) {
                        this.disabled(true);
                        if (this.code == 'gift_message_available') {
                            this.visible(false);
                        }
                    }
                    if (!this.globalEnabled && this.index == 'use_config_gift_message_available') {
                        this.visible(false);
                    }
                    if (this.levelScope == 1 && this.scopeLabel == '[WEBSITE]') {
                        this.disabled(true);
                    }
                }
                return this;
            },
        });
        return function (target) {
            return target.extend(mixin);
        };
    }
);
