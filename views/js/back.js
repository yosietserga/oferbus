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


var token = Date.now();
var file='';
var arrayDestinos=[];
var arrayDestinosExport=[];
var arrayHotels=[];
var action='';
$(document).ready(function(){        
    var room = $('#panelfotos').attr('data-room');
    if (room == '') {
        var des = $("#id_destiny option:selected").val();
        $.ajax({
            method: 'post',
            dataType: 'json',
            url: $('#panelfotos').attr('data-url'),
            data: {
                ajax: 1,
                id_destiny: des,
                action: 'getHotelsByDestiny',
            },
            success: function (response) {
                console.info(response);
                $("#id_hotel").html('');
                $.each(response, function (key, value) {
                    $("#id_hotel").append("<option value=" + value.id_hotel + ">" + value.name + "</option>");
                });
            }
        });
    }else{
        var hotel = $('#id_hotel').val();
        
        $.ajax({
            method: 'post',
            dataType: 'json',
            url: $('#panelfotos').attr('data-url'),
            data: {
                ajax: 1,
                id_hotel:hotel ,
                action: 'getDestinyByHotel',
            },
            success: function (response) {                
                $("#id_destiny option").each(function () {
                    if($(this).attr('value')==response.id_destiny){
                        $(this).attr('selected','selected');
                    }
                });
                $.ajax({
                    method: 'post',
                    dataType: 'json',
                    url: $('#panelfotos').attr('data-url'),
                    data: {
                        ajax: 1,
                        id_destiny: response.id_destiny,
                        action: 'getHotelsByDestiny',
                    },
                    success: function (response2) {
                        $("#id_hotel").html('');
                        $.each(response2, function (key, value) {
                            $("#id_hotel").append("<option value=" + value.id_hotel + ">" + value.name + "</option>");
                        });
                        $("#id_hotel option").each(function () {
                            if ($(this).attr('value') == hotel) {
                                $(this).attr('selected', 'selected');
                            }
                        });
                    }
                });
            }
        });
    }    
    $('#id_destiny').on('change', function () {
        var id_destiny=$(this).val();
        $.ajax({
            method: 'post',
            dataType: 'json',
            url: $('#panelfotos').attr('data-url'),
            data: {
                ajax: 1,
                id_destiny: id_destiny,
                action: 'getHotelsByDestiny',
            },
            success: function (response) {
                 $("#id_hotel").html('');
                $.each(response, function (key, value) {
                    $("#id_hotel").append("<option value=" + value.id_hotel + ">" + value.name + "</option>");
                });
            }
        });
    });
    $("#inv_rooms_form").append('<input type="hidden" name="tok" value="'+token+'">');
    var html=$('#total').html();
    $('#total').remove();
    $('#inv_rooms_form .form-wrapper').append('<div class="form-group">' + html + '</div>');
    $("#inv_rooms_form").validate({
        rules: {
            name: "required",
            cant: "required",
            price: "required",
        },
        messages: {
            name: "Debe escribir un nombre",
            cant: "Debe escribir la cantidad de personas",
            price: "Debe escribir un precio ",
        }
    });   

    $('.bodytablephotos ').on('click', '.removephotoroom', function () {
        var name = $(this).attr('data-name');
        var tt = $(this);
        var roomPhoto = $(this).attr('data-roomphoto');
        var room = $(this).attr('data-room');
        if (confirm('La imagen se eliminara de forma permanente. ¿Desea continuar?')) {
            $.ajax({
                method: 'post',
                dataType: 'json',
                url: $('#panelfotos').attr('data-url'),
                data: {
                    ajax: 1,
                    name: name,
                    roomphoto: roomPhoto,
                    room: room,
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
    $('#image').on('change',function (e) {  
        var room=$('#panelfotos').attr('data-room');
        var tt=this;
        formdata = new FormData();   
        var file = this.files[0];
        if (formdata) {
            formdata.append("image", file);
            formdata.append("ajax", 1);
            formdata.append("tok", token);
            formdata.append("room", room);
            formdata.append("action", 'uploadPhoto');
            $('#loading').show();
            jQuery.ajax({
                url: $('#panelfotos').attr('data-url'),
                type: "POST",
                data: formdata,
                processData: false,
                contentType: false,
                success:function(response){
                    $('#loading').hide();
                    if(response!==''){
                        addImageRoom(tt,response);
                    }else{
                      alert('La imagen no pudo ser cargada');  
                    }                    
                }
            });
        }    
    });
    $('#image-selectbutton').click(function (e) {
        $('#image').trigger('click');
    });

    $('#image-name').click(function (e) {
        $('#image').trigger('click');
    });

    $('#image-name').on('dragenter', function (e) {
        e.stopPropagation();
        e.preventDefault();
    });

    $('#image-name').on('dragover', function (e) {
        e.stopPropagation();
        e.preventDefault();
    });

    $('#image-name').on('drop', function (e) {
        e.preventDefault();
        var files = e.originalEvent.dataTransfer.files;
        $('#image')[0].files = files;
        $(this).val(files[0].name);
    });

    $('#image').change(function (e) {
        if ($(this)[0].files !== undefined)
        {
            var files = $(this)[0].files;
            var name = '';

            $.each(files, function (index, value) {
                name += value.name + ', ';
            });

            $('#image-name').val(name.slice(0, -2));
        } else // Internet Explorer 9 Compatibility
        {
            var name = $(this).val().split(/[\\/]/);
            $('#image-name').val(name[name.length - 1]);
        }
    });
    
    $('#image').closest('form').on('submit', function (e) {
        if ($('#image')[0].files.length > 8) {
            e.preventDefault();
            alert('You can upload a maximum of  files');
        }
    }); 
    $('#cant').on('input', function () {
        this.value = this.value.replace(/[^0-9]/g, '');
    });
    $('#price').on('input', function () {
        this.value = this.value.replace(/[^0-9]/g, '');
    });
});

function addImageRoom(input,name) {    
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function (e) {
            var content='';
            var contentbutton="<div class='panel-heading-action'><a class='btn removephotoroom' data-name="+name+" ><span class='label label-danger'><i class='icon-remove'></i></span></a></div>"
            content+="<tr><td style='text-align:center;'><img src='"+e.target.result+"' style='width:100px;'/></td><td>"+input.files[0].name+"</td><td>"+contentbutton+"</td></tr>"
            $('.bodytablephotos').append(content);
        }
        reader.readAsDataURL(input.files[0]);
    }
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



