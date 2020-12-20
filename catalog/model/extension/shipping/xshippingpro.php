<?php 
class ModelExtensionShippingXshippingpro extends Model {
	
	function getQuote($address) {

		$this->load->language('extension/shipping/xshippingpro');
		$this->load->model('catalog/product');

		$language_id=$this->config->get('config_language_id');
		$store_id=(isset($_POST['store_id']))?$_POST['store_id']:$this->config->get('config_store_id');
		$payment_method=isset($this->session->data['payment_method']['code'])?$this->session->data['payment_method']['code']:'';
		if(isset($this->session->data['default']['payment_method']['code'])) $payment_method = $this->session->data['default']['payment_method']['code'];
		
		$is_admin = (isset($_REQUEST['route']) && strpos($_REQUEST['route'],'api')!==false)?true:false;
		$is_quote = (isset($_REQUEST['route']) && strpos($_REQUEST['route'],'shipping/quote')!==false)?true:false;
		if (isset($_GET['store_id']) && $_GET['store_id'] != "") {
			$store_id = $_GET['store_id'];
		}

		/*
		if (isset($_REQUEST['route']) && strpos($_REQUEST['route'],'journal2/checkout') !== false && isset($this->session->data['shipping_method'])) {
		 	unset($this->session->data['shipping_method']);
		} */
		
		/*Quick checkout fucking bug fix*/
		if (isset($address['zone_id'])
			&& !$address['zone_id']
			&& isset($this->session->data['shipping_address'])
			&& isset($this->session->data['shipping_address']['zone_id'])
			&& $this->session->data['shipping_address']['zone_id']) {
			$address['zone_id'] = $this->session->data['shipping_address']['zone_id'];
		}

		/*Quick checkout fucking bug fix*/
		if (isset($address['country_id'])
			&& !$address['country_id']
			&& isset($this->session->data['shipping_address'])
			&& isset($this->session->data['shipping_address']['country_id'])
			&& $this->session->data['shipping_address']['country_id']) {
			$address['country_id'] = $this->session->data['shipping_address']['country_id'];
		} 

		/*Quick checkout f***ing bug fix*/
		if (isset($address['city'])
			&& !$address['city']
			&& isset($this->session->data['shipping_address'])
			&& isset($this->session->data['shipping_address']['city'])
			&& $this->session->data['shipping_address']['city']) {
		  $address['city'] = $this->session->data['shipping_address']['city'];
		}  

		$method_data = array();
		$quote_data = array();
		$sort_data = array(); 

		$xshippingpro_heading=$this->config->get('shipping_xshippingpro_heading');
		$xshippingpro_group=$this->config->get('shipping_xshippingpro_group');
		$xshippingpro_group_limit=$this->config->get('shipping_xshippingpro_group_limit');
		$xshippingpro_sub_group=$this->config->get('shipping_xshippingpro_sub_group');
		$xshippingpro_sub_group_limit=$this->config->get('shipping_xshippingpro_sub_group_limit');
		$xshippingpro_sub_group_name=$this->config->get('shipping_xshippingpro_sub_group_name');
		$xshippingpro_debug=$this->config->get('shipping_xshippingpro_debug');

		$xshippingpro_group=($xshippingpro_group)?$xshippingpro_group:'no_group';
		$xshippingpro_group_limit=($xshippingpro_group_limit)?(int)$xshippingpro_group_limit:1;

		$xshippingpro_sub_group=($xshippingpro_sub_group)?$xshippingpro_sub_group:array();
		$xshippingpro_sub_group_limit=($xshippingpro_sub_group_limit)?$xshippingpro_sub_group_limit:array();
		$xshippingpro_sub_group_name=($xshippingpro_sub_group_name)?$xshippingpro_sub_group_name:array();

		$xshippingpro_sorting=$this->config->get('shipping_xshippingpro_sorting');
		$xshippingpro_sorting = ($xshippingpro_sorting)?(int)$xshippingpro_sorting:1;

		$currency_code = isset($this->session->data['currency']) ? $this->session->data['currency'] : $this->config->get('config_currency');
		$order_info='';
		if (isset($this->session->data['order_id'])) {
			$this->load->model('checkout/order');
			$order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);
		}

		if (isset($this->request->get['order_id'])) {
			$this->load->model('checkout/order');
			$order_info = $this->model_checkout_order->getOrder($this->request->get['order_id']);
		}

		$cart_products=$this->cart->getProducts();
		$cart_weight=$this->cart->getWeight(); 
		$cart_quantity=$this->cart->countProducts();
		$cart_subtotal=$this->cart->getSubTotal();
		$cart_total=$this->cart->getTotal();
		$coupon_value=0;
		$grand_total = 0;


		$xtotals = array();
		$xtaxes = $this->cart->getTaxes();
		$xtotal = $cart_total;

				// Because __call can not keep var references so we put them into an array. 
		$xtotal_data = array(
			'totals' => &$xtotals,
			'taxes'  => &$xtaxes,
			'total'  => &$xtotal
			);

		$coupon_code = '';

		if (isset($this->session->data['default']['coupon']) && $this->session->data['default']['coupon']) {
			$coupon_code = $this->session->data['default']['coupon'];
		}

		if (isset($this->session->data['coupon']) && $this->session->data['coupon']) {
			$coupon_code = $this->session->data['coupon'];
		}

		if ($coupon_code) {

			if ($this->config->get('total_coupon_status')) {
				$this->load->model('extension/total/coupon');
				$this->{'model_extension_total_coupon'}->getTotal($xtotal_data);
			}
			if (isset($xtotal_data['totals'][0]['code']) && $xtotal_data['totals'][0]['code']=='coupon') {
				$coupon_value=$xtotal_data['totals'][0]['value'];
			}
			$coupon_code = strtolower($coupon_code);  
		}

		/* reward calc*/
		$xtotals = array();
				// Because __call can not keep var references so we put them into an array. 
		$xtotal_data = array(
			'totals' => &$xtotals,
			'taxes'  => &$xtaxes,
			'total'  => &$xtotal
			);
		$reward=0;
		if (isset($this->session->data['reward']) && $this->session->data['reward']) {

			if ($this->config->get('total_reward_status')) {
				$this->load->model('extension/total/reward');
				$this->{'model_extension_total_reward'}->getTotal($xtotal_data);
			}
			if (isset($xtotal_data['totals'][0]['code']) && $xtotal_data['totals'][0]['code']=='reward') {
				$reward=$xtotal_data['totals'][0]['value'];
			} 
		}

		$cart_total_without_coupon=$cart_total+$coupon_value+$reward;

		$cart_categories=array();
		$cart_product_ids=array();
		$cart_manufacturers=array();
		$cart_options = array();
		$cart_volume=0;
		$multi_category=false;
		foreach($cart_products as $inc=>$product) {
			$product_categories=$this->model_catalog_product->getCategories($product['product_id']);
			$cart_product_ids[]=$product['product_id']; 
			$cart_products[$inc]['categories']=array();
			if ($product_categories) {
				if (count($product_categories)>1)$multi_category=true;
				foreach($product_categories as $category) {
					$cart_categories[]=$category['category_id'];  
					$cart_products[$inc]['categories'][]=$category['category_id']; //store for future use 
				} 
			}
			
			$product_volume=(($product['width']*$product['height']*$product['length'])*$product['quantity']);		
			$cart_volume+=$product_volume; 
			$cart_products[$inc]['volume']=$product_volume; //store for future use
			$cart_products[$inc]['dimensional']=0; // just initialize for now. Will calc later for method wise
			$cart_products[$inc]['weight'] = $this->weight->convert($product['weight'], $product['weight_class_id'], $this->config->get('config_weight_class_id'));
			
			
			
			$product_info=$this->model_catalog_product->getProduct($product['product_id']);
			if ($product_info) {
				$cart_manufacturers[]=$product_info['manufacturer_id'];
				$cart_products[$inc]['manufacturer_id']=$product_info['manufacturer_id']; //store for future use
			}
			
			$cart_products[$inc]['options']=array();
			if (isset($product['option']) && $product['option'] && is_array($product['option'])) {
				foreach($product['option'] as $option) {
					if ($option['type'] == 'select' || $option['type'] == 'radio' || $option['type'] == 'checkbox') {
						$cart_options[]=$option['option_value_id'];  
				   		$cart_products[$inc]['options'][]=$option['option_value_id']; //store for future use 
				   	}
				   }
				}

		} 

