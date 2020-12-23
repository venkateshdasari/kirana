<?php
class ModelExtensionPurpletreeMultivendorProductReturns extends Model {
		
		public function editReturn($return_id, $data) {
			$this->db->query("UPDATE `" . DB_PREFIX . "return` SET order_id = '" . (int)$data['order_id'] . "', product_id = '" . (int)$data['product_id'] . "', customer_id = '" . (int)$data['customer_id'] . "', firstname = '" . $this->db->escape($data['firstname']) . "', lastname = '" . $this->db->escape($data['lastname']) . "', email = '" . $this->db->escape($data['email']) . "', telephone = '" . $this->db->escape($data['telephone']) . "', product = '" . $this->db->escape($data['product']) . "', model = '" . $this->db->escape($data['model']) . "', quantity = '" . (int)$data['quantity'] . "', opened = '" . (int)$data['opened'] . "', return_reason_id = '" . (int)$data['return_reason_id'] . "', return_action_id = '" . (int)$data['return_action_id'] . "', comment = '" . $this->db->escape($data['comment']) . "', date_ordered = '" . $this->db->escape($data['date_ordered']) . "', date_modified = NOW() WHERE return_id = '" . (int)$return_id . "'");
		}	
		
		public function getReturn($return_id) {
			$query = $this->db->query("SELECT DISTINCT *, (SELECT CONCAT(c.firstname, ' ', c.lastname) FROM " . DB_PREFIX . "customer c WHERE c.customer_id = r.customer_id) AS customer, (SELECT rs.name FROM " . DB_PREFIX . "return_status rs WHERE rs.return_status_id = r.return_status_id AND rs.language_id = '" . (int)$this->config->get('config_language_id') . "') AS return_status FROM `" . DB_PREFIX . "return` r WHERE r.return_id = '" . (int)$return_id . "'");
			
			return $query->row;
		}
		
