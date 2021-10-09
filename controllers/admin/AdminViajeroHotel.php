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
 * */
class AdminViajeroHotelController extends ModuleAdminController {

    protected $status_array = array();
    protected $destinations = array();

    public function __construct() {
        $this->bootstrap = true;
        $this->table = "inv_hotels";
        $this->className = "ViajeroHotel";
        $this->identifier = "id_hotel";
        $this->fields_list = array();
        parent::__construct();
//                $this->lang = true;
        $this->actions = array('edit', 'delete');
        $this->_select = 'des.`destiny` as destino ,';
        $this->_join = 'LEFT JOIN `' . _DB_PREFIX_ . 'inv_destinations` des ON des.id_destiny = a.id_destiny';
        $destinations = ViajeroDestinos::getDestinations();
        
        foreach ($destinations as $destiny) {
            $this->destinations[$destiny['id_destiny']] = $destiny['destiny'];
        }

        $this->fields_list = array(
            'id_hotel' => array(
                'title' => $this->l('ID'),
            ),
            'name' => array(
                'title' => $this->l('Hotel')
            ),
            'destino' => array(
                'title' => $this->l('Destino'),
                'type' => 'select',
                'list' => $this->destinations,
                'filter_key' => 'a!id_destiny'),
            'latitud' => array(
                'title' => $this->l('Latitud')
            ),
            'longitud' => array(
                'title' => $this->l('Longitud')
            ),
            'stars' => array(
                'title' => $this->l('Estrellas')
            ),
            // 'limninos' => array(
            //     'title' => $this->l('Limite edad niños')
            // ),
            // 'limbebes' => array(
            //     'title' => $this->l('Limite edad bebes')
            // ),
        );
    }

    public function postProcess() {
//        var_dump($_POST);exit();
        if (Tools::getValue('action') == "uploadPhoto") {
            echo json_encode($this->tempphoto());
        }

        if (Tools::getValue('action') == "deleteImgTemp") {
            echo json_encode($this->delTempPhoto());
        }
        if (Tools::isSubmit('submitAddinv_hotels')) {
            $this->saveRoom();
        }
        if (Tools::getValue('deleteinv_hotels') !== false) {
            $this->deletePhotos();
            parent::postProcess();
        }
        if(Tools::getValue('submitResetinv_hotels')){
            parent::postProcess();
        }
        if(Tools::getValue('submitFilterinv_hotels')){
            parent::postProcess();
        }
//        parent::postProcess();
    }

    public function saveRoom() {
        if (Tools::getValue('id_hotel') !== false) {
            $hotel = new ViajeroHotel(Tools::getValue('id_hotel'));
        } else {
            $hotel = new ViajeroHotel();
        }

        $hotel->id_destiny = Tools::getValue('id_destiny');
        $hotel->name = Tools::getValue('name');
        $hotel->latitud = Tools::getValue('latitud');
        $hotel->longitud = Tools::getValue('longitud');
        $hotel->stars = Tools::getValue('stars');
        // $hotel->limninos = Tools::getValue('limninos');
        // $hotel->limbebes = Tools::getValue('limbebes');
        $hotel->limninos = 0;
        $hotel->limbebes = 0;
        $hotel->description = Tools::getValue('description');
        if (Tools::getValue('id_hotel') !== false) {
            $hotel->update();
        } else {
            $hotel->tok = Tools::getValue('tok');
            $hotel->save();
            $this->saveImages($hotel->id, $hotel->tok);
        }
        return $hotel->id;
    }

    public function initPageHeaderToolbar() {
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
            $this->page_header_toolbar_btn['new_seat'] = array(
                'href' => $this->context->link->getAdminLink('AdminViajeroButaca'),
                'desc' => $this->l('Butacas', null, null, false),
                'icon' => 'icon-pushpin'
            );
        }   
        if (empty($this->display)) {
            $this->page_header_toolbar_btn['new_aditional'] = array(
                'href' => $this->context->link->getAdminLink('AdminViajeroAdicionales'),
                'desc' => $this->l('Adicionales', null, null, false),
                'icon' => 'icon-gittip '
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
            $this->page_header_toolbar_btn['add_hotel'] = array(
                'href' => $this->context->link->getAdminLink('AdminViajeroHotel') . '&addinv_hotels',
                'desc' => $this->l('Agregar Hotel', null, null, false),
                'icon' => 'icon-adn '
            );
        }
        parent::initPageHeaderToolbar();
    }