		$cart_categories=array_unique($cart_categories);
		$cart_product_ids=array_unique($cart_product_ids);
		$cart_manufacturers=array_unique($cart_manufacturers);
		$cart_options=array_unique($cart_options);
		$operators= array('+','-','/','*');

		$debugging=array();
		$shipping_group_methods=array();
		$isGrandFound = false; 
		$isSubGroupFound = false;
		$hiddenMethods = array();

		$methods = $this->db->query("SELECT * FROM `" . DB_PREFIX . "xshippingpro`")->rows;

		foreach($methods as $single_method) {

			$no_of_tab = $single_method['tab_id'];
			$xshippingpro = $single_method['method_data'];
			$xshippingpro = @unserialize(@base64_decode($xshippingpro));
			if (!is_array($xshippingpro)) $xshippingpro = array();

			$debugging_message=array();

			if (!isset($xshippingpro['customer_group'])) $xshippingpro['customer_group']=array();
			if (!isset($xshippingpro['geo_zone_id'])) $xshippingpro['geo_zone_id']=array();
			if (!isset($xshippingpro['product_category'])) $xshippingpro['product_category']=array();
			if (!isset($xshippingpro['product_product'])) $xshippingpro['product_product']=array();
			if (!isset($xshippingpro['store'])) $xshippingpro['store']=array();
			if (!isset($xshippingpro['manufacturer'])) $xshippingpro['manufacturer']=array();
			if (!isset($xshippingpro['payment'])) $xshippingpro['payment']=array();
			if (!isset($xshippingpro['days'])) $xshippingpro['days']=array();
			if (!isset($xshippingpro['rate_start'])) $xshippingpro['rate_start']=array();
			if (!isset($xshippingpro['rate_end'])) $xshippingpro['rate_end']=array();
			if (!isset($xshippingpro['rate_total'])) $xshippingpro['rate_total']=array();
			if (!isset($xshippingpro['rate_block'])) $xshippingpro['rate_block']=array();
			if (!isset($xshippingpro['rate_partial'])) $xshippingpro['rate_partial']=array();
			if (!isset($xshippingpro['country'])) $xshippingpro['country']=array();

			if (!is_array($xshippingpro['customer_group'])) $xshippingpro['customer_group']=array();
			if (!is_array($xshippingpro['geo_zone_id'])) $xshippingpro['geo_zone_id']=array();
			if (!is_array($xshippingpro['product_category'])) $xshippingpro['product_category']=array();
			if (!is_array($xshippingpro['product_product'])) $xshippingpro['product_product']=array();
			if (!is_array($xshippingpro['store'])) $xshippingpro['store']=array();
			if (!is_array($xshippingpro['manufacturer'])) $xshippingpro['manufacturer']=array();
			if (!is_array($xshippingpro['payment'])) $xshippingpro['payment']=array();
			if (!is_array($xshippingpro['days'])) $xshippingpro['days']=array();
			if (!is_array($xshippingpro['rate_start'])) $xshippingpro['rate_start']=array();
			if (!is_array($xshippingpro['rate_end'])) $xshippingpro['rate_end']=array();
			if (!is_array($xshippingpro['rate_total'])) $xshippingpro['rate_total']=array();
			if (!is_array($xshippingpro['rate_block'])) $xshippingpro['rate_block']=array();
			if (!is_array($xshippingpro['rate_partial'])) $xshippingpro['rate_partial']=array();
			if (!is_array($xshippingpro['country'])) $xshippingpro['country']=array();

			if (!isset($xshippingpro['inc_weight'])) $xshippingpro['inc_weight']='';
			if (!isset($xshippingpro['dimensional_overfule'])) $xshippingpro['dimensional_overfule']='';
			if (!isset($xshippingpro['dimensional_factor']) || !$xshippingpro['dimensional_factor'])$xshippingpro['dimensional_factor']= ($xshippingpro['rate_type']=='volume' || $xshippingpro['rate_type']=='volume_method')?1:6000;

			if (!isset($xshippingpro['desc'])) $xshippingpro['desc']=array();
			if (!is_array($xshippingpro['desc'])) $xshippingpro['desc']=array();
			if (!isset($xshippingpro['name'])) $xshippingpro['name']=array();
			if (!is_array($xshippingpro['name'])) $xshippingpro['name']=array();

			if (!isset($xshippingpro['customer_group_all'])) $xshippingpro['customer_group_all']='';
			if (!isset($xshippingpro['geo_zone_all'])) $xshippingpro['geo_zone_all']='';
			if (!isset($xshippingpro['store_all'])) $xshippingpro['store_all']='';
			if (!isset($xshippingpro['manufacturer_all'])) $xshippingpro['manufacturer_all']='';
			if (!isset($xshippingpro['postal_all'])) $xshippingpro['postal_all']='';
			if (!isset($xshippingpro['coupon_all'])) $xshippingpro['coupon_all']='';
			if (!isset($xshippingpro['payment_all'])) $xshippingpro['payment_all']='';
			if (!isset($xshippingpro['postal'])) $xshippingpro['postal']='';
			if (!isset($xshippingpro['coupon'])) $xshippingpro['coupon']='';
			if (!isset($xshippingpro['postal_rule'])) $xshippingpro['postal_rule']='inclusive';
			if (!isset($xshippingpro['coupon_rule'])) $xshippingpro['coupon_rule']='inclusive';
			if (!isset($xshippingpro['time_start'])) $xshippingpro['time_start']='';
			if (!isset($xshippingpro['time_end'])) $xshippingpro['time_end']='';
			if (!isset($xshippingpro['rate_final'])) $xshippingpro['rate_final']='single';
			if (!isset($xshippingpro['rate_percent'])) $xshippingpro['rate_percent']='sub';
			if (!isset($xshippingpro['rate_min'])) $xshippingpro['rate_min']=0;
			if (!isset($xshippingpro['rate_max'])) $xshippingpro['rate_max']=0;
			if (!isset($xshippingpro['rate_add'])) $xshippingpro['rate_add']=0;
			if (!isset($xshippingpro['modifier_ignore'])) $xshippingpro['modifier_ignore']='';
			if (!isset($xshippingpro['country_all'])) $xshippingpro['country_all']='';

			if (!isset($xshippingpro['manufacturer_rule'])) $xshippingpro['manufacturer_rule']='2';
			if (!isset($xshippingpro['multi_category'])) $xshippingpro['multi_category']='all';  
			if (!isset($xshippingpro['additional'])) $xshippingpro['additional']=0; 
			if (!isset($xshippingpro['logo'])) $xshippingpro['logo']='';
			if (!isset($xshippingpro['group'])) $xshippingpro['group']=0;

			if (!isset($xshippingpro['order_total_start'])) $xshippingpro['order_total_start']=0;
			if (!isset($xshippingpro['order_total_end'])) $xshippingpro['order_total_end']=0;
			if (!isset($xshippingpro['weight_start'])) $xshippingpro['weight_start']=0;
			if (!isset($xshippingpro['weight_end'])) $xshippingpro['weight_end']=0;
			if (!isset($xshippingpro['quantity_start'])) $xshippingpro['quantity_start']=0;
			if (!isset($xshippingpro['quantity_end'])) $xshippingpro['quantity_end']=0;
			if (!isset($xshippingpro['mask'])) $xshippingpro['mask']='';
			if (!isset($xshippingpro['equation'])) $xshippingpro['equation']='';

			if (!isset($xshippingpro['option_all'])) $xshippingpro['option_all']='';
			if (!isset($xshippingpro['option'])) $xshippingpro['option']='1';
			if (!isset($xshippingpro['product_option'])) $xshippingpro['product_option']=array();
			if (!is_array($xshippingpro['product_option'])) $xshippingpro['product_option']=array();

			if(!isset($xshippingpro['hide']))$xshippingpro['hide']=array();
			if(!is_array($xshippingpro['hide']))$xshippingpro['hide']=array();
			if (!isset($xshippingpro['address_type'])) $xshippingpro['address_type']='delivery';
			if(!isset($xshippingpro['city_all']))$xshippingpro['city_all']='';
			if(!isset($xshippingpro['city']))$xshippingpro['city']='';
			if(!isset($xshippingpro['city_rule']))$xshippingpro['city_rule']='inclusive';


			$shipping_group_methods[intval($xshippingpro['group'])][]=$no_of_tab;

			if ($xshippingpro['rate_type']=='grand' && !$isGrandFound) {
				/* Finding grand-total */
				$this->load->model('setting/extension');
				$total_mods = $this->model_setting_extension->getExtensions('total');

				$xtotals = array();
				$xtaxes = $this->cart->getTaxes();
				$xtotal = 0;

				// Because __call can not keep var references so we put them into an array. 
				$xtotal_data = array(
					'totals' => &$xtotals,
					'taxes'  => &$xtaxes,
					'total'  => &$xtotal
				);

				$sort_order = array();
				foreach ($total_mods as $key => $value) {
					$sort_order[$key] = $this->config->get('total_'.$value['code'] . '_sort_order');
				}

				array_multisort($sort_order, SORT_ASC, $total_mods);
				$isTotalFound = false;
				foreach ($total_mods as $total_mod) {

					if ($total_mod['code']=='shipping') continue;

					if ($this->config->get('total_'.$total_mod['code'] . '_status')) {
						$this->load->model('extension/total/' . $total_mod['code']);

						$this->{'model_extension_total_' . $total_mod['code']}->getTotal($xtotal_data);
						if ($total_mod['code']=='total') {
							$grand_total = $xtotal_data['total'];
							$isTotalFound = true;
							break;
						}
					}
				}

				if (!$grand_total && !$isTotalFound) $grand_total = $cart_total;			
				/* end of grand-total */	
				$isGrandFound =true;
			}

			$status = true;
			$block_found=false;

			if ($xshippingpro['geo_zone_id'] && $xshippingpro['geo_zone_all']!=1) {
				$country_id = ($xshippingpro['address_type'] == 'payment') && isset($this->session->data['payment_address']) && $this->session->data['payment_address'] ? $this->session->data['payment_address']['country_id'] : $address['country_id'];
				$zone_id = ($xshippingpro['address_type'] == 'payment') && isset($this->session->data['payment_address']) && $this->session->data['payment_address'] ? $this->session->data['payment_address']['zone_id'] : $address['zone_id'];

				$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "zone_to_geo_zone WHERE geo_zone_id in (" . implode(',',$xshippingpro['geo_zone_id']) . ") AND country_id = '" . (int)$country_id . "' AND (zone_id = '" . (int)$zone_id . "' OR zone_id = '0')"); 
			}

