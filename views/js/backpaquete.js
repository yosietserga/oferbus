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

var file = '';
var arrayDestinos = [];
var arrayDestinosExport = [];
var arrayHotels = [];
var action = '';
var content2 = '';
var roomspac = '';
var token = Date.now();
var new_tinymce = 0;

var count_id_origin = 1,
    count_id_accordion_destiny = 1;

$(document).ready(function(){
    var pathArray = window.location.pathname.split('/');
    var urlsite=window.location.origin+'/'+pathArray[1];

    $('input[type=checkbox]').live('click', function(){
        var parent = $(this).parent().attr('id');
        $('input[type=checkbox]').removeAttr('checked');
        $(this).attr('checked', 'checked');
    });
    $('.chosen-select').chosen({width: '95%'});

    $('#fieldset_0').on("click", ".addorigin", function () {
        var destiny = $(this).attr('data-token');
        var flag = 0;        
        $('.bodytableorigin' + destiny +' .rowOrigin' ).each(function () {
            if ($(this).attr('data-id') == $("#origenes"+destiny).val()) {
                flag=1;
            }
        });
        if (flag == 0) {            
            if ($("#origenes" + destiny).val() === '' || $("#price_origin" + destiny).val() == '') {
                alert('Debe llenar los campos origen y precio ');
            } else {
                var inputOrigin = $("#origenes" + destiny),
                    optionOrigin = $("#origenes" + destiny + ' option:selected');

                addOrigin(inputOrigin.val(), $("#price_origin" + destiny).val(), destiny, optionOrigin.text(), optionOrigin);
            }
        }else{
            alert('Este origen ya fue agregado en esta fecha');
        }        
    });


    $('#fieldset_0').on("click", ".addallorigin", function () {
        var destiny = $(this).attr('data-token');
        var flag = 0;
        var inputOrigin = $("#origenes" + destiny).val();
        var priceOrigin = $("#price_origin" + destiny).val();

        if ($("#price_origin" + destiny).val() == '') {
            alert('Debe llenar el campo precio');
            return;
        }

        $(`#origenes${destiny} option`).each(function(){

            var optionOrigin = $(this);
            addOrigin(inputOrigin, priceOrigin, destiny, optionOrigin.text(), optionOrigin);

        });


        /*if (flag == 0) {
            if ($("#origenes" + destiny).val() === '' || $("#price_origin" + destiny).val() == '') {
                alert('Debe llenar los campos origen y precio ');
            } else {
                var inputOrigin = $("#origenes" + destiny),
                    optionOrigin = $("#origenes" + destiny + ' option:selected');

                addOrigin(inputOrigin.val(), $("#price_origin" + destiny).val(), destiny, optionOrigin.text(), optionOrigin);
            }
        }else{
            alert('Este origen ya fue agregado en esta fecha');
        }*/
    });




    
    $('#addbutaca').on("click", function () {
        if ($("#price_butaca").val() == '') {
            alert('Debe llenar el campo precio ');
        } else {
            addButaca();
        }
    });
    
    $('#addadicional').on("click", function () {
        if ($("#price_adicional").val() == '') {
            alert('Debe llenar el campo precio ');
        } else {
            addAdicional();
        }
    });  

    $('#datepickerfrom').on('change',function(){  
        var startdate=$(this).val();
        var myDate = startdate.split('-');
        var year = myDate[2].split(' ');
        $('#datepickerTo').datepicker( "option", "minDate", new Date(myDate[0], myDate[1] - 1, year[0]));
    });    
    $('#fieldset_0').on('click','.cancelarorigen',function(){ 
    $(this).parent().parent().parent().remove();
    });
    $('.bodytablebutaca ').on('click','.cancelarbutaca',function(){        
    $(this).parent().parent().parent().remove();
    });
    $('.bodytableadicional ').on('click','.cancelaradicional',function(){        
    $(this).parent().parent().parent().remove();
    });
    $('#tipoadd').on('change',function(){
        if($(this).val()=='%'){
        $('#price_adicional').attr('maxlength','2'); 
        }else{
        $('#price_adicional').removeAttr('maxlength');
        }
        $('#tipod').text($(this).val());
    });
    
    $("#inv_packages_form").validate({
        rules: {
            Name: "required",
            photo: "required",
            edadninos: "required",
            edadbebes: "required",
            valueninos: "required",
            valuebebes: "required",
            pricereference: "required",
        },
        messages: {
            Name: "Debe escribir un nombre",
            photo: "Debe ingresar una imagen para el paquete",
            edadninos: "Debe ingresar una edad limite para niños",
            edadbebes: "Debe ingresar una edad limite para bebes",
            valueninos: "Ingrese el porcentaje",
            valuebebes: "Ingrese el porcentaje",
            pricereference: "Ingrese precio de referencia del paquete",
        }
    });

    $("#inv_packages_form").submit(function(e) {        
        e.preventDefault();
        var lineas = getLineas();
        var butacas = getButacas();
        var adicionales = getAdicionales();        
        if($('#inv_packages_form_submit_btn').attr('data-action')==''){
            var action='savePackage';
        }else{
            var action='updatePackage';            
        }
        if ($("#inv_packages_form").valid()) {      
            var ff = $('#photo');
            var file = ff[0].files[0]; 
            if (typeof(file) === 'undefined') {
                var flag=false;
            }else{
                var flag=true;
            }
            var iframe = $('#detalles_ifr');
            var editorContent = $('#tinymce[data-id="detalles"]', iframe.contents()).html();   
            var iframe2 = $('#legales_ifr');
            var legales = $('#tinymce[data-id="legales"]', iframe2.contents()).html();   
            var flag2=false;
            if(validateCant()&& validateCampos(lineas)){
                var cateChosen = '', cantRoom = '';
                $("#category > option:selected").each(function(){
                    cateChosen += $(this).val() + ',';
                });

                var data = {
                    image:flag,
                    ajax:1,
                    Name:$('#Name').val(),
                    //quota:$('#quota').val(),
                    quota_api:$('#form-package-quota').val(),
                    category: cateChosen,
                    disponibilidad:$('#disponibilidad').val(),
                    edadninos:$('#edadninos').val(),
                    edadbebes:$('#edadbebes').val(),
                    valuebebes:$('#valuebebes').val(),
                    valueninos:$('#valueninos').val(),
                    pricereference:$('#pricereference').val(),
                    detalles: editorContent,
                    legales: legales,
                    butacas:butacas,              
                    lineas:lineas,              
                    tok:token,                   
                    id_package:$('#inv_packages_form_submit_btn').attr('data-action'),
                    adicionales:adicionales,                     
                    action: action,
                    api: false
                };

                if (dataSelected.api) {
                    var currentPackage = getPackageSelected(formPackageApi.val());

                    data.api = true;
                    data.id_package_api = formPackageApi.val();
                    data.data_api = dataLoad;
                    data.data_selected = dataSelected;
                    data.quota_api = formQuotaApi.val();
                    data.company_id = currentPackage.EmpresaID;
                }

                cateChosen = cateChosen.substr(0, cateChosen.length - 1);
                //console.log(data);
                $.ajax({
                    url: $('#fieldset_0').attr('data-url'),
                    method: 'post',
                    dataType: 'json',
                    data: data,
                    success: function (response) {
                        $('html, body').animate({scrollTop: 0}, 800);
                        $('#alertdone').fadeIn();
                        setTimeout(function () {
                            // location.href=window.history.back();
                            let backUrl = $('.cancel-package').attr('href');
                            location.href = backUrl;
                        }, 3000);

                    }
                });
            }
        } else {
            
        }        
    });
    
    $('#photo').on('change', function (e) {
        var tt = this;
        addImagePackage(tt);
    });
    $('.bodytablephotos ').on('click', '.removephotoroom', function () {
        var name = $(this).attr('data-name');
        var tt = $(this);
        var packagePhoto = $(this).attr('data-hotelphoto');
        var package = $(this).attr('data-hotel');
        if (confirm('La imagen se eliminara de forma permanente. ¿Desea continuar?')) {
            $.ajax({
                method: 'post',
                dataType: 'json',
                url: $('#fieldset_0').attr('data-url'),
                data: {
                    ajax: 1,
                    name: name,
                    packagePhoto: packagePhoto,
                    package: package,
                    tok: token,
                    action: 'deleteImgTemp',
                },
                success: function (response) {
                    if (response !== '') {
                        tt.parent().parent().parent().remove();
                    }
                }
            });
        }
    });
    $('#photo').on('change', function (e) {
        var package = $('#inv_packages_form_submit_btn').attr('data-action');
        var tt = this;
        formdata = new FormData();
        var file = this.files[0];
        if (formdata) {
            formdata.append("image", file);
            formdata.append("ajax", 1);
            formdata.append("tok", token);
            formdata.append("package", package);
            formdata.append("action", 'uploadPhoto');
            $('#loading').show();
            jQuery.ajax({
                url: $('#fieldset_0').attr('data-url'),
                type: "POST",
                data: formdata,
                processData: false,
                contentType: false,
                success: function (response) {
                    $('#loading').hide();
                    if (response !== '') {
                        addImageRoom(tt, response);
                    } else {
                        alert('La imagen no pudo ser cargada');
                    }
                }
            });
        }
    });

    initTinymc();

    $("#date-departure").datetimepicker({
        dateFormat: 'yy-mm-dd',
        minDate: 'today',
        language: 'es',
        changeYear: true,
        numberOfMonths: 1,
        changeMonth: true,
        yearRange: '-90 :+100'
    });

    $("#date-arrival").datetimepicker({
        dateFormat: 'yy-mm-dd',
        minDate: 'today',
        language: 'es',
        changeYear: true,
        numberOfMonths: 1,
        changeMonth: true,
        yearRange: '-90 :+100'
    });

    $('#date-departure').on('change',function(){  
        var startdate=$(this).val();
        var myDate = startdate.split('-');
        var year = myDate[2].split(' ');
        $('#date-arrival').datepicker( "option", "minDate", new Date(myDate[0], myDate[1] - 1, year[0]));
    });

    $("#btn-add-origin").click(function(e){
        e.preventDefault();
        var $date_departure = $("#date-departure"),
            date_departure = $date_departure.val(),
            $date_arrival = $("#date-arrival"),
            date_arrival = $date_arrival.val();        

        if ( date_departure == '' || date_arrival == '') {
            alert('Por favor complete los campos de Fecha');
            return;
        }

        if (dataSelected.api && (!formDepartureApi.val() || formDepartureApi.val() == '')) {
            alert('Por favor selecciona una salida del API');
            return
        }

        //var origin_name = $origin.find("option[value='"+origin_id+"']").html();

        if (dataSelected.api) {
            validateDepartureDetails(formDepartureApi.val(), date_departure, date_arrival);
            return;
        }


        addOriginAccordion(false, date_departure, date_arrival, true);
    });

    $("#btn-clone-origin").click(function(e){
        e.preventDefault();
        var $date_departure = $("#date-departure"),
            date_departure = $date_departure.val(),
            $date_arrival = $("#date-arrival"),
            date_arrival = $date_arrival.val();

        if ( date_departure == '' || date_arrival == '') {
            alert('Por favor complete los campos de Fecha');
            return;
        }
        if ($('.select_package').is(':checked')) {
            var id_package_linea = $(".select_package:checked").data('id-package');
            getCloneLine(id_package_linea, date_departure, date_arrival);
        } else {
            alert('Tilde un salida para copiar');
            return;
        }
        
        
    });

    $("#container-list-lines").on('click', '.btn-delete-collapse', function(e){
        e.preventDefault();
        var btn = $(this),
            collapsible = btn.parent().parent();
            container_parent = collapsible.parent(),
            all_panels = container_parent.find('> .panel'),
            delete_parent = false;

        if (all_panels.length == 1) {
            delete_parent = true;
        }

        collapsible.remove();

        if (delete_parent) {
            container_parent.remove();
        }
    });

    $("#container-list-lines").on('change', '.select-destiny-line', function(){
        var select = $(this),
            current_destiny = select.val(),
            container_parent = select.parent().parent(),
            select_hotel = container_parent.find('.select-hotel-line'),
            content = '<option value="">--</option>';

        if (dataSelected.api) {
            getHotelsDestination(select);
            return;
        }

        $.ajax({
            method: 'post',
            dataType: 'json',
            url: $('#fieldset_0').attr('data-url'),
            data: {
                ajax: 1,
                id_destiny: current_destiny,
                action: 'getHotelsByDestiny',
            },
            success: function (hotels) {
                $.each(hotels, function(key, hotel){
                    content += '<option value="'+hotel.id_hotel+'">'+hotel.name+'</option>';
                });
        
                select_hotel.find('option').remove();
                select_hotel.append(content);
            }
        });
    });

    $("#container-list-lines").on('click', '.btn-add-destiny-line', function(e){
        e.preventDefault();
        var btn = $(this),
            panel_origin = btn.parents('.panel-line-origin'),
            $cupo = panel_origin.find('.cupo'),

            $impuesto = panel_origin.find('.impuesto'),
            cupo =$cupo.val(),

            impuesto =$impuesto.val(),                        
            $destiny = panel_origin.find('.select-destiny-line'),
            destiny_id = $destiny.val(),
            $hotel = panel_origin.find('.select-hotel-line'),
            hotel_id = $hotel.val(),
            $pricedes = 0,
            precio=0;
            var origins= panel_origin.find('.rowOrigin');
        if (cupo == '' || impuesto == '') {
            alert('Por favor complete los campos de cupo y impuesto');
            return;
        }
        if (origins.length==0) {
            alert('Debe agregar al menos un origen');
            return;
        }
        if (destiny_id == '' || hotel_id == '') {
            alert('Por favor complete los campos del destino');
            return;
        }

        var destiny_name = $destiny.find("option[value='"+destiny_id+"']").html(),
            hotel_name = $hotel.find("option[value='"+hotel_id+"']").html();
        var idLinea = $(this).attr('data-id');

        if (dataSelected.api) {
            getRoomsHotels(panel_origin);
            return;
        }
        
        $.ajax({
            method: 'post',
            dataType: 'json',
            url: $('#fieldset_0').attr('data-url'),
            data: {
            ajax: 1,
                id_hotel: hotel_id,
                action: 'getRoomsByHotelPack',
            },
            success: function (rooms) {
                if(rooms.length!==0){
                    addDestinyAccordion(panel_origin, destiny_id, destiny_name, hotel_id, hotel_name, rooms,idLinea, 0);
                }else{
                    alert('Este hotel no tiene habitaciones asignadas');
                }
                
            }
        });

    });

});

