<?php
class ControllerExtensionPurpletreeMultivendorSellerarea extends Controller {
		private $error = array();
		
		public function index() {		
			if (!$this->customer->validateSeller()) {
				$this->load->language('purpletree_multivendor/ptsmultivendor');
				$this->session->data['error_warning'] = $this->language->get('error_license');				
			}
			$this->load->language('purpletree_multivendor/vendor');
			$this->load->language('purpletree_multivendor/sellerarea');
			
			$this->document->setTitle($this->language->get('heading_title'));
			
			$this->load->model('extension/purpletree_multivendor/sellerarea');
			
			$this->getList();
		}
		
		public function add() {
			if (!$this->customer->validateSeller()) {
				$this->load->language('purpletree_multivendor/ptsmultivendor');
				$this->session->data['error_warning'] = $this->language->get('error_license');
				$this->response->redirect($this->url->link('extension/purpletree_multivendor/sellerarea', 'user_token=' . $this->session->data['user_token'] . $url, true));
			}
			$this->load->language('purpletree_multivendor/sellerarea');
			$this->document->setTitle($this->language->get('heading_title'));
			
			$this->load->model('extension/purpletree_multivendor/sellerarea');
			
			if (($this->request->server['REQUEST_METHOD'] == 'POST')  && $this->validateForm() ) {
				
				$this->model_extension_purpletree_multivendor_sellerarea->addSellerArea($this->request->post);
				
				$this->session->data['success'] = $this->language->get('text_success');
				
				$url = '';
				
				if (isset($this->request->get['sort'])) {
					$url .= '&sort=' . $this->request->get['sort'];
				}
				
				if (isset($this->request->get['order'])) {
					$url .= '&order=' . $this->request->get['order'];
				}
				
				if (isset($this->request->get['page'])) {
					$url .= '&page=' . $this->request->get['page'];
				}
				
				$this->response->redirect($this->url->link('extension/purpletree_multivendor/sellerarea', 'user_token=' . $this->session->data['user_token'] . $url, true));
			} 
			$this->getForm();
		}
		
		public function edit() {
			if (!$this->customer->validateSeller()) {
				$this->load->language('purpletree_multivendor/ptsmultivendor');
				$this->session->data['error_warning'] = $this->language->get('error_license');
				$this->response->redirect($this->url->link('extension/purpletree_multivendor/sellerarea', 'user_token=' . $this->session->data['user_token'] . $url, true));
			}
			$this->load->language('purpletree_multivendor/sellerarea');
			
			$this->document->setTitle($this->language->get('heading_title'));
			
			$this->load->model('extension/purpletree_multivendor/sellerarea');
			
			if (($this->request->server['REQUEST_METHOD'] == 'POST')  && $this->validateForm() ) {				
				$this->model_extension_purpletree_multivendor_sellerarea->editSellerArea($this->request->get['area_id'], $this->request->post);
				
				$this->session->data['success'] = $this->language->get('text_success');
				
				$url = '';
				
				if (isset($this->request->get['sort'])) {
					$url .= '&sort=' . $this->request->get['sort'];
				}
				
				if (isset($this->request->get['order'])) {
					$url .= '&order=' . $this->request->get['order'];
				}
				
				if (isset($this->request->get['page'])) {
					$url .= '&page=' . $this->request->get['page'];
				}
				
				$this->response->redirect($this->url->link('extension/purpletree_multivendor/sellerarea', 'user_token=' . $this->session->data['user_token'] . $url, true));
			}
			
			$this->getForm();
		}
		