			if ($xshippingpro['geo_zone_all']!=1) {
				if ($xshippingpro['geo_zone_id'] && $query->num_rows==0) {
					$status = false;
					$debugging_message[]='GEO Zone';
				} 
			}

			if ($xshippingpro['city_all']!=1) {
				$city = isset($address['city']) ? strtolower(trim($address['city'])) : '';
				$cities = explode(PHP_EOL, $xshippingpro['city']);
				$city_rule=($xshippingpro['city_rule']=='inclusive')?false:true;

				$cities = array_map('strtolower', $cities);
				$cities = array_map('trim', $cities);

				if (in_array($city, $cities)===$city_rule) {
					$status = false;
					$debugging_message[]='City - ('.$city.')';
				} 
			}

			if ($xshippingpro['country_all']!=1) {
				if (!in_array((int)$address['country_id'], $xshippingpro['country'])) {
					$status = false;
					$debugging_message[]='Country';
				} 
			}

			if (!$xshippingpro['status']) {
				$status = false;
				$debugging_message[]='Status';
			}

			/*store checking*/
			if ($xshippingpro['store_all']!=1) {
				if (!in_array((int)$store_id,$xshippingpro['store'])) {
					$status = false;
					$debugging_message[]='Store';
				}
			}

			$method_categories=array();
			$exclude_categories = array();
			// if multi-cateogry rule is any, then recalculate method categories
			if ($multi_category && $xshippingpro['multi_category']=='any') {
				foreach($cart_products as $product) {
					if (array_intersect($xshippingpro['product_category'],$product['categories'])) {
						$method_categories=array_merge($method_categories, $product['categories']); 
					}
					else {
						$exclude_categories=array_merge($exclude_categories, $product['categories']);  
					} 
				}
				$method_categories=array_unique($method_categories);
				$method_categories=array_diff($method_categories, $exclude_categories); 
				$xshippingpro['product_category']=$method_categories;
			}

			$resultant_category=array_intersect($xshippingpro['product_category'],$cart_categories);
			$resultant_products=array_intersect($xshippingpro['product_product'],$cart_product_ids);
			$resultant_manufacturers=array_intersect($xshippingpro['manufacturer'],$cart_manufacturers);
			$resultant_options=array_intersect($xshippingpro['product_option'],$cart_options);

			// print_r($xshippingpro['product_category']);
			// print_r($resultant_category);

			/*Manufacturer checking*/
			$applicable_manufacturer=$cart_manufacturers;
			if ($xshippingpro['manufacturer_all']!=1) {

				if ($xshippingpro['manufacturer_rule']==2) {
					if (count($resultant_manufacturers)!=count($xshippingpro['manufacturer'])) {
						$status = false; 
						$debugging_message[]='Manufacturer';
					}
					$applicable_manufacturer=$xshippingpro['manufacturer'];
				}

				if ($xshippingpro['manufacturer_rule']==3) {
					if (!$resultant_manufacturers) {
						$status = false; 
						$debugging_message[]='Manufacturer';
					}
					$applicable_manufacturer=$xshippingpro['manufacturer'];
				}

				if ($xshippingpro['manufacturer_rule']==4) {

					if (count($resultant_manufacturers)!=count($xshippingpro['manufacturer']) || count($resultant_manufacturers)!=count($cart_manufacturers)) {
						$status = false; 
						$debugging_message[]='Manufacturer';
					}
					$applicable_manufacturer=$xshippingpro['manufacturer'];
				}

				if ($xshippingpro['manufacturer_rule']==5) {
					if ($resultant_manufacturers) {
						$status = false; 
						$debugging_message[]='Manufacturer';
					}
					$applicable_manufacturer= array_diff($cart_manufacturers, $xshippingpro['manufacturer']); 
				}

				if ($xshippingpro['manufacturer_rule']==6) {

					if (!$resultant_manufacturers || count($resultant_manufacturers)!=count($cart_manufacturers)) {
						$status = false; 
						$debugging_message[]='Manufacturer';
					}
					$applicable_manufacturer=$xshippingpro['manufacturer'];
				}

				if ($xshippingpro['manufacturer_rule']==7) {

					if ($resultant_manufacturers && count($resultant_manufacturers)==count($cart_manufacturers)) {
						$status = false; 
						$debugging_message[]='Manufacturer';
					}

					$applicable_manufacturer= array_diff($cart_manufacturers, $xshippingpro['manufacturer']);
				}

			}
			/* End manufacturer checking*/

