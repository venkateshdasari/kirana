<?php
class ControllerExtensionAccountPurpletreeMultivendorSellercontact extends Controller {
		private $error = array();
		
		protected function validatemessage() {
			
			if(!$this->customer->validateSeller()) {
				$this->load->language('purpletree_multivendor/ptsmultivendor');
				$this->error['error_warning'] = $this->language->get('error_license');
			}
			
			
			if ((utf8_strlen($this->request->post['customer_message']) < 10) || (utf8_strlen($this->request->post['customer_message']) > 3000)) {
				$this->error['customer_message'] = $this->language->get('error_enquiry');
			}
			
			// Captcha
			if ($this->config->get('captcha_' . $this->config->get('config_captcha') . '_status') && in_array('contact', (array)$this->config->get('config_captcha_page'))) {
				$captcha = $this->load->controller('extension/captcha/' . $this->config->get('config_captcha') . '/validate');

				if ($captcha) {
					$this->error['captcha'] = $captcha;
				}
			}
			return !$this->error;
		}
		protected function validate() {
			if(!$this->customer->validateSeller()) {
				$this->load->language('purpletree_multivendor/ptsmultivendor');
				$this->error['error_warning'] = $this->language->get('error_license');
			}
			
			if ((utf8_strlen($this->request->post['customer_name']) < 3) || (utf8_strlen($this->request->post['customer_name']) > 32)) {
				$this->error['customer_name'] = $this->language->get('error_name');
			}
			$EMAIL_REGEX='/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,4})$/';
			if (preg_match($EMAIL_REGEX, $this->request->post['customer_email'])==false) {
				$this->error['customer_email'] = $this->language->get('error_email');
			}
			
			if ((utf8_strlen($this->request->post['customer_message']) < 10) || (utf8_strlen($this->request->post['customer_message']) > 3000)) {
				$this->error['customer_message'] = $this->language->get('error_enquiry');
			}
			
			// Captcha
			if ($this->config->get('captcha_' . $this->config->get('config_captcha') . '_status') && in_array('contact', (array)$this->config->get('config_captcha_page'))) {
				$captcha = $this->load->controller('extension/captcha/' . $this->config->get('config_captcha') . '/validate');

				if ($captcha) {
					$this->error['captcha'] = $captcha;
				}
			}
			
			return !$this->error;
		}
		public function customerContactlist(){
			if(!$this->customer->validateSeller()) {
				$this->load->language('purpletree_multivendor/ptsmultivendor');
				$this->error['error_warning'] = $this->language->get('error_license');
			}
			$store_detail = $this->customer->isSeller();
			if(isset($store_detail['store_status'])){
				$stores=array();
						if(isset($store_detail['multi_store_id'])){
							$stores=explode(',',$store_detail['multi_store_id']);
						}
						
					if(isset($store_detail['store_status']) && !in_array($this->config->get('config_store_id'),$stores)){	
					$this->response->redirect($this->url->link('account/account','', true));
				}
			}
			$this->load->model('extension/purpletree_multivendor/dashboard');
			
			$this->model_extension_purpletree_multivendor_dashboard->checkSellerApproval();
			$this->load->model('extension/purpletree_multivendor/sellercontact');
			$this->load->language('purpletree_multivendor/sellercontact');
			
			$this->document->setTitle($this->language->get('heading_title'));
			
			$data['heading_title'] = $this->language->get('heading_title');
			$data['text_empty_result'] = $this->language->get('text_empty_result');
			
			if($this->customer->isLogged()){
				
				if (isset($this->request->get['page'])) {
					$page = $this->request->get['page'];
					} else {
					$page = 1;
				}	
				
				if (isset($this->request->get['limit'])) {
					$limit = (int)$this->request->get['limit'];
					} else {
					$limit = 10;
				}
				$customer_id = $this->customer->getId();
				
				$data['breadcrumbs'] = array();
				
				$data['breadcrumbs'][] = array(
				'text' => $this->language->get('text_home'),
				'href' => $this->url->link('common/home','',true)
				);
				
				$data['breadcrumbs'][] = array(
				'text' => $this->language->get('text_heading'),
				'href' => $this->url->link('extension/account/purpletree_multivendor/sellercontact/customerContactlist', '', true)
				);
				$data['text_heading'] = $this->language->get('text_heading');
				$filter_data = array(
				'start'              => ($page - 1) * $limit,
				'limit'              => $limit,
				'customer_id' 		=> $customer_id
				);
				$contact_total = $this->model_extension_purpletree_multivendor_sellercontact->getSellerContactCustomers122($filter_data);
				
				$results1 = $this->model_extension_purpletree_multivendor_sellercontact->getSellerContactCustomers1($filter_data); 
				
				$data['sellercontacts'] = array();
				if(!empty($results1)) {
					foreach($results1 as $re) {
						$seller_id 	= $re['seller_id'];
						$customernnaaa = $this->model_extension_purpletree_multivendor_sellercontact->getCustomer($seller_id);
						$results2 	= $this->model_extension_purpletree_multivendor_sellercontact->getSellerContactCustomerschat($customer_id,$seller_id);
						//$message = array();
						//$contact_from = array();
						if(!empty($results2)) {
							foreach($results2 as $result){
								$data['sellercontacts'][$seller_id] = array(
								'id' 			 =>  $result['id'],
								'message' 		 =>  nl2br($result['customer_message']),
								'seller_id' 	 =>  $result['seller_id'],
								'customer_id' 	 =>  $customer_id,
								'contact_from' 	 =>  $result['contact_from'],
								'customer_name'  => $customernnaaa['firstname'].' '. $customernnaaa['lastname'],
								'customer_email'  => $customernnaaa['email'],
								'date_added' 	 => date($this->language->get('date_format_short'), strtotime($result['created_at'])),
								'reply'			 => $this->url->link('extension/account/purpletree_multivendor/sellercontact/customerreply','id='. $result['id'], true)
								);
							}
						}
					}
				}
				
				$data['config_contactseller']=$this->config->get('module_purpletree_multivendor_seller_contact');
				
				} else {
				$this->session->data['redirect'] = $this->url->link('extension/account/purpletree_multivendor/sellercontact/customerContactlist','', true);
				$this->response->redirect($this->url->link('account/login','', true));				
			}
			
			$pagination = new Pagination();
			$pagination->total = $contact_total;
			$pagination->page = $page;
			$pagination->limit = $limit;
			$pagination->url = $this->url->link('extension/account/purpletree_multivendor/sellercontact/customercontactlist', '&page={page}',true);
			
			$data['pagination'] = $pagination->render();
			
			$data['results'] = sprintf($this->language->get('text_pagination'), ($contact_total) ? (($page - 1) * $limit) + 1 : 0, ((($page - 1) * $limit) > ($contact_total - $limit)) ? $contact_total : ((($page - 1) * $limit) + $limit), $contact_total, ceil($contact_total / $limit));
						$direction = $this->language->get('direction');
		 if ($direction=='rtl'){
			$this->document->addStyle('catalog/view/javascript/purpletree/bootstrap/css/bootstrap.min-a.css');
			$this->document->addStyle('catalog/view/theme/default/stylesheet/purpletree/custom-a.css'); 
			}else{
			$this->document->addStyle('catalog/view/javascript/purpletree/bootstrap/css/bootstrap.min.css'); 
			$this->document->addStyle('catalog/view/theme/default/stylesheet/purpletree/custom.css'); 
			}
			$data['column_left'] = $this->load->controller('common/column_left');
			$data['column_right'] = $this->load->controller('common/column_right');
			$data['content_top'] = $this->load->controller('common/content_top');
			$data['content_bottom'] = $this->load->controller('common/content_bottom');
			$data['footer'] = $this->load->controller('common/footer');
			$data['header'] = $this->load->controller('common/header');
			if (isset($this->session->data['success'])) {
				$data['success'] = $this->session->data['success'];
				
				unset($this->session->data['success']);
				} else {
				$data['success'] = '';
			}
			
			$this->response->setOutput($this->load->view('account/purpletree_multivendor/customercontactlist', $data));
		}
		
		
		public function sellercontactlist(){
			if(!$this->customer->validateSeller()) {
				$this->load->language('purpletree_multivendor/ptsmultivendor');
				$this->session->data['error_warning'] = $this->language->get('error_license');
				
			}
			$store_detail = $this->customer->isSeller();
			if(!isset($store_detail['store_status'])){
				$this->response->redirect($this->url->link('account/account', '', true));
				}else{
				$stores=array();
						if(isset($store_detail['multi_store_id'])){
							$stores=explode(',',$store_detail['multi_store_id']);
						}
						
					if(isset($store_detail['store_status']) && !in_array($this->config->get('config_store_id'),$stores)){	
					$this->response->redirect($this->url->link('account/account','', true));
				}
			}
			$this->load->model('extension/purpletree_multivendor/dashboard');
			
			$this->model_extension_purpletree_multivendor_dashboard->checkSellerApproval();
			$data['contact_mode'] = $this->config->get('module_purpletree_multivendor_seller_contact');
			$this->load->model('extension/purpletree_multivendor/sellercontact');
			$this->load->language('purpletree_multivendor/sellercontact');
			
			$this->document->setTitle($this->language->get('heading_title'));
			
			$data['heading_title'] = $this->language->get('heading_title');
			$data['text_empty_result'] = $this->language->get('text_empty_result');
			
			if (isset($this->error['warning'])) {
				$data['error_warning'] = $this->error['warning'];
				} elseif (isset($this->session->data['error_warning'])) {
				$data['error_warning'] = $this->session->data['error_warning'];
				unset($this->session->data['error_warning']);
				} else {
				$data['error_warning'] = '';
			}
			
			if (isset($this->session->data['success'])) {
				$data['success'] = $this->session->data['success'];
				
				unset($this->session->data['success']);
				} else {
				$data['success'] = '';
			}
			if($this->customer->isSeller()){
				
				if (isset($this->request->get['page'])) {
					$page = $this->request->get['page'];
					} else {
					$page = 1;
				}	
				
				if (isset($this->request->get['limit'])) {
					$limit = (int)$this->request->get['limit'];
					} else {
					$limit = 10;
				}
				$seller_id = $this->customer->getId();
				//die;
				$data['breadcrumbs'] = array();
				
				$data['breadcrumbs'][] = array(
				'text' => $this->language->get('text_home'),
				'href' => $this->url->link('common/home','',true)
				);
				
				$data['breadcrumbs'][] = array(
				'text' => $this->language->get('text_heading'),
				'href' => $this->url->link('extension/account/purpletree_multivendor/sellercontact/sellercontactlist', '', true)
				);
				$data['text_heading'] = $this->language->get('text_heading');
				$filter_data = array(
				'start'              => ($page - 1) * $limit,
				'limit'              => $limit,
				'seller_id' 		=> $seller_id
				);
				
				$contact_total = $this->model_extension_purpletree_multivendor_sellercontact->getSellerContactCustomers1111($filter_data);
				$results1 = $this->model_extension_purpletree_multivendor_sellercontact->getSellerContactCustomers($filter_data);
				$data['sellercontacts'] = array();
				if(!empty($results1)) {
					foreach($results1 as $re) {
						$custid 	= $re['customer_id'];
						$customernnaaa = $this->model_extension_purpletree_multivendor_sellercontact->getCustomer($custid);
						$results2 	= $this->model_extension_purpletree_multivendor_sellercontact->getSellerContactCustomerschat($custid,$seller_id);
						$message = array();
						$contact_from = array();
						if(!empty($results2)) {
							foreach($results2 as $result){
								if($result['customer_id'] == '0') {
									$nameeee = "Guest";
									$emailll = "";
									} else {
									$nameeee = $customernnaaa['firstname'].' '. $customernnaaa['lastname'];
									$emailll = $customernnaaa['email'];
								}
								$data['sellercontacts'][] = array(
								'id' 			 =>  $result['id'],
								'message' 		 =>   html_entity_decode($result['customer_message'], ENT_QUOTES, 'UTF-8') . "\n",
								'customer_id' 	 =>  $result['customer_id'],
								'contact_from' 	 =>  $result['contact_from'],
								'customer_name'  =>  $nameeee,
								'seen' 	 =>  $result['seen'],
								'customer_email'  => $emailll,
								'date_added' 	 => date($this->language->get('date_format_short'), strtotime($result['created_at'])),
								'reply'			 => $this->url->link('extension/account/purpletree_multivendor/sellercontact/reply','id='. $result['id'], true)
								);
								
							}
						}
					}
				}
				$data['config_contactseller']	=	$this->config->get('module_purpletree_multivendor_seller_contact');
				
				$pagination = new Pagination();
				$pagination->total = $contact_total;
				$pagination->page = $page;
				$pagination->limit = $limit;
				$pagination->url = $this->url->link('extension/account/purpletree_multivendor/sellercontact/sellercontactlist', '&page={page}',true);
				
				$data['pagination'] = $pagination->render(); 
				
				$data['results'] = sprintf($this->language->get('text_pagination'), ($contact_total) ? (($page - 1) * $limit) + 1 : 0, ((($page - 1) * $limit) > ($contact_total - $limit)) ? $contact_total : ((($page - 1) * $limit) + $limit), $contact_total, ceil($contact_total / $limit)); 
				
				$data['column_left'] = $this->load->controller('extension/account/purpletree_multivendor/common/column_left');
			$data['footer'] = $this->load->controller('extension/account/purpletree_multivendor/common/footer');
			$data['header'] = $this->load->controller('extension/account/purpletree_multivendor/common/header');
				if (isset($this->session->data['success'])) {
					$data['success'] = $this->session->data['success'];
					
					unset($this->session->data['success']);
					} else {
					$data['success'] = '';
				}
				
				$this->response->setOutput($this->load->view('account/purpletree_multivendor/contactlist', $data));
				} else {
				$this->response->redirect($this->url->link('account/account','',true));	
			}
		}
		
		
		public function reply() {
			if(!$this->customer->validateSeller()) {
				$this->load->language('purpletree_multivendor/ptsmultivendor');
				$this->error['error_warning'] = $this->language->get('error_license');
			}
			$store_detail = $this->customer->isSeller();
			if(!isset($store_detail['store_status'])){
				$this->response->redirect($this->url->link('account/account', '', true));
				}else{
				$stores=array();
						if(isset($store_detail['multi_store_id'])){
							$stores=explode(',',$store_detail['multi_store_id']);
						}
						
					if(isset($store_detail['store_status']) && !in_array($this->config->get('config_store_id'),$stores)){	
					$this->response->redirect($this->url->link('account/account','', true));
				}
			}
			$this->load->model('extension/purpletree_multivendor/dashboard');
			
			$this->model_extension_purpletree_multivendor_dashboard->checkSellerApproval();
			if ($this->customer->isLogged()) {
				$data['loggedin'] = '1';
				} else {
				$data['loggedin'] = '0';
			}
			//if (!$this->customer->isLogged()) {
			$data['contact_mode'] = $this->config->get('module_purpletree_multivendor_seller_contact');
			//}
			if($this->config->get('module_purpletree_multivendor_seller_contact')==1){
				if (!$this->customer->isLogged()) {
					$this->session->data['redirect'] = $this->url->link('extension/account/purpletree_multivendor/sellercontact/reply', 'id='.$this->request->get['customer_id'], true);
					$this->response->redirect($this->url->link('account/login', '', true));
				}
			}
			
			$this->load->language('purpletree_multivendor/sellercontact');
			
			$this->document->setTitle($this->language->get('heading_title'));
			
			$this->load->model('extension/purpletree_multivendor/sellercontact');
			$this->load->model('extension/purpletree_multivendor/vendor');
			if(!$this->customer->isSeller()){
				$this->response->redirect($this->url->link('account/account','',true));	
			}
			if(!isset($this->request->get['id'])){
				$this->response->redirect($this->url->link('extension/account/purpletree_multivendor/sellercontact/sellercontactlist','',true));	
			}
			$customerid = $this->model_extension_purpletree_multivendor_sellercontact->getCustomerid($this->request->get['id']);
			$data['customer_id'] = $customerid;
			$seller_id = $this->customer->getId();
			if($customerid == $seller_id) {
				$this->response->redirect($this->url->link('extension/account/purpletree_multivendor/sellercontact/sellercontactlist','',true));
			}
			$data['customer'] = $this->customer->getId();	
			
			if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validatemessage()) {
				
				$fileData=array();			
				if(!empty($_FILES["attached_file"]['tmp_name'])){
					$seller_folder = "Seller_".$seller_id;
					$directory = DIR_IMAGE . 'catalog';
					if (!is_dir($directory . '/' . $seller_folder)) {
					mkdir($directory . '/' . $seller_folder, 0777);
					chmod($directory . '/' . $seller_folder, 0777);
					@touch($directory . '/' . $seller_folder . '/' . 'index.html');
					}
					
					$seller_folder = "Seller_".$seller_id."/enquiries_file";
					$directory = DIR_IMAGE . 'catalog';
					if (!is_dir($directory . '/' . $seller_folder)) {
					mkdir($directory . '/' . $seller_folder, 0777);
					chmod($directory . '/' . $seller_folder, 0777);
					@touch($directory . '/' . $seller_folder . '/' . 'index.html');
					}
					
					$upload_url='catalog/Seller_'.$seller_id.'/enquiries_file/';
					foreach($_FILES["attached_file"]['tmp_name'] as $key=>$file){
						if($file){
							$file_root=$upload_url.date("ddmmyyyyhis").$_FILES["attached_file"]['name'][$key];
							$file_name= basename($upload_url.$_FILES["attached_file"]['name'][$key]);
							$fileData[$key]=array(
							'file_root'=>$file_root,
							'file_name'=>$file_name,
							);
							move_uploaded_file($file,DIR_IMAGE . $file_root);	
						}
					}
				}
				
				
				$customerid = $this->request->post['customer_id'];
				$customer = $this->model_extension_purpletree_multivendor_sellercontact->getCustomer($customerid);
				$selllleeerr = $this->model_extension_purpletree_multivendor_sellercontact->getCustomer($seller_id);
				$dataa = array(
				'customer_id' 	 => $customerid,
				'seller_id'		 => $seller_id,
				'customer_name'  => $selllleeerr['firstname'].' '. $selllleeerr['lastname'],
				'customer_email'  => $selllleeerr['email'],
				'customer_message'  => $this->request->post['customer_message'],
				'contact_from'   => 1,
				'attached_file'   => $fileData
				);
				$chat_id=$this->model_extension_purpletree_multivendor_sellercontact->addContact($dataa);
				$attached_files 	= $this->model_extension_purpletree_multivendor_sellercontact->getAttachedEnquiriesFile($chat_id);
						$attach_file=array();
						if(!empty($attached_files)){
							foreach($attached_files as $filess){
								if ($this->request->server['HTTPS']) {
									$file_root = $this->config->get('config_ssl') . 'image/' . $filess['image'];
								} else {
									$file_root = $this->config->get('config_url') . 'image/' . $filess['image'];
								}
								$attach_file[]=$file_root;
							}	
						}
				
				$ptsmv_current_page='';
				$seller_name = $selllleeerr['firstname'].' '. $selllleeerr['lastname'];
				$email_code = 'seller_reply_to_customer_inquiry';
				$register_template = $this->model_extension_purpletree_multivendor_vendor->getSelleRegisterEmailTemplate($email_code);
				$subtemplatefromdb = $register_template['new_subject'];
				$messtemplatefromdb = $register_template['new_message'];
				$replacevarsub = array('_SELLER_NAME_' => $seller_name);
				$email_subject = $this->model_extension_purpletree_multivendor_vendor->getmsgfromarray($replacevarsub,$subtemplatefromdb);
				$replacevar = array('_SELLER_NAME_' =>$seller_name,
									'_SELLER_EMAIL_' =>$selllleeerr['email'],
									'_SELLERMESSAGE_' =>$this->request->post['customer_message']
									);
				$email_message = $this->model_extension_purpletree_multivendor_vendor->getmsgfromarray($replacevar,$messtemplatefromdb);
				$reciver = $customer['email'];
				$this->model_extension_purpletree_multivendor_vendor->ptsSendMail($reciver,$email_subject,$email_message,$attach_file);
				
				$this->session->data['success'] = $this->language->get('text_success');
				
				
				
				//$this->response->redirect($this->url->link('extension/account/purpletree_multivendor/sellercontact/sellercontactlist','',true));
				} else {
				if (isset($this->request->post['customer_message'])) {
					$data['customer_message'] = $this->request->post['customer_message'];
					} else {
					$data['customer_message'] = '';
				}
			}
			if (isset($this->session->data['success'])) {
				$data['success'] = $this->session->data['success'];
				
				unset($this->session->data['success']);
				} else {
				$data['success'] = '';
			}
			
