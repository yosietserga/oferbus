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
var texto = "";
var precioOrigen = 0; //origenes
var precioButaca = 0; //butacas
var precioAditional = 0; //adicionales
var precioAbitaciones = 0; //Habitaciones
var precioAbitacionesNinos = 0; //Habitaciones
var precioAbitacionesBebes = 0; //Habitaciones
var precioSalida = 0; // salida agregar fecha
var impuestoSalida = 0; // salida agregar fecha
var porcentajeAditional = 0;
var precioRegimen = 0;
var precioRe = 0;
var limninos = 0;
var limbebes = 0;
var precioRe = 0;
var total = 0; // total del paquete
var totalGeneral = 0;
var destino = [];
var flagcupo = 1;
var arregloPasajeros = [];
var resumen = {
    id_origen: 0,
    precio_origen: 0,
    id_butaca: 0,
    precio_butaca: 0,
    id_salida: 0,
    precio_salida: 0,
    adicionales: 0,
};
//
var impuesto_fix = 0;
var habit_selec = 0;


var hotel_id = -1;
var flagspin = 0;
var alojamientohtml = "";
var aditionales = [];
var habitaciones = [];
var cantNinos = 0;

var data_selected = {
    origen: {
        id: 0,
        city: '',
        date: ''
    },
    butaca: {
        id: 0,
        name: ''
    },
    linea: 0,
    hotels: {
        amount: 0,
        rooms: [],
    },
    aditionals: [],
    package_rooms: [],
    amount_lines: 0
};

var data_resumen = {
    price_hotel_origin: 0,
    price_max: {
        adult: 0,
        child: 0,
        baby: 0
    },
    price_line: 0,
    tarifa: 0,
    impuesto_line: 0,
    impuesto_total: 0,
    price_butaca: 0,
    price_destiny: 0,
    total_adultos: 1,
    total_real_adultos: 0,
    total_childrens: 0,
    total_babys: 0,
    total_adults_childs: 0,
    total_price_adultos: 0,
    total_price_childrens: 0,
    total_price_babys: 0,
    total_price_line: 0,
    max_age_childrens: 0,
    max_age_babys: 0,
    max_age_childrens_hotel: 0,
    max_age_babys_hotel: 0,
    percentage_childrens: 0,
    percentage_babys: 0,
    total_hotel_adults: 0,
    total_hotel_childrens: 0,
    total_hotel_babys: 0,
    total_capacity: 0,
    aditionals: {
        percentage: 0,
        price: 0,
        total_aditionals: 0
    },
    subtotal: 0,
    big_total: 0
};

var cant_room_total = 0;
var inc = 1;

var hotelsGroups = [];

var hotelsGroupSelect = [];

var packageEdit = null;
var preselectPackage = false;

var dataLoadEdit = {
    origen: false,
    salida: false,
    data_rooms: false,
    data_hotels: false,
    butaca: false,
    adicionals: false,
    pasajeros: false
};

var destinyPreselect = 0;

var globalTyperoom = null;


