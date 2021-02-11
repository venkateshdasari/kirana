<?php
class ModelSaleReturn extends Model {
	public function addReturn($data) {
		$this->db->query("INSERT INTO `" . DB_PREFIX . "return` SET order_id = '" . (int)$data['order_id'] . "', product_id = '" . (int)$data['product_id'] . "', customer_id = '" . (int)$data['customer_id'] . "', firstname = '" . $this->db->escape($data['firstname']) . "', lastname = '" . $this->db->escape($data['lastname']) . "', email = '" . $this->db->escape($data['email']) . "', telephone = '" . $this->db->escape($data['telephone']) . "', product = '" . $this->db->escape($data['product']) . "', model = '" . $this->db->escape($data['model']) . "', quantity = '" . (int)$data['quantity'] . "', opened = '" . (int)$data['opened'] . "', return_reason_id = '" . (int)$data['return_reason_id'] . "', return_action_id = '" . (int)$data['return_action_id'] . "', return_status_id = '" . (int)$data['return_status_id'] . "', comment = '" . $this->db->escape($data['comment']) . "', date_ordered = '" . $this->db->escape($data['date_ordered']) . "', date_added = NOW(), date_modified = NOW()");
	
		return $this->db->getLastId();
	}

	public function editReturn($return_id, $data) {
		$this->db->query("UPDATE `" . DB_PREFIX . "return` SET order_id = '" . (int)$data['order_id'] . "', product_id = '" . (int)$data['product_id'] . "', customer_id = '" . (int)$data['customer_id'] . "', firstname = '" . $this->db->escape($data['firstname']) . "', lastname = '" . $this->db->escape($data['lastname']) . "', email = '" . $this->db->escape($data['email']) . "', telephone = '" . $this->db->escape($data['telephone']) . "', product = '" . $this->db->escape($data['product']) . "', model = '" . $this->db->escape($data['model']) . "', quantity = '" . (int)$data['quantity'] . "', opened = '" . (int)$data['opened'] . "', return_reason_id = '" . (int)$data['return_reason_id'] . "', return_action_id = '" . (int)$data['return_action_id'] . "', comment = '" . $this->db->escape($data['comment']) . "', date_ordered = '" . $this->db->escape($data['date_ordered']) . "', date_modified = NOW() WHERE return_id = '" . (int)$return_id . "'");
if ($this->config->get('module_purpletree_multivendor_status')) {
			  if($this->config->get('module_purpletree_multivendor_refund_status')== $data['return_action_id'] ){
			$proudct_return_statuss = '';
			$seller_id_returnn = '';
		     $query10 = $this->db->query("SELECT status FROM `" . DB_PREFIX . "purpletree_vendor_products_return` WHERE return_id = '" . (int)$return_id . "'");
				  if($query10->num_rows){
					$proudct_return_statuss = $query10->row['status'];
				  }
			
			$product_price = '';
			
			if(isset($proudct_return_statuss) && $proudct_return_statuss == 0){
			$this->db->query("UPDATE `" . DB_PREFIX . "purpletree_vendor_products_return` SET status = 1, modified_date = NOW() WHERE return_id = '" . (int)$return_id . "'");
			
			 $query1 = $this->db->query("SELECT  seller_id FROM `" . DB_PREFIX . "purpletree_vendor_products_return` WHERE return_id = '" . (int)$return_id . "'");
			if($query1->num_rows) {
				$seller_id_returnn = $query1->row['seller_id'];
			} 
			
			if($seller_id_returnn != '') {
				// vendor_order_total
				$query3 = $this->db->query("SELECT total_price FROM `" . DB_PREFIX . "purpletree_vendor_orders` WHERE order_id = '" . (int)$data['order_id'] . "' AND seller_id = '".(int)$seller_id_returnn."' AND product_id = '" . (int)$data['product_id'] . "'");
				if($query3->num_rows) {
				$product_price = -($query3->row['total_price']);
			    }
				$product_id = (int)$data['product_id'];
				$query2 = $this->db->query("SELECT order_total_id FROM `" . DB_PREFIX . "purpletree_order_total` WHERE order_id = '" . (int)$data['order_id'] . "' AND seller_id = '".(int)$seller_id_returnn."' AND code ='refunded_".$product_id."' ");
					
				if($query2->num_rows){
					$order_total = $query2->row['order_total_id'];
					
					$this->db->query("UPDATE `" . DB_PREFIX . "purpletree_order_total` SET order_id = '" . (int)$data['order_id'] . "', seller_id = '" . (int)$seller_id_returnn . "', code = 'refunded_".$product_id."', title  = 'Refunded', value = '" . (float)$product_price . "', sort_order = 8 WHERE order_total_id='".(int)$order_total ."'");
				} else {
					$this->db->query("INSERT INTO `" . DB_PREFIX . "purpletree_order_total` SET order_id = '" . (int)$data['order_id'] . "', seller_id = '" . (int)$seller_id_returnn . "', code = 'refunded_".$product_id."', title  = 'Refunded', value = '" . (float)$product_price . "', sort_order = 8");
				}
				  
					// order_total
					$query4 = $this->db->query("SELECT *  FROM `" . DB_PREFIX . "order_total` WHERE `order_id` = '" . (int)$data['order_id'] . "' AND `code` LIKE '%refunded%' ORDER BY `order_total_id`  DESC");				    
					if($query4->num_rows){
						$order_total_id = $query4->row['order_total_id'];
						$ids = explode('_',$query4->row['code']);
						
						$seller_idd = $ids[1];
						$product_id = $ids[2];
						
						$id_code = array();
						
						$id_code = array(
						'0' => 'refunded',
						'1' => $seller_id_returnn,
						'2' => $data['product_id']						
						);
						
						$id_codes = implode('_',$id_code);
                
						if($seller_id_returnn == $seller_idd && $product_id == $data['product_id']) { 
						  $this->db->query("UPDATE `" . DB_PREFIX . "order_total` SET order_id = '" . (int)$data['order_id'] . "', code = '" . (int)$ids . "', title  = 'Refunded', value = '" . (float)$product_price . "', sort_order = 8 WHERE order_total_id='".(int)$order_total ."'");
						} else {
							$this->db->query("INSERT INTO `" . DB_PREFIX . "order_total` SET order_id = '" . (int)$data['order_id'] . "', code = '" . $this->db->escape($id_codes). "', title  = 'Refunded', value = '" . (float)$product_price . "', sort_order = 8");
							
						}
					} else {
						$id_code = array();
						$id_code = array(
							'0' => 'refunded',
							'1' => $seller_id_returnn,
							'2' => $data['product_id']						
						);
						$id_codes = implode('_',$id_code);
						$this->db->query("INSERT INTO `" . DB_PREFIX . "order_total` SET order_id = '" . (int)$data['order_id'] . "', code = '" . $this->db->escape($id_codes). "', title  = 'Refunded', value = '" . (float)$product_price . "', sort_order = 8");
					}
					// order_total
			}
		 }
		}
		
		///Calculate refund total
		$seller_id_return = '';
		$proudct_return_status = '';
		$query1 = $this->db->query("SELECT  seller_id FROM `" . DB_PREFIX . "purpletree_vendor_products_return` WHERE return_id = '" . (int)$return_id . "'");
			if($query1->num_rows) {
				$seller_id_return = $query1->row['seller_id'];
			}
		$query5 = $this->db->query("SELECT status FROM `" . DB_PREFIX . "purpletree_vendor_products_return` WHERE return_id = '" . (int)$return_id . "'");
				  if($query5->num_rows){
					$proudct_return_status = $query5->row['status'];
				  }
				 
				 
		if(isset($proudct_return_status) && $proudct_return_status == 1){			
			// For vendor total 
			$total_after_refund = "";
			$order_total_sid = "";
			$total_refund = "";
			$total_value = "";
			$total_refundd = "";
			$id_codes = "";
			$order_total_id = "";
			$product_id = (int)$data['product_id'];
			$id_code = array();
						$id_code = array(
							'0' => 'refunded',
							'1' => $seller_id_return,
							'2' => $data['product_id']						
						);
			$id_codes = implode('_',$id_code);
			$query6 = $this->db->query("SELECT order_total_id, value FROM `" . DB_PREFIX . "purpletree_order_total` WHERE order_id = '" . (int)$data['order_id'] . "' AND seller_id = '".(int)$seller_id_return."' AND code ='refunded_".$product_id."' ");
					
				if($query6->num_rows){
					$order_total_sid = $query6->row['order_total_id'];
					$total_refund = $query6->row['value'];
				}
				
				$query7 = $this->db->query("SELECT order_total_id, value FROM `" . DB_PREFIX . "purpletree_order_total` WHERE order_id = '" . (int)$data['order_id'] . "' AND seller_id = '".(int)$seller_id_return."' AND code ='total' ");
					
				if($query7->num_rows){
					$order_total_id = $query7->row['order_total_id'];
					$total_value = $query7->row['value'];
				}
						
			    $total_after_refund = $total_value + $total_refund;	
			 
				$this->db->query("UPDATE `" . DB_PREFIX . "purpletree_order_total` SET value = '" . (float)$total_after_refund . "' WHERE order_total_id='". (int)$order_total_id ."'");
				
				$this->db->query("UPDATE `" . DB_PREFIX . "purpletree_vendor_orders` SET  	total_price  = '" . (int)$total_after_refund . "' WHERE order_id = '" . (int)$data['order_id'] . "' AND seller_id = '".(int)$seller_id_return."' AND product_id = '" . (int)$data['product_id'] . "'");
				
				
		    // For admin total
			$query8 = $this->db->query("SELECT order_total_id, value FROM `" . DB_PREFIX . "order_total` WHERE order_id = '" . (int)$data['order_id'] . "'  AND code ='" . $this->db->escape($id_codes) . "'");
					
				if($query8->num_rows){
					$order_total_sidd = $query8->row['order_total_id'];
					$total_refundd = $query8->row['value'];
				}
				$query9 = $this->db->query("SELECT order_total_id, value FROM `" . DB_PREFIX . "order_total` WHERE order_id = '" . (int)$data['order_id'] . "' AND code ='total' ");
					
				if($query9->num_rows){
					$order_total_idd = $query9->row['order_total_id'];
					$total_valuee = $query9->row['value'];
				}
	
				$total_after_refundd = $total_valuee + $total_refundd;				
				
				$this->db->query("UPDATE `" . DB_PREFIX . "order_total` SET value = '" . (float)$total_after_refundd . "' WHERE order_total_id='". (int)$order_total_idd ."'");	
                			
			    $this->db->query("UPDATE `" . DB_PREFIX . "purpletree_vendor_products_return` SET status = 2, modified_date = NOW() WHERE return_id = '" . (int)$return_id . "'");
		}
		}
	}

