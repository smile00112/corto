<?php
class ControllerExtensionEventDAjaxFilter extends Controller {

    private $codename = 'd_ajax_filter';
    private $route = 'extension/module/d_ajax_filter';
    
    private $route_model = '';
    private $common_setting;

    public function __construct($registry)
    {
        parent::__construct($registry);
        
        $this->load->model($this->route);
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

    public function view_before(&$route, &$data, &$output){
        if(isset($this->request->get['ajax'])){
            $data['header'] = $data['column_left'] = $data['column_right'] = $data['content_top'] = $data['content_bottom'] = $data['footer'] = '';
        }
        $data['content_top'] .= '<div id="ajax-filter-container">';
        $data['content_bottom'] = '</div>' . $data['content_bottom'];

        $url = $this->{'model_extension_module_'.$this->codename}->getUrlParams();
        
        if(!empty($data['pagination'])){
            $html_dom = new d_simple_html_dom();
            $html_dom->load($data['pagination'], $lowercase = true, $stripRN = false, $defaultBRText = DEFAULT_BR_TEXT);
            foreach ($html_dom->find('a') as $link){
                //check on existing
                $re = '/\&|\?/m';
                preg_match_all($re, $link->href, $matches, PREG_SET_ORDER, 0);
                if (count($matches)){
                    $link->href.="&".$url;
                }else{
                    $link->href.="?".$url;
                }
            }
            $data['pagination']=(string)$html_dom;

        }

        if(!empty($data['sorts'])){
            foreach ($data['sorts'] as $key => $sort) {
                $data['sorts'][$key]['href'] .= '&'.$url;
            }
        }

        if(!empty($data['limits'])){
            foreach ($data['limits'] as $key => $limit) {
                $data['limits'][$key]['href'] .= '&'.$url;
            }
        }
    }

    public function model_getProducts_before(&$route, &$data){
        if(isset($data[0])){
            if($this->common_setting['display_sub_category']){
                $data[0]['filter_sub_category'] = true;
            }
            else{
                $data[0]['filter_sub_category'] = false;
            }
        }
    }
}