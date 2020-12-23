<?php
class ControllerExtensionAccountPurpletreeMultivendorApiSellerorder extends Controller{
		private $error = array();
		
		public function index(){
			$this->checkPlugin();
			$this->load->language('purpletree_multivendor/api');
			$json['status'] = 'error';
			if (!$this->customer->isMobileApiCall()) { 
				$json['status'] = 'error';
				$json['message'] = $this->language->get('error_permission');
				$this->response->addHeader('Content-Type: application/json');
				return $this->response->setOutput(json_encode($json));
			}
			if (!$this->customer->isLogged()) {
				$json['status'] = 'error';
				$json['message'] = $this->language->get('seller_not_logged');
				$this->response->addHeader('Content-Type: application/json');
				return $this->response->setOutput(json_encode($json));
			}
			$store_detail = $this->customer->isSeller();
			if(!isset($store_detail['store_status'])){
				$json['status'] = 'error';
				$json['message'] = $this->language->get('seller_not_approved');
				$this->response->addHeader('Content-Type: application/json');
				return $this->response->setOutput(json_encode($json)); 
			}
			if(!$this->customer->validateSeller()) {		
				$json['status'] = 'error';
				$json['message'] = $this->language->get('error_license');
				$this->response->addHeader('Content-Type: application/json');
				return $this->response->setOutput(json_encode($json));
			} 
			$this->load->language('purpletree_multivendor/sellerorder');
			
			$this->load->model('extension/purpletree_multivendor/sellerorder');
			
			$json['data']['seller_orders'] = array();
			
			if (isset($this->request->get['filter_date_from'])) {
				$filter_date_from = $this->request->get['filter_date_from'];
				} else {
				$end_date = date('Y-m-d', strtotime("-30 days"));
				$filter_date_from = $end_date;
			}
			
			if (isset($this->request->get['filter_date_to'])) {
				$filter_date_to = $this->request->get['filter_date_to'];
				} else {
				$end_date = date('Y-m-d');
				$filter_date_to = $end_date;
			}
			if (isset($this->request->get['page'])) {
				$page = $this->request->get['page'];
				} else {
				$page = 1;
			} 
			if (isset($this->session->data['error_warning'])) {
				$json['status'] = 'error';
				$json['message'] = $this->error['error_warning'];
				return $json;
				unset($this->session->data['error_warning']);
			}
			$filter_data = array(
			'filter_order_status'  => null,
			'filter_admin_order_status'  => null,
			'filter_date_from'    => $filter_date_from,
			'filter_date_to' => $filter_date_to,
			'start'                => ($page - 1) * 4,
			'limit'                => 4,
			'seller_id'            => $this->customer->getId()
			);
			$seller_id = $this->customer->getId();
			
			$json['data']['total_sale'] = 0;
			$json['data']['total_pay'] = 0;
			$json['data']['total_commission'] = 0;
			
			$total_sale = 0;
			$total_commission = 0;
			$total_payable = 0;
			
			$sellerstore = $this->customer->isSeller();
			
			//$order_total = $this->model_extension_purpletree_multivendor_sellerorder->getTotalSellerOrders($filter_data);
			
			$results = $this->model_extension_purpletree_multivendor_sellerorder->getSellerOrders($filter_data);
			if(!empty($results)) {
				foreach ($results as $result) {
					$total = 0;
					$totalall = 0;
					$product_totals  = $this->model_extension_purpletree_multivendor_sellerorder->getSellerOrdersTotal($seller_id,$result['order_id']);
					if(is_array($this->model_extension_purpletree_multivendor_sellerorder->getTotalllseller($seller_id,$result['order_id']))) {
						if(isset($this->model_extension_purpletree_multivendor_sellerorder->getTotalllseller($seller_id,$result['order_id'])['total'])) {
							$totalall  = $this->model_extension_purpletree_multivendor_sellerorder->getTotalllseller($seller_id,$result['order_id'])['total'];
						}
					};
					
					if(isset($product_totals['total'])){
						$total = $product_totals['total'];
						} else {
						$total = 0;
					}
					
					$product_commission  = $this->model_extension_purpletree_multivendor_sellerorder->getSellerOrdersCommissionTotal($result['order_id'],$seller_id);
					
					$total_sale+= $total;
					$orderstatus = 0;
					if(null !== $this->config->get('module_purpletree_multivendor_commission_status')) {
						$orderstatus = $this->config->get('module_purpletree_multivendor_commission_status');
						} else {
						$data['error_warning'] = $this->language->get('module_purpletree_multivendor_commission_status_warning');
					}
					if($result['admin_order_status_idd'] == $result['seller_order_status_idd'] && $result['seller_order_status_idd'] == $orderstatus && $result['admin_order_status_idd'] == $orderstatus) {
						$total_payable += $total;
						$total_commission+= $product_commission['total_commission'];
					}
					$json['data']['seller_orders'][] = array(
					'order_id'      => $result['order_id'],
					'customer'      => $result['customer'],
					'admin_order_status'      => $result['admin_order_status'],
					'order_status'  => $result['order_status'] ? $result['order_status'] : $this->language->get('text_missing'),
					'total'         => $this->currency->format($totalall, $result['currency_code'], $result['currency_value']),
					'commission'         => $this->currency->format($product_commission['total_commission'], $result['currency_code'], $result['currency_value']),
					'date_added'    => date($this->language->get('date_format_short'), strtotime($result['date_added'])),
					'date_modified' => date($this->language->get('date_format_short'), strtotime($result['date_modified'])),
					'shipping_code' => $result['shipping_code']
					);
				} 
				$json['status'] = 'success';
				} else {
				$json['message'] = $this->language->get('no_data');
				$json['status'] = 'success';
			}
			
			if(!empty($results)){
				$json['data']['total_sale'] = $this->currency->format($total_sale, $results[0]['currency_code'], $results[0]['currency_value']);
				$json['data']['total_pay'] = $this->currency->format(($total_payable-$total_commission), $results[0]['currency_code'], $results[0]['currency_value']);
				$json['data']['total_commission'] = $this->currency->format($total_commission, $results[0]['currency_code'], $results[0]['currency_value']);
				} else {
				$curency = $this->config->get('config_currency');
				$this->load->model('extension/purpletree_multivendor/sellerpayment');
				$currency_detail = $this->model_extension_purpletree_multivendor_sellerpayment->getCurrencySymbol($curency);
				$json['data']['total_sale'] = $this->currency->format($total_sale, $currency_detail['code'], $currency_detail['value']);
				$json['data']['total_pay'] = $this->currency->format(($total_payable-$total_commission), $currency_detail['code'], $currency_detail['value']);
				$json['data']['total_commission'] = $this->currency->format($total_commission, $currency_detail['code'], $currency_detail['value']);
			}	
			
			//$json['data']['filter_order_status'] = $filter_order_status;
			//$json['data']['filter_admin_order_status'] = $filter_admin_order_status;
			$json['data']['filter_date_from'] = $filter_date_from;
			$json['data']['filter_date_to'] = $filter_date_to;
			
			//$this->load->model('extension/localisation/ptsorder_status');
			
			//$json['data']['order_statuses'] = $this->model_extension_localisation_ptsorder_status->getOrderStatuses();
			
			//$json['data']['pagination']['total'] = $order_total;
			//$json['data']['pagination']['page'] = $page;
			//$json['data']['pagination']['limit'] = $this->config->get('config_limit_admin');
			//$json['data']['results'] = sprintf($this->language->get('text_pagination'), ($order_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($order_total - $this->config->get('config_limit_admin'))) ? $order_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $order_total, ceil($order_total / $this->config->get('config_limit_admin')));
			
			
			$this->response->addHeader('Content-Type: application/json');
			return $this->response->setOutput(json_encode($json));
			
		}	
		
