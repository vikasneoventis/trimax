define([
    "jquery",
    "jquery/ui"
], function ($) {
    "use strict";

    return function (config, element) {

        $(document).ready(function ($) {
            if (config.mode == 'empty') {
                return false;
            }

            var unitsAllow = config.allow_units;
            var parentElement;

            if (unitsAllow == "1") {
                renderUnits(config, element);
            }

            if (config.replace_qty != "0") {
                replaceQty(config, element);
            }

            setStyles(config, element);

        });
    };
});

function renderUnits(config, element) {
    var mode = config.mode;
    var pricePer = config.price_per;
    var pricePerDivider = config.price_per_divider;

    var unitElement = '<span class="aitocUnit">' + pricePerDivider + ' ' + pricePer + '</span>';

    switch (mode) {
        case 'product':

            if (jQuery(element).parents('.special-price').length > 0) {
                var parentElement = jQuery(element).parents('.special-price');
            } else {
                var parentElement =  jQuery(element).parents('.price-box');
            }

            //for luma magento2 theme
            parentElement.css({
                'width': 'initial',
                'font-size': '29px'
            });
            parentElement.find('.price-container').css({
                'float': 'left'
            });
            return;
            break;

        default:
            return;
            break;
    }

    parentElement.append(unitElement);
}

function replaceQty(config, element) {
    var mode = config.mode;
    var replaceQty = config.replace_qty; //1 - dropdown, 2 - slider, 3 - plus-minus, 4 - arrows
    var useQuantities = config.use_quantities.replace(' ', '').split(',');
    var qtyType = config.qty_type;
    var qtyElement;

    if (qtyType == 1) {
        var dynamicUseQuantities = [];
        var tempQty = parseFloat(config.start_qty);
        var qtyIncrement = parseFloat(config.qty_increment);
        var endQty = parseFloat(config.end_qty);

        do {
            dynamicUseQuantities.push(tempQty);
            tempQty += qtyIncrement;
        } while (tempQty < endQty);
        dynamicUseQuantities.push(endQty);

        useQuantities = dynamicUseQuantities;
    }

    //get qty element
    if (mode === 'product') {
        var oldElementsCount = jQuery(element).parents('.product-info-main').find('.control').find('.aitocElement ').length;
        if (oldElementsCount != 0) {
            return;
        }
        qtyElement = jQuery(element).parents('.product-info-main').find('#qty');
    } else if (mode === 'cart') {
        qtyElement = jQuery(element);
    } else if (mode === 'wishlist' || mode === 'grouped_view') {
        qtyElement = jQuery(element).parents('.product-item-info').find('input.qty');
        if (jQuery(element).parents('.product-item-info').find('.aitocElement').length > 0) {
            return;
        }

        //for grouped product
        if (qtyElement.length == 0) {
            qtyElement = jQuery(element).parents('tbody').find('input.qty');
        }
    } else {
        return;
    }

    //get original attributes
    var elementOptions = {
        'id': qtyElement.attr('id'),
        'class': qtyElement.attr('class'),
        'name': qtyElement.attr('name'),
        'data-cart-item-id': qtyElement.attr('data-cart-item-id')
    };

    //prepare use quantities array
    useQuantities.forEach(function (item, i) {
        useQuantities[i] = parseFloat(parseFloat(item).toFixed(3));
    });
    var currentValue = qtyElement.val();
    if (useQuantities.indexOf(parseFloat(currentValue)) == -1 && (mode === 'cart' || mode === 'wishlist' || mode === 'grouped_view')) {
        useQuantities.push(parseFloat(currentValue));
    } else if (mode != 'cart' && mode != 'wishlist' && mode != 'grouped_view') {
        currentValue = useQuantities[0];
    }
    useQuantities.sort(function (a, b) {
        return a - b;
    });

    //render custom element
    switch (replaceQty) {
        case '1':
            renderDropdown(useQuantities, qtyElement, elementOptions, currentValue);
            break;
        case '2':
            renderSlider(useQuantities, qtyElement, elementOptions, currentValue);
            break;
        case '3':
            renderPlusMinus(useQuantities, qtyElement, elementOptions, currentValue);
            break;
        case '4':
            renderArrows(useQuantities, qtyElement, elementOptions, currentValue);
            break;
    }
}

