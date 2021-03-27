<?php
class ModelExtensionCatalogOverride extends Model {

		public function sellerMinOrderFreeShipping($seller_ids) {
		    $id_array = implode("','",$seller_ids);
            $min_shipping_info = [];
            $seller_sql = "SELECT c.*,pvs.seller_id, pvs.store_name FROM " . DB_PREFIX . "customer c JOIN " . DB_PREFIX . "purpletree_vendor_stores pvs  ON(pvs.seller_id=c.customer_id)  WHERE seller_id IN ('".$id_array."')";

            $custom_field_sql = "SELECT cf.* FROM " . DB_PREFIX . "custom_field cf JOIN " . DB_PREFIX . "custom_field_description cfd  ON(cf.custom_field_id=cfd.custom_field_id)  WHERE cfd.name ='min_order_free_shipping'";
            $query = $this->db->query($custom_field_sql);
            $shipping_setting = $query->row;
            if(!$shipping_setting){
                return $min_shipping_info;
            }
            $query = $this->db->query($seller_sql);
            if ($query->num_rows) {
                foreach ($query->rows as $row) {
                    $minimum_order_value = json_decode($row['custom_field'], true)[$shipping_setting['custom_field_id']];

                    $row['minimum_order_value'] = $minimum_order_value ? $minimum_order_value : $shipping_setting['value'];
                    $min_shipping_info[$row['seller_id']] = $row;
                }
            }
                return $min_shipping_info;
        }
}