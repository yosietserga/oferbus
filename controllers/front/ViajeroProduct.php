<?php

class viajeroViajeroProductModuleFrontController extends ModuleFrontController {

    public $jsonResult = array();    
    
    public function __construct() {
        parent::__construct();
        $this->jsonResult = array('result' => null, 'success' => true, 'has_errors' => false, 'errors' => $this->errors);
    }

    public function initContent() {  
        $this->display_column_left = false;
        $this->display_column_right = false;
        //var_dump(Tools::getToken() );exit();
        if ($this->ajax == false) {        
            $product = new Product($_GET['id_product']);

            $idPackage = ViajeroPaquetes::getPackageByIdProduct($product->id);
            //var_dump(Tools::getToken());
            $package = new ViajeroPaquetes($idPackage);
            $origenes = ViajeroPaqueteOrigins::getOriginssBypackage($idPackage);
            // $destinos = ViajeroPaqueteDestinos::getDestinosBypackageName($idPackage);
//            $origenes_lineas = ViajeroPaqueteLinea::getOriginsLinesBypackage($idPackage);
//            var_dump($destinos);exit();
            $hoteles = ViajeroPaqueteRooms::getHotelsByRoom($idPackage);
            
            //$butacas = ViajeroPaqueteSeats::getSeatBypackage($idPackage);
            $butacas = ViajeroPaqueteSeats::getTypeSeatByPackage($idPackage);
            /*print_r(json_encode($butacas));
            exit;*/
            $adicionales= ViajeroPaqueteAditionals::getAditionalsBypackage($idPackage); 
//            var_dump($destinos);exit();
//            foreach($destinos as $key=>$destino){
//                $values = explode(' ',$destino['date_sal']);                
//                $values2 = explode(' ',$destino['date_lle']);   
//                $destinos[$key]['date_sal']=$this->obtenerFechaEnLetra($values[0]).' '.$values[1];              
//                $destinos[$key]['date_lle']=$this->obtenerFechaEnLetra($values2[0]).' '.$values2[1];                 
//            }
            $this->context->smarty->assign(array(
                'urlImage' => $this->module->getPathUri().'uploads/img/',
                'photos' => ViajeroPackagePhotos::getPhotosByPackage($idPackage),
                'package' => $package,
                'origenes' => $origenes,
//                'origenes_lineas' => $origenes_lineas,
//                'destinos' => $destinos,
                'butacas' => $butacas,
                'hoteles' => $hoteles,
                'adicionales' => $adicionales,
                'urlAjax' => $this->context->link->getModuleLink( 'viajero/ViajeroProduct'),
            ));

//            $this->addJS('module:viajero/views/js/frontProduct.js');
//            $this->addJS('module:viajero/views/js/jquery.validate.js');
//            $this->addJS('module:viajero/views/js/bootstrap.min.js');
//            $this->addJS('module:viajero/views/js/fontawesome.js');
//            $this->addCSS('module:viajero/views/css/front/bootstrap.min.css');
//            $this->addCSS('https://use.fontawesome.com/releases/v5.3.1/css/all.css');
//            $this->addCSS('module:viajero/views/css/front/fontawesome.css');
            
            $this->context->controller->addJqueryUI('ui.datepicker');
            $this->addJqueryUI('ui.datepicker');             
            parent::initContent();
            $this->setTemplate('module:viajero/views/templates/front/viajeroProduct.tpl');
            
        } else {
            
        }
    }
    