		public function getReturns($data = array()) {
			$sql = "SELECT *, CONCAT(r.firstname, ' ', r.lastname) AS customer, (SELECT rs.name FROM " . DB_PREFIX . "return_status rs WHERE rs.return_status_id = r.return_status_id AND rs.language_id = '" . (int)$this->config->get('config_language_id') . "') AS return_status FROM `" . DB_PREFIX . "return` r JOIN " . DB_PREFIX . "purpletree_vendor_orders pov ON (pov.product_id = r.product_id )";
			
			$implode = array();
			$implode[] = " pov.seller_id = '" . (int)$this->customer->getId(). "'";
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
			
			$sql .= " GROUP BY r.return_id ";
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
			$sql = "SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "return` r JOIN " . DB_PREFIX . "purpletree_vendor_orders pov ON (pov.product_id = r.product_id )";
			
			$implode = array();
			$implode[] = " pov.seller_id = '" . (int)$this->customer->getId(). "'";
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
			
			$sql .= " GROUP BY r.return_id ";
			$query = $this->db->query($sql);
			if(!empty($query->row)){
				return $query->num_rows;
				}else{
				return 0;
			}
			
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
			if ($notify) {
				$return_info = $this->model_extension_purpletree_multivendor_product_returns->getReturn($return_id);
				
				if ($return_info) {
					
					$this->load->language('purpletree_multivendor/product_returns');
					
					$data['return_id'] = $return_id;
					$data['date_added'] = date($this->language->get('date_format_short'), strtotime($return_info['date_modified']));
					$data['return_status'] = $return_info['return_status'];
					$data['comment'] = strip_tags(html_entity_decode($comment, ENT_QUOTES, 'UTF-8'));
					$this->load->model('extension/purpletree_multivendor/vendor');
					$admin_store_name = $this->config->get('config_name');
					// reture mail for customer
					if($data['comment'] != ''){
					        $email_code = 'product_return_status_update_mail_to_customer_with_comments';
							$register_template = $this->model_extension_purpletree_multivendor_vendor->getSelleRegisterEmailTemplate($email_code);
							$subtemplatefromdb = $register_template['new_subject'];
							$messtemplatefromdb = $register_template['new_message'];
							$replacevarsub = array('_ADMIN_STORE_NAME_' => $admin_store_name,'_RETURN_ID_' => $data['return_id']);
							$email_subject = $this->model_extension_purpletree_multivendor_vendor->getmsgfromarray($replacevarsub,$subtemplatefromdb);
							$replacevar = array('_RETURN_ID_' => $data['return_id'],
												'_RETURN_DATE_' =>$data['date_added'],
												'_RETURN_STATUS_' =>$data['return_status'],
												'_RETURN_COMMENTS_' =>$data['comment']
												);
							$email_message = $this->model_extension_purpletree_multivendor_vendor->getmsgfromarray($replacevar,$messtemplatefromdb);
							
						}else{
						    $email_code = 'product_return_status_update_mail_to_customer_without_comments';
							$register_template = $this->model_extension_purpletree_multivendor_vendor->getSelleRegisterEmailTemplate($email_code);
							$subtemplatefromdb = $register_template['new_subject'];
							$messtemplatefromdb = $register_template['new_message'];
							$replacevarsub = array('_ADMIN_STORE_NAME_' => $admin_store_name,'_RETURN_ID_' => $data['return_id']);
							$email_subject = $this->model_extension_purpletree_multivendor_vendor->getmsgfromarray($replacevarsub,$subtemplatefromdb);
							$replacevar = array('_RETURN_ID_' => $data['return_id'],
												'_RETURN_DATE_' =>$data['date_added'],
												'_RETURN_STATUS_' =>$data['return_status']
												);
							$email_message = $this->model_extension_purpletree_multivendor_vendor->getmsgfromarray($replacevar,$messtemplatefromdb);
						}
						    $reciver = $return_info['email'];
							$this->model_extension_purpletree_multivendor_vendor->ptsSendMail($reciver,$email_subject,$email_message);
						
						// reture mail alert for admin
					if($data['comment'] != ''){
					        $email_code = 'product_return_status_update_mail_to_admin_with_comments';
							$register_template = $this->model_extension_purpletree_multivendor_vendor->getSelleRegisterEmailTemplate($email_code);
							$subtemplatefromdb = $register_template['new_subject'];
							$messtemplatefromdb = $register_template['new_message'];
							$replacevarsub = array('_ADMIN_STORE_NAME_' => $admin_store_name,'_RETURN_ID_' => $data['return_id']);
							$email_subject = $this->model_extension_purpletree_multivendor_vendor->getmsgfromarray($replacevarsub,$subtemplatefromdb);
							$replacevar = array('_RETURN_ID_' => $data['return_id'],
												'_RETURN_DATE_' =>$data['date_added'],
												'_RETURN_STATUS_' =>$data['return_status'],
												'_RETURN_COMMENTS_' =>$data['comment']
												);
							$email_message = $this->model_extension_purpletree_multivendor_vendor->getmsgfromarray($replacevar,$messtemplatefromdb);
						}else{
						    $email_code = 'product_return_status_update_mail_to_admin_without_comments';
							$register_template = $this->model_extension_purpletree_multivendor_vendor->getSelleRegisterEmailTemplate($email_code);
							$subtemplatefromdb = $register_template['new_subject'];
							$messtemplatefromdb = $register_template['new_message'];
							$replacevarsub = array('_ADMIN_STORE_NAME_' => $admin_store_name,'_RETURN_ID_' => $data['return_id']);
							$email_subject = $this->model_extension_purpletree_multivendor_vendor->getmsgfromarray($replacevarsub,$subtemplatefromdb);
							$replacevar = array('_RETURN_ID_' => $data['return_id'],
												'_RETURN_DATE_' =>$data['date_added'],
												'_RETURN_STATUS_' =>$data['return_status']
												);
							$email_message = $this->model_extension_purpletree_multivendor_vendor->getmsgfromarray($replacevar,$messtemplatefromdb);
						}
						$reciver = $this->config->get('config_email');
						$this->model_extension_purpletree_multivendor_vendor->ptsSendMail($reciver,$email_subject,$email_message);					
				}
			}
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
		public function getReturnStatuses($data = array()) {
			if ($data) {
				$sql = "SELECT * FROM " . DB_PREFIX . "return_status WHERE language_id = '" . (int)$this->config->get('config_language_id') . "'";
				
				$sql .= " ORDER BY name";
				
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
				} else {
				$return_status_data = $this->cache->get('return_status.' . (int)$this->config->get('config_language_id'));
				
				if (!$return_status_data) {
					$query = $this->db->query("SELECT return_status_id, name FROM " . DB_PREFIX . "return_status WHERE language_id = '" . (int)$this->config->get('config_language_id') . "' ORDER BY name");
					
					$return_status_data = $query->rows;
					
					$this->cache->set('return_status.' . (int)$this->config->get('config_language_id'), $return_status_data);
				}
				
				return $return_status_data;
			}
		}
		public function getReturnActions($data = array()) {
			if ($data) {
				$sql = "SELECT * FROM " . DB_PREFIX . "return_action WHERE language_id = '" . (int)$this->config->get('config_language_id') . "'";
				
				$sql .= " ORDER BY name";
				
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
				} else {
				$return_action_data = $this->cache->get('return_action.' . (int)$this->config->get('config_language_id'));
				
				if (!$return_action_data) {
					$query = $this->db->query("SELECT return_action_id, name FROM " . DB_PREFIX . "return_action WHERE language_id = '" . (int)$this->config->get('config_language_id') . "' ORDER BY name");
					
					$return_action_data = $query->rows;
					
					$this->cache->set('return_action.' . (int)$this->config->get('config_language_id'), $return_action_data);
				}
				
				return $return_action_data;
			}
		}
		public function getReturnReasons($data = array()) {
			if ($data) {
				$sql = "SELECT * FROM " . DB_PREFIX . "return_reason WHERE language_id = '" . (int)$this->config->get('config_language_id') . "'";
				
				$sql .= " ORDER BY name";
				
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
				} else {
				$return_reason_data = $this->cache->get('return_reason.' . (int)$this->config->get('config_language_id'));
				
				if (!$return_reason_data) {
					$query = $this->db->query("SELECT return_reason_id, name FROM " . DB_PREFIX . "return_reason WHERE language_id = '" . (int)$this->config->get('config_language_id') . "' ORDER BY name");
					
					$return_reason_data = $query->rows;
					
					$this->cache->set('return_reason.' . (int)$this->config->get('config_language_id'), $return_reason_data);
				}
				
				return $return_reason_data;
			}
		}
		public function getProducts($data = array()) {
			$sql = "SELECT * FROM " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id)  JOIN  " . DB_PREFIX . "purpletree_vendor_orders pov ON (pov.product_id = pd.product_id ) JOIN  " . DB_PREFIX . "return r ON(pov.product_id = r.product_id) WHERE pd.language_id = '" . (int)$this->config->get('config_language_id') . "'";
			
			if (!empty($data['filter_name'])) {
				$sql .= " AND pd.name LIKE '%" . $this->db->escape($data['filter_name']) . "%'";
			}
			
			if (!empty($data['filter_model'])) {
				$sql .= " AND p.model LIKE '" . $this->db->escape($data['filter_model']) . "%'";
			}
			
			if (isset($data['filter_price']) && !is_null($data['filter_price'])) {
				$sql .= " AND p.price LIKE '" . $this->db->escape($data['filter_price']) . "%'";
			}
			
			if (isset($data['filter_quantity']) && !is_null($data['filter_quantity'])) {
				$sql .= " AND p.quantity = '" . (int)$data['filter_quantity'] . "'";
			}
			
			if (isset($data['filter_status']) && !is_null($data['filter_status'])) {
				$sql .= " AND p.status = '" . (int)$data['filter_status'] . "'";
			}
			
			if (isset($data['seller_id']) && !is_null($data['seller_id'])) {
				$sql .= " AND pov.seller_id = '" . (int)$data['seller_id'] . "'";	
			}
			
			$sql .= " GROUP BY p.product_id";
			
			$sort_data = array(
			'pd.name',
			'p.model',
			'p.price',
			'p.quantity',
			'p.status',
			'p.sort_order'
			);
			
			if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
				$sql .= " ORDER BY " . $data['sort'];
				} else {
				$sql .= " ORDER BY pd.name";
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
}