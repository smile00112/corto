<?php

class ControllerExtensionModuleDAjaxFilter extends Controller
{
    protected $codename = 'd_ajax_filter';
    protected $route_url = 'extension/module/d_ajax_filter';
    
    private $route = 'extension/module/d_ajax_filter';
    
    private $extension = '';
    
    private $error = array();
    
    private $theme = 'default';
    
    private $common_setting = array();
    
    public function __construct($registry)
    {
        parent::__construct($registry);
        
        if(!empty($this->request->get['ajax'])){
            return ;
        }

        $this->load->model($this->route);
        $this->load->language($this->route);
        
        
        if ($this->config->get('config_theme') == 'default') {
            $this->theme = $this->config->get('theme_default_directory');
        } else {
            $this->theme = $this->config->get('config_theme');
        }

        if(!$this->theme){
            $this->theme = $this->config->get('config_template');
        }
        $this->load->model('setting/setting');
        $common_setting = $this->model_setting_setting->getSetting($this->codename);
        
        if(empty($common_setting[$this->codename.'_setting'])){
            $this->config->load('d_ajax_filter');
            $setting = $this->config->get('d_ajax_filter_setting');
            
            $common_setting = $setting['general'];
        }
        else{
            $common_setting = $common_setting[$this->codename.'_setting'];
        }
        
        $this->common_setting = $common_setting;
        
        $this->extension = json_decode(file_get_contents(DIR_SYSTEM.'library/d_shopunity/extension/'.$this->codename.'.json'), true);
    }

    
    public function index($setting)
    {

        if(!empty($this->request->get['ajax'])){
            return ;
        }

        if (preg_match('/(iPhone|iPod|iPad|Android|Windows Phone)/', $this->request->server['HTTP_USER_AGENT'])) {
            $mobile = $data['mobile'] = 1;
        }
        else {
            $mobile = $data['mobile'] = 0;
        }
        
        if($mobile && !$setting['show_mobile']) {
            return ;
        }
        
        if (file_exists(DIR_TEMPLATE . $this->theme. '/stylesheet/d_ajax_filter/d_ajax_filter.css')) {
            $this->document->addStyle('catalog/view/theme/' . $this->theme . '/stylesheet/d_ajax_filter/d_ajax_filter.css');
        } else {
            $this->document->addStyle('catalog/view/theme/default/stylesheet/d_ajax_filter/d_ajax_filter.css');
        }
        
        $this->document->addScript('catalog/view/javascript/d_ajax_filter/library/underscore-min.js');
        $this->document->addScript('catalog/view/javascript/d_riot/riotcompiler.min.js');
        
        if (file_exists(DIR_TEMPLATE . $this->theme . '/javascript/d_ajax_filter/d_ajax_filter.js')) {
            $this->document->addScript('catalog/view/theme/' . $this->theme . '/javascript/d_ajax_filter/d_ajax_filter.js');
        } else {
            $this->document->addScript('catalog/view/theme/default/javascript/d_ajax_filter/d_ajax_filter.js');
        }
        
        $this->document->addStyle('catalog/view/javascript/d_ajax_filter/library/Ion.RangeSlider/ion.rangeSlider.css');
        $this->document->addStyle('catalog/view/javascript/d_ajax_filter/library/Ion.RangeSlider/ion.rangeSlider.skinAjaxFilter.css');
        $this->document->addScript('catalog/view/javascript/d_ajax_filter/library/Ion.RangeSlider/ion.rangeSlider.min.js');

        $this->document->addScript('catalog/view/javascript/d_ajax_filter/library/mCustomScrollbar/jquery.mCustomScrollbar.min.js');
        $this->document->addStyle('catalog/view/javascript/d_ajax_filter/library/mCustomScrollbar/jquery.mCustomScrollbar.min.css');
        
        $this->document->addScript('catalog/view/javascript/d_ajax_filter/library/wNumb.js');
        $this->document->addScript('catalog/view/javascript/d_ajax_filter/library/jquery.touchwipe.min.js');
        
        //Bootstrap Rating
        $this->document->addStyle('catalog/view/javascript/d_bootstrap_rating/bootstrap-rating.css');
        $this->document->addScript('catalog/view/javascript/d_bootstrap_rating/bootstrap-rating.js');
        
        if($setting['theme'] != 'custom'){
            if (file_exists(DIR_TEMPLATE . $this->theme . '/stylesheet/d_ajax_filter/themes/'.$setting['theme'].'.css')) {
                $this->document->addStyle('catalog/view/theme/' . $this->theme . '/stylesheet/d_ajax_filter/themes/'.$setting['theme'].'.css');
            } else {
                $this->document->addStyle('catalog/view/theme/default/stylesheet/d_ajax_filter/themes/'.$setting['theme'].'.css');
            }
        }
        
        $data['riot_tags'] = $this->{'model_extension_module_'.$this->codename}->getRiotTags();
        
        $json = array();
        
        $data['setting'] = $setting;
        $json['common_setting'] = $this->common_setting;
        $json['common_setting']['selected_path'] = html_entity_decode($this->common_setting['selected_path'], ENT_QUOTES, 'UTF-8');
        $json['selected'] = $this->{'model_extension_module_'.$this->codename}->getParamsToArray();
        
        if(!empty($setting['title'][$this->config->get('config_language_id')])){
            $data['setting']['heading_title'] = $setting['title'][$this->config->get('config_language_id')];
        } else {
            $data['setting']['heading_title'] = $this->language->get('heading_title');
        }
        $json['translate']['text_none'] = $this->language->get('text_none');
        $json['translate']['text_search'] = $this->language->get('text_search_placeholder');
        
        $json['translate']['text_price'] = $this->language->get('text_price');
        
        $json['translate']['button_filter'] = $this->language->get('button_filter');
        $json['translate']['button_reset'] = $this->language->get('button_reset');
        
        $json['translate']['text_show_more'] = $this->language->get('text_show_more');
        $json['translate']['text_shrink'] = $this->language->get('text_shrink');
        
        $json['translate']['text_symbol_left'] = $this->currency->getSymbolLeft($this->session->data['currency']);
        $json['translate']['text_symbol_right'] = $this->currency->getSymbolRight($this->session->data['currency']);
        $json['translate']['text_not_found'] = $this->language->get('text_not_found');
        
        $quantity_status = $setting['display_quantity'];
        
        $filter_data = $this->{'model_extension_module_'.$this->codename}->getFitlerData();

        if(isset($filter_data['filter_category_id'])){
            if(!empty($setting['show_mode'])){
                if($setting['show_mode'] == 'show'){
                    if(!in_array($filter_data['filter_category_id'], $setting['categories'])){
                        return ;
                    }
                }
                if($setting['show_mode'] == 'hide'){
                    if(in_array($filter_data['filter_category_id'], $setting['categories'])){
                        return ;
                    }
                }
            }
        }
        
        $quantity_data = array();
        
        $base_attribs =  array_filter($setting['base_attribs'], function($base_attrib) {
            return $base_attrib['status'];
        });

        if(empty($base_attribs)){
            return ;
        }
        
        $data['groups'] = array();
        
        $this->{'model_extension_module_'.$this->codename}->prepareTable($filter_data, isset($base_attribs['price'])?true:false);
        uasort($base_attribs, array($this, "compare"));
        
        foreach ($base_attribs as $key => $value) {
            if(file_exists(DIR_APPLICATION.'/controller/extension/d_ajax_filter/'.$key.'.php')){
                $result = $this->load->controller('extension/'.$this->codename.'/'.$key, ($value + array('module_setting' => $setting)));
                if(!empty($result)){
                    $data['groups'][$key] = $result;
                }
            }
        }
        
        $data['custom_style'] = html_entity_decode($setting['custom_style'], ENT_QUOTES, 'UTF-8');
        $data['custom_script'] = html_entity_decode($this->common_setting['custom_script'], ENT_QUOTES, 'UTF-8');
        
        $data['id'] = 'af'.rand();
        
        $data['current_path'] = isset($this->request->get['path']) ? $this->request->get['path'] : '';
        $data['current_manufacturer_id'] = isset($this->request->get['manufacturer_id']) ? $this->request->get['manufacturer_id'] : '';
        $data['current_route'] = isset($this->request->get['route'])?$this->request->get['route']:'';
        
        $url = $this->{'model_extension_module_'.$this->codename}->getURLQuery();
        
        $json['url'] = array(
            'quantity' => 'index.php?route='.$this->route_url.'/getQuantity&'.$url,
            'ajax' =>'index.php?route='.$this->route_url.'/ajax&'.$url
            );
        
        $data['json'] = $json;
        $data['groups'] = json_encode($data['groups']);

        $this->load->model('extension/d_opencart_patch/load');

        if (VERSION >= '2.2.0.0'){
            return $this->model_extension_d_opencart_patch_load->view('extension/d_ajax_filter/d_ajax_filter', $data);
        } elseif (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/extension/d_ajax_filter/d_ajax_filter')) {
            return $this->model_extension_d_opencart_patch_load->view($this->config->get('config_template') . '/template/extension/d_ajax_filter/d_ajax_filter', $data);
        } else {
            return $this->model_extension_d_opencart_patch_load->view('default/template/extension/d_ajax_filter/d_ajax_filter', $data);
        }
    }
    
