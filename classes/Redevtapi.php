<?php
/**
 2007-2018 PrestaShop

 NOTICE OF LICENSE

 This source file is subject to the Open Software License (OSL 3.0)
 that is bundled with this package in the file LICENSE.txt.
 It is also available through the world-wide-web at this URL:
 http://opensource.org/licenses/osl-3.0.php
 If you did not receive a copy of the license and are unable to
 obtain it through the world-wide-web, please send an email
 to license@prestashop.com so we can send you a copy immediately.

 DISCLAIMER

 Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 versions in the future. If you wish to customize PrestaShop for your
  needs please refer to http://www.prestashop.com for more information.

  @author PrestaShop SA <contact@prestashop.com>
  @copyright  2007-2018 PrestaShop SA
  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
  International Registered Trademark & Property of PrestaShop SA
**/

class Redevtapi extends Module
{


    public function __construct()
    {
    }

    /**
     * 
     * Send request to the api
     *
     * @param string $url url of the request
     * @param string $type  Get | Post
     * @param array $data  description
     * @return array
     */
    private function request($url, $type = 'get', $data = [], $auth = true)
    {
        $url_server = Configuration::get('INV_URL_API');
        $curl = curl_init($url_server.$url);

        $headers = [
            'Accept: application/json',
            'Content-Type: application/json'
        ];

        if ($auth) {
            $token = $this->getToken();
            $headers[] = 'Authorization: Bearer '. $token;
        }

        if ($type === 'post') {
            $data = json_encode($data);
            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);

            $headers[] = 'Content-Length: ' . strlen($data);
        }

        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

        //curl_exec ($curl);

        $info = curl_getinfo($curl);
        $response = curl_exec($curl);

        $result = [
            'info' => $info,
            'response' => $response
        ];

        curl_close($curl);
        $response = json_decode($response);
        
        if (property_exists($response, 'Message') && $info['http_code'] == 401) {
            $this->clearToken();
            $this->request($url, $type, $data, $auth);
        }

