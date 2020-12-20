<?php
class ControllerExtensionPaymentCod2 extends Controller {
	public function index() {
		//return $this->load->view('extension/payment/cod2');


		//Костылёк т.к. нет вьюхи с javascript переходом на cod/confirm
		if ($this->session->data['payment_method']['code'] == 'cod2') {
			$this->load->model('checkout/order');

			$this->model_checkout_order->addOrderHistory($this->session->data['order_id'], $this->config->get('payment_cod2_order_status_id'));
		
			//$json['redirect'] = $this->url->link('checkout/success');
		}		
	}

	public function confirm() {
		$json = array();
		
		if ($this->session->data['payment_method']['code'] == 'cod2') {
			$this->load->model('checkout/order');

			$this->model_checkout_order->addOrderHistory($this->session->data['order_id'], $this->config->get('payment_cod2_order_status_id'));
		
			$json['redirect'] = $this->url->link('checkout/success');
		}
		
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));		
	}
}
