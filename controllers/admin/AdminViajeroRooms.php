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

class AdminViajeroRoomsController extends ModuleAdminController
{

    protected $rooms = array();

    public function __construct()
    {
        $this->bootstrap = true;
        $this->table = "inv_rooms";
        $this->className = "ViajeroRooms";
        $this->identifier = "id_room";
        $this->fields_list = array();
        parent::__construct();
        $this->actions = array('edit', 'delete');
        $this->_select = 'h.`name` as hotel ,d.destiny as destino';
        $this->_join = 'LEFT JOIN `' . _DB_PREFIX_ . 'inv_hotels` h ON h.id_hotel= a.id_hotel LEFT JOIN `' . _DB_PREFIX_ . 'inv_destinations` d ON h.id_destiny= d.id_destiny';
//        $this->_join = ;
        $hotels = ViajeroHotel::getHotels();
        foreach ($hotels as $hotels) {
            $this->hotels[$hotels['id_hotel']] = $hotels['name'];
        }
        $destinos = ViajeroDestinos::getDestinations();
        foreach ($destinos as $destinos) {
            $this->destinos[$destinos['id_destiny']] = $destinos['destiny'];
        }
        $this->fields_list = array(
            'id_room' => array(
                'title' => $this->l('ID'),
            ),
            'name' => array(
                'title' => $this->l('Nombre')
            ),
            'destino' => array(
                'title' => $this->l('Destino'),
                'type' => 'select',
                'list' => $this->destinos,
                'filter_key' => 'h!id_destiny'
            ),
            'hotel' => array(
                'title' => $this->l('Hotel'),
                'type' => 'select',
                'list' => $this->hotels,
                'filter_key' => 'a!id_hotel'
            ),
            'cant' => array(
                'title' => $this->l('Cantidad de personas')
            ),            
            'observations' => array(
                'title' => $this->l('Descripción'),
                'callback' => 'getDescriptionClean',
            ),
        );
    }
    
