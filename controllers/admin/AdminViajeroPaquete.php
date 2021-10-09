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

class AdminViajeroPaqueteController extends ModuleAdminController
{

    public function __construct()
    {
        $this->bootstrap = true;
        $this->table = "inv_packages";
        $this->className = "ViajeroPaquetes";
        $this->identifier = "id_package";
        $this->fields_list = array();
        parent::__construct();
        $this->actions = array('edit', 'delete');
        $this->fields_list = array(
            'name' => array(
                'title' => $this->l('Nombre')
            ),
            'disponibilidad' => array(
                'title' => $this->l('Facturar sin cupo')
            ),
            'pricereference' => array(
                'title' => $this->l('Precio de referencia')
            ),
            'id_product' => array(
                'title' => $this->l('Producto padre')
            ),
           
        );
        
    }
    public function postProcess()
    {        
        //var_dump($_GET);exit();
        if ($this->ajax) {
            // API

            $api = new Redevtapi();

            if (Tools::getValue('action') == "getApiPackages") {
                die(Tools::jsonEncode($api->getPackages()));
            }
            if (Tools::getValue('action') == "getApiDepartures") {
                die(Tools::jsonEncode($api->getDepartures(Tools::getValue('id_package'))));
            }
            if (Tools::getValue('action') == "getApiDepartureDetails") {
                die(Tools::jsonEncode($api->getDepartureById(Tools::getValue('departure_id'))));
            }
            if (Tools::getValue('action') == "getApiOriginsDeparture") {
                die(Tools::jsonEncode($api->getOriginsDeparture(Tools::getValue('departure_id'))));
            }
            if (Tools::getValue('action') == "getApiSeats") {
                die(Tools::jsonEncode($api->getSeats()));
            }

            // API


            if (Tools::getValue('action') == "getHotelsByDestiny") {
                 die(Tools::jsonEncode(ViajeroHotel::getHotelsByDestiny(Tools::getValue('id_destiny'))));
            }
            if (Tools::getValue('action') == "getRoomsByHotel") {
                die(Tools::jsonEncode(ViajeroRooms::getRoomsByHotel(Tools::getValue('id_hotel'))));
            }
            if (Tools::getValue('action') == "getRoomsByHotelPack") {
                die(Tools::jsonEncode(ViajeroRooms::getRoomsByHotelPack(Tools::getValue('id_hotel'))));
            }
            if (Tools::getValue('action') == "getOriginsDestiny") {
                die(Tools::jsonEncode(ViajeroOrigenes::getOriginsByDestiny(Tools::getValue('id_destiny'))));                
            }
            if (Tools::getValue('action') == "getOriginsDestinySelect") {
                die(Tools::jsonEncode(ViajeroPaqueteOrigins::getOriginsByDestinySelect2(Tools::getValue('id_package'),Tools::getValue('id_destiny'))));                
            }
            if (Tools::getValue('action') == "getRoompack") {
                die(Tools::jsonEncode(ViajeroPaqueteRooms::getRoomsPack(Tools::getValue('id_room'),Tools::getValue('id_package_linea'))));
            }
            if (Tools::getValue('action') == "getLineaId") {
                die(Tools::jsonEncode(ViajeroPaqueteLinea::getLinesById(Tools::getValue('id_package'), Tools::getValue('id_package_linea'))));
            }
            if (Tools::getValue('action') == "savePackage") {
               $this->savePackage();
            }
            if(Tools::getValue('action')=='getHotelsSelects'){
                die(Tools::jsonEncode(ViajeroPaqueteRooms::getHotelsSelects(Tools::getValue('id_destiny'),Tools::getValue('id_package'))));
            }
            if (Tools::getValue('action') == "updatePackage") {
//                var_dump($_POST);exit();
               $this->updatePackage();              
            }
            if (Tools::getValue('action') == "uploadPhoto") {
              die(Tools::jsonEncode($this->tempphoto()));           
            }
            if (Tools::getValue('action') == "deleteImgTemp") {
                die(Tools::jsonEncode($this->delTempPhoto()));
            }
            if (Tools::getValue('action') == "uploadPhotoPackage") {
//                $name = $this->savePhoto();
//                if ($name !== false){
//                  $package = new ViajeroPaquetes(Tools::getValue('id_package'));
//                  $package->photo=$name;
//                  $package->update();
//               }
               $response=array('url'=>$this->context->link->getAdminLink('AdminViajeroPaquete'),'response'=>$name);
              die(Tools::jsonEncode($response));
            }
            
        } else {
            if (array_key_exists('deleteinv_packages', $_GET)) {
                
                $this->deletePackage();
//                var_dump('asdfasdf');exit();
                $this->deletePhotos();
                
                parent::postProcess();
            }
            if (Tools::getValue('submitResetinv_packages')) {
                parent::postProcess();
            }
            if (Tools::getValue('submitFilterinv_packages')) {
                parent::postProcess();
            }
        }
//        parent::postProcess();
    }

