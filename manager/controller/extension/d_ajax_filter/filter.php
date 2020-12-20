<?php

/*
*  location: admin/controller
*/

class ControllerExtensionDAjaxFilterFilter extends Controller
{
    private $codename = 'd_ajax_filter';
    private $route = 'extension/d_ajax_filter/filter';
    private $extension = array();
    private $config_file = '';
    private $store_id = 0;
    private $error = array();
    
    public function __construct($registry)
    {
        parent::__construct($registry);
        $this->load->model($this->route);
        $this->load->model('extension/module/'.$this->codename);
        $this->load->language($this->route);
        
        //extension.json
        $this->extension = json_decode(file_get_contents(DIR_SYSTEM.'library/d_shopunity/extension/'.$this->codename.'.json'), true);
        $this->d_shopunity = (file_exists(DIR_SYSTEM.'library/d_shopunity/extension/d_shopunity.json'));
        $this->d_admin_style = (file_exists(DIR_SYSTEM.'library/d_shopunity/extension/d_admin_style.json'));
        
        //Store_id (for multistore)
        if (isset($this->request->get['store_id'])) { 
            $this->store_id = $this->request->get['store_id'];
        }
    }

    
    public function index()
    {
        $this->load->model('extension/d_opencart_patch/url');
        $this->load->model('extension/d_opencart_patch/store');
        $this->load->model('extension/d_opencart_patch/setting');
        $this->load->model('extension/d_opencart_patch/load');
        $this->load->model('extension/d_opencart_patch/module');
        $this->load->model('extension/d_opencart_patch/user');

        $this->document->addStyle('view/stylesheet/d_bootstrap_extra/bootstrap.css');

        $this->document->addScript('view/javascript/d_bootstrap_switch/js/bootstrap-switch.min.js');
        $this->document->addStyle('view/javascript/d_bootstrap_switch/css/bootstrap-switch.css');

        $this->document->addScript('view/javascript/d_rubaxa_sortable/sortable.min.js');
        $this->document->addStyle('view/javascript/d_rubaxa_sortable/sortable.css');

        $this->document->addScript('view/javascript/d_ajax_filter/library/tinysort.js');

        $this->document->addStyle('view/stylesheet/d_ajax_filter/filter.css');

        $this->document->addScript('view/javascript/d_ajax_filter/library/jquery.serializejson.js');
        $this->document->addScript('view/javascript/d_ajax_filter/library/underscore-min.js');
        $this->document->addScript('view/javascript/d_ajax_filter/filter.js');

        if($this->d_admin_style){
            $this->load->model('extension/d_admin_style/style');

            $this->model_extension_d_admin_style_style->getAdminStyle('light');
        }

        // Add more styles, links or scripts to the project is necessary
        $url_params = array();
        $url = '';

        if (isset($this->response->get['store_id'])) {
            $url_params['store_id'] = $this->store_id;
        }

        if (isset($this->response->get['config'])) {
            $url_params['config'] = $this->response->get['config'];
        }

        $url = ((!empty($url_params)) ? '&' : '') . http_build_query($url_params);

        if(isset($this->session->data['success'])){
            $data['success'] = $this->session->data['success'];
            unset($this->session->data['success']);
        }

        // Breadcrumbs
        $data['breadcrumbs'] = array();
        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' =>$this->model_extension_d_opencart_patch_url->link('common/home')
            );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_module'),
            'href' => $this->model_extension_d_opencart_patch_url->link('marketplace/extension', 'type=module')
            );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title_main'),
            'href' => $this->model_extension_d_opencart_patch_url->link($this->route, $url)
            );


        $this->document->setTitle($this->language->get('heading_title_main'));
        $data['heading_title'] = $this->language->get('heading_title_main');
        $data['text_form'] = $this->language->get('text_form');

        $data['text_setting'] = $this->language->get('text_setting');
        $data['text_list'] = $this->language->get('text_list');
        $data['text_image'] = $this->language->get('text_image');
        $data['text_filter_default'] = $this->language->get('text_filter_default');
        $data['text_individual_filter_setting'] = $this->language->get('text_individual_filter_setting');
        $data['text_warning_select_filter'] = $this->language->get('text_warning_select_filter');
        $data['text_important'] = $this->language->get('text_important');
        $data['text_warning_filter_individual'] = $this->language->get('text_warning_filter_individual');
        $data['text_warning_default_setting'] = $this->language->get('text_warning_default_setting');
        $data['text_warning_image_filter'] = $this->language->get('text_warning_image_filter');
        $data['text_file_manager'] = $this->language->get('text_file_manager');
        $data['text_filter_setting'] = $this->language->get('text_filter_setting');
        $data['text_general_filter_setting'] = $this->language->get('text_general_filter_setting');
        $data['text_individual_setting'] = $this->language->get('text_individual_setting');
        $data['text_default_setting'] = $this->language->get('text_default_setting');
        $data['text_on'] = $this->language->get('text_on');
        $data['text_off'] = $this->language->get('text_off');
        $data['text_filter_default_general'] = $this->language->get('text_filter_default_general');

        $data['text_yes'] = $this->language->get('text_yes');
        $data['text_no'] = $this->language->get('text_no');
        $data['text_none'] = $this->language->get('text_none');
        $data['text_enabled'] = $this->language->get('text_enabled');

        $data['column_status'] = $this->language->get('column_status');
        $data['column_type'] = $this->language->get('column_type');
        $data['column_collapse'] = $this->language->get('column_collapse');
        $data['column_sort_order_values'] = $this->language->get('column_sort_order_values');

        $data['entry_filter'] = $this->language->get('entry_filter');
        $data['entry_additional_image'] = $this->language->get('entry_additional_image');
        $data['entry_filter_value'] = $this->language->get('entry_filter_value');
        $data['entry_type'] = $this->language->get('entry_type');
        $data['entry_sort_order_values'] = $this->language->get('entry_sort_order_values');
        $data['entry_collapse'] = $this->language->get('entry_collapse');

        $data['button_save'] = $this->language->get('button_save');
        $data['button_cancel'] = $this->language->get('button_cancel');
        $data['button_reset_image'] = $this->language->get('button_reset_image');

        $data['tabs'] = $this->{'model_extension_module_'.$this->codename}->getTabs('filter');
        $url = '';

        if(isset($this->request->get['module_id'])){
            $url .='&module_id='.$this->request->get['module_id'];
        }

        $data['action'] = $this->model_extension_d_opencart_patch_url->link('extension/'.$this->codename.'/filter/save', $url);

        $data['cancel'] = $this->model_extension_d_opencart_patch_url->link('marketplace/extension', 'type=module');
        // Variable
        $data['codename'] = $this->codename;
        $data['route'] = $this->route;
        $data['store_id'] = $this->store_id;
        $data['extension'] = $this->extension;
        $data['config'] = $this->config_file;
        $data['version'] = $this->extension['version'];
        $data['token'] = $this->model_extension_d_opencart_patch_user->getToken();
        $data['token_url'] = $this->model_extension_d_opencart_patch_user->getUrlToken();

        $this->load->model('setting/setting');

        $setting = $this->model_setting_setting->getSetting($this->codename.'_filters');

        if(!empty($setting[$this->codename.'_filters'])){
            $data['setting'] = $setting[$this->codename.'_filters'];
        }
        else{
            $this->config->load('d_ajax_filter');
            $setting = $this->config->get('d_ajax_filter_setting');

            $data['setting'] = $setting['filters'];
        }

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

        if(!empty($data['setting']['filters'])){
            $this->load->model('catalog/filter');
            foreach ($data['setting']['filters'] as $filter_group_id => $value) {
                $filter_group_info = $this->model_catalog_filter->getFilterGroup($filter_group_id);
                $data['setting']['filters'][$filter_group_id]['name'] = strip_tags(html_entity_decode($filter_group_info['name'], ENT_QUOTES, 'UTF-8'));
            }
        }

        $this->load->model('localisation/language');

        $data['languages'] = $this->model_localisation_language->getLanguages();

        $this->load->model('tool/image');

        $data['placeholder'] = $this->model_tool_image->resize('no_image.png', 100, 100);

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');
        $this->response->setOutput($this->model_extension_d_opencart_patch_load->view($this->route, $data));
    }

    public function save(){
        $json = array();

        $this->load->model('setting/setting');
        $this->load->model('extension/d_opencart_patch/url');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
            $this->model_setting_setting->editSetting($this->codename.'_filters', $this->request->post);

            $this->session->data['success'] = $this->language->get('text_success');
            $url = '';

            if(isset($this->request->get['module_id'])){
                $url .= '&module_id='.$this->request->get['module_id'];
            }
            $json['redirect'] = str_replace('&amp;','&',$this->model_extension_d_opencart_patch_url->link($this->route, $url));
            $json['success'] = 'success';
        }
        else{
            $json['errors'] = $this->error;
            $json['error'] = $this->error['warning'];

        }
        
        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
        
    }

    public function getFilterGroups(){
        $json = array();
        if(isset($this->request->post['language_id'])){
            $language_id = $this->request->post['language_id'];
        }
        if(isset($language_id))
        {
            $this->load->model('tool/image');

            $json['values'] = array();

            $results = $this->{'model_extension_'.$this->codename.'_filter'}->getFilterGroupsByLanguageId($language_id);

            foreach ($results as $filter_group) {
                $json['values'][] = array(
                    'id' => $filter_group['filter_group_id'],
                    'name' => strip_tags(html_entity_decode($filter_group['name'], ENT_QUOTES, 'UTF-8'))
                    );
            }
            $json['success'] = 'success';
        }
        else{
            $json['error'] = 'error';
        }
        $this->response->setOutput(json_encode($json));
    }

    public function getFilterImages(){
        $json = array();
        if(isset($this->request->post['filter_group_id'])){
            $filter_group_id = $this->request->post['filter_group_id'];
        }
        if(isset($this->request->post['language_id'])){
            $language_id = $this->request->post['language_id'];
        }
        if(isset($filter_group_id)&&isset($language_id))
        {

            $this->load->model('tool/image');

            $results = $this->{'model_extension_'.$this->codename.'_filter'}->getFilterImages($filter_group_id, $language_id);

            $json['values']=array();

            foreach ($results as $filter_value) {

                if(!empty($filter_value['image']))
                {
                    $thumb = $this->model_tool_image->resize($filter_value['image'],100,100);
                }
                else {
                    $thumb = $this->model_tool_image->resize('no_image.png',100,100);
                }

                $json['values'][] =  array(
                    'filter_id' => $filter_value['filter_id'],
                    'image' => $filter_value['image'],
                    'text' => $filter_value['name'],
                    'thumb' => $thumb
                    );
            }
            $json['success'] = 'success';
        }
        else{
            $json['error'] = 'error';
        }
        $this->response->setOutput(json_encode($json));
    }

    public function editFitlerImages()
    {
        $json = array();
        if(isset($this->request->get['language_id'])){
            $language_id = $this->request->get['language_id'];
        }
        if(isset($language_id)&&!empty($this->request->post['filter_images']))
        {
            $this->{'model_extension_'.$this->codename.'_filter'}->editFilterImages($language_id, $this->request->post['filter_images']);
            $json['success'] = 'success';
        }
        else {
            $json['error'] = 'error';
        }
        $this->response->setOutput(json_encode($json));
    }

    private function validate($permission = 'modify')
    {

        if (!$this->user->hasPermission($permission, $this->route)) {
            $this->error['warning'] = $this->language->get('error_permission');
            return false;
        }

        return true;
    }

    public function autocomplete() {
        $json = array();

        if (isset($this->request->get['filter_name'])) {

            $filter_data = array(
                'filter_name' => $this->request->get['filter_name'],
                'start'       => 0,
                'limit'       => 10
                );

            $results = $this->{'model_extension_'.$this->codename.'_filter'}->getFilterGroups($filter_data);

            foreach ($results as $fitler_group) {
                $json[] = array(
                    'filter_group_id' => $fitler_group['filter_group_id'],
                    'name'      => strip_tags(html_entity_decode($fitler_group['name'], ENT_QUOTES, 'UTF-8'))
                    );
            }
        }

        $sort_order = array();

        foreach ($json as $key => $value) {
            $sort_order[$key] = $value['name'];
        }

        array_multisort($sort_order, SORT_ASC, $json);

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }
}