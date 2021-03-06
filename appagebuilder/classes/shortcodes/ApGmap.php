<?php
/**
 * 2007-2015 Apollotheme
 *
 * NOTICE OF LICENSE
 *
 * ApPageBuilder is module help you can build content for your shop
 *
 * DISCLAIMER
 *
 *  @author    Apollotheme <apollotheme@gmail.com>
 *  @copyright 2007-2015 Apollotheme
 *  @license   http://apollotheme.com - prestashop template provider
 */

if (!defined('_PS_VERSION_')) {
    # module validation
    exit;
}

class ApGmap extends ApShortCodeBase
{
    public $name = 'ApGmap';
    public $for_module = 'manage';

    public function getInfo()
    {
        return array('label' => $this->l('Google Map'),
            'position' => 5,
            'desc' => $this->l('Create a Google Map'),
            'icon_class' => 'icon-map-marker',
            'tag' => 'content');
    }

    public function getConfigList()
    {
        $iselect = Tools::getValue('display_store');
        
        if ($iselect === false) {
            $script_update_select = '<script>$("#display_store").removeAttr("checked");</script>';
        } elseif ($iselect == 1) {
            $script_update_select = '<script>$("#display_store").attr("checked", "checked");</script>';
        }
        // Get all store of shop
        $base_model = new ApPageBuilderModel();
        $data_list = $base_model->getAllStoreByShop();
        // Options for switch elements
        $zoom_option = array();
        for ($i = 1; $i <= 20; $i++) {
            $zoom_option[] = array('id' => $i, 'value' => $i);
        }
        $inputs = array(
            array(
                'type' => 'text',
                'name' => 'title',
                'label' => $this->l('Title'),
                'desc' => $this->l('Auto hide if leave it blank'),
                'lang' => 'true',
                'form_group_class' => 'aprow_general',
                'default' => ''
            ),
            array(
                'type' => 'textarea',
                'name' => 'sub_title',
                'label' => $this->l('Sub Title'),
                'lang' => true,
                'values' => '',
                'autoload_rte' => false,
                'default' => '',
            ),
            array(
                'type' => 'text',
                'label' => $this->l('Google Key'),
                'name' => 'gkey',
                'desc' => $this->l('Example: AIzaSyCWJmaoDNR_l3GVkP6uRnMzsGG5iuuU_AM'),
                'default' => '',
            ),
            array(
                'type' => 'select',
                'label' => $this->l('Zoom'),
                'name' => 'zoom',
                'default' => '11',
                'options' => array(
                    'query' => $zoom_option,
                    'id' => 'id',
                    'name' => 'value'
                )
            ),
            array(
                'type' => 'text',
                'label' => $this->l('Width'),
                'name' => 'width',
                'desc' => $this->l('Example: 100%, 100px'),
                'default' => '100%',
            ),
            array(
                'type' => 'text',
                'label' => $this->l('Height'),
                'name' => 'height',
                'desc' => $this->l('Example: 100%, 100px'),
                'default' => '300px',
            ),
            array(
                'type' => 'checkbox',
                'name' => 'display',
                'label' => $this->l('Select display store on map'),
                'class' => 'checkbox-group',
//                'desc' => $this->l('Uncheck is display all stores'),
                'values' => array(
                    'query' => array(
                        array(
                            'id' => 'store',
                            'name' => $this->l('Select store'),
                            'val' => '1'
                        )
                    ),
                    'id' => 'id',
                    'name' => 'name'
                )
            ),
            array(
                'type' => 'html',
                'name' => 'default_html',
                'html_content' => '<div class="alert alert-info">'.$this->l('Uncheck to Display all stores').'</div>'.$script_update_select,
            ),
            array(
                'type' => 'select',
                'label' => $this->l('List stores'),
                'desc' => $this->l('Can select multi store'),
                'name' => 'store[]',
                'multiple' => true,
                'options' => array(
                    'query' => $data_list,
                    'id' => 'id_store',
                    'name' => 'name'
                ),
                'form_group_class' => 'aprow_exceptions',
                'default' => 'all',
                'form_group_class' => 'display_store',
            ),
            array(
                'type' => 'switch',
                'label' => $this->l('Show Store Menu'),
                'name' => 'is_display_list',
                'values' => ApPageSetting::returnYesNo(),
                'default' => '0'
            ),
            array(
                'type' => 'switch',
                'label' => $this->l('Enable Gmap at Our_Stores page'),
                'name' => 'stores',
                'values' => ApPageSetting::returnYesNo(),
                'default' => '0'
            ),
            array(
                'type' => 'switch',
                'label' => $this->l('Enable Gmap at Sitemap page'),
                'name' => 'sitemap',
                'values' => ApPageSetting::returnYesNo(),
                'default' => '0'
            )
        );
        return $inputs;
    }

