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

if (!defined('_PS_VERSION_')) {
    exit;
}

include_once dirname(__FILE__).'/classes/ViajeroDestinos.php';
include_once dirname(__FILE__).'/classes/ViajeroButaca.php';
include_once dirname(__FILE__).'/classes/ViajeroHotel.php';
include_once dirname(__FILE__).'/classes/ViajeroPaquetes.php';
include_once dirname(__FILE__).'/classes/ViajeroRooms.php';
include_once dirname(__FILE__).'/classes/ViajeroHotelsphotos.php';
include_once dirname(__FILE__).'/classes/ViajeroRoomsphotos.php';
include_once dirname(__FILE__).'/classes/ViajeroAdicionales.php';
include_once dirname(__FILE__).'/classes/ViajeroPaqueteSeats.php';
include_once dirname(__FILE__).'/classes/ViajeroPaqueteAditionals.php';
include_once dirname(__FILE__).'/classes/ViajeroPaqueteRooms.php';
include_once dirname(__FILE__).'/classes/ViajeroPaqueteOrigins.php';
include_once dirname(__FILE__).'/classes/ViajeroPaqueteDestinos.php';
include_once dirname(__FILE__).'/classes/ViajeroPaqueteRegimen.php';
include_once dirname(__FILE__).'/classes/ViajeroPaqueteHistorial.php';
include_once dirname(__FILE__).'/classes/ViajeroPaqueteHistorialAdicionales.php';
include_once dirname(__FILE__).'/classes/ViajeroPaqueteHistorialRooms.php';
include_once dirname(__FILE__).'/classes/ViajeroPaqueteHistorialPasajeros.php';
include_once dirname(__FILE__).'/classes/ViajeroOrigenes.php';
include_once dirname(__FILE__).'/classes/ViajeroPackagePhotos.php';
include_once dirname(__FILE__).'/classes/ViajeroPaqueteLinea.php';
include_once dirname(__FILE__).'/classes/ViajeroPaqueteLineaServicios.php';
include_once dirname(__FILE__).'/classes/Redevtapi.php';
include_once dirname(__FILE__).'/classes/TokenAuth.php';
include_once dirname(__FILE__).'/classes/ViajeroPaqueteLineaTransportes.php';
include_once dirname(__FILE__).'/classes/ViajeroPaqueteLineaTransportesTipo.php';

//include_once dirname(__FILE__).'..'

class Viajero extends Module
{
    const INSTALL_SQL_FILE = 'install.sql';
    const UNINSTALL_SQL_FILE = 'uninstall.sql';
    protected $GROUP = '';
    protected $config_form = false;