		    /*Customer group checking*/
			if (isset($_POST['customer_group_id']) && $_POST['customer_group_id']) {
				$customer_group_id=$_POST['customer_group_id'];
			}
			elseif (isset($_GET['customer_group_id']) && $_GET['customer_group_id']) {
				$customer_group_id=$_GET['customer_group_id'];
			}
			elseif ($this->customer->isLogged()) {
				$customer_group_id = $this->customer->getGroupId();
			} else {
				$customer_group_id = $this->config->get('config_customer_group_id');
			}

			if ($is_admin) $customer_group_id =  '';

			if ($customer_group_id && !in_array($customer_group_id,$xshippingpro['customer_group']) && $xshippingpro['customer_group_all']!=1) {
				$status = false; 
				$debugging_message[]='Customer Group';
			}

			/*Rearraging method for x-payment*/
			$payment_methods=array();
			if(is_array($xshippingpro['payment'])){
				foreach($xshippingpro['payment'] as $method){
					$payment_methods[]=$method;
					$payment_methods[]=$method.'.'.$method;
				}
			}
				
			/*Payment checking*/		
			if ($xshippingpro['payment_all']!=1 && $payment_method) {
				if($xshippingpro['payment']){
					if(!in_array($payment_method,$payment_methods)){
						$status = false; 
						$debugging_message[]='Payment';
					}  
				}

				if(!$payment_methods && $payment_method){  
					$status = false;  
					$debugging_message[]='Payment';
				}       
			}

			/*postal checking*/
			if ($xshippingpro['postal_all']!=1) {
				$postal=$xshippingpro['postal']; 
				$postal_rule=$xshippingpro['postal_rule'];
				$postal_rule=($postal_rule=='inclusive')?true:false;
				$postal_found=false;
				if ($postal && isset($address['postcode'])) {
					$deliver_postal = str_replace('-','',$address['postcode']); 
					$postal=explode(',',trim($postal));
					foreach($postal as $postal_code) {
						$postal_code=trim($postal_code);

						/* In case of range postal code - only numeric */
						if (strpos($postal_code,'-')!==false && substr_count($postal_code,'-')==1 ) {
							list($start_postal,$end_postal)=	explode('-',$postal_code); 

							$start_postal=(int)$start_postal;
							$end_postal=(int)$end_postal;

							if ( $deliver_postal >= $start_postal &&  $deliver_postal <= $end_postal) {
								$postal_found=true;
							}
						}
						/* End of range checking*/

						/* In case of range postal code wiht prefix*/
						elseif (strpos($postal_code,'-')!==false && substr_count($postal_code,'-')==2) {
							list($prefix,$start_postal,$end_postal)=	explode('-',$postal_code); 
							$start_postal=(int)$start_postal;
							$end_postal=(int)$end_postal;

							if ($start_postal<=$end_postal) {
								for($i=$start_postal;$i<=$end_postal;$i++) {

									if (preg_match('/^'.str_replace(array('\*','\?'),array('(.*?)','[a-zA-Z0-9]'),preg_quote($prefix.$i)).'$/i',trim($deliver_postal))) {
										$postal_found=true; 
										break; 
									}

								}
							}
						}
						/* End of range checking*/
						/* In case of range postal code wiht prefix and sufiix*/
						elseif (strpos($postal_code,'-')!==false && substr_count($postal_code,'-')==3) {
							list($prefix,$start_postal,$end_postal,$sufiix)=	explode('-',$postal_code); 
							$start_postal=(int)$start_postal;
							$end_postal=(int)$end_postal;

							if ($start_postal<=$end_postal) {
								for($i=$start_postal;$i<=$end_postal;$i++) {

									if (preg_match('/^'.str_replace(array('\*','\?'),array('(.*?)','[a-zA-Z0-9]'),preg_quote($prefix.$i.$sufiix)).'$/i',trim($deliver_postal))) {
										$postal_found=true;  
										break;
									}
								}
							}
						}
						/* End of range checking*/

						/* In case of wildcards use code*/
						elseif (strpos($postal_code,'*')!==false || strpos($postal_code,'?')!==false) {

							if (preg_match('/^'.str_replace(array('\*','\?'),array('(.*?)','[a-zA-Z0-9]'),preg_quote($postal_code)).'$/i',trim($deliver_postal))) {
								$postal_found=true;  
							}


						}
						/* End of wildcards checking*/
						else{

							if (trim(strtolower($deliver_postal))==strtolower($postal_code)) {
								$postal_found=true; 
							} 
						}
					}

					if ((boolean)$postal_found!==$postal_rule) {
						$status = false;
						$debugging_message[]='Zip/Postal -'.$address['postcode'];
					} 
				}	  
			}

			/*coupon checking*/
			if ($xshippingpro['coupon_all']!=1) {
				$coupon=$xshippingpro['coupon']; 
				$coupon_rule=$xshippingpro['coupon_rule'];

				if ($coupon) {
					$coupon=explode(',',trim($coupon));
					$coupon_rule=($coupon_rule=='inclusive')?false:true;

					if ($coupon_rule===false && !$coupon_code) {
						$status = false;
						$debugging_message[]='Coupon';
					}

					if ($coupon_code && in_array(trim($coupon_code),$coupon)===$coupon_rule) {
						$status = false;
						$debugging_message[]='Coupon';
					} 
				}	  
			}


			/*category checking*/
			$applicable_category=$cart_categories;
			if ($xshippingpro['category']==2) {
				if (count($resultant_category)!=count($xshippingpro['product_category'])) {
					$status = false; 
					$debugging_message[]='Category';
				}
				$applicable_category=$xshippingpro['product_category'];
			}

			if ($xshippingpro['category']==3) {
				if (!$resultant_category) {
					$status = false; 
					$debugging_message[]='Category';
				}
				$applicable_category=$xshippingpro['product_category'];
			}

			if ($xshippingpro['category']==4) {

				if (count($resultant_category)!=count($xshippingpro['product_category']) || count($resultant_category)!=count($cart_categories)) {
					$status = false; 
					$debugging_message[]='Category';
				}
				$applicable_category=$xshippingpro['product_category'];
			}

			if ($xshippingpro['category']==5) {
				if ($resultant_category) {
					$status = false; 
					$debugging_message[]='Category';
				}
				$applicable_category= array_diff($cart_categories, $xshippingpro['product_category']); 
			}

			if ($xshippingpro['category']==6) {

				if (!$resultant_category || count($resultant_category)!=count($cart_categories)) {
					$status = false; 
					$debugging_message[]='Category';
				}
				$applicable_category=$xshippingpro['product_category'];
			}

			if ($xshippingpro['category']==7) {

				if ($resultant_category && count($resultant_category)==count($cart_categories)) {
					$status = false; 
					$debugging_message[]='Category';
				}
				$applicable_category= array_diff($cart_categories, $xshippingpro['product_category']);
			}

			/* End of category */

			/*product checking*/
			$applicable_product=$cart_product_ids;
			if ($xshippingpro['product']==2) {
				if (count($resultant_products)!=count($xshippingpro['product_product'])) {
					$status = false; 
					$debugging_message[]='Product';
				}
				$applicable_product=$xshippingpro['product_product'];
			}
			if ($xshippingpro['product']==3) {
				if (!$resultant_products) {
					$status = false; 
					$debugging_message[]='Product';
				}
				$applicable_product=$xshippingpro['product_product']; 
			}
			if ($xshippingpro['product']==4) {
				if (count($resultant_products)!=count($xshippingpro['product_product']) || count($resultant_products)!=count($cart_product_ids)) {
					$status = false;
					$debugging_message[]='Product'; 
				}
				$applicable_product=$xshippingpro['product_product']; 
			}

			if ($xshippingpro['product']==5) {
				if ($resultant_products) {
					$status = false; 
					$debugging_message[]='Product';
				}
				$applicable_product= array_diff($cart_product_ids, $xshippingpro['product_product']); 
			}

