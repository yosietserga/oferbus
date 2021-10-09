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

class ViajeroDestinos extends ObjectModel
{
    
    public $id_destiny;
    public $destiny;
    public $latitud;
    public $longitud;
    public $date_add;
    public $date_upd;
    public $id_destination_api;
    
    public static $definition = array(
        'table' => 'inv_destinations',
        'primary' => 'id_destiny',
        'fields' => array(
            'id_destiny' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'destiny' => array('type' => self::TYPE_STRING, 'size' => 200, 'required' => true),
            'latitud' => array('type' => self::TYPE_STRING, 'size' => 200, 'required' => true),
            'longitud' => array('type' => self::TYPE_STRING, 'size' => 200, 'required' => true),
            'date_add' => array('type' => self::TYPE_DATE, 'validate' => 'isDate', 'copy_post' => false),
            'date_upd' => array('type' => self::TYPE_DATE, 'validate' => 'isDate', 'copy_post' => false),
            'id_destination_api' => array('type' => self::TYPE_INT),
        ),
    );

    public static function getDestinations()
    {
        $destinations = array();
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
		SELECT * FROM `' . _DB_PREFIX_ . 'inv_destinations` D 						
		ORDER BY D.destiny ASC');
        foreach ($result as $row) {
            $destinations[$row['id_destiny']] = $row;
        }
        return $destinations;
    }

    public static function getDestinationByName($name)
    {
        $sql = 'SELECT * FROM `' . _DB_PREFIX_ . 'inv_destinations` D WHERE D.destiny="'.$name.'" ORDER BY D.destiny ASC';
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS($sql);
        return $result;
    }
}