			$data['breadcrumbs'] = array();
			
			$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/home','',true)
			);
			$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('extension/account/purpletree_multivendor/sellercontact/sellercontactlist','',true)
			);		
			
			$data['heading_title'] = $this->language->get('heading_title');
			
			$data['entry_name'] = $this->language->get('entry_name');
			$data['entry_email'] = $this->language->get('entry_email');
			$data['entry_enquiry'] = $this->language->get('entry_enquiry');
			
			if (isset($this->error['customer_message'])) {
				$data['error_enquiry'] = $this->error['customer_message'];
				} else {
				$data['error_enquiry'] = '';
			}
			
			if (isset($this->error['error_warning'])) {
				$data['error_warning'] = $this->error['error_warning'];
				} else {
				$data['error_warning'] = '';
			}
			
			$data['button_submit'] = $this->language->get('button_submit');
			
			//$data['reply'] =$this->url->link('extension/account/purpletree_multivendor/sellerorder/seller_order_info', '', true);
			
			$customer_detal=array();
			$data['customer_id']=$customerid;
			$data['sellercontacts'] = array();
			$results2 	= $this->model_extension_purpletree_multivendor_sellercontact->getSellerContactCustomerschat122($seller_id,$customerid);
			$message = array();
			$contact_from = array();
			$date_added = array();
			if(!empty($results2)) {
				foreach($results2 as $result){
					$attached_files 	= $this->model_extension_purpletree_multivendor_sellercontact->getAttachedEnquiriesFile($result['id']);
						$attachedFileLinks=array();
						if(!empty($attached_files)){
							foreach($attached_files as $filess){
								if ($this->request->server['HTTPS']) {
									$file_root = $this->config->get('config_ssl') . 'image/' . $filess['image'];
								} else {
									$file_root = $this->config->get('config_url') . 'image/' . $filess['image'];
								}
								$name = $filess['image_name'];
								$attachedFileLinks[]=array(
								'name'=>$name,
								'images'=>$file_root
								);
							}	
						}
					$data['sellercontacts'][] = array(
					'contact_from'     => $result['contact_from'],
					'customer_id'     => $result['customer_id'],
					'customer_name'     => $result['customer_name'],
					'seen'     => $result['seen'],
					'customer_email'     => $result['customer_email'],
					'customer_messages'       => html_entity_decode($result['customer_message'], ENT_QUOTES, 'UTF-8') . "\n",
					'date_added' => date($this->language->get('date_format_short'), strtotime($result['created_at'])),
					'attached_file'=>$attachedFileLinks 
					);
				}
			}
			$this->model_extension_purpletree_multivendor_sellercontact->updateSeenContact($seller_id,$customerid);
			// Captcha
			$data['captcha'] = $this->load->controller('captcha/' . $this->config->get('config_captcha'), $this->error);
			$data['allow_seller_to_reply'] = $this->config->get('module_purpletree_multivendor_allow_seller_to_reply');
			$data['column_left'] = $this->load->controller('extension/account/purpletree_multivendor/common/column_left');
			$data['footer'] = $this->load->controller('extension/account/purpletree_multivendor/common/footer');
			$data['header'] = $this->load->controller('extension/account/purpletree_multivendor/common/header');
			
			$this->response->setOutput($this->load->view('account/purpletree_multivendor/customer_reply', $data));
		}
		
		public function customerReply() {
			if(!$this->customer->validateSeller()) {
				$this->load->language('purpletree_multivendor/ptsmultivendor');
				$this->error['error_warning'] = $this->language->get('error_license');
			}

			if ($this->customer->isLogged()) {
				$data['loggedin'] = '1';
				} else {
				$data['loggedin'] = '0';
			}
			$this->load->model('extension/purpletree_multivendor/vendor');
			$this->load->model('extension/purpletree_multivendor/sellercontact');
			
			if(isset($this->request->get['seller_id'])){
				$seller_storessst = $this->model_extension_purpletree_multivendor_sellercontact->checkSellerVacations($this->request->get['seller_id']);
				if($seller_storessst>=1){	
					return $this->purpletreeStore404();
				}
			}
			$data['contact_mode'] = $this->config->get('module_purpletree_multivendor_seller_contact');
			if($this->config->get('module_purpletree_multivendor_seller_contact')==1){
				if (!$this->customer->isLogged()) {
					if(isset($this->request->get['seller_id'])) {
						$this->session->data['redirect'] = $this->url->link('extension/account/purpletree_multivendor/sellercontact/customerReply', 'seller_id='.$this->request->get['seller_id'], true);
						} elseif(isset($this->request->get['id'])) {
						$this->session->data['redirect'] = $this->url->link('extension/account/purpletree_multivendor/sellercontact/customerReply', 'id='.$this->request->get['id'], true);
					}
					$this->response->redirect($this->url->link('account/login', '', true));
				}
			}
			if ($this->request->server['HTTPS']) {
			   $proto='https://';
		     } else {
			    $proto='http://';
		     }
			define('SERVER_PROTOCOL',$proto);
			
			if(isset($this->request->get['seller_id'])) {
				$seller_id = $this->request->get['seller_id'];
				} elseif(isset($this->request->get['id'])) {
				$seller_id = $this->model_extension_purpletree_multivendor_sellercontact->getSellerId($this->request->get['id']);
				} else {
				$this->response->redirect($this->url->link('account/account','',true));	
			}
			$customerid = $this->customer->getId();
			if($customerid == $seller_id) {
				$this->response->redirect($this->url->link('account/account', '', true));
			}
			
			$this->load->language('purpletree_multivendor/sellercontact');
			
			$this->document->setTitle($this->language->get('heading_title'));
			
			//$data['id'] = $this->request->get['id'];	
			if (!$this->customer->isLogged()) {
				if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
					
				$fileData=array();			
				if(!empty($_FILES["attached_file"]['tmp_name'])){
					$seller_folder = "Seller_".$seller_id;
					$directory = DIR_IMAGE . 'catalog';
					if (!is_dir($directory . '/' . $seller_folder)) {
					mkdir($directory . '/' . $seller_folder, 0777);
					chmod($directory . '/' . $seller_folder, 0777);
					@touch($directory . '/' . $seller_folder . '/' . 'index.html');
					}
					
					$seller_folder = "Seller_".$seller_id."/enquiries_file";
					$directory = DIR_IMAGE . 'catalog';
					if (!is_dir($directory . '/' . $seller_folder)) {
					mkdir($directory . '/' . $seller_folder, 0777);
					chmod($directory . '/' . $seller_folder, 0777);
					@touch($directory . '/' . $seller_folder . '/' . 'index.html');
					}
					
					$upload_url='catalog/Seller_'.$seller_id.'/enquiries_file/';
					foreach($_FILES["attached_file"]['tmp_name'] as $key=>$file){
						if($file){
							$file_root=$upload_url.date("ddmmyyyyhis").$_FILES["attached_file"]['name'][$key];
							$file_name= basename($upload_url.$_FILES["attached_file"]['name'][$key]);
							$fileData[$key]=array(
							'file_root'=>$file_root,
							'file_name'=>$file_name,
							);
							move_uploaded_file($file,DIR_IMAGE . $file_root);	
						}
					}
				}
					
					//$seller_id = $this->request->post['seller_id'];
					$sellerr = $this->model_extension_purpletree_multivendor_sellercontact->getCustomer($seller_id);
					//$customerrr = $this->model_extension_purpletree_multivendor_sellercontact->getCustomer($customerid);
					$referrerUrl = '';
					if(isset($this->session->data['ptsmv_current_page']) && isset($this->session->data['ptsmv_current_page_name'])) {
						$producturl = $this->session->data['ptsmv_current_page'];
						$pagename = $this->session->data['ptsmv_current_page_name'];
                        $referrerUrl = '<a href="'.SERVER_PROTOCOL.$producturl.'" target="_blank">'.$pagename.'</a>';
					}
					$dataa = array(
					'customer_id' 	 => $customerid,
					'seller_id'		 => $seller_id,
					'customer_name'  => $this->request->post['customer_name'],
					'customer_email'  => $this->request->post['customer_email'],
					'customer_message'  => $this->request->post['customer_message'].$referrerUrl,
					'contact_from'   => 0,
					'attached_file'   => $fileData
					);
					$chat_id = $this->model_extension_purpletree_multivendor_sellercontact->addContact($dataa);
					$attached_files 	= $this->model_extension_purpletree_multivendor_sellercontact->getAttachedEnquiriesFile($chat_id);
						$attach_file=array();
						if(!empty($attached_files)){
							foreach($attached_files as $filess){
								if ($this->request->server['HTTPS']) {
									$file_root = $this->config->get('config_ssl') . 'image/' . $filess['image'];
								} else {
									$file_root = $this->config->get('config_url') . 'image/' . $filess['image'];
								}
								$attach_file[]=$file_root;
							}	
						}
					$ptsmv_current_page='';
					if(isset($this->session->data['ptsmv_current_page'])) {						
						if($referrerUrl != '') {
						
							$email_code = 'customer_inquiry_email_with_producturl_and_referrerpage';
							$register_template = $this->model_extension_purpletree_multivendor_vendor->getSelleRegisterEmailTemplate($email_code);
							$subtemplatefromdb = $register_template['new_subject'];
							$messtemplatefromdb = $register_template['new_message'];
							$replacevarsub = array('_CUSTOMER_NAME_' => $this->request->post['customer_name']);
							$email_subject = $this->model_extension_purpletree_multivendor_vendor->getmsgfromarray($replacevarsub,$subtemplatefromdb);
							$replacevar = array('_CUSTOMER_NAME_' =>$this->request->post['customer_name'],
												'_CUSTOMER_EMAIL_' =>$this->request->post['customer_email'],
												'_CUSTOMERMESSAGE_' =>$this->request->post['customer_message'],
												'_PRODUCTURL_' =>$this->session->data['ptsmv_current_page'],
												'_REFERRERPAGE_' =>$referrerUrl
												);
							$email_message = $this->model_extension_purpletree_multivendor_vendor->getmsgfromarray($replacevar,$messtemplatefromdb);
					    }else{
						   $email_code = 'customer_inquiry_email_with_producturl_without_referrerpage';
							$register_template = $this->model_extension_purpletree_multivendor_vendor->getSelleRegisterEmailTemplate($email_code);
							$subtemplatefromdb = $register_template['new_subject'];
							$messtemplatefromdb = $register_template['new_message'];
							$replacevarsub = array('_CUSTOMER_NAME_' => $this->request->post['customer_name']);
							$email_subject = $this->model_extension_purpletree_multivendor_vendor->getmsgfromarray($replacevarsub,$subtemplatefromdb);
							$replacevar = array('_CUSTOMER_NAME_' =>$this->request->post['customer_name'],
												'_CUSTOMER_EMAIL_' =>$this->request->post['customer_email'],
												'_CUSTOMERMESSAGE_' =>$this->request->post['customer_message'],
												'_PRODUCTURL_' =>$this->session->data['ptsmv_current_page']
												);
							$email_message = $this->model_extension_purpletree_multivendor_vendor->getmsgfromarray($replacevar,$messtemplatefromdb);
						}
					}else{
					   if($referrerUrl != '') {						
							  $email_code = 'customer_inquiry_email_without_producturl_with_referrerpage';
							$register_template = $this->model_extension_purpletree_multivendor_vendor->getSelleRegisterEmailTemplate($email_code);
							$subtemplatefromdb = $register_template['new_subject'];
							$messtemplatefromdb = $register_template['new_message'];
							$replacevarsub = array('_CUSTOMER_NAME_' => $this->request->post['customer_name']);
							$email_subject = $this->model_extension_purpletree_multivendor_vendor->getmsgfromarray($replacevarsub,$subtemplatefromdb);
							$replacevar = array('_CUSTOMER_NAME_' =>$this->request->post['customer_name'],
												'_CUSTOMER_EMAIL_' =>$this->request->post['customer_email'],
												'_CUSTOMERMESSAGE_' =>$this->request->post['customer_message'],
												'_REFERRERPAGE_' =>$referrerUrl
												);
							$email_message = $this->model_extension_purpletree_multivendor_vendor->getmsgfromarray($replacevar,$messtemplatefromdb);
					    }else{
							 $email_code = 'customer_inquiry_email_without_producturl_and_without_referrerpage';
							$register_template = $this->model_extension_purpletree_multivendor_vendor->getSelleRegisterEmailTemplate($email_code);
							$subtemplatefromdb = $register_template['new_subject'];
							$messtemplatefromdb = $register_template['new_message'];
							$replacevarsub = array('_CUSTOMER_NAME_' => $this->request->post['customer_name']);
							$email_subject = $this->model_extension_purpletree_multivendor_vendor->getmsgfromarray($replacevarsub,$subtemplatefromdb);
							$replacevar = array('_CUSTOMER_NAME_' =>$this->request->post['customer_name'],
												'_CUSTOMER_EMAIL_' =>$this->request->post['customer_email'],
												'_CUSTOMERMESSAGE_' =>$this->request->post['customer_message']
												);
							$email_message = $this->model_extension_purpletree_multivendor_vendor->getmsgfromarray($replacevar,$messtemplatefromdb);
						}
					}					
					$reciver = $sellerr['email'];
					$this->model_extension_purpletree_multivendor_vendor->ptsSendMail($reciver,$email_subject,$email_message,$attach_file);
					
					$this->session->data['success'] = $this->language->get('text_success');
					unset($this->session->data['ptsmv_current_page']);
					} else {
					if (isset($this->request->post['customer_message'])) {
						$data['customer_message'] = $this->request->post['customer_message'];
						} else {
						$data['customer_message'] = '';
					}
					if (isset($this->request->post['customer_name'])) {
						$data['customer_name'] = $this->request->post['customer_name'];
						} else {
						$data['customer_name'] = '';
					}
					
					if (isset($this->request->post['customer_email'])) {
						$data['customer_email'] = $this->request->post['customer_email'];
						} else {
						$data['customer_email'] = '';
					}
				}
				
				} else {
				if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validatemessage()) {
					
				$fileData=array();
			
			if(!empty($_FILES["attached_file"]['tmp_name'])){
					$seller_folder = "Seller_".$seller_id;
					$directory = DIR_IMAGE . 'catalog';
					if (!is_dir($directory . '/' . $seller_folder)) {
					mkdir($directory . '/' . $seller_folder, 0777);
					chmod($directory . '/' . $seller_folder, 0777);
					@touch($directory . '/' . $seller_folder . '/' . 'index.html');
					}
					
					$seller_folder = "Seller_".$seller_id."/enquiries_file";
					$directory = DIR_IMAGE . 'catalog';
					if (!is_dir($directory . '/' . $seller_folder)) {
					mkdir($directory . '/' . $seller_folder, 0777);
					chmod($directory . '/' . $seller_folder, 0777);
					@touch($directory . '/' . $seller_folder . '/' . 'index.html');
					}
					
					$upload_url='catalog/Seller_'.$seller_id.'/enquiries_file/';
					foreach($_FILES["attached_file"]['tmp_name'] as $key=>$file){
						if($file){
							$file_root=$upload_url.date("ddmmyyyyhis").$_FILES["attached_file"]['name'][$key];
							$file_name= basename($upload_url.$_FILES["attached_file"]['name'][$key]);
							$fileData[$key]=array(
							'file_root'=>$file_root,
							'file_name'=>$file_name,
							);
							move_uploaded_file($file,DIR_IMAGE . $file_root);	
						}
					}
				}

					//$seller_id = $this->request->post['seller_id'];
					$sellerr = $this->model_extension_purpletree_multivendor_sellercontact->getCustomer($seller_id);
					$customerrr = $this->model_extension_purpletree_multivendor_sellercontact->getCustomer($customerid);
					$referrerUrl = '';
					if(isset($this->session->data['ptsmv_current_page']) && isset($this->session->data['ptsmv_current_page_name'])) {
						$producturl = $this->session->data['ptsmv_current_page'];
						$pagename = $this->session->data['ptsmv_current_page_name'];
						$referrerUrl = '<a href="'.SERVER_PROTOCOL.$producturl.'" target="_blank">'.$pagename.'</a>';
					}
					$dataa = array(
					'customer_id' 	 => $customerid,
					'seller_id'		 => $seller_id,
					'customer_name'  => $customerrr['firstname'].' '. $customerrr['lastname'],
					'customer_email'  => $customerrr['email'],
					'customer_message'  => $this->request->post['customer_message'].$referrerUrl,
					'contact_from'   => 0,
					'attached_file'   => $fileData
					);
					$customer_name  = $customerrr['firstname'].' '. $customerrr['lastname'];
					$chat_id=$this->model_extension_purpletree_multivendor_sellercontact->addContact($dataa);
					$attached_files 	= $this->model_extension_purpletree_multivendor_sellercontact->getAttachedEnquiriesFile($chat_id);
						$attach_file=array();
						if(!empty($attached_files)){
							foreach($attached_files as $filess){
								if ($this->request->server['HTTPS']) {
									$file_root = $this->config->get('config_ssl') . 'image/' . $filess['image'];
								} else {
									$file_root = $this->config->get('config_url') . 'image/' . $filess['image'];
								}
								$attach_file[]=$file_root;
							}	
						}
					$product_id = $this->db->getLastId();
					$ptsmv_current_page='';
					if(isset($this->session->data['ptsmv_current_page'])) {						
						if($referrerUrl != '') {
						
							$email_code = 'customer_inquiry_email_with_producturl_and_referrerpage';
							$register_template = $this->model_extension_purpletree_multivendor_vendor->getSelleRegisterEmailTemplate($email_code);
							$subtemplatefromdb = $register_template['new_subject'];
							$messtemplatefromdb = $register_template['new_message'];
							$replacevarsub = array('_CUSTOMER_NAME_' => $customer_name);
							$email_subject = $this->model_extension_purpletree_multivendor_vendor->getmsgfromarray($replacevarsub,$subtemplatefromdb);
							$replacevar = array('_CUSTOMER_NAME_' =>$customer_name,
												'_CUSTOMER_EMAIL_' =>$customerrr['email'],
												'_CUSTOMERMESSAGE_' =>$this->request->post['customer_message'],
												'_PRODUCTURL_' =>$this->session->data['ptsmv_current_page'],
												'_REFERRERPAGE_' =>$referrerUrl
												);
							$email_message = $this->model_extension_purpletree_multivendor_vendor->getmsgfromarray($replacevar,$messtemplatefromdb);
					    }else{
						   $email_code = 'customer_inquiry_email_with_producturl_without_referrerpage';
							$register_template = $this->model_extension_purpletree_multivendor_vendor->getSelleRegisterEmailTemplate($email_code);
							$subtemplatefromdb = $register_template['new_subject'];
							$messtemplatefromdb = $register_template['new_message'];
							$replacevarsub = array('_CUSTOMER_NAME_' => $customer_name);
							$email_subject = $this->model_extension_purpletree_multivendor_vendor->getmsgfromarray($replacevarsub,$subtemplatefromdb);
							$replacevar = array('_CUSTOMER_NAME_' =>$customer_name,
												'_CUSTOMER_EMAIL_' =>$customerrr['email'],
												'_CUSTOMERMESSAGE_' =>$this->request->post['customer_message'],
												'_PRODUCTURL_' =>$this->session->data['ptsmv_current_page']
												);
							$email_message = $this->model_extension_purpletree_multivendor_vendor->getmsgfromarray($replacevar,$messtemplatefromdb);
						}
					}else{
					   if($referrerUrl != '') {						
							  $email_code = 'customer_inquiry_email_without_producturl_with_referrerpage';
							$register_template = $this->model_extension_purpletree_multivendor_vendor->getSelleRegisterEmailTemplate($email_code);
							$subtemplatefromdb = $register_template['new_subject'];
							$messtemplatefromdb = $register_template['new_message'];
							$replacevarsub = array('_CUSTOMER_NAME_' => $customer_name);
							$email_subject = $this->model_extension_purpletree_multivendor_vendor->getmsgfromarray($replacevarsub,$subtemplatefromdb);
							$replacevar = array('_CUSTOMER_NAME_' =>$customer_name,
												'_CUSTOMER_EMAIL_' =>$customerrr['email'],
												'_CUSTOMERMESSAGE_' =>$this->request->post['customer_message'],
												'_REFERRERPAGE_' =>$referrerUrl
												);
							$email_message = $this->model_extension_purpletree_multivendor_vendor->getmsgfromarray($replacevar,$messtemplatefromdb);
					    }else{
							 $email_code = 'customer_inquiry_email_without_producturl_and_without_referrerpage';
							$register_template = $this->model_extension_purpletree_multivendor_vendor->getSelleRegisterEmailTemplate($email_code);
							$subtemplatefromdb = $register_template['new_subject'];
							$messtemplatefromdb = $register_template['new_message'];
							$replacevarsub = array('_CUSTOMER_NAME_' => $customer_name);
							$email_subject = $this->model_extension_purpletree_multivendor_vendor->getmsgfromarray($replacevarsub,$subtemplatefromdb);
							$replacevar = array('_CUSTOMER_NAME_' =>$customer_name,
												'_CUSTOMER_EMAIL_' =>$customerrr['email'],
												'_CUSTOMERMESSAGE_' =>$this->request->post['customer_message']
												);
							$email_message = $this->model_extension_purpletree_multivendor_vendor->getmsgfromarray($replacevar,$messtemplatefromdb);
						}
					}					
					$reciver = $sellerr['email'];
					$this->model_extension_purpletree_multivendor_vendor->ptsSendMail($reciver,$email_subject,$email_message,$attach_file);
					
					$this->session->data['success'] = $this->language->get('text_success');
					unset($this->session->data['ptsmv_current_page']);
					} else {
					if (isset($this->request->post['customer_message'])) {
						$data['customer_message'] = $this->request->post['customer_message'];
						} else {
						$data['customer_message'] = '';
					}
				}
			}
			
			if (isset($this->session->data['success'])) {
				$data['success'] = $this->session->data['success'];
				
				unset($this->session->data['success']);
				} else {
				$data['success'] = '';
			}
			
			$data['breadcrumbs'] = array();
			
			$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/home','',true)
			);
			
			$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('extension/account/purpletree_multivendor/sellercontact/customercontactlist','',true)
			);
			
			$data['heading_title'] = $this->language->get('heading_title');
			
			$data['entry_name'] = $this->language->get('entry_name');
			$data['entry_email'] = $this->language->get('entry_email');
			$data['entry_enquiry'] = $this->language->get('entry_enquiry');
			
			if (isset($this->error['error_warning'])) {
				$data['error_warning'] = $this->error['error_warning'];
				} else {
				$data['error_warning'] = '';
			}
			if (isset($this->error['customer_message'])) {
				$data['error_enquiry'] = $this->error['customer_message'];
				} else {
				$data['error_enquiry'] = '';
			}
			if (isset($this->error['customer_name'])) {
				$data['error_name'] = $this->error['customer_name'];
				} else {
				$data['error_name'] = '';
			}
			
			if (isset($this->error['customer_email'])) {
				$data['error_email'] = $this->error['customer_email'];
				} else {
				$data['error_email'] = '';
			}
			$data['button_submit'] = $this->language->get('button_submit');
			$data['seller_id']=$seller_id;
			$data['sellercontacts'] = array();
			if ($this->customer->isLogged()) {
				$customer_id = $customerid;
				$results2 	= $this->model_extension_purpletree_multivendor_sellercontact->getSellerContactCustomerschat11($seller_id,$customer_id);

				$sellerr = $this->model_extension_purpletree_multivendor_sellercontact->getCustomer($seller_id);
				$message = array();
				$contact_from = array();
				$date_added = array();
				if(!empty($results2)) {
					foreach($results2 as $result){
						$attached_files 	= $this->model_extension_purpletree_multivendor_sellercontact->getAttachedEnquiriesFile($result['id']);
						$attachedFileLinks=array();
						if(!empty($attached_files)){
							foreach($attached_files as $filess){
								if ($this->request->server['HTTPS']) {
									$file_root = $this->config->get('config_ssl') . 'image/' . $filess['image'];
								} else {
									$file_root = $this->config->get('config_url') . 'image/' . $filess['image'];
								}
								$name = $filess['image_name'];
								$attachedFileLinks[]=array(
								'name'=>$name,
								'images'=>$file_root
								);
							}	
						}
						$data['sellercontacts'][] = array(
						'contact_from'     => $result['contact_from'],
						'customer_id'     => $result['customer_id'],
						'customer_name'     => $result['customer_name'].'<br>'.$sellerr['email'],
						'customer_email'     => $result['customer_email'],
						'customer_messages'       =>   html_entity_decode($result['customer_message'], ENT_QUOTES, 'UTF-8') . "\n",
						'date_added' => date($this->language->get('date_format_short'), strtotime($result['created_at'])),
						'attached_file'=>$attachedFileLinks 
						);
					}
				}
			}
			/* // Captcha
			$data['captcha'] = $this->load->controller('captcha/' . $this->config->get('config_captcha'), $this->error); */
			// Captcha
				if ($this->config->get('captcha_' . $this->config->get('config_captcha') . '_status') && in_array('contact', (array)$this->config->get('config_captcha_page'))) {
					$data['captcha'] = $this->load->controller('extension/captcha/' . $this->config->get('config_captcha'), $this->error);
				} else {
					$data['captcha'] = '';
				}
						$direction = $this->language->get('direction');
		 if ($direction=='rtl'){
			$this->document->addStyle('catalog/view/javascript/purpletree/bootstrap/css/bootstrap.min-a.css');
			$this->document->addStyle('catalog/view/theme/default/stylesheet/purpletree/custom-a.css'); 
			}else{
			$this->document->addStyle('catalog/view/javascript/purpletree/bootstrap/css/bootstrap.min.css'); 
			$this->document->addStyle('catalog/view/theme/default/stylesheet/purpletree/custom.css'); 
			}
			$data['column_left'] = $this->load->controller('common/column_left');
			$data['column_right'] = $this->load->controller('common/column_right');
			$data['content_top'] = $this->load->controller('common/content_top');
			$data['content_bottom'] = $this->load->controller('common/content_bottom');
			$data['footer'] = $this->load->controller('common/footer');
			$data['header'] = $this->load->controller('common/header');
			
			$this->response->setOutput($this->load->view('account/purpletree_multivendor/seller_reply', $data));
		}
		public function purpletreeStore404(){
			$this->load->language('purpletree_multivendor/sellercontact');
			
			$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_error'),
			'href' => $this->url->link('extension/account/purpletree_multivendor/sellerstore','',true)
			);
			$this->load->model('extension/purpletree_multivendor/dashboard');
			
			$this->model_extension_purpletree_multivendor_dashboard->checkSellerApproval();
			$this->document->setTitle($this->language->get('text_error'));
			
			$data['heading_title'] = $this->language->get('text_error');
			
			$data['text_error'] = $this->language->get('text_error');
			
			$data['button_continue'] = $this->language->get('button_continue');
			
			$data['continue'] = $this->url->link('common/home','',true);
			
			$this->response->addHeader($this->request->server['SERVER_PROTOCOL'] . ' 404 Not Found');
						$direction = $this->language->get('direction');
		 if ($direction=='rtl'){
			$this->document->addStyle('catalog/view/javascript/purpletree/bootstrap/css/bootstrap.min-a.css');
			$this->document->addStyle('catalog/view/theme/default/stylesheet/purpletree/custom-a.css'); 
			}else{
			$this->document->addStyle('catalog/view/javascript/purpletree/bootstrap/css/bootstrap.min.css'); 
			$this->document->addStyle('catalog/view/theme/default/stylesheet/purpletree/custom.css'); 
			}
			$data['column_left'] = $this->load->controller('common/column_left');
			$data['column_right'] = $this->load->controller('common/column_right');
			$data['content_top'] = $this->load->controller('common/content_top');
			$data['content_bottom'] = $this->load->controller('common/content_bottom');
			$data['footer'] = $this->load->controller('common/footer');
			$data['header'] = $this->load->controller('common/header');
			
			$this->response->setOutput($this->load->view('error/not_found', $data));
		}
	}
?>