function renderDropdown(useQuantities, qtyElement, elementOptions, currentValue) {
    var parentContainer = qtyElement.parent();
    var dropdown = document.createElement('select');
    dropdown.classList.add('aitocElement');

    useQuantities.forEach(function (item) {
        var option = document.createElement('option');
        option.value = parseFloat(item);
        option.innerText = item;
        dropdown.append(option);
    });


    dropdown.value = currentValue;

    //set attributes
    for (var attr in elementOptions) {
        if (elementOptions[attr] != undefined) {
            dropdown[attr] = elementOptions[attr];
        }
    }

    dropdown.style = 'min-width: 75px';

    parentContainer.append(dropdown);

    //remove original input element
    qtyElement.remove();
}

function renderSlider(useQuantities, qtyElement, elementOptions, currentValue) {
    var sliderElement = '<div class="aitSlider aitocElement"><div id="custom-handle" class="ui-slider-handle"></div></div>';
    qtyElement.parent().append(sliderElement);
    qtyElement.val(currentValue);
    qtyElement.hide();

    var sliderStep =  calculateStep(useQuantities);
    var handle = qtyElement.parent().find('#custom-handle');
    var slider = qtyElement.parent().find('.aitSlider').slider({
        orientation: 'horizontal',
        step: sliderStep,
        min: useQuantities[0],
        max: useQuantities[useQuantities.length - 1],
        value: currentValue,
        create: function () {
            handle.text(jQuery(this).slider("value"));
            qtyElement.parent().find('#custom-handle').css({
                'width': '2.5em',
                'height': '1.6em',
                'top': '50%',
                'margin-top': '-.8em',
                'text-align': 'center',
                'line-height': '1.6em',
                'color': '#fff',
                'cursor': 'pointer'
            });
        },
        slide: function (event, ui) {
            ui.value = parseFloat(ui.value);
            if (useQuantities.indexOf(ui.value) == -1) {
                return false;
            }
            handle.text(ui.value);
            qtyElement.val(ui.value);
            qtyElement.trigger('change');
        }
    });
}



function calculateStep(values) {
    var len, a, b;
    len = values.length;
    if (!len) {
        return null;
    }
    a = values[0];
    for (var i = 1; i < len; i++) {
        b = values[i];
        a = findGCD(a, b);
    }
    return a;
}

function findGCD(x, y) {
    if ((typeof x !== 'number') || (typeof y !== 'number'))
        return false;
    x = Math.abs(x);
    y = Math.abs(y);
    while(y) {
        var t = y;
        y = x % y;
        x = t;
    }
    return x;
}

function renderPlusMinus(useQuantities, qtyElement, elementOptions, currentValue) {
    var parentContainer = qtyElement.parent();
    var minusElement = '<button class="aitocElement aitocQtyButton aitMinusButton"> - </button>';
    var plusElement = '<button class="aitocElement aitocQtyButton aitPlusButton"> + </button>';
    //globalUseQuantities[elementOptions.id] = useQuantities;

    parentContainer.html(minusElement + parentContainer.html() + plusElement);

    //set default value
    parentContainer.find('#' + elementOptions.id).val(currentValue);

    parentContainer.on("click", ".aitMinusButton", function () {
        var localUseQuantities = useQuantities;
        var currentQty = parseFloat(parentContainer.find('input').val());
        //var currentQty = parseFloat(parentContainer.find('#' + elementOptions.id).val());

        if (currentQty === localUseQuantities[0]) {
            return false;
        } else {
            parentContainer.find('input').val(localUseQuantities[localUseQuantities.indexOf(currentQty) - 1]);
            parentContainer.find('input').trigger('change');
            //parentContainer.find('#' + elementOptions.id).val(localUseQuantities[localUseQuantities.indexOf(currentQty) - 1]);
            return false;
        }
    });

    parentContainer.on("click", ".aitPlusButton", function () {
        var localUseQuantities = useQuantities;
        var currentQty = parseFloat(parentContainer.find('input').val());
        //var currentQty = parseFloat(parentContainer.find('#' + elementOptions.id).val());

        if (currentQty === localUseQuantities[localUseQuantities.length - 1]) {
            return false;
        } else {
            parentContainer.find('input').val(localUseQuantities[localUseQuantities.indexOf(currentQty) + 1]);
            parentContainer.find('input').trigger('change');
            //parentContainer.find('#' + elementOptions.id).val(localUseQuantities[localUseQuantities.indexOf(currentQty) + 1]);
            return false;
        }
    });
}

