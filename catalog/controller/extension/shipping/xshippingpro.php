<?php
class ControllerExtensionShippingXshippingpro extends Controller {
	
	public function onShippingMethod($route, &$data) {

		$image = true;

	    if (strpos($route, 'quickcheckout/shipping') !== false) {
	    	$image = false;
	    }

	    /* not sure about, will decide later */
	    /* 
	    if (strpos($route, 'onepagecheckout/shipping') !== false) {
	    	$image = false;
	    }

	    if (strpos($route, 'journal2/checkout') !== false) {

	    }

	    if (strpos($route, 'd_quickcheckout/shipping') !== false) {

	    } */

	    $this->_append($data, $image);		
	}	

	public function onOrderEmail($route, &$data) {

		$shipping_xshippingpro_desc_mail=$this->config->get('shipping_xshippingpro_desc_mail');

		if( $shipping_xshippingpro_desc_mail ) {

				$order_info = $this->model_checkout_order->getOrder($data['order_id']);
				$language_id = $order_info['language_id'];

				if (strpos($order_info['shipping_code'], 'xshippingpro') !== false) {
					$tab_id = str_replace('xshippingpro.xshippingpro', '', $order_info['shipping_code']);
					$method = $this->db->query("SELECT * FROM `" . DB_PREFIX . "xshippingpro` WHERE tab_id='".(int)$tab_id."'")->row;

					$xshippingpro = $method['method_data'];
					$xshippingpro = @unserialize(@base64_decode($xshippingpro));
					if (!is_array($xshippingpro)) $xshippingpro = array();
					if (!isset($xshippingpro['desc'])) $xshippingpro['desc']=array();

					
				}
		}
	}

	private function _append(&$data, $image) {
//print_r($data['shipping_methods']);
		foreach ($data['shipping_methods'] as $code => $methods) {
			if ($code === 'xshippingpro') {
				foreach ($methods['quote'] as $key => $value) {
					
					if (isset($value['desc']) && $value['desc']) {
						$data['shipping_methods'][$code]['quote'][$key]['text'] .= $value['desc']; 	
					}

				   if ($image && isset($value['image']) && $value['image']) {
						$data['shipping_methods'][$code]['quote'][$key]['image'] = '<img class="xshipping-logo" style="margin-right:3px; vertical-align:middle" src="'.$value['image'].'"  alt="'.$value['title'].'" />'; 	
						// $data['shipping_methods'][$code]['quote'][$key]['title']
					}
					
					
				}
			}
		}
	}
}
