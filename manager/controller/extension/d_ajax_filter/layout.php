<?php

/*
*  location: admin/controller
*/

class ControllerExtensionDAjaxFilterLayout extends Controller
{
    private $codename = 'd_ajax_filter';
    private $route = 'extension/d_ajax_filter/layout';
    private $extension = array();
    private $config_file = '';
    private $store_id = 0;
    private $ocmod_xml = '';
    private $error = array();
    
    public function __construct($registry)
    {
        parent::__construct($registry);
        
        $this->load->model($this->route);
        $this->load->model('extension/module/'.$this->codename);
        $this->load->language($this->route);
        
        //extension.json
        $this->extension = json_decode(file_get_contents(DIR_SYSTEM.'library/d_shopunity/extension/'.$this->codename.'.json'), true);
        $this->d_shopunity = (file_exists(DIR_SYSTEM.'library/d_shopunity/extension/d_shopunity.json'));
        $this->event_manager = (file_exists(DIR_SYSTEM.'library/d_shopunity/extension/d_event_manager.json'));
        $this->d_admin_style = (file_exists(DIR_SYSTEM.'library/d_shopunity/extension/d_admin_style.json'));
        
        //Store_id (for multistore)
        if (isset($this->request->get['store_id'])) {
            $this->store_id = $this->request->get['store_id'];
        }
    }

