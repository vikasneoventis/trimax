define(['jquery', 'underscore', 'jquery/ui'], function ($, _) {
    'use strict';

    $.widget('custom.preorder', {
        options: {
            classes: {
                attributeInput: 'product',
                attributeStock:'',
            },

            jsonConfig: {},
            jsonText: {},
            change:0,
            selectorTag: 'button'
        },


        /**
         * @private
         */
        _init: function () {

        },

        /**
         * @private
         */
        _create: function () {

            var $widget = this,
                container = this.element,
                classes = this.options.classes,
                config = this.options.jsonConfig,
                change = this.options.change;
            if (change) {
                $.each(config, function (i, item) {
                    //  $element(classes)
                    var element = $(container).find('input[name="' + classes.attributeInput + '"][value=' + i + ']').parent();
                    if ($(element).find($widget.options.selectorTag).size() > 0) {
                        $(element).find($widget.options.selectorTag).attr("title", item);
                        $(element).find($widget.options.selectorTag + " span").text(item);
                    } else {
                        $(document).find($widget.options.selectorTag).not(".subscribe").attr("title", item);
                        $(document).find($widget.options.selectorTag).not(".subscribe").find("span").text(item);
                    }

                });
                if (Object.keys($widget.options.jsonText).length > 0) {
                    $.each($widget.options.jsonText, function (i, item) {
                        $(container).find('div.' + $widget.options.classes.attributeStock + ' span').text(item);
                    });
                }
            }
        },
    });

});