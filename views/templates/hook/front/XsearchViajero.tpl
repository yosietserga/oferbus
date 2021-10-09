<script>
    var url_ajax_search = '{$urlSearch}';
    var urlAjaxViajero = '{$ViajeroProduct}';
    var destinos_ajax = '{$destinos}';
    var origenes_ajax = '{$origenes2}';
</script>
<style>
/** @Override Select2 */
.select2-container .select2-selection--single .select2-selection__rendered {
    height: 45px;
}
.select2-results__option, 
.select2-search--dropdown .select2-search__field {
  font-size: 18px;
}
.select2-selection--single:active,
.select2-selection--single:focus {
    border: 0;
}
</style>
<div id="displayTopViajero" data-des="{$destinos}" data-ori="{$origenes2}" class="search-bar-top" data-url="{$urlSearch}">
    <div class="container">
        <form id="form-search-product" action="?" method="post">
            <div class="row">
                <div class="col-md-12 text-center">                    
                    <h2 class="tt">Elegí tu próximo viaje en bus</h2>
                </div>
                <div class="col-md-10 col-xs-12">
                    <div class="row">
						<div class="col-xs-12 col-md-3 mb-3 mb-md-0">                         
                            <div class="">
                                <label for=""><h4>Paquete</h4></label>
                                <select name="paquete" id="select-paquete" class="form-control ">
                                    
                                        <option value="1">1</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-xs-12 col-md-3 mb-3 mb-md-0">                         
                            <div class="">
                                <label for=""><h4>Origenes</h4></label>
                                <select name="origen" id="select-origen" class="form-control select2-hidden-accessible">
                                    <option></option>
                                    {foreach from=$origenes key=key item=origen name=myLoop}
                                        <option value="{$origen['id_origen']}">{$origen['origen']}</option>
                                    {/foreach}
                                </select>
                            </div>
                        </div>
                        <div class="col-xs-12 col-md-3 mb-3 mb-md-0">
                            <div class="">
                                <label for=""><h4>Destinos</h4></label>
                                <select name="destino" id="select-destino" class="form-control select2-hidden-accessible" data-destinos="{$destinos}">
                                    <option></option>
                                    {foreach from=$destinos2 key=key item=destino name=myLoop}
                                        <option value="{$destino['id_destiny']}">{$destino['destiny']}</option>
                                    {/foreach}                                    
                                </select>
                            </div>
                        </div>
                        <div class="col-xs-12 col-md-3 mb-3 mb-md-0">
                            <div class="">
                                <label for=""><h4>Mes</h4></label>
                                <select name="salida" id="select-salida" class="form-control select2-hidden-accessible">
                                    <option></option>
                                    {foreach from=$months key=key item=month name=myLoop}
                                        <option value="{$key}">{$month}</option>
                                    {/foreach}
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xs-12 col-md-2 container-button">
                    <div class="text-center">
                        <a href="#" id="btn-restart" class="btn btn-info btn-hide" style="display:none;">RESTABLECER</a>
                        <button type="submit" class="btn btn-theme">BUSCAR</button>
                    </div>
                </div>
            </div>
    </div>
</form>
</div>
</div>

