<?php
class ModelExtensionPurpletreeMultivendorSellerarea extends Model {
		public function addSellerArea($data) {
			$this->db->query("INSERT INTO " . DB_PREFIX . "purpletree_vendor_area SET sort_order = '" . (int)$data['sort_order'] . "', status = '" . (int)$data['status'] . "'");
			$area_id = $this->db->getLastId();
			
			foreach ($data['sellerarea'] as $language_id => $value) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "purpletree_vendor_area_discaription SET area_id = '" . (int)$area_id . "',language_id = '" . (int)$language_id . "', area_name = '" . $this->db->escape($value['name']) . "'");
			}
		}
		
		
		public function editSellerArea($area_id,$data) {
			$this->db->query("UPDATE " . DB_PREFIX . "purpletree_vendor_area SET  sort_order = '" . (int)$data['sort_order'] . "', status = '" . (int)$data['status'] . "' WHERE area_id='". (int)$area_id."'");
			
			$this->db->query("DELETE FROM " . DB_PREFIX . "purpletree_vendor_area_discaription WHERE area_id = '" . (int)$area_id . "'");
			foreach ($data['sellerarea'] as $language_id => $value) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "purpletree_vendor_area_discaription SET area_id = '" . (int)$area_id . "',language_id = '" . (int)$language_id . "', area_name = '" . $this->db->escape($value['name']) . "'");
			}
		}
		
		
		
		public function getSellerAreas($data = array()) {			
			
			$sql="SELECT pva.*,pvad.area_name FROM ". DB_PREFIX ."purpletree_vendor_area pva LEFT JOIN ". DB_PREFIX ."purpletree_vendor_area_discaription pvad ON (pva.area_id=pvad.area_id) WHERE pvad.language_id='".(int)$this->config->get('config_language_id') ."'";
			
			
			$query = $this->db->query($sql);
			
			return $query->rows;
		}
		
		
		public function getSellerAreaDescriptions($area_id) {
			$seller_area_description_data = array();
			
			$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "purpletree_vendor_area_discaription WHERE area_id = '" . (int)$area_id . "'");
			
			foreach ($query->rows as $result) {
				$seller_area_description_data[$result['language_id']] = array(
				'name'             => $result['area_name']				
				);
			}
			
			return $seller_area_description_data;
		}
		
		public function getArea($area_id) {
			$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "purpletree_vendor_area WHERE area_id = '" . (int)$area_id . "'");
			
			return $query->row;
		}
		public function deleteSellerArea($area_id) {
			$this->db->query("DELETE FROM " . DB_PREFIX . "purpletree_vendor_area_discaription WHERE area_id = '" . (int)$area_id . "'");
			$this->db->query("DELETE FROM " . DB_PREFIX . "purpletree_vendor_area WHERE area_id = '" . (int)$area_id . "'");		
			
		}
		public function checkSellerArea($area_id) {
			//$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "purpletree_vendor_seller_plan WHERE area_id = '" . (int)$area_id . "'");
			
			if($query->rows){
				return $query->row;
				}else{
				return NULL;
			}
		}
		
		
}