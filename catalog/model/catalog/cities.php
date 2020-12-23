<?php
class ModelCatalogCities extends Model {
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

}