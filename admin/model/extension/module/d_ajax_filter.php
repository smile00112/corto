<?php
/*
*  location: admin/model
*/

class ModelExtensionModuleDAjaxFilter extends Model {

    public $codename = 'd_ajax_filter';

    public function CreateDatabase(){

        $this->db->query("CREATE TABLE IF NOT EXISTS `".DB_PREFIX."af_translit` (
           `type` VARCHAR(64) NOT NULL,
           `group_id` INT(11) NOT NULL,
           `value` INT(11) NOT NULL,
           `language_id` INT(11) NOT NULL,
           `text` VARCHAR(256) NOT NULL
           )
           COLLATE='utf8_general_ci' ENGINE=InnoDB;");
    }

    public function DropDatabase(){
        $this->db->query("DROP TABLE IF EXISTS ". DB_PREFIX . "af_translit");
        $this->load->model('extension/'.$this->codename.'/cache');
        $this->{'model_extension_'.$this->codename.'_cache'}->disableCache();
    }
    
    public function checkCache($redirect = true){
        $this->load->model('setting/setting');
        $setting = $this->model_setting_setting->getSetting($this->codename.'_cache');

        if(!empty($setting)){

            $this->load->model('extension/'.$this->codename.'/cache');

            $steps = $this->{'model_extension_'.$this->codename.'_cache'}->getModulesForCache();

            $steps[] = 'product';
            
            if(!empty($setting[$this->codename.'_cache']['steps']) && !$redirect){

                $results = array_diff($steps, $setting[$this->codename.'_cache']['steps']);
                if(!empty($results)){
                    return false;
                }
                else{
                    return true;
                }
            }
            else{
                return true;
            }
            
        }
        else{
            return false;
        }
    }
    
    public function clearFileCache(){
        $this->cache->delete('af-attribute');
        $this->cache->delete('af-price');
        $this->cache->delete('af-total-attribute');
        $this->cache->delete('af-total-category');
        $this->cache->delete('af-total-ean');
        $this->cache->delete('af-total-manufacturer');
        $this->cache->delete('af-total-option');
        $this->cache->delete('af-total-rating');
        $this->cache->delete('af-total-stock-status');
        $this->cache->delete('af-total-tag');
    }

    public function getTabs($active){
        $dir = DIR_APPLICATION.'controller/extension/'.$this->codename.'/*.php';
        $files = glob($dir);
        $result = array('layout');
        foreach($files as $file){
            $name = basename($file, '.php');
            if(!in_array($name, array('setting', 'layout', 'cache'))){
                $result[] = $name;
            }
        }
        $result[] = 'setting';
        return $this->prepareTabs($result, $active);
    }

    public function prepareTabs($tabs, $active){

        $this->load->model('extension/d_opencart_patch/url');
        $this->load->model('extension/d_opencart_patch/load');

        $this->load->language('extension/module/'.$this->codename);
        $data['tabs'] = array();
        $icons =array('setting'=> 'fa fa-cog', 'layout' => 'fa fa-file');
        $url = '';
        if(isset($this->request->get['module_id'])){
            $url .="&module_id=".$this->request->get['module_id'];
        }
        foreach ($tabs as $tab) {
            $this->load->language('extension/'.$this->codename.'/'.$tab);

            $module_setting = $this->getModuleSetting($tab);
            if(isset($icons[$tab])){
                $icon = 'fa fa-cog';
            }elseif(isset($module_setting['icon'])){
                $icon = $module_setting['icon'];
            }
            else{
                $icon = 'fa fa-list';
            }

            $data['tabs'][] = array(
                'title' => $this->language->get('text_title'),
                'active' => ($tab == $active)?true:false,
                'icon' => $icon,
                'href' => $this->model_extension_d_opencart_patch_url->link('extension/'.$this->codename.'/'.$tab, $url)
                );
        }

        $data['status_cache'] = $this->checkCache(false);

        $data['help_cache_support'] = $this->language->get('help_cache_support');
        $data['install_cache'] = $this->language->get('install_cache');
        $data['text_install_cache'] = $this->language->get('text_install_cache');

        $data['text_complete_version'] = $this->language->get('text_complete_version');

        $this->load->model('extension/'.$this->codename.'/layout');
        $data['notify'] = $this->{'model_extension_'.$this->codename.'_layout'}->checkCompleteVersion();

        $data['install_cache'] = $this->model_extension_d_opencart_patch_url->link('extension/'.$this->codename.'/cache');

        return $this->model_extension_d_opencart_patch_load->view('extension/'.$this->codename.'/partials/tabs', $data);
    }

    public function getModuleSetting($type){
        $results = array();

        $file = DIR_CONFIG.$this->codename.'/'.$type.'.php';
        
        if (file_exists($file)) {
            $_ = array();

            require($file);

            $results = array_merge($results, $_);
        }

        return $results;
    }

    public function getStores(){
        $this->load->model('setting/store');
        $stores = $this->model_setting_store->getStores();
        $result = array();
        if($stores){
            $result[] = array(
                'store_id' => 0,
                'name' => $this->config->get('config_name')
                );
            foreach ($stores as $store) {
                $result[] = array(
                    'store_id' => $store['store_id'],
                    'name' => $store['name']
                    );
            }
        }
        return $result;
    }

    public function getGroupId(){
        if(VERSION >= '2.0.0.0'){
            $user_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "user WHERE user_id = '" . $this->user->getId() . "'");
            $user_group_id = (int)$user_query->row['user_group_id'];
        }else{
            $user_group_id = $this->user->getGroupId();
        }

        return $user_group_id;
    }
}
?>
