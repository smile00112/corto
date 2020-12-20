<?php
class ControllerExtensionShippingXshippingpro extends Controller {
	private $error = array(); 
	
	public function index() {   

		$this->load->language('extension/shipping/xshippingpro');

		$this->document->setTitle($this->language->get('heading_title'));
		
		$this->load->model('setting/setting');
		$this->load->model('extension/xshippingpro/xshippingpro');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {

			if(isset($this->request->post['action']) && $this->request->post['action']=='import') {
				$this->import();
				$this->response->redirect($this->url->link('extension/shipping/xshippingpro', 'user_token=' . $this->session->data['user_token'], 'SSL'));
			}

			$this->session->data['success'] = $this->language->get('text_success');	
			$this->response->redirect($this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=shipping', true));
		}
		
		if($this->model_extension_xshippingpro_xshippingpro->isDBBUPdateAvail()){
			$this->model_extension_xshippingpro_xshippingpro->install();
		}


		$data['heading_title'] = $this->language->get('heading_title');

		$data['tab_rate'] = $this->language->get('tab_rate');
		$data['entry_name'] = $this->language->get('entry_name');
		$data['entry_order_total'] = $this->language->get('entry_order_total');
		$data['entry_order_weight'] = $this->language->get('entry_order_weight');
		$data['entry_quantity'] = $this->language->get('entry_quantity');
		$data['entry_to'] = $this->language->get('entry_to');
		$data['entry_order_hints'] = $this->language->get('entry_order_hints');
		$data['text_enabled'] = $this->language->get('text_enabled');
		$data['text_disabled'] = $this->language->get('text_disabled');
		$data['text_all_zones'] = $this->language->get('text_all_zones');
		$data['text_none'] = $this->language->get('text_none');
		$data['text_edit'] = $this->language->get('text_edit');
		$data['ignore_modifier'] = $this->language->get('ignore_modifier');
		$data['tip_weight'] = $this->language->get('tip_weight');
		$data['tip_total'] = $this->language->get('tip_total');
		$data['tip_quantity'] = $this->language->get('tip_quantity');
		
		$data['entry_cost'] = $this->language->get('entry_cost');
		$data['entry_tax'] = $this->language->get('entry_tax');
		$data['entry_geo_zone'] = $this->language->get('entry_geo_zone');
		$data['entry_status'] = $this->language->get('entry_status');
		$data['entry_sort_order'] = $this->language->get('entry_sort_order');
		$data['entry_customer_group'] = $this->language->get('entry_customer_group');
		$data['text_all'] = $this->language->get('text_all');
		$data['text_category'] = $this->language->get('text_category');
		$data['text_category_any'] = $this->language->get('text_category_any');
		$data['text_category_all'] = $this->language->get('text_category_all');
		$data['text_category_least'] = $this->language->get('text_category_least');
		$data['text_category_least_with_other'] = $this->language->get('text_category_least_with_other'); 
		$data['text_category_except_other'] = $this->language->get('text_category_except_other');
		
		$data['text_grand_total'] = $this->language->get('text_grand_total');
		$data['text_category_except'] = $this->language->get('text_category_except');
		$data['text_category_exact'] = $this->language->get('text_category_exact');
		$data['entry_category'] = $this->language->get('entry_category');
		$data['entry_weight_include'] = $this->language->get('entry_weight_include');
		$data['entry_desc'] = $this->language->get('entry_desc');
		$data['text_select_all'] = $this->language->get('text_select_all');
		$data['text_unselect_all'] = $this->language->get('text_unselect_all');
		$data['text_any'] = $this->language->get('text_any');
		$data['module_status'] = $this->language->get('module_status');
		$data['text_heading'] = $this->language->get('text_heading');
		$data['entry_store'] = $this->language->get('entry_store');
		$data['entry_manufacturer'] = $this->language->get('entry_manufacturer');
		$data['text_product'] = $this->language->get('text_product');
		$data['text_product_any'] = $this->language->get('text_product_any');
		$data['text_product_all'] = $this->language->get('text_product_all');
		$data['text_product_least'] = $this->language->get('text_product_least');
		$data['text_product_least_with_other'] = $this->language->get('text_product_least_with_other');
		$data['text_product_exact'] = $this->language->get('text_product_exact');
		$data['text_product_except'] = $this->language->get('text_product_except');
		$data['text_product_except_other'] = $this->language->get('text_product_except_other');
		$data['entry_product'] = $this->language->get('entry_product');
		$data['text_debug'] = $this->language->get('text_debug');

		$data['text_description'] = $this->language->get('text_description');
		$data['text_desc_estimate_popup'] = $this->language->get('text_desc_estimate_popup');
		$data['text_desc_delivery_method'] = $this->language->get('text_desc_delivery_method');
		$data['text_desc_confirmation'] = $this->language->get('text_desc_confirmation');
		$data['text_desc_site_order_detail'] = $this->language->get('text_desc_site_order_detail');
		$data['text_desc_admin_order_detail'] = $this->language->get('text_desc_admin_order_detail');
		$data['text_desc_order_email'] = $this->language->get('text_desc_order_email');
		$data['text_desc_order_invoice'] = $this->language->get('text_desc_order_invoice');
		
		$data['text_manufacturer_rule'] = $this->language->get('text_manufacturer_rule');
		$data['text_manufacturer_any'] = $this->language->get('text_manufacturer_any');
		$data['text_manufacturer_all'] = $this->language->get('text_manufacturer_all');
		$data['text_manufacturer_least'] = $this->language->get('text_manufacturer_least');
		$data['text_manufacturer_least_with_other'] = $this->language->get('text_manufacturer_least_with_other');
		$data['text_manufacturer_exact'] = $this->language->get('text_manufacturer_exact');
		$data['text_manufacturer_except'] = $this->language->get('text_manufacturer_except');
		$data['text_manufacturer_except_other'] = $this->language->get('text_manufacturer_except_other');
		$data['tip_manufacturer_rule'] = $this->language->get('tip_manufacturer_rule');
		
		$data['text_rate_total_method'] = $this->language->get('text_rate_total_method');
		$data['text_rate_sub_total_method'] = $this->language->get('text_rate_sub_total_method');
		$data['text_rate_quantity_method'] = $this->language->get('text_rate_quantity_method');
		$data['text_rate_weight_method'] = $this->language->get('text_rate_weight_method');
		$data['text_rate_volume_method'] = $this->language->get('text_rate_volume_method');
		
		$data['button_save'] = $this->language->get('button_save');
		$data['button_cancel'] = $this->language->get('button_cancel');
		$data['button_save_continue'] = $this->language->get('button_save_continue');
		$data['tab_general'] = $this->language->get('tab_general');
		$data['text_method_remove'] = $this->language->get('text_method_remove');
		$data['text_method_copy'] = $this->language->get('text_method_copy');

		$data['text_group_shipping_mode'] = $this->language->get('text_group_shipping_mode');
		$data['text_no_grouping'] = $this->language->get('text_no_grouping');
		$data['text_lowest'] = $this->language->get('text_lowest');
		$data['text_highest'] = $this->language->get('text_highest');
		$data['text_average'] = $this->language->get('text_average');
		$data['text_sum'] = $this->language->get('text_sum');
		$data['text_and'] = $this->language->get('text_and');
		$data['text_add_new_method'] = $this->language->get('text_add_new_method');
		$data['text_remove'] = $this->language->get('text_remove');
		$data['text_general'] = $this->language->get('text_general');
		$data['text_criteria_setting'] = $this->language->get('text_criteria_setting');
		$data['text_category_product'] = $this->language->get('text_category_product');
		$data['text_price_setting'] = $this->language->get('text_price_setting');
		$data['text_others'] = $this->language->get('text_others');
		$data['text_zip_postal'] = $this->language->get('text_zip_postal');
		$data['text_enter_zip'] = $this->language->get('text_enter_zip');
		$data['text_zip_rule'] = $this->language->get('text_zip_rule');
		$data['text_zip_rule_inclusive'] = $this->language->get('text_zip_rule_inclusive');
		$data['text_zip_rule_exclusive'] = $this->language->get('text_zip_rule_exclusive');
		$data['text_coupon'] = $this->language->get('text_coupon');
		$data['text_enter_coupon'] = $this->language->get('text_enter_coupon');
		$data['text_coupon_rule'] = $this->language->get('text_coupon_rule');
		$data['text_coupon_rule_inclusive'] = $this->language->get('text_coupon_rule_inclusive');
		$data['text_coupon_rule_exclusive'] = $this->language->get('text_coupon_rule_exclusive');
		$data['text_rate_type'] = $this->language->get('text_rate_type');
		$data['text_rate_flat'] = $this->language->get('text_rate_flat');
		$data['text_rate_quantity'] = $this->language->get('text_rate_quantity');
		$data['text_rate_weight'] = $this->language->get('text_rate_weight');
		$data['text_rate_volume'] = $this->language->get('text_rate_volume'); 
		$data['text_rate_total_coupon'] = $this->language->get('text_rate_total_coupon');
		$data['text_rate_total'] = $this->language->get('text_rate_total');
		$data['text_rate_sub_total'] = $this->language->get('text_rate_sub_total');
		$data['text_unit_range'] = $this->language->get('text_unit_range');
		$data['text_delete_all'] = $this->language->get('text_delete_all');
		$data['text_csv_import'] = $this->language->get('text_csv_import');
		$data['text_start'] = $this->language->get('text_start');
		$data['text_end'] = $this->language->get('text_end');
		$data['text_cost'] = $this->language->get('text_cost');
		$data['text_qnty_block'] = $this->language->get('text_qnty_block');
		$data['text_add_new'] = $this->language->get('text_add_new');
		$data['text_final_cost'] = $this->language->get('text_final_cost');
		$data['text_final_single'] = $this->language->get('text_final_single');
		$data['text_final_cumulative'] = $this->language->get('text_final_cumulative');
		$data['text_percentage_related'] = $this->language->get('text_percentage_related');
		$data['text_percent_sub_total'] = $this->language->get('text_percent_sub_total');
		$data['text_percent_total'] = $this->language->get('text_percent_total');
		$data['text_price_adjustment'] = $this->language->get('text_price_adjustment');
		$data['text_price_min'] = $this->language->get('text_price_min');
		$data['text_price_max'] = $this->language->get('text_price_max');
		$data['text_price_add'] = $this->language->get('text_price_add');
		$data['text_days_week'] = $this->language->get('text_days_week');
		$data['text_time_period'] = $this->language->get('text_time_period');
		$data['text_sunday'] = $this->language->get('text_sunday');
		$data['text_monday'] = $this->language->get('text_monday');
		$data['text_tuesday'] = $this->language->get('text_tuesday');
		$data['text_wednesday'] = $this->language->get('text_wednesday');
		$data['text_thursday'] = $this->language->get('text_thursday');
		$data['text_friday'] = $this->language->get('text_friday');
		$data['text_saturday'] = $this->language->get('text_saturday');
		$data['text_country'] = $this->language->get('text_country');
		
		$data['tip_weight_include'] = $this->language->get('tip_weight_include');
		$data['tip_sorting_own'] = $this->language->get('tip_sorting_own');
		$data['tip_status_own'] = $this->language->get('tip_status_own');
		$data['tip_store'] = $this->language->get('tip_store');
		$data['tip_geo'] = $this->language->get('tip_geo');
		$data['tip_manufacturer'] = $this->language->get('tip_manufacturer');
		$data['tip_customer_group'] = $this->language->get('tip_customer_group');
		$data['tip_zip'] = $this->language->get('tip_zip');
		$data['tip_coupon'] = $this->language->get('tip_coupon');
		$data['tip_category'] = $this->language->get('tip_category');
		$data['tip_product'] = $this->language->get('tip_product');
		$data['tip_rate_type'] = $this->language->get('tip_rate_type');
		$data['tip_cost'] = $this->language->get('tip_cost');
		$data['tip_unit_start'] = $this->language->get('tip_unit_start');
		$data['tip_unit_end'] = $this->language->get('tip_unit_end');
		$data['tip_unit_price'] = $this->language->get('tip_unit_price');
		$data['tip_unit_ppu'] = $this->language->get('tip_unit_ppu');
		$data['tip_single_commulative'] = $this->language->get('tip_single_commulative');
		$data['tip_percentage'] = $this->language->get('tip_percentage');
		$data['tip_price_adjust'] = $this->language->get('tip_price_adjust');
		$data['tip_day'] = $this->language->get('tip_day');
		$data['tip_time'] = $this->language->get('tip_time');
		$data['tip_heading'] = $this->language->get('tip_heading');
		$data['tip_status_global'] = $this->language->get('tip_status_global');
		$data['tip_sorting_global'] = $this->language->get('tip_sorting_global');
		$data['tip_grouping'] = $this->language->get('tip_grouping');
		$data['tip_debug'] = $this->language->get('tip_debug');
		$data['tip_desc'] = $this->language->get('tip_desc');
		$data['tip_import'] = $this->language->get('tip_import');
		$data['tip_postal_code'] = $this->language->get('tip_postal_code');
		$data['tip_multi_category'] = $this->language->get('tip_multi_category');
		$data['text_multi_category'] = $this->language->get('text_multi_category');
		$data['entry_all'] = $this->language->get('entry_all');
		$data['entry_any'] = $this->language->get('entry_any');
		$data['tip_group_limit'] = $this->language->get('tip_group_limit');
		$data['text_group_limit'] = $this->language->get('text_group_limit');
		$data['no_unit_row'] = $this->language->get('no_unit_row');
		
		$data['text_partial'] = $this->language->get('text_partial');
		$data['tip_partial'] = $this->language->get('tip_partial');
		$data['text_yes'] = $this->language->get('text_yes');
		$data['text_no'] = $this->language->get('text_no');
		$data['text_additional'] = $this->language->get('text_additional');
		$data['tip_additional'] = $this->language->get('tip_additional');

		$data['text_dimensional_weight'] = $this->language->get('text_dimensional_weight');
		$data['text_dimensional_factor'] = $this->language->get('text_dimensional_factor');
		$data['text_dimensional_overrule'] = $this->language->get('text_dimensional_overrule');
		$data['text_dimensional_weight_method'] = $this->language->get('text_dimensional_weight_method'); 
		$data['text_logo'] = $this->language->get('text_logo');
		$data['tip_text_logo'] = $this->language->get('tip_text_logo');    
		
		$data['text_sort_manual'] = $this->language->get('text_sort_manual'); 
		$data['text_sort_type'] = $this->language->get('text_sort_type'); 
		$data['text_sort_price_asc'] = $this->language->get('text_sort_price_asc'); 
		$data['text_sort_price_desc'] = $this->language->get('text_sort_price_desc'); 
		$data['tip_text_sort_type'] = $this->language->get('tip_text_sort_type'); 
		$data['tab_general_global'] = $this->language->get('tab_general_global');  
		$data['tab_general_general'] = $this->language->get('tab_general_general'); 

		$data['text_export'] = $this->language->get('text_export');   
		$data['tip_export'] = $this->language->get('tip_export');   
		$data['text_import'] = $this->language->get('text_import');   
		$data['tip_import'] = $this->language->get('tip_import');  
		$data['tab_import_export'] = $this->language->get('tab_import_export');    
		$data['error_import'] = $this->language->get('error_import'); 
		$data['text_mask_price'] = $this->language->get('text_mask_price');     

		$data['text_percent_shipping'] = $this->language->get('text_percent_shipping'); 
		$data['text_percent_sub_total_shipping'] = $this->language->get('text_percent_sub_total_shipping'); 
		$data['text_percent_total_shipping'] = $this->language->get('text_percent_total_shipping'); 
		$data['tip_group_name'] = $this->language->get('tip_group_name');
		$data['entry_group_name'] = $this->language->get('entry_group_name'); 

		$data['text_equation'] = $this->language->get('text_equation'); 
		$data['tip_equation'] = $this->language->get('tip_equation'); 
		$data['text_equation_help'] = $this->language->get('text_equation_help'); 
		$data['text_admin_name'] = $this->language->get('text_admin_name');
		$data['text_admin_name_tip'] = $this->language->get('text_admin_name_tip'); 
		$data['text_name_tip'] = $this->language->get('text_name_tip');  
		$data['text_hide'] = $this->language->get('text_hide');  
		$data['text_hide_tip'] = $this->language->get('text_hide_tip'); 
		$data['text_hide_placeholder'] = $this->language->get('text_hide_placeholder');  

		$data['text_option'] = $this->language->get('text_option');
		$data['tip_option'] = $this->language->get('tip_option');
		$data['text_option_any'] = $this->language->get('text_option_any');
		$data['text_option_all'] = $this->language->get('text_option_all');
		$data['text_option_least'] = $this->language->get('text_option_least');
		$data['text_option_least_with_other'] = $this->language->get('text_option_least_with_other');
		$data['text_option_exact'] = $this->language->get('text_option_exact');
		$data['text_option_except'] = $this->language->get('text_option_except');
		$data['text_option_except_other'] = $this->language->get('text_option_except_other');
		$data['entry_option'] = $this->language->get('entry_option');  
		$data['entry_payment'] = $this->language->get('entry_payment');
		$data['tip_payment'] = $this->language->get('tip_payment');
		$data['text_geo_address'] = $this->language->get('text_geo_address');   
		$data['text_delivery'] = $this->language->get('text_delivery');  
		$data['text_payment'] = $this->language->get('text_payment');  

		$data['text_city_rule'] = $this->language->get('text_city_rule'); 
		$data['tip_city'] = $this->language->get('tip_city');  
		$data['text_city'] = $this->language->get('text_city');  
		$data['text_city_enter_tip'] = $this->language->get('text_city_enter_tip'); 
		$data['text_city_enter'] = $this->language->get('text_city_enter'); 
		$data['text_city_rule_inclusive'] = $this->language->get('text_city_rule_inclusive');
		$data['text_city_rule_exclusive'] = $this->language->get('text_city_rule_exclusive');
		$data['text_coupon_tip'] = $this->language->get('text_coupon_tip');
		$data['text_country_tip'] = $this->language->get('text_country_tip');

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->session->data['warning'])) {
			$data['error_warning'] = $this->session->data['warning'];
			unset($this->session->data['warning']);
		} 
		if (isset($this->session->data['success'])) {
			$data['success'] = $this->session->data['success'];
			unset($this->session->data['success']);
		} else {
			$data['success'] = '';
		}
		