    private function compare($a, $b) {
        if(isset($a['sort_order']) && isset($b['sort_order'])){
            if ($a['sort_order'] == $b['sort_order']) {
                return 0;
            }
            return ($a['sort_order'] < $b['sort_order']) ? -1 : 1;
        }
        else {
            return 0;
        }
    }
    
    public function getQuantity(){
        if(isset($this->request->get['curRoute'])){
            $route = $this->request->get['curRoute'];
        }
        
        if(isset($this->request->post['status'])){
            $status = $this->request->post['status'];
        }
        
        $json = array();
        if(isset($route)&& !empty($status)){
            if(isset($this->request->get['path'])){
                $parts = explode('_', (string)$this->request->get['path']);
                $categoryId = array_pop($parts);
            }
            else{
                $categoryId = false;
            }
            if(isset($this->request->get['search'])){
                $search = $this->request->get['search'];
            }
            else{
                $search = '';
            }
            if(isset($this->request->get['path'])){
                $path = $this->request->get['path'];
            }
            else{
                $path = '';
            }
            if(isset($this->request->get['tag'])){
                $tag = $this->request->get['tag'];
            }
            else{
                $tag = $search;
            }
            if(isset($this->request->get['manufacturer_id'])){
                $manufacturer_id = $this->request->get['manufacturer_id'];
            }
            else{
                $manufacturer_id = 0;
            }
            if(isset($this->request->get['description'])){
                $description = $this->request->get['description'];
            }
            else{
                $description = '';
            }
            if($this->common_setting['display_sub_category']){
                $sub_category = true;
            }
            else{
                $sub_category = false;
            }
            if(isset($this->request->get['quantity_status'])){
                $quantity_status = $this->request->get['quantity_status'];
            }
            else{
                $quantity_status = false;
            }
            
            if($route == 'product/special'){
                $special = true;
            }
            else{
                $special = false;
            }
            
            $data = array(
                'filter_category_id' => $categoryId,
                'filter_name' => $search,
                'filter_tag'  => $tag,
                'filter_description'  => $description,
                'filter_sub_category' => $sub_category,
                'filter_manufacturer_id' => $manufacturer_id,
                'filter_special' => $special
                );
            
            $this->request->get['ajax'] = 'ajax';
            
            $json['success'] = 'success';
            
            $this->{'model_extension_module_'.$this->codename}->prepareTable($data, true);
            
            $json['quantity'] = $this->{'model_extension_module_'.$this->codename}->getQuantity($status);
            
        }
        else{
            $json['error'] = 'error';
        }
        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }
    
    
    public function ajax(){

        if(isset($this->request->get['curRoute'])){
            $route = $this->request->get['curRoute'];
        }
        
        if(isset($this->request->post['status'])){
            $status = $this->request->post['status'];
        }
        
        $json = array();
        if(isset($route)&& !empty($status)){
            if(isset($this->request->get['path'])){
                $parts = explode('_', (string)$this->request->get['path']);
                $categoryId = array_pop($parts);
            }
            else{
                $categoryId = false;
            }
            if(isset($this->request->get['search'])){
                $search = $this->request->get['search'];
            }
            else{
                $search = '';
            }
            if(isset($this->request->get['path'])){
                $path = $this->request->get['path'];
            }
            else{
                $path = '';
            }
            if(isset($this->request->get['tag'])){
                $tag = $this->request->get['tag'];
            }
            else{
                $tag = $search;
            }
            if(isset($this->request->get['manufacturer_id'])){
                $manufacturer_id = $this->request->get['manufacturer_id'];
            }
            else{
                $manufacturer_id = 0;
            }
            if(isset($this->request->get['description'])){
                $description = $this->request->get['description'];
            }
            else{
                $description = '';
            }
            if($this->common_setting['display_sub_category']){
                $sub_category = true;
            }
            else{
                $sub_category = false;
            }
            if(isset($this->request->post['quantity_status'])){
                $quantity_status = $this->request->post['quantity_status'];
            }
            else{
                $quantity_status = false;
            }
            
            if($route == 'product/special'){
                $special = true;
            }
            else{
                $special = false;
            }
            
            $data = array(
                'filter_category_id' => $categoryId,
                'filter_name' => $search,
                'filter_tag'  => $tag,
                'filter_description'  => $description,
                'filter_sub_category' => $sub_category,
                'filter_manufacturer_id' => $manufacturer_id,
                'filter_special' => $special
                );
            
            $this->request->get['ajax'] = 'ajax';
            
            $json['success'] = 'success';
            $json['get'] = $this->request->get;
            $json['url'] = $this->{'model_extension_module_'.$this->codename}->getUrl($route);

            $this->config->set('config_product_count', false);
            
            $this->{'model_extension_module_'.$this->codename}->prepareTable($data, true);
            
            if($this->theme == 'BurnEngine'){
                $json['products'] = $this->{'model_extension_module_'.$this->codename}->get_data($json['url']);
            }
            else{
                $this->load->controller($route,$data);
                $json['products'] = $this->response->getOutput();
            }
            
            $json['quantity'] = array();

            if($quantity_status){
                $json['quantity'] = $this->{'model_extension_module_'.$this->codename}->getQuantity($status);
            }
            
        }
        else{
            $json['error'] = 'error';
        }
        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }
}
