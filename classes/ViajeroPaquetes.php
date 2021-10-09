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

class ViajeroPaquetes extends ObjectModel
{
    public $id_package;
    public $name;
    public $date;
    public $photo;
    public $tok;
    public $disponibilidad;
    public $edadninos;
    public $edadbebes;
    public $valueninos;
    public $valuebebes;
    public $detalles;
    public $legales;
    public $pricereference;
    public $id_product;
    public $date_add;
    public $date_upd;
    public $api;
    public $id_package_api;
    public $quota_api;
    // public $company_id;
    
    public static $definition = array(
        'table' => 'inv_packages',
        'primary' => 'id_package',
        'fields' => array(
            'id_package' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'api' => array('type' => self::TYPE_INT),
            'id_package_api' => array('type' => self::TYPE_INT),
            'quota_api' => array('type' => self::TYPE_INT),
            // 'company_id' => array('type' => self::TYPE_INT),
            'tok' => array('type' => self::TYPE_INT),
            'name' => array('type' => self::TYPE_STRING, 'size' => 200, 'required' => true),
            'photo' => array('type' => self::TYPE_STRING, 'size' => 200),
            'disponibilidad'=>array('type' => self::TYPE_STRING, 'required' => true),
            'edadninos'=>array('type' => self::TYPE_INT),
            'edadbebes'=>array('type' => self::TYPE_INT),
            'valueninos'=>array('type' => self::TYPE_FLOAT),
            'valuebebes'=>array('type' => self::TYPE_FLOAT),
            'pricereference'=>array('type' => self::TYPE_FLOAT),
            'detalles' =>array('type' => self::TYPE_HTML, 'validate' => 'isCleanHtml','size' => 3999999999999),
            'legales' =>array('type' => self::TYPE_HTML, 'validate' => 'isCleanHtml','size' => 3999999999999),
            'id_product'=>array('type' => self::TYPE_INT),
            'date' => array('type' => self::TYPE_DATE, 'validate' => 'isDate', 'required' => true),
            'date_add' => array('type' => self::TYPE_DATE, 'validate' => 'isDate', 'copy_post' => false),
            'date_upd' => array('type' => self::TYPE_DATE, 'validate' => 'isDate', 'copy_post' => false),
        ),
    );
    public static function getIdAttributeClassName($name){
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
		SELECT * FROM ' . _DB_PREFIX_ . 'attribute_group_lang atl where atl.public_name like "'. $name.'"');        
        return $result[0]['id_attribute_group'];
    }
    public static function getProperties(){
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
		SELECT c.email as email,g.name as namepro FROM `' . _DB_PREFIX_ . 'customer` c '                               
                . ' left join `' . _DB_PREFIX_ . 'customer_group` AS cg ON cg.id_customer=c.id_customer'
                . ' left join `' . _DB_PREFIX_ . 'group_lang` g ON g.id_group=cg.id_group'
                . ' where g.name like "propietario"');
        return $result;
    }
    
    public static function getIdAttributeName($name){
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
		SELECT * FROM ' . _DB_PREFIX_ . 'attribute_lang atl where atl.name like "'. $name.'"');        
        return $result[0]['id_attribute'];
    }
    public static function createAssociate($category,$product){
       
       $result2 = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('SELECT id_category, MAX( position ) FROM '._DB_PREFIX_.'category_product GROUP BY position ORDER BY id_category DESC limit 1');       
//       var_dump((int)$result2[0]['MAX( position )']);exit();
       $pos = (int)$result2[0]['MAX( position )'];
       $query='INSERT INTO `'. _DB_PREFIX_.'category_product`(`id_category`, `id_product`, `position`) VALUES ('.$category.','.$product.','. ($pos+1) .')';
//       var_dump($query);exit();
       $result = Db::getInstance()->execute($query);
       return true;
    }
    public static function getPackages()
    {
        $packages = array();
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
		SELECT * FROM `' . _DB_PREFIX_ . 'inv_packages` D 						
		ORDER BY D.name ASC');
        foreach ($result as $row) {
            $packages[$row['id_package']] = $row;
        }
        return $packages;
    }


    public static function getPackageByIdProduct($id)
    {
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
		SELECT * FROM ' . _DB_PREFIX_ . 'inv_packages atl where atl.id_product = '. $id .'');        
        return $result[0]['id_package'];
    }
    
    public static function getHistorial($id)
    {
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
        SELECT * FROM `' . _DB_PREFIX_ . 'inv_packages_historiales` cp
        WHERE payment = 1 AND id_package='.$id.' LIMIT 1'); 

        return $result;
    }
}