		public function seller_order_info(){
			$this->checkPlugin();
			$this->load->language('purpletree_multivendor/api');
			$json['status'] = 'error';
			$json['message'] = $this->language->get('no_data');
			if (!$this->customer->isMobileApiCall()) { 
				$json['status'] = 'error';
				$json['message'] = $this->language->get('error_permission');
				$this->response->addHeader('Content-Type: application/json');
				return $this->response->setOutput(json_encode($json));
			}
			if (!$this->customer->isLogged()) {
				$json['status'] = 'error';
				$json['message'] = $this->language->get('seller_not_logged');
				$this->response->addHeader('Content-Type: application/json');
				return $this->response->setOutput(json_encode($json));
			}
			
			$store_detail = $this->customer->isSeller();
			if(!isset($store_detail['store_status'])){
				$json['status'] = 'error';
				$json['message'] = $this->language->get('seller_not_approved');
				$this->response->addHeader('Content-Type: application/json');
				return $this->response->setOutput(json_encode($json)); 
			}
			if(!$this->customer->validateSeller()) {		
				$json['status'] = 'error';
				$json['message'] = $this->language->get('error_license');
				$this->response->addHeader('Content-Type: application/json');
				return $this->response->setOutput(json_encode($json));
			} 
			$this->load->language('purpletree_multivendor/sellerorder');
			
			$this->load->model('extension/purpletree_multivendor/sellerorder');
			
			if (isset($this->request->get['order_id'])) {
				$order_id = $this->request->get['order_id'];
				} else {
				$order_id = 0;
				$json['status'] = 'error';
				$json['message'] = $this->language->get('no_orderid_found');
				$this->response->addHeader('Content-Type: application/json');
				return $this->response->setOutput(json_encode($json));
			}
			
			$seller_id = $this->customer->getId();
			$json['data']['manage_order'] = $this->config->get('module_purpletree_multivendor_seller_manage_order');
			
			$order_info = $this->model_extension_purpletree_multivendor_sellerorder->getOrder($order_id,$seller_id);
			$json['data']['admin_order_status_id'] = $order_info['admin_order_status_id'];
			$orderstatus = 0;
			if(null !== $this->config->get('module_purpletree_multivendor_commission_status')) {
				$orderstatus = $this->config->get('module_purpletree_multivendor_commission_status');
				} else {
				$json['status'] = 'error';
				$json['message'] = $this->language->get('module_purpletree_multivendor_commission_status_warning');
				return $json;
			}
			$json['data']['module_purpletree_multivendor_commission_status'] = $orderstatus;
			if ($order_info) { 
				$this->load->language('purpletree_multivendor/sellerorder');
				
				$json['data']['order_id'] = $this->request->get['order_id'];
				$json['data']['seller_id'] = $this->customer->getId();
				
				$json['data']['store_id'] = $order_info['store_id'];
				$this->load->model('extension/purpletree_multivendor/vendor');
				$seller_store = $this->model_extension_purpletree_multivendor_vendor->getStoreDetail($seller_id);
				$json['data']['store_name'] = $seller_store['store_name'];
				
				if ($order_info['store_id'] == 0) {
					$json['data']['store_url'] = '';
					} else {
					$json['data']['store_url'] = $order_info['store_url'];
				}
				
				if ($order_info['invoice_no']) {
					$json['data']['invoice_no'] = $order_info['invoice_prefix'] . $order_info['invoice_no'];
					} else {
					$json['data']['invoice_no'] = '';
				}
				
				$json['data']['date_added'] = date($this->language->get('date_format_short'), strtotime($order_info['date_added']));
				
				$json['data']['firstname'] = $order_info['firstname'];
				$json['data']['lastname'] = $order_info['lastname'];
				
				
				if ($order_info['customer_id']) {
					$json['data']['customer'] = $order_info['customer_id'];
					} else {
					$json['data']['customer'] = '';
				}
				
				$this->load->model('extension/purpletree_multivendor/customer_group');
				
				$customer_group_info = $this->model_extension_purpletree_multivendor_customer_group->getCustomerGroup($order_info['customer_group_id']);
				
				if ($customer_group_info) {
					$json['data']['customer_group'] = $customer_group_info['name'];
					} else {
					$json['data']['customer_group'] = '';
				}
				
				$json['data']['email'] = $order_info['email'];
				$json['data']['telephone'] = $order_info['telephone'];
				
				$json['data']['shipping_method'] = $order_info['shipping_method'];
				$json['data']['payment_method'] = $order_info['payment_method'];
				
				// Payment Address
				if ($order_info['payment_address_format']) {
					$format = $order_info['payment_address_format'];
					} else {
					$format = '{firstname} {lastname}' . "\n" . '{company}' . "\n" . '{address_1}' . "\n" . '{address_2}' . "\n" . '{city} {postcode}' . "\n" . '{zone}' . "\n" . '{country}';
				}
				
				$find = array(
				'{firstname}',
				'{lastname}',
				'{company}',
				'{address_1}',
				'{address_2}',
				'{city}',
				'{postcode}',
				'{zone}',
				'{zone_code}',
				'{country}'
				);
				
				$replace = array(
				'firstname' => $order_info['payment_firstname'],
				'lastname'  => $order_info['payment_lastname'],
				'company'   => $order_info['payment_company'],
				'address_1' => $order_info['payment_address_1'],
				'address_2' => $order_info['payment_address_2'],
				'city'      => $order_info['payment_city'],
				'postcode'  => $order_info['payment_postcode'],
				'zone'      => $order_info['payment_zone'],
				'zone_code' => $order_info['payment_zone_code'],
				'country'   => $order_info['payment_country']
				);
				
				$json['data']['payment_address'] = base64_encode(html_entity_decode(str_replace(array("\r\n", "\r", "\n"), '<br />', preg_replace(array("/\s\s+/", "/\r\r+/", "/\n\n+/"), '<br />', trim(str_replace($find, $replace, $format)))), ENT_QUOTES, 'UTF-8')) . "\n";
				
				// Shipping Address
				if ($order_info['shipping_address_format']) {
					$format = $order_info['shipping_address_format'];
					} else {
					$format = '{firstname} {lastname}' . "\n" . '{company}' . "\n" . '{address_1}' . "\n" . '{address_2}' . "\n" . '{city} {postcode}' . "\n" . '{zone}' . "\n" . '{country}';
				}
				
				$find = array(
				'{firstname}',
				'{lastname}',
				'{company}',
				'{address_1}',
				'{address_2}',
				'{city}',
				'{postcode}',
				'{zone}',
				'{zone_code}',
				'{country}'
				);
				
				$replace = array(
				'firstname' => $order_info['shipping_firstname'],
				'lastname'  => $order_info['shipping_lastname'],
				'company'   => $order_info['shipping_company'],
				'address_1' => $order_info['shipping_address_1'],
				'address_2' => $order_info['shipping_address_2'],
				'city'      => $order_info['shipping_city'],
				'postcode'  => $order_info['shipping_postcode'],
				'zone'      => $order_info['shipping_zone'],
				'zone_code' => $order_info['shipping_zone_code'],
				'country'   => $order_info['shipping_country']
				);
				
				$json['data']['shipping_address'] = base64_encode(html_entity_decode(str_replace(array("\r\n", "\r", "\n"), '<br />', preg_replace(array("/\s\s+/", "/\r\r+/", "/\n\n+/"), '<br />', trim(str_replace($find, $replace, $format)))), ENT_QUOTES, 'UTF-8')) . "\n";
				
				// Uploaded files
				$this->load->model('tool/upload');
				
				$json['data']['products'] = array();
				
				$products = $this->model_extension_purpletree_multivendor_sellerorder->getSellerOrderProducts($this->request->get['order_id'],$this->customer->getId());
				
				$total_shipping = 0;
				$product_total = 0;
				foreach ($products as $product) {
					$option_data = array();
					
					$options = $this->model_extension_purpletree_multivendor_sellerorder->getOrderOptions($this->request->get['order_id'], $product['order_product_id']);
					
					$total_shipping += $product['shipping'];
					
					$product_total += $product['total'];
					
					foreach ($options as $option) {
						if ($option['type'] != 'file') {
							$option_data[] = array(
							'name'  => $option['name'],
							'value' => $option['value'],
							'type'  => $option['type']
							);
							} else {
							$upload_info = $this->model_tool_upload->getUploadByCode($option['value']);
							
							if ($upload_info) {
								$option_data[] = array(
								'name'  => $option['name'],
								'value' => $upload_info['name'],
								'type'  => $option['type'],
								'href'  => $this->url->link('tool/upload/download', 'code=' . $upload_info['code'], true)
								);
							}
						}
					}
					
					$json['data']['products'][] = array(
					'order_product_id' => $product['order_product_id'],
					'product_id'       => $product['product_id'],
					'name'    	 	   => $product['name'],
					'model'    		   => $product['model'],
					'option'   		   => $option_data,
					'quantity'		   => $product['quantity'],
					'shipping'		   => $this->currency->format($total_shipping, $order_info['currency_code'], $order_info['currency_value']),
					'seller_name'		=> $product['seller_name'],
					'seller_id'		=> $product['seller_id'],
					'price'    		   => $this->currency->format($product['price'] + ($this->config->get('config_tax') ? $product['tax'] : 0), $order_info['currency_code'], $order_info['currency_value']),
					'total'    		   => $this->currency->format($product['total'] + ($this->config->get('config_tax') ? ($product['tax'] * $product['quantity']) : 0), $order_info['currency_code'], $order_info['currency_value']),
					'href'     		   => $product['product_id']
					);
				}
				
				$json['data']['vouchers'] = array();
				
				$vouchers = $this->model_extension_purpletree_multivendor_sellerorder->getOrderVouchers($this->request->get['order_id']);
				
				foreach ($vouchers as $voucher) {
					$json['data']['vouchers'][] = array(
					'description' => $voucher['description'],
					'amount'      => $this->currency->format($voucher['amount'], $order_info['currency_code'], $order_info['currency_value']),
					'voucher_id'        => $voucher['voucher_id']
					);
				}
				
				$json['data']['totals'] = array();
				
				$totals = $this->model_extension_purpletree_multivendor_sellerorder->getOrderTotals($this->request->get['order_id'],$this->customer->getId());
				
				foreach ($totals as $total) {
					$json['data']['totals'][] = array(
					'title' => $total['title'],
					'text'  => $this->currency->format($total['value'], $order_info['currency_code'], $order_info['currency_value'])
					);
				}
				
				$json['data']['comment'] = nl2br($order_info['comment']);
				
				$json['data']['reward'] = $order_info['reward'];
				
				$json['data']['reward_total'] = $this->model_extension_purpletree_multivendor_sellerorder->getTotalCustomerRewardsByOrderId($this->request->get['order_id']);
				
				$json['data']['affiliate_firstname'] = $order_info['affiliate_firstname'];
				$json['data']['affiliate_lastname'] = $order_info['affiliate_lastname'];
				
				if ($order_info['affiliate_id']) {
					$json['data']['affiliate'] = $order_info['affiliate_id'];
					} else {
					$json['data']['affiliate'] = '';
				}
				
				$json['data']['commission'] = $this->currency->format($order_info['commission'], $order_info['currency_code'], $order_info['currency_value']);
				
				
				$json['data']['commission_total'] = '';
				
				$this->load->model('extension/localisation/ptsorder_status');
				
				$order_status_info = $this->model_extension_localisation_ptsorder_status->getOrderStatus($order_info['order_status_id']);
				
				if ($order_status_info) {
					$json['data']['order_status'] = $order_status_info['name'];
					} else {
					$json['data']['order_status'] = '';
				}
				
				$json['data']['order_statuses'] = $this->model_extension_localisation_ptsorder_status->getOrderStatuses();
				
				$json['data']['order_status_id'] = $order_info['order_status_id'];
				
				$json['data']['account_custom_field'] = $order_info['custom_field'];
				
				// Custom Fields
				$this->load->model('extension/purpletree_multivendor/custom_field');
				
				$json['data']['account_custom_fields'] = array();
				
				$filter_data = array(
				'sort'  => 'cf.sort_order',
				'order' => 'ASC'
				);
				
				$custom_fields = $this->model_extension_purpletree_multivendor_custom_field->getCustomFields($filter_data);
				
				foreach ($custom_fields as $custom_field) {
					if ($custom_field['location'] == 'account' && isset($order_info['custom_field'][$custom_field['custom_field_id']])) {
						if ($custom_field['type'] == 'select' || $custom_field['type'] == 'radio') {
							$custom_field_value_info = $this->model_extension_purpletree_multivendor_custom_field->getCustomFieldValue($order_info['custom_field'][$custom_field['custom_field_id']]);
							
							if ($custom_field_value_info) {
								$json['data']['account_custom_fields'][] = array(
								'name'  => $custom_field['name'],
								'value' => $custom_field_value_info['name']
								);
							}
						}
						
						if ($custom_field['type'] == 'checkbox' && is_array($order_info['custom_field'][$custom_field['custom_field_id']])) {
							foreach ($order_info['custom_field'][$custom_field['custom_field_id']] as $custom_field_value_id) {
								$custom_field_value_info = $this->model_customer_custom_field->getCustomFieldValue($custom_field_value_id);
								
								if ($custom_field_value_info) {
									$json['data']['account_custom_fields'][] = array(
									'name'  => $custom_field['name'],
									'value' => $custom_field_value_info['name']
									);
								}
							}
						}
						
						if ($custom_field['type'] == 'text' || $custom_field['type'] == 'textarea' || $custom_field['type'] == 'file' || $custom_field['type'] == 'date' || $custom_field['type'] == 'datetime' || $custom_field['type'] == 'time') {
							$json['data']['account_custom_fields'][] = array(
							'name'  => $custom_field['name'],
							'value' => $order_info['custom_field'][$custom_field['custom_field_id']]
							);
						}
						
						if ($custom_field['type'] == 'file') {
							$upload_info = $this->model_tool_upload->getUploadByCode($order_info['custom_field'][$custom_field['custom_field_id']]);
							
							if ($upload_info) {
								$json['data']['account_custom_fields'][] = array(
								'name'  => $custom_field['name'],
								'value' => $upload_info['name']
								);
							}
						}
					}
				}
				
				// Custom fields
				$json['data']['payment_custom_fields'] = array();
				
				foreach ($custom_fields as $custom_field) {
					if ($custom_field['location'] == 'address' && isset($order_info['payment_custom_field'][$custom_field['custom_field_id']])) {
						if ($custom_field['type'] == 'select' || $custom_field['type'] == 'radio') {
							$custom_field_value_info = $this->model_customer_custom_field->getCustomFieldValue($order_info['payment_custom_field'][$custom_field['custom_field_id']]);
							
							if ($custom_field_value_info) {
								$json['data']['payment_custom_fields'][] = array(
								'name'  => $custom_field['name'],
								'value' => $custom_field_value_info['name'],
								'sort_order' => $custom_field['sort_order']
								);
							}
						}
						
						if ($custom_field['type'] == 'checkbox' && is_array($order_info['payment_custom_field'][$custom_field['custom_field_id']])) {
							foreach ($order_info['payment_custom_field'][$custom_field['custom_field_id']] as $custom_field_value_id) {
								$custom_field_value_info = $this->model_customer_custom_field->getCustomFieldValue($custom_field_value_id);
								
								if ($custom_field_value_info) {
									$json['data']['payment_custom_fields'][] = array(
									'name'  => $custom_field['name'],
									'value' => $custom_field_value_info['name'],
									'sort_order' => $custom_field['sort_order']
									);
								}
							}
						}
						
						if ($custom_field['type'] == 'text' || $custom_field['type'] == 'textarea' || $custom_field['type'] == 'file' || $custom_field['type'] == 'date' || $custom_field['type'] == 'datetime' || $custom_field['type'] == 'time') {
							$json['data']['payment_custom_fields'][] = array(
							'name'  => $custom_field['name'],
							'value' => $order_info['payment_custom_field'][$custom_field['custom_field_id']],
							'sort_order' => $custom_field['sort_order']
							);
						}
						
						if ($custom_field['type'] == 'file') {
							$upload_info = $this->model_tool_upload->getUploadByCode($order_info['payment_custom_field'][$custom_field['custom_field_id']]);
							
							if ($upload_info) {
								$json['data']['payment_custom_fields'][] = array(
								'name'  => $custom_field['name'],
								'value' => $upload_info['name'],
								'sort_order' => $custom_field['sort_order']
								);
							}
						}
					}
				}
				
				// Shipping
				$json['data']['shipping_custom_fields'] = array();
				
				foreach ($custom_fields as $custom_field) {
					if ($custom_field['location'] == 'address' && isset($order_info['shipping_custom_field'][$custom_field['custom_field_id']])) {
						if ($custom_field['type'] == 'select' || $custom_field['type'] == 'radio') {
							$custom_field_value_info = $this->model_customer_custom_field->getCustomFieldValue($order_info['shipping_custom_field'][$custom_field['custom_field_id']]);
							
							if ($custom_field_value_info) {
								$json['data']['shipping_custom_fields'][] = array(
								'name'  => $custom_field['name'],
								'value' => $custom_field_value_info['name'],
								'sort_order' => $custom_field['sort_order']
								);
							}
						}
						
						if ($custom_field['type'] == 'checkbox' && is_array($order_info['shipping_custom_field'][$custom_field['custom_field_id']])) {
							foreach ($order_info['shipping_custom_field'][$custom_field['custom_field_id']] as $custom_field_value_id) {
								$custom_field_value_info = $this->model_customer_custom_field->getCustomFieldValue($custom_field_value_id);
								
								if ($custom_field_value_info) {
									$json['data']['shipping_custom_fields'][] = array(
									'name'  => $custom_field['name'],
									'value' => $custom_field_value_info['name'],
									'sort_order' => $custom_field['sort_order']
									);
								}
							}
						}
						
						if ($custom_field['type'] == 'text' || $custom_field['type'] == 'textarea' || $custom_field['type'] == 'file' || $custom_field['type'] == 'date' || $custom_field['type'] == 'datetime' || $custom_field['type'] == 'time') {
							$json['data']['shipping_custom_fields'][] = array(
							'name'  => $custom_field['name'],
							'value' => $order_info['shipping_custom_field'][$custom_field['custom_field_id']],
							'sort_order' => $custom_field['sort_order']
							);
						}
						
						if ($custom_field['type'] == 'file') {
							$upload_info = $this->model_tool_upload->getUploadByCode($order_info['shipping_custom_field'][$custom_field['custom_field_id']]);
							
							if ($upload_info) {
								$json['data']['shipping_custom_fields'][] = array(
								'name'  => $custom_field['name'],
								'value' => $upload_info['name'],
								'sort_order' => $custom_field['sort_order']
								);
							}
						}
					}
				}
				
				
				
				// Additional Tabs
				$json['data']['tabs'] = array(); 
				
				
				
			}
			
			$json['status'] = 'success';
			$this->response->addHeader('Content-Type: application/json');
			return $this->response->setOutput(json_encode($json));
		}
		
