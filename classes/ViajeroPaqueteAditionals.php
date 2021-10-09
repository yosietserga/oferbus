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

class ViajeroPaqueteAditionals extends ObjectModel
{
    
    public $id_package_aditional;
    public $id_package;
    public $id_aditional;
    public $id_product_attribute;
    public $type;
    public $value;
    public $date_add;
    public $date_upd;
    
    public static $definition = array(
        'table' => 'inv_packages_aditionals',
        'primary' => 'id_package_aditional',
        'fields' => array(
            'id_package_aditional' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'id_package' => array('type' => self::TYPE_INT, 'required' => true),
            'id_aditional' => array('type' => self::TYPE_INT, 'required' => true),
            'id_product_attribute' => array('type' => self::TYPE_INT),
            'type' => array('type' => self::TYPE_STRING, 'required' => true),
            'value' => array('type' => self::TYPE_FLOAT, 'required' => true),
            'date_add' => array('type' => self::TYPE_DATE, 'validate' => 'isDate', 'copy_post' => false),
            'date_upd' => array('type' => self::TYPE_DATE, 'validate' => 'isDate', 'copy_post' => false),
        ),
    );

    public static function getAditionalsBypackage($id) {
        $aditionals = array();
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
		SELECT * FROM `' . _DB_PREFIX_ . 'inv_packages_aditionals` D INNER JOIN `' . _DB_PREFIX_ . 'inv_aditionals` S where D.id_aditional=S.id_aditional AND id_package=' . $id .
                ' ORDER BY D.id_package_aditional ASC');
        foreach ($result as $row) {
            $aditionals[$row['id_package_aditional']] = $row;
        }
//        var_dump($aditionals);exit();
        return $aditionals;
    }

    public static function getPriceAditionals($id){
        $aditional = array();
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
        SELECT * FROM `' . _DB_PREFIX_ . 'inv_packages_aditionals` where id_package_aditional='.$id.'' );
        foreach ($result as $row) {
            $aditional[$row['id_package_aditional']] = $row;
        }
        //var_dump($seats);exit();
        return $aditional;
        
    }

}
