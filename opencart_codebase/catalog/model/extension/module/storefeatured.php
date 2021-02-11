<?php
class ModelExtensionModuleStorefeatured extends Model {
		public function getLatest() {
			$status='status';
			if($this->config->get('purpletree_multivendor_multiple_subscription_plan_active')==1){
				$status='new_status';	
			}
			$stores = array();
			$store_id=(int)$this->config->get('config_store_id');
			$query = $this->db->query("SELECT pvs.* FROM " . DB_PREFIX . "purpletree_vendor_stores pvs LEFT JOIN " . DB_PREFIX . "purpletree_vendor_plan_subscription pvps ON (pvps.seller_id =pvs.seller_id) LEFT JOIN " . DB_PREFIX . "purpletree_vendor_seller_plan pvsp ON (pvsp.seller_id =pvs.seller_id) LEFT JOIN " . DB_PREFIX . "purpletree_vendor_plan pvp ON (pvp.plan_id =pvsp.plan_id) WHERE pvps.status_id = 1 AND pvsp.".$status."=1 AND pvp.featured_store =1 AND pvs.vacation = '0' AND FIND_IN_SET('".$store_id."',pvs.multi_store_id) AND pvs.multi_store_id='".(int)$this->config->get('config_store_id') ."'  GROUP BY pvs.seller_id ORDER BY pvs.sort_order "  );
				
			if($query->num_rows) {
				if($this->config->get('module_purpletree_multivendor_hyperlocal_status')){
			   if((isset($this->session->data['seller_area'])&& ($this->session->data['seller_area'] > 0))){
				   $current_area = $this->session->data['seller_area'];
				   foreach($query->rows as $key => $value){
					   $assign_area = array();
					if($value['store_area'] != ''){
						$assign_area = unserialize($value['store_area']);
					}
						if(empty($assign_area) || in_array($current_area,$assign_area)){
							$stores[$key] = $value;
						}
					
				   }
			   } else {
					$stores = $query->rows;
			   }
				} else {
					$stores = $query->rows;
				}
			}
			return $stores;
			
		}
}