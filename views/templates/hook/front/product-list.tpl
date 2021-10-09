{if count($products) > 0}
    <div class="row">
    {foreach from=$products item=product name=myLoop}
        <div class="col-md-3 col-xs-12 pb-5 {if !$product['showPaquete']}hide{/if}" >
            <div class="owl-item active">
                <div class="item" itemscope="" itemtype="http://schema.org/Product">
                    <div style="display:none;">{$product|@var_dump}</div>
                    <div class="card b-radius-15" onClick="goToPackage('{$viajero_product_link}?id_product={$product['id_product']}')" itemtype="http://schema.org/Product" style="cursor:pointer !important;">
                        <div class="card-wrap-image">
                            <div class="date-top pt-1">
                                <span class="icon-package">
                                    <i class="fa fa-briefcase f-20"></i>
                                </span>
                                <span class="date-out pl-1 text-{if !empty($product['id_package_photo'])}white{else}dark{/if}">{$product['fechasalida']}</span>
                            </div>
                            <div class="mask-img">
                                <div class="img-cover img-cover-mobile" style="background: url({if !empty($product['id_package_photo'])}{$urlImage}package/{$product['id_package_photo']}/{$product['url']}{else}img/p/es-default-tm_home_default.jpg{/if}) center top; background-size: 100% 100%; cursor:pointer;"></div>
                            </div>
                            {if $product['servicios']['regimen']}
                            <div class="regimen">
                                <span class="f-12 font-weight-bold">{$product['servicios']['regimen']}</span>
                            </div>
                            {/if}
                            <div class="adicionales">
                                {if $product['servicios']['transporte']}
                                <span class="transporte" data-toggle="tooltip" data-placement="top" title="{$product['servicios']['transporte']}">
                                    <i class="fa fa-bus f-25"></i>
                                </span>
                                {/if}
                                {if $product['servicios']['coordinacion']}
                                <span class="hospedaje" data-toggle="tooltip" data-placement="top" title="Hotel">
                                    <i class="fa fa-building f-25"></i>
                                </span>
                                {/if}
                                {if $product['servicios']['asistencia']}
                                <span class="asistencia" data-toggle="tooltip" data-placement="top" title="{$product['servicios']['asistencia']}">
                                    <i class="fa fa-medkit f-25"></i>
                                </span>
                                {/if}
                            </div>
                        </div>
                        <div class="card-body text-center">
                            <h5 class="card-title f-20 m-1 font-weight-bold product-title">
                            {$product['name']}
                            </h5>
                                <p class="card-text">
                                    {$product['servicios']['duracion']}
                                </p>
                                <h5 class="mt-0 font-weight-bold" itemprop="price">
                                    <span class="font-weight-normal">desde</span>
                                    <span itemprop="price" class="price f-20">
                                        ARS {number_format($product['pricereference'], 2, ',', '.')}
                                    </span>
                                </h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    {/foreach}
    <div class="col-xs-12 text-center">
        <ul class="pagination">
            {for $page=1 to $total_pages}
                <li data-page="{$page}" class="btn-paginate {if $page eq $page_act}active{/if}" style="margin: 0 auto;" ><a href="#">{$page}</a></li>
            {/for}
        </ul>
    </div>
    </div>
    {else}
    <div class="col-xs-12 text-center"><h3>No tiene paquetes para mostrar</h3></div>
{/if}