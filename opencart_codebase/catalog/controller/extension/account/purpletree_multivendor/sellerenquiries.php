<?php
class ControllerExtensionAccountPurpletreeMultivendorSellerenquiries extends Controller {
		private $error = array();
		
		public function index(){
			if (!$this->customer->isLogged()) {
				$this->session->data['redirect'] = $this->url->link('extension/account/purpletree_multivendor/sellerstore', '', true);
				$this->response->redirect($this->url->link('account/login', '', true));
			}
			$this->load->model('extension/purpletree_multivendor/dashboard');
			
			$this->model_extension_purpletree_multivendor_dashboard->checkSellerApproval();
			
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
			if(!$this->customer->validateSeller()) {
				$this->load->language('purpletree_multivendor/ptsmultivendor');
				$this->session->data['error_warning'] = $this->language->get('error_license');
				$this->response->redirect($this->url->link('account/account', '', true));
			}
			$this->load->language('purpletree_multivendor/sellerenquiries');
			$this->document->setTitle($this->language->get('heading_title'));
			$this->load->model('extension/purpletree_multivendor/sellerenquiries');
			$data['seller_id'] = $this->customer->getId();
			$this->model_extension_purpletree_multivendor_sellerenquiries->updateSeenMessage($data['seller_id']);	
			if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validatemessage()) {
				
				$this->model_extension_purpletree_multivendor_sellerenquiries->sendSellerMessage($this->request->post);
				
				//seller to admin mail notification
				$seller_info = $this->model_extension_purpletree_multivendor_sellerenquiries->getSellername($data['seller_id']);
				$seller_name = $seller_info['seller_name'];
				$store_name = $seller_info['store_name'];
				$seller_email = $seller_info['email'];
				$this->load->model('extension/purpletree_multivendor/vendor');
			    $email_code = 'seller_email_message_to_admin';
				$register_template = $this->model_extension_purpletree_multivendor_vendor->getSelleRegisterEmailTemplate($email_code);				
				$messtemplatefromdb = $register_template['new_message'];				
				$email_subject =  $register_template['new_subject'];
				$replacevar = array('_SELLERNAME_' => $seller_name,
				                    '_STORENAME_' => $store_name,
				                    '_SELLEREMAIL_' => $seller_email,
									'_MESSAGE_' =>$this->request->post['message']
									);
				$email_message = $this->model_extension_purpletree_multivendor_vendor->getmsgfromarray($replacevar,$messtemplatefromdb);
				$reciver = $this->config->get('config_email');
				$this->model_extension_purpletree_multivendor_vendor->ptsSendMail($reciver,$email_subject,$email_message);
				
				$this->session->data['success'] = $this->language->get('text_success');
				
				$this->response->redirect($this->url->link('extension/account/purpletree_multivendor/sellerenquiries','',true));
			}
			$data['breadcrumbs'] = array();
			$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/home','',true)
			);
			$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_store'),
			'href' => $this->url->link('extension/account/purpletree_multivendor/sellerenquiries', '', true)
			);
			
			$data['heading_title'] = $this->language->get('heading_title');
			
			$data['button_continue'] = $this->language->get('button_save');
			$data['button_back'] = $this->language->get('button_back');
			$data['entry_message'] = $this->language->get('entry_message');
			$data['entry_messages'] = $this->language->get('entry_messages');
			$data['error_enquiry'] = $this->language->get('error_enquiry');
			
			if (isset($store_id)) {
				$data['store_id'] = $store_id;
				} else {
				$data['store_id'] = 0;
			}
			
			if (isset($this->error['error_warning'])) {
				$data['error_warning'] = $this->error['error_warning'];
				} else {
				$data['error_warning'] = '';
			}
			if (isset($this->error['error_msg'])) {
				$data['error_msg'] = $this->error['error_msg'];
				} else {
				$data['error_msg'] = '';
			}
			
			if (isset($this->session->data['success'])) {
				$data['success'] = $this->session->data['success'];
				
				unset($this->session->data['success']);
				} else {
				$data['success'] = '';
			}
			
			if (isset($this->request->get['page'])) {
				$page = $this->request->get['page'];
				} else {
				$page = 1;
			}
			$url ='';
			
			$filter_data = array(
			'start'                => ($page - 1) * $this->config->get('config_limit_admin'),
			'limit'                => $this->config->get('config_limit_admin'),
			'seller_id'            => $this->customer->getId()
			);
			$data['messages']=$this->model_extension_purpletree_multivendor_sellerenquiries->getSellerMessage($filter_data);
			$order_total = $this->model_extension_purpletree_multivendor_sellerenquiries->getTotalSellerMessage($filter_data);
			
			$pagination = new Pagination();
			$pagination->total = $order_total;
			$pagination->page = $page;
			$pagination->limit = $this->config->get('config_limit_admin');
			$pagination->url = $this->url->link('extension/account/purpletree_multivendor/sellerenquiries', $url . '&page={page}', true);
			
			$data['pagination'] = $pagination->render();
			
			$data['results'] = sprintf($this->language->get('text_pagination'), ($order_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($order_total - $this->config->get('config_limit_admin'))) ? $order_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $order_total, ceil($order_total / $this->config->get('config_limit_admin')));
			
			
			$data['column_left'] = $this->load->controller('extension/account/purpletree_multivendor/common/column_left');
			$data['footer'] = $this->load->controller('extension/account/purpletree_multivendor/common/footer');
			$data['header'] = $this->load->controller('extension/account/purpletree_multivendor/common/header');
			
			$this->response->setOutput($this->load->view('account/purpletree_multivendor/sellerenquiries', $data));
		}	
		
		protected function validatemessage() {
			if(!$this->customer->validateSeller()) {
				$this->load->language('purpletree_multivendor/sellerenquiries');
				$this->error['error_warning'] = $this->language->get('error_license');
			}
			if (utf8_strlen($this->request->post['message']) < 1) {
				$this->error['error_msg'] = $this->language->get('error_enquiry');
			}
			return !$this->error;
		}
		
	}