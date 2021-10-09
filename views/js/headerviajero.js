/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

$(document).ready(function () {   
    if (screen.width >= 770) {
        $('.user_info').css('display', 'block');
        $('.user_info').css('position', 'relative');
        $('.user_info').css('float', 'right');
        $('.user_info').css('margin-bottom', '10px');
//        $('.user_info a').css('background', 'transparent');
        $('#tmsearch').prepend($('.user_info'));
        $('#tmsearch').css('margin-top', '10px');
    }    
    
    if(typeof($('.order_carrier_content')) !=='undefined'){
        $('.order_carrier_content p.alert').css('display','none');
        $('#carrier_area>h1').html('<span>2</span> Condiciones De Venta');
    }
});