    public function add()
    {
        $json = array();

        $this->load->model('extension/d_opencart_patch/module');
        $this->load->model('extension/d_opencart_patch/url');
        $this->load->model('extension/d_opencart_patch/modification');

        $this->model_extension_d_opencart_patch_modification->setModification($this->codename.'.xml', 0);
        $this->model_extension_d_opencart_patch_modification->refreshCache();
        $this->uninstallEvents();


        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
            $this->model_extension_d_opencart_patch_module->addModule($this->codename, $this->request->post['module_setting']);

            $module_id = $this->db->getLastId();

            $layout_setting = $this->request->post['layout'];

            if (!empty($layout_setting['layouts'])) {
                foreach ($layout_setting['layouts'] as $value) {
                    $this->{'model_extension_'.$this->codename.'_layout'}->addModuleToLayout($module_id, $value, $layout_setting['position'], $layout_setting['sort_order']);
                }
            }
            $global_status = $this->request->post['module_setting']['status'];

            if (!empty($this->request->post['module_status'])) {
                foreach ($this->request->post['module_status'] as $module_id => $status) {
                    $this->{'model_extension_'.$this->codename.'_layout'}->editModuleStatus($module_id, $status);
                    if ($status) {
                        $global_status = true;
                    }
                }
            }

            if ($global_status) {
                $this->model_extension_d_opencart_patch_modification->setModification($this->codename.'.xml', 1);
                $this->model_extension_d_opencart_patch_modification->refreshCache();
                $this->installEvents();
            }

            $this->session->data['success'] = $this->language->get('text_success');
            $json['redirect'] = str_replace('&amp;', '&', $this->model_extension_d_opencart_patch_url->link($this->route, 'module_id='.$module_id));
            $json['success'] = 'success';
        } else {
            $json['error'] = $this->language->get('error_warning');
            $json['errors'] = $this->error;
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    public function edit()
    {
        $json = array();

        $this->load->model('extension/d_opencart_patch/module');
        $this->load->model('extension/d_opencart_patch/url');
        $this->load->model('extension/d_opencart_patch/modification');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
            $this->model_extension_d_opencart_patch_module->editModule($this->request->get['module_id'], $this->request->post['module_setting']);

            $this->{'model_extension_'.$this->codename.'_layout'}->clearLayoutsByModule($this->request->get['module_id']);

            $this->model_extension_d_opencart_patch_modification->setModification($this->codename.'.xml', 0);
            $this->model_extension_d_opencart_patch_modification->refreshCache();
            $this->uninstallEvents();

            $global_status = false;
            if (!empty($this->request->post['module_status'])) {
                foreach ($this->request->post['module_status'] as $module_id => $status) {
                    $this->{'model_extension_'.$this->codename.'_layout'}->editModuleStatus($module_id, $status);
                    if ($status) {
                        $global_status = true;
                    }
                }
            }

            if ($global_status) {
                $this->model_extension_d_opencart_patch_modification->setModification($this->codename.'.xml', 1);
                $this->model_extension_d_opencart_patch_modification->refreshCache();
                $this->installEvents();
            }

            $layout_setting = $this->request->post['layout'];

            if (!empty($layout_setting['layouts'])) {
                foreach ($layout_setting['layouts'] as $value) {
                    $this->{'model_extension_'.$this->codename.'_layout'}->addModuleToLayout($this->request->get['module_id'], $value, $layout_setting['position'], $layout_setting['sort_order']);
                }
            }

            $this->session->data['success'] = $this->language->get('text_success');
            $json['redirect'] = str_replace('&amp;', '&', $this->model_extension_d_opencart_patch_url->link($this->route, 'module_id='.$this->request->get['module_id'], 'SSL'));
            $json['success'] = 'success';
        } else {
            $json['errors'] = $this->error;
            $json['error'] = $this->error['warning'];
        }
        
        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    public function delete()
    {
        $json = array();

        $this->load->model('extension/d_opencart_patch/module');
        $this->load->model('extension/d_opencart_patch/url');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateDelete()) {
            $this->model_extension_d_opencart_patch_module->deleteModule($this->request->post['module_id']);

            $this->session->data['success'] = $this->language->get('text_success');
            $url = '';
            if (!empty($this->request->get['module_id'])) {
                $url .= '&module_id='.$this->request->get['module_id'];
            }
            $json['redirect'] = str_replace('&amp;', '&', $this->model_extension_d_opencart_patch_url->link($this->route, $url));
            $json['success'] = 'success';
        } else {
            $json['errors'] = $this->error;
            $json['error'] = $this->error['warning'];
        }
        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    public function index()
    {
        $this->load->model('extension/d_opencart_patch/url');
        $this->load->model('extension/d_opencart_patch/store');
        $this->load->model('extension/d_opencart_patch/setting');
        $this->load->model('extension/d_opencart_patch/load');
        $this->load->model('extension/d_opencart_patch/module');
        $this->load->model('extension/d_opencart_patch/user');

        $this->document->addStyle('view/stylesheet/d_bootstrap_extra/bootstrap.css');
        // sortable
        $this->document->addScript('view/javascript/d_rubaxa_sortable/sortable.min.js');
        $this->document->addStyle('view/javascript/d_rubaxa_sortable/sortable.css');
        $this->document->addScript('view/javascript/d_tinysort/tinysort.min.js');
        $this->document->addScript('view/javascript/d_tinysort/jquery.tinysort.min.js');

        $this->document->addScript('view/javascript/d_ajax_filter/library/tinycolor-min.js');

        $this->document->addScript('view/javascript/d_ajax_filter/library/bootstrap.colorpickersliders/bootstrap.colorpickersliders.min.js');
        $this->document->addStyle('view/javascript/d_ajax_filter/library/bootstrap.colorpickersliders/bootstrap.colorpickersliders.min.css');
        
        $this->document->addScript('view/javascript/d_bootstrap_switch/js/bootstrap-switch.min.js');
        $this->document->addStyle('view/javascript/d_bootstrap_switch/css/bootstrap-switch.css');

        $this->document->addScript('view/javascript/d_ajax_filter/library/jquery.serializejson.js');
        $this->document->addScript('view/javascript/d_ajax_filter/library/underscore-min.js');

        $this->document->addScript('view/javascript/d_ajax_filter/layout.js');

        $this->document->addStyle('view/stylesheet/d_ajax_filter/layout.css');
        if ($this->d_shopunity) {
            $this->document->addScript('view/javascript/d_shopunity/d_shopunity_widget.js');
        }

        if ($this->d_admin_style) {
            $this->load->model('extension/d_admin_style/style');

            $this->model_extension_d_admin_style_style->getAdminStyle('light');
        }

        $this->load->language($this->route);
        // Add more styles, links or scripts to the project is necessary
        $url_params = array();
        $url = '';

        if (isset($this->response->get['store_id'])) {
            $url_params['store_id'] = $this->store_id;
        }

        if (isset($this->response->get['config'])) {
            $url_params['config'] = $this->response->get['config'];
        }

        $url = ((!empty($url_params)) ? '&' : '') . http_build_query($url_params);

        if (isset($this->session->data['success'])) {
            $data['success'] = $this->session->data['success'];
            unset($this->session->data['success']);
        }

        // Breadcrumbs
        $data['breadcrumbs'] = array();
        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->model_extension_d_opencart_patch_url->link('common/home')
            );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_module'),
            'href' => $this->model_extension_d_opencart_patch_url->link('marketplace/extension', 'type=module')
            );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title_main'),
            'href' => $this->model_extension_d_opencart_patch_url->link($this->route, $url)
            );

        if (!empty($this->request->get['module_id'])) {
            $module_id = $this->request->get['module_id'];
            $data['module_id'] = $module_id;
        }

        $this->document->setTitle($this->language->get('heading_title_main'));
        $data['heading_title'] = $this->language->get('heading_title_main');
        $data['text_edit'] = $this->language->get('text_edit');
        $data['text_list'] = $this->language->get('text_list');
        $data['text_sort_values'] = $this->language->get('text_sort_values');
        $data['text_image'] = $this->language->get('text_image');
        $data['text_form'] = $this->language->get('text_form');
        $data['text_yes'] = $this->language->get('text_yes');
        $data['text_no'] = $this->language->get('text_no');
        $data['text_enabled'] = $this->language->get('text_enabled');
        $data['text_select_all'] = $this->language->get('text_select_all');
        $data['text_unselect_all'] = $this->language->get('text_unselect_all');
        $data['text_header'] = $this->language->get('text_header');
        $data['text_product_quantity'] = $this->language->get('text_product_quantity');
        $data['text_price_slider'] = $this->language->get('text_price_slider');
        $data['text_group_block_header'] = $this->language->get('text_group_block_header');
        $data['text_button_color'] = $this->language->get('text_button_color');
        $data['text_place_bottom'] = $this->language->get('text_place_bottom');
        $data['text_floating_button'] = $this->language->get('text_floating_button');
        $data['text_option_default'] = $this->language->get('text_option_default');
        $data['text_attribute_default'] = $this->language->get('text_attribute_default');
        $data['text_filter_default'] = $this->language->get('text_filter_default');
        $data['text_individual_option_setting'] = $this->language->get('text_individual_option_setting');
        $data['text_individual_attribute_setting'] = $this->language->get('text_individual_attribute_setting');
        $data['text_individual_filter_setting'] = $this->language->get('text_individual_filter_setting');
        $data['text_important'] = $this->language->get('text_important');
        $data['text_warning_option_individual'] = $this->language->get('text_warning_option_individual');
        $data['text_warning_attribute_individual'] = $this->language->get('text_warning_attribute_individual');
        $data['text_warning_filter_individual'] = $this->language->get('text_warning_filter_individual');
        $data['text_warning_select_option'] = $this->language->get('text_warning_select_option');
        $data['text_warning_select_attribute'] = $this->language->get('text_warning_select_attribute');
        $data['text_warning_select_filter'] = $this->language->get('text_warning_select_filter');
        $data['text_status'] = $this->language->get('text_status');
        $data['text_new_setting'] = $this->language->get('text_new_setting');
        $data['text_create_setting'] = $this->language->get('text_create_setting');
        $data['text_default'] = $this->language->get('text_default');
        $data['text_not_positioned'] = $this->language->get('text_not_positioned');
        $data['text_quick_setup_title'] = $this->language->get('text_quick_setup_title');
        $data['text_quick_setup_text'] = $this->language->get('text_quick_setup_text');
        

        $data['column_name'] = $this->language->get('column_name');
        $data['column_type'] = $this->language->get('column_type');
        $data['column_status'] = $this->language->get('column_status');
        $data['column_collapse'] = $this->language->get('column_collapse');
        $data['column_sort_order_values'] = $this->language->get('column_sort_order_values');

        $data['entry_name'] = $this->language->get('entry_name');
        $data['entry_status'] = $this->language->get('entry_status');
        $data['entry_show_mobile'] = $this->language->get('entry_show_mobile');
        $data['entry_ajax'] = $this->language->get('entry_ajax');
        $data['entry_support'] = $this->language->get('entry_support');
        $data['entry_contant_path'] = $this->language->get('entry_contant_path');
        $data['entry_show_mode'] = $this->language->get('entry_show_mode');
        $data['entry_categories'] = $this->language->get('entry_categories');
        $data['entry_layouts'] = $this->language->get('entry_layouts');
        $data['entry_position'] = $this->language->get('entry_position');
        $data['entry_sort_order'] = $this->language->get('entry_sort_order');
        $data['entry_background'] = $this->language->get('entry_background');
        $data['entry_text'] = $this->language->get('entry_text');
        $data['entry_title'] = $this->language->get('entry_title');
        $data['entry_theme'] = $this->language->get('entry_theme');
        $data['entry_theme_scrollbar'] = $this->language->get('entry_theme_scrollbar');
        $data['entry_active_area_background'] = $this->language->get('entry_active_area_background');
        $data['entry_handle_background'] = $this->language->get('entry_handle_background');
        $data['entry_handle_border'] = $this->language->get('entry_handle_border');
        $data['entry_border'] = $this->language->get('entry_border');
        $data['entry_color_button_filter'] = $this->language->get('entry_color_button_filter');
        $data['entry_color_border_image'] = $this->language->get('entry_color_border_image');
        $data['entry_color_button_selected'] = $this->language->get('entry_color_button_selected');
        $data['entry_border_radius_image'] = $this->language->get('entry_border_radius_image');
        $data['entry_color_button_reset'] = $this->language->get('entry_color_button_reset');
        $data['entry_any_change'] = $this->language->get('entry_any_change');
        $data['entry_apply_button'] = $this->language->get('entry_apply_button');
        $data['entry_several_filter'] = $this->language->get('entry_several_filter');
        $data['entry_position_button'] = $this->language->get('entry_position_button');
        $data['entry_time'] = $this->language->get('entry_time');
        $data['entry_show_selected_filters'] = $this->language->get('entry_show_selected_filters');
        $data['entry_button_reset'] = $this->language->get('entry_button_reset');
        $data['entry_display_quantity_product'] = $this->language->get('entry_display_quantity_product');
        $data['entry_limit_height'] = $this->language->get('entry_limit_height');
        $data['entry_height'] = $this->language->get('entry_height');
        $data['entry_limit_block'] = $this->language->get('entry_limit_block');
        $data['entry_count_element'] = $this->language->get('entry_count_element');
        $data['entry_min_elemnts'] = $this->language->get('entry_min_elemnts');
        $data['entry_event_filter'] = $this->language->get('entry_event_filter');
        $data['entry_custom_style'] = $this->language->get('entry_custom_style');

        $data['event_support'] = $this->language->get('event_support');

        $data['button_support_email'] = $this->language->get('button_support_email');
        $data['button_quick_setup'] = $this->language->get('button_quick_setup');
        $data['button_edit'] = $this->language->get('button_edit');
        $data['button_save'] = $this->language->get('button_save');
        $data['button_cancel'] = $this->language->get('button_cancel');
        $data['button_delete'] = $this->language->get('button_delete');

        // Variable
        $data['id'] = $this->codename;
        $data['route'] = $this->route;
        $data['store_id'] = $this->store_id;
        $data['extension'] = $this->extension;
        $data['config'] = $this->config_file;
        $data['shopunity_support'] = $this->d_shopunity;

        $data['version'] = $this->extension['version'];
        $data['token'] = $this->model_extension_d_opencart_patch_user->getToken();
        $data['token_url'] = $this->model_extension_d_opencart_patch_user->getUrlToken();
        $data['support_email'] = $this->extension['support']['email'];

        if (isset($this->request->get['module_id'])) {
            $data['action'] = $this->model_extension_d_opencart_patch_url->link('extension/'.$this->codename.'/layout/edit', 'module_id='.$this->request->get['module_id']);
        } else {
            $data['action'] = $this->model_extension_d_opencart_patch_url->link('extension/'.$this->codename.'/layout/add');
        }

        $data['cancel'] = $this->model_extension_d_opencart_patch_url->link('marketplace/extension', 'type=module');

        $data['delete_module'] = str_replace('&amp;', '&', $this->model_extension_d_opencart_patch_url->link('extension/'.$this->codename.'/layout/delete'));

        $modules = $this->model_extension_d_opencart_patch_module->getModulesByCode($this->codename);
        
        if (empty($modules)) {
            $this->response->redirect($this->model_extension_d_opencart_patch_url->link($this->route.'/welcome'));
        }

        $data['modules'] = array();

        if (!empty($modules)) {
            foreach ($modules as $value) {
                if (isset($module_id) && $value['module_id'] == $module_id) {
                    $active = true;
                } else {
                    $active = false;
                }
                $data['modules'][] = array(
                    'name' => $value['name'],
                    'module_id' => $value['module_id'],
                    'active' => $active,
                    'layouts' => $this->{'model_extension_'.$this->codename.'_layout'}->getLayoutsByModules($value['module_id']),
                    'setting' => $this->model_extension_d_opencart_patch_module->getModule($value['module_id']),
                    'link' => $this->model_extension_d_opencart_patch_url->link('extension/'.$this->codename.'/layout', 'module_id='.$value['module_id'])
                    );
            }
        }

        $data['tabs'] = $this->{'model_extension_module_'.$this->codename}->getTabs('layout');

        $data['layout_tabs'] = $this->{'model_extension_'.$this->codename.'_layout'}->getTabs(!empty($data['modules'])?true:false);

        $data['show_modes'] = array(
            'all' => $this->language->get('text_show_mode_all'),
            'show' => $this->language->get('text_show_mode_show'),
            'hide' => $this->language->get('text_show_mode_hide')
            );

        $data['positions'] = array(
            'column_left' => $this->language->get('text_position_column_left'),
            'column_right' => $this->language->get('text_position_column_right'),
            'content_top' => $this->language->get('text_position_column_top'),
            'content_bottom' => $this->language->get('text_position_column_bottom')
            );

        $this->load->model('localisation/language');

        $data['languages'] = $this->model_localisation_language->getLanguages();
        foreach ($data['languages'] as $key =>  $language) {
            if (VERSION >= '2.2.0.0') {
                $data['languages'][$key]['flag'] = 'language/'.$language['code'].'/'.$language['code'].'.png';
            } else {
                $data['languages'][$key]['flag'] = 'view/image/flags/'.$language['image'];
            }
        }


        $this->load->model('catalog/category');
        $data['categories'] = array();

        if (VERSION>='2.2.0.0') {
            $results = $this->model_catalog_category->getCategories();
        } else {
            $results = $this->model_catalog_category->getCategories(array());
        }
        
        foreach ($results as $value) {
            $data['categories'][$value['category_id']] = $value['name'];
        }
        $this->load->model('design/layout');

        $results = $this->model_design_layout->getLayouts();

        $data['layouts'] = array();

        foreach ($results as $value) {
            $data['layouts'][$value['layout_id']] = $value['name'];
        }

        $data['selected_layouts'] = array();

        if (isset($module_id)) {
            $results = $this->{'model_extension_'.$this->codename.'_layout'}->getLayoutsByModules($module_id);

            foreach ($results as $value) {
                $data['selected_layouts'][] = $value['layout_id'];
            }
        }

        if (isset($module_id)) {
            $data['position'] = $this->{'model_extension_'.$this->codename.'_layout'}->getPrositionByModule($module_id);
        } else {
            $data['position'] = 'column_left';
        }

        if (isset($module_id)) {
            $data['sort_order'] = $this->{'model_extension_'.$this->codename.'_layout'}->getSortOrderByModule($module_id);
        } else {
            $data['sort_order'] = '0';
        }
        $this->config->load('d_ajax_filter');
        $config_setting = $this->config->get('d_ajax_filter_setting');
        if (isset($module_id)) {
            $data['setting'] = $this->model_extension_d_opencart_patch_module->getModule($module_id);
        } else {
            $data['setting'] = $config_setting['default'];
        }

        $data['base_attribs'] = $this->{'model_extension_'.$this->codename.'_layout'}->getBaseAttribs();
        $that = $this;
        array_walk($data['base_attribs'], function (&$value, $index) use ($data, $that) {
            $base_attrib_setting = $that->{'model_extension_'.$that->codename.'_layout'}->getModuleSetting($index);

            if (isset($data['setting']['base_attribs'][$index])) {
                $value=$data['setting']['base_attribs'][$index];
            }
            
            $value['allowed_types'] = $base_attrib_setting['allowed_types'];
            $value['name'] = $that->language->get('text_base_attrib_'.$index);
        });

        $data['themes_scrollbar'] = array(
            'light' => $this->language->get('text_scroolbar_theme_light'),
            'dark' => $this->language->get('text_scroolbar_theme_dark'),
            'minimal' => $this->language->get('text_scroolbar_theme_minimal'),
            'minimal-dark' => $this->language->get('text_scroolbar_theme_minimal_dark'),
            'light-2' => $this->language->get('text_scroolbar_theme_light_2'),
            'dark-2' => $this->language->get('text_scroolbar_theme_dark_2'),
            'light-3' => $this->language->get('text_scroolbar_theme_light_3'),
            'dark-3' => $this->language->get('text_scroolbar_theme_dark_3'),
            'light-thick' => $this->language->get('text_scroolbar_theme_light_thick'),
            'dark-thick' => $this->language->get('text_scroolbar_theme_dark_thick'),
            'light-thin' => $this->language->get('text_scroolbar_theme_light_thin'),
            'dark-thin' => $this->language->get('text_scroolbar_theme_dark_thin'),
            'inset' => $this->language->get('text_scroolbar_theme_inset'),
            'inset-dark' => $this->language->get('text_scroolbar_theme_inset_dark'),
            'inset-2' => $this->language->get('text_scroolbar_theme_inset_2'),
            'inset-2-dark' => $this->language->get('text_scroolbar_theme_inset_2_dark'),
            'inset-3' => $this->language->get('text_scroolbar_theme_inset_3'),
            'inset-3-dark' => $this->language->get('text_scroolbar_theme_inset_3_dark'),
            'rounded' => $this->language->get('text_scroolbar_theme_rounded'),
            'rounded-dark' => $this->language->get('text_scroolbar_theme_rounded_dark'),
            'rounded-dots' => $this->language->get('text_scroolbar_theme_rounded_dots'),
            'rounded-dots-dark' => $this->language->get('text_scroolbar_theme_rounded_dots_dark'),
            '3d' => $this->language->get('text_scroolbar_theme_3d'),
            '3d-dark' => $this->language->get('text_scroolbar_theme_3d_dark'),
            '3d-thick' => $this->language->get('text_scroolbar_theme_3d_thick'),
            '3d-thick-dark' => $this->language->get('text_scroolbar_theme_3d_thick_dark'),
        );

        $data['themes'] = $this->{'model_extension_'.$this->codename.'_layout'}->getThemes();

        if (isset($data['setting']['theme'])) {
            if ($data['setting']['theme'] != 'custom') {
                $data['design'] = $config_setting['theme'];
            } else {
                $data['design'] = $data['setting']['design'];
            }
        } else {
            $data['design'] = $config_setting['default'];
        }

        $data['base_types'] = array(
            'radio' => $this->language->get('text_base_type_radio'),
            'select' => $this->language->get('text_base_type_select'),
            'checkbox' => $this->language->get('text_base_type_checkbox'),
            'radio_and_image' => $this->language->get('text_base_type_radio_and_image'),
            'checkbox_and_image' => $this->language->get('text_base_type_checkbox_and_image'),
            'image_radio' => $this->language->get('text_base_type_image_radio'),
            'image_checkbox' => $this->language->get('text_base_type_image_checkbox'),
            'slider_inputs' => $this->language->get('text_base_type_slider_inputs'),
            'slider_label' => $this->language->get('text_base_type_slider_label'),
            'star_checkbox' => $this->language->get('text_base_type_checkbox'),
            'star_radio' => $this->language->get('text_base_type_radio'),
            'text' => $this->language->get('text_base_type_text')
            );

        $data['sort_order_types'] = array(
            'default' => $this->language->get('text_sort_order_type_default'),
            'string_asc' => $this->language->get('text_sort_order_type_string_asc'),
            'string_desc' => $this->language->get('text_sort_order_type_string_desc'),
            'numeric_asc' => $this->language->get('text_sort_order_type_numeric_asc'),
            'numeric_desc' => $this->language->get('text_sort_order_type_numeric_desc'),
            );

        $results = $this->{'model_extension_'.$this->codename.'_layout'}->getTemplates();

        $data['templates'] = array();
        
        foreach ($results as $value) {
            $data['templates'][] = $this->load->controller('extension/'.$this->codename.'_module/'.$value.'/prepare_template', $data['setting']);
        }

        $data['notify'] = $this->{'model_extension_'.$this->codename.'_layout'}->checkCompleteVersion();
        
        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');
        $this->response->setOutput($this->model_extension_d_opencart_patch_load->view($this->route, $data));
    }

    public function quickInstall()
    {
        $json = array();
        
        $this->load->model('extension/d_opencart_patch/url');
        $this->load->model('extension/d_opencart_patch/modification');
        $this->load->model('extension/d_opencart_patch/module');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateQuickInstall()) {
            $this->model_extension_d_opencart_patch_modification->setModification($this->codename.'.xml', 0);
            $this->model_extension_d_opencart_patch_modification->refreshCache();
            $this->uninstallEvents();

            if (isset($this->request->get['status_setup'])) {
                $status_setup = $this->request->get['status_setup'];
            } else {
                $status_setup = false;
            }

            $module_id = $this->{'model_extension_'.$this->codename.'_layout'}->quickInstall($status_setup);

            if (!empty($this->request->get['layout_setting'])) {
                $layouts = $this->{'model_extension_'.$this->codename.'_layout'}->getLayoutsByRoute('product/category');
                if (!empty($layouts)) {
                    $layout_info = $layouts[0];
                    $this->{'model_extension_'.$this->codename.'_layout'}->addModuleToLayout($module_id, $layout_info['layout_id'], 'column_left', 0);
                }
            }

            $global_status = false;

            $modules = $this->model_extension_d_opencart_patch_module->getModulesByCode($this->codename);
            if (!empty($modules)) {
                foreach ($modules as $value) {
                    $setting = $this->model_extension_d_opencart_patch_module->getModule($value['module_id']);
                    if ($setting['status']) {
                        $global_status = true;
                    }
                }
            }
            
            if ($global_status) {
                $this->model_extension_d_opencart_patch_modification->setModification($this->codename.'.xml', 1);
                $this->model_extension_d_opencart_patch_modification->refreshCache();
                $this->installEvents();
            }

            $json['redirect'] = str_replace('&amp;', '&', $this->model_extension_d_opencart_patch_url->link($this->route, 'module_id='.$module_id));
            $json['success'] = 'success';
        } else {
            $json['error'] = 'error';
        }
        
        $this->response->addHeader("Content-Type: application/json");
        $this->response->setOutput(json_encode($json));
    }



    public function installEvents()
    {
        if ($this->event_manager) {
            $this->load->model('extension/module/d_event_manager');
            $this->model_extension_module_d_event_manager->addEvent($this->codename, 'admin/model/catalog/product/editProduct/after', 'extension/d_ajax_filter/cache/model_updateProduct');
            $this->model_extension_module_d_event_manager->addEvent($this->codename, 'admin/model/catalog/product/addProduct/after', 'extension/d_ajax_filter/cache/model_updateProduct');
            $this->model_extension_module_d_event_manager->addEvent($this->codename, 'catalog/view/product/category/before', 'extension/event/d_ajax_filter/view_before');
            $this->model_extension_module_d_event_manager->addEvent($this->codename, 'catalog/view/product/special/before', 'extension/event/d_ajax_filter/view_before');
            $this->model_extension_module_d_event_manager->addEvent($this->codename, 'catalog/view/product/manufacturer_info/before', 'extension/event/d_ajax_filter/view_before');
            $this->model_extension_module_d_event_manager->addEvent($this->codename, 'catalog/view/product/search/before', 'extension/event/d_ajax_filter/view_before');
            $this->model_extension_module_d_event_manager->addEvent($this->codename, 'catalog/model/catalog/product/getProducts/before', 'extension/event/d_ajax_filter/model_getProducts_before');
            $this->model_extension_module_d_event_manager->addEvent($this->codename, 'catalog/model/catalog/product/getProductSpecials/before', 'extension/event/d_ajax_filter/model_getProducts_before');
            $this->model_extension_module_d_event_manager->addEvent($this->codename, 'catalog/model/catalog/product/getTotalProducts/before', 'extension/event/d_ajax_filter/model_getProducts_before');
        }
    }
    public function uninstallEvents()
    {
        if ($this->event_manager) {
            $this->load->model('extension/module/d_event_manager');
            $this->model_extension_module_d_event_manager->deleteEvent($this->codename);
        }
    }

    public function validateQuickInstall($permission = 'modify')
    {
        if (!$this->user->hasPermission($permission, $this->route)) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        if (!empty($this->error)) {
            $this->error['warning'] =$this->language->get('error_warning');
        }

        return !$this->error;
    }

    private function validate($permission = 'modify')
    {
        if (!$this->user->hasPermission($permission, $this->route)) {
            $this->error['warning'] = $this->language->get('error_permission');
        }
        if (strlen($this->request->post['module_setting']['name'])<=0 || strlen($this->request->post['module_setting']['name'])>64) {
            $this->error['name'] = $this->language->get('error_name');
        }

        $this->load->model('localisation/language');

        $languages = $this->model_localisation_language->getLanguages();

        foreach ($languages as $language) {
            if (strlen($this->request->post['module_setting']['title'][$language['language_id']])<=0 || strlen($this->request->post['module_setting']['title'][$language['language_id']])>96) {
                $this->error['title'.$language['language_id']] = $this->language->get('error_title');
            }
        }

        if ($this->request->post['layout']['sort_order']=='' || !is_numeric($this->request->post['layout']['sort_order'])) {
            $this->error['sort_order'] = $this->language->get('error_sort_order');
        }

        if (!empty($this->error)) {
            $this->error['warning'] =$this->language->get('error_warning');
        }

        return !$this->error;
    }

    private function validateDelete($permission = 'modify')
    {
        if (!$this->user->hasPermission($permission, $this->route)) {
            $this->error['warning'] = $this->language->get('error_permission');
            return false;
        }

        return true;
    }

    public function welcome()
    {
        $this->load->model('extension/d_opencart_patch/load');
        $this->load->model('extension/d_opencart_patch/url');

        if ($this->d_admin_style) {
            $this->load->model('extension/d_admin_style/style');

            $this->model_extension_d_admin_style_style->getAdminStyle('light');
        }
        
        $url_params = array();
        
        if (isset($this->response->get['store_id'])) {
            $url_params['store_id'] = $this->store_id;
        }
        
        $url = ((!empty($url_params)) ? '&' : '') . http_build_query($url_params);
        
        // Breadcrumbs
        $data['breadcrumbs'] = array();
        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->model_extension_d_opencart_patch_url->link('common/home')
            );

        $data['breadcrumbs'][] = array(
            'text'      => $this->language->get('text_module'),
            'href'      => $this->model_extension_d_opencart_patch_url->link('marketplace/extension', 'type=module')
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title_main'),
            'href' => $this->model_extension_d_opencart_patch_url->link('marketplace/extension', $url)
        );
        
        // Notification
        foreach ($this->error as $key => $error) {
            $data['error'][$key] = $error;
        }
        
        // Heading
        $this->document->setTitle($this->language->get('heading_title_main'));
        $data['heading_title'] = $this->language->get('heading_title_main');
        $data['text_edit'] = $this->language->get('text_form');
        $data['cancel'] =$this->model_extension_d_opencart_patch_url->getExtensionLink('module');

        $data['version'] = $this->extension['version'];
        
        $data['text_welcome_title'] = $this->language->get('text_welcome_title');
        $data['text_welcome_description'] = $this->language->get('text_welcome_description');

        $data['text_welcome_easy_setup'] = $this->language->get('text_welcome_easy_setup');
        $data['text_welcome_filter_by'] = $this->language->get('text_welcome_filter_by');
        $data['text_welcome_vertical_and_horizontal'] = $this->language->get('text_welcome_vertical_and_horizontal');
        $data['text_welcome_fast'] = $this->language->get('text_welcome_fast');

        $data['button_setup'] = $this->language->get('button_setup');

        $data['quick_setup'] = $this->model_extension_d_opencart_patch_url->ajax($this->route.'/quickSetup', '&layout_setting=1&status_setup=1');
        
        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->model_extension_d_opencart_patch_load->view('extension/'.$this->codename.'/welcome', $data));
    }

    public function quickSetup()
    {
        $this->{'model_extension_module_'.$this->codename}->CreateDatabase();
        $this->quickInstall();
    }
}