		public function delete() {
			if (!$this->customer->validateSeller()) {
				$this->load->language('purpletree_multivendor/ptsmultivendor');
				$this->session->data['error_warning'] = $this->language->get('error_license');
				$this->response->redirect($this->url->link('extension/purpletree_multivendor/sellerarea', 'user_token=' . $this->session->data['user_token'] . $url, true));
			}
			$this->load->language('purpletree_multivendor/sellerarea');
			
			$this->document->setTitle($this->language->get('heading_title'));
			
			$this->load->model('extension/purpletree_multivendor/sellerarea');
			
			if (isset($this->request->post['selected']) && $this->validateDelete()) {
				
				$url = '';
				
				foreach ($this->request->post['selected'] as $area_id) {
					$check_area ='';
					//$check_area = $this->model_extension_purpletree_multivendor_sellerarea->checkSellerArea($area_id);
					
					if(!empty($check_area)){
						$this->session->data['error_warning'] = $this->language->get('error_permission');
						}else{
						try {     
							
							$this->model_extension_purpletree_multivendor_sellerarea->deleteSellerArea($area_id);		
							
							$this->session->data['success'] = $this->language->get('text_success');
						}
						catch (Exception $e) {
							$this->session->data['error_warning'] = $e->getMessage();
						}
						
					}
					
				}
				
				if (isset($this->request->get['sort'])) {
					$url .= '&sort=' . $this->request->get['sort'];
				}
				
				if (isset($this->request->get['order'])) {
					$url .= '&order=' . $this->request->get['order'];
				}
				
				if (isset($this->request->get['page'])) {
					$url .= '&page=' . $this->request->get['page'];
				}
				
				$this->response->redirect($this->url->link('extension/purpletree_multivendor/sellerarea', 'user_token=' . $this->session->data['user_token'] . $url, true));
			}
			
			$this->getList();
		}
		protected function getList() {
			
			$url = '';
			
			$data['breadcrumbs'] = array();
			
			$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
			);
			
