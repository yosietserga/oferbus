<?php
/**
* 2007-2018 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Open Software License (OSL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/osl-3.0.php
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
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
**/

class ViajeroRooms extends ObjectModel
{
              
    public $id_room;
    public $id_hotel;
    public $cant;
    public $name;
    public $price;
    public $tok;
    public $observations;
    public $date_add;
    public $date_upd;
    public $id_room_api;
    public $categoria_habitacion_id;
    public $tipo_habitacion_id;
    public $regimen_id;
    
    public static $definition = array(
        'table' => 'inv_rooms',
        'primary' => 'id_room',
        'fields' => array(
            'id_room' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'id_hotel' => array('type' => self::TYPE_INT, 'required' => true),
            'cant' => array('type' => self::TYPE_INT, 'required' => true),
            'tok' => array('type' => self::TYPE_STRING, 'required' => true),
            'name' => array('type' => self::TYPE_STRING, 'size' => 200, 'required' => true),
            'observations' => array('type' => self::TYPE_HTML, 'validate' => 'isCleanHtml', 'size' => 3999999999999),
            'date_add' => array('type' => self::TYPE_DATE, 'validate' => 'isDate', 'copy_post' => false),
            'date_upd' => array('type' => self::TYPE_DATE, 'validate' => 'isDate', 'copy_post' => false),
            'id_room_api' => array('type' => self::TYPE_INT),
            'categoria_habitacion_id' => array('type' => self::TYPE_INT),
            'tipo_habitacion_id' => array('type' => self::TYPE_INT),
            'regimen_id' => array('type' => self::TYPE_INT),
        ),
    );

    public static function getRooms()
    {
        $rooms = array();
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
		SELECT * FROM `' . _DB_PREFIX_ . 'inv_rooms` D 						
		ORDER BY D.id_room ASC');
        foreach ($result as $row) {
            $rooms[$row['id_room']] = $row;
        }
        return $rooms;
    }
    
    public static function getRoomsByHotel($id, $id_package)
    {
        $packages = array();
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
        SELECT D.*
                FROM `' . _DB_PREFIX_ . 'inv_rooms` D 
            WHERE D.id_hotel='.$id.'
        ORDER BY D.name ASC');
        foreach($result as $room) {
            $cupos_room = Db::getInstance()->getValue('SELECT PR.cant as cant 
                                                            FROM `'._DB_PREFIX_ .'inv_rooms` D 
                                                        LEFT JOIN `'._DB_PREFIX_.'inv_packages_rooms` AS PR ON PR.id_room = D.id_room 
                                                        WHERE '.($id_package > 0 ? 'PR.id_package = '.$id_package.' AND ' : '').'D.id_hotel = '.$id);
            $room['cant'] = (int)$cupos_room;
            array_push($packages, $room);
        }
        
        return $packages;
    }
    public static function getRoomsByHotelPack($id)
    {
          $rooms = array();
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
        SELECT PR.id_package as id_package, D.id_room as id_room, D.name as name,
               PR.cant as cant, D.observations as observations, PR.cant as cantperso 
            FROM `' . _DB_PREFIX_ . 'inv_rooms` D 
            LEFT JOIN `' . _DB_PREFIX_ . 'inv_packages_rooms` AS PR ON PR.id_room = D.id_room 
            WHERE D.id_hotel='.$id);
        foreach ($result as $row) {
            $rooms[$row['id_room']] = $row;
        }
//        var_dump($result);exit();
        return $rooms;
    }
    public static function getRoom($id)
    {
          $rooms = array();
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
		SELECT * FROM `' . _DB_PREFIX_ . 'inv_rooms` D 
                WHERE D.id_room='.$id);
        foreach ($result as $row) {
            $rooms[$row['id_room']] = $row;
        }
        return $rooms;
    }

    public static function getIdHotelbyRoom($id) 
    {
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('
        SELECT D.id_hotel FROM `' . _DB_PREFIX_ . 'inv_rooms` D 
            LEFT JOIN `' . _DB_PREFIX_ . 'inv_packages_rooms` AS PR ON PR.id_room = D.id_room 
        WHERE PR.id_package_room='.$id);
        return $result;
    } 

    public static function getRoomByName($hotel_id, $name) 
    {
        $sql = 'SELECT * FROM `' . _DB_PREFIX_ . 'inv_rooms` D WHERE D.name="'.$name.'" AND D.id_hotel = "'.$hotel_id.'" ORDER BY D.name ASC';
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS($sql);
        return $result;
    } 


}
