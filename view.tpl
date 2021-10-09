<style>
    .titulo-paquete{
        margin-bottom: 30px;
    }
</style>

<div class="container" style="width: 95%; margin-left: 2.5%; background: #fff; text-align: left; padding: 0px 20px 20px 20px; font-size: 15px; box-shadow: 4px 0px 10px 0px rgba(0,0,0,.2); float: left; background: #2a2a2a; color: white">
    <h1 class="text-center titulo-paquete" style="margin-bottom: 0px">{$package->name} (Salida: {$fechaTxt})</h1>
</div>

<div class="container" style="width: 95%; margin-left: 2.5%; background: #fff; text-align: left; padding: 20px; font-size: 15px; box-shadow: 4px 4px 10px 0px rgba(0,0,0,.4);">       
    <div class="col-sm-12">
        <div class="row" style="margin-top: 20px">
            <div class="col-sm-12"><strong>Fecha de Reserva:&nbsp</strong>{$package->date_add}</div>
            <div class="col-sm-12"><strong>Referencia:</strong> {$referencia} - {$packagehistorial->fileIdRedEvt}</div>
            <div class="col-sm-12"><strong>Forma de Pago:</strong> {$metodoPago}</div>
        </div>
        
        <div class="row" style="margin-top: 20px">
            <div class="col-sm-12 text-center" style="margin-bottom: 15px; background: #666; padding: 10px; color: white"><strong>DETALLE</strong></div>                            
            <div class="col-sm-6"><strong>Salida: &nbsp{$packageLineaSal} / Llegada: {$packageLineaLLe}  (Horario Salida: se informa 48hs previo embarque, via e-mail)</strong></div>
        </div>
        <hr class="col-sm-12">
        <div class="row">
            <div class="col-sm-8"><strong>Origen:&nbsp</strong> {$packageorigen['origen']}</div>

            {*<div class="col-sm-11 col-sm-offset-1" style='margin-top10px;'>{$packageLinea['description']}</div>*}

        </div>
        <hr class="col-sm-12">
        <div class="row">
            <div class="col-sm-8"><strong>Servicios:&nbsp</strong>Incluye Asistencia al Viajero</div>
        </div>
        <hr class="col-sm-12">
        <div class="row">
            <div class="col-sm-8"><strong>Butaca:&nbsp</strong>{$packageButaca['name']}</div>            
        </div>
         <hr class="col-sm-12">
        <div class="row">
            <div class="col-sm-8"><strong>Impuesto:&nbsp</strong></div>            
            <div class="col-sm-4 text-right"><strong>${$packagehistorial->price-($packagehistorial->price/1.21)}</strong></div>
        </div>
        <hr class="col-sm-12">
        <div class="row">
            <div class="col-sm-8"><strong>Total:</strong></div>
            <div class="col-sm-4 text-right"><strong>${($packagehistorial->price)}</strong></div>
        </div>


        <hr class="col-sm-12">

{*

        <div class="row">

            {assign var=adi value=1}

            {foreach from=$packageAditionals item=adicional name=myLoop}
                {if $adi == 1}
                    <div class="col-sm-4"><strong>Adicionales:</strong></div>
                {else}
                    <div class="col-sm-4"></div>
                {/if}
                <div class="col-sm-4">{$adicional['name']}</div>
                <div class="col-sm-4 text-right"><strong class="text-danger">${$adicional['value']}</strong></div>

                <div class="col-sm-12"><hr class="col-sm-12"></div>
                    {$adi = $adi + 1}
                {/foreach}
        </div>
*}
        <div class="row">
            <div class="col-sm-12 text-center" style="margin-bottom: 15px; background: #666; padding: 10px; color: white"><strong>Alojamiento</strong></div>                
            {foreach from=$packageRooms item=habitacion name=myLoop}                     
                <div class="row">
                    <div class="col-sm-2"><strong>Destino:</strong></div>
                    <div class="col-sm-2">{$habitacion['namedestiny']}</div>
                    <div class="col-sm-2"><strong>Hotel:</strong></div>
                    <div class="col-sm-2">{$habitacion['nameHotel']}</div>
                    <div class="col-sm-2"><strong>Habitación:</strong></div>
                    <div class="col-sm-2">{$habitacion['name']}</div>
                </div>   
                <hr class="col-sm-12">
            {/foreach}
        </div>
    </div>


    <div class="row">
        {*           <hr class="col-sm-12">*}
        {assign var=adi value=1}
        {foreach from=$packagePasajeros item=pasajeros name=myLoop}

            {if $adi == 1}   
                <div class="col-sm-12 text-center" style="margin-bottom: 15px; background: #666; padding: 10px; color: white"><strong>Pasajeros</strong></div>
                <div class="col-sm-2"><strong>Cant Adultos:</strong></div>
                <div class="col-sm-2">{$packagehistorial->cantPassAdult}</div>
                <div class="col-sm-2"><strong>Cant Niños:</strong></div>
                <div class="col-sm-2">{$packagehistorial->cantPassNinos}</div>
                <div class="col-sm-2"><strong>Cant Bebes:</strong></div>
                <div class="col-sm-2">{$packagehistorial->cantPassBebes}</div>
                <hr class="col-sm-12">
            {else}
                <div class="col-sm-12"></div>    
            {/if}
            <div class="col-sm-3"><strong>Nombre Completo: </strong></div>
            <div class="col-sm-3">{$pasajeros['nombre']} {$pasajeros['apellido']}</div>
            <div class="col-sm-3 text-right"><strong>Dni: </strong></div>
            <div class="col-sm-3 text-right"><strong class="text-info">{$pasajeros['dni']}</strong></div>
            <div class="col-sm-3 "><strong>Fecha nacimiento: </strong></div>
            <div class="col-sm-3 ">{$pasajeros['fecha_nacimiento']|replace:"00:00:00":""}</div>
            <div class="col-sm-3 text-right"><strong>Celular: </strong></div>
            <div class="col-sm-3 text-right"><strong class="text-info">{$pasajeros['telefono']}</strong></div>
            <div class="col-sm-3 "><strong>Sexo: </strong></div>
            <div class="col-sm-3 ">{$pasajeros['sexo']}</div>
            <div class="col-sm-12"><hr class="col-sm-12"></div>
                {$adi = $adi + 1}
            {/foreach}        
    </div>

    <div class="row">

        <div class="col-sm-12 text-center" style="margin-bottom: 15px; background: #666; padding: 10px; color: white"><strong>Datos del comprador </strong></div>

        <div class="col-sm-4 text-center"><strong>Nombre:</strong> </div>
        <div class="col-sm-8 text-right" style="border-bottom: 1px solid #888; padding-bottom: 5px">&nbsp{$customer->firstname}</div>

        <div class="col-sm-4 text-center" style="margin-top: 10px"><strong>Apellido:</strong> </div>
        <div class="col-sm-8 text-right" style="border-bottom: 1px solid #888; padding-bottom: 5px; margin-top: 10px">&nbsp{$customer->lastname}</div>

        <div class="col-sm-4 text-center" style="margin-top: 10px"><strong>Correo Electronico:</strong> </div>
        <div class="col-sm-8 text-right" style="border-bottom: 1px solid #888; padding-bottom: 5px; margin-top: 10px">&nbsp{$customer->email}</div>

    </div>    

</div>

</div>






