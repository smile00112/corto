<?php
class ModelExtensionDAjaxFilterSearch extends Model {

    private $codename="d_ajax_filter";

    public function __construct($registry) {
        parent::__construct($registry);
        $this->load->model('extension/module/'.$this->codename);
    }

    public function checkProduct($data){
        $sql = "SELECT count(`product_id`) as total FROM `".DB_PREFIX."af_temporary`";
        $hash = md5(json_encode($data));

        $result = $this->cache->get('af-search.' . $hash);

        if(!$result){
            $query = $this->db->query($sql);
            $result = $query->row;
            $this->cache->set('af-search.' .$hash , $result);
        }
        if($result['total'] > 0){
            return true;
        }
        else{
            return false;
        }
    }

    public function getTotalQuery($search, $table_name){

        $keywords =  $search[0][0];
        
        $implode = array();
        $words = explode(' ', trim(preg_replace('/\s+/', ' ', $keywords)));

        foreach ($words as $word) {
            $implode[] = "pd_search.name LIKE '%" . $this->db->escape($word) . "%'";
        }

        if(!empty($implode)){
            $sql ="(SELECT count(pd_search.product_id) FROM (SELECT pd_search_2.product_id, pd_search_2.name, pd_search_2.language_id FROM `".DB_PREFIX."product_description` pd_search_2) pd_search WHERE pd_search.language_id = '".(int)$this->config->get('config_language_id')."' AND ".implode(' AND ', $implode)." AND pd_search.product_id = ".$table_name.".product_id) > 0";
        }
        else{
            $sql = '';
        }
        return $sql;
    }
}