		array_walk($data, function(&$value, $key){
			if(strpos($key,'tip') >= 0) {
				$value = str_replace(array("\r", "\n"), '', $value);
			}
		}, $data);

		$data['breadcrumbs'] = array();
		$data['breadcrumbs'][] = array(
			'text'      => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
			);
		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_extension'),
			'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=shipping', true)
			);
		$data['breadcrumbs'][] = array(
			'text'      => $this->language->get('heading_title'),
			'href'      => $this->url->link('extension/shipping/xshippingpro', 'user_token=' . $this->session->data['user_token'], true)
			);
		
		$data['action'] = $this->url->link('extension/shipping/xshippingpro', 'user_token=' . $this->session->data['user_token'], true);
		$data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=shipping', true);
		$data['export'] = $this->url->link('extension/shipping/xshippingpro/export', 'user_token=' . $this->session->data['user_token'], true);
		
		$data['user_token']=$this->session->data['user_token'];

		$data['method_data'] = $this->model_extension_xshippingpro_xshippingpro->getData();
		

		if (isset($this->request->post['shipping_xshippingpro_status'])) {
			$data['shipping_xshippingpro_status'] = $this->request->post['shipping_xshippingpro_status'];
		} else {
			$data['shipping_xshippingpro_status'] = $this->config->get('shipping_xshippingpro_status');
		}
		
		if (isset($this->request->post['shipping_xshippingpro_sort_order'])) {
			$data['shipping_xshippingpro_sort_order'] = $this->request->post['shipping_xshippingpro_sort_order'];
		} else {
			$data['shipping_xshippingpro_sort_order'] = $this->config->get('shipping_xshippingpro_sort_order');
		}

		if (isset($this->request->post['shipping_xshippingpro_group'])) {
			$data['shipping_xshippingpro_group'] = $this->request->post['shipping_xshippingpro_group'];
		} else {
			$data['shipping_xshippingpro_group'] = $this->config->get('shipping_xshippingpro_group');
		}

		if (isset($this->request->post['shipping_xshippingpro_group_limit'])) {
			$data['shipping_xshippingpro_group_limit'] = $this->request->post['shipping_xshippingpro_group_limit'];
		} else {
			$data['shipping_xshippingpro_group_limit'] = $this->config->get('shipping_xshippingpro_group_limit');
		}

		if (isset($this->request->post['shipping_xshippingpro_sorting'])) {
			$data['shipping_xshippingpro_sorting'] = $this->request->post['shipping_xshippingpro_sorting'];
		} else {
			$data['shipping_xshippingpro_sorting'] = $this->config->get('shipping_xshippingpro_sorting');
		}

		if (isset($this->request->post['shipping_xshippingpro_heading'])) {
			$data['shipping_xshippingpro_heading'] = $this->request->post['shipping_xshippingpro_heading'];
		} else {
			$data['shipping_xshippingpro_heading'] = $this->config->get('shipping_xshippingpro_heading');
		}


		if (isset($this->request->post['shipping_xshippingpro_desc_mail'])) {
			$data['shipping_xshippingpro_desc_mail'] = isset($this->request->post['shipping_xshippingpro_desc_mail'])?1:0;
		} else {
			$data['shipping_xshippingpro_desc_mail'] = $this->config->get('shipping_xshippingpro_desc_mail');
		} 


		if (isset($this->request->post['shipping_xshippingpro_debug'])) {
			$data['shipping_xshippingpro_debug'] = $this->request->post['shipping_xshippingpro_debug'];
		} else {
			$data['shipping_xshippingpro_debug'] = $this->config->get('shipping_xshippingpro_debug');
		}

		if (isset($this->request->post['shipping_xshippingpro_sub_group'])) {
			$data['shipping_xshippingpro_sub_group'] = $this->request->post['shipping_xshippingpro_sub_group'];
		} else {
			$data['shipping_xshippingpro_sub_group'] = $this->config->get('shipping_xshippingpro_sub_group');
		}

		if (isset($this->request->post['shipping_xshippingpro_sub_group_limit'])) {
			$data['shipping_xshippingpro_sub_group_limit'] = $this->request->post['shipping_xshippingpro_sub_group_limit'];
		} else {
			$data['shipping_xshippingpro_sub_group_limit'] = $this->config->get('shipping_xshippingpro_sub_group_limit');
		}

		if (isset($this->request->post['shipping_xshippingpro_sub_group_name'])) {
			$data['shipping_xshippingpro_sub_group_name'] = $this->request->post['shipping_xshippingpro_sub_group_name'];
		} else {
			$data['shipping_xshippingpro_sub_group_name'] = $this->config->get('shipping_xshippingpro_sub_group_name');
		}


		$this->load->model('localisation/tax_class');
		$data['tax_classes'] = $this->model_localisation_tax_class->getTaxClasses();
		
		$this->load->model('localisation/geo_zone');
		$data['geo_zones'] = $this->model_localisation_geo_zone->getGeoZones();

		$this->load->model('setting/store');
		$data['stores'] = $this->model_setting_store->getStores();
		$data['stores']=  array_merge(array(array('store_id'=>0,'name'=>$this->language->get('store_default'))),$data['stores']);


		if(intval(str_replace('.','',VERSION)) >=  2101) {
			$this->load->model('customer/customer_group');
			$data['customer_groups'] = $this->model_customer_customer_group->getCustomerGroups();
		} else {
			$this->load->model('sale/customer_group');
			$data['customer_groups'] = $this->model_sale_customer_group->getCustomerGroups();
		}

		$this->load->model('catalog/manufacturer');
		$data['manufacturers'] = $this->model_catalog_manufacturer->getManufacturers();

		if(count($data['manufacturers']) > 1000) {
			$data['manufacturers'] = array_splice($data['manufacturers'], 0, 1000);
		}
		
		/* Payment rule*/
		$payment_mods=array();
		$xpayment_installed=false;
		$result=$this->db->query("select * from " . DB_PREFIX . "extension where type='payment'");
		if($result->rows){
			foreach($result->rows as $row){
				$payment_mods[$row['code']]=$this->getModuleName($row['code'],$row['type']);  
				if($row['code']=='xpayment') $xpayment_installed=true;
			}
		}
		
		$data['payment_mods'] = $payment_mods;
		
		/*For X-Payment*/
		$xpayments=array();
		if($xpayment_installed){
			$language_id=$this->config->get('config_language_id');
			$xpayment=$this->config->get('xpayment');
			if($xpayment){
				$xpayment=unserialize(base64_decode($xpayment));
			}

			if(!isset($xpayment['name']))$xpayment['name']=array();
			if(!is_array($xpayment['name']))$xpayment['name']=array();

			$xpayment_methods=array();
			foreach($xpayment['name'] as $no_of_tab=>$names){

				if(isset($names[$language_id]) && $names[$language_id]){
					$code = 'xpayment'.'.xpayment'.$no_of_tab;
					$xpayment_methods[$code]=$names[$language_id];
				}
			}
			$xpayments['xpayment'] = $xpayment_methods;
		}
		$data['xpayments'] = $xpayments;
		/*End of X-Payment*/

		$this->load->model('localisation/language');
		$data['languages'] = $this->model_localisation_language->getLanguages();
		$data['language_id']=$this->config->get('config_language_id');

		$data['language_dir'] = 'view/image/flags/';

		if(intval(str_replace('.','',VERSION)) >= 2200) { 
			$data['language_dir'] = 'language/';

			foreach($data['languages'] as $inc=>$language) {
				$data['languages'][$inc]['image'] = $language['code'].'/'.$language['code'].'.png'; 
			}
		}

		$this->load->model('localisation/country');
		$data['countries'] = $this->model_localisation_country->getCountries();

		$data['group_options']=array('no_group'=>$this->language->get('text_no_grouping'),'lowest'=>$this->language->get('text_lowest'),'highest'=>$this->language->get('text_highest'),'average'=>$this->language->get('text_average'),'sum'=>$this->language->get('text_sum'),'and'=>$this->language->get('text_and'));
		$data['sort_options']=array('1'=>$this->language->get('text_sort_manual'),'2'=>$this->language->get('text_sort_price_asc'),'3'=>$this->language->get('text_sort_price_desc'));       

		$data['text_group_none']=$this->language->get('text_group_none');
		$data['entry_group']=$this->language->get('entry_group'); 
		$data['entry_group_tip']=$this->language->get('entry_group_tip');  
		$data['text_group_name']=$this->language->get('text_group_name'); 
		$data['text_group_type']=$this->language->get('text_group_type');   
		$data['text_method_group']=$this->language->get('text_method_group'); 
		$data['tip_method_group']=$this->language->get('tip_method_group');

		/* default values of global setting*/
		if (!$data['shipping_xshippingpro_group']) $data['shipping_xshippingpro_group'] = 'no_group';
		if (!$data['shipping_xshippingpro_heading']) {
			foreach ($data['languages'] as $key => $value) {
				$data['shipping_xshippingpro_heading'][$value['language_id']] = 'Способы доставки ';
			}
		} 

		$data['shipping_xshippingpro_sub_groups_count']=10;
		$data['tpl']= $this->getFormData($data, true);
		$data['sub_groups'] = $this->getSubGroups($data);
		
		$data['methods']=$this->getMethodList($data);
		$data['form_data']=$this->getFormData($data);
		
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');
		
		$this->response->setOutput($this->load->view('extension/shipping/xshippingpro', $data));
	}

	public function quick_save() {

		$this->load->language('extension/shipping/xshippingpro');
		$this->load->model('extension/xshippingpro/xshippingpro');
		$json=array();
		
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			
			$save=array();
			if(isset($this->request->post['xshippingpro']) && isset($this->request->get['tab_id'])) {
				$save['method_data']=base64_encode(serialize($this->request->post['xshippingpro']));
				$save['tab_id'] = $this->request->get['tab_id'];
				$this->model_extension_xshippingpro_xshippingpro->addData($save);
				$json['success']=1;
			}
			else {
				$json['error']='error! - unable to save';
			}

		} else{

			$json['error']=$this->language->get('error_permission');
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json)); 
	} 

	public function save_general(){

		$this->load->language('extension/shipping/xshippingpro');
		$this->load->model('setting/setting');
		$json=array();

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			
			$save=array();
			$save['shipping_xshippingpro_status']=$this->request->post['shipping_xshippingpro_status'];
			$save['shipping_xshippingpro_group']=$this->request->post['shipping_xshippingpro_group'];
			$save['shipping_xshippingpro_group_limit']=$this->request->post['shipping_xshippingpro_group_limit'];
			$save['shipping_xshippingpro_heading']=$this->request->post['shipping_xshippingpro_heading'];
			$save['shipping_xshippingpro_sort_order']=$this->request->post['shipping_xshippingpro_sort_order']; 
			$save['shipping_xshippingpro_desc_mail']=isset($this->request->post['shipping_xshippingpro_desc_mail'])?1:0;
			$save['shipping_xshippingpro_debug']=$this->request->post['shipping_xshippingpro_debug'];
			$save['shipping_xshippingpro_sorting']=$this->request->post['shipping_xshippingpro_sorting'];
			$save['shipping_xshippingpro_sub_group']=$this->request->post['shipping_xshippingpro_sub_group'];
			$save['shipping_xshippingpro_sub_group_limit']=$this->request->post['shipping_xshippingpro_sub_group_limit'];
			$save['shipping_xshippingpro_sub_group_name']=$this->request->post['shipping_xshippingpro_sub_group_name'];

			if(isset($this->request->post['shipping_xshippingpro_status'])) {
				$this->model_setting_setting->editSetting('shipping_xshippingpro', $save);
				$json['success']=1;
			}
			else {
				$json['error']='error! - unable to save';
			}

		} else{
			$json['error']=$this->language->get('error_permission');
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json)); 

	}

	public function delete() {

		$this->load->language('extension/shipping/xshippingpro');
		$this->load->model('extension/xshippingpro/xshippingpro');
		$json=array();

		if (($this->request->server['REQUEST_METHOD'] == 'GET') && $this->validate()) {

			if($this->request->get['tab_id']) {
				$this->model_extension_xshippingpro_xshippingpro->deleteData($this->request->get['tab_id']);
				$json['success']=1;
			}
			else {
				$json['error']='error! - unable to delete';
			}

		} else{
			$json['error']=$this->language->get('error_permission');
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json)); 
	} 

	public function export(){

		$export = array();

		if(isset($this->request->get['no'])) {
			$this->exportMethod($this->request->get['no']);
		}

		$this->load->model('extension/xshippingpro/xshippingpro');

		$export['method_data'] = $this->model_extension_xshippingpro_xshippingpro->getData();

		if (isset($this->request->post['shipping_xshippingpro_status'])) {
			$export['shipping_xshippingpro_status'] = $this->request->post['shipping_xshippingpro_status'];
		} else {
			$export['shipping_xshippingpro_status'] = $this->config->get('shipping_xshippingpro_status');
		}
		
		if (isset($this->request->post['shipping_xshippingpro_sort_order'])) {
			$export['shipping_xshippingpro_sort_order'] = $this->request->post['shipping_xshippingpro_sort_order'];
		} else {
			$export['shipping_xshippingpro_sort_order'] = $this->config->get('shipping_xshippingpro_sort_order');
		}

		if (isset($this->request->post['shipping_xshippingpro_group'])) {
			$export['shipping_xshippingpro_group'] = $this->request->post['shipping_xshippingpro_group'];
		} else {
			$export['shipping_xshippingpro_group'] = $this->config->get('shipping_xshippingpro_group');
		}

		if (isset($this->request->post['shipping_xshippingpro_group_limit'])) {
			$export['shipping_xshippingpro_group_limit'] = $this->request->post['shipping_xshippingpro_group_limit'];
		} else {
			$export['shipping_xshippingpro_group_limit'] = $this->config->get('shipping_xshippingpro_group_limit');
		}

		if (isset($this->request->post['shipping_xshippingpro_sorting'])) {
			$export['shipping_xshippingpro_sorting'] = $this->request->post['shipping_xshippingpro_sorting'];
		} else {
			$export['shipping_xshippingpro_sorting'] = $this->config->get('shipping_xshippingpro_sorting');
		}

		if (isset($this->request->post['shipping_xshippingpro_heading'])) {
			$export['shipping_xshippingpro_heading'] = $this->request->post['shipping_xshippingpro_heading'];
		} else {
			$export['shipping_xshippingpro_heading'] = $this->config->get('shipping_xshippingpro_heading');
		}


		if (isset($this->request->post['shipping_xshippingpro_desc_mail'])) {
			$export['shipping_xshippingpro_desc_mail'] = isset($this->request->post['shipping_xshippingpro_desc_mail'])?1:0;
		} else {
			$export['shipping_xshippingpro_desc_mail'] = $this->config->get('shipping_xshippingpro_desc_mail');
		} 


		if (isset($this->request->post['shipping_xshippingpro_debug'])) {
			$export['shipping_xshippingpro_debug'] = $this->request->post['shipping_xshippingpro_debug'];
		} else {
			$export['shipping_xshippingpro_debug'] = $this->config->get('shipping_xshippingpro_debug');
		}

		if (isset($this->request->post['shipping_xshippingpro_sub_group'])) {
			$export['shipping_xshippingpro_sub_group'] = $this->request->post['shipping_xshippingpro_sub_group'];
		} else {
			$export['shipping_xshippingpro_sub_group'] = $this->config->get('shipping_xshippingpro_sub_group');
		}

		if (isset($this->request->post['shipping_xshippingpro_sub_group_limit'])) {
			$export['shipping_xshippingpro_sub_group_limit'] = $this->request->post['shipping_xshippingpro_sub_group_limit'];
		} else {
			$export['shipping_xshippingpro_sub_group_limit'] = $this->config->get('shipping_xshippingpro_sub_group_limit');
		}

		if (isset($this->request->post['shipping_xshippingpro_sub_group_name'])) {
			$export['shipping_xshippingpro_sub_group_name'] = $this->request->post['shipping_xshippingpro_sub_group_name'];
		} else {
			$export['shipping_xshippingpro_sub_group_name'] = $this->config->get('shipping_xshippingpro_sub_group_name');
		}

		$out = base64_encode(serialize($export));  
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header("Content-Length: " . strlen($out));
		header("Content-type: text/txt");
		header("Content-Disposition: attachment; filename=xshippingpro.txt");
		echo $out;
		exit;
	} 

	public function exportMethod($tab_id) {

		$this->load->model('extension/xshippingpro/xshippingpro');

		$method_row = $this->model_extension_xshippingpro_xshippingpro->getDataByTabId($tab_id);
		
		if(!$method_row) return false;
		
		$method_data = $method_row['method_data'];
		$method_data = @unserialize(@base64_decode($method_data));
		if(!is_array($method_data)) $method_data = array();


		$csv_terminated = "\n";
		$csv_separator = ",";
		$csv_enclosed = '"';
		$csv_escaped = "\\";
		$out="";
		
		$heading = array('Start','End','Cost','Per Unit Block','Allow Partial');
		foreach($heading as $head) {		
			$out .= $csv_enclosed . str_replace($csv_enclosed, $csv_escaped . $csv_enclosed,			
				stripslashes($head)) . $csv_enclosed;			
			$out .= $csv_separator;

		}
		
		$out= rtrim($out,$csv_separator);		
		$out .= $csv_terminated;

		$language_id = $this->config->get('config_language_id');
		$method_name = (!isset($method_data['name'][$language_id]) || !$method_data['name'][$language_id]) ? 'Untitled Method '.$no_of_tab : $method_data['name'][$language_id]; 

		if(isset($method_data['rate_start']) && is_array($method_data['rate_start'])) {

			foreach ($method_data['rate_start'] as $inc=>$rate_start) { 

				if(!isset($method_data['rate_partial'][$inc])) $method_data['rate_partial'][$inc]='0'; 

				$out .= $csv_enclosed . str_replace($csv_enclosed, $csv_escaped . $csv_enclosed,			
					stripslashes($rate_start)) . $csv_enclosed;			
				$out .= $csv_separator;

				$out .= $csv_enclosed . str_replace($csv_enclosed, $csv_escaped . $csv_enclosed,			
					stripslashes($method_data['rate_end'][$inc])) . $csv_enclosed;			
				$out .= $csv_separator;

				$out .= $csv_enclosed . str_replace($csv_enclosed, $csv_escaped . $csv_enclosed,			
					stripslashes($method_data['rate_total'][$inc])) . $csv_enclosed;			
				$out .= $csv_separator;

				$out .= $csv_enclosed . str_replace($csv_enclosed, $csv_escaped . $csv_enclosed,			
					stripslashes($method_data['rate_block'][$inc])) . $csv_enclosed;			
				$out .= $csv_separator;

				$out .= $csv_enclosed . str_replace($csv_enclosed, $csv_escaped . $csv_enclosed,			
					stripslashes($method_data['rate_partial'][$inc])) . $csv_enclosed;			

				$out .= $csv_terminated;
			}
		}	

		$filename = str_replace(array('#',' ',"'",'"','!','@','#','$','%','^','&','*','(',')','~','`'),'_',$method_name).'.csv'; 

		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header("Content-Length: " . strlen($out));
		header("Content-type: text/x-csv");
		header("Content-Disposition: attachment; filename=$filename");
		echo $out;
		exit;

	}


	public function import(){

		$this->load->model('setting/setting');
		$this->load->model('extension/xshippingpro/xshippingpro');
		$success = false;

		if ($this->request->server['REQUEST_METHOD'] == 'POST' && is_uploaded_file($this->request->files['file_import']['tmp_name']) && file_exists($this->request->files['file_import']['tmp_name'])) {
			
			$import_data = file_get_contents($this->request->files['file_import']['tmp_name']);
			if($import_data) {

				$import_data=@unserialize(@base64_decode($import_data));


				if(is_array($import_data) && (isset($import_data['method_data']) || isset($import_data['xshippingpro']))) {

					$save=array();

					if (isset($import_data['shipping_xshippingpro_status'])) {
						$save['shipping_xshippingpro_status']= $import_data['shipping_xshippingpro_status'];
					} else if (isset($import_data['xshippingpro_status'])) {
						$save['shipping_xshippingpro_status']= $import_data['xshippingpro_status'];
					} else {
						$save['shipping_xshippingpro_status'] = 1; 
					}

					if (isset($import_data['shipping_xshippingpro_group'])) {
						$save['shipping_xshippingpro_group']= $import_data['shipping_xshippingpro_group'];
					} else if (isset($import_data['xshippingpro_group'])) {
						$save['shipping_xshippingpro_group']= $import_data['xshippingpro_group'];
					}

					if (isset($import_data['shipping_xshippingpro_group_limit'])) {
						$save['shipping_xshippingpro_group_limit']= $import_data['shipping_xshippingpro_group_limit'];
					} else if (isset($import_data['xshippingpro_group_limit'])) {
						$save['shipping_xshippingpro_group_limit']= $import_data['xshippingpro_group_limit'];
					}

					if (isset($import_data['shipping_xshippingpro_heading'])) {
						$save['shipping_xshippingpro_heading']= $import_data['shipping_xshippingpro_heading'];
					} else if (isset($import_data['xshippingpro_heading'])) {
						$save['shipping_xshippingpro_heading']= $import_data['xshippingpro_heading'];
					}

					if (isset($import_data['shipping_xshippingpro_sort_order'])) {
						$save['shipping_xshippingpro_sort_order']= $import_data['shipping_xshippingpro_sort_order'];
					} else if (isset($import_data['xshippingpro_sort_order'])) {
						$save['shipping_xshippingpro_sort_order']= $import_data['xshippingpro_sort_order'];
					}

					if (isset($import_data['shipping_xshippingpro_desc_mail'])) {
						$save['shipping_xshippingpro_desc_mail']= 1;
					} else if (isset($import_data['xshippingpro_desc_mail'])) {
						$save['shipping_xshippingpro_desc_mail']= 1;
					} else {
						$save['shipping_xshippingpro_desc_mail']= 0;
					}


					if (isset($import_data['shipping_xshippingpro_debug'])) {
						$save['shipping_xshippingpro_debug']= $import_data['shipping_xshippingpro_debug'];
					} else if (isset($import_data['xshippingpro_debug'])) {
						$save['shipping_xshippingpro_debug']= $import_data['xshippingpro_debug'];
					}

					if (isset($import_data['shipping_xshippingpro_sorting'])) {
						$save['shipping_xshippingpro_sorting']= $import_data['shipping_xshippingpro_sorting'];
					} else if (isset($import_data['xshippingpro_sorting'])) {
						$save['shipping_xshippingpro_sorting']= $import_data['xshippingpro_sorting'];
					}

					if (isset($import_data['shipping_xshippingpro_sub_group'])) {
						$save['shipping_xshippingpro_sub_group']= $import_data['shipping_xshippingpro_sub_group'];
					} else if (isset($import_data['xshippingpro_sub_group'])) {
						$save['shipping_xshippingpro_sub_group']= $import_data['xshippingpro_sub_group'];
					}

					if (isset($import_data['shipping_xshippingpro_sub_group_limit'])) {
						$save['shipping_xshippingpro_sub_group_limit']= $import_data['shipping_xshippingpro_sub_group_limit'];
					} else if (isset($import_data['xshippingpro_sub_group_limit'])) {
						$save['shipping_xshippingpro_sub_group_limit']= $import_data['xshippingpro_sub_group_limit'];
					}

					if (isset($import_data['shipping_xshippingpro_sub_group_name'])) {
						$save['shipping_xshippingpro_sub_group_name']= $import_data['shipping_xshippingpro_sub_group_name'];
					} else if (isset($import_data['xshippingpro_sub_group_name'])) {
						$save['shipping_xshippingpro_sub_group_name']= $import_data['xshippingpro_sub_group_name'];
					}

					$this->model_setting_setting->editSetting('shipping_xshippingpro', $save);

					if (isset($import_data['xshippingpro']) && $import_data['xshippingpro']) {
						$this->latencyImport($import_data['xshippingpro']);
					}

					if (isset($import_data['method_data']) && $import_data['method_data'] && is_array($import_data['method_data'])) {
						foreach($import_data['method_data'] as $single) {
							$this->model_extension_xshippingpro_xshippingpro->addData($single);
						}
					}
					$success = true;

				}
			}		

		} 

		if($success) {
			$this->session->data['success'] = $this->language->get('text_success');
		} else {
			$this->session->data['warning'] = $this->language->get('error_import');
		}
		
	}

	public function latencyImport($data) {
		
		$this->load->model('extension/xshippingpro/xshippingpro');
		
		if ($data) {
			$data=unserialize(base64_decode($data)); 
		}
		
		if (!is_array($data)) $data=array();

		$methods = array();
		foreach($data as $key=>$value) {
			if($value  && is_array($value)) {
				foreach ($value as $tab_id => $field_value) {
					$methods[$tab_id][$key] = $field_value;
				}
			}
	    }

	    foreach ($methods as $tab_id => $method_data) {
	    	$save = array();
	    	$save['method_data']=base64_encode(serialize($method_data));
			$save['tab_id'] = $tab_id;
			$this->model_extension_xshippingpro_xshippingpro->addData($save);
	    }

	}     

	public function csv_upload(){

		ini_set('auto_detect_line_endings', true);
		$this->load->language('extension/shipping/xshippingpro');

		$json = array();
		if (!empty($this->request->files['file']['name'])) {
			$filename = basename(preg_replace('/[^a-zA-Z0-9\.\-\s+]/', '', html_entity_decode($this->request->files['file']['name'], ENT_QUOTES, 'UTF-8')));

			$allowed=  array('csv');
			if (!in_array(substr(strrchr($filename, '.'), 1), $allowed)) {
				$json['error'] = $this->language->get('error_filetype');
			}
			if ($this->request->files['file']['error'] != UPLOAD_ERR_OK) {
				$json['error'] = $this->language->get('error_partial');
			}
		}
		else{
			$json['error']=$this->language->get('error_upload');  
		}

		if (!$json && is_uploaded_file($this->request->files['file']['tmp_name']) && file_exists($this->request->files['file']['tmp_name'])) {

			$isFound=false;
			$json['data']=array();
			if (($handle = fopen($this->request->files['file']['tmp_name'], "r")) !== FALSE) {
				while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
					$start=$data[0];  
					$end=$data[1]; 
					$cost=$data[2]; 
					$pg=isset($data[3])?$data[3]:0; 
					$pa=isset($data[4])?$data[4]:0; 
					if(is_numeric($start) && is_numeric($end) && is_numeric($cost)){
						$json['data'][]=array('start'=>(float)$start,'end'=>(float)$end,'cost'=>(float)$cost,'pg'=>(float)$pg,'pa'=>(int)$pa); 
						$isFound=true;
					}
				}
				fclose($handle);
			}
			if(!$isFound)$json['error']=$this->language->get('error_no_data');     
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json)); 

	}

	private function validate() {
		$this->load->language('extension/shipping/xshippingpro');
		if (!$this->user->hasPermission('modify', 'extension/shipping/xshippingpro')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}
		
		if (!$this->error) {
			return true;
		} else {
			return false;
		}	
	}
	
	public function copyMehthod()
	{
		$tabId=$this->requrest->get['tabId'];
	}
	
	public function install(){
		$this->load->model('extension/xshippingpro/xshippingpro');
		$this->model_extension_xshippingpro_xshippingpro->install();
	}

	public function uninstall(){        
		$this->load->model('extension/xshippingpro/xshippingpro');
		$this->model_extension_xshippingpro_xshippingpro->uninstall();
	}

	private function getFormData($data, $new_tab = false)
	{
		$this->load->model('catalog/category');
		$this->load->model('catalog/product');
		$this->load->model('catalog/option');

		if ($new_tab) {
			$data['method_data'] = array(
				array('tab_id' => '__INDEX__', 'method_data' => '')
				);	
		}

		$defaul_values = $this->getInitialValues();

		$return='';
		foreach($data['method_data'] as $single_method) {
			$no_of_tab = $single_method['tab_id'];
			$method_data = $single_method['method_data'];
			$method_data = @unserialize(@base64_decode($method_data));
			if(!is_array($method_data)) $method_data = array();

			if ($new_tab) $method_data = $this->getDefaultValues();

			$method_data = array_merge($defaul_values, $method_data);

			$return.='<div id="shipping-'.$no_of_tab.'" class="tab-pane shipping">'
			.'<div class="form-group display-name-row">'
			.'<label class="col-sm-2 control-label" for="input-display'.$no_of_tab.'"><span data-toggle="tooltip" title="'.$data['text_admin_name_tip'].'">'.$data['text_admin_name'].' </span></label>'
			.'<div class="col-sm-10">'
			.'<input style="width:250px" type="text" name="xshippingpro[display]" value="'.$method_data['display'].'" class="form-control display-name" id="input-display'.$no_of_tab.'" />'
			.'<div class="action-btn">'
			.'<button class="btn btn-warning btn-copy" data-toggle="tooltip" type="button" data-original-title="'.$data['text_method_copy'].'"><i class="fa fa-copy"></i></button>'
			.'<button class="btn btn-danger btn-delete" data-toggle="tooltip" type="button" data-original-title="'.$data['text_method_remove'].'"><i class="fa fa-trash-o"></i></button>'
			.'</div>'
			.'</div>'
			.'</div>'
			.'<ul class="nav nav-tabs" id="language'.$no_of_tab.'">';

			$inc=0; 
			foreach ($data['languages'] as $language) { 
				$active_cls=($inc==0)?'class="active"':''; 
				$inc++;
				$return.='<li '.$active_cls.' ><a href="#language'.$language['language_id'].'_'.$no_of_tab.'" data-toggle="tab"><img src="'.$data['language_dir'].$language['image'].'" title="'.$language['name'].'" /> '.$language['name'].'</a></li>';
			} 
			$return.='</ul>'
			.'<div class="tab-content">';

			$inc=0;
			foreach ($data['languages'] as $language) { 
				$active_cls=($inc==0)?' active':''; 
				$lang_cls=($inc==0)?'':'-lang'; $inc++; 
				if(!isset($method_data['name'][$language['language_id']]) || !$method_data['name'][$language['language_id']])$method_data['name'][$language['language_id']]='Untitled Method '.$no_of_tab; 
				if(!isset($method_data['desc'][$language['language_id']]) || !$method_data['desc'][$language['language_id']])$method_data['desc'][$language['language_id']]='';
				
				$return.='<div class="tab-pane'.$active_cls.'" id="language'.$language['language_id'].'_'.$no_of_tab.'">'
				.'<div class="form-group required">'
				.'<label class="col-sm-2 control-label" for="lang-name-'.$no_of_tab.''.$language['language_id'].'"><span data-toggle="tooltip" title="'.$data['text_name_tip'].'">'.$data['entry_name'].'</span></label>'
				.'<div class="col-sm-10">'
				.'<input type="text" name="xshippingpro[name]['.$language['language_id'].']" value="'.$method_data['name'][$language['language_id']].'" placeholder="'.$data['entry_name'].'" id="lang-name-'.$no_of_tab.''.$language['language_id'].'" class="form-control method-name'.$lang_cls.'" />'
				.'</div>'
				.'</div>'
				.'<div class="form-group">'
				.'<label class="col-sm-2 control-label" for="lang-desc-'.$no_of_tab.''.$language['language_id'].'"><span data-toggle="tooltip" title="'.$data['tip_desc'].'">'.$data['entry_desc'].' </span></label>'
				.'<div class="col-sm-10">'
				.'<input type="text" name="xshippingpro[desc]['.$language['language_id'].']" value="'.$method_data['desc'][$language['language_id']].'" placeholder="'.$data['entry_desc'].'" id="lang-desc-'.$no_of_tab.''.$language['language_id'].'" class="form-control" />'
				.'</div>'
				.'</div>'
				.'</div>';
			} 
			$return.='</div>'
			.'<ul class="nav nav-tabs method-tab" id="method-tab-'.$no_of_tab.'">'
			.'<li class="active"><a href="#common_'.$no_of_tab.'" data-toggle="tab">'.$data['text_general'].'</a></li>'
			.'<li><a href="#criteria_'.$no_of_tab.'" data-toggle="tab">'.$data['text_criteria_setting'].'</a></li>'
			.'<li><a href="#catprod_'.$no_of_tab.'" data-toggle="tab">'.$data['text_category_product'].'</a></li>'
			.'<li><a href="#price_'.$no_of_tab.'" data-toggle="tab">'.$data['text_price_setting'].'</a></li>'
			.'<li><a href="#other_'.$no_of_tab.'" data-toggle="tab">'.$data['text_others'].'</a></li>'
			.'</ul>' 
			.'<div class="tab-content method-content">'
			.'<div class="tab-pane active" id="common_'.$no_of_tab.'">'
			.'<div class="form-group">'
			.'<label class="col-sm-2 control-label" for="input-weight'.$no_of_tab.'"><span data-toggle="tooltip" title="'.$data['tip_weight_include'].'">'.$data['entry_weight_include'].'</span></label>'
			.'<div class="col-sm-10"><input '.(($method_data['inc_weight']=='1')?'checked="checked"':'').' type="checkbox" name="xshippingpro[inc_weight]" value="1" id="input-weight'.$no_of_tab.'" /></div>'
			.'</div>'
			.'<div class="form-group">'
			.'<label class="col-sm-2 control-label" for="input-tax-class'.$no_of_tab.'">'.$data['entry_tax'].'</label>'
			.'<div class="col-sm-10"><select id="input-tax-class'.$no_of_tab.'" name="xshippingpro[tax_class_id]" class="form-control" >'
			.'<option value="0">'.$data['text_none'].'</option>';

			foreach ($data['tax_classes'] as $tax_class) { 
				$return.='<option '.(($method_data['tax_class_id']==$tax_class['tax_class_id'])?'selected':'').' value="'.$tax_class['tax_class_id'].'">'.$tax_class['title'].'</option>';
			} 
			$return.='</select></div>'
			.'</div>'
			.'<div class="form-group">'
			.'<label class="col-sm-2 control-label" for="input-logo'.$no_of_tab.'"><span data-toggle="tooltip" title="'.$data['tip_text_logo'].'">'.$data['text_logo'].' </span></label>'
			.'<div class="col-sm-10"><input type="text" name="xshippingpro[logo]" value="'.$method_data['logo'].'" class="form-control" id="input-logo'.$no_of_tab.'" /></div>'
			.'</div>'
			.'<div class="form-group">'
			.'<label class="col-sm-2 control-label" for="input-sortorder'.$no_of_tab.'"><span data-toggle="tooltip" title="'.$data['tip_sorting_own'].'">'.$data['entry_sort_order'].' </span></label>'
			.'<div class="col-sm-10"><input type="text" name="xshippingpro[sort_order]" value="'.$method_data['sort_order'].'" class="form-control" id="input-sortorder'.$no_of_tab.'" /></div>'
			.'</div>'
			.'<div class="form-group">'
			.'<label class="col-sm-2 control-label" for="input-status'.$no_of_tab.'"><span data-toggle="tooltip" title="'.$data['tip_status_own'].'">'.$data['entry_status'].'</span></label>'
			.'<div class="col-sm-10"><select class="form-control" id="input-status'.$no_of_tab.'" name="xshippingpro[status]">'
			.'<option value="1" '.(($method_data['status']==1 || $method_data['status']=='')?'selected':'').'>'.$data['text_enabled'].'</option>'
			.'<option value="0" '.(($method_data['status']==0)?'selected':'').'>'.$data['text_disabled'].'</option>'
			.'</select></div>'
			.'</div>'
			.'<div class="form-group">'
			.'<label class="col-sm-2 control-label" for="input-group'.$no_of_tab.'"><span data-toggle="tooltip" title="'.$data['entry_group_tip'].'">'.$data['entry_group'].'</span></label>'
			.'<div class="col-sm-10"><select class="form-control" id="input-group'.$no_of_tab.'" name="xshippingpro[group]">'
			.'<option value="0">'.$data['text_group_none'].'</option>';

			for($sg=1; $sg<=$data['shipping_xshippingpro_sub_groups_count'];$sg++) { 
				$return.='<option '.(($method_data['group']==$sg)?'selected':'').' value="'.$sg.'">Group'.$sg.'</option>';
			} 
			$return.='</select></div>'
			.'</div>'
			.'</div>'
			.'<div class="tab-pane" id="criteria_'.$no_of_tab.'">'
			.'<div class="form-group">'
			.'<label class="col-sm-2 control-label"><span data-toggle="tooltip" title="'.$data['tip_store'].'">'.$data['entry_store'].'</span></label>' 
			.'<div class="col-sm-10">'
			.'<label class="any-class"><input '.(($method_data['store_all']=='1')?'checked="checked"':'').' type="checkbox" name="xshippingpro[store_all]" class="choose-any" value="1" />&nbsp;'.$data['text_any'].'</label>'
			.'<div class="well well-sm" style="height: 70px; overflow: auto;'.(($method_data['store_all']!='1')?'display:block':'').'">'
			.'<div class="checkbox xshipping-checkbox">';

			foreach ($data['stores'] as $store) {
				$return.='<label>'
				.'<input '.((in_array($store['store_id'],$method_data['store']))?'checked':'').' type="checkbox" name="xshippingpro[store][]" value="'.$store['store_id'].'" />'.$store['name'].''
				.'</label>';
			} 
			$return.='</div>'
			.'</div>'
			.'</div>'
			.'</div>'

			.'<div class="form-group">'
			.'<label class="col-sm-2 control-label"><span data-toggle="tooltip" title="'.$data['tip_geo'].'">'.$data['entry_geo_zone'].'</span></label>' 
			.'<div class="col-sm-10">'
			.'<label class="any-class"><input '.(($method_data['geo_zone_all']=='1')?'checked="checked"':'').' type="checkbox" name="xshippingpro[geo_zone_all]" class="choose-any" value="1" />&nbsp;'.$data['text_any'].'</label>'
			.'<div class="well well-sm" style="height: 100px; overflow: auto;'.(($method_data['geo_zone_all']!='1')?'display:block':'').'">'
			.'<div class="checkbox xshipping-checkbox">';

			foreach ($data['geo_zones'] as $geo_zone) {

				$return.='<label>'
				.'<input '.((in_array($geo_zone['geo_zone_id'],$method_data['geo_zone_id']))?'checked':'').' type="checkbox" name="xshippingpro[geo_zone_id][]" value="'.$geo_zone['geo_zone_id'].'" />'.$geo_zone['name'].''
				.'</label>';
			} 
			$return.='</div>'
			.'</div>'
			.'</div>'
			.'</div>'

			.'<div class="form-group">'
			.'<label class="col-sm-2 control-label" for="input-city'.$no_of_tab.'"><span data-toggle="tooltip" title="'.$data['tip_city'].'">'.$data['text_city'].'</span></label>' 
			.'<div class="col-sm-10">'
			.'<label class="any-class"><input '.(($method_data['city_all']=='1')?'checked="checked"':'').' type="checkbox" name="xshippingpro[city_all]" class="choose-any-with" rel="city-option" value="1" id="input-city'.$no_of_tab.'" />'.$data['text_any'].'</label>'
			.'</div>'
			.'</div>'
			.'<div class="form-group city-option" '.(($method_data['city_all']!='1')?'style="display:block"':'').'>'
			.'<label class="col-sm-2 control-label" for="input-city_data'.$no_of_tab.'"><span data-toggle="tooltip" title="'.$data['text_city_enter_tip'].'">'.$data['text_city_enter'].'</span></label>'
			.'<div class="col-sm-10"><textarea class="form-control" id="input-city_data'.$no_of_tab.'" name="xshippingpro[city]" rows="8" cols="70" />'.$method_data['city'].'</textarea></div>'
			.'</div>'
			.'<div class="form-group city-option" '.(($method_data['city_all']!='1')?'style="display:block"':'').'>'
			.'<label class="col-sm-2 control-label" for="input-city-rule'.$no_of_tab.'">'.$data['text_city_rule'].'</label>'
			.'<div class="col-sm-10"><select class="form-control" id="input-city-rule'.$no_of_tab.'" name="xshippingpro[city_rule]">'
			.'<option value="inclusive" '.(($method_data['city_rule']=='inclusive')?'selected':'').'>'.$data['text_city_rule_inclusive'].'</option>'
			.'<option value="exclusive" '.(($method_data['city_rule']=='exclusive')?'selected':'').'>'.$data['text_city_rule_exclusive'].'</option>'
			.'</select></div>'
			.'</div>' 

			.'<div class="form-group">'
			.'<label class="col-sm-2 control-label"><span data-toggle="tooltip" title="'.$data['text_country_tip'].'">'.$data['text_country'].'</span></label>' 
			.'<div class="col-sm-10">'
			.'<label class="any-class"><input '.(($method_data['country_all']=='1')?'checked="checked"':'').' type="checkbox" name="xshippingpro[country_all]" class="choose-any" value="1" />&nbsp;'.$data['text_any'].'</label>'
			.'<div class="well well-sm" style="height: 115px; overflow: auto;'.(($method_data['country_all']!='1')?'display:block':'').'"><div class="checkbox xshipping-checkbox">';
			foreach ($data['countries'] as $country) {

				$return.='<label>'
				.'<input '.((in_array($country['country_id'],$method_data['country']))?'checked':'').' type="checkbox" name="xshippingpro[country][]" value="'.$country['country_id'].'" />'.$country['name'].''
				.'</label>';

			}

			$return.='</div></div>'
			.'</div>'
			.'</div>'

			.'<div class="form-group">'
			.'<label class="col-sm-2 control-label"><span data-toggle="tooltip" title="'.$data['tip_customer_group'].'">'.$data['entry_customer_group'].'</span></label>' 
			.'<div class="col-sm-10">'
			.'<label class="any-class"><input '.(($method_data['customer_group_all']=='1')?'checked="checked"':'').' type="checkbox" name="xshippingpro[customer_group_all]" class="choose-any" value="1" />&nbsp;'.$data['text_any'].'</label>'
			.'<div class="well well-sm" style="height: 70px; overflow: auto;'.(($method_data['customer_group_all']!='1')?'display:block':'').'">'
			.'<div class="checkbox xshipping-checkbox">';

			foreach ($data['customer_groups'] as $customer_group) {

				$return.='<label>'
				.'<input '.((in_array($customer_group['customer_group_id'],$method_data['customer_group']))?'checked':'').' type="checkbox" name="xshippingpro[customer_group][]" value="'.$customer_group['customer_group_id'].'" />'.$customer_group['name'].''
				.'</label>';
			} 
			$return.='</div>'
			.'</div>'
			.'</div>'
			.'</div>'
			
			.'<div class="form-group">'
			.'<label class="col-sm-2 control-label"><span data-toggle="tooltip" title="'.$data['tip_payment'].'">'.$data['entry_payment'].'</span></label>' 
			.'<div class="col-sm-10">'
			.'<label class="any-class"><input '.(($method_data['payment_all']=='1')?'checked="checked"':'').' type="checkbox" name="xshippingpro[payment_all]" class="choose-any" value="1" />&nbsp;'.$data['text_any'].'</label>'
			.'<div class="well well-sm" style="height: 70px; overflow: auto;'.(($method_data['payment_all']!='1')?'display:block':'').'">'
			.'<div class="checkbox xshipping-checkbox">';

			foreach ($data['payment_mods'] as $code=>$value) {

				if (array_key_exists($code,$data['xpayments'])) {
					if(!isset($data['xpayments'][$code])) $data['xpayments'][$code]=array();
					$prefix=$value;
					foreach($data['xpayments'][$code] as $code =>$value) {
						$return.='<label>'
						.'<input '.((in_array($code,$method_data['payment']))?'checked':'').' type="checkbox" name="xshippingpro[payment][]" value="'.$code.'" />'.$prefix.'- '.$value.''
						.'</label>';
					}
					continue;
				}
				$return.='<label>'
				.'<input '.((in_array($code,$method_data['payment']))?'checked':'').' type="checkbox" name="xshippingpro[payment][]" value="'.$code.'" />'.$value.''
				.'</label>';
			} 
			$return.='</div>'
			.'</div>'
			.'</div>'
			.'</div>'

			.'<div class="form-group">'
			.'<label class="col-sm-2 control-label"><span data-toggle="tooltip" title="'.$data['tip_manufacturer'].'">'.$data['entry_manufacturer'].'</span></label>' 
			.'<div class="col-sm-10">'
			.'<label class="any-class"><input '.(($method_data['manufacturer_all']=='1')?'checked="checked"':'').' type="checkbox" name="xshippingpro[manufacturer_all]" class="choose-any-with" rel="manufacturer-option" value="1" />&nbsp;'.$data['text_any'].'</label>'
			.'<div class="well well-sm" style="height: 100px; overflow: auto;'.(($method_data['manufacturer_all']!='1')?'display:block':'').'">'
			.'<div class="checkbox xshipping-checkbox">';

			foreach ($data['manufacturers'] as $manufacturer) {

				$return.='<label>'
				.'<input '.((in_array($manufacturer['manufacturer_id'],$method_data['manufacturer']))?'checked':'').' type="checkbox" name="xshippingpro[manufacturer][]" value="'.$manufacturer['manufacturer_id'].'" />'.$manufacturer['name'].''
				.'</label>';
			} 
			$return.='</div>'
			.'</div>'
			.'</div>'
			.'</div>'
			.'<div class="form-group manufacturer-option" '.(($method_data['manufacturer_all']!='1')?'style="display:block"':'').'>'
			.'<label class="col-sm-2 control-label" for="input-make-rule'.$no_of_tab.'"><span data-toggle="tooltip" title="'.$data['tip_manufacturer_rule'].'">'.$data['text_manufacturer_rule'].'</span></label>'
			.'<div class="col-sm-10"><select class="form-control" id="input-make-rule'.$no_of_tab.'" name="xshippingpro[manufacturer_rule]">'
			.'<option value="6" '.(($method_data['manufacturer_rule']==6)?'selected':'').'>'.$data['text_manufacturer_least'].'</option>'
			.'<option value="3" '.(($method_data['manufacturer_rule']==3)?'selected':'').'>'.$data['text_manufacturer_least_with_other'].'</option>'
			.'<option value="4" '.(($method_data['manufacturer_rule']==4)?'selected':'').'>'.$data['text_manufacturer_exact'].'</option>'
			.'<option value="2" '.(($method_data['manufacturer_rule']==2)?'selected':'').'>'.$data['text_manufacturer_all'].'</option>'
			.'<option value="5" '.(($method_data['manufacturer_rule']==5)?'selected':'').'>'.$data['text_manufacturer_except'].'</option>'
			.'<option value="7" '.(($method_data['manufacturer_rule']==7)?'selected':'').'>'.$data['text_manufacturer_except_other'].'</option>'
			.'</select></div>'
			.'</div>'

			.'<div class="form-group">'
			.'<label class="col-sm-2 control-label" for="input-postal'.$no_of_tab.'"><span data-toggle="tooltip" title="'.$data['tip_zip'].'">'.$data['text_zip_postal'].'</span></label>' 
			.'<div class="col-sm-10">'
			.'<label class="any-class"><input '.(($method_data['postal_all']=='1')?'checked="checked"':'').' type="checkbox" name="xshippingpro[postal_all]" class="choose-any-with" rel="postal-option" value="1" id="input-postal'.$no_of_tab.'" />'.$data['text_any'].'</label>'
			.'</div>'
			.'</div>'
			.'<div class="form-group postal-option" '.(($method_data['postal_all']!='1')?'style="display:block"':'').'>'
			.'<label class="col-sm-2 control-label" for="input-zip'.$no_of_tab.'"><span data-toggle="tooltip" title="'.$data['tip_postal_code'].'">'.$data['text_enter_zip'].'</span></label>'
			.'<div class="col-sm-10"><textarea class="form-control" id="input-zip'.$no_of_tab.'" name="xshippingpro[postal]" rows="8" cols="70" />'.$method_data['postal'].'</textarea></div>'
			.'</div>'
			.'<div class="form-group postal-option" '.(($method_data['postal_all']!='1')?'style="display:block"':'').'>'
			.'<label class="col-sm-2 control-label" for="input-zip-rule'.$no_of_tab.'">'.$data['text_zip_rule'].'</label>'
			.'<div class="col-sm-10"><select class="form-control" id="input-zip-rule'.$no_of_tab.'" name="xshippingpro[postal_rule]">'
			.'<option value="inclusive" '.(($method_data['postal_rule']=='inclusive')?'selected':'').'>'.$data['text_zip_rule_inclusive'].'</option>'
			.'<option value="exclusive" '.(($method_data['postal_rule']=='exclusive')?'selected':'').'>'.$data['text_zip_rule_exclusive'].'</option>'
			.'</select></div>'
			.'</div>'  

			.'<div class="form-group">'
			.'<label class="col-sm-2 control-label" for="input-coupon'.$no_of_tab.'"><span data-toggle="tooltip" title="'.$data['tip_coupon'].'">'.$data['text_coupon'].'</span></label>' 
			.'<div class="col-sm-10">'
			.'<label class="any-class"><input '.(($method_data['coupon_all']=='1')?'checked="checked"':'').' type="checkbox" name="xshippingpro[coupon_all]" class="choose-any-with" rel="coupon-option" value="1" id="input-coupon'.$no_of_tab.'" />'.$data['text_any'].'</label>'
			.'</div>'
			.'</div>'
			.'<div class="form-group coupon-option" '.(($method_data['coupon_all']!='1')?'style="display:block"':'').'>'
			.'<label class="col-sm-2 control-label" for="input-coupon-here'.$no_of_tab.'"><span data-toggle="tooltip" title="'.$data['text_coupon_tip'].'">'.$data['text_enter_coupon'].'</span><</label>'
			.'<div class="col-sm-10"><textarea class="form-control" id="input-coupon-here'.$no_of_tab.'" name="xshippingpro[coupon]" rows="8" cols="70" />'.$method_data['coupon'].'</textarea></div>'
			.'</div>'
			.'<div class="form-group coupon-option" '.(($method_data['coupon_all']!='1')?'style="display:block"':'').'>'
			.'<label class="col-sm-2 control-label" for="input-coupon-rule'.$no_of_tab.'">'.$data['text_coupon_rule'].'</label>'
			.'<div class="col-sm-10"><select class="form-control" id="input-coupon-rule'.$no_of_tab.'" name="xshippingpro[coupon_rule]">'
			.'<option value="inclusive" '.(($method_data['coupon_rule']=='inclusive')?'selected':'').'>'.$data['text_coupon_rule_inclusive'].'</option>'
			.'<option value="exclusive" '.(($method_data['coupon_rule']=='exclusive')?'selected':'').'>'.$data['text_coupon_rule_exclusive'].'</option>'
			.'</select></div>'
			.'</div>'
			.'</div>' 
			.'<div class="tab-pane" id="catprod_'.$no_of_tab.'">'
			.'<div class="form-group">'
			.'<label class="col-sm-2 control-label" for="input-cat-rule'.$no_of_tab.'"><span data-toggle="tooltip" title="'.$data['tip_category'].'">'.$data['text_category'].'</span></label>'
			.'<div class="col-sm-10"><select id="input-cat-rule'.$no_of_tab.'" class="form-control selection" rel="category" name="xshippingpro[category]">'
			.'<option value="1" '.(($method_data['category']==1)?'selected':'').'>'.$data['text_category_any'].'</option>'
			.'<option value="6" '.(($method_data['category']==6)?'selected':'').'>'.$data['text_category_least'].'</option>'
			.'<option value="3" '.(($method_data['category']==3)?'selected':'').'>'.$data['text_category_least_with_other'].'</option>'
			.'<option value="4" '.(($method_data['category']==4)?'selected':'').'>'.$data['text_category_exact'].'</option>'
			.'<option value="2" '.(($method_data['category']==2)?'selected':'').'>'.$data['text_category_all'].'</option>'
			.'<option value="5" '.(($method_data['category']==5)?'selected':'').'>'.$data['text_category_except'].'</option>'
			.'<option value="7" '.(($method_data['category']==7)?'selected':'').'>'.$data['text_category_except_other'].'</option>'
			.'</select></div>'
			.'</div>'
			.'<div class="form-group category" '.(($method_data['category']!=1)?'style="display:block"':'').'>'
			.'<label class="col-sm-2 control-label" for="input-mul-cat-rule'.$no_of_tab.'"><span data-toggle="tooltip" title="'.$data['tip_multi_category'].'">'.$data['text_multi_category'].'</span></label>'
			.'<div class="col-sm-10"><select id="input-mul-cat-rule'.$no_of_tab.'" class="form-control" name="xshippingpro[multi_category]">'
			.'<option value="all" '.(($method_data['multi_category']=='all')?'selected':'').'>'.$data['entry_all'].'</option>'
			.'<option value="any" '.(($method_data['multi_category']=='any')?'selected':'').'>'.$data['entry_any'].'</option>'
			.'</select></div>'
			.'</div>'
			.'<div class="form-group category" '.(($method_data['category']!=1)?'style="display:block"':'').'>'
			.'<label class="col-sm-2 control-label" for="input-category'.$no_of_tab.'">'.$data['entry_category'].'</label>'
			.'<div class="col-sm-10"><input type="text" name="category" value="" placeholder="'.$data['entry_category'].'" id="input-category'.$no_of_tab.'" class="form-control" />'
			.'<div class="well well-sm product-category" style="height: 150px; overflow: auto;">';
			foreach ($method_data['product_category'] as $category_id) {
				$category_info = $this->model_catalog_category->getCategory($category_id);

				if(!$category_info) {
					$category_info['path'] = '';
					$category_info['name'] = '';
				}

				if($category_info['path']) $category_info['path'] .=  '&nbsp;&nbsp;&gt;&nbsp;&nbsp;';
				$return.='<div class="product-category'.$category_id. '"><i class="fa fa-minus-circle"></i> '.$category_info['path'].$category_info['name'].'<input type="hidden" class="category" name="xshippingpro[product_category][]" value="'.$category_id.'" /></div>';
			}
			$return.='</div>'
			.'</div>'
			.'</div>'
			.'<div class="form-group">'
			.'<label class="col-sm-2 control-label" for="input-product_rule'.$no_of_tab.'"><span data-toggle="tooltip" title="'.$data['tip_product'].'">'.$data['text_product'].'</span></label>'
			.'<div class="col-sm-10"><select id="input-product_rule'.$no_of_tab.'" class="form-control selection" rel="product" name="xshippingpro[product]">'
			.'<option value="1" '.(($method_data['product']==1)?'selected':'').'>'.$data['text_product_any'].'</option>'
			.'<option value="6" '.(($method_data['product']==6)?'selected':'').'>'.$data['text_product_least'].'</option>'
			.'<option value="3" '.(($method_data['product']==3)?'selected':'').'>'.$data['text_product_least_with_other'].'</option>'
			.'<option value="4" '.(($method_data['product']==4)?'selected':'').'>'.$data['text_product_exact'].'</option>'
			.'<option value="2" '.(($method_data['product']==2)?'selected':'').'>'.$data['text_product_all'].'</option>'
			.'<option value="5" '.(($method_data['product']==5)?'selected':'').'>'.$data['text_product_except'].'</option>'
			.'<option value="7" '.(($method_data['product']==7)?'selected':'').'>'.$data['text_product_except_other'].'</option>'
			.'</select></div>'
			.'</div>'
			.'<div class="form-group product" ' .(($method_data['product']!=1)?'style="display:block"':'').'>'
			.'<label class="col-sm-2 control-label" for="input-product'.$no_of_tab.'">'.$data['entry_product'].'</label>'
			.'<div class="col-sm-10"><input type="text" name="product" value="" placeholder="'.$data['entry_product'].'" id="input-product'.$no_of_tab.'" class="form-control" />'
			.'<div class="well well-sm product-product" style="height: 150px; overflow: auto;">';
			foreach ($method_data['product_product'] as $product_id) {
				$product_info = $this->model_catalog_product->getProduct($product_id);
				if(!$product_info) {
					$product_info['name'] = '';
				}
				$return.='<div class="product-product'.$product_id. '"><i class="fa fa-minus-circle"></i> '.(isset($product_info['name'])?$product_info['name']:'').'<input type="hidden" name="xshippingpro[product_product][]" value="'.$product_id.'" /></div>';

			}
			$return.='</div>'
			.'</div>'
			.'</div>'
			.'<div class="form-group">'
			.'<label class="col-sm-2 control-label" for="input-option_rule'.$no_of_tab.'"><span data-toggle="tooltip" title="'.$data['tip_option'].'">'.$data['text_option'].'</span></label>'
			.'<div class="col-sm-10"><select id="input-option_rule'.$no_of_tab.'" class="form-control selection" rel="option" name="xshippingpro[option]">'
			.'<option value="1" '.(($method_data['option']==1)?'selected':'').'>'.$data['text_option_any'].'</option>'
			.'<option value="6" '.(($method_data['option']==6)?'selected':'').'>'.$data['text_option_least'].'</option>'
			.'<option value="3" '.(($method_data['option']==3)?'selected':'').'>'.$data['text_option_least_with_other'].'</option>'
			.'<option value="4" '.(($method_data['option']==4)?'selected':'').'>'.$data['text_option_exact'].'</option>'
			.'<option value="2" '.(($method_data['option']==2)?'selected':'').'>'.$data['text_option_all'].'</option>'
			.'<option value="5" '.(($method_data['option']==5)?'selected':'').'>'.$data['text_option_except'].'</option>'
			.'<option value="7" '.(($method_data['option']==7)?'selected':'').'>'.$data['text_option_except_other'].'</option>'
			.'</select></div>'
			.'</div>'
			.'<div class="form-group option" ' .(($method_data['option']!=1)?'style="display:block"':'').'>'
			.'<label class="col-sm-2 control-label" for="input-option'.$no_of_tab.'">'.$data['entry_option'].'</label>'
			.'<div class="col-sm-10"><input type="text" name="option" value="" placeholder="'.$data['entry_option'].'" id="input-option'.$no_of_tab.'" class="form-control" />'
			.'<div class="well well-sm product-option" style="height: 150px; overflow: auto;">';
			foreach ($method_data['product_option'] as $option_value_id) {
				$optn_name = '';
				$option_value_info = $this->model_catalog_option->getOptionValue($option_value_id);
				if($option_value_info) {
					$option_info = $this->model_catalog_option->getOption($option_value_info['option_id']);
					if($option_info) {
						$optn_name = strip_tags(html_entity_decode($option_info['name'], ENT_QUOTES, 'UTF-8')).'&nbsp;&nbsp;&gt;&nbsp;&nbsp;'.strip_tags(html_entity_decode($option_value_info['name'], ENT_QUOTES, 'UTF-8'));
					}
				}
				$return.='<div class="product-option'.$option_value_id. '"><i class="fa fa-minus-circle"></i> '.$optn_name.'<input type="hidden" name="xshippingpro[product_option][]" value="'.$option_value_id.'" /></div>';

			}
			$return.='</div>'
			.'</div>'
			.'</div>'
			.'</div>'
			.'<div class="tab-pane" id="price_'.$no_of_tab.'">'
			.'<div class="form-group">'
			.'<label class="col-sm-2 control-label" for="input-rate'.$no_of_tab.'"><span data-toggle="tooltip" title="'.$data['tip_rate_type'].'">'.$data['text_rate_type'].'</span></label>'
			.'<div class="col-sm-10"><select id="input-rate'.$no_of_tab.'" class="rate-selection form-control" name="xshippingpro[rate_type]">'
			.'<option value="flat" '.(($method_data['rate_type']=='flat')?'selected':'').'>'.$data['text_rate_flat'].'</option>'
			.'<option value="quantity" '.(($method_data['rate_type']=='quantity')?'selected':'').'>'.$data['text_rate_quantity'].'</option>'
			.'<option value="weight" '.(($method_data['rate_type']=='weight')?'selected':'').'>'.$data['text_rate_weight'].'</option>'
			.'<option value="dimensional" '.(($method_data['rate_type']=='dimensional')?'selected':'').'>'.$data['text_dimensional_weight'].'</option>'
			.'<option value="volume" '.(($method_data['rate_type']=='volume')?'selected':'').'>'.$data['text_rate_volume'].'</option>'
			.'<option value="total" '.(($method_data['rate_type']=='total')?'selected':'').'>'.$data['text_rate_total'].'</option>'
			.'<option value="total_coupon" '.(($method_data['rate_type']=='total_coupon')?'selected':'').'>'.$data['text_rate_total_coupon'].'</option>'
			.'<option value="sub" '.(($method_data['rate_type']=='sub')?'selected':'').'>'.$data['text_rate_sub_total'].'</option>'
			.'<option value="grand" '.(($method_data['rate_type']=='grand')?'selected':'').'>'.$data['text_grand_total'].'</option>'
			.'<option value="total_method" '.(($method_data['rate_type']=='total_method')?'selected':'').'>'.$data['text_rate_total_method'].'</option>'
			.'<option value="sub_method" '.(($method_data['rate_type']=='sub_method')?'selected':'').'>'.$data['text_rate_sub_total_method'].'</option>'
			.'<option value="quantity_method" '.(($method_data['rate_type']=='quantity_method')?'selected':'').'>'.$data['text_rate_quantity_method'].'</option>'
			.'<option value="weight_method" '.(($method_data['rate_type']=='weight_method')?'selected':'').'>'.$data['text_rate_weight_method'].'</option>'
			.'<option value="dimensional_method" '.(($method_data['rate_type']=='dimensional_method')?'selected':'').'>'.$data['text_dimensional_weight_method'].'</option>'
			.'<option value="volume_method" '.(($method_data['rate_type']=='volume_method')?'selected':'').'>'.$data['text_rate_volume_method'].'</option>'
			.'</select></div>'
			.'</div>'
			.'<div class="form-group dimensional-option" '.(($method_data['rate_type']=='dimensional' || $method_data['rate_type']=='dimensional_method' || $method_data['rate_type']=='volume' || $method_data['rate_type']=='volume_method')?'style="display:block"':'style="display:none"').'>'
			.'<label class="col-sm-3 control-label" for="input-dimension_factor'.$no_of_tab.'">'.$data['text_dimensional_factor'].'</label>'
			.'<div class="col-sm-9"><input id="input-dimension_factor'.$no_of_tab.'" type="text" name="xshippingpro[dimensional_factor]" value="'.$method_data['dimensional_factor'].'" class="form-control" /></div>'
			.'</div>'
			.'<div class="form-group dimensional-option" '.(($method_data['rate_type']=='dimensional' || $method_data['rate_type']=='dimensional_method' || $method_data['rate_type']=='volume' || $method_data['rate_type']=='volume_method')?'style="display:block"':'style="display:none"').'>'
			.'<label class="col-sm-4 control-label" for="input-dimension_overrule'.$no_of_tab.'">'.$data['text_dimensional_overrule'].'</label>'
			.'<div class="col-sm-8"><input '.(($method_data['dimensional_overfule']=='1')?'checked="checked"':'').' id="input-dimension_overrule'.$no_of_tab.'" type="checkbox" name="xshippingpro[dimensional_overfule]" value="1" /></div>'
			.'</div>'
			.'<div class="form-group single-option" '.(($method_data['rate_type']=='flat')?'style="display:block"':'style="display:none"').'>'
			.'<label class="col-sm-2 control-label" for="input-cost'.$no_of_tab.'"><span data-toggle="tooltip" title="'.$data['tip_cost'].'">'.$data['entry_cost'].'</span></label>'
			.'<div class="col-sm-10"><input id="input-cost'.$no_of_tab.'" class="form-control" type="text" name="xshippingpro[cost]" value="'.$method_data['cost'].'" /></div>'
			.'</div>'
			.'<div class="form-group range-option" '.(($method_data['rate_type']!='flat')?'style="display:block"':'').'>'
			.'<label class="col-sm-2 control-label"><span data-toggle="tooltip" title="'.$data['tip_import'].'">'.$data['text_unit_range'].'</span></label>'
			.'<div class="col-sm-10">'
			.'<div class="tbl-wrapper">'
			.'<div class="import-btn-wrapper">'
			.'<a href="'.$data['export'].'&no='.$no_of_tab.'" class="btn btn-info export-btn rate-btn">'.$data['text_export'].'</a>&nbsp;<a class="btn btn-danger delete-all rate-btn">'.$data['text_delete_all'].'</a>&nbsp;<a  class="btn btn-primary import-btn rate-btn">'.$data['text_csv_import'].'</a>'
			.'</div>'
			.'<div class="table-responsive">'
			.'<table class="table table-striped table-bordered table-hover">'
			.'<thead>'
			.'<tr>'
			.'<td class="text-left"><label class="control-label"><span data-toggle="tooltip" title="'.$data['tip_unit_start'].'">'.$data['text_start'].'</span></label></td>'
			.'<td class="text-left"><label class="control-label"><span data-toggle="tooltip" title="'.$data['tip_unit_end'].'">'.$data['text_end'].'</span></label></td>'
			.'<td class="text-left"><label class="control-label"><span data-toggle="tooltip" title="'.$data['tip_unit_price'].'">'.$data['text_cost'].'</span></label></td>'
			.'<td class="text-left"><label class="control-label"><span data-toggle="tooltip" title="'.$data['tip_unit_ppu'].'">'.$data['text_qnty_block'].'</span></label></td>'
			.'<td class="text-left"><label class="control-label"><span data-toggle="tooltip" title="'.$data['tip_partial'].'">'.$data['text_partial'].'</span></label></td>'
			.'<td class="left"></td>'
			.'</tr>'
			.'</thead>'
			.'<tbody>';

			$is_row_found=false;
			foreach ($method_data['rate_start'] as $inc=>$rate_start) { 
				if(!isset($method_data['rate_partial'][$inc]))$method_data['rate_partial'][$inc]='0'; 
				$is_row_found=true; 
				$return.='<tr>' 
				.'<td class="text-left"><input size="15" type="text" class="form-control" name="xshippingpro[rate_start][]" value="'.$rate_start.'" /></td>'
				.'<td class="text-left"><input size="15" type="text" class="form-control" name="xshippingpro[rate_end][]" value="'.$method_data['rate_end'][$inc].'" /></td>'
				.'<td class="text-left"><input size="15" type="text" class="form-control" name="xshippingpro[rate_total][]" value="'.$method_data['rate_total'][$inc].'" /></td>'
				.'<td class="text-left"><input size="6" type="text" class="form-control" name="xshippingpro[rate_block][]" value="'.$method_data['rate_block'][$inc].'" /></td>'
				.'<td class="text-left"><select name="xshippingpro[rate_partial][]"><option '.(($method_data['rate_partial'][$inc]=='1')?'selected':'').' value="1">'.$data['text_yes'].'</option><option '.(($method_data['rate_partial'][$inc]=='0')?'selected':'').' value="0">'.$data['text_no'].'</option></select></td>'
				.'<td class="text-right"><a class="btn btn-danger remove-row">'.$data['text_remove'].'</a></td>'
				.'</tr>';
			}
			if(!$is_row_found)$return.='<tr class="no-row"><td colspan="6">'.$data['no_unit_row'].'</td></tr>';

			$return.='</tbody>'
			.'<tfoot>'
			.'<tr>'
			.'<td colspan="5">&nbsp;</td>'
			.'<td class="right">&nbsp;<a class="btn btn-primary add-row"><i class="fa fa-plus-circle"></i>'.$data['text_add_new'].'</span></label>'
			.'</tr>'
			.'</tfoot>'     
			.'</table>'
			.'</div>'
			.'</div>'
			.'</div>'
			.'</div>'
			.'<div class="form-group range-option" '.(($method_data['rate_type']!='flat')?'style="display:block"':'style="display:none"').'>'
			.'<label class="col-sm-2 control-label" for="input-additional'.$no_of_tab.'"><span data-toggle="tooltip" title="'.$data['tip_additional'].'">'.$data['text_additional'].'</span></label>'
			.'<div class="col-sm-10"><input id="input-additional'.$no_of_tab.'" class="form-control" type="text" name="xshippingpro[additional]" value="'.$method_data['additional'].'" /></div>'
			.'</div>'
			.'<div class="form-group range-option" '.(($method_data['rate_type']!='flat')?'style="display:block"':'').'>'
			.'<label class="col-sm-2 control-label" for="input-rate-final'.$no_of_tab.'"><span data-toggle="tooltip" title="'.$data['tip_single_commulative'].'">'.$data['text_final_cost'].'</span></label>'
			.'<div class="col-sm-10"><select id="input-rate-final'.$no_of_tab.'" class="form-control" name="xshippingpro[rate_final]">'
			.'<option '.(($method_data['rate_final']=='single')?'selected':'').' value="single">'.$data['text_final_single'].'</option>'
			.'<option '.(($method_data['rate_final']=='cumulative')?'selected':'').' value="cumulative">'.$data['text_final_cumulative'].'</option>'
			.'</select></div>'
			.'</div>'
			.'<div class="form-group">'
			.'<label class="col-sm-2 control-label" for="input-rate-percent'.$no_of_tab.'"><span data-toggle="tooltip" title="'.$data['tip_percentage'].'">'.$data['text_percentage_related'].'</span></label>'
			.'<div class="col-sm-10"><select class="form-control" id="input-rate-percent'.$no_of_tab.'" name="xshippingpro[rate_percent]">'
			.'<option '.(($method_data['rate_percent']=='sub')?'selected':'').' value="sub">'.$data['text_percent_sub_total'].'</option>'
			.'<option '.(($method_data['rate_percent']=='total')?'selected':'').' value="total">'.$data['text_percent_total'].'</option>'
			.'<option '.(($method_data['rate_percent']=='shipping')?'selected':'').' value="shipping">'.$data['text_percent_shipping'].'</option>'
			.'<option '.(($method_data['rate_percent']=='sub_shipping')?'selected':'').' value="sub_shipping">'.$data['text_percent_sub_total_shipping'].'</option>'
			.'<option '.(($method_data['rate_percent']=='total_shipping')?'selected':'').' value="total_shipping">'.$data['text_percent_total_shipping'].'</option>'
			.'</select></div>'
			.'</div>'
			.'<div class="form-group single-option" '.(($method_data['rate_type']=='flat')?'style="display:block"':'style="display:none"').'>'
			.'<label class="col-sm-2 control-label" for="input-mask'.$no_of_tab.'">'.$data['text_mask_price'].'</label>'
			.'<div class="col-sm-10"><input id="input-mask'.$no_of_tab.'" class="form-control" type="text" name="xshippingpro[mask]" value="'.$method_data['mask'].'" /></div>'
			.'</div>'
			.'<div class="form-group range-option" '.(($method_data['rate_type']!='flat')?'style="display:block"':'').'>'
			.'<label class="col-sm-2 control-label"><span data-toggle="tooltip" title="'.$data['tip_price_adjust'].'">'.$data['text_price_adjustment'].'</span></label>'
			.'<div class="col-sm-10">'
			.'<div class="row">'
			.'<div class="col-sm-4">'
			.' <input class="form-control" type="text" name="xshippingpro[rate_min]" placeholder="'.$data['text_price_min'].'" value="'.$method_data['rate_min'].'" />'
			.'</div>'
			.'<div class="col-sm-4">'
			.'<input class="form-control" type="text" name="xshippingpro[rate_max]" placeholder="'.$data['text_price_max'].'" value="'.$method_data['rate_max'].'" />'
			.'</div>'  
			.'<div class="col-sm-4">'
			.'<input class="form-control" type="text" name="xshippingpro[rate_add]" placeholder="'.$data['text_price_add'].'" value="'.$method_data['rate_add'].'" />'
			.'</div>'	   
			.'</div>'
			.'<div class="row"><div class="col-sm-12"><input '.(($method_data['modifier_ignore'])?'checked':'').' type="checkbox" value="1" name="xshippingpro[modifier_ignore]" />'.$data['ignore_modifier'].'</div></div>'
			.'</div>'
			.'</div>'
			.'<div class="form-group range-option" '.(($method_data['rate_type']!='flat')?'style="display:block"':'').'>'
			.'<label class="col-sm-2 control-label" for="input-equation'.$no_of_tab.'"><span data-toggle="tooltip" title="'.$data['tip_equation'].'">'.$data['text_equation'].'</span></label>'
			.'<div class="col-sm-10"><textarea class="form-control" id="lang-equation'.$no_of_tab.'" name="xshippingpro[equation]" rows="8" cols="70" />'.$method_data['equation'].'</textarea>'.$data['text_equation_help'].'</div>'
			.'</div>'
			.'</div>'
			.'<div class="tab-pane" id="other_'.$no_of_tab.'">'
			.'<div class="form-group">'
			.'<label class="col-sm-2 control-label"><span data-toggle="tooltip" title="'.$data['tip_day'].'">'.$data['text_days_week'].'</span></label>'
			.'<div class="col-sm-10">'
			.'<div class="well well-sm well-days" style="height: 80px; overflow: auto;">'
			.'<div class="checkbox xshipping-checkbox">' 
			.'<label><input name="xshippingpro[days][]" '.((in_array(0,$method_data['days']))?'checked':'').' type="checkbox" value="0" />&nbsp; '.$data['text_sunday'].'</label>'
			.'<label><input name="xshippingpro[days][]" '.((in_array(1,$method_data['days']))?'checked':'').' type="checkbox" value="1" />&nbsp; '.$data['text_monday'].'</label>'
			.'<label><input name="xshippingpro[days][]" '.((in_array(2,$method_data['days']))?'checked':'').' type="checkbox" value="2" />&nbsp; '.$data['text_tuesday'].'</label>'
			.'<label><input name="xshippingpro[days][]" '.((in_array(3,$method_data['days']))?'checked':'').' type="checkbox" value="3" />&nbsp; '.$data['text_wednesday'].'</label>'
			.'<label><input name="xshippingpro[days][]" '.((in_array(4,$method_data['days']))?'checked':'').' type="checkbox" value="4" />&nbsp; '.$data['text_thursday'].'</label>'
			.'<label><input name="xshippingpro[days][]" '.((in_array(5,$method_data['days']))?'checked':'').' type="checkbox" value="5" />&nbsp; '.$data['text_friday'].'</label>'
			.'<label><input name="xshippingpro[days][]" '.((in_array(6,$method_data['days']))?'checked':'').' type="checkbox" value="6" />&nbsp; '.$data['text_saturday'].'</label>'
			.'</div>'
			.'</div>' 
			.'</div>'
			.'</div>'
			.'<div class="form-group">'
			.'<label class="col-sm-2 control-label" for="input-time-start'.$no_of_tab.'"><span data-toggle="tooltip" title="'.$data['tip_time'].'">'.$data['text_time_period'].'</span></label>'
			.'<div class="col-sm-10">'
			.'<div class="row">'
			.'<div class="col-sm-4">'
			.'<select id="input-time-start'.$no_of_tab.'" class="form-control" name="xshippingpro[time_start]">'
			.'<option value="">'.$data['text_any'].'</option>';
			for($i = 0; $i <= 23; $i++) { 
				$return.='<option '.(($method_data['time_start']==$i && $method_data['time_start']!='')?'selected':'').' value="'.$i.'">'.date("h:i A", strtotime("$i:00")).'</option>';
			} 
			$return.='</select>'
			.'</div>'
			.'<div class="col-sm-4">'
			.'<select class="form-control" name="xshippingpro[time_end]">'
			.'<option value="">'.$data['text_any'].'</option>';
			for($i = 0; $i <= 23; $i++) { 
				$return.='<option '.(($method_data['time_end']==$i && $method_data['time_end']!='')?'selected':'').' value="'.$i.'">'.date("h:i A", strtotime("$i:00")).'</option>';
			}
			$return.='</select>'
			.'</div>'
			.'</div>'
			.'</div>'
			.'</div>'
			.'<div class="form-group">'
			.'<label class="col-sm-2 control-label" for="input-total'.$no_of_tab.'"><span data-toggle="tooltip" title="'.$data['tip_total'].'">'.$data['entry_order_total'].'</span></label>'
			.'<div class="col-sm-10">'
			.'<div class="row-fluid">'
			.'<div class="col-sm-5">'
			.'<input size="15" class="form-control" type="text" name="xshippingpro[order_total_start]" value="'.$method_data['order_total_start'].'" />'
			.'</div>'
			.'<div class="col-sm-1">'.$data['entry_to'].'</div>'
			.'<div class="col-sm-5">'
			.'<input class="form-control" size="15" type="text" name="xshippingpro[order_total_end]" value="'.$method_data['order_total_end'].'" />'
			.'</div>'
			.'</div>'
			.'</div>'
			.'</div>'
			.'<div class="form-group">'
			.'<label class="col-sm-2 control-label" for="input-total'.$no_of_tab.'"><span data-toggle="tooltip" title="'.$data['tip_weight'].'">'.$data['entry_order_weight'].'</span></label>'
			.'<div class="col-sm-10">'
			.'<div class="row-fluid">'
			.'<div class="col-sm-5">'
			.'<input size="15" class="form-control" type="text" name="xshippingpro[weight_start]" value="'.$method_data['weight_start'].'" />'
			.'</div>'
			.'<div class="col-sm-1">'.$data['entry_to'].'</div>'
			.'<div class="col-sm-5">'
			.'<input class="form-control" size="15" type="text" name="xshippingpro[weight_end]" value="'.$method_data['weight_end'].'" />'
			.'</div>'
			.'</div>'
			.'</div>'
			.'</div>'
			.'<div class="form-group">'
			.'<label class="col-sm-2 control-label" for="input-total'.$no_of_tab.'"><span data-toggle="tooltip" title="'.$data['tip_quantity'].'">'.$data['entry_quantity'].'</span></label>'
			.'<div class="col-sm-10">'
			.'<div class="row-fluid">'
			.'<div class="col-sm-5">'
			.'<input size="15" class="form-control" type="text" name="xshippingpro[quantity_start]" value="'.$method_data['quantity_start'].'" />'
			.'</div>'
			.'<div class="col-sm-1">'.$data['entry_to'].'</div>'
			.'<div class="col-sm-5">'
			.'<input class="form-control" size="15" type="text" name="xshippingpro[quantity_end]" value="'.$method_data['quantity_end'].'" />'
			.'</div>'
			.'</div>'
			.'</div>'
			.'</div>'
			.'<div class="form-group">'
			.'<label class="col-sm-2 control-label" for="input-address'.$no_of_tab.'">'.$data['text_geo_address'].'</label>'
			.'<div class="col-sm-10"><select class="form-control" id="input-address'.$no_of_tab.'" name="xshippingpro[address_type]">'
			.'<option value="delivery" '.(($method_data['address_type']=='delivery')?'selected':'').'>'.$data['text_delivery'].'</option>'
			.'<option value="payment" '.(($method_data['address_type']=='payment')?'selected':'').'>'.$data['text_payment'].'</option>'
			.'</select></div>'
			.'</div>'
			.'<div class="form-group">'
			.'<label class="col-sm-2 control-label" for="input-method'.$no_of_tab.'"><span data-toggle="tooltip" title="'.$data['text_hide_tip'].'">'.$data['text_hide'].'</span></label>'
			.'<div class="col-sm-10"><input type="text" value="" placeholder="'.$data['text_hide_placeholder'].'" id="input-method'.$no_of_tab.'" class="form-control hide-shipping" />'
			.'<div class="well well-sm hide-methods" style="height: 150px; overflow: auto;">';
			foreach ($method_data['hide'] as $hide_tab_id) {
				if (isset($data['methods'][$hide_tab_id])) {
					$return.='<div class="hide-method'.$hide_tab_id. '"><i class="fa fa-minus-circle"></i> '.$data['methods'][$hide_tab_id].'<input type="hidden" name="xshippingpro[hide][]" value="'.$hide_tab_id.'" /></div>';
				}
			}
			$return.='</div>'
			.'</div>'
			.'</div>'
			.'</div>'
			.'</div>' 
			.'</div>';

		}
		
		return $return;		
	}
	
	public function getOption() {
		$json = array();

		if (isset($this->request->get['filter_name'])) {
			$this->load->language('catalog/option');
			$this->load->model('catalog/option');

			$filter_data = array(
				'filter_name' => $this->request->get['filter_name'],
				'start'       => 0,
				'limit'       => 5
				);

			$options = $this->model_catalog_option->getOptions($filter_data);

			foreach ($options as $option) {
				$option_value_data = array();

				if ($option['type'] == 'select' || $option['type'] == 'radio' || $option['type'] == 'checkbox') {
					$option_values = $this->model_catalog_option->getOptionValues($option['option_id']);

					foreach ($option_values as $option_value) {

						$json[] = array(
							'option_value_id'    => $option_value['option_value_id'],
							'name'         => strip_tags(html_entity_decode($option['name'], ENT_QUOTES, 'UTF-8')).'&nbsp;&nbsp;&gt;&nbsp;&nbsp;'.strip_tags(html_entity_decode($option_value['name'], ENT_QUOTES, 'UTF-8'))
							);
					}
				}
			}
		}

		$sort_order = array();

		foreach ($json as $key => $value) {
			$sort_order[$key] = $value['name'];
		}

		array_multisort($sort_order, SORT_ASC, $json);

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
	
	private function getMethodList($data) {

		$return = array();
		
		foreach($data['method_data'] as $single_method) {
			$no_of_tab = $single_method['tab_id'];
			$method_data = $single_method['method_data'];
			$method_data = @unserialize(@base64_decode($method_data));
			if(!is_array($method_data)) $method_data = array();

			if(!isset($method_data['display'])) $method_data['display'] = '';

			if(!$method_data['display'])
			{
				$return[$no_of_tab] = isset($method_data['name'][$this->config->get('config_language_id')])? $method_data['name'][$this->config->get('config_language_id')] : 'Untitled Method'.$no_of_tab;
			}
			else {
				$return[$no_of_tab] = $method_data['display'];
			}
		}

		return $return;  
	}

	private function getDefaultValues() {
		return array(
			'status' => 1,
			'store_all' => 1,
			'geo_zone_all' => 1,
			'customer_group_all' => 1,
			'payment_all' => 1,
			'city_all' => 1,
			'manufacturer_all' => 1,
			'country_all' => 1,
			'postal_all' => 1,
			'coupon_all' => 1,
			'category' => 1,
			'multi_category' => 'any',
			'product' => 1,
			'option' => 1,
			'rate_type' => 'flat',
			'dimensional_factor' => 5000,
			'days' => array(0,1,2,3,4,5,6),
			'display' => 'Untitled Method',

			);	
	}

	private function getInitialValues() {
		return array(

			/* array rules */	
			'customer_group' => array(),
			'geo_zone_id' => array(),
			'product_category' => array(),
			'product_product' => array(),
			'store' => array(),
			'manufacturer' => array(),
			'payment' => array(),
			'days' => array(),
			'rate_start' => array(),
			'rate_end' => array(),
			'rate_total' => array(),
			'rate_block' => array(),
			'country' => array(),
			'name' => array(),
			'desc' => array(),
			'product_option' => array(),
			'hide' => array(),

			/* string rules*/
			'inc_weight' => '',
			'dimensional_factor' => '',
			'dimensional_overfule' => '',
			'customer_group_all' => '',
			'geo_zone_all' => '',
			'country_all' => '',
			'store_all' => '',
			'manufacturer_all' => '',
			'postal_all' => '',
			'coupon_all' => '',
			'payment_all' => '',
			'city_all' => '',
			'city' => '',
			'postal' => '',
			'coupon' => '',
			'city_rule' => 'inclusive',
			'postal_rule' => 'inclusive',
			'coupon_rule' => 'inclusive',
			'time_start' => '',
			'time_end' => '',
			'rate_final' => 'single',
			'rate_percent' => 'sub',
			'rate_min' => '',
			'rate_max' => '',
			'rate_add' => '',
			'manufacturer_rule' => 2,
			'multi_category' => 'all',
			'additional' => 0,
			'modifier_ignore' => '',
			'logo' => '',
			'group' => 0,
			'order_total_start' => 0,
			'order_total_end' => 0,
			'weight_start' => 0,
			'weight_end' => 0,
			'quantity_start' => 0,
			'quantity_end' => 0,
			'mask' => '',
			'equation' => '',
			'tax_class_id' => '',
			'option_all' => '',
			'option' => 1,
			'address_type' => 'shipping',
			'sort_order' => '',
			'status' => '',
			'category' => '',
			'product' => '',
			'rate_type' => '',
			'cost' => '',
			'display' => ''
			);
	}

	private function getSubGroups($data) {

		$return = '';              
		for($i=1; $i<=$data['shipping_xshippingpro_sub_groups_count']; $i++) {

			$current_method_mode = 'lowest';
			$current_method_name =  isset($data['shipping_xshippingpro_sub_group_name'][$i]) ? $data['shipping_xshippingpro_sub_group_name'][$i]:'';

			$return .='<tr>
				<td class="text-left">Group'.$i.'</td>
				<td class="text-left">
				<select class="shipping_xshippingpro_sub_group'.$i.'" name="shipping_xshippingpro_sub_group['.$i.']">';
			
			foreach($data['group_options'] as $type=>$name) {
				if($type =='no_group') continue;
				$selected=(isset($data['shipping_xshippingpro_sub_group'][$i]) && $data['shipping_xshippingpro_sub_group'][$i]==$type) ? 'selected':'';
				$current_method_mode = (isset($data['shipping_xshippingpro_sub_group'][$i]) && $data['shipping_xshippingpro_sub_group'][$i]==$type)? $type: $current_method_mode; 

				$return .='<option value="'.$type.'" '.$selected.'>'.$name.'</option>';
			}

			$return .='. </select>';

		    $display = ($current_method_mode!='lowest' && $current_method_mode!='highest') ? 'style="display:none;"' : '';

			$return .= '</td>
						<td class="text-left"> 
							<select '.$display.' class="shipping_xshippingpro_sub_group_limit'.$i.'" name="shipping_xshippingpro_sub_group_limit['.$i.']">';

							for($j=1; $j <=5; $j++) {
								$selected=(isset($data['shipping_xshippingpro_sub_group_limit'][$j]) && $data['shipping_xshippingpro_sub_group_limit'][$j]==$j) ? 'selected':'';
								$return .='<option value="'.$j.'" '.$selected.'>'.$j.'</option>';
							}

							$return .='</select>
						</td>
						<td class="text-left"> 
							<input type="text" name="shipping_xshippingpro_sub_group_name['.$i.']" value="'.$current_method_name.'" placeholder="'.$data['entry_name'].'" class="form-control" />
						</td>
					</tr>';
				}
				return $return;    
			}

			private function getModuleName($code,$type)
			{
				if(!$code) return '';

				$this->language->load('extension/'.$type.'/'.$code);
				return $this->language->get('heading_title');
			}	
		}
		?>