function getCloneLine(id_package_linea, datesal, datelle) {
    $.ajax({
        method: 'post',
        dataType: 'json',
        url: $('#fieldset_0').attr('data-url'),
        data: {
            ajax: 1,
            id_package_linea:id_package_linea,
            action: 'getLineaId',
        },
        success: function (lineas) {
            $.each(lineas, function (key, linea) {
                let result = addOriginAccordion(linea.id_package_linea, datesal, datelle, false, linea.inventario,
                    linea.price, linea.impuesto, linea.description, linea.origins, linea.services);

                $.each(linea.hotels, function(key2, hotel){
                    let panelorigen = result.conta.find('.panel-line-origin').first();
                    addDestinyAccordion(panelorigen, hotel.iddestiny, hotel.destinyname, hotel.idhotel, hotel.namehotel, hotel.rooms,result.ahora, linea.idpackagelinea, hotel.rooms[0].cant)
                });        
            });
            cargeCants();
            $(".select_package:checkbox").attr('checked', false);
        }
    });
}

function getLineas(){    
    var lineas=[];
    var panel_lineas = $($('.panel-line-origin').get().reverse());
    panel_lineas.each(function () {
        var panel = $(this);
        var iframe = panel.find("iframe[id^='description']"),
            iframe_id = iframe.attr("id"),
            id_editor = iframe_id.substr(0, (iframe_id.length - 4));

        var editorContent = tinyMCE.get(id_editor).getContent();
        var rooms = [];
        var origins = [];
        $(this).find('.rowRooms').each(function (keyRoom, roomContent) {
            var cupos_room = $(this).parent().parent().parent().find('.cupos_room').val();
            var id_hotel = $(this).parent().parent().parent().find('.line_hotel_id').val();
            var srv_alojamiento_id = $(this).parent().parent().parent().find('.line_srv_alojamiento_id').val();

            if ($(this).find('.price_room').val() !== '' ) {
                var $room = $(roomContent);

                var room = {
                    id_room: $(this).attr('data-id'),
                    id_hotel: id_hotel,
                    price_room: $(this).find('.price_room').val(),
                    price_ninos: $(this).find('.price_ninos').val(),
                    price_bebes: $(this).find('.price_bebes').val(),
                    cupos_room: cupos_room,
                    srv_alojamiento_id : srv_alojamiento_id
                };

                if (dataSelected.api) {
                    room.hotel_name = $room.attr('data-hotel-name');
                    room.room_name = $room.attr('data-room-name');
                    room.destination_id = $room.attr('data-destination-id');
                    room.destination_name = $room.attr('data-destination-name');
                    room.capacidad = $room.attr('data-capacidad');

                    room.categoria_habitacion_id = $room.attr('data-categoria-habitacion-id');
                    room.tipo_habitacion_id = $room.attr('data-tipo-habitacion-id');
                    room.regimen_id = $room.attr('data-regimen-id');
                }

                //price_destino: $(this).attr('data-price-destino'),
                rooms.push(room);
            }
        });
         $(this).find('.rowOrigin').each(function () {
            if ($(this).attr('data-id') !== '' && $(this).attr('data-price') !== '') {
                var origin = {
                    id_origen: $(this).attr('data-id'),
                    price: $(this).attr('data-price'),
                    name: $(this).attr('data-name'),
                    provincia_id: $(this).attr('data-provincia-id'),
                    provincia_name: $(this).attr('data-provincia')
                };

                origins.push(origin);
            }
        });

        var services = {transporte: $(this).find('.transporte').val(),
            duracion: $(this).find('.duracion').val(),
            regimen: $(this).find('.regimen').val(),
            asistencia: $(this).find('.asistencia').val(),
            coordinacion: $(this).find('.coordinacion').val()};

        var Linea = {
            id_origen: $(this).find('.line_origin_id').val(),
            date_sal: $(this).find('.line_departure_id').val(),
            date_lle: $(this).find('.line_arrival_id').val(),
            inventario: $(this).find('.cupo').val(),
            price: 0,
            impuesto: $(this).find('.impuesto').val(),
            description: editorContent,
            rooms: rooms,
            services: services,
            origins: origins,
        };

        if (dataSelected.api) {
            Linea.departure_id = panel.attr('data-departure');

            Linea.transporte = {
                tipo_servicio_id: panel.attr('data-tipo_servicio-id'),
                //tipo_butaca_id: panel.attr('data-tipo-butaca-id'),
                tipo_cupo_id: panel.attr('data-tipo-cupo-id'),
                transporte_name: panel.attr('data-transporte-name'),
                transporte_id: panel.attr('data-transporte-id'),
            };
        }

        lineas.push(Linea);        
    });
    return lineas;
}
function addImageRoom(input, name) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function (e) {
            var content22 = '';
            var contentbutton = "<div class='panel-heading-action'><a class='btn removephotoroom' data-name=" + name + " ><span class='label label-danger'><i class='icon-remove'></i></span></a></div>"
            content22 += "<tr><td style='text-align:center;'><img src='" + e.target.result + "' style='width:100px;'/></td><td>" + input.files[0].name + "</td><td>" + contentbutton + "</td></tr>"
            $('.bodytablephotos').append(content22);
        }
        reader.readAsDataURL(input.files[0]);
    }
}
function cargeOrigins(){
//    orgigen, priceorigen,destiny
    $('#fieldset_0').find('.tableorigins').each(function(){
        var package = $('#inv_packages_form_submit_btn').attr('data-action');
        var destiny = $(this).attr('data-destiny');
            $.ajax({
            method: 'post',
            dataType: 'json',
            url: $('#fieldset_0').attr('data-url'),
            data: {
                ajax: 1,
                id_package:package,
                id_destiny:destiny,
                action: 'getOriginsDestinySelect',
            },
            success: function (response) {
                // console.info(response);
                // console.info(response.length);
                $('.bodytableorigin'+destiny).html('');  
                $.each(response, function (i, val) {
                   addOrigin(val.id_origin,val.price,val.id_destiny,val.origen);
                });                
            }
        });
    });
}
function cargeCants(){
    $('#fieldset_0').find('.rowRooms').each(function(){

        var tt = $(this);
        var id_room = $(this).attr('data-id');

        if (init_api === 1 && assignDefault) {
            id_room = $(this).attr('data-room-prev');
        }

        if (id_room) {
            var packagelinea = $(this).attr('dat-linea');
            $.ajax({
                method: 'post',
                dataType: 'json',
                url: $('#fieldset_0').attr('data-url'),
                data: {
                    ajax: 1,
                    id_room:id_room,
                    id_package_linea:packagelinea,
                    action: 'getRoompack',
                },
                success: function (response) {
                    if(response !==0){
                        // tt.find('.cant_room').val(response.cant);
                        tt.find('.price_room').val(response.price);
                        tt.find('.price_ninos').val(response.priceninos);
                        tt.find('.price_bebes').val(response.pricebebes);
    //                    tt.attr('data-price-destino',response.pricedestino);
                    }
                }
            });
        }
    });
}
function validateCampos(lineas){
    var flag =true;
    var butacas = document.getElementsByClassName("rowButaca").length;
//    if(butacas==0){
//        alert('Debe haber seleccionado al menos una butaca');
//        flag =false;
//    }
    if (lineas.length == 0) {
        alert('Debe Agregar al menos una Fecha de salida y llegada');
        flag = false;
    }
    lineas.forEach(function (linea, index) {
        var date_lle_format = new Date(linea.date_lle),
                day_lle = date_lle_format.getDate(),
                month_lle = (date_lle_format.getMonth()) + 1,
                year_lle = date_lle_format.getFullYear();
        full_date_lle = day_lle + '-' + month_lle + '-' + year_lle;

        var date_sal_format = new Date(linea.date_sal),
                day_sal = date_sal_format.getDate(),
                month_sal = (date_sal_format.getMonth()) + 1,
                year_sal = date_sal_format.getFullYear();
        full_date_sal = day_sal + '-' + month_sal + '-' + year_sal;
        if (linea.origins.length == 0) {
            alert('La fecha de salida ' + full_date_sal + ' y fecha de llegada ' + full_date_lle + ' no tiene origenes vinculados');
            flag = false;
        }

        if (typeof linea.rooms == 'undefined') {
            alert('La fecha de salida ' + full_date_sal + ' y fecha de llegada ' + full_date_lle + ' debe llenar la informacion de las habitaciones');
            flag = false;
        }/* else {
            if (linea.rooms.length == 0) {
                alert('La fecha de salida ' + full_date_sal + ' y fecha de llegada ' + full_date_lle + ' debe llenar la informacion de las habitaciones');
                flag = false;
            }
        }*/

    });
    return flag;
    
}
function validateCant(){
    
    var flag = true;
    $('#fieldset_0').find('.panel-line-destiny').each(function(){
        var cupo = parseInt($('#fieldset_0').find('#cupo'+$(this).parent().attr('data-linea')).val());
//        console.info(cupo);
        var name =$(this).find('.panel-heading a').text();     
        
        var total=0;
       $(this).find('.rowRooms').each(function(){
//            if($(this).find('.cant_room').val()!==''){
//                 total=total + (parseInt($(this).attr('data-canthues'))* parseInt($(this).find('.cant_room').val()))           
// //                console.info(total);
//            }
       });       
//       console.info(total);
//       console.info(cupo);
    //    if(total!==cupo && SOLD < 1){//only if room has no sold vacancies.
    //        alert('La cantidad de habitaciones no son iguales para el cupo asignado al paquete en el destino '+ name );
    //        flag =false;
    //    }   
    });
    return flag;
}
function getRegimen(){
    var regimens=[];
    $('.regimen').each(function(){
        var regimen=[$(this).attr('data-id_hotel'),$(this).find('.desayuno').val(),$(this).find('.media').val(),$(this).find('.completa').val(),$(this).find('.limninos').val(),$(this).find('.limbebes').val()];      
        regimens.push(regimen);
    });
    return regimens;
}
function getButacas(){
    var butacas=[];
    $('.rowButaca').each(function(){
        var butaca=[$(this).attr('data-id'),$(this).attr('data-price')];
        butacas.push(butaca);
    });
    return butacas;
}