    public function setMedia()
    {
        parent::setMedia();
        
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
            $this->page_header_toolbar_btn['new_butaca'] = array(
                'href' => $this->context->link->getAdminLink('AdminViajeroButaca'),
                'desc' => $this->l('butacas', null, null, false),
                'icon' => 'icon-adn '
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
            $this->page_header_toolbar_btn['add_package'] = array(
                'href' => $this->context->link->getAdminLink('AdminViajeroPaquete').'&addinv_packages',
                'desc' => $this->l('Agregar paquete', null, null, false),
                'icon' => 'icon-windows'
            );
        }
        parent::initPageHeaderToolbar();
    }
    protected function deletePhotos() {
        $photos = ViajeroPackagePhotos::getPhotosByPackage(Tools::getValue('id_package'));
        foreach ($photos as $photo) {
            $photoRoom = new ViajeroPackagePhotos($photo['id_package_photo']);
            $photoRoom->delete();
        }
        
        $folder = _PS_MODULE_DIR_ . 'viajero/uploads/img/package/' . Tools::getValue('id_package');
        $this->rmDirrf($folder);
        return true;
    }

    public function delTempPhoto() {
        if (Tools::getValue('packagePhoto') !== '') {
            $folder = _PS_MODULE_DIR_ . 'viajero/uploads/img/package/' . Tools::getValue('package') . '/' . Tools::getValue('name');
            $photoroom = new ViajeroPackagePhotos((int) Tools::getValue('packagePhoto'));
            $photoroom->delete();
            $image = new Image($photoroom->id_image);
            $image->delete();
        } else {
            $name = Tools::getValue('name');
            $folder = _PS_MODULE_DIR_ . 'viajero/uploads/tmp/package/img_' . Tools::getValue('tok') . '/' . $name;
        }
        if (file_exists($folder)) {
            unlink($folder);
            return true;
        } else {
            return false;
        }
    }