$(document).ready(function() {

    $('.btn-back').click(function(e) {
        e.preventDefault();
        localStorage.setItem('edit-package', true);
        window.history.back();
    });

    $(".container-salidas").removeClass('hide').hide();
	$(".container-info-pasajeros").removeClass('hide').hide();
    $('#category #content-wrapper').removeClass('col-lg-9');
    $('#category #content-wrapper').addClass('col-lg-7');
    if (typeof($('#checkout-delivery-step')) !== 'undefined') {
        $('#checkout-delivery-step h1').html('<span class="step-number">3</span>Comentarios')
    }
    if (typeof($('#cart-summary-product-list')) !== 'undefined') {
        $('#cart-summary-product-list a').remove();
    }
    $('.ubicacion_input_2 ').change('.niñosinp input', function() {
        totalPrice();
    });

    $('.ubicacion_input_2').on('change', '.age-childrens', function() {
        updateTotalChildrens();
    });

    $('.contenedor_centrado').on('click', '.resethotel', function() {
        var des = $(this).attr('data-destiny');
        $('#f' + des).trigger('click');
    });
    $("#info-cantidadniños").on('change', function() {
        $('.ubicacion_input_2').html('');
        cantNinos = $(this).val();
        var inputninos = '';
        var i;
        for (i = 1; i <= cantNinos; i++) {
            inputninos += '<div class="col-4 mb-3"><div class="quantity quantity_new niñosinp"><input class="age-childrens" type="number" min="0" max="17" step="1" data-val="' + i + '" value="0" id="edadniños' + i + '"></div></div>'
        }
        if ($(window).width() > 1024)
            $('.children-dd-desktop .ubicacion_input_2').append(inputninos);
        else {
            $('.children-dd-mobile .ubicacion_input_2').append(inputninos);
        }
        updateTotalChildrens();
        if ($(this).val() == 0) {
            $('.ubicacion_input_2').html('');
            $('.container-child-years').addClass('ocultar');
        } else {
            $('.container-child-years').removeClass('ocultar');
        }


        jQuery('<div class="quantity-nav"><div class="quantity-button quantity-up"><i class="fa fa-angle-up"></i></div><div class="quantity-button quantity-down"><i class="fa fa-angle-down"></i></div></div>').insertAfter('.quantity_new input');
        jQuery('.quantity.quantity_new').each(function() {
            var spinner = jQuery(this),
                input = spinner.find('input[type="number"]'),
                btnUp = spinner.find('.quantity-up'),
                btnDown = spinner.find('.quantity-down'),
                min = input.attr('min'),
                max = input.attr('max');
            btnUp.click(function() {

                $("#ejecutarActualizar").trigger("click");
                var oldValue = parseFloat(input.val());
                if (oldValue >= max) {
                    var newVal = oldValue;
                } else {
                    var newVal = oldValue + 1;
                }
                spinner.find("input").val(newVal);
                //SelectorSlide.val(newVal);.    aquí pone el selector del slide
                spinner.find("input").trigger("change");
            });

            btnDown.click(function() {

                var oldValue = parseFloat(input.val());
                if (oldValue <= min) {
                    var newVal = oldValue;
                } else {
                    var newVal = oldValue - 1;
                }
                spinner.find("input").val(newVal);
                //SelectorSlide.val(newVal);        aquí pone el selector del slide
                spinner.find("input").trigger("change");
            });

        });
    });
    $(".modal-backdrop").removeClass("show");
    //$("#modalGaleria").modal("show");
    $("#modalGaleria").css({ "z-index": "999999999999999999999 !important" });
    $("#modalGaleria .modal-dialog").css({ "margin-top": "80px" });
    $("#modalMapa").css({ "margin-top": "50px" });


    //    $(".seleccionar_hotel").hide();
    $(".tabla-pasajeros").hide();
    $(".boton_intermedio").addClass('animacion_borde_neutro');
    $(".boton_central").addClass('color_neutro');
    //cargarMapa('total', -17.2984809, -64.8917252);

    // BOTONERAS DE INPUT DE PASAJEROS
    jQuery('<div class="quantity-nav"><div class="quantity-button quantity-up"><i class="fa fa-angle-up"></i></div><div class="quantity-button quantity-down"><i class="fa fa-angle-down"></i></div></div>').insertAfter('div.ubicacion_input div.quantity input');
    jQuery('.ubicacion_input .quantity').each(function() {
        var spinner = jQuery(this),
            input = spinner.find('input[type="number"]'),
            btnUp = spinner.find('.quantity-up'),
            btnDown = spinner.find('.quantity-down'),
            min = input.attr('min'),
            max = input.attr('max');
        btnUp.click(function() {
            $("#ejecutarActualizar").trigger("click");
            var oldValue = parseFloat(input.val());
            if (oldValue >= max) {
                var newVal = oldValue;
            } else {
                var newVal = oldValue + 1;
            }
            spinner.find("input").val(newVal);
            //SelectorSlide.val(newVal);.    aquí pone el selector del slide
            spinner.find("input").trigger("change");
        });

        btnDown.click(function() {
            var oldValue = parseFloat(input.val());
            if (oldValue <= min) {
                var newVal = oldValue;
            } else {
                var newVal = oldValue - 1;
            }
            spinner.find("input").val(newVal);
            //SelectorSlide.val(newVal);        aquí pone el selector del slide
            spinner.find("input").trigger("change");
        });

    });
    $('#info-cantidadadultos').on('change', function() {
        var input = $(this);
        amount = input.val();
        data_resumen.total_adultos = parseInt(amount);

        calculatePrices();
        //totalPrice();
    });

    $('#info-cantidadniños').on('change', function() {

        //totalPrice();
    });

    $(".seleccion").click(function() {

        $(".estiloseleccion").css({ "background": "#fff" });
        $(".estiloseleccion").css({ "border": "1px solid rgba(0,0,0,.2)" });

        $("#div" + $(this).attr('id') + "").css({ "background": "#FF7F00" });
        $("#div" + $(this).attr('id') + "").css({ "border": "1px solid rgba(0,0,0,.0)" });

    })
    // SELECCION DE ORIGEN DE SALIDA
    $(".seleccion_vuelo").click(function() {
        var input = $(this);
        selectOrigin(input);

        return;
    });
	
    $("#container-dates-package").on('click', '.seleccion_fecha', function() {
        precioSalida = 0;
        var salida_actual = $(this);

        selectDateLine(salida_actual);
    });

	// SELECCION DE BUTACA
    $(".seleccion_butaca").click(function() {
        var input = $(this);
        selectButaca(input);
    });

    $(".seleccion_adicional").click(function() {
        precioAditional = 0;
        aditionales = [];
        var id = $(this).attr('id').replace("d", "");
        if ($("#adicional" + id + "").hasClass('arrowLeft')) {
            $("#adicional" + id + "").removeClass('arrowLeft');
            $("#adicional" + id + "").addClass('arrowbLeft');
            $("#adicional" + id + "").css({ "background": "#FF7F00" });

        } else {

            $("#adicional" + id + "").addClass('arrowLeft');
            $("#adicional" + id + "").removeClass('arrowbLeft');
            $("#adicional" + id + "").css({ "background": "#A9A9A9" });
        }

        calcularAdicional();
    });

    function calcularAdicional() {
        precioAditional = 0;
        porcentajeAditional = 0;
        var id_adicionales = [];
        //totalPrice();
        $('.seleccion_adicional:checkbox:checked').each(function() {
            var valor = $(this).attr('valor');
            var type = $(this).attr('type_add');
            var ida = $(this).val(),
                id_adicional = $(this).attr("data-id");

            if (type == "$") {
                precioAditional += parseFloat(valor);
            } else if (type == "%") {
                porcentajeAditional += parseFloat(valor);
            }

            aditionales.push(ida);
            resumen.adicionales = aditionales;
            id_adicionales.push(parseInt(id_adicional));
        });

        data_selected.aditionals = id_adicionales;
        data_resumen.aditionals.percentage = porcentajeAditional;
        data_resumen.aditionals.price = precioAditional;

        calculatePrices();
    }

    $(".seleccion_alojamiento").click(function() {
        var id = $(this).attr('id').replace("e", "");

        $(".adicional").removeClass('arrowbLeft');
        $(".adicional").addClass('arrowLeft');

        $("#adicional" + id + "").removeClass('arrowLeft');
        $("#adicional" + id + "").addClass('arrowbLeft');

        $(".adicional").css({ "background": "#A9A9A9" });
        $("#adicional" + id + "").css({ "background": "#FF7F00" });

    })

    function definirPrecioRegimen() {
        precioRegimen = 0;
        var cantidadArreglos = 0;
        //porcentajeAditional = 0;
        if ($.each(destino, function(index, value) {
                var valorAcumuladoRegimen = 0;
                if (value && value != 0) {
                    cantidadArreglos = cantidadArreglos + 1;
                    var atributo = arregloNuevo[index][1].toLowerCase();
                    var idregimen = arregloNuevo[index][2];
                    //precioRegimen
                    $.ajax({
                        method: 'post',
                        dataType: 'json',
                        url: urlAjaxViajero,
                        data: {
                            idregimen: idregimen,
                            atributo: atributo,
                            action: 'getPriceRegimens',
                        },
                        success: function(valor) {
                            var valorRegimen = parseInt(valor[idregimen][atributo]);
                            precioRegimen = precioRegimen + valorRegimen;
                            /*$.each(valor, function (key, respuesta) {
                                console.log(respuesta.""+atributo+"");
                            });*/
                            /*console.log("id: "+idregimen);
                            console.log("valor hasta la fecha "+precioRegimen); */
                            //console.log("Precio n "+ (parseInt(precioRegimen) / parseInt(cantidadArreglos) ) );
                            precioRe = ((parseInt(precioRegimen) / parseInt(cantidadArreglos)));
                            //                    $("#precioregimen").html(precioRe);

                            //console.log("cantidad arreglo: "+cantidadArreglos);
                        }
                    });
                }

            })) {

            setTimeout(function() { totalPrice(); }, 500);


        }
    }

    function totalPrice() {
        return;
        //        var cantidadNiños = $("#info-cantidadniños").val();
        var cantidadAdultos = $("#info-cantidadadultos").val();
        var cantidadAdultosHab = $("#info-cantidadadultos").val();
        var cantninos = 0;
        var cantbebes = 0;
        var cantninosHab = 0;
        var cantbebesHab = 0;
        var i;
        var j;
        var edad = 0;
        var edad2 = 0;
        for (i = 1; i <= cantNinos; i++) {
            edad = $('#edadniños' + i).val();
            if (edad <= edadninos) {
                if (edad <= edadbebes) {
                    cantbebes = cantbebes + 1;
                } else {
                    cantninos = cantninos + 1;
                }
            } else {
                cantidadAdultos = parseInt(cantidadAdultos) + 1;
            }
        }
        for (j = 1; j <= cantNinos; j++) {
            edad2 = $('#edadniños' + j).val();
            if (edad2 <= parseInt(limninos)) {
                if (edad2 <= parseInt(limbebes)) {
                    cantbebesHab = cantbebesHab + 1;
                } else {
                    cantninosHab = cantninosHab + 1;
                }
            } else {
                cantidadAdultosHab = parseInt(cantidadAdultosHab) + 1;
            }
        }
        var totalninosAdi = ((parseInt(precioOrigen) + parseInt(precioSalida) + parseInt(precioButaca) + parseInt(precioAditional) + parseInt(impuestoSalida)) * valueninos / 100) * cantninos;
        var totalbebesAdi = ((parseInt(precioOrigen) + parseInt(precioSalida) + parseInt(precioButaca) + parseInt(precioAditional) + parseInt(impuestoSalida)) * valuebebes / 100) * cantbebes;
        //        var cantidadpersonas = ( parseInt(cantidadNiños) + parseInt(cantidadAdultos) );
        var cantidadpersonas = parseInt(cantidadAdultos);
        //        console.info("este es"+precioRe);
        var precioHabitacionAdulto = parseInt(precioAbitaciones) * cantidadAdultosHab;
        var precioHabitacionNino = parseInt(precioAbitacionesNinos) * cantninosHab;
        var precioHabitacionBebe = parseInt(precioAbitacionesBebes) * cantbebesHab;

        total = ((parseInt(precioOrigen) + parseInt(precioSalida) + parseInt(precioButaca) + parseInt(precioAditional) + parseInt(impuestoSalida)) * cantidadpersonas) + totalninosAdi + totalbebesAdi + precioHabitacionAdulto + precioHabitacionNino + precioHabitacionBebe;
        var porcentajeTotal = (total * (parseFloat("0." + porcentajeAditional)));
        totalGeneral = (total + porcentajeTotal);

        var total_general_format = formatMoney(parseInt(totalGeneral), 2, ',', '.'),
            precio_adicional_format = formatMoney(parseInt(precioAditional) + parseInt(porcentajeTotal), 2, ',', '.'),
            valor_adicional_format = formatMoney(parseInt(precioAditional) + parseInt(porcentajeTotal), 2, ',', '.'),
            valor_impuestos_format = formatMoney(parseInt(impuestoSalida) * cantidadpersonas),
            total_babys_format = formatMoney(parseInt(totalninosAdi), 2, ',', '.'),
            total_babys_format = formatMoney(parseInt(totalbebesAdi), 2, ',', '.'),
            precio_habitaciones_kids = formatMoney(parseInt(precioHabitacionNino), 2, ',', '.'),
            precio_habitaciones_babys = formatMoney(parseInt(precioHabitacionBebe), 2, ',', '.');


        $("#totalPrice").html(total_general_format);
        $("#informacion-total").html("$ " + total_general_format);
        $("#precioAditional").html("$ " + precio_adicional_format);
        $("#valorAdiconales").html("$ " + valor_adicional_format);
        $("#valorImpuestos").html("$ " + valor_impuestos_format);
        $("#ninosadicionales").html("$ " + total_babys_format);
        $("#bebesadicionales").html("$ " + total_babys_format);
        $("#precioHabitacionesninos").html("$ " + precio_habitaciones_kids);
        $("#precioHabitacionesbebes").html("$ " + precio_habitaciones_babys);
    }

    var contadorEjecucion = 0;
    $("#ejecutarActualizar").click(function() {
        alojamientohtml = "";
        precioAbitaciones = 0;
        $("#informacion-alojamientos").html(alojamientohtml);
        $.each(destino, function(index, value) {
            if (value && value != 0) {
                //console.log("llego", arregloNuevo[index][1]);
                //console.log("index: "+index+" valor : "+value);
                $.ajax({
                    method: 'post',
                    dataType: 'json',
                    url: urlAjaxViajero,
                    data: {
                        id_package_destination: parseInt(index),
                        id_package_room: parseInt(value),
                        action: 'getInformationDestination',
                    },
                    success: function(r7) {
                        //                console.info(r7)

                        $.each(r7, function(key, information) {
                            var cantidadAdultosHab = $("#info-cantidadadultos").val();
                            var cantninosHab = 0;
                            var cantbebesHab = 0;
                            var edad2 = 0;
                            var j;
                            limninos = information.limninos;
                            limbebes = information.limbebes;
                            for (j = 1; j <= cantNinos; j++) {
                                edad2 = $('#edadniños' + j).val();
                                if (edad2 <= parseInt(limninos)) {
                                    if (edad2 <= parseInt(limbebes)) {
                                        cantbebesHab = cantbebesHab + 1;
                                    } else {
                                        cantninosHab = cantninosHab + 1;
                                    }
                                } else {
                                    cantidadAdultosHab = parseInt(cantidadAdultosHab) + 1;
                                }
                            }

                            //console.log("nombre hotel: "+information.nombrehotel+", precio habitacion: "+information.preciohabitacion+", cantidad noches: "+information.cantidadnoches+", destino: "+information.destino+", id destino: "+information.iddestino+", id room: "+information.idroom);
                            var valorNetoHabitacion = ((parseInt(information.preciohabitacion) / parseInt(information.cantidadpersonas)));
                            var valorNetoNinos = ((parseInt(information.priceninos) / parseInt(information.cantidadpersonas)));
                            var valorNetoBebes = ((parseInt(information.pricebebes) / parseInt(information.cantidadpersonas)));
                            //                            var totalAdult=( parseInt( valorNetoHabitacion + parseInt(precioButaca) + parseInt(arregloNuevo[index][3]) )*parseInt(cantidadAdultosHab))
                            var totalAdult = (parseInt(valorNetoHabitacion + parseInt(precioButaca) + parseInt(0)) * parseInt(cantidadAdultosHab))
                                //                            var totalNinos=( parseInt( valorNetoNinos+parseInt(precioButaca)+parseInt(arregloNuevo[index][3]))*parseInt(cantninosHab));
                            var totalNinos = (parseInt(valorNetoNinos + parseInt(precioButaca) + parseInt(0)) * parseInt(cantninosHab));
                            var totalBebes = (parseInt(valorNetoBebes) * parseInt(cantbebesHab));
                            console.info(totalAdult);
                            console.info(totalNinos);
                            console.info(totalBebes);
                            console.info(totalAdult + totalNinos + totalBebes)
                            var format_total = formatMoney((totalAdult + totalNinos + totalBebes), 2, '.', ',');

                            //$("#tiporoom"+information.idroom).text().toUpperCase()

                            alojamientohtml = `
                            <div class="col-sm-8" style="font-size:14px;">
                                <h5 class="mb-2">ALOJAMIENTO EN <b>` + information.destino + `</b></h5>
                                <p class="mb-2 mt-2"><b>` + information.nombrehotel + `</b></p>
                                <p class="mb-2 mt-2"><b>` + information.nameroom + `</b></p>
                            </div>
                            <div class="col-sm-4 float_right_precio">
                                <p>

                                     <i><strong>$ ` + format_total + `</strong></i><br>

                                </p>
                            </div>

                            <div class="col-sm-12">
                                <hr style="width: 100%">
                            </div>
                            `;

                            //console.log("cantidad personas: "+information.cantidadpersonas);
                            var valorNetoHabitacion = ((parseInt(information.preciohabitacion) / parseInt(information.cantidadpersonas)));
                            var valorNetoNinos = ((parseInt(information.priceninos) / parseInt(information.cantidadpersonas)));
                            var valorNetoBebes = ((parseInt(information.pricebebes) / parseInt(information.cantidadpersonas)));
                            $("#informacion-alojamientos").append(alojamientohtml);
                            precioAbitaciones = (parseInt(precioAbitaciones) + parseInt(valorNetoHabitacion));
                            precioAbitacionesNinos = (parseInt(precioAbitacionesNinos) + parseInt(valorNetoNinos));
                            precioAbitacionesBebes = (parseInt(precioAbitacionesBebes) + parseInt(valorNetoBebes));

                        });
                        $("#precioHabitaciones").html(precioAbitaciones);

                    }
                });
                $("#precioHabitaciones").html(precioAbitaciones);
                //                definirPrecioRegimen();
            }
        });


    });
    //    var first = $('.contentroo').attr('data-flag');
    //
    //    $('#f'+first).trigger('click');
    var contenidohtml = "";

    $(".boton_contenedor").click(function() {
        texto = $("#" + $(this).attr('id') + " small").text();
        $("#cambio_alojamiento").html('<strong class="">SELECCIONE EL ALOJAMIENTO EN</strong> ' + texto.toUpperCase());
    })

    $("#container-destinations").on('click', '.seleccion_estadia', function() {
        var input = $(this);
        selectDestiny(input);
    });
    $('.boton_central').mouseover(function() {
        var id = $(this).attr('id').replace("estadia", "");
        if ($("#estadia" + id + "").hasClass('color_verde')) {
            $("#estadia" + id + "").addClass('color_rojo');
        }
    });

    $('.boton_central').mouseleave(function() {
        var id = $(this).attr('id').replace("estadia", "");
        if ($("#estadia" + id + "").hasClass('color_verde')) {
            $("#estadia" + id + "").removeClass('color_rojo');
        }
    });

    $('#container-destinations').on('click', '.btn-ver-galeria', function(e) {
        e.preventDefault();
        var btn = $(this),
            roomId = btn.attr('data-id');

        traergaleria(roomId);
    });

    $("#viajeroProduct").on('click', '.datesaccor', function() {
        var tthis = $(this);
        tthis.find('input[name=salida]').prop('checked', true);
        $('.datesaccor').removeClass('green-active');
        setTimeout(function() { tthis.addClass('green-active'); }, 100);
    });



   /* $("#viajeroProduct").on('click', '.seleccion_fecha.rad', function() {
        var tthis = $(this);
        tthis.find('input[name=salida]').prop('checked', true);
        $('.datesaccor').removeClass('green-active');
        setTimeout(function() { tthis.addClass('green-active'); }, 100);
    });*/


    $("#viajeroProduct").on('click', '.estadiaaccor', function() {
        $(this).find('input[name=estadia]').prop('checked', true);
    });
    // CANCELACION DE SELECCION DE HABITACION DE HOTEL
    $("#container-destinations").on('click', '.btn-cancel-room', function(e) {
        var btn = $(this),
            id_destiny = btn.attr("data-destiny"),
            name_destiny = btn.attr("data-name-destiny"),
            destiny = hotelsGroups[id_destiny],
            hotel = destiny[btn.attr('data-hotel')],
            group = hotel.group_rooms[btn.attr('data-group')],
            container_parents = btn.parents('.panel-parent-container');

        $.each(hotelsGroupSelect, function(index, hotelGroupSelect) {
            if (hotelGroupSelect.destiny == id_destiny) {
                delete hotelsGroupSelect[index].hotel;
                delete hotelsGroupSelect[index].group_room;
            }
        });


        container_parents.find('.stay-panel-container').removeClass('selected');

        btn.addClass('hide');

        var buttonsSelectRoom = container_parents.find('.btn-select-room');
        buttonsSelectRoom.removeAttr('disabled', 'disabled')

        $.each(group, function(key, room) {
            var id_room = room.id_room,
                price_room = room.price,
                cupo_room = room.cant,
                capacidad = room.capacidad,
                price_child_room = room.priceninos,
                price_baby_room = room.pricebebes,
                age_babys = hotel.limbebes,
                age_childrens = hotel.limninos,
                hotel_name = hotel.name,
                hotel_stars = hotel.stars,
                id_package_room = room.pr_id_room,
                name_room = room.name;
                hotel = {
                    name: hotel_name,
                    stars: hotel_stars
                };

            var cant_room = btn.parent().parent().parent().parent().parent().find('.cant_room[data-room_id="'+ id_room +'"]').val();
            cant_room_total -= (parseInt(cant_room) * parseInt(cupo_room));
            // ---------
            habit_selec -= 1;
            if (habit_selec == 0) {data_resumen.impuesto_origen = 0;}

            var valueHab = cant_room;

            // OVerride de Prices
            data_resumen.total_price_adultos -= data_resumen.total_price_adultos;
            data_resumen.impuesto_line -= calculateImpHab(valueHab, capacidad);

            //Se muestran las botoneras del input y se quita la inhabilitacion

            //Habilita el boton principal de seleccion
            btn.parent().parent().find('.btn-select-room')
                .removeClass('active color_verde').html('Seleccionar')
                .removeAttr('disabled');

            for (var i = 0; i < data_selected.package_rooms.length; i++) {
                if (parseInt(id_package_room) === data_selected.package_rooms[i].id_package_room) {
                    data_selected.package_rooms.splice(i, 1);
                }
            }
            for (var e = 0; e < data_selected.hotels.rooms.length; e++) {
                if (typeof(data_selected.hotels.rooms[e].id_package_room) !== 'undefined') {
                    if (parseInt(id_package_room) === data_selected.hotels.rooms[e].id_package_room) {
                        data_selected.hotels.rooms.splice(e, 1);
                    }
                }
            }

            if (data_selected.package_rooms.length > 0) {
                price_room = data_selected.package_rooms[0].price_room;
            }

            data_resumen.price_hotel_origin = parseFloat(price_room);
            data_resumen.price_child_room = parseFloat(price_child_room);
            data_resumen.price_baby_room = parseFloat(price_baby_room);

            var container_parents = btn.parents('.panel-parent-container'),
                btn_actives = container_parents.find('.btn-select-room.active');

            calculatePrices();
            updateInfoResumen();
        });
    });

    // PROCESO DE CALCULO DEL BOTON SELECCIONAR HOTEL/HABITACION Y CANTIDAD
    $("#container-destinations").on('click', '.btn-select-room', function(e) {
        e.preventDefault();

        // Se hace lectura de los metaddatos
        var btn = $(this),
            id_destiny = btn.attr("data-destiny"),
            name_destiny = btn.attr("data-name-destiny"),
            destiny = hotelsGroups[id_destiny],
            hotel = destiny[btn.attr('data-hotel')],
            group = hotel.group_rooms[btn.attr('data-group')],
            container_parents = btn.parents('.panel-parent-container');

        $.each(hotelsGroupSelect, function(index, hotelGroupSelect) {
            if (hotelGroupSelect.destiny == id_destiny) {
                hotelsGroupSelect[index].hotel = btn.attr('data-hotel');
                hotelsGroupSelect[index].group_room = btn.attr('data-group');
            }
        });

        container_parents.find('.stay-panel-container').addClass('selected');
        var buttonsSelectRoom = container_parents.find('.btn-select-room');
        buttonsSelectRoom.attr('disabled', 'disabled')

        //Tomo el valor del input tras presionar el boton de seleccion

        //Se ocultan las botoneras del INPUT
        // cantt.parent().find('.quantity-up').hide();
        // cantt.parent().find('.quantity-down').hide();

        //Se inhabilita el boton seleccionar despues de presionar
        btn.attr('disabled', 'disabled').addClass('color_verde active').html('Seleccionado');
        // cantt.attr('disabled', 'disabled');

        //Se desoculta el boton cancelar
        btn.parent().parent().find('.btn-cancel-room').removeClass('hide');
        $.each(group, function(key, room) {
            var id_room = room.id_room,
                price_room = room.price,
                cupo_room = room.cant,
                capacidad = room.capacidad,
                price_child_room = room.priceninos,
                price_baby_room = room.pricebebes,
                age_babys = hotel.limbebes,
                age_childrens = hotel.limninos,
                hotel_name = hotel.name,
                hotel_stars = hotel.stars,
                id_package_room = room.pr_id_room,
                name_room = room.name;
                hotel = {
                    name: hotel_name,
                    stars: hotel_stars
                };

            var cantt = btn.parent().parent().parent().parent().parent().find('.cant_room[data-room_id="'+ id_room +'"]');
            var valueHab = cantt.val();

            data_resumen.impuesto_line += calculateImpHab(valueHab, capacidad);
            data_resumen.price_hotel_origin = parseFloat(price_room);
            let price_child = (data_resumen.price_child_room > parseFloat(price_child_room) && data_selected.package_rooms.length > 1 ? data_resumen.price_child_room : parseFloat(price_child_room));
            data_resumen.price_child_room = price_child;
            data_resumen.price_baby_room = parseFloat(price_baby_room);
            //-----
            habit_selec += 1;
            data_resumen.impuesto_origen = impuesto_fix;

            //Tomo lectura de todos los datos enviados y los envio a la funcion de calculo final
            selectRoom(id_room, price_room, price_child_room, price_baby_room, age_babys, age_childrens, hotel, id_package_room, valueHab, cupo_room, id_destiny, name_destiny, name_room, capacidad);
        });
    });

    $("#container-destinations").on('click', '.quantity-up', function(e) {
        e.preventDefault();
        var btn = $(this),
            room_id = btn.attr("data-room_id");

        var canroom = $('.canroom[data-room_id=' + room_id + ']');
        var max = canroom.attr('max');

        $("#ejecutarActualizar").trigger("click");

        var oldValue = parseFloat(canroom.val());
        if (oldValue >= max) {
            var newVal = oldValue;
        } else {
            var newVal = oldValue + 1;
        }

        canroom.val(newVal);
        canroom.trigger("change");
    });

    $("#container-destinations").on('click', '.quantity-down', function(e) {
        e.preventDefault();
        var btn = $(this),
            room_id = btn.attr("data-room_id");

        var canroom = $('.canroom[data-room_id=' + room_id + ']');
        var min = canroom.attr('min');

        $("#ejecutarActualizar").trigger("click");

        var oldValue = parseFloat(canroom.val());
        if (oldValue <= min) {
            var newVal = oldValue;
        } else {
            var newVal = oldValue - 1;
        }

        canroom.val(newVal);
        canroom.trigger("change");
    });

     // BOTON AGREGAR MAS HOTELES
     $("#container-destinations").on('click', '#boton_agregar_habitacion', function(e) {

        var fila = `
        <hr><h2>Contenido agregado</h2>
           `;
        $(".hoteles").append(fila);

       /* $(".eliminar-fila").click(function() {
            $(this).parents('tr').remove();
            recorrerPersonas();
        })*/

    });
    // TOMADOR DE VALORES DE INPUT DE HABITACION -- TOMA EL VALOR DE LOS SPINER ( VIEJO METODO )
    /* $('.quantity-down`+room.id_room+`').click(function () {
        var min = $('.canroom`+room.id_room+`').attr('min');
        var oldValue = parseFloat($('.canroom`+room.id_room+`').val());
        if (oldValue <= min) {
            var newVal = oldValue;
        } else {
            var newVal = oldValue - 1;
        }
        $('.canroom`+room.id_room+`').val(newVal);
        $('.canroom`+room.id_room+`').trigger("change");
    });*/

    $(window).resize(function() {
        ordenarContenedores();
    });

    initResume();
    //    selectFirstElements();
    //createForm(inc);


    var editPackage = localStorage.getItem('edit-package');
    if (editPackage == 'true') {
        var dataEditPackage = localStorage.getItem('data-package');

        preselectPackage = true;
        dataLoadEdit = {
            origen: false,
            salida: false,
            data_rooms: false,
            data_hotels: false,
            butaca: false,
            adicionals: false,
            pasajeros: false
        };

        packageEdit = JSON.parse(dataEditPackage);
        selectOriginDefault();
        selectButacaDefault();

        setTimeout(() => {
            selectAdicionalDefault();
            selectRoomsDefault();
        }, 0);

        localStorage.removeItem('edit-package');
        localStorage.removeItem('data-package');
    }
});