function getOrigenes(){
    var origenes=[];
    $('.rowOrigin').each(function(){
        var Origen=[$(this).attr('data-id'),$(this).attr('data-price')];
        origenes.push(Origen);
    });
    return origenes;
}
function getDestinos(){
    var origenes=[]
    var destinos=[];
    $('.destinos').each(function(){
        var des = $(this).attr('data-id');
        $('.rowOrigin'+des).each(function () {
            var Origen = [$(this).attr('data-id'), $(this).attr('data-price'),des];
            origenes.push(Origen);
        });
        var iframe = $('#description'+$(this).attr('data-id')+'_ifr');
        var editorContent = $('#tinymce[data-id="description'+$(this).attr('data-id')+'"]', iframe.contents()).html();        
        var destino=[$(this).attr('data-id'),$(this).attr('data-price'),$(this).attr('data-fesal'),$(this).attr('data-feclle'),editorContent ,$(this).attr('data-impuesto'),origenes ];
        destinos.push(destino);
        origenes = [];
    });
    return destinos;
}
function getAdicionales(){
    var adicionales=[];
    $('.rowAditional').each(function(){
        var adicional=[$(this).attr('data-id'),$(this).attr('data-Type'),$(this).attr('data-price')];
        adicionales.push(adicional);
    });
    return adicionales;
}
function getRooms(){
    var rooms=[];
    $('.rowRooms').each(function(){    
        if($(this).find('.price_room').val()!==''){
            // var room=[$(this).attr('data-id'),$(this).find('.cant_room').val(),$(this).attr('data-fechasal'),$(this).attr('data-fechalle'),$(this).find('.price_room').val(),$(this).find('.price_ninos').val(),$(this).find('.price_bebes').val()];
            var room=[$(this).attr('data-id'), 0,$(this).attr('data-fechasal'),$(this).attr('data-fechalle'),$(this).find('.price_room').val(),$(this).find('.price_ninos').val(),$(this).find('.price_bebes').val()];
            rooms.push(room);
        }
    });    
    return rooms;
}