		public function history() {
			$this->checkPlugin();
			$this->load->language('purpletree_multivendor/api');
			$json['status'] = 'error';
			$json['message'] = $this->language->get('no_data');
			if (!$this->customer->isMobileApiCall()) { 
				$json['status'] = 'error';
				$json['message'] = $this->language->get('error_permission');
				$this->response->addHeader('Content-Type: application/json');
				return $this->response->setOutput(json_encode($json));
			}
			if (!$this->customer->isLogged()) {
				$json['status'] = 'error';
				$json['message'] = $this->language->get('seller_not_logged');
				$this->response->addHeader('Content-Type: application/json');
				return $this->response->setOutput(json_encode($json));
			}
			
			$store_detail = $this->customer->isSeller();
			if(!isset($store_detail['store_status'])){
				$json['status'] = 'error';
				$json['message'] = $this->language->get('seller_not_approved');
				$this->response->addHeader('Content-Type: application/json');
				return $this->response->setOutput(json_encode($json)); 
			}
			if(!$this->customer->validateSeller()) {		
				$json['status'] = 'error';
				$json['message'] = $this->language->get('error_license');
				$this->response->addHeader('Content-Type: application/json');
				return $this->response->setOutput(json_encode($json));
			} 
			$this->load->language('purpletree_multivendor/sellerorder');
			
			$json = array();
			$json['data'] = array();
			
			// Add keys for missing post vars
			$keys = array(
			'order_status_id',
			'notify',
			'override',
			'comment'
			);
			
			foreach ($keys as $key) {
				if (!isset($this->request->post[$key])) {
					$this->request->post[$key] = '';
				}
			}
			
			$this->load->model('extension/purpletree_multivendor/sellerorder');
			
			if (isset($this->request->get['order_id'])) {
				$order_id = $this->request->get['order_id'];
				} else {
				$order_id = 0;
			}
			$seller_id = $this->customer->getId();
			$order_info = $this->model_extension_purpletree_multivendor_sellerorder->getOrder($order_id,$seller_id);
			$requestjson2 = file_get_contents('php://input');
			$requestjson1 = json_decode($requestjson2, true);
			$this->request->post['order_status_id'] = $requestjson1['order_status_id'];
			$this->request->post['comment'] = $requestjson1['comment'];
			$this->request->post['notify'] = $requestjson1['notify'];
			$this->request->post['override'] = $requestjson1['override'];
			if ($order_info) {
				$this->model_extension_purpletree_multivendor_sellerorder->addOrderHistory($order_id,$seller_id, $this->request->post['order_status_id'], $this->request->post['comment'], $this->request->post['notify'], $this->request->post['override']);
				$json['status'] = 'success';
			    $json['message'] = $this->language->get('text_success');
				$this->response->addHeader('Content-Type: application/json');
		        $this->response->setOutput(json_encode($json));
				
				} else {
				$json['status'] = 'error';
			    $json['message'] = $this->language->get('seller_order_not_found');
				$this->response->addHeader('Content-Type: application/json');
				$this->response->setOutput(json_encode($json));
			}
		}
		
