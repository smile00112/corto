<?php
// *	@source		See SOURCE.txt for source and other copyright.
// *	@license	GNU General Public License version 3; see LICENSE.txt

class ControllerProductCategory extends Controller {
	public function index() {
		$this->load->language('product/category');

		$this->load->model('catalog/category');

		$this->load->model('catalog/product');

		$this->load->model('tool/image');
		


		$data['text_empty'] = $this->language->get('text_empty');

		if (isset($this->request->get['filter'])) {
			$filter = $this->request->get['filter'];
			$this->document->setRobots('noindex,follow');
		} else {
			$filter = '';
		}

		if (isset($this->request->get['sort'])) {
			$sort = $this->request->get['sort'];
			$this->document->setRobots('noindex,follow');
		} else {
			$sort = 'p.sort_order';
		}

		if (isset($this->request->get['order'])) {
			$order = $this->request->get['order'];
			$this->document->setRobots('noindex,follow');
		} else {
			$order = 'ASC';
		}

		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
			$this->document->setRobots('noindex,follow');
		} else {
			$page = 1;
		}

		if (isset($this->request->get['limit'])) {
			$limit = (int)$this->request->get['limit'];
			$this->document->setRobots('noindex,follow');
		} else {
			$limit = $this->config->get('theme_' . $this->config->get('config_theme') . '_product_limit');
		}

