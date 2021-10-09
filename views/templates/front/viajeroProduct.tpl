<!doctype html>
<html lang="{$language.iso_code}">
    <head>
        {block name='head'}
            {include file='_partials/head.tpl'}
        {/block}
    </head>
    <script>
        var urlAjax = '{$urlAjax}';
        var urlAjaxViajero = '{$ViajeroProduct}';
        var edadninos = '{$package->edadninos}';
        var edadbebes = '{$package->edadbebes}';
        var valueninos = '{$package->valueninos}';
        var valuebebes = '{$package->valuebebes}';
        var disponibilidad = '{$package->disponibilidad}';
        var is_api = parseInt('{$package->api}');
        var id_api = '{$package->id_package_api}';
        var minQuota = parseInt('{$package->quota_api}');
    </script>

    <body id="{$page.page_name}" class="{$page.body_classes|classnames}">

        {hook h='displayAfterBodyOpeningTag'}

        <main>

            <header id="header">
                <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.6.3/css/all.css" integrity="sha384-UHRtZLI+pbxtHCWp1t77Bi1L4ZtiqrqD80Kn4Z8NTSRyMA2Fd33n5dQ8lWUE00s/" crossorigin="anonymous">
                {block name='header'}
                    {include file='_partials/header.tpl'}
                {/block}
            </header>

            <section id="wrapper">
                <div class="container responsive-container" style="font-family: "Montserrat", sans-serif;">

                    {block name="content_wrapper"}
                        <div id="content-wrapper">
                            {block name="content"}
                                <div id="viajeroProduct" class="col-md-10 col-sm-12 col-xs-12" data-url="{$ViajeroProduct}" data-package='{$package->id_package}'>
                                    <div class="contenedor_centrado">
                                        <script src="http://maps.googleapis.com/maps/api/js?key=AIzaSyD855TFM9LVJzvEF22-0vssUQdWNr5UUGc" type="text/javascript"></script>

                                        <div class="row">
                                            <div class="col-lg-6 col-md-6">
                                                <div id="sliderPackage" class="carousel slide" data-ride="carousel">
                                                    {assign var="flag" value="1"}
                                                    <div class="carousel-inner" id="agregarImagenPackage">
                                                        {foreach from=$photos item=photo name=myLoop}
                                                            <div class="carousel-item {if $flag eq "1"}active{/if}">
                                                                <img src="{$urlImage|escape:'htmlall':'UTF-8'}package/{$photo.id_package|escape:'htmlall':'UTF-8'}/{$photo.url|escape:'htmlall':'UTF-8'}" class="imagen_hotel" alt="">
                                                            </div>
                                                            {assign var="flag" value="0"}
                                                        {/foreach}
                                                    </div>

                                                    <a class="carousel-control-prev" href="#sliderPackage" data-slide="prev">
                                                        <span class="carousel-control-prev-icon"></span>
                                                    </a>
                                                    <a class="carousel-control-next" href="#sliderPackage" data-slide="next">
                                                        <span class="carousel-control-next-icon"></span>
                                                    </a>
                                                </div>
                                                {*<div  id="map" style="width: 100%; height: 300px;"></div>*}
                                                <div>
                                                    <table class="table table-striped hide" id="table-valores" style="margin-top: 20px; font-size: 15px;">
                                                        <tr>
                                                            <th>Descripción</th>
                                                            <th>Valor</th>
                                                        </tr>
                                                        <tr>
                                                            <td>Valor origen</td>
                                                            <td class="precioOrigen"></td>
                                                        </tr>
                                                        <tr>
                                                            <td>Valor linea</td>
                                                            <td class="precioLinea"></td>
                                                        </tr>
                                                        <tr>
                                                            <td>Valor Destinos</td>
                                                            <td class="precioDestinos"></td>
                                                        </tr>
                                                        <tr>
                                                            <td>Valor Impuesto</td>
                                                            <td class="precioImpuesto"></td>
                                                        </tr>
                                                        <tr>
                                                            <td>Valor butaca</td>
                                                            <td class="precioButaca"></td>
                                                        </tr>
                                                        <tr>
                                                            <td>Valor Adicionales</td>
                                                            <td class="precioAdicionales"></td>
                                                        </tr>
                                                        <tr>
                                                            <td>Cantidad adultos</td>
                                                            <td class="cantidadAdultos"></td>
                                                        </tr>
                                                        <tr>
                                                            <td>Cantidad niños</td>
                                                            <td class="cantidadChildrens"></td>
                                                        </tr>
                                                        <tr>
                                                            <td>Cantidad bebes</td>
                                                            <td class="cantidadBabys"></td>
                                                        </tr>
                                                        <tr class="bg-success">
                                                            <td class="text-center bg-success" colspan="2" style="color:#fff;">Totales</td>
                                                        </tr>
                                                        <tr>
                                                            <td>Valor total adultos</td>
                                                            <td class="precioAdultosTotal"></td>
                                                        </tr>
                                                        <tr>
                                                            <td>Valor total niños</td>
                                                            <td class="precioChildrenTotal"></td>
                                                        </tr>
                                                        <tr>
                                                            <td>Valor total bebes</td>
                                                            <td class="precioBabysTotal"></td>
                                                        </tr>
                                                        <tr class="bg-success">
                                                            <td class="text-center bg-success" colspan="2" style="color:#fff;">Hotel</td>
                                                        </tr>
                                                        <tr>
                                                            <td>Valor total adultos hotel</td>
                                                            <td class="precioAdultosHotel"></td>
                                                        </tr>
                                                        <tr>
                                                            <td>Valor total niños hotel</td>
                                                            <td class="precioChildrensHotel"></td>
                                                        </tr>
                                                        <tr>
                                                            <td>Valor total bebes hotel</td>
                                                            <td class="precioBabysHotel"></td>
                                                        </tr>

                                                        <tr>
                                                            <td>valor fecha salida</td>
                                                            <td>precioSalida</td>
                                                            <td class="precioSali"></td>
                                                        </tr>
                                                        <tr>
                                                            <td>valor fecha salida</td>
                                                            <td>impuestoSalida</td>
                                                            <td class="impuestoSali"></td>
                                                        </tr>
                                                        <tr>
                                                            <td>valor niños adicionales</td>
                                                            <td>niñosadic</td>
                                                            <td class="ninosadicionales"></td>
                                                        </tr>
                                                        <tr>
                                                            <td>valor niños adicionales</td>
                                                            <td>bebesadici</td>
                                                            <td class="bebesadicionales"></td>
                                                        </tr>
                                                        <tr>
                                                            <td>valor adicionales</td>
                                                            <td>precioAditional</td>
                                                            <td class="precioAditional"></td>
                                                        </tr>
                                                        <tr>
                                                            <td>valor habitaciones</td>
                                                            <td>precioAbitaciones</td>
                                                            <td class="precioHabitaciones"></td>
                                                        </tr>
                                                        <tr>
                                                            <td>valor habitaciones</td>
                                                            <td>precioAbitacionesniños</td>
                                                            <td class="precioHabitacionesninos"></td>
                                                        </tr>
                                                        <tr>
                                                            <td>valor habitaciones</td>
                                                            <td>precioAbitacionesbebes</td>
                                                            <td class="precioHabitacionesbebes"></td>
                                                        </tr>
                                                        <tr>
                                                            <td>valor regimen</td>
                                                            <td>precioRe</td>
                                                            <td class="precioregimen"></td>
                                                        </tr>
                                                        <tr>
                                                            <td colspan="2">Total</td>
                                                            <td class="totalPrice"></td>
                                                        </tr>
                                                    </table>

                                                    <table class="table table-striped" id="tablaPasajeros" style="margin-top: 20px; font-size: 15px;display:none;">
                                                        <thead>
                                                            <tr>
                                                                <th>NOMBRE</th>
                                                                <th>APELLIDO</th>
                                                                <th>DNI</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                        </tbody>
                                                    </table>

                                                </div>
                                            </div>
                                            <div class="col-lg-6 col-md-6 ">
                                                <h3 class="title-package" style="margin-top: -5px">{$package->name}</h3>
                                                <hr>
                                                <p>
                                                    {$package->detalles 'nofilter'}
                                                    {*{$package->detalles}*}
                                                </p>
                                            </div>
                                        </div>

                                        <div id="content-steps">
                                            <ul>
                                                <li><a href="#form-step-1">1</a></li>
                                                <li><a href="#form-step-2">2</a></li>
                                                <li><a href="#form-step-3">3</a></li>
                                            </ul>
                                            <div>
                                                <div id="form-step-1" class="content-step">
                                                    <div class="row mb-5">
                                                        <div class="col-lg-12 col-md-12">
                                                            <div class="container-fecha-salida" >
                                                                <div class="fff group-select-origin">
                                                                    <div class="row">
                                                                        <div class="col-md-12" >
                                                                            <h5 class="fecha_salida subtitle-package ttline mb-5">Seleccione un origen</h5>
                                                                        </div>
                                                                        <div class="col-md-12">
                                                                            <table class="table others" style="border-top: 0px">
                                                                        </div>

                                                                                {assign var="zonas" value=array()}
                                                                                {foreach from=$origenes key=key item=origen name=myLoop}
                                                                                {foreach from=$origenes item=curr_or}
                                                                                {assign var="vals" value=explode("-", $curr_or['origen'] )}
                                                                                {if (!in_array(trim($vals[1]), $zonas))}
                                                                                    {append var='zonas' value=trim($vals[1]) }

                                                                                {/if}
                                                                                {/foreach}
                                                                                {/foreach}

                                                                                {assign var="con" value=natcasesort($zonas)}

                                                                                {foreach from=$zonas item=zona}

                                                                                <tr style="border-bottom: 0px">
                                                                                    <td>
                                                                                        <button class="accordion" data-toggle="collapse" data-target="#demo" href="#collapse1">{$zona}</button>
                                                                                        <div id="collapse1" class="panel-collapse collapse" style="margin-top: 1em;">
                                                                                            <ol class="list-group">

                                                                                {foreach from=$origenes item=origen}

                                                                                {$content = explode("-", $origen['origen'])}

                                                                                {if $zona == trim($content[1])}
                                                                                        <li class="list-group-item" style="border: 0px"><label style="font-weight: normal"><input type="radio" style="text-align: center;vertical-align: middle;" class="seleccion_vuelo" name="vuelo" value="{$origen['origen']}" data-price="{$origen['price']}" latitud='{$origen['latitud']}' longitud='{$origen['longitud']}' name="vuelo" data-origen="{$origen['id_origen']}" id="or{$origen['id_origen']}"> {$content[0]}</label></li>
                                                                                {/if}
                                                                                {/foreach}
                                                                                    </div>
                                                                                    </ol>
                                                                                    </td>
                                                                                </tr>
                                                                                {/foreach}

                                                                            </table>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="container-salida">
                                                        <div class="row mt-5 mb-5">
                                                            <div class="col-md-12" >
                                                                <h5 class="fecha_salida subtitle-package ttline">Seleccione una salida</h5>
                                                            </div>
                                                            <div id="container-dates-package" class="col-12 mt-2" style="padding-right:0;position: inherit;">

                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="row">
                                                        <div class="col-12">
                                                            <div class="form-error-1"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div id="form-step-2" class="content-step">

                                                    <div class="row">
                                                        <div class="col-12">
                                                            <div class="container-icons-habitaciones pull-right">
                                                                <div class="icono-habitacion">
                                                                    <i class="fas fa-bed"></i>
                                                                    <span id="viajero_numbero_of_room_badge">0</span>
                                                                </div>
                                                                <div class="icono-habitacion">
                                                                    <i class="fas fa-user"></i>
                                                                    <span id="viajero_numbero_of_passengers_badge">0</span>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="col-12 mb-4">
                                                            <h5 class="fecha_salida subtitle-package ttline">{l s='Seleccione Habitaciones' momod='viajero'}</h5>
                                                        </div>

                                                        <div class="col-12">
                                                            <form action="?" id="form-pasajeros" method="post" onsubmit="validateRoomTypeForm()">

                                                                <div id="viajero_rooms" class="row mb-4"></div>

                                                                <div class="row mb-4">
                                                                    <div class="col-sm-12">
                                                                        <div id="viajero_alert_message"></div>
                                                                    </div>
                                                                    <div class="col-md-6 col-12">
                                                                        <div class="row">
                                                                            <div class="col-6">
                                                                                <button type="button" class="btn btn-custom btn-outline" id="viajero_add_room">Añadir Habitación</button>
                                                                            </div>
                                                                            <div class="col-6">
                                                                                <button type="submit" class="btn btn-custom">Aplicar</button>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </form>
                                                    </div><!--end pasajeros-->

                                                    <div class="row mt-5">
                                                        <div class="col-12">
                                                            <div class="panel-group">
                                                                <div class="row" id="container-destinations">
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="row">
                                                        <div class="col-12">
                                                            <div class="form-error-2"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div id="form-step-3" class="content-step">

                                                    <div class="row">
                                                        {if count($butacas ) gt 0}
                                                            <div class="col-md-12 mb-2">
                                                                <h5 class="fecha_salida subtitle-package ttline">Seleccione tipo de butaca</h5>
                                                            </div>

                                                            <div class="col-md-12">
                                                                <table class="table others">


                                                                    {foreach from=$butacas item=butaca name=myLoop}
                                                                        <tr>
                                                                            <td style="width:75%">
                                                                                <label class="radio-inline ">
                                                                                    <input type="radio"
                                                                                           class="seleccion_butaca"
                                                                                           value="{$butaca['name']}"
                                                                                           tipocupoId = "{$butaca['tipo_cupo_id']}"
                                                                                           name="butaca"
                                                                                           id="c{$butaca['id_package_seat']}" {if $butaca['cupos_butaca'] == 0 } {'disabled'} {/if}/>
                                                                                    {$butaca['name']}  ({$butaca['nombre_tipo_cupo']}) {if $butaca['cupos_butaca'] == 0 } <span style="color:red;">No Disponible</span> {/if}</label>
                                                                            </td>
                                                                            <td>
                                                                                (valor adicional) &nbsp; &nbsp;  $ {number_format($butaca['price'], 2, ',', '.')}
                                                                            </td>
                                                                        </tr>
                                                                    {/foreach}
                                                                </table>
                                                            </div>
                                                        {/if}
                                                    </div>
                                                    <div class="row  mt-5">
                                                        {if count($adicionales) gt 0 }
                                                            <div class="col-md-12 mb-2">
                                                                <h5 class="fecha_salida subtitle-package ttline">Seleccione Adicionales</h5>
                                                            </div>
                                                            <div class="col-md-12 no-padding-left-mobile">
                                                                <table class="table others">
                                                                    {foreach from=$adicionales item=adicional name=myLoop}
                                                                        <tr>
                                                                            <td style="width:82%">
                                                                                <label class="checkbox-inline "><input type="checkbox" class="seleccion_adicional " value="{$adicional['id_package_aditional']}" type_add="{$adicional['type']}" valor="{$adicional['value']}" name="adicional" data-name='{$adicional['name']}' data-id="{$adicional['id_package_aditional']}" id="d{$adicional['id_package_aditional']}"/>{$adicional['name']}</label>
                                                                            </td>
                                                                            <td >
                                                                                <strong>{if $adicional['type'] eq '$'}ARS{else}{$adicional['type']}{/if}</strong>
                                                                            </td>
                                                                            <td>
                                                                                {number_format($adicional['value'], 2, ',', '.')}
                                                                            </td>
                                                                        </tr>
                                                                    {/foreach}
                                                                </table>
                                                            </div>
                                                        {/if}
                                                    </div>
													<div class="container-info-pasajeros">
														<div class="row  mt-5">
															<div class="col-12 mb-2">
																<h5 class="fecha_salida subtitle-package ttline mb-0">Información de pasajeros</h5>
															</div>
															<div class="col-12 col-md-12 ">

																<div id="errores_pasajeros"></div>
																<br>
																<div class="row mobile-full">
																	<div class="col-12 pasajeros_formularios mb-5"></div>
																	<!--<div class="col-xs-12 col-md-2 d-flex justify-content-center align-items-center mobile-no-padding-right">
																	<button id="ejecutarActualizar" class="ocultar"></button>
																	<span class="fa fa-user-plus align-middle" id="boton_agregar_pasajero"></span>
																	</div>-->
																	<!-- <div class="col-sm-12">
																		<table class="table table-responsive tabla-pasajeros mb-5" >
																			<thead>
																				<tr>
																					<th>Nombres</th>
																					<th>Apellido</th>
																					<th>Celular</th>
																					<th>Sexo</th>
																					<th>Fecha Nacimiento</th>
																					<th>Tipo Doc.</th>
																					<th>Nro. Doc.</th>
																					<th></th>
																				</tr>
																			</thead>
																			<tbody>

																			</tbody>
																		</table>
																	</div> -->
																</div>
															</div>
														</div>
													</div>
                                                    <div class="row">
                                                        <div class="col-12">
                                                            <div class="form-error-3"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row btn-toolbar">
                                            <div class="col-6">
                                                <button id="btn-prev-form" class="btn-custom disabled">Anterior</button>
                                            </div>
                                            <div class="col-6 text-right">
                                                <button id="btn-next-form" class="btn-custom">Siguiente</button>
                                            </div>
                                        </div>

                                        <div class="content-legales">
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <h5 style="width:10%;" class="fecha_salida subtitle-package ttline mb-2">Legales</h5>
                                                </div>
                                                <div class="col-md-12">
                                                    {$package->legales 'nofilter'}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="modal fade" id="galeriaRooms" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="exampleModalLongTitle">Galeria</h5>
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-body">

                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                                                <button type="button" class="btn btn-primary">Guardar</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="modal" id="modalGaleria">
                                    <div class="modal-dialog">
                                        <div class="modal-content">

                                            <div class="modal-header">
                                                <h4 class="modal-title">Galería Habitación</h4>
                                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                                            </div>

                                            <div class="modal-body cuerpo-galeria">

                                            </div>

                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-danger" data-dismiss="modal">Cerrar</button>
                                            </div>

                                        </div>
                                    </div>
                                </div>

                                <div class="modal" id="modalMapa">
                                    <div class="modal-dialog">
                                        <div class="modal-content">

                                            <div class="modal-header">
                                                <h4 class="modal-title">Mapa Localizacion</h4>
                                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                                            </div>

                                            <div class="modal-body cuerpo-cordenadas" id="cuerpo-cordenadas" style="height: 400px;">

                                            </div>

                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-danger" data-dismiss="modal">Cerrar</button>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                            {/block}
                        </div>
                        <div class="fixed-info-container col-md-2  col-sm-12 col-xs-12">
                            <div class="fixed-info">
                                <div class="col-12  container-resumen resumen-float">
                                    <div class="row">
                                        <div class="col-12">
                                            <h5 class="subtitle mb-4">{$package->name}</h5>
                                            <hr>
                                        </div>
                                        <div class="col-12">
                                            <strong>Salida: </strong><span id="salidadate">-</span>
                                        </div>
                                        <div class="col-12">
                                            <strong>Duración: </strong><span id="duracion">-</span>
                                        </div>
                                        <hr>
                                    </div>
                                    <hr>
                                    <div>
                                        <strong>Origen: </strong><span id="origenresumen">-</span>
                                    </div>
                                    <div class="resumen-alojamiento"></div>
                                    <div class=" butacaresumen"></div>
                                    <div class=" adicionalresumen mb-2"></div>
                                    {*<div class="row">
                                        <strong>butaca: </strong><span id="salidadate"></span>
                                        <div class="col-md-8 col-12 d-flex justify-content-start align-items-center">
                                            <ul>
                                                <li><b>BUTACA + ADICIONAL</b></li>
                                            </ul>
                                        </div>
                                        <div class="col-md-4 col-12 d-flex justify-content-start justify-content-md-end align-items-center content-price">
                                            <p><strong class="resumen-adicional-total">$ 0</strong></p>
                                        </div>
                                    </div>*}

                                    <div class="alert alert-danger" role="alert" id="alertbuy" style="display:none;"></div>
                                    <div class="row" id="contenedor-total">
                                        <!--<div class='col-md-12 porper'>
                                            <div class='pull-left'>Por persona</div>
                                            <div class='pull-right'>
                                                <b class="resumen-porpersona" > $ 0,00 </b>
                                            </div>
                                        </div>-->
                                        <div class='col-md-12'>
                                            <div class='pull-left cantpesona'></div>
                                            <div class='pull-right '>
                                                <b class="resumen-subtotal"></b>
                                            </div>
                                        </div>
                                        <div class='col-md-12' style='padding-bottom: 10px;'>
                                            <div class='pull-left '>Impuestos</div>
                                            <div class='pull-right '>
                                                <b class="resumen-impuestos-total"> $ 0,00</b>
                                            </div>
                                        </div>
                                        <div class="col-md-12 totalres">
                                            <div class='pull-left '><h3>Total:</h3></div>
                                            <div class='pull-right '>
                                                <h3 class="resumen-total"> $ 0,00</h3>
                                            </div>
                                        </div>
                                        <div class="col-md-12 text-center">
                                            <button id="btn-comprar" data-product="{$package->id_product}" class="btn btn-comprar asignar btn-custom disabled">Comprar</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    {/block}

                </div>




                <div class="modal fade" id="selectRoomTypeModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                  <div class="modal-dialog" role="document">
                    <div class="modal-content">
                      <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="gridSystemModalLabel">Tipo de Habitación</h4>
                      </div>
                      <div class="modal-body">
                        <input id="id_hidden" type="hidden" name="id_hidden" value="">
                        <input id="namedestiny_hidden" type="hidden" name="namedestiny_hidden" value="">

                        <div class="form-group">
                          <label for="exampleInputEmail1">Por favor, seleccione un tipo de Habitación</label>
                          <select id="selectRoomTypeModalSelect" class="form-control">

                          </select>
                        </div>

                      </div>
                      <div class="modal-footer">
                        <!-- <button type="button" class="btn btn-default" data-dismiss="modal">Close</button> -->
                        <button type="button" class="btn btn-primary" onclick="saveSelectRoomTypeModal()">Seleccionar</button>
                      </div>
                    </div><!-- /.modal-content -->
                  </div><!-- /.modal-dialog -->
                </div><!-- /.modal -->


            </section>

            <footer id="footer">
                {block name="footer"}
                    {include file="_partials/footer.tpl"}
                {/block}
            </footer>

            <div class="fixed-info-mobile">
                <div class="expand col-xs-12" style="display: none;">
                    <br />
                    <h5 class="subtitle">{$package->name}</h5>
                    <div class="col-xs-6 pr-3 container-resumen">
                        <strong>Salida: </strong><span id="salidadate2">-</span>
                        <br />
                        <strong>Duración: </strong><span id="duracion2">-</span>
                        <br />
                        <strong>Origen: </strong><span id="origenresumen2">-</span>
                        <br />
                        <br />
                        <br />
                        <br />

                        <div class="row">
                            <div class="col-12">
                                <div class="pull-left">
                                    <strong>Por persona </strong>
                                </div>
                                <div class="pull-right">
                                    <b class="resumen-porpersona">$ 0,00</b>
                                </div>
                            </div>
                            <div class="col-12 mt-1">
                                <div class="pull-left">
                                    <strong class='cantpesona'></strong>
                                </div>
                                <div class="pull-right">
                                    <b class="resumen-subtotal"></b>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xs-6 pl-4 container-resumen">
                        <span class="resumen-alojamiento resumen-alojamiento-small">
                        </span>
                        <span class="butacaresumen butaca-small">

                        </span>
                        <span class=" adicionalresumen">

                        </span>
                    </div>
                    <br />
                </div>
                <div class="layer-mobile col-xs-12">
                    <div class="alert alert-danger" role="alert" id="alertbuy2" style="display:none;"></div>
                    <i class="fa fa-angle-up open-arrow"></i>
                    <i class="fa fa-angle-down close-arrow" style="display:none;"></i>
                    <br />
                    <table class="fixed-totals container-resumen" id="contenedor-total">
                        <tr>
                            <td>
                                <b>Impuestos</b>
                            </td>
                            <td class="text-right">
                                <b class="resumen-impuestos-total">$ 0,00</b>
                            </td>
                            <td rowspan="2" class="text-right px-3">
                                <button data-product="{$package->id_product}" class="btn btn-comprar asignar btn-custom disabled">Comprar</button>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <h3>TOTAL</h3>
                            </td>
                            <td class="text-right">
                                <h3 class="resumen-total">$ 0,00</h3>
                            </td>
                        <tr>
                    </table>
                    <br />
                </div>
            </div>
            <div class="layer-mobile-shadow">
            </div>
        </main>

        {hook h='displayBeforeBodyClosingTag'}

        {block name='javascript_bottom'}
            {include file="_partials/javascript.tpl" javascript=$javascript.bottom}
        {/block}

    </body>

</html>