function renderArrows(useQuantities, qtyElement, elementOptions, currentValue) {
    var parentContainer = qtyElement.parent();
    var minusElement = '<div class="aitocElement aitocQtyDiv aitDownButton"></div>';
    var plusElement = '<div class="aitocElement aitocQtyDiv aitUpButton"></div>';

    //globalUseQuantities[elementOptions.id] = useQuantities;

    parentContainer.html(minusElement + parentContainer.html() + plusElement);

    //set default value
    parentContainer.find('#' + elementOptions.id).val(currentValue);

    parentContainer.on("click", ".aitDownButton", function () {
        var localUseQuantities = useQuantities;
        var currentQty = parseFloat(parentContainer.find('input').val());

        if (currentQty === localUseQuantities[0]) {
            return false;
        } else {
            parentContainer.find('input').val(localUseQuantities[localUseQuantities.indexOf(currentQty) - 1]);
            parentContainer.find('input').trigger('change');
            return false;
        }
    });

    parentContainer.on("click", ".aitUpButton", function () {
        var localUseQuantities = useQuantities;
        var currentQty = parseFloat(parentContainer.find('input').val());

        if (currentQty === localUseQuantities[localUseQuantities.length - 1]) {
            return false;
        } else {
            parentContainer.find('input').val(localUseQuantities[localUseQuantities.indexOf(currentQty) + 1]);
            parentContainer.find('input').trigger('change');
            return false;
        }
    });
}

function setStyles(config, element) {

    jQuery('.aitocQtyButton').css({
        'padding': '5px 10px',
        'cursor': 'pointer',
        'color': '#fff',
        'background': '#f98b25',
        'border-radius': '50%',
        'margin': '0 10px',
        'box-shadow': 'none',
        'min-width': '30px'
    });
    jQuery('.aitocQtyDiv').css({
        'border': '5px solid transparent',
        'color': '#f98b25',
        'width': '1px',
        'margin': '0 10px',
        'cursor': 'pointer'
    });
    jQuery('.aitDownButton').css({
        'display': 'inline-flex',
        'border-top': '10px solid'
    });
    jQuery('.aitUpButton').css({
        'display': 'inline-block',
        'border-bottom': '10px solid'
    });

    jQuery('.aitocUnit').css({
        'white-space': 'nowrap',
        'padding-left': '0px'
    });

    jQuery('.col.qty').css({
        'text-align': 'center'
    });

    if (config.mode == 'cart') {
        jQuery('.aitSlider').css({
            'min-width': '100px',
            'max-width': '100px',
            'margin': '0 auto',
            'margin-top': '10px',
            'background': '#dadada',
            'position': 'relative'
        });
    } else {
        jQuery('.aitSlider').css({
            'min-width': '100px',
            'max-width': '200px',
            'margin-top': '10px',
            'background': '#dadada',
            'position': 'relative'
        });
    }

    if (config.mode == 'wishlist') {
        jQuery('.field.qty').css({
            'width': '100%',
            'display': 'block'
        });
    } else if(config.mode === 'grouped_view') {
        jQuery('.col.qty').css({
            'min-width': '162px'
        });
    }

}
