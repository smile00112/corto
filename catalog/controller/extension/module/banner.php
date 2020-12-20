<?php
class ControllerExtensionModuleBanner extends Controller {
	public function index($setting) {
		static $module = 0;

		$this->load->model('design/banner');
		$this->load->model('tool/image');

/*
		$this->document->addStyle('catalog/view/javascript/jquery/swiper/css/swiper.min.css');
		$this->document->addStyle('catalog/view/javascript/jquery/swiper/css/opencart.css');
		$this->document->addScript('catalog/view/javascript/jquery/swiper/js/swiper.jquery.js'); 
*/
		$data['banners'] = array();

		$results = $this->model_design_banner->getBanner($setting['banner_id']);

		$template_name = (!empty($setting['template_name'])) ? $setting['template_name'] : 'banner';

		if ($this->request->server['HTTPS']) {
			$server = $this->config->get('config_ssl');
		} else {
			$server = $this->config->get('config_url');
		}

		//Текущая страница
		$current_page = @reset(explode('?',$this->request->server['REQUEST_URI']));
		$data['current_page_link'] = trim($server, '/').$current_page;
		$data['current_page'] = $current_page;

		foreach ($results as $result) {
				$data['banners'][$result['group']][] = array(
					'title' => $result['title'],
					'link'  => $result['link'],
					'thumb' => $this->model_tool_image->resize($result['image'], $setting['width'], $setting['height']),
					'image' => '/image/'.$result['image']
				);
		}
		$data['title'] = $setting['name'];
		$data['module'] = $module++;

		return $this->load->view('extension/module/'.$template_name, $data);
	}
}