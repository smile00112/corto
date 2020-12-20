<?php
class ControllerExtensionPaymentCod3 extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('extension/payment/cod3');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('setting/setting');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('payment_cod3', $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$this->response->redirect($this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=payment', true));
		}

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}
		
		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_extension'),
			'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=payment', true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('extension/payment/cod3', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['action'] = $this->url->link('extension/payment/cod3', 'user_token=' . $this->session->data['user_token'], true);

		$data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=payment', true);

		if (isset($this->request->post['payment_cod3_total'])) {
			$data['payment_cod3_total'] = $this->request->post['payment_cod3_total'];
		} else {
			$data['payment_cod3_total'] = $this->config->get('payment_cod3_total');
		}

		if (isset($this->request->post['payment_cod3_image'])) {
			$data['payment_cod3_image'] = $this->request->post['payment_cod3_image'];
		} else {
			$data['payment_cod3_image'] = $this->config->get('payment_cod3_image');
		}

		if (isset($this->request->post['payment_cod3_order_status_id'])) {
			$data['payment_cod3_order_status_id'] = $this->request->post['payment_cod3_order_status_id'];
		} else {
			$data['payment_cod3_order_status_id'] = $this->config->get('payment_cod3_order_status_id');
		}

		$this->load->model('localisation/order_status');

		$data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();

		if (isset($this->request->post['payment_cod3_geo_zone_id'])) {
			$data['payment_cod3_geo_zone_id'] = $this->request->post['payment_cod3_geo_zone_id'];
		} else {
			$data['payment_cod3_geo_zone_id'] = $this->config->get('payment_cod3_geo_zone_id');
		}

		$this->load->model('localisation/geo_zone');

		$data['geo_zones'] = $this->model_localisation_geo_zone->getGeoZones();

		if (isset($this->request->post['payment_cod3_status'])) {
			$data['payment_cod3_status'] = $this->request->post['payment_cod3_status'];
		} else {
			$data['payment_cod3_status'] = $this->config->get('payment_cod3_status');
		}

		if (isset($this->request->post['payment_cod3_sort_order'])) {
			$data['payment_cod3_sort_order'] = $this->request->post['payment_cod3_sort_order'];
		} else {
			$data['payment_cod3_sort_order'] = $this->config->get('payment_cod3_sort_order');
		}

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/payment/cod3', $data));
	}

	protected function validate() {
		if (!$this->user->hasPermission('modify', 'extension/payment/cod3')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}
}