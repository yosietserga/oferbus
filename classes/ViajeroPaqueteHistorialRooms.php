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

class ViajeroPaqueteHistorialRooms extends ObjectModel
{
  
    public $id_package_historial_room;
    public $id_package_historial;
    public $id_package_room;
    public $cant_room;
    public $adults;
    public $children;
    public $babys;
    public $date_add;
    public $date_upd;
    
    public static $definition = array(
        'table' => 'inv_packages_historiales_rooms',
        'primary' => 'id_package_historial_room',
        'fields' => array(
            'id_package_historial_room' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),            
            'id_package_historial' => array('type' => self::TYPE_INT, 'required' => true),
            'id_package_room' => array('type' => self::TYPE_INT, 'required' => true),            
            'cant_room' => array('type' => self::TYPE_INT, 'required' => true),            
            'adults' => array('type' => self::TYPE_INT),
            'children' => array('type' => self::TYPE_INT),
            'babys' => array('type' => self::TYPE_INT),
            'date_add' => array('type' => self::TYPE_DATE, 'validate' => 'isDate', 'copy_post' => false),
            'date_upd' => array('type' => self::TYPE_DATE, 'validate' => 'isDate', 'copy_post' => false),
        ),
    );

    public static function getHistorialRooms()
    {
        $historial = array();
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
        SELECT * FROM `' . _DB_PREFIX_ . 'inv_packages_historiales_rooms` D                       
        ORDER BY id_package_historial_room ASC');
        foreach ($result as $row) {
            $historial[$row['id_package_historial_room']] = $row;
        }
        return $historial;
    }
    public static function getHistorialRoomByhistorial($id)
    {
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
        SELECT * FROM `' . _DB_PREFIX_ . 'inv_packages_historiales_rooms` cp                       
        where id_package_historial='.$id);        
        return $result;
    }

}