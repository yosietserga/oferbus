/*
 * 2007-2018 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 *  @author PrestaShop SA <contact@prestashop.com>
 *  @copyright  2007-2018 PrestaShop SA
 *  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */
$(document).ready(function () {
    var urlajax = $('#displayTopViajero').attr('data-url');
    var search = $('#displayTopViajero');
    $('.blockcart').hide();
    $('#homepage-slider').after(search);
    var pathUrl = location.pathname.split("/");
    if (pathUrl.length == 2 && pathUrl[1] == '' 
    || pathUrl.length == 4 && pathUrl[3] === "ViajeroProduct") {
        cleanCart();
    }
    $('#origen').autocomplete(
            urlajax,
            {
                minChars: 3,
                dataType: "json",
                formatItem: function (data, i, max, value, term) {
                    return value;
                },
                extraParams: {
                    ajaxSearch: 1,
                    action:'origen'
                }
            }
    ).result(function (event, data, formatted) {
        $.each(data,function(key,value){
           $('#origen').val(value.origen);
       });        
    });

    var contentItems = $('.cart-item .product-line-grid, #js-checkout-summary');
    console.log('asdasasd');
    if (contentItems.length > 0) {
        // Checkout
        loadStorage();
    }

    var newPackageStorage = localStorage.getItem('data-store-package');

    if (newPackageStorage) {
        var newItemsCartItems = $('.cart-overview.js-cart .cart-items');

        localStorage.removeItem('data-store-package');
        if (newItemsCartItems.length == 0) {
            location.reload();
        }
    }
});

var cleanCart = function () {
    $(document).ready(function(e) {
        $.ajax({
            method: 'post',
            dataType: 'json',
            // url: urlAjaxViajero,
            url: prestashop.urls.base_url + "module/viajero/ViajeroProduct",
            data: {
                action: 'cleanCart'
            },
            success: function (product) {
                console.log('Limpiamos Carrito.');
            }
        });
    }); 
}

function loadStorage() {
    const infoStorage = localStorage.getItem('infoStorage');
    const template = `<div class="container-resumen resumen-float" style="padding: 15px; overflow-y:scroll !important;">${infoStorage}</div>`;
    $('.cart-detailed-totals').html(template);
    $('.cart-detailed-totals').find('#comprar').addClass('hide');
    $('#js-checkout-summary').html(template);
    $('#js-checkout-summary').find('#comprar').addClass('hide');
    $('.cart-detailed-actions').find('a').addClass('btn-lg');
    $('#order-items').find('card-title').html('ARTICULOS DEL PAQUETE');
    $('#order-details').find('card-title').html('DETALLES DEL PAQUETE');
}
function goToPackage(link) {
    $(document).ready(function () {
        $(location).attr('href', link);
    });
}

function onlyNumbers(e){
	var key = window.Event ? e.which : e.keyCode
	return (key >= 48 && key <= 57)
}

function validateForm(e){	
	return $('#formFile').Valid();
}