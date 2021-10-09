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

class AdminViajeroAdicionalesController extends ModuleAdminController
{
    protected $status_array = array();
    public function __construct()
    {
        $this->bootstrap = true;
        $this->table = "inv_aditionals";
        $this->className = "ViajeroAdicionales";
        $this->identifier = "id_aditional";
        $this->fields_list = array();
        parent::__construct();
        $this->actions = array('edit', 'delete');

        $this->fields_list = array(
            'id_aditional' => array(
                'title' => $this->l('ID'),
            ),
            'name' => array(
                'title' => $this->l('Adicional')
            ),
        );
    }

    public function postProcess()
    {

        parent::postProcess();
    }
     public function deleteAditionals(){
        $aditional = new ViajeroAdicionales(Tools::getValue('id_seat'));
        $attribute = new AttributeCore($aditional->id_attribute);
        $attribute->delete();       
        $aditional->delete();
        return true;
    }
    public function updateAditionals(){
        $aditional = new ViajeroAdicionales(Tools::getValue('id_aditional'));
        $aditional->name=Tools::getValue('name');
        $aditional->update();  
        $attribute = new AttributeCore($aditional->id_attribute);        
        $attribute->name[$this->context->language->id] = Tools::getValue('name');     
        $attribute->update();                      
        return true;
    }
    
    public function saveAditionals(){
        if (Tools::getValue('id_aditional') == '') {
            $idAtribute = (int) ViajeroPaquetes::getIdAttributeClassName('adicionales');
            AttributeGroupCore::getHigherPosition();
            $attribute = new AttributeCore();
            $attribute->id_attribute_group = $idAtribute;
            $attribute->position = AttributeGroupCore::getHigherPosition() + 1;
            $attribute->name[$this->context->language->id] = Tools::getValue('name');
            $attribute->add();
            $aditional = new ViajeroAdicionales();
            $aditional->name = Tools::getValue('name');
            $aditional->id_attribute = $attribute->id;
            $aditional->save();
        } else {
            $this->updateAditionals();
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
    public function renderForm()
    {

        $this->fields_form = array(
            'legend' => array(
                'title' => $this->l("tipo de Adicional"),
                'icon' => 'icon-folder-close'
            ),
            'input' => array(
                array(
                    'type' => 'text',
                    'label' => $this->l("Adicional"),
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
