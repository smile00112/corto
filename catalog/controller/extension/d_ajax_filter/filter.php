<?php

class ControllerExtensionDAjaxFilterFilter extends Controller
{
    private $codename = 'd_ajax_filter';
    private $route = 'extension/d_ajax_filter/filter';
    private $filter_data = array();
    
    private $filter_setting = array();

    public function __construct($registry)
    {
        parent::__construct($registry);
        $this->load->model('extension/module/'.$this->codename);
        $this->load->model($this->route);

        $this->load->language('extension/module/'.$this->codename);

        $this->filter_data = $this->{'model_extension_module_'.$this->codename}->getFitlerData();
        

        $filter_setting = $this->config->get($this->codename.'_filters');
        if(empty($filter_setting)){
            $this->config->load('d_ajax_filter');
            $setting = $this->config->get('d_ajax_filter_setting');

            $filter_setting = $setting['filters'];
        }

        $this->filter_setting = $filter_setting;
    }

    public function index($setting) {
        $result_filters = array();
        $results = $this->{'model_extension_'.$this->codename.'_filter'}->getFilterGroups($this->filter_data);

        foreach ($results as $filter_group_id => $filter_group_info) {
            $filters = $filter_group_info['filters'];

            $filter_setting = $this->{'model_extension_'.$this->codename.'_filter'}->getSetting($filter_group_id, $this->filter_setting, $setting['module_setting']);
            $this->load->model('tool/image');
            $filter_data = array();
            if($filter_setting['status']){
                
                foreach ($filters as $filter_id => $filter_info) {

                    if(!empty($filter_info['image'])&&file_exists(DIR_IMAGE.$filter_info['image'])) {
                        $thumb = $this->model_tool_image->resize($filter_info['image'],45,45);
                    }
                    else {
                        $thumb = $this->model_tool_image->resize('no_image.png',45,45);
                    }

                    $filter_data['_'.$filter_id] = array(
                        'name' => html_entity_decode($filter_info['name'], ENT_QUOTES, 'UTF-8'),
                        'value' => $filter_id,
                        'thumb' => $thumb
                        );
                }

                if(!empty($filter_data)){

                    $filter_data = $this->{'model_extension_module_'.$this->codename}->sort_values($filter_data, $filter_setting['sort_order_values']);
                    
                    $result_filters['_'.$filter_group_id] = array(
                        'caption' => html_entity_decode($filter_group_info['name'], ENT_QUOTES, 'UTF-8'),
                        'name' => 'filter',
                        'group_id' => $filter_group_id,
                        'type' =>  $filter_setting['type'],
                        'collapse' =>  $filter_setting['collapse'],
                        'values' => $filter_data,
                        'sort_order'=> $setting['sort_order']
                        );
                }
            }
        }

        return $result_filters;
    }


    public function quantity(){

        $quantity = $this->{'model_extension_'.$this->codename.'_filter'}->getFilterCount($this->filter_data);


        if(isset($quantity['filter'])){
            $filter_quantity = $quantity['filter'];
        }
        else{
            $filter_quantity = array();
        }

        return $filter_quantity;
    }

    public function url($query){
        $groups = array();

        preg_match_all('/f([0-9]+)-([^<>,]*),([^&>\\/<]*)(\/|\\z)/', $query, $matches, PREG_SET_ORDER);

        if(!empty($matches)){
            foreach ($matches as $match) {
                if(!empty($match[1]) && !empty($match[3])){
                    $group_id = (int)$match[1];
                    $names = explode(',', $match[3]);
                    $names =array_map(function($val){ return "'".$val."'"; }, $names);

                    $results = $this->{'model_extension_module_'.$this->codename}->getTranslit($names, 'filter', $group_id);
                    if(!empty($results)){
                        $groups[$group_id] = $results;
                    }
                }
            }
        }
        
        return $groups;
    }

    public function rewrite($data){
        $result = array();
        if(!empty($data)){
            foreach ($data as $filter_group_id => $filters) {
                $filter_group_info = $this->{'model_extension_'.$this->codename.'_filter'}->getFilterGroup($filter_group_id);
                $filter_group_info['name'] = html_entity_decode($filter_group_info['name'], ENT_QUOTES, 'UTF-8');
                $query = array('f'.$filter_group_id.'-'.$this->{'model_extension_module_'.$this->codename}->translit($filter_group_info['name']));
                foreach ($filters as $filter_id) {
                    $filter_info = $this->{'model_extension_'.$this->codename.'_filter'}->getFilter($filter_id);

                    $name = html_entity_decode($filter_info['name'], ENT_QUOTES, 'UTF-8');

                    $query[] = $this->{'model_extension_module_'.$this->codename}->setTranslit($name, 'filter', $filter_group_id, $filter_id);
                }
                if(count($query) > 1){
                    $result[] = implode(',', $query);
                }
            }
            
        }

        return $result;
    }

}
