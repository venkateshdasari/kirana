<?php
class ControllerExtensionModulePurpletreeMultivendor extends Controller {
		private $error = array();
		
		public function index() {
			$this->load->language('extension/module/purpletree_multivendor');
			$this->document->addStyle('view/javascript/purpletreecss/commonstylesheet.css');
			$this->document->setTitle($this->language->get('heading_title'));
			$data['version'] = "Version 3.16.10";
			$this->load->model('setting/setting'); 
			$this->load->model('localisation/order_status');
			$data['order_statuses'] = array();
			$data['order_statuses1'] = $this->model_localisation_order_status->getOrderStatuses();
			
			$dashboard_icons=array(
				'orders_icons'	        =>'Orders',
				'messages_icons'          =>'Admin Messages',
				'enquiries_icons'         =>'Customer Enquiries',
				'plan_icons'	           =>'Subscription Plan',
				'reviews_icons'           =>'My reviews',
				'downloads_icons'         =>'Downloads',
				'view_store_icons'        =>'View Store',
				'store_information_icons' =>'Store Information',
				'products_icons'          =>'Products',
				'blog_icons'	          =>'Blog',
				'blog_comments_icons'     =>'Blog Comments',
				'product_temp_icons'      =>'Product Template',
				'upload_icons'            =>'Bulk Product Upload',
				'payments_icons'	       =>'Payments',
				'commissions_icons'       =>'Commissions',
				'comm_invoices_icons'     =>'Commission Invoices',
				'shipping_rates_icons'	  =>'Shipping Rates',
				'returns_icons'           =>'Returns',
				'sub_invoice_icons'       =>'Subscription Invoice',
				'seller_coupons_icons'    =>'Seller Coupons',
				'summary_icons'           =>'Summary',
				'remove_as_seller_icons'  =>'Remove as a seller',
				'sales_icons'             =>'Sales',
				'dashboard_iconss'         =>'Dashboard',
				'pass_icons'              =>'Password',
				'catalog_icons'           =>'Catalog',
				'log_icons_icons'               =>'Logout',
				'admin_approval_icons'    =>'Admin Seller Approval'
 				);
				foreach($dashboard_icons as $key=>$value){
				        
				   $all_icons[]=array(
								'id'=>$key
							   );
				  
				}
			  foreach($all_icons as $key=>$allow_icon_status){
			      $all_selected_icone[$key]= $allow_icon_status['id'];
			  }
			  
			foreach($data['order_statuses1'] as $ordersstatus) {
				if($ordersstatus['name'] != 'Canceled' && $ordersstatus['name'] != 'Canceled Reversal' &&  $ordersstatus['name'] != 'Chargeback' && $ordersstatus['name'] != 'Denied' && $ordersstatus['name'] != 'Expired' && $ordersstatus['name'] != 'Failed' && $ordersstatus['name'] != 'Refunded' && $ordersstatus['name'] != 'Reversed' && $ordersstatus['name'] != 'Voided' ) {
					$data['order_statuses'][] = array(
					'order_status_id' => $ordersstatus['order_status_id'],
					'name' => $ordersstatus['name']
					);
				}
			}
			///////refund
			$data['return_actions'] = array();			
			$this->load->model('localisation/return_action');
			$data['return_actions'] = $this->model_localisation_return_action->getReturnActions();
			///////refund
			/* if (($this->request->server['REQUEST_METHOD'] == 'POST')) {
				echo "<pre>";
				print_r($this->request->post);				
				echo "</pre>";				
				die;
			}  */
			
			foreach($data['order_statuses1'] as $ordersstatus) {
			  $order_status[]= $ordersstatus['order_status_id'];
			}
			if (($this->request->server['REQUEST_METHOD'] == 'POST')){
				if($this->validate()) {
				if(isset($this->request->post['module_purpletree_multivendor_allow_order_status'])){				      
				       $this->request->post['module_purpletree_multivendor_allow_order_status'] = serialize($this->request->post['module_purpletree_multivendor_allow_order_status']);
					   }else{
					   
						   if(null ===$this->config->get('module_purpletree_multivendor_allow_order_status')){
						
						   $this->request->post['module_purpletree_multivendor_allow_order_status'] = serialize($order_status);
						   }else{
							 $this->request->post['module_purpletree_multivendor_allow_order_status'] = array();
						   }
					   }
					    if(empty($this->request->post['module_purpletree_multivendor_icons_status'])){		   
						   if(null ===$this->config->get('module_purpletree_multivendor_icons_status')){
						    $this->request->post['module_purpletree_multivendor_icons_status'] = $all_selected_icone;
						   }else{
							 $this->request->post['module_purpletree_multivendor_icons_status'] = '';
						   }
					   }
					/* if($this->request->post['module_purpletree_multivendor_validate_text']==0 || !$this->config->get('module_purpletree_multivendor_status')){  */
					$module	    	= 'purpletree_multivendor_oc';
					
					if($_SERVER['HTTP_HOST'] == 'localhost') {
						$domain = 'http://'.$_SERVER['SERVER_NAME'].$_SERVER['PHP_SELF'];
						
						} else {
						$domain = 'http://'.$_SERVER['HTTP_HOST'];
					} 
					$valuee = $this->request->post['module_purpletree_multivendor_process_data'];
					$ip_address = $this->get_client_ip();
					$url = "https://www.process.purpletreesoftware.com/occheckdata.php";
					$handle=curl_init($url);					
					curl_setopt($handle, CURLOPT_VERBOSE, true);
					curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
					curl_setopt($handle, CURLOPT_SSL_VERIFYPEER, false);
					curl_setopt($handle, CURLOPT_POSTFIELDS,
					"process_data=$valuee&domain_name=$domain&ip_address=$ip_address&module_name=$module");
					$result = curl_exec($handle);
					$result1 = json_decode($result);
					if(curl_error($handle))
					{
						echo 'error';
						die;
					}
					$ip_a = $_SERVER['HTTP_HOST'];
					////$ip_a = '122.177.1.229';
					if ($result1->status == 'success') {
						if (preg_match('(localhost|demo|test)',$domain)) {
							$str = 'qtriangle.in';
							$this->request->post['module_purpletree_multivendor_encypt_text'] = md5($str);
							$this->request->post['module_purpletree_multivendor_live_validate_text']=0;
							} elseif(str_replace(array(':', '.'), '', $ip_a)) {
							if(is_numeric($ip_a)){
								$str = 'qtriangle.in';
								$this->request->post['module_purpletree_multivendor_encypt_text'] = md5($str);
								$this->request->post['module_purpletree_multivendor_live_validate_text']=0;
							}
							}  else {
							$this->request->post['module_purpletree_multivendor_encypt_text'] = md5($domain);
							$this->request->post['module_purpletree_multivendor_live_validate_text']=1;
						}
						$this->request->post['module_purpletree_multivendor_validate_text']=1;
						$this->model_setting_setting->editSetting('module_purpletree_multivendor', $this->request->post);
						
						$this->session->data['success'] = $this->language->get('text_success');
						} else {
						$this->session->data['warning'] = $this->language->get('text_license_error');
					} 
					/* } else {
						$this->model_setting_setting->editSetting('module_purpletree_multivendor', $this->request->post);
						
						$this->session->data['success'] = $this->language->get('text_success');
					}  */
					
					if(isset($this->request->post['module_purpletree_multivendor_status'])) {
						
						$check_price_extension = $this->db->query("SELECT `code` FROM " . DB_PREFIX . "extension WHERE code = 'purpletree_sellerprice'");	
						if($check_price_extension->num_rows){  } else {
							$this->db->query("INSERT INTO " . DB_PREFIX . "extension SET type = 'module', code = 'purpletree_sellerprice'");
						}
						$check_price_extension2 = $this->db->query("SELECT `code` FROM " . DB_PREFIX . "setting WHERE code = 'module_purpletree_multivendor' AND `key`= 'module_purpletree_sellerprice_status'");	
						if($check_price_extension2->num_rows){
							$this->db->query("UPDATE `" . DB_PREFIX . "setting` SET `value` = '1' WHERE code = 'module_purpletree_multivendor' AND `key`= 'module_purpletree_sellerprice_status'");
							} else {
							
							$this->db->query("INSERT INTO `" . DB_PREFIX . "setting` (`setting_id`, `store_id`, `code`, `key`, `value`, `serialized`) VALUES (NULL, '0', 'module_purpletree_multivendor', 'module_purpletree_sellerprice_status', '1', '0')");
						}
						$check_price_extension3 = $this->db->query("Select layout_id FROM " . DB_PREFIX . "layout WHERE name = 'Product'");
						if($check_price_extension3->num_rows) { 
							
							$check_price_extension4 = $this->db->query("Select `code` FROM " . DB_PREFIX . "layout_module WHERE code = 'purpletree_sellerprice'");
							if($check_price_extension4->num_rows) { } else {
								$this->db->query("INSERT INTO " . DB_PREFIX . "layout_module SET layout_id = '".$check_price_extension3->row['layout_id']."', code = 'purpletree_sellerprice', position = 'column_right', sort_order = '-1'");
							}
						}
					    $check_sellerdetail_extension = $this->db->query("SELECT `code` FROM " . DB_PREFIX . "extension WHERE code = 'purpletree_sellerdetail'");	
						if($check_sellerdetail_extension->num_rows){  } else {
							$this->db->query("INSERT INTO " . DB_PREFIX . "extension SET type = 'module', code = 'purpletree_sellerdetail'");
						}
						$check_sellerdetail_extension2 = $this->db->query("SELECT `code` FROM " . DB_PREFIX . "setting WHERE code = 'module_purpletree_multivendor' AND `key`= 'module_purpletree_sellerdetail_status'");	
						if($check_sellerdetail_extension2->num_rows){
							$this->db->query("UPDATE `" . DB_PREFIX . "setting` SET `value` = '1' WHERE code = 'module_purpletree_multivendor' AND `key`= 'module_purpletree_sellerdetail_status'");
							} else {
							
							$this->db->query("INSERT INTO `" . DB_PREFIX . "setting` (`setting_id`, `store_id`, `code`, `key`, `value`, `serialized`) VALUES (NULL, '0', 'module_purpletree_multivendor', 'module_purpletree_sellerdetail_status', '1', '0')");
						}
						$check_sellerdetail_extension3 = $this->db->query("Select layout_id FROM " . DB_PREFIX . "layout WHERE name = 'Product'");
						if($check_sellerdetail_extension3->num_rows) { 
							
							$check_sellerdetail_extension4 = $this->db->query("Select `code` FROM " . DB_PREFIX . "layout_module WHERE code = 'purpletree_sellerdetail'");
							if($check_sellerdetail_extension4->num_rows) { } else {
								$this->db->query("INSERT INTO " . DB_PREFIX . "layout_module SET layout_id = '".$check_sellerdetail_extension3->row['layout_id']."', code = 'purpletree_sellerdetail', position = 'column_right', sort_order = '-1'");
							}
						}	
					}
					$this->response->redirect($this->url->link('extension/module/purpletree_multivendor', 'user_token=' . $this->session->data['user_token'] . '&type=module', true));
					
					} else {
					$this->error['warning'] = $this->language->get('form_error_warning');//'Chek cthe form carefyully';
				}
			}
			
			
			
			$data['heading_title'] = $this->language->get('heading_title');
			$data['vendor_heading'] = $this->language->get('vendor_heading');
			$data['order_heading'] = $this->language->get('order_heading');
			$data['seller_product_heading'] = $this->language->get('seller_product_heading');
			$data['seller_review_heading'] = $this->language->get('seller_review_heading');
			$data['seller_email_heading'] = $this->language->get('seller_email_heading');
			$data['seller_store_heading'] = $this->language->get('seller_store_heading');
			
			$data['text_edit'] = $this->language->get('text_edit');
			$data['text_enabled'] = $this->language->get('text_enabled');
			$data['text_disabled'] = $this->language->get('text_disabled');
			$data['text_yes'] = $this->language->get('text_yes');
			$data['text_no'] = $this->language->get('text_no');
			$data['text_allowed_categories'] = $this->language->get('text_allowed_categories');
			$data['text_selected_categories'] = $this->language->get('text_selected_categories');
			$data['text_assign_categories'] = $this->language->get('text_assign_categories');
			$data['text_store_email'] = $this->language->get('text_store_email');
			$data['text_store_phone'] = $this->language->get('text_store_phone');
			$data['text_store_address'] = $this->language->get('text_store_address');
			
			$data['entry_status'] = $this->language->get('entry_status');
			$data['entry_commission'] = $this->language->get('entry_commission');
			$data['entry_commission_status'] = $this->language->get('entry_commission_status');
			$data['please_select'] = $this->language->get('please_select');
			$data['entry_seller_manage_order'] = $this->language->get('entry_seller_manage_order');
			$data['entry_customer_manage_order'] = $this->language->get('entry_customer_manage_order');//quick order
			$data['entry_seller_approval'] = $this->language->get('entry_seller_approval');
			$data['entry_product_approval'] = $this->language->get('entry_product_approval');
			$data['entry_product_edit_approval'] = $this->language->get('entry_product_edit_approval');
			$data['entry_allow_category'] = $this->language->get('entry_allow_category');
			$data['entry_seller_login'] = $this->language->get('entry_seller_login');
			$data['entry_order_approval'] = $this->language->get('entry_order_approval');
			$data['entry_allow_related'] = $this->language->get('entry_allow_related');
			$data['entry_limit_purchase'] = $this->language->get('entry_limit_purchase');
			$data['entry_allow_metals'] = $this->language->get('entry_allow_metals');
			$data['entry_seller_review'] = $this->language->get('entry_seller_review');
			$data['entry_license'] = $this->language->get('entry_license');
			$data['entry_seller_store'] = $this->language->get('entry_seller_store');
			$data['entry_seller_mode'] = $this->language->get('entry_seller_mode');
			$data['entry_seller_invoice'] = $this->language->get('entry_seller_invoice');
			$data['entry_order_id'] = $this->language->get('entry_order_id');
			$data['entry_email_id'] = $this->language->get('entry_email_id');
			$data['entry_allow_live_chat'] = $this->language->get('entry_allow_live_chat');
			$data['allow_browse_sellers'] = $this->language->get('allow_browse_sellers');
			$data['entry_refund_status'] = $this->language->get('entry_refund_status');
			
			$data['button_save'] = $this->language->get('button_save');
			$data['button_cancel'] = $this->language->get('button_cancel');
			$data['button_get_license'] = $this->language->get('change_license_key');
			if(null === $this->config->get('module_purpletree_multivendor_process_data') || $this->config->get('module_purpletree_multivendor_process_data') == '') {
				$data['button_get_license'] = $this->language->get('button_get_license');
			}
			$data['button_submit'] = $this->language->get('button_submit');
			$data['error_order_id'] = $this->language->get('error_order_id');
			$data['error_email_id'] = $this->language->get('error_email_id');
			$data['please_wait'] = $this->language->get('please_wait');
			$data['text_seller_logedin'] = $this->language->get('text_seller_logedin');
			$data['text_seller_general'] = $this->language->get('text_seller_general');
			$data['entry_seller_contact'] = $this->language->get('entry_seller_contact');
			$data['seller_contact_heading'] = $this->language->get('seller_contact_heading');
			$data['button_ok'] = $this->language->get('button_ok');
			$data['enter_license_key1'] = $this->language->get('enter_license_key1');
			$data['dont_have_lisence_key'] = $this->language->get('dont_have_lisence_key');
			$data['paypal_hosted_button_id'] = $this->language->get('paypal_hosted_button_id');
			$data['entry_featured_enabled_hide_edit'] = $this->language->get('entry_featured_enabled_hide_edit');
			$data['entry_seller_featured_product'] = $this->language->get('entry_seller_featured_product');
			$data['entry_seller_category_featured_product'] = $this->language->get('entry_seller_category_featured_product');
			///////category for single product///////
			$data['entry_seller_product_category'] = $this->language->get('entry_seller_product_category');
			$data['text_single'] = $this->language->get('text_single');
			$data['text_multiple'] = $this->language->get('text_multiple');
			/////////////////////////////
			$data['entry_shipping_commission'] = $this->language->get('entry_shipping_commission');
			$data['commission_from_seller_group'] = $this->language->get('commission_from_seller_group');
			$data['text_store_social_link'] = $this->language->get('text_store_social_link');//social link
			/////////////////Start seller Blog setting/////////////////
			$data['seller_blog_heading']    = $this->language->get('seller_blog_heading');
			$data['text_sort_order']    = $this->language->get('text_sort_order');
			$data['text_create_date_order']    = $this->language->get('text_create_date_order');
			$data['entry_seller_sort_by']    = $this->language->get('entry_seller_sort_by');
			/////////////////End  seller Blog setting/////////////////
			$data['text_seller_product_template']    = $this->language->get('text_seller_product_template');
			$data['text_allow_seller_to_reply_customer'] = $this->language->get('text_allow_seller_to_reply_customer');
			
			/// product view /////
			$data['entry_products_view'] = $this->language->get('entry_products_view');
			$data['text_list'] = $this->language->get('text_list');
			$data['text_grid'] = $this->language->get('text_grid');
			/// End product view ///// 
			
			$data['entry_seller_info_success'] = $this->language->get('entry_seller_info_success');
			if (isset($this->error['warning'])) {
				$data['error_warning'] = $this->error['warning'];
				} elseif(isset($this->session->data['warning'])){ 
				$data['error_warning'] = $this->session->data['warning'];
				unset($this->session->data['warning']);
				} else {
				$data['error_warning'] = '';
			}
			
			if(isset($this->session->data['success'])) {
				$data['success'] = $this->session->data['success'];
				unset($this->session->data['success']);
				} else {
				$data['success'] = '';
			}
			
			if (isset($this->error['commission'])) {
				$data['commission_error'] = $this->error['commission'];
			} 
			if (isset($this->error['commission_status'])) {
				$data['commission_status_error'] = $this->error['commission_status'];
			} 
			
			if (isset($this->error['shipping_commission_error'])) {
				$data['shipping_commission_error'] = $this->error['shipping_commission_error'];
			} 
			
			if (isset($this->error['product_limit'])) {
				$data['product_limit_error'] = $this->error['product_limit'];
			} 
			
			if (isset($this->error['process_data'])) {
				$data['error_warning'] = $this->error['process_data'];
			} 
			//subscription plan tax value
			if (isset($this->error['tax_name'])) {
				$data['tax_name_error'] = $this->error['tax_name'];
			} 
			if (isset($this->error['tax_value'])) {
				$data['tax_value_error'] = $this->error['tax_value'];
			} 
			if (isset($this->error['grace_period'])) {
				$data['error_grace_period'] = $this->error['grace_period'];
			} 
			
			if (isset($this->error['reminder_one_days'])) {
				$data['error_reminder_one_days'] = $this->error['reminder_one_days'];
			} 
			if (isset($this->error['reminder_two_days'])) {
				$data['error_reminder_two_days'] = $this->error['reminder_two_days'];
			} 
			if (isset($this->error['reminder_three_days'])) {
				$data['error_reminder_three_days'] = $this->error['reminder_three_days'];
			}
			if (isset($this->error['paypal_email'])) {
				$data['error_paypal_email'] = $this->error['paypal_email'];
			}
			
			
			$data['breadcrumbs'] = array();
			
			$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
			);
			
