<?php
class ModelExtensionPaymentPPAdaptive extends Model {
	
	public function getPayout_batch_id($order_id){
		$query = $this->db->query("SELECT payment_key FROM " . DB_PREFIX . "purpletree_vendor_adaptive_paykey WHERE order_id = '" . (int)$order_id . "'");
			if($query->num_rows>0){
				return $query->row['payment_key'];
			}
		}
	public function getpayoutcomm($order_id,$seller_id){
		$query = $this->db->query("SELECT pvcii.link_id as invoice_id,pvcii.seller_id FROM " . DB_PREFIX . "purpletree_vendor_commission_invoice_items pvcii LEFT JOIN " . DB_PREFIX . "purpletree_vendor_commission_invoice pvci ON (pvci.id = pvcii.link_id) WHERE pvcii.order_id = '" . (int)$order_id . "' AND pvcii.seller_id = '" . (int)$seller_id . "'");
			if($query->num_rows>0){
				return $query->rows;
			}
		}	
}
?>