			if ($xshippingpro['product']==6) {
				if (!$resultant_products || count($resultant_products)!=count($cart_product_ids)) {
					$status = false; 
					$debugging_message[]='Product';
				}
				$applicable_product=$xshippingpro['product_product']; 
			}

			if ($xshippingpro['product']==7) {

				if ($resultant_products && count($resultant_products)==count($cart_product_ids)) {
					$status = false; 
					$debugging_message[]='Product';
				}
				$applicable_product= array_diff($cart_product_ids, $xshippingpro['product_product']);
			}

			/* End of product */

			/*product option*/
			$applicable_option=$cart_options;
			if ($xshippingpro['option']==2) {
				if (count($resultant_options)!=count($xshippingpro['product_option'])) {
					$status = false; 
					$debugging_message[]='Option';
				}
				$applicable_option=$xshippingpro['product_option'];
			}
			if ($xshippingpro['option']==3) {
				if (!$resultant_options) {
					$status = false; 
					$debugging_message[]='Option';
				}
				$applicable_option=$xshippingpro['product_option']; 
			}
			if ($xshippingpro['option']==4) {
				if (count($resultant_options)!=count($xshippingpro['product_option']) || count($resultant_options)!=count($cart_options)) {
					$status = false;
					$debugging_message[]='Option'; 
				}
				$applicable_option=$xshippingpro['product_option']; 
			}

			if ($xshippingpro['option']==5) {
				if ($resultant_options) {
					$status = false; 
					$debugging_message[]='Option';
				}
				$applicable_option= array_diff($cart_options, $xshippingpro['product_option']); 
			}

			if ($xshippingpro['option']==6) {
				if (!$resultant_options || count($resultant_options)!=count($cart_options)) {
					$status = false; 
					$debugging_message[]='Option';
				}
				$applicable_option=$xshippingpro['product_option']; 
			}

			if ($xshippingpro['option']==7) {

				if ($resultant_options && count($resultant_options)==count($cart_options)) {
					$status = false; 
					$debugging_message[]='Option';
				}
				$applicable_option= array_diff($cart_options, $xshippingpro['product_option']);
			}

			/* End of product option */

			/*Days of week checking*/
			$day=date('w');
			if (!in_array($day,$xshippingpro['days'])) {
				$status = false; 
				$debugging_message[]='Day Option';
			}
			/* Day checking*/

			/*time checking*/

			$time=date('G'); /* 'G' return 0-23 */
			if ($xshippingpro['time_start'] != "" && $xshippingpro['time_end']) {
				
				$time_start = (int)$xshippingpro['time_start'];
				$time_end = (int)$xshippingpro['time_end'];

				if ($time_start >= 12 && $time_start > $time_end) {
					$time_start -= 12;
					$time_end +=12;
					if ($time >= 12) $time -=12;
				}

				if ($time < $time_start || $time >= $time_end) {
					$status = false; 
					$debugging_message[]='Time Setting H: '.$time;
				}  
			}


			/*Day checking*/

			$cart_dimensional_weight=0;

			/* Calculate dimension weight*/
			if ($xshippingpro['rate_type']=='dimensional' || $xshippingpro['rate_type']=='dimensional_method') {
				foreach($cart_products as $inc=>$product) {

					$product_dimensional_weight=($product['volume']/$xshippingpro['dimensional_factor'])*$product['weight'];  


					if ($xshippingpro['dimensional_overfule'] && $product_dimensional_weight<$product['weight']) {
						$product_dimensional_weight= $product['weight'];
					}

					$cart_products[$inc]['dimensional']=$product_dimensional_weight;
					$cart_dimensional_weight+=$product_dimensional_weight;
				}
			}
			/* End of dimension weight*/

			/* Calculate volumetric weight*/
			$volumetric_weight = 0;
			if ($xshippingpro['rate_type']=='volume' || $xshippingpro['rate_type']=='volume_method') {

				foreach($cart_products as $inc=>$product) {

					$product_volumetric_weight=($product['volume']*$xshippingpro['dimensional_factor']);  


					if ($xshippingpro['dimensional_overfule'] && $product_volumetric_weight<$product['weight']) {
						$product_volumetric_weight= $product['weight'];
					}

					$cart_products[$inc]['volumetric']=$product_volumetric_weight;
					$volumetric_weight+=$product_volumetric_weight;
				}
			}
			/* End of volumetric weight*/



			/* Calculate method wise data if needed*/
			if ($xshippingpro['rate_type']=='total_method' || $xshippingpro['rate_type']=='quantity_method' || $xshippingpro['rate_type']=='sub_method' || $xshippingpro['rate_type']=='weight_method' || $xshippingpro['rate_type']=='volume_method' || $xshippingpro['rate_type']=='dimensional_method') {
				$method_quantity=0;	
				$method_weight=0;
				$method_total=0;
				$method_sub=0;
				$method_volume=0;
				$method_dimensional_weight=0;
				$method_volumetric_weight = 0;

				foreach($cart_products as $product) {

					if (($xshippingpro['manufacturer_rule']==2 || $xshippingpro['manufacturer_rule']==3 || $xshippingpro['manufacturer_rule']==4 || $xshippingpro['manufacturer_rule']==5 || $xshippingpro['manufacturer_rule']==6 || $xshippingpro['manufacturer_rule']==7) && !in_array($product['manufacturer_id'],$applicable_manufacturer)) {   
						continue;
					} 

					if (($xshippingpro['category']==2 || $xshippingpro['category']==3 || $xshippingpro['category']==4 || $xshippingpro['category']==5 || $xshippingpro['category']==6 || $xshippingpro['category']==7) && !array_intersect($product['categories'],$applicable_category)) {   
						continue;
					}

					if (($xshippingpro['product']==2 || $xshippingpro['product']==3 || $xshippingpro['product']==4 || $xshippingpro['product']==5 || $xshippingpro['product']==6 || $xshippingpro['product']==7) && !in_array($product['product_id'],$applicable_product)) {   
						continue;
					}

					if (($xshippingpro['option']==2 || $xshippingpro['option']==3 || $xshippingpro['option']==4 || $xshippingpro['option']==5 || $xshippingpro['option']==6 || $xshippingpro['option']==7) && !array_intersect($product['options'],$applicable_option)) {   
						continue;
					}

					/*Extra check for rule 7 and 5*/
					if ($multi_category && $xshippingpro['multi_category']=='any' 
						&& ($xshippingpro['category']==5 || $xshippingpro['category']==7)
						&& array_intersect($xshippingpro['product_category'],$product['categories'])) {

						continue;
				}

				$method_quantity+= $product['quantity'];

				if ($product['weight_class_id']) {
					$method_weight+= $this->weight->convert($product['weight'], $product['weight_class_id'], $this->config->get('config_weight_class_id'));
				}

				$product['tax_class_id']= isset($product['tax_class_id'])?$product['tax_class_id']:0;
				$method_total+=  $this->tax->calculate($product['price'], $product['tax_class_id'], $this->config->get('config_tax')) * $product['quantity'];

				$method_sub+= $product['total']; 
				$method_volume+= $product['volume'];  
				$method_dimensional_weight+= isset($product['dimensional'])?$product['dimensional']:0;
				$method_volumetric_weight+= isset($product['volumetric'])?$product['volumetric']:0; 
			}

		}


		/*rate calculation*/
		$cost=0;
		$percent_to_be_considered=($xshippingpro['rate_percent']=='sub')?$cart_subtotal:$cart_total;

