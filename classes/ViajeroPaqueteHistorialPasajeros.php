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

class ViajeroPaqueteHistorialPasajeros extends ObjectModel
{
  
    public $id_package_historial_pasajero;
    public $id_package_historial;
    public $nombre;
    public $apellido;
    public $dni;
	public $tipo_doc;
    public $sexo;
    public $fecha_nacimiento;
    public $telefono;
    public $date_add;
    public $date_upd;
    
    public static $definition = array(
        'table' => 'inv_packages_historiales_pasajeros',
        'primary' => 'id_package_historial_pasajero',
        'fields' => array(
            'id_package_historial_pasajero' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),            
            'id_package_historial' => array('type' => self::TYPE_INT, 'required' => true),
            'nombre' => array('type' => self::TYPE_STRING, 'required' => true),        
            'apellido' => array('type' => self::TYPE_STRING, 'required' => true),        
            'telefono' => array('type' => self::TYPE_STRING, 'required' => true),      
            'fecha_nacimiento' => array('type' => self::TYPE_STRING, 'required' => true),
            'sexo' => array('type' => self::TYPE_STRING, 'required' => true),       
			'tipo_doc' => array('type' => self::TYPE_STRING, 'required' => true),        			
            'dni' => array('type' => self::TYPE_STRING, 'required' => true),        
            'date_add' => array('type' => self::TYPE_DATE, 'validate' => 'isDate', 'copy_post' => false),
            'date_upd' => array('type' => self::TYPE_DATE, 'validate' => 'isDate', 'copy_post' => false),
        ),
    );

    public static function getHistorialPasajeros()
    {
        $historial = array();
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
        SELECT * FROM `' . _DB_PREFIX_ . 'inv_packages_historiales_pasajeros` D                       
        ORDER BY id_package_historial_pasajero ASC');
        foreach ($result as $row) {
            $historial[$row['id_package_historial_pasajero']] = $row;
        }
        return $historial;
    }

    public static function getHistorialPasajerosByhistorial($id)
    {
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
        SELECT * FROM `' . _DB_PREFIX_ . 'inv_packages_historiales_pasajeros` cp                       
        where id_package_historial='.$id);        
        return $result;
    }

}