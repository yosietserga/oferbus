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
 * */
class ViajeroPaqueteRooms extends ObjectModel {

    public $id_package_room;
    public $id_package;
    public $id_room;
    public $id_package_Linea;
    public $cant;
    public $srv_alojamiento_id;
    public $priceninos;
    public $pricebebes;
//    public $pricedestino;
    public $price;
    public $date_add;
    public $date_upd;
    public static $definition = array(
        'table' => 'inv_packages_rooms',
        'primary' => 'id_package_room',
        'fields' => array(
            'id_package_room' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'id_package' => array('type' => self::TYPE_INT, 'required' => true),
            'id_room' => array('type' => self::TYPE_INT, 'required' => true),
            'id_package_Linea' => array('type' => self::TYPE_INT, 'required' => true),
            'cant' => array('type' => self::TYPE_INT, 'required' => true),
            'priceninos' => array('type' => self::TYPE_FLOAT),
            'pricebebes' => array('type' => self::TYPE_FLOAT),
            'srv_alojamiento_id' => array('type' => self::TYPE_INT),
//            'pricedestino' => array('type' => self::TYPE_FLOAT),
            'price' => array('type' => self::TYPE_FLOAT),
            'date_add' => array('type' => self::TYPE_DATE, 'validate' => 'isDate', 'copy_post' => false),
            'date_upd' => array('type' => self::TYPE_DATE, 'validate' => 'isDate', 'copy_post' => false),
        ),
    );

    public static function getRoomsPack($room, $linea) {
        $rooms = array();
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
		SELECT * FROM `' . _DB_PREFIX_ . 'inv_packages_rooms` D 
                WHERE D.id_room=' . $room . ' and D.id_package_linea=' . $linea);
        if (count($result) == 0) {
            return 0;
        } else {
            return $result[0];
        }
    }
    public static function getHotelsSelects($destiny,$id){
         $roomspak = array();
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
		SELECT * FROM `' . _DB_PREFIX_ . 'inv_packages_rooms` PR '
                . ' inner join `' . _DB_PREFIX_ . 'inv_rooms` AS R  ON PR.id_room=R.id_room'
                . ' inner join `' . _DB_PREFIX_ . 'inv_hotels` AS H ON R.id_hotel=H.id_hotel'
                . ' where PR.id_package=' . $id . ' and H.id_destiny='.$destiny.
                ' ORDER BY PR.id_package_room ASC');
//        var_dump($result);exit();
        foreach ($result as $row) {
            $roomspak[$row['id_hotel']] = $row;
            $regimen = ViajeroPaqueteRegimen::getRegimen($row['id_hotel']);
            array_push($roomspak[$row['id_hotel']],array('regimen'=>$regimen));          
        }        
//        var_dump($roomspak);exit();
        return $roomspak;
    }

    public static function getRoomsBypackage($id) {
        $roomspak = array();
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
		SELECT D.id_destiny as id_destiny ,D.destiny as destiny,H.id_hotel as id_hotel, H.name as namehotel ,PR.date_sal as salida,PR.date_lle as llegada , PR.id_package_room as id_packroom, PR.cant as cantperso,PR.price as priceroom,R.id_room as id_room,R.name as room,PR.price as priceroom,R.observations as obserroom, R.cant as cant,PR.priceninos as priceninos,PR.pricebebes as pricebebes FROM `' . _DB_PREFIX_ . 'inv_packages_rooms` PR '
                . ' inner join `' . _DB_PREFIX_ . 'inv_rooms` AS R  ON PR.id_room=R.id_room'
                . ' inner join `' . _DB_PREFIX_ . 'inv_hotels` AS H ON R.id_hotel=H.id_hotel'
                . ' inner join `' . _DB_PREFIX_ . 'inv_destinations` AS D ON H.id_destiny=D.id_destiny'
                . ' where PR.id_package=' . $id .
                ' ORDER BY PR.id_package_room ASC');

        foreach ($result as $row) {
            $roomspak[$row['id_packroom']] = $row;
        }
        return $roomspak;
    }
    public static function getRoomsBypackagess($id) {
        $roomspak = array();
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
		SELECT * FROM `' . _DB_PREFIX_ . 'inv_packages_rooms` PR '
                . ' where PR.id_package=' . $id .
                ' ORDER BY PR.id_package_room ASC');

    
        return $result;
    }

    public static function getDestinysBypackage($id) {
        $roomspak = array();
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
		SELECT DISTINCT PD.price as pricedestiny, D.id_destiny,D.destiny,PR.date_sal as salida,PR.date_lle as llegada FROM `' . _DB_PREFIX_ . 'inv_packages_rooms` PR '
                . ' inner join `' . _DB_PREFIX_ . 'inv_rooms` AS R  ON PR.id_room=R.id_room'
                . ' inner join `' . _DB_PREFIX_ . 'inv_hotels` AS H ON R.id_hotel=H.id_hotel'
                . ' inner join `' . _DB_PREFIX_ . 'inv_destinations` AS D ON H.id_destiny=D.id_destiny'
                . ' inner join `' . _DB_PREFIX_ . 'inv_packages_destinos` AS PD ON PD.id_destiny=PD.id_destiny'
                . ' where PR.id_package=' . $id);
        foreach ($result as $row) {
            $roomspak[$row['id_destiny']] = $row;
        }
        return $roomspak;
    }
    public static function getDestinysBypackage2($id) {
        $roomspak = array();
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
		SELECT  distinct PD.inventario, PD.price as pricedestiny,PD.description as descrip,PD.impuesto as impuesto, D.id_destiny,D.destiny,PD.date_sal as salida,PD.date_lle as llegada FROM `' . _DB_PREFIX_ . 'inv_packages_destinos` PD '                                                
                . ' left join `' . _DB_PREFIX_ . 'inv_destinations` AS D ON PD.id_destiny=D.id_destiny'
//                . ' inner join `' . _DB_PREFIX_ . 'inv_origins` AS O ON O.id_destiny=H.id_destiny'
                . ' where PD.id_package=' . $id  );
