<?php

/*
*  location: admin/controller
*/

class ControllerExtensionDAjaxFilterCache extends Controller
{
    private $codename = 'd_ajax_filter';
    private $route = 'extension/d_ajax_filter/cache';
    private $extension = array();
    private $config_file = '';
    private $store_id = 0;
    private $error = array();
    
    public function __construct($registry)
    {
        parent::__construct($registry);
        $this->load->model($this->route);
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
        $this->load->model('extension/d_opencart_patch/load');
        $this->load->model('extension/d_opencart_patch/user');

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

        if(isset($this->request->get['filter_option_mode']))

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
            'href' => $this->model_extension_d_opencart_patch_url->link($this->route)
            );

        $this->document->setTitle($this->language->get('heading_title_main'));
        $data['heading_title'] = $this->language->get('heading_title_main');
        $data['text_edit'] = $this->language->get('text_edit');
        $data['text_list'] = $this->language->get('text_list');
        $data['text_sort_values'] = $this->language->get('text_sort_values');
        $data['text_image'] = $this->language->get('text_image');
        $data['text_form'] = $this->language->get('text_form');
        $data['text_installation_progress'] = $this->language->get('text_installation_progress');

        $data['button_cancel'] = $this->language->get('button_cancel');

        // Variable
        $data['id'] = $this->codename;
        $data['route'] = $this->route;
        $data['store_id'] = $this->store_id;
        $data['extension'] = $this->extension;
        $data['config'] = $this->config_file;

        $data['version'] = $this->extension['version'];
        $data['token'] = $this->model_extension_d_opencart_patch_user->getToken();

        $url = '';

        if(isset($this->request->get['module_id'])){
            $url .= '&module_id='.$this->request->get['module_id'];
        }

        $data['create_cache'] = str_replace('&amp;', '&', $this->model_extension_d_opencart_patch_url->link('extension/'.$this->codename.'/cache/createCache'));

        $data['create_complete'] =  str_replace('&amp;', '&', $this->model_extension_d_opencart_patch_url->link('extension/module/'.$this->codename, $url));

        $data['cancel'] = $this->model_extension_d_opencart_patch_url->link('marketplace/extension', 'type=module');

        $this->{'model_extension_'.$this->codename.'_cache'}->checkCache();

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');
        $this->response->setOutput($this->model_extension_d_opencart_patch_load->view($this->route, $data));
    }

    public function createCache(){

        $json = $this->{'model_extension_'.$this->codename.'_cache'}->createCache();

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    public function model_updateProduct(&$route, &$data, &$output){
        if(!empty($output)){
            $json = $this->{'model_extension_'.$this->codename.'_cache'}->updateProduct($output);
        }
        else{
            $json = $this->{'model_extension_'.$this->codename.'_cache'}->updateProduct($data[0]);
        }
    }
}
