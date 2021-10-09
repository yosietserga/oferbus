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

class ViajeroPaqueteLineaServicios extends ObjectModel
{             
    public $id_packages_linea_servicio;
    public $id_package_linea;
    public $id_package;
    public $name;
    public $description;
    public $date_add;
    public $date_upd;
    
    public static $definition = array(
        'table' => 'inv_packages_linea_servicios',
        'primary' => 'id_packages_linea_servicio',
        'fields' => array(
            'id_packages_linea_servicio' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'id_package_linea' => array('type' => self::TYPE_INT, 'required' => true),            
            'id_package' => array('type' => self::TYPE_INT, 'required' => true),            
            'name' =>array('type' => self::TYPE_STRING),
            'description' =>array('type' => self::TYPE_STRING),
            'date_add' => array('type' => self::TYPE_DATE, 'validate' => 'isDate', 'copy_post' => false),
            'date_upd' => array('type' => self::TYPE_DATE, 'validate' => 'isDate', 'copy_post' => false),
        ),

    );   
    
    public static function getServicesByPackage($id) {
        $seats = array();
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
		SELECT * FROM `' . _DB_PREFIX_ . 'inv_packages_linea_servicios` D where id_package=' . $id .
                ' ORDER BY D.id_packages_linea_servicio ASC');
        foreach ($result as $row) {
            $seats[$row['id_packages_linea_servicio']] = $row;
        }
//        var_dump($seats);exit();
        return $seats;
    }

    
     public static function getServicesByLinea($id) {
        $services = array();
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
		SELECT * FROM `' . _DB_PREFIX_ . 'inv_packages_linea_servicios`  where id_package_linea=' . $id );
        foreach ($result as $row) {
            $services[$row['name']] = $row;
        }
//        var_dump($origins);exit();
        return $services;
    }
}