    public static function getDescriptionClean($description)
    {
        return Tools::getDescriptionClean($description);
    }
    public function postProcess()
    {
        if ($this->ajax) {
            if (Tools::getValue('action') == "uploadPhoto") {
                echo json_encode($this->tempphoto());
            }
            if (Tools::getValue('action') == "getHotelsByDestiny") {
                 die(Tools::jsonEncode(ViajeroHotel::getHotelsByDestiny(Tools::getValue('id_destiny'))));             
            }            
            if (Tools::getValue('action') == "getDestinyByHotel") {
                $hotel =new ViajeroHotel(Tools::getValue('id_hotel'));
                $destiny = new ViajeroDestinos($hotel->id_destiny);                
                die(Tools::jsonEncode($destiny));             
            }            
            if (Tools::getValue('action') == "deleteImgTemp") {
                echo json_encode($this->delTempPhoto());
            }
        } else {
            if (Tools::isSubmit('submitAddinv_rooms')) {
                $this->saveRoom();
            }
            if (Tools::getValue('deleteinv_rooms')!==false) {
                $room = new ViajeroRooms(Tools::getValue('id_room'));
                $attribute = new AttributeCore($room->id_attribute);
                $attribute->delete();
                $this->deletePhotos();
                parent::postProcess();
            }
            if (Tools::getValue('submitResetinv_rooms')) {
                parent::postProcess();
            }
            if (Tools::getValue('submitFilterinv_rooms')) {
                parent::postProcess();
            }
        }
//        parent::postProcess();
    }

    
    public function saveRoom()
    {
        if (Tools::getValue('id_room') !== false) {
            $room = new ViajeroRooms(Tools::getValue('id_room'));            
        } else {
            $room = new ViajeroRooms();           
        }
        
        $room->id_hotel = Tools::getValue('id_hotel');
        $room->name = Tools::getValue('name');
        $room->id_attribute =  $attribute->id;
        $room->cant = Tools::getValue('cant');
        $room->price = Tools::getValue('price');
        $room->observations = Tools::getValue('observations');
        if (Tools::getValue('id_room') !== false) {
            $room->update();
//            $this->saveImages(Tools::getValue('id_room'),Tools::getValue('tok'));
        } else {
            $room->tok = Tools::getValue('tok');
            $room->save();
            $this->saveImages($room->id, $room->tok);
        }

        return $room->id;
    }
    public function initPageHeaderToolbar()
    {
        if (empty($this->display)) {
            $this->page_header_toolbar_btn['new_Destiny'] = array(
                'href' => $this->context->link->getAdminLink('AdminViajeroDestino'),
                'desc' => $this->l('Destinos de viaje', null, null, false),
                'icon' => 'icon-map-marker'
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
            $this->page_header_toolbar_btn['new_package'] = array(
                'href' => $this->context->link->getAdminLink('AdminViajeroPaquete'),
                'desc' => $this->l('Paquetes de viaje', null, null, false),
                'icon' => 'icon-windows'
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
            $this->page_header_toolbar_btn['add_room'] = array(
                'href' => $this->context->link->getAdminLink('AdminViajeroRooms').'&addinv_rooms',
                'desc' => $this->l('Agregar Habitación', null, null, false),
                'icon' => 'icon-adn '
            );
        }
        parent::initPageHeaderToolbar();
    }
    public function renderForm()
    {
        $this->fields_form = array(
            'legend' => array(
                'title' => $this->l("Habitaciones"),
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
                    'type' => 'select',
                    'label' => $this->l('Hotel'),
                    'name' => 'id_hotel',
                    'col' => '4',
                    'options' => array(
                        'query' => ViajeroHotel::getHotels(),
                        'id' => 'id_hotel',
                        'name' => 'name'
                    )
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l("Nombre"),
                    'name' => 'name',
                    'col' => '6',
                    'required' => true
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l("Cantidad de personas"),
                    'name' => 'cant',
                    'col' => '2',
                    'hint' => $this->l('Caracteres validos:') . ' 0-9',
                    'required' => true
                ),               
                array(
                    'type' => 'textarea',
                    'label' => $this->l("Descripción"),
                    'name' => 'observations',
                    'autoload_rte' => true,
                ),
            ),
            'submit' => array(
                'title' => $this->l('Guardar'),
            )
        );
        $this->context->smarty->assign(array(
            'urlajax' => $this->context->link->getAdminLink('AdminViajeroRooms'),
            'urlviajero' => $this->module->getPathUri(),
            'photos' => ViajeroRoomsphotos::getPhotosByRooms(Tools::getValue('id_room')),
            'room' => Tools::getValue('id_room'),
            'Hoteles' => ViajeroHotel::getHotels(),
        ));
        $this->context->controller->addJS($this->module->getPathUri() . 'views/js/back.js');
        $this->context->controller->addJS($this->module->getPathUri() . 'views/js/jquery.validate.js');
        $this->content .= $this->module->getForm('rooms/form.tpl');
        return parent::renderForm();
    }
    public function tempphoto()
    {
        if (Tools::getValue('room') !== '') {
            $folder = _PS_MODULE_DIR_ . 'viajero/uploads/img/rooms/' . Tools::getValue('room');
            $photoRoom = new ViajeroRoomsphotos();
            $photoRoom->id_room = Tools::getValue('room');
            $photoRoom->url = $_FILES['image']['name'];
            $photoRoom->save();
        } else {
            $folder = _PS_MODULE_DIR_ . 'viajero/uploads/tmp/rooms/img_' . Tools::getValue('tok');
        }
        if (!file_exists($folder)) {
            mkdir($folder, 0777);
        }
        $origen=$_FILES["image"]["tmp_name"];
        $name= $_FILES['image']['name'];
        $destino = $folder . "/" .$name;
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
    public function delTempPhoto()
    {
        if (Tools::getValue('roomphoto') !== '') {
            $folder = _PS_MODULE_DIR_.'viajero/uploads/img/rooms/'.Tools::getValue('room').'/'.Tools::getValue('name');
            $photoroom = new ViajeroRoomsphotos(Tools::getValue('roomphoto'));
            $photoroom->delete();
        } else {
            $name = Tools::getValue('name');
            $folder = _PS_MODULE_DIR_ . 'viajero/uploads/tmp/rooms/img_' . Tools::getValue('tok') . '/' . $name;
        }
        if (file_exists($folder)) {
            unlink($folder);
            return true;
        } else {
            return false;
        }
    }
    public function saveImages($id, $tok)
    {
        $to = _PS_MODULE_DIR_ . 'viajero/uploads/img/rooms/' . $id;
        if (!file_exists($to)) {
            mkdir($to, 0777);
        }
        $from = _PS_MODULE_DIR_ . 'viajero/uploads/tmp/rooms/img_' . $tok;

        $dir = opendir($from);
        if (file_exists($from)) {
            while (($file = readdir($dir)) !== false) {
                if (strpos($file, '.') !== 0) {
                    copy($from . '/' . $file, $to . '/' . $file);
                    $photoRoom = new ViajeroRoomsphotos();
                    $photoRoom->id_room = $id;
                    $photoRoom->url = $file;
                    $photoRoom->save();
                }
            }
            $this->rmDirrf($from);
        }
        return true;
    }
    
    protected function deletePhotos()
    {
        $photos= ViajeroRoomsphotos::getPhotosByRooms(Tools::getValue('id_room'));
        foreach ($photos as $photo) {
            $photoRoom = new ViajeroRoomsphotos($photo['id_room_photo']);
            $photoRoom->delete();
        }
        $folder = _PS_MODULE_DIR_ . 'viajero/uploads/img/rooms/'.Tools::getValue('id_room');
        $this->rmDirrf($folder);
        return true;
    }
    
    public function rmDirrf($carpeta)
    {
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
