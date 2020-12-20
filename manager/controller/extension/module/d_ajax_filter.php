<?php

/*
*  location: admin/controller
*/

class ControllerExtensionModuleDAjaxFilter extends Controller
{
    private $codename = 'd_ajax_filter';
    private $route = 'extension/module/d_ajax_filter';
    private $extension = array();
    private $config_file = '';
    private $store_id = 0;
    private $error = array();
    
    public function __construct($registry)
    {
        parent::__construct($registry);
        $this->load->model($this->route);
        $this->load->Language($this->route);
        
        $this->d_shopunity = (file_exists(DIR_SYSTEM.'library/d_shopunity/extension/d_shopunity.json'));
        $this->d_opencart_patch = (file_exists(DIR_SYSTEM.'library/d_shopunity/extension/d_opencart_patch.json'));
        $this->d_twig_manager = (file_exists(DIR_SYSTEM.'library/d_shopunity/extension/d_twig_manager.json'));
        $this->d_event_manager = (file_exists(DIR_SYSTEM.'library/d_shopunity/extension/d_event_manager.json'));
    }
    
    public function index()
    {

        $this->load->model('extension/d_opencart_patch/url');

        if($this->d_twig_manager){
            $this->load->model('extension/module/d_twig_manager');
            $this->model_extension_module_d_twig_manager->installCompatibility();
        }
        
        if ($this->d_event_manager) {
            $this->load->model('extension/module/d_event_manager');
            $this->model_extension_module_d_event_manager->installCompatibility();
        }

        if($this->d_shopunity){
            $this->load->model('extension/d_shopunity/mbooth');
            $this->model_extension_d_shopunity_mbooth->validateDependencies($this->codename);
        }

        $this->load->model('extension/'.$this->codename.'/cache');

        $this->{'model_extension_'.$this->codename.'_cache'}->checkCache();

        $cache_status = $this->{'model_extension_module_'.$this->codename}->checkCache();

        if($cache_status){
            $this->load->controller('extension/'.$this->codename.'/layout');
        }
        else{
            $this->response->redirect($this->model_extension_d_opencart_patch_url->link('extension/'.$this->codename.'/cache'));
        }
    }

    public function getFileManager() {
        $this->load->model('user/user_group');
        $this->load->model('extension/d_opencart_patch/user');
        $this->load->model('extension/d_opencart_patch/load');
        $this->model_user_user_group->addPermission($this->model_extension_d_opencart_patch_user->getGroupId(), 'access', 'extension/d_elfinder');
        $this->model_user_user_group->addPermission($this->model_extension_d_opencart_patch_user->getGroupId(), 'modify', 'extension/d_elfinder');

        if (isset($this->request->server['HTTPS']) && (($this->request->server['HTTPS'] == 'on') || ($this->request->server['HTTPS'] == '1'))) {
            $data['base'] = HTTPS_CATALOG;
        } else {
            $data['base'] = HTTP_CATALOG;
        }

        $data['token'] = $this->model_extension_d_opencart_patch_user->getUrlToken();
        $data['route'] = 'extension/d_elfinder/d_elfinder';

        $this->response->setOutput($this->model_extension_d_opencart_patch_load->view('extension/d_elfinder/d_elfinder', $data));
    }

    public function getImage() {
        $this->load->model('tool/image');

        if (isset($this->request->get['image'])) {
            $this->response->setOutput($this->model_tool_image->resize(html_entity_decode($this->request->get['image'], ENT_QUOTES, 'UTF-8'), 100, 100));
        }
    }

    public function install()
    {
        if($this->d_shopunity){
            $this->load->model('extension/d_shopunity/mbooth');
            $this->model_extension_d_shopunity_mbooth->installDependencies($this->codename);
        }
        
        $this->load->model('user/user_group');
        $this->model_user_user_group->addPermission($this->{'model_extension_module_'.$this->codename}->getGroupId(), 'access', 'extension/'.$this->codename);
        $this->model_user_user_group->addPermission($this->{'model_extension_module_'.$this->codename}->getGroupId(), 'access', 'extension/'.$this->codename.'/attribute');
        $this->model_user_user_group->addPermission($this->{'model_extension_module_'.$this->codename}->getGroupId(), 'access', 'extension/'.$this->codename.'/cache');
        $this->model_user_user_group->addPermission($this->{'model_extension_module_'.$this->codename}->getGroupId(), 'access', 'extension/'.$this->codename.'/filter');
        $this->model_user_user_group->addPermission($this->{'model_extension_module_'.$this->codename}->getGroupId(), 'access', 'extension/'.$this->codename.'/layout');
        $this->model_user_user_group->addPermission($this->{'model_extension_module_'.$this->codename}->getGroupId(), 'access', 'extension/'.$this->codename.'/option');
        $this->model_user_user_group->addPermission($this->{'model_extension_module_'.$this->codename}->getGroupId(), 'access', 'extension/'.$this->codename.'/setting');

        $this->model_user_user_group->addPermission($this->{'model_extension_module_'.$this->codename}->getGroupId(), 'modify', 'extension/'.$this->codename);
        $this->model_user_user_group->addPermission($this->{'model_extension_module_'.$this->codename}->getGroupId(), 'modify', 'extension/'.$this->codename.'/attribute');
        $this->model_user_user_group->addPermission($this->{'model_extension_module_'.$this->codename}->getGroupId(), 'modify', 'extension/'.$this->codename.'/cache');
        $this->model_user_user_group->addPermission($this->{'model_extension_module_'.$this->codename}->getGroupId(), 'modify', 'extension/'.$this->codename.'/filter');
        $this->model_user_user_group->addPermission($this->{'model_extension_module_'.$this->codename}->getGroupId(), 'modify', 'extension/'.$this->codename.'/layout');
        $this->model_user_user_group->addPermission($this->{'model_extension_module_'.$this->codename}->getGroupId(), 'modify', 'extension/'.$this->codename.'/option');
        $this->model_user_user_group->addPermission($this->{'model_extension_module_'.$this->codename}->getGroupId(), 'modify', 'extension/'.$this->codename.'/setting');
    }
}