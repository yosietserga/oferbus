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
$(document).ready(function () {

    $('#description_ifr').contents().find('body').html('<div> blah </div>');

    $("#inv_hotels_form").append('<input type="hidden" name="tok" value="' + token + '">');
    var html = $('#total').html();
    $('#total').remove();
    $('#inv_hotels_form .form-wrapper').append('<div class="form-group">' + html + '</div>');
    $("#inv_hotels_form").validate({
        rules: {
            name: "required",
            latitud: "required",
            longitud: "required",
            stars: "required",
        },
        messages: {
            name: "Debe escribir un nombre",
            latitud: "Debe ingresar coordena latitud ",
            longitud: "Debe ingresar coordena longitud ",
            stars: "Ingrese la calificación en estrellas del hotel",
        }
    });
    $('.bodytablephotos ').on('click', '.removephotoroom', function () {
        var name = $(this).attr('data-name');
        var tt = $(this);
        var hotelPhoto = $(this).attr('data-hotelphoto');
        var hotel = $(this).attr('data-hotel');
        if (confirm('La imagen se eliminara de forma permanente. ¿Desea continuar?')) {
            $.ajax({
                method: 'post',
                dataType: 'json',
                url: $('#panelfotos').attr('data-url'),
                data: {
                    ajax: 1,
                    name: name,
                    hotelphoto: hotelPhoto,
                    hotel: hotel,
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
    $('#stars').on('input', function () {
        this.value = this.value.replace(/[^0-6]/g, '');
    });
    $('#image').on('change', function (e) {
        var hotel = $('#panelfotos').attr('data-hotel');
        var tt = this;
        formdata = new FormData();
        var file = this.files[0];
        if (formdata) {
            formdata.append("image", file);
            formdata.append("ajax", 1);
            formdata.append("tok", token);
            formdata.append("hotel", hotel);
            formdata.append("action", 'uploadPhoto');
            $('#loading').show();
            jQuery.ajax({
                url: $('#panelfotos').attr('data-url'),
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



});
function addImageRoom(input, name) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function (e) {
            var content = '';
            var contentbutton = "<div class='panel-heading-action'><a class='btn removephotoroom' data-name=" + name + " ><span class='label label-danger'><i class='icon-remove'></i></span></a></div>"
            content += "<tr><td style='text-align:center;'><img src='" + e.target.result + "' style='width:100px;'/></td><td>" + input.files[0].name + "</td><td>" + contentbutton + "</td></tr>"
            $('.bodytablephotos').append(content);
        }
        reader.readAsDataURL(input.files[0]);
    }
}
