<?php
class ModelExtensionModuleSellerfeatured extends Model {
		public function getFeatured() {
		    $current_area = '';
			$products = array();
			if($this->config->get('module_purpletree_multivendor_hyperlocal_status')){
			   if((isset($this->session->data['seller_area'])&& ($this->session->data['seller_area'] > 0))){
			        $assign_area = array();
					$current_area = $this->session->data['seller_area'];
					$store_id=(int)$this->config->get('config_store_id');
					$query1 = $this->db->query("SELECT pvp.product_id,pvs.store_area FROM " . DB_PREFIX . "purpletree_vendor_products pvp LEFT JOIN " . DB_PREFIX . "product p ON (p.product_id = pvp.product_id) LEFT JOIN " . DB_PREFIX . "purpletree_vendor_stores pvs ON (pvs.seller_id = pvp.seller_id) WHERE pvp.is_featured ='1' AND FIND_IN_SET('".$store_id."',pvs.multi_store_id) AND p.status=1 AND pvp.is_approved = 1 AND p.date_available <= NOW() AND p.quantity >= 1 ORDER BY RAND() DESC " );
					if($query1->num_rows){
					foreach($query1 ->rows as $key =>$value){
						$assign_area = array();
					if($value['store_area'] != ''){
						$assign_area = unserialize($value['store_area']);
					}
						 if(empty($assign_area) || in_array($current_area,$assign_area)){
						   $products[$key]=array(
						   'product_id' => $value['product_id']
						   );
						 }
					
						}
					}
			   }else{
			     $query = $this->db->query("SELECT pvp.product_id FROM " . DB_PREFIX . "purpletree_vendor_products pvp LEFT JOIN " . DB_PREFIX . "product p ON (p.product_id = pvp.product_id) WHERE pvp.is_featured ='1' AND p.status=1 AND pvp.is_approved = 1 AND p.date_available <= NOW() AND p.quantity >= 1 ORDER BY RAND() DESC " );
				 if($query->num_rows) {
					$products = $query->rows;
				 }
			   }
			}else{
			$query = $this->db->query("SELECT pvp.product_id FROM " . DB_PREFIX . "purpletree_vendor_products pvp LEFT JOIN " . DB_PREFIX . "product p ON (p.product_id = pvp.product_id) WHERE pvp.is_featured ='1' AND p.status=1 AND pvp.is_approved = 1 AND p.date_available <= NOW() AND p.quantity >= 1 ORDER BY RAND() DESC " );
			if($query->num_rows) {
				$products = $query->rows;
			}
			}
			return $products;
		}
}