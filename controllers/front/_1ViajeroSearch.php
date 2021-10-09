<?php

class viajeroViajeroSearchModuleFrontController extends ModuleFrontController {


    public function init() {
        parent::init();
    }

    public function postProcess() {
        if (Tools::getValue('action') == 'origen') {
            die(ViajeroOrigenes::getOrigins());                
//            var_dump('entro');exit();
        }
        if(Tools::getValue('action')=='getProductSearch'){
            die($this->getProductSearch());
        }
        if(Tools::getValue('action')=='getDestinysByOrigin'){
            die(Tools::jsonEncode(ViajeroPaqueteDestinos::getDestinosByOrigin(Tools::getValue('origin'))));
        }
        if(Tools::getValue('action')=='getOriginesByDestiny'){
            die(Tools::jsonEncode(ViajeroPaqueteDestinos::getOriginesByDestiny(Tools::getValue('destino'))));
        }
    }

    public function getProductSearch(){
        $origin = Tools::getValue('origin');
        $destiny = Tools::getValue('destiny');
        $month_id = Tools::getValue('month_id');
        $page = Tools::getValue('page');

        $per_page = 30;
        $page_act = 1;

        if (strlen($page) > 0 && $page != null && $page !== false) {
            $page_act = $page;
        }

        $sql = 'SELECT pack.*,
                        min(lin.date_sal) as fechasalida,
                        lins.name as linsname,
                        lins.description as descriptionduracion,
                        "" as servicios , 
                        pho.url, 
                        pho.id_package as id_package_photo
                FROM pr_product pro
                JOIN pr_inv_packages pack ON pack.id_product = pro.id_product
                LEFT JOIN pr_inv_package_photos pho ON pho.id_package = pack.id_package
                JOIN pr_inv_packages_linea lin ON lin.id_package = pack.id_package
                LEFT JOIN pr_inv_packages_linea_servicios lins ON lins.id_package_linea = lin.id_package_linea
                JOIN pr_inv_packages_origins ori ON ori.id_package_linea = lin.id_package_Linea
                JOIN pr_inv_packages_rooms pacr ON lin.id_package_Linea = pacr.id_package_Linea
                JOIN pr_inv_rooms room ON room.id_room = pacr.id_room
                JOIN pr_inv_hotels hot ON hot.id_hotel = room.id_hotel
                JOIN pr_inv_destinations des ON des.id_destiny = hot.id_destiny
                WHERE pro.active = "1" ';
//        WHERE pro.active = "1" AND lins.name LIKE "duracion" ';

        if (strlen($origin) > 0) {
            $sql .= ' AND ori.id_origin = "'.$origin.'" ';
        }
        if(strlen($destiny) > 0){
            $sql .= ' AND des.id_destiny = "'.$destiny.'" ';
        }
        if(strlen($month_id) > 0){
            $sql .= ' AND MONTH(lin.date_sal) = "'.(int)$month_id.'"';
        }

        $sql .= ' AND lin.date_sal >= now() GROUP BY pack.id_product ORDER BY pack.id_product DESC';

        $products = Db::getInstance()->ExecuteS($sql);
        $total_products = count($products);
        $last_page = ceil($total_products / $per_page);
        if ($total_products > 0) {
            $reg_act = ($page_act - 1) * $per_page;
            $sql .= ' LIMIT '.$reg_act.', '.$per_page.' ';

            $products = Db::getInstance()->ExecuteS($sql);
        }

        foreach ($products as $key=> $p) {
            $explodefecha = explode(' ',$p['fechasalida']);    
            $explodefecha2 = explode('-',$explodefecha[0]);
            $fecha = "Salidas a partir de ".$this->getMonthName((int)$explodefecha2[1]);
            $products[$key]['showPaquete'] = $this->showPaquete($p['id_package']);
            $products[$key]['fechasalida'] = $fecha;
            $products[$key]['servicios'] = $this->getServicios($p['id_package']);
        }            
        $this->context->smarty->assign(array(
            'products' => $products,
            'page_act' => $page_act,
            'total_pages' => $last_page,
            'viajero_product_link' => $this->context->link->getModuleLink('viajero', 'ViajeroProduct'),
            'urlImage' => $this->module->getPathUri().'uploads/img/',
        ));
        return $this->context->smarty->fetch(_PS_MODULE_DIR_.'viajero/views/templates/hook/front/product-list.tpl');
    }

    //put your code here
    public function initContent() {
        
    }

    public function setMedia() {
        parent::setMedia();       
    }

    public function showPaquete($id_package) {
        $sqlDate = "SELECT date_sal FROM pr_inv_packages_linea WHERE id_package = ".(int)$id_package." ORDER BY date_sal ASC";
        $dates = Db::getInstance()->ExecuteS($sqlDate);
        $nowDate = date('Y-m-d');
        $showPaquete = true;
        foreach ($dates as $key=> $date) {
            $fecha = explode(' ',$date['date_sal']);
            if ($fecha[0] < $nowDate) {
                $showPaquete = false;
            } else {
                $showPaquete = true;
            }
        }

        return $showPaquete;
    }

    public function getMonthName($month_id) {
        $mesesN=array( 1 =>"Enero",
                       2 => "Febrero",
                       3 => "Marzo",
                       4 => "Abril",
                       5 => "Mayo",
                       6 => "Junio",
                       7 => "Julio",
                       8 => "Agosto",
                       9 => "Septiembre",
                       10 => "Octubre",
                       11 => "Noviembre",
                       12 => "Diciembre");
        return strtoupper($mesesN[$month_id]);
    }

    public function getServicios($id_package) {
        $sqlServicios = "SELECT distinct name, description FROM pr_inv_packages_linea_servicios WHERE id_package = ".(int)$id_package;
        $servicios = Db::getInstance()->ExecuteS($sqlServicios);
        return array('transporte' => $servicios[0]['description'], 'duracion' => $servicios[1]['description'], 'regimen' => $servicios[2]['description'], 'asistencia' => $servicios[3]['description'], 'coordinacion' => $servicios[4]['description']);
    }
}

?>