        return $result;
    }

    private function clearToken()
    {
        $tokens_auth = TokenAuth::getToken();

        foreach ($tokens_auth as $token_auth) {
            $current_token = new TokenAuth($token_auth['id_token']);
            $current_token->delete();
        }
    }

    private function getToken()
    {
        $tokens_auth = TokenAuth::getToken();

        if (count($tokens_auth) == 0) {
            return $this->assignToken();
        }

        $token_auth = $tokens_auth[0];
        $date_expiration = $token_auth['token_expiration'];

        $current_date = date('Y-m-d H:i:s');

        if ($current_date > $date_expiration) {
            $current_token = new TokenAuth($token_auth['id_token']);
            $current_token->delete();

            return $this->assignToken();
        }

        return $token_auth['code'];
    }

    private function setToken($response)
    {
        $token_auth = new TokenAuth();
        $token_auth->code = $response->token;
        $token_auth->token_expiration = date("Y-m-d H:i:s", strtotime($response->expiration));
        $token_auth->save();

        return $response->token;
    }

    /**
     * Set values for the inputs.
     */
    private function assignToken()
    {
        $url = 'Autenticacion/ObtenerToken';
        $data = [
            'Usuario' => Configuration::get('INV_USER_API'),
            'Password' => Configuration::get('INV_PASS_API')
        ];
        $type = 'post';
        $result = $this->request($url, $type, $data, false);
        $response = json_decode($result['response']);
        
        return $this->setToken($response);
    }


    /**
     * get values for the inputs.
     */
    public function getPackages()
    {
        $url = 'Productos?page=1&pageSize=100';
        $result = $this->request($url);

        $response = json_decode($result['response']);
        return $response;
    }

    /**
     * get values for the inputs.
     */
    public function getSeats()
    {
        $url = 'Productos/TiposButacas';
        $result = $this->request($url);
        $response = json_decode($result['response']);
        return $response;
    }
  
    /**
     * get values for the inputs.
     */
    public function getDepartures($id_package)
    {
        $url = 'Productos/Salidas?idProducto=' . $id_package;
        $result = $this->request($url);
        $response = json_decode($result['response']);
        return $response;
    }

    public function getOriginsDeparture($departure_id)
    {
        $url = 'Productos/Origenes?idDestino=' . $departure_id;
        $result = $this->request($url);
        $response = json_decode($result['response']);
        return $response;
    }

    /**
     * get values for the inputs.
     */
    public function getDepartureById($departure_id)
    {
        $url = 'Productos/Salida?idSalida=' . $departure_id;
        $result = $this->request($url);
        $response = json_decode($result['response']);
        return $response;
    }

    public function generateReservation($packageHistorial, $ok = true)
    {
        $package = new ViajeroPaquetes($packageHistorial->id_package);
        $linea = new ViajeroPaqueteLinea($packageHistorial->id_package_linea);
        $origin = new ViajeroOrigenes($packageHistorial->id_origin);


        $amount_adults = $packageHistorial->cantPassAdult;
        $amount_children = $packageHistorial->cantPassNinos;
        $amount_babys = $packageHistorial->cantPassBebes;

        $url = 'Reservas/NuevaReserva';
        $state = 'OK';

        if (!$ok) {
            $state = 'BQ';
        }

        $data_request = [
            'pItemProductoID' => $linea->departure_id,
            'pLocalidadID_Origen' => $origin->id_origin_api,
            'pCantADL' => $amount_adults,
            'pCantCHD' => $amount_children,
            'pCantINF' => $amount_babys,
            'pEMPRESAID' => 202,
            'pEMPRESAID_Cliente' => 14757,
            //'pEMPRESAID' => $package->company_id,
            'pEstadoID' => $state,
            'SRV_Servicios' => [
                    [
                      "TipoServicioID"=> "CSRV",
                      "SRV_ServicioID"=> 0,
                      "CantAdultos"=> 0,
                      "CantChilds"=> 0,
                      "CantInfoas"=> 0,
                      "Cantidad"=> 0
                    ]
            ]
        ];

        /*'SRV_Servicios' => [
                [
                    'TipoServicioID' => 'SRV',
                    'SRV_ServicioID' => 3,
                    'CantAdultos' => $amount_adults,
                    'CantChilds' => $amount_children,
                    'CantInfoas' => $amount_babys,
                    'Cantidad' => 1
                ]
            ],*/

        $data_accommodation = $this->getAccommodationApi($packageHistorial);
        $data_transport = $this->getTransportApi($packageHistorial);
        $data_passengers = $this->getPassengersApi($packageHistorial);

        $data_request['SRV_Alojamientos'] = $data_accommodation['rooms'];
        $data_request['pLocalidadID_Destino'] = $data_accommodation['destination'];
        $data_request['SRV_Transportes'] = [$data_transport];
        $data_request['PAX_Pasajeros'] = $data_passengers;

        $type = 'post';

        //print_r(json_encode($data_request));
        //var_dump($data_request);
        //exit();
        //print_r(json_encode($data_request, true));
        //echo 'abababa <br/><br/>';

         $result = $this->request($url, $type, $data_request);
         $response = json_decode($result['response']);


        //print_r($response);
        //echo 'abababa <br/><br/>';
        //var_dump($response);

         
        if (!$ok) {
            $packageHistorial->id_proceso = $response->fileID;
           
            //AGREGADO POR TUTTO PARA QUE GUARDE EL DATO EN LA BASE
            $packageHistorial->fileIdRedEvt=$response->fileID;
                        
            $packageHistorial->update();

        }
        //exit();
    }

    public function updateStateReservation($packageHistorial)
    {
        $id_process = $packageHistorial->id_proceso;

        $data = [
            "FileID" => $id_process,
            "EstadoID" => "OK",
        ];

        $url = 'Reservas/CambiarEstado';
        $type = 'post';
        $result = $this->request($url, $type, $data);
        $response = json_decode($result['response']);

        $packageHistorial->id_proceso = null;
        $packageHistorial->update();
    }

    private function getAccommodationApi($packageHistorial)
    {
        $rooms_historial = ViajeroPaqueteHistorialRooms::getHistorialRoomByhistorial($packageHistorial->id);
        $destination_select = null;

        $first_destination = true;
        $data_rooms = [];

        foreach ($rooms_historial as $room_historial) {
            $room_package = new ViajeroPaqueteRooms($room_historial['id_package_room']);
            $room = new ViajeroRooms($room_package->id_room);
            $hotel = new ViajeroHotel($room->id_hotel);

            if ($first_destination) {
                $first_destination = false;
                $destination_select = new ViajeroDestinos($hotel->id_destiny);
            }

            $data_room = [
                "TipoServicioID" => 'CALO',
                "SRV_AlojamientoID" => $room_package->srv_alojamiento_id,
                //"SRV_AlojamientoID" => $room->id_room_api,
                "CategoriaHabitacionID" => $room->categoria_habitacion_id,
                "TipoHabitacionID" => $room->id_room_api,//$room->tipo_habitacion_id,
                "EspecificacionID" => 1,
                "RegimenID" => $room->regimen_id,
                "CantAdultos" => $room_historial['adults'],
                "CantChilds" => $room_historial['children'],
                "CantInfoas" => $room_historial['babys'],
                "Cantidad" => $room_historial['cant_room'],
            ];

            $data_rooms[] = $data_room;
        }

        return [
            'destination' => $destination_select->id_destination_api,
            'rooms' => $data_rooms
        ];
    }

    private function getTransportApi($packageHistorial)
    {
        $line = new ViajeroPaqueteLinea($packageHistorial->id_package_linea);
        $line_transport = ViajeroPaqueteLineaTransportes::getTransportsByLine($line->id);

        $line_transport_type = ViajeroPaqueteLineaTransportesTipo::getTypeByTransport($packageHistorial->id_package_butaca);

        $data_transport = [
            "TipoServicioID" => 'CTPE',
            //"SRV_TransporteID" => $line_transport['transporte_id'],
            "SRV_TransporteID" => $line_transport['tipo_servicio_id'],
            //"TipoButacaID" => $line_transport['tipo_butaca_id'],
            "TipoButacaID" => (int)$line_transport_type['id_seat'],
            //"TipoCupoID" => $line_transport['tipo_cupo_id'],
            "TipoCupoID" => $packageHistorial->id_package_butaca_tipo_cupo,
            "Cantidad" => 1,
        ];

        return $data_transport;
    }

    private function getPassengersApi($packageHistorial)
    {
        $data_passengers = [];

        $passengers = ViajeroPaqueteHistorialPasajeros::getHistorialPasajerosByhistorial($packageHistorial->id);

        foreach ($passengers as $passenger) {
            $new_date = date("Y-m-d", strtotime($passenger['fecha_nacimiento']));

            $data_passenger = [
                "Apellido" => $passenger['apellido'],
                "Nombre" => $passenger['nombre'],
                "FechaNacimiento" => $new_date,
                "NroDocumento" => $passenger['dni'],
                "Sexo" => $passenger['sexo'],
                // "TipoDocumentoID" => '', // Falta
                // "PaisID_Nacionalidad" => '', // Falta
            ];

            $data_passengers[] = $data_passenger;
        }

        return $data_passengers;
    }

}