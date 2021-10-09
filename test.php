<?php
include '../../config/config.inc.php';
include './viajero.php';
// $linea= new ViajeroPaqueteLinea();
// $cupos_max = $linea->getLineasBypackagess(205);
// $result = Db::getInstance()->ExecuteS('SELECT cupos_room FROM `' . _DB_PREFIX_ . 'inv_packages_linea` WHERE id_package = 1');
// echo 'valor cupos:'.$result[0]['cupos_room'];
// echo '<pre>';var_dump($result);
// $sql = 'CREATE TABLE IF NOT EXISTS `pr_inv_package_eqv_room_hotel` (
//             `id_package_linea` INT(10),
//             `id_package` INT(10),
//             `id_room` INT(10),
//             `id_hotel` INT(10),
//             `value` INT(10),
//             PRIMARY KEY (`id_package_linea`)
//         )ENGINE=InnoDB DEFAULT CHARSET=latin1;';
// Db::getInstance()->execute($sql);

$packages = array();
// $result = Db::getInstance()->ExecuteS('
// SELECT DISTINCT D.*
//     FROM `pr_inv_rooms` D 
// INNER JOIN `pr_inv_packages_rooms` AS PR ON PR.id_room = D.id_room 
//         WHERE D.id_hotel=1
// ORDER BY D.name ASC');
// // echo '<pre>';var_dump($result);
// foreach($result as $room) {
//     $cupos_room = Db::getInstance()->getValue('SELECT PR.cant as cant 
//                                                     FROM `pr_inv_rooms` D 
//                                                 LEFT JOIN `pr_inv_packages_rooms` AS PR ON PR.id_room = D.id_room 
//                                                 WHERE D.id_hotel = 1 AND PR.cant > 0');
//     $room['cant'] = $cupos_room;
//     array_push($packages, $room);
// }
// echo '<pre>';var_dump($packages);

// $linea = ViajeroPaqueteLinea::getLinesById(326);
// echo '<pre>'; var_dump($linea);

// $table = Db::getInstance()->Execute('CREATE TABLE IF NOT EXISTS `pr_inv_packages_destinos` (
//     `id_package_destino` int(10) NOT NULL AUTO_INCREMENT, 
//     `id_package` int(10) NOT NULL ,   
//     `id_destiny` int(10) NOT NULL ,
//     `id_product_attribute` int(10) NOT NULL,
//     `price` float NULL,
//     `impuesto` float NULL,
//     `description` text NULL,
//     `inventario` float NULL,
//     `date_sal` datetime NOT NULL ,
//     `date_lle` datetime NOT NULL ,
//     `date_add` datetime NOT NULL ,
//     `date_upd` datetime NOT NULL ,
//         PRIMARY KEY  (`id_package_destino`)
//     );
// ');

$pack= new ViajeroPaquetes(9);
$linea= new ViajeroPaqueteLinea(389);

$Historial = new ViajeroPaqueteHistorial();
$Historial->id_package = 9;
$Historial->id_product = 310;
$Historial->price = 2400;
$Historial->id_package_linea=389;
$Historial->id_package_butaca = 0;
$Historial->payment = 0;
if ($pack->disponibilidad == 'Si' && $linea->inventario == 0) {
    $Historial->state = 'Vendido sin cupo';
} else {
    $Historial->state = '';
}
// $data_pasajeros = Tools::getvalue('data_pasajeros');
// $Historial->cantPassAdult=$data_pasajeros['adultos'];
// $Historial->cantPassNinos=$data_pasajeros['childrens'];
// $Historial->cantPassBebes=$data_pasajeros['babys'];
// $cant_room_seld = 0;
// $cupos_max = Db::getInstance()->ExecuteS('SELECT cupos_room FROM `' . _DB_PREFIX_ . 'inv_packages_linea` WHERE id_package = '.(int)$Historial->id_package);
// foreach(Tools::getValue('habitaciones') as $room) { $cant_room_seld += (int)$room['cant']; }
// Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
// UPDATE `' . _DB_PREFIX_ . 'inv_packages_linea` PD SET cupos_room = '.((int)$cupos_max[0]['cupos_room'] - $cant_room_seld).'  WHERE PD.id_package = ' .(int)$Historial->id_package);
$Historial->add();
echo '<pre>';var_dump($Historial);
echo $Historial->id;
?>