function initResume() {
    if (typeof(edadninos) !== 'undefined') {
        data_resumen.max_age_childrens = parseInt(edadninos);
        data_resumen.max_age_babys = parseInt(edadbebes);
        data_resumen.percentage_childrens = parseFloat(valueninos);
        data_resumen.percentage_babys = parseFloat(valuebebes);
    }
}

function addForm($form) {
    const form = getFormData($form);
    arregloPasajeros.push(form);
}

function validateForms() {
    let flag = true;
    let message = [];

    let butacas = $(".seleccion_butaca");

    if (butacas.length > 0) {
        if(data_selected.butaca.id == 0){
            message.push('Seleccione una Butaca');
            flag = false;
        }
    }

    arregloPasajeros.forEach(function(value, key) {
        if (value['NOMBRE'] === '' || value['APELLIDO'] === '' || value['DNI'] === ''|| value['TIPODOC'] === ''
        || key == 0 && value['TELEFONO'] === '' || value['FECHANACIMIENTO'] === '' || value['SEXO'] === '' || value['SEXO'] === null) {
            flag = false;
            message.push(`Los campos de Pasajero ${key+1} no pueden estar vacios.`);
            return;
        }
    });

    return (typeof message[0] === 'string' ? message[0] : flag);
}

function createForm(inc) {
    $('.pasajeros_formularios').append(`

    <div class="row">
        <form id="formm-${inc}" class="col-md-12 col-xs-12">
            <h3 style="padding: 5px 0;">Pasajero ${inc}</h3>
            <div class="row">
                <div class="col-md pr-md-2 col-xs-12">
					<div class="input-group mt-2">
						<label>Nombre</label>
                    </div>
                    <div class="input-group mt-2">
                        <input type="text" id="pasajeroNombre" name="NOMBRE" class="form-control" placeholder="">
                    </div>
                </div>
                <div class="col-md px-md-2 col-xs-12">
					<div class="input-group mt-2">
						<label>Apellido</label>
                    </div>
                    <div class="input-group mt-2">
                        <input type="text" id="pasajeroApellido" name="APELLIDO" class="form-control" placeholder="">
                    </div>
                </div>
				<div class="col-md pl-md-2 col-xs-12">
					<div class="input-group mt-2">
						<label>Tipo Doc.</label>
                    </div>
                    <div class="input-group mt-2">
                        <div class="no-uniform w-100">
                            <select class="form-control" data-no-uniform="true" id="pasajeroTipoDoc" name="TIPODOC">
                                <option selected value="" disabled></option>
                                <option value="DNI">DNI</option>
                                <option value="Pasaporte">Pasaporte</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="col-md px-md-2 col-xs-12">
					<div class="input-group mt-2">
						<label>Nro. Doc.</label>
                    </div>
                    <div class="input-group mt-2">
                        <input type="number" id="pasajeroDni" min="0" name="DNI" class="form-control" onkeypress='validate(event)' placeholder="">
                    </div>
                </div>
                <div class="col-md px-md-2 col-xs-12">
					<div class="input-group mt-2">
						<label>Teléfono</label>
                    </div>
                    <div class="input-group mt-2">
                        <input type="number" id="pasajeroTelefono" name="TELEFONO" class="form-control" onkeypress="" placeholder="">
                    </div>
                </div>
                <div class="col-md-2 px-md-2 col-xs-12">
					<div class="input-group mt-2">
						<label>Fecha Nacimiento</label>
                    </div>
                    <div class="input-group mt-2">
                        <input type="text" name="FECHANACIMIENTO" autocomplete="off" class="form-control pasajero-fechanacimiente pasajeroFechanacimiento-${inc} datepicker date-input-calendar" placeholder="">
                    </div>
                </div>
                <div class="col-md pl-md-2 col-xs-12">
					<div class="input-group mt-2">
						<label>Sexo</label>
                    </div>
                    <div class="input-group mt-2">
                        <div class="no-uniform w-100">
                            <select class="form-control" data-no-uniform="true" id="pasajeroSexo" name="SEXO">
                                <option selected value="" disabled></option>
                                <option value="M">Masculino</option>
                                <option value="F">Femenino</option>
                            </select>
                        </div>
                    </div>
                </div>

                <hr class="col-12 mt-4 mb-1">
            </div>
        </form>
    </div>`);


    var inputsDate = $('.date-input-calendar');
    if (inputsDate.length > 0) {
        inputsDate.datepicker();
        inputsDate.removeClass('date-input-calendar');
    }
}

function validate(evt) {
    var theEvent = evt || window.event;

    // Handle paste
    if (theEvent.type === 'paste') {
        key = event.clipboardData.getData('text/plain');
    } else {
        // Handle key press
        var key = theEvent.keyCode || theEvent.which;
        key = String.fromCharCode(key);
    }
    var regex = /^[0-9]*$/;
    if( !regex.test(key) ) {
        theEvent.returnValue = false;
        if(theEvent.preventDefault) theEvent.preventDefault();
    }
}

function deleteForm(inc) {
    $(`#formm-${inc}`).remove();
}
function recorrerPersonas(form) {
    $(".pasajeros_formularios div#formm"+inc+":last").remove();
    //console.log(arregloPasajeros);
    /* Obtenemos todos los tr del Body*/
    inc = inc - 1;
    var rowsBody = $(".pasajeros_formularios div#formm-"+inc);
    /* Obtenemos todos los th del Thead */
    var rowsHead = $(".pasajeros_formularios").find('thead > tr > th');
    /* Iteramos sobre as filas del tbody*/
    for (var i = 0; i < rowsBody.length; i++) {
        var obj = {
            NOMBRE: rowsBody[i].getElementById('pasajeroNombre')[0].innerText,
            APELLIDO: rowsBody[i].getElementById('pasajeroApellido')[1].innerText,
            TELEFONO: rowsBody[i].getElementById('pasajeroTelefono')[2].innerText,
            SEXO: rowsBody[i].getElementById('pasajeroSexo')[3].innerText,
            FECHANACIMIENTO: rowsBody[i].getElementsByClassName('pasajeroFechanacimiento')[4].innerText,
			TIPODOC: rowsBody[i].getElementById('pasajeroTipoDoc')[5].innerText,
			DNI: rowsBody[i].getElementById('pasajeroDni')[6].innerText
        }; /* auxiliar*/


        arregloPasajeros.push(obj); /* Añadimos al Array Principal*/
    }
}

function getFormData($form){
    var unindexed_array = $($form).serializeArray(),
        sexo = $($form).find('select[name="SEXO"]').val();

    unindexed_array.push({
        name: "SEXO",
        value: sexo
    });

    var indexed_array = {};

    $.map(unindexed_array, function(n, i){
        indexed_array[n['name']] = n['value'];
    });

    return indexed_array;
}

function cargarMapa(name, latitud, longitud) {

    var locations = [
        [name, latitud, longitud, 4],
    ];
    var map = new google.maps.Map(document.getElementById('map'), {
        zoom: 10,
        center: new google.maps.LatLng(latitud, longitud),
        mapTypeId: google.maps.MapTypeId.ROADMAP
    });
    var infowindow = new google.maps.InfoWindow();
    var marker, i;
    for (i = 0; i < locations.length; i++) {
        marker = new google.maps.Marker({
            position: new google.maps.LatLng(locations[i][1], locations[i][2]),
            map: map
        });
        google.maps.event.addListener(marker, 'click', (function(marker, i) {
            return function() {
                infowindow.setContent(locations[i][0]);
                infowindow.open(map, marker);
            }
        })(marker, i));
    }
    return true;
}


function validatepackage() {
    var flag = true,
        message;


    var valForms = validateForms();
    if (valForms !== true) {
        message = valForms;
    }

    //console.log(formatNumber.new(10000.20, "$"));
    if (!$("input.seleccion_vuelo:checked").val()) {
        message = 'Seleccione un origen';
    } else if (parseInt(data_selected.linea) == 0) {
        message = 'Seleccione una salida';
    } else if ((parseInt($("#info-cantidadadultos").val()) + parseInt($("#info-cantidadniños").val())) <= 0) {
        message = 'Seleccione pasajeros';
    } else if (data_selected.amount_lines == 0) {
        message = 'Seleccione una habitación';
    } else if (data_selected.package_rooms.length < data_selected.hotels.amount) {
        message = 'Seleccione una habitación';
    } else if ((parseInt(arregloPasajeros.length) <= 0) || (parseInt(arregloPasajeros.length) < (parseInt($("#info-cantidadadultos").val()) + parseInt($("#info-cantidadniños").val())))) {
        message = 'Complete la información de los pasajeros';
    }

    // if (data_resumen.total_real_adultos > cant_room_total) {
    //     message = 'Debe seleccionar una habitación adicional para poder compensar el cupo de personas'
    // }
    if (data_resumen.total_real_adultos < data_resumen.total_childrens) {
        message = 'Los niños deben ir acompañado por un adulto.';
    }

    var tt = parseInt(data_resumen.total_real_adultos) + parseInt(data_resumen.total_childrens) + parseInt(data_resumen.total_babys);
    var inventario = $('#salida' + data_selected.linea).attr('data-inventario');
    if (disponibilidad == 'No' && tt > inventario) {
        if (parseInt(inventario) > 1) {
            message = 'Solo hay cupo para ' + inventario + ' personas, en esta fecha de salida';
        } else {
            message = 'Solo hay cupo para ' + inventario + ' persona, en esta fecha de salida';
        }
    }
    var valrooms = validateRooms();
    if (valrooms !== true) {
        message = valrooms;
    }

    console.log(typeof message);
    return (typeof message === 'string' &&
        typeof message !== undefined ? message : flag);
}

function ordenarContenedores() {
    var cantidadContenedres = 0;
    $(".boton_contenedor").each(function(index) {
        cantidadContenedres = cantidadContenedres + 1;
    });
    var margen = ((parseInt($(".boton_contenedor").parent('div').width()) - (cantidadContenedres * 100)) / (cantidadContenedres - 1));
    $(".boton_contenedor").css({ "margin-right": "" + margen + "px" });
    $(".boton_contenedor:nth-child(" + cantidadContenedres + ")").css({ "margin-right": "0px" });
}


function formatMoney(n, c, d, t) {
    var c = isNaN(c = Math.abs(c)) ? 2 : c,
        d = d == undefined ? "." : d,
        t = t == undefined ? "," : t,
        s = n < 0 ? "-" : "",
        i = String(parseInt(n = Math.abs(Number(n) || 0).toFixed(c))),
        j = (j = i.length) > 3 ? j % 3 : 0;

    return s + (j ? i.substr(0, j) + t : "") + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + t) + (c ? d + Math.abs(n - i).toFixed(c).slice(2) : "");
};

function numbersvalid(e) {
    tecla = (document.all) ? e.keyCode : e.which;
    if (tecla == 8) {
        return true;
    }
    patron = /[0-9]/;
    tecla_final = String.fromCharCode(tecla);
    return patron.test(tecla_final);
}

function selectFirstElements() {
    var butacas = $(".seleccion_butaca");

    if (butacas.length > 0) {
        var butaca_select = butacas.first();
        butaca_select.prop('checked', true);
        selectButaca(butaca_select);
    }

    var origenes_select = $(".seleccion_vuelo");
    if (origenes_select.length > 0) {
        var first_origin = origenes_select.first();
        first_origin.prop('checked', true);
        selectOrigin(first_origin);
    }
}

function selectButaca(butaca_select) {
	$(".container-info-pasajeros").fadeIn();
	$(".container-info-pasajeros").show();
	
    precioButaca = 0;

    var id = butaca_select.attr('id').replace("c", ""),
        container_butaca = butaca_select.parents('.group-butaca'),
        butaca_active = $(".group-butaca.active"),
        name_butaca = butaca_select.val(),
        tipocupoId = butaca_select.attr('tipocupoId'),
        id_butaca_active = 0;

    if (butaca_active.length == 1) {
        var id_butaca_active = butaca_active.find('.seleccion_butaca').attr('id').replace("c", "");

        if (id_butaca_active == id) {
            return;
        }

        butaca_active.removeClass('active');
    }



    container_butaca.addClass('active');
    data_selected.butaca.id = id;
    data_selected.butaca.name = name_butaca;
    data_selected.butaca.tipoCupoId = tipocupoId;

    console.log(data_selected.butaca);
    updateInfoResumen();

    $.ajax({
        method: 'post',
        dataType: 'json',
        url: urlAjaxViajero,
        data: {
            id_seat: id,
            action: 'getPriceSeats',
        },
        success: function(data) {
            var price_butaca = data[id].price;

            data_resumen.price_butaca = parseFloat(price_butaca);
            calculatePrices();
        }
    });
}

//function selectButaca(butaca_select) {
//	$(".container-info-pasajeros").fadeIn();
//	$(".container-info-pasajeros").show();
//}

