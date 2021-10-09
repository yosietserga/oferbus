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

class ViajeroPaqueteLinea extends ObjectModel
{
    
    public $id_package_Linea;
    public $id_package;
    public $description;
    public $impuesto;
    public $price;
    public $inventario;
    public $cupos_room;
    public $date_sal;
    public $date_lle;
    public $date_add;
    public $date_upd;
    public $departure_id;
    
    public static $definition = array(
        'table' => 'inv_packages_linea',
        'primary' => 'id_package_Linea',
        'fields' => array(
            'id_package_Linea' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'id_package' => array('type' => self::TYPE_INT, 'required' => true),            
            'impuesto' => array('type' => self::TYPE_FLOAT),
            'price' => array('type' => self::TYPE_FLOAT),
            'inventario' => array('type' => self::TYPE_INT),
            'cupos_room' => array('type' => self::TYPE_INT),
            'description' =>array('type' => self::TYPE_HTML, 'validate' => 'isCleanHtml','size' => 3999999999999),
            'date_sal' => array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
            'date_lle' => array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
            'date_add' => array('type' => self::TYPE_DATE, 'validate' => 'isDate', 'copy_post' => false),
            'date_upd' => array('type' => self::TYPE_DATE, 'validate' => 'isDate', 'copy_post' => false),
            'departure_id' => array('type' => self::TYPE_INT)
        ),

    );

