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

class AdminViajeroHistorialController extends ModuleAdminController
{
    protected $status_array = array();
    public function __construct()
    {
        $this->bootstrap = true;
        $this->table = "inv_packages_historiales";
        $this->className = "ViajeroPaqueteHistorial";
        $this->identifier = "id_package_historial";
        $this->fields_list = array();
        $this->_where = 'AND a.`payment` = 1';
        parent::__construct();
        $this->actions = array('view');
        $this->_select="P.name, a.price, id_package_historial, firstname, lastname, state, a.date_add, PL.date_sal";
        $this->_join = 'LEFT JOIN `' . _DB_PREFIX_ . 'customer` C ON (a.`id_customer` = C.`id_customer`)
        LEFT JOIN `' . _DB_PREFIX_ . 'inv_packages` P ON (a.`id_package` = P.`id_package`)
        LEFT JOIN `' . _DB_PREFIX_ . 'inv_packages_linea` PL ON (a.`id_package` = PL.`id_package`) ';
        $this->_group = 'GROUP BY a.id_package_historial';
        $this->_orderBy = 'date_add';
        $this->_orderWay = 'DESC';
        $this->context->controller->addJS(_PS_JS_DIR_.'jquery/jquery-1.11.0.min.js');
        $this->context->controller->addJS($this->module->getPathUri() . 'views/js/jquery-ui-1.9.2.custom.min.js');
        $this->context->controller->addJS($this->module->getPathUri() . 'views/js/historial.js');
        $this->fields_list = array(
            'reference' => array(
                'title' => $this->l('ID'),
            ),
            'name' => array(
                'title' => $this->l('Paquete')
            ),
            'date_sal' => array(
                'title' => $this->l('Fecha de Salida'),
            ),
            'price' => array(
                'title' => $this->l('Precio')
            ),
            'firstname' => array(
                'title' => $this->l('Nombres Comprador')
            ),
            'lastname' => array(
                'title' => $this->l('apellidos Comprador')
            ),
            'state' => array(
                'title' => $this->l('Detalle compra'),
            ),
            'date_add' => array(
                'title' => $this->l('Fecha de Reserva'),
            ),
        );
    }

    public function postProcess()
    {

        parent::postProcess();
    }
       
    public function initPageHeaderToolbar()
    {
        if (empty($this->display)) {
            $this->page_header_toolbar_btn['new_hotel'] = array(
                'href' => $this->context->link->getAdminLink('AdminViajeroHotel'),
                'desc' => $this->l('Hoteles', null, null, false),
                'icon' => 'icon-picture'
            );
        }
        if (empty($this->display)) {
            $this->page_header_toolbar_btn['new_Destiny'] = array(
                'href' => $this->context->link->getAdminLink('AdminViajeroDestino'),
                'desc' => $this->l('Destinos de viaje', null, null, false),
                'icon' => 'icon-map-marker'
            );
        }
        if (empty($this->display)) {
            $this->page_header_toolbar_btn['new_room'] = array(
                'href' => $this->context->link->getAdminLink('AdminViajeroRooms'),
                'desc' => $this->l('Habitaciones', null, null, false),
                'icon' => 'icon-globe'
            );
        }
        if (empty($this->display)) {
            $this->page_header_toolbar_btn['new_package'] = array(
                'href' => $this->context->link->getAdminLink('AdminViajeroPaquete'),
                'desc' => $this->l('Paquetes de viaje', null, null, false),
                'icon' => 'icon-windows'
            );
        }
        if (empty($this->display)) {
            $this->page_header_toolbar_btn['new_butaca'] = array(
                'href' => $this->context->link->getAdminLink('AdminViajeroButaca'),
                'desc' => $this->l('butacas', null, null, false),
                'icon' => 'icon-adn '
            );
        }
        if (empty($this->display)) {
            $this->page_header_toolbar_btn['new_Origin'] = array(
                'href' => $this->context->link->getAdminLink('AdminViajeroOrigenes'),
                'desc' => $this->l('Origenes', null, null, false),
                'icon' => 'icon-map-marker'
            );
        }
        if (empty($this->display)) {
            $this->page_header_toolbar_btn['add_aditional'] = array(
                'href' => $this->context->link->getAdminLink('AdminViajeroAdicionales').'&addinv_aditionals',
                'desc' => $this->l('Agregar Adicional', null, null, false),
                'icon' => 'icon-gittip '
            );
        }
        parent::initPageHeaderToolbar();
    }

    public function renderView()
    {
        $packageHistorial = new ViajeroPaqueteHistorial(Tools::getValue('id_package_historial'));
        $package = new ViajeroPaquetes($packageHistorial->id_package);
        $packageLinea = ViajeroPaqueteHistorial::getOrigin(Tools::getValue('id_package_historial'));   
        $packageorigen = ViajeroPaqueteHistorial::getOrigin2($packageLinea['id_package_linea']);
        $packageButaca = ViajeroPaqueteHistorial::getSeat(Tools::getValue('id_package_historial'));
        $packageAditionals = ViajeroPaqueteHistorial::getAditionals(Tools::getValue('id_package_historial'));       
        $packagePasajeros = ViajeroPaqueteHistorial::getPassagers(Tools::getValue('id_package_historial'));
        $packageRooms = ViajeroPaqueteHistorial::getRooms(Tools::getValue('id_package_historial'));        
        $customer = new CustomerCore($packageHistorial->id_customer);
        $sal = new DateTime($packageLinea['date_sal']);        
        $lle = new DateTime($packageLinea['date_lle']);
       
        $this->context->smarty->assign(array(
            'packagehistorial' =>$packageHistorial,
            'package' => $package,
            'packageDes' => strip_tags($package->detalles),
            'packageLinea' => $packageLinea,
            'packageLineaSal' => $sal->format('d-m-Y'),
            'packageLineaLLe' => $lle->format('d-m-Y'),
            'packageorigen' => $packageorigen,
            'packageButaca' => $packageButaca,
            'packageAditionals' => $packageAditionals,
            'packagePasajeros' => $packagePasajeros,
            'packageRooms' => $packageRooms,
            'customer' => $customer,
        ));
        $this->content .= $this->module->getForm('historial/view.tpl');
//        return parent::renderView();
    }
}
