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

class ViajeroPaqueteLineaTransportesTipo extends ObjectModel
{
    public $id;
    public $id_package;
    public $id_packages_linea_transportes;
    public $tipo_butaca_id;
    public $tipo_cupo_id;
    public $cupos_butaca;

    public static $definition = array(
        'table' => 'inv_packages_linea_transportes_tipos',
        'primary' => 'id',
        'fields' => array(
            'id_package' => array('type' => self::TYPE_INT),
            'id_packages_linea_transportes' => array('type' => self::TYPE_INT),
            'tipo_butaca_id' => array('type' => self::TYPE_INT),
            'tipo_cupo_id' => array('type' => self::TYPE_INT),
            'nombre_tipo_cupo' => array('type' => self::TYPE_STRING),
            'cupos_butaca' => array('type' => self::TYPE_INT)
        ),
    );

    public static function getTypeByTransport($id) {

        $result = Db::getInstance(_PS_USE_SQL_SLAVE)->ExecuteS("
                SELECT
                  *
                FROM
                    pr_inv_packages_seats d
                WHERE
                    d.id_package_seat = ".$id
        );

        $transport = $result[0];
        return $transport;
    }

    public static function getTypesByPackage($package_id)
    {
        $result = Db::getInstance(_PS_USE_SQL_SLAVE)->ExecuteS("
                SELECT
                  *
                FROM
                    pr_inv_packages_linea_transportes_tipos s
                WHERE
                    s.id_package = ".$package_id
        );

        foreach ($result as $row) {
            $types_seats[$row['id']] = $row;
        }

        return $types_seats;
    }

    public static function getTypeByTransport1($id,$package) {
        $sql = 'SELECT * FROM `' . _DB_PREFIX_ . 'inv_packages_linea_transportes_tipos`  where id_packages_linea_transportes=' . $id .' AND id_package='.$package;
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS($sql);

        $transport = $result[0];
        return $transport;
    }
}