			$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_extension'),
			'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true)
			);
			
			$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('extension/module/purpletree_multivendor', 'user_token=' . $this->session->data['user_token'], true)
			);
			
			$data['action'] = $this->url->link('extension/module/purpletree_multivendor', 'user_token=' . $this->session->data['user_token'], true);
			
			$data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true);
			$data['text_normal_mode'] = $this->language->get('text_normal_mode');
			$data['text_category_mode'] = $this->language->get('text_category_mode');
			$data['text_service_mode'] = $this->language->get('text_service_mode');
			
			if (isset($this->request->post['module_purpletree_multivendor_status'])) {
				$data['module_purpletree_multivendor_status'] = $this->request->post['module_purpletree_multivendor_status'];
				} else {
				$data['module_purpletree_multivendor_status'] = $this->config->get('module_purpletree_multivendor_status');
			}
			
			if (isset($this->request->post['module_purpletree_multivendor_process_data'])) {
				$data['module_purpletree_multivendor_process_data'] = $this->request->post['module_purpletree_multivendor_process_data'];
				} else {
				$data['module_purpletree_multivendor_process_data'] = $this->config->get('module_purpletree_multivendor_process_data');
			}
			
			if (isset($this->request->post['module_purpletree_multivendor_validate_text'])) {
				$data['module_purpletree_multivendor_validate_text'] = 1;
				} else {
				$data['module_purpletree_multivendor_validate_text'] = $this->config->get('module_purpletree_multivendor_validate_text');
			}
			
			if (isset($this->request->post['module_purpletree_multivendor_live_validate_text'])) {
				$data['module_purpletree_multivendor_live_validate_text'] = 0;
				} else {
				$data['module_purpletree_multivendor_live_validate_text'] = $this->config->get('module_purpletree_multivendor_live_validate_text');
			}
			
			if (isset($this->request->post['module_purpletree_multivendor_encypt_text'])) {
				$str = 'qtriangle.in';
				$data['module_purpletree_multivendor_encypt_text'] = md5($str);
				} else {
				$data['module_purpletree_multivendor_encypt_text'] = $this->config->get('module_purpletree_multivendor_encypt_text');
			}
			if(isset($this->request->post['module_purpletree_multivendor_shippingtype'])){
				$data['module_purpletree_multivendor_shippingtype'] = $this->request->post['module_purpletree_multivendor_shippingtype'];
				} elseif(NULL !== $this->config->get('module_purpletree_multivendor_shippingtype')){
				$data['module_purpletree_multivendor_shippingtype'] = $this->config->get('module_purpletree_multivendor_shippingtype');
				} else {
				$data['module_purpletree_multivendor_shippingtype'] = "0";
			}			
			if (isset($this->request->post['module_purpletree_multivendor_commission'])) {
				$data['module_purpletree_multivendor_commission'] = $this->request->post['module_purpletree_multivendor_commission'];
				} elseif($this->config->get('module_purpletree_multivendor_commission') || $this->config->get('module_purpletree_multivendor_commission') == '0') {
				$data['module_purpletree_multivendor_commission'] = $this->config->get('module_purpletree_multivendor_commission');
				} else {
				$data['module_purpletree_multivendor_commission'] = "10";
			}
			///*** Fix commission ***///			
			$data['text_fix_commission'] = $this->language->get('text_fix_commission');
			if (isset($this->request->post['module_purpletree_multivendor_fix_commission'])) {
				$data['module_purpletree_multivendor_fix_commission'] = $this->request->post['module_purpletree_multivendor_fix_commission'];
				} elseif($this->config->get('module_purpletree_multivendor_fix_commission')){
				$data['module_purpletree_multivendor_fix_commission'] = $this->config->get('module_purpletree_multivendor_fix_commission');
				} else {
				$data['module_purpletree_multivendor_fix_commission'] = "0";
			}
			///*** End fix commission ***///
			if (isset($this->request->post['module_purpletree_multivendor_commission_status'])) {
				$data['module_purpletree_multivendor_commission_status'] = $this->request->post['module_purpletree_multivendor_commission_status'];
				} else {
				$data['module_purpletree_multivendor_commission_status'] = $this->config->get('module_purpletree_multivendor_commission_status');
			}
			
			/////////////////refund
			if (isset($this->request->post['module_purpletree_multivendor_refund_status'])) {
				$data['module_purpletree_multivendor_refund_status'] = $this->request->post['module_purpletree_multivendor_refund_status'];
				} else {
				$data['module_purpletree_multivendor_refund_status'] = $this->config->get('module_purpletree_multivendor_refund_status');
			}
			//////////////
			
			if (isset($this->request->post['module_purpletree_multivendor_seller_manage_order'])) {
				$data['module_purpletree_multivendor_seller_manage_order'] = $this->request->post['module_purpletree_multivendor_seller_manage_order'];
				} else {
				$data['module_purpletree_multivendor_seller_manage_order'] = $this->config->get('module_purpletree_multivendor_seller_manage_order');
			}
			
			///quick order
			if (defined('QUICK_ORDER') && QUICK_ORDER == 1 ){
			     $data['quick_order_check'] = QUICK_ORDER;
				}
				if (isset($this->request->post['module_purpletree_multivendor_customer_manage_order'])) {
				$data['module_purpletree_multivendor_customer_manage_order'] = $this->request->post['module_purpletree_multivendor_customer_manage_order'];
				} else {
				$data['module_purpletree_multivendor_customer_manage_order'] = $this->config->get('module_purpletree_multivendor_customer_manage_order');
				}
			$this->load->model('localisation/order_status');

		     $data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();
			 	if (isset($this->request->post['module_purpletree_multivendor_allow_order_status'])) {
				$data['allow_order_statuse'] = Unserialize($this->request->post['module_purpletree_multivendor_allow_order_status']);
				} else {
				if(empty($this->config->get('module_purpletree_multivendor_allow_order_status') )){				    
					 $data['allow_order_statuse'] = '';
					}else{						
				  $data['allow_order_statuse'] = Unserialize($this->config->get('module_purpletree_multivendor_allow_order_status'));
			    }
			}
			
			if(!empty($data['allow_order_statuse'])){
			$allow_order_statuse = array_flip($data['allow_order_statuse']);
			foreach($data['allow_order_statuse'] as $key=>$value){
				$allow_order_statuse1[$value]='selected';
			}
			}			
			foreach($data['order_statuses'] as $key => $value){
			    $allow_order_statuse4 ='';
				if(isset($allow_order_statuse1[$value['order_status_id']])){
				   $allow_order_statuse4= 'selected';
					}
			    $data['allow_order_statuse3'][] = array(
				'order_status_id' => $value['order_status_id'],
				'name' => $value['name'],
				'selected' => $allow_order_statuse4
				);
				
				}
				//echo"<pre>";print_r($data['allow_order_statuse3']);die;
			$data['all'] = 0;
			if(isset($data['allow_order_statuse'][0])){
			    $data['all'] = $data['allow_order_statuse'][0];
			}
			if (isset($this->request->post['module_purpletree_multivendor_seller_info_on_order_sucess'])) {
				$data['module_purpletree_multivendor_seller_info_on_order_sucess'] = $this->request->post['module_purpletree_multivendor_seller_info_on_order_sucess'];
				} else {
				$data['module_purpletree_multivendor_seller_info_on_order_sucess'] = $this->config->get('module_purpletree_multivendor_seller_info_on_order_sucess');
			}
			if (isset($this->request->post['module_purpletree_multivendor_quick_order_tab_position'])) {
				$data['module_purpletree_multivendor_quick_order_tab_position'] = $this->request->post['module_purpletree_multivendor_quick_order_tab_position'];
				} else {
				$data['module_purpletree_multivendor_quick_order_tab_position'] = $this->config->get('module_purpletree_multivendor_quick_order_tab_position');
			}
			if (isset($this->request->post['module_purpletree_multivendor_quick_order_api'])) {
				$data['module_purpletree_multivendor_quick_order_api'] = $this->request->post['module_purpletree_multivendor_quick_order_api'];
				} else {
				$data['module_purpletree_multivendor_quick_order_api'] = $this->config->get('module_purpletree_multivendor_quick_order_api');
			}
			/// end quick order

			           ///dashboard icons

			if (isset($this->request->post['module_purpletree_multivendor_icons_status'])) {
				$data['module_purpletree_multivendor_icons_status'] = $this->request->post['module_purpletree_multivendor_icons_status'];
				} else {
				$data['module_purpletree_multivendor_icons_status'] = $this->config->get('module_purpletree_multivendor_icons_status');
			}			 
			 	if (isset($this->request->post['module_purpletree_multivendor_icons_status'])) {
				$data['allow_icon_statuse'] =$this->request->post['module_purpletree_multivendor_icons_status'];
				} else {
                    
                     $allow_icon_statuse =$this->config->get('module_purpletree_multivendor_icons_status');
                 
                         
                         if(empty($allow_icon_statuse)){
							$allow_icon_statuse=array('no_value');
                         }	

					      foreach($dashboard_icons as $key=>$value){
				          if(in_array($key,$allow_icon_statuse)){
				              $data['allow_icon_statuse'][]=array(
				                              'id'=>$key,
				                              'value'=>$value,
				                              'selected'=>'selected'
				                           );
				          } else {
				           $data['allow_icon_statuse'][]=array(
				                            'id'=>$key,
				                            'value'=>$value,
				                            'selected'=>''
				                           );
				               }	
						    }
						}
					
	
         /// End dashboard icons section
         /// start Hyper local 

			if (isset($this->request->post['module_purpletree_multivendor_hyperlocal_status'])) {
				$data['module_purpletree_multivendor_hyperlocal_status'] = $this->request->post['module_purpletree_multivendor_hyperlocal_status'];
				}else {
				$data['module_purpletree_multivendor_hyperlocal_status'] = $this->config->get('module_purpletree_multivendor_hyperlocal_status');
			}
			
			$data['seller_area_link'] = $this->url->link('extension/purpletree_multivendor/sellerarea', 'user_token=' . $this->session->data['user_token'], true);
 
		///	end hyper local	

		/// hyper local pop up heading 

			if(isset($this->request->post['module_purpletree_multivendor_hp_heading'])){
				$data['module_purpletree_multivendor_hp_heading'] = $this->request->post['module_purpletree_multivendor_hp_heading'];
				} elseif($this->config->get('module_purpletree_multivendor_hp_heading')){
				$data['module_purpletree_multivendor_hp_heading'] = $this->config->get('module_purpletree_multivendor_hp_heading');
				} else {
				$data['module_purpletree_multivendor_hp_heading'] = $this->language->get('text_hyper_delivering');
			}

             if (isset($this->request->post['module_purpletree_multivendor_area_status'])) {
				$data['module_purpletree_multivendor_area_status'] = $this->request->post['module_purpletree_multivendor_area_status'];
				} else {
				$data['module_purpletree_multivendor_area_status'] = $this->config->get('module_purpletree_multivendor_area_status');
			}				
		///	end hyper local	pop up heading 				
			if (isset($this->request->post['module_purpletree_multivendor_seller_approval'])) {
				$data['module_purpletree_multivendor_seller_approval'] = $this->request->post['module_purpletree_multivendor_seller_approval'];
				} else {
				$data['module_purpletree_multivendor_seller_approval'] = $this->config->get('module_purpletree_multivendor_seller_approval');
			}
			
			
			if (isset($this->request->post['module_purpletree_multivendor_product_approval'])) {
				$data['module_purpletree_multivendor_product_approval'] = $this->request->post['module_purpletree_multivendor_product_approval'];
				} else {
				$data['module_purpletree_multivendor_product_approval'] = $this->config->get('module_purpletree_multivendor_product_approval');
			}
			
			if (isset($this->request->post['module_purpletree_multivendor_allow_categorytype'])) {
				$data['module_purpletree_multivendor_allow_categorytype'] = $this->request->post['module_purpletree_multivendor_allow_categorytype'];
				} else {
				$data['module_purpletree_multivendor_allow_categorytype'] = $this->config->get('module_purpletree_multivendor_allow_categorytype');
			}
			$data['module_purpletree_multivendor_allow_category1'] = array();
			
			if (isset($this->request->post['module_purpletree_multivendor_allow_category'])) {
				$data['module_purpletree_multivendor_allow_category'] = $this->request->post['module_purpletree_multivendor_allow_category'];
				$data['module_purpletree_multivendor_allow_category1'] = $this->request->post['module_purpletree_multivendor_allow_category'];
				} elseif($this->config->get('module_purpletree_multivendor_allow_category')) {
				$data['module_purpletree_multivendor_allow_category'] = $this->config->get('module_purpletree_multivendor_allow_category');
				$data['module_purpletree_multivendor_allow_category1'] = $this->config->get('module_purpletree_multivendor_allow_category');
				} else {
				$data['module_purpletree_multivendor_allow_category'] = array();
				$data['module_purpletree_multivendor_allow_category1'] = array();
			}
			$data['module_purpletree_multivendor_allow_category'] = array();
			$this->load->model('catalog/category');
			$results = $this->model_catalog_category->getCategories();
			foreach ($results as $result) {
				if($data['module_purpletree_multivendor_allow_categorytype']) {
					
					$data['module_purpletree_multivendor_allow_category'][strip_tags(html_entity_decode($result['name'], ENT_QUOTES, 'UTF-8'))] = $result['category_id'];
					} else {
					if(in_array($result['category_id'],$data['module_purpletree_multivendor_allow_category1'])) {
						$data['module_purpletree_multivendor_allow_category'][strip_tags(html_entity_decode($result['name'], ENT_QUOTES, 'UTF-8'))] = $result['category_id'];
					}
				}
			}
			
			//////category for single product///////
			if (isset($this->request->post['module_purpletree_multivendor_seller_product_category'])) {
				$data['module_purpletree_multivendor_seller_product_category'] = $this->request->post['module_purpletree_multivendor_seller_product_category'];
				} else {
				$data['module_purpletree_multivendor_seller_product_category'] = $this->config->get('module_purpletree_multivendor_seller_product_category');
			}
			////////////////////////////////////////
			if(isset($this->request->post['module_purpletree_multivendor_seller_login'])){
				$data['module_purpletree_multivendor_seller_login'] = $this->request->post['module_purpletree_multivendor_seller_login'];
				} elseif(NULL !== $this->config->get('module_purpletree_multivendor_seller_login')){
				$data['module_purpletree_multivendor_seller_login'] = $this->config->get('module_purpletree_multivendor_seller_login');
				} else {
				$data['module_purpletree_multivendor_seller_login'] = "1";
			}	
			if (isset($this->request->post['module_purpletree_multivendor_storepage_layout'])) {
				$data['module_purpletree_multivendor_storepage_layout'] = $this->request->post['module_purpletree_multivendor_storepage_layout'];
				} else {
				$data['module_purpletree_multivendor_storepage_layout'] = $this->config->get('module_purpletree_multivendor_storepage_layout');
			}	
			/// Product view ///
				if (isset($this->request->post['module_purpletree_multivendor_products_view'])) {
				$data['module_purpletree_multivendor_products_view'] = $this->request->post['module_purpletree_multivendor_products_view'];
				} else {
				$data['module_purpletree_multivendor_products_view'] = $this->config->get('module_purpletree_multivendor_products_view');
			}	
			//// end  product view ///
			if (isset($this->request->post['module_purpletree_multivendor_allow_selleron_category'])) {
				$data['module_purpletree_multivendor_allow_selleron_category'] = $this->request->post['module_purpletree_multivendor_allow_selleron_category'];
				} else {
				$data['module_purpletree_multivendor_allow_selleron_category'] = $this->config->get('module_purpletree_multivendor_allow_selleron_category');
			}
			
			$data['entry_hide_seller_detail'] = $this->language->get('entry_hide_seller_detail');
			if (isset($this->request->post['module_purpletree_multivendor_hide_seller_detail'])) {
				$data['module_purpletree_multivendor_hide_seller_detail'] = $this->request->post['module_purpletree_multivendor_hide_seller_detail'];
				} else {
				$data['module_purpletree_multivendor_hide_seller_detail'] = $this->config->get('module_purpletree_multivendor_hide_seller_detail');
			}
			if (isset($this->request->post['module_purpletree_multivendor_template_description'])) {
				$data['module_purpletree_multivendor_template_description'] = $this->request->post['module_purpletree_multivendor_template_description'];
				} else {
				$data['module_purpletree_multivendor_template_description'] = $this->config->get('module_purpletree_multivendor_template_description');
			}		
			
			if (isset($this->request->post['module_purpletree_multivendor_featured_enabled_hide_edit'])) {
				$data['module_purpletree_multivendor_featured_enabled_hide_edit'] = $this->request->post['module_purpletree_multivendor_featured_enabled_hide_edit'];
				} else {
				$data['module_purpletree_multivendor_featured_enabled_hide_edit'] = $this->config->get('module_purpletree_multivendor_featured_enabled_hide_edit');
			}		
			if (isset($this->request->post['module_purpletree_multivendor_seller_featured_products'])) {
				$data['module_purpletree_multivendor_seller_featured_products'] = $this->request->post['module_purpletree_multivendor_seller_featured_products'];
				} else {
				$data['module_purpletree_multivendor_seller_featured_products'] = $this->config->get('module_purpletree_multivendor_seller_featured_products');
			}		
			if (isset($this->request->post['module_purpletree_multivendor_seller_category_featured'])) {
				$data['module_purpletree_multivendor_seller_category_featured'] = $this->request->post['module_purpletree_multivendor_seller_category_featured'];
				} else {
				$data['module_purpletree_multivendor_seller_category_featured'] = $this->config->get('module_purpletree_multivendor_seller_category_featured');
			}
			
			if (isset($this->request->post['module_purpletree_multivendor_allow_related_product'])) {
				$data['module_purpletree_multivendor_allow_related_product'] = $this->request->post['module_purpletree_multivendor_allow_related_product'];
				} else {
				$data['module_purpletree_multivendor_allow_related_product'] = $this->config->get('module_purpletree_multivendor_allow_related_product');
			}
			
			
			if (isset($this->request->post['module_purpletree_multivendor_product_limit'])) {
				$data['module_purpletree_multivendor_product_limit'] = $this->request->post['module_purpletree_multivendor_product_limit'];
				} elseif($this->config->get('module_purpletree_multivendor_product_limit') || $this->config->get('module_purpletree_multivendor_product_limit') == '0') {
				$data['module_purpletree_multivendor_product_limit'] = $this->config->get('module_purpletree_multivendor_product_limit');
				} else {
				$data['module_purpletree_multivendor_product_limit'] = "10";
			}
			if (isset($this->request->post['module_purpletree_multivendor_allow_metals_product'])) {
				$data['module_purpletree_multivendor_allow_metals_product'] = $this->request->post['module_purpletree_multivendor_allow_metals_product'];
				} else {
				$data['module_purpletree_multivendor_allow_metals_product'] = $this->config->get('module_purpletree_multivendor_allow_metals_product');
			}
			
			if (isset($this->request->post['module_purpletree_multivendor_seller_review'])) {
				$data['module_purpletree_multivendor_seller_review'] = $this->request->post['module_purpletree_multivendor_seller_review'];
				} else {
				$data['module_purpletree_multivendor_seller_review'] = $this->config->get('module_purpletree_multivendor_seller_review');
			}
			
            //////////Seller Name/////////
			
			if (isset($this->request->post['module_purpletree_multivendor_seller_name'])) {
				$data['module_purpletree_multivendor_seller_name'] = $this->request->post['module_purpletree_multivendor_seller_name'];
				} else {
				$data['module_purpletree_multivendor_seller_name'] = $this->config->get('module_purpletree_multivendor_seller_name');
			}
			
			//////////////////////////////      		
			if (isset($this->request->post['module_purpletree_multivendor_seller_contact'])) {
				$data['module_purpletree_multivendor_seller_contact'] = $this->request->post['module_purpletree_multivendor_seller_contact'];
				} else {
				$data['module_purpletree_multivendor_seller_contact'] = $this->config->get('module_purpletree_multivendor_seller_contact');
			}
			
			if (isset($this->request->post['module_purpletree_multivendor_store_email'])) {
				$data['module_purpletree_multivendor_store_email'] = $this->request->post['module_purpletree_multivendor_store_email'];
				} else {
				$data['module_purpletree_multivendor_store_email'] = $this->config->get('module_purpletree_multivendor_store_email');
			}
			
			if (isset($this->request->post['module_purpletree_multivendor_store_phone'])) {
				$data['module_purpletree_multivendor_store_phone'] = $this->request->post['module_purpletree_multivendor_store_phone'];
				} else {
				$data['module_purpletree_multivendor_store_phone'] = $this->config->get('module_purpletree_multivendor_store_phone');
			}
			
			if (isset($this->request->post['module_purpletree_multivendor_store_address'])) {
				$data['module_purpletree_multivendor_store_address'] = $this->request->post['module_purpletree_multivendor_store_address'];
				} else {
				$data['module_purpletree_multivendor_store_address'] = $this->config->get('module_purpletree_multivendor_store_address');
			}
			/////    Store social link   //////
			
			if (isset($this->request->post['module_purpletree_multivendor_store_social_link'])) {
				$data['module_purpletree_multivendor_store_social_link'] = $this->request->post['module_purpletree_multivendor_store_social_link'];
				} else {
				$data['module_purpletree_multivendor_store_social_link'] = $this->config->get('module_purpletree_multivendor_store_social_link');
			}
			////  Store social link end //////
			
			if (isset($this->request->post['module_purpletree_multivendor_seller_invoice'])) {
				$data['module_purpletree_multivendor_seller_invoice'] = $this->request->post['module_purpletree_multivendor_seller_invoice'];
				} else {
				$data['module_purpletree_multivendor_seller_invoice'] = $this->config->get('module_purpletree_multivendor_seller_invoice');
			}
			if (isset($this->request->post['module_purpletree_multivendor_allow_live_chat'])) {
				$data['module_purpletree_multivendor_allow_live_chat'] = $this->request->post['module_purpletree_multivendor_allow_live_chat'];
				} else {
				$data['module_purpletree_multivendor_allow_live_chat'] = $this->config->get('module_purpletree_multivendor_allow_live_chat');
			}
			if(isset($this->request->post['module_purpletree_multivendor_browse_sellers'])){
				$data['module_purpletree_multivendor_browse_sellers'] = $this->request->post['module_purpletree_multivendor_browse_sellers'];
				} elseif(NULL !== $this->config->get('module_purpletree_multivendor_browse_sellers')){
				$data['module_purpletree_multivendor_browse_sellers'] = $this->config->get('module_purpletree_multivendor_browse_sellers');
				} else {
				$data['module_purpletree_multivendor_browse_sellers'] = "1";
			}
			////Hide User Menu////
			if (isset($this->request->post['module_purpletree_multivendor_hide_user_menu'])) {
				$data['module_purpletree_multivendor_hide_user_menu'] = $this->request->post['module_purpletree_multivendor_hide_user_menu'];
				} else {
				$data['module_purpletree_multivendor_hide_user_menu'] = $this->config->get('module_purpletree_multivendor_hide_user_menu');
			}
			////End Hide User Menu////
			//Start domain wise store
			if (isset($this->request->post['module_purpletree_multivendor_multi_store'])) {
				$data['module_purpletree_multivendor_multi_store'] = $this->request->post['module_purpletree_multivendor_multi_store'];
				} else {
				$data['module_purpletree_multivendor_multi_store'] = $this->config->get('module_purpletree_multivendor_multi_store');
			}
			//End domain wise store
			
			if (isset($this->request->post['module_purpletree_multivendor_seller_contact'])) {
				$data['module_purpletree_multivendor_seller_contact'] = $this->request->post['module_purpletree_multivendor_seller_contact'];
				} else {
				$data['module_purpletree_multivendor_seller_contact'] = $this->config->get('module_purpletree_multivendor_seller_contact');
			}
			//subscription plan
			
			if(isset($this->request->post['module_purpletree_multivendor_subscription_plans'])){
				$data['module_purpletree_multivendor_subscription_plans'] = $this->request->post['module_purpletree_multivendor_subscription_plans'];
				} elseif($this->config->get('module_purpletree_multivendor_subscription_plans')){
				$data['module_purpletree_multivendor_subscription_plans'] = $this->config->get('module_purpletree_multivendor_subscription_plans');
				} else {
				$data['module_purpletree_multivendor_subscription_plans'] = "0";
			}
			if(isset($this->request->post['module_purpletree_multivendor_paypal_email'])){
				$data['module_purpletree_multivendor_paypal_email'] = $this->request->post['module_purpletree_multivendor_paypal_email'];
				} else{
				$data['module_purpletree_multivendor_paypal_email'] = $this->config->get('module_purpletree_multivendor_paypal_email');
			}
			
			if(isset($this->request->post['module_purpletree_multivendor_subscription_price'])){
				$data['module_purpletree_multivendor_subscription_price'] = $this->request->post['module_purpletree_multivendor_subscription_price'];
				} else{
				$data['module_purpletree_multivendor_subscription_price'] = $this->config->get('module_purpletree_multivendor_subscription_price');
			}
			if(isset($this->request->post['module_purpletree_multivendor_joining_fees'])){
				$data['module_purpletree_multivendor_joining_fees'] = $this->request->post['module_purpletree_multivendor_joining_fees'];
				} else{
				$data['module_purpletree_multivendor_joining_fees'] = $this->config->get('module_purpletree_multivendor_joining_fees');
			}
			if(isset($this->request->post['module_purpletree_multivendor_tax_name'])){
				$data['module_purpletree_multivendor_tax_name'] = $this->request->post['module_purpletree_multivendor_tax_name'];
				} elseif($this->config->get('module_purpletree_multivendor_tax_name') || $this->config->get('module_purpletree_multivendor_tax_name') == '0'){
				$data['module_purpletree_multivendor_tax_name'] = $this->config->get('module_purpletree_multivendor_tax_name');
				} else {
				$data['module_purpletree_multivendor_tax_name'] = "Tax";
			}
			if(isset($this->request->post['module_purpletree_multivendor_tax_value'])){
				$data['module_purpletree_multivendor_tax_value'] = $this->request->post['module_purpletree_multivendor_tax_value'];
				} elseif($this->config->get('module_purpletree_multivendor_tax_value') || $this->config->get('module_purpletree_multivendor_tax_value') == '0'){
				$data['module_purpletree_multivendor_tax_value'] = $this->config->get('module_purpletree_multivendor_tax_value');
				} else {
				$data['module_purpletree_multivendor_tax_value'] = "0";
			}
			
			if(isset($this->request->post['module_purpletree_multivendor_reminder_one_days'])){
				$data['module_purpletree_multivendor_reminder_one_days'] = $this->request->post['module_purpletree_multivendor_reminder_one_days'];
				} elseif($this->config->get('module_purpletree_multivendor_reminder_one_days') || $this->config->get('module_purpletree_multivendor_reminder_one_days') == '0'){
				$data['module_purpletree_multivendor_reminder_one_days'] = $this->config->get('module_purpletree_multivendor_reminder_one_days');
				} else {
				$data['module_purpletree_multivendor_reminder_one_days'] = "10";
			}
			
			if(isset($this->request->post['module_purpletree_multivendor_reminder_two_days'])){
				$data['module_purpletree_multivendor_reminder_two_days'] = $this->request->post['module_purpletree_multivendor_reminder_two_days'];
				} elseif($this->config->get('module_purpletree_multivendor_reminder_two_days') || $this->config->get('module_purpletree_multivendor_reminder_two_days') == '0'){
				$data['module_purpletree_multivendor_reminder_two_days'] = $this->config->get('module_purpletree_multivendor_reminder_two_days');
				} else {
				$data['module_purpletree_multivendor_reminder_two_days'] = "5";
			}
			if(isset($this->request->post['module_purpletree_multivendor_reminder_three_days'])){
				$data['module_purpletree_multivendor_reminder_three_days'] = $this->request->post['module_purpletree_multivendor_reminder_three_days'];
				} elseif($this->config->get('module_purpletree_multivendor_reminder_three_days') || $this->config->get('module_purpletree_multivendor_reminder_three_days') == '0'){
				$data['module_purpletree_multivendor_reminder_three_days'] = $this->config->get('module_purpletree_multivendor_reminder_three_days');
				} else {
				$data['module_purpletree_multivendor_reminder_three_days'] = "1";
			}
			if(isset($this->request->post['module_purpletree_multivendor_grace_period'])){
				$data['module_purpletree_multivendor_grace_period'] = $this->request->post['module_purpletree_multivendor_grace_period'];
				} elseif($this->config->get('module_purpletree_multivendor_grace_period') || $this->config->get('module_purpletree_multivendor_grace_period') == '0'){
				$data['module_purpletree_multivendor_grace_period'] = $this->config->get('module_purpletree_multivendor_grace_period');
				} else {
				$data['module_purpletree_multivendor_grace_period'] = "3";
			}
			if (isset($this->request->post['module_purpletree_multivendor_shipping_commission'])) {
				$data['module_purpletree_multivendor_shipping_commission'] = $this->request->post['module_purpletree_multivendor_shipping_commission'];
				} elseif($this->config->get('module_purpletree_multivendor_shipping_commission')  || $this->config->get('module_purpletree_multivendor_shipping_commission') == '0') {
				$data['module_purpletree_multivendor_shipping_commission'] = $this->config->get('module_purpletree_multivendor_shipping_commission');
				} else {
				$data['module_purpletree_multivendor_shipping_commission'] = "0";
			}
			if (isset($this->request->post['module_purpletree_multivendor_seller_group'])) {
				$data['module_purpletree_multivendor_seller_group'] = $this->request->post['module_purpletree_multivendor_seller_group'];
				} else {
				$data['module_purpletree_multivendor_seller_group'] = $this->config->get('module_purpletree_multivendor_seller_group');
			}
			if (isset($this->request->post['module_purpletree_multivendor_footer_text'])) {
				$data['module_purpletree_multivendor_footer_text'] = $this->request->post['module_purpletree_multivendor_footer_text'];
				} else {
				$data['module_purpletree_multivendor_footer_text'] = $this->config->get('module_purpletree_multivendor_footer_text');
			}
			
			///// development option //////
			if(isset($this->request->post['module_purpletree_multivendor_include_jquery'])){
				$data['module_purpletree_multivendor_include_jquery'] = $this->request->post['module_purpletree_multivendor_include_jquery'];
				} elseif(NULL !== $this->config->get('module_purpletree_multivendor_include_jquery')){
				$data['module_purpletree_multivendor_include_jquery'] = $this->config->get('module_purpletree_multivendor_include_jquery');
				} else {
				$data['module_purpletree_multivendor_include_jquery'] = "1";
			}
			///// development option //////
			
			//// Hide seller product tab ////
			$data['text_hide_seller_product_tab'] = $this->language->get('text_hide_seller_product_tab');
			if (isset($this->request->post['module_purpletree_multivendor_hide_seller_product_tab'])) {
				$data['module_purpletree_multivendor_hide_seller_product_tab'] = $this->request->post['module_purpletree_multivendor_hide_seller_product_tab'];
				} else {
				$data['module_purpletree_multivendor_hide_seller_product_tab'] = $this->config->get('module_purpletree_multivendor_hide_seller_product_tab');
			}
			//// End Hide seller product tab ////
			if (isset($this->request->post['module_purpletree_multivendor_seller_product_template'])) {
				$data['module_purpletree_multivendor_seller_product_template'] = $this->request->post['module_purpletree_multivendor_seller_product_template'];
				} else {
				$data['module_purpletree_multivendor_seller_product_template'] = $this->config->get('module_purpletree_multivendor_seller_product_template');
			}
			if(isset($this->request->post['module_purpletree_multivendor_multiple_subscription_plan_active'])){
				$data['module_purpletree_multivendor_multiple_subscription_plan_active'] = $this->request->post['module_purpletree_multivendor_multiple_subscription_plan_active'];
				} elseif($this->config->get('module_purpletree_multivendor_multiple_subscription_plan_active') || $this->config->get('module_purpletree_multivendor_multiple_subscription_plan_active') == '0'){
				$data['module_purpletree_multivendor_multiple_subscription_plan_active'] = $this->config->get('module_purpletree_multivendor_multiple_subscription_plan_active');
				} else {
				$data['module_purpletree_multivendor_multiple_subscription_plan_active'] = "0";
			}
			//paypal Currency
			$this->load->model('localisation/currency');
			
			$data['paypalcurrencies'] = $this->model_localisation_currency->getCurrencies();
			
			if (isset($this->request->post['module_purpletree_multivendor_paypal_currency'])) {
				$data['module_purpletree_multivendor_paypal_currency'] = $this->request->post['module_purpletree_multivendor_paypal_currency'];
				} else {
				$data['module_purpletree_multivendor_paypal_currency'] = (NULL != $this->config->get('module_purpletree_multivendor_paypal_currency'))?$this->config->get('module_purpletree_multivendor_paypal_currency'):$this->config->get('config_currency');
			}
			
			/////////////////Start seller Blog setting/////////////////
			if(isset($this->request->post['module_purpletree_multivendor_seller_blog_order'])){
				$data['module_purpletree_multivendor_seller_blog_order'] = $this->request->post['module_purpletree_multivendor_seller_blog_order'];
				} elseif($this->config->get('module_purpletree_multivendor_seller_blog_order') || $this->config->get('module_purpletree_multivendor_seller_blog_order') == '0'){
				$data['module_purpletree_multivendor_seller_blog_order'] = $this->config->get('module_purpletree_multivendor_seller_blog_order');
				} else {
				$data['module_purpletree_multivendor_seller_blog_order'] = "0";
			}	
			/////////////////End  seller Blog setting/////////////////
			
			/////////////////Start Quick order /////////////////
			$data['entry_show_seller_name'] = $this->language->get('entry_show_seller_name');
			$data['entry_show_seller_address'] = $this->language->get('entry_show_seller_address');
			if(isset($this->request->post['module_purpletree_multivendor_show_seller_name'])){
				$data['module_purpletree_multivendor_show_seller_name'] = $this->request->post['module_purpletree_multivendor_show_seller_name'];
				} elseif($this->config->get('module_purpletree_multivendor_show_seller_name')){
				$data['module_purpletree_multivendor_show_seller_name'] = $this->config->get('module_purpletree_multivendor_show_seller_name');
				} else {
				$data['module_purpletree_multivendor_show_seller_name'] = "0";
			}
			if(isset($this->request->post['module_purpletree_multivendor_show_seller_address'])){
				$data['module_purpletree_multivendor_show_seller_address'] = $this->request->post['module_purpletree_multivendor_show_seller_address'];
				} elseif($this->config->get('module_purpletree_multivendor_show_seller_address')){
				$data['module_purpletree_multivendor_show_seller_address'] = $this->config->get('module_purpletree_multivendor_show_seller_address');
				} else {
				$data['module_purpletree_multivendor_show_seller_address'] = "0";
			}
			/////////////////End  Quick order/////////////////
			//Allow seller to reply 
			if (isset($this->request->post['module_purpletree_multivendor_allow_seller_to_reply'])) {
				$data['module_purpletree_multivendor_allow_seller_to_reply'] = $this->request->post['module_purpletree_multivendor_allow_seller_to_reply'];
				} else {
				$data['module_purpletree_multivendor_allow_seller_to_reply'] = $this->config->get('module_purpletree_multivendor_allow_seller_to_reply');
			}  
			$data['user_token'] = $this->session->data['user_token'];
			$data['header'] = $this->load->controller('common/header');
			$data['column_left'] = $this->load->controller('common/column_left');
			$data['footer'] = $this->load->controller('common/footer');
			
			$this->response->setOutput($this->load->view('extension/module/purpletree_multivendor', $data));
		}
		
		public function get_client_ip() {
			$ipaddress = '';
			if (getenv('HTTP_CLIENT_IP'))
			$ipaddress = getenv('HTTP_CLIENT_IP');
			else if(getenv('HTTP_X_FORWARDED_FOR'))
			$ipaddress = getenv('HTTP_X_FORWARDED_FOR');
			else if(getenv('HTTP_X_FORWARDED'))
			$ipaddress = getenv('HTTP_X_FORWARDED');
			else if(getenv('HTTP_FORWARDED_FOR'))
			$ipaddress = getenv('HTTP_FORWARDED_FOR');
			else if(getenv('HTTP_FORWARDED'))
			$ipaddress = getenv('HTTP_FORWARDED');
			else if(getenv('REMOTE_ADDR'))
			$ipaddress = getenv('REMOTE_ADDR');
			else
			$ipaddress = 'UNKNOWN';
			return $ipaddress;
		}
		
		protected function validate() {
			if (!$this->user->hasPermission('modify', 'extension/module/purpletree_multivendor')) {
				$this->error['warning'] = $this->language->get('error_permission');
			}
			
			if(!isset($this->request->post['module_purpletree_multivendor_commission_status']) || $this->request->post['module_purpletree_multivendor_commission_status'] == '') {
				
				$this->error['commission_status'] = $this->language->get('error_commission_status');
			}
			if(!isset($this->request->post['module_purpletree_multivendor_commission'])){
				$this->error['commission'] = $this->language->get('error_commission');
				} else {
				if($this->request->post['module_purpletree_multivendor_commission'] > 100){
					
					$this->error['commission'] = $this->language->get('error_commission');			
					
					}elseif( ! filter_var($this->request->post['module_purpletree_multivendor_commission'], FILTER_VALIDATE_FLOAT) && $this->request->post['module_purpletree_multivendor_commission'] != '0' ){
					$this->error['commission'] = $this->language->get('error_commission');
					
					} elseif($this->request->post['module_purpletree_multivendor_commission'] < 0){
					$this->error['commission'] = $this->language->get('error_commission');
				}
			}
			if(!isset($this->request->post['module_purpletree_multivendor_process_data']) || utf8_strlen($this->request->post['module_purpletree_multivendor_process_data']) < 1 ){
				$this->error['process_data'] = $this->language->get('error_process_data');
			}
			
			if(!isset($this->request->post['module_purpletree_multivendor_product_limit'])){
				
				$this->error['product_limit'] = $this->language->get('error_product_limit');
				
				} else {
				
				if( ! filter_var($this->request->post['module_purpletree_multivendor_product_limit'], FILTER_VALIDATE_FLOAT) && $this->request->post['module_purpletree_multivendor_product_limit'] != '0' ){
					$this->error['product_limit'] = $this->language->get('error_product_limit');
					
					} elseif($this->request->post['module_purpletree_multivendor_product_limit'] < 0){
					$this->error['product_limit'] = $this->language->get('error_product_limit');
				}
			}
			//subscription plan tax value
			if(!isset($this->request->post['module_purpletree_multivendor_subscription_plans']) || $this->request->post['module_purpletree_multivendor_subscription_plans']){
				if(!isset($this->request->post['module_purpletree_multivendor_tax_value'])){
					$this->error['tax_value'] = $this->language->get('error_tax_value');
					} else {
					if($this->request->post['module_purpletree_multivendor_tax_value'] > 100){
						$this->error['tax_value'] = $this->language->get('error_tax_value');
						}elseif( ! filter_var($this->request->post['module_purpletree_multivendor_tax_value'], FILTER_VALIDATE_FLOAT) && $this->request->post['module_purpletree_multivendor_tax_value'] != '0' ){
						$this->error['tax_value'] = $this->language->get('error_tax_value');
						
						} elseif($this->request->post['module_purpletree_multivendor_tax_value'] < 0){
						$this->error['tax_value'] = $this->language->get('error_tax_value');
					}
					
				}
				
				
				if(!isset($this->request->post['module_purpletree_multivendor_tax_name']) || $this->request->post['module_purpletree_multivendor_tax_name'] =='' ){
					$this->error['tax_name'] = $this->language->get('error_tax_name');
				}
				
				if(!isset($this->request->post['module_purpletree_multivendor_reminder_one_days']) || !is_numeric($this->request->post['module_purpletree_multivendor_reminder_one_days']) || $this->request->post['module_purpletree_multivendor_reminder_one_days'] < 0 )
				{
					$this->error['reminder_one_days'] = $this->language->get('reminder_one_days_error');
				}
				if(!isset($this->request->post['module_purpletree_multivendor_reminder_two_days']) || !is_numeric($this->request->post['module_purpletree_multivendor_reminder_two_days']) || $this->request->post['module_purpletree_multivendor_reminder_two_days'] < 0){
					$this->error['reminder_two_days'] = $this->language->get('reminder_two_days_error');
				}
				if(!isset($this->request->post['module_purpletree_multivendor_reminder_three_days']) || !is_numeric($this->request->post['module_purpletree_multivendor_reminder_three_days']) || $this->request->post['module_purpletree_multivendor_reminder_three_days'] < 0 ){
					$this->error['reminder_three_days'] = $this->language->get('reminder_three_days_error');
				}
				if(!isset($this->request->post['module_purpletree_multivendor_grace_period']) || !is_numeric($this->request->post['module_purpletree_multivendor_grace_period']) || $this->request->post['module_purpletree_multivendor_grace_period'] < 0 ){
					$this->error['grace_period'] = $this->language->get('grace_period_error');
				}
			}
			if(!isset($this->request->post['module_purpletree_multivendor_shipping_commission'])){
				$this->error['shipping_commission_error'] = $this->language->get('error_shipping_commission');
				} else {
				if($this->request->post['module_purpletree_multivendor_shipping_commission'] > 100){
					
					$this->error['shipping_commission_error'] = $this->language->get('error_shipping_commission');			
					
					}elseif( ! filter_var($this->request->post['module_purpletree_multivendor_shipping_commission'], FILTER_VALIDATE_FLOAT) && $this->request->post['module_purpletree_multivendor_shipping_commission'] != '0' ){
					$this->error['shipping_commission_error'] = $this->language->get('error_shipping_commission');
					
					} elseif($this->request->post['module_purpletree_multivendor_shipping_commission'] < 0){
					$this->error['shipping_commission_error'] = $this->language->get('error_shipping_commission');
				}
			}
			if(isset($this->request->post['module_purpletree_multivendor_paypal_email']) && $this->request->post['module_purpletree_multivendor_paypal_email'] != ''){
				if ((utf8_strlen($this->request->post['module_purpletree_multivendor_paypal_email']) > 96) || !filter_var($this->request->post['module_purpletree_multivendor_paypal_email'], FILTER_VALIDATE_EMAIL)) {
					$this->error['paypal_email'] = $this->language->get('error_email_id');
				}
			}
			
			return !$this->error;
		}
		
		public function getSelectedCategory()
		{
			$json = array();
			$this->load->model('catalog/category');
			$results = $this->model_catalog_category->getCategories();
			if(!empty($results)){
				foreach ($results as $result) {
					
					$categories = $this->config->get('module_purpletree_multivendor_allow_category');
					if(!empty($categories)){
						if(in_array($result['category_id'],$categories)) {
							$json[] = array(
							'category_id' => $result['category_id'],
							'name'        => strip_tags(html_entity_decode($result['name'], ENT_QUOTES, 'UTF-8'))
							);
						}
					}
				}
			} 
			/* if(!empty($categories)){
				foreach ($categories as $key => $value) {
				$json[] = array(
				'category_id' => $value,
				'name'        => strip_tags(html_entity_decode($key, ENT_QUOTES, 'UTF-8'))
				);
				}
			} */
			$this->response->addHeader('Content-Type: application/json');
			$this->response->setOutput(json_encode($json));
		}
	}