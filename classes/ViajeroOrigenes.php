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

class ViajeroOrigenes extends ObjectModel
{
    
    public $id_origen;
    public $origen;
    public $latitud;
    public $longitud;
    public $date_add;
    public $date_upd;
    public $id_provincia;
    public $id_origin_api;
    
    public static $definition = array(
        'table' => 'inv_origins',
        'primary' => 'id_origen',
        'fields' => array(
            'id_origen' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'origen' => array('type' => self::TYPE_STRING, 'size' => 200, 'required' => true),
            'latitud' => array('type' => self::TYPE_STRING, 'size' => 200, 'required' => true),
            'longitud' => array('type' => self::TYPE_STRING, 'size' => 200, 'required' => true),
            'date_add' => array('type' => self::TYPE_DATE, 'validate' => 'isDate', 'copy_post' => false),
            'date_upd' => array('type' => self::TYPE_DATE, 'validate' => 'isDate', 'copy_post' => false),
            'id_provincia' => array('type' => self::TYPE_INT),
            'id_origin_api' => array('type' => self::TYPE_INT),
        ),
    );

    public static function getOrigins()
    {
        $origenes = array();
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
		SELECT * FROM `' . _DB_PREFIX_ . 'inv_origins` D 						
		ORDER BY D.origen ASC');
        foreach ($result as $row) {
            $origenes[$row['id_origen']] = $row;
        }
        return $origenes;
    }
    
    public static function getOriginsByDestiny($id)
    {
        $hotels = array();
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
		SELECT * FROM `' . _DB_PREFIX_ . 'inv_origins` D 
                WHERE D.id_destiny='.$id.'
		ORDER BY D.origen ASC');
        foreach ($result as $row) {
            $hotels[$row['id_origen']] = $row;
        }
        return $hotels;
    }

    public static function getOriginsByName($name)
    {
        $sql = 'SELECT * FROM `' . _DB_PREFIX_ . 'inv_origins` D WHERE D.origen="'.$name.'" ORDER BY D.origen ASC';
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS($sql);
        return $result;
    }
}