//        var_dump($result);exit();
        foreach ($result as $row) {
//            var_dump($row['id_origennn']);
            $roomspak[$row['id_destiny']] = $row;
            $hotels = ViajeroPaqueteRooms::getHotelsSelects($row['id_destiny'],$id);           
//            $originsSelect = ViajeroPaqueteOrigins::getOriginsByDestinySelect2($id,$row['packagedestiny']);
            array_push($roomspak[$row['id_destiny']],array('hotels'=>$hotels)); 
            array_push($roomspak[$row['id_destiny']],array('origins'=>$origins)); 
//            array_push($roomspak[$row['id_destiny']],array('originsselect'=>$originsSelect)); 
        }
//        exit();
        return $roomspak;
    }

    public static function getHotelsBypackage($id) {
        $roomspak = array();
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
		SELECT DISTINCT RG.desayuno AS desayuno,RG.media as media, RG.completa as completa,H.id_destiny as id_destiny, H.id_hotel as id_hotel,H.name as namehotel,PR.date_sal as salida,PR.date_lle as llegada FROM `' . _DB_PREFIX_ . 'inv_packages_rooms` PR '
                . ' inner join `' . _DB_PREFIX_ . 'inv_rooms` AS R  ON PR.id_room=R.id_room'
                . ' inner join `' . _DB_PREFIX_ . 'inv_hotels` AS H ON R.id_hotel=H.id_hotel'
                . ' inner join `' . _DB_PREFIX_ . 'inv_packages_regimens` AS RG ON RG.id_hotel=H.id_hotel'
                . ' where PR.id_package=' . $id);
        foreach ($result as $row) {
            $roomspak[$row['id_hotel']] = $row;
        }
        return $roomspak;
    }

    public static function getPhotosByHotel($hotel) {
        $hotel = (int) $hotel;
        //var_dump($hotel);exit();
        $roomspak = array();
        $consulta = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
		SELECT url, id_hotel_photo , id_hotel FROM `' . _DB_PREFIX_ . 'inv_hotels_photos`
                WHERE id_hotel=' . $hotel);
        foreach ($consulta as $row) {
            $roomspak[$row['id_hotel_photo']] = $row;
        }
        return $roomspak;
    }

    public static function getRoomsByDestiny($id) {
        $roomspak = array();
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
		SELECT PD.*,H.id_hotel as id_hotel,H.name as hotelname, H.stars, H.description FROM ' . _DB_PREFIX_ . 'inv_packages_destinos PD '
                . ' inner join ' . _DB_PREFIX_ . 'inv_hotels AS H  ON H.id_destiny=PD.id_destiny'
                . ' inner join ' . _DB_PREFIX_ . 'inv_packages_rooms AS J  ON J.id_package=PD.id_package'
                . ' where PD.id_package_destino=' . $id );       
        //var_dump($result);exit();
        return $result;
    }
    public static function getHotelsByRoom2($id,$package) {
        $response = array();
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
		SELECT D.*,H.id_hotel as id_hotel,H.name as hotelname, H.stars, H.description FROM ' . _DB_PREFIX_ . 'inv_packages_destinos D '
                . ' inner join ' . _DB_PREFIX_ . 'inv_hotels AS H  ON H.id_destiny=D.id_destiny'
                . ' inner join ' . _DB_PREFIX_ . 'inv_rooms AS R  ON R.id_hotel=H.id_hotel'
                . ' inner join ' . _DB_PREFIX_ . 'inv_packages_rooms AS P  ON P.id_room=R.id_room'
                . ' where D.id_package_destino=' . $id . ' group by H.id_hotel');
