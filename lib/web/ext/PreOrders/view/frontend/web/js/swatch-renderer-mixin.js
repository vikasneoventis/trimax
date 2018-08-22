define(
    [
        'jquery',
        'underscore',
        'jquery/ui',
        'jquery/jquery.parsequery',
    ],
    function ($, _) {
        'use strict';

        return function (target) {
            $.widget('mage.SwatchRenderer', target, {
                options: {
                    attrPreorder        : 'preorder',
                    attrPreorderdescript: 'preorderdescript',
                    preorderMatrix      : []
                },
                /**
                 *
                 * @private
                 */
                _init  : function () {
                    if (this.options.jsonConfig !== '' && this.options.jsonSwatchConfig !== '') {
                        var $widget = this;
                        $.each(this.options.jsonConfig.attributes, function (index, item) {
                            if ((item.code == $widget.options.attrPreorder) || (item.code == $widget.options.attrPreorderdescript)) {
                                $widget.options.preorderMatrix.push($widget.options.jsonConfig.attributes[index]);
                                delete $widget.options.jsonConfig.attributes[index];
                            }
                        });
                    }
                    this._super();
                },

                /**
                 * Add note for pre-order
                 *
                 * @param product
                 * @private
                 */
                _PreorderScript: function (product) {
                    var $widget = this;
                    var productId = $widget.options.jsonConfig.productId;
                    $.each($widget.options.preorderMatrix, function () {
                        if ((this.code == $widget.options.attrPreorder) || (this.code == $widget.options.attrPreorderdescript)) {
                            var $code = this.code;
                            $.each(this.options, function () {
                                if ($widget._inArray(product, this.products)) {
                                    $widget._replaceBlocks($code, this.label, $widget, productId);
                                }
                            })
                        }
                    });

                },

                /**
                 * Replace block
                 *
                 * @param code
                 * @param label
                 * @param widget
                 * @param product
                 * @private
                 */
                _replaceBlocks: function (code, label, widget, product) {
                    if (code == widget.options.attrPreorder) {
                        if ($("#product-addtocart-button").size() > 0) {
                            $("#product-addtocart-button").text(label);
                        } else {
                            if ($('input[name="product"][value=' + product + '] ~ button').size() > 0) {
                                $('input[name="product"][value=' + product + '] ~ button span').text(label);
                            }
                        }
                    }
                    if (code == widget.options.attrPreorderdescript) {
                        if ($(".stock").size() > 0) {
                            $(".stock").text(label);
                        }
                    }
                },

                /**
                 *   Check in_array
                 *
                 * @param product
                 * @param arrays
                 * @returns {number}
                 * @private
                 */
                _inArray: function (product, arrays) {
                    var result = 0;
                    $.each(arrays, function () {
                        if (parseInt(product) == parseInt(this)) {
                            result = 1;
                        }
                    });

                    return result;
                },

                /**
                 *
                 * @param $this
                 * @param response
                 * @private
                 */
                _ProductMediaCallback: function ($this, response) {
                    this._PreorderScript(response.product);
                    this._super($this, response);
                }
            });


            return $.mage.SwatchRenderer;
        };
    });
