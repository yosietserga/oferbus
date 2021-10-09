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

class AdminViajeroOrigenesController extends ModuleAdminController
{
    protected $status_array = array();
    protected $destinations = array();
    public function __construct()
    {
        $this->bootstrap = true;
        $this->table = "inv_origins";
        $this->className = "ViajeroOrigenes";
        $this->identifier = "id_origen";
        $this->fields_list = array();
        parent::__construct();
        $this->actions = array('edit', 'delete');
    
        parent::__construct();

        $this->fields_list = array(
            'id_origen' => array(
                'title' => $this->l('ID'),
            ),
            'origen' => array(
                'title' => $this->l('Origen')
            ),
            'latitud' => array(
                'title' => $this->l('Latitud')
            ),
            'longitud' => array(
                'title' => $this->l('Longitud')
            ),
        );
    }

    public function postProcess()
    {
        parent::postProcess();
    }
    
    public function deleteOrigins() {
        $origen = new ViajeroOrigenes(Tools::getValue('id_origen'));
        $attribute = new AttributeCore($origen->id_attribute);
        $attribute->delete();
        $origen->delete();
        return true;
    }

    public function updateOrigins()
    {
        $origen = new ViajeroOrigenes(Tools::getValue('id_origen'));
        $origen->origen=Tools::getValue('origen');
        $origen->latitud=Tools::getValue('latitud');
        $origen->longitud=Tools::getValue('longitud');
        $origen->id_destiny = Tools::getValue('id_destiny');
        $origen->id_provincia = Tools::getValue('id_provincia');
        $origen->update();  
        $attribute = new AttributeCore($origen->id_attribute);        
        $attribute->name[$this->context->language->id] = Tools::getValue('origen');     
        $attribute->update();                      
        return true;
    }
    
    public function saveOrigins() 
    {
        if (Tools::getValue('id_origen') == '') {
            $idAtribute = (int) ViajeroPaquetes::getIdAttributeClassName('origenes');
            AttributeGroupCore::getHigherPosition();
            $attribute = new AttributeCore();
            $attribute->id_attribute_group = $idAtribute;
            $attribute->position = AttributeGroupCore::getHigherPosition() + 1;
            $attribute->name[$this->context->language->id] = Tools::getValue('origen');
            $attribute->add();
            $origen = new ViajeroOrigenes();
            $origen->origen = Tools::getValue('origen');
            $origen->latitud = Tools::getValue('latitud');
            $origen->longitud = Tools::getValue('longitud');
            $origen->id_attribute = $attribute->id;
            $origen->id_provincia = Tools::getValue('id_provincia');
            $origen->save();
        } else {
            $this->updateOrigins();
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
            $this->page_header_toolbar_btn['new_Destiny'] = array(
                'href' => $this->context->link->getAdminLink('AdminViajeroDestino'),
                'desc' => $this->l('Destinos de viaje', null, null, false),
                'icon' => 'icon-map-marker'
            );
        }
        if (empty($this->display)) {
            $this->page_header_toolbar_btn['add_origins'] = array(
                'href' => $this->context->link->getAdminLink('AdminViajeroOrigenes') . '&addinv_origins',
                'desc' => $this->l('Agregar Origines', null, null, false),
                'icon' => 'icon-adn '
            );
        }           
        parent::initPageHeaderToolbar();
    }
    public function renderForm()
    {
        $states_array = State::getStatesByIdCountry(44);
        $this->fields_form = array(
            'legend' => array(
                'title' => $this->l("Origen"),
                'icon' => 'icon-folder-close'
            ),
            'input' => array(
                array(
                    'type' => 'text',
                    'label' => $this->l("Origen"),
                    'name' => 'origen',
                    'col' => '4',
                    'required' => true
                ),
                array(
                    'type' => 'select',
                    'label' => $this->l('Provincia'),
                    'name' => 'id_provincia',
                    'col' => '2',
                    'required' => true,
                    'options' => array(
                        'query' => State::getStatesByIdCountry(44),
                        'id' => 'id_state',
                        'name' => 'name'
                    ),
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
            ),
            'submit' => array(
                'title' => $this->l('Save'),
            )
        );

        return parent::renderForm();
    }
}
