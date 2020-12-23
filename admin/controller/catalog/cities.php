<?php
class ControllerCatalogCities extends Controller { 
	private $error = array();

	public function index() {
		$this->load->language('catalog/city');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('catalog/city');

		$this->getList();
	}

	public function add() {
		$this->load->language('catalog/city');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('catalog/city');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_catalog_city->addCity($this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}

			$this->response->redirect($this->url->link('catalog/cities', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->getForm();
	}

	public function add_office() {
		$this->load->language('catalog/office');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('catalog/city');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateOfficeForm()) {
			$this->model_catalog_city->addOffice($this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}

			$this->response->redirect($this->url->link('catalog/cities', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->getOfficeForm();
	}

	public function edit() {
		$this->load->language('catalog/cities');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('catalog/city');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {

			$this->model_catalog_city->editCity($this->request->get['city_id'], $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}

			$this->response->redirect($this->url->link('catalog/cities', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->getForm();
	}

	public function edit_office() {
		$this->load->language('catalog/office');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('catalog/city');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateOfficeForm()) {

			$this->model_catalog_city->editOffice($this->request->get['office_id'], $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}

			$this->response->redirect($this->url->link('catalog/cities', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->getOfficeForm();
	}
	
	public function delete() {
		$this->load->language('catalog/cities');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('catalog/information');

		if (isset($this->request->post['selected']) && $this->validateDelete()) {
			foreach ($this->request->post['selected'] as $city_id) {
				$this->model_catalog_city->deleteFaq($city_id);
			}

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}

			$this->response->redirect($this->url->link('catalog/cities', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->getList();
	}

	protected function getList() {
		if (isset($this->request->get['sort'])) {
			$sort = $this->request->get['sort'];
		} else {
			$sort = 'gbdate';
		}

		if (isset($this->request->get['order'])) {
			$order = $this->request->get['order'];
		} else {
			$order = 'DESC';
		}

		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}

		$url = '';

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('catalog/cities', 'user_token=' . $this->session->data['user_token'] . $url, true)
		);

		$data['add'] = $this->url->link('catalog/cities/add', 'user_token=' . $this->session->data['user_token'] . $url, true);
		$data['delete'] = $this->url->link('catalog/cities/delete', 'user_token=' . $this->session->data['user_token'] . $url, true);
		$data['enabled'] = $this->url->link('catalog/cities/enable', 'user_token=' . $this->session->data['user_token'] . $url, true);
		$data['disabled'] = $this->url->link('catalog/cities/disable', 'user_token=' . $this->session->data['user_token'] . $url, true);
		$data['ajax_pub'] = $this->url->link('catalog/cities/set_published', 'user_token=' . $this->session->data['user_token'] . $url, true);

		$data['informations'] = array();

		$filter_data = array(
			'sort'  => $sort,
			'order' => $order,
			'start' => ($page - 1) * $this->config->get('config_limit_admin'),
			'limit' => $this->config->get('config_limit_admin')
		);

		$information_total = $this->model_catalog_city->getTotalCities();

		$results = $this->model_catalog_city->getCities($filter_data);

		foreach ($results as $result) {
			$offices =  $this->model_catalog_city->getOffices(['city_id' => $result['city_id']]);
			foreach ($offices as &$office){
				$office['edit'] = $this->url->link('catalog/cities/edit_office', 'user_token=' . $this->session->data['user_token'] . '&city_id=' . $office['city_id']. '&office_id=' . $office['office_id'], true);
			}

			$data['informations'][] = array(
				'city_id' => $result['city_id'],				
				'name'          => $result['name'],
				'coordinates'     => $result['coordinates'],
				'status'  	  	 => $result['status'],
				'sort_order'  	  	 => $result['sort_order'],
				'date_added'  	  	 => $result['date_added'],
				'offices' => $offices,
				'add_office'   => $this->url->link('catalog/cities/edit_office', 'user_token=' . $this->session->data['user_token'] . '&city_id=' . $result['city_id'] . $url, true),
				'edit'           => $this->url->link('catalog/cities/edit', 'user_token=' . $this->session->data['user_token'] . '&city_id=' . $result['city_id'] . $url, true),
			);
		}

		//print_r($data['informations']);


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

		if (isset($this->request->post['selected'])) {
			$data['selected'] = (array)$this->request->post['selected'];
		} else {
			$data['selected'] = array();
		}

		$url = '';

		if ($order == 'ASC') {
			$url .= '&order=DESC';
		} else {
			$url .= '&order=ASC';
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$data['sort_title'] = $this->url->link('catalog/cities', 'user_token=' . $this->session->data['user_token'] . '&sort=gbdate' . $url, true);
		$data['sort_sort_order'] = $this->url->link('catalog/cities', 'user_token=' . $this->session->data['user_token'] . '&sort=i.sort_order' . $url, true);
		$data['sort_noindex'] = $this->url->link('catalog/cities', 'user_token=' . $this->session->data['user_token'] . '&sort=i.noindex' . $url, true);
		
		$url = '';

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		$pagination = new Pagination();
		$pagination->total = $information_total;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_limit_admin');
		$pagination->url = $this->url->link('catalog/cities', 'user_token=' . $this->session->data['user_token'] . $url . '&page={page}', true);

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($information_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($information_total - $this->config->get('config_limit_admin'))) ? $information_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $information_total, ceil($information_total / $this->config->get('config_limit_admin')));

		$data['sort'] = $sort;
		$data['order'] = $order;

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('catalog/cities_list', $data));
	}

	protected function getForm() {
		$data['text_form'] = !isset($this->request->get['city_id']) ? $this->language->get('text_add') : $this->language->get('text_edit');

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->error['name'])) {
			$data['error_name'] = $this->error['name'];
		} else {
			$data['error_name'] = array();
		}

		if (isset($this->error['coordinates'])) {
			$data['error_coordinates'] = $this->error['gbname'];
		} else {
			$data['error_coordinates'] = array();
		}


		$url = '';

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('catalog/cities', 'user_token=' . $this->session->data['user_token'] . $url, true)
		);

		if (!isset($this->request->get['city_id'])) {
			$data['action'] = $this->url->link('catalog/cities/add', 'user_token=' . $this->session->data['user_token'] . $url, true);
		} else {
			$data['action'] = $this->url->link('catalog/cities/edit', 'user_token=' . $this->session->data['user_token'] . '&city_id=' . $this->request->get['city_id'] . $url, true);
		}

		$data['cancel'] = $this->url->link('catalog/cities', 'user_token=' . $this->session->data['user_token'] . $url, true);

		if (isset($this->request->get['city_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
			$city_info = $this->model_catalog_city->getCity($this->request->get['city_id']);
		}
		$data['user_token'] = $this->session->data['user_token'];

		$this->load->model('localisation/language');

		$data['languages'] = $this->model_localisation_language->getLanguages();

		if (isset($this->request->post['information_description'])) {
			$data['information_description'] = $this->request->post['information_description'];
		} elseif (isset($this->request->get['city_id'])) {
			//$data['information_description'] = $this->model_catalog_city->getInformationDescriptions($this->request->get['city_id']);
			$data['information_description'][1] = $city_info;
		} else {
			$data['information_description'] = array();
		}
		


		$language_id = $this->config->get('config_language_id');
		if (isset($data['information_description'][$language_id]['title'])) {
			$data['heading_title'] = $data['information_description'][$language_id]['title'];
		}

		$this->load->model('setting/store');

		$data['stores'] = array();
		
		$data['stores'][] = array(
			'store_id' => 0,
			'name'     => $this->language->get('text_default')
		);
		
		$stores = $this->model_setting_store->getStores();

		foreach ($stores as $store) {
			$data['stores'][] = array(
				'store_id' => $store['store_id'],
				'name'     => $store['name']
			);
		}



		if (isset($this->request->post['bottom'])) {
			$data['bottom'] = $this->request->post['bottom'];
		} elseif (!empty($information_info)) {
			$data['bottom'] = $information_info['bottom'];
		} else {
			$data['bottom'] = 0;
		}

		if (isset($this->request->post['status'])) {
			$data['status'] = $this->request->post['status'];
		} elseif (!empty($information_info)) {
			$data['status'] = $information_info['status'];
		} else {
			$data['status'] = true;
		}
		
		if (isset($this->request->post['noindex'])) {
			$data['noindex'] = $this->request->post['noindex'];
		} elseif (!empty($information_info)) {
			$data['noindex'] = $information_info['noindex'];
		} else {
			$data['noindex'] = 1;
		}

		if (isset($this->request->post['sort_order'])) {
			$data['sort_order'] = $this->request->post['sort_order'];
		} elseif (!empty($information_info)) {
			$data['sort_order'] = $information_info['sort_order'];
		} else {
			$data['sort_order'] = '';
		}
		


		$this->load->model('design/layout');

		$data['layouts'] = $this->model_design_layout->getLayouts();

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('catalog/city_form', $data));
	}

	protected function getOfficeForm() {
		
		$data['text_form'] = !isset($this->request->get['office_id']) ? $this->language->get('text_add') : $this->language->get('text_edit');

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->error['name'])) {
			$data['error_name'] = $this->error['name'];
		} else {
			$data['error_name'] = array();
		}

		if (isset($this->error['coordinates'])) {
			$data['error_coordinates'] = $this->error['gbname'];
		} else {
			$data['error_coordinates'] = array();
		}


		$url = '';

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('catalog/cities', 'user_token=' . $this->session->data['user_token'] . $url, true)
		);



		if (!isset($this->request->get['office_id'])) {
			$data['action'] = $this->url->link('catalog/cities/add_office', 'user_token=' . $this->session->data['user_token'] . $url, true);
		} else {
			$data['action'] = $this->url->link('catalog/cities/edit_office', 'user_token=' . $this->session->data['user_token'] . '&office_id=' . $this->request->get['office_id'] . $url, true);
		}
		
		$data['cancel'] = $this->url->link('catalog/cities', 'user_token=' . $this->session->data['user_token'] . $url, true);

		if (isset($this->request->get['office_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
			$office_info = $this->model_catalog_city->getOffice($this->request->get['office_id']);
		}

	/*	if (!isset($this->request->get['city_id'])) {
			die('Ошибка. Нет города');

		} else*/
		 {
			$city_info = $this->model_catalog_city->getCity($this->request->get['city_id']);
			$data['city_id'] = $this->request->get['city_id'];
			$data['city_info'] = $city_info;
		}

		$data['user_token'] = $this->session->data['user_token'];

		$this->load->model('localisation/language');

		$data['languages'] = $this->model_localisation_language->getLanguages();

		if (isset($this->request->post['information_description'])) {
			$data['information_description'] = $this->request->post['information_description'];
		} elseif (isset($this->request->get['office_id'])) {
			//$data['information_description'] = $this->model_catalog_city->getInformationDescriptions($this->request->get['city_id']);
			$data['information_description'][1] = $office_info;
		} else {
			$data['information_description'] = array();
		}


		$language_id = $this->config->get('config_language_id');
		if (isset($data['information_description'][$language_id]['title'])) {
			$data['heading_title'] = $data['information_description'][$language_id]['title'];
		}

		$this->load->model('setting/store');

		$data['stores'] = array();
		
		$data['stores'][] = array(
			'store_id' => 0,
			'name'     => $this->language->get('text_default')
		);
		
		$stores = $this->model_setting_store->getStores();

		foreach ($stores as $store) {
			$data['stores'][] = array(
				'store_id' => $store['store_id'],
				'name'     => $store['name']
			);
		}



		if (isset($this->request->post['bottom'])) {
			$data['bottom'] = $this->request->post['bottom'];
		} elseif (!empty($information_info)) {
			$data['bottom'] = $information_info['bottom'];
		} else {
			$data['bottom'] = 0;
		}

		if (isset($this->request->post['status'])) {
			$data['status'] = $this->request->post['status'];
		} elseif (!empty($information_info)) {
			$data['status'] = $information_info['status'];
		} else {
			$data['status'] = true;
		}
		
		if (isset($this->request->post['noindex'])) {
			$data['noindex'] = $this->request->post['noindex'];
		} elseif (!empty($information_info)) {
			$data['noindex'] = $information_info['noindex'];
		} else {
			$data['noindex'] = 1;
		}

		if (isset($this->request->post['sort_order'])) {
			$data['sort_order'] = $this->request->post['sort_order'];
		} elseif (!empty($information_info)) {
			$data['sort_order'] = $information_info['sort_order'];
		} else {
			$data['sort_order'] = '';
		}
		


		$this->load->model('design/layout');

		$data['layouts'] = $this->model_design_layout->getLayouts();

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('catalog/office_form', $data));
	}
	
	protected function validateForm() {
		if (!$this->user->hasPermission('modify', 'catalog/cities')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		foreach ($this->request->post['information_description'] as $language_id => $value) {
			if ((utf8_strlen($value['name']) < 1) || (utf8_strlen($value['name']) > 64)) {
				$this->error['name'][$language_id] = $this->language->get('error_name');
			}

			if (utf8_strlen($value['coordinates']) < 3) {
				$this->error['coordinates'][$language_id] = 'Введите координаты города';
			}

		
		}

		if ($this->error && !isset($this->error['warning'])) {
			$this->error['warning'] = $this->language->get('error_warning');
		}

		return !$this->error;
	}
	
	protected function validateOfficeForm() {
		if (!$this->user->hasPermission('modify', 'catalog/cities')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}
		foreach ($this->request->post['information_description'] as $language_id => $value) {
			if ((utf8_strlen($value['address']) < 1) || (utf8_strlen($value['address']) > 264)) {
				$this->error['address'][$language_id] = $this->language->get('error_address');
			}

			if (utf8_strlen($value['coordinates']) < 3) {
				$this->error['coordinates'][$language_id] = 'Введите координаты города';
			}

		
		}

		if ($this->error && !isset($this->error['warning'])) {
			$this->error['warning'] = $this->language->get('error_warning');
		}

		return !$this->error;
	}	

	public function enable() {
        $this->load->language('catalog/cities');
        $this->document->setTitle($this->language->get('heading_title'));
        $this->load->model('catalog/information');
        if (isset($this->request->post['selected']) && $this->validateEnable()) {
            foreach ($this->request->post['selected'] as $city_id) {
                $this->model_catalog_city->editInformationStatus($city_id, 1);
            }
            $this->session->data['success'] = $this->language->get('text_success');
            $url = '';
            if (isset($this->request->get['page'])) {
                $url .= '&page=' . $this->request->get['page'];
            }
            if (isset($this->request->get['sort'])) {
                $url .= '&sort=' . $this->request->get['sort'];
            }
            if (isset($this->request->get['order'])) {
                $url .= '&order=' . $this->request->get['order'];
            }
            $this->response->redirect($this->url->link('catalog/cities', 'user_token=' . $this->session->data['user_token'] . $url, true));
        }
        $this->getList();
    }
	
    public function disable() {
        $this->load->language('catalog/cities');
        $this->document->setTitle($this->language->get('heading_title'));
        $this->load->model('catalog/information');
        if (isset($this->request->post['selected']) && $this->validateDisable()) {
            foreach ($this->request->post['selected'] as $city_id) {
                $this->model_catalog_city->editInformationStatus($city_id, 0);
            }
            $this->session->data['success'] = $this->language->get('text_success');
            $url = '';
            if (isset($this->request->get['page'])) {
                $url .= '&page=' . $this->request->get['page'];
            }
            if (isset($this->request->get['sort'])) {
                $url .= '&sort=' . $this->request->get['sort'];
            }
            if (isset($this->request->get['order'])) {
                $url .= '&order=' . $this->request->get['order'];
            }
            $this->response->redirect($this->url->link('catalog/cities', 'user_token=' . $this->session->data['user_token'] . $url, true));
        }
        $this->getList();
    }
	
	protected function validateEnable() {
		if (!$this->user->hasPermission('modify', 'catalog/cities')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}
	
	protected function validateDisable() {
		if (!$this->user->hasPermission('modify', 'catalog/cities')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}

	protected function validateDelete() {
		if (!$this->user->hasPermission('modify', 'catalog/cities')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		$this->load->model('setting/store');

		foreach ($this->request->post['selected'] as $city_id) {
			if ($this->config->get('config_account_id') == $city_id) {
				$this->error['warning'] = $this->language->get('error_account');
			}

			if ($this->config->get('config_checkout_id') == $city_id) {
				$this->error['warning'] = $this->language->get('error_checkout');
			}

			if ($this->config->get('config_affiliate_id') == $city_id) {
				$this->error['warning'] = $this->language->get('error_affiliate');
			}

			if ($this->config->get('config_return_id') == $city_id) {
				$this->error['warning'] = $this->language->get('error_return');
			}

			$store_total = $this->model_setting_store->getTotalStoresByInformationId($city_id);

			if ($store_total) {
				$this->error['warning'] = sprintf($this->language->get('error_store'), $store_total);
			}
		}

		return !$this->error;
	}
	protected function preview_text($value, $limit = 300)
	{
		$value = stripslashes($value);		
		$value = htmlspecialchars_decode($value, ENT_QUOTES);
		$value = str_ireplace(array('<br>', '<br />', '<br/>'), ' ', $value);
		$value = strip_tags($value);
		$value = trim($value);
	
		if (mb_strlen($value) < $limit) {
			return $value;
		} else {
			$value   = mb_substr($value, 0, $limit);
			$length  = mb_strripos($value, ' ');
			$end     = mb_substr($value, $length - 1, 1);
	
			if (empty($length)) {
				return $value;
			} elseif (in_array($end, array('.', '!', '?'))) {
				return mb_substr($value, 0, $length);
			} elseif (in_array($end, array(',', ':', ';', '«', '»', '…', '(', ')', '—', '–', '-'))) {
				return trim(mb_substr($value, 0, $length - 1)) . '...';
			} else {
				return trim(mb_substr($value, 0, $length)) . '...';
			}
			
			return trim();
		}
	}

	public function set_published() {

		$json = [];
		if(empty($this->request->post['city_id'])){ return false; }

		if(empty($json['error'])){

			$this->load->model('catalog/information');
			$this->model_catalog_city->set_published($this->request->post);

			$json['success'] = 'сохранено';
		}else{
			$json['error'] = implode(' ', $json['error']);
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
}