define([
    "jquery",
    "jquery/ui"
], function ($) {
    "use strict";

    return function (config, element) {
        jQuery(document).ready(function ($) {
            var unitsAllow = config.allow_units;
            var parentElement;

            if (unitsAllow == "1") {
                renderUnits(config, element);
            }

            setStyles(config, element);
        });
    };
});

function renderUnits(config, element)
{
    var mode = config.mode;
    var pricePer = config.price_per;
    var pricePerDivider = config.price_per_divider;

    var unitElement = '<span class="aitocUnit">' + pricePerDivider + ' ' + pricePer + '</span>';

    switch(mode) {
        case 'order_admin':
            var parentElements = [];
            var parentElementPriceOriginal = jQuery(element).parents('tbody').find('td.col-price-original');
            var parentElementPrice = jQuery(element).parents('tbody').find('td.col-price .price-excl-tax');

            parentElements.push(parentElementPriceOriginal);
            parentElements.push(parentElementPrice);
            break;

        default :
            return;
            break;
    }

    parentElements.forEach(function (parentElement) {
        parentElement.append(unitElement);
    });
}

function setStyles(config, element)
{

    jQuery('.aitocQtyButton').css({
        'padding'      : '5px 10px',
        'cursor'       : 'pointer',
        'color'        : '#fff',
        'background'   : '#f98b25',
        'border-radius': '50%',
        'margin'       : '0 10px',
        'box-shadow'   : 'none',
        'min-width'    : '30px'
    });
    jQuery('.aitocQtyDiv').css({
        'border': '5px solid transparent',
        'color' : '#f98b25',
        'width' : '1px',
        'margin': '0 10px',
        'cursor': 'pointer'
    });
    jQuery('.aitDownButton').css({
        'display'   : 'inline-flex',
        'border-top': '10px solid'
    });
    jQuery('.aitUpButton').css({
        'display'      : 'inline-block',
        'border-bottom': '10px solid'
    });

    jQuery('.aitocUnit').css({
        'white-space' : 'nowrap',
        'padding-left': '10px'
    });

    jQuery('.col.qty').css({
        'text-align': 'center'
    });

    if (config.mode == 'cart') {
        jQuery('.aitSlider').css({
            'min-width' : '100px',
            'max-width' : '100px',
            'margin'    : '0 auto',
            'margin-top': '10px',
            'background': '#dadada',
            'position'  : 'relative'
        });
    } else {
        jQuery('.aitSlider').css({
            'min-width' : '100px',
            'max-width' : '250px',
            'margin-top': '10px',
            'background': '#dadada',
            'position'  : 'relative'
        });
    }

    if (config.mode == 'grid') {
        jQuery('.field.qty').css({
            'width': '100%',
            'display': 'block'
        });
    }
}
