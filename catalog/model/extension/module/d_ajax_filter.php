<?php

/*
* location: admin/model
*/

class ModelExtensionModuleDAjaxFilter extends Model
{
    private $codename="d_ajax_filter";

    private $common_setting;

    private static $tmp_table_status=0;

    public function __construct($registry)
    {
        parent::__construct($registry);

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
    }
    
    public function prepareTable($data){

        if(!$this->tmp_table_status){
            $this->db->query("CREATE TEMPORARY TABLE IF NOT EXISTS `".DB_PREFIX."af_tax_fixed` (PRIMARY KEY (`tax_class_id`)) AS (".$this->getTax('F').")");

            $this->db->query("CREATE TEMPORARY TABLE IF NOT EXISTS `".DB_PREFIX."af_tax_percent` (PRIMARY KEY (`tax_class_id`)) AS (".$this->getTax('P').")");

            $sql = "CREATE TEMPORARY TABLE IF NOT EXISTS `".DB_PREFIX."af_temporary` (PRIMARY KEY (`product_id`)) ";

            $params = $this->getParamsToArray();


            $sql .= "SELECT p.product_id, p.manufacturer_id, IF(p.quantity > 0, ".$this->common_setting['in_stock_status'].", p.stock_status_id) as stock_status_id, MIN(pd2.price) as discount, MIN(ps.price) as special, tax_p.sum_rate as tax_precent, tax_f.sum_rate as tax_fixed, AVG(rating) AS rating FROM " . DB_PREFIX . "product p LEFT JOIN `".DB_PREFIX."af_tax_fixed` tax_f ON p.tax_class_id = tax_f.tax_class_id LEFT JOIN `".DB_PREFIX."af_tax_percent` tax_p ON p.tax_class_id = tax_p.tax_class_id ";

            if (!empty($data['filter_category_id'])) {
                if (!empty($data['filter_sub_category'])) {
                    $sql .= " INNER JOIN " . DB_PREFIX . "product_to_category p2c ON (p.product_id = p2c.product_id) INNER JOIN " . DB_PREFIX . "category_path cp ON (cp.category_id = p2c.category_id)";
                } else {
                    $sql .= " INNER JOIN " . DB_PREFIX . "product_to_category p2c ON (p.product_id = p2c.product_id)";
                }
            }

            if (!empty($data['filter_name']) || !empty($data['filter_tag'])) {
                $sql .= " LEFT JOIN (SELECT pd2.product_id, pd2.language_id, ";
                $implode = array();
                if(!empty($data['filter_name'])){
                    $implode[] = " pd2.name, pd2.description ";
                }
                if(!empty($data['filter_tag'])){
                    $implode[] = "pd2.tag";
                }
                if(count($implode) > 0){
                    $sql .= implode(' , ', $implode);
                }
                $sql .=" FROM `" . DB_PREFIX . "product_description` pd2) pd ON (p.product_id = pd.product_id)";
            }
            
            $sql .= " LEFT JOIN ".DB_PREFIX."product_discount pd2 ON (pd2.product_id = p.product_id AND pd2.quantity = '1' AND (pd2.date_start = '0000-00-00' OR pd2.date_start < NOW()) AND (pd2.date_end = '0000-00-00' OR pd2.date_end > NOW()) AND pd2.customer_group_id = '".(int)$this->getCustomerGroupId()."')";
            $sql .= " LEFT JOIN ".DB_PREFIX."product_special ps ON (ps.product_id = p.product_id AND (ps.date_end = '0000-00-00' OR ps.date_end > NOW()) AND (ps.date_start = '0000-00-00' OR ps.date_start < NOW()) AND ps.customer_group_id = '".(int)$this->getCustomerGroupId()."')";

            $sql .= " LEFT JOIN ".DB_PREFIX."review r1 ON (r1.product_id = p.product_id AND r1.status = 1)";

            $sql .= " INNER JOIN ".DB_PREFIX."product_to_store AS p2s ON
            p2s.product_id = p.product_id AND p2s.store_id = '" . (int)$this->config->get('config_store_id') . "' ";

            $sql .= " WHERE p.date_available <= NOW() AND p.status = 1 ";
            if (!empty($data['filter_name']) || !empty($data['filter_tag']) || isset($params['keyword'])) {
                $sql .= " AND pd.language_id = '".(int)$this->config->get('config_language_id')."' ";
            }
            if(empty($this->common_setting['display_out_of_stock']))
            {
                $sql .= " AND p.quantity > 0";
            }
            $query_data = array();

            if (!empty($data['filter_category_id'])) {
                if (!empty($data['filter_sub_category'])) {
                    $sql .= " AND cp.path_id = '" . (int)$data['filter_category_id'] . "' ";
                } else {
                    $sql .= " AND p2c.category_id = '" . (int)$data['filter_category_id'] . "' ";
                }
            }

            if(!empty($data['filter_special'])){
                $sql .= " AND p.product_id IN (SELECT ps.product_id FROM ".DB_PREFIX."product_special ps WHERE 
                ps.customer_group_id = '" . (int)$this->config->get('config_customer_group_id') . "' 
                AND ((ps.date_start = '0000-00-00' OR ps.date_start < NOW()) AND (ps.date_end = '0000-00-00' OR ps.date_end > NOW()))
                )";
            }

            if(!empty($data['filter_manufacturer_id'])){
                $sql .= " AND p.manufacturer_id = '".$data['filter_manufacturer_id']."'";
            }
            if (!empty($data['filter_name']) || !empty($data['filter_tag'])) {
                $sql .= " AND (";

                if (!empty($data['filter_name'])) {
                    $implode = array();

                    $words = explode(' ', trim(preg_replace('/\s+/', ' ', $data['filter_name'])));

                    foreach ($words as $word) {
                        if(!empty($this->request->get['route'])){
                            if($this->request->get['route'] == 'product/isearch'){
                                $implode[] = "LOWER(pd.name) LIKE '%" . $this->db->escape(utf8_strtolower($word)) . "%'";
                            }elseif($this->request->get['route'] == 'modul/d_ajax_filter/ajax' && $this->request->get['curRoute'] == 'product/iSearch'){
                                $implode[] = "LOWER(pd.name) LIKE '%" . $this->db->escape(utf8_strtolower($word)) . "%'";
                            }
                            else{
                                $implode[] = "pd.name LIKE '%" . $this->db->escape($word) . "%'";
                            }
                        }
                        else{
                            $implode[] = "pd.name LIKE '%" . $this->db->escape($word) . "%'";
                        }
                    }
                    if ($implode) {
                        $sql .= " " . implode(" AND ", $implode) . "";
                    }
                    if (!empty($data['filter_description'])) {
                        if(!empty($this->request->get['route'])){
                            if($this->request->get['route'] == 'product/isearch'){
                                $sql .= " OR LOWER(pd.description) LIKE '%" . $this->db->escape(utf8_strtolower($data['filter_name'])) . "%'";
                            }elseif($this->request->get['route'] == 'modul/d_ajax_filter/ajax' && $this->request->get['curRoute'] == 'product/iSearch'){
                                $sql .= " OR LOWER(pd.description) LIKE '%" . $this->db->escape(utf8_strtolower($data['filter_name'])) . "%'";
                            }
                            else{
                                $sql .= " OR pd.description LIKE '%" . $this->db->escape($data['filter_name']) . "%'";
                            }
                        }
                        else{
                            $sql .= " OR pd.description LIKE '%" . $this->db->escape($data['filter_name']) . "%'";
                        }
                    }

                }

                if (!empty($data['filter_name']) && !empty($data['filter_tag'])) {
                    $sql .= " OR ";
                }

                if (!empty($data['filter_tag'])) {
                    $sql .= "pd.tag LIKE '%" . $this->db->escape(utf8_strtolower($data['filter_tag'])) . "%'";
                }

                if (!empty($data['filter_name'])) {
                    $sql .= " OR LCASE(p.model) = '" . $this->db->escape(utf8_strtolower($data['filter_name'])) . "'";
                    $sql .= " OR LCASE(p.sku) = '" . $this->db->escape(utf8_strtolower($data['filter_name'])) . "'";
                    $sql .= " OR LCASE(p.upc) = '" . $this->db->escape(utf8_strtolower($data['filter_name'])) . "'";
                    $sql .= " OR LCASE(p.ean) = '" . $this->db->escape(utf8_strtolower($data['filter_name'])) . "'";
                    $sql .= " OR LCASE(p.jan) = '" . $this->db->escape(utf8_strtolower($data['filter_name'])) . "'";
                    $sql .= " OR LCASE(p.isbn) = '" . $this->db->escape(utf8_strtolower($data['filter_name'])) . "'";
                    $sql .= " OR LCASE(p.mpn) = '" . $this->db->escape(utf8_strtolower($data['filter_name'])) . "'";
                }

                $sql .= ")";
            }
            $sql .= " GROUP BY p.product_id";

            $this->db->query($sql);

            $this->db->query("DROP TEMPORARY TABLE IF EXISTS `".DB_PREFIX."af_tax_fixed`");
            $this->db->query("DROP TEMPORARY TABLE IF EXISTS `".DB_PREFIX."af_tax_percent`");
            $this->tmp_table_status = 1;
        }

    }

    public function getParamsQuery($params, $table_name = 'p'){
        $implode = array();

        foreach ($params as $type => $param) {
            if(file_exists(DIR_APPLICATION.'model/extension/'.$this->codename.'/'.$type.'.php')){

                $this->load->model('extension/'.$this->codename.'/'.$type);

                $result = $this->{'model_extension_'.$this->codename.'_'.$type}->getTotalQuery($param, $table_name);

                if(!empty($result)){
                    $implode[] = $result;
                }
            }
        }
        if(!empty($implode)){
            $sql = implode(' AND ', $implode);
        }
        else{
            $sql = "";
        }
        
        return $sql;
    }

    public function getTypes(){
        $dir = DIR_APPLICATION.'controller/extension/'.$this->codename.'/*.php';

        $files = glob($dir);

        $type_data = array();

        foreach($files as $file){

            $type_data[] = basename($file, '.php');

        }

        return $type_data;
    }

    public function getParamsToArray(){

        $result = array();

        $types= $this->getTypes();

        if(isset($this->request->get['ajaxfilter'])){
            $params = $this->request->get['ajaxfilter'];

            $hash = md5(json_encode($params));

            $result = $this->cache->get('af-url-params.' . $hash);

            if(!$result){
                foreach ($types as $type) {
                    if(file_exists(DIR_APPLICATION.'/controller/extension/d_ajax_filter/'.$type.'.php')){
                        $output = $this->load->controller('extension/'.$this->codename.'/'.$type.'/url', $params);
                        if(!empty($output)){
                            $result[$type] = $output;
                        }
                    }
                }
                $this->cache->set('af-url-params.' .$hash , $result);
            }
            
        }
        else{
            foreach ($types as $type) {
                if(isset($this->request->post[$type])){
                    $result[$type] = $this->request->post[$type];
                }
            }
        }

        return $result;
    }
    public function getUrlParams(){
        $result = array();
        
        $types= $this->getTypes();
        $params = $this->getParamsToArray();

        foreach ($params as $type => $param) {
            if(file_exists(DIR_APPLICATION.'/controller/extension/d_ajax_filter/'.$type.'.php')){
                $output = $this->load->controller('extension/'.$this->codename.'/'.$type.'/rewrite', $param);
                if(!empty($output)){
                    $result =  array_merge($result, $output);
                }
            }
        }

        if(!empty($result)){
            $result = 'ajaxfilter='.implode('/',$result);
        }
        else{
            $result = '';
        }

        return $result;
    }

    public function getTranslit($text, $type, $group_id){

        $sql = "SELECT * FROM `".DB_PREFIX."af_translit` WHERE `type` = '".$type."' AND `group_id` = '".$group_id."' AND `text`IN (".implode(',' , $text).") GROUP BY `value`";
        
        $hash = md5($sql);

        $result = $this->cache->get('af-translit.' . $hash);

        if(!$result){
            $query = $this->db->query($sql);
            $result = $query->rows;
            $this->cache->set('af-translit.' .$hash , $result);
        }
        $translit_data = array();
        
        if(!empty($result)){
            $translit_data = array_map(function($val){
                if(isset($val['value'])){ 
                    return $val['value']; 
                }
            }, $result);
        }

        return $translit_data;
    }

    public function setTranslit($text, $type, $group_id, $value){

        $text = $this->translit($text);

        $query = $this->db->query("SELECT * FROM `".DB_PREFIX."af_translit` WHERE 
            `type` = '".$type."' AND 
            `group_id` = '".(int)$group_id."' AND 
            `value` = '".(int)$value."' AND 
            `language_id` = '".(int)$this->config->get('config_language_id')."' AND 
            `text` = '".$this->db->escape($text)."'");

        if(!$query->num_rows){
            $this->db->query("DELETE FROM `".DB_PREFIX."af_translit` WHERE 
                `type` = '".$type."' AND 
                `group_id` = '".(int)$group_id."' AND 
                `value` = '".(int)$value."' AND 
                `language_id` = '".(int)$this->config->get('config_language_id')."'");
            $this->db->query("INSERT INTO `".DB_PREFIX."af_translit` SET 
                `type` = '".$type."', 
                `group_id` = '".$group_id."', 
                `value` = '".$value."', 
                `language_id` = '".(int)$this->config->get('config_language_id')."',
                `text` = '".$this->db->escape($text)."'");
        }

        return $text;
    }

    public function translit($text){

        $translit_data = $this->config->get('d_ajax_filter_translit');
        
        if(empty($translit_data)){
            $this->config->load('d_ajax_filter_translit');
            $translit_data = $this->config->get('d_ajax_filter_translit');
        }
        $text = strtr($text, $translit_data['translit_symbol']);
        $text = mb_strtolower( trim( preg_replace( '/-+/', '-', preg_replace( '/ +/', '-', $text ) ), '-' ), 'utf-8' );

        return $text;
    }

    public function getFitlerData(){
        $filter_data = array();

        if(!empty($this->request->get['curRoute'])){
            $route = $this->request->get['curRoute'];
        }
        elseif(isset($this->request->get['route'])){
            $route = $this->request->get['route'];
        }
        else{
            $route='common/home';
        }

        if ($route == 'product/category') {
            if (isset($this->request->get['path'])) {

                $path = '';

                $parts = explode('_', (string)$this->request->get['path']);

                $category_id = (int)array_pop($parts);

                foreach ($parts as $path_id) {
                    if (!$path) {
                        $path = (int)$path_id;
                    } else {
                        $path .= '_' . (int)$path_id;
                    }
                }
            } else {
                $category_id = 0;
            }
            $filter_data = array(
                'filter_category_id' => $category_id
                );
            if($this->common_setting['display_sub_category']){
                $filter_data['filter_sub_category'] = true;
            }
        }

        if ($route == 'product/special') {
            $filter_data = array(
                'filter_special' => true
                );
        }
        if ($route == 'product/manufacturer/info') {
            if (isset($this->request->get['manufacturer_id'])) {

                $manufacturer_id = $this->request->get['manufacturer_id'];
            } else {
                $manufacturer_id = '';
            }
            $filter_data = array(
                'filter_manufacturer_id' => $manufacturer_id
                );
        }

        if ($route == 'product/search') {
            if (isset($this->request->get['search'])) {

                $search = $this->request->get['search'];
            } else {
                $search = '';
            }
            if (isset($this->request->get['tag'])) {
                $tag = $this->request->get['tag'];
            } elseif (isset($this->request->get['search'])) {
                $tag = $this->request->get['search'];
            } else {
                $tag = '';
            }
            if (isset($this->request->get['description'])) {
                $description = $this->request->get['description'];
            } else {
                $description = '';
            }
            $filter_data = array(
                'filter_name' => $search,
                'filter_tag' => $tag,
                'filter_description' => $description
                );
        }

        if ($route == 'product/isearch') {
            if (isset($this->request->get['search'])) {

                $search = $this->request->get['search'];
            } else {
                $search = '';
            }

            if (isset($this->request->get['tag'])) {
                $tag = $this->request->get['tag'];
            } elseif (isset($this->request->get['search'])) {
                $tag = $this->request->get['search'];
            } else {
                $tag = '';
            }

            if (isset($this->request->get['description'])) {
                $description = $this->request->get['description'];
            } else {
                $description = '';
            }
            $filter_data = array(
                'filter_name' => $search,
                'filter_tag' => $tag,
                'filter_description' => $description
                );
        }

        return $filter_data;
    }


    public function getURLQuery(){
        $implode = array();
        if(isset($this->request->get['route'])){
            $implode[] = 'curRoute='.$this->request->get['route'];
        }
        if(isset($this->request->get['path'])){
            $implode[] = 'path='.$this->request->get['path'];
        }
        if(isset($this->request->get['search'])){
            $implode[] = 'search='.$this->request->get['search'];
        }
        if(isset($this->request->get['tag'])){
            $implode[] = 'tag='.$this->request->get['tag'];
        }
        if(isset($this->request->get['category_id'])){
            $implode[] = 'category_id='.$this->request->get['category_id'];
        }
        if(isset($this->request->get['manufacturer_id'])){
            $implode[] = 'manufacturer_id='.$this->request->get['manufacturer_id'];
        }
        if(isset($this->request->get['description'])){
            $implode[] = 'description='.$this->request->get['description'];
        }
        if(isset($this->request->get['sub_category'])){
            $implode[] = 'sub_category='.$this->request->get['sub_category'];
        }
        if(isset($this->request->get['limit'])){
            $implode[] = 'limit='.$this->request->get['limit'];
        }
        if(isset($this->request->get['filter'])){
            $implode[] = 'filter='.$this->request->get['filter'];
        }
        if(isset($this->request->get['sort'])){
            $implode[] = 'sort='.$this->request->get['sort'];
        }
        if(isset($this->request->get['order'])){
            $implode[] = 'order='.$this->request->get['order'];
        }

        if(count($implode) >0){
            $result = implode('&',$implode);
        }
        else{
            $result = '';
        }
        return $result;
    }

    public function getUrl($route){
        $query = array();
        if(isset($this->request->get['path'])){
            $query[] = 'path='.$this->request->get['path'];
        }
        if(isset($this->request->get['search'])){
            $query[] = 'search='.$this->request->get['search'];
        }
        if(isset($this->request->get['tag'])){
            $query[] = 'tag='.$this->request->get['tag'];
        }
        if(isset($this->request->get['category_id'])){
            $query[] = 'category_id='.$this->request->get['category_id'];
        }
        if(isset($this->request->get['manufacturer_id'])){
            $query[] = 'manufacturer_id='.$this->request->get['manufacturer_id'];
        }
        if(isset($this->request->get['description'])){
            $query[] = 'description='.$this->request->get['description'];
        }
        if(isset($this->request->get['sub_category'])){
            $query[] = 'sub_category='.$this->request->get['sub_category'];
        }
        if(isset($this->request->get['limit'])){
            $query[] = 'limit='.$this->request->get['limit'];
        }
        if(isset($this->request->get['filter'])){
            $query[] = 'filter='.$this->request->get['filter'];
        }
        if(isset($this->request->get['sort'])){
            $query[] = 'sort='.$this->request->get['sort'];
        }
        if(isset($this->request->get['order'])){
            $query[] = 'order='.$this->request->get['order'];
        }
        
        $params = $this->getUrlParams();

        if(is_array($params)){
            $query_params = array();
            if(!empty($params)){
                $query_params[] = $params;
            }
            $query_params = implode('&',$query_params);
        }
        else{
            if(!empty($params)){
                $query_params = '&'.$params;
            }
            else{
                $query_params = $params;
            }
            
        }
        $query = implode('&',$query);

        if(!empty($query)){
            $url = $this->url->link($route,$query.$query_params,'SSL');
        }
        else{
            $url =$this->url->link($route,$query_params,'SSL');
        } 

        $url = str_replace('&amp;', '&', $url);
        return $url;
    }

    public function convertResultTotal($data){
        $output = array();

        if (count($data)) {
            foreach ($data as $row) {
                if (!isset($output[$row['type']])) {
                    $output[$row['type']] = array();
                }
                if (!isset($output[$row['type']][$row['id']])) {
                    $output[$row['type']][$row['id']] = array();
                }
                $output[$row['type']][$row['id']][$row['val']] = $row['c'];
                
            }
        }
        return $output;
    }

    public function mergeTotal($in,$out){

        foreach ($out as $type => $groups) {
            foreach ($groups as $group_id => $values) {
                foreach ($values as $value => $total) {
                    if (!isset($in[$type])) {
                        $in[$type] = array();
                    }
                    if (!isset($in[$type][$group_id])) {
                        $in[$type][$group_id] = array();
                    }
                    $in[$type][$group_id][$value] = $total;
                }
            }
        }
        return $in;
    }

    public function getProductsQuery($price_status = false){
        $params = $this->getParamsToArray();
        $customerGroupId = ($this->customer->isLogged()) ? $this->customer->getGroupId() : $this->config->get('config_customer_group_id');
        $sql = "SELECT aft.*  ";
        if(isset($params['price']) || $price_status){
            $sql .= " , ((IFNULL(aft.special, IFNULL(aft.discount, p2.price)))*(1+IFNULL(aft.tax_precent,0)/100)+IFNULL(aft.tax_fixed, 0)) as af_price ";
        }
        if(!empty($params)){
            $sql .= " , p2.af_values ";
        }
        if(!empty($params['tag'])){
            $sql .= " , p2.af_tags ";
        }

        $sql .= " FROM `".DB_PREFIX."af_temporary` aft
        INNER JOIN `".DB_PREFIX."product` p2 ON aft.product_id = p2.product_id ";

        $sql .= " GROUP BY aft.product_id ";

        return $sql;
    }

    public function getQuantity($status){

        $result = array();

        foreach ($status as $type) {
            if(file_exists(DIR_APPLICATION.'/controller/extension/d_ajax_filter/'.$type.'.php')){
                $output = $this->load->controller('extension/'.$this->codename.'/'.$type.'/quantity');
                if(!empty($output)){
                    $result[$type] = $output;
                }
            }
        }
        return $result;
    }

    public function prepareAjaxFilter($data, $sql)
    {
        $filter_data = $this->getFitlerData();
        if(isset($filter_data['filter_category_id']) && isset($data['filter_category_id'])){
            if($filter_data['filter_category_id'] != $data['filter_category_id']){
                return $sql;
            }
        }
        $this->prepareTable($data);
        $total_query = $this->getProductsQuery(true);
        $sql = "SELECT p.product_id, aft.rating, aft.discount, aft.special 
        FROM (" . $total_query . ") aft
        INNER JOIN `".DB_PREFIX."product` p ON aft.product_id = p.product_id
        INNER JOIN `".DB_PREFIX."product_description` pd ON pd.product_id = p.product_id
        WHERE pd.language_id = '".$this->config->get('config_language_id')."' ";

        $params = $this->getParamsToArray();

        if (!empty($params)) {

            $result = $this->getParamsQuery($params, 'aft');
            if(!empty($result)){
                $sql.=" AND ".$result;
            }
        }
        if(empty($this->common_setting['display_out_of_stock']))
        {
            $sql .= "  AND p.quantity > 0 ";
        }

        $sql .= " GROUP BY p.product_id";

        $sort_data = array(
            'pd.name',
            'p.model',
            'p.quantity',
            'p.price',
            'rating',
            'p.sort_order',
            'p.date_added'
            );

        if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
            if ($data['sort'] == 'pd.name' || $data['sort'] == 'p.model') {
                $sql .= " ORDER BY LCASE(" . $data['sort'] . ")";
            } elseif ($data['sort'] == 'p.price') {
                $sql .= " ORDER BY (CASE WHEN special IS NOT NULL THEN special WHEN discount IS NOT NULL THEN discount ELSE p.price END)";
            } else {
                $sql .= " ORDER BY " . $data['sort'];
            }
        } else {
            $sql .= " ORDER BY p.sort_order";
        }

        if (isset($data['order']) && ($data['order'] == 'DESC')) {
            $sql .= " DESC, LCASE(pd.name) DESC";
        } else {
            $sql .= " ASC, LCASE(pd.name) ASC";
        }

        if (isset($data['start']) || isset($data['limit'])) {
            if ($data['start'] < 0) {
                $data['start'] = 0;
            }

            if ($data['limit'] < 1) {
                $data['limit'] = 20;
            }

            $sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
        }

        return $sql; 
    }

    public function getValue($type,$group_id,$value){
        $sql = "SELECT av.af_value_id 
        FROM ".DB_PREFIX."af_values av 
        WHERE av.type='".$type."' AND av.group_id = '".$group_id."' AND av.value='".$value."'";
        return $sql;
    }


    public function prepareAjaxFilterForTotal($data, $sql){
        $filter_data = $this->getFitlerData();
        
        if(isset($filter_data['filter_category_id']) && isset($data['filter_category_id'])){
            if($filter_data['filter_category_id'] != $data['filter_category_id']){
                return $sql;
            }
        }
        elseif(isset($data['filter_category_id']) && !isset($this->request->get['path'])){
            return $sql;
        }
        $this->prepareTable($data);
        $total_query = $this->getProductsQuery(true);
        $sql = "SELECT count(p.product_id) as total 
        FROM (".$total_query.") aft
        INNER JOIN `".DB_PREFIX."product` p ON aft.product_id = p.product_id";

        $params = $this->getParamsToArray();

        if (!empty($params)) {

            $result = $this->getParamsQuery($params, 'aft');
            if(!empty($result)){
                $sql.=" AND ".$result;
            }
        }
        if(empty($this->common_setting['display_out_of_stock']))
        {
            $sql .= "  AND p.quantity > 0 ";
        }
        return $sql;
    }

    public function getRiotTags(){
        $result = array();
        $files = glob(DIR_APPLICATION . 'view/theme/default/template/extension/d_ajax_filter/component/*.tag', GLOB_BRACE);
        foreach($files as $file){
            $result[] = 'catalog/view/theme/default/template/extension/d_ajax_filter/component/'.basename($file).'?'.rand();
        }
        
        $files = glob(DIR_APPLICATION . 'view/theme/default/template/extension/d_ajax_filter/group/*.tag', GLOB_BRACE);
        foreach($files as $file){
            $result[] = 'catalog/view/theme/default/template/extension/d_ajax_filter/group/'.basename($file).'?'.rand();
        }
        return $result;
    }

    private function getCustomerGroupId() {
        return  $this->customer->isLogged() ?  $this->customer->getGroupId() : $this->config->get( 'config_customer_group_id' );
    }

    public function getTax($type){
        $sql = "SELECT tr1.tax_class_id, sum(tr2.rate) as sum_rate
        FROM " . DB_PREFIX . "tax_rule tr1
        LEFT JOIN " . DB_PREFIX . "tax_rate tr2
        ON (tr1.tax_rate_id = tr2.tax_rate_id)
        INNER JOIN " . DB_PREFIX . "tax_rate_to_customer_group tr2cg
        ON (tr2.tax_rate_id = tr2cg.tax_rate_id)
        LEFT JOIN " . DB_PREFIX . "zone_to_geo_zone z2gz
        ON (tr2.geo_zone_id = z2gz.geo_zone_id)
        LEFT JOIN " . DB_PREFIX . "geo_zone gz
        ON (tr2.geo_zone_id = gz.geo_zone_id)
        WHERE tr2.type='" . $type . "'
        AND tr2cg.customer_group_id = '" . $this->getCustomerGroupId() . "'
        AND ( " . $this->getTaxConditions() . " )
        GROUP BY tr1.tax_class_id";
        return $sql;
    }

    private function getTaxConditions() {
        $conditions = array();

        $country_id = $p_country_id = $s_country_id = (int) $this->config->get('config_country_id');
        $zone_id = $p_zone_id = $s_zone_id = (int) $this->config->get('config_zone_id');

        if( ! empty( $this->session->data['payment_country_id'] ) && ! empty( $this->session->data['payment_zone_id'] ) ) {
            $p_country_id = (int) $this->session->data['payment_country_id'];
            $p_zone_id = (int) $this->session->data['payment_zone_id'];
        }

        if( ! empty( $this->session->data['shipping_country_id'] ) && ! empty( $this->session->data['shipping_zone_id'] ) ) {
            $s_country_id = (int) $this->session->data['shipping_country_id'];
            $s_zone_id = (int) $this->session->data['shipping_zone_id'];
        }

        $conditions[] = "(
        `tr1`.`based` = 'store' AND
        `z2gz`.`country_id` = " . $country_id . " AND (
        `z2gz`.`zone_id` = '0' OR `z2gz`.`zone_id` = '" . $zone_id . "'
        )
        )";

        $conditions[] = "(
        `tr1`.`based` = 'payment' AND
        `z2gz`.`country_id` = " . $p_country_id . " AND (
        `z2gz`.`zone_id` = '0' OR `z2gz`.`zone_id` = '" . $p_zone_id . "'
        )
        )";

        $conditions[] = "(
        `tr1`.`based` = 'shipping' AND
        `z2gz`.`country_id` = " . $s_country_id . " AND (
        `z2gz`.`zone_id` = '0' OR `z2gz`.`zone_id` = '" . $s_zone_id . "'
        )
        )";

        return implode( ' OR ', $conditions );
    }

    public function sort_values($values, $type='default'){
        if($type=='default'){
            return $values;
        }

        switch ($type) {
            case 'string_asc':

            uasort($values,  function($a, $b){
                return strcmp($a['name'], $b['name']);
            });

            break;
            case 'string_desc':
            uasort($values, function ($a, $b){
                return (-1)*strcmp($a['name'], $b['name']);
            });
            break;
            case 'numeric_asc':
            uasort($values, function ($a, $b){
                return strnatcmp($a['name'], $b['name']);
            });
            break;
            case 'numeric_desc':
            uasort($values, function ($a, $b){
                return (-1)*strnatcmp($a['name'], $b['name']);
            });
            break;
        }
        return $values;
    }

    /* gets the data from a URL */
    public function get_data($url){
        $ch = curl_init();
        $timeout = 5;
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4 );
        curl_setopt($ch, CURLOPT_ENCODING,  '');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        $data = curl_exec($ch);
        curl_close($ch);
        return $data;
    }


    /*
    * Format the link to work with ajax requests
    */
    public function ajax($link)
    {
        return str_replace('&amp;', '&', $link);
    }
}
