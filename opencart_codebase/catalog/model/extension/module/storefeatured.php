<?php
class ModelExtensionModuleStorefeatured extends Model {
		public function getLatest() {
			$status='status';
			if($this->config->get('purpletree_multivendor_multiple_subscription_plan_active')==1){
				$status='new_status';	
			}
			$stores = array();
			$query = $this->db->query("SELECT pvs.* FROM " . DB_PREFIX . "purpletree_vendor_stores pvs LEFT JOIN " . DB_PREFIX . "purpletree_vendor_plan_subscription pvps ON (pvps.seller_id =pvs.seller_id) LEFT JOIN " . DB_PREFIX . "purpletree_vendor_seller_plan pvsp ON (pvsp.seller_id =pvs.seller_id) LEFT JOIN " . DB_PREFIX . "purpletree_vendor_plan pvp ON (pvp.plan_id =pvsp.plan_id) WHERE pvps.status_id = 1 AND pvsp.".$status."=1 AND pvp.featured_store =1 AND pvs.vacation = '0' AND pvs.multi_store_id='".(int)$this->config->get('config_store_id') ."'  GROUP BY pvs.seller_id ORDER BY pvs.sort_order "  );
			if($query->num_rows) {
				$stores = $query->rows;
			}
			return $stores;
			
		}
}