<?php
class ModelExtensionPurpletreeMultivendorSellerattribute extends Model {
		public function addAttribute($seller_id,$data) {
			$this->db->query("INSERT INTO " . DB_PREFIX . "attribute SET attribute_group_id = '" . (int)$data['attribute_group_id'] . "', sort_order = '" . (int)$data['sort_order'] . "'");
			
			
			$attribute_id = $this->db->getLastId();
			
			$this->db->query("INSERT INTO " . DB_PREFIX . "purpletree_vendor_attribute SET seller_id = '" . (int)$seller_id . "', attribute_id = '" . (int)$attribute_id . "'");
			
			foreach ($data['attribute_description'] as $language_id => $value) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "attribute_description SET attribute_id = '" . (int)$attribute_id . "', language_id = '" . (int)$language_id . "', name = '" . $this->db->escape($value['name']) . "'");
			}
			
			return $attribute_id;
		}
		
		public function editSellerAttribute($data) {
			//echo"<pre>"; print_r($data); die;
			$this->db->query("UPDATE " . DB_PREFIX . "attribute SET attribute_group_id = '" . (int)$data['attribute_group_id'] . "', sort_order = '" . (int)$data['sort_order'] . "' WHERE attribute_id = '" . (int)$data['attribute_idd'] . "'");
			
			$this->db->query("DELETE FROM " . DB_PREFIX . "attribute_description WHERE attribute_id = '" . (int)$data['attribute_idd'] . "'");
			
			foreach ($data['attribute_description'] as $language_id => $value) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "attribute_description SET attribute_id = '" . (int)$data['attribute_idd'] . "', language_id = '" . (int)$language_id . "', name = '" . $this->db->escape($value['name']) . "'");
			}
		}
		
		public function deleteSellerAttribute($attribute_id) {
			$this->db->query("DELETE FROM " . DB_PREFIX . "attribute WHERE attribute_id = '" . (int)$attribute_id . "'");
			$this->db->query("DELETE FROM " . DB_PREFIX . "attribute_description WHERE attribute_id = '" . (int)$attribute_id . "'");
			$this->db->query("DELETE FROM " . DB_PREFIX . "purpletree_vendor_attribute WHERE attribute_id = '" . (int)$attribute_id . "'");
		}
		
		public function getSellerAttribute($attribute_id) {
			$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "attribute a LEFT JOIN " . DB_PREFIX . "attribute_description ad ON (a.attribute_id = ad.attribute_id) LEFT JOIN " . DB_PREFIX . "purpletree_vendor_attribute pva ON (a.attribute_id = pva.attribute_id) WHERE pva.id = '" . (int)$attribute_id . "' AND ad.language_id = '" . (int)$this->config->get('config_language_id') . "'");
			
			return $query->row;
		}
		
		public function getSellerAttributeDescriptions($id) {
			$attribute_data = array();
			
			$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "attribute_description ad LEFT JOIN " . DB_PREFIX . "purpletree_vendor_attribute pva ON (ad.attribute_id = pva.attribute_id) WHERE pva.id = '" . (int)$id . "'");
			
			foreach ($query->rows as $result) {
				$attribute_data[$result['language_id']] = array('name' => $result['name']);
			}
			
			return $attribute_data;
		}
		public function getSellerAtrributes($seller_id,$data = array()) {
			$sql = "SELECT a.*,pva.*,ad.*,agd.attribute_group_id,agd.name AS group_name FROM " . DB_PREFIX . "purpletree_vendor_attribute pva  INNER JOIN " . DB_PREFIX . "attribute a ON(pva.attribute_id=a.attribute_id) INNER JOIN " . DB_PREFIX . "attribute_description ad ON(a.attribute_id=ad.attribute_id) INNER JOIN " . DB_PREFIX . "attribute_group_description agd ON(a.attribute_group_id=agd.attribute_group_id) WHERE pva.seller_id ='".(int)$seller_id."' AND agd.language_id = '" . (int)$this->config->get('config_language_id') . "'";
			
			$sort_data = array(
			'name',
			'code',
			'discount',
			'date_start',
			'date_end',
			'status'
			);
			$sql .= " GROUP BY ad.attribute_id";
			
			if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
				$sql .= " ORDER BY " . $data['sort'];
				} else {
				$sql .= " ORDER BY ad.name";
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
		public function getOtherSellerOptions() {
			$sql = "SELECT option_id FROM " . DB_PREFIX . "purpletree_vendor_option WHERE seller_id != '".$this->customer->getId()."'";
			
			$query = $this->db->query($sql);
			if($query->num_rows){
				return $query->rows;
			}
		}	
		public function getOtherSellerAttributes() {
			$sql = "SELECT attribute_id FROM " . DB_PREFIX . "purpletree_vendor_attribute WHERE seller_id != '".$this->customer->getId()."'";
			
			$query = $this->db->query($sql);
			if($query->num_rows){
				return $query->rows;
			}
		}	
		public function getOtherSellerAttributeGroups() {
			$sql = "SELECT attribute_group_id FROM " . DB_PREFIX . "purpletree_vendor_attribute_group WHERE seller_id != '".$this->customer->getId()."'";
			
			$query = $this->db->query($sql);
			if($query->num_rows){
				return $query->rows;
			}
		}		
		public function getSellerAttributeGroups($stringgattr) {
			$sql = "SELECT * FROM " . DB_PREFIX . "attribute_group ag LEFT JOIN " . DB_PREFIX . "attribute_group_description agd ON (ag.attribute_group_id = agd.attribute_group_id) WHERE agd.language_id = '" . (int)$this->config->get('config_language_id') . "'";
			
			if($stringgattr != '') {
				$sql .=	"AND ag.attribute_group_id NOT IN (".$stringgattr.")";
			}
			
			$query = $this->db->query($sql);
			
			return $query->rows;
		}
		
		public function getCouponProducts($coupon_id) {
			$coupon_product_data = array();
			
			$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "coupon_product WHERE coupon_id = '" . (int)$coupon_id . "'");
			
			foreach ($query->rows as $result) {
				$coupon_product_data[] = $result['product_id'];
			}
			
			return $coupon_product_data;
		}
		
		public function getCouponCategories($coupon_id) {
			$coupon_category_data = array();
			
			$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "coupon_category WHERE coupon_id = '" . (int)$coupon_id . "'");
			
			foreach ($query->rows as $result) {
				$coupon_category_data[] = $result['category_id'];
			}
			
			return $coupon_category_data;
		}
		
		public function getTotalAttributes($seller_id) {
			$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "purpletree_vendor_attribute WHERE seller_id = '" . (int)$seller_id . "'");
			
			return $query->row['total'];
		}
		
		public function getCouponHistories($coupon_id, $start = 0, $limit = 10) {
			if ($start < 0) {
				$start = 0;
			}
			
			if ($limit < 1) {
				$limit = 10;
			}
			
			$query = $this->db->query("SELECT ch.order_id, CONCAT(c.firstname, ' ', c.lastname) AS customer, ch.amount, ch.date_added FROM " . DB_PREFIX . "coupon_history ch LEFT JOIN " . DB_PREFIX . "customer c ON (ch.customer_id = c.customer_id) WHERE ch.coupon_id = '" . (int)$coupon_id . "' ORDER BY ch.date_added ASC LIMIT " . (int)$start . "," . (int)$limit);
			
			return $query->rows;
		}
		
		public function getTotalCouponHistories($coupon_id,$seller_id) {
			$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "coupon_history ch INNER JOIN " . DB_PREFIX . "purpletree_vendor_coupons pvc ON (ch.coupon_id = pvc.coupon_id) WHERE pvc.coupon_id = '" . (int)$coupon_id . "' AND pvc.seller_id = '" . (int)$seller_id . "'");
			
			return $query->row['total'];
		}
}
?>