	public function deleteReturn($return_id) {
		$this->db->query("DELETE FROM `" . DB_PREFIX . "return` WHERE `return_id` = '" . (int)$return_id . "'");
		$this->db->query("DELETE FROM `" . DB_PREFIX . "return_history` WHERE `return_id` = '" . (int)$return_id . "'");
	}

	public function getReturn($return_id) {
		$query = $this->db->query("SELECT DISTINCT *, (SELECT CONCAT(c.firstname, ' ', c.lastname) FROM " . DB_PREFIX . "customer c WHERE c.customer_id = r.customer_id) AS customer, (SELECT rs.name FROM " . DB_PREFIX . "return_status rs WHERE rs.return_status_id = r.return_status_id AND rs.language_id = '" . (int)$this->config->get('config_language_id') . "') AS return_status FROM `" . DB_PREFIX . "return` r WHERE r.return_id = '" . (int)$return_id . "'");

		return $query->row;
	}

	public function getReturns($data = array()) {
		$sql = "SELECT *, CONCAT(r.firstname, ' ', r.lastname) AS customer, (SELECT rs.name FROM " . DB_PREFIX . "return_status rs WHERE rs.return_status_id = r.return_status_id AND rs.language_id = '" . (int)$this->config->get('config_language_id') . "') AS return_status FROM `" . DB_PREFIX . "return` r";

		$implode = array();

		if (!empty($data['filter_return_id'])) {
			$implode[] = "r.return_id = '" . (int)$data['filter_return_id'] . "'";
		}

		if (!empty($data['filter_order_id'])) {
			$implode[] = "r.order_id = '" . (int)$data['filter_order_id'] . "'";
		}

		if (!empty($data['filter_customer'])) {
			$implode[] = "CONCAT(r.firstname, ' ', r.lastname) LIKE '" . $this->db->escape($data['filter_customer']) . "%'";
		}

		if (!empty($data['filter_product'])) {
			$implode[] = "r.product = '" . $this->db->escape($data['filter_product']) . "'";
		}

		if (!empty($data['filter_model'])) {
			$implode[] = "r.model = '" . $this->db->escape($data['filter_model']) . "'";
		}

		if (!empty($data['filter_return_status_id'])) {
			$implode[] = "r.return_status_id = '" . (int)$data['filter_return_status_id'] . "'";
		}

		if (!empty($data['filter_date_added'])) {
			$implode[] = "DATE(r.date_added) = DATE('" . $this->db->escape($data['filter_date_added']) . "')";
		}

		if (!empty($data['filter_date_modified'])) {
			$implode[] = "DATE(r.date_modified) = DATE('" . $this->db->escape($data['filter_date_modified']) . "')";
		}

		if ($implode) {
			$sql .= " WHERE " . implode(" AND ", $implode);
		}

		$sort_data = array(
			'r.return_id',
			'r.order_id',
			'customer',
			'r.product',
			'r.model',
			'status',
			'r.date_added',
			'r.date_modified'
		);

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];
		} else {
			$sql .= " ORDER BY r.return_id";
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

	public function getTotalReturns($data = array()) {
		$sql = "SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "return`r";

		$implode = array();

		if (!empty($data['filter_return_id'])) {
			$implode[] = "r.return_id = '" . (int)$data['filter_return_id'] . "'";
		}

		if (!empty($data['filter_customer'])) {
			$implode[] = "CONCAT(r.firstname, ' ', r.lastname) LIKE '" . $this->db->escape($data['filter_customer']) . "%'";
		}

		if (!empty($data['filter_order_id'])) {
			$implode[] = "r.order_id = '" . $this->db->escape($data['filter_order_id']) . "'";
		}

		if (!empty($data['filter_product'])) {
			$implode[] = "r.product = '" . $this->db->escape($data['filter_product']) . "'";
		}

		if (!empty($data['filter_model'])) {
			$implode[] = "r.model = '" . $this->db->escape($data['filter_model']) . "'";
		}

		if (!empty($data['filter_return_status_id'])) {
			$implode[] = "r.return_status_id = '" . (int)$data['filter_return_status_id'] . "'";
		}

		if (!empty($data['filter_date_added'])) {
			$implode[] = "DATE(r.date_added) = DATE('" . $this->db->escape($data['filter_date_added']) . "')";
		}

		if (!empty($data['filter_date_modified'])) {
			$implode[] = "DATE(r.date_modified) = DATE('" . $this->db->escape($data['filter_date_modified']) . "')";
		}

		if ($implode) {
			$sql .= " WHERE " . implode(" AND ", $implode);
		}

		$query = $this->db->query($sql);

		return $query->row['total'];
	}

	public function getTotalReturnsByReturnStatusId($return_status_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "return` WHERE return_status_id = '" . (int)$return_status_id . "'");

		return $query->row['total'];
	}

	public function getTotalReturnsByReturnReasonId($return_reason_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "return` WHERE return_reason_id = '" . (int)$return_reason_id . "'");

		return $query->row['total'];
	}

	public function getTotalReturnsByReturnActionId($return_action_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "return` WHERE return_action_id = '" . (int)$return_action_id . "'");

		return $query->row['total'];
	}
	
	public function addReturnHistory($return_id, $return_status_id, $comment, $notify) {
		$this->db->query("UPDATE `" . DB_PREFIX . "return` SET `return_status_id` = '" . (int)$return_status_id . "', date_modified = NOW() WHERE return_id = '" . (int)$return_id . "'");
		$this->db->query("INSERT INTO `" . DB_PREFIX . "return_history` SET `return_id` = '" . (int)$return_id . "', return_status_id = '" . (int)$return_status_id . "', notify = '" . (int)$notify . "', comment = '" . $this->db->escape(strip_tags($comment)) . "', date_added = NOW()");
	}

	public function getReturnHistories($return_id, $start = 0, $limit = 10) {
		if ($start < 0) {
			$start = 0;
		}

		if ($limit < 1) {
			$limit = 10;
		}

		$query = $this->db->query("SELECT rh.date_added, rs.name AS status, rh.comment, rh.notify FROM " . DB_PREFIX . "return_history rh LEFT JOIN " . DB_PREFIX . "return_status rs ON rh.return_status_id = rs.return_status_id WHERE rh.return_id = '" . (int)$return_id . "' AND rs.language_id = '" . (int)$this->config->get('config_language_id') . "' ORDER BY rh.date_added DESC LIMIT " . (int)$start . "," . (int)$limit);

		return $query->rows;
	}

	public function getTotalReturnHistories($return_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "return_history WHERE return_id = '" . (int)$return_id . "'");

		return $query->row['total'];
	}

	public function getTotalReturnHistoriesByReturnStatusId($return_status_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "return_history WHERE return_status_id = '" . (int)$return_status_id . "'");

		return $query->row['total'];
	}
}