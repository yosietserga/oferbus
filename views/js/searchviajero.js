var origen_selected = null,
        destino_selected = null,
        month_selected = null;
var Destinos = null;
$(document).ready(function () {     
    $('#ishiproductstab').hide();
    $('#ishiproductsblock .home-title').text(' ')
    if(typeof($('#displayTopViajero').attr('data-des')) !=='undefined'){
        Destinos = JSON.parse($('#displayTopViajero').attr('data-des'));
    origenes = JSON.parse($('#displayTopViajero').attr('data-ori'));
    }    
    $('#select-origen').select2({
        id: '-1',
        placeholder: "Seleccione..",
        language: "es",
        allowClear: true
    });

    $('#select-destino').select2({
        id: '-1',
        placeholder: "Seleccione..",
        language: "es",
        allowClear: true
    });

    $('#select-salida').select2({
        id: '-1',
        placeholder: "Seleccione..",
        language: "es",
        allowClear: true
    });

    $("#btn-restart").click(function (e) {
        e.preventDefault();
        var btn = $(this);
        btn.addClass("btn-hide").fadeOut();
        $('#select-origen').val("").trigger('change');
        $('#select-destino').val("").trigger('change');
        $('#select-salida').val("").trigger('change');
        loadProducts(null, null, null, 1);
    });

    $('#select-origen').change(function (e) {
        e.preventDefault();
        var select = $(this),
                origen = select.val();
         if (origen === ''||origen === null) {
            getAll();
        }else{
            $.ajax({
                url: url_ajax_search,
                method: 'post',
                data: {
                    action: 'getDestinysByOrigin',
                    origin: origen
                },
                success: function (destinies) {
                    destinies = JSON.parse(destinies);
                    $('#select-destino').empty();
                    //var options = $('#select-destino').find('option');
                    //options.remove();
                    for (var i = 0; i < destinies.length; i++) {
                        var destiny = destinies[i];

                        var option = new Option(destiny.destiny, destiny.id_destiny, false, false);
                        $('#select-destino').append(option)
                    }
//                    $('#select-destino').val(null).trigger("change");
                }
            });
        }
    });
    $('#select-destino').change(function (e) {
        e.preventDefault();
        var select = $(this),
                destino = select.val();
        if (destino === ''||destino === null) {
            getAll();
        }else{
            $.ajax({
                url: url_ajax_search,
                method: 'post',
                data: {
                    action: 'getOriginesByDestiny',
                    destino: destino
                },
                success: function (origenes) {
                    origenes = JSON.parse(origenes)
                    //var options = $('#select-origen').find('option');
                    //options.remove();
                    for (var i = 0; i < origenes.length; i++) {
                        var origen = origenes[i];

                        var option = new Option(origen.origen, origen.id_origen, false, false);
                        $('#select-origen').append(option)
                    }
//                    $('#select-origen').val(null).trigger("change");
                }
            });
        } 
    });

    $("#form-search-product").submit(function (e) {
        e.preventDefault();
        var form = $(this),
                btn = form.find('button[type=submit]'),
                text_btn = btn.html(),
                $origen = $('#select-origen'),
                origen = $origen.val(),
                $destino = $('#select-destino'),
                destino = $destino.val(),
                $salida = $('#select-salida'),
                salida = $salida.val(),
                btn_restart = $("#btn-restart");        
        if ((origen == '' || origen == null) && (destino == '' || destino == null) && (salida == '' || salida == null)) {
            alert('seleccione al menos una opción');
            return;
        }

        if (btn_restart.hasClass('btn-hide')) {
            btn_restart.removeClass('btn-hide').fadeIn();
        }

        origen_selected = origen;
        destino_selected = destino;
        month_selected = salida;

        loadProducts(origen, destino, salida, 1);
    });

    $("#ishi-featured-products").on('click', '.btn-paginate', function (e) {
        e.preventDefault();
        var btn = $(this),
                page = btn.attr("data-page");

        if (btn.hasClass('active'))
            return;
        loadProducts(origen_selected, destino_selected, month_selected, page);
    });

    loadProducts(null, null, null, 1);

    if ($('#ishiproductstab').length > 0) {
        // validateApiStatus();
    }
});

function validateApiStatus() {
    $.ajax({
        method: 'post',
        dataType: 'json',
        url: urlAjaxViajero,
        data: {
            action: 'getPackages',
        },
        success: function(packages) {
        },
        error: function(response) {
        }
    });
}

function getAll() {
    var options = $('#select-origen').find('option');
    options.remove();
    $('#select-origen').append('<option></option>');
    $.each(origenes, function (key, origen) {
        var option = new Option(origen.origen, origen.id_origen, false, false);
        $('#select-origen').append(option);

    });
//    $('#select-origen').val(null).trigger("change");
    var options2 = $('#select-destino').find('option');
    options2.remove();
    $('#select-destino').append('<option></option>');
    $.each(Destinos, function (key, destino) {
        var option = new Option(destino.destiny, destino.id_destiny, false, false);
        $('#select-destino').append(option);
    });
//    $('#select-destino').val(null).trigger("change");
}
function loadProducts(origen, destino, salida, page) {
    var container = $("#ishi-featured-products");
    if (typeof(url_ajax_search) !== 'undefined') {
        $("#ishi-featured-products ").html('');
        $.ajax({
            url: url_ajax_search,
            method: 'post',
            data: {
                action: 'getProductSearch',
                origin: origen,
                destiny: destino,
                month_id: salida,
                page: page
            },
            success: function (data) {
                console.log('productos:',data);
                container.html(data);
            }
        });
    }

}