    public function setMedia() {
        parent::setMedia();       

    }
    public function postProcess() {
        $api = new Redevtapi();

        if(Tools::getValue('action')=='getHotelsByRoom'){
            //var_dump($_POST);exit();
             die(Tools::jsonEncode(ViajeroPaqueteRooms::getHotelsByRoom2(Tools::getValue('id_pacakage_destiny'),Tools::getValue('id_package'))));
        }
        if(Tools::getValue('action')=='getPriceOrigin'){
             die(Tools::jsonEncode(ViajeroPaqueteOrigins::getOriginPrice(Tools::getValue('id_package_origin'))));
        }
        if(Tools::getValue('action')=='getPriceSeats'){
             die(Tools::jsonEncode(ViajeroPaqueteSeats::getSeatPrice(Tools::getValue('id_seat'))));
        }
        if(Tools::getValue('action')=='getPriceAditional'){
             die(Tools::jsonEncode(ViajeroPaqueteAditionals::getPriceAditionals(Tools::getValue('id_aditional'))));
        }
        if(Tools::getValue('action')=='getInformationRoom'){
             die(Tools::jsonEncode(ViajeroPaqueteRooms::getInformationRooms(Tools::getValue('id_package_room'))));
        }
        if(Tools::getValue('action')=='getInformationDestination'){
             die(Tools::jsonEncode(ViajeroPaqueteDestinos::getInformationDestinos(Tools::getValue('id_package_destination'),Tools::getValue('id_package_room'))));
        }
        if(Tools::getValue('action')=='getPriceDestiny'){
             die(Tools::jsonEncode(ViajeroPaqueteDestinos::getPriceDestinos(Tools::getValue('id_destiny'))));
        }

        if(Tools::getValue('action')=='getRegimenByHotel'){
             die(Tools::jsonEncode(ViajeroPaqueteRegimen::getRegimen(Tools::getValue('id_hotel'))));
        }
        if(Tools::getValue('action')=='getPriceRegimens'){
             die(Tools::jsonEncode(ViajeroPaqueteRegimen::getPrice(Tools::getValue('idregimen'),Tools::getValue('atributo'))));
        }
        
        if(Tools::getValue('action')=='getPhotosByHotel'){
             die(Tools::jsonEncode(ViajeroPaqueteRooms::getPhotosByHotel(Tools::getValue('id_hotel'))));
        }
        if(Tools::getValue('action')=='getPhotosByRoom'){
             die(Tools::jsonEncode(ViajeroPaqueteRooms::getPhotosRoom(Tools::getValue('id_room'))));
        }

        if(Tools::getValue('action')=='getCoordsByHotel'){
             die(Tools::jsonEncode(ViajeroHotel::getCoords(Tools::getValue('id_hotel'))));
        }
        
        if(Tools::getValue('action')=='getRooms'){
             die(Tools::jsonEncode(ViajeroPaqueteRooms::getRoomsByHotelFront(Tools::getValue('id_hotel'),Tools::getValue('id_package'))));
        }
        if(Tools::getValue('action')=='createProduct'){            
            die(Tools::jsonEncode($this->createProduct()));
        }
        if(Tools::getValue('action')=='cleanCart'){    
            die($this->context->cart->delete());
        }
        if(Tools::getValue('action')=='getLinesOrigin'){
            die(Tools::jsonEncode(ViajeroPaqueteLinea::getLinesByPackageOrigin(Tools::getValue('id_origin'),Tools::getValue('id_package'))));
        }
        if(Tools::getValue('action')=='getDestinysLine'){
            die(Tools::jsonEncode(ViajeroPaqueteLinea::getDestinysByLine(Tools::getValue('id_line'))));
        }
        if(Tools::getValue('action')=='getHotelsByDestinyLine'){
            die(Tools::jsonEncode(ViajeroPaqueteLinea::getHotelsByDestinyLine(Tools::getValue('id_line'), Tools::getValue('id_destiny'), Tools::getValue("filter_data")  )));
        }
        /*if(Tools::getValue('action')=='validateQuotasLine'){
            die(Tools::jsonEncode($api->getDepartureById(Tools::getValue('departure_id'))));
        }*/

        if(Tools::getValue('action')=='validateQuotasLine'){
            die(Tools::jsonEncode(ViajeroPaqueteLinea::getLineasByDeparturePackage(Tools::getValue('departure_id'),Tools::getValue('package_id'))));
        }

        if(Tools::getValue('action')=='getPackages'){
            die(Tools::jsonEncode($api->getPackages()));
        }
    }