    public function prepareFontContent($assign, $module = null)
    {
        // validate module
        unset($module);
        // Get all store of shop
        $base_model = new ApPageBuilderModel();
        $data_list = $base_model->getAllStoreByShop();
        $form_atts = $assign['formAtts'];
        $not_all = (isset($form_atts['display_store']) && $form_atts['display_store']);
        $store_ids = explode(',', (isset($form_atts['store']) && $form_atts['store']) ? $form_atts['store'] : '');
        $assign['hasListStore'] = (isset($form_atts['is_display_list']) && $form_atts['is_display_list']) ? 'is_display_list' : '';
        $markers = array();
        if ($not_all) {
            foreach ($store_ids as $id) {
                foreach ($data_list as $store) {
                    if ($id == $store['id_store']) {
                        $markers[] = $store;
                        break;
                    }
                }
            }
        } else {
            $markers = $data_list;
        }
        foreach ($markers as &$marker) {
            $address = $this->processStoreAddress($marker);
            $marker['other'] = $this->renderStoreWorkingHours($marker);
            $marker['address'] = $address;
            $marker['has_store_picture'] = file_exists(_PS_STORE_IMG_DIR_.(int)$marker['id_store'].'.jpg');
        }
        
        $assign['marker_center'] = Tools::jsonEncode($this->getMarkerCenter($markers));
        $assign['marker_list'] = Tools::jsonEncode($markers);
        
        $assign['apGMap'] = Tools::jsonEncode(array(
            'translation_5'  => $this->l('Cliquez pour agrandir la carte'),
            'logo_store'  => Configuration::get('PS_STORES_ICON'),
            'img_store_dir' => _THEME_STORE_DIR_,
            'img_ps_dir' => _PS_IMG_,
        ));
        
        return $assign;
    }

    /**
     * Get formatted string address
     */
    protected function processStoreAddress($store)
    {
        $ignore_field = array(
            'firstname',
            'lastname'
        );
        $out_datas = array();
        $address_datas = AddressFormat::getOrderedAddressFields($store['id_country'], false, true);
        $state = (isset($store['id_state'])) ? new State($store['id_state']) : null;
        foreach ($address_datas as $data_line) {
            $data_fields = explode(' ', $data_line);
            $addr_out = array();
            $data_fields_mod = false;
            foreach ($data_fields as $field_item) {
                $field_item = trim($field_item);
                if (!in_array($field_item, $ignore_field) && !empty($store[$field_item])) {
                    $addr_out[] = ($field_item == 'city' && $state && isset($state->iso_code) && Tools::strlen($state->iso_code)) ?
                            $store[$field_item].', '.$state->iso_code : $store[$field_item];
                    $data_fields_mod = true;
                }
            }
            if ($data_fields_mod) {
                $out_datas[] = implode(' ', $addr_out);
            }
        }
        $out = implode('<br />', $out_datas);
        return $out;
    }

    public function renderStoreWorkingHours($store)
    {
        $days = array();
        $days[1] = 'Monday';
        $days[2] = 'Tuesday';
        $days[3] = 'Wednesday';
        $days[4] = 'Thursday';
        $days[5] = 'Friday';
        $days[6] = 'Saturday';
        $days[7] = 'Sunday';
        
        $hours_temp = $store['hours'];
        $hours_temp = Tools::jsonDecode($hours_temp);
        $hours = array();
        //DONGND:: fix when stores do not have the data of open time
        if (count($hours_temp) > 0) {
            foreach ($hours_temp as $h) {
                $hours[] = implode(' | ', $h);
            }
        }
        
        if (!empty($hours)) {
            $result = '';
            for ($i = 1; $i < 8; $i++) {
                if (isset($hours[(int)$i - 1])) {
                    Context::getContext()->smarty->assign(array(
                        'days' => $days,
                        'hours' => $hours,
                        'i' => $i,
                    ));
                    
                    $file_name = _PS_MODULE_DIR_.'appagebuilder/views/templates/front/shortcodes/ApGmap.tpl';
                    $result .= Context::getContext()->smarty->fetch($file_name);
                }
            }
            return $result;
        }
        return false;
    }

    private function getMarkerCenter($markers)
    {
        //'default lat/long = 21.010904,105.787736 is location of LeoTheme
        $lat = 21.010904;
        $long = 105.787736;
        return (is_array($markers) && count($markers) > 0) ? $markers[0] : array('latitude' => $lat, 'longitude' => $long);
    }
}