function addImagePackage(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function (e) {
            var contentbutton = "<label style='margin-right: 24px;'>"+input.files[0].name+"</label><img src='" + e.target.result + "' style='width:30%;'/>"
            $('#imgpack').html(contentbutton);
        }
        reader.readAsDataURL(input.files[0]);
    }
}
function addOrigin(origin, priceorigin, destiny, origen, optionSelect = null) {
 
    var content=''
    var contentbutton='<div class="panel-heading-action"><a class="btn cancelarorigen" ><span class="label label-danger"><i class="icon-remove"></i></span></a></div>'
    if(typeof origen==='undefined'){
        content+='<tr class="rowOrigin" data-id="'+origin+'" data-name="' + $('#origenes'+destiny+' option:selected').text() + '" data-price="'+priceorigin+'"><td>'+$('#origenes'+destiny+' option:selected').text()+'</td><td>'+priceorigin+'</td><td>'+contentbutton+'</td></tr>'
    }else{
        if (dataSelected.api) {
            var provincia_id = optionSelect.attr('data-provincia-id'),
                provincia_name = optionSelect.attr('data-provincia');

            content+='<tr class="rowOrigin" data-provincia="' +provincia_name+ '" data-provincia-id="' + provincia_id + '" data-id="'+origin+'" data-name="' + origen + '" data-price="'+priceorigin+'"><td>'+origen+'</td><td>'+priceorigin+'</td><td>'+contentbutton+'</td></tr>'
        } else {
            content+='<tr class="rowOrigin" data-id="'+origin+'" data-name="' + origen + '" data-price="'+priceorigin+'"><td>'+origen+'</td><td>'+priceorigin+'</td><td>'+contentbutton+'</td></tr>'
        }
    }
    $('.bodytableorigin'+destiny).append(content);      
    $("#fieldset_0 #price_origin"+destiny).val(0);
}
function addOrigin2(origin,priceorigin,destiny){    
    var content=''
    var contentbutton='<div class="panel-heading-action"><a class="btn cancelarorigen" ><span class="label label-danger"><i class="icon-remove"></i></span></a></div>'
    content+='<tr class="rowOrigin'+destiny+'" data-id="'+origin+'" data-price="'+priceorigin+'"><td>'+$('#origenes'+destiny+' option:selected').text()+'</td><td>'+priceorigin+'</td><td>'+contentbutton+'</td></tr>'
    return content;    
}
function addButaca(){
    var content=''
    var contentbutton='<div class="panel-heading-action"><a class="btn cancelarbutaca" ><span class="label label-danger"><i class="icon-remove"></i></span></a></div>'
    content+='<tr class="rowButaca" data-price="'+$('#price_butaca').val()+'"data-id="'+$('#butacas').val()+'"><td>'+$('#butacas option:selected').text()+'</td><td>'+$('#price_butaca').val()+'</td><td>'+contentbutton+'</td></tr>'
    $('.bodytablebutaca').append(content); 
    $("#price_butaca").val(0);
}

function addAdicional(){
    var content=''
    var contentbutton='<div class="panel-heading-action"><a class="btn cancelaradicional" ><span class="label label-danger"><i class="icon-remove"></i></span></a></div>'
    content+='<tr class="rowAditional" data-id="'+ $('#adicionales option:selected').val()+'" data-type="'+ $('#tipoadd').val() +'" data-price="'+ $('#price_adicional').val() +'"><td>'+$('#adicionales option:selected').text()+'</td><td> '+$('#tipoadd').val()+$('#price_adicional').val()+'</td><td>'+contentbutton+'</td></tr>'
    $('.bodytableadicional').append(content); 
    $("#price_adicional").val('');
}

function addDestiny(id_destiny,datestart,dateend){
    
//        if(typeof arrayDestinos[id_destiny] !== 'undefined'){
//           return false;
//        }else{                          
//             destino={id_destiny:id_destiny,fecha_salida:datestart,fecha_llegada:dateend};
//             arrayDestinos[id_destiny]=destino;
//        }      
	return true;
}
function addHotel(e){  
//      
//        if(arrayHotels.includes(e)){
//           return false;
//        }else{
//             arrayHotels.push(e);
//        }       
	return true;
}





function numbersvalid(e){
    tecla = (document.all) ? e.keyCode : e.which;
    if (tecla==8){
        return true;
    }
    patron =/[0-9]/;
    tecla_final = String.fromCharCode(tecla);
    return patron.test(tecla_final);
}

 function numbersvalid2(e){
     tecla = (document.all) ? e.keyCode : e.which;
     if (tecla==8){
         return true;
     }
     patron =/^\d*\.?\d*$/;
     tecla_final = String.fromCharCode(tecla);
     return patron.test(tecla_final);
 }



/* Functions */


