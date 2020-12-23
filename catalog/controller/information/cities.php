<?php
// *	@source		See SOURCE.txt for source and other copyright.
// *	@license	GNU General Public License version 3; see LICENSE.txt

class ControllerInformationCities extends Controller {
	public function index() {


		echo '		НЕ РАБОТАЕТ 404		ХЗ	';



		$this->load->model('information/cities');



		$cities_info = $this->model_catalog_cities->getCities();

		if ($cities_info) {
			
			foreach ($cities_info as $result) {
				$offices =  $this->model_catalog_city->getOffices(['city_id' => $result['city_id']]);
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
					'add_office'   => $this->url->link('catalog/cities/edit_office', 'user_token=' . $this->session->data['user_token'] . '&city_id=' . $result['city_id'] . $url, true),
					'edit'           => $this->url->link('catalog/cities/edit', 'user_token=' . $this->session->data['user_token'] . '&city_id=' . $result['city_id'] . $url, true),
				);
			}
			print_R($data['cities']);
			return $data['cities'];
			//$this->response->setOutput($this->load->view('information/information', $data));
		}

	}
}