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

<script>
    $('#description').text('{$description}');
    $('#description_ifr').load(function () {       
        $(this).contents().find("body").html('{$description}');
    });
</script>
<style>
    .error{
        color:red !important;
    }
</style>
<div id="total">
    <div class="panel" id="panelfotos" data-hotel="{$hotel|escape:'htmlall':'UTF-8'}" data-url="{$urlajax|escape:'htmlall':'UTF-8'}">
        <div class="panel-heading">
            Fotos Hotel
        </div>
        <div class="panel-body">
            <div class="form-group">

                <label class="control-label col-lg-1">
                    <span class="label-tooltip" data-toggle="tooltip" data-html="true" title="" data-original-title="Upload a logo from your computer. (.gif, .jpg, .jpeg or .png)">

                    </span>
                </label>
                <div class="col-lg-8">

                    <div class="form-group">
                        <div class="col-sm-6">
                            <input id="image" type="file" name="files" class="hide" accept="image/x-png, image/gif, image/jpeg, image/jpg">
                            <div class="dummyfile input-group">
                                <span class="input-group-addon"><i class="icon-file"></i></span>
                                <input id="image-name" type="text" name="filename" readonly="" >
                                <span class="input-group-btn">
                                    <button id="image-selectbutton" type="button" name="submitAddAttachments" class="btn btn-default">
                                        <i class="icon-folder-open"></i> Agregar foto</button>
                                </span>
                            </div>
                            <p class="help-block">
                                Formato JPG, GIF, PNG. Tamaño 8.00 MB máximo.
                                Tamaño actual <span id="carrier_logo_size">indefinido</span>.
                            </p>                    
                        </div>                        

                    </div>
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
                                    <td style="text-align:center;"><img src="{$urlviajero|escape:'htmlall':'UTF-8'}uploads/img/hotels/{$photo.id_hotel|escape:'htmlall':'UTF-8'}/{$photo.url|escape:'htmlall':'UTF-8'}" style="width:100px;">
                                    </td>
                                    <td>{$photo.url|escape:'htmlall':'UTF-8'}</td>
                                    <td>
                                        <div class="panel-heading-action">
                                            <a class="btn removephotoroom" data-hotel="{$photo.id_hotel|escape:'htmlall':'UTF-8'}" data-hotelphoto="{$photo.id_hotel_photo|escape:'htmlall':'UTF-8'}" data-name="{$photo.url|escape:'htmlall':'UTF-8'}">
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
                    <img id="loading" style="width:150px;display:none;" src="{$urlviajero|escape:'htmlall':'UTF-8'}views/img/loading.gif" />
                </div>
            </div>
        </div>                        
    </div>   
</div>   






