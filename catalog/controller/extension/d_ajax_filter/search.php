<?php

class ControllerExtensionDAjaxFilterSearch extends Controller
{
    private $codename = 'd_ajax_filter';
    private $route = 'extension/d_ajax_filter/search';
    private $filter_data = array();
    

    public function __construct($registry)
    {
        parent::__construct($registry);
        $this->load->model('extension/module/'.$this->codename);
        $this->load->language('extension/module/'.$this->codename);
        $this->load->model($this->route);
        $this->filter_data = $this->{'model_extension_module_'.$this->codename}->getFitlerData();
        

    }

    public function index($setting){
        $filters = array();
        $result = $this->{'model_extension_'.$this->codename.'_search'}->checkProduct($this->filter_data);
        if($result){
            $filters['_0'] = array(
                'caption' => $this->language->get('text_search'),
                'collapse' => $setting['collapse'],
                'name' => 'search',
                'group_id' => 0,
                'type' => $setting['type'],
                'values' => array(!empty($this->selected_params['search'][0][0])?$this->selected_params['search'][0][0]:''),
                'sort_order'=> $setting['sort_order']
                );
        }
        
        return $filters;
    }

    public function url($query){
        $groups = array();

        preg_match('/search,([^&><]*)\\/?/', $query, $matches);

        if(!empty($matches[1])){
            $groups[0][0] = $matches[1];
        }
        
        return $groups;
    }

    public function rewrite($data){
        $result = array();
        if(!empty($data[0][0])){
            $result[] = 'search,'.$data[0][0];
        }

        return $result;
    }
}