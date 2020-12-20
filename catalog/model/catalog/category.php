<?php
class ModelCatalogCategory extends Model {
	public function getCategory($category_id) {
		$query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "category c LEFT JOIN " . DB_PREFIX . "category_description cd ON (c.category_id = cd.category_id) LEFT JOIN " . DB_PREFIX . "category_to_store c2s ON (c.category_id = c2s.category_id) WHERE c.category_id = '" . (int)$category_id . "' AND cd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND c2s.store_id = '" . (int)$this->config->get('config_store_id') . "' AND c.status = '1'");

		return $query->row;
	}

	public function getCategories($parent_id = 0) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "category c LEFT JOIN " . DB_PREFIX . "category_description cd ON (c.category_id = cd.category_id) 
		LEFT JOIN " . DB_PREFIX . "category_to_store c2s ON (c.category_id = c2s.category_id) 
		WHERE c.parent_id = '" . (int)$parent_id . "' AND 
		cd.language_id = '" . (int)$this->config->get('config_language_id') . "'
		 AND c2s.store_id = '" . (int)$this->config->get('config_store_id') . "' 
		  AND c.status = '1' ORDER BY c.sort_order, LCASE(cd.name)");

		return $query->rows;
	}

	public function searchCategories($filter = []) {
		if(empty($filter['filter_name'])) return false;
		//if(empty($filter['limit'])) 
		$filter['limit'] = 3;

		$query = $this->db->query("
			SELECT * FROM " . DB_PREFIX . "category c 
			LEFT JOIN " . DB_PREFIX . "category_description cd ON (c.category_id = cd.category_id) 
			LEFT JOIN " . DB_PREFIX . "category_to_store c2s ON (c.category_id = c2s.category_id) 
			WHERE
				cd.name LIKE '%".$filter['filter_name']."%'
				AND cd.language_id = '" . (int)$this->config->get('config_language_id') . "'
				AND c2s.store_id = '" . (int)$this->config->get('config_store_id') . "'
				AND c.status = '1' 
				
				ORDER BY c.sort_order, LCASE(cd.name)
				LIMIT ".(int)$filter['limit']."
			"
		
		);

		return $query->rows;
	}

	public function getAllCategories($level = 0, $parent = 0) {
		$this->cache->delete('categoryes.all.level.'.$level); 
		$all_categoryes = $this->cache->get('categoryes.all.level.'.$level);
		if (!$all_categoryes) {
			//$q_parent = $parent ? " AND c.parent_id = {$parent} " : "";
			$q_parent = '';
			$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "category c LEFT JOIN " . DB_PREFIX . "category_description cd ON (c.category_id = cd.category_id) 
				LEFT JOIN " . DB_PREFIX . "category_to_store c2s ON (c.category_id = c2s.category_id) 
				WHERE  
				cd.language_id = '" . (int)$this->config->get('config_language_id') . "'
				AND c2s.store_id = '" . (int)$this->config->get('config_store_id') . "' 
				AND c.status = '1' 
				{$q_parent}
				ORDER BY c.sort_order
			");

			$all_categoryes = [];
			$rows = $query->rows;

			$all_categoryes = $this->makeCatTree($rows, $parent);
			//$all_categoryes	= $query->rows;
		 	$this->cache->set('categoryes.all.level.'.$level, $all_categoryes);
		}

		return $all_categoryes;
	}
	

	public function getAllFilters_atr($data) {
		$attributes = [];
		//Получаем уникальные значения атрибута Цвет (50)
		$query = $this->db->query("
		SELECT DISTINCT text FROM " . DB_PREFIX . "product_attribute pa 
		JOIN oc_product p ON p.product_id = pa.product_id AND p.status = 1
		JOIN oc_product_to_category с_p ON с_p.product_id = pa.product_id 
		WHERE pa.attribute_id = '50' AND с_p.category_id = ".(integer)$data['filter_category_id']."
		ORDER BY pa.text
		");
		$r = $query->rows;
		$min = $max = 0;
		foreach($r as $a){
			if( $min > (int)$a['text'] ) $min = (int)$a['text'];
			if( $max < (int)$a['text'] ) $max = (int)$a['text'];
		}
		$attributes[50] = [
						'results'=> $r,
						'min' => $min,
						'max' => $max,
						];
		//Получаем уникальные значения атрибута Производитель (44)
		$query = $this->db->query("
		SELECT text FROM " . DB_PREFIX . "product_attribute pa 
		JOIN oc_product p ON p.product_id = pa.product_id AND p.status = 1
		JOIN oc_product_to_category с_p ON с_p.product_id = pa.product_id 
		WHERE pa.attribute_id = '44' AND с_p.category_id = ".(integer)$data['filter_category_id']."
		ORDER BY pa.text
		");
		$r = $query->rows;
		$min = $max = 0;
		foreach($r as $a){
			if( $min > (int)$a['text'] ) $min = (int)$a['text'];
			if( $max < (int)$a['text'] ) $max = (int)$a['text'];
		}
		$attributes[44] = [
						'results'=> $r,
						'min' => $min,
						'max' => $max,
						];


		//Получаем уникальные значения атрибута Страна  (16)
		$query = $this->db->query("
		SELECT DISTINCT text FROM " . DB_PREFIX . "product_attribute pa 
		JOIN oc_product p ON p.product_id = pa.product_id AND p.status = 1
		JOIN oc_product_to_category с_p ON с_p.product_id = pa.product_id 
		WHERE pa.attribute_id = '16' AND с_p.category_id = ".(integer)$data['filter_category_id']."
		ORDER BY pa.text
		");
		
		$r = $query->rows; 
		
		$min = $max = 0;
		foreach($r as $a){
			if( $min > (int)$a['text'] ) $min = (int)$a['text'];
			if( $max < (int)$a['text'] ) $max = (int)$a['text'];
		}

		$attributes[16] = [
						'results'=> $r,
						'min' => $min,
						'max' => $max,
						];
		
		//Получаем уникальные значения атрибута модель  (56)
		$query = $this->db->query("
		SELECT DISTINCT text FROM " . DB_PREFIX . "product_attribute pa 
		JOIN oc_product p ON p.product_id = pa.product_id AND p.status = 1
		JOIN oc_product_to_category с_p ON с_p.product_id = pa.product_id 
		WHERE pa.attribute_id = '56' AND с_p.category_id = ".(integer)$data['filter_category_id']."
		ORDER BY pa.text
		");
		$r = $query->rows;
		$min = $max = 0;
		foreach($r as $a){
			if( $min > (int)$a['text'] ) $min = (int)$a['text'];
			if( $max < (int)$a['text'] ) $max = (int)$a['text'];
		}
		
		$attributes[56] = [
						'results'=> $r,
						'min' => $min,
						'max' => $max,
						];		
		
		
		//Получаем уникальные значения атрибута Материал (55)
		$query = $this->db->query("
		SELECT DISTINCT text FROM " . DB_PREFIX . "product_attribute pa 
		JOIN oc_product p ON p.product_id = pa.product_id AND p.status = 1
		JOIN oc_product_to_category с_p ON с_p.product_id = pa.product_id 
		WHERE pa.attribute_id = '55' AND с_p.category_id = ".(integer)$data['filter_category_id']."
		ORDER BY pa.text
		");
		$r = $query->rows;
		$min = $max = 0;
		foreach($r as $a){
			if( $min > (int)$a['text'] ) $min = (int)$a['text'];
			if( $max < (int)$a['text'] ) $max = (int)$a['text'];
		}
		$attributes[55] = [
						'results'=> $r,
						'min' => $min,
						'max' => $max,
						];

		//Получаем уникальные значения атрибута Производитель (44)
		$query = $this->db->query("
		SELECT DISTINCT text FROM " . DB_PREFIX . "product_attribute pa 
		JOIN oc_product p ON p.product_id = pa.product_id AND p.status = 1
		JOIN oc_product_to_category с_p ON с_p.product_id = pa.product_id 
		WHERE pa.attribute_id = '44' AND с_p.category_id = ".(integer)$data['filter_category_id']."
		ORDER BY pa.text
		");
		$r = $query->rows;
		$min = $max = 0;
		foreach($r as $a){
			if( $min > (int)$a['text'] ) $min = (int)$a['text'];
			if( $max < (int)$a['text'] ) $max = (int)$a['text'];
		}
		$attributes[44] = [
						'results'=> $r,
						'min' => $min,
						'max' => $max,
						];
		// //Получаем уникальные значения атрибута Эффекты  (18)
		// $query = $this->db->query("
		// SELECT DISTINCT text FROM " . DB_PREFIX . "product_attribute pa 
		// JOIN oc_product p ON p.product_id = pa.product_id AND p.status = 1
		// WHERE pa.attribute_id = '18' 
		// ORDER BY pa.text
		// ");
		// $r = $query->rows;
		// $min = $max = 0;
		// $rezults_distinct=[];
		// foreach($r as $a){
		// 	//if( $min > (int)$a['text'] ) $min = (int)$a['text'];
		// 	//if( $max < (int)$a['text'] ) $max = (int)$a['text'];
			
		// 	$RR = explode(",", $a['text']);
		// 	foreach($RR as $rr){
		// 		$rezults_distinct[$rr]=['text'=>$rr];
		// 	}
		// }
		// $attributes[18] = [
		// 				'results'=> $rezults_distinct,
		// 				'min' => $min,
		// 				'max' => $max,
		// 				];

		return $attributes;
	}

	private function makeCatTree($categoryes, $parent, $only_parent = false ) {

			$c = array();
			foreach ($categoryes as $cat) {
				if($cat['parent_id'] == $parent){
					$c[$cat['category_id']] = $cat;
					$c[$cat['category_id']]['children'] = $this->makeCatTree($categoryes,$cat['category_id']);
				}
			}
			
	
		return $c;
	}


	public function getCategoryFilters($category_id) {
		$implode = array();

		$query = $this->db->query("SELECT filter_id FROM " . DB_PREFIX . "category_filter WHERE category_id = '" . (int)$category_id . "'");

		foreach ($query->rows as $result) {
			$implode[] = (int)$result['filter_id'];
		}

		$filter_group_data = array();

		if ($implode) {
			$filter_group_query = $this->db->query("SELECT DISTINCT f.filter_group_id, fgd.name, fg.sort_order FROM " . DB_PREFIX . "filter f LEFT JOIN " . DB_PREFIX . "filter_group fg ON (f.filter_group_id = fg.filter_group_id) LEFT JOIN " . DB_PREFIX . "filter_group_description fgd ON (fg.filter_group_id = fgd.filter_group_id) WHERE f.filter_id IN (" . implode(',', $implode) . ") AND fgd.language_id = '" . (int)$this->config->get('config_language_id') . "' GROUP BY f.filter_group_id ORDER BY fg.sort_order, LCASE(fgd.name)");

			foreach ($filter_group_query->rows as $filter_group) {
				$filter_data = array();

				$filter_query = $this->db->query("SELECT DISTINCT f.filter_id, fd.name FROM " . DB_PREFIX . "filter f LEFT JOIN " . DB_PREFIX . "filter_description fd ON (f.filter_id = fd.filter_id) WHERE f.filter_id IN (" . implode(',', $implode) . ") AND f.filter_group_id = '" . (int)$filter_group['filter_group_id'] . "' AND fd.language_id = '" . (int)$this->config->get('config_language_id') . "' ORDER BY f.sort_order, LCASE(fd.name)");

				foreach ($filter_query->rows as $filter) {
					$filter_data[] = array(
						'filter_id' => $filter['filter_id'],
						'name'      => $filter['name']
					);
				}

				if ($filter_data) {
					$filter_group_data[] = array(
						'filter_group_id' => $filter_group['filter_group_id'],
						'name'            => $filter_group['name'],
						'filter'          => $filter_data
					);
				}
			}
		}

		return $filter_group_data;
	}

	public function getCategoryLayoutId($category_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "category_to_layout WHERE category_id = '" . (int)$category_id . "' AND store_id = '" . (int)$this->config->get('config_store_id') . "'");

		if ($query->num_rows) {
			return (int)$query->row['layout_id'];
		} else {
			return 0;
		}
	}

	public function getTotalCategoriesByCategoryId($parent_id = 0) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "category c LEFT JOIN " . DB_PREFIX . "category_to_store c2s ON (c.category_id = c2s.category_id) WHERE c.parent_id = '" . (int)$parent_id . "' AND c2s.store_id = '" . (int)$this->config->get('config_store_id') . "' AND c.status = '1'");

		return $query->row['total'];
	}
}