		    public function changeStatus() {
				$this->checkPlugin();
			$this->load->language('purpletree_multivendor/sellerorder');
			
			$this->load->model('extension/purpletree_multivendor/dashboard');
			
			$this->model_extension_purpletree_multivendor_dashboard->checkSellerApproval();
			$requestjson2 = file_get_contents('php://input');
			$this->request->post = json_decode($requestjson2, true);
			$json = array();

			$keys = array(
			'order_status_id',
			'notify',
			'override',
			'comment',
			);
			
			foreach ($keys as $key) {
				if (!isset($this->request->post[$key])) {
					$this->request->post[$key] = '';
				}
			}
			
			$this->load->model('extension/purpletree_multivendor/sellerorder');
			
			if (isset($this->request->get['id'])) {
				$order_id = $this->request->get['id'];
				} else {
				$order_id = 0;
			}
			if (isset($this->request->post['seller_id'])) {
				$seller_id = $this->request->post['seller_id'];
				} 
			$order_info = $this->model_extension_purpletree_multivendor_sellerorder->getOrder($order_id,$seller_id);
			if ($order_info) {
				$this->model_extension_purpletree_multivendor_sellerorder->addOrderHistory($order_id,$seller_id, $this->request->post['order_status_id'], $this->request->post['comment'], $this->request->post['notify'], $this->request->post['override']);
				
				$json['success'] = $this->language->get('text_success');
				$json['status'] = 'success';
				} else {
				$json['status'] = 'error';
				$json['error'] = $this->language->get('error_not_found');
			}
			$this->response->addHeader('Content-Type: application/json');
			return $this->response->setOutput(json_encode($json));
		}
		
