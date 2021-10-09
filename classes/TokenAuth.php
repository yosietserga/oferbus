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

class TokenAuth extends ObjectModel
{
    
    public $id_token;
    public $code;
    public $token_expiration;
    
    public static $definition = array(
        'table' => 'inv_token_auth',
        'primary' => 'id_token',
        'fields' => array(
            'id_token' => array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
            'code' => array('type' => self::TYPE_STRING, 'size' => 500, 'required' => true),
            'token_expiration' => array('type' => self::TYPE_DATE, 'validate' => 'isDate', 'copy_post' => false)
        ),
    );

    public static function getToken()
    {
        $sql = 'SELECT * FROM `' . _DB_PREFIX_ . 'inv_token_auth`';
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS($sql);
        return $result;
    }

}