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

class ViajeroPaqueteSeats extends ObjectModel
{
    
    public $id_package_seat;
    public $id_package;
    public $id_seat;
    public $id_product_attribute;
    public $price;
    public $date_add;
    public $date_upd;
    
    public static $definition = array(
        'table' => 'inv_packages_seats',
        'primary' => 'id_package_seat',
        'fields' => array(
            'id_package_seat' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'id_package' => array('type' => self::TYPE_INT, 'required' => true),
            'id_seat' => array('type' => self::TYPE_INT, 'required' => true),
            'id_product_attribute' => array('type' => self::TYPE_INT),
            'id_product_attribute' => array('type' => self::TYPE_INT),
            'price' => array('type' => self::TYPE_FLOAT, 'required' => true),
            'date_add' => array('type' => self::TYPE_DATE, 'validate' => 'isDate', 'copy_post' => false),
            'date_upd' => array('type' => self::TYPE_DATE, 'validate' => 'isDate', 'copy_post' => false),
        ),
    );
    public static function getSeatBypackage($id) {
        $seats = array();
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
		SELECT * FROM `' . _DB_PREFIX_ . 'inv_packages_seats` D INNER JOIN `' . _DB_PREFIX_ . 'inv_seats` S where D.id_seat=S.id_seat AND id_package='.$id.						
		' ORDER BY D.id_package_seat ASC' );
        foreach ($result as $row) {
            $seats[$row['id_package_seat']] = $row;
        }
        //var_dump($seats);exit();
        return $seats;
    }

    public static function getTypeSeatByPackage($id)
    {
        $seats = array();

        /*$result = Db::getInstance(_PS_USE_SQL_SLAVE)->ExecuteS("
                SELECT
                  s.*,
                  IF(ltp.cupos_butaca, ltp.cupos_butaca, 0) as cupos_butaca,
                  IF(ltp.cupos_butaca, TRUE, FALSE) as validar_cupo_api,
                  d.*,
                  ltp.tipo_cupo_id,
                  ltp.nombre_tipo_cupo
                FROM
                    pr_inv_packages_seats d
                INNER JOIN pr_inv_seats s ON
                    s.id_seat = d.id_seat
                INNER JOIN pr_inv_packages_linea_transportes_tipos ltp ON
                    ltp.tipo_butaca_id = s.id_seat
                WHERE
                    d.id_package = ".$id
         );*/

        $result = Db::getInstance(_PS_USE_SQL_SLAVE)->ExecuteS("
                SELECT
                    s.*,
                    d.*,
                    IF(ptt.cupos_butaca > 0 , ptt.cupos_butaca, 0) as cupos_butaca,
                    IF(ptt.cupos_butaca, TRUE, FALSE) as validar_cupo_api,
                    IF(ptt.tipo_butaca_id != '', ptt.tipo_butaca_id,  d.id_seat) as tipo_butaca_id,
                    IF(ptt.nombre_tipo_cupo != '', ptt.nombre_tipo_cupo, 'No Tiene') as nombre_tipo_cupo,
                    IF(ptt.tipo_cupo_id != '', ptt.tipo_cupo_id, 0) as tipo_cupo_id
                FROM
                    pr_inv_packages_seats d
                LEFT JOIN pr_inv_packages_linea_transportes_tipos ptt ON ptt.tipo_butaca_id = d.id_seat AND d.id_package = ptt.id_package
                INNER JOIN pr_inv_seats s ON s.id_seat = d.id_seat
                WHERE d.id_package = $id
				  and ptt.nombre_tipo_cupo = 'Estandar'
                GROUP BY nombre_tipo_cupo,tipo_butaca_id
                ORDER BY s.name "
        );

        return $result;
    }

    public static function getSeatByIdHistory($id)
    {
        $result = Db::getInstance(_PS_USE_SQL_SLAVE)->ExecuteS("
                SELECT
                    ltp.id as id_tlp,
                    ltp.cupos_butaca,
                    s.id_package
                FROM
                    pr_inv_packages_seats s
                INNER JOIN pr_inv_packages_linea_transportes_tipos ltp ON
                    ltp.tipo_butaca_id = s.id_seat AND ltp.id_package = s.id_package
                WHERE
                    s.id_package_seat = $id"
        );

        return $result[0];
    }

    public static function getSeatPrice($id){
        $seats = array();
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
        SELECT * FROM `' . _DB_PREFIX_ . 'inv_packages_seats` where id_package_seat='.$id.'' );
        foreach ($result as $row) {
            $seats[$row['id_package_seat']] = $row;
        }
        //var_dump($seats);exit();
        return $seats;
    }
}
