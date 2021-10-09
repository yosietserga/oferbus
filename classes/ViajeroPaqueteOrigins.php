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

class ViajeroPaqueteOrigins extends ObjectModel
{
    
    public $id_package_origin;
    public $id_package;
    public $id_origin;
    public $id_package_linea;
    public $price;   
    public $date_add;
    public $date_upd;
    
    public static $definition = array(
        'table' => 'inv_packages_origins',
        'primary' => 'id_package_origin',
        'fields' => array(
            'id_package_origin' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'id_package' => array('type' => self::TYPE_INT, 'required' => true),
            'id_origin' => array('type' => self::TYPE_INT, 'required' => true), 
            'id_package_linea' => array('type' => self::TYPE_INT),
            'price' => array('type' => self::TYPE_FLOAT, 'required' => true),
            'date_add' => array('type' => self::TYPE_DATE, 'validate' => 'isDate', 'copy_post' => false),
            'date_upd' => array('type' => self::TYPE_DATE, 'validate' => 'isDate', 'copy_post' => false),
        ),
    );

    public static function getOriginssBypackage($id) {
        $origins = array();
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
		SELECT * FROM `' . _DB_PREFIX_ . 'inv_packages_origins` D INNER JOIN `' . _DB_PREFIX_ . 'inv_origins` O where D.id_origin=O.id_origen AND id_package=' . $id .
                ' GROUP BY D.id_origin ORDER BY O.origen ASC');
        foreach ($result as $row) {
            $origins[$row['id_package_origin']] = $row;
        }
//        var_dump($aditionals);exit();
        return $origins;
    }
    public static function getOriginsByLinea($id) {
        $origins = array();
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
		SELECT * FROM `' . _DB_PREFIX_ . 'inv_packages_origins` D INNER JOIN `' . _DB_PREFIX_ . 'inv_origins` O'.
                ' ON D.id_origin=O.id_origen where id_package_linea=' . $id .                
                ' ORDER BY O.origen ASC');
        foreach ($result as $row) {
            $origins[$row['id_package_origin']] = $row;
        }
        return $origins;
    }

    public static function getOriginPrice($id){
        $price = array();
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
        SELECT * FROM `' . _DB_PREFIX_ . 'inv_packages_origins` where id_package_origin='.$id.'');
        foreach ($result as $row) {
            $price[$row['id_package_origin']] = $row;
        }
//        var_dump($aditionals);exit();
        return $price;
    }    
    public static function getOriginsByDestinySelect2($package,$destiny){
        
        $price = array();
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
        SELECT PR.*,O.* FROM `' . _DB_PREFIX_ . 'inv_packages_origins` PR'
                . ' Left join `' . _DB_PREFIX_ . 'inv_origins` AS O  ON PR.id_origin=O.id_origen'
                . ' inner join `' . _DB_PREFIX_ . 'inv_packages_destinos` AS PD  ON PD.id_package_destino=PR.id_package_destino'
                . ' where PR.id_package='.$package.' and PD.id_destiny='.$destiny);
        foreach ($result as $row) {
            $price[$row['id_package_origin']] = $row;
        }
//        var_dump($price);exit();
        return $price;
    }    
    public static function getOriginsByDestinySelect($package,$destiny){
        
        $price = array();
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
        SELECT PR.*,O.* FROM `' . _DB_PREFIX_ . 'inv_packages_origins` PR'
                . ' Left join `' . _DB_PREFIX_ . 'inv_origins` AS O  ON PR.id_origin=O.id_origen'
                . ' inner join `' . _DB_PREFIX_ . 'inv_packages_destinos` AS PD  ON PD.id_package_destino=PR.id_package_destino'
                . ' where PR.id_package='.$package.' and PD.id_package_destino='.$destiny);
        foreach ($result as $row) {
            $price[$row['id_package_origin']] = $row;
        }
//        var_dump($price);exit();
        return $price;
    }

    public static function deleteAllOriginPackage($id)
    {
         $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('DELETE FROM pr_inv_packages_origins WHERE id_package='.$id);

        return $result;
    }
    
}
