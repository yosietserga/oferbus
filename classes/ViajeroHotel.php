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

class ViajeroHotel extends ObjectModel
{

    public $id_hotel;
    public $id_destiny;
    public $name;
    public $latitud;
    public $longitud;
    public $stars;
    public $description;
    public $limninos;
    public $limbebes;
    public $tok;
    public $date_add;
    public $date_upd;
    public $id_hotel_api;
    
    public static $definition = array(
        'table' => 'inv_hotels',
        'primary' => 'id_hotel',
        'fields' => array(
            'id_hotel' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'id_destiny' => array('type' => self::TYPE_INT, 'required' => true),
            'limninos' => array('type' => self::TYPE_INT, 'required' => true),
            'limbebes' => array('type' => self::TYPE_INT, 'required' => true),
            'name' => array('type' => self::TYPE_STRING, 'size' => 200, 'required' => true),
            'latitud' => array('type' => self::TYPE_STRING, 'size' => 200, 'required' => true),
            'longitud' => array('type' => self::TYPE_STRING, 'size' => 200, 'required' => true),
            'stars' => array('type' => self::TYPE_STRING, 'size' => 200, 'required' => true),
            'description' =>array('type' => self::TYPE_HTML, 'validate' => 'isCleanHtml','size' => 3999999999999),
            'tok' => array('type' => self::TYPE_STRING, 'required' => true),
            'date_add' => array('type' => self::TYPE_DATE, 'validate' => 'isDate', 'copy_post' => false),
            'date_upd' => array('type' => self::TYPE_DATE, 'validate' => 'isDate', 'copy_post' => false),
            'id_hotel_api' => array('type' => self::TYPE_INT),
        ),
    );

    public static function getHotels()
    {
        $hotels = array();
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
		SELECT * FROM `' . _DB_PREFIX_ . 'inv_hotels` D 						
		ORDER BY D.name ASC');
        foreach ($result as $row) {
            $hotels[$row['id_hotel']] = $row;
        }
        return $hotels;
    }
    public static function getHotelsByDestiny($id)
    {
        $hotels = array();
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
		SELECT * FROM `' . _DB_PREFIX_ . 'inv_hotels` D 
                WHERE D.id_destiny='.$id.'
		ORDER BY D.name ASC');
        foreach ($result as $row) {
            $hotels[$row['id_hotel']] = $row;
        }
        return $hotels;
    }

    public static function getCoords($id)
    {
        $hotels = array();
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
        SELECT * FROM `' . _DB_PREFIX_ . 'inv_hotels` D 
                WHERE D.id_hotel='.$id.'');
        foreach ($result as $row) {
            $hotels[$row['id_hotel']] = $row;
        }
        return $hotels;
    }

    public static function getHotelByName($destination_id, $name)
    {
        $sql = 'SELECT * FROM `' . _DB_PREFIX_ . 'inv_hotels` D WHERE D.name="'.$name.'" AND D.id_destiny="'.$destination_id.'" ORDER BY D.name ASC';
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS($sql);
        return $result;
    }


    
}
