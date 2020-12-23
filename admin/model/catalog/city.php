<?php
// *	@source		See SOURCE.txt for source and other copyright.
// *	@license	GNU General Public License version 3; see LICENSE.txt

class ModelCatalogCity extends Model {

	public function addCity($data) {
		$data = $data['information_description'][1];

		$this->db->query("INSERT INTO " . DB_PREFIX . "cities SET name = '" . $this->db->escape($data['name']) . "', coordinates = '" . $this->db->escape($data['coordinates']) . "', sort_order = '" . (int)$data['sort_order'] . "', status = '" . (int)$data['status'] . "', date_added = NOW()");

		$city_id = $this->db->getLastId();

		$this->cache->delete('cities');

		return $city_id;
	}

	public function addOffice($data) {

		$data = $data['information_description'][1];
		$this->db->query("INSERT INTO " . DB_PREFIX . "cities_offices SET 
			`city_id` = '".(int)$data['city_id']."',
			`address` = '".$this->db->escape($data['address'])."',
			`phone` = '".$this->db->escape($data['phone'])."',
			`worktime` = '".$this->db->escape($data['worktime'])."',
			`coordinates` = '".$this->db->escape($data['coordinates'])."',
			`email` = '".$this->db->escape($data['email'])."',
			`site` = '".$this->db->escape($data['site'])."',
			`status` = '".(int)$data['status']."',
			`sort_order` = '".(int)$data['sort_order']."',
			`date_added` = NOW()
		");

		$city_id = $this->db->getLastId();

		$this->cache->delete('cities');

		return $city_id;
	}	
	
	// public function editInformationStatus($information_id, $status) {
    //     $this->db->query("UPDATE " . DB_PREFIX . "information SET status = '" . (int)$status . "'WHERE information_id = '" . (int)$information_id . "'");
        
	// 	$this->cache->delete('information');
		
    // }


	public function editCity($city_id, $data = array()) {
		$data = $data['information_description'][1];

		$query = $this->db->query("UPDATE " . DB_PREFIX . "cities SET
			`name` = '".$this->db->escape($data['name'])."',
			`coordinates` = '".$this->db->escape($data['coordinates'])."',
			`status` = '".(int)$data['status']."',
			`sort_order` = '".(int)$data['sort_order']."'		

			WHERE city_id = '".(int)$city_id."'
		");
		$this->cache->delete('cities');

		return;
	}
	
	public function editOffice($office_id, $data = array()) {
		$data = $data['information_description'][1];

		$query = $this->db->query("UPDATE " . DB_PREFIX . "cities_offices SET
			`city_id` = '".(int)$data['city_id']."',
			`address` = '".$this->db->escape($data['address'])."',
			`phone` = '".$this->db->escape($data['phone'])."',
			`worktime` = '".$this->db->escape($data['worktime'])."',
			`coordinates` = '".$this->db->escape($data['coordinates'])."',
			`email` = '".$this->db->escape($data['email'])."',
			`site` = '".$this->db->escape($data['site'])."',
			`status` = '".(int)$data['status']."',
			`sort_order` = '".(int)$data['sort_order']."'		

			WHERE office_id = '".(int)$office_id."'
		");
		$this->cache->delete('cities_offices');

		return;
	}
	
	public function deleteCity($city_id) {
		$query = $this->db->query("DELETE FROM  " . DB_PREFIX . "cityes  WHERE city_id='".(int)$city_id."'");
		
		$this->cache->delete('cities');

		return;
	}
	public function getCities($data = array()) {
		//if ($data) 
		{
			$sql = "SELECT * FROM " . DB_PREFIX . "cities i  WHERE 1";

			$sort_data = array(
				'id.name',
				'i.sort_order'
			);

			$sql .= " ORDER BY date_added DESC";

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

	public function getOffices($data = array()) {
		//if ($data) 
		{
			$sql = "SELECT * FROM " . DB_PREFIX . "cities_offices i  WHERE 1";

			$sort_data = array(
				'id.name',
				'i.sort_order'
			);

			if(!empty($data['city_id'])){
				$sql .= " AND city_id='".(int)$data['city_id']."'";
			}

			$sql .= " ORDER BY date_added DESC";

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

	public function getCity($city_id) {
		$query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "cities WHERE city_id = '" . (int)$city_id . "'");

		return $query->row;
	}

	public function getOffice($office_id) {
		$query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "cities_offices WHERE office_id = '" . (int)$office_id . "'");

		return $query->row;
	}

	public function getTotalCities() {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "cities");

		return $query->row['total'];
	}

	public function set_published($data) {
		$this->db->query("UPDATE " . DB_PREFIX . "cities  SET status = '".(int)$data['status']."' WHERE id='".(int)$data['city_id']."'");
		return false;
	}
	



	
}