    public function __construct()
    {
        $this->name = 'viajero';
        $this->tab='administration';
        $this->version = '1.0.1';
        $this->author = 'inventiba';
        $this->need_instance = 0;
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Gestor de paquetes');
        $this->description = $this->l('Crea tus paquetes de viajes');

        $this->confirmUninstall = $this->l('Esta seguro de desinstalar el modulo Viajero?');

        $this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);
    }

    public function install()
    {
        if (!file_exists(dirname(__FILE__) . '/' . self::INSTALL_SQL_FILE)) {
            return false;
        } elseif (!$sql = Tools::file_get_contents(dirname(__FILE__) . '/' . self::INSTALL_SQL_FILE)) {
            return false;
        }
        $version=_PS_VERSION_;
        $sql = str_replace('PREFIX_', _DB_PREFIX_, $sql);
        Db::getInstance()->execute(trim($sql));
        $version[2]=="6"?$this->installTabs('Administration'):$this->installTabs('SELL');
//        $this->installAtributtes();

        return parent::install() &&
            $this->registerHook('header') &&
            $this->registerHook('displayHeader') &&
            $this->registerHook('displayTop') &&
            $this->registerHook('displayCustomerAccount') &&
            $this->registerHook('backOfficeHeader') &&
            $this->registerHook('displayHomeTop') &&
            $this->registerHook('displayProductTab') &&
            $this->registerHook('actionValidateOrder') &&
            $this->registerHook('actionOrderStatusUpdate') &&
            $this->registerHook('actionPaymentConfirmation') &&
            $this->registerHook('displayTopViajero');
    }

    public function createDir($folder)
    {
        if (!file_exists($folder)) {
            mkdir($folder, 0777);
            return true;
        }
    }

    private function installTabs($type)
    {
        $this->installTab($type, 'AdminViajero', 'Gestor de paquetes');
        $this->installTab('AdminViajero', 'AdminViajeroDestino', 'Destinos');
        $this->installTab('AdminViajero', 'AdminViajeroOrigenes', 'Origenes');
        $this->installTab('AdminViajero', 'AdminViajeroHotel', 'Hoteles');
        $this->installTab('AdminViajero', 'AdminViajeroRooms', 'Habitaciones');
        $this->installTab('AdminViajero', 'AdminViajeroButaca', 'Butacas');
        $this->installTab('AdminViajero', 'AdminViajeroAdicionales', 'Adicionales');
        $this->installTab('AdminViajero', 'AdminViajeroPaquete', 'Paquetes de viaje');
        $this->installTab('AdminViajero', 'AdminViajeroHistorial', 'Ventas');
    }

    public function installTab($parent, $class_name, $tab_name)
    {
        // Create new admin tab
        $tab = new Tab();
        $tab->id_parent = (int) Tab::getIdFromClassName($parent);
        $tab->name = array();
        foreach (Language::getLanguages(true) as $lang) {
            $tab->name[$lang['id_lang']] = $tab_name;
        }
        $tab->class_name = $class_name;
        $tab->module = $this->name;
        $tab->active = 1;
        $tab->position = 0;
        return $tab->add();
    }
    public function uninstallTab($parent)
    {
        $tab = new Tab(Tab::getIdFromClassName($parent));
        return $tab->delete();
    }


    public function uninstall()
    {
        if (!file_exists(dirname(__FILE__) . '/' . self::UNINSTALL_SQL_FILE)) {
            return false;
        } elseif (!$sql = Tools::file_get_contents(dirname(__FILE__) . '/' . self::UNINSTALL_SQL_FILE)) {
            return false;
        }
        $this->uninstallTab('AdminViajeroButaca');
        $this->uninstallTab('AdminViajeroHotel');
        $this->uninstallTab('AdminViajeroTyperooms');
        $this->uninstallTab('AdminViajeroRooms');
        $this->uninstallTab('AdminViajeroPaquete');
        $this->uninstallTab('AdminViajeroAdicionales');
        $this->uninstallTab('AdminViajero');

        $sql = str_replace('PREFIX_', _DB_PREFIX_, $sql);
        Db::getInstance()->execute(trim($sql));
        $this->unregisterHook('header');
        $this->unregisterHook('displayHeader');
        $this->unregisterHook('displayCustomerAccount');
        $this->unregisterHook('backOfficeHeader');
        $this->unregisterHook('displayHomeTop');
        $this->unregisterHook('displayProductTab');
        $this->unregisterHook('actionPaymentConfirmation');
        $this->unregisterHook('actionOrderStatusUpdate');
        $this->unregisterHook('displayTopViajero');
        return parent::uninstall();
    }

    /**
     * Load the configuration form
     */
    public function getContent()
    {
        $output = null;
        if (Tools::isSubmit('submit'.$this->name)) {
            $user_api = Tools::getValue('INV_USER_API');
            $pass_api = Tools::getValue('INV_PASS_API');
            $url_api = Tools::getValue('INV_URL_API');

            $last_character_url = substr($url_api, -1);

            if ($last_character_url !== '/') {
                $url_api .= '/';
            }

            Configuration::updateValue('INV_USER_API', $user_api);
            Configuration::updateValue('INV_PASS_API', $pass_api);
            Configuration::updateValue('INV_URL_API', $url_api);

            $output .= $this->displayConfirmation($this->l('Configuración actualizada'));
        }

        return $output . $this->displayFormConfiguration();
    }

    public function displayFormConfiguration()
    {
        $user_api = Configuration::get('INV_USER_API');
        $pass_api = Configuration::get('INV_PASS_API');
        $url_api = Configuration::get('INV_URL_API');

        // Init Fields form array
        $fieldsForm[0]['form'] = [
            'legend' => [
                'title' => $this->l('Settings'),
            ],
            'input' => [
                [
                    'type' => 'text',
                    'label' => $this->l('Usuario API'),
                    'name' => 'INV_USER_API',
                    'size' => 6,
                    'required' => true
                ],
                [
                    'type' => 'text',
                    'label' => $this->l('Contraseña API'),
                    'name' => 'INV_PASS_API',
                    'size' => 6,
                    'required' => true
                ],
                [
                    'type' => 'text',
                    'label' => $this->l('Url API'),
                    'name' => 'INV_URL_API',
                    'size' => 6,
                    'required' => true
                ]
            ],
            'submit' => [
                'title' => $this->l('Save'),
                'class' => 'btn btn-default pull-right'
            ]
        ];

        $helper = new HelperForm();

        // Module, Token and currentIndex
        $helper->module = $this;
        $helper->name_controller = $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->currentIndex = AdminController::$currentIndex.'&configure='.$this->name;

        // title and Toolbar
        $helper->title = $this->displayName;
        $helper->show_toolbar = true;        // false -> remove toolbar
        $helper->toolbar_scroll = true;      // yes - > Toolbar is always visible on the top of the screen.
        $helper->submit_action = 'submit'.$this->name;
        $helper->toolbar_btn = [
            'save' => [
                'desc' => $this->l('Save'),
                'href' => AdminController::$currentIndex.'&configure='.$this->name.'&save'.$this->name.
                    '&token='.Tools::getAdminTokenLite('AdminModules'),
            ],
            'back' => [
                'href' => AdminController::$currentIndex.'&token='.Tools::getAdminTokenLite('AdminModules'),
                'desc' => $this->l('Back to list')
            ]
        ];

        $curent_url_api = Tools::getValue('INV_URL_API');
        if ($curent_url_api) {

            $last_character_url = substr($curent_url_api, -1);

            if ($last_character_url !== '/') {
                $curent_url_api .= '/';
            }
        } else {
            $curent_url_api = $url_api;
        }

        // Load current value
        $helper->fields_value['INV_USER_API'] = Tools::getValue('INV_USER_API', $user_api);
        $helper->fields_value['INV_PASS_API'] = Tools::getValue('INV_PASS_API', $pass_api);
        $helper->fields_value['INV_URL_API'] = $curent_url_api;

        return $helper->generateForm($fieldsForm);
    }

    /**
     * Save form data.
     */
    protected function postProcess()
    {
//        Configuration::updateValue('GROUPWRITERSALL', Tools::getValue('GROUPWRITERSALL'));
    }

    /**
     * Add the CSS & JavaScript files you want to be loaded in the BO.
     */
    public function hookDisplayCustomerAccount()
    {
        //       if ($this->context->customer->id_default_group == GROUPWRITERSALL) {
        //           return $this->display(__FILE__, 'myaccount.tpl');
        //    }
    }

    public function hookActionPaymentConfirmation(){

        $products = ViajeroPaqueteHistorial::getAllProductsByCart($this->context->cart->id);
        $products_id = [];
        foreach ($products as $product) {
            $products_id[] = $product['id_product'];
        }

        $api = new Redevtapi();

        $cart = ViajeroPaqueteHistorial::getCart($this->context->cart->id);
        $order = ViajeroPaqueteHistorial::getOrder($this->context->cart->id);
        $packagesHis= ViajeroPaqueteHistorial::getAllPackagesByProduct($products_id);

        $enviarMail = 0;
        foreach ($packagesHis as $packageHis) {
            $roomsHistorial = ViajeroPaqueteHistorialRooms::getHistorialRoomByhistorial($packageHis['id_package_historial']);
            $packageHistorial = new ViajeroPaqueteHistorial($packageHis['id_package_historial']);

            /*
            foreach($roomsHistorial as $roomhis){
                if ($packageHistorial->payment == 0) {

                    $roompack = new ViajeroPaqueteRooms($roomhis['id_package_room']);
                    // $roompack->cant = $roompack->cant - (int) $roomhis['cant_room'];
                    // $roompack->update();

                    $id_hotel = ViajeroRooms::getIdHotelbyRoom($roomhis['id_package_room']);
                    $rooms = Db::getInstance()->ExecuteS('SELECT PR.id_package_room
                                                            FROM `pr_inv_rooms` D
                                                            LEFT JOIN `pr_inv_packages_rooms` AS PR ON PR.id_room = D.id_room
                                                            WHERE PR.id_package = '.$packageHistorial->id_package.'
                                                              AND D.id_hotel = '.$id_hotel);
                    foreach($rooms as $room) {
                        Db::getInstance()->execute('UPDATE pr_inv_packages_rooms
                                                        SET cant = '.($roompack->cant - (int)$roomhis['cant_room']).'
                                                    WHERE id_package_room = '.$room['id_package_room']);
                    }
                }
            }
            */
            $package = new ViajeroPaquetes($packageHistorial->id_package);
            if($packageHistorial->payment==0){

                $linea = new ViajeroPaqueteLinea($packageHistorial->id_package_linea);
                // $linea->inventario = $linea->inventario - ($packageHistorial->cantPassAdult + $packageHistorial->cantPassNinos + $packageHistorial->cantPassBebes);
                $linea->inventario = $linea->inventario - ($packageHistorial->cantPassAdult + $packageHistorial->cantPassNinos);
                if ($linea->inventario <= 0) {
                    $linea->inventario = 0;
                }
                $linea->update();

                //DESCONTAR CUPOS BUTACA
                $seat = new ViajeroPaqueteLineaTransportes($packageHistorial->id_package_butaca);

                $query_type = Db::getInstance()
                    ->ExecuteS('
                    SELECT 
                      tp.id,
                      tp.cupos_butaca
                    FROM pr_inv_packages_linea_transportes_tipos tp 
                    WHERE 
                      tp.id_package = '.$seat->id_package.' 
                      AND  tp.tipo_butaca_id='.$seat->tipo_butaca_id);

                $cant_cupos = $query_type[0]['cupos_butaca'] - ($packageHistorial->cantPassAdult + $packageHistorial->cantPassNinos);

                Db::getInstance()
                    ->execute('
                        UPDATE 
                          pr_inv_packages_linea_transportes_tipos
                        SET cupos_butaca = '.$cant_cupos.'
                        WHERE id = '.$query_type[0]['id']);
                //FIN DESCONTAR PAQUETE BUTACA

            }

            //INSERT INTO `prueba_1`(`id`, `texto_prueba`) VALUES ([value-1],[value-2])
            $texto = 'hookActionPaymentConfirmation '.date('h:m:s');
            $prueba = "INSERT INTO prueba_1 (texto_prueba) VALUES ('$texto')";

            Db::getInstance()->execute($prueba);

            $paid = false;
            if($packageHistorial->payment==0){
                $paid = true;
                $packageHistorial->payment = 1;
                $packageHistorial->reference=$order['reference'];
                $packageHistorial->id_customer=$cart['id_customer'];
                $packageHistorial->update();
            }
            if($enviarMail == 0){

                $enviarMail = $enviarMail + 1;
                $headers  = 'MIME-Version: 1.0' . "\r\n";
                $headers .= 'Content-type: text/html; charset=UTF-8' . "\r\n";
                $headers .= 'From: oferbus@oferbus.com.ar' . "\r\n";
                $headers .= 'Bcc: linguamartin@gmail.com';
                $asuntoBienvenida = "[OFERBUS] Confirmación de pedido";

                $customer = new CustomerCore($packageHistorial->id_customer);
                $servicios = ViajeroPaqueteHistorial::getServiceLine($packageHistorial->id_package_historial);
                //TUTTO CAMBIO ID PACKAGE HISTORIAL
                //$packageorigen = ViajeroPaqueteHistorial::getOrigin2($packageHistorial->id_package_linea);
                $packageorigen = ViajeroPaqueteHistorial::getOrigin2($packageHistorial->id_package_historial);
                $metodoPago = ViajeroPaqueteHistorial::getPaymentMethod($order['reference']);
                $packagePasajeros = ViajeroPaqueteHistorial::getPassagers($packageHistorial->id_package_historial);
                $packageLinea = ViajeroPaqueteHistorial::getOrigin($packageHistorial->id_package_historial);
                $packageButaca = ViajeroPaqueteHistorial::getSeat($packageHistorial->id_package_historial);
                $sal = new DateTime($packageLinea['date_sal']);
                $lle = new DateTime($packageLinea['date_lle']);
                $packageRooms = ViajeroPaqueteHistorial::getRooms($packageHistorial->id_package_historial);
                $fechaTxt = $sal->format('d-m-Y');
                $meses = array("Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre");
                $fechaTxt = strftime("%d de ", strtotime($fechaTxt)) . $meses[intval($sal->format('m'))-1] . " " .  strftime("%Y",strtotime($fechaTxt));

                $body .= '<style>
        .titulo-paquete{
            margin-bottom: 30px;
        }
    </style>
    <div align="center">
    <td align="center" class="logo" style="border-bottom: 4px solid #333333; padding: 7px 0;"> <img src="data:image/jpeg;base64,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"/> </a></td>
    </div>
    <div class="container" style="width: 95%; margin-left: 2.5%; background: #fff; text-align: left; padding: 0px 20px 20px 20px; font-size: 15px; box-shadow: 4px 0px 10px 0px rgba(0,0,0,.2); float: left; background: #2a2a2a; color: white">
        <h1 class="text-center titulo-paquete" style="margin-bottom: 0px">'.$package->name.' (Salida: '.$fechaTxt.')</h1>
    </div>
    
    <div class="container" style="width: 95%; margin-left: 2.5%; background: #fff; text-align: left; padding: 20px; font-size: 15px; box-shadow: 4px 4px 10px 0px rgba(0,0,0,.4);">       
        <div class="col-sm-12">
            <div class="row" style="margin-top: 20px">
                <div class="col-sm-12"><strong>Fecha de Reserva: </strong></div>
                <div class="col-sm-12">'.$packageHistorial->date_add.' </div>
                <div class="col-sm-12"><strong>Referencia: </strong></div>
                <div class="col-sm-12">'.$order['reference'].' - '.$packageHistorial->fileIdRedEvt.'</div>
                <div class="col-sm-12"><strong>Forma de Pago: </strong></div>
                <div class="col-sm-12">'.$metodoPago.'</div>
            </div>
            
            <div class="row" style="margin-top: 20px">
                <div class="col-sm-12 text-center" style="margin-bottom: 15px; background: #666; padding: 10px; color: white"><strong>DETALLE</strong></div>                            
                <div class="col-sm-6"><strong>Salida: '.$sal->format('d-m-Y').' -- Llegada: '.$lle->format('d-m-Y').'</strong></div>
            </div>
            <hr class="col-sm-12">
            <div class="row">
                <div class="col-sm-8"><strong>Origen: </strong></div>
                <div class="col-sm-8">' .$packageorigen['origen'].'</div>
            </div>
            <hr class="col-sm-12">
            <div class="row">
                <div class="col-sm-8"><strong>Servicios: </strong></div>
                <div class="col-sm-8">'.$servicios.'</div>
            </div>
            <hr class="col-sm-12">
            <div class="row">
                <div class="col-sm-8"><strong>Destino: </strong></div>
                <div class="col-sm-8">'.$packageRooms[0]['namedestiny'].'</div>                        
            </div>
            <hr class="col-sm-12">
            <div class="row">
                <div class="col-sm-8"><strong>Impuestos: </strong><span style="font-size: 25px;">$ '.number_format($packageHistorial->impuesto, 0, '', '.').'</span></div>
            </div>
            <hr class="col-sm-12">
            <div class="row">
                <div class="col-sm-8"><strong>Total viaje: </strong><span style="font-size: 25px;">$ '.number_format($packageHistorial->price, 0, '', '.').'</span></div>
            </div>
            <hr class="col-sm-12">'
                ;
                $body .='<div class="row">
            <div class="col-sm-12 text-center" style="margin-bottom: 15px; background: #666; padding: 10px; color: white"><strong>Alojamiento</strong></div>';
                foreach ($packageRooms as $habitacion){
                    $body .= '<div class="row">
                <div class="col-sm-2"><strong>Destino:</strong></div>
                <div class="col-sm-2">'.$habitacion['namedestiny'].'</div>
                <div class="col-sm-2"><strong>Hotel:</strong></div>
                <div class="col-sm-2">'.$habitacion['nameHotel'].'</div>
                <div class="col-sm-2"><strong>Habitacion:</strong></div>
                <div class="col-sm-2">'.$habitacion['name'].'</div>
            </div>   
            <hr class="col-sm-12">';

                }
                $body .='    </div>
            </div>';
                $body .=' <div class="row">';
                $a = 1;
                foreach($packagePasajeros as $pasajeros){
                    if($a==1){
                        $body .=' <div class="col-sm-12 text-center" style="margin-bottom: 15px; background: #666; padding: 10px; color: white"><strong>Pasajeros</strong></div>
                    <div class="col-sm-2"><strong>Cant Adultos:</strong></div>
                    <div class="col-sm-2">'.$packageHistorial->cantPassAdult.'</div>
                    <div class="col-sm-2"><strong>Cant Niños:</strong></div>
                    <div class="col-sm-2">'.$packageHistorial->cantPassNinos.'</div>
                    <div class="col-sm-2"><strong>Cant Bebes:</strong></div>
                    <div class="col-sm-2">'.$packageHistorial->cantPassBebes.'</div>
                    <hr class="col-sm-12">';
                    }
                    else{
                        $body .= '<div class="col-sm-12"></div>';
                    }
                    $body .='<div class="col-sm-3"><strong>Nombre Completo: </strong></div>
                <div class="col-sm-3">'.$pasajeros['nombre'].' '.$pasajeros['apellido'].'</div>
                <div class="col-sm-3 text-right"><strong>Tipo Doc.: </strong></div>
                <div class="col-sm-3 text-right">'.$pasajeros['tipo_doc'].'</div>
				<div class="col-sm-3 text-right"><strong>Nro. Doc.: </strong></div>
                <div class="col-sm-3 text-right">'.$pasajeros['dni'].'</div>
                <div class="col-sm-3 "><strong>Fecha nacimiento: </strong></div>
                <div class="col-sm-3 ">'.$pasajeros['fecha_nacimiento'].'</div>
                <div class="col-sm-3 text-right"><strong>Celular: </strong></div>
                <div class="col-sm-3 text-right">'.$pasajeros['telefono'].'</div>
                <div class="col-sm-3 "><strong>Sexo: </strong></div>
                <div class="col-sm-3 ">'.$pasajeros['sexo'].'</div>
                <div class="col-sm-12"><hr class="col-sm-12"></div>';
                    $a = $a + 1;
                }

                $body .='</div>';
                $body .='
        <div class="row">
    
            <div class="col-sm-12 text-center" style="margin-bottom: 15px; background: #666; padding: 10px; color: white"><strong>Datos del comprador </strong></div>
    
            <div class="col-sm-4 text-center"><strong>Nombre:</strong> </div>
            <div class="col-sm-8 text-right" style="border-bottom: 1px solid #888; padding-bottom: 5px">'.$customer->firstname.'</div>
    
            <div class="col-sm-4 text-center" style="margin-top: 10px"><strong>Apellidos:</strong> </div>
            <div class="col-sm-8 text-right" style="border-bottom: 1px solid #888; padding-bottom: 5px; margin-top: 10px">'.$customer->lastname.'</div>
    
            <div class="col-sm-4 text-center" style="margin-top: 10px"><strong>Correo Electronico:</strong> </div>
            <div class="col-sm-8 text-right" style="border-bottom: 1px solid #888; padding-bottom: 5px; margin-top: 10px">'.$customer->email.'</div>
    
        </div>    
    
    </div>
    
    </div>';


                $enviarMail = $enviarMail + 1;
            }

            $api->generateReservation($packageHistorial, false);
                mail($customer->email,$asuntoBienvenida, $body,$headers);


            // foreach (ViajeroPaquetes::getProperties() as $propie) {

            //     $template_vars = array(
            //         '{reference}' => $order['reference'],
            //         '{packagename}' => $package->name,
            //         '{fecha_salida}' => $linea->date_sal,
            //         '{fecha_llegada}' => $linea->date_lle,
            //     );
            //     //                http://prestashop_dev.local.com/admingm/index.php?controller=AdminViajeroHistorial
            //     $template_vars2 = array(
            //         '{reference}' => $order['reference'],
            //         '{packagename}' => $package->name,
            //         '{fecha_salida}' => $linea->date_sal,
            //         '{fecha_llegada}' => $linea->date_lle,
            //         '{url}' =>  Tools::getShopDomain().'/admingm/index.php?controller=AdminViajeroHistorial',
            //     );
            //     if ($packageHistorial->state !== '') {
            //         $rree = Mail::Send($this->context->language->id, 'ventasincupo', ' Venta sin cupo en pedido #' . $order['reference'], $template_vars, $propie['email'], null, null, null, null, null, $this->local_path . 'mails/');
            //     }

            //     $rre78e = Mail::Send($this->context->language->id, 'venta', ' Se ha generado una compra en Oferbus, pedido #' . $order['reference'], $template_vars2, $propie['email'], null, null, null, null, null, $this->local_path . 'mails/');
            // }

            // if ($package->api == 1 && $status == 'bankwire' && $paid) {
            //     $texto = 'bankwireee '.date('h:m:s');
            //     $prueba = "INSERT INTO prueba_1 (texto_prueba) VALUES ('$texto')";

            //     Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS($prueba);

            //     $api->generateReservation($packageHistorial, false);
            // }

            // if ($package->api == 1 && $status == 'payment' && $paid) {
            //     $texto = 'payment '.date('h:m:s');
            //     $prueba = "INSERT INTO prueba_1 (texto_prueba) VALUES ('$texto')";

            //     Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS($prueba);
            //     $api->generateReservation($packageHistorial, true);
            // }

            // if ($package->api == 1 && !$paid && $status == 'payment' && $packageHistorial->id_proceso) {
            //     $texto = 'payment updated'.date('h:m:s');
            //     $prueba = "INSERT INTO prueba_1 (texto_prueba) VALUES ('$texto')";

            //     $api->updateStateReservation($packageHistorial);
            // }



        }

        return true;
    }

    public function hookActionOrderStatusUpdate($data){
        // Pago transferencia
        // {"name":"En espera de pago por transferencia bancaria","template":"bankwire","send_email":"1","module_name":"ps_wirepayment","invoice":"0","color":"#4169E1","unremovable":"1","logable":"0","delivery":"0","hidden":"0","shipped":"0","paid":"0","pdf_invoice":"0","pdf_delivery":"0","deleted":"0","id":10,"id_shop_list":null,"force_id":false}

        // Pago aceptado
        // {"name":"Pago aceptado","template":"payment","send_email":"1","module_name":"","invoice":"1","color":"#32CD32","unremovable":"1","logable":"1","delivery":"0","hidden":"0","shipped":"0","paid":"1","pdf_invoice":"1","pdf_delivery":"0","deleted":"0","id":2,"id_shop_list":null,"force_id":false}


        $status = $data['newOrderStatus']->template;
        $api = new Redevtapi();

        $products = ViajeroPaqueteHistorial::getAllProductsByCart($this->context->cart->id);
        $products_id = [];
        foreach ($products as $product) {
            $products_id[] = $product['id_product'];
        }

        $cart = ViajeroPaqueteHistorial::getCart($this->context->cart->id);
        $order = ViajeroPaqueteHistorial::getOrder($this->context->cart->id);
        $packagesHis= ViajeroPaqueteHistorial::getAllPackagesByProduct($products_id);

        $enviarMail = 0;
        foreach ($packagesHis as $packageHis) {
            $roomsHistorial = ViajeroPaqueteHistorialRooms::getHistorialRoomByhistorial($packageHis['id_package_historial']);
            $packageHistorial = new ViajeroPaqueteHistorial($packageHis['id_package_historial']);

            foreach($roomsHistorial as $roomhis){



                if ($packageHistorial->payment == 0) {

                    $roompack = new ViajeroPaqueteRooms($roomhis['id_package_room']);
                    // $roompack->cant = $roompack->cant - (int) $roomhis['cant_room'];
                    // $roompack->update();

                    $id_hotel = ViajeroRooms::getIdHotelbyRoom($roomhis['id_package_room']);
                    $rooms = Db::getInstance()->ExecuteS('SELECT PR.id_package_room 
                                                            FROM `pr_inv_rooms` D 
                                                            LEFT JOIN `pr_inv_packages_rooms` AS PR ON PR.id_room = D.id_room 
                                                            WHERE PR.id_package = '.$packageHistorial->id_package.'
                                                              AND D.id_hotel = '.$id_hotel);
                    foreach($rooms as $room) {
                        Db::getInstance()->execute('UPDATE pr_inv_packages_rooms
                                                        SET cant = '.($roompack->cant - (int)$roomhis['cant_room']).'
                                                    WHERE id_package_room = '.$room['id_package_room']);
                    }
                }
            }

            $package = new ViajeroPaquetes($packageHistorial->id_package);
            if($packageHistorial->payment==0){

                $linea = new ViajeroPaqueteLinea($packageHistorial->id_package_linea);
                // $linea->inventario = $linea->inventario - ($packageHistorial->cantPassAdult + $packageHistorial->cantPassNinos + $packageHistorial->cantPassBebes);
                $linea->inventario = $linea->inventario - ($packageHistorial->cantPassAdult + $packageHistorial->cantPassNinos);
                if ($linea->inventario <= 0) {
                    $linea->inventario = 0;
                }
                $linea->update();


                //DESCONTAR CUPOS BUTACA

                $seat = ViajeroPaqueteSeats::getSeatByIdHistory($packageHistorial->id_package_butaca,$packageHistorial->id_package_butaca_tipo_cupo);


                $cant_cupos = $seat['cupos_butaca'] - ($packageHistorial->cantPassAdult + $packageHistorial->cantPassNinos);

                $sql_updated = "UPDATE
                          pr_inv_packages_linea_transportes_tipos
                         SET cupos_butaca = $cant_cupos
                         WHERE id = ". $seat['id_tlp'];


                 Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS($sql_updated);

                //FIN DESCONTAR PAQUETE BUTACA



            }

            $paid = false;
            if($packageHistorial->payment ==0){
                $paid = true;
                $packageHistorial->payment = 1;
                $packageHistorial->reference=$order['reference'];
                $packageHistorial->id_customer=$cart['id_customer'];
                $packageHistorial->update();
            }

            if($enviarMail == 0){
                $enviarMail = $enviarMail + 1;
                $headers  = 'MIME-Version: 1.0' . "\r\n";
                $headers .= 'Content-type: text/html; charset=UTF-8' . "\r\n";
                $headers .= 'From: oferbus@oferbus.com.ar' . "\r\n";
                $headers .= 'Bcc: linguamartin@gmail.com';
                $asuntoBienvenida = "[OFERBUS] Confirmación de pedido";

                $customer = new CustomerCore($packageHistorial->id_customer);
                $servicios = ViajeroPaqueteHistorial::getServiceLine($packageHistorial->id_package_historial);
                //TUTTO CAMBIO ID PACKAGE HISTORIAL
                //$packageorigen = ViajeroPaqueteHistorial::getOrigin2($packageHistorial->id_package_linea);
                $packageorigen = ViajeroPaqueteHistorial::getOrigin2($packageHistorial->id_package_historial);
                $metodoPago = ViajeroPaqueteHistorial::getPaymentMethod($order['reference']);
                $packagePasajeros = ViajeroPaqueteHistorial::getPassagers($packageHistorial->id_package_historial);
                $packageLinea = ViajeroPaqueteHistorial::getOrigin($packageHistorial->id_package_historial);
                $packageButaca = ViajeroPaqueteHistorial::getSeat($packageHistorial->id_package_historial);
                $sal = new DateTime($packageLinea['date_sal']);
                $lle = new DateTime($packageLinea['date_lle']);
                $packageRooms = ViajeroPaqueteHistorial::getRooms($packageHistorial->id_package_historial);
                $fechaTxt = $sal->format('d-m-Y');
                $meses = array("Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre");
                $fechaTxt = strftime("%d de ", strtotime($fechaTxt)) . $meses[intval($sal->format('m'))-1] . " " .  strftime("%Y",strtotime($fechaTxt));

                $body .= '<style>
        .titulo-paquete{
            margin-bottom: 30px;
        }
    </style>
    <div align="center">
    <td align="center" class="logo" style="border-bottom: 4px solid #333333; padding: 7px 0;"> <img src="data:image/jpeg;base64,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�'    p�'                     ('            �%'    ��'            ��'     @      ��'            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"/> </a></td>
    </div>
    <div class="container" style="width: 95%; margin-left: 2.5%; background: #fff; text-align: left; padding: 0px 20px 20px 20px; font-size: 15px; box-shadow: 4px 0px 10px 0px rgba(0,0,0,.2); float: left; background: #2a2a2a; color: white">
        <h1 class="text-center titulo-paquete" style="margin-bottom: 0px">'.$package->name.' (Salida: '.$fechaTxt.')</h1>
    </div>
    
    <div class="container" style="width: 95%; margin-left: 2.5%; background: #fff; text-align: left; padding: 20px; font-size: 15px; box-shadow: 4px 4px 10px 0px rgba(0,0,0,.4);">       
        <div class="col-sm-12">
            <div class="row" style="margin-top: 20px">
                <div class="col-sm-12"><strong>Fecha de Reserva: </strong></div>
                <div class="col-sm-12">'.$packageHistorial->date_add.' </div>
                <div class="col-sm-12"><strong>Referencia: </strong></div>
                <div class="col-sm-12">'.$order['reference'].'</div>
                <div class="col-sm-12"><strong>Forma de Pago: </strong></div>
                <div class="col-sm-12">'.$metodoPago.'</div>
            </div>
            
            <div class="row" style="margin-top: 20px">
                <div class="col-sm-12 text-center" style="margin-bottom: 15px; background: #666; padding: 10px; color: white"><strong>DETALLE</strong></div>                            
                <div class="col-sm-6"><strong>Salida: '.$sal->format('d-m-Y').' -- Llegada: '.$lle->format('d-m-Y').'</strong></div>
            </div>
            <hr class="col-sm-12">
            <div class="row">
                <div class="col-sm-8"><strong>Origen: </strong></div>
                <div class="col-sm-8">' .$packageorigen['origen'].'</div>
            </div>
            <hr class="col-sm-12">
            <div class="row">
                <div class="col-sm-8"><strong>Servicios: </strong></div>
                <div class="col-sm-8">'.$servicios.'</div>
            </div>
            <hr class="col-sm-12">
            <div class="row">
                <div class="col-sm-8"><strong>Destino: </strong></div>
                <div class="col-sm-8">'.$packageRooms[0]['namedestiny'].'</div>                        
            </div>
            <hr class="col-sm-12">
            <div class="row">
                <div class="col-sm-8"><strong>Impuestos: </strong><span style="font-size: 25px;">$ '.number_format($packageHistorial->impuesto, 0, '', '.').'</span></div>
            </div>
            <hr class="col-sm-12">
            <div class="row">
                <div class="col-sm-8"><strong>Total viaje: </strong><span style="font-size: 25px;">$ '.number_format($packageHistorial->price, 0, '', '.').'</span></div>
            </div>
            <hr class="col-sm-12">'
                ;
                $body .='<div class="row">
            <div class="col-sm-12 text-center" style="margin-bottom: 15px; background: #666; padding: 10px; color: white"><strong>Alojamiento</strong></div>';
                foreach ($packageRooms as $habitacion){
                    $body .= '<div class="row">
                <div class="col-sm-2"><strong>Destino:</strong></div>
                <div class="col-sm-2">'.$habitacion['namedestiny'].'</div>
                <div class="col-sm-2"><strong>Hotel:</strong></div>
                <div class="col-sm-2">'.$habitacion['nameHotel'].'</div>
                <div class="col-sm-2"><strong>Habitacion:</strong></div>
                <div class="col-sm-2">'.$habitacion['name'].'</div>
            </div>   
            <hr class="col-sm-12">';

                }
                $body .='    </div>
            </div>';
                $body .=' <div class="row">';
                $a = 1;
                foreach($packagePasajeros as $pasajeros){
                    if($a==1){
                        $body .=' <div class="col-sm-12 text-center" style="margin-bottom: 15px; background: #666; padding: 10px; color: white"><strong>Pasajeros</strong></div>
                    <div class="col-sm-2"><strong>Cant Adultos:</strong></div>
                    <div class="col-sm-2">'.$packageHistorial->cantPassAdult.'</div>
                    <div class="col-sm-2"><strong>Cant Niños:</strong></div>
                    <div class="col-sm-2">'.$packageHistorial->cantPassNinos.'</div>
                    <div class="col-sm-2"><strong>Cant Bebes:</strong></div>
                    <div class="col-sm-2">'.$packageHistorial->cantPassBebes.'</div>
                    <hr class="col-sm-12">';
                    }
                    else{
                        $body .= '<div class="col-sm-12"></div>';
                    }
                    $body .='<div class="col-sm-3"><strong>Nombre Completo: </strong></div>
                <div class="col-sm-3">'.$pasajeros['nombre'].' '.$pasajeros['apellido'].'</div>
                <div class="col-sm-3 text-right"><strong>Tipo Doc: </strong></div>
                <div class="col-sm-3 text-right">'.$pasajeros['tipo_doc'].'</div>
				<div class="col-sm-3 text-right"><strong>Nro. Doc.: </strong></div>
                <div class="col-sm-3 text-right">'.$pasajeros['dni'].'</div>
                <div class="col-sm-3 "><strong>Fecha nacimiento: </strong></div>
                <div class="col-sm-3 ">'.$pasajeros['fecha_nacimiento'].'</div>
                <div class="col-sm-3 text-right"><strong>Celular: </strong></div>
                <div class="col-sm-3 text-right">'.$pasajeros['telefono'].'</div>
                <div class="col-sm-3 "><strong>Sexo: </strong></div>
                <div class="col-sm-3 ">'.$pasajeros['sexo'].'</div>
                <div class="col-sm-12"><hr class="col-sm-12"></div>';
                    $a = $a + 1;
                }

                $body .='</div>';
                $body .='
        <div class="row">
    
            <div class="col-sm-12 text-center" style="margin-bottom: 15px; background: #666; padding: 10px; color: white"><strong>Datos del comprador </strong></div>
    
            <div class="col-sm-4 text-center"><strong>Nombre:</strong> </div>
            <div class="col-sm-8 text-right" style="border-bottom: 1px solid #888; padding-bottom: 5px">'.$customer->firstname.'</div>
    
            <div class="col-sm-4 text-center" style="margin-top: 10px"><strong>Apellidos:</strong> </div>
            <div class="col-sm-8 text-right" style="border-bottom: 1px solid #888; padding-bottom: 5px; margin-top: 10px">'.$customer->lastname.'</div>
    
            <div class="col-sm-4 text-center" style="margin-top: 10px"><strong>Correo Electronico:</strong> </div>
            <div class="col-sm-8 text-right" style="border-bottom: 1px solid #888; padding-bottom: 5px; margin-top: 10px">'.$customer->email.'</div>
    
        </div>    
    
    </div>
    
    </div>';

                //TUTTO: lo muevo al hookActionPaymentConfirmation
                //mail($customer->email,$asuntoBienvenida, $body,$headers);
                $enviarMail = $enviarMail + 1;
            }

            foreach (ViajeroPaquetes::getProperties() as $propie) {

                $template_vars = array(
                    '{reference}' => $order['reference'],
                    '{packagename}' => $package->name,
                    '{fecha_salida}' => $linea->date_sal,
                    '{fecha_llegada}' => $linea->date_lle,
                );
                //                http://prestashop_dev.local.com/admingm/index.php?controller=AdminViajeroHistorial
                $template_vars2 = array(
                    '{reference}' => $order['reference'],
                    '{packagename}' => $package->name,
                    '{fecha_salida}' => $linea->date_sal,
                    '{fecha_llegada}' => $linea->date_lle,
                    '{url}' =>  Tools::getShopDomain().'/admingm/index.php?controller=AdminViajeroHistorial',
                );
                if ($packageHistorial->state !== '') {
                    $rree = Mail::Send($this->context->language->id, 'ventasincupo', ' Venta sin cupo en pedido #' . $order['reference'], $template_vars, $propie['email'], null, null, null, null, null, $this->local_path . 'mails/');
                }

                $rre78e = Mail::Send($this->context->language->id, 'venta', ' Se ha generado una compra en Oferbus, pedido #' . $order['reference'], $template_vars2, $propie['email'], null, null, null, null, null, $this->local_path . 'mails/');
            }


            $orderActual = new Order((int)$this->context->cart->id);

            if( $orderActual->current_state != 6 && $orderActual->current_state != 10 && $orderActual->current_state != 9 ){
                if ($package->api == 1 && $status == 'bankwire' && $paid) {
                    $api->generateReservation($packageHistorial, false);
                    mail($customer->email,$asuntoBienvenida, $body,$headers);
                }
            }

            if ($package->api == 1 && $paid && $status == 'payment') {

                    $api->generateReservation($packageHistorial, false);
                    mail($customer->email,$asuntoBienvenida, $body,$headers);

            }

            if ($package->api == 1 && !$paid && $status == 'payment' && $packageHistorial->id_proceso) {

                $api->updateStateReservation($packageHistorial);
            }

        }

        return true;
    }

    public function sendMail() {

    }

    public function hookDisplayProductTab() {
        $this->context->controller->addCSS($this->_path . '/views/css/front/bootstrap.css');
        $this->context->controller->addCSS($this->_path . '/views/css/front/font-awesome.css');
        $this->context->controller->addJS($this->_path . 'views/js/jquery.validate.js');
        $this->context->controller->addJS($this->_path . 'views/js/frontProduct.js');
        $this->context->smarty->assign(array(
            'ViajeroProduct' => $this->context->link->getModuleLink($this->name, 'ViajeroProduct'),
        ));
        return $this->display(__FILE__, 'front/viajeroProduct.tpl');
    }

    public function hookDisplayTopViajero(){
        $this->context->controller->addCSS($this->_path . '/views/css/front/bootstrap.css');
        $this->context->controller->addCSS($this->_path . '/views/css/front/availables.css');
        $this->context->controller->addCSS($this->_path . '/views/css/front/viajeroTop.css');
        $this->context->controller->addJS($this->_path . 'views/js/jquery.validate.js');


        return $this->display(__FILE__, 'front/searchViajero.tpl');
    }

    public function hookDisplayHomeTop(){
        if (!isset($this->context->controller->php_self)){
            return;
        } elseif($this->context->controller->php_self != 'index'){
            return;
        }

        $this->context->controller->addCSS($this->_path . '/views/css/front/bootstrap.css');
        $this->context->controller->addCSS($this->_path . '/views/css/front/availables.css');
        $this->context->controller->addCSS($this->_path . '/views/css/front/select2.min.css');
        $this->context->controller->addCSS($this->_path . '/views/css/front/viajeroTop.css');
        $this->context->controller->addCSS($this->_path . '/views/css/front/override.css');
        $this->context->controller->addJS($this->_path . 'views/js/select2.min.js');
        $this->context->controller->addJS($this->_path . 'views/js/searchviajero.js');
        $this->context->controller->addJS($this->_path . 'views/js/jquery.validate.js');
        $this->context->controller->addJqueryPlugin('autocomplete');
        $this->context->controller->addJS($this->_path . 'views/js/front.js');
        $months = [
            1 => 'Enero',
            2 => 'Febrero',
            3 => 'Marzo',
            4 => 'Abril',
            5 => 'Mayo',
            6 => 'Junio',
            7 => 'Julio',
            8 => 'Agosto',
            9 => 'Septiembre',
            10 => 'Octubre',
            11 => 'Noviembre',
            12 => 'Diciembre'
        ];
        $origenes = ViajeroOrigenes::getOrigins();
        $destinos = ViajeroDestinos::getDestinations();
//        $dest = str_replace('"','\"',json_encode($destinos));
//        var_dump($dest);exit();
        $this->context->smarty->assign(array(
            'module_dir' => $this->_path,
            'origenes' => $origenes,
            'origenes2' => json_encode($origenes),
            'destinos' => json_encode($destinos),
            'destinos2' => $destinos,
            'urlSearch' => $this->context->link->getModuleLink($this->name, 'ViajeroSearch'),
            'months' => $months
        ));
        return $this->display(__FILE__, 'front/searchViajero.tpl');
    }
    public function hookDisplayHeader()
    {
        $this->context->controller->addCSS($this->getPathUri() . '/views/css/front/bootstrap.css');
        $this->context->controller->addCSS($this->getPathUri() . '/views/css/front/availables.css');
        $this->context->controller->addCSS($this->getPathUri() . '/views/css/front/viajeroTop.css');

        // Modificación añadida por Sebastian Leiva
        $this->context->controller->addCSS($this->getPathUri() . '/views/css/front/smart_wizard.min.css');
        $this->context->controller->addCSS($this->getPathUri() . '/views/css/front/smart_wizard_theme_circles.min.css');
        // Modificación añadida por Sebastian Leiva


        $this->context->controller->addCSS($this->_path . '/views/css/front/bootstrap.css');
        $this->context->controller->addCSS($this->_path . '/views/css/front/availables.css');
        $this->context->controller->addCSS($this->_path . '/views/css/front/select2.min.css');
        $this->context->controller->addCSS($this->_path . '/views/css/front/viajeroTop.css');
        $this->context->controller->addJS($this->_path . 'views/js/select2.min.js');
        $this->context->controller->addJS($this->_path . 'views/js/searchviajero.js');
        $this->context->controller->addJqueryPlugin('autocomplete');
        $this->context->controller->addJS($this->_path . 'views/js/front.js');
        $this->context->controller->addJS($this->_path . 'views/js/headerviajero.js');
        $this->context->controller->addJS($this->_path . 'views/js/frontProduct.js');
        $this->context->controller->addJS($this->_path . 'views/js/jquery.validate.js');
        $this->context->controller->addJS($this->_path . 'views/js/bootstrap.min.js');
        $this->context->controller->addJS($this->_path . 'views/js/fontawesome.js');

        //modificación añadida por Carlos Espinoza
        /* $this->context->controller->addJS($this->_path . 'views/js/viajero_selector_de_pasajeros.js'); */
        //modificación añadida por Carlos Espinoza


        //modificación añadida por Sebastian Leiva
        $this->context->controller->addJS($this->_path . 'views/js/viajero_pasajeros.js');
        $this->context->controller->addJS($this->_path . 'views/js/jquery.smartWizard.min.js');
        $this->context->controller->addJS($this->_path . 'views/js/form-steps.js');
        //modificación añadida por Sebastian Leiva

        $this->context->controller->addCSS($this->_path . '/views/css/front/frontProduct.css');
        $this->context->controller->addCSS($this->_path . 'views/css/front/bootstrap.min.css');
        $this->context->controller->addCSS($this->_path . '/views/css/front/all.css');
        $this->context->controller->addCSS($this->_path . '/views/css/front/fontawesome.css');
        $this->context->controller->addCSS($this->_path . '/views/css/front/frontProduct.css');
        $this->context->controller->addJqueryUI('ui.datepicker');
//        $this->context->controller->addJS($this->_path . 'views/js/searchviajero.js');
        $this->context->controller->addCSS($this->_path . '/css/front.css');
        if (Tools::getValue('package')) {
            $photos = ViajeroPackagePhotos::getPhotosByPackage(Tools::getValue('package'));
            $this->context->smarty->assign(array(
                'ViajeroProduct' => $this->context->link->getModuleLink($this->name, 'ViajeroProduct'),
                'Packagephoto' => 'modules/viajero/uploads/img/package/' . Tools::getValue('package').'/'.$photos[0]['url'],
            ));
        } else {
            $this->context->smarty->assign(array(
                'ViajeroProduct' => $this->context->link->getModuleLink($this->name, 'ViajeroProduct')
            ));
        }

        return $this->display(__FILE__, 'header.tpl');
    }
    /**
     * Add the CSS & JavaScript files you want to be added on the FO.
     */
    public function hookHeader()
    {
        return $this->display(__FILE__, 'header.tpl');
    }

    public function hookTop($params)
    {
        $this->smarty->assign(array(
            'this_path' => $this->_path,
            'link' => Context::getContext()->link,
        ));

        $this->smarty->assign('stock', array(

        ));

        return $this->display(__FILE__, 'views/js/customScript.tpl');
    }

    public function getForm($tpl)
    {
        return $this->display(__FILE__, 'admin/' . $tpl);
    }
}
