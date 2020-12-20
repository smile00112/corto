<?php
class ModelUserPriceUploadVendor extends Model {

	public function addAttribute($data) {
		$this->db->query("INSERT INTO " . DB_PREFIX . "attribute SET attribute_group_id = '" . (int)$data['attribute_group_id'] . "', sort_order = '" . (int)$data['sort_order'] . "'");

		$attribute_id = $this->db->getLastId();

		foreach ($data['attribute_description'] as $language_id => $value) {
			$this->db->query("INSERT INTO " . DB_PREFIX . "attribute_description SET attribute_id = '" . (int)$attribute_id . "', language_id = '" . (int)$language_id . "', name = '" . $this->db->escape($value['name']) . "'");
		}

		return $attribute_id;
	}
	
	public function addVendorProduct($data) {
		$this->db->query("
		INSERT INTO import_products SET
			name = '" . $this->db->escape($data['name']) . "',
			offer_id = '" . $this->db->escape($data['offer_id']) . "',
			artikul = '" . $this->db->escape($data['artikul']) . "',
			description = '" . $this->db->escape($data['description']) . "',
			category = '" . $this->db->escape($data['categoryId']) . "',
			image = '" . $this->db->escape($data['image']) . "',
			images = '" . $this->db->escape($data['images']) . "',
			vendor = '" . $this->db->escape($data['vendor']) . "',
			quantity = '" . $this->db->escape($data['quantity']) . "',
			price = '" . $this->db->escape($data['price']) . "',
			manufacturier = '" . $this->db->escape($data['manufacturier']) . "',
			model = '" . $this->db->escape($data['model']) . "'

		");

		$product_id = $this->db->getLastId();

		//$this->cache->delete('product');
		return $product_id;
	}

	public function updateVendorProduct($data) {
		if(!$data['id']) return false;

		$this->db->query("
		UPDATE import_products SET
			name = '" . $this->db->escape($data['name']) . "',
			offer_id = '" . $this->db->escape($data['offer_id']) . "',
			artikul = '" . $this->db->escape($data['artikul']) . "',
			description = '" . $this->db->escape($data['description']) . "',
			category = '" . $this->db->escape($data['categoryId']) . "',
			image = '" . $this->db->escape($data['image']) . "',
			images = '" . $this->db->escape($data['images']) . "',
			vendor = '" . $this->db->escape($data['vendor']) . "',
			quantity = '" . $this->db->escape($data['quantity']) . "',
			price = '" . $this->db->escape($data['price']) . "',
			manufacturier = '" . $this->db->escape($data['manufacturier']) . "',
			model = '" . $this->db->escape($data['model']) . "'

		WHERE id = ".$data['id']."
		");

		//$this->cache->delete('product');
		return true;
	}

	public function getVendorProductsCategoryes($vendor) {
		if(!$vendor) return false;

		$query = $this->db->query("
		SELECT category,  COUNT(category) as p_count  from import_products where vendor = '".$vendor."' GROUP BY category
		");

		return $query->rows;
	}

	public function updateVendorProductPrice($data) {
		if(!$data['id']) return false;
		if(empty($data['spesial'])) $spesial = 0;
		else $spesial = $data['spesial'];

			$this->db->query("
				UPDATE import_products SET
					quantity = '" . $this->db->escape($data['quantity']) . "',
					price = '" . $this->db->escape($data['price']) . "',
					spesial = '" . $this->db->escape($spesial) . "'

					WHERE id = ".$data['id']."
			");

		//$this->cache->delete('product');
		return true;
	}

	public function vendorProductCharactersClear($vendor) {
		$this->db->query("
			DELETE FROM import_products_character WHERE vendor = '".$vendor."'
		");

		return true;
	}
	
	public function clearProduct_Group_prices($product_id) {
	//	echo '___clearr__'.$product_id.'<br>';
		if(!empty($product_id))
			$this->db->query("
				DELETE FROM oc_product_discount WHERE product_id = '".$product_id."'
			");
			$this->db->query("
				DELETE FROM oc_product_special WHERE product_id = '".$product_id."'
			");
		return true;
	}
	
	public function addProduct_Group_price($product_id, $price, $price_special, $quantity, $group_id) {
		if(empty($product_id) || empty($group_id)) return false;
		
			if(!empty($price)){
				/*echo "
					INSERT INTO oc_product_special VALUES(NULL, '".$product_id."', '".$group_id."', '1', '1', '".$price."', '0000-00-00', '0000-00-00')
				<br>";
				*/
				$this->db->query("
					INSERT INTO oc_product_discount VALUES(NULL, '".$product_id."', '".$group_id."', '1', '1', '".$price."', '0000-00-00', '0000-00-00')
				");
				
				if($price_special)
					$this->db->query("
						INSERT INTO oc_product_special VALUES(NULL, '".$product_id."', '".$group_id."', '1', '".$price_special."', '0000-00-00', '0000-00-00')
					");
			}	

		return true;
	}	
	
	
	public function getVendorProducts($vendor) {
		$query = $this->db->query("SELECT c.* FROM import_products c
		WHERE c.vendor = '" . $this->db->escape($vendor) . "'");

		return $query->rows;
	}
	public function getVendorProducts_onSite($vendor) {
		$query = $this->db->query("
			SELECT p.product_id, p.sku, p.isbn as vendor, p.mpn
			FROM " . DB_PREFIX . "product p
			WHERE p.isbn = '" . $this->db->escape($vendor) . "'
		");

		return $query->rows;
	}
	public function getSiteProducts() {
		$query = $this->db->query("
			SELECT p.product_id, p.sku, p.isbn as vendor, p.mpn, (SELECT pd.name FROM  " . DB_PREFIX . "product_description pd WHERE pd.product_id = p.product_id AND language_id = '" . (int)$this->config->get('config_language_id') . "') AS name
			FROM " . DB_PREFIX . "product p
		");

		return $query->rows;
	}
	
	public function getSiteCustomerGroups() {
		$query = $this->db->query("
			SELECT g.*	FROM " . DB_PREFIX . "customer_group_description g
		");

		return $query->rows;
	}
	
	public function getCategoryProductsOnSite($data) {
		$query = $this->db->query("
			SELECT p.product_id, p.sku, p.isbn, p.mpn as vendor, ptn.category_id  FROM " . DB_PREFIX . "product p
			JOIN " . DB_PREFIX . "product_to_category ptn ON p.product_id = ptn.product_id
			WHERE category_id = '" . $this->db->escape($data['category']) . "'"
		);

		return $query->rows;
	}
	public function getCategoryProducts($cat_id, $vendor) {
		$query = $this->db->query("
			SELECT * FROM import_products p
			WHERE category = '" . $this->db->escape($cat_id) . "' AND vendor = '" . $this->db->escape($vendor) . "'"
		);

		return $query->rows;
	}
	public function disableVendorProducts_onSite($vendor) {
		$query = $this->db->query("
			UPDATE " . DB_PREFIX . "product p
			SET status = '0' WHERE isbn = '".$vendor."'
			"
		);

		return false;
	}
	public function disableSiteProducts() {
		$query = $this->db->query("
			UPDATE " . DB_PREFIX . "product p
			SET status = '0'
			"
		);

		return false;
	}
	public function addImagesToPoduct_onSite($product_id = 0, $images='', $ypakovka='', $vendor) {
		if($product_id){
			//echo $product_id.'<br>';
			$query = $this->db->query("
						UPDATE  import_products SET
							images = '$images'
							WHERE id = $product_id
						"
					);
			if($ypakovka){
				$query = $this->db->query("
						DELETE FROM import_products_character
							WHERE product_id = $product_id AND  param_name = 'Упаковка'
						"
					);

				$query = $this->db->query("
						INSERT INTO import_products_character
						VALUES ( NULL, 'Упаковка', '$ypakovka', $product_id, '$vendor');
						"
					);
			}
		}

		return false;
	}
	public function getCategoryProductsAttributes($data) {
		if(!$data['vendor']) return false;
		$query = $this->db->query("
			SELECT pa.*, ata.site_attribute
			FROM  import_products_character pa
			JOIN  import_products ip ON ip.id = pa.product_id
			JOIN import_attribute_to_attribute ata ON ata.import_attribute = pa.param_name AND ata.vendor = '".$data['vendor']."'
			WHERE  ip.category = " . $this->db->escape($data['category']) . "
			ORDER BY product_id
		"
		);

		return $query->rows;
	}

	public function getCategoryProductsFilters($data) {
		$query = $this->db->query("
			SELECT pa.*, atf.site_filter_group
			FROM  import_products_character pa
			JOIN  import_products ip ON ip.id = pa.product_id
			JOIN import_attribute_to_filter atf ON atf.import_attribute = pa.param_name AND atf.vendor = '".$data['vendor']."'
			WHERE  ip.category = " . $this->db->escape($data['category']) . "
			ORDER BY product_id
		"
		);

		return $query->rows;
	}

	public function editProduct_Attribute($product_id, $attributes=array()) {

		$added = array();
		//Удаляем атрибуты товара
		$query = $this->db->query("DELETE FROM " . DB_PREFIX . "product_attribute WHERE product_id = '".$product_id."'");
		//echo "DELETE FROM " . DB_PREFIX . "product_attribute WHERE product_id = '".$product_id."'<br>";
		if(is_array($attributes))
		foreach($attributes as $attribute){ 
			if($attribute['value']){
				//Удаляем атрибут у товара
				//$query = $this->db->query("DELETE FROM " . DB_PREFIX . "product_attribute WHERE product_id = '".$product_id."' AND attribute_id = '".		$attribute['attrbute_id']."'");

				$query = $this->db->query("
					INSERT INTO " . DB_PREFIX . "product_attribute
					VALUES('".$product_id."', '".$attribute['attrbute_id']."', '1', '".$attribute['value']."')

				"
				);
			}
		}
	}

	public function editProduct_Filters($product_id, $import_product_id, $data = array()) {
		//Удаляем Фильтры товара
		$query = $this->db->query("DELETE FROM " . DB_PREFIX . "product_filter WHERE product_id = '".$product_id."'");
		//echo "DELETE FROM " . DB_PREFIX . "product_filter WHERE product_id = '".$product_id."'<br>";
		if(is_array($data))
		foreach($data as $filter){//site_filter_group
			if($filter['product_id'] == $import_product_id){
				//print_r($filter);
				$checkFilter = $this->checkProduct_Filter($product_id, $filter['site_filter_group'], $filter['param_value']);
				//echo $checkFilter;

				if( !$checkFilter ){
					$checkFilter = $this->addProduct_Filter($product_id, $filter['site_filter_group'], $filter['param_value']);
				}else{

				}
				if($checkFilter)
					$query = $this->db->query("
						INSERT INTO " . DB_PREFIX . "product_filter
						VALUES ('".$product_id."', '".$checkFilter."')
					"
					);


			}
		}

	}
	public function checkProduct_Filter($product_id, $filter_group, $param_value) {

		$query = $this->db->query("
			SELECT * FROM sh_sh_filter_description
			WHERE filter_group_id = '" . $this->db->escape($filter_group) . "' AND name='". $this->db->escape($param_value) ."'"
		);


		$rez = $query->rows;
		if(count($rez)){
			return $rez[0]['filter_id'];
		}
		return false;
	}

	public function addProduct_Filter($product_id, $filter_group, $param_value) {
		$query = $this->db->query("
			INSERT INTO sh_sh_filter
			VALUES (NULL, '".$filter_group."', 0);
		");
		$f_id = $this->db->getLastId();
		if($f_id){
			$query = $this->db->query("
			INSERT INTO sh_sh_filter_description
			VALUES ('".$f_id."', '2', '".$filter_group."', '".$param_value."')
			"
			);

			return $f_id;
		}


		return false;
	}

	public function clearVendorProducts($vendor) {
		$query = $this->db->query("DELETE FROM import_products
		WHERE vendor = '" . $this->db->escape($vendor) . "'");

		return true;
	}
	public function clearVendorProductsCharacters($vendor) {
		$query = $this->db->query("
		DELETE FROM import_products_character
		WHERE vendor = '" . $this->db->escape($vendor) . "'");

		return true;
	}
	public function addVendorCategory($data) {
		$this->db->query("
		INSERT INTO import_categoryes SET
		name = '" . $this->db->escape($data['name']) . "',
		cat_parent = '" . $this->db->escape($data['cat_parent']) . "',
		cat_id = '" . $this->db->escape($data['cat_id']) . "',
		vendor = '" . $this->db->escape($data['vendor']) . "'

		");

		//INSERT INTO `import_products` (`name`, `description`, `category`, `image`, `images`, `vendor`) VALUES ('111', '222', '3', '4444', '5', '6');

		$category_id = $this->db->getLastId();

		//$this->cache->delete('product');
		return $category_id;
	}
	public function updateVendorCategory($data) {
		if(!$data['id']) return false;

		$this->db->query("
			UPDATE import_categoryes SET
			name = '" . $this->db->escape($data['name']) . "',
			cat_parent = '" . $this->db->escape($data['cat_parent']) . "',
			cat_id = '" . $this->db->escape($data['cat_id']) . "',
			vendor = '" . $this->db->escape($data['vendor']) . "'
			where id = " . (int)$data['id'] . "
		");


		return true;
	}

	public function getVendorCategoryes($vendor) {
		$query = $this->db->query("SELECT c.* FROM import_categoryes c
		WHERE c.vendor = '" . $this->db->escape($vendor) . "'");

		return $query->rows;
	}
	public function getVendorAttributes($vendor) {
		$query = $this->db->query("SELECT DISTINCT ch.param_name, '".$vendor."' as vendorname FROM import_products_character ch
		WHERE ch.vendor = '" . $this->db->escape($vendor) . "'  ORDER BY ch.param_name LIMIT 40");

		return $query->rows;
	}
	public function getVendorLinkedCategoryes($vendor) {
		$query = $this->db->query("
		SELECT cat.*, ctc.site_cat_id FROM import_categoryes cat
		JOIN import_categoryes_to_category ctc ON (ctc.vendor_cat_id = cat.id AND ctc.status=1)
		WHERE cat.vendor = '" . $this->db->escape($vendor) . "' AND ctc.site_cat_id != 0");

		return $query->rows;
	}
	public function getSiteCategoryes() {
		$query = $this->db->query("
		SELECT c.*, (SELECT cd.name FROM " . DB_PREFIX . "category_description cd WHERE cd.category_id = c.category_id AND language_id = '" . (int)$this->config->get('config_language_id') . "') as name
		FROM " . DB_PREFIX . "category c
		ORDER BY parent_id, name
		");

		return $query->rows;
	}

	public function getSiteAttributes() {

		$query = $this->db->query("
		SELECT a.*, (SELECT ad.name FROM " . DB_PREFIX . "attribute_description ad WHERE ad.attribute_id = a.attribute_id) as name
		FROM " . DB_PREFIX . "attribute a
		ORDER BY name
		");

		return $query->rows;
	}

	public function getSiteFiltersGroups() {

		$query = $this->db->query("
		SELECT f.*, (SELECT fd.name FROM " . DB_PREFIX . "filter_group_description fd WHERE fd.filter_group_id = f.filter_group_id) as name

		FROM " . DB_PREFIX . "filter_group f
		ORDER BY sort_order, name
		");

		return $query->rows;
	}

	public function getSiteCategory($data) {
		$site_cat_id = 0;

		$query = $this->db->query("
		SELECT c.*, cd.name	FROM " . DB_PREFIX . "category c
		INNER JOIN " . DB_PREFIX . "category_description cd ON cd.category_id = c.category_id
		WHERE  c.category_id = " . $this->db->escape($data['site_cat_id']) . "
		LIMIT 1
		");
		if ($query->num_rows)
			return $query->rows;
		else return false;

	}

	public function getSiteCategoryFiltered($data) {
		$site_cat_id = 0;

		$query = $this->db->query("
		SELECT c.*, cd.name	FROM " . DB_PREFIX . "category c
		INNER JOIN " . DB_PREFIX . "category_description cd ON cd.category_id = c.category_id
		WHERE  c.parent_id = " . $this->db->escape($data['parent']) . " AND cd.name = '" . $this->db->escape($data['name']) . "'
		LIMIT 1
		");

		//Нет на сайте, добавляем
		if (!$query->num_rows){
			$site_cat_id = $this->addCategory(
			array( 'parent_id' => $data['parent'],
			'top' => 0,
			'sort_order' => 0,
			'column' => 0,
			'sort' => 0,
			'image' => '',
			'status' => 1,
			'category_description' => array('2'=> array('name'=>$data['name'],
															'meta_title'=>$data['name'],
															'description'=>'',
															'meta_description'=>'',
															'meta_keyword'=>'',
														),
											),
			));
			$query = $this->db->query("
					UPDATE import_categoryes SET site_id = '".$site_cat_id."' WHERE id = '".$data['id']."'
				");
		}else{
			$site_cat_id = $query->rows[0]['category_id'];
				$query = $this->db->query("
					UPDATE import_categoryes SET site_id = '".$site_cat_id."' WHERE id = '".$data['id']."'
				");
		}

		return($site_cat_id);
	}


	public function findAndImportCategoryProducts($data) {

		require_once DIR_SYSTEM . 'library/sqllib.php';
		$this->load->model('extension/module/brainyfilter');
		$site_cat_id = 0;


		$query = $this->db->query("
			SELECT ip.*	FROM import_products ip
			WHERE  ip.category = " . $this->db->escape($data['cat_id']) . "
		");

		//У категории нет товаров во временной табл.
		if (!$query->num_rows){
			return false;

		//У категории есть товары во временной табл.
		}else{

			$siteCategoryProducts = $this->getCategoryProductsOnSite(array('category'=>$data['site_id']));
			$siteCategoryProductsAttributes = $this->getCategoryProductsAttributes(array('category'=>$data['cat_id'], 'vendor'=>$data['vendor']));
			$siteCategoryProductsFilters = $this->getCategoryProductsFilters(array('category'=>$data['cat_id'], 'vendor'=>$data['vendor']));

			$import_products = $query->rows;
			foreach ($import_products as $ip) {
				$import_product_id = $ip['id'];
				$search = array_search($ip['artikul'], array_column($siteCategoryProducts, 'sku'));
				$images = $images_arr = explode(";", $ip['images']);

				if(!$ip['model']) $model = 'Нет';
				else $model = $ip['model'];
				//Продукт найден
				if($search!== false){
					$product_id = $siteCategoryProducts[$search]['product_id'];

					$attributeData = array();
					$this->editProduct_Attribute($product_id, $import_product_id, $siteCategoryProductsAttributes);
					$this->editProduct_Filters($product_id, $import_product_id, $siteCategoryProductsFilters);
					$status = $ip['quantity'] ? 1 : 0;
					if($status && !$ip['image'])  $status = 0;

					$this->editProduct_Import($product_id, array(
							'model' => $model,
							'sku' => $ip['artikul'],
							'product_id' => $product_id,
							'upc' => '',
							'ean' =>'',
							'jan' => '',
							'category' => $data['site_id'],
							'mpn' => '',
							'isbn' => $ip['vendor'],
							'location' => '',
							'quantity' =>  $ip['quantity'],
							'minimum' => 1,
							'subtract' => 1,
							'stock_status_id' => 7,  //Присутствие на складе
							'date_available' => date("Y.m.d"),
							'manufacturer_id' => $ip['manufacturier'],
							'shipping' => 1,
							'price' => $ip['price'],
							'product_special' => $ip['spesial'],
							'points' => 0,
							'weight' => 0,
							'weight_class_id' => 1,
							'length' => 0,
							'width' => 0,
							'height' => 0,
							'length_class_id' => 1,
							'status' => $status,
							'product_store' => array(0),
							'tax_class_id' => 9,
							'sort_order' => 0,
							'image' => $ip['image'],
							'images' => $images,
							'product_category' => explode('_', $data['path']),
							'product_description' => array('2'=>array(
															'name' => $ip['name'],
															'description' => $ip['description'],
															'keyword' => '',
															'tag' => '',
															'meta_title' => $ip['name'],
															'meta_description' => $ip['description'],
															'meta_keyword' => '',
														)),

						));
						//Обновляем данные для фильтра
						//$this->model_extension_module_brainyfilter->updateProductAttributeValues($product_id);
						$this->model_extension_module_brainyfilter->cacheProductProperties(array($product_id));

				//	exit;
				}
				else{
				if($data['debug'])	echo '<br>NOOT find artikul '.$ip['artikul'].''.'<br>';
				$status = $ip['quantity'] ? 1 : 0;
				if($status && !$ip['image'])  $status = 0;

					$product_id = $this->addProduct_Import(array(
							'model' => $model,
							'sku' => $ip['artikul'],
							'upc' => '',
							'ean' =>'',
							'jan' => '',
							'category' => $data['site_id'],
							'mpn' => '',
							'isbn' => $ip['vendor'],
							'location' => '',
							'quantity' =>  $ip['quantity'],
							'minimum' => 1,
							'subtract' => 1,
							'stock_status_id' => 7,  //Присутствие на складе
							'date_available' => date("Y.m.d"),
							'manufacturer_id' => 0,
							'shipping' => 1,
							'price' => $ip['price'],
							'product_special' => $ip['spesial'],
							'points' => 0,
							'weight' => 0,
							'weight_class_id' => 1,
							'length' => 0,
							'width' => 0,
							'height' => 0,
							'length_class_id' => 1,
							'status' => $status,
							'product_store' => array(0),
							'tax_class_id' => 9,
							'sort_order' => 0,
							'image' => $ip['image'],
							'images' => $images,
							'product_category' => explode('_', $data['path']),
							'product_description' => array('2'=>array(
															'name' => $ip['name'],
															'description' => $ip['description'],
															'keyword' => '',
															'tag' => '',
															'meta_title' => $ip['name'],
															'meta_description' => $ip['description'],
															'meta_keyword' => '',
														)),

						));
					$this->editProduct_Attribute($product_id, $import_product_id, $siteCategoryProductsAttributes);
					if($data['debug'])	echo '<br>Add product__'.$product_id;
						$this->model_extension_module_brainyfilter->cacheProductProperties(array($product_id));
				}


			}

			$site_cat_id = $query->rows[0]['category'];
				$query = $this->db->query("
					UPDATE import_categoryes SET site_id = '".$site_cat_id."' WHERE id = '".$data['id']."'
				");
		}

		return($site_cat_id);
	}
/*
echo
	public function getSiteCategory($categoryName) {
		$query = $this->db->query("
			SELECT c.*, (SELECT cd.name FROM " . DB_PREFIX . "category_description cd WHERE cd.category_id = c.category_id) as name
			FROM " . DB_PREFIX . "category c
			WHERE name = '$categoryName'
		");
		if ($query->num_rows)
			return $query->rows;
		else return false;
	}
*/
	protected function getImportCategoriesByParentId($parent_id = 0, $vendor = '') {
		$query = $this->db->query("
		SELECT * FROM import_categoryes c
		WHERE c.cat_parent = '" . (int)$parent_id . "' AND c.vendor='".$vendor."'
		");

		return $query->rows;
	}

	protected function getImportCategoryByParentId($parent_id = 0) {
		$query = $this->db->query("
		SELECT * FROM import_categoryes c
		WHERE c.cat_id = '" . (int)$parent_id . "'");

		return $query->rows;
	}

	public function loadCategories($parent_id, $current_path = '', $cid = '', $site_cat_id = 0, $debug = false, $vendor='') {
	if($debug) echo 'loadCategories__'.$parent_id.'<br>';
		$parent_info = $this->getImportCategoryByParentId($parent_id);
		$results = $this->getImportCategoriesByParentId($parent_id, $vendor);

		$kat_tree = array();
		$ret_string = '';

		if (!count($results) && $parent_info[0]['cat_parent']==0) {
		//echo 'count_0__'.$parent_id.'<br>';
			//$results = $parent_info;
			$site_cat_data = $this->getSiteCategory(array('site_cat_id'=>$site_cat_id));
			$new_path = $site_cat_data[0]['parent_id'] . '_' . $site_cat_id;
			$this->findAndImportCategoryProducts(array('id'=>$site_cat_id, 'cat_id'=>$parent_id, 'site_id'=>$site_cat_id, 'path'=>$new_path, 'debug'=>$debug, 'vendor'=>$vendor));
		/**/
		}
		else
		foreach ($results as $result) {

			$kat_tree[$result['cat_id']] = $result;
			//Проверяем есть ли такая категория (по названию и родителю), Если такая категория катеория есть, то обновляем site_id
			//$site_cat_id_cur = $this->getSiteCategoryFiltered(array('name'=> $result['name'], 'parent'=>$site_cat_id, 'id'=>$result['id']));
			$site_cat_id_cur = $site_cat_id;
			$site_cat_data = $this->getSiteCategory(array('name'=> $result['name'], 'site_cat_id'=>$site_cat_id));

			if($site_cat_data){
				$new_path = $site_cat_data[0]['parent_id'] . '_' . $site_cat_id;


				//Ищем товары текущей категории, и если находим, импортируем их на сайт
				$this->findAndImportCategoryProducts(array('id'=>$result['id'], 'cat_id'=>$result['cat_id'], 'site_id'=>$site_cat_id, 'path'=>$new_path, 'debug'=>$debug, 'vendor'=>$vendor));


				if($debug)
					echo 'checkCategory__'.$result['name'].'__'.$result['cat_id'].'____'.$site_cat_id.'____'.$site_cat_id_cur.'<br>';


				$childrens = $this->loadCategories($result['cat_id'], $new_path, $cid, $site_cat_id_cur, $debug);
				$kat_tree[$result['cat_id']]['path'] = $new_path;
				$kat_tree[$result['cat_id']]['site_id'] = $site_cat_id_cur;
				if ($childrens) {
					$kat_tree[$result['cat_id']]['childrens'] = $childrens;

				}
			}
		}

		return $kat_tree;

	}

	public function getSiteCategoryes_to_category() {
		$query = $this->db->query("
		SELECT c.*
		FROM import_categoryes_to_category c
		");

		return $query->rows;
	}

	public function getVendorAttribute_to_attribute($vendor) {
		$query = $this->db->query("
		SELECT c.*
		FROM import_attribute_to_attribute c
		WHERE vendor = '$vendor' ORDER BY import_attribute
		");

		return $query->rows;
	}
	public function getVendorAttribute_to_filter($vendor) {
		$query = $this->db->query("
		SELECT c.*
		FROM import_attribute_to_filter c
		WHERE vendor = '$vendor' ORDER BY import_attribute
		");

		return $query->rows;
	}

	public function attribute_to_attribute($data) {
		print_r($data);
		if(!isset($data['vendor'])) return false;
		//!$data['import_attribute'] || !isset($data['site_attribute_id']) ||

		if(!$data['import_attribute']){
			$query = $this->db->query("
				DELETE FROM import_attribute_to_attribute WHERE site_attribute = '".$this->db->escape($data['site_attribute_id'])."'  AND vendor = '".$this->db->escape($data['vendor'])."'
			");
		}elseif(!$data['site_attribute_id']){
			$query = $this->db->query("
				DELETE FROM import_attribute_to_attribute WHERE import_attribute = '".$this->db->escape($data['import_attribute'])."'  AND vendor = '".$this->db->escape($data['vendor'])."'
			");
		}else{
/*
			$query = $this->db->query("
				SELECT a.* FROM import_attribute_to_attribute a WHERE a.import_attribute = '".$this->db->escape($data['import_attribute'])."'  AND vendor = '".$this->db->escape($data['vendor'])."'
			");
*/
			$query = $this->db->query("
				DELETE FROM  import_attribute_to_attribute  WHERE
				(import_attribute = '".$this->db->escape($data['import_attribute'])."' OR site_attribute = '".$this->db->escape($data['site_attribute_id'])."')
				AND vendor = '".$this->db->escape($data['vendor'])."'
			");

			$import_attribute = $this->db->escape($data['import_attribute']);
			$site_attribute_id = $this->db->escape($data['site_attribute_id']);
			$vendor = $this->db->escape($data['vendor']);


			//if(!count($query->row) && $site_attribute_id != 0)
			{
				$query = $this->db->query("
				INSERT INTO import_attribute_to_attribute SET
					 import_attribute = '".$import_attribute."',
					 site_attribute = '".$site_attribute_id."',
					 vendor = '".$vendor."',
					 status = '1'
			");
			}
			/*else{
				$query = $this->db->query("
					UPDATE import_attribute_to_attribute SET
					 site_attribute = '".$site_attribute_id."'
					 WHERE  import_attribute = '".$import_attribute."'  AND vendor = '".$this->db->escape($data['vendor'])."'

				");
			}*/
		}
		return true;
		//return $query->rows;
	}


	public function attribute_to_filter($data) {
		print_r($data);
		//if( !isset($data['vendor']) || !isset($data['site_filter_group'])) return false;
		// ||!$data['import_attribute']

		if(!$data['import_attribute']){
			//echo '___'.$data['import_attribute'].'   1111_';
			$query = $this->db->query("
				DELETE FROM import_attribute_to_filter WHERE site_filter_group = '".$this->db->escape($data['site_filter_group'])."' AND vendor = '".$this->db->escape($data['vendor'])."'
			");
		}else{

			/*
			$query = $this->db->query("
				SELECT a.* FROM import_attribute_to_filter a WHERE a.site_filter_group = '".$this->db->escape($data['site_filter_group'])."' AND vendor = '".$this->db->escape($data['vendor'])."'
			");

			*/
			$query = $this->db->query("
				DELETE FROM import_attribute_to_filter WHERE
				(site_filter_group = '".$this->db->escape($data['site_filter_group'])."' OR import_attribute = '".$this->db->escape($data['import_attribute'])."')
				AND vendor = '".$this->db->escape($data['vendor'])."'
			");
			$import_attribute = $this->db->escape($data['import_attribute']);
			$site_filter_group = $this->db->escape($data['site_filter_group']);
			$vendor = $this->db->escape($data['vendor']);


			//if(!count($query->row) && $site_filter_group != 0)
			{
				$query = $this->db->query("
				INSERT INTO import_attribute_to_filter SET
					 import_attribute = '".$import_attribute."',
					 site_filter_group = '".$site_filter_group."',
					 vendor = '".$vendor."',
					 status = '1'
			");
			}/*else{

				$query = $this->db->query("
					UPDATE import_attribute_to_filter SET
					 site_filter_group = '".$site_filter_group."'
					 WHERE  import_attribute = '".$import_attribute."'  AND vendor = '".$this->db->escape($data['vendor'])."'

				");
			}*/
		}
		//echo 133333;
		return true;
		//return $query->rows;
	}
	public function category_to_category($data) {
		if(!$data['import_cat_id'] || !isset($data['site_cat_id'])) return false;
		$query = $this->db->query("
			SELECT c.* FROM import_categoryes_to_category c WHERE c.vendor_cat_id = '".$data['import_cat_id']."'
		");

		if(!count($query->row)){
			$query = $this->db->query("
			INSERT INTO import_categoryes_to_category SET
				 vendor_cat_id = '".$data['import_cat_id']."',
				 site_cat_id = '".$data['site_cat_id']."',
				 status = '1'
		");
		}else{
			$query = $this->db->query("
				UPDATE import_categoryes_to_category SET
				 site_cat_id = '".$data['site_cat_id']."'

				 WHERE  vendor_cat_id = '".$data['import_cat_id']."'

			");
		}
		return true;
		//return $query->rows;
	}
	public function addProduct($data) {
		$this->db->query("INSERT INTO " . DB_PREFIX . "product SET model = '" . $this->db->escape($data['model']) . "', sku = '" . $this->db->escape($data['sku']) . "', upc = '" . $this->db->escape($data['upc']) . "', ean = '" . $this->db->escape($data['ean']) . "', jan = '" . $this->db->escape($data['jan']) . "', isbn = '" . $this->db->escape($data['isbn']) . "', mpn = '" . $this->db->escape($data['mpn']) . "', location = '" . $this->db->escape($data['location']) . "', quantity = '" . (int)$data['quantity'] . "', minimum = '" . (int)$data['minimum'] . "', subtract = '" . (int)$data['subtract'] . "', stock_status_id = '" . (int)$data['stock_status_id'] . "', date_available = '" . $this->db->escape($data['date_available']) . "', manufacturer_id = '" . (int)$data['manufacturer_id'] . "', shipping = '" . (int)$data['shipping'] . "', hit = '" . (int)$data['hit'] . "', newproduct = '" . (int)$data['newproduct'] . "', price = '" . (float)$data['price'] . "', points = '" . (int)$data['points'] . "', weight = '" . (float)$data['weight'] . "', weight_class_id = '" . (int)$data['weight_class_id'] . "', length = '" . (float)$data['length'] . "', width = '" . (float)$data['width'] . "', height = '" . (float)$data['height'] . "', length_class_id = '" . (int)$data['length_class_id'] . "', status = '" . (int)$data['status'] . "', tax_class_id = '" . (int)$data['tax_class_id'] . "', sort_order = '" . (int)$data['sort_order'] . "', date_added = NOW()");

		$product_id = $this->db->getLastId();

		if (isset($data['image'])) {
			$this->db->query("UPDATE " . DB_PREFIX . "product SET image = '" . $this->db->escape($data['image']) . "' WHERE product_id = '" . (int)$product_id . "'");
		}

		foreach ($data['product_description'] as $language_id => $value) {
			$this->db->query("INSERT INTO " . DB_PREFIX . "product_description SET product_id = '" . (int)$product_id . "', language_id = '" . (int)$language_id . "', name = '" . $this->db->escape($value['name']) . "', description = '" . $this->db->escape($value['description']) . "', tag = '" . $this->db->escape($value['tag']) . "', meta_title = '" . $this->db->escape($value['meta_title']) . "', meta_description = '" . $this->db->escape($value['meta_description']) . "', meta_keyword = '" . $this->db->escape($value['meta_keyword']) . "'");
		}

		if (isset($data['product_store'])) {
			foreach ($data['product_store'] as $store_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_to_store SET product_id = '" . (int)$product_id . "', store_id = '" . (int)$store_id . "'");
			}
		}

		if (isset($data['product_attribute'])) {
			foreach ($data['product_attribute'] as $product_attribute) {
				if ($product_attribute['attribute_id']) {
					// Removes duplicates
					$this->db->query("DELETE FROM " . DB_PREFIX . "product_attribute WHERE product_id = '" . (int)$product_id . "' AND attribute_id = '" . (int)$product_attribute['attribute_id'] . "'");

					foreach ($product_attribute['product_attribute_description'] as $language_id => $product_attribute_description) {
						$this->db->query("DELETE FROM " . DB_PREFIX . "product_attribute WHERE product_id = '" . (int)$product_id . "' AND attribute_id = '" . (int)$product_attribute['attribute_id'] . "' AND language_id = '" . (int)$language_id . "'");

						$this->db->query("INSERT INTO " . DB_PREFIX . "product_attribute SET product_id = '" . (int)$product_id . "', attribute_id = '" . (int)$product_attribute['attribute_id'] . "', language_id = '" . (int)$language_id . "', text = '" .  $this->db->escape($product_attribute_description['text']) . "'");
					}
				}
			}
		}

		if (isset($data['product_option'])) {
			foreach ($data['product_option'] as $product_option) {
				if ($product_option['type'] == 'select' || $product_option['type'] == 'radio' || $product_option['type'] == 'checkbox' || $product_option['type'] == 'image') {
					if (isset($product_option['product_option_value'])) {
						$this->db->query("INSERT INTO " . DB_PREFIX . "product_option SET product_id = '" . (int)$product_id . "', option_id = '" . (int)$product_option['option_id'] . "', required = '" . (int)$product_option['required'] . "'");

						$product_option_id = $this->db->getLastId();

						foreach ($product_option['product_option_value'] as $product_option_value) {
							$this->db->query("INSERT INTO " . DB_PREFIX . "product_option_value SET product_option_id = '" . (int)$product_option_id . "', product_id = '" . (int)$product_id . "', option_id = '" . (int)$product_option['option_id'] . "', option_value_id = '" . (int)$product_option_value['option_value_id'] . "', quantity = '" . (int)$product_option_value['quantity'] . "', subtract = '" . (int)$product_option_value['subtract'] . "', price = '" . (float)$product_option_value['price'] . "', price_prefix = '" . $this->db->escape($product_option_value['price_prefix']) . "', points = '" . (int)$product_option_value['points'] . "', points_prefix = '" . $this->db->escape($product_option_value['points_prefix']) . "', weight = '" . (float)$product_option_value['weight'] . "', weight_prefix = '" . $this->db->escape($product_option_value['weight_prefix']) . "'");
						}
					}
				} else {
					$this->db->query("INSERT INTO " . DB_PREFIX . "product_option SET product_id = '" . (int)$product_id . "', option_id = '" . (int)$product_option['option_id'] . "', value = '" . $this->db->escape($product_option['value']) . "', required = '" . (int)$product_option['required'] . "'");
				}
			}
		}

		if (isset($data['product_discount'])) {
			foreach ($data['product_discount'] as $product_discount) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_discount SET product_id = '" . (int)$product_id . "', customer_group_id = '" . (int)$product_discount['customer_group_id'] . "', quantity = '" . (int)$product_discount['quantity'] . "', priority = '" . (int)$product_discount['priority'] . "', price = '" . (float)$product_discount['price'] . "', date_start = '" . $this->db->escape($product_discount['date_start']) . "', date_end = '" . $this->db->escape($product_discount['date_end']) . "'");
			}
		}

		if (isset($data['product_special'])) {
			foreach ($data['product_special'] as $product_special) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_special SET product_id = '" . (int)$product_id . "', customer_group_id = '" . (int)$product_special['customer_group_id'] . "', priority = '" . (int)$product_special['priority'] . "', price = '" . (float)$product_special['price'] . "', date_start = '" . $this->db->escape($product_special['date_start']) . "', date_end = '" . $this->db->escape($product_special['date_end']) . "'");
			}
		}

		if (isset($data['product_image'])) {
			foreach ($data['product_image'] as $product_image) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_image SET product_id = '" . (int)$product_id . "', image = '" . $this->db->escape($product_image['image']) . "', sort_order = '" . (int)$product_image['sort_order'] . "'");
			}
		}

		if (isset($data['product_download'])) {
			foreach ($data['product_download'] as $download_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_to_download SET product_id = '" . (int)$product_id . "', download_id = '" . (int)$download_id . "'");
			}
		}

		if (isset($data['product_category'])) {
			foreach ($data['product_category'] as $category_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_to_category SET product_id = '" . (int)$product_id . "', category_id = '" . (int)$category_id . "'");
			}
		}

		if (isset($data['product_filter'])) {
			foreach ($data['product_filter'] as $filter_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_filter SET product_id = '" . (int)$product_id . "', filter_id = '" . (int)$filter_id . "'");
			}
		}

		if (isset($data['product_related'])) {
			foreach ($data['product_related'] as $related_id) {
				$this->db->query("DELETE FROM " . DB_PREFIX . "product_related WHERE product_id = '" . (int)$product_id . "' AND related_id = '" . (int)$related_id . "'");
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_related SET product_id = '" . (int)$product_id . "', related_id = '" . (int)$related_id . "'");
				$this->db->query("DELETE FROM " . DB_PREFIX . "product_related WHERE product_id = '" . (int)$related_id . "' AND related_id = '" . (int)$product_id . "'");
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_related SET product_id = '" . (int)$related_id . "', related_id = '" . (int)$product_id . "'");
			}
		}

		if (isset($data['product_reward'])) {
			foreach ($data['product_reward'] as $customer_group_id => $product_reward) {
				if ((int)$product_reward['points'] > 0) {
					$this->db->query("INSERT INTO " . DB_PREFIX . "product_reward SET product_id = '" . (int)$product_id . "', customer_group_id = '" . (int)$customer_group_id . "', points = '" . (int)$product_reward['points'] . "'");
				}
			}
		}

		if (isset($data['product_layout'])) {
			foreach ($data['product_layout'] as $store_id => $layout_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_to_layout SET product_id = '" . (int)$product_id . "', store_id = '" . (int)$store_id . "', layout_id = '" . (int)$layout_id . "'");
			}
		}

		if ($data['keyword']) {
			$this->db->query("INSERT INTO " . DB_PREFIX . "url_alias SET query = 'product_id=" . (int)$product_id . "', keyword = '" . $this->db->escape($data['keyword']) . "'");
		}

		if (isset($data['product_recurring'])) {
			foreach ($data['product_recurring'] as $recurring) {
				$this->db->query("INSERT INTO `" . DB_PREFIX . "product_recurring` SET `product_id` = " . (int)$product_id . ", customer_group_id = " . (int)$recurring['customer_group_id'] . ", `recurring_id` = " . (int)$recurring['recurring_id']);
			}
		}

		$this->cache->delete('product');

		return $product_id;
	}

	public function editProduct($product_id, $data) {
		$this->db->query("UPDATE " . DB_PREFIX . "product SET model = '" . $this->db->escape($data['model']) . "', sku = '" . $this->db->escape($data['sku']) . "', upc = '" . $this->db->escape($data['upc']) . "', ean = '" . $this->db->escape($data['ean']) . "', jan = '" . $this->db->escape($data['jan']) . "', isbn = '" . $this->db->escape($data['isbn']) . "', mpn = '" . $this->db->escape($data['mpn']) . "', location = '" . $this->db->escape($data['location']) . "', quantity = '" . (int)$data['quantity'] . "', minimum = '" . (int)$data['minimum'] . "', subtract = '" . (int)$data['subtract'] . "', stock_status_id = '" . (int)$data['stock_status_id'] . "', date_available = '" . $this->db->escape($data['date_available']) . "', manufacturer_id = '" . (int)$data['manufacturer_id'] . "', shipping = '" . (int)$data['shipping'] . "', hit = '" . (int)$data['hit'] . "', newproduct = '" . (int)$data['newproduct'] . "', price = '" . (float)$data['price'] . "', points = '" . (int)$data['points'] . "', weight = '" . (float)$data['weight'] . "', weight_class_id = '" . (int)$data['weight_class_id'] . "', length = '" . (float)$data['length'] . "', width = '" . (float)$data['width'] . "', height = '" . (float)$data['height'] . "', length_class_id = '" . (int)$data['length_class_id'] . "', status = '" . (int)$data['status'] . "', tax_class_id = '" . (int)$data['tax_class_id'] . "', sort_order = '" . (int)$data['sort_order'] . "', date_modified = NOW() WHERE product_id = '" . (int)$product_id . "'");

		if (isset($data['image'])) {
			$this->db->query("UPDATE " . DB_PREFIX . "product SET image = '" . $this->db->escape($data['image']) . "' WHERE product_id = '" . (int)$product_id . "'");
		}

		$this->db->query("DELETE FROM " . DB_PREFIX . "product_description WHERE product_id = '" . (int)$product_id . "'");

		foreach ($data['product_description'] as $language_id => $value) {
			$this->db->query("INSERT INTO " . DB_PREFIX . "product_description SET product_id = '" . (int)$product_id . "', language_id = '" . (int)$language_id . "', name = '" . $this->db->escape($value['name']) . "', description = '" . $this->db->escape($value['description']) . "', tag = '" . $this->db->escape($value['tag']) . "', meta_title = '" . $this->db->escape($value['meta_title']) . "', meta_description = '" . $this->db->escape($value['meta_description']) . "', meta_keyword = '" . $this->db->escape($value['meta_keyword']) . "'");
		}

		$this->db->query("DELETE FROM " . DB_PREFIX . "product_to_store WHERE product_id = '" . (int)$product_id . "'");

		if (isset($data['product_store'])) {
			foreach ($data['product_store'] as $store_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_to_store SET product_id = '" . (int)$product_id . "', store_id = '" . (int)$store_id . "'");
			}
		}

		$this->db->query("DELETE FROM " . DB_PREFIX . "product_attribute WHERE product_id = '" . (int)$product_id . "'");

		if (!empty($data['product_attribute'])) {
			foreach ($data['product_attribute'] as $product_attribute) {
				if ($product_attribute['attribute_id']) {
					// Removes duplicates
					$this->db->query("DELETE FROM " . DB_PREFIX . "product_attribute WHERE product_id = '" . (int)$product_id . "' AND attribute_id = '" . (int)$product_attribute['attribute_id'] . "'");

					foreach ($product_attribute['product_attribute_description'] as $language_id => $product_attribute_description) {
						$this->db->query("INSERT INTO " . DB_PREFIX . "product_attribute SET product_id = '" . (int)$product_id . "', attribute_id = '" . (int)$product_attribute['attribute_id'] . "', language_id = '" . (int)$language_id . "', text = '" .  $this->db->escape($product_attribute_description['text']) . "'");
					}
				}
			}
		}

		$this->db->query("DELETE FROM " . DB_PREFIX . "product_option WHERE product_id = '" . (int)$product_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_option_value WHERE product_id = '" . (int)$product_id . "'");

		if (isset($data['product_option'])) {
			foreach ($data['product_option'] as $product_option) {
				if ($product_option['type'] == 'select' || $product_option['type'] == 'radio' || $product_option['type'] == 'checkbox' || $product_option['type'] == 'image') {
					if (isset($product_option['product_option_value'])) {
						$this->db->query("INSERT INTO " . DB_PREFIX . "product_option SET product_option_id = '" . (int)$product_option['product_option_id'] . "', product_id = '" . (int)$product_id . "', option_id = '" . (int)$product_option['option_id'] . "', required = '" . (int)$product_option['required'] . "'");

						$product_option_id = $this->db->getLastId();

						foreach ($product_option['product_option_value'] as $product_option_value) {
							$this->db->query("INSERT INTO " . DB_PREFIX . "product_option_value SET product_option_value_id = '" . (int)$product_option_value['product_option_value_id'] . "', product_option_id = '" . (int)$product_option_id . "', product_id = '" . (int)$product_id . "', option_id = '" . (int)$product_option['option_id'] . "', option_value_id = '" . (int)$product_option_value['option_value_id'] . "', quantity = '" . (int)$product_option_value['quantity'] . "', subtract = '" . (int)$product_option_value['subtract'] . "', price = '" . (float)$product_option_value['price'] . "', price_prefix = '" . $this->db->escape($product_option_value['price_prefix']) . "', points = '" . (int)$product_option_value['points'] . "', points_prefix = '" . $this->db->escape($product_option_value['points_prefix']) . "', weight = '" . (float)$product_option_value['weight'] . "', weight_prefix = '" . $this->db->escape($product_option_value['weight_prefix']) . "'");
						}
					}
				} else {
					$this->db->query("INSERT INTO " . DB_PREFIX . "product_option SET product_option_id = '" . (int)$product_option['product_option_id'] . "', product_id = '" . (int)$product_id . "', option_id = '" . (int)$product_option['option_id'] . "', value = '" . $this->db->escape($product_option['value']) . "', required = '" . (int)$product_option['required'] . "'");
				}
			}
		}

		$this->db->query("DELETE FROM " . DB_PREFIX . "product_discount WHERE product_id = '" . (int)$product_id . "'");

		if (isset($data['product_discount'])) {
			foreach ($data['product_discount'] as $product_discount) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_discount SET product_id = '" . (int)$product_id . "', customer_group_id = '" . (int)$product_discount['customer_group_id'] . "', quantity = '" . (int)$product_discount['quantity'] . "', priority = '" . (int)$product_discount['priority'] . "', price = '" . (float)$product_discount['price'] . "', date_start = '" . $this->db->escape($product_discount['date_start']) . "', date_end = '" . $this->db->escape($product_discount['date_end']) . "'");
			}
		}

		$this->db->query("DELETE FROM " . DB_PREFIX . "product_special WHERE product_id = '" . (int)$product_id . "'");

		if (isset($data['product_special'])) {
			foreach ($data['product_special'] as $product_special) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_special SET product_id = '" . (int)$product_id . "', customer_group_id = '" . (int)$product_special['customer_group_id'] . "', priority = '" . (int)$product_special['priority'] . "', price = '" . (float)$product_special['price'] . "', date_start = '" . $this->db->escape($product_special['date_start']) . "', date_end = '" . $this->db->escape($product_special['date_end']) . "'");
			}
		}

		$this->db->query("DELETE FROM " . DB_PREFIX . "product_image WHERE product_id = '" . (int)$product_id . "'");

		if (isset($data['product_image'])) {
			foreach ($data['product_image'] as $product_image) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_image SET product_id = '" . (int)$product_id . "', image = '" . $this->db->escape($product_image['image']) . "', sort_order = '" . (int)$product_image['sort_order'] . "'");
			}
		}

		$this->db->query("DELETE FROM " . DB_PREFIX . "product_to_download WHERE product_id = '" . (int)$product_id . "'");

		if (isset($data['product_download'])) {
			foreach ($data['product_download'] as $download_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_to_download SET product_id = '" . (int)$product_id . "', download_id = '" . (int)$download_id . "'");
			}
		}

		$this->db->query("DELETE FROM " . DB_PREFIX . "product_to_category WHERE product_id = '" . (int)$product_id . "'");

		if (isset($data['product_category'])) {
			foreach ($data['product_category'] as $category_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_to_category SET product_id = '" . (int)$product_id . "', category_id = '" . (int)$category_id . "'");
			}
		}

		$this->db->query("DELETE FROM " . DB_PREFIX . "product_filter WHERE product_id = '" . (int)$product_id . "'");

		if (isset($data['product_filter'])) {
			foreach ($data['product_filter'] as $filter_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_filter SET product_id = '" . (int)$product_id . "', filter_id = '" . (int)$filter_id . "'");
			}
		}

		$this->db->query("DELETE FROM " . DB_PREFIX . "product_related WHERE product_id = '" . (int)$product_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_related WHERE related_id = '" . (int)$product_id . "'");

		if (isset($data['product_related'])) {
			foreach ($data['product_related'] as $related_id) {
				$this->db->query("DELETE FROM " . DB_PREFIX . "product_related WHERE product_id = '" . (int)$product_id . "' AND related_id = '" . (int)$related_id . "'");
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_related SET product_id = '" . (int)$product_id . "', related_id = '" . (int)$related_id . "'");
				$this->db->query("DELETE FROM " . DB_PREFIX . "product_related WHERE product_id = '" . (int)$related_id . "' AND related_id = '" . (int)$product_id . "'");
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_related SET product_id = '" . (int)$related_id . "', related_id = '" . (int)$product_id . "'");
			}
		}

		$this->db->query("DELETE FROM " . DB_PREFIX . "product_reward WHERE product_id = '" . (int)$product_id . "'");

		if (isset($data['product_reward'])) {
			foreach ($data['product_reward'] as $customer_group_id => $value) {
				if ((int)$value['points'] > 0) {
					$this->db->query("INSERT INTO " . DB_PREFIX . "product_reward SET product_id = '" . (int)$product_id . "', customer_group_id = '" . (int)$customer_group_id . "', points = '" . (int)$value['points'] . "'");
				}
			}
		}

		$this->db->query("DELETE FROM " . DB_PREFIX . "product_to_layout WHERE product_id = '" . (int)$product_id . "'");

		if (isset($data['product_layout'])) {
			foreach ($data['product_layout'] as $store_id => $layout_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_to_layout SET product_id = '" . (int)$product_id . "', store_id = '" . (int)$store_id . "', layout_id = '" . (int)$layout_id . "'");
			}
		}

		$this->db->query("DELETE FROM " . DB_PREFIX . "url_alias WHERE query = 'product_id=" . (int)$product_id . "'");

		if ($data['keyword']) {
			$this->db->query("INSERT INTO " . DB_PREFIX . "url_alias SET query = 'product_id=" . (int)$product_id . "', keyword = '" . $this->db->escape($data['keyword']) . "'");
		}

		$this->db->query("DELETE FROM `" . DB_PREFIX . "product_recurring` WHERE product_id = " . (int)$product_id);

		if (isset($data['product_recurring'])) {
			foreach ($data['product_recurring'] as $product_recurring) {
				$this->db->query("INSERT INTO `" . DB_PREFIX . "product_recurring` SET `product_id` = " . (int)$product_id . ", customer_group_id = " . (int)$product_recurring['customer_group_id'] . ", `recurring_id` = " . (int)$product_recurring['recurring_id']);
			}
		}

		$this->cache->delete('product');
	}

	public function copyProduct($product_id) {
		$query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "product p WHERE p.product_id = '" . (int)$product_id . "'");

		if ($query->num_rows) {
			$data = $query->row;

			$data['sku'] = '';
			$data['upc'] = '';
			$data['viewed'] = '0';
			$data['keyword'] = '';
			$data['status'] = '0';

			$data['product_attribute'] = $this->getProductAttributes($product_id);
			$data['product_description'] = $this->getProductDescriptions($product_id);
			$data['product_discount'] = $this->getProductDiscounts($product_id);
			$data['product_filter'] = $this->getProductFilters($product_id);
			$data['product_image'] = $this->getProductImages($product_id);
			$data['product_option'] = $this->getProductOptions($product_id);
			$data['product_related'] = $this->getProductRelated($product_id);
			$data['product_reward'] = $this->getProductRewards($product_id);
			$data['product_special'] = $this->getProductSpecials($product_id);
			$data['product_category'] = $this->getProductCategories($product_id);
			$data['product_download'] = $this->getProductDownloads($product_id);
			$data['product_layout'] = $this->getProductLayouts($product_id);
			$data['product_store'] = $this->getProductStores($product_id);
			$data['product_recurrings'] = $this->getRecurrings($product_id);

			$this->addProduct($data);
		}
	}

	public function deleteProduct($product_id) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "product WHERE product_id = '" . (int)$product_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_attribute WHERE product_id = '" . (int)$product_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_description WHERE product_id = '" . (int)$product_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_discount WHERE product_id = '" . (int)$product_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_filter WHERE product_id = '" . (int)$product_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_image WHERE product_id = '" . (int)$product_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_option WHERE product_id = '" . (int)$product_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_option_value WHERE product_id = '" . (int)$product_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_related WHERE product_id = '" . (int)$product_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_related WHERE related_id = '" . (int)$product_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_reward WHERE product_id = '" . (int)$product_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_special WHERE product_id = '" . (int)$product_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_to_category WHERE product_id = '" . (int)$product_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_to_download WHERE product_id = '" . (int)$product_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_to_layout WHERE product_id = '" . (int)$product_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_to_store WHERE product_id = '" . (int)$product_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_recurring WHERE product_id = " . (int)$product_id);
		$this->db->query("DELETE FROM " . DB_PREFIX . "review WHERE product_id = '" . (int)$product_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "url_alias WHERE query = 'product_id=" . (int)$product_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "coupon_product WHERE product_id = '" . (int)$product_id . "'");

		$this->cache->delete('product');
	}

	public function getProduct($product_id) {
		$query = $this->db->query("SELECT DISTINCT *, (SELECT keyword FROM " . DB_PREFIX . "url_alias WHERE query = 'product_id=" . (int)$product_id . "') AS keyword FROM " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) WHERE p.product_id = '" . (int)$product_id . "' AND pd.language_id = '" . (int)$this->config->get('config_language_id') . "'");

		return $query->row;
	}

	public function getProducts($data = array()) {
		$sql = "SELECT * FROM " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) WHERE pd.language_id = '" . (int)$this->config->get('config_language_id') . "'";

		if (!empty($data['filter_name'])) {
			$sql .= " AND pd.name LIKE '" . $this->db->escape($data['filter_name']) . "%'";
		}

		if (!empty($data['filter_model'])) {
			$sql .= " AND p.model LIKE '" . $this->db->escape($data['filter_model']) . "%'";
		}

		if (isset($data['filter_price']) && !is_null($data['filter_price'])) {
			$sql .= " AND p.price LIKE '" . $this->db->escape($data['filter_price']) . "%'";
		}

		if (isset($data['filter_quantity']) && !is_null($data['filter_quantity'])) {
			$sql .= " AND p.quantity = '" . (int)$data['filter_quantity'] . "'";
		}

		if (isset($data['filter_status']) && !is_null($data['filter_status'])) {
			$sql .= " AND p.status = '" . (int)$data['filter_status'] . "'";
		}

		if (isset($data['filter_image']) && !is_null($data['filter_image'])) {
			if ($data['filter_image'] == 1) {
				$sql .= " AND (p.image IS NOT NULL AND p.image <> '' AND p.image <> 'no_image.png')";
			} else {
				$sql .= " AND (p.image IS NULL OR p.image = '' OR p.image = 'no_image.png')";
			}
		}

		$sql .= " GROUP BY p.product_id";

		$sort_data = array(
			'pd.name',
			'p.model',
			'p.price',
			'p.quantity',
			'p.status',
			'p.sort_order'
		);

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];
		} else {
			$sql .= " ORDER BY pd.name";
		}

		if (isset($data['order']) && ($data['order'] == 'DESC')) {
			$sql .= " DESC";
		} else {
			$sql .= " ASC";
		}

		if (isset($data['start']) || isset($data['limit'])) {
			if ($data['start'] < 0) {
				$data['start'] = 0;
			}

			if ($data['limit'] < 1) {
				$data['limit'] = 20;
			}

			$sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
		}

		$query = $this->db->query($sql);

		return $query->rows;
	}

	public function getProductsByCategoryId($category_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) LEFT JOIN " . DB_PREFIX . "product_to_category p2c ON (p.product_id = p2c.product_id) WHERE pd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND p2c.category_id = '" . (int)$category_id . "' ORDER BY pd.name ASC");

		return $query->rows;
	}

	public function getProductDescriptions($product_id) {
		$product_description_data = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_description WHERE product_id = '" . (int)$product_id . "'");

		foreach ($query->rows as $result) {
			$product_description_data[$result['language_id']] = array(
				'name'             => $result['name'],
				'description'      => $result['description'],
				'meta_title'       => $result['meta_title'],
				'meta_description' => $result['meta_description'],
				'meta_keyword'     => $result['meta_keyword'],
				'tag'              => $result['tag']
			);
		}

		return $product_description_data;
	}

	public function getProductCategories($product_id) {
		$product_category_data = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_to_category WHERE product_id = '" . (int)$product_id . "'");

		foreach ($query->rows as $result) {
			$product_category_data[] = $result['category_id'];
		}

		return $product_category_data;
	}

	public function getProductFilters($product_id) {
		$product_filter_data = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_filter WHERE product_id = '" . (int)$product_id . "'");

		foreach ($query->rows as $result) {
			$product_filter_data[] = $result['filter_id'];
		}

		return $product_filter_data;
	}

	public function getProductAttributes($product_id) {
		$product_attribute_data = array();

		$product_attribute_query = $this->db->query("SELECT attribute_id FROM " . DB_PREFIX . "product_attribute WHERE product_id = '" . (int)$product_id . "' GROUP BY attribute_id");

		foreach ($product_attribute_query->rows as $product_attribute) {
			$product_attribute_description_data = array();

			$product_attribute_description_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_attribute WHERE product_id = '" . (int)$product_id . "' AND attribute_id = '" . (int)$product_attribute['attribute_id'] . "'");

			foreach ($product_attribute_description_query->rows as $product_attribute_description) {
				$product_attribute_description_data[$product_attribute_description['language_id']] = array('text' => $product_attribute_description['text']);
			}

			$product_attribute_data[] = array(
				'attribute_id'                  => $product_attribute['attribute_id'],
				'product_attribute_description' => $product_attribute_description_data
			);
		}

		return $product_attribute_data;
	}

	public function getProductOptions($product_id) {
		$product_option_data = array();

		$product_option_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "product_option` po LEFT JOIN `" . DB_PREFIX . "option` o ON (po.option_id = o.option_id) LEFT JOIN `" . DB_PREFIX . "option_description` od ON (o.option_id = od.option_id) WHERE po.product_id = '" . (int)$product_id . "' AND od.language_id = '" . (int)$this->config->get('config_language_id') . "'");

		foreach ($product_option_query->rows as $product_option) {
			$product_option_value_data = array();

			$product_option_value_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_option_value pov LEFT JOIN " . DB_PREFIX . "option_value ov ON(pov.option_value_id = ov.option_value_id) WHERE pov.product_option_id = '" . (int)$product_option['product_option_id'] . "' ORDER BY ov.sort_order ASC");

			foreach ($product_option_value_query->rows as $product_option_value) {
				$product_option_value_data[] = array(
					'product_option_value_id' => $product_option_value['product_option_value_id'],
					'option_value_id'         => $product_option_value['option_value_id'],
					'quantity'                => $product_option_value['quantity'],
					'subtract'                => $product_option_value['subtract'],
					'price'                   => $product_option_value['price'],
					'price_prefix'            => $product_option_value['price_prefix'],
					'points'                  => $product_option_value['points'],
					'points_prefix'           => $product_option_value['points_prefix'],
					'weight'                  => $product_option_value['weight'],
					'weight_prefix'           => $product_option_value['weight_prefix']
				);
			}

			$product_option_data[] = array(
				'product_option_id'    => $product_option['product_option_id'],
				'product_option_value' => $product_option_value_data,
				'option_id'            => $product_option['option_id'],
				'name'                 => $product_option['name'],
				'type'                 => $product_option['type'],
				'value'                => $product_option['value'],
				'required'             => $product_option['required']
			);
		}

		return $product_option_data;
	}

	public function getProductOptionValue($product_id, $product_option_value_id) {
		$query = $this->db->query("SELECT pov.option_value_id, ovd.name, pov.quantity, pov.subtract, pov.price, pov.price_prefix, pov.points, pov.points_prefix, pov.weight, pov.weight_prefix FROM " . DB_PREFIX . "product_option_value pov LEFT JOIN " . DB_PREFIX . "option_value ov ON (pov.option_value_id = ov.option_value_id) LEFT JOIN " . DB_PREFIX . "option_value_description ovd ON (ov.option_value_id = ovd.option_value_id) WHERE pov.product_id = '" . (int)$product_id . "' AND pov.product_option_value_id = '" . (int)$product_option_value_id . "' AND ovd.language_id = '" . (int)$this->config->get('config_language_id') . "'");

		return $query->row;
	}

	public function getProductImages($product_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_image WHERE product_id = '" . (int)$product_id . "' ORDER BY sort_order ASC");

		return $query->rows;
	}

	public function getProductDiscounts($product_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_discount WHERE product_id = '" . (int)$product_id . "' ORDER BY quantity, priority, price");

		return $query->rows;
	}

	public function getProductSpecials($product_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_special WHERE product_id = '" . (int)$product_id . "' ORDER BY priority, price");

		return $query->rows;
	}

	public function getProductRewards($product_id) {
		$product_reward_data = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_reward WHERE product_id = '" . (int)$product_id . "'");

		foreach ($query->rows as $result) {
			$product_reward_data[$result['customer_group_id']] = array('points' => $result['points']);
		}

		return $product_reward_data;
	}

	public function getProductDownloads($product_id) {
		$product_download_data = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_to_download WHERE product_id = '" . (int)$product_id . "'");

		foreach ($query->rows as $result) {
			$product_download_data[] = $result['download_id'];
		}

		return $product_download_data;
	}

	public function getProductStores($product_id) {
		$product_store_data = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_to_store WHERE product_id = '" . (int)$product_id . "'");

		foreach ($query->rows as $result) {
			$product_store_data[] = $result['store_id'];
		}

		return $product_store_data;
	}

	public function getProductLayouts($product_id) {
		$product_layout_data = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_to_layout WHERE product_id = '" . (int)$product_id . "'");

		foreach ($query->rows as $result) {
			$product_layout_data[$result['store_id']] = $result['layout_id'];
		}

		return $product_layout_data;
	}

	public function getProductRelated($product_id) {
		$product_related_data = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_related WHERE product_id = '" . (int)$product_id . "'");

		foreach ($query->rows as $result) {
			$product_related_data[] = $result['related_id'];
		}

		return $product_related_data;
	}

	public function getRecurrings($product_id) {
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "product_recurring` WHERE product_id = '" . (int)$product_id . "'");

		return $query->rows;
	}

	public function getTotalProducts($data = array()) {
		$sql = "SELECT COUNT(DISTINCT p.product_id) AS total FROM " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id)";

		$sql .= " WHERE pd.language_id = '" . (int)$this->config->get('config_language_id') . "'";

		if (!empty($data['filter_name'])) {
			$sql .= " AND pd.name LIKE '" . $this->db->escape($data['filter_name']) . "%'";
		}

		if (!empty($data['filter_model'])) {
			$sql .= " AND p.model LIKE '" . $this->db->escape($data['filter_model']) . "%'";
		}

		if (isset($data['filter_price']) && !is_null($data['filter_price'])) {
			$sql .= " AND p.price LIKE '" . $this->db->escape($data['filter_price']) . "%'";
		}

		if (isset($data['filter_quantity']) && !is_null($data['filter_quantity'])) {
			$sql .= " AND p.quantity = '" . (int)$data['filter_quantity'] . "'";
		}

		if (isset($data['filter_status']) && !is_null($data['filter_status'])) {
			$sql .= " AND p.status = '" . (int)$data['filter_status'] . "'";
		}

		if (isset($data['filter_image']) && !is_null($data['filter_image'])) {
			if ($data['filter_image'] == 1) {
				$sql .= " AND (p.image IS NOT NULL AND p.image <> '' AND p.image <> 'no_image.png')";
			} else {
				$sql .= " AND (p.image IS NULL OR p.image = '' OR p.image = 'no_image.png')";
			}
		}

		$query = $this->db->query($sql);

		return $query->row['total'];
	}

	public function getTotalProductsByTaxClassId($tax_class_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "product WHERE tax_class_id = '" . (int)$tax_class_id . "'");

		return $query->row['total'];
	}

	public function getTotalProductsByStockStatusId($stock_status_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "product WHERE stock_status_id = '" . (int)$stock_status_id . "'");

		return $query->row['total'];
	}

	public function getTotalProductsByWeightClassId($weight_class_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "product WHERE weight_class_id = '" . (int)$weight_class_id . "'");

		return $query->row['total'];
	}

	public function getTotalProductsByLengthClassId($length_class_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "product WHERE length_class_id = '" . (int)$length_class_id . "'");

		return $query->row['total'];
	}

	public function getTotalProductsByDownloadId($download_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "product_to_download WHERE download_id = '" . (int)$download_id . "'");

		return $query->row['total'];
	}

	public function getTotalProductsByManufacturerId($manufacturer_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "product WHERE manufacturer_id = '" . (int)$manufacturer_id . "'");

		return $query->row['total'];
	}

	public function getTotalProductsByAttributeId($attribute_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "product_attribute WHERE attribute_id = '" . (int)$attribute_id . "'");

		return $query->row['total'];
	}

	public function getTotalProductsByOptionId($option_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "product_option WHERE option_id = '" . (int)$option_id . "'");

		return $query->row['total'];
	}

	public function getTotalProductsByProfileId($recurring_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "product_recurring WHERE recurring_id = '" . (int)$recurring_id . "'");

		return $query->row['total'];
	}

	public function getTotalProductsByLayoutId($layout_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "product_to_layout WHERE layout_id = '" . (int)$layout_id . "'");

		return $query->row['total'];
	}

public function addCategory($data) {
/*
	echo "INSERT INTO " . DB_PREFIX . "category SET
		 parent_id = '" . (int)$data['parent_id'] . "',
		 `top` = '" . (isset($data['top']) ? (int)$data['top'] : 0) . "',
		  `column` = '" . (int)$data['column'] . "',
			sort_order = '" . (int)$data['sort_order'] . "',
			status = '" . (int)$data['status'] . "', date_modified = NOW(), date_added = NOW()<br><br>";
*/
			
		$this->db->query("
			INSERT INTO " . DB_PREFIX . "category SET
				parent_id = '" . (int)$data['parent_id'] . "',
				`top` = '" . (isset($data['top']) ? (int)$data['top'] : 0) . "',
				`column` = '" . (int)$data['column'] . "',
				sort_order = '" . (int)$data['sort_order'] . "',
				origin_id = '" . $data['origin_id'] . "',
				status = '" . (int)$data['status'] . "', date_modified = NOW(), date_added = NOW()
		");

		$category_id = $this->db->getLastId();

		if (isset($data['image'])) {
			$this->db->query("UPDATE " . DB_PREFIX . "category SET image = '" . $this->db->escape($data['image']) . "' WHERE category_id = '" . (int)$category_id . "'");
		}

		foreach ($data['category_description'] as $language_id => $value) {
			$this->db->query("INSERT INTO " . DB_PREFIX . "category_description SET category_id = '" . (int)$category_id . "', language_id = '" . (int)$language_id . "', name = '" . $this->db->escape($value['name']) . "', description = '" . $this->db->escape($value['description']) . "', meta_title = '" . $this->db->escape($value['meta_title']) . "', meta_h1 = '" . $this->db->escape($value['meta_h1']) . "', meta_description = '" . $this->db->escape($value['meta_description']) . "', meta_keyword = '" . $this->db->escape($value['meta_keyword']) . "'");
		}

		// MySQL Hierarchical Data Closure Table Pattern
		$level = 0;

		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "category_path` WHERE category_id = '" . (int)$data['parent_id'] . "' ORDER BY `level` ASC");

		foreach ($query->rows as $result) {
			$this->db->query("INSERT INTO `" . DB_PREFIX . "category_path` SET `category_id` = '" . (int)$category_id . "', `path_id` = '" . (int)$result['path_id'] . "', `level` = '" . (int)$level . "'");

			$level++;
		}

		$this->db->query("INSERT INTO `" . DB_PREFIX . "category_path` SET `category_id` = '" . (int)$category_id . "', `path_id` = '" . (int)$category_id . "', `level` = '" . (int)$level . "'");

		if (isset($data['category_filter'])) {
			foreach ($data['category_filter'] as $filter_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "category_filter SET category_id = '" . (int)$category_id . "', filter_id = '" . (int)$filter_id . "'");
			}
		}

		if (isset($data['category_store'])) {
			foreach ($data['category_store'] as $store_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "category_to_store SET category_id = '" . (int)$category_id . "', store_id = '" . (int)$store_id . "'");
			}
		}
		
		// SEO URL
		if (!empty($data['category_seo_url'])) {
			$store_id = 0;
			$language_id = 1;
			$this->db->query("INSERT INTO " . DB_PREFIX . "seo_url SET store_id = '" . (int)$store_id . "', language_id = '" . (int)$language_id . "', query = 'category_id=" . (int)$category_id . "', keyword = '" . $data['category_seo_url'] . "'");
	
			
		}
		
		if (isset($data['product_related'])) {
			foreach ($data['product_related'] as $related_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_related_wb SET category_id = '" . (int)$category_id . "', product_id = '" . (int)$related_id . "'");
			}
		}
	
		if (isset($data['article_related'])) {
			foreach ($data['article_related'] as $related_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "article_related_wb SET category_id = '" . (int)$category_id . "', article_id = '" . (int)$related_id . "'");
			}
		}
		
		// Set which layout to use with this category
		if (isset($data['category_layout'])) {
			foreach ($data['category_layout'] as $store_id => $layout_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "category_to_layout SET category_id = '" . (int)$category_id . "', store_id = '" . (int)$store_id . "', layout_id = '" . (int)$layout_id . "'");
			}
		}

		$this->cache->delete('category');
		
		if($this->config->get('config_seo_pro')){		
		$this->cache->delete('seopro');
		}

		return $category_id;
	}


public function editCategory($category_id, $data) {
		$this->db->query("
		UPDATE " . DB_PREFIX . "category SET 
		parent_id = '" . (int)$data['parent_id'] . "', 
		`column` = '" . (int)$data['column'] . "', 
		sort_order = '" . (int)$data['sort_order'] . "', 
		status = '" . (int)$data['status'] . "', 
		noindex = '0', 
		date_modified = NOW() 
		
		WHERE category_id = '" . (int)$category_id . "'
		
		");

		if (isset($data['image'])) {
			$this->db->query("UPDATE " . DB_PREFIX . "category SET image = '" . $this->db->escape($data['image']) . "' WHERE category_id = '" . (int)$category_id . "'");
		}

		// $this->db->query("DELETE FROM " . DB_PREFIX . "category_description WHERE category_id = '" . (int)$category_id . "'");

		// foreach ($data['category_description'] as $language_id => $value) {
		// 	$this->db->query("INSERT INTO " . DB_PREFIX . "category_description SET category_id = '" . (int)$category_id . "', language_id = '" . (int)$language_id . "', name = '" . $this->db->escape($value['name']) . "', description = '" . $this->db->escape($value['description']) . "', meta_title = '" . $this->db->escape($value['meta_title']) . "', meta_h1 = '" . $this->db->escape($value['meta_h1']) . "', meta_description = '" . $this->db->escape($value['meta_description']) . "', meta_keyword = '" . $this->db->escape($value['meta_keyword']) . "'");
		// }

		// MySQL Hierarchical Data Closure Table Pattern
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "category_path` WHERE path_id = '" . (int)$category_id . "' ORDER BY level ASC");

		if ($query->rows) {
			foreach ($query->rows as $category_path) {
				// Delete the path below the current one
				$this->db->query("DELETE FROM `" . DB_PREFIX . "category_path` WHERE category_id = '" . (int)$category_path['category_id'] . "' AND level < '" . (int)$category_path['level'] . "'");

				$path = array();

				// Get the nodes new parents
				$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "category_path` WHERE category_id = '" . (int)$data['parent_id'] . "' ORDER BY level ASC");

				foreach ($query->rows as $result) {
					$path[] = $result['path_id'];
				}

				// Get whats left of the nodes current path
				$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "category_path` WHERE category_id = '" . (int)$category_path['category_id'] . "' ORDER BY level ASC");

				foreach ($query->rows as $result) {
					$path[] = $result['path_id'];
				}

				// Combine the paths with a new level
				$level = 0;

				foreach ($path as $path_id) {
					$this->db->query("REPLACE INTO `" . DB_PREFIX . "category_path` SET category_id = '" . (int)$category_path['category_id'] . "', `path_id` = '" . (int)$path_id . "', level = '" . (int)$level . "'");

					$level++;
				}
			}
		} else {
			// Delete the path below the current one
			$this->db->query("DELETE FROM `" . DB_PREFIX . "category_path` WHERE category_id = '" . (int)$category_id . "'");

			// Fix for records with no paths
			$level = 0;

			$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "category_path` WHERE category_id = '" . (int)$data['parent_id'] . "' ORDER BY level ASC");

			foreach ($query->rows as $result) {
				$this->db->query("INSERT INTO `" . DB_PREFIX . "category_path` SET category_id = '" . (int)$category_id . "', `path_id` = '" . (int)$result['path_id'] . "', level = '" . (int)$level . "'");

				$level++;
			}

			$this->db->query("REPLACE INTO `" . DB_PREFIX . "category_path` SET category_id = '" . (int)$category_id . "', `path_id` = '" . (int)$category_id . "', level = '" . (int)$level . "'");
		}

		$this->db->query("DELETE FROM " . DB_PREFIX . "category_filter WHERE category_id = '" . (int)$category_id . "'");

		if (isset($data['category_filter'])) {
			foreach ($data['category_filter'] as $filter_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "category_filter SET category_id = '" . (int)$category_id . "', filter_id = '" . (int)$filter_id . "'");
			}
		}

		$this->db->query("DELETE FROM " . DB_PREFIX . "category_to_store WHERE category_id = '" . (int)$category_id . "'");

		if (isset($data['category_store'])) {
			foreach ($data['category_store'] as $store_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "category_to_store SET category_id = '" . (int)$category_id . "', store_id = '" . (int)$store_id . "'");
			}
		}

		// SEO URL
		$this->db->query("DELETE FROM `" . DB_PREFIX . "seo_url` WHERE query = 'category_id=" . (int)$category_id . "'");

		if (!empty($data['category_seo_url'])) {
			$store_id = 0;
			$language_id = 1;
			$this->db->query("INSERT INTO " . DB_PREFIX . "seo_url SET store_id = '" . (int)$store_id . "', language_id = '" . (int)$language_id . "', query = 'category_id=" . (int)$category_id . "', keyword = '" . $data['category_seo_url'] . "'");
	
			
		}
		
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_related_wb WHERE category_id = '" . (int)$category_id . "'");
	
		if (isset($data['product_related'])) {
			foreach ($data['product_related'] as $related_id) {
				$this->db->query("DELETE FROM " . DB_PREFIX . "product_related_wb WHERE category_id = '" . (int)$category_id . "' AND product_id = '" . (int)$related_id . "'");
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_related_wb SET category_id = '" . (int)$category_id . "', product_id = '" . (int)$related_id . "'");
				
	
			}
		}
		
		$this->db->query("DELETE FROM " . DB_PREFIX . "article_related_wb WHERE category_id = '" . (int)$category_id . "'");
	
		if (isset($data['article_related'])) {
			foreach ($data['article_related'] as $related_id) {
				$this->db->query("DELETE FROM " . DB_PREFIX . "article_related_wb WHERE category_id = '" . (int)$category_id . "' AND article_id = '" . (int)$related_id . "'");
				$this->db->query("INSERT INTO " . DB_PREFIX . "article_related_wb SET category_id = '" . (int)$category_id . "', article_id = '" . (int)$related_id . "'");
				
	
			}
		}
		
		$this->db->query("DELETE FROM " . DB_PREFIX . "category_to_layout WHERE category_id = '" . (int)$category_id . "'");

		if (isset($data['category_layout'])) {
			foreach ($data['category_layout'] as $store_id => $layout_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "category_to_layout SET category_id = '" . (int)$category_id . "', store_id = '" . (int)$store_id . "', layout_id = '" . (int)$layout_id . "'");
			}
		}

		$this->cache->delete('category');
		
		if($this->config->get('config_seo_pro')){		
		$this->cache->delete('seopro');
		}
	}

public function get_task_info($task_name) {
	
	$query = $this->db->query("SELECT  * FROM  cron_task_limits  WHERE task_name = '" . $this->db->escape($task_name) . "'");

	return $query->rows[0];
	
}

public function edit_task_info($task_name, $data = array()) {
	if(empty($data['target'])) return false;
	
	if($data['target'] == 'file_timestamp'){
		if($data['file_timestamp'])
			$query = $this->db->query("
				UPDATE  cron_task_limits SET 
					current_step = 0, 
					end = 0, 
					file_timestamp = '". $data['file_timestamp'] ."' 
				WHERE task_name = '" . $this->db->escape($task_name) . "'
			");
	}
	
	if($data['target'] == 'step'){
		if(!empty($data['step']))
			$query = $this->db->query("
				UPDATE  cron_task_limits SET 
					current_step = '" .(int)$data['step'] . "'
				WHERE task_name = '" . $this->db->escape($task_name) . "'
			");
	}	
	
	if($data['target'] == 'end'){
		if($data['file_timestamp'])
			$query = $this->db->query("
				UPDATE  cron_task_limits SET 
					end = 0
				WHERE task_name = '" . $this->db->escape($task_name) . "'
			");
	}		
	
	return false;
	
}

	public function addProduct_Import($data) { 



		$this->db->query("INSERT INTO " . DB_PREFIX . "product SET model = '" . $this->db->escape($data['model']) . "', sku = '" . $this->db->escape($data['sku']) . "', upc = '" . $this->db->escape($data['upc']) . "', ean = '" . $this->db->escape($data['ean']) . "', jan = '" . $this->db->escape($data['jan']) . "', isbn = '" . $this->db->escape($data['isbn']) . "', mpn = '" . $this->db->escape($data['mpn']) . "', location = '" . $this->db->escape($data['location']) . "', quantity = '" . (int)$data['quantity'] . "', minimum = '" . (int)$data['minimum'] . "', subtract = '" . (int)$data['subtract'] . "', stock_status_id = '" . (int)$data['stock_status_id'] . "', date_available = '" . $this->db->escape($data['date_available']) . "', manufacturer_id = '" . (int)$data['manufacturer_id'] . "', shipping = '" . (int)$data['shipping'] . "', price = '" . (float)$data['price'] . "', points = '" . (int)$data['points'] . "', weight = '" . (float)$data['weight'] . "', weight_class_id = '" . (int)$data['weight_class_id'] . "', length = '" . (float)$data['length'] . "', width = '" . (float)$data['width'] . "', height = '" . (float)$data['height'] . "', length_class_id = '" . (int)$data['length_class_id'] . "', status = '" . (int)$data['status'] . "', tax_class_id = '" . (int)$data['tax_class_id'] . "', sort_order = '" . (int)$data['sort_order'] . "', date_added = NOW() ");

		$product_id = $this->db->getLastId();

		if (isset($data['image'])) {
			$this->db->query("UPDATE " . DB_PREFIX . "product SET image = '" . $this->db->escape($data['image']) . "' WHERE product_id = '" . (int)$product_id . "'");
		}

		foreach ($data['product_description'] as $language_id => $value) {
			$this->db->query("INSERT INTO " . DB_PREFIX . "product_description SET product_id = '" . (int)$product_id . "', language_id = '1', name = '" . $this->db->escape($value['name']) . "', description = '" . $this->db->escape($value['description']) . "', tag = '" . $this->db->escape($value['tag']) . "', meta_title = '" . $this->db->escape($value['meta_title']) . "', meta_description = '" . $this->db->escape($value['meta_description']) . "', meta_keyword = '" . $this->db->escape($value['meta_keyword']) . "'");
		}

		if (isset($data['product_store'])) {
			foreach ($data['product_store'] as $store_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_to_store SET product_id = '" . (int)$product_id . "', store_id = '" . (int)$store_id . "'");
			}
		}
/*
		if (isset($data['product_attribute'])) {
			foreach ($data['product_attribute'] as $product_attribute) {
				if ($product_attribute['attribute_id']) {
					// Removes duplicates
					$this->db->query("DELETE FROM " . DB_PREFIX . "product_attribute WHERE product_id = '" . (int)$product_id . "' AND attribute_id = '" . (int)$product_attribute['attribute_id'] . "'");

					foreach ($product_attribute['product_attribute_description'] as $language_id => $product_attribute_description) {
						$this->db->query("DELETE FROM " . DB_PREFIX . "product_attribute WHERE product_id = '" . (int)$product_id . "' AND attribute_id = '" . (int)$product_attribute['attribute_id'] . "' AND language_id = '" . (int)$language_id . "'");

						$this->db->query("INSERT INTO " . DB_PREFIX . "product_attribute SET product_id = '" . (int)$product_id . "', attribute_id = '" . (int)$product_attribute['attribute_id'] . "', language_id = '" . (int)$language_id . "', text = '" .  $this->db->escape($product_attribute_description['text']) . "'");
					}
				}
			}
		}
*/
		if (isset($data['product_option'])) {
			foreach ($data['product_option'] as $product_option) {
				if ($product_option['type'] == 'select' || $product_option['type'] == 'radio' || $product_option['type'] == 'checkbox' || $product_option['type'] == 'image') {
					if (isset($product_option['product_option_value'])) {
						$this->db->query("INSERT INTO " . DB_PREFIX . "product_option SET product_id = '" . (int)$product_id . "', option_id = '" . (int)$product_option['option_id'] . "', required = '" . (int)$product_option['required'] . "'");

						$product_option_id = $this->db->getLastId();

						foreach ($product_option['product_option_value'] as $product_option_value) {
							$this->db->query("INSERT INTO " . DB_PREFIX . "product_option_value SET product_option_id = '" . (int)$product_option_id . "', product_id = '" . (int)$product_id . "', option_id = '" . (int)$product_option['option_id'] . "', option_value_id = '" . (int)$product_option_value['option_value_id'] . "', quantity = '" . (int)$product_option_value['quantity'] . "', subtract = '" . (int)$product_option_value['subtract'] . "', price = '" . (float)$product_option_value['price'] . "', price_prefix = '" . $this->db->escape($product_option_value['price_prefix']) . "', points = '" . (int)$product_option_value['points'] . "', points_prefix = '" . $this->db->escape($product_option_value['points_prefix']) . "', weight = '" . (float)$product_option_value['weight'] . "', weight_prefix = '" . $this->db->escape($product_option_value['weight_prefix']) . "'");
						}
					}
				} else {
					$this->db->query("INSERT INTO " . DB_PREFIX . "product_option SET product_id = '" . (int)$product_id . "', option_id = '" . (int)$product_option['option_id'] . "', value = '" . $this->db->escape($product_option['value']) . "', required = '" . (int)$product_option['required'] . "'");
				}
			}
		}

		if (isset($data['product_discount'])) {
			foreach ($data['product_discount'] as $product_discount) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_discount SET product_id = '" . (int)$product_id . "', customer_group_id = '" . (int)$product_discount['customer_group_id'] . "', quantity = '" . (int)$product_discount['quantity'] . "', priority = '" . (int)$product_discount['priority'] . "', price = '" . (float)$product_discount['price'] . "', date_start = '" . $this->db->escape($product_discount['date_start']) . "', date_end = '" . $this->db->escape($product_discount['date_end']) . "'");
			}
		}
/*
		if (isset($data['product_special'])) {
			foreach ($data['product_special'] as $product_special) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_special SET product_id = '" . (int)$product_id . "', customer_group_id = '" . (int)$product_special['customer_group_id'] . "', priority = '" . (int)$product_special['priority'] . "', price = '" . (float)$product_special['price'] . "', date_start = '" . $this->db->escape($product_special['date_start']) . "', date_end = '" . $this->db->escape($product_special['date_end']) . "'");
			}
		}
*/
		if (!empty($data['product_special'])) {
			//$this->db->query("DELETE FROM " . DB_PREFIX . "product_special WHERE product_id = '" . (int)$product_id . "'");
			$this->db->query("INSERT INTO " . DB_PREFIX . "product_special SET
			product_id = '" . (int)$product_id . "',
			customer_group_id = '1',
			priority = '0',
			price = '" . (float)$data['product_special'] . "',
			date_start = '0000-00-00',
			date_end = '0000-00-00'
			");
		}

		if (isset($data['images'])) {
			foreach ($data['images'] as $product_image) {
				if($product_image)
					$this->db->query("INSERT INTO " . DB_PREFIX . "product_image SET product_id = '" . (int)$product_id . "', image = '" . $this->db->escape($product_image) . "', sort_order = '0'");
			}
		}

		if (isset($data['product_download'])) {
			foreach ($data['product_download'] as $download_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_to_download SET product_id = '" . (int)$product_id . "', download_id = '" . (int)$download_id . "'");
			}
		}

		if (isset($data['product_category'])) {
			foreach ($data['product_category'] as $category_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_to_category SET product_id = '" . (int)$product_id . "', category_id = '" . (int)$category_id . "', main_category = '1'");
			}

			if (isset($data['category'])){
			//	$this->db->query("UPDATE " . DB_PREFIX . "product_to_category SET main_category = '1'  WHERE  product_id = '".$product_id."' AND category_id = '".$data['category']."'");
			}
		}
/*
		if (isset($data['main_category_id']) && $data['main_category_id'] > 0) {
			$this->db->query("DELETE FROM " . DB_PREFIX . "product_to_category WHERE product_id = '" . (int)$product_id . "' AND category_id = '" . (int)$data['main_category_id'] . "'");
			$this->db->query("INSERT INTO " . DB_PREFIX . "product_to_category SET product_id = '" . (int)$product_id . "', category_id = '" . (int)$data['main_category_id'] . "', main_category = 1");
				} elseif (isset($data['product_category'][0])) {
			$this->db->query("UPDATE " . DB_PREFIX . "product_to_category SET main_category = 1 WHERE product_id = '" . (int)$product_id . "' AND category_id = '" . (int)$data['product_category'][0] . "'");
		}
*/
/*		if (!empty($data['category'])){
			
			if(count($data['category'])){
				
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_to_category SET product_id = '" . (int)$product_id . "', category_id = '" . (int)$data['category']['category_id'] . "'");
				
				$this->db->query("UPDATE " . DB_PREFIX . "product_to_category SET main_category = '1'  WHERE  product_id = '".$product_id."' AND category_id = '".$data['category']['category_id']."'");
			
			}
		}
*/
/*
		if (isset($data['product_filter'])) {
			foreach ($data['product_filter'] as $filter_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_filter SET product_id = '" . (int)$product_id . "', filter_id = '" . (int)$filter_id . "'");
			}
		}
*/
		if (isset($data['product_related'])) {
			foreach ($data['product_related'] as $related_id) {
				$this->db->query("DELETE FROM " . DB_PREFIX . "product_related WHERE product_id = '" . (int)$product_id . "' AND related_id = '" . (int)$related_id . "'");
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_related SET product_id = '" . (int)$product_id . "', related_id = '" . (int)$related_id . "'");
				$this->db->query("DELETE FROM " . DB_PREFIX . "product_related WHERE product_id = '" . (int)$related_id . "' AND related_id = '" . (int)$product_id . "'");
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_related SET product_id = '" . (int)$related_id . "', related_id = '" . (int)$product_id . "'");
			}
		}

		if (isset($data['product_reward'])) {
			foreach ($data['product_reward'] as $customer_group_id => $product_reward) {
				if ((int)$product_reward['points'] > 0) {
					$this->db->query("INSERT INTO " . DB_PREFIX . "product_reward SET product_id = '" . (int)$product_id . "', customer_group_id = '" . (int)$customer_group_id . "', points = '" . (int)$product_reward['points'] . "'");
				}
			}
		}

		if (isset($data['product_layout'])) {
			foreach ($data['product_layout'] as $store_id => $layout_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_to_layout SET product_id = '" . (int)$product_id . "', store_id = '" . (int)$store_id . "', layout_id = '" . (int)$layout_id . "'");
			}
		}

/* 		if (!empty($data['keyword'])) {
			$this->db->query("INSERT INTO " . DB_PREFIX . "url_alias SET query = 'product_id=" . (int)$product_id . "', keyword = '" . $this->db->escape($data['keyword']) . "'");
		} 
*/
		// SEO URL
		if (isset($data['product_seo_url'])) {
			foreach ($data['product_seo_url'] as $store_id => $language) {
				foreach ($language as $language_id => $keyword) {
					if (!empty($keyword)) {
						$this->db->query("INSERT INTO " . DB_PREFIX . "seo_url SET store_id = '" . (int)$store_id . "', language_id = '" . (int)$language_id . "', query = 'product_id=" . (int)$product_id . "', keyword = '" . $this->db->escape($keyword) . "'");
					}
				}
			}
		}
		
		if (isset($data['product_recurring'])) {
			foreach ($data['product_recurring'] as $recurring) {
				$this->db->query("INSERT INTO `" . DB_PREFIX . "product_recurring` SET `product_id` = " . (int)$product_id . ", customer_group_id = " . (int)$recurring['customer_group_id'] . ", `recurring_id` = " . (int)$recurring['recurring_id']);
			}
		}

		$this->cache->delete('product');

		return $product_id;
	}

	public function editProduct_Import($product_id, $data) {
		$this->db->query("UPDATE " . DB_PREFIX . "product SET 
		model = '" . $this->db->escape($data['model']) . "', 
		sku = '" . $this->db->escape($data['sku']) . "', 
		upc = '" . $this->db->escape($data['upc']) . "', 
		ean = '" . $this->db->escape($data['ean']) . "', 
		jan = '" . $this->db->escape($data['jan']) . "', 
		isbn = '" . $this->db->escape($data['isbn']) . "', 
		mpn = '" . $this->db->escape($data['mpn']) . "', 
		location = '" . $this->db->escape($data['location']) . "', 
		quantity = '" . (int)$data['quantity'] . "', 
		minimum = '" . (int)$data['minimum'] . "', 
		subtract = '" . (int)$data['subtract'] . "', 
		stock_status_id = '" . (int)$data['stock_status_id'] . "', 
		date_available = '" . $this->db->escape($data['date_available']) . "',
		 manufacturer_id = '" . (int)$data['manufacturer_id'] . "', 
		 shipping = '" . (int)$data['shipping'] . "', 
		 price = '" . (float)$data['price'] . "', 
		 points = '" . (int)$data['points'] . "', 
		 weight = '" . (float)$data['weight'] . "',
		 weight_class_id = '" . (int)$data['weight_class_id'] . "', 
		 length = '" . (float)$data['length'] . "', width = '" . (float)$data['width'] . "', 
		 height = '" . (float)$data['height'] . "', length_class_id = '" . (int)$data['length_class_id'] . "',
		 status = '" . (int)$data['status'] . "', 
		 tax_class_id = '" . (int)$data['tax_class_id'] . "', 
		 sort_order = '" . (int)$data['sort_order'] . "', 
		 date_modified = NOW() WHERE product_id = '" . (int)$product_id . "'");

		if (!empty($data['image'])) {
			$this->db->query("UPDATE " . DB_PREFIX . "product SET image = '" . $this->db->escape($data['image']) . "' WHERE product_id = '" . (int)$product_id . "'");
		}

//Не изменяем имя товара и описание при обновлении
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_description WHERE product_id = '" . (int)$product_id . "'");

		foreach ($data['product_description'] as $language_id => $value) {
			$this->db->query("INSERT INTO " . DB_PREFIX . "product_description SET product_id = '" . (int)$product_id . "', language_id = '1', name = '" . $this->db->escape($value['name']) . "', description = '" . $this->db->escape($value['description']) . "', tag = '" . $this->db->escape($value['tag']) . "', meta_title = '" . $this->db->escape($value['meta_title']) . "', meta_description = '" . $this->db->escape($value['meta_description']) . "', meta_keyword = '" . $this->db->escape($value['meta_keyword']) . "'");
		}
/**/
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_to_store WHERE product_id = '" . (int)$product_id . "'");

		if (isset($data['product_store'])) {
			foreach ($data['product_store'] as $store_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_to_store SET product_id = '" . (int)$product_id . "', store_id = '" . (int)$store_id . "'");
			}
		}
/*
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_attribute WHERE product_id = '" . (int)$product_id . "'");

		if (!empty($data['product_attribute'])) {
			foreach ($data['product_attribute'] as $product_attribute) {
				if ($product_attribute['attribute_id']) {
					// Removes duplicates
					$this->db->query("DELETE FROM " . DB_PREFIX . "product_attribute WHERE product_id = '" . (int)$product_id . "' AND attribute_id = '" . (int)$product_attribute['attribute_id'] . "'");

					foreach ($product_attribute['product_attribute_description'] as $language_id => $product_attribute_description) {
						$this->db->query("INSERT INTO " . DB_PREFIX . "product_attribute SET product_id = '" . (int)$product_id . "', attribute_id = '" . (int)$product_attribute['attribute_id'] . "', language_id = '" . (int)$language_id . "', text = '" .  $this->db->escape($product_attribute_description['text']) . "'");
					}
				}
			}
		}
*/
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_option WHERE product_id = '" . (int)$product_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_option_value WHERE product_id = '" . (int)$product_id . "'");

		if (isset($data['product_option'])) {
			foreach ($data['product_option'] as $product_option) {
				if ($product_option['type'] == 'select' || $product_option['type'] == 'radio' || $product_option['type'] == 'checkbox' || $product_option['type'] == 'image') {
					if (isset($product_option['product_option_value'])) {
						$this->db->query("INSERT INTO " . DB_PREFIX . "product_option SET product_option_id = '" . (int)$product_option['product_option_id'] . "', product_id = '" . (int)$product_id . "', option_id = '" . (int)$product_option['option_id'] . "', required = '" . (int)$product_option['required'] . "'");

						$product_option_id = $this->db->getLastId();

						foreach ($product_option['product_option_value'] as $product_option_value) {
							$this->db->query("INSERT INTO " . DB_PREFIX . "product_option_value SET product_option_value_id = '" . (int)$product_option_value['product_option_value_id'] . "', product_option_id = '" . (int)$product_option_id . "', product_id = '" . (int)$product_id . "', option_id = '" . (int)$product_option['option_id'] . "', option_value_id = '" . (int)$product_option_value['option_value_id'] . "', quantity = '" . (int)$product_option_value['quantity'] . "', subtract = '" . (int)$product_option_value['subtract'] . "', price = '" . (float)$product_option_value['price'] . "', price_prefix = '" . $this->db->escape($product_option_value['price_prefix']) . "', points = '" . (int)$product_option_value['points'] . "', points_prefix = '" . $this->db->escape($product_option_value['points_prefix']) . "', weight = '" . (float)$product_option_value['weight'] . "', weight_prefix = '" . $this->db->escape($product_option_value['weight_prefix']) . "'");
						}
					}
				} else {
					$this->db->query("INSERT INTO " . DB_PREFIX . "product_option SET product_option_id = '" . (int)$product_option['product_option_id'] . "', product_id = '" . (int)$product_id . "', option_id = '" . (int)$product_option['option_id'] . "', value = '" . $this->db->escape($product_option['value']) . "', required = '" . (int)$product_option['required'] . "'");
				}
			}
		}
/*
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_discount WHERE product_id = '" . (int)$product_id . "'");

		if (isset($data['product_discount'])) {
			foreach ($data['product_discount'] as $product_discount) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_discount SET product_id = '" . (int)$product_id . "', customer_group_id = '" . (int)$product_discount['customer_group_id'] . "', quantity = '" . (int)$product_discount['quantity'] . "', priority = '" . (int)$product_discount['priority'] . "', price = '" . (float)$product_discount['price'] . "', date_start = '" . $this->db->escape($product_discount['date_start']) . "', date_end = '" . $this->db->escape($product_discount['date_end']) . "'");
			}
		}

		$this->db->query("DELETE FROM " . DB_PREFIX . "product_special WHERE product_id = '" . (int)$product_id . "'");

		if (isset($data['product_special'])) {
			foreach ($data['product_special'] as $product_special) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_special SET product_id = '" . (int)$product_id . "', customer_group_id = '" . (int)$product_special['customer_group_id'] . "', priority = '" . (int)$product_special['priority'] . "', price = '" . (float)$product_special['price'] . "', date_start = '" . $this->db->escape($product_special['date_start']) . "', date_end = '" . $this->db->escape($product_special['date_end']) . "'");
			}
		}
*/
		if (!empty($data['product_special'])) {
			$this->db->query("DELETE FROM " . DB_PREFIX . "product_special WHERE product_id = '" . (int)$product_id . "'");
			$this->db->query("INSERT INTO " . DB_PREFIX . "product_special SET
			product_id = '" . (int)$product_id . "',
			customer_group_id = '1',
			priority = '0',
			price = '" . (float)$data['product_special'] . "',
			date_start = '0000-00-00',
			date_end = '0000-00-00'
			");

		}

		$this->db->query("DELETE FROM " . DB_PREFIX . "product_image WHERE product_id = '" . (int)$product_id . "'");

		if (isset($data['images'])) {
			foreach ($data['images'] as $ind => $product_image) {
				if($product_image)
					$this->db->query("INSERT INTO " . DB_PREFIX . "product_image SET product_id = '" . (int)$product_id . "', image = '" . $this->db->escape($product_image) . "', sort_order = '" . (int)$ind . "'");
			}
		}

		$this->db->query("DELETE FROM " . DB_PREFIX . "product_to_download WHERE product_id = '" . (int)$product_id . "'");

		if (isset($data['product_download'])) {
			foreach ($data['product_download'] as $download_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_to_download SET product_id = '" . (int)$product_id . "', download_id = '" . (int)$download_id . "'");
			}
		}

		if(!empty($data['product_category'])){
				$this->db->query("DELETE FROM " . DB_PREFIX . "product_to_category WHERE product_id = '" . (int)$product_id . "'");

				if (isset($data['product_category'])) {
					//$this->db->query("DELETE FROM " . DB_PREFIX . "product_to_category WHERE product_id = '" . (int)$product_id . "'");

					foreach ($data['product_category'] as $category_id) {

						$this->db->query("INSERT INTO " . DB_PREFIX . "product_to_category SET product_id = '" . (int)$product_id . "', category_id = '" . (int)$category_id . "'");
					}

					if (isset($data['category'])){
						//$this->db->query("UPDATE " . DB_PREFIX . "product_to_category SET main_category = '1'  WHERE  product_id = '".(int)$product_id."' AND category_id = '".$data['category']."'");
						//echo "<br>UPDATE " . DB_PREFIX . "product_to_category SET main_category = '1'  WHERE  product_id = '".$product_id."' AND category_id = '".$data['category']."'<br>";
					}
				}
		}		
				
/*  
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_filter WHERE product_id = '" . (int)$product_id . "'");

		if (isset($data['product_filter'])) {
			foreach ($data['product_filter'] as $filter_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_filter SET product_id = '" . (int)$product_id . "', filter_id = '" . (int)$filter_id . "'");
			}
		}
*/
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_related WHERE product_id = '" . (int)$product_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "product_related WHERE related_id = '" . (int)$product_id . "'");

		if (isset($data['product_related'])) {
			foreach ($data['product_related'] as $related_id) {
				$this->db->query("DELETE FROM " . DB_PREFIX . "product_related WHERE product_id = '" . (int)$product_id . "' AND related_id = '" . (int)$related_id . "'");
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_related SET product_id = '" . (int)$product_id . "', related_id = '" . (int)$related_id . "'");
				$this->db->query("DELETE FROM " . DB_PREFIX . "product_related WHERE product_id = '" . (int)$related_id . "' AND related_id = '" . (int)$product_id . "'");
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_related SET product_id = '" . (int)$related_id . "', related_id = '" . (int)$product_id . "'");
			}
		}

		$this->db->query("DELETE FROM " . DB_PREFIX . "product_reward WHERE product_id = '" . (int)$product_id . "'");

		if (isset($data['product_reward'])) {
			foreach ($data['product_reward'] as $customer_group_id => $value) {
				if ((int)$value['points'] > 0) {
					$this->db->query("INSERT INTO " . DB_PREFIX . "product_reward SET product_id = '" . (int)$product_id . "', customer_group_id = '" . (int)$customer_group_id . "', points = '" . (int)$value['points'] . "'");
				}
			}
		}

		$this->db->query("DELETE FROM " . DB_PREFIX . "product_to_layout WHERE product_id = '" . (int)$product_id . "'");

		if (isset($data['product_layout'])) {
			foreach ($data['product_layout'] as $store_id => $layout_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "product_to_layout SET product_id = '" . (int)$product_id . "', store_id = '" . (int)$store_id . "', layout_id = '" . (int)$layout_id . "'");
			}
		}
/* 
		$this->db->query("DELETE FROM " . DB_PREFIX . "url_alias WHERE query = 'product_id=" . (int)$product_id . "'");

		if (!empty($data['keyword'])) {
			$this->db->query("INSERT INTO " . DB_PREFIX . "url_alias SET query = 'product_id=" . (int)$product_id . "', keyword = '" . $this->db->escape($data['keyword']) . "'");
		}
  */
		$this->db->query("DELETE FROM `" . DB_PREFIX . "product_recurring` WHERE product_id = " . (int)$product_id);

		if (isset($data['product_recurring'])) {
			foreach ($data['product_recurring'] as $product_recurring) {
				$this->db->query("INSERT INTO `" . DB_PREFIX . "product_recurring` SET `product_id` = " . (int)$product_id . ", customer_group_id = " . (int)$product_recurring['customer_group_id'] . ", `recurring_id` = " . (int)$product_recurring['recurring_id']);
			}
		}

		$this->cache->delete('product');
	}

	public function addManufacturer($data) {
		$this->db->query("INSERT INTO " . DB_PREFIX . "manufacturer SET name = '" . $this->db->escape($data['name']) . "', sort_order = '" . (int)$data['sort_order'] . "'");

		$manufacturer_id = $this->db->getLastId();

		if (isset($data['image'])) {
			$this->db->query("UPDATE " . DB_PREFIX . "manufacturer SET image = '" . $this->db->escape($data['image']) . "' WHERE manufacturer_id = '" . (int)$manufacturer_id . "'");
		}

		if (isset($data['manufacturer_store'])) {
			foreach ($data['manufacturer_store'] as $store_id) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "manufacturer_to_store SET manufacturer_id = '" . (int)$manufacturer_id . "', store_id = '" . (int)$store_id . "'");
			}
		}

/* 		if (isset($data['keyword'])) {
			$this->db->query("INSERT INTO " . DB_PREFIX . "url_alias SET query = 'manufacturer_id=" . (int)$manufacturer_id . "', keyword = '" . $this->db->escape($data['keyword']) . "'");
		}
 */
		$this->cache->delete('manufacturer');

		return $manufacturer_id;
	}

	public function getManufacturers($data = array()) {
		$sql = "SELECT * FROM " . DB_PREFIX . "manufacturer";

		if (!empty($data['filter_name'])) {
			$sql .= " WHERE name LIKE '" . $this->db->escape($data['filter_name']) . "%'";
		}

		$sort_data = array(
			'name',
			'sort_order'
		);

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];
		} else {
			$sql .= " ORDER BY name";
		}

		if (isset($data['order']) && ($data['order'] == 'DESC')) {
			$sql .= " DESC";
		} else {
			$sql .= " ASC";
		}

		if (isset($data['start']) || isset($data['limit'])) {
			if ($data['start'] < 0) {
				$data['start'] = 0;
			}

			if ($data['limit'] < 1) {
				$data['limit'] = 20;
			}

			$sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
		}

		$query = $this->db->query($sql);

		return $query->rows;
	}
}