    public function createProduct(){

        $category = CategoryCore::getRootCategory();    
        $package = new ViajeroPaquetes(Tools::getvalue('package'));
        $product = new Product();
        $product->active = 1;
        $product->price=Tools::getValue('total');
        $product->visibility='none';
        $product->description_short= $package->detalles;
        $product->id_shop_default = $this->context->shop->id;
        $product->id_category_default = $category->id_category;
        $product->name[$this->context->language->id] = $package->name;
        $product->quantity =1000;
        StockAvailable::setQuantity((int) $product->id,0,1000, (int) $this->context->shop->id);
        $product->link_rewrite[$this->context->language->id] = Tools::link_rewrite($product->name[$this->context->language->id]);
        $original_url = Tools::getValue('original_url');
        $pieces = explode("id_product=", $original_url);
        $original_id = intval(trim($pieces[1]));
        $product->width = "$original_id";
        $product->save();
        $this->saveImages($package->id_package,$product->id);
        ViajeroPaquetes::createAssociate($category->id_category,$product->id);


        try {
            $id_history = $this->createHistorial($product->id);

            $this->createPackageRooms($id_history);
            $this->createPassagers($id_history);
            $this->createAditionals($id_history);
        } catch (Exception $e) {
            echo 'Excepción capturada: ',  $e->getMessage(), "\n";
        }
//        $this->savePhoto(Tools::getvalue('package'),$product->id);
        return $product;
    }
    public function saveImages($id_history,$product) {
        $to = _PS_MODULE_DIR_ . 'viajero/uploads/img/package/' . $id_history;
        $dir = opendir($to);
        if (file_exists($to)) {
            while (($file = readdir($dir)) !== false) {
                if (strpos($file, '.') !== 0) {
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
                }
            }
        }
//        $this->rmDirrf($from);
        return true;
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
    public function createHistorial($id_history){

        $pack= new ViajeroPaquetes(Tools::getvalue('package'));
        $linea= new ViajeroPaqueteLinea(Tools::getvalue('linea'));
        
        $Historial = new ViajeroPaqueteHistorial();
        $Historial->id_package=Tools::getvalue('package');
        $Historial->id_product=$id_history;
        $Historial->id_origin = Tools::getvalue('origen');
        $Historial->price=Tools::getvalue('total');
        $Historial->id_package_linea=Tools::getvalue('linea');
        $Historial->id_package_butaca = (int)Tools::getvalue('butaca');
        $Historial->id_package_butaca_tipo_cupo = (int)Tools::getvalue('butaca_tipo_cupo_id');
        $Historial->payment = 0;
        if ($pack->disponibilidad == 'Si' && $linea->inventario == 0) {
            $Historial->state = 'Vendido sin cupo';
        } else {
            $Historial->state = '';
        }
        $data_pasajeros = Tools::getvalue('data_pasajeros');
        $Historial->cantPassAdult=$data_pasajeros['adultos'];
        $Historial->cantPassNinos=$data_pasajeros['childrens'];
        $Historial->cantPassBebes=$data_pasajeros['babys'];
        $Historial->id_proceso = null;

        $resultSql = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
        SELECT impuesto FROM `' . _DB_PREFIX_ . 'inv_packages_linea` P where P.id_package_Linea=' . Tools::getvalue('linea'));
        $impuesto = $resultSql[0]['impuesto'] * ($data_pasajeros['adultos'] + $data_pasajeros['childrens']);
        $Historial->impuesto = $impuesto;
        
        $cant_room_seld = 0;

        $cupos_max = Db::getInstance()->ExecuteS('SELECT cupos_room FROM `' . _DB_PREFIX_ . 'inv_packages_linea` WHERE id_package = '.(int)$Historial->id_package);
        foreach(Tools::getValue('habitaciones') as $room) { $cant_room_seld += (int)$room['cant']; }
        Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
		UPDATE `' . _DB_PREFIX_ . 'inv_packages_linea` PD SET cupos_room = '.((int)$cupos_max[0]['cupos_room'] - $cant_room_seld).'  WHERE PD.id_package = ' .(int)$Historial->id_package);
        $Historial->add();

        return $Historial->id;
    }
    public function createPackageRooms($id_history){
        $data_rooms = Tools::getValue('data_rooms');
        $data_roomas_finish = [];
        foreach(Tools::getValue('habitaciones') as $key => $room) {
            $data_room = $data_rooms[$key];

            $historialRoom = new ViajeroPaqueteHistorialRooms();
            $historialRoom->id_package_historial = $id_history;
            $historialRoom->id_package_room = $room['id_package_room'];
            $historialRoom->cant_room = $room['cant'];

            $historialRoom->adults = $data_room['adults'];
            $historialRoom->children = $data_room['childs'];

            $historialRoom->add();
            $data_roomas_finish[] = $historialRoom;
        }

        return true;
    }
    public function createAditionals($id_history) {
        foreach (Tools::getvalue('adicionales') as $adi) {
            $historialAditional = new ViajeroPaqueteHistorialAdicionales();
            $historialAditional->id_package_historial = $id_history;
            $historialAditional->id_package_adicional = $adi;
            $historialAditional->add();
        }
    }

    public function createPassagers($id_history) {
        foreach (Tools::getvalue('pasajeros') as $pasajero) {
            $phone = empty($pasajero['TELEFONO']) ? '0000' : $pasajero['TELEFONO'];
            $historialPasajero = new ViajeroPaqueteHistorialPasajeros();
            $historialPasajero->id_package_historial = $id_history;
            $historialPasajero->nombre = $pasajero['NOMBRE'];
            $historialPasajero->apellido = $pasajero['APELLIDO'];
            $historialPasajero->fecha_nacimiento = $pasajero['FECHANACIMIENTO'];
            $historialPasajero->telefono = $phone;
            $historialPasajero->sexo = $pasajero['SEXO'];
            $historialPasajero->tipo_doc = $pasajero['TIPODOC'];
			$historialPasajero->dni = $pasajero['DNI'];
            $historialPasajero->add();
        }
        return true;
    }

    private function clearText($text) {
        return preg_replace("/[\/\&%#\$]/", "", $text);
    }

    public function obtenerFechaEnLetra($fecha) {

        $dia = $this->conocerDiaSemanaFecha($fecha);
        $num = date("j", strtotime($fecha));
        $anno = date("Y", strtotime($fecha));
        $mes = array('enero', 'febrero', 'marzo', 'abril', 'mayo', 'junio', 'julio', 'agosto', 'septiembre', 'octubre', 'noviembre', 'diciembre');
        $mes = $mes[(date('m', strtotime($fecha)) * 1) - 1];
        return $dia . ', ' . $num . ' de ' . $mes . ' del ' . $anno;
    }

    public function conocerDiaSemanaFecha($fecha) {
        $dias = array('Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado');
        $dia = $dias[date('w', strtotime($fecha))];
        return $dia;
    }
    
}