function addOriginAccordion(idpackagelinea, date_departure, date_arrival, collapse_panel,cupo,precio,impuesto,description,origins,services){
    var content = '',
        container = $("#container-list-lines"),
        class_collapse = (collapse_panel)? ' in ' : '';

    if (container.find('#accordion-lines').length == 0) {
        var collapsible_parent = '<div class="panel-group accordion-panels" id="accordion-lines" role="tablist" aria-multiselectable="true"></div>';
        container.html(collapsible_parent);
    }    
    if (typeof (services) != 'undefined') {
        if (typeof (services['transporte']) != 'undefined') {
            var transporte = services['transporte'].description;
        } else {
            var transporte = '';
        }        
        if (typeof (services['coordinacion']) != 'undefined') {
            var coordinacion = services['coordinacion'].description;
        } else {
            var coordinacion = '';
        }
        if (typeof (services['duracion']) != 'undefined') {
            var duracion = services['duracion'].description;
        } else {
            var duracion = '';
        }
        if (typeof (services['regimen']) != 'undefined') {
            var regimen = services['regimen'].description;
        } else {
            var regimen = '';
        }
        if (typeof (services['asistencia']) != 'undefined') {
            var asistencia = services['asistencia'].description;
        } else {
            var asistencia = '';
        }
    }

    container = container.find('#accordion-lines');    
    var date_apt = Date.parse(date_departure);    
    var date_arr = Date.parse(date_arrival);

    var date_lle_parts_full = date_arrival.split(' ')[0],
        date_lle_parts = date_lle_parts_full.split('-'),
        full_date_lle = date_lle_parts[2] + '-' + date_lle_parts[1] + '-' + date_lle_parts[0];

    var date_sal_parts_full = date_departure.split(' ')[0],
        date_sal_parts = date_sal_parts_full.split('-'),
        full_date_sal = date_sal_parts[2] + '-' + date_sal_parts[1] + '-' + date_sal_parts[0];

    var ahora =Date.now();
    var btn_clone = '';
    if (idpackagelinea) {
        btn_clone = '<div style="position:absolute;top:0;right:40px;"><input class="select_package" type="checkbox" name="select_package" data-id-package="'+idpackagelinea+'" style="margin-top:8px; width: 20px; height: 20px;" /></div>';
    }
    if(typeof cupo!=='undefined' &&typeof impuesto!=='undefined'){    
        content = '<div class="panel panel-default panel-line-origin" data-id="'+ahora+'"><div class="panel-heading" role="tab"><a role="button" data-toggle="collapse" data-parent="#accordion-lines" href="#accordion-origin-'+count_id_origin+'" aria-expanded="true"> FECHA SALIDA: '+full_date_sal+' --- FECHA LLEGADA: '+full_date_lle+'</a><a href="#" class="btn-delete-collapse"><span class="label label-danger"><i class="icon-remove"></i></span></a>'+btn_clone+'</div><div id="accordion-origin-'+count_id_origin+'" class="panel-collapse collapse '+class_collapse+'" role="tabpanel"><div class="panel-body">';
    }else{
        content = '<div class="panel panel-default panel-line-origin" data-id="'+(date_apt+date_arr)+'"><div class="panel-heading" role="tab"><a role="button" data-toggle="collapse" data-parent="#accordion-lines" href="#accordion-origin-'+count_id_origin+'" aria-expanded="true"> FECHA SALIDA: '+full_date_sal+' --- FECHA LLEGADA: '+full_date_lle+'</a><a href="#" class="btn-delete-collapse"><span class="label label-danger"><i class="icon-remove"></i></span></a>'+btn_clone+'</div><div id="accordion-origin-'+count_id_origin+'" class="panel-collapse collapse '+class_collapse+'" role="tabpanel"><div class="panel-body">';
    }    
    content += '<input type="hidden" class="line_departure_id" value="'+date_departure+'">';
    content += '<input type="hidden" class="line_arrival_id" value="'+date_arrival+'">';

    
    content += '<div class="form-group"><div class="row"><div class="col-lg-10 col-lg-offset-1"><div class="row" style="padding-top:15px;">';
    if(typeof cupo!=='undefined'&&typeof impuesto!=='undefined'){
        if (dataSelected.api) {
            content += '<div style="padding-bottom:10px;" class="col-lg-6 hide"><label>Cupo Asiento (API)</label><input readonly onkeypress="return numbersvalid(event)" class="cupo-asiento" type="text" placeholder="Asiento"></div>';
            content += '<div style="padding-bottom:10px;" class="col-lg-6 hide"><label>Cupo Alojamiento (API)</label><input readonly onkeypress="return numbersvalid(event)" class="cupo-alojamiento" type="text" placeholder="Impuesto"></div>';
        }

        content += '<div style="padding-bottom:10px;" class="col-lg-6"><label>Cupo Asientos</label><input value="'+cupo+'"onkeypress="return numbersvalid(event)" id="cupo'+ ahora +'" class="cupo" type="text" placeholder="Cupo"></div>';        
        // content += '<div class="col-lg-4"><label>Tarifa</label><input value="'+precio+'" onkeypress="return numbersvalid(event)" class="precio" type="text" placeholder="Tarifa"></div>';
        content += '<div style="padding-bottom:10px;" class="col-lg-6"><label>Impuesto</label><input value="'+impuesto+'" onkeypress="return numbersvalid2(event)" class="impuesto" type="text" placeholder="Impuesto"></div>';
        content +='<div class="panel panel-default" style="margin-bottom:10px !important;margin-top:64px !important;"><div class="panel-heading">Servicios</div><div class="panel-body">'
        content += '<div class="col-lg-4"><label>Transporte</label><input value="'+ transporte+'" id="transportess" class="transporte" type="text" placeholder="Transporte"></div>';        
        content += '<div class="col-lg-4"><label>Duración</label><input value="'+ duracion+'" class="duracion" type="text" placeholder="Duración"></div>';
        content += '<div class="col-lg-4"><label>Regimen</label><input value="'+ regimen+'" class="regimen" type="text" placeholder="regimen"></div>';  
        content += '<div class="col-lg-4"><label>Asistencia</label><input value="'+ asistencia+'" class="asistencia" type="text" placeholder="Asistencia"></div>';  
        content += '<div class="col-lg-4"><label>Coordinación</label><input value="'+ coordinacion+'" class="coordinacion" type="text" placeholder="Coordinación"></div>';  
        content += '</div></div></div>'
    }else{
        if (dataSelected.api) {
            content += '<div style="padding-bottom:10px;" class="col-lg-6 hide"><label>Cupo Asiento (API)</label><input readonly onkeypress="return numbersvalid(event)" class="cupo-asiento" type="text" placeholder="Asiento"></div>';
            content += '<div style="padding-bottom:10px;" class="col-lg-6 hide"><label>Cupo Alojamiento (API)</label><input readonly onkeypress="return numbersvalid(event)" class="cupo-alojamiento" type="text" placeholder="Impuesto"></div>';
        }

        content += '<div style="padding-bottom:10px;" class="col-lg-6"><label>Cupo Asiento</label><input onkeypress="return numbersvalid(event)" id="cupo' + ( date_apt + date_arr) + '" class="cupo" type="text" readonly placeholder="Cupo"></div>';
        // content += '<div class="col-lg-4"><label>Tarifa</label><input onkeypress="return numbersvalid(event)" class="precio" type="text" placeholder="Tarifa"></div>';
        content += '<div style="padding-bottom:10px;" class="col-lg-6"><label>Impuesto</label><input onkeypress="return numbersvalid2(event)" class="impuesto" type="text" placeholder="Impuesto"></div>';
        content +='<div class="panel panel-default" style="margin-bottom:10px !important;margin-top:64px !important;"><div class="panel-heading">Servicios</div><div class="panel-body">'
        content += '<div class="col-lg-4"><label>Transporte</label><input  id="transportess" class="transporte" type="text" placeholder="Transporte"></div>';        
        content += '<div class="col-lg-4"><label>Duración</label><input  class="duracion" type="text" placeholder="Duración"></div>';
        content += '<div class="col-lg-4"><label>Regimen</label><input class="regimen" type="text" placeholder="regimen"></div>';  
        content += '<div class="col-lg-4"><label>Asistencia</label><input  class="asistencia" type="text" placeholder="Asistencia"></div>';  
        content += '<div class="col-lg-4"><label>Coordinación</label><input  class="coordinacion" type="text" placeholder="Coordinación"></div>';  
        content += '</div></div></div>'
    }    
    content += '</div></div></div></div>';
    content += '<div class="form-group"><div class="row"><div class="col-lg-10 col-lg-offset-1"><div class="row">';
    if(typeof cupo!=='undefined'&&typeof impuesto!=='undefined'){
        content += '<div class="col-lg-12"><label>Descripción</label><textarea id="description'+ahora+'"rows="5">'+description+'</textarea></div>';
        id_textarea = 'description'+ahora;
    }else{
        content += '<div class="col-lg-12"><label>Descripción</label><textarea id="description'+(date_apt+date_arr+ahora)+'"rows="5"></textarea></div>';
        id_textarea = 'description'+(date_apt+date_arr+ahora);
    }
    content += '</div></div></div></div>';
     var origin = "";
    origin += '<div class="panel-group accordion-panels" id="" role="tabori" aria-multiselectable="">';
    origin += '<div class="panel panel-default panel-select-origin" style="margin-bottom:27px !important;">';
    origin += '<div class="panel-heading" data-toggle="collapse" role="tabori"><a role="button" data-toggle="collapse"  href="#accordion-select-origin-'+count_id_origin+'">Origenes</a></div>';
    origin += '<div id="accordion-select-origin-'+count_id_origin+'" class="panel-collapse collapse in" role="tabori"><div class="panel-body">';
    origin += '<div class="form-group"><label class="control-label col-lg-1 required">Origen</label><div class="col-lg-4">';

    //<div class="panel-body"><div class="form-group"><label class="control-label col-lg-1 required">Origen</label><div class="col-lg-4">
    //<div id="accordion-origin-'+count_id_origin+'" class="panel-collapse collapse '+class_collapse+'" role="tabpanel"><div class="panel-body">'

    origin += '<select class="origin-departure" id="origenes' +( date_apt + date_arr+ahora)+'" data-token="'+(date_apt+date_arr+ahora)+'">';
    $.each(origenes_json, function (i, val) {
        origin += '<option value="' + val.id_origen + '">' + val.origen + '</option>';
    });
    origin += ' </select></div><label class="control-label col-lg-1 required">Precio origen</label><div class="col-lg-2">';
    origin += '<div class="input-group"><input type="text" name="price_origin" id="price_origin' +( date_apt + date_arr+ahora)+'" data-token="" value="0" class="" onkeypress="return numbersvalid(event)" ><span class="input-group-addon">$</span></div></div><div class="col-lg-4">';
    origin += ' <a  data-token="' +( date_apt + date_arr+ahora)+'"  class="addorigin btn-primary btn">Agregar origen</a> ';
    origin += ' <a  data-token="' +( date_apt + date_arr+ahora)+'"  class="addallorigin btn-primary btn">Agregar todos</a> ';
    origin += '  </div></div><div class="col-lg-10"><table class="table"><thead><tr><th><span class="title_box ">Origen</span></th><th><span class="title_box ">Precio origen</span></th><th><span class="title_box ">Accion</span></th></tr></thead>';
    origin += '  <tbody class="tableorigins bodytableorigin' +( date_apt + date_arr+ahora)+'"  data-token="" data-destiny="">';
    if (typeof origins !== 'undefined') {
        $.each(origins, function (i, origi) {
            var id_origin_api = 0;
            if (dataSelected.api) {
                id_origin_api = origi.id_origin_api;
            }

            var contentbutton = '<div class="panel-heading-action"><a class="btn cancelarorigen" ><span class="label label-danger"><i class="icon-remove"></i></span></a></div>'
            origin += '<tr class="rowOrigin" data-api-id="'+ id_origin_api +'" data-name="' + origi.origen + '" data-id="' + origi.id_origin + '" data-price="' + origi.price + '"><td>' + origi.origen + '</td><td>' + origi.price + '</td><td>' + contentbutton + '</td></tr>'
        });
    }    
    origin += '</tbody>';
    origin += '  </table></div></div></div>';
    origin += '</div>';
    origin += '</div>';
    content += origin;
    
    
    content += '<div class="form-group"><div class="row"><div class="col-lg-10 col-lg-offset-1"><div class="row">';

    content += '<div class="col-lg-3"><label>Destino</label><select class="select-destiny-line"><option value="">--</option>';
    
    $.each(destinos_select, function(key, destiny){
        content += '<option value="'+destiny.id_destiny+'">'+destiny.destiny+'</option>';
    });

    content += '</select></div>';
    content += '<div class="col-lg-3"><label>Seleccione hotel</label><select class="select-hotel-line"><option value="">--</option></select></div>';

//    content += '<div class="col-lg-2"><label>Precio Destino</label><input onkeypress="return numbersvalid(event)" class="precio_destino" type="text" placeholder="Precio"></div>';
    
    content += '<div class="col-lg-2"><a style="margin-top:21px;" data-id="'+(date_apt+date_arr)+'" class="btn btn-primary btn-block btn-add-destiny-line">Agregar</a></div>';


    content += '</div></div></div></div>';

    content += '<div class="container-destiny-lines"></div>';

    content += '</div></div></div></div>';


    var accordion_active = container.find('.panel-collapse.collapse.in');
    if (accordion_active.length == 1) {
        accordion_active.removeClass('in');
    }

    container.prepend(content);    

    addTinymc(id_textarea);
//    console.info(description);

    count_id_origin++;
    if(typeof cupo!=='undefined'&&typeof impuesto!=='undefined'){
       var result={conta:container,ahora:ahora, container: container.find('.panel-line-origin').first()};
       return result;
    }    
}

