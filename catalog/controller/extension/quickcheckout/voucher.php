<?php 
class ControllerExtensionQuickCheckoutVoucher extends Controller {
	public function index() {
		$data = $this->load->language('checkout/checkout');
		$data = array_merge($data, $this->load->language('extension/quickcheckout/checkout'));
		
		$points_total = 0;
		
		foreach ($this->cart->getProducts() as $product) {
			if ($product['points']) {
				$points_total += $product['points'];
			}
		}
		
		$data['entry_reward'] = sprintf($this->language->get('entry_reward'), $points_total, $this->customer->getRewardPoints());
		
		if ($points_total && $this->customer->isLogged()) {
			$data['reward'] = true;
		} else {
			$data['reward'] = false;
		}
		
		// All variables
		$data['voucher_module'] = $this->config->get('quickcheckout_voucher');
		$data['coupon_module'] = $this->config->get('quickcheckout_coupon');
		$data['reward_module'] = $this->config->get('quickcheckout_reward');
	
		return $this->load->view('extension/quickcheckout/voucher', $data);
	}
	
	public function validateCoupon() {
		$this->load->language('checkout/checkout');
		$this->load->language('extension/quickcheckout/checkout');

		$json = array();
		
		if (!isset($this->request->post['coupon']) || empty($this->request->post['coupon'])) {
			$this->request->post['coupon'] = '';
			$this->session->data['coupon'] = '';
		}
		
		$this->load->model('extension/total/coupon');
		
		if ($this->request->post['coupon'] == '') {
			unset($this->session->data['coupon']);
			
			$json['success'] = $this->language->get('text_coupon_removed');
		} else {
			$coupon_info = $this->model_extension_total_coupon->getCoupon($this->request->post['coupon']);
			
			if (!$coupon_info) {			
				$json['error']['warning'] = $this->language->get('error_coupon');
			}
			
			if (!$json) {
				$this->session->data['coupon'] = $this->request->post['coupon'];

				$json['success'] = $this->language->get('text_coupon');
			}
		}
		
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));		
	}
	
	public function validateVoucher() {
		$this->load->language('checkout/checkout');
		$this->load->language('extension/quickcheckout/checkout');
		
		$json = array();
		
		if (!isset($this->request->post['voucher']) || empty($this->request->post['voucher'])) {
			$this->request->post['voucher'] = '';
			$this->session->data['voucher'] = '';
		}
		
		$this->load->model('extension/total/voucher');
		
		if ($this->request->post['voucher'] == '') {
			unset($this->session->data['voucher']);
			
			$json['success'] = $this->language->get('text_voucher_removed');
		} else {
			$voucher_info = $this->model_extension_total_voucher->getVoucher($this->request->post['voucher']);
			
			if (!$voucher_info) {
				$json['error']['warning'] = $this->language->get('error_voucher');
			}
			
			if (!$json) {
				$this->session->data['voucher'] = $this->request->post['voucher'];

				$json['success'] = $this->language->get('text_coupon');
			}
		}
		
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
	
	public function validateReward() {
		$this->load->language('checkout/checkout');
		$this->load->language('extension/quickcheckout/checkout');
		
		$points = $this->customer->getRewardPoints();
		
		$points_total = 0;
		
		foreach ($this->cart->getProducts() as $product) {
			if ($product['points']) {
				$points_total += $product['points'];
			}
		}	
		
		$json = array();
		
		if ($this->request->post['reward'] == '') {
			unset($this->session->data['reward']);
			
			$json['success'] = $this->language->get('text_reward_removed');
		} else {
			if (empty($this->request->post['reward'])) {
				$json['error']['warning'] = $this->language->get('error_reward');
			}
		
			if ($this->request->post['reward'] > $points) {
				$json['error']['warning'] = sprintf($this->language->get('error_points'), $this->request->post['reward']);
			}
			
			if ($this->request->post['reward'] > $points_total) {
				$json['error']['warning'] = sprintf($this->language->get('error_maximum'), $points_total);
			}
			
			if (!$json) {
				$this->session->data['reward'] = abs($this->request->post['reward']);
				
				$json['success'] = $this->language->get('text_reward');
			}
		}
		
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));	
	}
}