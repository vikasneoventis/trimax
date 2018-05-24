
define([
    'jquery',
    'jquery/ui'
], function ($) {

    return {

        addAllButton : null,

        peTimeoutId : 0,
        peQtyIconIsPressed : 0,
        isTouch : false,


        _create: function(){

            $.extend(this, this.options);

            this._on({
                'click .pe-add-column button' : this.addItemToCart
            });


            $('input.qty').on(
                'focus blur',
                $.proxy(this.checkQty, this)
            );

            $('.pe-image-container span').on(
                'click touchstart',
                $.proxy(this.highlightProducts, this)
            );

            $('.pe-image-container span').on({
                mouseover: function(e) {
                    if(!this.isTouch) {
                        var detailId = '#short_des_' + $(this).attr('data-number');
                        $(detailId).show();
                    }
                },

                mouseout: function(e) {
                    var detailId = '#short_des_'+$(this).attr('data-number');
                    $(detailId).hide();
                }
            });

            $('span.pe-qty-icon').on(
                'mousedown touchstart',
                $.proxy(this.onQtyIconMousedown, this)
            ).on(
                'mouseup mouseleave touchend',
                $.proxy(this.onQtyIconMouseup, this)
            );

            this.addAllButton = $('#'+this.addAllButtonId);
            this.addAllButton.prop('title', this.addAllButtonTitle);
            this.addAllButton.find('span').text(this.addAllButtonTitle);

            this.addAllButton.on('click', this.addAllToCart);
        },


        highlightProducts : function (e) {
            if (!this.isTouch){
                if (e.type == "touchstart")
                    this.isTouch = true;
            } else {
                if (e.type != "touchstart")
                    return;
            }

            var label = $(e.target);
            var number = label.data("number");

            $('span.pe-active').removeClass('pe-active');
            label.addClass('pe-active');

            $('tr.pe-highlight').removeClass('pe-highlight');

            var rows = $('.pe-product-'+number);
            if (rows.length == 0){
                return;
            }

            rows.addClass('pe-highlight');

            var qtyFields = $('.pe-highlight input.qty');
            var firstEl = qtyFields.length ? qtyFields.eq(0) : rows.eq(0);
            $('html, body').animate({scrollTop: firstEl.offset().top - ($(window).height() / 2)}, 1000, function(){if (firstEl.hasClass('qty')){firstEl.focus()}});
        },


        addItemToCart : function (e) {
            var button = $(e.target).closest('td').find('button');
            var qtyEl = button.closest('tr').find('input.qty');
            $('input.qty').each(function(){
                if (this.id != qtyEl[0].id){
                    this.value = 0;
                }
            });

            if (qtyEl.val() < 1)
                qtyEl.val(1);

            $('.pe-product-table button.tocart').removeClass('tocart');

            button.addClass('tocart');

            this.addAllButton.click();
        },


        addAllToCart : function (e) {
            if (!e.originalEvent)
                return true;

            $('.pe-product-table button.tocart').removeClass('tocart');

            return true;
        },


        checkQty : function (e) {
            var qtyInput = $(e.target);
            var value = parseInt(qtyInput.val());
            if (isNaN(value) || value < 1){
                if (qtyInput[0] === document.activeElement){
                    qtyInput.val('');
                } else {
                    qtyInput.val(0);
                }
            }
        },


        updateQty : function (el) {

            var qty = el.prev('input');
            var direction = 1;

            if (qty.length == 0){
                qty = el.next('input');
                direction = -1;
            }

            var value = parseInt(qty.val());
            if (isNaN(value) || value < 0){
                value = 0;
            }

            if (direction > 0){
                qty.val(value + 1);
            } else {
                if (value > 0){
                    qty.val(value - 1);
                }
            }

            if (this.peQtyIconIsPressed){
                var time = this.peTimeoutId == 0 ? 500 : 100;
                this.peTimeoutId = setTimeout($.proxy(this.updateQty, this, el), time);
            }
        },


        onQtyIconMousedown: function (e) {
            if (!this.isTouch){
                if (e.type == "touchstart")
                    this.isTouch = true;
            } else {
                if (e.type != "touchstart")
                    return;
            }
            this.peQtyIconIsPressed = 1;
            var el = $(e.target).hasClass('pe-qty-icon') ? $(e.target) : $(e.target).closest('.pe-qty-icon');
            this.updateQty(el);
        },

        onQtyIconMouseup: function (e) {
            if (this.peQtyIconIsPressed == 0){
                return;
            }
            clearTimeout(this.peTimeoutId);
            this.peQtyIconIsPressed = 0;
            this.peTimeoutId = 0;
        }


    };

});
    