		if ($xshippingpro['rate_type']=='flat') {
			if (substr(trim($xshippingpro['cost']), -1)=='%') {
				$percent=rtrim(trim($xshippingpro['cost']),'%'); 
				$cost=(float)(($percent*$percent_to_be_considered)/100);
			}else{
				$cost=(float)$xshippingpro['cost'];  
			}  
		} else {

			$target_value=0;
			if ($xshippingpro['rate_type']=='quantity') {
				$target_value=$cart_quantity;

			}

			if ($xshippingpro['rate_type']=='quantity_method') {
				$target_value=$method_quantity;  
			}

			if ($xshippingpro['rate_type']=='weight') {
				$target_value=$cart_weight;  
			}

			if ($xshippingpro['rate_type']=='weight_method') {
				$target_value=$method_weight;  
			}

			if ($xshippingpro['rate_type']=='dimensional') {
				$target_value=$cart_dimensional_weight;
			}
			if ($xshippingpro['rate_type']=='dimensional_method') {
				$target_value=$method_dimensional_weight;
			}

			if ($xshippingpro['rate_type']=='volume') {
				$target_value=$volumetric_weight;  
			}

			if ($xshippingpro['rate_type']=='volume_method') {
				$target_value=$method_volumetric_weight;  
			}

			if ($xshippingpro['rate_type']=='total' || $xshippingpro['rate_type']=='total_method') {

				$target_value=$cart_total;  

				if ($xshippingpro['rate_type']=='total_method') {
					$target_value=$method_total;  
				}
			}

			if ($xshippingpro['rate_type']=='total_coupon') {

				$target_value=$cart_total_without_coupon;  
			}

			if ($xshippingpro['rate_type']=='sub' || $xshippingpro['rate_type']=='sub_method') {

				$target_value=$cart_subtotal;  

				if ($xshippingpro['rate_type']=='sub_method') {
					$target_value=$method_sub;  
				}
			}

			if ($xshippingpro['rate_type']=='grand') {

				$target_value=$grand_total;  
			}	

			if ($xshippingpro['rate_final']=='single') {
				if (!$this->getSinglePrice($xshippingpro['rate_start'],$xshippingpro['rate_end'],$xshippingpro['rate_total'],$xshippingpro['rate_block'],$xshippingpro['rate_partial'],$xshippingpro['additional'],$target_value,$percent_to_be_considered,$cost,$block_found)) {
					if (!$xshippingpro['equation'] && !$xshippingpro['rate_min']) {
						$status = false; 
						$debugging_message[]='Price Single ('.$xshippingpro['rate_type'].'='.$target_value.')';
					}  
				}
			}

			if ($xshippingpro['rate_final']=='cumulative') {

				if (!$this->getCumulativePrice($xshippingpro['rate_start'],$xshippingpro['rate_end'],$xshippingpro['rate_total'],$xshippingpro['rate_block'],$xshippingpro['rate_partial'],$xshippingpro['additional'],$target_value,$percent_to_be_considered,$cost,$block_found)) {
					if (!$xshippingpro['equation'] && !$xshippingpro['rate_min']) {
						$status = false; 
						$debugging_message[]='Price Cumulative ('.$xshippingpro['rate_type'].'='.$target_value.')';
					}  
				}
			}


			$modifier_allowed=true;
			if ($xshippingpro['modifier_ignore'] && !$block_found) $modifier_allowed=false;

			if ($xshippingpro['rate_min'] && $xshippingpro['rate_min']>$cost && $modifier_allowed) {
				$cost=(float)$xshippingpro['rate_min'];
			}

			if ($xshippingpro['rate_max'] && $xshippingpro['rate_max']<$cost && $modifier_allowed) {
				$cost=(float)$xshippingpro['rate_max'];
			}

			$eq_shipping = $cost;
			$eq_modifier = 0;

			/* find modifier*/
			if ($xshippingpro['rate_percent']=='sub') {
				$percent_to_be_considered = $cart_subtotal; 
			}
			if ($xshippingpro['rate_percent']=='total') {
				$percent_to_be_considered = $cart_total;
			}
			if ($xshippingpro['rate_percent']=='shipping') {
				$percent_to_be_considered = $cost;
			}
			if ($xshippingpro['rate_percent']=='sub_shipping') {
				$percent_to_be_considered = $cart_subtotal + $cost;
			}
			if ($xshippingpro['rate_percent']=='total_shipping') {
				$percent_to_be_considered = $cart_total + $cost;
			}

			$modifier = substr(trim($xshippingpro['rate_add']),0,1);
			$modifier = in_array($modifier,$operators)?$modifier:'+';
			$xshippingpro['rate_add']=str_replace($operators,'',$xshippingpro['rate_add']);
			$modification=0;
			if (substr(trim($xshippingpro['rate_add']), -1)=='%') {
				$add_percent=rtrim(trim($xshippingpro['rate_add']),'%'); 
				$modification=(float)(($add_percent*$percent_to_be_considered)/100);	 
			} else {
				$modification=(float)$xshippingpro['rate_add'];	
			}

			if ($modification && $modifier_allowed) {
				if ($modifier=='+') $cost +=$modification; 
				if ($modifier=='-') $cost -=$modification; 
				if ($modifier=='*') $cost *=$modification; 
				if ($modifier=='/') $cost /=$modification; 
				$eq_modifier = $cost;
			}

			/*Equation*/
			if ($xshippingpro['equation']) {

				$equation = $xshippingpro['equation']; 
				$placholder = array('{cartTotal}','{cartQnty}','{cartWeight}', '{shipping}', '{modifier}', '{volume}');

				$eq_total = ($xshippingpro['rate_type']=='total_method') ? $method_total : $cart_total;
				$eq_qnty = ($xshippingpro['rate_type']=='quantity_method') ? $method_quantity : $cart_quantity;
				$eq_weight = ($xshippingpro['rate_type']=='weight_method') ? $method_weight : $cart_weight;
				$eq_volume = ($xshippingpro['rate_type']=='volume_method') ? $method_volume : $cart_volume;

				$replacer = array($eq_total,$eq_qnty,$eq_weight,$eq_shipping,$eq_modifier, $eq_volume);
				$equation = str_replace($placholder, $replacer, $equation);

				$cost =(float)$this->calculate_string($equation);
			}
		}

		/* additional Ranges checking*/
		if ((float)$xshippingpro['order_total_end']>0) {

			if ($cart_total < (float)$xshippingpro['order_total_start'] || $cart_total> (float)$xshippingpro['order_total_end']) {
				$status = false;
				$debugging_message[]='Additional Order Total Ranges';
			} 
		}

		if ((float)$xshippingpro['weight_end']>0) {

			if ($cart_weight < (float)$xshippingpro['weight_start'] || $cart_weight > (float)$xshippingpro['weight_end']) {
				$status = false;
				$debugging_message[]='Additional Weight Ranges';
			}
		}

		if ((int)$xshippingpro['quantity_end']>0) {

			if ($cart_quantity < (int)$xshippingpro['quantity_start'] || $cart_quantity > (int)$xshippingpro['quantity_end']) {
				$status = false;
				$debugging_message[]='Additional Quantity Ranges';
			}
		}

		/* End of ranges of checking*/ 		

		/*Ended rate cal*/
		
		if(!isset($xshippingpro['display'])) $xshippingpro['display'] = '';
		if (!$xshippingpro['display']) {
			$xshippingpro['display'] = isset($xshippingpro['name'][$language_id]) ? isset($xshippingpro['name'][$language_id]) : '';
		}

		if (!isset($xshippingpro['name'][$language_id]) || !$xshippingpro['name'][$language_id]) {
			$status = false;
			$debugging_message[]='Name Missing';
		}


		if (!$status) {
			$debugging[]=array('name'=>$xshippingpro['display'],'filter'=>$debugging_message,'index'=>$no_of_tab);
		}

		if ($xshippingpro['inc_weight']==1 && $cart_weight>0) {
			$xshippingpro['name'][$language_id].=' ('.$this->weight->format($cart_weight, $this->config->get('config_weight_class_id'), $this->language->get('decimal_point'), $this->language->get('thousand_point')).')';
		}

