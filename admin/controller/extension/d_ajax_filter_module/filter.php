<?php

class ControllerExtensionDAjaxFilterModuleFilter extends Controller
{
    private $codename = 'd_ajax_filter';
    private $route = 'extension/d_ajax_filter_module/filter';
    
    public function __construct($registry)
    {
        parent::__construct($registry);
        $this->load->model($this->route);
        $this->load->language('extension/'.$this->codename.'/filter');
    }
    public function updateProduct($product_id){
        $new_values = $this->{'model_extension_'.$this->codename.'_module_filter'}->updateProduct($product_id);
        return $new_values;
    }

    public function step($data){
        $count = $this->{'model_extension_'.$this->codename.'_module_filter'}->step($data);
        return $count;
    }

    public function save($data){
        $count = $this->{'model_extension_'.$this->codename.'_module_filter'}->save($data);
        return $count;
    }

    public function restore($data){
        $count = $this->{'model_extension_'.$this->codename.'_module_filter'}->restore($data);
        return $count;
    }

    public function prepare(){
        $this->{'model_extension_'.$this->codename.'_module_filter'}->prepare();
    }

    public function cleaning(){
        $this->{'model_extension_'.$this->codename.'_module_filter'}->cleaning();
    }

    public function cleaning_before(){
        $this->{'model_extension_'.$this->codename.'_module_filter'}->cleaning_before();
    }

    public function prepare_template($setting){

        $this->load->model('extension/d_opencart_patch/url');
        $this->load->model('extension/d_opencart_patch/user');
        $this->load->model('extension/d_opencart_patch/load');

        $data['entry_type'] = $this->language->get('entry_type');
        $data['entry_collapse'] = $this->language->get('entry_collapse');
        $data['entry_sort_order_values'] = $this->language->get('entry_sort_order_values');

        $data['column_status'] = $this->language->get('column_status');
        $data['column_type'] = $this->language->get('column_type');
        $data['column_collapse'] = $this->language->get('column_collapse');
        $data['column_sort_order_values'] = $this->language->get('column_sort_order_values');

        $data['text_filter_default'] = $this->language->get('text_filter_default');
        $data['text_individual_filter_setting'] = $this->language->get('text_individual_filter_setting');
        $data['text_warning_select_filter'] = $this->language->get('text_warning_select_filter');
        $data['text_yes'] = $this->language->get('text_yes');
        $data['text_no'] = $this->language->get('text_no');
        $data['text_default'] = $this->language->get('text_default');
        $data['text_enabled'] = $this->language->get('text_enabled');
        $data['text_important'] = $this->language->get('text_important');
        $data['text_warning_filter_individual'] = $this->language->get('text_warning_filter_individual');
        $data['text_default_filter_settings'] = $this->language->get('text_default_filter_settings');
        $data['text_filter_setting'] = $this->language->get('text_filter_setting');
        $data['text_individual_setting'] = $this->language->get('text_individual_setting');
        $data['text_default_setting'] = $this->language->get('text_default_setting');
        $data['text_on'] = $this->language->get('text_on');
        $data['text_off'] = $this->language->get('text_off');
        $data['text_global'] = $this->language->get('text_global');

        $data['button_edit_default'] = $this->language->get('button_edit_default');

        $data['token'] = $this->model_extension_d_opencart_patch_user->getUrlToken();

        $data['base_types'] = array(
            'radio' => $this->language->get('text_base_type_radio'),
            'select' => $this->language->get('text_base_type_select'),
            'checkbox' => $this->language->get('text_base_type_checkbox'),
            'radio_and_image' => $this->language->get('text_base_type_radio_and_image'),
            'checkbox_and_image' => $this->language->get('text_base_type_checkbox_and_image'),
            'image_radio' => $this->language->get('text_base_type_image_radio'),
            'image_checkbox' => $this->language->get('text_base_type_image_checkbox')
            );

        $data['sort_order_types'] = array(
            'default' => $this->language->get('text_sort_order_type_default'),
            'string_asc' => $this->language->get('text_sort_order_type_string_asc'),
            'string_desc' => $this->language->get('text_sort_order_type_string_desc'),
            'numeric_asc' => $this->language->get('text_sort_order_type_numeric_asc'),
            'numeric_desc' => $this->language->get('text_sort_order_type_numeric_desc'),
            );

        $data['filters'] = !empty($setting['filters'])?$setting['filters']:array();

        $url = '';

        if(isset($this->request->get['module_id'])){
            $url = '&module_id='.$this->request->get['module_id'];
        }

        $data['filter_href'] = $this->model_extension_d_opencart_patch_url->link('extension/'.$this->codename.'/filter', $url);

        $this->load->model('catalog/filter');
        
        array_walk($data['filters'], function(&$value, $index){
            $filter_info = $this->model_catalog_filter->getFilterGroup($index);
            $value['name'] = strip_tags(html_entity_decode($filter_info['name'], ENT_QUOTES, 'UTF-8'));

        });

        $filter_default = $this->{'model_extension_'.$this->codename.'_layout'}->getModuleSetting('filter');

        $data['filter_default'] = isset($setting['filter_default'])?$setting['filter_default']:$filter_default['default'];

        $data['default'] = $filter_default['default'];
        
        return $this->model_extension_d_opencart_patch_load->view('extension/'.$this->codename.'/layout_partial/filter', $data);
    }

    public function install(){
        $this->{'model_extension_'.$this->codename.'_module_filter'}->installModule();
    }

    public function uninstall(){
        $this->{'model_extension_'.$this->codename.'_module_filter'}->uninstallModule();
    }
}