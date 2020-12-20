<?php
class ControllerUserPriceUpload extends Controller {
	private $error = array();

	public function index() {

				echo '<meta charset="utf-8">';
			
				$data['user_token'] = $this->session->data['user_token'];
				$url = '';
				$data['action_import_price'] = $this->url->link('user_price/upload/import_products', 'user_token=' . $this->session->data['user_token'] . $url, true);;


				$data['header'] = $this->load->controller('common/header');
				$data['column_left'] = $this->load->controller('common/column_left');
				$data['footer'] = $this->load->controller('common/footer');
		
				$this->response->setOutput($this->load->view('user_price/upload', $data));


			//	$this->response->setOutput(json_encode($json)); getSiteProducts_group_by_article
			//	$this->response->redirect($this->url->link('user_price/upload/asabella', 'token=' . $this->session->data['token'], 'SSL'));

	}



	//Основной импорт
	public function import_products() {

		echo '<meta charset="utf-8">';
		
		$this->load->model('user_price/upload_vendor');
		$all_products = $all_customer_groups = [];
		//$all_products_t = $this->model_user_price_upload_vendor->getSiteProducts();
		include_once('simple_html_dom.php');
		$xml_file_link = str_replace('/admin/', '', $this->config->get('site_ssl')).'/upload/goods.xml';
		$xml = simplexml_load_file($xml_file_link);
		$importFile = $_SERVER['DOCUMENT_ROOT'].'/upload/goods.xml';
		$fileDate = strtotime(date("YmdHis",filemtime($importFile)));
		
		$all_products = $all_customer_groups = [];
		$all_products_t = $this->model_user_price_upload_vendor->getSiteProducts();
		$all_site_categoryes_t = $this->model_user_price_upload_vendor->getSiteCategoryes();
		$all_site_manufacturers = $this->model_user_price_upload_vendor->getManufacturers();
		$all_site_attributes = $this->model_user_price_upload_vendor->getSiteAttributes();		
		$all_site_categoryes = [];

		$th_mod = $this->model_user_price_upload_vendor;
		$products = [];
		$config_language_id = (int)$this->config->get('config_language_id');

		//Обрабатываем КАТЕГОРИИ
		foreach ($xml->catalog->categories->category as $c) {

			if(!empty($c->images->image)){
				if(file_exists($_SERVER['DOCUMENT_ROOT'].'/image/catalog/products/'.(string)$c->images->image))
					$category_image = 'catalog/products/'.(string)$c->images->image;
				else $category_image = 'catalog/noimage.png';
				//$category_image = 'catalog/noimage.png';
			}else{
				$category_image = 'catalog/noimage.png';
			}
			$category_name = (string)$c->Наименование;
			$category_origin_id = (string)$c->id;
			//$category_prefix = (string)$c->prefix;
			$category_prefix = '';

			//Ищем категорию на сайте по id
			$search = array_search($category_origin_id, array_column($all_site_categoryes_t, 'origin_id'));
			if($search!==false){
				$all_site_categoryes[$category_origin_id] = $all_site_categoryes_t[$search];
				
				//Проверяем есть ли изменения в инфе по категориям (если есть, то обновляем)
				if(($category_name != $all_site_categoryes_t[$search]['name']) || ((string)$c->images->image != $all_site_categoryes_t[$search]['image'])){
					
					$parent_id = $all_site_categoryes_t[$search]['parent_id'];
					$new_cat_id = $this->model_user_price_upload_vendor->editCategory(
						$all_site_categoryes_t[$search]['category_id'],
						array( 
							//'category_id' => $all_site_categoryes_t[$search]['category_id'],
							'parent_id' => $parent_id,
							'category_seo_url'  => $category_prefix.'_'.$category_origin_id,
							'top' => 0,
							'sort_order' => 0,
							'column' => 0,
							'sort' => 0,
							'image' => $category_image,
							'status' => 1,
							'category_store' => array(0),
							'category_description' => array($config_language_id => array('name'=>$category_name,
																		'meta_title'=>$category_name,
																		'meta_h1'=>$category_name,
																		'description'=>$category_name,
																		'meta_description'=>'',
																		'meta_keyword'=>'',
																	),
														),
							//'category_seo_url' => array(), 
						)
					);
				}
				
				
			}	
			else {//Категория не найдена -> Добавляем 
				
				$parent_id = 0;
				if($c->parent_id)
					if(isset($all_site_categoryes[(string)$c->parent_id])){
						$parent_id = $all_site_categoryes[(string)$c->parent_id]['category_id'];
					}else {
						// echo '<br>NoParent!!!!<br>'; 
						// continue;
						$parent_id = 0;
					}
				$image = (string)$c->images->image;
				if($image) $image = 'catalog/products/'.$image;
				else  {
					$image = 'catalog/noimage.png';
					
				}

				echo $c->id.'__add__'.$category_name.'__'.$parent_id.'<br>';

				$new_cat_id = $this->model_user_price_upload_vendor->addCategory(
					array( 
					'parent_id' => $parent_id,
					'origin_id' => $category_origin_id,
					'category_seo_url'  => $category_prefix.'_'.$category_origin_id,
					'top' => 0,
					'img' => (string)$c->images->image,
					'sort_order' => 0,
					'column' => 0,
					'sort' => 0,
					'image' => $image,
					'category_store' => array(0),
					'status' => 1,
					'category_description' => array($config_language_id => array('name'=>$category_name,
																'meta_title'=>$category_name,
																'meta_h1'=>$category_name,
																'description'=>$category_name,
																'meta_description'=>'',
																'meta_keyword'=>'',
															),
					),
					));
				$all_site_categoryes[$category_origin_id] = array(
					'name'=> $category_name,
					'parent_id' => $parent_id,
					'category_id' => $new_cat_id,
				);
			}
		}

		//Обрабатываем ТОВАРЫ
		$ii =0;
		$params = [];
		foreach ($xml->catalog->goods->item as $i) {
			$ii++;			
			$params = $filters =[];
			$name = (string)$i->name;
			
			$novinka= 0;
			$xit= 0;
			$priviewText = (string)$i->description;
			$datailText =  (string)$i->description;
			//$location =  (string)$i->country;
			$artikul =  (string)$i->articul;
			$m_code =  (string)$i->m_code;
			//$Weight =  (string)$i->Weight;
			//$artikul =  (string)$i->id;
			$category =  (string)$i->category;
			$price =  (string)$i->price->price_value;
			$quantity =  (string)$i->rest->rest_value;			
		//	if(!$price) $price = 0;
			//$quantity =  (string)$i->total_quantity;
			$image = (string)$i->images->image;
			$n_group = (string)$i->n_group;
			$manufacturer = (string)$i->manufacturer;
			//$catalog_quantity = (string)$i->catalog_quantity;
			$description = (string)$i->description;
			$country = (string)$i->country;
			$novinka = (boolean)$i->new;
			$action = (boolean)$i->action;
			$Weight = (string)$i->Weight;
			$params = [];
			//if($catalog_quantity) $params[12]= array('attrbute_id'=> 12, 'name' => 'Количество по каталогу',  'value' => $catalog_quantity);
			//if($description) $params[14]= array('attrbute_id'=> 14, 'name' => 'Описание',  'value' => $description);
			
			//Проходим по свойствам товара
			foreach ($i->properties->property as $p) {
				$p_name = (string)$p->name;
				$p_value = (string)$p->value;
				$p_name = str_replace(
					[
						'Производитель аксессуаров 1', 
						'Производитель аксессуаров',
						'Доп особенность 1'
					],
					[
						"Производитель",
						"Производитель",
						"Особенность",
					], $p_name);
					
					//print_r($all_site_attributes);
					$search = array_search($p_name, array_column($all_site_attributes, 'name'));
					//Если названия характеристики нет в базе, заносим её
					if($search === false){
						$new_prop_id = $th_mod->addAttribute([	
							'attribute_group_id' => 8,
							'sort_order' => 0,
							'attribute_description' => [
								$config_language_id => [
									'name' => $p_name
								]
							],
							'attribute_group_id' => 8,
						]);
						if($new_prop_id) $all_site_attributes[] = ['attribute_id' => $new_prop_id, 'name' => $p_name];

					}else{

						$attribute_id = $all_site_attributes[$search]['attribute_id'];
						$params[$attribute_id]= array('attrbute_id'=> $attribute_id, 'name' => $p_name,  'value' => $p_value);
					}

					
					
			}
	
			//Страна производитель фиксированна
			if($country) $params[16]= array('attrbute_id'=> 16, 'name' => 'Страна производитель',  'value' => $country);

			//if($image) $image = 'catalog/products/'.$image;
			//else 
			{
					//!!! ВРЕМЕННО делаем рафндомные фотки товаров !!!
					$rand = rand ( 1 , 5 );
					if($rand == 5 ) $image = 'catalog/1.png';
						else $image = "catalog/{$rand}.png";
			}	
			$active = $quantity ? 'Y' : 'N';
		//	$active = 'Y';
		//	$active = $price ? 'Y' : 'N';
			$p = [
				'artikul' => $artikul,
				'model' => $artikul,
				'm_code' => $m_code,
				'mpn' => $n_group,
				'novinka' => $novinka,
				'xit' => $xit,
				'action' => $action,
				'manufacturer' => $manufacturer,
				'active' => $active,
				'name' => trim($name),
				'category' => $category,
				'image' => $image,
				'quantity' => $quantity,
				'price' => $price,
				'params' => $params,
				'priviewText' => $priviewText,
				'detailText' => $datailText,
				'Weight' => $Weight,				
			];
//print_r($p);
			if(!$p['name']) continue;

			//составляем цепочку категорий
				if(!empty($all_site_categoryes[$p['category']])){
					$cat=[];
					$_cat = $all_site_categoryes[$p['category']]; 
//print_R($_cat);
					while($_cat){

//echo $_cat['category_id'].'<br>';

						$cat[]= $_cat['category_id'];
						$search = array_search($_cat['parent_id'], array_column($all_site_categoryes_t, 'category_id'));
						$_cat = ($search!==false) ? $all_site_categoryes_t[$search] : false ; 
						
					}
				}else 
					$cat = [0];
// echo '<pre>';
// print_r($cat);
// echo '</pre>';		
// print_r($_cat);
// exit;
				$search_manufacturer = array_search($p['manufacturer'], array_column($all_site_manufacturers, '1c_id'));
				$manufacturer_site_id = ($search_manufacturer!==false ) ? $all_site_manufacturers[$search_manufacturer]['manufacturer_id'] : 0;

				$search_product = array_search($p['artikul'], array_column($all_products_t, 'sku'));




					if($search_product!==false ){
					
						echo $ii.'__m='.$manufacturer_site_id.'____'.$p['name'].'_____';
						echo '_cat__'.$p['category'].'/'.$cat[0].'<br>';
						
						$product_id = $all_products_t[$search_product]['product_id'];
						$active = ($p['active'] == 'Y') ? 1 : 0;
					 	$this->model_user_price_upload_vendor->editProduct_Import($product_id, array(
							'model' => $p['model'],
							'product_category' => $cat,
							'sku' => $p['artikul'], 
							'upc' => $p['m_code'], 
							'price' => $p['price'], 
							'manufacturer_id' => $manufacturer_site_id, 
							'novinka' => $novinka,
							'xit' => $xit,
							'mpn' => $n_group,
							'ean' =>'',
							'jan' => '',
							//'category' => $p['category'][0],
							'isbn' => '',
							'location' => '',
							'quantity' =>  $p['quantity'],
							'minimum' => 1,
							'subtract' => 1,
							'stock_status_id' => $p['quantity'] ? 7 : 5,  //Присутствие на складе  7 - есть на складе, 5 нет в наличии, 8-предзаказ
							'date_available' => date("Y.m.d"),
							'shipping' => 1,
							'product_special' => 0,
							'points' => 0,
							'weight' => 0,
							'weight_class_id' => 1,
							'length' => 0,
							'width' => 0,
							'height' => 0,
							'length_class_id' => 1,
							'status' => $active,
							'product_store' => array(0),
							'tax_class_id' => 9,
							'sort_order' => $n_group,
							'image' => $p['image'],

							//'images' => array('catalog/products'.$p['image_dop']),
							//'product_category' => $p['category'],
							'product_description' => array('1'=>array(
															'name' => $p['name'],
															'description' => $p['detailText'],
															'keyword' => '',
															'tag' => '',
															'meta_title' => $p['name'],
															'meta_description' => $p['priviewText'],
															'meta_keyword' => '',
														)),
							// 'product_seo_url' => [
							// 	0 => [
							// 		$config_language_id => $this->getTranslit($p['name']),
							// 	 ]
							// ],			

						)); 
						if(count($p['params']))
							$this->model_user_price_upload_vendor->editProduct_Attribute($product_id, $p['params']);

					}else{
						echo $p['artikul'].'__Add<br>';
						
						$active = ($p['active'] == 'Y') ? 1 : 0;
						$product_id = $this->model_user_price_upload_vendor->addProduct_Import(array(
							'model' => $p['artikul'],
							'sku' => $p['artikul'],
							'category' => $cat,
							'product_category' => $cat,
							//'icon' => $p['icon'], 
							'upc' => $p['m_code'], 
							'novinka' => $novinka,
							'manufacturer_id' => $manufacturer_site_id, 
							'xit' => $xit,
							'ean' =>'',
							'jan' => '',
							'keyword' => $this->getTranslit($p['name']),
							'mpn' => $n_group,
							'isbn' => '',
							'location' => '',
							'quantity' =>  $p['quantity'],
							'minimum' => 1,
							'subtract' => 1,
							'stock_status_id' => $p['quantity'] ? 7 : 5,  //Присутствие на складе  7 - есть на складе, 5 нет в наличии, 8-предзаказ
							'date_available' => date("Y.m.d"),
							'shipping' => 1,
							'price' => $p['price'], 
							'product_special' => 0,
							'points' => 0,
							'weight' => 0,
							'weight_class_id' => 1,
							'length' => 0,
							'width' => 0,
							'height' => 0,
							'length_class_id' => 1,
							'status' => $active,
							'product_store' => array(0),
							'tax_class_id' => 9,
							'sort_order' => $n_group,
							'image' => $p['image'],
							//'images' => array('catalog/products'.$p['image_dop']),
							//'product_category' => $p['category'],
							'product_description' => array('2'=>array(
															'name' => $p['name'],
															'description' => $p['detailText'],
															'keyword' => '',
															'tag' => '',
															'meta_title' => $p['name'],
															'meta_description' => $p['priviewText'],
															'meta_keyword' => '',
														)),
							'product_seo_url' => [
								0 => [
									$config_language_id => $this->getTranslit($p['name']),
									]
							],							


						));  

						if(count($p['params']))
							$this->model_user_price_upload_vendor->editProduct_Attribute($product_id, $p['params']);												

					}

					if($ii > 12222)
						{		
							
							echo '<pre>';
							print_r($p);
							echo '</pre>';
							exit;
						}
					
		// print_r($p);
		// exit;
		if($ii > 100500){

 exit;
		}
		}


		//Информируем	
		//mail("gorely.aleksei@yandex.ru", "cron import_price", "import_price\n");


		echo '<br>end price';
		exit;

		//	$this->response->setOutput(json_encode($json));
		//	$this->response->redirect($this->url->link('user_price/upload/asabella', 'token=' . $this->session->data['token'], 'SSL'));

}

public function import_products_aks() {

	echo '<meta charset="utf-8">';
	
	$this->load->model('user_price/upload_vendor');
	$all_products = $all_customer_groups = [];
	//$all_products_t = $this->model_user_price_upload_vendor->getSiteProducts();


	include_once('simple_html_dom.php');
	$xml = simplexml_load_file('http://alexgo3j.bget.ru/upload/goods_2.xml');
	$importFile = $_SERVER['DOCUMENT_ROOT'].'/upload/goods_2.xml';
	$fileDate = strtotime(date("YmdHis",filemtime($importFile)));
	
	$all_products = $all_customer_groups = [];
	$all_products_t = $this->model_user_price_upload_vendor->getSiteProducts();
	$all_site_categoryes_t = $this->model_user_price_upload_vendor->getSiteCategoryes();
	$all_site_manufacturers = $this->model_user_price_upload_vendor->getManufacturers();
	$all_site_Attributes = $this->model_user_price_upload_vendor->getSiteAttributes();
	$all_site_Attributes_t = [];
	$all_site_categoryes = [];

	
	$products = [];
	$config_language_id = (int)$this->config->get('config_language_id');
	foreach ($xml->categories->category as $c) {
		if(!empty($c->images->image)){
			if(file_exists($_SERVER['DOCUMENT_ROOT'].'/image/catalog/products/'.(string)$c->images->image))
				$category_image = 'catalog/products/'.(string)$c->images->image;
			else $category_image = 'catalog/noimage.png';
			//$category_image = 'catalog/noimage.png';
		}else{
			$category_image = 'catalog/noimage.png';
		}
		$category_name = (string)$c->name;
		$p_id = (string)$c->parent_id;
		$category_origin_id = (string)$c->id;
		$category_prefix = (string)$c->prefix;
		$category_origin_id = $category_prefix.'_'.$category_origin_id;
		$parent_code = $category_prefix.'_'.$p_id;

		//Ищем категорию на сайте по id
		$search = array_search($category_origin_id, array_column($all_site_categoryes_t, 'origin_id'));
		if($search!==false){
			
			$all_site_categoryes[$category_origin_id] = $all_site_categoryes_t[$search];

echo 'cat_edit___'.$category_origin_id.'___'.(string)$c->name.'__'.(string)$c->id.'_____'.$all_site_categoryes_t[$search]['name'].$all_site_categoryes_t[$search]['category_id'].'<br>';			

			//Проверяем есть ли изменения в инфе по категориям (если есть, то обновляем)
			if(($category_name != $all_site_categoryes_t[$search]['name']) || ((string)$c->images->image != $all_site_categoryes_t[$search]['image'])){
				
				$parent_id = $all_site_categoryes_t[$search]['parent_id'];
				$new_cat_id = $this->model_user_price_upload_vendor->editCategory(
					$all_site_categoryes_t[$search]['category_id'],
					array( 
						//'category_id' => $all_site_categoryes_t[$search]['category_id'],
						'parent_id' => $parent_id,
						'category_seo_url'  => preg_replace("/[0]{2,}/", "", $category_origin_id),
						'top' => 0,
						'sort_order' => 0,
						'column' => 0,
						'sort' => 0,
						'image' => $category_image,
						'status' => 1,
						'category_store' => array(0),
						'category_description' => array($config_language_id => array('name'=>$category_name,
																	'meta_title'=>$category_name,
																	'meta_h1'=>$category_name,
																	'description'=>$category_name,
																	'meta_description'=>'',
																	'meta_keyword'=>'',
																),
													),
						//'category_seo_url' => array(), 
					)
				);
			}
			
			
		}	
		else {//Категория не найдена -> Добавляем 

echo 'cat_add___'.$category_origin_id.'___'.(string)$c->name.'__'.(string)$c->id.'_____'.$all_site_categoryes_t[$search]['name'].$all_site_categoryes_t[$search]['category_id'].'<br>';			
			
			//id Категории запчасти
			$parent_id = 20738;
			if($c->parent_id)
				if(isset($all_site_categoryes[$parent_code])){
					$parent_id = $all_site_categoryes[$parent_code]['category_id'];
				}else {
					// echo '<br>NoParent!!!!<br>'; 
					// continue;
					
					//$parent_id = 20738;
				}

				echo $parent_id.'<br>';;
			$image = (string)$c->images->image;
			if($image) $image = 'catalog/products/'.$image;
			else  $image = 'catalog/noimage.png';
			//echo $c->id.'____'.$parent_id.'<br>';	
			$new_cat_id = $this->model_user_price_upload_vendor->addCategory(
				array( 
				'parent_id' => $parent_id,
				'origin_id' => $category_origin_id,
				'category_seo_url'  => preg_replace("/[0]{2,}/", "", $category_origin_id),
				'top' => 0,
				'img' => (string)$c->images->image,
				'sort_order' => 0,
				'column' => 0,
				'sort' => 0,
				'image' => $image,
				'category_store' => array(0),
				'status' => 1,
				'category_description' => array($config_language_id => array('name'=>$category_name,
															'meta_title'=>$category_name,
															'meta_h1'=>$category_name,
															'description'=>$category_name,
															'meta_description'=>'',
															'meta_keyword'=>'',
														),
				),
				));
			$all_site_categoryes[$category_origin_id] = array(
				'name'=> $category_name,
				'parent_id' => $parent_id,
				'category_id' => $new_cat_id,
			);
		}
	}

	//Обрабатывает свойства
	foreach ($xml->properties->property as $p) {
		$propery_name = (string)$p->name;
		$propery_id = (string)$p->id;

		$search_attr = array_search($propery_name, array_column($all_site_Attributes, 'name'));
		if($search_attr===false){
			$attr_id = $this->model_user_price_upload_vendor->addAttribute(
				[
					'sort_order' => 0,
					'attribute_group_id' => 7,
					'attribute_description'=>[
						'1' => [
							'name' => $propery_name,
						]
					],
				]
			);
			$all_site_Attributes[] = [ 'attribute_id' => $attr_id, 'name' => $propery_name];
			$all_site_Attributes_t[$propery_id] = [ 'attribute_id' => $attr_id, 'name' => $propery_name];
		}else{
			$all_site_Attributes_t[$propery_id] = $all_site_Attributes[$search_attr];
		}
	}

print_r($all_site_Attributes_t);
exit;

$ii =0;
$params = [];
	foreach ($xml->goods->item as $i) {
		$ii++;			
		$params = $filters =[];
		$name = (string)$i->name;
		
		$novinka= 0;
		$xit= 0;
		$priviewText = (string)$i->description;
		$datailText =  (string)$i->description;
		//$location =  (string)$i->country;
		$artikul =  (string)$i->articul;
		$m_code =  (string)$i->m_code;
		//$Weight =  (string)$i->Weight;
		//$artikul =  (string)$i->id;
		$category =  'ca_'.(string)$i->category;
		$price =  (string)$i->price;
	//	if(!$price) $price = 0;
		$quantity =  (string)$i->total_quantity;
		$image = (string)$i->images->image;
		$n_group = (string)$i->n_group;
		$manufacturer = (string)$i->manufacturer;
		$catalog_quantity = (string)$i->catalog_quantity;
		$description = (string)$i->description;
		$country = (string)$i->country;

		if(!empty($i->properties)){
			foreach ($i->properties->property as $p) {
				$p_id = (string)$p->id;
				if(isset($all_site_Attributes_t[$p_id]))
					$params[$all_site_Attributes_t[$p_id]['attribute_id']] = array(
						'attrbute_id'=> $all_site_Attributes_t[$p_id]['attribute_id'], 
						'name' => $all_site_Attributes_t[$p_id]['name'],  
						'value' => $p
					);
			}
		}	
		
		if($catalog_quantity) $params[12]= array('attrbute_id'=> 12, 'name' => 'Количество по каталогу',  'value' => $catalog_quantity);
		if($description) $params[14]= array('attrbute_id'=> 14, 'name' => 'Описание',  'value' => $description);
		if($country) $params[13]= array('attrbute_id'=> 13, 'name' => 'Страна',  'value' => $country);
		

		if($image) $image = 'catalog/products/'.$image;
		else  $image = 'catalog/noimage.png';
	//	$active = $quantity ? 'Y' : 'N';
	//	$active = 'Y';
		$active = $price ? 'Y' : 'N';
		$p = [
			'artikul' => $artikul,
			'model' => $artikul,
			'm_code' => $m_code,
			'mpn' => $n_group,
			'novinka' => $novinka,
			'xit' => $xit,
			'manufacturer' => $manufacturer,
			'active' => $active,
			'name' => trim($name),
			'category' => $category,
			'image' => $image,
			'quantity' => $quantity,
			'price' => $price,
			'params' => $params,
			'priviewText' => $priviewText,
			'detailText' => $datailText,
		];

		if(!$p['name']) continue;

			if(!empty($all_site_categoryes[$p['category']])) 
				$cat = [$all_site_categoryes[$p['category']]['category_id']];
			else $cat = [0];
			$search_manufacturer = array_search($p['manufacturer'], array_column($all_site_manufacturers, '1c_id'));
			$manufacturer_site_id = ($search_manufacturer!==false ) ? $all_site_manufacturers[$search_manufacturer]['manufacturer_id'] : 0;

			$search_product = array_search($p['artikul'], array_column($all_products_t, 'sku'));
			
				if($search_product!==false ){
				
					echo $ii.'__m='.$manufacturer_site_id.'____'.$p['name'].'_____';
					echo '_cat__'.$p['category'].'/'.$cat[0].'<br>';
					$product_id = $all_products_t[$search_product]['product_id'];
					$active = ($p['active'] == 'Y') ? 1 : 0;
					 $this->model_user_price_upload_vendor->editProduct_Import($product_id, array(
						'model' => $p['model'],
						'product_category' => $cat,
						'sku' => $p['artikul'], 
						'upc' => $p['m_code'], 
						'price' => $p['price'], 
						'manufacturer_id' => $manufacturer_site_id, 
						'novinka' => $novinka,
						'xit' => $xit,
						'mpn' => $n_group,
						'ean' =>'',
						'jan' => '',
						//'category' => $p['category'][0],
						'isbn' => '',
						'location' => '',
						'quantity' =>  $p['quantity'],
						'minimum' => 1,
						'subtract' => 1,
						'stock_status_id' => $p['quantity'] ? 7 : 5,  //Присутствие на складе  7 - есть на складе, 5 нет в наличии, 8-предзаказ
						'date_available' => date("Y.m.d"),
						'shipping' => 1,
						'product_special' => 0,
						'points' => 0,
						'weight' => 0,
						'weight_class_id' => 1,
						'length' => 0,
						'width' => 0,
						'height' => 0,
						'length_class_id' => 1,
						'status' => $active,
						'product_store' => array(0),
						'tax_class_id' => 9,
						'sort_order' => $n_group,
						'image' => $p['image'],

						//'images' => array('catalog/products'.$p['image_dop']),
						//'product_category' => $p['category'],
						'product_description' => array('1'=>array(
														'name' => $p['name'],
														'description' => $p['detailText'],
														'keyword' => '',
														'tag' => '',
														'meta_title' => $p['name'],
														'meta_description' => $p['priviewText'],
														'meta_keyword' => '',
													)),
						//'product_seo_url' => array(),			

					)); 
					if(count($p['params']))
						$this->model_user_price_upload_vendor->editProduct_Attribute($product_id, $p['params']);

				}else{
					echo $p['artikul'].'__Add<br>';
					
					$active = ($p['active'] == 'Y') ? 1 : 0;
					$product_id = $this->model_user_price_upload_vendor->addProduct_Import(array(
						'model' => $p['artikul'],
						'sku' => $p['artikul'],
						'category' => $cat,
						'product_category' => $cat,
						//'icon' => $p['icon'], 
						'upc' => $p['m_code'], 
						'novinka' => $novinka,
						'manufacturer_id' => $manufacturer_site_id, 
						'xit' => $xit,
						'ean' =>'',
						'jan' => '',
						'keyword' => $this->getTranslit($p['name']),
						'mpn' => $n_group,
						'isbn' => '',
						'location' => '',
						'quantity' =>  $p['quantity'],
						'minimum' => 1,
						'subtract' => 1,
						'stock_status_id' => $p['quantity'] ? 7 : 5,  //Присутствие на складе  7 - есть на складе, 5 нет в наличии, 8-предзаказ
						'date_available' => date("Y.m.d"),
						'shipping' => 1,
						'price' => $p['price'], 
						'product_special' => 0,
						'points' => 0,
						'weight' => 0,
						'weight_class_id' => 1,
						'length' => 0,
						'width' => 0,
						'height' => 0,
						'length_class_id' => 1,
						'status' => $active,
						'product_store' => array(0),
						'tax_class_id' => 9,
						'sort_order' => $n_group,
						'image' => $p['image'],
						//'images' => array('catalog/products'.$p['image_dop']),
						//'product_category' => $p['category'],
						'product_description' => array('2'=>array(
														'name' => $p['name'],
														'description' => $p['detailText'],
														'keyword' => '',
														'tag' => '',
														'meta_title' => $p['name'],
														'meta_description' => $p['priviewText'],
														'meta_keyword' => '',
													)),
						//'product_seo_url' => array(),							


					));  

					if(count($p['params']))
						$this->model_user_price_upload_vendor->editProduct_Attribute($product_id, $p['params']);												

				}
			
// print_r($p);
// exit;
if($ii > 100500) exit;
}


//Информируем	
//mail("gorely.aleksei@yandex.ru", "cron import_price", "import_price\n");


echo '<br>end price';
exit;

//	$this->response->setOutput(json_encode($json));
//	$this->response->redirect($this->url->link('user_price/upload/asabella', 'token=' . $this->session->data['token'], 'SSL'));

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

	public function upload222() {
		$this->load->model('user_price/modified_price');
		$a = false;
		if(isset($_FILES['picewp']) && $_FILES['picewp']['error'] == 0){ // Проверяем, загрузил ли пользователь файл
			$destination_dir =  $_SERVER['DOCUMENT_ROOT'].'/upload/user-price-wp.xls'; // Директория для размещения файла
		 	move_uploaded_file($_FILES['picewp']['tmp_name'], $destination_dir ); // Перемещаем файл в желаемую директорию
			$a = 'Файл успешно загружен';$this->session->data['success_upload'] = $a;
			//echo '_File Uploaded'; // Оповещаем пользователя об успешной загрузке файла
			$this->model_user_price_modified_price->updateprice($_SERVER['DOCUMENT_ROOT'].'/upload/user-price-wp.xls');
		}else
		if(isset($_FILES['picep']) && $_FILES['picep']['error'] == 0){ // Проверяем, загрузил ли пользователь файл
			$destination_dir =  $_SERVER['DOCUMENT_ROOT'].'/upload/user-price-p.xls'; // Директория для размещения файла
			move_uploaded_file($_FILES['picep']['tmp_name'], $destination_dir ); // Перемещаем файл в желаемую директорию
			 $a = 'Файл успешно загружен';$this->session->data['success_upload'] = $a;
			 $this->model_user_price_modified_price->updateprice($_SERVER['DOCUMENT_ROOT'].'/upload/user-price-p.xls');
			//echo '_File Uploaded'; // Оповещаем пользователя об успешной загрузке файла

		}
		else {
			$this->session->data['error'] = 'Произошла ошибка';
		}


			$this->response->redirect($this->url->link('user_price/upload', 'token=' . $this->session->data['token'], 'SSL'));
	}

	public function updateprice() {
		$this->load->model('user_price/modified_price');
		$this->model_user_price_modified_price->updateprice('12345');
	}

	public function updatecategorytocategory() {
		$this->load->model('user_price/upload_vendor');

		$data = array(
			'import_cat_id' => $this->request->post['import_cat_id'],
			'site_cat_id' => $this->request->post['site_cat_id'],

		);

		//print_r($_POST);
		$this->model_user_price_upload_vendor->category_to_category($data);

		//$json['filter_data'] = $filter_data;
		$json['success'] = 1;
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function updateattributetoattribute() {
		$this->load->model('user_price/upload_vendor');

		$data = array(
			'import_attribute' => $this->request->post['import_attribute'],
			'site_attribute_id' => $this->request->post['site_attribute_id'],
			'vendor' => $this->request->post['vendor'],

		);

		//print_r($_POST);
		$this->model_user_price_upload_vendor->attribute_to_attribute($data);

		//$json['filter_data'] = $filter_data;
		$json['success'] = 1;
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function updateattributetofilter() {
		$this->load->model('user_price/upload_vendor');

		$data = array(
			'import_attribute' => $this->request->post['import_attribute'],
			'site_filter_group' => $this->request->post['site_filter_group'],
			'vendor' => $this->request->post['vendor'],

		);

		//print_r($_POST);
		$this->model_user_price_upload_vendor->attribute_to_filter($data);

		//$json['filter_data'] = $filter_data;
		$json['success'] = 1;
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function get_category_products() {
		$this->load->model('user_price/upload_vendor');
		$cat_id = $this->request->post['cat_id'];
		$vendor = $this->request->post['vendor'];


		//print_r($_POST);
		$rez = $this->model_user_price_upload_vendor->getCategoryProducts($cat_id, $vendor);

		//$json['filter_data'] = $filter_data;
		$json['success'] = 1;
		$json['rez'] = $rez;
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
}