    public function renderForm() {
        $this->fields_form = array(
            'legend' => array(
                'title' => $this->l("Hotel"),
                'icon' => 'icon-folder-close'
            ),
            'input' => array(
                array(
                    'type' => 'select',
                    'label' => $this->l('Destino'),
                    'name' => 'id_destiny',
                    'col' => '4',
                    'options' => array(
                        'query' => ViajeroDestinos::getDestinations(),
                        'id' => 'id_destiny',
                        'name' => 'destiny'
                    )
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l("Nombre Del Hotel"),
                    'name' => 'name',
                    'col' => '4',
                    'required' => true
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l("Latitud"),
                    'name' => 'latitud',
                    'col' => '2',
                    'hint' => $this->l('Coordenadas para mapas ubicacion hotel'),
                    'required' => true
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l("Longitud"),
                    'name' => 'longitud',
                    'col' => '2',
                    'hint' => $this->l('Coordenadas para mapas ubicacion hotel'),
                    'required' => true
                ),
                // array(
                //     'type' => 'text',
                //     'label' => $this->l("Limite edad niños"),
                //     'name' => 'limninos',
                //     'col' => '4',
                //     'required' => true
                // ),
                // array(
                //     'type' => 'text',
                //     'label' => $this->l("Limite edad bebes"),
                //     'name' => 'limbebes',
                //     'col' => '4',
                //     'required' => true
                // ),
                array(
                    'type' => 'select',
                    'label' => $this->l('Estrellas'),
                    'name' => 'stars',
                    'required' => true,
                    'options' => array(
                        'query' => array(
                            array(
                                'id' => 0,
                                'name' => $this->l('0')
                            ),
                            array(
                                'id' => 1,
                                'name' => $this->l('1')
                            ),
                            array(
                                'id' => 2,
                                'name' => $this->l('2')
                            ),
                            array(
                                'id' => 3,
                                'name' => $this->l('3')
                            ),
                            array(
                                'id' => 4,
                                'name' => $this->l('4')
                            ),
                            array(
                                'id' => 5,
                                'name' => $this->l('5')
                            ),
                            array(
                                'id' => 6,
                                'name' => $this->l('6')
                            ),
                        ),
                        'id' => 'id',
                        'name' => 'name'
                    ),
                ),
                array(
                    'type' => 'textarea',
                    'label' => $this->l("Descripción"),
                    'name' => 'description',
                    'autoload_rte' => true,

                ),
            ),
            'submit' => array(
                'title' => $this->l('Save'),
            )
        );        
//        var_dump($_GET);EXIT();
        $hote = new ViajeroHotel(Tools::getValue('id_hotel'));
//        var_dump(Tools::htmlentitiesDecodeUTF8($hote->description));exit();
        $this->context->smarty->assign(array(
            'urlajax' => $this->context->link->getAdminLink('AdminViajeroHotel'),
            'urlviajero' => $this->module->getPathUri(),
            'photos' => Tools::getValue('id_hotel')!=''?ViajeroHotelsphotos::getPhotosByHotel(Tools::getValue('id_hotel')):false,
            'hotel' => Tools::getValue('id_hotel'),
            'description' => Tools::htmlentitiesDecodeUTF8($hote->description),
        ));
        $this->context->controller->addJS($this->module->getPathUri() . 'views/js/backhotels.js');
        $this->context->controller->addJS($this->module->getPathUri() . 'views/js/jquery.validate.js');
        $this->content .= $this->module->getForm('hotels/form.tpl');
        return parent::renderForm();
    }

    public function tempphoto() {
        if (Tools::getValue('hotel') !== '') {
            $folder = _PS_MODULE_DIR_ . 'viajero/uploads/img/hotels/' . Tools::getValue('hotel');
            $photoHotel = new ViajeroHotelsphotos();
            $photoHotel->id_hotel = Tools::getValue('hotel');
            $photoHotel->url = $_FILES['image']['name'];
            $photoHotel->save();
        } else {
            $folder = _PS_MODULE_DIR_ . 'viajero/uploads/tmp/hotels/img_' . Tools::getValue('tok');
        }
        if (!file_exists($folder)) {
            mkdir($folder, 0777);
        }
        $origen = $_FILES["image"]["tmp_name"];
        $name = $_FILES['image']['name'];
        $destino = $folder . "/" . $name;
        if (!file_exists($destino)) {
            if (move_uploaded_file($origen, $destino)) {
                return $name;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    public function delTempPhoto() {
        if (Tools::getValue('hotelphoto') !== '') {
            $folder = _PS_MODULE_DIR_ . 'viajero/uploads/img/hotels/' . Tools::getValue('hotel') . '/' . Tools::getValue('name');
            $photoroom = new ViajeroHotelsphotos((int) Tools::getValue('hotelphoto'));
            $photoroom->delete();
        } else {
            $name = Tools::getValue('name');
            $folder = _PS_MODULE_DIR_ . 'viajero/uploads/tmp/hotels/img_' . Tools::getValue('tok') . '/' . $name;
        }
        if (file_exists($folder)) {
            unlink($folder);
            return true;
        } else {
            return false;
        }
    }

    public function saveImages($id, $tok) {
        $to = _PS_MODULE_DIR_ . 'viajero/uploads/img/hotels/' . $id;
        if (!file_exists($to)) {
            mkdir($to, 0777);
        }
        $from = _PS_MODULE_DIR_ . 'viajero/uploads/tmp/hotels/img_' . $tok;

        $dir = opendir($from);
        if (file_exists($from)) {
            while (($file = readdir($dir)) !== false) {
                if (strpos($file, '.') !== 0) {
                    copy($from . '/' . $file, $to . '/' . $file);
                    $photohotel = new ViajeroHotelsphotos();
                    $photohotel->id_hotel = $id;
                    $photohotel->url = $file;
                    $photohotel->save();
                }
            }
        }
        $this->rmDirrf($from);
        return true;
    }

    protected function deletePhotos() {
        $photos = ViajeroHotelsphotos::getPhotosByHotel(Tools::getValue('id_hotel'));
        foreach ($photos as $photo) {
            $photoRoom = new ViajeroHotelsphotos($photo['id_hotel_photo']);
            $photoRoom->delete();
        }
        $folder = _PS_MODULE_DIR_ . 'viajero/uploads/img/hotels/' . Tools::getValue('id_hotel');
        $this->rmDirrf($folder);
        return true;
    }

    public function rmDirrf($carpeta) {
        foreach (glob($carpeta . "/*") as $archivos_carpeta) {
            if (is_dir($archivos_carpeta)) {
                $this->rmDirrf($archivos_carpeta);
            } else {
                unlink($archivos_carpeta);
            }
        }
        rmdir($carpeta);
        return true;
    }

}
