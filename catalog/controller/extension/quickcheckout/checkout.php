<?php
class ControllerExtensionQuickCheckoutCheckout extends Controller {
	protected $code = 'quickcheckout';

	
	public function index() {

		if ($this->config->get('quickcheckout_force_bootstrap')) {
			$this->document->addScript('catalog/view/javascript/bootstrap/js/bootstrap.min.js');
			$this->document->addStyle('catalog/view/javascript/bootstrap/css/bootstrap.min.css');
		}

		$this->document->addScript('catalog/view/javascript/jquery/quickcheckout/quickcheckout.js');
		
		if ($this->config->get('quickcheckout_load_screen')) {
			$this->document->addScript('catalog/view/javascript/jquery/quickcheckout/quickcheckout.block.js');
		}
		
		$theme = $this->config->get('config_theme');
		
		if (file_exists(DIR_TEMPLATE . $theme . '/stylesheet/quickcheckout.css')) {
			$this->document->addStyle('catalog/view/theme/' . $theme . '/stylesheet/quickcheckout.css');
		} else {
			$this->document->addStyle('catalog/view/theme/default/stylesheet/quickcheckout.css');
		}
		
		if ($this->config->get('quickcheckout_layout') == '1') {
			$stylesheet = 'one';
		} elseif ($this->config->get('quickcheckout_layout') == '2') {
			$stylesheet = 'two';
		} elseif ($this->config->get('quickcheckout_layout') == '3') {
			$stylesheet = 'three';
		} else {
			$stylesheet = 'custom';
			$this->document->addScript('catalog/view/javascript/jquery/quickcheckout/jquery.tinysort.min.js');
		}
		
		if (file_exists(DIR_TEMPLATE . $theme . '/stylesheet/quickcheckout_' . $stylesheet . '.css')) {
			$data['stylesheet'] = 'catalog/view/theme/' . $theme . '/stylesheet/quickcheckout_' . $stylesheet . '.css';
		} else {
			$data['stylesheet'] = 'catalog/view/theme/default/stylesheet/quickcheckout_' . $stylesheet . '.css';
		}
		
		if ($this->config->get('quickcheckout_responsive')) {
			if (file_exists(DIR_TEMPLATE . $theme . '/stylesheet/quickcheckout_mobile.css')) {
				$data['mobile_stylesheet'] = 'catalog/view/theme/' . $theme . '/stylesheet/quickcheckout_mobile.css';
			} else {
				$data['mobile_stylesheet'] = 'catalog/view/theme/default/stylesheet/quickcheckout_mobile.css';
			}
		} else {
			$data['mobile_stylesheet'] = '';
		}
		

		if (!$this->config->get('quickcheckout_debug') || !isset($this->request->get['debug'])) {
			if (!$this->config->get('quickcheckout_status')) {
				$this->response->redirect($this->url->link('checkout/checkout', '', true));
			}
		}
		
		//$this->document->addScript('catalog/view/javascript/jquery/datetimepicker/moment/moment.min.js');
		$this->document->addScript('catalog/view/javascript/jquery/quickcheckout/moment-with-locales.min.js');
		$this->document->addScript('catalog/view/javascript/jquery/quickcheckout/bootstrap-datetimepicker.min.js');
		$this->document->addStyle('catalog/view/javascript/jquery/quickcheckout/bootstrap-datetimepicker.min.css');

		$data = array_merge($data, $this->load->language('checkout/checkout'));
		$data = array_merge($data, $this->load->language('extension/quickcheckout/checkout'));
		$data['error_warning'] = '';

		// Required by klarna
		if ($this->config->get('klarna_account') || $this->config->get('klarna_invoice')) {
			$this->document->addScript('http://cdn.klarna.com/public/kitt/toc/v1.0/js/klarna.terms.min.js');
		}

		// Validate cart has products
		if (!$this->cart->hasProducts() && empty($this->session->data['vouchers'])) {
	  		$this->response->redirect($this->url->link('checkout/cart'));
    	}
		
		// Validate minimum quantity requirements.			
		$products = $this->cart->getProducts();
				
		foreach ($products as $product) {
			$product_total = 0;
				
			foreach ($products as $product_2) {
				if ($product_2['product_id'] == $product['product_id']) {
					$product_total += $product_2['quantity'];
				}
			}		
			
			if ($product['minimum'] > $product_total) {
				$data['error_warning_minimum'] = sprintf($this->language->get('error_minimum'), $product['name'], $product['minimum']);
			} else {
				$data['error_warning_minimum'] = '';
			}				
		}

		// Validate cart has stock, minimum order total and minimum quantity product
		if (!$this->cart->hasStock() && (!$this->config->get('config_stock_checkout') || $this->config->get('config_stock_warning'))) {
            $data['error_warning'] = $this->language->get('error_stock');
        } elseif ($this->cart->getTotal() < (float)$this->config->get('quickcheckout_minimum_order')) {
			$this->session->data['error'] = sprintf($this->language->get('error_minimum_order'), $this->currency->format($this->config->get('quickcheckout_minimum_order'), $this->session->data['currency']));
			$data['error_warning'] = sprintf($this->language->get('error_minimum_order'), $this->currency->format($this->config->get('quickcheckout_minimum_order'), $this->session->data['currency']));
		} 
		
		$this->document->setTitle($this->language->get('heading_title')); 
		$data['heading_title'] = $this->language->get('heading_title'); 

		$data['breadcrumbs'] = array();

      	$data['breadcrumbs'][] = array(
        	'text'      => $this->language->get('text_home'),
			'href'      => $this->url->link('common/home')
      	); 

      	$data['breadcrumbs'][] = array(
        	'text'      => $this->language->get('text_cart'),
			'href'      => $this->url->link('checkout/cart')
      	);
		
      	$data['breadcrumbs'][] = array(
        	'text'      => $this->language->get('heading_title'),
			'href'      => $this->url->link('extension/quickcheckout/checkout', '', true)
      	);	
		
		if ($this->config->get('quickcheckout_html_header')) {
			$html_header = $this->config->get('quickcheckout_html_header');
			
			if (!empty($html_header[$this->config->get('config_language_id')])) {
				$data['html_header'] = html_entity_decode($html_header[$this->config->get('config_language_id')]);
			} else {
				$data['html_header'] = '';
			}
		} else {
			$data['html_header'] = '';
		}
		
		if ($this->config->get('quickcheckout_html_footer')) {
			$html_footer = $this->config->get('quickcheckout_html_footer');
			
			if (!empty($html_footer[$this->config->get('config_language_id')])) {
				$data['html_footer'] = html_entity_decode($html_footer[$this->config->get('config_language_id')]);
			} else {
				$data['html_footer'] = '';
			}
		} else {
			$data['html_footer'] = '';
		}
		
		// All variables
		$data['logged'] = $this->customer->isLogged();
		$data['shipping_required'] = $this->cart->hasShipping();
		$data['show_shipping_address'] = $this->config->get('quickcheckout_show_shipping_address');
		$data['load_screen'] = $this->config->get('quickcheckout_load_screen');
		$data['loading_display'] = $this->config->get('quickcheckout_loading_display') ? 'true' : 'false';
		$data['edit_cart'] = $this->config->get('quickcheckout_edit_cart');
		$data['highlight_error'] = $this->config->get('quickcheckout_highlight_error');
		$data['text_error'] = $this->config->get('quickcheckout_text_error');
		$data['quickcheckout_layout'] = $this->config->get('quickcheckout_layout');
		$data['slide_effect'] = $this->config->get('quickcheckout_slide_effect');
		$data['debug'] = $this->config->get('quickcheckout_debug');
		$data['auto_submit'] = $this->config->get('quickcheckout_auto_submit');
		$data['coupon_module'] = $this->config->get('quickcheckout_coupon');
		$data['voucher_module'] = $this->config->get('quickcheckout_voucher');
		$data['reward_module'] = $this->config->get('quickcheckout_reward');
		$data['cart_module'] = $this->config->get('quickcheckout_cart');
		$data['payment_module'] = $this->config->get('quickcheckout_payment_module');
		$data['shipping_module'] = $this->config->get('quickcheckout_shipping_module');
		$data['login_module'] = $this->config->get('quickcheckout_login_module');
		$data['save_data'] = $this->config->get('quickcheckout_save_data');
		$data['custom_css'] = html_entity_decode($this->config->get('quickcheckout_custom_css'), ENT_QUOTES);
		$data['confirmation_page'] = $this->config->get('quickcheckout_confirmation_page');
		$data['column'] = $this->config->get('quickcheckout_column');
		$data['step'] = $this->config->get('quickcheckout_step');
		
		if (!$this->customer->isLogged()) {
			$data['guest'] = $this->load->controller('extension/quickcheckout/guest');
			$data['guest_shipping'] = $this->load->controller('extension/quickcheckout/guest_shipping');
			
			if ($this->config->get('quickcheckout_login_module')) {
				$data['login'] = $this->load->controller('extension/quickcheckout/login');
			}
		} else {
			$data['payment_address'] = $this->load->controller('extension/quickcheckout/payment_address');
			$data['shipping_address'] = $this->load->controller('extension/quickcheckout/shipping_address');
		}
		
		$data['voucher'] = $this->load->controller('extension/quickcheckout/voucher');
		$data['terms'] = $this->load->controller('extension/quickcheckout/terms');
		
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['column_right'] = $this->load->controller('common/column_right');
		$data['content_top'] = $this->load->controller('common/content_top');
		$data['content_bottom'] = $this->load->controller('common/content_bottom');
		$data['footer'] = $this->load->controller('common/footer');
		$data['header'] = $this->load->controller('common/header');

		$this->response->setOutput($this->load->view('extension/quickcheckout/checkout', $data));
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
		
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
	
	public function customfield() {
		$json = array();

		$this->load->model('account/custom_field');

		// Customer Group
		if (isset($this->request->get['customer_group_id']) && is_array($this->config->get('config_customer_group_display')) && in_array($this->request->get['customer_group_id'], $this->config->get('config_customer_group_display'))) {
			$customer_group_id = $this->request->get['customer_group_id'];
		} else {
			$customer_group_id = $this->config->get('config_customer_group_id');
		}

		$custom_fields = $this->model_account_custom_field->getCustomFields($customer_group_id);

		foreach ($custom_fields as $custom_field) {
			$json[] = array(
				'custom_field_id' => $custom_field['custom_field_id'],
				'required'        => $custom_field['required']
			);
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
	
	public function save() {
		$json = array();

		if (!$this->customer->isLogged() && isset($this->request->get['type'])) {
			if ($this->request->get['type'] == 'payment') {
				$this->session->data['guest']['firstname'] = $this->request->post['firstname'];
				$this->session->data['guest']['lastname'] = $this->request->post['lastname'];
				$this->session->data['guest']['email'] = $this->request->post['email'];
				$this->session->data['guest']['telephone'] = $this->request->post['telephone'];
				$this->session->data['guest']['shipping_address'] = isset($this->request->post['shipping_address']) ? true : false;
				$this->session->data['guest']['create_account'] = isset($this->request->post['create_account']) ? true : false;
				
				$this->session->data['payment_address']['firstname'] = $this->request->post['firstname'];
				$this->session->data['payment_address']['lastname'] = $this->request->post['lastname'];
				$this->session->data['payment_address']['company'] = $this->request->post['company'];
				$this->session->data['payment_address']['address_1'] = $this->request->post['address_1'];
				$this->session->data['payment_address']['address_2'] = $this->request->post['address_2'];
				$this->session->data['payment_address']['postcode'] = $this->request->post['postcode'];
				$this->session->data['payment_address']['city'] = $this->request->post['city'];
				$this->session->data['payment_address']['country_id'] = $this->request->post['country_id'];
				$this->session->data['payment_address']['zone_id'] = $this->request->post['zone_id'];
				
				if (isset($this->request->post['custom_field']['address'])) {
					$this->session->data['payment_address']['custom_field'] = $this->request->post['custom_field']['address'];
				} else {
					$this->session->data['payment_address']['custom_field'] = array();
				}
			} else {
				$this->session->data['shipping_address']['firstname'] = $this->request->post['firstname'];
				$this->session->data['shipping_address']['lastname'] = $this->request->post['lastname'];
				$this->session->data['shipping_address']['company'] = $this->request->post['company'];
				$this->session->data['shipping_address']['address_1'] = $this->request->post['address_1'];
				$this->session->data['shipping_address']['address_2'] = $this->request->post['address_2'];
				$this->session->data['shipping_address']['postcode'] = $this->request->post['postcode'];
				$this->session->data['shipping_address']['city'] = $this->request->post['city'];
				$this->session->data['shipping_address']['country_id'] = $this->request->post['country_id'];
				$this->session->data['shipping_address']['zone_id'] = $this->request->post['zone_id'];
				
				if (isset($this->request->post['custom_field'])) {
					$this->session->data['shipping_address']['custom_field'] = $this->request->post['custom_field']['address'];
				} else {
					$this->session->data['shipping_address']['custom_field'] = array();
				}
			}
		}
		
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
	
	public function eventPreControllerCheckoutCheckout($route, $data) {
		if ($this->config->get('quickcheckout_status')) {
			$this->response->redirect($this->url->link('extension/quickcheckout/checkout', '', true));
		}
	}
	
	public function eventPreControllerCheckoutSuccess($route, $data) {
		unset($this->session->data['order_comment']);
		unset($this->session->data['delivery_date']);
		unset($this->session->data['delivery_time']);
		unset($this->session->data['survey']);
		unset($this->session->data['shipping_address']);
		unset($this->session->data['payment_address']);
	}
}