		$method_desc= isset($xshippingpro['desc'][$language_id]) ? '<span style="color: #999999;font-size: 11px;display:block" class="x-shipping-desc">'.$xshippingpro['desc'][$language_id].'</span>' : '';

		if (intval($xshippingpro['group'])) {
			$isSubGroupFound = true;
		}

		if ($status) {

			if(count($xshippingpro['hide']) > 0) {
				$hiddenMethods[$no_of_tab] = array(
					'hide' => $xshippingpro['hide'],
					'display' => $xshippingpro['display']
					);
			}

			$quote_desc = ($is_quote) ? html_entity_decode($method_desc) : '';
			$quote_data['xshippingpro'.$no_of_tab] = array(
				'code'         => 'xshippingpro'.'.xshippingpro'.$no_of_tab,
				'title'        => $xshippingpro['name'][$language_id],
				'desc'         => html_entity_decode($method_desc),
				'description'	=> html_entity_decode($xshippingpro['desc'][$language_id]),
				
				'display' => $xshippingpro['display'],
				'logo'         => $xshippingpro['logo'],
				'image'         => $xshippingpro['logo'], /* for other checkout module*/
				'cost'         => $cost,
				'cost_currency' => $this->currency->format($this->tax->calculate($cost, $xshippingpro['tax_class_id'], $this->config->get('config_tax')),$currency_code),
				'group'         => intval($xshippingpro['group']),
				'sort_order'         => intval($xshippingpro['sort_order']),
				'tax_class_id' => $xshippingpro['tax_class_id'],
				'text'         => ($xshippingpro['mask'])? $xshippingpro['mask'].$quote_desc: $this->currency->format($this->tax->calculate($cost, $xshippingpro['tax_class_id'], $this->config->get('config_tax')),$currency_code).$quote_desc
				);
		 }
	}

	/* Hide methods from hide option*/
	if($hiddenMethods) {
		foreach($hiddenMethods as $hide_by => $hide_single) {
			foreach($hide_single['hide'] as $no_of_tab) {
				if(isset($quote_data['xshippingpro'.$no_of_tab])) {
					$debugging[]=array('name'=>$quote_data['xshippingpro'.$no_of_tab]['display'],'filter'=>array('Hide by '.$hide_single['display']),'index'=>$no_of_tab);
					unset($quote_data['xshippingpro'.$no_of_tab]);
				}
			}  
		}
	}


	/*Finding sub grouping*/
	if ($isSubGroupFound) {	

		$grouping_methods=array();
		foreach($quote_data as $xkey=>$single) {
			$single['xkey']=$xkey;
			$grouping_methods[$single['group']][]=$single;    
		}

		$new_quote_data=array();

		foreach($grouping_methods as $sub_group_id=>$grouping_method) {

			if ($sub_group_id && $xshippingpro_sub_group[$sub_group_id] =='and' && count($grouping_method)!=count($shipping_group_methods[$sub_group_id])) {
				continue;
			}

			if (count($grouping_method)==1 || empty($sub_group_id)) {

				$append_methods = array();
				foreach($grouping_method as $single) {
					$append_methods[$single['xkey']]= $single;  
				}

				$new_quote_data = array_merge($new_quote_data,$append_methods);
				continue;
			}

			$sub_group_type = $xshippingpro_sub_group[$sub_group_id];
			$sub_group_limit = isset($xshippingpro_sub_group_limit[$sub_group_id])?$xshippingpro_sub_group_limit[$sub_group_id]:1;
			$sub_group_name = isset($xshippingpro_sub_group_name[$sub_group_id])?$xshippingpro_sub_group_name[$sub_group_id]:'';

			if (isset($grouping_method)) {
				$new_quote_data = array_merge($new_quote_data,$this->findGroup($grouping_method, $sub_group_type, $sub_group_limit, $sub_group_name));
			}

		}

		$quote_data= $new_quote_data;  

	}

	/* find top grouping*/
	if ($xshippingpro_group!='no_group') {

		$grouping_methods=array();
		foreach($quote_data as $xkey=>$single) {
			$single['xkey']=$xkey;
			$grouping_methods[$single['sort_order']][]=$single;    
		}

		$new_quote_data=array();
		foreach($grouping_methods as $group_id=>$grouping_method) {

			if (count($grouping_method)==1) {

				$append_methods = array();
				foreach($grouping_method as $single) {
					$append_methods[$single['xkey']]= $single;  
				}

				$new_quote_data = array_merge($new_quote_data,$append_methods);
				continue;
			}

			if (isset($grouping_method)) {
				$new_quote_data = array_merge($new_quote_data,$this->findGroup($grouping_method, $xshippingpro_group, $xshippingpro_group_limit));
			}   
		}

		$quote_data= $new_quote_data;   
	}


	/*Sorting final method*/
	$sort_order = array();
	$price_order = array();
	foreach ($quote_data as $key => $value) {
		$sort_order[$key] = $value['sort_order'];
		$price_order[$key] = $value['cost'];
	}

	if ( $xshippingpro_sorting == 2) {
		array_multisort($price_order, SORT_ASC, $quote_data);
	}
	elseif ( $xshippingpro_sorting == 3) {
		array_multisort($price_order, SORT_DESC, $quote_data);
	}
	else {
		array_multisort($sort_order, SORT_ASC, $quote_data);
	}


	$xshippingpro_heading=isset($xshippingpro_heading[$language_id])?$xshippingpro_heading[$language_id]:'';

	$method_data = array(
		'code'       => 'xshippingpro',
		'title'      => ($xshippingpro_heading) ? html_entity_decode($xshippingpro_heading) : $this->language->get('text_title'),
		'quote'      => $quote_data,
		'sort_order' => $this->config->get('shipping_xshippingpro_sort_order'),
		'error'      => false
		);	

	if ($xshippingpro_debug && $debugging  && !$is_admin && !$is_quote) { 
		echo '<div style="border: 1px solid #FF0000; margin: 20px 5px;padding: 10px;">
		<i style="color:red;">This is xhippingpro debug message. Please disable debug mode to hide this messsage</i><br />';
		foreach($debugging as $debug){
			echo '<b>'.$debug['name'].' ('.$debug['index'].')</b> restricted by rules <u>'.implode(',',$debug['filter']).'</u><br />';
		}
		echo '</div>';
	}

	if (!$quote_data) return array();
	return $method_data;
}