function addDestinyAccordion(panel_origin, destiny_id, destiny_name, hotel_id, hotel_name, rooms,idLinea,linea, cupos_room, srv_alojamiento_id){
    var content = '',
        container = panel_origin.find(".container-destiny-lines");
    if (container.find('.accordion-destiny').length == 0) {
        var collapsible_parent = '<div class="panel-group accordion-panels accordion-destiny" id="accordion_destiny-'+count_id_accordion_destiny+'" data-id="'+count_id_accordion_destiny+'" data-linea="'+idLinea+'" role="tablist" aria-multiselectable="true"></div>';
        container.html(collapsible_parent);

        count_id_accordion_destiny++;
    }
//    var pricedes = panel_origin.find('.precio_destino').val();
//    console.info(pricedes);

    container = container.find('.accordion-destiny');
    var id_accordion_destiny = container.attr("data-id");

    content = '<div class="panel panel-default panel-line-destiny"><div class="panel-heading" role="tab"><a role="button" data-toggle="collapse" data-parent="#id_accordion_destiny-'+id_accordion_destiny+'" href="#accordion-origin-'+count_id_origin+'" aria-expanded="true">DESTINO '+destiny_name+' --- HOTEL: '+hotel_name+' </a><a href="#" class="btn-delete-collapse"><span class="label label-danger"><i class="icon-remove"></i></span></a></div><div id="accordion-origin-'+count_id_origin+'" class="panel-collapse collapse in" role="tabpanel"><div class="panel-body">';
    console.log('Cupo habitacion:', cupos_room);
    if (typeof cupos_room === 'undefined') cupos_room = 0;
    if (typeof srv_alojamiento_id === 'undefined') srv_alojamiento_id = 0;
    content += '<div class="col-lg-4"><label>Cupo Habitación</label><input value="' + parseInt(cupos_room) + '" class="cupos_room" type="text" readonly placeholder="Cantidad"></div>';
    content += '<input type="hidden" class="line_destiny_id" value="'+destiny_id+'">';
    content += '<input type="hidden" class="line_hotel_id" value="' + hotel_id + '">';
    content += '<input type="hidden" class="line_srv_alojamiento_id" value="' + srv_alojamiento_id + '">'; //srv_alojamiento_id

    content += '<table class="table"><thead><tr><th class="hide"><span class="title_box">Habitación</span></th><th><span class="title_box hide">N. huespedes</span></th><th><span class="title_box ">Observaciones</span></th><th><span class="title_box ">Tarifa adulto</span></th><th><span class="title_box">Tarifa niño</span></th><th><span class="title_box ">Tarifa bebe</span></th></tr></thead><tbody>'
    //console.log(rooms);
    $.each(rooms, function(key, room){
        if (room.id_room_prev) {
            content += '<tr class="rowRooms" data-room-prev="'+ room.id_room_prev +'" data-capacidad="' + room.cant + '" data-price-destino="" dat-linea="'+linea+'" data-linea="'+idLinea+'" data-canthues="' + room.cant + '" data-room-name="' + room.name + '" data-id="' + room.id_room + '"><th>' + room.name + '</th><th class="hide">' + room.cant + '</th><th>' + room.observations + '</th><th><input type="text" name="cant_reserv' + room.id_room + '" value="" class="price_room"  placeholder="Ingrese precio habitación" onkeypress="return numbersvalid(event)"></th><th><input class="price_ninos"type="text" name="price_ninos' + room.id_room + '" value="" placeholder="Ingrese precio niños" onkeypress="return numbersvalid(event)"/></th><th><input class="price_bebes"type="text" name="price_bebes' + room.id_room + ' " value="" placeholder="Ingrese precio bebes" onkeypress="return numbersvalid(event)"/></th></tr>';
        } else {
            if(typeof room.pricedestino !=='undefined'){
                content += '<tr class="rowRooms" data-capacidad="' + room.cant + '" data-price-destino="" dat-linea="'+linea+'" data-linea="'+idLinea+'" data-canthues="' + room.cant + '" data-room-name="' + room.name + '" data-id="' + room.id_room + '"><th>' + room.name + '</th><th class="hide">' + room.cant + '</th><th>' + room.observations + '</th><th><input type="text" name="cant_reserv' + room.id_room + '" value="" class="price_room"  placeholder="Ingrese precio habitación" onkeypress="return numbersvalid(event)"></th><th><input class="price_ninos"type="text" name="price_ninos' + room.id_room + '" value="" placeholder="Ingrese precio niños" onkeypress="return numbersvalid(event)"/></th><th><input class="price_bebes"type="text" name="price_bebes' + room.id_room + ' " value="" placeholder="Ingrese precio bebes" onkeypress="return numbersvalid(event)"/></th></tr>';
            }else{
                content += '<tr class="rowRooms" data-capacidad="' + room.cant + '" dat-linea="'+linea+'"  data-linea="'+idLinea+'" data-canthues="' + room.cant + '" data-room-name="' + room.name + '" data-id="' + room.id_room + '"><th>' + room.name + '</th><th class="hide">' + room.cant + '</th><th>' + room.observations + '</th><th><input type="text" name="cant_reserv' + room.id_room + '" value="" class="price_room"  placeholder="Ingrese precio habitación" onkeypress="return numbersvalid(event)"></th><th><input class="price_ninos"type="text" name="price_ninos' + room.id_room + '" value="" placeholder="Ingrese precio niños" onkeypress="return numbersvalid(event)"/></th><th><input class="price_bebes"type="text" name="price_bebes' + room.id_room + '" value="" placeholder="Ingrese precio bebes" onkeypress="return numbersvalid(event)"/></th></tr>';
            }
        }
    });

    content += '</tbody></table>';

    content += '</div></div></div></div>';
    
    var accordion_active = container.find('.panel-collapse.collapse.in');
    if (accordion_active.length == 1) {
        accordion_active.removeClass('in');
    }
//console.info(container);
    container.prepend(content);
    count_id_origin++;

    return container.find('.panel-line-destiny').first();
}

function addTinymc(textarea_id){
    console.log(textarea_id);
    if(!tinyMCE.get(textarea_id)) {
        tinymce.EditorManager.execCommand('mceAddEditor', true, textarea_id);
    }
}

function initTinymc(){
    tinymce.init({ selector:'textarea' });
}


// API

var urlAjax = '';
var contentPackageApi = '';
var contentDepartureApi = '';
var formPackageApi = '';
var formDepartureApi = '';
var formQuotaApi = '';
var assignDefault = false;

var dataSelected = {
    api: false,
    departures: [],
};

var dataLoad = {
    destinations: [],
    packages: []
};

var isValidApi = false;