    public static function getLineasBypackagess($id) {
        $seats = array();
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
		SELECT * FROM `' . _DB_PREFIX_ . 'inv_packages_linea` D where  id_package=' . $id .
                ' ORDER BY D.id_package_Linea ASC');
        foreach ($result as $row) {
            $seats[$row['id_package_Linea']] = $row;
        }
//        var_dump($seats);exit();
        return $seats;
    }

    //lineas

    public static function getLineasByDeparturePackage($departurId,$packageId)
    {
        $query = "SELECT
                        a.cupos_room as CupoAlojamiento,
                        SUM(c.cupos_butaca) as CupoTransporte
                    FROM
                        pr_inv_packages_linea as a
                    INNER JOIN pr_inv_packages_linea_transportes as b on b.id_package_linea = a.id_package_Linea 
                    INNER JOIN pr_inv_packages_linea_transportes_tipos as c on c.id_packages_linea_transportes = b.id_packages_linea_transporte
                    WHERE    a.departure_id = $departurId AND a.id_package = $packageId";

        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS($query);
        //return $query;
        if($result){
            return $result[0];
        }else{
            return false;
        }

    }

    public static function getLineasByPakage($id) {
        $roomspak = array();
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
        SELECT PD.id_package as idpackage, PD.id_package_linea as idpackagelinea, 
               date_lle as datelle, date_sal as datesal, inventario, description,
               impuesto, price, departure_id
            FROM `' . _DB_PREFIX_ . 'inv_packages_linea` PD 
        WHERE PD.id_package=' . $id);
//        var_dump($result);exit();
        foreach ($result as $key2=>$row) {
            $hotels = ViajeroPaqueteLinea::getHotelsByRoomsLine($row['idpackagelinea']);
            foreach($hotels as $key=>$hotel){                
                $rooms = ViajeroRooms::getRoomsByHotel($hotel['idhotel'], $id);
                $hotels[$key]['rooms'] = $rooms;
            }                     
            $result[$key2]['hotels']=$hotels;
            $result[$key2]['origins']=ViajeroPaqueteOrigins::getOriginsByLinea($row['idpackagelinea']);
                        $result[$key2]['services']=ViajeroPaqueteLineaServicios::getServicesByLinea($row['idpackagelinea']);
        }
        return $result;
    }
    public static function getHotelsByRoomsLine($id){
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
		SELECT H.id_hotel as idhotel,H.name as namehotel, H.id_hotel_api as id_hotel_api,D.destiny as destinyname,D.id_destiny as iddestiny, D.id_destination_api as id_destination_api, PR.srv_alojamiento_id FROM `' . _DB_PREFIX_ . 'inv_packages_rooms` PR '
                . ' left join `' . _DB_PREFIX_ . 'inv_rooms` AS R ON PR.id_room=R.id_room'
                . ' left join `' . _DB_PREFIX_ . 'inv_hotels` AS H ON R.id_hotel=H.id_hotel'
                . ' left join `' . _DB_PREFIX_ . 'inv_destinations` AS D ON D.id_destiny=H.id_destiny'
                . ' where PR.id_package_Linea=' . $id. ' group by H.id_hotel ' );
        return $result;        
    } 
    
    public static function getLinesBypackage($id) {
        $lines = array();
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
        SELECT * FROM `' . _DB_PREFIX_ . 'inv_packages_linea` pl INNER JOIN `' . _DB_PREFIX_ . 'inv_origins` o WHERE pl.id_origen= o.id_origen AND pl.id_package=' . $id .' ORDER BY pl.date_sal ASC');
        foreach ($result as $row) {
            $lines[] = $row;
        }

        return $lines;
    }

    public static function getLinesById($id, $id_package_Linea) {
        $lines = array();
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
        SELECT PD.id_package as idpackage, PD.id_package_linea as idpackagelinea, 
               date_lle as datelle, date_sal as datesal, inventario, description,
               impuesto, price
            FROM `' . _DB_PREFIX_ . 'inv_packages_linea` PD 
        WHERE PD.id_package_linea=' . (int)$id_package_Linea);
//        var_dump($result);exit();
        foreach ($result as $key2=>$row) {
            $hotels = ViajeroPaqueteLinea::getHotelsByRoomsLine($row['idpackagelinea']);
            foreach($hotels as $key=>$hotel){                
                $rooms = ViajeroRooms::getRoomsByHotel($hotel['idhotel'], $id);
                $hotels[$key]['rooms'] = $rooms;
            }                     
            $result[$key2]['hotels']=$hotels;
            $result[$key2]['origins']=ViajeroPaqueteOrigins::getOriginsByLinea($row['idpackagelinea']);
                        $result[$key2]['services']=ViajeroPaqueteLineaServicios::getServicesByLinea($row['idpackagelinea']);
        }
        return $result;
    }

    public static function getOriginsLinesBypackage($id) {
        $lines = array();
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
        SELECT * FROM `' . _DB_PREFIX_ . 'inv_packages_linea` pl INNER JOIN `' . _DB_PREFIX_ . 'inv_origins` o WHERE pl.id_origen= o.id_origen AND pl.id_package=' . $id .' GROUP BY o.id_origen ORDER BY pl.date_sal ASC');
        foreach ($result as $row) {
            $lines[] = $row;
        }
        
        return $lines;
    }
    /*
     ==== FECHA DE SALIDAS
     */
    public static function getLinesByPackageOrigin($id_origin, $id_package) {
        $lines = array();
        $sql = 'SELECT *,pl.price as priceline, pl.id_package, pl.departure_id FROM ' . _DB_PREFIX_ . 'inv_packages_linea pl JOIN ' .
                _DB_PREFIX_ . 'inv_packages_origins po ON pl.id_package_Linea = po.id_package_linea '
                . 'WHERE pl.date_sal>="'.date("Ymd").'" AND pl.id_package="' . $id_package .'" AND po.id_origin="' . $id_origin . '"';

        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS($sql);
        //var_dump($sql);exit();
        foreach ($result as $row) {
            //var_dump(self::getLineasByDeparturePackage($row['departure_id'], $row['id_package']));

            $fecha_sal_letra = self::_obtenerFechaEnLetra($row['date_sal']);
            $fecha_sal_letra2 = self::_obtenerFechaEnLetra2($row['date_sal']);
            $fecha_lle_letra = self::_obtenerFechaEnLetra($row['date_lle']);
            $row['date_sal_letra'] = $fecha_sal_letra;
            $row['date_sal_resumen'] = $fecha_sal_letra2;
            $row['date_lle_letra'] = $fecha_lle_letra;
            $row['services']= ViajeroPaqueteLineaServicios::getServicesByLinea($row['id_package_Linea']);
            $row['inventario_api'] =  self::getLineasByDeparturePackage($row['departure_id'], $row['id_package']) ? self::getLineasByDeparturePackage($row['departure_id'], $row['id_package'])['CupoTransporte'] : '';

            $lines[] = $row;
        }
        
        return $lines;
    }

    public static function getDestinysByLine($id_line) {
        $destinys = array();
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
        SELECT d.* FROM `' . _DB_PREFIX_ . 'inv_destinations` d
        JOIN `' . _DB_PREFIX_ . 'inv_hotels` h ON d.id_destiny = h.id_destiny
        JOIN `' . _DB_PREFIX_ . 'inv_rooms` r ON h.id_hotel = r.id_hotel
        JOIN `' . _DB_PREFIX_ . 'inv_packages_rooms` pr ON pr.id_room = r.id_room
        WHERE pr.id_package_Linea = "'.$id_line.'" GROUP BY d.id_destiny');
        
        foreach ($result as $row) {
            $destinys[] = $row;
        }
//        var_dump($destinys);exit();
        return $destinys;
    }

    public static function getHotelsByDestinyLine($id_line, $id_destiny, $filterData = null) {
        $data_return = [];
        
        $data_hotels = [];
        $data_rooms = [];

        $id_hotels = [];
        $id_rooms = [];

        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
        SELECT
        r.id_room as r_id_room,
        r.id_hotel as r_id_hotel,
        r.name as r_name,
        r.cant as capacidad,
        pr.cant as r_cant,
        r.tok as r_tok,
        r.observations as r_observations,
        h.id_hotel as h_id_hotel,
        h.id_destiny as h_id_destiny,
        h.name as h_name,
        h.latitud as h_latitud,
        h.longitud as h_longitud,
        h.stars as h_stars,
        h.description as h_description,
        h.tok as h_tok,
        h.limninos as h_limninos,
        h.limbebes as h_limbebes,
        pr.id_package_room as pr_id_room,
        pr.cant as pr_cant,
        pr.priceninos as pr_priceninos,
        pricebebes as pr_pricebebes,
        pr.price as pr_price,
        pr.inventario as pr_inventario
        FROM `' . _DB_PREFIX_ . 'inv_destinations` d
        JOIN `' . _DB_PREFIX_ . 'inv_hotels` h ON d.id_destiny = h.id_destiny
        JOIN `' . _DB_PREFIX_ . 'inv_rooms` r ON h.id_hotel = r.id_hotel
        JOIN `' . _DB_PREFIX_ . 'inv_packages_rooms` pr ON pr.id_room = r.id_room
        WHERE pr.id_package_Linea = "'.$id_line.'"
        AND d.id_destiny = "'.$id_destiny.'"
        ');
        
        $cant_cupos = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('SELECT cupos_room FROM `' . _DB_PREFIX_ . 'inv_packages_linea` WHERE id_package_Linea = "'.$id_line.'"');
        
        foreach ($result as $row) {
            if (!array_key_exists($row['h_id_hotel'], $data_hotels)) {
                $data_hotel = [
                    'id_hotel' => $row['h_id_hotel'],
                    'id_destiny' => $row['h_id_destiny'],
                    'name' => $row['h_name'],
                    'latitud' => $row['h_latitud'],
                    'longitud' => $row['h_longitud'],
                    'stars' => $row['h_stars'],
                    'description' => $row['h_description'],
                    'tok' => $row['h_tok'],
                    'limninos' => $row['h_limninos'],
                    'limbebes' => $row['h_limbebes'],
                    'rooms' => [],
                    'photos' => []
                ];

                $id_hotels[] = $row['h_id_hotel'];
                $data_hotels[$row['h_id_hotel']] = $data_hotel;
            }

            if (!array_key_exists($row['r_id_room'], $data_rooms)) {
                $data_room = [
                    'id_room' => $row['r_id_room'],
                    'id_hotel' => $row['r_id_hotel'],
                    'name' => $row['r_name'],
                    'rcant' => $row['r_cant'],
                    'capacidad' => $row['capacidad'],
                    'cupos_room' => $cant_cupos,
                    'tok' => $row['r_tok'],
                    'observations' => $row['r_observations'],
                    'cant' => $row['pr_cant'],
                    'priceninos' => $row['pr_priceninos'],
                    'pricebebes' => $row['pr_pricebebes'],
                    'price' => $row['pr_price'],
                    'inventario' => $row['pr_inventario'],
                    'pr_id_room' => $row['pr_id_room'],
                    'photos' => []
                ];

                $id_rooms[] = $row['r_id_room'];
                $data_rooms[$row['r_id_room']] = $data_room;
            }
        }

        $photos_hotels = ViajeroHotelsphotos::getPhotosByHotels($id_hotels);
        if (count($photos_hotels) > 0) {
            foreach ($photos_hotels as $photos) {
                $data_hotels[$photos['id_hotel']]['photos'][] = $photos;
            }
        }

        $data_room = array_values($data_room);
        $data_rooms_hotels = [];
        foreach ($data_rooms as $room) {
            $data_rooms_hotels[$room['id_hotel']]['rooms'][] = $room;
            // $data_hotels[$room['id_hotel']]['rooms'][] = $room;
        }

        //Sebastian Leiva
        foreach ($data_rooms_hotels as $id_hotel => $hotel) {
            $rooms = $hotel['rooms'];

            // Order group rooms
            $groups_rooms = self::_getRoomsCombinations($rooms, count($filterData));
            $groups_rooms = self::_getGroupRoomsFilter($groups_rooms, $filterData);

            $data_hotels[$id_hotel]['group_rooms'] = $groups_rooms;
        }
        //Sebastian Leiva

        $data_hotels = array_values($data_hotels);
        return $data_hotels;
    }
    
    public static function _obtenerFechaEnLetra($fecha) {
        $dia = self::_conocerDiaSemanaFecha($fecha);
        $num = date("j", strtotime($fecha));
        $anno = date("Y", strtotime($fecha));
        $mes = array('enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio', 'julio', 'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre');
        $mes = $mes[(date('m', strtotime($fecha)) * 1) - 1];
        return $dia . ', ' . $num . ' de ' . $mes ;
    }
    public static function _obtenerFechaEnLetra2($fecha) {
        $dia = self::_conocerDiaSemanaFecha($fecha);
        $num = date("j", strtotime($fecha));
        $anno = date("Y", strtotime($fecha));
        $mes = array('ene', 'feb', 'mar', 'abr', 'may', 'jun', 'jul', 'ago', 'sep', 'oct', 'nov', 'dic');
        $mes = $mes[(date('m', strtotime($fecha)) * 1) - 1];
        return  $num . ' de ' . $mes .','.$anno;
    }

    public static function _conocerDiaSemanaFecha($fecha) {
        $dias = array('Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado');
        $dia = $dias[date('w', strtotime($fecha))];
        return $dia;
    }


    /**
     * Get all combinations of rooms without repeating
     *
     * @param  array $rooms
     * @param  int $length
     * @return array
     */
    public static function _getRoomsCombinationsUnique($rooms, $length)
    {
        $rooms_groups = [[]];
        $rooms = array_reverse($rooms);

        foreach ($rooms as $room) {
            foreach ($rooms_groups as $combination) {
                $room_insert = array_merge([$room], $combination);
                if (count($room_insert) <= $length) {
                    $rooms_groups[] = $room_insert;
                }
            }
        }

        $result_groups = [];
        foreach ($rooms_groups as $rooms_group) {
            if (count($rooms_group) == $length) {
                $result_groups[] = $rooms_group;
            }
        }

        return $rooms_groups;
    }

    /**
     * Get all combinations
     *
     * @param  array $rooms
     * @param  int $length
     * @return array
     */
    public static function _getRoomsCombinations($rooms, $length)
    {
        $rooms_groups = [];
        foreach ($rooms as $room) {
            $sub_group = [];
            if ($length > 1) {
                $sub_group = self::_getRoomsCombinations($rooms, $length - 1);
                foreach ($sub_group as $room_group) {
                    $room_group[] = $room;
                    $rooms_groups[] = $room_group;
                }
            } else {
                $rooms_groups[] = [$room];
            }
        }

        return $rooms_groups;
    }


    /**
     * Get only room groups that meet the number of adults selected
     *
     * @param  array $groups_rooms
     * @param  array $filter_data
     * @return array
     */
    public static function _getGroupRoomsFilter($groups_rooms, $filter_data)
    {
        $filtered_groups = [];
        $groups_id = [];

        foreach ($groups_rooms as $group_rooms) {
            $selected_rooms = [];
            $current_group_id = [];

            foreach ($filter_data as $filter_room) {

                foreach ($group_rooms as $index_room => $room) {

                    if (!in_array($index_room, $selected_rooms)) {

                        if ($room['capacidad'] == $filter_room['adults']) {
                            $selected_rooms[] = $index_room;
                            $current_group_id[] = $room['id_room'];
                            break;
                        }
                    }
                }
            }

            if (count($selected_rooms) == count($filter_data)) {
                $group_valid = true;

                sort($current_group_id);
                $current_group_id = implode(',', $current_group_id);
                foreach ($groups_id as $group_id) {
                    if ($current_group_id == $group_id) {
                        $group_valid = false;
                    }
                }

                if ($group_valid) {
                    $filtered_groups[] = $group_rooms;
                    $groups_id[] = $current_group_id;
                }
            }
        }

        $data_groups = [];
        foreach ($filtered_groups as $filtered_group) {
            $data_group = [];
            foreach ($filtered_group as $room) {
                if (array_key_exists($room['id_room'], $data_group)) {
                    $data_group[$room['id_room']]['cantidad']++;
                } else {
                    $room['cantidad'] = 1;
                    $data_group[$room['id_room']] = $room;
                }
            }

            $data_group = array_values($data_group);
            $data_groups[] = $data_group;
        }

        return $data_groups;
    }

}