function selectOrigin(origin_select) {
    precioOrigen = 0;
    data_selected.amount_lines = 0;
    data_selected.linea = 0;
    //setTimeout(function() { $('.datesaccor').addClass('green-active'); }, 600);
    setTimeout(function() { $('.seleccion_fecha.rad').prop('checked', false); }, 600); // ward

    var container_origen = origin_select.parents('.group-select-origin'),
        id = origin_select.attr('data-origen'),
        price = origin_select.attr('data-price'),
        city = origin_select.val();
    $('#origenresumen').html(origin_select.val())
    $('#origenresumen2').html(origin_select.val())
        //        container_active = $(".group-select-origin.active"),
        //        id_active = 0;

    //    if (container_active.length == 1) {
    //        id_active = container_active.find('.seleccion_vuelo').attr('data-origen');
    //        if (id_active == id) {
    //            return;
    //        }
    //    }

    var latitud = origin_select.attr('latitud');
    var longitud = origin_select.attr('longitud');
    //    cargarMapa(origin_select.val(), latitud, longitud);

    //    if (container_active.length == 1) {
    //        container_active.removeClass('active');
    //    }
    //    container_origen.addClass('active');

    data_resumen.price_line = 0;
    data_resumen.tarifa = 0;
    data_resumen.impuesto_origen = 0;
    data_resumen.impuesto_line = 0;
    data_resumen.total_hotel_adults = 0;
    data_resumen.total_hotel_babys = 0;
    data_resumen.total_hotel_childrens = 0;

    data_selected.origen.id = id;
    data_selected.origen.city = city;
    data_selected.origen.price = price;

    $("#container-destinations").html('');
    $('.show-hotel-selection').css('display', 'none');
    data_selected.package_rooms = [];
    calculatePrices();

    updateInfoResumen();
    loadLinesOrigin(id);
}

function loadLinesOrigin(id_origin) {
    var containerSalida = $('.container-salida'),
        container_lines = $('#container-dates-package'),
        id_package = $("#viajeroProduct").attr("data-package"),
        inventario,
        //        content = '<h5 class="mt-2 mb-3"><b>SELECCIONE FECHA DE SALIDA</b></h5>';
        content = '<div class="row col-md-12" style="padding-right:0;"><h5 class="fecha_salida subtitle-package ttline">Salidas</h5><hr class="line"></div>';
    content = '<div class="panel-group" id="accordion">'

    containerSalida.fadeOut();
    $(".container-salidas").show();

    $.ajax({
        method: 'post',
        dataType: 'json',
        url: urlAjaxViajero,
        data: {
            id_origin: id_origin,
            id_package: id_package,
            action: 'getLinesOrigin',
        },
        success: function(lines) {
            var last = content;
            $.each(lines, function(key_line, line) {

                is_api ? inventario = parseInt(line.inventario_api) : inventario = parseInt(line.inventario);

                var thisDuracion = 0;
                if (disponibilidad == 'No' && inventario == 0) {} else {
                    var view_services = '';
                    if (Object.keys(line.services).length == 0) {
                        //                        view_services = 'hide';
                        view_services = '';
                    }
                    if (typeof line.services.duracion !== 'undefined') {
                        thisDuracion = line.services.duracion.description;
                    }
                    if ($(window).width() > 1024) {
                        content += '<div style="padding-left: 0;" class="col-md-12"><div class="panel panel-default panel-custom"><div class="panel-heading datesaccor seleccion_fecha" data-departure-id="' + line.departure_id + '" data-state="true" value="' + line.origen + '" data-date="' + line.date_sal_letra + '" data-impuesto="' + line.impuesto + '" data-price_line="' + line.price + '" data-tarifa="' + line.priceline + '" name="salida" data-line="' + line.id_package_Linea + '">';
                        content += `
                        <div class="row">
                            <div class="col-9 d-flex justify-content-start align-items-center">
                                <input type="radio" data-duracion="${ thisDuracion }" data-date="${ line.date_sal_resumen }" class="seleccion_fecha rad" data-state="true" data-departure-id="${ line.departure_id }" value="${ line.id_package_Linea }" data-date="${ line.date_sal_letra }" data-impuesto="${ line.impuesto }" data-price_line="${ line.price }" data-tarifa="${ line.priceline }" name="salida" data-line="${ line.id_package_Linea }" id="b${ line.id_package_Linea }">
                                <h5 class="panel-title ml-4">${ line.date_sal_letra }</h5>
                            </div>
                            <div class="col-3 text-right">
                                <p class="icon-dropdown m-0 d-flex justify-content-end align-items-center" data-toggle="collapse" data-parent="#accordion" data-target="#collapse${ line.id_package_Linea }" aria-expanded="false">
                                    <span class="align-middle detail_accordeon mr-3">Ver Detalle</span>
                                    <i class="fas fa-chevron-up" id="colapso"></i>
                                    <i class="fas fa-chevron-down" id="no_colapso"></i>
                                </p>
                            </div>
                        </div>`;


                        content += '</div><div id="collapse' + line.id_package_Linea + '" class="panel-collapse collapse">';
                        content += '<div class="panel-body border-0 ' + view_services + '"><div class="col-sm-4">' + createServicesDesktop(line.services) + '</div><div class="col-sm-8" style="word-break: break-all;">' + line.description + '</div></div></div></div></div>';
                    } else {
                        //                        content += '<div style="padding-left: 0;" class="col-md-12"><div class="panel panel-default"><div class="panel-heading datesaccor seleccion_fecha" data-parent="#accordion" data-target="#collapse' + line.id_package_Linea + '" data-toggle="collapse" data-state="true" value="' + line.origen + '" data-date="' + line.date_sal_letra + '" data-impuesto="' + line.impuesto + '" data-price_line="' + line.priceline + '" name="salida" data-line="' + line.id_package_Linea + '">';
                        //                        content += '<table class="table"><tr>'
                        //                        content += '<td><input type="radio" data-duracion="'+ line.services.duracion.description +'" data-date="'+line.date_sal_resumen+'" class="seleccion_fecha rad" data-state="true" value="' + line.origen + '" data-date="' + line.date_sal_letra + '" data-impuesto="' + line.impuesto + '" data-price_line="' + line.priceline + '" name="salida" data-line="' + line.id_package_Linea + '" id="b' + line.id_package_Linea + '"/></td>';
                        //                        content += '<td><h5 class="panel-title" ">' + line.date_sal_letra + '</h5></td>';
                        //                        content += '<td><i class="fas fa-check"></i> Cupos (' + line.inventario + ')';
                        //    //                    console.info(line.services.duracion);
                        //                        if (disponibilidad == 'Si' && parseInt(line.inventario) == 0) {
                        //                            content += 'sujeto a disponibilidad ';
                        //                        }
                        //                        content +='</td></tr><tr>'
                        //                        content += '<td colspan="3">'+createServices(line.services)+'</td></tr><tr>'
                        //                        content += '<td align="right" colspan="2"><strong>ARS</strong></td>'
                        //                        content += '<td>' + formatMoney(line.priceline, 2, ',', '.') + '</td>'
                        //                        content +='</tr></table>'
                        //                        content += '</div><div id="collapse' + line.id_package_Linea + '" class="panel-collapse collapse in">';
                        //                        content += '<div class="panel-body">' + line.description + '</div></div></div></div>';


                        /*content += '<td><i class="fas fa-check"></i> Cupos (' + line.inventario + ')';
                        //                    console.info(line.services.duracion);
                        if (disponibilidad == 'Si' && parseInt(line.inventario) == 0) {
                            content += 'sujeto a disponibilidad ';
                        }
                        content += '</td>'*/

                        content += `
                            <div style="padding-left: 0;" class="col-md-12">
                                <div class="panel panel-default panel-custom">
                                    <div class="panel-heading datesaccor seleccion_fecha" data-departure-id="${ line.departure_id }" data-parent="#accordion" data-target="#collapse${ line.id_package_Linea }" data-toggle="collapse" data-state="true" value="${ line.origen }" data-date="${ line.date_sal_letra }" data-impuesto="${ line.impuesto }" data-price_line="${ line.priceline }" data-tarifa="${ line.priceline }" name="salida" data-line="${ line.id_package_Linea }">
                                        <div class="row">
                                            <div class="col-9 d-flex justify-content-start align-items-center">
                                                <input type="radio" data-duracion="${ thisDuracion }" data-departure-id="${ line.departure_id }" data-date="${ line.date_sal_resumen }" class="seleccion_fecha rad" data-state="true" value="${ line.origen }" data-date="${ line.date_sal_letra }" data-impuesto="${ line.impuesto }" data-price_line="${ line.priceline }" data-tarifa="${ line.priceline }" name="salida" data-line="${ line.id_package_Linea }" id="b${ line.id_package_Linea }"/>
                                                <h5 class="panel-title ml-4">${ line.date_sal_letra }</h5>
                                            </div>
                                            <div class="col-3 text-right">
                                                <p class="hide-at-will"><strong>ARS</strong></p>
                                                <p class="hide-at-will">${ formatMoney(line.priceline, 2, ',', '.') }</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div id="collapse${ line.id_package_Linea }" class="panel-collapse collapse">
                                        <div class="panel-body">
                                            <div class="col-sm-4">
                                                ${ createServicesDesktop(line.services) }
                                            </div>
                                            <div class="col-sm-8" style="word-break: break-all;">
                                                <br />
                                                ${ line.description }
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        `;

                    }
                }
            });
            if (last == content) {
                content += '<div style="color:red;">Cupos Agotados</div>'
            }
            containerSalida.hide();
            container_lines.html(content);
            containerSalida.fadeIn();

            scrollContent(container_lines, 100);

            if (preselectPackage && !dataLoadEdit.salida) {
                dataLoadEdit.salida = true;
                selectDestinyDefault();
            }

            if (lines.length === 1 && !preselectPackage) {
                var lines_select = container_lines.find('.seleccion_fecha'),
                line_select = lines_select.first();

                setTimeout(function() {
                    line_select.trigger('click');
                }, 500);
            }
        }
    });
}

function createServices(services) {
    var content = '<ul class="result__item__services grid" data-align="center">';
    $.each(services, function(key, service) {
        content += '<li data-col="7" data-col-mobile="3" class="text-center">';
        content += getIconService(key);
        content += ' <br><strong>' + service.description + '</strong></p></li>';
    });
    if (services.length <= 0) {
        content += '<li data-col="7" data-col-mobile="3" class="text-center">';
        content += '<span>No hay servicios incluidos</span>';
        content += ' <br><strong></strong></p></li>';
    }
    content += '</ul>';
    return content;
}

function createServicesDesktop(services) {
    var content = '<ul class="result__item__services grid" data-align="center">';
    $.each(services, function(key, service) {
        content += '<li data-col="7" data-col-mobile="3" class="text-center">';
        content += getIconService(key);
        content += ' <strong>' + service.description + '</strong></li><br>';
    });
    if (services.length <= 0) {
        content += '<li data-col="7" data-col-mobile="3" class="text-center">';
        content += '<span>No hay servicios incluidos</span>';
        content += ' <strong></strong></li><br>';
    }
    content += '</ul>';
    return content;
}

function getIconService(ser) {
    if (ser == 'transporte') {
        return '<i class="fas fa-bus"></i><!--Transporte-->';
    } else if (ser == 'duracion') {
        return '<i class="fas fa-clock"></i><!--Duración-->';
    } else if (ser == 'regimen') {
        return '<i class="fas fa-tag"></i><!--Régimen-->';
    } else if (ser == 'asistencia') {
        return '<i class="fas fa-briefcase-medical"></i><!--Asistencia-->';
    } else if (ser == 'coordinacion') {
        return '<i class="fas fa-bullhorn"></i><!--Coordinación-->';
    } else {
        return '';
    }
}

function selectDateLine(line_select) {
    var state = line_select.attr('data-state');
    var line_id = line_select.attr("data-line"),
        price_line = line_select.attr("data-price_line"),
        tarifa = line_select.attr("data-tarifa"),
        impuesto_line = line_select.attr("data-impuesto"),
        date = line_select.attr("data-date"),
        container_line = line_select.parents('.group-select-line'),
        container_active = $(".group-select-line.active"),
        description = container_line.find('.description'),
        id_active = 0,
        departure_id = line_select.attr('data-departure-id');

    if (typeof(line_select.find('.seleccion_fecha').attr('data-date')) == 'undefined') {
        $('#salidadate').html(line_select.attr('data-date'));
        $('#salidadate2').html(line_select.attr('data-date'));
        $('#duracion').html(line_select.attr('data-duracion'));
        $('#duracion2').html(line_select.attr('data-duracion'));
    } else {
        $('#salidadate').html(line_select.find('.rad').attr('data-date'));
        $('#salidadate2').html(line_select.find('.rad').attr('data-date'));
        $('#duracion').html(line_select.find('.rad').attr('data-duracion'));
        $('#duracion2').html(line_select.find('.rad').attr('data-duracion'));
    }
    if (state == 'true') {
        if (container_active.length == 1) {
            id_active = container_active.find('.seleccion_fecha').attr('data-line');
            if (id_active == line_id) {
                return;
            }

            container_active.removeClass('active');
            container_active.find('.description').slideUp();
        }

        container_line.addClass('active');
        description.slideDown();

        data_selected.linea = line_id;
        data_selected.departure_id = departure_id;

        clearContainerHotel();
        data_resumen.price_line = parseFloat(price_line);
        data_resumen.tarifa = parseFloat(tarifa);
        //data_resumen.impuesto_origen = parseFloat(impuesto_line);
        impuesto_fix = parseFloat(impuesto_line);
        data_resumen.impuesto_origen = 0;
        data_selected.origen.date = date;

        calculatePrices();
        /* loadDestinysLine(line_id); */
    }
}

// CARGA DE DESTINOS DE HOTELES
function loadDestinysLine(id_line) {
    data_selected.amount_lines = 0;
    var container = $("#container-destinations"),
        content = '';
    $.ajax({
        method: 'post',
        dataType: 'json',
        url: urlAjaxViajero,
        data: {
            id_line: id_line,
            action: 'getDestinysLine',
        },
        success: function(destinations) {
            var total_destiny = 0;
            hotelsGroups = [];
            hotelsGroupSelect = [];

			content +=`<div class="col-12 mb-4"> <h5 class="fecha_salida subtitle-package ttline">Alojamientos definidos para tu estadía</h5></div>`;

            $.each(destinations, function(key, destiny) {
                hotelsGroupSelect.push({
                    destiny: destiny.id_destiny
                });

                content += `
                <div class="col-md-12">
                    <div class="panel panel-default panel-parent-container panel-custom panel-active">
                        <div class="stay-panel-container panel-heading estadiaaccor seleccion_fecha seleccion_estadia" data-name-destiny="${ destiny.destiny }" data-destiny="${ destiny.id_destiny }" data-toggle="collapse"  data-parent="#container-destinations" data-target="#collapseCC${ destiny.id_destiny }">
                            <div class="row">
                                <div class="col-12 d-flex justify-content-start align-items-center">
                                    <inpu type="radio" class="seleccion_estadia" value="${ destiny.destiny }" name="estadia" data-destiny="${ destiny.id_destiny }" id="f${ destiny.id_destiny }"/>
                                    <h5 class="panel-title ml-4">Alojamiento en ${ destiny.destiny } ↓</h5>
                                </div>
                            </div>
                        </div>
                        <div id="collapseCC${ destiny.id_destiny }" class="panel-collapse collapse">
                            <div class="panel-body">
                            </div>
                        </div>
                    </div>
                </div>
                `;
            });

            //data_resumen.price_destiny = total_destiny;

            clearDestinies();

            data_selected.amount_lines = destinations.length;
            data_selected.hotels.amount = destinations.length;

            updateInfoResumen();
            calculatePrices();
            //miguel
            //content += '<div id="linea_union"></div>';
            $('.show-hotel-selection').slideDown('fast');
            container.html(content);

            /*if (preselectPackage) {
                selectDestiniesLineDefault();
            } else {
                if (destinations.length > 0) {
                    var firstDestiny = container.find('.seleccion_fecha.seleccion_estadia').first();
                    firstDestiny.trigger('click');
                }
            }*/
            selectDestiniesLineDefault();

            ordenarContenedores();

            let seleccionEstadiaList = $('#container-destinations').find('.seleccion_estadia');
            if(seleccionEstadiaList.length>0){
              var input = $(seleccionEstadiaList[0]);
              var idTemp = input.attr('data-destiny');
              var nameTemp = input.attr('data-name-destiny');
              $.ajax({
                  method: 'post',
                  dataType: 'json',
                  url: urlAjaxViajero,
                  data: {
                      id_line: data_selected.linea,
                      id_destiny: idTemp,
                      //Cambio añadido por Sebastian Leiva
                      filter_data: dataRooms,
                      //Cambio añadido por Sebastian Leiva
                      action: 'getHotelsByDestinyLine',
                  },
                  success: function(hotels) {
                    console.log('mira la respuesta',hotels);

                    if(hotels.length > 0){
                      if(hotels[0].group_rooms.length > 1){
                      //  selectDestiniesLineDefault()
                      console.log('loadHotelsDestiny '+idTemp+'  '+nameTemp);
                        loadHotelsDestiny(idTemp, nameTemp);
                      }
                    }
                  }
                })
            }

        }
    });
}

