<?php
// *	@source		See SOURCE.txt for source and other copyright.
// *	@license	GNU General Public License version 3; see LICENSE.txt

class ControllerCommonHeader extends Controller {
	public function index() {
		// Analytics
		$this->load->model('setting/extension');

		$data['analytics'] = array();

		$analytics = $this->model_setting_extension->getExtensions('analytics');

		foreach ($analytics as $analytic) {
			if ($this->config->get('analytics_' . $analytic['code'] . '_status')) {
				$data['analytics'][] = $this->load->controller('extension/analytics/' . $analytic['code'], $this->config->get('analytics_' . $analytic['code'] . '_status'));
			}
		}

		if ($this->request->server['HTTPS']) {
			$server = $this->config->get('config_ssl');
		} else {
			$server = $this->config->get('config_url');
		}

		if (is_file(DIR_IMAGE . $this->config->get('config_icon'))) {
			$this->document->addLink($server . 'image/' . $this->config->get('config_icon'), 'icon');
		}

		$data['title'] = $this->document->getTitle();

		$data['base'] = $server;
		$data['description'] = $this->document->getDescription();
		$data['keywords'] = $this->document->getKeywords();
		$data['links'] = $this->document->getLinks();
		$data['robots'] = $this->document->getRobots();
		$data['styles'] = $this->document->getStyles();
		$data['scripts'] = $this->document->getScripts('header');
		$data['lang'] = $this->language->get('code');
		$data['direction'] = $this->language->get('direction');

		$data['name'] = $this->config->get('config_name');

		if (is_file(DIR_IMAGE . $this->config->get('config_logo'))) {
			$data['logo'] = $server . 'image/' . $this->config->get('config_logo');
		} else {
			$data['logo'] = '';
		}

		$this->load->language('common/header');
		
		
		$host = isset($this->request->server['HTTPS']) && (($this->request->server['HTTPS'] == 'on') || ($this->request->server['HTTPS'] == '1')) ? HTTPS_SERVER : HTTP_SERVER;
		if ($this->request->server['REQUEST_URI'] == '/') {
			$data['og_url'] = $this->url->link('common/home');
		} else {
			$data['og_url'] = $host . substr($this->request->server['REQUEST_URI'], 1, (strlen($this->request->server['REQUEST_URI'])-1));
		}
		
		$data['og_image'] = $this->document->getOgImage();
		
		//Текущая страница
		$current_page = @reset(explode('?',$this->request->server['REQUEST_URI']));
		$data['current_page_link'] = trim($server, '/').$current_page;
		$data['current_page'] = $current_page;

		//Подключаем меню каталога
		$this->load->model('catalog/category');
		$data['categories'] = array();
		$categories = $this->model_catalog_category->getAllCategories(0, 34718);

		foreach ($categories as $category) {
			$children_data = array();

			$filter_data = array(
				'filter_category_id'  => $category['category_id'],
				'filter_sub_category' => true
			);

			$childs = [];
			foreach ($category['children'] as $ch) {
				$ch['href'] = $this->url->link('product/category', 'path=' . $ch['category_id']);
				$childs[]=$ch;
			}

			if(empty($category['children'])) $category['children'] = [];
			if(!empty($category['top']) || $category['category_id'] == 31718)
				$data['categories'][] = array(
					'category_id' => $category['category_id'],
					//'name'	=> $category['name'],
					'name'	=> $category['meta_h1'],
					'icon'	=> html_entity_decode($category['icon']),
					'product_count' => $this->config->get('config_product_count') ? ' (' . $this->model_catalog_product->getTotalProducts($filter_data) . ')' : '',
					'children'    => $childs,
					'href'        => $this->url->link('product/category', 'path=' . $category['category_id']),
					'current' => $this->url->link('product/category', 'path=' . $category['category_id']) == $current_page ? 1 : 0,
				);
		}

		//print_r($data['categories']);
		//--Подключаем меню каталога для мобильной версии
/*
		// Wishlist
		if ($this->customer->isLogged()) {
			$this->load->model('account/wishlist');
			$this->load->model('account/messages');

			$data['text_wishlist'] = sprintf($this->language->get('text_wishlist'), $this->model_account_wishlist->getTotalWishlist());
			$wl = $this->model_account_wishlist->getWishlist();
			$data['wishlist_count'] =  count($wl);
			//$data['wishlist_count'] = (isset($this->session->data['wishlist'])) ? count($this->session->data['wishlist']) : 0;
			
			$data['messages_count'] = $this->model_account_messages->getTotalMessages();
			
		} else {
			$data['messages_count'] = 0;
			$data['text_wishlist'] = sprintf($this->language->get('text_wishlist'), (isset($this->session->data['wishlist']) ? count($this->session->data['wishlist']) : 0));
			$data['wishlist_count'] = (isset($this->session->data['wishlist'])) ? count($this->session->data['wishlist']) : 0;
		}
		//---Wishlist
*/

		$data['text_logged'] = sprintf($this->language->get('text_logged'), $this->url->link('account/account', '', true), $this->customer->getFirstName(), $this->url->link('account/logout', '', true));
		
		$data['home'] = $this->url->link('common/home');
		$data['wishlist'] = $this->url->link('account/wishlist', '', true);
		$data['messages'] = $this->url->link('account/newsletter', '', true);
		$data['logged'] = $this->customer->isLogged();
		$data['account'] = $this->url->link('account/account', '', true);
		$data['forgotten'] = $this->url->link('account/forgotten', '', true);
		$data['register'] = $this->url->link('account/register', '', true);
		$data['login'] = $this->url->link('account/login', '', true);
		$data['order'] = $this->url->link('account/order', '', true);
		$data['transaction'] = $this->url->link('account/transaction', '', true);
		$data['download'] = $this->url->link('account/download', '', true);
		$data['logout'] = $this->url->link('account/logout', '', true);
		$data['shopping_cart'] = $this->url->link('checkout/cart');
		$data['checkout'] = $this->url->link('checkout/checkout', '', true);
		$data['contact'] = $this->url->link('information/contact');
		$data['telephone'] = $this->config->get('config_telephone');
		
		$data['language'] = $this->load->controller('common/language');
		$data['currency'] = $this->load->controller('common/currency');
		$data['currency'] = $this->load->controller('common/currency');
		if ($this->config->get('configblog_blog_menu')) {
			$data['blog_menu'] = $this->load->controller('blog/menu');
		} else {
			$data['blog_menu'] = '';
		}
		$data['search'] = $this->load->controller('common/search');
		$data['cart'] = $this->load->controller('common/cart');
		$data['menu'] = $this->load->controller('common/menu');

		return $this->load->view('common/header', $data);
	}
}
