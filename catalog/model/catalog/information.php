<?php
class ModelCatalogInformation extends Model {
	public function getInformation($information_id) {
		$query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "information i LEFT JOIN " . DB_PREFIX . "information_description id ON (i.information_id = id.information_id) LEFT JOIN " . DB_PREFIX . "information_to_store i2s ON (i.information_id = i2s.information_id) WHERE i.information_id = '" . (int)$information_id . "' AND id.language_id = '" . (int)$this->config->get('config_language_id') . "' AND i2s.store_id = '" . (int)$this->config->get('config_store_id') . "' AND i.status = '1'");

		return $query->row;
	}

	public function getInformations() {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "information i LEFT JOIN " . DB_PREFIX . "information_description id ON (i.information_id = id.information_id) LEFT JOIN " . DB_PREFIX . "information_to_store i2s ON (i.information_id = i2s.information_id) WHERE id.language_id = '" . (int)$this->config->get('config_language_id') . "' AND i2s.store_id = '" . (int)$this->config->get('config_store_id') . "' AND i.status = '1' ORDER BY i.sort_order, LCASE(id.title) ASC");

		return $query->rows;
	}

	public function getInformationLayoutId($information_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "information_to_layout WHERE information_id = '" . (int)$information_id . "' AND store_id = '" . (int)$this->config->get('config_store_id') . "'");

		if ($query->num_rows) {
			return (int)$query->row['layout_id'];
		} else {
			return 0;
		}
	}

	public function getFaqes($data = array()) {
		$sql = "SELECT f.*,  DATE_FORMAT(gbdate, '%d.%m.%Y %H:%i') AS 'gbdate'  FROM " . DB_PREFIX . "faq f WHERE published = 1  ORDER BY `id` DESC ";
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

	public function faqesCount() {
		$query = $this->db->query("SELECT count(id) as count FROM " . DB_PREFIX . "faq WHERE published = 1  ORDER BY `gbdate` DESC");
		return $query->row['count'];
	}

	public function get_managers_emails() {
		$query = $this->db->query("SELECT email FROM " . DB_PREFIX . "user WHERE user_group_id = 2 AND status = 1");
		return $query->rows;
	}

	public function add_faq($data = array()) {
		$query = $this->db->query("INSERT INTO " . DB_PREFIX . "faq ( gbname, gbmail, gbloca, gbtext, gbdate ) VALUES('".$this->db->escape($data['gbname'])."', '".$this->db->escape($data['gbmail'])."', '".$this->db->escape($data['gbloca'])."', '".$this->db->escape($data['gbtext'])."', NOW()) ");

		return;
	}
}