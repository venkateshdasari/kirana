<?php
class ModelExtensionModulePurpletreeCasellerfeatured extends Model {
		public function getFeatured($category_id) {
		    $current_area = '';
		    $products = array();
			if($this->config->get('module_purpletree_multivendor_hyperlocal_status')){
			   if((isset($this->session->data['seller_area'])&& ($this->session->data['seller_area'] > 0))){
			        $assign_area = array();
					$current_area = $this->session->data['seller_area'];
					$query1 = $this->db->query("SELECT pvp.product_id,pvs.store_area FROM " . DB_PREFIX . "purpletree_vendor_products pvp LEFT JOIN " . DB_PREFIX . "product p ON (p.product_id = pvp.product_id) LEFT JOIN " . DB_PREFIX . "product_to_category pc ON (p.product_id = pc.product_id) LEFT JOIN " . DB_PREFIX . "purpletree_vendor_stores pvs ON (pvs.seller_id = pvp.seller_id) WHERE pvp.is_category_featured ='1' AND p.status=1 AND pc.category_id = '" . (int)$category_id . "' AND pvp.is_approved = 1 AND p.date_available <= NOW() AND p.quantity >= 1 ORDER BY RAND() DESC" );
					if($query1->num_rows){
					foreach($query1 ->rows as $key =>$value){
					if($value['store_area'] != ''){
						 $assign_area = unserialize($value['store_area']);	
						 if(in_array($current_area,$assign_area)){
						   $products[$key]=array(
						   'product_id' => $value['product_id']
						   );
						 }
					}
						}
					}
			   }else{
			     $query = $this->db->query("SELECT pvp.product_id FROM " . DB_PREFIX . "purpletree_vendor_products pvp LEFT JOIN " . DB_PREFIX . "product p ON (p.product_id = pvp.product_id) LEFT JOIN " . DB_PREFIX . "product_to_category pc ON (p.product_id = pc.product_id) WHERE pvp.is_category_featured ='1' AND p.status=1 AND pc.category_id = '" . (int)$category_id . "' AND pvp.is_approved = 1 AND p.date_available <= NOW() AND p.quantity >= 1 ORDER BY RAND() DESC" );
				if($query->num_rows) {
					$products = $query->rows;
				} 
			   }
		}else{
			$query = $this->db->query("SELECT pvp.product_id FROM " . DB_PREFIX . "purpletree_vendor_products pvp LEFT JOIN " . DB_PREFIX . "product p ON (p.product_id = pvp.product_id) LEFT JOIN " . DB_PREFIX . "product_to_category pc ON (p.product_id = pc.product_id) WHERE pvp.is_category_featured ='1' AND p.status=1 AND pc.category_id = '" . (int)$category_id . "' AND pvp.is_approved = 1 AND p.date_available <= NOW() AND p.quantity >= 1 ORDER BY RAND() DESC" );
			if($query->num_rows) {
				$products = $query->rows;
			} 
			} 			
				return $products;			
		}
}