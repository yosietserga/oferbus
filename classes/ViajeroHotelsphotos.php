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

class ViajeroHotelsphotos extends ObjectModel
{
    
    public $id_hotel_photos;
    public $id_hotel;
    public $url;
    public $date_add;
    public $date_upd;
    
    public static $definition = array(
        'table' => 'inv_hotels_photos',
        'primary' => 'id_hotel_photo',
        'fields' => array(
            'id_hotel_photo' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'id_hotel' => array('type' => self::TYPE_INT, 'required' => true),
            'url' => array('type' => self::TYPE_STRING, 'size' => 300, 'required' => true),
            'date_add' => array('type' => self::TYPE_DATE, 'validate' => 'isDate', 'copy_post' => false),
            'date_upd' => array('type' => self::TYPE_DATE, 'validate' => 'isDate', 'copy_post' => false),
        ),
    );

    public static function getPhotosByHotel($id)
    {
        $hotels = array();
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
		SELECT * FROM `' . _DB_PREFIX_ . 'inv_hotels_photos` D 
                WHERE D.id_hotel='.$id.'
		ORDER BY D.id_hotel_photo ASC');
        foreach ($result as $row) {
            $hotels[$row['id_hotel_photo']] = $row;
        }
        return $hotels;
    }

    public static function getPhotosByHotels($ids)
    {
        $ids = implode('", "', $ids);

        $photos = array();
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
		SELECT * FROM `' . _DB_PREFIX_ . 'inv_hotels_photos` D 
                WHERE D.id_hotel IN ("'.$ids.'")
		ORDER BY D.id_hotel_photo ASC');
        foreach ($result as $row) {
            $photos[$row['id_hotel_photo']] = $row;
        }
        return $photos;
    }
}