		$data['breadcrumbs'] = array();
/*
		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/home')
		);
*/
		if (isset($this->request->get['path'])) {
			$url = '';

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['limit'])) {
				$url .= '&limit=' . $this->request->get['limit'];
			}

			$path = '';

			$parts = explode('_', (string)$this->request->get['path']);

			$category_id = (int)array_pop($parts);

			foreach ($parts as $path_id) {
				if (!$path) {
					$path = (int)$path_id;
				} else {
					$path .= '_' . (int)$path_id;
				}

				$category_info = $this->model_catalog_category->getCategory($path_id);

				if ($category_info) {
					$data['breadcrumbs'][] = array(
						'text' => $category_info['name'],
						'href' => $this->url->link('product/category', 'path=' . $path . $url)
					);
				}
			}
		} else {
			$category_id = 0;
		}
		
		$category_info = [];
		if($this->request->get['path'] != '35774')
			$category_info = $this->model_catalog_category->getCategory($category_id);

		if ($category_info) {
			$data['categories'] = array();


			//ищем вылосипеды
			$results = $this->model_catalog_category->getCategories($category_id);

			foreach ($results as $result) {
				$filter_data = array(
					'filter_category_id'  => $result['category_id'],
					'filter_sub_category' => true
				);
				$category_name =  $result['name'];
				$data['categories'][] = array(
					'name' => $result['name'] . ($this->config->get('config_product_count') ? ' (' . $this->model_catalog_product->getTotalProducts($filter_data) . ')' : ''),
					'href' => $this->url->link('product/category', 'path=' . $this->request->get['path'] . '_' . $result['category_id'] . $url),
					'thumb' => ($result['image']) ? $this->model_tool_image->resize($result['image'], 500, 500) : '',
				);

				//ищем товары в  категории
				$filter_data = array(
					'filter_category_id' => $result['category_id'],
					'filter_filter'      => [],
					'sort'               => $sort,
					'order'              => $order,
					'start'              => 0,
					'limit'              => 1000
				);
				$results = $this->model_catalog_product->getProducts($filter_data);

				foreach ($results as $result) {
					
					if ($result['image']) {
						$image = $this->model_tool_image->resize($result['image'], $this->config->get('theme_' . $this->config->get('config_theme') . '_image_product_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_product_height'));
					} else {
						$image = $this->model_tool_image->resize('placeholder.png', $this->config->get('theme_' . $this->config->get('config_theme') . '_image_product_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_product_height'));
					}
	
					if ($this->customer->isLogged() || !$this->config->get('config_customer_price')) {
						$price = $this->currency->format($this->tax->calculate($result['price'], $result['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
					} else {
						$price = false;
					}
				
					if($result['image'] != 'catalog/noimage.png')
						$i = $this->model_tool_image->resize($result['image'], $this->config->get('theme_' . $this->config->get('config_theme') . '_image_popup_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_popup_height'));
					else $i = '';
	
					$data['products'][$category_name][] = array(
						'product_id'  => $result['product_id'],
						'thumb'       => $image,
						//'image'       => 'image/' . $result['image'],
						'image' => $i,
						'mpn'        => $result['mpn'],
						'sku'        => $result['sku'],
						'upc'        => $result['upc'],
						'quantity' =>  $result['quantity'],
						'name'        => $result['name'],
						//'description' => utf8_substr(trim(strip_tags(html_entity_decode($result['description'], ENT_QUOTES, 'UTF-8'))), 0, $this->config->get('theme_' . $this->config->get('config_theme') . '_product_description_length')) . '..',
						'price'       => $price,
						'price_not_formated' => number_format( $this->tax->calculate($result['price'], $result['tax_class_id'], $this->config->get('config_tax')), 0, '.', ' '),
						'href'        => $this->url->link('product/product', 'path=' . $this->request->get['path'] . '&product_id=' . $result['product_id'] . $url)
						//'href'        => $this->url->link('product/product', 'path=' . $this->request->get['path'] . '&product_id=' . $result['product_id'] . $url)
					);
				}
			}
				
				
			// if ($category_info['meta_title']) {
			// 	$this->document->setTitle($category_info['meta_title']);
			// } else {
			// 	$this->document->setTitle($category_info['name']);
			// }
/*
			if( $page > 1 )  $this->document->setTitle($category_info['meta_title'].', страница '.$page);
				else $this->document->setTitle($category_info['meta_title']);


			if ($category_info['noindex'] <= 0) {
				$this->document->setRobots('noindex,follow');
			}
			
			if ($category_info['meta_h1']) {
				$data['heading_title'] = $category_info['meta_h1'];
			} else {
				$data['heading_title'] = $category_info['name'];
			}
			
			$this->document->setDescription($category_info['meta_description']);
			$this->document->setKeywords($category_info['meta_keyword']);

			$data['text_compare'] = sprintf($this->language->get('text_compare'), (isset($this->session->data['compare']) ? count($this->session->data['compare']) : 0));

			// Set the last category breadcrumb
			$data['breadcrumbs'][] = array(
				'text' => $category_info['name'],
				//'href' => $this->url->link('product/category', 'path=' . $this->request->get['path'])
			);

			if ($category_info['image']) {
				$data['image'] = '/image/'.$category_info['image'];
				$data['thumb'] = $this->model_tool_image->resize($category_info['image'], $this->config->get('theme_' . $this->config->get('config_theme') . '_image_category_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_category_height'));
			} else {
				$data['thumb'] = '';
				$data['image'] = '';
			}
			$data['category_name'] =  $category_info['name'];
			$data['description'] = html_entity_decode($category_info['description'], ENT_QUOTES, 'UTF-8');
			$data['compare'] = $this->url->link('product/compare');

			$url = '';

			if (isset($this->request->get['filter'])) {
				$url .= '&filter=' . $this->request->get['filter'];
			}

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['limit'])) {
				$url .= '&limit=' . $this->request->get['limit'];
			}

			$data['categories'] = array();

			$results = $this->model_catalog_category->getCategories($category_id);

			foreach ($results as $result) {
				$filter_data = array(
					'filter_category_id'  => $result['category_id'],
					'filter_sub_category' => true
				);

				$data['categories'][] = array(
					'name' => $result['name'] . ($this->config->get('config_product_count') ? ' (' . $this->model_catalog_product->getTotalProducts($filter_data) . ')' : ''),
					'href' => $this->url->link('product/category', 'path=' . $this->request->get['path'] . '_' . $result['category_id'] . $url),
					'thumb' => ($result['image']) ? $this->model_tool_image->resize($result['image'], 500, 500) : '',
					
				);
			}

			$data['products'] = array();

			$filter_data = array(
				'filter_category_id' => $category_id,
				'filter_filter'      => $filter,
				'sort'               => $sort,
				'order'              => $order,
				'start'              => ($page - 1) * $limit,
				'limit'              => $limit
			);
			if(!empty($this->request->get['price_from'])) $filter_data['filter_price_from'] = $this->request->get['price_from'];
			if(!empty($this->request->get['price_to'])) $filter_data['filter_price_to'] = $this->request->get['price_to'];

			$data['price_from'] = !empty($this->request->get['price_from']) ? $this->request->get['price_from'] : 0;
			$data['price_to'] = !empty($this->request->get['price_to']) ? $this->request->get['price_to'] : 50000;

			if(!empty($this->request->get['color'])) $filter_data['filter_color'] = explode(";", $this->request->get['color']);
			if(!empty($this->request->get['country'])) $filter_data['filter_country'] = explode(";", $this->request->get['country']);
			if(!empty($this->request->get['model'])) $filter_data['filter_model'] = explode(";", $this->request->get['model']);
			if(!empty($this->request->get['matherial'])) $filter_data['filter_matherial'] = explode(";", $this->request->get['matherial']);
			if(!empty($this->request->get['manufacturer'])) $filter_data['filter_manufacturer'] = explode(";", $this->request->get['manufacturer']);			

			//if(!empty($this->request->get['category'])) $filter_data['filter_categoryes_id'] = $this->request->get['category'];
			// if(!empty($this->request->get['effects'])) $filter_data['filter_effects'] = $this->request->get['effects'];
			// if(!empty($this->request->get['power'])) $filter_data['filter_power'] = $this->request->get['power'];
			// if(!empty($this->request->get['zarjd_from'])) $filter_data['filter_zarjd_from'] = $this->request->get['zarjd_from'];
			// if(!empty($this->request->get['zarjd_to'])) $filter_data['filter_zarjd_to'] = $this->request->get['zarjd_to'];
			// if(!empty($this->request->get['kalibr_from'])) $filter_data['filter_kalibr_from'] = $this->request->get['kalibr_from'];
			// if(!empty($this->request->get['kalibr_to'])) $filter_data['filter_kalibr_to'] = $this->request->get['kalibr_to'];
			// if(!empty($this->request->get['work_time_from'])) $filter_data['filter_work_time_from'] = $this->request->get['work_time_from'];
			// if(!empty($this->request->get['work_time_to'])) $filter_data['filter_work_time_to'] = $this->request->get['work_time_to'];

			// if(!empty($this->request->get['novinki'])) $filter_data['filter_novinki'] = $this->request->get['novinki'];
			// if(!empty($this->request->get['special'])) $filter_data['filter_special'] = $this->request->get['special'];

		 	$product_total = $this->model_catalog_product->getTotalProducts($filter_data);

			$data['product_total'] = $product_total;

			$filter_data_wl = array(
				'filter_category_id' => $category_id,
			);
			$filters_attr = [];

			//Варианты для фильтра
			$filters_attr['price']['min'] = $data['price_from'];
			$filters_attr['price']['max'] = $data['price_to'];
			
			$filters_attr['attributes'] = $this->model_catalog_category->getAllFilters_atr($filter_data_wl);


			//Карта товаров с характеристиками
			$data['category_products_map2'] = $this->model_catalog_product->get_AllCategoryProducts(['category_id'=>$category_id ]);
		 	$data['category_products_map'] = json_encode($data['category_products_map2']);

			$data['filter_color'] = $filters_attr['attributes'][50]['results'];
			$data['filter_manufacturer'] = $filters_attr['attributes'][44]['results'];
			$data['filter_matherial'] = $filters_attr['attributes'][55]['results'];
			$data['filter_model'] = $filters_attr['attributes'][56]['results'];
			
			//Дорабатывает цвета
			$request_color = !empty($this->request->get['color']) ? $this->request->get['color'] : '';
			$request_color = explode(';', $request_color); 
			foreach($data['filter_color'] as &$c){
				$c['translit'] =  $this->getTranslit($c['text']);
				$c['checked'] = in_array( $c['text'], $request_color ) ? true : false;
			}

			$request_manufacturer = !empty($this->request->get['manufacturer']) ? $this->request->get['manufacturer'] : '';
			$request_manufacturer = explode(';', $request_manufacturer); 
			foreach($data['filter_manufacturer'] as &$c){
				$c['checked'] = in_array( $c['text'], $request_manufacturer ) ? true : false;
			}

			$request_matherial = !empty($this->request->get['matherial']) ? $this->request->get['matherial'] : '';
			$request_matherial = explode(';', $request_matherial); 
			foreach($data['filter_matherial'] as &$c){
				$c['checked'] = in_array( $c['text'], $request_matherial ) ? true : false;
			}

			$request_model = !empty($this->request->get['model']) ? $this->request->get['model'] : '';
			$request_model = explode(';', $request_model); 
			foreach($data['filter_model'] as &$c){
				$c['checked'] = in_array( $c['text'], $request_model ) ? true : false;
			}


			


			$results = $this->model_catalog_product->getProducts($filter_data);

			foreach ($results as $result) {
				
				$productAttributes = $this->model_catalog_product->getProductAttributes($result['product_id']);
				$kol_po_katalogy = $opisanie = '';

				foreach($productAttributes as $pa_gr){
					if($pa_gr['name'] == 'Запчасти'){
						foreach($pa_gr['attribute'] as $pa_gr_atr){
							if($pa_gr_atr['name'] == 'Количество по каталогу'){
								$kol_po_katalogy = $pa_gr_atr['text'];
							}elseif($pa_gr_atr['name'] == 'Описание'){
								$opisanie = $pa_gr_atr['text'];
							}
							
						}
					}
				}

				if ($result['image']) {
					$image = $this->model_tool_image->resize($result['image'], $this->config->get('theme_' . $this->config->get('config_theme') . '_image_product_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_product_height'));
				} else {
					$image = $this->model_tool_image->resize('placeholder.png', $this->config->get('theme_' . $this->config->get('config_theme') . '_image_product_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_product_height'));
				}

				if ($this->customer->isLogged() || !$this->config->get('config_customer_price')) {
					$price = $this->currency->format($this->tax->calculate($result['price'], $result['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
				} else {
					$price = false;
				}
				
				
				if ((float)$result['special']) {
					$special = $this->currency->format($this->tax->calculate($result['special'], $result['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
				} else {
					$special = false;
				}

				if ($this->config->get('config_tax')) {
					$tax = $this->currency->format((float)$result['special'] ? $result['special'] : $result['price'], $this->session->data['currency']);
				} else {
					$tax = false;
				}

				if ($this->config->get('config_review_status')) {
					$rating = (int)$result['rating'];
				} else {
					$rating = false;
				}
				
				if($result['image'] != 'catalog/noimage.png')
					$i = $this->model_tool_image->resize($result['image'], $this->config->get('theme_' . $this->config->get('config_theme') . '_image_popup_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_popup_height'));
				else $i = '';

				$data['products'][] = array(
					'product_id'  => $result['product_id'],
					'kol_po_katalogy'  => $kol_po_katalogy,
					'opisanie'  => $opisanie,
					'thumb'       => $image,
					//'image'       => 'image/' . $result['image'],
					'image' => $i,
					'mpn'        => $result['mpn'],
					'sku'        => $result['sku'],
					'upc'        => $result['upc'],
					'quantity' =>  $result['quantity'],
					'name'        => $result['name'],
					'description' => utf8_substr(trim(strip_tags(html_entity_decode($result['description'], ENT_QUOTES, 'UTF-8'))), 0, $this->config->get('theme_' . $this->config->get('config_theme') . '_product_description_length')) . '..',
					'price'       => $price,
					'price_not_formated' => number_format( $this->tax->calculate($result['price'], $result['tax_class_id'], $this->config->get('config_tax')), 0, '.', ' '),
					'special'     => $special,
					'tax'         => $tax,
					'minimum'     => $result['minimum'] > 0 ? $result['minimum'] : 1,
					'rating'      => $result['rating'],
					'href'        => $this->url->link('product/product', 'path=' . $this->request->get['path'] . '&product_id=' . $result['product_id'] . $url)
				);
			}

			$url = '';

			if (isset($this->request->get['filter'])) {
				$url .= '&filter=' . $this->request->get['filter'];
			}

			if (isset($this->request->get['limit'])) {
				$url .= '&limit=' . $this->request->get['limit'];
			}

			$data['sorts'] = array();

			$data['sorts'][] = array(
				'text'  => $this->language->get('text_default'),
				'value' => 'p.sort_order-ASC',
				'href'  => $this->url->link('product/category', 'path=' . $this->request->get['path'] . '&sort=p.sort_order&order=ASC' . $url)
			);
			
			if( $sort == 'p.sort_order'){
				if($order == 'ASC') $data['sort_sort_order'] = $this->url->link('product/category', 'path=' . $this->request->get['path'] . '&sort=p.sort_order&order=DESC' . $url);
				else  $data['sort_sort_order'] = $this->url->link('product/category', 'path=' . $this->request->get['path'] . '&sort=p.sort_order&order=ASC' . $url);
			}else{
				$data['sort_sort_order'] = $this->url->link('product/category', 'path=' . $this->request->get['path'] . '&sort=p.sort_order&order=ASC' . $url);
			}

			$data['sorts'][] = array(
				'text'  => $this->language->get('text_name_asc'),
				'value' => 'pd.name-ASC',
				'href'  => $this->url->link('product/category', 'path=' . $this->request->get['path'] . '&sort=pd.name&order=ASC' . $url)
			);

			if( $sort == 'p.name'){
				if($order == 'ASC') $data['sort_sort_name'] = $this->url->link('product/category', 'path=' . $this->request->get['path'] . '&sort=p.name&order=DESC' . $url);
				else  $data['sort_sort_name'] = $this->url->link('product/category', 'path=' . $this->request->get['path'] . '&sort=p.name&order=ASC' . $url);
			}else{
				$data['sort_sort_name'] = $this->url->link('product/category', 'path=' . $this->request->get['path'] . '&sort=p.name&order=ASC' . $url);
			}

			$data['sort_sort_name_asc'] = $this->url->link('product/category', 'path=' . $this->request->get['path'] . '&sort=p.name&order=ASC' . $url);
			$data['sort_sort_name_desc'] = $this->url->link('product/category', 'path=' . $this->request->get['path'] . '&sort=p.name&order=DESC' . $url);		

			$data['sorts'][] = array(
				'text'  => $this->language->get('text_name_desc'),
				'value' => 'pd.name-DESC',
				'href'  => $this->url->link('product/category', 'path=' . $this->request->get['path'] . '&sort=pd.name&order=DESC' . $url)
			);

			$data['sorts'][] = array(
				'text'  => $this->language->get('text_price_asc'),
				'value' => 'p.price-ASC',
				'href'  => $this->url->link('product/category', 'path=' . $this->request->get['path'] . '&sort=p.price&order=ASC' . $url)
			);

			if( $sort == 'p.price'){
				if($order == 'ASC') $data['sort_sort_price'] = $this->url->link('product/category', 'path=' . $this->request->get['path'] . '&sort=p.price&order=DESC' . $url);
				else  $data['sort_sort_price'] = $this->url->link('product/category', 'path=' . $this->request->get['path'] . '&sort=p.price&order=ASC' . $url);
			}else{
				$data['sort_sort_price'] = $this->url->link('product/category', 'path=' . $this->request->get['path'] . '&sort=p.price&order=ASC' . $url);
			}
			$data['sort_sort_price_asc'] = $this->url->link('product/category', 'path=' . $this->request->get['path'] . '&sort=p.price&order=ASC' . $url);
			$data['sort_sort_price_desc'] = $this->url->link('product/category', 'path=' . $this->request->get['path'] . '&sort=p.price&order=DESC' . $url);

			$data['sorts'][] = array(
				'text'  => $this->language->get('text_price_desc'),
				'value' => 'p.price-DESC',
				'href'  => $this->url->link('product/category', 'path=' . $this->request->get['path'] . '&sort=p.price&order=DESC' . $url)
			);

			if ($this->config->get('config_review_status')) {
				$data['sorts'][] = array(
					'text'  => $this->language->get('text_rating_desc'),
					'value' => 'rating-DESC',
					'href'  => $this->url->link('product/category', 'path=' . $this->request->get['path'] . '&sort=rating&order=DESC' . $url)
				);

				$data['sorts'][] = array(
					'text'  => $this->language->get('text_rating_asc'),
					'value' => 'rating-ASC',
					'href'  => $this->url->link('product/category', 'path=' . $this->request->get['path'] . '&sort=rating&order=ASC' . $url)
				);
			}

			$data['sorts'][] = array(
				'text'  => $this->language->get('text_model_asc'),
				'value' => 'p.model-ASC',
				'href'  => $this->url->link('product/category', 'path=' . $this->request->get['path'] . '&sort=p.model&order=ASC' . $url)
			);

			$data['sorts'][] = array(
				'text'  => $this->language->get('text_model_desc'),
				'value' => 'p.model-DESC',
				'href'  => $this->url->link('product/category', 'path=' . $this->request->get['path'] . '&sort=p.model&order=DESC' . $url)
			);

			$url = '';

			if (isset($this->request->get['filter'])) {
				$url .= '&filter=' . $this->request->get['filter'];
			}

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			$data['limits'] = array();

			$limits = array_unique(array($this->config->get('theme_' . $this->config->get('config_theme') . '_product_limit'), 25, 50, 75, 100));

			sort($limits);

			foreach($limits as $value) {
				$data['limits'][] = array(
					'text'  => $value,
					'value' => $value,
					'href'  => $this->url->link('product/category', 'path=' . $this->request->get['path'] . $url . '&limit=' . $value)
				);
			}

			$url = '';

			if (isset($this->request->get['filter'])) {
				$url .= '&filter=' . $this->request->get['filter'];
			}

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['limit'])) {
				$url .= '&limit=' . $this->request->get['limit'];
			}

			$pagination = new Pagination();
			$pagination->total = $product_total;
			$pagination->page = $page;
			$pagination->limit = $limit;
			$pagination->url = $this->url->link('product/category', 'path=' . $this->request->get['path'] . $url . '&page={page}');

			$data['pagination'] = $pagination->render();

			$data['results'] = sprintf($this->language->get('text_pagination'), ($product_total) ? (($page - 1) * $limit) + 1 : 0, ((($page - 1) * $limit) > ($product_total - $limit)) ? $product_total : ((($page - 1) * $limit) + $limit), $product_total, ceil($product_total / $limit));


			//Ссылка для фильтра 
			$url .= '&path=' . $category_id;
			$u = $this->url->link('product/category', $url);
			$u = htmlspecialchars_decode($u);
			$data['u'] = html_entity_decode($u);


						
			// http://googlewebmastercentral.blogspot.com/2011/09/pagination-with-relnext-and-relprev.html
			if ($page != 1) {
			    $this->document->addLink($this->url->link('product/category', 'path=' . $category_info['category_id']), 'canonical');
			} else {
				$this->document->addLink($this->url->link('product/category', 'path=' . $category_info['category_id'] . '&page='. $page), 'canonical');
			}
			
			if ($page > 1) {
			    $this->document->addLink($this->url->link('product/category', 'path=' . $category_info['category_id'] . (($page - 2) ? '&page='. ($page - 1) : '')), 'prev');
			}

			if ($limit && ceil($product_total / $limit) > $page) {
			    $this->document->addLink($this->url->link('product/category', 'path=' . $category_info['category_id'] . '&page='. ($page + 1)), 'next');
			}

			$data['sort'] = $sort;
			$data['order'] = $order;
			$data['limit'] = $limit;
*/
			$data['continue'] = $this->url->link('common/home');

			$data['column_left'] = $this->load->controller('common/column_left');
			$data['column_right'] = $this->load->controller('common/column_right');
			$data['content_top'] = $this->load->controller('common/content_top');
			$data['content_bottom'] = $this->load->controller('common/content_bottom');
			$data['footer'] = $this->load->controller('common/footer');
			$data['header'] = $this->load->controller('common/header');
			

			$this->response->setOutput($this->load->view('product/category_catalog', $data));
		} else if($this->request->get['path'] == 0){
			// мы в /catalog/

			
			$data['categories'] = array();

			//ищем вылосипеды
			$results = $this->model_catalog_category->getCategories(35762);

			foreach ($results as $result) {
				$filter_data = array(
					'filter_category_id'  => $result['category_id'],
					'filter_sub_category' => true
				);
				$category_name =  $result['name'];
				$data['categories'][] = array(
					'name' => $result['name'] . ($this->config->get('config_product_count') ? ' (' . $this->model_catalog_product->getTotalProducts($filter_data) . ')' : ''),
					'href' => $this->url->link('product/category', 'path=' . $this->request->get['path'] . '_' . $result['category_id'] . $url),
					'thumb' => ($result['image']) ? $this->model_tool_image->resize($result['image'], 500, 500) : '',
				);

				//ищем товары в  категории
				$filter_data = array(
					'filter_category_id' => $result['category_id'],
					'filter_filter'      => [],
					'sort'               => $sort,
					'order'              => $order,
					'start'              => 0,
					'limit'              => 1000
				);
				$results = $this->model_catalog_product->getProducts($filter_data);

				foreach ($results as $result) {
					
					if ($result['image']) {
						$image = $this->model_tool_image->resize($result['image'], $this->config->get('theme_' . $this->config->get('config_theme') . '_image_product_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_product_height'));
					} else {
						$image = $this->model_tool_image->resize('placeholder.png', $this->config->get('theme_' . $this->config->get('config_theme') . '_image_product_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_product_height'));
					}
	
					if ($this->customer->isLogged() || !$this->config->get('config_customer_price')) {
						$price = $this->currency->format($this->tax->calculate($result['price'], $result['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
					} else {
						$price = false;
					}
				
					if($result['image'] != 'catalog/noimage.png')
						$i = $this->model_tool_image->resize($result['image'], $this->config->get('theme_' . $this->config->get('config_theme') . '_image_popup_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_popup_height'));
					else $i = '';
	
					$data['products'][$category_name][] = array(
						'product_id'  => $result['product_id'],
						'thumb'       => $image,
						//'image'       => 'image/' . $result['image'],
						'image' => $i,
						'mpn'        => $result['mpn'],
						'sku'        => $result['sku'],
						'upc'        => $result['upc'],
						'quantity' =>  $result['quantity'],
						'name'        => $result['name'],
						//'description' => utf8_substr(trim(strip_tags(html_entity_decode($result['description'], ENT_QUOTES, 'UTF-8'))), 0, $this->config->get('theme_' . $this->config->get('config_theme') . '_product_description_length')) . '..',
						'price'       => $price,
						'price_not_formated' => number_format( $this->tax->calculate($result['price'], $result['tax_class_id'], $this->config->get('config_tax')), 0, '.', ' '),
						'href'        => $this->url->link('product/product', 'path=' . $this->request->get['path'] . '&product_id=' . $result['product_id'] . $url)
						//'href'        => $this->url->link('product/product', 'path=' . $this->request->get['path'] . '&product_id=' . $result['product_id'] . $url)
					);
				}


			}

			//print_r($data['products']);


			$data['column_left'] = $this->load->controller('common/column_left');
			$data['column_right'] = $this->load->controller('common/column_right');
			$data['content_top'] = $this->load->controller('common/content_top');
			$data['content_bottom'] = $this->load->controller('common/content_bottom');
			$data['footer'] = $this->load->controller('common/footer');
			$data['header'] = $this->load->controller('common/header');
			$this->response->setOutput($this->load->view('product/category_catalog', $data));

		}		
		else  if($this->request->get['path'] == '35774'){
			$data['categories'] = array();
			//print_r($this->request->get);
			//ищем вылосипеды
			$results = $this->model_catalog_category->getCategories(35774);

			foreach ($results as $result) {
				$filter_data = array(
					'filter_category_id'  => $result['category_id'],
					'filter_sub_category' => true
				);
				$category_name =  $result['name'];
				$data['categories'][] = array(
					'name' => $result['name'] . ($this->config->get('config_product_count') ? ' (' . $this->model_catalog_product->getTotalProducts($filter_data) . ')' : ''),
					'href' => $this->url->link('product/category', 'path=' . $this->request->get['path'] . '_' . $result['category_id'] . $url),
					'thumb' => ($result['image']) ? $this->model_tool_image->resize($result['image'], 500, 500) : '',
				);

				//ищем товары в  категории
				$filter_data = array(
					'filter_category_id' => $result['category_id'],
					'filter_filter'      => [],
					'sort'               => $sort,
					'order'              => $order,
					'start'              => 0,
					'limit'              => 1000
				);
				$results = $this->model_catalog_product->getProducts($filter_data);

				foreach ($results as $result) {
					
					if ($result['image']) {
						$image = $this->model_tool_image->resize($result['image'], $this->config->get('theme_' . $this->config->get('config_theme') . '_image_product_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_product_height'));
					} else {
						$image = $this->model_tool_image->resize('placeholder.png', $this->config->get('theme_' . $this->config->get('config_theme') . '_image_product_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_product_height'));
					}
	
					if ($this->customer->isLogged() || !$this->config->get('config_customer_price')) {
						$price = $this->currency->format($this->tax->calculate($result['price'], $result['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
					} else {
						$price = false;
					}
				
					if($result['image'] != 'catalog/noimage.png')
						$i = $this->model_tool_image->resize($result['image'], $this->config->get('theme_' . $this->config->get('config_theme') . '_image_popup_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_popup_height'));
					else $i = '';
	
					$data['products'][$category_name][] = array(
						'product_id'  => $result['product_id'],
						'thumb'       => $image,
						//'image'       => 'image/' . $result['image'],
						'image' => $i,
						'mpn'        => $result['mpn'],
						'sku'        => $result['sku'],
						'upc'        => $result['upc'],
						'quantity' =>  $result['quantity'],
						'name'        => $result['name'],
						//'description' => utf8_substr(trim(strip_tags(html_entity_decode($result['description'], ENT_QUOTES, 'UTF-8'))), 0, $this->config->get('theme_' . $this->config->get('config_theme') . '_product_description_length')) . '..',
						'price'       => $price,
						'price_not_formated' => number_format( $this->tax->calculate($result['price'], $result['tax_class_id'], $this->config->get('config_tax')), 0, '.', ' '),
						'href'        => $this->url->link('product/product', 'path=' . $this->request->get['path'] . '&product_id=' . $result['product_id'] . $url)
						//'href'        => $this->url->link('product/product', 'path=' . $this->request->get['path'] . '&product_id=' . $result['product_id'] . $url)
					);
				}


			}

			//print_r($data['products']);


			$data['column_left'] = $this->load->controller('common/column_left');
			$data['column_right'] = $this->load->controller('common/column_right');
			$data['content_top'] = $this->load->controller('common/content_top');
			$data['content_bottom'] = $this->load->controller('common/content_bottom');
			$data['footer'] = $this->load->controller('common/footer');
			$data['header'] = $this->load->controller('common/header');
			$this->response->setOutput($this->load->view('product/category_catalog', $data));
		}
	}

	public function getTranslit($text, $translit = 'ru_en') {
	
		$RU['ru'] = array( 
			'Ё', 'Ж', 'Ц', 'Ч', 'Щ', 'Ш', 'Ы',  
			'Э', 'Ю', 'Я', 'ё', 'ж', 'ц', 'ч',  
			'ш', 'щ', 'ы', 'э', 'ю', 'я', 'А',  
			'Б', 'В', 'Г', 'Д', 'Е', 'З', 'И',  
			'Й', 'К', 'Л', 'М', 'Н', 'О', 'П',  
			'Р', 'С', 'Т', 'У', 'Ф', 'Х', 'Ъ',  
			'Ь', 'а', 'б', 'в', 'г', 'д', 'е',  
			'з', 'и', 'й', 'к', 'л', 'м', 'н',  
			'о', 'п', 'р', 'с', 'т', 'у', 'ф',  
			'х', 'ъ', 'ь', '/'
			); 

		$EN['en'] = array( 
			"Yo", "Zh",  "Cz", "Ch", "Shh","Sh", "Y'",  
			"E'", "Yu",  "Ya", "yo", "zh", "cz", "ch",  
			"sh", "shh", "y'", "e'", "yu", "ya", "A",  
			"B" , "V" ,  "G",  "D",  "E",  "Z",  "I",  
			"J",  "K",   "L",  "M",  "N",  "O",  "P",  
			"R",  "S",   "T",  "U",  "F",  "Kh",  "''", 
			"'",  "a",   "b",  "v",  "g",  "d",  "e",  
			"z",  "i",   "j",  "k",  "l",  "m",  "n",   
			"o",  "p",   "r",  "s",  "t",  "u",  "f",   
			"h",  "''",  "'",  "-"
			); 
		if($translit == 'en_ru') { 
			$t = str_replace($EN['en'], $RU['ru'], $text);         
			$t = preg_replace('/(?<=[а-яё])Ь/u', 'ь', $t); 
			$t = preg_replace('/(?<=[а-яё])Ъ/u', 'ъ', $t); 
			} 
		else {
			$t = str_replace($RU['ru'], $EN['en'], $text);
			$t = preg_replace("/[\s]+/u", "_", $t); 
			$t = preg_replace("/[^a-z0-9_\-]/iu", "", $t); 
			$t = strtolower($t);
			}
		return $t; 
	
	}
}