		public function historylist() { 
			$this->checkPlugin();
			$this->load->language('purpletree_multivendor/api');
			$json['status'] = 'error';
			if (!$this->customer->isMobileApiCall()) { 
				$json['status'] = 'error';
				$json['message'] = $this->language->get('error_permission');
				$this->response->addHeader('Content-Type: application/json');
				return $this->response->setOutput(json_encode($json));
			}
			if (!$this->customer->isLogged()) {
				$json['status'] = 'error';
				$json['message'] = $this->language->get('seller_not_logged');
				$this->response->addHeader('Content-Type: application/json');
				return $this->response->setOutput(json_encode($json));
			}
			
			$store_detail = $this->customer->isSeller();
			if(!isset($store_detail['store_status'])){
				$json['status'] = 'error';
				$json['message'] = $this->language->get('seller_not_approved');
				$this->response->addHeader('Content-Type: application/json');
				return $this->response->setOutput(json_encode($json)); 
			}
			if(!$this->customer->validateSeller()) {		
				$json['status'] = 'error';
				$json['message'] = $this->language->get('error_license');
				$this->response->addHeader('Content-Type: application/json');
				return $this->response->setOutput(json_encode($json));
			} 
			/* 	if (isset($this->request->get['limit'])) {
				$limit = (int)$this->request->get['limit'];
				} else {
				$limit = 20;
			} */
			
			
			$this->load->language('purpletree_multivendor/sellerorder');
			
			/* 	if (isset($this->request->get['page'])) {
				$page = $this->request->get['page'];
				} else {
			} */
			$page = 1;
			
			$json['data']['histories'] = array();
			
			$this->load->model('extension/purpletree_multivendor/sellerorder');
			if (isset($this->request->get['order_id'])) {
				$order_id = $this->request->get['order_id'];
				} else {
				$order_id = 0;
			}
			$seller_id = $this->customer->getId();
			$results = $this->model_extension_purpletree_multivendor_sellerorder->getOrderHistories($order_id,$seller_id, ($page - 1) * 10, 10);
			
			if(!empty($results)) {
				foreach ($results as $result) {
					$json['data']['histories'][] = array(
					'notify'     => $result['notify'] ? $this->language->get('text_yes') : $this->language->get('text_no'),
					'status'     => $result['status'],
					'comment'    => nl2br($result['comment']),
					'date_added' => date($this->language->get('date_format_short'), strtotime($result['created_at']))
					);
				}
				$json['status'] = 'success';
				} else {
				$json['status'] = 'success';
				$json['message'] = $this->language->get('no_data');
			}
			//$history_total = $this->model_extension_purpletree_multivendor_sellerorder->getTotalOrderHistories($order_id,$seller_id);
			
			//$json['data']['pagination']['total'] = $history_total;
			//$json['data']['pagination']['page'] = $page;
			//$json['data']['pagination']['limit'] = $limit;
			//$json['data']['results'] = sprintf($this->language->get('text_pagination'), ($history_total) ? (($page - 1) * $limit) + 1 : 0, ((($page - 1) * $limit) > ($history_total - $limit)) ? $history_total : ((($page - 1) * $limit) + $limit), $history_total, ceil($history_total / $limit));
			
			$this->response->addHeader('Content-Type: application/json');
			$this->response->setOutput(json_encode($json));
			
		} 
		private function checkPlugin() {
			header('Access-Control-Allow-Origin:*');
			header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
			header('Access-Control-Max-Age: 286400');
			header('Access-Control-Allow-Credentials: true');
			header('Access-Control-Allow-Headers: languageid,LANGUAGEID,Languageid,purpletreemultivendor,Purpletreemultivendor,PURPLETREEMULTIVENDOR,xocmerchantid,XOCMERCHANTID,Xocmerchantid,XOCSESSION,xocsession,Xocsession,content-type,CONTENT-TYPE,Content-Type');
		}
}