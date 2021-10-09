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

class AdminViajeroButacaController extends ModuleAdminController
{
    protected $status_array = array();
    public function __construct()
    {
        $this->bootstrap = true;
        $this->table = "inv_seats";
        $this->className = "ViajeroButaca";
        $this->identifier = "id_seat";
        $this->fields_list = array();
        parent::__construct();
        $this->actions = array('edit', 'delete');

        $this->fields_list = array(
            'id_seat' => array(
                'title' => $this->l('ID'),
            ),
            'name' => array(
                'title' => $this->l('Butaca')
            ),
        );
    }

    public function postProcess()
    {     
    

          parent::postProcess();
    }
    public function deleteSeat(){
        $seat = new ViajeroButaca(Tools::getValue('id_seat'));
        $attribute = new AttributeCore($seat->id_attribute);
        $attribute->delete();       
        $seat->delete();
        return true;
    }
    public function updateSeat(){
        $seat = new ViajeroButaca(Tools::getValue('id_seat'));
        $seat->name=Tools::getValue('name');
        $seat->update();  
        $attribute = new AttributeCore($seat->id_attribute);        
        $attribute->name[$this->context->language->id] = Tools::getValue('name');     
        $attribute->update();                      
        return true;
    }
    
    public function saveSeat(){
        if (Tools::getValue('id_seat') == '') {
            $idAtribute = (int) ViajeroPaquetes::getIdAttributeClassName('butacas');
            AttributeGroupCore::getHigherPosition();
            $attribute = new AttributeCore();
            $attribute->id_attribute_group = $idAtribute;
            $attribute->position = AttributeGroupCore::getHigherPosition() + 1;
            $attribute->name[$this->context->language->id] = Tools::getValue('name');
            $attribute->add();
            $seat = new ViajeroButaca();
            $seat->name = Tools::getValue('name');
            $seat->id_attribute = $attribute->id;
            $seat->save();
        } else {
            $this->updateSeat();
        }
        return true;
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
            $this->page_header_toolbar_btn['add_seat'] = array(
                'href' => $this->context->link->getAdminLink('AdminViajeroButaca').'&addinv_seats',
                'desc' => $this->l('Agregar Butaca', null, null, false),
                'icon' => 'icon-adn '
            );
        }                                
        parent::initPageHeaderToolbar();
    }
    public function renderForm()
    {
        $this->fields_form = array(
            'legend' => array(
                'title' => $this->l("Butacas"),
                'icon' => 'icon-folder-close'
            ),
            'input' => array(
                array(
                    'type' => 'text',
                    'label' => $this->l("Butaca"),
                    'name' => 'name',
                    'col' => '4',
                    'required' => true
                ),
            ),           
            'submit' => array(
                'title' => $this->l('Save'),
            )
        );
        return parent::renderForm();
    }
}