function clearDestinies() {
    var container = $("#container-destinations");

    data_selected.hotels.amount = 0;

    data_resumen.price_destiny = 0;
    data_selected.hotels.rooms = [];

    data_selected.package_rooms = [];

    $('.show-hotel-selection').slideUp('fast');
    container.html('');
}

//HAGO LECTURA DE LOS VALORES DEL DESTINO DE HOTEL
function selectDestiny(destiny_select) {
    var id = destiny_select.attr('data-destiny');
    var name = destiny_select.attr('data-name-destiny');
    loadHotelsDestiny(id, name); //llamo a la funcion para cargar el hotel
}

$('.info-cantidad').on('change', function() {
    const cantAdultos = parseInt($("#info-cantidadadultos").val());
    const cantNinos = parseInt($('#info-cantidadniños').val());
    const total = cantAdultos + cantNinos;
    /*if (inc < total) {
        inc = inc+ 1;
        createForm(inc);
    }
    if (inc > total) {
        deleteForm(inc);
        inc = inc - 1;
    }*/
});


//////////////////////////////////////////////////////////////
// LIMPIADO DE CONTENIDO DE HOTEL ( OBSERVADO )
function clearContainerHotel() {
    clearDestinies();
    hideContainerHotels();
}
function hideContainerHotels() {
      //  $(".seleccionar_hotel").fadeOut();
}
/////////////////////////////////////////////////////////////

// CARGA DE DATOS COMPLETOS DEL HOTEL SELECCIONADO
function loadHotelsDestiny(id, namedestiny, isOpenedModal = null, typeRoomSelected = null) {

    var falgalready = 0;
    $.ajax({
        method: 'post',
        dataType: 'json',
        url: urlAjaxViajero,
        data: {
            id_line: data_selected.linea,
            id_destiny: id,
            //Cambio añadido por Sebastian Leiva
            filter_data: dataRooms,
            //Cambio añadido por Sebastian Leiva
            action: 'getHotelsByDestinyLine',
        },
        success: function(hotels) {
            content = '';
            hotelsGroups[id] = hotels;
            //---Toma valor impuesto
            data_resumen.impuesto_origen = impuesto_fix;

            var issetHotels = false,
                amountGroupRooms = 0,
                selectUniqueRoom = {},
                hotelSelect = false,
                roomDisabled = false,
                disabledButtons = 'hidden';
            $.each(hotels, function(key_hotel, hotel) {

                var hotelSel = $('#container-destinations').find('.flaghotel' + hotel.id_hotel).length;
                if (hotelSel > 0) {
                    hotelSelect = true;
                    $('#container-destinations').find('.flaghotel' + hotel.id_hotel).remove();
                }

                if (hotel.group_rooms.length > 0) {
                    issetHotels = true;
                    content += `
                    <div data-destiny="` + id + `" class="row contenedor_estadias limpiar-resultados hotels destiny` + id + `  flaghotel` + hotel.id_hotel + `">
                    <div class="col-12 col-md-5">
                        <div id="slider` + hotel.id_hotel + `" class="carousel slide" data-ride="carousel">
                            <div class="carousel-inner">`;

                    if (hotel.photos.length > 0) {
                        var count_photo = 0;
                        $.each(hotel.photos, function(key_photo, photo) {
                            var active_photo = (count_photo == 0) ? 'active' : '';
                            content += `
                                                <div class="carousel-item ` + active_photo + `">
                                                    <img src="/modules/viajero/uploads/img/hotels/` + hotel.id_hotel + `/` + photo.url + `" class="imagen_hotel" alt="">
                                                </div>`;

                            count_photo++;
                        });
                    }

                    content += `</div>

                            <a class="carousel-control-prev" href="#slider` + hotel.id_hotel + `" data-slide="prev">
                                <span class="carousel-control-prev-icon"></span>
                            </a>

                            <a class="carousel-control-next" href="#slider` + hotel.id_hotel + `" data-slide="next">
                                <span class="carousel-control-next-icon"></span>
                            </a>
                        </div>
                    </div>`;


                    var content_stars = generateStars(hotel.stars);

                    content += `
                    <div class="col-12 col-md-7 mt-5 mt-md-0">
                        <div class="row">
                            <div class="col-md-12 col-12">
                                <div class="title-hotel d-flex align-items-center">
                                    <div class="row help-mobile-center">
                                        <div style="max-width:100%" class="col-md-auto text-center text-md-left pr-0">
                                            <h3 class="subtitle-package text-upper mr-0"><b>` + hotel.name + `</b></h3>
                                        </div>
                                        <div class="col-md-auto col-12 text-center text-md-left">
                                        ` + content_stars + `
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12 col-12 d-flex align-items-center mt-3 mt-md-0">
                                <a class="btn-link-custom mt-3 mb-3" href='#' onclick="cargarMapaHotel('` + hotel.name + `', '` + hotel.latitud + `', '` + hotel.longitud + `')">
                                    <i class="fa fa-map-marker-alt mr-2 color-green"></i> <span>Ver ubicación</span>
                                </a>
                            </div>
                        </div>
                        <div class="mt-2">` + hotel.description + `</div>
                    </div>`;
                    // LINEA DE HABITACIONES DISPONIBLES
                    content +=`
                    <!-- AQUI VA LA CARGA DE LAS HABITACIONES -->
                        <div class="col-12 hoteles" id="hotel` + hotel.id_hotel + `">`;

                                // Sebastian Leiva
                                if (hotel.group_rooms.length == 1) {


                                      $.each(hotel.group_rooms, function(key_group, group_room){
                                          var count_room = 0;

                                          if (key_group > 0) {
                                              //content += '<hr class="hr-group-room">';
                                          }

                                          content += `
                                              <div class="group-room">
                                                  <div class="row">
                                                      <div class="col-md-8 col-12">
                                          `;

                                          var showError = false,
                                              availabilityRooms = true;

                                          $.each(group_room, function(key_room, room){
                                          //for (var i = 0; i < group_room.length; i++) {
                                            //let room = group_room[i];
                                              price_adult = formatMoney(room.price, 2, ",", ".");
                                              price_child = formatMoney(room.priceninos, 2, ",", ".");
                                              price_baby = formatMoney(room.pricebebes, 2, ",", ".");

                      //                        if(disponibilidad=='No'&& parseInt(room.cant)!== 0){
                                              if (count_room > 0 && count_room < group_room.length) {
                                                  content += '<hr>';
                                              }

                                              content += `<div class="row relative-position">
                                                              <div class="col-md-8 col-12 border-right2">
                                                                  <div class="row">
                                                                      <div class="col-12">
                                                                          <h4 class="mt-1 subtitle-package">`+room.name.toUpperCase()+`</h4>
                                                                          <p class="obtener-noches">`+room.observations+`</p>
                                                                      </div>
                                                                      <div class="col-12 mb-4 mb-md-5 text-left">
                                                                          <a href="#" class="d-flex justify-content-start align-items-center btn-link-custom btn-ver-galeria" data-id="${ room.id_room }">
                                                                              <i class="fa fa-picture-o mr-3 color-green" style="font-size:25px;"></i> <span>Ver fotos</span>
                                                                          </a>
                                                                      </div>
                                                                  </div>
                                                              </div>
                                                              <div class="col-md-4 col-12 mb-5 mb-md-0 help-mobile-height border-right2 d-flex justify-content-center align-items-center">
                                                                  <div class="ubicacion_input">
                                                                      <div class="quantity-room quantity" >
                                                                          <input disabled min="1" max="` + room.cant + `" class="text-center cant_room canroom mb-4 mt-2" data-hotel="${key_hotel}" data-group="${key_group}" data-destiny="${id}" data-room_id="` + room.id_room + `" type="number" value="${room.cantidad}" />
                                                                          <p class="subtitle-package textprice text-center" style="vertical-align:middle; font-size: 14px;font-weight: 700; margin-top:5px;">
                                                                              Habitaciones
                                                                          </p>
                                                                          <!-- <div class="quantity-nav2">
                                                                              <div class="quantity-button quantity-up quanup2" data-room_id="` + room.id_room + `">
                                                                                  <i class="fa ">+</i>
                                                                              </div>
                                                                          </div>
                                                                          <div class="quantity-nav3">
                                                                              <div class="quantity-button quantity-down quandown2" data-room_id="` + room.id_room + `">
                                                                                  <i class="fa ">-</i>
                                                                              </div>
                                                                          </div> -->
                                                                      </div>
                                                                  </div>
                                                              </div>
                                                          </div>`;

                                              if(disponibilidad == 'No' && parseInt(room.cant) <= 0){
                                                  showError = true;
                                                  availabilityRooms = false;
                                              } else if (disponibilidad == 'Si' && parseInt(room.cant) <= 0){
                                                  if (availabilityRooms) {
                                                      showError = true;
                                                      availabilityRooms = true;
                                                  }
                                              }
                                          });

                                          var disabledAttr = '',
                                              contentShowError = '';

                                          if (!availabilityRooms) {
                                              disabledAttr = 'disabled';
                                          }

                                          if (showError) {
                                              if (disponibilidad == 'Si') {
                                                  contentShowError = '<span class="titleroom red">¡Sujeto a disponibilidad!</span>';
                                              } else {
                                                  roomDisabled = true;
                                                  contentShowError = '<span class="titleroom red">¡Sin cupos!</span>';
                                              }
                                          }

                                          if(hotel.group_rooms.length > 1){
                                              disabledButtons = 'hidden'
                                          }

                                          content += `
                                                  </div>
                                                  <div class="col-md-4 col-12 d-flex justify-content-center align-items-center">
                                                      <div class="d-block w-100">
                                                          <div class="row">
                                                              <div class="col-12 mb-4 text-center">
                                                                  <button ${ disabledAttr } ${ disabledButtons } class="btn-seleccionar asignar btn-select-room btn-custom btn-outline" data-hotel="${key_hotel}" data-group="${key_group}" data-destiny="${id}" data-name-destiny="${namedestiny}">
                                                                      Seleccionar
                                                                  </button>
                                                              </div>
                                                              <div class="col-12 text-center">
                                                                  <button ${ disabledButtons }  class="btn btn-seleccionar hide btn-danger btn-cancel-room btn-custom btn-outline" data-hotel="${key_hotel}" data-group="${key_group}" data-destiny="${id}" data-name-destiny="${namedestiny}">
                                                                      Cancelar
                                                                  </button>
                                                              </div>
                                                              <div class="col-12 text-center">
                                                                  ${ contentShowError }
                                                              </div>
                                                          </div>
                                                      </div>
                                                  </div>
                                              </div>
                                          </div>
                                          `;

                                          count_room++;

                                          if (amountGroupRooms == 0) {
                                              selectUniqueRoom = {
                                                  hotel: key_hotel,
                                                  group: key_group,
                                                  destiny: id,
                                              };
                                          }

                                          amountGroupRooms++;
                                      });

                                }else if (hotel.group_rooms.length > 1){
                                  isOpenedModal = isOpenedModal != null ? isOpenedModal : 0;
                                  isOpenedModal = globalTyperoom != null ? 1 : isOpenedModal;

                                  if(isOpenedModal == 0){
                                    $('#selectRoomTypeModal').modal({
                                      show: true,
                                      keyboard: false,
                                      backdrop: 'static'
                                    });

									$('#selectRoomTypeModal').on('hidden.bs.modal', function (e) {
                                      setTimeout(()=>{
                                          selectDestiniesLineDefault()
                                      }, 1000)
                                    });

                                    $('#selectRoomTypeModalSelect').empty();
                                    for (var u = 0; u < hotel.group_rooms.length; u++) {
                                      let optionsTemp = hotel.group_rooms[u];
                                      $('#selectRoomTypeModalSelect').append(`<option>${optionsTemp[0].name}</option>`);
                                    }

                                    $('#selectRoomTypeModal').find('#id_hidden').val(id);

                                    $('#selectRoomTypeModal').find('#namedestiny_hidden').val(namedestiny);
                                  }else if(isOpenedModal == 1){

                                    let typeRoomTemp = typeRoomSelected;
                                    typeRoomTemp = globalTyperoom != null ? globalTyperoom : typeRoomTemp;
                                      $.each(hotel.group_rooms, function(key_group, group_room){
                                        if(group_room[0].name == typeRoomTemp){
                                          var count_room = 0;

                                          if (key_group > 0) {
                                              //content += '<hr class="hr-group-room">';
                                          }

                                          content += `
                                              <div class="group-room">
                                                  <div class="row">
                                                      <div class="col-md-8 col-12">
                                          `;

                                          var showError = false,
                                              availabilityRooms = true;

                                          $.each(group_room, function(key_room, room){
                                          //for (var i = 0; i < group_room.length; i++) {
                                            //let room = group_room[i];
                                              price_adult = formatMoney(room.price, 2, ",", ".");
                                              price_child = formatMoney(room.priceninos, 2, ",", ".");
                                              price_baby = formatMoney(room.pricebebes, 2, ",", ".");

                      //                        if(disponibilidad=='No'&& parseInt(room.cant)!== 0){
                                              if (count_room > 0 && count_room < group_room.length) {
                                                  content += '<hr>';
                                              }

                                              content += `<div class="row relative-position">
                                                              <div class="col-md-8 col-12 border-right2">
                                                                  <div class="row">
                                                                      <div class="col-12">
                                                                          <h4 class="mt-1 subtitle-package">`+room.name.toUpperCase()+`</h4>
                                                                          <p class="obtener-noches">`+room.observations+`</p>
                                                                      </div>
                                                                      <div class="col-12 mb-4 mb-md-5 text-left">
                                                                          <a href="#" class="d-flex justify-content-start align-items-center btn-link-custom btn-ver-galeria" data-id="${ room.id_room }">
                                                                              <i class="fa fa-picture-o mr-3 color-green" style="font-size:25px;"></i> <span>Ver fotos</span>
                                                                          </a>
                                                                      </div>
                                                                  </div>
                                                              </div>
                                                              <div class="col-md-4 col-12 mb-5 mb-md-0 help-mobile-height border-right2 d-flex justify-content-center align-items-center">
                                                                  <div class="ubicacion_input">
                                                                      <div class="quantity-room quantity" >
                                                                          <input disabled min="1" max="` + room.cant + `" class="text-center cant_room canroom mb-4 mt-2" data-hotel="${key_hotel}" data-group="${key_group}" data-destiny="${id}" data-room_id="` + room.id_room + `" type="number" value="${room.cantidad}" />
                                                                          <p class="subtitle-package textprice text-center" style="vertical-align:middle; font-size: 14px;font-weight: 700; margin-top:5px;">
                                                                              Habitaciones
                                                                          </p>
                                                                          <!-- <div class="quantity-nav2">
                                                                              <div class="quantity-button quantity-up quanup2" data-room_id="` + room.id_room + `">
                                                                                  <i class="fa ">+</i>
                                                                              </div>
                                                                          </div>
                                                                          <div class="quantity-nav3">
                                                                              <div class="quantity-button quantity-down quandown2" data-room_id="` + room.id_room + `">
                                                                                  <i class="fa ">-</i>
                                                                              </div>
                                                                          </div> -->
                                                                      </div>
                                                                  </div>
                                                              </div>
                                                          </div>`;

                                              if(disponibilidad == 'No' && parseInt(room.cant) <= 0){
                                                  showError = true;
                                                  availabilityRooms = false;
                                              } else if (disponibilidad == 'Si' && parseInt(room.cant) <= 0){
                                                  if (availabilityRooms) {
                                                      showError = true;
                                                      availabilityRooms = true;
                                                  }
                                              }

                                              // updateInfoResumen();
                                              // calculatePrices();
                                          });

                                          var disabledAttr = '',
                                              contentShowError = '';

                                          if (!availabilityRooms) {
                                              disabledAttr = 'disabled';
                                          }

                                          if (showError) {
                                              if (disponibilidad == 'Si') {
                                                  contentShowError = '<span class="titleroom red">¡Sujeto a disponibilidad!</span>';
                                              } else {
                                                  roomDisabled = true;
                                                  contentShowError = '<span class="titleroom red">¡Sin cupos!</span>';
                                              }
                                          }

                                          if(hotel.group_rooms.length > 1){
                                              disabledButtons = 'hidden'
                                          }

                                          content += `
                                                  </div>
                                                  <div class="col-md-4 col-12 d-flex justify-content-center align-items-center">
                                                      <div class="d-block w-100">
                                                          <div class="row">
                                                              <div class="col-12 mb-4 text-center">
                                                                  <button ${ disabledAttr } ${ disabledButtons } class="btn-seleccionar asignar btn-select-room btn-custom btn-outline" data-hotel="${key_hotel}" data-group="${key_group}" data-destiny="${id}" data-name-destiny="${namedestiny}">
                                                                      Seleccionar
                                                                  </button>
                                                              </div>
                                                              <div class="col-12 text-center">
                                                                  <button ${ disabledButtons }  class="btn btn-seleccionar hide btn-danger btn-cancel-room btn-custom btn-outline" data-hotel="${key_hotel}" data-group="${key_group}" data-destiny="${id}" data-name-destiny="${namedestiny}">
                                                                      Cancelar
                                                                  </button>
                                                              </div>
                                                              <div class="col-12 text-center">
                                                                  ${ contentShowError }
                                                              </div>
                                                          </div>
                                                      </div>
                                                  </div>
                                              </div>
                                          </div>
                                          `;

                                          count_room++;

                                          if (amountGroupRooms == 0) {
                                              selectUniqueRoom = {
                                                  hotel: key_hotel,
                                                  group: key_group,
                                                  destiny: id,
                                              };
                                          }

                                          amountGroupRooms++;
                                        }
                                      });

                                  }

                                }
/*
                                <button class="btn-seleccionar asignar btn-select-room"
                                                        data-rooms="${data_rooms}"
                                                        data-destiny="` + id + `"
                                                        data-name-destiny="`+namedestiny+`"
                                                        data-hotel="` + room.id_hotel + `"
                                                        data-setcant="` + room.cant + `"
                                                        data-capacidad="` + room.capacidad + `"
                                                        data-hotel_name="` + hotel.name + `"
                                                        data-hotel_stars="` + hotel.stars + `"
                                                        data-name-room="` + room.name + `"
                                                        data-room="` + room.id_room + `"
                                                        data-price="` + room.price + `"
                                                        data-pr_id="` + room.pr_id_room + `"
                                                        data-price_child="` + room.priceninos + `"
                                                        data-price_baby="` + room.pricebebes + `"
                                                        data-age_babys="` + hotel.limbebes + `"
                                                        data-age_childrens="` + hotel.limninos + `">
                                                        Seleccionar</button>
                                                    <button class="btn btn-seleccionar hide btn-danger btn-cancel-room"
                                                        data-destiny="` + id + `"
                                                        data-hotel="` + room.id_hotel + `"
                                                        data-setcant="` + room.cant + `"
                                                        data-hotel_name="` + hotel.name + `"
                                                        data-hotel_stars="` + hotel.stars + `"
                                                        data-room="` + room.id_room + `"
                                                        data-price="` + room.price + `"
                                                        data-pr_id="` + room.pr_id_room + `"
                                                        data-price_child="` + room.priceninos + `"
                                                        data-price_baby="` + room.pricebebes + `"
                                                        data-age_babys="` + hotel.limbebes + `"
                                                        data-age_childrens="` + hotel.limninos + `"
                                                        data-capacidad="` + room.capacidad + `" >Cancelar</button> */

                                // Sebastian Leiva
                     content +=`

                    </div>
                </div>
                </div>

                </div>`;
                }
            });


            if (!issetHotels && !hotelSelect) {
                var containerError = $("#container-destinations #collapseCC" + id + ' .panel-body .error-rooms-empty');
                if (containerError) {
                    containerError.remove();
                }

                content = `<div class="alert alert-danger error-rooms-empty">No hay disponibilidad. Intente nueva búsqueda.</div>`;
            }

            $("#container-destinations #collapseCC" + id + ' .panel-body').append(content);
            //            $('.show-hotel-selection').slideDown('fast');
            //            jQuery("#container-destinations  .panel-body").find('.quantity-room').each(function () {
            //                var spinner = jQuery(this),
            //                        input = spinner.find('.cant_room'),
            //                        btnUp = spinner.find('.quantity-up'),
            //                        btnDown = spinner.find('.quantity-down'),
            //                        min = input.attr('min'),
            //                        max = input.attr('max');
            //                btnUp.click(function () {
            //                    $("#ejecutarActualizar").trigger("click");
            //                    var oldValue = parseFloat(input.val());
            //                    if (oldValue >= max) {
            //                        var newVal = oldValue;
            //                    } else {
            //                        var newVal = oldValue + 1;
            //                    }
            //                    spinner.find("input").val(newVal);
            //                    //SelectorSlide.val(newVal);.    /*aquí pone el selector del slide*/
            //                    spinner.find("input").trigger("change");
            //                });
            //
            //                btnDown.click(function (e) {
            //                    e.preventDefault();
            //                    var oldValue = parseFloat(input.val());
            //                    if (oldValue <= min) {
            //                        var newVal = oldValue;
            //                    } else {
            //                        var newVal = oldValue - 1;
            //                    }
            //                    spinner.find("input").val(newVal);
            //                    //SelectorSlide.val(newVal);        /*aquí pone el selector del slide*/
            //                    spinner.find("input").trigger("change");
            //                });
            //            });
            $(".seleccionar_hotel").fadeIn();


            var preselectGroup = true;
            if (preselectPackage && !dataLoadEdit.data_hotels) {
                destinyPreselect--;
                preselectGroup = false;
                selectHotelDefault(id);

                if (destinyPreselect == 0) {
                    dataLoadEdit.data_hotels = true;
                }
            }

            if (preselectGroup && amountGroupRooms == 1 && !roomDisabled) {
                var btnGroupRoom = $('.btn-select-room[data-hotel="'+ selectUniqueRoom.hotel +'"][data-group="'+ selectUniqueRoom.group +'"][data-destiny="'+ selectUniqueRoom.destiny +'"]');
                btnGroupRoom.trigger("click");
            }
        }
    });
}

