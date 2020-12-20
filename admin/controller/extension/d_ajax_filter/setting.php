<?php

/*
*  location: admin/controller
*/

class ControllerExtensionDAjaxFilterSetting extends Controller
{
    private $codename = 'd_ajax_filter';
    private $route = 'extension/d_ajax_filter/setting';
    private $extension = array();
    private $config_file = '';
    private $store_id = 0;
    private $error = array();
    
    public function __construct($registry)
    {
        parent::__construct($registry);
        $this->load->language($this->route);
        $this->load->model('extension/module/'.$this->codename);
        
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

        $this->document->addStyle('view/stylesheet/d_ajax_filter/setting.css');
        $this->document->addScript('view/javascript/d_ajax_filter/library/jquery.serializejson.js');

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

        if (isset($this->session->data['success'])) {
            $data['success'] = $this->session->data['success'];
            unset($this->session->data['success']);
        }
        
        // Breadcrumbs
        $data['breadcrumbs'] = array();
        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->model_extension_d_opencart_patch_url->link('common/home')
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

        
        $data['text_tab_general'] = $this->language->get('text_tab_general');
        $data['text_tab_custom_script'] = $this->language->get('text_tab_custom_script');
        $data['text_important'] = $this->language->get('text_important');
        $data['text_warning_multiple_value'] = $this->language->get('text_warning_multiple_value');
        $data['text_warning_genaral_setting'] = $this->language->get('text_warning_genaral_setting');

        $data['text_yes'] = $this->language->get('text_yes');
        $data['text_no'] = $this->language->get('text_no');
        $data['text_enabled'] = $this->language->get('text_enabled');

        $data['entry_ajax'] = $this->language->get('entry_ajax');
        $data['entry_contant_path'] = $this->language->get('entry_contant_path');
        $data['entry_display_out_of_stock'] = $this->language->get('entry_display_out_of_stock');
        $data['entry_in_stock_status'] = $this->language->get('entry_in_stock_status');
        $data['entry_display_sub_category'] = $this->language->get('entry_display_sub_category');
        $data['entry_recreate_cache'] = $this->language->get('entry_recreate_cache');
        $data['entry_multiple_attributes_value'] = $this->language->get('entry_multiple_attributes_value');
        $data['entry_separator'] = $this->language->get('entry_separator');
        $data['entry_fade_out_product'] = $this->language->get('entry_fade_out_product');
        $data['entry_display_out_of_stock'] = $this->language->get('entry_display_out_of_stock');
        $data['entry_display_loader'] = $this->language->get('entry_display_loader');
        $data['entry_display_selected_top'] = $this->language->get('entry_display_selected_top');
        $data['entry_selected_path'] = $this->language->get('entry_selected_path');

        $data['button_save'] = $this->language->get('button_save');
        $data['button_cancel'] = $this->language->get('button_cancel');
        $data['button_recreate_cache'] = $this->language->get('button_recreate_cache');

        $data['tabs'] = $this->{'model_extension_module_'.$this->codename}->getTabs('setting');

        $url = '';

        if (isset($this->request->get['module_id'])) {
            $url .= '&module_id='.$this->request->get['module_id'];
        }

        $data['action'] = $this->model_extension_d_opencart_patch_url->link('extension/'.$this->codename.'/setting/save', $url);

        $data['cancel'] = $this->model_extension_d_opencart_patch_url->link('marketplace/extension', 'type=module');

        // Variable
        $data['codename'] = $this->codename;
        $data['route'] = $this->route;
        $data['store_id'] = $this->store_id;
        $data['extension'] = $this->extension;
        $data['config'] = $this->config_file;
        $data['version'] = $this->extension['version'];
        $data['token'] = $this->model_extension_d_opencart_patch_user->getToken();

        $this->load->model('setting/setting');

        $setting = $this->model_setting_setting->getSetting($this->codename);

        if (!empty($setting[$this->codename.'_setting'])) {
            $data['setting'] = $setting[$this->codename.'_setting'];
        } else {
            $this->config->load('d_ajax_filter');
            $setting = $this->config->get('d_ajax_filter_setting');

            $data['setting'] = $setting['general'];
        }

        if (!empty($data['setting']['attributes'])) {
            $this->load->model('catalog/attribute');
            foreach ($data['setting']['attributes'] as $attribute_id => $value) {
                $attribute_info = $this->model_catalog_attribute->getAttribute($attribute_id);
                $data['setting']['attributes'][$attribute_id]['name'] = strip_tags(html_entity_decode($attribute_info['name'], ENT_QUOTES, 'UTF-8'));
            }
        }

        $this->load->model('localisation/stock_status');

        $data['stock_statuses'] = $this->model_localisation_stock_status->getStockStatuses();

        $url = '';

        if (isset($this->request->get['module_id'])) {
            $url .= '&module_id='.$this->request->get['module_id'];
        }

        $data['recreate_cache'] = $this->model_extension_d_opencart_patch_url->link('extension/'.$this->codename.'/cache', $url);

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');
        $this->response->setOutput($this->model_extension_d_opencart_patch_load->view($this->route, $data));
    }

    public function save()
    {
        $json = array();

        $this->load->model('setting/setting');
        $this->load->model('extension/d_opencart_patch/url');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
            $this->model_setting_setting->editSetting($this->codename, $this->request->post);

            $this->session->data['success'] = $this->language->get('text_success');
            $this->cache->delete('af-category');
            $this->cache->delete('af-manufacturer');
            $this->cache->delete('af-price');
            $this->cache->delete('af-ean');
            $this->cache->delete('af-filter');
            $this->cache->delete('af-option');
            $this->cache->delete('af-option-values');
            $this->cache->delete('af-total-attribute');
            $this->cache->delete('af-total-category');
            $this->cache->delete('af-total-manufacturer');
            $this->cache->delete('af-total-option');
            $this->cache->delete('af-total-stock-status');
            $this->cache->delete('af-total-filter');
            $this->cache->delete('af-total-rating');
            $this->cache->delete('af-total-ean');
            $this->cache->delete('af-translit');
            $this->cache->delete('af-url-params');
            $url = '';

            if (isset($this->request->get['module_id'])) {
                $url .= '&module_id='.$this->request->get['module_id'];
            }
            $json['redirect'] = str_replace('&amp;', '&', $this->model_extension_d_opencart_patch_url->link($this->route, $url));
            $json['success'] = 'success';
        } else {
            $json['errors'] = $this->error;
            $json['error'] = $this->error['warning'];
        }
        
        $this->response->addHeader('Content-Type: application/json');
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
}