//        var_dump($result);exit();
        foreach($result as $key=>$res){
            $exits = ViajeroPaqueteRooms::getRoomsByHotelFront($res['id_hotel'],$package);
            if(!empty($exits)){
                $response[]=$result[$key];
            }                        
        }
//        var_dump($response);exit();
        return $response;
    }
    
    public static function getRoomsByHotelFront($id_hotel,$id_package) {
//        $roomspak = array();
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
		SELECT  R.observations, R.name as nameroom,H.priceninos as priceninos,H.pricebebes as pricebebes,H.id_room,H.price,H.id_package_room,R.id_hotel,R.cant FROM ' . _DB_PREFIX_ . 'inv_rooms R '
                . ' inner join ' . _DB_PREFIX_ . 'inv_packages_rooms AS H  ON R.id_room=H.id_room'
//                . ' left join ' . _DB_PREFIX_ . 'inv_packages_regimens AS J  ON R.id_hotel=J.id_hotel'
                . ' where R.id_hotel=' . $id_hotel . ' and H.id_package='.$id_package );
       //var_dump(count($result));exit();
        return $result;
    }

    public static function getHotelsByRoom($id) {
        $idd = '1';
        $roomspak = array();
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
		SELECT * FROM ' . _DB_PREFIX_ . 'inv_destinations D '
                . ' inner join ' . _DB_PREFIX_ . 'inv_hotels AS H  ON H.id_destiny=D.id_destiny'
                . ' inner join ' . _DB_PREFIX_ . 'inv_rooms AS R  ON R.id_hotel=H.id_hotel'
                . ' inner join ' . _DB_PREFIX_ . 'inv_packages_rooms AS P  ON P.id_room=R.id_room'
                . ' where D.id_destiny=' . $idd .
                ' ORDER BY H.id_hotel ASC');

        foreach ($result as $row) {
            $roomspak[$row['id_destiny']] = $row;
        }

        return $roomspak;
    }

    public static function getInformationRooms($id) {
        $room = array();
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
        SELECT * FROM ' . _DB_PREFIX_ . 'inv_packages_rooms  where id_package_room=' . $id .
                ' ');

        foreach ($result as $row) {
            $room[$row['id_package_room']] = $row;
        }

        return $room;
    }

    public static function getPhotosRoom($id){
        $room = array();
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
        SELECT * FROM ' . _DB_PREFIX_ . 'inv_rooms_photos  where id_room=' . $id .
                ' ');
        foreach ($result as $row) {
            $room[$row['id_room_photo']] = $row;
        }

        return $room;
    }

    

}