$(document).ready(function () {
    contentPackageApi = $('#content-package-api');
    contentDepartureApi = $('#content-api-departures');
    urlAjax = $('#form-url-ajax').val();
    formPackageApi = $('#form-package-api');
    formDepartureApi = $('#form-departure-api');
    formQuotaApi = $('#form-package-quota');

    $('#form-connect-api').change(function() {
        var input = $(this),
            connectApi = input.val();

        if (connectApi === 'true' || connectApi === true) {
            clearDestinations();
            showApi();
        } else {
            clearDestinations();
            hideApi();
        }
    });

    formPackageApi.change(function() {
        if (!assignDefault) {
            clearDestinations();
        }

        contentDepartureApi.fadeIn();
        getApiDepartures(formPackageApi.val());
    });

    formDepartureApi.change(function() {

        let rowsSeats = $('.bodytablebutaca tr').length;

        if(rowsSeats < 1){

            alert('Agregue butacas al paquete')
            addSelectDepatures();
            return;
        }


        var $departure = $(this),
            departure = $departure.val(),
            option = $departure.find('option[value="'+departure+'"]'),
            departureSelect = option.attr('data-departure'),
            arrivalSelect = option.attr('data-arrival');

        console.log(departureSelect)
        console.log(arrivalSelect)

        $('#date-departure').val(departureSelect);

        var dateArrival = isNaN(arrivalSelect) && arrivalSelect != '' ? arrivalSelect : departureSelect;

        $('#date-arrival').val(dateArrival);

    });

    if (init_api === 1) {
        assignDefault = true;
        showApi();
    } else {
        contentPackageApi.fadeOut();
        contentDepartureApi.fadeOut();
        setDataDefault();
    }

});

function showApi() {
    isValidApi = false;
    dataSelected.api = true;
    contentPackageApi.fadeIn();

    formQuotaApi.attr('required', 'required');
    formPackageApi.attr('required', 'required');

    getApiPackages();

    setTimeout(function() {
        validateFinishPackageApi();
    }, 8000);
}

function validateFinishPackageApi() {
    if (!isValidApi) {
        var containerError = $('#package-api-alert');
        textError = 'No se pudo obtener los paquetes';
    
        containerError.removeClass('alert-info alert-danger')
            .addClass('alert alert-danger')
            .html(textError)
            .show();
    }
}

function hideApi() {
    dataSelected.api = false;
    contentPackageApi.fadeOut();
    contentDepartureApi.fadeOut();
    formQuotaApi.removeAttr('required');
    formPackageApi.removeAttr('required');
}

function clearDestinations() {
    var buttons = $('#container-list-lines .btn-delete-collapse');

    buttons.each(function(ket, button) {
        var $button = $(button);
        $button.trigger('click');
    });
}


function getApiPackages() {
    var containerError = $('#package-api-alert');
    var textError = 'Espere un momento, buscando paquetes...';
    
    containerError.removeClass('alert-info alert-danger')
        .addClass('alert alert-info')
        .html(textError)
        .show();

    $.ajax({
        method: 'post',
        dataType: 'json',
        url: urlAjax,
        data: {
            ajax: 1,
            action: 'getApiPackages',
        },
        success: function (packages) {
            isValidApi = true;
            containerError.fadeOut(100);

            var content = '<option disabled selected>Paquete (API)</option>';
            $.each(packages, function(key, package){
                if (package.Titulo) {
                    content += '<option value="'+package.ProductoID+'">'+package.Titulo+'</option>';
                }
            });

            formPackageApi.empty();
            formPackageApi.append(content);
            formPackageApi.trigger("chosen:updated");

            dataLoad.packages = packages;

            if (assignDefault) {
                setPackageDefault();
            }
        },
        error: function () {
            isValidApi = true;

            textError = 'No se pudo obtener los paquetes';
            containerError.removeClass('alert-info')
                .addClass('alert alert-danger')
                .html(textError)
                .show()
                .fadeOut(5000)
        }
    });
}

function getPackageSelected(packageId) {
    var packageSelected;

    $.each(dataLoad.packages, function (key, currentPackage) {
        if (currentPackage.ProductoID == packageId) {
            packageSelected = currentPackage;
        }
    });

    return packageSelected;
}

function setPackageDefault() {
    formPackageApi.val(package_api_select);
    formPackageApi.trigger("chosen:updated");
    formPackageApi.trigger("change");
}

function getApiDepartures(packageId) {
    $.ajax({
        method: 'post',
        dataType: 'json',
        url: urlAjax,
        data: {
            ajax: 1,
            action: 'getApiDepartures',
            id_package: packageId
        },
        success: function (departures) {
            dataSelected.departures = departures;
            addSelectDepatures();
        }
    });
}

function addSelectDepatures() {
    var content = '<option disabled selected>Salida (API)</option>';
    $.each(dataSelected.departures, function(key, departure){
        var d = new Date(departure.Fecha);
        var dateString = new Date(d.getTime() - (d.getTimezoneOffset() * 60000 )).toISOString().split("T")[0];
        var title_parts = departure.Titulo.split(' ');
        var days = title_parts[0];
        var date_arrival = '';

        if (!isNaN(days)) {
            date_arrival = addDays(dateString, parseInt(days) + 1);
        }

        content += '<option data-departure="'+dateString+'" data-arrival="'+date_arrival+'" value="'+departure.SalidaID+'">' + departure.Titulo + ' - ' + dateString + '</option>';
    });

    formDepartureApi.empty();
    formDepartureApi.append(content);
    formDepartureApi.trigger("chosen:updated");

    if (init_api === 1 && assignDefault == true) {
        setDataDefault();
    }
}

function addDays(date, days) {
    var result = new Date(date);
    result.setDate(result.getDate() + days);

    return result.getFullYear() + "-" + ("0" + (result.getMonth() + 1)).slice(-2) + "-" + ("0" + result.getDate()).slice(-2);
}

function validateDepartureDetails(departureId, date_departure, date_arrival) {
    $.ajax({
        method: 'post',
        dataType: 'json',
        url: urlAjax,
        data: {
            ajax: 1,
            action: 'getApiDepartureDetails',
            departure_id: departureId
        },
        success: function (departure_details) {
            if (typeof departure_details === 'string') {
                alert('La salida seleccionada no se encuentra disponible');
                return;
            }

            addOriginAccordion(false, date_departure, date_arrival, true);
            getApiDepartureDetails(formDepartureApi.val());
        }
    });
}

function getHotelsDestination(select) {
    var destiny = select.val(),
        containerParent = select.parent().parent(),
        selectHotel = containerParent.find('.select-hotel-line'),
        content = '<option value="">--</option>',
        keyDestination = select.attr('data-key');

    var destinations = dataLoad.destinations[keyDestination],
        accommodations = [];

    $.each(destinations, function(key, destination) {
        if (destination.id == destiny) {
            accommodations = destination.accommodations;
        }
    });

    $.each(accommodations, function(key, accommodation){
        content += '<option value="'+accommodation.AlojamientoID+'">'+accommodation.NombreAlojamiento+'</option>';
    });

    selectHotel.find('option').remove();
    selectHotel.append(content);
}

function getApiDepartureDetails(departureId, linea = null, result = null) {
    $.ajax({
        method: 'post',
        dataType: 'json',
        url: urlAjax,
        data: {
            ajax: 1,
            action: 'getApiDepartureDetails',
            departure_id: departureId
        },
        success: function (departureDetails) {

            var destinations = [];
            var content = '<option disabled selected>Destino</option>';
            var cupo = departureDetails.CupoTransporte;
            var total_cupos = 0;

            var transporte = departureDetails.Transportes[0];

            $.each(departureDetails.Alojamientos, function(key, alojamiento) {
                var issetDestination = false,
                    keyDestination = 0;

                $.each(destinations, function(key2, destination) {
                    if (alojamiento.LocalidadID == destination.id) {
                        issetDestination = true;
                        keyDestination = key2;
                    }
                });

                if (issetDestination) {
                    destinations[keyDestination].accommodations.push(alojamiento);
                } else {
                    destinations.push({
                        id: alojamiento.LocalidadID,
                        name: alojamiento.Localidad,
                        accommodations: [alojamiento]
                    });
                }
            });

            $.each(destinations, function(key, destination) {
                content += '<option value="' + destination.id + '">' + destination.name + '</option>';
            });

            $.each(departureDetails.Transportes, function(key, transport){
                total_cupos +=  transport.Cupo;
            });

            var keySelect = dataLoad.destinations.length;
            dataLoad.destinations.push(destinations);

            var container = $('.panel-line-origin').first();
            if (result) {
                container = result.container;
            }

            var select = container.find('.select-destiny-line'),
                selectHotel = container.find('.select-hotel-line'),
                inputCupo = container.find('.cupo'),
                inputCupoAsiento = container.find('.cupo-asiento'),
                inputCupoAlojamiento = container.find('.cupo-alojamiento');

            select.attr('data-key', keySelect);
            selectHotel.attr('data-key', keySelect);

            container.attr('data-departure', departureId);

            container.attr('data-tipo_servicio-id', transporte.SRV_TransporteID);
            //container.attr('data-tipo-butaca-id', transporte.TipoButacaID);
            container.attr('data-tipo-cupo-id', transporte.TipoCupoID);
            container.attr('data-transporte-name', transporte.Transporte);
            container.attr('data-transporte-id', transporte.TransporteID);

            var options = select.find('option'),
                optionsHotel = selectHotel.find('option');

            options.remove();
            optionsHotel.remove();

            select.append(content);

            inputCupoAsiento.val(departureDetails.CupoTransporte);

            inputCupoAlojamiento.val(departureDetails.CupoAlojamiento);

            //inputCupo.val(departureDetails.CupoTransporte);
            inputCupo.val(total_cupos);

            if (init_api === 1 && assignDefault && linea) {
                loadLine(linea, result);
            } else {
                // inputCupo.val(cupo);
            }

            getApiOriginsDeparture(departureDetails, container);
        }
    });
}

