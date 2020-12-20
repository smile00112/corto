<?php
class ControllerExtensionModuleQuickCheckout extends Controller {
	protected $error = array();
	protected $code = 'quickcheckout';

	public function index() {
		$this->load->language('extension/module/quickcheckout');
		$data['heading_title'] = $this->language->get('heading_title');

		$this->document->setTitle(strip_tags($this->language->get('page_title')));

		$this->document->addScript('view/javascript/quickcheckout/tinysort/jquery.tinysort.min.js');
        $this->document->addScript('view/javascript/quickcheckout/bootstrap-sortable.js');
        $this->document->addScript('view/javascript/quickcheckout/bootstrap-slider/js/bootstrap-slider.js');
        $this->document->addStyle('view/javascript/quickcheckout/bootstrap-slider/css/slider.css');
        $this->document->addStyle('view/stylesheet/quickcheckout.css');
		
		if (isset($this->request->get['store_id'])) {
			$store_id = $this->request->get['store_id'];
		} else {
			$store_id = 0;
		}
		
		$this->load->model('setting/setting');
		$this->load->model('extension/module/quickcheckout');
		
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && ($this->validate())) {

			$this->model_extension_module_quickcheckout->saveKeyword('quickcheckout', $this->request->post, $store_id);

			$this->model_setting_setting->editSetting('quickcheckout', $this->request->post, $store_id);		
			
			$this->session->data['success'] = $this->language->get('text_success');
		
			if (!isset($this->request->get['continue'])) {
				$this->response->redirect($this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true));
			} else {
				$this->response->redirect($this->url->link('extension/module/quickcheckout', 'user_token=' . $this->session->data['user_token'] . '&store_id=' . $store_id, true));
			}
		}
	
		// All fields
		$fields = array(
			'firstname',
			'lastname',
			'email',
			'telephone',
			'company',
			'customer_group',
			'address_1',
			'address_2',
			'city',
			'postcode',
			'country',
			'zone',
			'newsletter',
			'register',
			'shipping',
			'rules',
			'comment'
		);
		
		$data['fields'] = $fields;
		
 		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}
		
		if (isset($this->session->data['success'])) {
			$data['success'] = $this->session->data['success'];
			
			unset($this->session->data['success']);
		} else {
			$data['success'] = '';
		}
		
		$setting = $this->model_setting_setting->getSetting('quickcheckout', $store_id);
		
  		$data['breadcrumbs'] = array();

   		$data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('text_home'),
			'href'      => $this->url->link('common/home', 'user_token=' . $this->session->data['user_token'], true)
   		);

   		$data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('text_module'),
			'href'      => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true)
   		);
		
   		$data['breadcrumbs'][] = array(
       		'text'      => $this->language->get('page_title'),
			'href'      => $this->url->link('extension/module/quickcheckout', 'user_token=' . $this->session->data['user_token'], true)
   		);
		
		$data['action'] = $this->url->link('extension/module/quickcheckout', 'user_token=' . $this->session->data['user_token'] . '&store_id=' . $store_id, true);
		$data['continue'] = $this->url->link('extension/module/quickcheckout', 'user_token=' . $this->session->data['user_token'] . '&continue=1&store_id=' . $store_id, true);
		$data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true);
		
		// General
		if (isset($this->request->post['quickcheckout_status'])) {
			$data['quickcheckout_status'] = $this->request->post['quickcheckout_status'];
		} elseif (isset($setting['quickcheckout_status'])) {
			$data['quickcheckout_status'] = $setting['quickcheckout_status'];
		} else {
			$data['quickcheckout_status'] = 0;
		}
		
		if (isset($this->request->post['quickcheckout_minimum_order'])) {
			$data['quickcheckout_minimum_order'] = $this->request->post['quickcheckout_minimum_order'];
		} elseif (isset($setting['quickcheckout_minimum_order'])) {
			$data['quickcheckout_minimum_order'] = $setting['quickcheckout_minimum_order'];
		} else {
			$data['quickcheckout_minimum_order'] = 0;
		}
		
		if (isset($this->request->post['quickcheckout_debug'])) {
			$data['quickcheckout_debug'] = $this->request->post['quickcheckout_debug'];
		} elseif (isset($setting['quickcheckout_debug'])) {
			$data['quickcheckout_debug'] = $setting['quickcheckout_debug'];
		} else {
			$data['quickcheckout_debug'] = 0;
		}
		
		if (isset($this->request->post['quickcheckout_confirmation_page'])) {
			$data['quickcheckout_confirmation_page'] = $this->request->post['quickcheckout_confirmation_page'];
		} elseif (isset($setting['quickcheckout_confirmation_page'])) {
			$data['quickcheckout_confirmation_page'] = $setting['quickcheckout_confirmation_page'];
		} else {
			$data['quickcheckout_confirmation_page'] = 1;
		}
		
		if (isset($this->request->post['quickcheckout_save_data'])) {
			$data['quickcheckout_save_data'] = $this->request->post['quickcheckout_save_data'];
		} elseif (isset($setting['quickcheckout_save_data'])) {
			$data['quickcheckout_save_data'] = $setting['quickcheckout_save_data'];
		} else {
			$data['quickcheckout_save_data'] = 0;
		}
		
		if (isset($this->request->post['quickcheckout_edit_cart'])) {
			$data['quickcheckout_edit_cart'] = $this->request->post['quickcheckout_edit_cart'];
		} elseif (isset($setting['quickcheckout_edit_cart'])) {
			$data['quickcheckout_edit_cart'] = $setting['quickcheckout_edit_cart'];
		} else {
			$data['quickcheckout_edit_cart'] = 1;
		}
		
		if (isset($this->request->post['quickcheckout_highlight_error'])) {
			$data['quickcheckout_highlight_error'] = $this->request->post['quickcheckout_highlight_error'];
		} elseif (isset($setting['quickcheckout_highlight_error'])) {
			$data['quickcheckout_highlight_error'] = $setting['quickcheckout_highlight_error'];
		} else {
			$data['quickcheckout_highlight_error'] = 1;
		}
		
		if (isset($this->request->post['quickcheckout_text_error'])) {
			$data['quickcheckout_text_error'] = $this->request->post['quickcheckout_text_error'];
		} elseif (isset($setting['quickcheckout_text_error'])) {
			$data['quickcheckout_text_error'] = $setting['quickcheckout_text_error'];
		} else {
			$data['quickcheckout_text_error'] = 1;
		}
		
		if (isset($this->request->post['quickcheckout_auto_submit'])) {
			$data['quickcheckout_auto_submit'] = $this->request->post['quickcheckout_auto_submit'];
		} elseif (isset($setting['quickcheckout_auto_submit'])) {
			$data['quickcheckout_auto_submit'] = $setting['quickcheckout_auto_submit'];
		} else {
			$data['quickcheckout_auto_submit'] = 0;
		}

		if (isset($this->request->post['quickcheckout_skip_cart'])) {
			$data['quickcheckout_skip_cart'] = $this->request->post['quickcheckout_skip_cart'];
		} elseif (isset($setting['quickcheckout_skip_cart'])) {
			$data['quickcheckout_skip_cart'] = $setting['quickcheckout_skip_cart'];
		} else {
			$data['quickcheckout_skip_cart'] = 0;
		}

		if (isset($this->request->post['quickcheckout_force_bootstrap'])) {
			$data['quickcheckout_force_bootstrap'] = $this->request->post['quickcheckout_force_bootstrap'];
		} elseif (isset($setting['quickcheckout_force_bootstrap'])) {
			$data['quickcheckout_force_bootstrap'] = $setting['quickcheckout_force_bootstrap'];
		} else {
			$data['quickcheckout_force_bootstrap'] = 0;
		}

		if (isset($this->request->post['quickcheckout_show_shipping_address'])) {
			$data['quickcheckout_show_shipping_address'] = $this->request->post['quickcheckout_show_shipping_address'];
		} elseif (isset($setting['quickcheckout_show_shipping_address'])) {
			$data['quickcheckout_show_shipping_address'] = $setting['quickcheckout_show_shipping_address'];
		} else {
			$data['quickcheckout_show_shipping_address'] = 0;
		}

		
		if (isset($this->request->post['quickcheckout_payment_target'])) {
			$data['quickcheckout_payment_target'] = $this->request->post['quickcheckout_payment_target'];
		} elseif (isset($setting['quickcheckout_payment_target'])) {
			$data['quickcheckout_payment_target'] = $setting['quickcheckout_payment_target'];
		} else {
			$data['quickcheckout_payment_target'] = '#button-confirm, .button, .btn';
		}
		
		if (isset($this->request->post['quickcheckout_proceed_button_text'])) {
			$data['quickcheckout_proceed_button_text'] = $this->request->post['quickcheckout_proceed_button_text'];
		} elseif (isset($setting['quickcheckout_proceed_button_text']) && is_array($setting['quickcheckout_proceed_button_text'])) {
			$data['quickcheckout_proceed_button_text'] = $setting['quickcheckout_proceed_button_text'];
		} else {
			$data['quickcheckout_proceed_button_text'] = array();
		}

		if (isset($this->request->post['quickcheckout_keyword'])) {
			$data['quickcheckout_keyword'] = $this->request->post['quickcheckout_keyword'];
		} elseif (isset($setting['quickcheckout_keyword']) && is_array($setting['quickcheckout_keyword'])) {
			$data['quickcheckout_keyword'] = $setting['quickcheckout_keyword'];
		} else {
			$data['quickcheckout_keyword'] = array();
		}
		
		// Design
		if (isset($this->request->post['quickcheckout_load_screen'])) {
			$data['quickcheckout_load_screen'] = $this->request->post['quickcheckout_load_screen'];
		} elseif (isset($setting['quickcheckout_load_screen'])) {
			$data['quickcheckout_load_screen'] = $setting['quickcheckout_load_screen'];
		} else {
			$data['quickcheckout_load_screen'] = 0;
		}
		
		if (isset($this->request->post['quickcheckout_loading_display'])) {
			$data['quickcheckout_loading_display'] = $this->request->post['quickcheckout_loading_display'];
		} elseif (isset($setting['quickcheckout_loading_display'])) {
			$data['quickcheckout_loading_display'] = $setting['quickcheckout_loading_display'];
		} else {
			$data['quickcheckout_loading_display'] = 0;
		}
		
		if (isset($this->request->post['quickcheckout_layout'])) {
			$data['quickcheckout_layout'] = $this->request->post['quickcheckout_layout'];
		} elseif (isset($setting['quickcheckout_layout'])) {
			$data['quickcheckout_layout'] = $setting['quickcheckout_layout'];
		} else {
			$data['quickcheckout_layout'] = 2;
		}
		
		if (isset($this->request->post['quickcheckout_responsive'])) {
			$data['quickcheckout_responsive'] = $this->request->post['quickcheckout_responsive'];
		} elseif (isset($setting['quickcheckout_responsive'])) {
			$data['quickcheckout_responsive'] = $setting['quickcheckout_responsive'];
		} else {
			$data['quickcheckout_responsive'] = 1;
		}
		
		if (isset($this->request->post['quickcheckout_slide_effect'])) {
			$data['quickcheckout_slide_effect'] = $this->request->post['quickcheckout_slide_effect'];
		} elseif (isset($setting['quickcheckout_slide_effect'])) {
			$data['quickcheckout_slide_effect'] = $setting['quickcheckout_slide_effect'];
		} else {
			$data['quickcheckout_slide_effect'] = 0;
		}
		
		if (isset($this->request->post['quickcheckout_custom_css'])) {
			$data['quickcheckout_custom_css'] = $this->request->post['quickcheckout_custom_css'];
		} elseif (isset($setting['quickcheckout_custom_css'])) {
			$data['quickcheckout_custom_css'] = $setting['quickcheckout_custom_css'];
		} else {
			$data['quickcheckout_custom_css'] = '';
		}
		
		// Fields
		foreach ($fields as $field) {
			if (isset($this->request->post['quickcheckout_field_' . $field])) {
				$data['quickcheckout_field_' . $field] = $this->request->post['quickcheckout_field_' . $field];
			} elseif (isset($setting['quickcheckout_field_' . $field]) && is_array($setting['quickcheckout_field_' . $field])) {
				$data['quickcheckout_field_' . $field] = $setting['quickcheckout_field_' . $field];
			} else {
				$data['quickcheckout_field_' . $field] = array();
			}
		}
		
		// Modules
		if (isset($this->request->post['quickcheckout_coupon'])) {
			$data['quickcheckout_coupon'] = $this->request->post['quickcheckout_coupon'];
		} elseif (isset($setting['quickcheckout_coupon'])) {
			$data['quickcheckout_coupon'] = $setting['quickcheckout_coupon'];
		} else {
			$data['quickcheckout_coupon'] = 0;
		}
		
		if (isset($this->request->post['quickcheckout_voucher'])) {
			$data['quickcheckout_voucher'] = $this->request->post['quickcheckout_voucher'];
		} elseif (isset($setting['quickcheckout_voucher'])) {
			$data['quickcheckout_voucher'] = $setting['quickcheckout_voucher'];
		} else {
			$data['quickcheckout_voucher'] = 0;
		}
		
		if (isset($this->request->post['quickcheckout_reward'])) {
			$data['quickcheckout_reward'] = $this->request->post['quickcheckout_reward'];
		} elseif (isset($setting['quickcheckout_reward'])) {
			$data['quickcheckout_reward'] = $setting['quickcheckout_reward'];
		} else {
			$data['quickcheckout_reward'] = 0;
		}
		
		if (isset($this->request->post['quickcheckout_cart'])) {
			$data['quickcheckout_cart'] = $this->request->post['quickcheckout_cart'];
		} elseif (isset($setting['quickcheckout_cart'])) {
			$data['quickcheckout_cart'] = $setting['quickcheckout_cart'];
		} else {
			$data['quickcheckout_cart'] = 0;
		}
		
		if (isset($this->request->post['quickcheckout_login_module'])) {
			$data['quickcheckout_login_module'] = $this->request->post['quickcheckout_login_module'];
		} elseif (isset($setting['quickcheckout_login_module'])) {
			$data['quickcheckout_login_module'] = $setting['quickcheckout_login_module'];
		} else {
			$data['quickcheckout_login_module'] = 0;
		}
		
		if (isset($this->request->post['quickcheckout_html_header'])) {
			$data['quickcheckout_html_header'] = $this->request->post['quickcheckout_html_header'];
		} elseif (isset($setting['quickcheckout_html_header']) && is_array($setting['quickcheckout_html_header'])) {
			$data['quickcheckout_html_header'] = $setting['quickcheckout_html_header'];
		} else {
			$data['quickcheckout_html_header'] = array();
		}
		
		if (isset($this->request->post['quickcheckout_html_footer'])) {
			$data['quickcheckout_html_footer'] = $this->request->post['quickcheckout_html_footer'];
		} elseif (isset($setting['quickcheckout_html_footer']) && is_array($setting['quickcheckout_html_footer'])) {
			$data['quickcheckout_html_footer'] = $setting['quickcheckout_html_footer'];
		} else {
			$data['quickcheckout_html_footer'] = array();
		}
		
		// Payment
		if (isset($this->request->post['quickcheckout_payment_module'])) {
			$data['quickcheckout_payment_module'] = $this->request->post['quickcheckout_payment_module'];
		} elseif (isset($setting['quickcheckout_payment_module'])) {
			$data['quickcheckout_payment_module'] = $setting['quickcheckout_payment_module'];
		} else {
			$data['quickcheckout_payment_module'] = 0;
		}
		
		if (isset($this->request->post['quickcheckout_payment_reload'])) {
			$data['quickcheckout_payment_reload'] = $this->request->post['quickcheckout_payment_reload'];
		} elseif (isset($setting['quickcheckout_payment_reload'])) {
			$data['quickcheckout_payment_reload'] = $setting['quickcheckout_payment_reload'];
		} else {
			$data['quickcheckout_payment_reload'] = 0;
		}
		
		if (isset($this->request->post['quickcheckout_payment'])) {
			$data['quickcheckout_payment'] = $this->request->post['quickcheckout_payment'];
		} elseif (isset($setting['quickcheckout_payment'])) {
			$data['quickcheckout_payment'] = $setting['quickcheckout_payment'];
		} else {
			$data['quickcheckout_payment'] = 0;
		}
		
		if (isset($this->request->post['quickcheckout_payment_default'])) {
			$data['quickcheckout_payment_default'] = $this->request->post['quickcheckout_payment_default'];
		} elseif (isset($setting['quickcheckout_payment_default'])) {
			$data['quickcheckout_payment_default'] = $setting['quickcheckout_payment_default'];
		} else {
			$data['quickcheckout_payment_default'] = '';
		}
		
		if (isset($this->request->post['quickcheckout_payment_logo'])) {
			$data['quickcheckout_payment_logo'] = $this->request->post['quickcheckout_payment_logo'];
		} elseif (isset($setting['quickcheckout_payment_logo']) && is_array($setting['quickcheckout_payment_logo'])) {
			$data['quickcheckout_payment_logo'] = $setting['quickcheckout_payment_logo'];
		} else {
			$data['quickcheckout_payment_logo'] = array();
		}
		
		// Shipping
		if (isset($this->request->post['quickcheckout_shipping_module'])) {
			$data['quickcheckout_shipping_module'] = $this->request->post['quickcheckout_shipping_module'];
		} elseif (isset($setting['quickcheckout_shipping_module'])) {
			$data['quickcheckout_shipping_module'] = $setting['quickcheckout_shipping_module'];
		} else {
			$data['quickcheckout_shipping_module'] = 0;
		}
		
		if (isset($this->request->post['quickcheckout_shipping'])) {
			$data['quickcheckout_shipping'] = $this->request->post['quickcheckout_shipping'];
		} elseif (isset($setting['quickcheckout_shipping'])) {
			$data['quickcheckout_shipping'] = $setting['quickcheckout_shipping'];
		} else {
			$data['quickcheckout_shipping'] = 0;
		}
		
		if (isset($this->request->post['quickcheckout_shipping_default'])) {
			$data['quickcheckout_shipping_default'] = $this->request->post['quickcheckout_shipping_default'];
		} elseif (isset($setting['quickcheckout_shipping_default'])) {
			$data['quickcheckout_shipping_default'] = $setting['quickcheckout_shipping_default'];
		} else {
			$data['quickcheckout_shipping_default'] = 0;
		}
		
		if (isset($this->request->post['quickcheckout_shipping_reload'])) {
			$data['quickcheckout_shipping_reload'] = $this->request->post['quickcheckout_shipping_reload'];
		} elseif (isset($setting['quickcheckout_shipping_reload'])) {
			$data['quickcheckout_shipping_reload'] = $setting['quickcheckout_shipping_reload'];
		} else {
			$data['quickcheckout_shipping_reload'] = 0;
		}
		
		if (isset($this->request->post['quickcheckout_shipping_logo'])) {
			$data['quickcheckout_shipping_logo'] = $this->request->post['quickcheckout_shipping_logo'];
		} elseif (isset($setting['quickcheckout_shipping_logo']) && is_array($setting['quickcheckout_shipping_logo'])) {
			$data['quickcheckout_shipping_logo'] = $setting['quickcheckout_shipping_logo'];
		} else {
			$data['quickcheckout_shipping_logo'] = array();
		}

		if (isset($this->request->post['quickcheckout_shipping_title_display'])) {
			$data['quickcheckout_shipping_title_display'] = $this->request->post['quickcheckout_shipping_title_display'];
		} elseif (isset($setting['quickcheckout_shipping_title_display'])) {
			$data['quickcheckout_shipping_title_display'] = $setting['quickcheckout_shipping_title_display'];
		} else {
			$data['quickcheckout_shipping_title_display'] = 0;
		}
		
		// Survey
		if (isset($this->request->post['quickcheckout_survey'])) {
			$data['quickcheckout_survey'] = $this->request->post['quickcheckout_survey'];
		} elseif (isset($setting['quickcheckout_survey'])) {
			$data['quickcheckout_survey'] = $setting['quickcheckout_survey'];
		} else {
			$data['quickcheckout_survey'] = 0;
		}
		
		if (isset($this->request->post['quickcheckout_survey_required'])) {
			$data['quickcheckout_survey_required'] = $this->request->post['quickcheckout_survey_required'];
		} elseif (isset($setting['quickcheckout_survey_required'])) {
			$data['quickcheckout_survey_required'] = $setting['quickcheckout_survey_required'];
		} else {
			$data['quickcheckout_survey_required'] = 0;
		}
		
		if (isset($this->request->post['quickcheckout_survey_text'])) {
			$data['quickcheckout_survey_text'] = $this->request->post['quickcheckout_survey_text'];
		} elseif (isset($setting['quickcheckout_survey_text']) && is_array($setting['quickcheckout_survey_text'])) {
			$data['quickcheckout_survey_text'] = $setting['quickcheckout_survey_text'];
		} else {
			$data['quickcheckout_survey_text'] = array();
		}
		
		if (isset($this->request->post['quickcheckout_survey_type'])) {
			$data['quickcheckout_survey_type'] = $this->request->post['quickcheckout_survey_type'];
		} elseif (isset($setting['quickcheckout_survey_type'])) {
			$data['quickcheckout_survey_type'] = $setting['quickcheckout_survey_type'];
		} else {
			$data['quickcheckout_survey_type'] = 0;
		}
		
		if (isset($this->request->post['quickcheckout_survey_answers'])) {
			$data['quickcheckout_survey_answers'] = $this->request->post['quickcheckout_survey_answers'];
		} elseif (isset($setting['quickcheckout_survey_answers']) && is_array($setting['quickcheckout_survey_answers'])) {
			$data['quickcheckout_survey_answers'] = $setting['quickcheckout_survey_answers'];
		} else {
			$data['quickcheckout_survey_answers'] = array();
		}
		
		// Delivery
		if (isset($this->request->post['quickcheckout_delivery'])) {
			$data['quickcheckout_delivery'] = $this->request->post['quickcheckout_delivery'];
		} elseif (isset($setting['quickcheckout_delivery'])) {
			$data['quickcheckout_delivery'] = $setting['quickcheckout_delivery'];
		} else {
			$data['quickcheckout_delivery'] = 0;
		}
		
		if (isset($this->request->post['quickcheckout_delivery_time'])) {
			$data['quickcheckout_delivery_time'] = $this->request->post['quickcheckout_delivery_time'];
		} elseif (isset($setting['quickcheckout_delivery_time'])) {
			$data['quickcheckout_delivery_time'] = $setting['quickcheckout_delivery_time'];
		} else {
			$data['quickcheckout_delivery_time'] = 0;
		}
		
		if (isset($this->request->post['quickcheckout_delivery_required'])) {
			$data['quickcheckout_delivery_required'] = $this->request->post['quickcheckout_delivery_required'];
		} elseif (isset($setting['quickcheckout_delivery_required'])) {
			$data['quickcheckout_delivery_required'] = $setting['quickcheckout_delivery_required'];
		} else {
			$data['quickcheckout_delivery_required'] = 0;
		}
		
		if (isset($this->request->post['quickcheckout_delivery_unavailable'])) {
			$data['quickcheckout_delivery_unavailable'] = $this->request->post['quickcheckout_delivery_unavailable'];
		} elseif (isset($setting['quickcheckout_delivery_unavailable'])) {
			$data['quickcheckout_delivery_unavailable'] = $setting['quickcheckout_delivery_unavailable'];
		} else {
			$data['quickcheckout_delivery_unavailable'] = '"6-3-2013", "7-3-2013", "8-3-2013"';
		}
		
		if (isset($this->request->post['quickcheckout_delivery_min'])) {
			$data['quickcheckout_delivery_min'] = $this->request->post['quickcheckout_delivery_min'];
		} elseif (isset($setting['quickcheckout_delivery_min'])) {
			$data['quickcheckout_delivery_min'] = $setting['quickcheckout_delivery_min'];
		} else {
			$data['quickcheckout_delivery_min'] = 1;
		}
		
		if (isset($this->request->post['quickcheckout_delivery_max'])) {
			$data['quickcheckout_delivery_max'] = $this->request->post['quickcheckout_delivery_max'];
		} elseif (isset($setting['quickcheckout_delivery_max'])) {
			$data['quickcheckout_delivery_max'] = $setting['quickcheckout_delivery_max'];
		} else {
			$data['quickcheckout_delivery_max'] = 30;
		}
		
		if (isset($this->request->post['quickcheckout_delivery_min_hour'])) {
			$data['quickcheckout_delivery_min_hour'] = $this->request->post['quickcheckout_delivery_min_hour'];
		} elseif (isset($setting['quickcheckout_delivery_min_hour'])) {
			$data['quickcheckout_delivery_min_hour'] = $setting['quickcheckout_delivery_min_hour'];
		} else {
			$data['quickcheckout_delivery_min_hour'] = '09';
		}
		
		if (isset($this->request->post['quickcheckout_delivery_max_hour'])) {
			$data['quickcheckout_delivery_max_hour'] = $this->request->post['quickcheckout_delivery_max_hour'];
		} elseif (isset($setting['quickcheckout_delivery_max_hour'])) {
			$data['quickcheckout_delivery_max_hour'] = $setting['quickcheckout_delivery_max_hour'];
		} else {
			$data['quickcheckout_delivery_max_hour'] = '17';
		}
		
		if (isset($this->request->post['quickcheckout_delivery_days_of_week'])) {
			$data['quickcheckout_delivery_days_of_week'] = $this->request->post['quickcheckout_delivery_days_of_week'];
		} elseif (isset($setting['quickcheckout_delivery_days_of_week'])) {
			$data['quickcheckout_delivery_days_of_week'] = $setting['quickcheckout_delivery_days_of_week'];
		} else {
			$data['quickcheckout_delivery_days_of_week'] = '';
		}
		
		if (isset($this->request->post['quickcheckout_delivery_times'])) {
			$data['quickcheckout_delivery_times'] = $this->request->post['quickcheckout_delivery_times'];
		} elseif (isset($setting['quickcheckout_delivery_times'])) {
			$data['quickcheckout_delivery_times'] = $setting['quickcheckout_delivery_times'];
		} else {
			$data['quickcheckout_delivery_times'] = array();
		}

		if (isset($this->request->post['quickcheckout_step'])) {
			$data['quickcheckout_step'] = $this->request->post['quickcheckout_step'];
		} elseif (isset($setting['quickcheckout_step'])) {
			$data['quickcheckout_step'] = $setting['quickcheckout_step'];
		} else {
			$data['quickcheckout_step'] = Array
			(
			    'login' => Array
			        (
			            'column' => 1,
			            'row' => 1
			        ),

			    'payment_address' => Array
			        (
			            'column' => 1,
			            'row' => 2
			        ),

			    'shipping_address' => Array
			        (
			            'column' => 1,
			            'row' => 3
			        ),

			    'shipping_method' => Array
			        (
			            'column' => 2,
			            'row' => 1
			        ),

			    'payment_method' => Array
			        (
			            'column' => 3,
			            'row' => 1
			        ),

			    'cart' => Array
			        (
			            'column' => 4,
			            'row' => 2
			        ),

			    'coupons' => Array
			        (
			            'column' => 4,
			            'row' => 2
			        ),

			    'confirm' => Array
			        (
			            'column' => 4,
			            'row' => 2
			        ),

			);
		}

		$data['steps'] = $data['quickcheckout_step'];

		if (isset($this->request->post['quickcheckout_column'])) {
			$data['quickcheckout_column'] = $this->request->post['quickcheckout_column'];
		} elseif (isset($setting['quickcheckout_column'])) {
			$data['quickcheckout_column'] = $setting['quickcheckout_column'];
		} else {
			$data['quickcheckout_column'] = array ( 1 => 4, 2 => 4, 3 => 4, 4 => 8 );
		}
		
		// Stores
		$data['store_id'] = $store_id;
		
		$this->load->model('setting/store');
		
		$data['stores'] = $this->model_setting_store->getStores();
		
		// Languages
		$this->load->model('localisation/language');
		
		$data['languages'] = $this->model_localisation_language->getLanguages();
		
		// Countries
		$this->load->model('localisation/country');
		
		$data['countries'] = $this->model_localisation_country->getCountries();

		if ($this->config->get('config_country_id')) {
			$this->load->model('localisation/zone');
			$data['zones'] = $this->model_localisation_zone->getZonesByCountryId($this->config->get('config_country_id'));
		}
		
		// Payment
		$files = glob(DIR_APPLICATION . 'controller/extension/payment/*.php');
		
		$data['payment_modules'] = array();
		
		if ($files) {
			foreach ($files as $file) {
				$extension = basename($file, '.php');

				if ($this->config->get('payment_' . $extension . '_status')) {
					$this->load->language('extension/payment/' . $extension);

					$data['payment_modules'][] = array(
						'name'		=> $this->language->get('heading_title'),
						'code'		=> $extension
					);
				}
			}
		}
		
		// Shipping
		$files = glob(DIR_APPLICATION . 'controller/extension/shipping/*.php');
		
		$data['shipping_modules'] = array();
		
		if ($files) {
			foreach ($files as $file) {
				$extension = basename($file, '.php');

				if ($this->config->get('shipping_' . $extension . '_status')) {
					$this->load->language('extension/shipping/' . $extension);

					$data['shipping_modules'][] = array(
						'name'		=> $this->language->get('heading_title'),
						'code'		=> $extension
					);
				}
			}
		}

		/* payment2shipping */
		if (isset($this->request->post['quickcheckout_payment2shipping_shippings'])) {
			$data['payment2shippings'] = $this->request->post['quickcheckout_payment2shipping_shippings'];
		} elseif ($this->config->has('quickcheckout_payment2shipping_shippings')) {
			$data['payment2shippings'] = $this->config->get('quickcheckout_payment2shipping_shippings');
		} else {
			$data['payment2shippings'] = array();
		}

		$sort_order = array();

		foreach ($data['payment2shippings'] as $shipping) {
			$sort_order[] = $shipping['shipping'];
		}

		array_multisort($sort_order, SORT_ASC, $data['payment2shippings']);

		$this->load->model('setting/extension');

		$extensions = $this->model_setting_extension->getInstalled('shipping');

		foreach ($extensions as $key => $value) {
			if (!is_file(DIR_APPLICATION . 'controller/extension/shipping/' . $value . '.php') && !is_file(DIR_APPLICATION . 'controller/shipping/' . $value . '.php')) {
				$this->model_setting_extension->uninstall('shipping', $value);

				unset($extensions[$key]);
			}
		}

		$data['payment2shippings_shippings'] = array();

		// Compatibility code for old extension folders
		$files = glob(DIR_APPLICATION . 'controller/extension/shipping/*.php');

		if ($files) {
			foreach ($files as $file) {
				$extension = basename($file, '.php');
				$this->load->language('extension/shipping/' . $extension, 'extension');

				if ($extension=='cs') {
					$multiple_shippings = $this->model_setting_setting->getSetting('shipping_cs');
					if (isset($multiple_shippings) && !empty($multiple_shippings['shipping_cs'])) {
						foreach ($multiple_shippings['shipping_cs'] as $shipping_id => $shipping) { 
							foreach ($shipping['shipping_description'] as $language_id => $shipping_description) {
								$data['payment2shippings_shippings'][] = array(
									'name'       => $shipping_description['name'],
									'extension'  => $extension.'.shipping_cs_'.$shipping_id,
									'status'     => $this->config->get('shipping_' . $extension . '_status') ? $this->language->get('text_enabled') : $this->language->get('text_disabled'),
									'sort_order' => $this->config->get('shipping_' . $extension . '_sort_order'),
									'install'    => $this->url->link('extension/extension/shipping/install', 'user_token=' . $this->session->data['user_token'] . '&extension=' . $extension, true),
									'uninstall'  => $this->url->link('extension/extension/shipping/uninstall', 'user_token=' . $this->session->data['user_token'] . '&extension=' . $extension, true),
									'installed'  => in_array($extension, $extensions),
									'edit'       => $this->url->link('extension/shipping/' . $extension, 'user_token=' . $this->session->data['user_token'], true)
								);
							}
						}
					}
				} else {
					$data['payment2shippings_shippings'][] = array(
							'name'       => $this->language->get('extension')->get('heading_title'),
							'extension'  => $extension,
							'status'     => $this->config->get('shipping_' . $extension . '_status') ? $this->language->get('text_enabled') : $this->language->get('text_disabled'),
							'sort_order' => $this->config->get('shipping_' . $extension . '_sort_order'),
							'install'    => $this->url->link('extension/extension/shipping/install', 'user_token=' . $this->session->data['user_token'] . '&extension=' . $extension, true),
							'uninstall'  => $this->url->link('extension/extension/shipping/uninstall', 'user_token=' . $this->session->data['user_token'] . '&extension=' . $extension, true),
							'installed'  => in_array($extension, $extensions),
							'edit'       => $this->url->link('extension/shipping/' . $extension, 'user_token=' . $this->session->data['user_token'], true)
					);
				}
			}
		}

		$extensions = $this->model_setting_extension->getInstalled('payment');

		foreach ($extensions as $key => $value) {
			if (!is_file(DIR_APPLICATION . 'controller/extension/payment/' . $value . '.php') && !is_file(DIR_APPLICATION . 'controller/payment/' . $value . '.php')) {
				$this->model_setting_extension->uninstall('payment', $value);

				unset($extensions[$key]);
			}
		}

		$data['payment2shippings_payments'] = array();

		// Compatibility code for old extension folders
		$files = glob(DIR_APPLICATION . 'controller/extension/payment/*.php');

		if ($files) {
			foreach ($files as $file) {
				$extension = basename($file, '.php');

				$this->load->language('extension/payment/' . $extension, 'extension');

				$text_link = $this->language->get('extension')->get('text_' . $extension);

				if ($text_link != 'text_' . $extension) {
					$link = $text_link;
				} else {
					$link = '';
				}

				$data['payment2shippings_payments'][] = array(
						'name'       => $this->language->get('extension')->get('heading_title'),
						'extension'  => $extension,
						'link'       => $link,
						'status'     => $this->config->get('payment_' . $extension . '_status') ? $this->language->get('text_enabled') : $this->language->get('text_disabled'),
						'sort_order' => $this->config->get('payment_' . $extension . '_sort_order'),
						'install'    => $this->url->link('extension/extension/payment/install', 'user_token=' . $this->session->data['user_token'] . '&extension=' . $extension, true),
						'uninstall'  => $this->url->link('extension/extension/payment/uninstall', 'user_token=' . $this->session->data['user_token'] . '&extension=' . $extension, true),
						'installed'  => in_array($extension, $extensions),
						'edit'       => $this->url->link('extension/payment/' . $extension, 'user_token=' . $this->session->data['user_token'], true)
				);
			}
		}

		//echo '<pre>'.__METHOD__.' ['.__LINE__.']: '; print_r($multiple_shippings); echo '</pre>';
		//echo '<pre>'.__METHOD__.' ['.__LINE__.']: '; print_r($data['payment2shippings_payments']); echo '</pre>';
		//echo '<pre>'.__METHOD__.' ['.__LINE__.']: '; print_r($data['payment2shippings_shippings']); echo '</pre>';


		$data['user_token'] = $this->session->data['user_token'];
		
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		//$this->generateOutput('extension/module/quickcheckout', $data);
		$this->response->setOutput($this->load->view('extension/module/quickcheckout', $data));
	}
	
	public function country() {
		$json = array();

		$this->load->model('localisation/country');

		$country_info = $this->model_localisation_country->getCountry($this->request->get['country_id']);

		if ($country_info) {
			$this->load->model('localisation/zone');

			$json = array(
				'country_id'        => $country_info['country_id'],
				'name'              => $country_info['name'],
				'iso_code_2'        => $country_info['iso_code_2'],
				'iso_code_3'        => $country_info['iso_code_3'],
				'address_format'    => $country_info['address_format'],
				'postcode_required' => $country_info['postcode_required'],
				'zone'              => $this->model_localisation_zone->getZonesByCountryId($this->request->get['country_id']),
				'status'            => $country_info['status']		
			);
		}

		$this->response->setOutput(json_encode($json));
	}
	
	public function install(){
		if (!$this->user->hasPermission('modify', 'extension/extension/module')) {
			return;
		}
		
		$this->load->language('extension/module/quickcheckout');

		$this->load->model('user/user_group');
		$this->model_user_user_group->addPermission($this->user->getGroupId(), 'access', 'extension/module/quickcheckout');
		$this->model_user_user_group->addPermission($this->user->getGroupId(), 'modify', 'extension/module/quickcheckout');
		
		$this->load->model('setting/setting');

		$data2 = array(
			'status'							=> '1',
			'module_quickcheckout_status'		=> '1',
		);
		$this->model_setting_setting->editSetting('module_quickcheckout', $data2);
		
		$data = array(
			'quickcheckout_status'				=> '1',
			'quickcheckout_minimum_order'		=> '0',
			'quickcheckout_debug'				=> '0',
			'quickcheckout_confirmation_page'	=> '0',
			'quickcheckout_save_data'			=> '1',
			'quickcheckout_edit_cart'			=> '1',
			'quickcheckout_highlight_error'		=> '1',
			'quickcheckout_text_error'			=> '1',
			'quickcheckout_auto_submit'			=> '1',
			'quickcheckout_skip_cart'			=> '1',
			'quickcheckout_force_bootstrap'		=> '0',
			'quickcheckout_payment_target'		=> '#button-confirm, .button, .btn',
			'quickcheckout_keyword'				=> array(
					'1'		=> 'checkout1',
					'2'		=> 'checkout2',
					'3'		=> 'checkout3'
			),
			'quickcheckout_load_screen'			=> '0',
			'quickcheckout_loading_display'		=> '0',
			'quickcheckout_layout'				=> '4',
			'quickcheckout_responsive'			=> '1',
			'quickcheckout_slide_effect'		=> '0',
			'quickcheckout_field_firstname'		=> array(
					'display'		=> '1',
					'required'		=> '1',
					'default'		=> '',
					'sort_order'	=> '1'
				),
			'quickcheckout_field_lastname'		=> array(
					'display'		=> '1',
					'required'		=> '1',
					'default'		=> '',
					'sort_order'	=> '2'
				),
			'quickcheckout_field_email'			=> array(
					'display'		=> '1',
					'required'		=> '1',
					'default'		=> '',
					'sort_order'	=> '3'
				),
			'quickcheckout_field_telephone'		=> array(
					'display'		=> '1',
					'required'		=> '1',
					'default'		=> '',
					'sort_order'	=> '4'
				),
			'quickcheckout_field_company'		=> array(
					'display'		=> '0',
					'required'		=> '0',
					'default'		=> '',
					'sort_order'	=> '6'
				),
			'quickcheckout_field_customer_group' => array(
					'display'		=> '0',
					'required'		=> '',
					'default'		=> '',
					'sort_order'	=> '7'
				),
			'quickcheckout_field_address_1'		=> array(
					'display'		=> '0',
					'required'		=> '0',
					'default'		=> '',
					'sort_order'	=> '8'
				),
			'quickcheckout_field_address_2'		=> array(
					'display'		=> '0',
					'required'		=> '0',
					'default'		=> '',
					'sort_order'	=> '9'
				),
			'quickcheckout_field_city'			=> array(
					'display'		=> '0',
					'required'		=> '0',
					'default'		=> '',
					'sort_order'	=> '10'
				),
			'quickcheckout_field_postcode'		=> array(
					'display'		=> '0',
					'required'		=> '0',
					'default'		=> '',
					'sort_order'	=> '11'
				),
			'quickcheckout_field_country'		=> array(
					'display'		=> '0',
					'required'		=> '0',
					'default'		=> $this->config->get('config_country_id'),
					'sort_order'	=> '12'
				),
			'quickcheckout_field_zone'			=> array(
					'display'		=> '0',
					'required'		=> '0',
					'default'		=> $this->config->get('config_zone_id'),
					'sort_order'	=> '13'
				),
			'quickcheckout_field_newsletter'	=> array(
					'display'		=> '0',
					'required'		=> '1',
					'default'		=> '0',
					'sort_order'	=> ''
				),
			'quickcheckout_field_register'		=> array(
					'display'		=> '0',
					'required'		=> '0',
					'default'		=> '',
					'sort_order'	=> ''
				),
			'quickcheckout_field_shipping'		=> array(
					'display'		=> '0',
					'required'		=> '0',
					'default'		=> '0',
					'sort_order'	=> ''
				),
			'quickcheckout_field_rules'		=> array(
					'display'		=> '1',
					'required'		=> '1',
					'default'		=> '0',
					'sort_order'	=> ''
				),
			'quickcheckout_field_comment'		=> array(
					'display'		=> '0',
					'required'		=> '0',
					'default'		=> '',
					'sort_order'	=> ''
				),
			'quickcheckout_coupon'				=> '0',
			'quickcheckout_voucher'				=> '0',
			'quickcheckout_reward'				=> '0',
			'quickcheckout_cart'				=> '1',
			'quickcheckout_login_module'		=> '1',
			'quickcheckout_html_header'			=> array(),
			'quickcheckout_html_footer'			=> array(),
			'quickcheckout_payment_module'		=> '1',
			'quickcheckout_payment_reload'		=> '0',
			'quickcheckout_payment'				=> '1',
			'quickcheckout_payment_logo'		=> array(),
			'quickcheckout_shipping_module'		=> '1',
			'quickcheckout_shipping'			=> '1',
			'quickcheckout_shipping_title_display'	=> '1',
			'quickcheckout_shipping_reload'		=> '0',
			'quickcheckout_shipping_logo'		=> array(),
			'quickcheckout_survey'				=> '0',
			'quickcheckout_survey_required'		=> '0',
			'quickcheckout_survey_text'			=> array(),
			'quickcheckout_delivery'			=> '0',
			'quickcheckout_delivery_time'		=> '0',
			'quickcheckout_delivery_required'	=> '0',
			'quickcheckout_delivery_unavailable'=> '"2017-10-31", "2017-08-11", "2017-12-25"',
			'quickcheckout_delivery_min'		=> '1',
			'quickcheckout_delivery_max'		=> '30',
			'quickcheckout_delivery_days_of_week'	=> '',
			'quickcheckout_shipping_title_display'	=> '0',
			'quickcheckout_show_shipping_address'	=> '0',
			'status'							=> '1',
		);
		
		$this->model_setting_setting->editSetting('quickcheckout', $data);
		
		$this->load->model('setting/store');
		
		$stores = $this->model_setting_store->getStores();
		
		foreach ($stores as $store) {
			$this->model_setting_setting->editSetting('quickcheckout', $data, $store['store_id']);
		}
		
		// Layout
		if (!$this->getLayout()) {
			$this->load->model('design/layout');
			
			$layout_data = array(
				'name'			=> 'Custom Quick Checkout',
				'layout_route'	=> array(
					array(
						'store_id'	=> 0,
						'route'		=> 'extension/quickcheckout/checkout'
					)
				)
			);
			
			$this->model_design_layout->addLayout($layout_data);
		}
		
		$this->load->model('setting/event');
		
		$this->model_setting_event->addEvent('module_quickcheckout', 'catalog/controller/checkout/checkout/before', 'extension/quickcheckout/checkout/eventPreControllerCheckoutCheckout');
		$this->model_setting_event->addEvent('module_quickcheckout', 'catalog/controller/checkout/success/before', 'extension/quickcheckout/checkout/eventPreControllerCheckoutSuccess');
	}
	
	public function uninstall() {
		if (!$this->user->hasPermission('modify', 'extension/extension/module')) {
			return;
		}
		
		if ($this->getLayout()) {
			$this->load->model('design/layout');
			
			$this->model_design_layout->deleteLayout($this->getLayout());
		}

		$this->load->model('setting/setting');
		$data2 = array(
			'status'							=> '0',
			'module_quickcheckout_status'		=> '0',
		);
		$this->model_setting_setting->editSetting('module_quickcheckout', $data2);
		
		$this->load->model('setting/event');

		$this->model_setting_event->deleteEventByCode('module_quickcheckout');
	}
	
	private function getLayout() {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "layout_route WHERE route = 'extension/quickcheckout/checkout'");
		
		if ($query->num_rows) {
			return $query->row['layout_id'];
		}
		
		return false;
	}
	
	protected function validate() {
		if (!$this->user->hasPermission('modify', 'extension/module/' . $this->code)) {
			$this->error['warning'] = $this->language->get('error_permission');
		}
		
		return !$this->error;
	}
}