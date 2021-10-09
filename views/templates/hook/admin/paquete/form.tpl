{*
* 2007-2018 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
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
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}

<style>
    #ui-datepicker-div{
        z-index:2 !important;
    }
</style>
<style>
    .error{
        color:red !important;
    }
</style>

<script type="text/javascript">
    var init_api = 0;
    SOLD = {$sold}
    hoteles = `{$Hoteles|escape:'htmlall':'UTF-8'}`;
   {* RoomsPackage = `{$RoomsPackage}`;*}
    DestinyPackage =  `{$DestinyPackage|regex_replace:"/\\\/":"\\\\\\"|replace:"`":"'"}`;
    LineasPackage =  `{$LineasPackage|regex_replace:"/\\\/":"\\\\\\"|replace:"`":"'"}`;
    {*HotelsPackage = `{$HotelsPackage}`;*}
    idpackage = `{$package->id_package|regex_replace:"/\\\/":"\\\\\\"|replace:"`":"'"}`;
    destinos_select = JSON.parse(`{$Destinos_json|regex_replace:"/\\\/":"\\\\\\"|replace:"`":"'"}`);
    origenes_json = JSON.parse(`{$Origenes_json|regex_replace:"/\\\/":"\\\\\\"|replace:"`":"'"}`);




    {if $package && $package->api}
        init_api = {$package->api};
        package_api_select = {$package->id_package_api};
    {/if}
    console.log(init_api);
</script>
<script src="https://cloud.tinymce.com/stable/tinymce.min.js?apiKey=3o2vrekcmiqogaoadfyqvdjr5idn6n5qtcegbgs16qpx2ojn"></script>

<input type="hidden" id="form-url-ajax" value="{$urlajax}">
<form id="inv_packages_form" class="defaultForm form-horizontal AdminViajeroPaquete" novalidate="">
    <div class="bootstrap" id="alertdone" style="display:none;">
        <div class="alert alert-success">
            {if $package}
                Su Paquete se ha actualizado satisfactoriamente, En un momento sera redigirigido ....
            {else if}
                Su Paquete se ha creado satisfactoriamente, En un momento sera redigirigido ....
            {/if}            
        </div>
    </div>
    <div class="panel" id="fieldset_0">
        <div class="panel-heading">
            <i class="icon-folder-close"></i>Paquete
        </div>
        <div class="form-wrapper">
            <div class="form-group">
                <label class="control-label col-lg-3 required">
                    Nombre
                </label>
                <div class="col-lg-4">
                    <input type="text" name="Name" id="Name" value="{$package->name}" class="" required="required">
                </div>
            </div>

           <div class="form-group">
                <label class="control-label col-lg-3 required">
                    Categoria
                </label>   
                <div class="col-lg-3">
                <select id="category" name="categorias[]" data-placeholder="Categorias" class="chosen-select" multiple tabindex="4">
                        {foreach from=$categories item=category name=myLoop}
                            {if $category['id_category'] ne 1}
                                <option value="{$category['id_category']}" {if $category['selected'] == true || $category['id_category'] == 2}{'selected'}{/if} >{$category['name']}</option>
                            {/if}
                        {/foreach}
                    </select>                                
                </div>                
           </div>

            <div class="form-group">
                <label class="control-label col-lg-3 required">
                    Facturar sin cupo 
                </label>
                <div class="col-lg-1">
                    <select id="disponibilidad">                         
                            <option value="No" {if $package->disponibilidad == 'No'}selected='selected'{/if}>No</option>                        
                            <option value="Si" {if $package->disponibilidad == 'Si'}selected='selected'{/if}>Si</option>                        
                    </select>                                
                </div>
            </div>  
            <div class="form-group">
                <div class="row">
                    <label class="control-label col-lg-3 required">
                        Conectar con API
                    </label>
                    <div class="col-lg-1">
                        <select id="form-connect-api">
                            <option {if $package->api == 0 } selected='selected'{/if} value="false">No</option>
                            <option {if $package->api == 1 } selected='selected'{/if} value="true">Si</option>
                        </select>
                    </div>
                    <div class="col-xs-12">
                        <div class="row mt-1">
                            <span class="col-md-9 col-md-offset-3"><b>Nota:</b> Si se cambia el valor, automaticamente se eliminaran las salidas que ya estén creadas</span>
                        </div>
                    </div>
                </div>
            </div>  
            <div id="content-package-api">
                <div class="form-group">
                    <div class="col-xs-12">
                        <div class="row mt-1">
                            <div id="package-api-alert"></div>
                        </div>
                    </div>
                    <label class="control-label col-lg-3 required">
                        Paquete (API)
                    </label>
                    <div class="col-lg-3">
                        <select id="form-package-api" class="chosen-select" data-placeholder="Paquete (API)">
                        </select>
                    </div>
                    <div class="col-xs-12">
                        <div class="row mt-1">
                            <span class="col-md-9 col-md-offset-3"><b>Nota:</b> Si se cambia el valor, automaticamente se eliminaran las salidas que ya estén creadas</span>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label class="control-label col-lg-3 required">
                        Minimo de cupos
                    </label>
                    <div class="col-lg-3">
                        <input type="text" id="form-package-quota" value="{$package->quota_api}" onkeypress="return numbersvalid(event)">
                    </div>
                </div>  
            </div>  
            <div class="form-group">
                <label class="control-label col-lg-3">
                    Detalles
                </label>
                <div class="col-lg-9">
                    <textarea name='detalles' id="detalles" col='8' row='8'>
                        {$package->detalles}
                    </textarea>                             
                </div>
            </div> 
            <div class="form-group">
                <label class="control-label col-lg-3">
                    Legales
                </label>
                <div class="col-lg-9">
                    <textarea name='legales' id="legales" col='8' row='8'>
                        {$package->legales}
                    </textarea>                             
                </div>
            </div>   
             <div class="form-group">
                 <label class="control-label col-lg-3 required">
                     Limite Edad Niños 
                 </label>               
                 <div class="col-lg-2">
                     <input type="text" name="edadninos" id="edadninos" value="{$package->edadninos}" class=""  onkeypress="return numbersvalid(event)" >
                 </div>                
                 {* <label class="control-label col-lg-2 required">Porcentaje a pagar</label> *}
                 <div class="col-lg-2">
                     <div class="input-group">
                        {if $package}
                        <input type="hidden" name="valueninos" id="valueninos" maxlength="3"  value="100" class=""  onkeypress="return numbersvalid(event)" ><span class="hide input-group-addon">%</span>
                        {else}
                        <input type="hidden" name="valueninos" id="valueninos" maxlength="3"  value="100" class=""  onkeypress="return numbersvalid(event)" ><span class="hide input-group-addon">%</span>
                        {/if}
                    </div>
                 </div>                
            </div>           
             <div class="form-group">
                 <label class="control-label col-lg-3 required">
                     Limite Edad Bebes 
                 </label>               
                 <div class="col-lg-2">
                     <input type="text" name="edadbebes" id="edadbebes" value="{$package->edadbebes}" class=""  onkeypress="return numbersvalid(event)" >
                 </div>                
                 {* <label class="control-label col-lg-2 required">Porcentaje a pagar</label>*}
                 <div class="col-lg-2">
                    <div class="input-group">
                    {if $package}
                        <input type="hidden" name="valuebebes" id="valuebebes" maxlength="3"  value="100" class=""  onkeypress="return numbersvalid(event)" ><span class="hide input-group-addon">%</span>
                    {else}
                        <input type="hidden" name="valuebebes" id="valuebebes" maxlength="3"  value="100" class=""  onkeypress="return numbersvalid(event)" ><span class="hide input-group-addon">%</span>
                    {/if}
                    </div>
                 </div>                
            </div> 
            <div class="form-group">
                <label class="control-label col-lg-3 required">
                    Precio Referencia
                </label>
                <div class="col-lg-2">
                    <div class="input-group">
                        <input type="text" name="pricereference" id="pricereference" value="{$package->pricereference}" onkeypress="return numbersvalid(event)" class="" required="required">
                        <span class="input-group-addon">$</span>
                    </div>                    
                </div>
            </div>             
            <div class="form-group">
                <label class="control-label col-lg-3">
                    Fotos paquete
                </label>
                <div class="col-lg-9">
                    <div class="form-group">
                        <div class="col-sm-6">
                            <input id="photo" type="file" name="photo" class="hide">
                            <div class="dummyfile input-group">
                                <span class="input-group-addon"><i class="icon-file"></i></span>
                                <input id="photo-name" type="text" name="filename" readonly="" value='{$package->photo}'>
                                <span class="input-group-btn">
                                    <button id="photo-selectbutton" type="button" name="submitAddAttachments" class="btn btn-default">
                                        <i class="icon-folder-open"></i> Añadir archivo				</button>
                                </span>
                            </div>
                        </div>
                                 <div class="col-lg-10"><table class="table">
                    <thead>
                        <tr>
                            <th>
                                <span class="title_box ">Imagen</span>
                            </th>                       
                            <th>
                                <span class="title_box ">Nombre</span>
                            </th>                       
                            <th>
                                <span class="title_box ">Accion</span>
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bodytablephotos">
                        {if !empty($photos)} 
                            {foreach from=$photos item=photo name=myLoop}
                                <tr>
                                    <td style="text-align:center;"><img src="{$urlphoto|escape:'htmlall':'UTF-8'}uploads/img/package/{$photo.id_package|escape:'htmlall':'UTF-8'}/{$photo.url|escape:'htmlall':'UTF-8'}" style="width:100px;">
                                    </td>
                                    <td>{$photo.url|escape:'htmlall':'UTF-8'}</td>
                                    <td>
                                        <div class="panel-heading-action">
                                            <a class="btn removephotoroom" data-hotel="{$photo.id_package|escape:'htmlall':'UTF-8'}" data-hotelphoto="{$photo.id_package_photo|escape:'htmlall':'UTF-8'}" data-name="{$photo.url|escape:'htmlall':'UTF-8'}">
                                                <span class="label label-danger"><i class="icon-remove"></i></span>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            {/foreach}
                        {/if} 
                    </tbody>
                </table>                                        
                <div class="col-lg-12 text-center">
                    <img id="loading" style="width:150px;display:none;" src="{$urlphoto|escape:'htmlall':'UTF-8'}views/img/loading.gif" />
                </div>
            </div>
                    </div>
                    <script type="text/javascript">
                        $(document).ready(function () {
                            $('#photo-selectbutton').click(function (e) {
                                $('#photo').trigger('click');
                            });

                            $('#photo-name').click(function (e) {
                                $('#photo').trigger('click');
                            });

                            $('#photo-name').on('dragenter', function (e) {
                                e.stopPropagation();
                                e.preventDefault();
                            });

                            $('#photo-name').on('dragover', function (e) {
                                e.stopPropagation();
                                e.preventDefault();
                            });

                            $('#photo-name').on('drop', function (e) {
                                e.preventDefault();
                                var files = e.originalEvent.dataTransfer.files;
                                $('#photo')[0].files = files;
                                $(this).val(files[0].name);
                            });

                            $('#photo').change(function (e) {
                                if ($(this)[0].files !== undefined)
                                {
                                    var files = $(this)[0].files;
                                    var name = '';

                                    $.each(files, function (index, value) {
                                        name += value.name + ', ';
                                    });

                                    $('#photo-name').val(name.slice(0, -2));
                                } else // Internet Explorer 9 Compatibility
                                {
                                    var name = $(this).val().split(/[\\/]/);
                                    $('#photo-name').val(name[name.length - 1]);
                                }
                            });

                            if (typeof photo_max_files !== 'undefined')
                            {
                                $('#photo').closest('form').on('submit', function (e) {
                                    if ($('#photo')[0].files.length > photo_max_files) {
                                        e.preventDefault();
                                        alert('You can upload a maximum of  files');
                                    }
                                });
                            }
                        });
                    </script>
                </div>
            </div>
            <div class="form-group">
              {*  <div id="imgpack" class="text-center" style="padding: 15px;">
                    {if $package->id_package neq ''}
                        {if $package->photo neq ''}
                            <label style="margin-right: 24px;">{$package->photo}</label>
                            <img src='{$urlphoto|escape:'htmlall':'UTF-8'}uploads/img/package/img_{$package->id_package}/{$package->photo}' style='width:30%;'/>
                        {/if}
                    {/if}
                </div>*}
                <div class="form-group">
                    <div class="panel" id="fieldset_0" data-url="{$urlajax}">            
                        <div class="col-lg-12">
                            <div class="panel panel-default">
                                <div class="panel-heading">Butacas                        
                                </div>
                                <div class="panel-body">
                                    <div class="form-group">
                                        <label class="control-label col-lg-1 required">
                                            Seleccionar Butaca
                                        </label>
                                        <div class="col-lg-4">
                                            <select id="butacas">
                                                {foreach from=$Butacas item=butaca name=myLoop}
                                                    <option value="{$butaca['id_seat']}">{$butaca['name']}</option>
                                                {/foreach}
                                            </select>                                
                                        </div>
                                        <label class="control-label col-lg-1 required">
                                            Precio butaca
                                        </label>
                                        <div class="col-lg-2">
                                            <div class="input-group">
                                                <input type="text" name="price_butaca" id="price_butaca" value="0" class="" onkeypress="return numbersvalid(event)" >
                                                <span class="input-group-addon">$</span>
                                            </div>
                                        </div>
                                        <div class="col-lg-4">
                                            <a id="addbutaca" class="btn-primary btn">Agregar butaca</a> 
                                        </div>
                                    </div>
                                    <div class="col-lg-10"><table class="table">
                                            <thead>
                                                <tr>
                                                    <th>
                                                        <span class="title_box ">Butaca</span>
                                                    </th>
                                                    <th>
                                                        <span class="title_box ">Precio butaca</span>
                                                    </th>
                                                    <th>
                                                        <span class="title_box ">Accion</span>
                                                    </th>
                                                </tr>
                                            </thead>
                                            <tbody class="bodytablebutaca">
                                                {if $ButacasPackage}
                                                    {foreach from=$ButacasPackage item=butaca name=myLoop}
                                                        <tr class="rowButaca" data-price="{$butaca['price']}" data-id="{$butaca['id_seat']}">
                                                            <td>{$butaca['name']}</td>
                                                            <td>{$butaca['price']}</td>
                                                            <td><div class="panel-heading-action">
                                                                    <a class="btn cancelarbutaca">
                                                                        <span class="label label-danger">
                                                                            <i class="icon-remove"></i>
                                                                        </span>
                                                                    </a>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    {/foreach}                                                  
                                                {/if}
                                            </tbody>
                                        </table>
                                    </div>
                                </div>                        
                            </div>                    
                            {*<div class="panel panel-default">
                                <div class="panel-heading">Adicionales                        
                                </div>
                                <div class="panel-body">
                                    <div class="form-group">
                                        <label class="control-label col-lg-1 required">
                                            Seleccionar Adicional
                                        </label>
                                        <div class="col-lg-3">
                                            <select id="adicionales">
                                                {foreach from=$Adicionales item=adicional name=myLoop}
                                                    <option value="{$adicional['id_aditional']}">{$adicional['name']}</option>
                                                {/foreach}
                                            </select>                                
                                        </div>
                                        <label class="control-label col-lg-1 required">
                                            Tipo de cobro
                                        </label>
                                        <div class="col-lg-1">
                                            <div class="input-group">
                                                <select id='tipoadd'>
                                                    <option val='$'>$</option>
                                                    <option val='%'>%</option>
                                                </select>
                                            </div>
                                        </div>
                                        <label class="control-label col-lg-1 required">
                                            valor
                                        </label>
                                        <div class="col-lg-2">
                                            <div class="input-group">
                                                <input type="text" name="price_adicional" id="price_adicional" value="" class="" onkeypress="return numbersvalid(event)" >
                                                <span class="input-group-addon" id='tipod'>$</span>
                                            </div>
                                        </div>
                                        <div class="col-lg-2">
                                            <a id="addadicional" class="btn-primary btn">Agregar adicional</a> 
                                        </div>
                                    </div>
                                    <div class="col-lg-10"><table class="table">
                                            <thead>
                                                <tr>
                                                    <th>
                                                        <span class="title_box ">Adicional</span>
                                                    </th>                                       
                                                    <th>
                                                        <span class="title_box ">Valor Adicional</span>
                                                    </th>
                                                    <th>
                                                        <span class="title_box ">Accion</span>
                                                    </th>
                                                </tr>
                                            </thead>
                                            <tbody class="bodytableadicional">
                                                {if $AditionalPackage}
                                                     {foreach from=$AditionalPackage item=aditional name=myLoop}
                                                         <tr class="rowAditional" data-id="{$aditional['id_aditional']}" data-type="{$aditional['type']}" data-price="{$aditional['value']}">
                                                             <td>{$aditional['name']} </td>
                                                             <td> {$aditional['type']}{$aditional['value']}</td>
                                                             <td><div class="panel-heading-action">
                                                                     <a class="btn cancelaradicional">
                                                                         <span class="label label-danger">
                                                                             <i class="icon-remove"></i>
                                                                         </span>
                                                                     </a>
                                                                 </div>
                                                             </td>
                                                         </tr>
                                                     {/foreach}
                                                {/if}
                                            </tbody>
                                        </table>
                                    </div>
                                </div>                        
                            </div>*}
                          {*  <div class="panel panel-default">
                                <div class="panel-heading">Origen                        
                                </div>
                                <div class="panel-body">
                                    <div class="form-group">
                                        <label class="control-label col-lg-1 required">
                                            Origen
                                        </label>
                                        <div class="col-lg-4">
                                              <select id="origenes">
                                                {foreach from=$Origenes item=origin name=myLoop}
                                                    <option value="{$origin['id_origen']}">{$origin['origen']}</option>
                                                {/foreach}
                                            </select>                                            
                                        </div>
                                        <label class="control-label col-lg-1 required">
                                            Precio origen
                                        </label>
                                        <div class="col-lg-2">
                                            <div class="input-group">
                                                <input type="text" name="price_origin" id="price_origin" value="" class="" onkeypress="return numbersvalid(event)" >
                                                <span class="input-group-addon">$</span>
                                            </div>
                                        </div>
                                        <div class="col-lg-4">
                                            <a id="addorigin" class="btn-primary btn">Agregar origen</a> 
                                        </div>
                                    </div>
                                    <div class="col-lg-10"><table class="table">
                                            <thead>
                                                <tr>
                                                    <th>
                                                        <span class="title_box ">Origen</span>
                                                    </th>
                                                    <th>
                                                        <span class="title_box ">Precio origen</span>
                                                    </th>
                                                    <th>
                                                        <span class="title_box ">Accion</span>
                                                    </th>
                                                </tr>
                                            </thead>
                                            <tbody class="bodytableorigin">
                                                {if  $OriginsPackage}
                                                    {foreach from=$OriginsPackage item=origin name=myLoop}
                                                        <tr class="rowOrigin" data-id="{$origin['id_origen']}" data-price="{$origin['price']}">
                                                            <td>{$origin['origen']}</td>
                                                            <td>{$origin['price']}</td>
                                                            <td><div class="panel-heading-action">
                                                                    <a class="btn cancelarorigen">
                                                                        <span class="label label-danger">
                                                                            <i class="icon-remove"></i>
                                                                        </span>
                                                                    </a>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    {/foreach}
                                                {/if}    
                                            </tbody>
                                        </table>
                                    </div>
                                </div>                        
                            </div>     *}               
                        </div>        
 {*                       <div class="form-group" id="destinations" style="">
                            <div class="col-lg-2">
                                Seleccionar destino
                                <select name="destinos" class=" fixed-width-xl" id="seldestinos">
                                    <option value="" >--</option>
                                    {foreach from=$Destinos item=destino name=myLoop}
                                        <option value="{$destino.id_destiny|escape:'htmlall':'UTF-8'}">{$destino.destiny|escape:'htmlall':'UTF-8'}</option>
                                    {/foreach}
                                </select>
                            </div>
                                <div class="col-lg-2">
                                    Precio <input type="text" id="pricedestiny" name="pricedestiny"> 
                                </div>                              
                                <div class="col-lg-2">
                                    Impuesto <input type="text" id="impuestodestiny" name="impuestodestiny"> 
                                </div>                              
                            <script>
                                $(function () {
                                    $("#datepickerfrom").datetimepicker({
                                        dateFormat: 'yy-mm-dd',
                                        minDate: 'today',
                                        language: 'es',
                                        changeYear: true,
                                        numberOfMonths: 1,
                                        changeMonth: true,
                                        yearRange: '-90 :+100'
                                    });
                                });
                                $(function () {
                                    $("#datepickerTo").datetimepicker({
                                        dateFormat: 'yy-mm-dd',
                                        minDate: 'today',
                                        language: 'es',
                                        changeYear: true,
                                        numberOfMonths: 1,
                                        changeMonth: true,
                                        yearRange: '-90 :+100'
                                    });
                                });
                            </script>
                            <div style="padding-bottom:10px;" class="col-lg-3">


                                <div class="col-lg-6">Fecha salida<input type="text" id="datepickerfrom" class="datepicker">
                                </div>
                                <div class="col-lg-6">Fecha llegada<input type="text" id="datepickerTo" class="datepicker">
                                </div>
                            </div>

                            <div class="col-lg-3" style="margin-top: 15px;">
                                <a id="adddestiny" class="btn-primary btn">Agregar destino</a> 
                            </div>
                        </div>*}
                        <hr>
                        {* CODE SEBASTIAN *}
                        <div class="form-group" id="container-destinations">
                            <div class="row">
                             {*   <div class="col-lg-2 col-xs-12 col-lg-offset-2">
                                    Seleccionar origen
                                    <select name="select-origins" class="fixed-width-xl" id="select-origins">
                                        <option value="">--</option>
                                        {foreach from=$Origenes item=origin name=myLoop}
                                            <option value="{$origin['id_origen']}">{$origin['origen']}</option>
                                        {/foreach}
                                    </select>
                                </div>*}
                                <div class="col-lg-3 col-xs-12 ">
                                    <div id="content-api-departures">
                                        Salida (API)
                                        <select id="form-departure-api" class="chosen-select" data-placeholder="Salida (API)">
                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-2 col-xs-12">
                                    Fecha salida
                                    <input type="text" id="date-departure" class="datepicker" autocomplete="off">
                                </div>
                                <div class="col-lg-2 col-xs-12">
                                    Fecha llegada
                                    <input type="text" id="date-arrival" class="datepicker" autocomplete="off">
                                </div>
                                <div class="col-lg-2 col-xs-12" style="margin-top: 15px;">
                                    <a id="btn-add-origin" class="btn btn-primary btn-block">Crear</a> 
                                </div>
                                {if $package->id_package != '' && !$package->api}
                                <div class="col-lg-2 col-xs-12" style="margin-top: 15px;">                                    
                                    <a id="btn-clone-origin" class="btn btn-primary btn-block">Clonar</a> 
                                </div>
                                {/if}
                            </div>
                        </div>
                        <div class="form-group" id="container-list-lines"></div>                           
                    </div><!-- /.form-wrapper -->
                </div><!-- /.form-wrapper -->
            </div></div><!-- /.form-wrapper -->
        <div class="panel-footer">
            <button type="submit"  id="inv_packages_form_submit_btn" data-action='{$package->id_package}' class="btn btn-default pull-right">
                <i class="process-icon-save"></i> Guardar
            </button>
            <a href="index.php?controller=AdminViajeroPaquete&amp;token={$toeee}" class="btn btn-default cancel-package" onclick="window.history.back();">
                <i class="process-icon-cancel"></i> Cancelar
            </a>
        </div>							
    </div>	
</form>

<script src="/modules/viajero/views/js/backpaquete.js"></script>