    public function tempphoto() {
//        var_dump(Tools::getValue('tok'));exit();
           $origen = $_FILES["image"]["tmp_name"];
        $name = $_FILES['image']['name'];
        
        if (Tools::getValue('package') !== '') { 
            $folder = _PS_MODULE_DIR_ . 'viajero/uploads/img/package/' . Tools::getValue('package');            
            $destino = $folder . "/" . $name;           
            $package = new ViajeroPaquetes( Tools::getValue('package'));
            $product_has_images = (bool) Image::getImages($this->context->language->id, (int) $package->id_product);
            $products = new Product((int) $package->id_product);
            $image = new Image();
            $image->id_product = (int) $products->id;
            $image->position = Image::getHighestPosition((int) $products->id) + 1;
            $image->cover = (!$product_has_images) ? true : false;
            $image->add();
            if (!$this->copyImg($package->id_product, $image->id,$origen, 'products')) {
                $image->delete();
            }
            $photoPackage = new ViajeroPackagephotos();
            $photoPackage->id_package = Tools::getValue('package');
            $photoPackage->url = $_FILES['image']['name'];
            $photoPackage->id_image =$image->id;
            $photoPackage->save();
        } else {
            $folder = _PS_MODULE_DIR_ . 'viajero/uploads/tmp/package/img_' . Tools::getValue('tok');
            $destino = $folder . "/" . $name;  
        }
        if (!file_exists($folder)) {
            mkdir($folder, 0777);
        }
     
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

    public function updatePackage(){

        //update package   
//        var_dump(Tools::getValue('legales'));exit();
        $rooms=tools::getValue('rooms');  
        $date = new DateTime($rooms[0][2]);       
        $package = new ViajeroPaquetes(tools::getValue('id_package'));
        $package->name=tools::getValue('Name');
        $package->date = $date->format('Y-m-d H:i:s');
        $package->disponibilidad = tools::getValue('disponibilidad');
        $package->edadninos = tools::getValue('edadninos');
        $package->edadbebes = tools::getValue('edadbebes');
        $package->valueninos = tools::getValue('valueninos');
        $package->valuebebes = tools::getValue('valuebebes');
        $package->detalles = tools::getValue('detalles');
        $package->legales = tools::getValue('legales');
        $package->pricereference = tools::getValue('pricereference');

        $package->api = 0;

        $validate_api = tools::getValue('api');
        $is_api = false;

        if ($validate_api === true || $validate_api === 'true') {
            $is_api = true;
            $package->api = 1;
            $package->id_package_api = tools::getValue('id_package_api');
            $package->quota_api = tools::getValue('quota_api');
            $package->company_id = tools::getValue('company_id');
        }

        $package->save();
        $product = new Product($package->id_product);
        $product->id_category_default = (int)Tools::getValue('category');
        $product->name[$this->context->language->id] = $package->name;
        $product->quantity = 1000;
        $product->price = $package->pricereference;
        StockAvailable::setQuantity((int) $product->id, 0, 1000, (int) $this->context->shop->id);
        $product->link_rewrite[$this->context->language->id] = Tools::link_rewrite($product->name[$this->context->language->id]);
        $product->update();
        $category_array = explode(',', Tools::getValue('category'));
        $product->updateCategories($category_array);
        ViajeroPaquetes::createAssociate(Tools::getValue('category'),$product->id);

        $this->updateSeats();
        $this->updateAditionals();
        $this->updateLineas();
        $this->updateRooms();
        $this->updateOrigins();
        $this->updateLineasTransportes();
        $this->updateLineasTransportesTipos();
        $this->updateOrigins();



        $this->saveSeats(Tools::getValue('id_package'),$package->id_product);
        $this->saveAditionals(Tools::getValue('id_package'),$package->id_product);
        $this->saveLineas(Tools::getValue('id_package'), $is_api);
        $response=array('id_package'=>$package->id,'url'=>$this->context->link->getAdminLink('AdminViajeroPaquete'),'image'=>tools::getValue('image'),'response'=>true);
        die(Tools::jsonEncode($response));        
    }

    public function deletePackage(){ 
        
        $this->updateSeats();
        
        $this->updateAditionals();
        
        $this->updateLineas();
        
        $this->updateRooms();
        
        $this->updateServices();

        $this->updateLineasTransportes();

        $this->updateOrigins();

        $this->updateLineasTransportesTipos();
       
        $package = new ViajeroPaquetes(Tools::getValue('id_package'));
        $product = new Product($package->id_product);
        $product->delete();
        return true;
    }

    public function updateLineasTransportes()
    {
        $transportes = ViajeroPaqueteLineaTransportes::getTransportsByPackage(Tools::getValue('id_package'));
        foreach($transportes as $transporte){
            $tip = new ViajeroPaqueteLineaTransportes($transporte['id_packages_linea_transporte']);
            $tip->delete();
        }
        return true;
    }

    public function updateLineasTransportesTipos()
    {
        $tipos = ViajeroPaqueteLineaTransportesTipo::getTypesByPackage(Tools::getValue('id_package'));
        foreach($tipos as $tipo){
            $tip = new ViajeroPaqueteLineaTransportesTipo($tipo['id']);
            $tip->delete();
        }
        return true;

    }

    public function updateLineas(){
        $lineas = ViajeroPaqueteLinea::getLineasBypackagess(Tools::getValue('id_package'));
         
        foreach($lineas as $linea){
            $lin = new ViajeroPaqueteLinea($linea['id_package_Linea']);
            $lin->delete();                        
        }          
        return true;
    }
    public function updateServices(){
        $services = ViajeroPaqueteLineaServicios::getServicesByPackage(Tools::getValue('id_package'));
//        var_dump($services);Exit();
        foreach($services as $service){
            $ser = new ViajeroPaqueteLineaServicios($service['id_package_linea_servicio']);
            $ser->delete();                        
        }  
        return true;
    }
    
    public function updateOrigins(){
        //$origins = ViajeroPaqueteOrigins::getOriginssBypackage(Tools::getValue('id_package'));

        $origins = ViajeroPaqueteOrigins::deleteAllOriginPackage(Tools::getValue('id_package'));

        /*foreach($origins as $origin){
            $ori = new ViajeroPaqueteOrigins($origin['id_package_origin']);
            $ori->delete();                        
        } */
        return true;
    }
    
    public function updateSeats(){
        $seats = ViajeroPaqueteSeats::getSeatBypackage(Tools::getValue('id_package'));
        foreach($seats as $se){
            $seat = new ViajeroPaqueteSeats($se['id_package_seat']);
            $seat->delete();
        }   
        return true;
    }
    
    public function updateAditionals(){
        $aditionals = ViajeroPaqueteAditionals::getAditionalsBypackage(Tools::getValue('id_package'));
        foreach($aditionals as $ad){           
            $seat = new ViajeroPaqueteAditionals($ad['id_package_aditional']);
            $seat->delete();
        }
        return true;        
    }
    
    public function updateRooms(){
        $rooms = ViajeroPaqueteRooms::getRoomsBypackagess(Tools::getValue('id_package'));
//        var_dump($rooms);
        foreach($rooms as $or){
            $or = new ViajeroPaqueteRooms($or['id_package_room']);
            $or->delete();
        }    
//        exit();
        return true;  
    }
    
    public function savePackage(){
        //save package 

        $date = new DateTime();
        $package = new ViajeroPaquetes();
        $package->name=tools::getValue('Name');
        $package->date=$date->format('Y-m-d H:i:s');
        $package->disponibilidad=tools::getValue('disponibilidad');
        $package->edadninos=tools::getValue('edadninos');
        $package->edadbebes=tools::getValue('edadbebes');
        $package->valueninos=tools::getValue('valueninos');
        $package->valuebebes=tools::getValue('valuebebes');
        $package->pricereference=tools::getValue('pricereference');
        $package->detalles=tools::getValue('detalles');
        $package->legales = tools::getValue('legales');
        $package->tok=tools::getValue('tok');
        $package->api = 0;

        $validate_api = tools::getValue('api');
        $is_api = false;

        if ($validate_api === true || $validate_api === 'true') {
            $is_api = true;
            $package->api = 1;
            $package->id_package_api = tools::getValue('id_package_api');
            $package->quota_api = tools::getValue('quota_api');
            // $package->company_id = tools::getValue('company_id');
        }

        $package->save();

        $package_id = $package->id;
        $product = $this->saveProduct($package_id);

        $this->saveImages($package_id, $package->tok,$product);
        $this->saveSeats($package_id,$product);
        $this->saveAditionals($package_id,$product);
        $this->saveLineas($package_id, $is_api);
        $response=array('id_package'=>$package_id,'url'=>$this->context->link->getAdminLink('AdminViajeroPaquete'),'image'=>tools::getValue('image'),'product'=>$product,'response'=>true);


        die(Tools::jsonEncode($response));
    }
    
    public function saveLineas($id_package, $is_api){
        $lineas = Tools::getValue('lineas');

        foreach($lineas as $linea){
            $datesal = new DateTime($linea['date_sal']);   
            $datelle = new DateTime($linea['date_lle']);   
            $rowLinea = new ViajeroPaqueteLinea();
            $rowLinea->date_sal = $datesal->format('Y-m-d H:i:s');
            $rowLinea->date_lle = $datelle->format('Y-m-d H:i:s');
            $rowLinea->inventario = $linea['inventario'];
            $rowLinea->description = $linea['description'];
            $rowLinea->impuesto = $linea['impuesto'];
            $tot_cupos_room = 0;

            foreach ($linea['rooms'] as $cupos_rooms){
                $tot_cupos_room += $cupos_rooms['cupos_room'];
            }
            $rowLinea->cupos_room = $tot_cupos_room;

            //$rowLinea->cupos_room = $linea['rooms'][0] ? $linea['rooms'][0]['cupos_room'] : 0;

            if ($is_api) {
                $rowLinea->departure_id = $linea['departure_id'];
            }

            $rowLinea->price = 0;
            $rowLinea->id_package = $id_package;
            $rowLinea->save();

            if ($is_api) {
                $data_transporte = $linea['transporte'];
                $paquete_transporte = new ViajeroPaqueteLineaTransportes();
                $paquete_transporte->id_package_linea = $rowLinea->id;
                $paquete_transporte->id_package = $id_package;
                $paquete_transporte->name = $data_transporte['transporte_name'];
                $paquete_transporte->tipo_servicio_id = $data_transporte['tipo_servicio_id'];
                $paquete_transporte->transporte_id = $data_transporte['transporte_id'];
                //$paquete_transporte->tipo_butaca_id = $data_transporte['tipo_butaca_id'];
                $paquete_transporte->tipo_cupo_id = $data_transporte['tipo_cupo_id'];
                $paquete_transporte->save();

                $api = new Redevtapi();
                $departure_detail = $api->getDepartureById($linea['departure_id']);

                foreach ($departure_detail->Transportes as $transporte) {

                    $paquete_transporte_tipo = new ViajeroPaqueteLineaTransportesTipo();
                    $paquete_transporte_tipo->id_package = $id_package;
                    $paquete_transporte_tipo->id_packages_linea_transportes = $paquete_transporte->id;
                    $paquete_transporte_tipo->tipo_butaca_id = $transporte->TipoButacaID;
                    $paquete_transporte_tipo->tipo_cupo_id = $transporte->TipoCupoID;
                    $paquete_transporte_tipo->nombre_tipo_cupo = $transporte->TipoCupo;
                    $paquete_transporte_tipo->cupos_butaca = $transporte->Cupo;
                    $paquete_transporte_tipo->save();

                }

            }

            foreach ($linea['rooms'] as $data_room) {
                $cupos_room = $data_room['cupos_room'];
                $room_id = $data_room['id_room'];
                if ($is_api) {
                    $room = $this->getRoomValidateApi($data_room);
                    $room_id = $room->id;
                }

                $roomspack = new ViajeroPaqueteRooms();
                $roomspack->id_package = $id_package;
                $roomspack->id_package_Linea = $rowLinea->id;
                $roomspack->id_room = $room_id;
                $roomspack->cant = $cupos_room;
                $is_api ? $roomspack->srv_alojamiento_id = (int)$data_room['srv_alojamiento_id'] : '';//$srv_alojamiento_id;
                $roomspack->priceninos = $data_room['price_ninos'] > 0 ? $data_room['price_ninos'] : null;
                $roomspack->pricebebes = $data_room['price_bebes'] > 0 ? $data_room['price_bebes'] : null;
//                $roomspack->pricedestino = $data_room['price_destino'] : null;
                $roomspack->price = $data_room['price_room'] > 0 ? $data_room['price_room']:null;
                $roomspack->save();

            }
            
            foreach ($linea['origins'] as $data_origin) {
                $origin = $data_origin;
                $id_origin = $data_origin['id_origen'];
                if ($is_api) {
                    $origin = $this->getOriginValidateApi($data_origin);
                    $id_origin = $origin->id;
                }

                $roomsorigin = new ViajeroPaqueteOrigins();
                $roomsorigin->id_package = $id_package;
                $roomsorigin->id_package_linea = $rowLinea->id;
                $roomsorigin->id_origin = $id_origin;
                $roomsorigin->price = $data_origin['price'];           
                $roomsorigin->save();
            }

            $this->saveServices($rowLinea->id, $id_package, $linea['services']);
        }   
       
    }    
    public function saveServices($linea,$paquete,$services){
        foreach($services as $key=>$service){
            if($service !==''){
                $ser = new ViajeroPaqueteLineaServicios();
                $ser->id_package_linea = $linea;
                $ser->id_package=$paquete;
                $ser->name=$key;
                $ser->description=$service;
                $ser->save();
            }
        }
    }
    public function saveSeats($id,$productId){
        $seats=tools::getValue('butacas');   
        //save seats
        foreach($seats as $seat){
            $seatpack=new ViajeroPaqueteSeats();
            $seatpack->id_package=$id;
            $seatpack->id_seat=$seat[0];
            $seatpack->price=$seat[1];
            $seatpack->save();
        }
        return true;
    }
    
    public function saveAditionals($id,$productId){
         $aditionals=tools::getValue('adicionales');
        //save aditionals
        foreach($aditionals as $aditional){
            $aditionalpack=new ViajeroPaqueteAditionals();
            $aditionalpack->id_package=$id;
            $aditionalpack->id_aditional=$aditional[0];
            $aditionalpack->type=$aditional[1];
            $aditionalpack->value=$aditional[2];
            $aditionalpack->save();
            $aditional = new ViajeroAdicionales((int)$aditional[0]);
        }  
        return true;
    }    
    public function renderForm()
    {
        $sold = 0;
        if(Tools::getValue('id_package')!=''){

            $package = new ViajeroPaquetes(Tools::getValue('id_package'));
            $packageHistorial= ViajeroPaquetes::getHistorial($package->id_package);

            if (count($packageHistorial) > 0) {
                $sold = $packageHistorial[0]['payment'];
            }
            $product = new ProductCore($package->id_product);
        }else{
            $package=null;
        }
        $cats = Category::getCategories( (int)($this->context->language->id), true, false);
        $catsProducts = Product::getProductCategoriesFull($product->id, 1);
        $index = 0;
        foreach($cats as $categorias ) {
            $cats[$index]['selected'] = false;
            foreach($catsProducts as $products) {
                if ($products['id_category'] === $categorias['id_category']) {
                    $cats[$index]['selected'] = true;
                }
            }
            $index++;
        }


        //var_dump(Tools::jsonEncode(ViajeroButaca::getSeats()));

        $this->context->smarty->assign(array(
            'urlajax' => $this->context->link->getAdminLink('AdminViajeroPaquete'),
            'package' => $package,
            'urlphoto' => $this->module->getPathUri(),
            'categories' => $cats,
            'Destinos' => ViajeroDestinos::getDestinations(),
            'Destinos_json' => Tools::jsonEncode(ViajeroDestinos::getDestinations()),
            'Origenes' => ViajeroOrigenes::getOrigins(),
            'Origenes_json' => Tools::jsonEncode(ViajeroOrigenes::getOrigins()),
            'Butacas' => ViajeroButaca::getSeats(),
            'Butacas_json' => Tools::jsonEncode(ViajeroButaca::getSeats()),
            'toeee' => Tools::getValue('token'),
            'photos' => $package !==null?ViajeroPackagePhotos::getPhotosByPackage($package->id_package):null,
            'ButacasPackage' => Tools::getValue('id_package')!=''?ViajeroPaqueteSeats::getSeatBypackage($package->id_package):false,
            'AditionalPackage' => Tools::getValue('id_package')!=''?ViajeroPaqueteAditionals::getAditionalsBypackage($package->id_package):false,
//            'OriginsPackage' => Tools::getValue('id_package')!=''?ViajeroPaqueteOrigins::getOriginssBypackage($package->id_package):false,
//            'RoomsPackage' => json_encode(Tools::getValue('id_package')!=''?ViajeroPaqueteRooms::getRoomsBypackage($package->id_package):false),
            'LineasPackage' => Tools::jsonEncode(Tools::getValue('id_package')!=''? ViajeroPaqueteLinea::getLineasByPakage($package->id_package):false),
//            'HotelsPackage' => json_encode(Tools::getValue('id_package')!=''?ViajeroPaqueteRooms::getHotelsBypackage($package->id_package):false),
            'Adicionales' => ViajeroAdicionales::getAditionals(),
            'Hoteles' => json_encode(ViajeroHotel::getHotels()),
            'sold' => $sold
        ));
        $this->context->controller->addJS($this->module->getPathUri() . 'views/js/jquery.validate.js');
        $this->context->controller->addJS($this->module->getPathUri() . 'views/js/validate.js');
        $this->context->controller->addCSS($this->module->getPathUri() . 'views/css/back/backPaquete.css');
        $this->context->controller->addJqueryUI('ui.datepicker');
        //$this->context->controller->addJS($this->module->getPathUri() . 'views/js/backpaquete.js');
        $this->addJqueryUI('ui.datepicker');             
        $this->content = $this->module->getForm('paquete/form.tpl');
//        return parent::renderForm();
    }

    function escapeJsonString($value) { # list from www.json.org: (\b backspace, \f formfeed)
    $escapers = array("\\", "/", "\"", "\n", "\r", "\t", "\x08", "\x0c");
    $replacements = array("\\\\", "\\/", "\\\"", "\\n", "\\r", "\\t", "\\f", "\\b");
    $result = str_replace($escapers, $replacements, $value);
    return $result;
}

    private function saveProduct($id) {  
        
//        $category = CategoryCore::getRootCategory();       
//        var_dump($category);exit();
        $package = new ViajeroPaquetes($id);
 
        $id_categoria = Tools::getValue('category');
        $product = new Product();
        $product->active = 1;
        //$product->price = $this->calcPriceMin();
        $product->price = $package->pricereference;
        $product->id_shop_default = $this->context->shop->id;
        $product->id_category_default = (int)$id_categoria;
        $product->name[$this->context->language->id] = $package->name;
        $product->quantity = $package->quota;
        StockAvailable::setQuantity((int) $product->id, 0, $package->quota, (int) $this->context->shop->id);
        $product->link_rewrite[$this->context->language->id] = Tools::link_rewrite($product->name[$this->context->language->id]);
        $product->add();
        $category_array = explode(',', Tools::getValue('category'));
        $product->updateCategories($category_array);
        $package->id_product=$product->id;
        $package->update();
        ViajeroPaquetes::createAssociate(Tools::getValue('category'),$product->id);
        $photo = _PS_MODULE_DIR_ . 'viajero/uploads/img/package/img_' . Tools::getValue('id_package').'/'.$package->photo;        
        return $product->id;
    }

    public static function categoriaPadre($parent)
    {

        $categories = Db::getInstance()->executeS('
        SELECT c.`id_category`, c.`id_parent`
                FROM `'._DB_PREFIX_.'category` c
            WHERE c.id_category="'.$parent.'"');

        $list = array();
        foreach ($categories as $cat)
        {
              $list[] = $cat['id_category'];
              $list = array_merge($list, self::categoriaPadre($cat['id_parent']));
        }

        return $list;
    }

    //   
    public function addCombinations($idProduct,$precio,$combinacioness){                           
        $oProduct = new Product($idProduct);
        // agregar la combinacion
        $id_product_attribute = $oProduct->addCombinationEntity(
                                0,//precio al por mayor
                                $precio * 1,//impacto en el precio  * precio del atributo
                                0,// impacto en el peso * peso del producto
                                0,//impacto en el precio unitario * precio unitario
                                0,
                                0,
                                '',
                                0,//referencia del atributo
                                null,
                                0,//codigo ean
                                0,// atributo por defecto 1 para estar activo
                                0,
                                0,//codigo de barras
                                1,//cantidad minima
                                array(),
                                0// fecha de disponibilidad
                            );  
//        var_dump($id_product_attribute);exit();
        // asignar combinacion a los atributos
        $combination = new Combination((int) $id_product_attribute);
        if(isset($combinacioness->id_attribute)){
            $combination->setAttributes(array($combinacioness->id_attribute));
        }else{
            $combination->setAttributes(array($combinacioness));
        }        
        return $id_product_attribute;
    }
    public function saveImages($id, $tok,$product) {
        $to = _PS_MODULE_DIR_ . 'viajero/uploads/img/package/' . $id;
        if (!file_exists($to)) {
            mkdir($to, 0777);
        }
        $from = _PS_MODULE_DIR_ . 'viajero/uploads/tmp/package/img_' . $tok;

        $dir = opendir($from);
        if (file_exists($from)) {
            while (($file = readdir($dir)) !== false) {
                if (strpos($file, '.') !== 0) {
                    copy($from . '/' . $file, $to . '/' . $file);
                    $product_has_images = (bool) Image::getImages($this->context->language->id, (int) $product);
                    $products = new Product((int) $product);
                    $image = new Image();
                    $image->id_product = (int) $products->id;
                    $image->position = Image::getHighestPosition((int) $products->id) + 1;
                    $image->cover = (!$product_has_images) ? true : false;
                    $image->add();
                    if (!$this->copyImg($product, $image->id, $to . '/' . $file, 'products')) {
                        $image->delete();
                    }
                    $photoPackage = new ViajeroPackagePhotos();
                    $photoPackage->id_package = $id;
                    $photoPackage->url = $file;
                    $photoPackage->id_image = $image->id;
                    $photoPackage->save();

                }
            }
        }
        $this->rmDirrf($from);
        return true;
    }
    public function savePhoto() {

        $folder = _PS_MODULE_DIR_ . 'viajero/uploads/img/package/img_' . Tools::getValue('id_package');
        if (!file_exists($folder)) {
            mkdir($folder, 0777);
        }
        $origen = $_FILES["image"]["tmp_name"];
        $name = $_FILES['image']['name'];
        $destino = $folder . "/" . $name;
        $languages = Language::getLanguages(false);
        if (!file_exists($destino)) {
            if (copy($origen, $destino)) {
                $product_has_images = (bool) Image::getImages($this->context->language->id, (int) Tools::getValue('product'));
                $product = new Product(Tools::getValue('product'));
                $image = new Image();
                $image->id_product = (int) $product->id;
                $image->position = Image::getHighestPosition(Tools::getValue('product')) + 1;
                $image->cover = (!$product_has_images) ? true : false;                
                $image->add();
                if (!$this->copyImg($product->id, $image->id, $destino, 'products')) {
                    $image->delete();
                }
                return $name;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    private function clearText($text) {

        return preg_replace("/[\/\&%#\$]/", "", $text);
    }

    public function copyImg($id_entity, $id_image, $url, $entity = 'products', $regenerate = true) {
        $tmpfile = tempnam(_PS_TMP_IMG_DIR_, 'ps_import');
        switch ($entity) {
            default:
            case 'products':
                $image_obj = new Image($id_image);
                $path = $image_obj->getPathForCreation();
                break;
            case 'categories':
                $path = _PS_CAT_IMG_DIR_ . (int) $id_entity;
                break;
            case 'manufacturers':
                $path = _PS_MANU_IMG_DIR_ . (int) $id_entity;
                break;
            case 'suppliers':
                $path = _PS_SUPP_IMG_DIR_ . (int) $id_entity;
                break;
        }
        $url = str_replace(' ', '%20', trim($url));


        // Evaluate the memory required to resize the image: if it's too much, you can't resize it.
        if (!ImageManager::checkImageMemoryLimit($url))
            return false;


        // 'file_exists' doesn't work on distant file, and getimagesize makes the import slower.
        // Just hide the warning, the processing will be the same.
        if (Tools::copy($url, $tmpfile)) {
            ImageManager::resize($tmpfile, $path . '.jpg');
            $images_types = ImageType::getImagesTypes($entity);


            if ($regenerate)
                foreach ($images_types as $image_type) {
                    ImageManager::resize($tmpfile, $path . '-' . stripslashes($image_type['name']) . '.jpg', $image_type['width'], $image_type['height']);
                }
        }
        else {
            unlink($tmpfile);
            return false;
        }
        unlink($tmpfile);
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


    // API

    public function getProvinciaValidateApi($data_provincia)
    {
        $states_array = State::getStatesByIdCountry(44);

        $isset_state = false;
        $select_state = null;
        foreach ($states_array as $state) {
            if ($state['name'] === $data_provincia['provincia_name']) {
                $isset_state = true;
                $select_state = $state;
            }
        }

        $provincia_id = 0;

        if ($isset_state) {
            $provincia_id = $select_state['id_state'];
        } else {
            $state = new State();
            $state->id_country = 44;
            $state->id_zone = 6;
            $state->name = $data_provincia['provincia_name'];
            $state->iso_code = 'A';
            $state->tax_behavior = 0;
            $state->active = 1;
            $state->save();

            $provincia_id = $state->id;
        }

        return $provincia_id;
    }

    public function getOriginValidateApi($data_origin)
    {
        $provincia_id = $this->getProvinciaValidateApi($data_origin);
        $origin = ViajeroOrigenes::getOriginsByName($data_origin['name']);

        if ($origin) {
            $origin = $origin[0];
            $origin = new ViajeroOrigenes($origin['id_origen']);
            $origin->id_origin_api = $data_origin['id_origen'];
            $origin->update();
        } else {
            $origin = new ViajeroOrigenes();
            $origin->origen = $data_origin['name'];
            $origin->latitud = 0;
            $origin->longitud = 0;
            $origin->id_provincia = $provincia_id;
            $origin->id_origin_api = $data_origin['id_origen'];
            $origin->save();
        }

        return $origin;
    }

    public function getDestinationValidateApi($data_destination)
    {
        $destination = ViajeroDestinos::getDestinationByName($data_destination['destination_name']);

        if ($destination) {
            $destination = $destination[0];
            $destination = new ViajeroDestinos($destination['id_destiny']);
            $destination->id_destination_api = $data_destination['destination_id'];
            $destination->update();
        } else {
            $destination = new ViajeroDestinos();
            $destination->destiny = $data_destination['destination_name'];
            $destination->latitud = 0;
            $destination->longitud = 0;
            $destination->id_destination_api = $data_destination['destination_id'];
            $destination->save();
        }

        return $destination;
    }

    public function getHotelValidateApi($data_hotel)
    {
        $destination = $this->getDestinationValidateApi($data_hotel);
        $hotel = ViajeroHotel::getHotelByName($destination->id, $data_hotel['hotel_name']);

        if ($hotel) {
            $hotel = $hotel[0];
            $hotel = new ViajeroHotel($hotel['id_hotel']);
            $hotel->id_hotel_api = $data_hotel['id_hotel'];
            $hotel->update();
        } else {
            $hotel = new ViajeroHotel();

            $hotel->id_destiny = $destination->id;
            $hotel->name = $data_hotel['hotel_name'];
            $hotel->latitud = 0;
            $hotel->longitud = 0;
            $hotel->stars = 1;
            $hotel->limninos = 0;
            $hotel->limbebes = 0;
            $hotel->tok = 0;
            $hotel->description = '';
            $hotel->id_hotel_api = $data_hotel['id_hotel'];
            $hotel->save();
        }

        return $hotel;
    }

    public function getRoomValidateApi($data_room)
    {
        $hotel = $this->getHotelValidateApi($data_room);
        $room = ViajeroRooms::getRoomByName($hotel->id, $data_room['room_name']);

        
        if ($room) {
            $room = $room[0];
            $room = new ViajeroRooms($room['id_room']);
            $room->id_room_api = $data_room['id_room'];

            $room->categoria_habitacion_id = $data_room['categoria_habitacion_id'];
            $room->tipo_habitacion_id = $data_room['tipo_habitacion_id'];
            $room->regimen_id = $data_room['regimen_id'];

            $room->cant = $data_room['capacidad'];
            $room->update();
        } else {
            $room = new ViajeroRooms();
            $room->id_hotel = $hotel->id;
            $room->name = $data_room['room_name'];
            $room->cant = $data_room['capacidad'];
            $room->id_room_api = $data_room['id_room'];

            $room->categoria_habitacion_id = $data_room['categoria_habitacion_id'];
            $room->tipo_habitacion_id = $data_room['tipo_habitacion_id'];
            $room->regimen_id = $data_room['regimen_id'];

            $room->tok = 0;
            $room->observations = '';
            $room->save();
        }

        return $room;
    }

}
