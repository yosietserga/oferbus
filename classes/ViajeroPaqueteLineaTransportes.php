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

class ViajeroPaqueteLineaTransportes extends ObjectModel
{             
    public $id_packages_linea_transporte;
    public $id_package_linea;
    public $id_package;
    public $name;
    public $tipo_servicio_id;
    public $transporte_id;
    public $tipo_butaca_id;
    public $tipo_cupo_id;
    public $date_add;
    public $date_upd;
    
    public static $definition = array(
        'table' => 'inv_packages_linea_transportes',
        'primary' => 'id_packages_linea_transporte',
        'fields' => array(
            'id_packages_linea_transporte' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'id_package_linea' => array('type' => self::TYPE_INT, 'required' => true),            
            'id_package' => array('type' => self::TYPE_INT, 'required' => true),            
            'name' =>array('type' => self::TYPE_STRING),
            'tipo_servicio_id' => array('type' => self::TYPE_INT),
            'transporte_id' => array('type' => self::TYPE_INT),
            'tipo_butaca_id' => array('type' => self::TYPE_INT),
            'tipo_cupo_id' => array('type' => self::TYPE_INT),
            'cant_cupo_butaca' => array('type' => self::TYPE_INT),
            'date_add' => array('type' => self::TYPE_DATE, 'validate' => 'isDate', 'copy_post' => false),
            'date_upd' => array('type' => self::TYPE_DATE, 'validate' => 'isDate', 'copy_post' => false),
        ),
    );   

    public static function getTransportsByLine($id) {
      $sql = 'SELECT * FROM `' . _DB_PREFIX_ . 'inv_packages_linea_transportes`  where id_package_linea=' . $id;

      $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS($sql);
      
      $transport = $result[0];
      return $transport;
    }

    public static function getTransportsByPackage($id) {
        $sql = 'SELECT * FROM `' . _DB_PREFIX_ . 'inv_packages_linea_transportes`  where id_package=' . $id;

        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS($sql);

        foreach ($result as $row) {
            $types_seats[$row['id_packages_linea_transporte']] = $row;
        }
        return $types_seats;
    }

}
