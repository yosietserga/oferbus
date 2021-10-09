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

class ViajeroPaqueteDestinos extends ObjectModel
{
    
    public $id_package_destino;
    public $id_package;
    public $id_destiny;
    public $id_product_attribute;
    public $price;
    public $description;
    public $impuesto;
    public $inventario;
    public $date_sal;
    public $date_lle;
    public $date_add;
    public $date_upd;
    
    public static $definition = array(
        'table' => 'inv_packages_destinos',
        'primary' => 'id_package_destino',
        'fields' => array(
            'id_package_destino' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'id_package' => array('type' => self::TYPE_INT, 'required' => true),
            'id_destiny' => array('type' => self::TYPE_INT, 'required' => true),
            'id_product_attribute' => array('type' => self::TYPE_INT),
            'price' => array('type' => self::TYPE_FLOAT, 'required' => true),
            'impuesto' => array('type' => self::TYPE_FLOAT),
            'inventario' => array('type' => self::TYPE_INT),
            'description' =>array('type' => self::TYPE_HTML, 'validate' => 'isCleanHtml','size' => 3999999999999),
            'date_sal' => array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
            'date_lle' => array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
            'date_add' => array('type' => self::TYPE_DATE, 'validate' => 'isDate', 'copy_post' => false),
            'date_upd' => array('type' => self::TYPE_DATE, 'validate' => 'isDate', 'copy_post' => false),
        ),
    );

    public static function getDestinosBypackage($id) {
        $destinos = array();
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
		SELECT * FROM `' . _DB_PREFIX_ . 'inv_packages_destinos` D where id_package=' . $id .
                ' ORDER BY D.id_package_destino ASC');
        foreach ($result as $row) {
            $destinos[$row['id_package_destino']] = $row;
        }
        return $destinos;
    }    
    public static function getDestinosBypackageName($id) {
        $destinos = array();
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
		SELECT * FROM ' . _DB_PREFIX_ . 'inv_packages_destinos D '
                . ' inner join ' . _DB_PREFIX_ . 'inv_destinations AS R  ON D.id_destiny=R.id_destiny'
                . ' where D.id_package=' . $id .
                ' ORDER BY D.id_package_destino ASC');
//        var_dump($result);
        foreach ($result as $key=>$row) {
            $destinos[$key] = $row;            
            $originsSelect = ViajeroPaqueteOrigins::getOriginsByDestinySelect($id,$row['id_package_destino']);
//            var_dump($originsSelect);
            array_push($destinos[$key],array('origins'=>$originsSelect)); 
        }                
//        exit();
        return $destinos;
    }

    public static function getInformationDestinos($id, $idd) {

        $destinos = array();
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
        SELECT J.name as nameroom,PR.limninos as limninos, PR.limbebes as limbebes, RR.priceninos as priceninos, RR.pricebebes as pricebebes, RR.price AS preciohabitacion,RR.id_room AS idroom,J.cant AS cantidadpersonas,H.name AS nombrehotel, D.id_package_destino AS iddestino,  R.destiny AS destino, D.price AS preciodestino  FROM ' . _DB_PREFIX_ . 'inv_packages_destinos  AS D'
                . ' inner join ' . _DB_PREFIX_ . 'inv_destinations AS R  ON D.id_destiny=R.id_destiny'
                . ' inner join ' . _DB_PREFIX_ . 'inv_packages_rooms AS RR  ON D.id_package=RR.id_package'
                . ' inner join ' . _DB_PREFIX_ . 'inv_rooms AS J  ON RR.id_room=J.id_room'
                . ' inner join ' . _DB_PREFIX_ . 'inv_hotels AS H  ON J.id_hotel=H.id_hotel'
                . ' inner join ' . _DB_PREFIX_ . 'inv_packages_regimens AS PR  ON J.id_hotel=PR.id_hotel'
                . ' where D.id_package_destino=' . $id 
                . ' and RR.id_package_room=' . $idd);
        foreach ($result as $row) {
            $destinos[$row['iddestino']] = $row;
        }
        return $destinos;
    }

    public static function getPriceDestinos($id){
        $precio = array();
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
        SELECT impuesto,price, id_package_destino FROM ' . _DB_PREFIX_ . 'inv_packages_destinos '
                . ' where id_package_destino=' . $id);
        foreach ($result as $row) {
            $precio[$row['id_package_destino']] = $row;
        }
        return $precio;
    }

    public static function getDestinosByOrigin($origin_id){
        $sql = 'SELECT des.*
                FROM '._DB_PREFIX_.'inv_packages_linea lin
                JOIN '._DB_PREFIX_.'inv_packages_origins ori ON ori.id_package_linea = lin.id_package_Linea
                JOIN '._DB_PREFIX_.'inv_packages_rooms pacr ON lin.id_package_Linea = pacr.id_package_Linea
                JOIN '._DB_PREFIX_.'inv_rooms room ON room.id_room = pacr.id_room
                JOIN '._DB_PREFIX_.'inv_hotels hot ON hot.id_hotel = room.id_hotel
                JOIN '._DB_PREFIX_.'inv_destinations des ON des.id_destiny = hot.id_destiny
                WHERE ori.id_origin = '.$origin_id.' GROUP BY des.id_destiny ';

        $destinys = Db::getInstance()->ExecuteS($sql);
        return $destinys;

    }
    
    public static function getOriginesByDestiny($destiny){
        $sql = 'SELECT o.*
                FROM '._DB_PREFIX_.'inv_packages_linea lin
                JOIN '._DB_PREFIX_.'inv_packages_origins ori ON ori.id_package_linea = lin.id_package_Linea
                JOIN '._DB_PREFIX_.'inv_packages_rooms pacr ON lin.id_package_Linea = pacr.id_package_Linea
                JOIN '._DB_PREFIX_.'inv_rooms room ON room.id_room = pacr.id_room
                JOIN '._DB_PREFIX_.'inv_hotels hot ON hot.id_hotel = room.id_hotel
                JOIN '._DB_PREFIX_.'inv_destinations des ON des.id_destiny = hot.id_destiny
                JOIN '._DB_PREFIX_.'inv_origins o ON o.id_origen = ori.id_origin                
                WHERE des.id_destiny = '.$destiny.' GROUP BY o.id_origen ';
        $destinys = Db::getInstance()->ExecuteS($sql);
//        var_dump($destinys );exit();
        return $destinys;

    }

        
}
