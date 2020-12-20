<?php
class ModelExtensionModuleQuickcheckout extends Model { 

	public function saveKeyword($code, $data, $store_id = 0) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "seo_url WHERE query = 'extension/quickcheckout/checkout'");
		if (!empty($data['quickcheckout_keyword'])) {
			foreach ($data['quickcheckout_keyword'] as $key=>$value) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "seo_url SET store_id = '".$store_id."', language_id = '".$key."', query = 'extension/quickcheckout/checkout', keyword = '" . $this->db->escape($value) . "'");
			}
		}
	}
	
}