function saveSelectRoomTypeModal(){
  let id = $('#selectRoomTypeModal').find('#id_hidden').val();

  let namedestiny = $('#selectRoomTypeModal').find('#namedestiny_hidden').val();

  let typeRoomSelected = $( "#selectRoomTypeModalSelect option:selected" ).text();

  loadHotelsDestiny(id, namedestiny, 1,typeRoomSelected);
  globalTyperoom = typeRoomSelected;
  $('#selectRoomTypeModal').modal('hide');
}


function traergaleria(id) {
    $(".cuerpo-galeria").html("");
    $.ajax({
        method: 'post',
        dataType: 'json',
        url: urlAjaxViajero,
        data: {
            id_room: id,
            action: 'getPhotosByRoom',
        },
        success: function(r7) {
            var contenGaleria = '';
            var amountImages = 0;
            $.each(r7, function(key, information) {
                contenGaleria += '<img class="col-sm-12 img-thumbnail" src="/modules/viajero/uploads/img/rooms/' + id + '/' + information.url + '" alt="">';
                amountImages++;
            });

            if (amountImages == 0) {
                contenGaleria = '<div class="alert alert-danger">No hay imágenes disponibles</div>';
            }
            $(".cuerpo-galeria").html(contenGaleria);
            $("#modalGaleria").modal("show");
        }
    });
}

function cargarMapaHotel(name, latitud, longitud) {


    latitud = parseInt(latitud);
    longitud = parseInt(longitud);
    var locations = [
        [name, latitud, longitud, 4],
    ];

    var map = new google.maps.Map(document.getElementById('cuerpo-cordenadas'), {
        zoom: 10,
        center: new google.maps.LatLng(latitud, longitud),
        mapTypeId: google.maps.MapTypeId.ROADMAP
    });

    var infowindow = new google.maps.InfoWindow();
    var marker, i;

    for (i = 0; i < locations.length; i++) {
        marker = new google.maps.Marker({
            position: new google.maps.LatLng(locations[i][1], locations[i][2]),
            map: map
        });

        google.maps.event.addListener(marker, 'click', (function(marker, i) {
            return function() {
                infowindow.setContent(locations[i][0]);
                infowindow.open(map, marker);
            }
        })(marker, i));
    }

    $("#modalMapa").modal("show");
    return true;
}


function selectRoom(id, price_room, price_child_room, price_baby_room, age_babys, age_childrens, hotel, id_package_room, cant_room, cupo_room, id_destiny, name_destiny, name_room, capacidad) {

    cant_room_total += (parseInt(cant_room) * parseInt(cupo_room));

    var container_destiny = $(".boton_contenedor.active_hotel"),
        //        $destiny = container_destiny.find('.seleccion_estadia')
        //        id_destiny = $destiny.attr("data-destiny"),
        //        name_destiny = namedestiny,
        destiny = {
            id: id_destiny,
            name: name_destiny
        };
    if (data_selected.package_rooms.length == 0) {
        container_destiny.removeClass('selected');

    } else {
        container_destiny.addClass('selected');
    }


    if (container_destiny.hasClass('active')) {
        container_destiny.removeClass('active');
    }


    addRoomSelect(id, destiny, price_room, price_child_room, price_baby_room, age_babys, age_childrens, hotel, id_package_room, cant_room, cupo_room, name_room, capacidad);
    hideContainerHotels();
}

function addRoomSelect(id, destiny, price_room, price_child_room, price_baby_room, age_babys, age_childrens, hotel, id_package_room, cant_room, cupo_room, name_room, capacidad) {
    //    var isset_destiny = false,
    //        key_select = -1,
    //        data_hotels = data_selected.package_rooms,
    //        id_destiny = destiny.id;
    //
    //    for (var i = 0; i < data_hotels.length; i++) {
    //        var current_room = data_hotels[i];
    //        if (current_room.destiny_id == id_destiny) {
    //            isset_destiny = true;
    //            key_select = i;
    //        }
    //    }
    //
    //    if (isset_destiny) {
    //        data_hotels.splice(key_select, 1);
    //        data_selected.package_rooms.splice(key_select, 1);
    //    }

    //    data_hotels.push({
    //        destiny_id: id_destiny,
    //        destiny_name: destiny.name,
    //        hotel_name: hotel.name,
    //        hotel_stars: hotel.stars,
    //        id_room: id,
    //        id_package_room: parseInt(id_package_room),
    //        price_room: parseFloat(price_room),
    //        cant_room: parseFloat(cant_room),
    //        price_child_room: parseFloat(price_child_room),
    //        price_baby_room: parseFloat(price_baby_room),
    //        age_babys: parseInt(age_babys),
    //        age_childrens: parseInt(age_childrens)
    //    });
    var room = {
        id_package_room: parseInt(id_package_room),
        destiny_id: destiny.id,
        destiny_name: destiny.name,
        hotel_name: hotel.name,
        hotel_stars: hotel.stars,
        name_room: name_room,
        id_room: id,
        price_room: parseFloat(price_room),
        cant_room: parseFloat(cant_room),
        price_child_room: parseFloat(price_child_room),
        price_baby_room: parseFloat(price_baby_room),
        age_babys: parseInt(age_babys),
        age_childrens: parseInt(age_childrens),
        cupo_room: parseInt(cupo_room),
        cant: cant_room,
        capacidad: capacidad
    };

    let saveAddRoom = true;
    for (var t = 0; t < data_selected.package_rooms.length; t++) {
      if(data_selected.package_rooms[t].id_room == room.id_room){
          saveAddRoom = false;
      }
    }
    if(saveAddRoom == true){
      data_selected.package_rooms.push(room);
    }
    //    data_selected.hotels.rooms= data_hotels;
    updateInfoResumen();
    calculatePrices();
}

function updateTotalChildrens() {
    var age_childrens = $(".age-childrens"),
        amount_childrens = 0,
        amount_kids = 0,
        amount_adults = 0;

    if (age_childrens.length > 0) {
        age_childrens.each(function() {
            var age_children = this;
            age_children = $(age_children);

            var age = age_children.val();
            if (age <= data_resumen.max_age_babys) {
                amount_kids++;
            } else if (age <= data_resumen.max_age_childrens) {
                amount_childrens++;
            } else {
                amount_adults++;
            }
        });
    }

    data_resumen.total_childrens = amount_childrens;
    data_resumen.total_babys = amount_kids;
    data_resumen.total_adults_childs = amount_adults;

    calculatePrices();
}

