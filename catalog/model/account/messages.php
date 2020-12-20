<?php
class ModelAccountMessages extends Model {


	public function deleteMessage($message_id) {
		//$this->db->query("DELETE FROM " . DB_PREFIX . "customer_wishlist WHERE customer_id = '" . (int)$this->customer->getId() . "' AND product_id = '" . (int)$product_id . "'");
	}

	public function getUserMessages() {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "user_messages WHERE customer_id = '" . (int)$this->customer->getId() . "'");

		return $query->rows;
	}
	

	public function getTotalMessages() {
		
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "user_messages WHERE customer_id = '" . (int)$this->customer->getId() . "'");

		return $query->row['total'];
	}
}
