<?php
/*
*  location: admin/model
*/

class ModelExtensionDAjaxFilterCache extends Model {

    private $codename = 'd_ajax_filter';

    private $setting = array();

    private $separator = '';

    public function __construct($registry)
    {
        parent::__construct($registry);

        $this->load->model('setting/setting');

        $setting = $this->model_setting_setting->getSetting($this->codename);

        if(!empty($setting[$this->codename.'_setting'])){
            $this->setting = $setting[$this->codename.'_setting'];
        }
        else{
            $this->config->load('d_ajax_filter');
            $setting = $this->config->get('d_ajax_filter_setting');

            $this->setting = $setting['general'];
        }

        if($this->setting['multiple_attributes_value'] && !empty($this->setting['separator'])){
            $this->separator = $this->setting['separator'];
        }

    }
    public function checkCache(){

        $modules = $this->getModulesForCache();

        foreach ($modules as $type) {
            $this->load->controller('extension/'.$this->codename.'_module/'.$type.'/install');
        }

        $this->db->query("CREATE TABLE IF NOT EXISTS " . DB_PREFIX . "af_values (
            `af_value_id` INT(11) NOT NULL AUTO_INCREMENT,
            `type` VARCHAR(64) NOT NULL,
            `group_id` INT(11) NOT NULL,
            `value` INT(11) NOT NULL,
            PRIMARY KEY (`af_value_id`),
            UNIQUE INDEX `type` (`type`, `group_id`, `value`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");

        if(!$this->checkColumn(DB_PREFIX."product","af_values")) {
            $this->db->query("ALTER TABLE ".DB_PREFIX."product ADD COLUMN af_values TEXT");
            $this->db->query("ALTER TABLE ".DB_PREFIX."product ADD FULLTEXT INDEX `af_values` (`af_values`)");
        }

        if(!$this->checkColumn(DB_PREFIX."product","af_tags")) {
            $this->db->query("ALTER TABLE ".DB_PREFIX."product ADD COLUMN af_tags TEXT");
            $this->db->query("ALTER TABLE ".DB_PREFIX."product ADD FULLTEXT INDEX `af_tags` (`af_tags`)");
        }
    }

    public function disableCache(){
        $modules = $this->getModulesForCache();

        foreach ($modules as $type) {
            $this->load->controller('extension/'.$this->codename.'_module/'.$type.'/uninstall');
        }

        $this->db->query("DROP TABLE IF EXISTS ". DB_PREFIX . "af_values");
        $this->db->query("DROP TABLE IF EXISTS ". DB_PREFIX . "af_values");

        if($this->checkColumn(DB_PREFIX."product","af_values")) {
            $this->db->query("ALTER TABLE ".DB_PREFIX."product DROP COLUMN af_values");
        }

        if($this->checkColumn(DB_PREFIX."product","af_tags")) {
            $this->db->query("ALTER TABLE ".DB_PREFIX."product DROP COLUMN af_tags");
        }
    }

    public function getModulesForCache(){
        $results = array();
        $this->load->model('extension/'.$this->codename.'/layout');
        $modules = $this->{'model_extension_'.$this->codename.'_layout'}->getModules();
        foreach ($modules as $type) {
            $module_setting = $this->{'model_extension_'.$this->codename.'_layout'}->getModuleSetting($type);
            if(!empty($module_setting['prepare'])){
                $results[] = 'prepare.'.$type;
            }
            if(!empty($module_setting['restore_after_cache'])){
                $results[] = 'save.'.$type;
            }
            if(!empty($module_setting['cleaning_before'])){
                $results[] = 'cleaning_before.'.$type;
            }
            if(!empty($module_setting['cache'])){
                $results[] = $type;
            }
            if(!empty($module_setting['restore_after_cache'])){
                $results[] = 'restore.'.$type;
            }
        }

        return $results;
    }

    public function createCache(){
        $steps = $this->getModulesForCache();

        $steps[] = 'product';

        $cache = 'af_create_cache';

        if(file_exists($cache)){
            $this->session->data['af_create_cache_progress'] = $this->cache->get($cache);
        }

        if(!isset($this->session->data['af_create_cache_progress'])){

            $this->session->data['af_create_cache_progress'] = array(
                'step' => 0,
                'last_step' => 0
                );
            $this->db->query('TRUNCATE TABLE '.DB_PREFIX.'af_values');
            $this->db->query( "UPDATE `" . DB_PREFIX . "product` SET `af_values` = ''");
            $this->db->query( "UPDATE `" . DB_PREFIX . "product` SET `af_tags` = ''");
        }

        $limit=100;
        $step = $this->session->data['af_create_cache_progress']['step'];
        $last_step = $this->session->data['af_create_cache_progress']['last_step'];
        $count = 0;

        if($steps[$step] != 'product'){
            $filter_data = array(
                'limit' => $limit,
                'last_step' => $last_step
                );
            if(strpos($steps[$step],'prepare.') == 0){
                $type = str_replace('prepare.', '', $steps[$step]);
                $count = $this->load->controller('extension/'.$this->codename.'_module/'.$steps[$step].'/prepare', $filter_data);
            }
            else if(strpos($steps[$step],'cleaning_before.') == 0){
                $type = str_replace('cleaning_before.', '', $steps[$step]);
                $count = $this->load->controller('extension/'.$this->codename.'_module/'.$steps[$step].'/cleaning_before', $filter_data);
            }
            else if(strpos($steps[$step],'save.') == 0){
                $type = str_replace('save.', '', $steps[$step]);
                $count = $this->load->controller('extension/'.$this->codename.'_module/'.$steps[$step].'/save', $filter_data);
            }
            elseif(strpos($steps[$step],'restore.') == 0) {
                $type = str_replace('restore.', '', $steps[$step]);
                $count = $this->load->controller('extension/'.$this->codename.'_module/'.$steps[$step].'/restore', $filter_data);
            }
            else{
                $count = $this->load->controller('extension/'.$this->codename.'_module/'.$steps[$step].'/step', $filter_data);
            }
            $last_step++;
        }
        else{
            $count = $this->db->query("SELECT COUNT(*) AS `c` FROM `" . DB_PREFIX . "product`");
            $count = $count->row['c'];
            $limit = 10;
            $products = $this->db->query( "SELECT * FROM `" . DB_PREFIX . "product` LIMIT " . ( $limit * $last_step ) . ', ' . $limit );
            foreach ($products->rows as $product) {
                $this->updateProduct($product['product_id']);
            }
            $last_step++;
        }

        $progress = $count ? round( $last_step * $limit / $count * 100, 3 ) : 100;

        if( $progress >= 100 ) {
            $step++;
            $last_step = 0;
            $progress = 0;
        }

        $return = array(
            'steps'     => count( $steps ),
            'progress'  => $progress > 100 ? 100 : $progress,
            'last_step' => $last_step,
            'step'      => $step + 1
            );

        if( $step >= count( $steps ) ) {
            unset( $this->session->data['af_create_cache_progress'] );

            if( file_exists( $cache ) ) {
                unlink( $cache );
            }

            foreach($steps as $type){
                if($type != 'product'){
                    $this->load->controller('extension/'.$this->codename.'_module/'.$type.'/cleaning');
                }
            }

            $this->load->model('setting/setting');

            $this->model_setting_setting->editSetting($this->codename.'_cache', array($this->codename.'_cache' => array('status' => true, 'steps' => $steps)));
            $return['step'] = $return['steps'];
            $return['success'] = true;
        } else {
            $this->session->data['af_create_cache_progress']['last_step'] = $last_step;
            $this->session->data['af_create_cache_progress']['step'] = $step;

            $this->cache->set($cache, $this->session->data['af_create_cache_progress']);
        }

        return $return;
    }

    public function updateProduct($product_id){

        $steps = $this->getModulesForCache();

        $new_values = array();
        $new_tags = array();

        foreach ($steps as $step) {

            $output = $this->load->controller('extension/'.$this->codename.'_module/'.$step.'/updateProduct', $product_id);
            if(!empty($output)) {
                if($step != 'tag') {
                    $new_values = array_merge($new_values, $output);
                }
                else{
                    $new_tags = array_merge($new_tags, $output);
                }
            }
        }

        $product = $this->db->query("SELECT * FROM `" . DB_PREFIX . "product` WHERE `product_id` = " . (int) $product_id )->row;

        $old_values = array();
        $old_tags = array();
        if(!empty($product['af_values'])){
            $old_values = explode( ',', $product['af_values'] );
        }
        if(!empty($product['af_tags'])){
            $old_tags = explode( ',', $product['af_tags'] );
        }

        $this->db->query(sprintf("UPDATE `".DB_PREFIX."product` SET `af_values` = '%s' WHERE `product_id` = %s", implode( ',', array_unique( $new_values )), $product_id));

        $this->db->query(sprintf("UPDATE `" . DB_PREFIX . "product` SET `af_tags` = '%s' WHERE `product_id` = %s", implode( ',', array_unique( $new_tags )), $product_id));


        $diffValues = array_diff( $old_values, $new_values );
        $diffTags = array_diff( $old_tags, $new_tags );

        if( $diffValues ) {
            foreach( $diffValues as $value ) {
                $values = $this->db->query(sprintf("SELECT * FROM `" . DB_PREFIX . "product` WHERE FIND_IN_SET( '%s', `af_values` ) LIMIT 1", $value));
                if(!$values->num_rows) {
                    $this->deleteValue(array($value));
                }
            }
        }

        if( $diffTags ) {
            foreach( $diffTags as $tag_id ) {
                $tags = $this->db->query(sprintf("SELECT * FROM `" . DB_PREFIX . "product` WHERE FIND_IN_SET( '%s', `af_tags` ) LIMIT 1", $tag_id));
                if( !$tags->num_rows ) {
                    $this->deleteTag(array($tag_id));
                }
            }
        }
    }

    public function deleteValue($values){
        if($values){
            $this->db->query(sprintf("DELETE FROM `" . DB_PREFIX . "af_values` WHERE `af_value_id` IN(%s)", implode( ',', $values)));
        }
    }

    public function deleteTag($tags){
        if($tags){
            $this->db->query(sprintf("DELETE FROM `" . DB_PREFIX . "af_tag` WHERE `tag_id` IN(%s)", implode( ',', $tags)));
        }
    }

    public function checkColumn($table_name, $column_name){
        $query = $this->db->query("SHOW COLUMNS FROM ".$table_name." LIKE '".$column_name."'");
        if($query->num_rows){
            return true;
        }
        else{
            return false;
        }
    }

    public function addValue($type, $group_id, $value){
        $this->db->query(sprintf("INSERT INTO `".DB_PREFIX."af_values`  SET `type` = '%s', `group_id` = '%s', `value` = '%s'", $type, (int)$group_id, (int)$value));
        $value_id = $this->db->getLastId();
        return $value_id;
    }
}