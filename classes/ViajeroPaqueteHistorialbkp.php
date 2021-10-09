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

class ViajeroPaqueteHistorial extends ObjectModel
{
    public $id_package_historial;
    public $id_package;
    public $id_product;
    public $price;
    public $id_package_linea;
    public $id_package_butaca;
    public $payment;
    public $reference;
    public $state;
    public $id_customer;
    public $cantPassAdult;
    public $cantPassNinos;
    public $cantPassBebes;
    public $date_add;
    public $date_upd;
    public $id_origin;
    public $id_proceso;
    
    public static $definition = array(
        'table' => 'inv_packages_historiales',
        'primary' => 'id_package_historial',
        'fields' => array(
            'id_package_historial' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),            
            'id_package' => array('type' => self::TYPE_INT, 'required' => true),
            'id_product' => array('type' => self::TYPE_INT, 'required' => true),
            'price' => array('type' => self::TYPE_FLOAT, 'required' => true),
            'id_package_linea' => array('type' => self::TYPE_INT, 'required' => true),
            'id_package_butaca' => array('type' => self::TYPE_INT, 'required' => true),
            'payment' => array('type' => self::TYPE_INT, 'required' => true),            
            'state' => array('type' => self::TYPE_STRING),            
            'reference' => array('type' => self::TYPE_STRING),            
            'cantPassAdult' => array('type' => self::TYPE_INT),
            'cantPassNinos' => array('type' => self::TYPE_INT),
            'cantPassBebes' => array('type' => self::TYPE_INT),
            'id_customer' => array('type' => self::TYPE_INT),
            'date_add' => array('type' => self::TYPE_DATE, 'validate' => 'isDate', 'copy_post' => false),
            'date_upd' => array('type' => self::TYPE_DATE, 'validate' => 'isDate', 'copy_post' => false),
            'id_origin' => array('type' => self::TYPE_INT, 'required' => false),
            'id_proceso' => array('type' => self::TYPE_INT, 'required' => false)

        ),
    );

    public static function getHistorial()
    {
        $historial = array();
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
        SELECT * FROM `' . _DB_PREFIX_ . 'inv_packages_historiales` D                       
        ORDER BY id_package_historial ASC');
        foreach ($result as $row) {
            $historial[$row['id_package_historial']] = $row;
        }
        return $historial;
    }
    public static function getOrigin($id){
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
		SELECT * FROM `' . _DB_PREFIX_ . 'inv_packages_historiales` PH '               
                . ' inner join `' . _DB_PREFIX_ . 'inv_packages_linea` AS PO ON PO.id_package_linea=PH.id_package_linea'
                . ' where PH.id_package_historial=' . $id );