function getApiOriginsDeparture(departureDetails, container) {
    var selectOrigins = [];

    $.each(departureDetails.Transportes, function(key, transporte) {
        $.each(transporte.Origenes, function (key2, origin) {
            var issetOrigin = false;

            $.each(selectOrigins, function (key3, selectOrigin) {
               // if (selectOrigin.id === origin.ProvinciaID) {
                if (selectOrigin.id === origin.OrigenID) {
                    issetOrigin = true;
                }
            });

            if (!issetOrigin) {
                selectOrigins.push({
                    id: origin.OrigenID,
                    name: origin.Localidad + " - " + origin.Provincia + " - " + origin.Pais,
                    provincia_id: origin.ProvinciaID,
                    provincia_name: origin.Provincia
                });
            }
        });
    });

    var content = '';

    $.each(selectOrigins, function (key, origin) {
        content += '<option data-provincia-id="' + origin.provincia_id + '" data-provincia="' + origin.provincia_name + '" data-name="' + origin.name + '" value="' + origin.id + '">' + origin.name + '</option>';
    });

    var $selectOrigins = container.find('.origin-departure'),
        options = $selectOrigins.find('option');

    options.remove();
    $selectOrigins.append(content);

    if (init_api === 1 && assignDefault) {
        setOriginsDefault(container, selectOrigins);
    }
}

function setOriginsDefault(container, origins) {
    var table = container.find('.tableorigins'),
        rows = table.find('.rowOrigin');

    rows.each(function (key, rowTable) {
        var $rowTable = $(rowTable),
            apiOriginId = $rowTable.attr('data-api-id');
        var selectOrigin = null;

        $.each(origins, function(key2, origin) {
            if (apiOriginId == origin.id) {
                selectOrigin = origin;
            }
        });

        $rowTable.attr('data-id', selectOrigin.id);
        $rowTable.attr('data-provincia', selectOrigin.provincia_name);
        $rowTable.attr('data-provincia-id', selectOrigin.provincia_id);
    });
}

function getRoomsHotels(panelOrigin) {
    var $destiny = panelOrigin.find('.select-destiny-line'),
        destiny_id = $destiny.val(),
        $hotel = panelOrigin.find('.select-hotel-line'),
        hotel_id = $hotel.val();

    var destiny_name = $destiny.find("option[value='"+destiny_id+"']").html(),
        hotel_name = $hotel.find("option[value='"+hotel_id+"']").html();
    var idLinea = $(this).attr('data-id');

    var rooms = [];

    var keyDestination = $destiny.attr('data-key'),
        destinations = dataLoad.destinations[keyDestination],
        accommodation,
        destinationSelect;


    $.each(destinations, function(key, destination) {
        if (destination.id == destiny_id) {
            destinationSelect = destination;
            var accommodations = destination.accommodations;

            $.each(accommodations, function(key2, accommodationSelect) {
                if (accommodationSelect.AlojamientoID == hotel_id) {
                    accommodation = accommodationSelect;
                }
            });
        }
    });

    $.each(accommodation.Habitaciones, function(key, dataRoom) {
        var room = {
            cant: dataRoom.Capacidad,
            id_room: dataRoom.TipoHabitacionID,
            name: dataRoom.CategoriaHabitacion + " " + dataRoom.TipoHabitacion,
            observations: '',
            categoria_habitacion_id: dataRoom.CategoriaHabitacionID,
            tipo_habitacion_id: dataRoom.TipoHabitacionID,
            regimen_id: dataRoom.RegimenID,
            new_room: true
        };

        rooms.push(room);
    });

    var container = addDestinyAccordion(panelOrigin, destiny_id, destiny_name, hotel_id, hotel_name, rooms,idLinea, 0);

    container.find('.cupos_room').last().val(accommodation.Cupo);
    container.find('.line_srv_alojamiento_id').last().val(accommodation.SRV_AlojamientoID);

    var dataRooms = {
        hotel: {
            id: hotel_id,
            name: hotel_name
        },
        rooms: rooms,
        destination: {
            id: destinationSelect.id,
            name: destinationSelect.name
        }
    };

    addRoomsAttributes(container, dataRooms);
}

function addRoomsAttributes(panelOrigin, dataRooms) {
    var $rooms = panelOrigin.find('.rowRooms');
    
    $rooms.each(function(key, roomContent) {
        var $room = $(roomContent),
            rooms = dataRooms.rooms,
            currentRoom = rooms[key];

        $room.attr('data-destination-id', dataRooms.destination.id);
        $room.attr('data-destination-name', dataRooms.destination.name);
        $room.attr('data-hotel-name', dataRooms.hotel.name);
        $room.attr('data-hotel-id', dataRooms.hotel.id);

        if(rooms.new_room) {
            $room.attr('data-categoria-habitacion-id', currentRoom.categoria_habitacion_id);
            $room.attr('data-tipo-habitacion-id', currentRoom.tipo_habitacion_id);
            $room.attr('data-regimen-id', currentRoom.regimen_id);
        }
    });
}

function setDataDefault() {
    var Lineas = JSON.parse(LineasPackage);

    $.each(Lineas, function (key, linea) {

        var result = addOriginAccordion(linea.idpackagelinea, linea.datesal, linea.datelle, false,linea.inventario,linea.price,linea.impuesto,linea.description,linea.origins,linea.services);

        if (init_api === 1 && assignDefault) {
            getApiDepartureDetails(linea.departure_id, linea, result);
        } else {
            loadLine(linea, result);
        }
    });
}

function loadLine(linea, result) {
    $.each(linea.hotels,function(key2,hotel){
        var panelOrigin = result.container;

        var $destiny = panelOrigin.find('.select-destiny-line'),
            destiny_id = parseInt(hotel.id_destination_api),
            $hotel = panelOrigin.find('.select-hotel-line'),
            hotel_id = hotel.id_hotel_api;

        var destiny_name = $destiny.find("option[value='"+destiny_id+"']").html(),
            hotel_name = $hotel.find("option[value='"+hotel_id+"']").html();
        var idLinea = $(this).attr('data-id');

        var rooms = [];

        if (init_api === 1 && assignDefault) {
            var keyDestination = $destiny.attr('data-key'),
                destinations = dataLoad.destinations[keyDestination],
                accommodation,
                destinationSelect;

            $.each(destinations, function(key, destination) {
                if (destination.id == destiny_id) {
                    destinationSelect = destination;
                    var accommodations = destination.accommodations;

                    $.each(accommodations, function(key2, accommodationSelect) {
                        if (accommodationSelect.AlojamientoID == hotel_id) {
                            accommodation = accommodationSelect;
                        }
                    });
                }
            });

            $.each(accommodation.Habitaciones, function(key, dataRoom) {
                var room = {
                    cant: dataRoom.Capacidad,
                    id_room: dataRoom.TipoHabitacionID,
                    name: dataRoom.CategoriaHabitacion + " " + dataRoom.TipoHabitacion,
                    observations: '',
                    categoria_habitacion_id: dataRoom.CategoriaHabitacionID,
                    tipo_habitacion_id: dataRoom.TipoHabitacionID,
                    regimen_id: dataRoom.RegimenID,
                    new_room: false
                };

                var issetRoom = null;

                $.each(hotel.rooms, function(key2, hotel_room) {
                    if (hotel_room.id_room_api == dataRoom.TipoHabitacionID) {
                        issetRoom = hotel_room;
                    }
                });

                if (issetRoom) {
                    room.id_room_prev = issetRoom.id_room;
                }

                rooms.push(room);
            });
            var container = addDestinyAccordion(panelOrigin, destiny_id, hotel.destinyname, hotel_id, hotel.namehotel, rooms, result.ahora,linea.idpackagelinea, hotel.rooms[0].cant,hotel.srv_alojamiento_id)
        } else {
            var container = addDestinyAccordion(panelOrigin, destiny_id, hotel.destinyname, hotel_id, hotel.namehotel, hotel.rooms,result.ahora,linea.idpackagelinea, hotel.rooms[0].cant, hotel.srv_alojamiento_id)
        }
        
        if (init_api === 1 && assignDefault) {

            var dataRooms = {
                hotel: {
                    id: hotel_id,
                    name: hotel.namehotel
                },
                rooms: rooms,
                destination: {
                    id: destinationSelect.id,
                    name: destinationSelect.name
                }
            };

            addRoomsAttributes(container, dataRooms);
        }
    });
    
    cargeCants();
}