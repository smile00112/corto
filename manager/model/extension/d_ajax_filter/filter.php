<?php
/*
*  location: admin/model
*/

class ModelExtensionDAjaxFilterFilter extends Model {

    private $codename = 'd_ajax_filter';

    public function getFilterGroups($data = array()) {
        $sql = "SELECT * FROM `" . DB_PREFIX . "filter_group` fg LEFT JOIN " . DB_PREFIX . "filter_group_description fgd ON (fg.filter_group_id = fgd.filter_group_id) WHERE fgd.language_id = '" . (int)$this->config->get('config_language_id') . "'";

        if (!empty($data['filter_name'])) {
            $sql .= " AND LCASE(fgd.name) LIKE '%" . $this->db->escape(utf8_strtolower($data['filter_name'])) . "%'";
        }

        $sort_data = array(
            'fgd.name',
            'fg.sort_order'
            );

        if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
            $sql .= " ORDER BY " . $data['sort'];
        } else {
            $sql .= " ORDER BY fgd.name";
        }

        if (isset($data['order']) && ($data['order'] == 'DESC')) {
            $sql .= " DESC";
        } else {
            $sql .= " ASC";
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

        $query = $this->db->query($sql);

        return $query->rows;
    }

    public function getFilterGroupsByLanguageId($language_id){
        $sql = "SELECT f.filter_group_id, fgd.name 
        FROM `".DB_PREFIX."product_filter` pf
        LEFT JOIN `".DB_PREFIX."filter` f
        ON f.filter_id = pf.filter_id
        LEFT JOIN `".DB_PREFIX."filter_group_description` fgd
        ON f.filter_group_id = fgd.filter_group_id
        LEFT JOIN `".DB_PREFIX."filter_group_description` fgd2
        ON f.filter_group_id = fgd2.filter_group_id
        WHERE fgd.language_id='".(int)$this->config->get('config_language_id')."' AND fgd2.language_id='".(int)$language_id."'
        GROUP BY f.filter_group_id";

        $query = $this->db->query($sql);

        return $query->rows;
    }

    public function getFilterImages($filter_group_id, $language_id){
        $sql = "SELECT f.filter_id, af.image, fd.name
        FROM `".DB_PREFIX."filter` f
        LEFT JOIN `".DB_PREFIX."filter_description` fd ON fd.filter_id = f.filter_id
        LEFT JOIN `".DB_PREFIX."filter_description` fd2 ON fd2.filter_id = f.filter_id
        LEFT JOIN `".DB_PREFIX."af_filter` af ON f.filter_id = af.filter_id AND fd2.language_id = af.language_id
        WHERE f.filter_group_id = '".(int)$filter_group_id."'  AND fd.language_id = '".(int)$this->config->get('config_language_id')."' AND fd2.language_id='".(int)$language_id."'";
        $query = $this->db->query($sql);

        return $query->rows;
    }

    public function editFilterImages($language_id, $filters)
    {
        foreach ($filters as $filter_id => $filter) {
            $query = $this->db->query("SELECT * FROM `".DB_PREFIX."af_filter` WHERE `filter_id`='".(int)$filter_id."' AND `language_id` = '".(int)$language_id."'");
            if($query->num_rows > 0){
                $this->db->query("UPDATE `".DB_PREFIX."af_filter` SET `image` = '" . $filter['image'] ."' WHERE  `filter_id`='".$filter_id."' AND `language_id`='".$language_id."'");
            }
            else{
                $this->db->query("INSERT INTO `".DB_PREFIX."af_filter` SET `image` = '" . $filter['image'] ."', `filter_id`='".(int)$filter_id."', `language_id`='".(int)$language_id."'");
            }
            
        }
    }
}