			$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('extension/purpletree_multivendor/sellerarea', 'user_token=' . $this->session->data['user_token'] . $url, true)
			);
			
			$data['add'] = $this->url->link('extension/purpletree_multivendor/sellerarea/add', 'user_token=' . $this->session->data['user_token'] . $url, true);
			$data['delete'] = $this->url->link('extension/purpletree_multivendor/sellerarea/delete', 'user_token=' . $this->session->data['user_token'] . $url, true);
			$data['repair'] = $this->url->link('extension/purpletree_multivendor/sellerarea/repair', 'user_token=' . $this->session->data['user_token'] . $url, true);
			
			$data['sellerareas'] = array();
			
			$results = $this->model_extension_purpletree_multivendor_sellerarea->getSellerAreas();
			$text_enabled =  $this->language->get('text_enabled');
			$text_disabled =  $this->language->get('text_disabled');
			foreach ($results as $result) {				
				$data['sellerareas'][] = array(
				'area_id' => $result['area_id'],
				'area_name'        => $result['area_name'],
				'status'       	   => ($result['status'])? $text_enabled :$text_disabled,
				'sort_order'    => $result['sort_order'],				
				'edit'        => $this->url->link('extension/purpletree_multivendor/sellerarea/edit', 'user_token=' . $this->session->data['user_token'] . '&area_id=' . $result['area_id'] . $url, true),
				'delete'      => $this->url->link('extension/purpletree_multivendor/sellerarea/delete', 'user_token=' . $this->session->data['user_token'] . '&area_id=' . $result['area_id'] . $url, true)
				);
			} 
			
			if (isset($this->error['warning'])) {
				$data['error_warning'] = $this->error['warning'];
				}elseif (isset($this->session->data['error_warning'])) {
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
			
			if (isset($this->request->post['selected'])) {
				$data['selected'] = (array)$this->request->post['selected'];
				} else {
				$data['selected'] = array();
			}
			
			$url = '';
			
			$data['header'] = $this->load->controller('common/header');
			$data['column_left'] = $this->load->controller('common/column_left');
			$data['footer'] = $this->load->controller('common/footer');
			
			$this->response->setOutput($this->load->view('extension/purpletree_multivendor/sellerarea_list', $data));
		}
		
		protected function getForm() {
			$data['text_form'] = !isset($this->request->get['area_id']) ? $this->language->get('text_add') : $this->language->get('text_edit');
			
			if (isset($this->error['warning'])) {
				$data['error_warning'] = $this->error['warning'];
				} else {
				$data['error_warning'] = '';
			}
			
			
			if (isset($this->error['name'])) {
				$data['error_name'] = $this->error['name'];
				} else {
				$data['error_name'] = array();
			}
			
			
			
			if (isset($this->error['sort_order'])) {
				$data['error_sort_order'] = $this->error['sort_order'];
				} else {
				$data['error_sort_order'] = '';
			}
			
			
			
			$url = '';
			
			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}
			
			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}
			
			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}
			
			$data['breadcrumbs'] = array();
			
			$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
			);
			
			$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('extension/purpletree_multivendor/sellerarea', 'user_token=' . $this->session->data['user_token'] . $url, true)
			);
			$this->load->language('purpletree_multivendor/sellerarea');
			$data['entry_joinning_fee']=$this->language->get('entry_joinning_fee');
			$data['entry_default_subscription_plan']=$this->language->get('entry_default_subscription_plan');
			
			if (!isset($this->request->get['area_id'])) {
				$data['action'] = $this->url->link('extension/purpletree_multivendor/sellerarea/add', 'user_token=' . $this->session->data['user_token'] . $url, true);
				} else {
				$data['action'] = $this->url->link('extension/purpletree_multivendor/sellerarea/edit', 'user_token=' . $this->session->data['user_token'] . '&area_id=' . $this->request->get['area_id'] . $url, true);
			}
			
			$data['cancel'] = $this->url->link('extension/purpletree_multivendor/sellerarea', 'user_token=' . $this->session->data['user_token'] . $url, true);
			
			
			$data['user_token'] = $this->session->data['user_token'];
			
			$this->load->model('localisation/language');
			
			$data['languages'] = $this->model_localisation_language->getLanguages();
			
			if (isset($this->request->post['sellerarea'])) {
				$data['sellerarea'] = $this->request->post['sellerarea'];
				} elseif (isset($this->request->get['area_id'])) {
				$data['sellerarea'] = $this->model_extension_purpletree_multivendor_sellerarea->getSellerAreaDescriptions($this->request->get['area_id']);
				} else {
				$data['subscription'] = array();
			}
			
			if (isset($this->request->get['area_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
				$seller_area_info = $this->model_extension_purpletree_multivendor_sellerarea->getArea($this->request->get['area_id']);		
			}
			
			if (isset($this->request->post['status'])) {
				$data['status'] = $this->request->post['status'];
				} elseif (!empty($seller_area_info)) {
				$data['status'] = $seller_area_info['status'];
				} else {
				$data['status'] = '';
			}
			
			if (isset($this->request->post['sort_order'])) {
				$data['sort_order'] = $this->request->post['sort_order'];
				} elseif (!empty($seller_area_info)) {
				$data['sort_order'] = $seller_area_info['sort_order'];
				} else {
				$data['sort_order'] = 0;
			}
			$data['ver']=VERSION;
			if($data['ver']=='3.1.0.0_b'){
				$this->document->addScriptpts('view/javascript/ckeditor/ckeditor.js');
				$this->document->addScriptpts('view/javascript/ckeditor/adapters/jquery.js');
			}
			$data['header'] = $this->load->controller('common/header');
			$data['column_left'] = $this->load->controller('common/column_left');
			$data['footer'] = $this->load->controller('common/footer');
			
			$this->response->setOutput($this->load->view('extension/purpletree_multivendor/sellerarea_form', $data));
		}
		
		protected function validateForm() {
			foreach ($this->request->post['sellerarea'] as $language_id => $value) {
				if ((utf8_strlen($value['name']) < 1) || (utf8_strlen($value['name']) > 255)) {
					$this->error['name'][$language_id] = $this->language->get('error_name');
				}
				
			}
			
			if($this->request->post['sort_order'] == '') {
				$this->error['sort_order']= $this->language->get('error_sort_order');
			}
			
			
			return !$this->error;
		}
		
		protected function validateDelete() {
			if (!$this->user->hasPermission('modify', 'extension/purpletree_multivendor/sellerarea')) {
				$this->error['warning'] = $this->language->get('error_permission');
			}
			
			return !$this->error;
		}
}