private function findGroup($group_method, $group_type, $group_limit, $group_name='') {

	$language_id=$this->config->get('config_language_id');
	$currency_code = isset($this->session->data['currency']) ? $this->session->data['currency'] : $this->config->get('config_currency');
	$return = array();
	$replacer = array();
	$replacer_price = array();
	if ($group_type=='lowest') {

		$lowest=array();
		$lowest_sort=array();

		foreach($group_method as $group_id=>$method) {

			$lowest_sort[$group_id]=$method['cost'];
			$lowest[$group_id]=$method;
			array_push($replacer, $method['title']);
			array_push($replacer_price, $this->currency->format((float)$method['cost'], $currency_code, false, true));
		}

		array_multisort($lowest_sort, SORT_ASC, $lowest);

		for($i=0;$i<$group_limit;$i++) {
			if (isset($lowest[$i]) && is_array($lowest[$i]) && $lowest[$i]) {	
				$return[$lowest[$i]['xkey']]= $lowest[$i]; 
			}
		}

	}


	if ($group_type=='highest') {


		$highest=array();
		$highest_sort=array();

		foreach($group_method as $group_id=>$method) {
			$highest_sort[$group_id]=$method['cost'];
			$highest[$group_id]=$method;
			array_push($replacer, $method['title']);
			array_push($replacer_price, $this->currency->format((float)$method['cost'], $currency_code, false, true));
		}

		array_multisort($highest_sort, SORT_DESC, $highest);

		for($i=0;$i<$group_limit;$i++) {

			if (isset($highest[$i]) && is_array($highest[$i]) && $highest[$i]) {	
				$return[$highest[$i]['xkey']]= $highest[$i]; 
			}
		} 
	} 

	if ($group_type=='average') {

		$sum=0;
		foreach($group_method as $group_id=>$method) {
			$sum+=$method['cost'];
			array_push($replacer, $method['title']);
			array_push($replacer_price, $this->currency->format((float)$method['cost'], $currency_code, false, true));
		}

		if (count($group_method)>1) {
			$group_method[0]['cost']=$sum/count($group_method); 
			$group_method[0]['text']=$this->currency->format($this->tax->calculate($group_method[0]['cost'], $group_method[0]['tax_class_id'], $this->config->get('config_tax')),$currency_code);
		}

		$return[$group_method[0]['xkey']]= $group_method[0]; 		     
	} 


	if ($group_type=='sum') {

		$sum=0;
		foreach($group_method as $group_id=>$method) {
			$sum+=$method['cost'];
			array_push($replacer, $method['title']);
			array_push($replacer_price, $this->currency->format((float)$method['cost'], $currency_code, false, true));
		}
		$group_method[0]['cost']=$sum;
		$group_method[0]['text']=$this->currency->format($this->tax->calculate($group_method[0]['cost'], $group_method[0]['tax_class_id'], $this->config->get('config_tax')),$currency_code);
		$return[$group_method[0]['xkey']]= $group_method[0];  
	} 


	if ($group_type=='and') {

		/* If AND success, show lowest price in case price is not equal*/
		$lowest=99999999;
		$target=0;
		foreach($group_method as $group_id=>$method) {
			if ($method['cost']<$lowest) {
				$target=$group_id; 
				$lowest=$method['cost'];
				array_push($replacer, $method['title']);
				array_push($replacer_price, $this->currency->format((float)$method['cost'], $currency_code, false, true));
			}
		}
		$return[$group_method[$target]['xkey']]= $group_method[$target]; 
	}

	$keywords = array('#1','#2','#3','#4','#5'); 
	$group_name = str_replace($keywords,$replacer, $group_name);

	$keywords = array('@1','@2','@3','@4','@5'); 
	$group_name = str_replace($keywords,$replacer_price, $group_name);

	if (count($return)==1 && $group_name) {
		foreach($return as $key => $method) {
			$return[$key]['title'] = $group_name;
		}
	}

	return $return;
} 

	private function getSinglePrice($start_range,$end_range,$price_range,$block_range,$partial,$additional,$target_value,$percent_to_be_considered,&$cost,&$block_found) {

		$status = false;
		$block = 0;
		$end = 0;
		foreach($start_range as $index=>$start) {
			$start=(float)$start;
			$end=(float)$end_range[$index];

			if (substr(trim($price_range[$index]), -1)=='%') {
				$percent=rtrim(trim($price_range[$index]),'%'); 
				$cost=(float)(($percent*$percent_to_be_considered)/100);
			} else {
				$cost=(float)$price_range[$index];  
			} 

			if ($start <= $target_value && $target_value<= $end) {
				$status = true; 
				$end=$target_value;
			}

			$block=((float)$block_range[$index])?(float)$block_range[$index]:0; 
			$partialAllow= (isset($partial[$index]) && $partial[$index])?(int)$partial[$index]:0;

			if ($block>0)
			{  
				/*round to complete block for iteration purpose*/
				if ($block < 1 && fmod($end,$block) != 0) {
					$end = ($end - fmod($end,$block)) + $block;
				} else if ($block >= 1 && ($end % $block) != 0) {
					$end = ($end - ($end % $block)) + $block; 
				}


				$no_of_blocks =0;

				if ($start == 0) {

					while( $start < $end ) {

						if ($partialAllow) {
							$no_of_blocks =  ($end-$start) >= $block ? ($no_of_blocks+1) : ($no_of_blocks+($end-$start)/$block);
						} else {
							$no_of_blocks++;
						}
						$start += $block;
					}

				} 

				else {

					while( $start <= $end ) {

						if ($partialAllow) {
							$no_of_blocks =  ($end-$start) >= $block ? ($no_of_blocks+1) : ($no_of_blocks+($end-$start)/$block);
						} else {
							$no_of_blocks++;
						}
						$start += $block;
					}
				}   
				
				$cost=($no_of_blocks*$cost);	  

			}

			if ($status) break; 
		}

		   //if not found and additional price was set
		if (substr(trim($additional), -1)=='%') {
			$percent=rtrim(trim($additional),'%'); 
			$additional=(float)(($percent*$percent_to_be_considered)/100);
		} else {
			$additional=(float)$additional;  
		}
		if (!$status && $additional) {

			if (!$block) $block = 1;
			while( $end < $target_value ) {
				$cost += $additional;
				$end += $block;
			}

			$status=true;	  
		}

		return $status;
	}

	private function getCumulativePrice($start_range,$end_range,$price_range,$block_range,$partial,$additional,$target_value,$percent_to_be_considered,&$cost,&$block_found) {
		$status = false;
		$block = 0;
		$end = 0;

		foreach($start_range as $index=>$start) {
			$start=(float)$start;
			$end=(float)$end_range[$index];

			if (substr(trim($price_range[$index]), -1)=='%') {
				$percent=rtrim(trim($price_range[$index]),'%'); 
				$block_price=(float)(($percent*$percent_to_be_considered)/100);	 
			} else {
				$block_price=(float)$price_range[$index];  
			}

			$block=((float)$block_range[$index])?(float)$block_range[$index]:0;
			$partialAllow= (isset($partial[$index]) && $partial[$index])?(int)$partial[$index]:0;

			if ($start <= $target_value && $target_value<= $end) {
				$status = true;
				$end=$target_value; 
			}

			if ($block==0) {
				$cost+= (float)$block_price;
			} else {
				/*round to complete block for iteration purpose*/
				if ($block < 1 && fmod($end,$block) != 0) {
					$end = ($end - fmod($end,$block)) + $block;
				} else if ($block >= 1 && ($end % $block) != 0) {
					$end = ($end - ($end % $block)) + $block; 
				}

				$no_of_blocks =0;

				if ($start == 0) {
					while( $start < $end ) {

						if ($partialAllow) {
							$no_of_blocks =  ($end-$start) >= $block ? ($no_of_blocks+1) : ($no_of_blocks+($end-$start)/$block);
						} else {
							$no_of_blocks++;
						}
						$start += $block;
					}
				}
				
				else {

					while( $start <= $end ) {
						if ($partialAllow) {
							$no_of_blocks =  ($end-$start) >= $block ? ($no_of_blocks+1) : ($no_of_blocks+($end-$start)/$block);
						} else {
							$no_of_blocks++;
						}
						$start += $block;
					}
				}

				$cost+=($no_of_blocks*(float)$block_price);
				$block_found=true;
			}

			if ($status) break;
		}

			 //if not found and additional price was set
		if (substr(trim($additional), -1)=='%') {
			$percent=rtrim(trim($additional),'%'); 
			$additional=(float)(($percent*$percent_to_be_considered)/100);
		} else {
			$additional=(float)$additional;  
		}

		if (!$status && $additional) {

			if (!$block) $block = 1;
			while( $end < $target_value ) {
				$cost += $additional;
				$end += $block;
			}

			$status=true;	 
		}
		return $status;
	}

	private function calculate_string( $mathString ) {
	    $mathString = trim($mathString);     // trim white spaces
	    $mathString = preg_replace ('[^0-9\+-\*\/\(\) ]', '', $mathString);    // remove any non-numbers chars; exception for math operators
	    $compute = create_function("", "return (" . html_entity_decode($mathString) . ");" );
	    return 0 + $compute();
	}

}