function calculatePriceHotels() {
    //    var data_rooms = data_selected.hotels.rooms,
    var data_rooms = data_selected.package_rooms;

    data_rooms.sort(function(a, b) {
        return (b.price_room - a.price_room);
    });

    var total_adult = data_resumen.total_real_adultos;
    var total_child = data_resumen.total_childrens;
    var total_babys = data_resumen.total_babys;

    /* var total_adult = amount_adults;
    var total_child = amount_childrens;
    var total_kids = amount_kids; */
    price_total_adult = 0;
    price_total_childs = 0;
    price_total_babys = 0;
    all_cupos = 0;
    all_adult = total_adult;
    for (var i = 0; i < data_rooms.length; i++) {
        var current_room = data_rooms[i],
            cupo_room = parseInt(current_room.cupo_room),
            cant_room = parseInt(current_room.cant_room);

        for (var i2 = 0; i2 < cant_room; i2++) {
            price_total_adult += current_room.price_room;
            all_cupos += cupo_room;
            if (total_adult > 0) {
                total_adult -= cupo_room;
                if (total_adult < 0) {
                    total_adult = 0;
                }
            }
        }

        /* if(total_adult > 0){
           total_child = total_adult - cantMax;
           price_total_adult += data_rooms[i].price_room * data_rooms[i].cant_room;
           if(total_adult<0){
              total_child = total_child+(total_adult);
           }
        }else if (total_adult<=0){
            total_child=total_child-cantMax-total_adult;
            price_total_childs += data_rooms[i].price_child_room * data_rooms[i].cant_room;
            if (total_child > 0) {
//                total_child = total_child - cantMax;
                price_total_childs += data_rooms[i].price_child_room * data_rooms[i].cant_room;
                if (total_child < 0) {
                    total_kids = total_kids + (total_child);
                }
            } else if (total_child <= 0) {
                total_kids = total_kids - cantMax-total_child;
                price_total_babys += data_rooms[i].price_baby_room * data_rooms[i].cant_room;
                if (total_kids > 0) {
//                    total_kids = total_kids - cantMax;
                    price_total_babys += data_rooms[i].price_baby_room * data_rooms[i].cant_room;
                } else if (total_kids == 0) {
                    price_total_babys += data_rooms[i].price_baby_room * data_rooms[i].cant_room;
                } else if (total_kids <= 0) {
                    break;
                }
            }
        }  */
    }
    //    console.info("all cupos: "+all_cupos);
    //    console.info("all adult: "+all_adult);

    if ((data_rooms.length > 0) && (all_cupos <= all_adult)) {
        //        console.log('entro');
        var first_room = data_rooms[0];

        var age_childrens = $(".age-childrens"),
            amount_childrens = 0,
            amount_kids = 0,
            amount_adults_childs = 0;

        if (age_childrens.length > 0) {
            age_childrens.each(function() {
                var age_children = this;
                age_children = $(age_children);

                var age = age_children.val();
                if (age <= first_room.age_babys) {
                    amount_kids++;
                } else if (age <= first_room.age_childrens) {
                    amount_childrens++;
                } else {
                    amount_adults_childs++;
                }
            });
        }

        price_total_babys += (first_room.price_baby_room * amount_kids);
        price_total_childs += (first_room.price_child_room * amount_childrens);
        price_total_adult += (first_room.price_room * amount_adults_childs);
    }

    // data_resumen.total_hotel_adults = price_total_adult;
    data_resumen.total_hotel_childrens = ((data_resumen.price_child_room * data_resumen.percentage_childrens) / 100 * total_child);
    data_resumen.total_hotel_babys = ((data_resumen.price_baby_room * data_resumen.percentage_babys) / 100 * total_babys);
}

function validateRooms() {
    let flag = true,
        message;
    var age_childrens = $(".age-childrens"),
        amount_childrens = 0,
        amount_kids = 0,
        amount_adults = 0;

    if (age_childrens.length > 0) {
        age_childrens.each(function() {
            var age_children = this;
            age_children = $(age_children);

            var age = age_children.val();
            if (age <= data_resumen.max_age_babys) {
                amount_kids++;
            } else if (age <= data_resumen.max_age_childrens) {
                amount_childrens++;
            } else {
                amount_adults++;
            }
        });
        amount_adults = parseInt(amount_adults) + parseInt($('#info-cantidadadultos').val());
    } else {
        amount_adults = $('#info-cantidadadultos').val();
    }
    var data_rooms = data_selected.package_rooms;
    var capacidad = 0;
    var cantmaxCapacidad = 0;
    var cantmaxCupo = 0;
    var cantCupoHab = 0;
    // old calculo
    // var hosts = parseInt(amount_adults) + parseInt(amount_childrens);
    var hosts = parseInt(amount_adults);

    for (var i = 0; i < data_rooms.length; i++) {
        // capacidad = (data_rooms[i].name_room == 'Single' ? 1 : 2) * parseInt(data_rooms[i].cant_room);
        cantmaxCapacidad += (parseInt(data_rooms[i].capacidad) * parseInt(data_rooms[i].cant_room));
        // cantmaxCapacidad += capacidad
        cantmaxCupo += parseInt(data_rooms[i].cant_room);
        cantCupoHab = parseInt(data_rooms[i].cupo_room);
    }

    data_resumen.total_capacity = cantmaxCapacidad;

    console.log('MaxCupo', cantmaxCupo);
    if (cantmaxCupo > hosts &&
        data_resumen.total_adultos > 1) {
        message = "Cupo de las habitaciones es mayor a la cantidad de huéspedes";
    } else if (cantCupoHab < data_rooms.length &&
        disponibilidad === 'No') {
        message = "Excedio el cupo de habitacion";
    } else if (cantmaxCapacidad < hosts) {
        message = "Las capacidades de las habitaciones seleccionadas son menores a la cantidad de huéspedes";
    }

    return (typeof message === 'string' ? message : flag);
}

/**
 * Calculamos el máximo precio de un listado de habitaciones
 *
 * return price
 */
function setPriceMax(key) {
    switch (key) {
        case 'adult':
            if (data_resumen.price_hotel_origin > data_resumen.price_max.adult) {
                data_resumen.price_max.adult = data_resumen.price_hotel_origin;
            }
            break;
        case 'child':
            if (data_resumen.price_child_room > data_resumen.price_max.child) {
                data_resumen.price_max.child = data_resumen.price_child_room;
            }
            break;
        case 'baby':
            if (data_resumen.price_baby_room > data_resumen.price_max.baby) {
                data_resumen.price_max.baby = data_resumen.price_baby_room;
            }
            break;
    }

}

/**
 * Calculamos los precios por adulto, niño y bebe por cada habitacion
 */
function calculatePrices() {

    calculatePriceHotels();

    // Seteamos price max de adultos, niños y bebes
    setPriceMax('adult');
    setPriceMax('child');
    setPriceMax('baby');
    var origen = parseFloat(data_selected.origen.price);
    var aditional = 0;
    // var total_base = (data_resumen.tarifa + data_resumen.price_line + data_resumen.price_butaca + data_resumen.price_destiny);
    var total_base = (data_resumen.tarifa + origen + data_resumen.price_butaca + data_resumen.price_destiny);

    data_resumen.total_real_adultos = data_resumen.total_adultos + data_resumen.total_adults_childs;
    data_resumen.aditionals.total_aditionals = 0;
    if (data_resumen.aditionals.price > 0) {
        data_resumen.aditionals.total_aditionals = data_resumen.aditionals.price;
    }

    var base_adult = (total_base + data_resumen.aditionals.total_aditionals),
        // base_childrens = (base_adult * data_resumen.percentage_childrens) / 100,
        base_childrens = base_adult,
        // base_babys = (data_resumen.price_baby_room * data_resumen.percentage_babys) / 100;
        base_babys = 0;

    data_resumen.total_price_childrens = data_resumen.total_childrens * (base_childrens);
    data_resumen.total_price_babys = data_resumen.total_babys * (base_babys);

    let total_childrens = updateTotalHotel('child');
    let total_babys = updateTotalHotel('baby');
    let total_adultos = updateTotalHotel('adult');
    let total_price_adultos = (data_resumen.total_real_adultos * base_adult);
    data_resumen.total_price_adultos = total_price_adultos;


    let total_hotel = parseFloat((total_adultos + total_childrens) + total_babys);

    let total_impuesto_porcentaje = ((data_resumen.total_price_adultos + data_resumen.total_price_childrens + data_resumen.total_price_babys + total_hotel) * data_resumen.impuesto_origen /100);

    //data_resumen.impuesto_total = (data_resumen.impuesto_origen * data_resumen.total_real_adultos) + (data_resumen.impuesto_origen * data_resumen.total_childrens);
    data_resumen.impuesto_total = total_impuesto_porcentaje;

    data_resumen.big_total = data_resumen.total_price_adultos + data_resumen.total_price_childrens + data_resumen.total_price_babys + total_hotel + data_resumen.impuesto_total + aditional;

    console.log(data_resumen.impuesto_origen);

    if (data_resumen.aditionals.percentage > 0) {
        let total_aditional = (((data_resumen.big_total - data_resumen.impuesto_total) - data_resumen.aditionals.total_aditionals) * data_resumen.aditionals.percentage) / 100;
        data_resumen.aditionals.total_aditionals = total_aditional;
        aditional = total_aditional;
    }
    data_resumen.subtotal = data_resumen.big_total - data_resumen.impuesto_total;

    updateInfoResumen();
    updateTablePrices();
    $('.alert').fadeOut('slow');
}

function updateTotalHotel(key) {
    let total_hotel = 0;
    priceHotelbyCapacity();

    switch (key) {
        case 'adult':
            let total_hotel_adults = data_resumen.total_hotel_adults;
            if (data_selected.package_rooms.length > 1) {
                if (data_resumen.total_real_adultos > data_resumen.total_capacity) {
                    total_hotel = total_hotel_adults + (data_resumen.total_real_adultos - data_resumen.total_capacity) * data_resumen.price_max.adult;

                } else {
                    total_hotel = total_hotel_adults;
                }
            } else {
                total_hotel = data_resumen.total_hotel_adults;
            }
            return data_resumen.total_hotel_adults;
            break;
        case 'child':
            let total_hotel_child = parseInt(data_resumen.total_hotel_childrens) || 0;
            if (data_resumen.total_childrens > 0) {
                const rooms = data_selected.package_rooms;
                total_hotel = 0;
                for (var iRoom = 0; iRoom < dataRooms.length; iRoom++) {
                    var dataRoom = dataRooms[iRoom],
                        repeatAmount = false;

                    if (dataRoom.childs > 0) {

                        for (var iChild = 0; iChild < dataRoom.ages.length; iChild++) {
                            var childAge = dataRoom.ages[iChild];
                            var priceChild = 0;


                            if (childAge > data_resumen.max_age_babys && childAge <= data_resumen.max_age_childrens) {
                                for (var i = 0; i < rooms.length; i++) {
                                    var room = rooms[i];
                                    var capacidad = parseInt(room.capacidad);

                                    if (capacidad == dataRoom.adults) {
                                        if (priceChild > 0) {
                                            repeatAmount = true;
                                        }

                                        priceChild = parseInt(room.price_child_room);
                                    }
                                }
                            }

                            if (repeatAmount) {
                                priceChild = total_hotel_child;
                            }

                            total_hotel += priceChild;
                        }

                    }
                }
            }

            return total_hotel;
            break;
        case 'baby':
            let total_hotel_babys = parseInt(data_resumen.total_hotel_babys) || 0;
            if (data_resumen.total_babys > 0) {
                const rooms = data_selected.package_rooms;
                total_hotel = 0;
                for (var iRoom = 0; iRoom < dataRooms.length; iRoom++) {
                    var dataRoom = dataRooms[iRoom],
                        repeatAmount = false;

                    if (dataRoom.childs > 0) {

                        for (var iChild = 0; iChild < dataRoom.ages.length; iChild++) {
                            var childAge = dataRoom.ages[iChild];
                            var priceChild = 0;


                            if (childAge <= data_resumen.max_age_babys) {
                                for (var i = 0; i < rooms.length; i++) {
                                    var room = rooms[i];
                                    var capacidad = parseInt(room.capacidad);

                                    if (capacidad == dataRoom.adults) {
                                        if (priceChild > 0) {
                                            repeatAmount = true;
                                        }

                                        priceChild = parseInt(room.price_baby_room);
                                    }
                                }
                            }

                            if (repeatAmount) {
                                priceChild = total_hotel_babys;
                            }

                            total_hotel += priceChild;
                        }

                    }
                }
            }
            return total_hotel;
            break;

    }
}

/**
 * Calculams el valor del hotel cuando la capacidad es mayor a los huespuedes
 * Promedio tarifa hotel (Niño || Bebe) / Cant de Habitaciones
 */
function priceHotelbyCapacity() {
    let newPrice = 0,
        total_price_child = 0,
        total_price_baby = 0,
        total_capacity = 0;
    const rooms = data_selected.package_rooms;
    for (var i = 0; i < rooms.length; i++) {
        total_price_child += parseInt(rooms[i].price_child_room);
        total_price_baby += parseInt(rooms[i].price_baby_room);
        newPrice += parseInt(rooms[i].price_room * rooms[i].capacidad) * rooms[i].cant;
        total_capacity += parseInt(rooms[i].capacidad);
    }
    data_resumen.total_capacity = total_capacity;
    data_resumen.total_hotel_adults = newPrice;
    data_resumen.total_hotel_childrens = (total_price_child / rooms.length);
    data_resumen.total_hotel_babys = (total_price_baby / rooms.length);
    // return
}
/**
 * Calculamos el impuesto segun capcaidad y cantidad por habitacion
 * @return impuesto
 */
function calculateImpHab(cantidad, capacidad) {
    return (data_resumen.impuesto_origen * capacidad) * cantidad;
}

