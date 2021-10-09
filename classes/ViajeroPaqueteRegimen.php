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

class ViajeroPaqueteRegimen extends ObjectModel
{
    
    public $id_package_regimen;
    public $id_package;
    public $id_hotel;
    public $id_attribute;
    public $id_product_attribute_des;
    public $id_product_attribute_med;
    public $id_product_attribute_com;
    public $desayuno;   
    public $media;   
    public $completa;   
    public $limninos;   
    public $limbebes;   
    public $date_add;
    public $date_upd;
    
    public static $definition = array(
        'table' => 'inv_packages_regimens',
        'primary' => 'id_package_regimen',
        'fields' => array(
            'id_package_regimen' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'id_package' => array('type' => self::TYPE_INT, 'required' => true),
            'id_hotel' => array('type' => self::TYPE_INT, 'required' => true),
            'id_product_attribute_des' => array('type' => self::TYPE_INT),
            'id_product_attribute_med' => array('type' => self::TYPE_INT),
            'id_product_attribute_com' => array('type' => self::TYPE_INT),
            'desayuno' => array('type' => self::TYPE_FLOAT),
            'media' => array('type' => self::TYPE_FLOAT),
            'completa' => array('type' => self::TYPE_FLOAT),
            'limninos' => array('type' => self::TYPE_INT),
            'limbebes' => array('type' => self::TYPE_INT),
            'date_add' => array('type' => self::TYPE_DATE, 'validate' => 'isDate', 'copy_post' => false),
            'date_upd' => array('type' => self::TYPE_DATE, 'validate' => 'isDate', 'copy_post' => false),
        ),
    );

    public static function getRegimensBypackage($id) {
        $regimen = array();
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
		SELECT * FROM `' . _DB_PREFIX_ . 'inv_packages_regimens` D where id_package=' . $id .
                ' ORDER BY D.id_package_regimen ASC');
        foreach ($result as $row) {
            $regimen[$row['id_package_regimen']] = $row;
        }
        return $regimen;
    }

    public static function getRegimen($id){
        $regimen = array();
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
        SELECT * FROM `' . _DB_PREFIX_ . 'inv_packages_regimens` where id_hotel=' . $id);
        foreach ($result as $row) {
            $regimen[$row['id_hotel']] = $row;
        }
        return $regimen;
    }

    public static function getPrice($id, $attr){
        $regimen = array();
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
        SELECT '.$attr.','.$id.' FROM `' . _DB_PREFIX_ . 'inv_packages_regimens` where id_package_regimen=' . $id);

        foreach ($result as $row) {
            $regimen[$row[''.$id.'']] = $row;
        }
        return $regimen;
    }       
}
