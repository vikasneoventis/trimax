define([
    'underscore',
    'uiRegistry',
    'Magento_Ui/js/form/element/select',
    'Magento_Ui/js/modal/modal'
], function (_, uiRegistry, select, modal) {
    'use strict';

    return select.extend({
        initialize: function () {
            this._super();
            this.changeDependableFieldsStatus(this.value());
            return this;
        },
        onUpdate: function (value) {
            this.changeDependableFieldsStatus(value);
            return this._super();
        },
        changeDependableFieldsStatus: function (value) {
            var staticMethod, dynamicMethod;
            switch (value){
                case 0:
                    staticMethod = 'enable';
                    dynamicMethod = 'disable';
                    break;
                case 1:
                    staticMethod = 'disable';
                    dynamicMethod = 'enable';
                    break;
            }

            // Change Static Qty Fields Status
            this.changeFieldState('use_quantities', staticMethod);
            // Change Dynamic Qty Fields Status
            this.changeFieldState('start_qty', dynamicMethod);
            this.changeFieldState('qty_increment', dynamicMethod);
            this.changeFieldState('end_qty', dynamicMethod);
        },
        changeFieldState: function (fieldIndex, method) {
            var field = uiRegistry.get('index = ' + fieldIndex);
            if(field !== undefined) {
                var useConfigCheckbox = uiRegistry.get('index = use_config_' + fieldIndex);
                var fieldMethod = method;

                if (useConfigCheckbox.checked()) {
                    fieldMethod = 'disable';
                }

                field[fieldMethod]();
                useConfigCheckbox[method]();
            }
            else {
                var self = this;
                setTimeout(function() {
                    self.changeFieldState(fieldIndex, method);
                }, 500);
            }


        }
    });
});