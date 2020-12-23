<?php
// *	@source		See SOURCE.txt for source and other copyright.
// *	@license	GNU General Public License version 3; see LICENSE.txt

class ControllerInformationInformation extends Controller {
	public function index() {
		$this->load->language('information/information');

		$this->load->model('catalog/information');

		$data['breadcrumbs'] = array();

		// $data['breadcrumbs'][] = array(
		// 	'text' => $this->language->get('text_home'),
		// 	'href' => $this->url->link('common/home')
		// );

		if (isset($this->request->get['information_id'])) {
			$information_id = (int)$this->request->get['information_id'];
		} else {
			$information_id = 0;
		}

		$information_info = $this->model_catalog_information->getInformation($information_id);

		if ($information_info) {
			
			if ($information_info['meta_title']) {
				$this->document->setTitle($information_info['meta_title']);
			} else {
				$this->document->setTitle($information_info['title']);
			}
			
			if ($information_info['noindex'] <= 0) {
				$this->document->setRobots('noindex,follow');
			}
			
			if ($information_info['meta_h1']) {
				$data['heading_title'] = $information_info['meta_h1'];
			} else {
				$data['heading_title'] = $information_info['title'];
			}
			
			$this->document->setDescription($information_info['meta_description']);
			$this->document->setKeywords($information_info['meta_keyword']);

			$data['breadcrumbs'][] = array(
				'text' => $information_info['title'],
				'href' => $this->url->link('information/information', 'information_id=' .  $information_id)
			);

			$data['description'] = html_entity_decode($information_info['description'], ENT_QUOTES, 'UTF-8');

			$data['continue'] = $this->url->link('common/home');

			$data['column_left'] = $this->load->controller('common/column_left');
			$data['column_right'] = $this->load->controller('common/column_right');
			$data['content_top'] = $this->load->controller('common/content_top');
			$data['content_bottom'] = $this->load->controller('common/content_bottom');


			if($information_id == 8 && !empty($_GET['test'])){
				$this->load->model('catalog/cities');

				$cities_info = $this->model_catalog_cities->getCities();

				if ($cities_info) {
					$this->document->addScript('https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.bundle.min.js','footer');
					$this->document->addScript('catalog/view/javascript/bootstrap/js/bootstrap-select.min.js','footer');
					$this->document->addStyle('https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css');					
					$this->document->addStyle('catalog/view/javascript/bootstrap/css/bootstrap-select.min.css');
					foreach ($cities_info as $result) {
						$offices =  $this->model_catalog_cities->getOffices(['city_id' => $result['city_id']]);
						// foreach ($offices as &$office){
						// 	$office['edit'] = $this->url->link('catalog/cities/edit_office', 'user_token=' . $this->session->data['user_token'] . '&city_id=' . $office['city_id']. '&office_id=' . $office['office_id'], true);
						// }
			
						$data['cities'][] = array(
							'city_id' => $result['city_id'],				
							'name'          => $result['name'],
							'coordinates'     => $result['coordinates'],
							'status'  	  	 => $result['status'],
							'sort_order'  	  	 => $result['sort_order'],
							'date_added'  	  	 => $result['date_added'],
							'offices' => $offices,
						);
					}

					$data['cities_json'] = json_encode( $data['cities'] );
				}
				$data['footer'] = $this->load->controller('common/footer');
				$data['header'] = $this->load->controller('common/header');
				//$data['cities'] = $this->load->controller('imformation/cities');
				// print_r($data['cities']);
				// exit;
				$this->response->setOutput($this->load->view('information/cities', $data));

			}else{
				$data['footer'] = $this->load->controller('common/footer');
				$data['header'] = $this->load->controller('common/header');
				$this->response->setOutput($this->load->view('information/information', $data));
			}	
		} else {
			$data['breadcrumbs'][] = array(
				'text' => $this->language->get('text_error'),
				'href' => $this->url->link('information/information', 'information_id=' . $information_id)
			);

			$this->document->setTitle($this->language->get('text_error'));

			$data['heading_title'] = $this->language->get('text_error');

			$data['text_error'] = $this->language->get('text_error');

			$data['continue'] = $this->url->link('common/home');

			$this->response->addHeader($this->request->server['SERVER_PROTOCOL'] . ' 404 Not Found');

			$data['column_left'] = $this->load->controller('common/column_left');
			$data['column_right'] = $this->load->controller('common/column_right');
			$data['content_top'] = $this->load->controller('common/content_top');
			$data['content_bottom'] = $this->load->controller('common/content_bottom');
			$data['footer'] = $this->load->controller('common/footer');
			$data['header'] = $this->load->controller('common/header');

			$this->response->setOutput($this->load->view('error/not_found', $data));
		}
	}

	public function agree() {
		$this->load->model('catalog/information');

		if (isset($this->request->get['information_id'])) {
			$information_id = (int)$this->request->get['information_id'];
		} else {
			$information_id = 0;
		}

		$output = '';

		$information_info = $this->model_catalog_information->getInformation($information_id);

		if ($information_info) {
			$output .= html_entity_decode($information_info['description'], ENT_QUOTES, 'UTF-8') . "\n";
		}

		$this->response->setOutput($output);
	}
}