//        var_dump($result);exit();
        return $result[0];
    }
    public static function getOrigin2($id){
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
		SELECT * FROM `' . _DB_PREFIX_ . 'inv_packages_linea` Pl '                               
                . ' left join `' . _DB_PREFIX_ . 'inv_packages_origins` AS O ON O.id_package_linea=Pl.id_package_linea'
                . ' left join `' . _DB_PREFIX_ . 'inv_origins` OO ON O.id_origin=OO.id_origen'
                . ' where Pl.id_package_linea=' . $id );
        return $result[0];
    }
    
    public static function getSeat($id){
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
		SELECT * FROM `' . _DB_PREFIX_ . 'inv_packages_historiales` PH '               
                . ' inner join `' . _DB_PREFIX_ . 'inv_packages_seats` AS PO ON PO.id_package_seat=PH.id_package_butaca'
                . ' inner join `' . _DB_PREFIX_ . 'inv_seats` AS O ON O.id_seat=PO.id_seat'
                . ' where PH.id_package_historial=' . $id );

        return $result[0];
    }
    public static function getAditionals($id){
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
		SELECT * FROM `' . _DB_PREFIX_ . 'inv_packages_historiales_adicionales` PHA '               
                . ' inner join `' . _DB_PREFIX_ . 'inv_packages_aditionals` AS PO ON PO.id_package_aditional=PHA.id_package_adicional'                
                . ' inner join `' . _DB_PREFIX_ . 'inv_aditionals` AS O ON O.id_aditional=PO.id_aditional'
                . ' where PHA.id_package_historial=' . $id );
        return $result;
    }
    public static function getDestinos($id){
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
		SELECT * FROM `' . _DB_PREFIX_ . 'inv_packages_historiales_des` PHD '               
                . ' inner join `' . _DB_PREFIX_ . 'inv_packages_destinos` AS PO ON PO.id_package_destino=PHD.id_package_destino'                
                . ' inner join `' . _DB_PREFIX_ . 'inv_destinations` AS O ON O.id_destiny=PO.id_destiny'
                . ' where PHD.id_package_historial=' . $id );
        return $result;
    }
    public static function getRooms($id){
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
		SELECT PHR.*,PO.*,O.*,H.id_destiny as id_destiny, H.name as nameHotel, D.destiny as namedestiny FROM `' . _DB_PREFIX_ . 'inv_packages_historiales_rooms` PHR '               
                . ' inner join `' . _DB_PREFIX_ . 'inv_packages_rooms` AS PO ON PO.id_package_room=PHR.id_package_room'                
                . ' inner join `' . _DB_PREFIX_ . 'inv_rooms` AS O ON O.id_room=PO.id_room'
                . ' left join `' . _DB_PREFIX_ . 'inv_hotels` AS H ON H.id_hotel=O.id_hotel'
                . ' left join `' . _DB_PREFIX_ . 'inv_destinations` AS D ON D.id_destiny=H.id_destiny'
                . ' where PHR.id_package_historial=' . $id );
        return $result;
    }   
    public static function getPassagers($id){
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
		SELECT * FROM `' . _DB_PREFIX_ . 'inv_packages_historiales_pasajeros` PHD '
                . ' where PHD.id_package_historial=' . $id );
        return $result;
    }
    
    public static function getProductsCart($id)
    {
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
        SELECT * FROM `' . _DB_PREFIX_ . 'cart_product` cp                       
        where id_cart='.$id);        
        return $result[0];
    }
    public static function getCart($id)
    {
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
        SELECT * FROM `' . _DB_PREFIX_ . 'cart` cp                       
        where id_cart='.$id);        
        return $result[0];
    }
    public static function getOrder($id)
    {
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
        SELECT * FROM `' . _DB_PREFIX_ . 'orders` cp                       
        where id_cart='.$id);        
        return $result[0];
    }
    public static function getPackageByProduct($id)
    {
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
        SELECT * FROM `' . _DB_PREFIX_ . 'inv_packages_historiales` cp                       
        where id_product='.$id); 
        
        return $result[0];
    }

    
    //Multiple products functions.
    public static function getAllProductsByCart($id)
    {
        $results = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
        SELECT * FROM `' . _DB_PREFIX_ . 'cart_product` cp                       
        where id_cart='.$id);        
        return $results;
    }

    public static function getAllPackagesByProduct($products_id)
    {
        $products_id = implode('","', $products_id);
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
        SELECT * FROM `' . _DB_PREFIX_ . 'inv_packages_historiales` cp                       
        where id_product IN("'.$products_id.'")'); 
        
        return $result;
    }

    
//----------
    public static function getPaymentMethod($id){
        //echo "<script>console.log( 'Debug Objects: " . $id . "' );</script>";
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
        SELECT * FROM `' . _DB_PREFIX_ . 'inv_packages_historiales` ID '               
                . ' inner join `' . _DB_PREFIX_ . 'order_payment` OP ON ID.reference=OP.order_reference'                
                . ' where ID.id_package_historial=' . $id );
    //    var_dump($result1[0]['payment_method']);
       // echo "<script>console.log( 'Debug Objects: " . $result[0].['payment_method'] . "' );</script>";
    
        return $result[0];
    }  
    //pr_inv_packages_linea_servicios
    public static function getServiceLine($id){
        //echo "<script>console.log( 'Debug Objects: " . $id . "' );</script>";
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
        SELECT * FROM `' . _DB_PREFIX_ . 'inv_packages_historiales` ID '
                . ' inner join `' . _DB_PREFIX_ . 'inv_packages_linea_servicios` LS ON ID.id_package_linea=LS.id_package_linea'
                . ' where ID.id_package_historial=' . $id );
                //var_dump($result);
                $resultado = "";
                foreach ($result as $row) {
                    $resultado .= $row['description'] . " - ";
                }
        $resultado = substr($resultado, 0, -2);
        return $resultado;
    }
//,SUM(impuesto*()cantPassNinos+cantPassAdult)
   public static function getImpuestos($id){
        //echo "<script>console.log( 'Debug Objects: " . $id . "' );</script>";
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
        SELECT SUM(impuesto*(cantPassAdult+cantPassNinos)) as imp  FROM `' . _DB_PREFIX_ . 'inv_packages_historiales` PH '               
                . ' inner join `' . _DB_PREFIX_ . 'inv_packages_linea` AS PL ON PH.id_package_linea=PL.id_package_linea'
                . ' where PH.id_package_historial=' . $id );
//        var_dump($result);exit();
        return $result[0];
}
//-----------

    

}
