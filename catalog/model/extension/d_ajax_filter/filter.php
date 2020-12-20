<?php
class ModelExtensionDAjaxFilterFilter extends Model {

    private $codename="d_ajax_filter";

    public function __construct($registry) {
        parent::__construct($registry);
        $this->load->model('extension/module/'.$this->codename);
    }

    public function getFilterGroups($filter_data){
        $sql = "SELECT f.filter_id, fd.name as filter_name, aff.image, f.filter_group_id, fgd.name as filter_group_name
        FROM `".DB_PREFIX."filter` f
        LEFT JOIN `".DB_PREFIX."af_filter` aff ON aff.filter_id = f.filter_id AND aff.language_id = '".(int)$this->config->get('config_language_id')."'
        INNER JOIN `".DB_PREFIX."filter_description` fd ON fd.filter_id = f.filter_id AND fd.language_id = '".(int)$this->config->get('config_language_id')."'
        INNER JOIN `".DB_PREFIX."filter_group` fg ON fg.filter_group_id = f.filter_group_id
        INNER JOIN `".DB_PREFIX."filter_group_description` fgd ON fgd.filter_group_id = f.filter_group_id AND fgd.language_id = '".(int)$this->config->get('config_language_id')."'
        WHERE f.filter_id IN(
        SELECT pf.filter_id
        FROM `".DB_PREFIX."product_filter` pf
        INNER JOIN `".DB_PREFIX."af_temporary` aft ON aft.product_id = pf.product_id
        ) GROUP BY f.filter_id ORDER BY fg.sort_order, f.sort_order";
        
        $filter_group_data = array();
        $hash = md5(json_encode(array($filter_data, (int)$this->config->get('config_language_id'))));

        $result = $this->cache->get('af-filter.' . $hash);

        if(!$result){
            $query = $this->db->query($sql);
            $result = $query->rows;
            $this->cache->set('af-filter.' .$hash , $result);
        }

        if(!empty($result)){
            foreach ($result as $row) {
                if(!isset($filter_group_data[$row['filter_group_id']])){
                    $filter_group_data[$row['filter_group_id']] = array(
                        'name' => $row['filter_group_name'],
                        'filter_group_id' => $row['filter_group_id'],
                        'filters' => array()
                        );
                }
                $filter_group_data[$row['filter_group_id']]['filters'][$row['filter_id']] = array(
                    'name' => $row['filter_name'],
                    'filter_id' => $row['filter_id'],
                    'image' => $row['image']
                    );
            }
        }

        return $filter_group_data;
    }

    public function getFilter($filter_id){
        $query = $this->db->query("SELECT * FROM `".DB_PREFIX."filter` f LEFT JOIN `".DB_PREFIX."filter_description` fd ON f.filter_id = fd.filter_id WHERE fd.language_id = '".(int)$this->config->get('config_language_id')."' AND f.filter_id = '".(int)$filter_id."'");
        return $query->row;
    }

    public function getFilterGroup($filter_group_id) {
        $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "filter_group` fg LEFT JOIN " . DB_PREFIX . "filter_group_description fgd ON (fg.filter_group_id = fgd.filter_group_id) WHERE fg.filter_group_id = '" . (int)$filter_group_id . "' AND fgd.language_id = '" . (int)$this->config->get('config_language_id') . "'");

        return $query->row;
    }

    public function getFilterCount($data){
        $params = $this->{'model_extension_module_'.$this->codename}->getParamsToArray();

        $in = $this->getTotalFilter($data);

        if(!empty($params['filter'])){
            foreach ($params['filter'] as $filter_group_id => $fitlers){
                $group_count = $this->getTotalFilter($data,$filter_group_id);
                if (isset($group_count['filter'][$filter_group_id])) {
                    $in = $this->{'model_extension_module_'.$this->codename}->mergeTotal($in, array('filter' => array($filter_group_id => $group_count['filter'][$filter_group_id])));
                }
            }
        }
        return $in;
    }

    public function getTotalFilter($data, $group_id=null){
        $params = $this->{'model_extension_module_'.$this->codename}->getParamsToArray(true);

        $total_query = $this->{'model_extension_module_'.$this->codename}->getProductsQuery();

        $sql = "SELECT 'filter' as type, f.filter_group_id as id, f.filter_id as val, COUNT(pf.product_id) as c
        FROM ".DB_PREFIX."filter f
        INNER JOIN ".DB_PREFIX."product_filter pf
        ON f.filter_id = pf.filter_id
        INNER JOIN (".$total_query.") p
        ON pf.product_id = p.product_id";

        if (!empty($params)) {

            if(!is_null($group_id)){
                unset($params['filter'][$group_id]);
            }

            $data['params'] = $params;

            $result = $this->{'model_extension_module_'.$this->codename}->getParamsQuery($params);
            if(!empty($result)){
                $sql.=" AND ".$result;
            }
        }

        $sql .= " GROUP BY f.filter_id ";
        $hash = md5(json_encode($data));

        $result = $this->cache->get('af-total-filter.' . $hash);

        if(!$result){
            $query = $this->db->query($sql);
            $result = $query->rows;
            $this->cache->set('af-total-filter.' .$hash , $result);
        }
        return $this->{'model_extension_module_'.$this->codename}->convertResultTotal($result);

    }

    public function getSetting($filter_group_id, $common_setting, $module_setting){

        if(isset($module_setting['filters'][$filter_group_id]) && $module_setting['filters'][$filter_group_id]['status'] != 'default'){
            return $module_setting['filters'][$filter_group_id];
        }

        if($module_setting['filter_default']['status'] != 'default'){
            return $module_setting['filter_default'];
        }
        if(isset($common_setting['filters'][$filter_group_id]) && $common_setting['filters'][$filter_group_id]['status'] != 'default'){
            return $common_setting['filters'][$filter_group_id];
        }
        return $common_setting['default'];
    }

    public function getTotalQuery($filter_groups, $table_name){
        $implode = array();
        foreach ($filter_groups as  $filter_group_id => $filters) {
            $value_array = array();
            foreach ($filters as $filter_id) {
                $value_array[]="FIND_IN_SET((".$this->{'model_extension_module_'.$this->codename}->getValue('filter', $filter_group_id, $filter_id)."),".$table_name.".af_values)";
            }
            $implode[] = "(".implode(' OR ',$value_array).")";
        }
        $sql = "";
        if(!empty($implode)){
            $sql = implode(' AND ', $implode);
        }
        return $sql;
    }

}