function updateTablePrices() {

    // Sebastian Leiva
    var canttotal = parseInt(totalAdults) + parseInt(totalChildren);
    // Sebastian Leiva

    // var canttotal = parseInt($('#info-cantidadadultos').val()) + parseInt($('#info-cantidadniños').val());
    var table = $("#table-valores"),
        $price_origen = table.find('.precioOrigen'),
        $price_line = table.find('.precioLinea'),
        $price_destinos = table.find('.precioDestinos'),
        $price_butaca = table.find('.precioButaca'),
        $price_adicional = table.find('.precioAdicionales'),
        $cantidad_adultos = table.find('.cantidadAdultos'),
        $cantidad_childrens = table.find('.cantidadChildrens'),
        $cantidad_babys = table.find('.cantidadBabys'),
        $price_total_childrens = table.find('.precioChildrenTotal'),
        $price_total_babys = table.find('.precioBabysTotal'),
        $price_total_childrens_hotel = table.find('.precioChildrensHotel'),
        $price_total_babys_hotel = table.find('.precioBabysHotel'),
        $price_total_adultos_hotel = table.find('.precioAdultosHotel'),
        $price_impuesto = table.find('.precioImpuesto'),
        $price_total_adultos = table.find('.precioAdultosTotal');

    var value_price_line = '$ ' + formatMoney(parseFloat(data_resumen.price_line), 2, ',', '.'),
        value_price_destinos = '$ ' + formatMoney(parseFloat(data_resumen.price_destiny), 2, ',', '.'),
        value_price_origen = '$ ' + formatMoney(parseFloat(data_selected.origen.price), 2, ',', '.'),
        value_price_adicional = '$ ' + formatMoney(parseFloat(data_resumen.aditionals.total_aditionals), 2, ',', '.'),
        value_price_butaca = '$ ' + formatMoney(parseFloat(data_resumen.price_butaca), 2, ',', '.'),
        value_price_total_childrens = '$ ' + formatMoney(parseFloat(data_resumen.total_price_childrens), 2, ',', '.'),
        value_price_total_babys = '$ ' + formatMoney(parseFloat(data_resumen.total_price_babys), 2, ',', '.'),
        value_price_total_adultos_hotel = '$ ' + formatMoney(parseFloat(data_resumen.total_hotel_adults), 2, ',', '.'),
        value_price_total_childrens_hotel = '$ ' + formatMoney(parseFloat(data_resumen.total_hotel_childrens), 2, ',', '.'),
        value_price_total_babys_hotel = '$ ' + formatMoney(parseFloat(data_resumen.total_hotel_babys), 2, ',', '.'),
        value_price_total_adultos = '$ ' + formatMoney(parseFloat(data_resumen.total_price_adultos), 2, ',', '.'),
        value_price_impuesto = '$ ' + formatMoney(parseFloat(data_resumen.impuesto_line), 2, ',', '.'),
        value_price_impuesto_total = '$ ' + formatMoney(parseFloat(data_resumen.impuesto_total), 2, ',', '.'),
        value_price_subtotal = '$ ' + formatMoney(parseFloat(data_resumen.subtotal), 2, ',', '.'),
        value_price_porpersona = '$ ' + formatMoney(parseFloat(data_resumen.subtotal / canttotal), 2, ',', '.'),
        value_price_big_total = '$ ' + formatMoney(parseFloat(data_resumen.big_total), 2, ',', '.'),
        value_butaca_adicional = '$ ' + formatMoney(parseFloat(data_resumen.price_butaca + data_resumen.aditionals.total_aditionals), 2, ',', '.');

    $price_origen.html(value_price_origen);
    $price_line.html(value_price_line);
    $price_destinos.html(value_price_destinos);
    $price_butaca.html(value_price_butaca);
    $price_adicional.html(value_price_adicional);
    $cantidad_adultos.html(data_resumen.total_real_adultos);
    $cantidad_childrens.html(data_resumen.total_childrens);
    $cantidad_babys.html(data_resumen.total_babys);
    $price_total_adultos.html(value_price_total_adultos);
    $price_total_childrens.html(value_price_total_childrens);
    $price_total_babys.html(value_price_total_babys);
    $price_total_adultos_hotel.html(value_price_total_adultos_hotel);
    $price_total_childrens_hotel.html(value_price_total_childrens_hotel);
    $price_total_babys_hotel.html(value_price_total_babys_hotel);
    $price_impuesto.html(value_price_impuesto);

    var container_resumen = $(".container-resumen"),
        $origin_price = container_resumen.find('.resumen-origin-price'),

        $detalle_valor_adiconales = container_resumen.find(".resumen-adicional-total"),
        $detalle_valor_impuesto = container_resumen.find(".resumen-impuestos-total"),
        $detalle_valor_cant = container_resumen.find('.cantpesona'),
        $detalle_valor_porpersona = container_resumen.find('.resumen-porpersona'),
        $detalle_valor_subtotal = container_resumen.find('.resumen-subtotal'),
        $detalle_valor_total = container_resumen.find('.resumen-total');

    var container_butaca = container_resumen.find('.butacaresumen'),
        butaca_seleccionada = $('input:radio[name=butaca]:checked');

    container_butaca.hide();
    if (butaca_seleccionada.length > 0) {
        var container_alojamiento = $(".container-resumen .resumen-alojamiento");

        var content_butaca_small = '<strong>Butaca:&nbsp;</strong><span >' + butaca_seleccionada.val() + '</span><br>';
        var content_butaca = '<hr>' + content_butaca_small;

        if (container_alojamiento.find('strong').length > 0) {
            content_butaca_small = '<hr>' + content_butaca_small;
        }
        container_butaca.each(function() {
            var butaca_actual = $(this);
            if (butaca_actual.hasClass('butaca-small')) {
                butaca_actual.html(content_butaca_small);
            } else {
                butaca_actual.html(content_butaca);
            }
        });
        container_butaca.show();
    }

    var content_adicionales = '',
        adicionales = $("input[name=adicional]:checked");

    if (adicionales.length > 0) {
        content_adicionales += '<hr><p><strong>Adicional:&nbsp;</strong></p><ul class="list-adicionales">';
        adicionales.each(function() {
            var adicional = $(this);
            content_adicionales += '<li>' + $(this).attr('data-name') + '</li>';
        });
        content_adicionales += '</ul>';
    }

    container_resumen.find('.adicionalresumen').html(content_adicionales)
        //$origin_price.html(value_price_line);
        //$butaca_price.html(value_price_butaca);
    var canttotal = parseInt(totalAdults) + parseInt(totalChildren);
    if (canttotal > 1) {
        $detalle_valor_cant.html(canttotal + '&nbsp;personas')
    } else {
        $detalle_valor_cant.html(canttotal + '&nbsp;persona')
    }

    $detalle_valor_porpersona.html(value_price_porpersona);
    $detalle_valor_adiconales.html(value_butaca_adicional);
    $detalle_valor_impuesto.html(value_price_impuesto_total);
    $detalle_valor_subtotal.html(value_price_subtotal);
    $detalle_valor_total.html(value_price_big_total);

}

function updateInfoResumen()
{
  // console.log('updateInfoResumen entro');
    var container = $(".container-resumen"),
        container_alojamiento = container.find('.resumen-alojamiento'),
        //ORIGIN
        $origin_city = container.find('.resumen-origin-city'),
        $origin_date = container.find('.resumen-origin-date'),
        //BUTACA
        $butaca_name = container.find('.resumen-butaca-name');

    var content_alojamiento = '',
        data_rooms = data_selected.package_rooms;
    if (data_rooms.length > 0) {
      let roomListTemp = [];
        for (var i = 0; i < data_rooms.length; i++) {
            var current_room = data_rooms[i];
            var content_stars = generateStars(current_room.hotel_stars),
                format_price_room = "$ " + formatMoney((current_room.price_room * current_room.cant_room), ',', '.');


            let saveRoom = true;
            for (var k = 0; k < roomListTemp.length; k++) {
              // console.log(roomListTemp[k]+'  '+current_room.id_room);
              if(roomListTemp[k] == current_room.id_room){
                saveRoom = false;
              }
            }

            if(saveRoom == true){
              if (i > 0) {
                  content_alojamiento += '<hr>';
              }
              content_alojamiento += `<div id="id_room-${current_room.id_room}">`;
              content_alojamiento += '<strong>Destino: </strong><span>' + current_room.destiny_name + '</span><br>';
              content_alojamiento += '<strong>Hotel: </strong><span>' + current_room.hotel_name + '</span><br>';
              content_alojamiento += '<strong>Habitación: </strong><span>' + current_room.name_room + '</span><br>';
              content_alojamiento += '</div>';
              roomListTemp.push(current_room.id_room)
            }
            //            content_alojamiento += '<div class="row"><div class="col-sm-8"><ul><li>Alojamiento en <b class="text_naranja">'+current_room.destiny_name+'</b>';
            //            if(current_room.cant_room==1){
            //                content_alojamiento +=   ' '+ current_room.cant_room +' Habitación'
            //            }else{
            //                content_alojamiento +=   ' '+  current_room.cant_room +' Habitaciones'
            //            }
            //            content_alojamiento +='</li><li>'+current_room.hotel_name+' <span class="ml-4">'+content_stars+'</span></li></ul></div><div class="col-sm-4 d-flex justify-content-end align-items-center content-price">'+format_price_room+'</div></div><hr>';
        }
        // console.log('roomListTemp',roomListTemp);
        container_alojamiento.each(function() {
            var alojamiento_actual = $(this);
            if (alojamiento_actual.hasClass('resumen-alojamiento-small')) {
                alojamiento_actual.html(content_alojamiento);
            } else {
                alojamiento_actual.html('<hr>' + content_alojamiento);
            }
        });
    } else {
        container_alojamiento.html('');
    }

}


function generateStars(number_stars) {
    var content = '<div class="estrellas">';

    for (var i = 1; i <= 6; i++) {
        if (i <= number_stars) {
            content += '<i class="fas fa-star"></i>';
        } else {
            content += '<i class="far fa-star"></i>';
        }
    }

    content += '</div>';

    return content;
}

function comprarPaquete() {
    $('#alertbuy').hide();
    $('#alertbuy2').hide();
    arregloPasajeros = [];
    $('.pasajeros_formularios form').each(function() {
        addForm(this);
    });
    var package = $('#viajeroProduct').attr('data-package');
    var product = $(this).attr('data-product');
    var res = validatepackage();

    if (res == true) {

        var btnsComprar = $('.btn-comprar, #btn-next-form');
        btnsComprar.addClass('disabled');

        var data_pasajeros = {
            adultos: data_resumen.total_real_adultos,
            childrens: data_resumen.total_childrens,
            babys: data_resumen.total_babys
        };

        $.ajax({
            method: 'post',
            dataType: 'json',
            url: urlAjaxViajero,
            data: {
                package: package,
                habitaciones: data_selected.package_rooms,
                adicionales: data_selected.aditionals,
                butaca: data_selected.butaca.id,
                butaca_tipo_cupo_id: data_selected.butaca.tipoCupoId,
                data_pasajeros: data_pasajeros,
                pasajeros: arregloPasajeros,
                product: product,
                linea: data_selected.linea,
                total: data_resumen.big_total,
                origen: data_selected.origen.id,
                original_url: window.location.href,
                data_rooms: dataRooms,
                action: 'createProduct'
            },
            success: function(product) {

                // static_token = static_token;
                $.ajax({
                    type: 'POST',
                    headers: { "cache-control": "no-cache" },
                    url: window.location.origin,
                    async: true,
                    cache: false,
                    dataType: 'json',
                    data: 'controller=cart&add=1&ajax=true&qty=1&id_product=' + product.id + '&token=' + prestashop.static_token,
                    success: function(jsonData) {

                    }
                });

                var packageEdit = {
                    data_selected: data_selected,
                    data_resumen: data_resumen,
                    data_rooms: dataRooms,
                    data_pasajeros: arregloPasajeros,
                    data_hotel: hotelsGroupSelect
                };

                localStorage.setItem('data-package', JSON.stringify(packageEdit));
                localStorage.setItem('data-store-package', true);
                localStorage.setItem('infoStorage', $('.container-resumen.resumen-float').html() + '<div id="container-frame-buy"></div>');
                $('html, body').animate({ scrollTop: 0 }, 800);
                setTimeout(function() {
                    location.href = '/carrito?action=show&package=' + package;
                }, 1000);

            },
            error: function(err) {
                console.error(err.responseText);
            }
        });
    } else {
        $('#alertbuy').html(res);
        $('#alertbuy').fadeIn('slow');
        $('#alertbuy2').html(res);
        $('#alertbuy2').fadeIn('slow');
    }
}

$(document).on("click", ".open-arrow", function() {
    var element = $(".fixed-info-mobile .expand");
    if (element.is(":hidden")) {
        $(".fixed-info-mobile .expand").show("slide", { direction: "down" }, 200);
        $('.open-arrow').hide();
        $('.close-arrow').show();
    }

});

$(document).on("click", ".close-arrow", function() {
    var element = $(".fixed-info-mobile .expand");
    if (!element.is(":hidden")) {
        $(".fixed-info-mobile .expand").hide("slide", { direction: "down" }, 200);
        $('.close-arrow').hide();
        $('.open-arrow').show();
    }
});

function selectOriginDefault() {
    var current_data_selected = packageEdit.data_selected;

    var origin = $('.seleccion_vuelo[data-origen="' + current_data_selected.origen.id + '"]');
    origin.trigger('click');

    dataLoadEdit.origen = true;
}

function selectRoomsDefault() {
    var current_data_rooms = packageEdit.data_rooms;
    var containerParent = $('#viajero_rooms');
    var btnAddRoom = $('#viajero_add_room');
    $.each(current_data_rooms, function (index, current_room) {
        if (index > 0) {
            btnAddRoom.trigger('click');
        }

        var contentRoom = containerParent.find('.viajero_room_item').last();
        var inputAdults = contentRoom.find('.viajero_adults');
        var inputChildren = contentRoom.find('.viajero_children');

        inputAdults.val(current_room.adults);
        inputAdults.trigger('change');

        inputChildren.val(current_room.childs);
        inputChildren.trigger('change');

        if (current_room.childs > 0) {
            var inputsAgeChildren = contentRoom.find('.age-childrens');
            inputsAgeChildren.each(function(indexAge, inputAge) {
                var contentInputAge = $(inputAge);
                contentInputAge.val(current_room.ages[indexAge]);
                contentInputAge.trigger('change');
            });
        }
    });

    dataLoadEdit.data_rooms = true;

    selectPasajerosDefault();
}

function selectDestinyDefault() {
    var current_data_selected = packageEdit.data_selected;
    var line = $('input.seleccion_fecha[data-line="' + current_data_selected.linea + '"][type="radio"]');

    line.trigger('click');
    setTimeout(function() {
        $('#form-pasajeros button[type="submit"]').trigger('click');
    }, 0);
}

//let ClicAutoControl = false; (force = false)
function selectDestiniesLineDefault() {

	//if (ClicAutoControl && !force) return;

    var destinies = $("#container-destinations .seleccion_fecha.seleccion_estadia");
    destinyPreselect = destinies.length;
    destinies.each(function(index, destiny) {
        var contentDestiny = $(destiny);
        contentDestiny.trigger('click');
/*
        if(index == 0){
            var test  = contentDestiny.attr('data-target');
        }

        console.log(contentDestiny.removeClass('in'));
*/
    });
   //dataLoadEdit.data_hotels
	 setTimeout(()=>{
    $('#container-destinations .seleccion_estadia').eq(0).click();
	//ClicAutoControl = true;
}, 1000 * 1);
}

function selectHotelDefault(id) {
    var containerHotel = $('.contenedor_estadias[data-destiny="'+id+'"]');

    $.each(packageEdit.data_hotel, function (index, data_hotel) {
        if (data_hotel.destiny == id) {
            var btn = containerHotel.find('.btn-select-room[data-destiny="'+data_hotel.destiny+'"][data-hotel="'+data_hotel.hotel+'"][data-group="'+data_hotel.group_room+'"]')
            if (btn.length > 0) {
                btn.trigger('click');
            }
        }
    });
}

function selectButacaDefault() {
    if (packageEdit.data_selected.butaca.id !== 0) {
        var inputButaca = $('.seleccion_butaca[id="c' + packageEdit.data_selected.butaca.id + '"]')
        inputButaca.trigger('click');
    }

    dataLoadEdit.butaca = true;
}

function selectAdicionalDefault() {
    if (packageEdit.data_selected.aditionals.length > 0) {
        $.each(packageEdit.data_selected.aditionals, function(index, adicionalId) {
            var inputAdicional = $('.seleccion_adicional[data-id="' + adicionalId + '"]');
            console.log(inputAdicional);
            inputAdicional.trigger('click');
        });
    }

    dataLoadEdit.adicionals = true;
}

function selectPasajerosDefault() {
    var data_pasajeros = packageEdit.data_pasajeros;
    var containerPasajeros = $('.pasajeros_formularios form');
    containerPasajeros.each(function(index, containerPasajero) {
        var form_pasajero = $(containerPasajero);
        var data_pasajero = data_pasajeros[index];

        form_pasajero.find('#pasajeroNombre').val(data_pasajero.NOMBRE);
        form_pasajero.find('#pasajeroApellido').val(data_pasajero.APELLIDO);
        form_pasajero.find('#pasajeroDni').val(data_pasajero.DNI);
        form_pasajero.find('#pasajeroTelefono').val(data_pasajero.TELEFONO);
        form_pasajero.find('#pasajeroSexo').val(data_pasajero.SEXO);
        form_pasajero.find('.pasajero-fechanacimiente').val(data_pasajero.FECHANACIMIENTO);
		form_pasajero.find('#pasajeroTipoDoc').val(data_pasajero.TIPODOC);
    });

    dataLoadEdit.pasajeros = true;
}

function validateRoomTypeForm(){
  globalTyperoom = null;
}

$(document).ready(function(){
    var _originalSize = $(window).width() + $(window).height()
    var containerMobile = $('.fixed-info-mobile');
    $(window).resize(function(){
        if($(window).width() + $(window).height() != _originalSize){
            if (!containerMobile.hasClass('hide')) {
                containerMobile.addClass('hide');
            }
        } else {
            if (containerMobile.hasClass('hide')) {
                containerMobile.removeClass('hide');
            }
        }
    });
});
var acc = document.getElementsByClassName("accordion");
var i;

for (i = 0; i < acc.length; i++) {
  acc[i].addEventListener("click", function() {
    /* Toggle between adding and removing the "active" class,
    to highlight the button that controls the panel */
    this.classList.toggle("active");

    /* Toggle between hiding and showing the active panel */
    var panel = this.nextElementSibling;
    if (panel.style.display === "block") {
      panel.style.display = "none";
    } else {
      panel.style.display = "block";
    }
  });
}
