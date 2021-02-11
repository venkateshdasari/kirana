<?php
class ControllerExtensionAccountPurpletreeMultivendorProductReturns extends Controller {
		private $error = array();
		
		public function index() {
			if (!$this->customer->isLogged()) {
				$this->session->data['redirect'] = $this->url->link('extension/account/purpletree_multivendor/sellerproduct', '', true);
				
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
			$this->load->language('purpletree_multivendor/product_returns');
			
			$this->document->setTitle($this->language->get('heading_title'));
			
			$this->load->model('extension/purpletree_multivendor/product_returns');
			
			$this->getList();
		}
		
		
		
		public function edit() {
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
			$this->load->language('purpletree_multivendor/product_returns');
			
			$this->document->setTitle($this->language->get('heading_title'));
			
			$this->load->model('extension/purpletree_multivendor/product_returns');
			
			$this->load->model('extension/purpletree_multivendor/dashboard');
			
			$this->model_extension_purpletree_multivendor_dashboard->checkSellerApproval();
			
			if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
				$this->model_extension_purpletree_multivendor_product_returns->editReturn($this->request->get['return_id'], $this->request->post);
				
				$this->session->data['success'] = $this->language->get('text_success');
				
				$url = '';
				
				if (isset($this->request->get['filter_return_id'])) {
					$url .= '&filter_return_id=' . $this->request->get['filter_return_id'];
				}
				
				if (isset($this->request->get['filter_order_id'])) {
					$url .= '&filter_order_id=' . $this->request->get['filter_order_id'];
				}
				
				if (isset($this->request->get['filter_customer'])) {
					$url .= '&filter_customer=' . urlencode(html_entity_decode($this->request->get['filter_customer'], ENT_QUOTES, 'UTF-8'));
				}
				
				if (isset($this->request->get['filter_product'])) {
					$url .= '&filter_product=' . urlencode(html_entity_decode($this->request->get['filter_product'], ENT_QUOTES, 'UTF-8'));
				}
				
				if (isset($this->request->get['filter_model'])) {
					$url .= '&filter_model=' . urlencode(html_entity_decode($this->request->get['filter_model'], ENT_QUOTES, 'UTF-8'));
				}
				
				if (isset($this->request->get['filter_return_status_id'])) {
					$url .= '&filter_return_status_id=' . $this->request->get['filter_return_status_id'];
				}
				
				if (isset($this->request->get['filter_date_added'])) {
					$url .= '&filter_date_added=' . $this->request->get['filter_date_added'];
				}
				
				if (isset($this->request->get['filter_date_modified'])) {
					$url .= '&filter_date_modified=' . $this->request->get['filter_date_modified'];
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
				
				$this->response->redirect($this->url->link('extension/account/purpletree_multivendor/product_returns',$url, true));
			}
			
			$this->getForm();
		}
		protected function getList() {
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
			$this->document->addScriptpts('catalog/view/javascript/purpletree/jquery/datetimepicker/moment/moment.min.js'); 
			$this->document->addScriptpts('catalog/view/javascript/purpletree/jquery/datetimepicker/moment/moment-with-locales.min.js'); 
			$this->document->addScriptpts('catalog/view/javascript/purpletree/jquery/datetimepicker/bootstrap-datetimepicker.min.js'); 
			$this->document->addStylepts('catalog/view/javascript/purpletree/jquery/datetimepicker/bootstrap-datetimepicker.min.css');
			
			$this->load->model('extension/purpletree_multivendor/dashboard');
			
			$this->model_extension_purpletree_multivendor_dashboard->checkSellerApproval();
			if (isset($this->request->get['filter_return_id'])) {
				$filter_return_id = $this->request->get['filter_return_id'];
				} else {
				$filter_return_id = '';
			}
			
			if (isset($this->request->get['filter_order_id'])) {
				$filter_order_id = $this->request->get['filter_order_id'];
				} else {
				$filter_order_id = '';
			}
			
			if (isset($this->request->get['filter_customer'])) {
				$filter_customer = $this->request->get['filter_customer'];
				} else {
				$filter_customer = '';
			}
			
			if (isset($this->request->get['filter_product'])) {
				$filter_product = $this->request->get['filter_product'];
				} else {
				$filter_product = '';
			}
			
			if (isset($this->request->get['filter_model'])) {
				$filter_model = $this->request->get['filter_model'];
				} else {
				$filter_model = '';
			}
			
			if (isset($this->request->get['filter_return_status_id'])) {
				$filter_return_status_id = $this->request->get['filter_return_status_id'];
				} else {
				$filter_return_status_id = '';
			}
			
			if (isset($this->request->get['filter_date_added'])) {
				$filter_date_added = $this->request->get['filter_date_added'];
				} else {
				$filter_date_added = '';
			}
			
			if (isset($this->request->get['filter_date_modified'])) {
				$filter_date_modified = $this->request->get['filter_date_modified'];
				} else {
				$filter_date_modified = '';
			}
			
			if (isset($this->request->get['sort'])) {
				$sort = $this->request->get['sort'];
				} else {
				$sort = 'r.return_id';
			}
			
			if (isset($this->request->get['order'])) {
				$order = $this->request->get['order'];
				} else {
				$order = 'DESC';
			}
			
			if (isset($this->request->get['page'])) {
				$page = $this->request->get['page'];
				} else {
				$page = 1;
			}
			
			$url = '';
			
			if (isset($this->request->get['filter_return_id'])) {
				$url .= '&filter_return_id=' . $this->request->get['filter_return_id'];
			}
			
			if (isset($this->request->get['filter_order_id'])) {
				$url .= '&filter_order_id=' . $this->request->get['filter_order_id'];
			}
			
			if (isset($this->request->get['filter_customer'])) {
				$url .= '&filter_customer=' . urlencode(html_entity_decode($this->request->get['filter_customer'], ENT_QUOTES, 'UTF-8'));
			}
			
			if (isset($this->request->get['filter_product'])) {
				$url .= '&filter_product=' . urlencode(html_entity_decode($this->request->get['filter_product'], ENT_QUOTES, 'UTF-8'));
			}
			
			if (isset($this->request->get['filter_model'])) {
				$url .= '&filter_model=' . urlencode(html_entity_decode($this->request->get['filter_model'], ENT_QUOTES, 'UTF-8'));
			}
			
			if (isset($this->request->get['filter_return_status_id'])) {
				$url .= '&filter_return_status_id=' . $this->request->get['filter_return_status_id'];
			}
			
			if (isset($this->request->get['filter_date_added'])) {
				$url .= '&filter_date_added=' . $this->request->get['filter_date_added'];
			}
			
			if (isset($this->request->get['filter_date_modified'])) {
				$url .= '&filter_date_modified=' . $this->request->get['filter_date_modified'];
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
			
			$data['breadcrumbs'] = array();
			$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/home','',true)
			);
			
			$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('extension/account/purpletree_multivendor/product_returns', $url, true)
			);
			$data['heading_title'] = $this->language->get('heading_title');
			$data['text_all'] = $this->language->get('text_all');
			$data['add'] = $this->url->link('extension/account/purpletree_multivendor/product_returns/add', $url, true);
			$data['delete'] = $this->url->link('extension/account/purpletree_multivendor/product_returns/delete', $url, true);
			
			$data['returns'] = array();
			
			$filter_data = array(
			'filter_return_id'        => $filter_return_id,
			'filter_order_id'         => $filter_order_id,
			'filter_customer'         => $filter_customer,
			'filter_product'          => $filter_product,
			'filter_model'            => $filter_model,
			'filter_return_status_id' => $filter_return_status_id,
			'filter_date_added'       => $filter_date_added,
			'filter_date_modified'    => $filter_date_modified,
			'sort'                    => $sort,
			'order'                   => $order,
			'start'                   => ($page - 1) * $this->config->get('config_limit_admin'),
			'limit'                   => $this->config->get('config_limit_admin')
			);
			
			$return_total = $this->model_extension_purpletree_multivendor_product_returns->getTotalReturns($filter_data);
			
			$results = $this->model_extension_purpletree_multivendor_product_returns->getReturns($filter_data);
			
			foreach ($results as $result) {
				$data['returns'][] = array(
				'return_id'     => $result['return_id'],
				'order_id'      => $result['order_id'],
				'customer'      => $result['customer'],
				'product'       => $result['product'],
				'model'         => $result['model'],
				'return_status' => $result['return_status'],
				'date_added'    => date($this->language->get('date_format_short'), strtotime($result['date_added'])),
				'date_modified' => date($this->language->get('date_format_short'), strtotime($result['date_modified'])),
				'edit'          => $this->url->link('extension/account/purpletree_multivendor/product_returns/edit','&return_id=' . $result['return_id'] . $url, true)
				);
			}
			
			if (isset($this->session->data['error'])) {
				$data['error_warning'] = $this->session->data['error'];
				
				unset($this->session->data['error']);
				} elseif (isset($this->error['warning'])) {
				$data['error_warning'] = $this->error['warning'];
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
			
			if (isset($this->request->get['filter_return_id'])) {
				$url .= '&filter_return_id=' . $this->request->get['filter_return_id'];
			}
			
			if (isset($this->request->get['filter_order_id'])) {
				$url .= '&filter_order_id=' . $this->request->get['filter_order_id'];
			}
			
			if (isset($this->request->get['filter_customer'])) {
				$url .= '&filter_customer=' . urlencode(html_entity_decode($this->request->get['filter_customer'], ENT_QUOTES, 'UTF-8'));
			}
			
			if (isset($this->request->get['filter_product'])) {
				$url .= '&filter_product=' . urlencode(html_entity_decode($this->request->get['filter_product'], ENT_QUOTES, 'UTF-8'));
			}
			
			if (isset($this->request->get['filter_model'])) {
				$url .= '&filter_model=' . urlencode(html_entity_decode($this->request->get['filter_model'], ENT_QUOTES, 'UTF-8'));
			}
			
			if (isset($this->request->get['filter_return_status_id'])) {
				$url .= '&filter_return_status_id=' . $this->request->get['filter_return_status_id'];
			}
			
			if (isset($this->request->get['filter_date_added'])) {
				$url .= '&filter_date_added=' . $this->request->get['filter_date_added'];
			}
			
			if (isset($this->request->get['filter_date_modified'])) {
				$url .= '&filter_date_modified=' . $this->request->get['filter_date_modified'];
			}
			
			if ($order == 'ASC') {
				$url .= '&order=DESC';
				} else {
				$url .= '&order=ASC';
			}
			
			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}
			
			$data['sort_return_id'] = $this->url->link('extension/account/purpletree_multivendor/product_returns', $url, true);
			$data['sort_order_id'] = $this->url->link('extension/account/purpletree_multivendor/product_returns', $url, true);
			$data['sort_customer'] = $this->url->link('extension/account/purpletree_multivendor/product_returns', $url, true);
			$data['sort_product'] = $this->url->link('extension/account/purpletree_multivendor/product_returns', $url, true);
			$data['sort_model'] = $this->url->link('extension/account/purpletree_multivendor/product_returns', $url, true);
			$data['sort_status'] = $this->url->link('extension/account/purpletree_multivendor/product_returns', $url, true);
			$data['sort_date_added'] = $this->url->link('extension/account/purpletree_multivendor/product_returns', $url, true);
			$data['sort_date_modified'] = $this->url->link('extension/account/purpletree_multivendor/product_returns', $url, true);
			
			$url = '';
			
			if (isset($this->request->get['filter_return_id'])) {
				$url .= '&filter_return_id=' . $this->request->get['filter_return_id'];
			}
			
			if (isset($this->request->get['filter_order_id'])) {
				$url .= '&filter_order_id=' . $this->request->get['filter_order_id'];
			}
			
			if (isset($this->request->get['filter_customer'])) {
				$url .= '&filter_customer=' . urlencode(html_entity_decode($this->request->get['filter_customer'], ENT_QUOTES, 'UTF-8'));
			}
			
			if (isset($this->request->get['filter_product'])) {
				$url .= '&filter_product=' . urlencode(html_entity_decode($this->request->get['filter_product'], ENT_QUOTES, 'UTF-8'));
			}
			
			if (isset($this->request->get['filter_model'])) {
				$url .= '&filter_model=' . urlencode(html_entity_decode($this->request->get['filter_model'], ENT_QUOTES, 'UTF-8'));
			}
			
			if (isset($this->request->get['filter_return_status_id'])) {
				$url .= '&filter_return_status_id=' . $this->request->get['filter_return_status_id'];
			}
			
			if (isset($this->request->get['filter_date_added'])) {
				$url .= '&filter_date_added=' . $this->request->get['filter_date_added'];
			}
			
			if (isset($this->request->get['filter_date_modified'])) {
				$url .= '&filter_date_modified=' . $this->request->get['filter_date_modified'];
			}
			
			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}
			
			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}
			
			$pagination = new Pagination();
			$pagination->total = $return_total;
			$pagination->page = $page;
			$pagination->limit = $this->config->get('config_limit_admin');
			$pagination->url = $this->url->link('extension/account/purpletree_multivendor/product_returns', $url . '&page={page}', true);
			
			$data['pagination'] = $pagination->render();
			
			$data['results'] = sprintf($this->language->get('text_pagination'), ($return_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($return_total - $this->config->get('config_limit_admin'))) ? $return_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $return_total, ceil($return_total / $this->config->get('config_limit_admin')));
			
			$data['filter_return_id'] = $filter_return_id;
			$data['filter_order_id'] = $filter_order_id;
			$data['filter_customer'] = $filter_customer;
			$data['filter_product'] = $filter_product;
			$data['filter_model'] = $filter_model;
			$data['filter_return_status_id'] = $filter_return_status_id;
			$data['filter_date_added'] = $filter_date_added;
			$data['filter_date_modified'] = $filter_date_modified;
			$data['return_statuses'] = $this->model_extension_purpletree_multivendor_product_returns->getReturnStatuses();
			
			$data['sort'] = $sort;
			$data['order'] = $order;
			$data['column_left'] = $this->load->controller('extension/account/purpletree_multivendor/common/column_left');
			$data['footer'] = $this->load->controller('extension/account/purpletree_multivendor/common/footer');
			$data['header'] = $this->load->controller('extension/account/purpletree_multivendor/common/header');
			
			$this->response->setOutput($this->load->view('account/purpletree_multivendor/product_returns_list', $data));
		}
		
		protected function getForm() {
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
			$data['text_form'] = !isset($this->request->get['return_id']) ? $this->language->get('text_add') : $this->language->get('text_edit');
			
			$this->load->model('extension/purpletree_multivendor/dashboard');
			
			$this->model_extension_purpletree_multivendor_dashboard->checkSellerApproval();
			
			$data['heading_title'] = $this->language->get('heading_title');
			
			if (isset($this->request->get['return_id'])) {
				$data['return_id'] = $this->request->get['return_id'];
				} else {
				$data['return_id'] = 0;
			}
			
			if (isset($this->error['warning'])) {
				$data['error_warning'] = $this->error['warning'];
				} else {
				$data['error_warning'] = '';
			}
			
			if (isset($this->error['order_id'])) {
				$data['error_order_id'] = $this->error['order_id'];
				} else {
				$data['error_order_id'] = '';
			}
			
			if (isset($this->error['firstname'])) {
				$data['error_firstname'] = $this->error['firstname'];
				} else {
				$data['error_firstname'] = '';
			}
			
			if (isset($this->error['lastname'])) {
				$data['error_lastname'] = $this->error['lastname'];
				} else {
				$data['error_lastname'] = '';
			}
			
			if (isset($this->error['email'])) {
				$data['error_email'] = $this->error['email'];
				} else {
				$data['error_email'] = '';
			}
			
			if (isset($this->error['telephone'])) {
				$data['error_telephone'] = $this->error['telephone'];
				} else {
				$data['error_telephone'] = '';
			}
			
			if (isset($this->error['product'])) {
				$data['error_product'] = $this->error['product'];
				} else {
				$data['error_product'] = '';
			}
			
			if (isset($this->error['model'])) {
				$data['error_model'] = $this->error['model'];
				} else {
				$data['error_model'] = '';
			}
			
			$url = '';
			
			if (isset($this->request->get['filter_return_id'])) {
				$url .= '&filter_return_id=' . $this->request->get['filter_return_id'];
			}
			
			if (isset($this->request->get['filter_order_id'])) {
				$url .= '&filter_order_id=' . $this->request->get['filter_order_id'];
			}
			
			if (isset($this->request->get['filter_customer'])) {
				$url .= '&filter_customer=' . urlencode(html_entity_decode($this->request->get['filter_customer'], ENT_QUOTES, 'UTF-8'));
			}
			
			if (isset($this->request->get['filter_product'])) {
				$url .= '&filter_product=' . urlencode(html_entity_decode($this->request->get['filter_product'], ENT_QUOTES, 'UTF-8'));
			}
			
			if (isset($this->request->get['filter_model'])) {
				$url .= '&filter_model=' . urlencode(html_entity_decode($this->request->get['filter_model'], ENT_QUOTES, 'UTF-8'));
			}
			
			if (isset($this->request->get['filter_return_status_id'])) {
				$url .= '&filter_return_status_id=' . $this->request->get['filter_return_status_id'];
			}
			
			if (isset($this->request->get['filter_date_added'])) {
				$url .= '&filter_date_added=' . $this->request->get['filter_date_added'];
			}
			
			if (isset($this->request->get['filter_date_modified'])) {
				$url .= '&filter_date_modified=' . $this->request->get['filter_date_modified'];
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
			
			$data['breadcrumbs'] = array();
			
			$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/home','',true)
			);
			
			$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('extension/account/purpletree_multivendor/product_returns', $url, true)
			);
			
			
			$data['action'] = $this->url->link('extension/account/purpletree_multivendor/product_returns/edit','&return_id=' . $this->request->get['return_id'] . $url, true);
			
			
			$data['cancel'] = $this->url->link('extension/account/purpletree_multivendor/product_returns', $url, true);
			
			if (isset($this->request->get['return_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
				$return_info = $this->model_extension_purpletree_multivendor_product_returns->getReturn($this->request->get['return_id']);
			}
			
			if (isset($this->request->post['order_id'])) {
				$data['order_id'] = $this->request->post['order_id'];
				} elseif (!empty($return_info)) {
				$data['order_id'] = $return_info['order_id'];
				} else {
				$data['order_id'] = '';
			}
			
			if (isset($this->request->post['date_ordered'])) {
				$data['date_ordered'] = $this->request->post['date_ordered'];
				} elseif (!empty($return_info)) {
				$data['date_ordered'] = ($return_info['date_ordered'] != '0000-00-00' ? $return_info['date_ordered'] : '');
				} else {
				$data['date_ordered'] = '';
			}
			
			if (isset($this->request->post['customer'])) {
				$data['customer'] = $this->request->post['customer'];
				} elseif (!empty($return_info)) {
				$data['customer'] = $return_info['customer'];
				} else {
				$data['customer'] = '';
			}
			
			if (isset($this->request->post['customer_id'])) {
				$data['customer_id'] = $this->request->post['customer_id'];
				} elseif (!empty($return_info)) {
				$data['customer_id'] = $return_info['customer_id'];
				} else {
				$data['customer_id'] = '';
			}
			
			if (isset($this->request->post['firstname'])) {
				$data['firstname'] = $this->request->post['firstname'];
				} elseif (!empty($return_info)) {
				$data['firstname'] = $return_info['firstname'];
				} else {
				$data['firstname'] = '';
			}
			
			if (isset($this->request->post['lastname'])) {
				$data['lastname'] = $this->request->post['lastname'];
				} elseif (!empty($return_info)) {
				$data['lastname'] = $return_info['lastname'];
				} else {
				$data['lastname'] = '';
			}
			
			if (isset($this->request->post['email'])) {
				$data['email'] = $this->request->post['email'];
				} elseif (!empty($return_info)) {
				$data['email'] = $return_info['email'];
				} else {
				$data['email'] = '';
			}
			
			if (isset($this->request->post['telephone'])) {
				$data['telephone'] = $this->request->post['telephone'];
				} elseif (!empty($return_info)) {
				$data['telephone'] = $return_info['telephone'];
				} else {
				$data['telephone'] = '';
			}
			
			if (isset($this->request->post['product'])) {
				$data['product'] = $this->request->post['product'];
				} elseif (!empty($return_info)) {
				$data['product'] = $return_info['product'];
				} else {
				$data['product'] = '';
			}
			
			if (isset($this->request->post['product_id'])) {
				$data['product_id'] = $this->request->post['product_id'];
				} elseif (!empty($return_info)) {
				$data['product_id'] = $return_info['product_id'];
				} else {
				$data['product_id'] = '';
			}
			
			if (isset($this->request->post['model'])) {
				$data['model'] = $this->request->post['model'];
				} elseif (!empty($return_info)) {
				$data['model'] = $return_info['model'];
				} else {
				$data['model'] = '';
			}
			
			if (isset($this->request->post['quantity'])) {
				$data['quantity'] = $this->request->post['quantity'];
				} elseif (!empty($return_info)) {
				$data['quantity'] = $return_info['quantity'];
				} else {
				$data['quantity'] = '';
			}
			
			if (isset($this->request->post['opened'])) {
				$data['opened'] = $this->request->post['opened'];
				} elseif (!empty($return_info)) {
				$data['opened'] = $return_info['opened'];
				} else {
				$data['opened'] = '';
			}
			
			if (isset($this->request->post['return_reason_id'])) {
				$data['return_reason_id'] = $this->request->post['return_reason_id'];
				} elseif (!empty($return_info)) {
				$data['return_reason_id'] = $return_info['return_reason_id'];
				} else {
				$data['return_reason_id'] = '';
			}
			
			
			$data['return_reasons'] = $this->model_extension_purpletree_multivendor_product_returns->getReturnReasons();
			
			if (isset($this->request->post['return_action_id'])) {
				$data['return_action_id'] = $this->request->post['return_action_id'];
				} elseif (!empty($return_info)) {
				$data['return_action_id'] = $return_info['return_action_id'];
				} else {
				$data['return_action_id'] = '';
			}
			
			$data['return_actions'] = $this->model_extension_purpletree_multivendor_product_returns->getReturnActions();
			
			if (isset($this->request->post['comment'])) {
				$data['comment'] = $this->request->post['comment'];
				} elseif (!empty($return_info)) {
				$data['comment'] = $return_info['comment'];
				} else {
				$data['comment'] = '';
			}
			
			if (isset($this->request->post['return_status_id'])) {
				$data['return_status_id'] = $this->request->post['return_status_id'];
				} elseif (!empty($return_info)) {
				$data['return_status_id'] = $return_info['return_status_id'];
				} else {
				$data['return_status_id'] = '';
			}
			$data['return_statuses'] = $this->model_extension_purpletree_multivendor_product_returns->getReturnStatuses();
			$data['return_statuses_name'] = "";
			foreach($data['return_statuses'] as $return_statuse ){
				
				if( $return_statuse['return_status_id'] == $data['return_reason_id'] ){
					$data['return_statuses_name'] = $return_statuse['name'];
					
				} 
				
			}
			
			$data['column_left'] = $this->load->controller('extension/account/purpletree_multivendor/common/column_left');
			$data['footer'] = $this->load->controller('extension/account/purpletree_multivendor/common/footer');
			$data['header'] = $this->load->controller('extension/account/purpletree_multivendor/common/header');
			
			$this->response->setOutput($this->load->view('account/purpletree_multivendor/product_return_form', $data));
		}
		
		protected function validateForm() {
			if (empty($this->request->post['order_id'])) {
				$this->error['order_id'] = $this->language->get('error_order_id');
			}
			
			if ((utf8_strlen(trim($this->request->post['firstname'])) < 1) || (utf8_strlen(trim($this->request->post['firstname'])) > 32)) {
				$this->error['firstname'] = $this->language->get('error_firstname');
			}
			
			if ((utf8_strlen(trim($this->request->post['lastname'])) < 1) || (utf8_strlen(trim($this->request->post['lastname'])) > 32)) {
				$this->error['lastname'] = $this->language->get('error_lastname');
			}
			
			if ((utf8_strlen($this->request->post['email']) > 96) || !filter_var($this->request->post['email'], FILTER_VALIDATE_EMAIL)) {
				$this->error['email'] = $this->language->get('error_email');
			}
			
			if ((utf8_strlen($this->request->post['telephone']) < 3) || (utf8_strlen($this->request->post['telephone']) > 32)) {
				$this->error['telephone'] = $this->language->get('error_telephone');
			}
			
			if ((utf8_strlen($this->request->post['product']) < 1) || (utf8_strlen($this->request->post['product']) > 255)) {
				$this->error['product'] = $this->language->get('error_product');
			}
			
			if ((utf8_strlen($this->request->post['model']) < 1) || (utf8_strlen($this->request->post['model']) > 64)) {
				$this->error['model'] = $this->language->get('error_model');
			}
			
			if (empty($this->request->post['return_reason_id'])) {
				$this->error['reason'] = $this->language->get('error_reason');
			}
			
			if ($this->error && !isset($this->error['warning'])) {
				$this->error['warning'] = $this->language->get('error_warning');
			}
			return !$this->error;
		}
		
		public function history() {
			$this->load->language('purpletree_multivendor/product_returns');
			
			$this->load->model('extension/purpletree_multivendor/product_returns');
			
			$this->load->model('extension/purpletree_multivendor/dashboard');
			
			$this->model_extension_purpletree_multivendor_dashboard->checkSellerApproval();
			
			if (isset($this->request->get['page'])) {
				$page = $this->request->get['page'];
				} else {
				$page = 1;
			}
			
			$data['histories'] = array();
			$results = $this->model_extension_purpletree_multivendor_product_returns->getReturnHistories($this->request->get['return_id'], ($page - 1) * 10, 10);
			
			foreach ($results as $result) {
				$data['histories'][] = array(
				'notify'     => $result['notify'] ? $this->language->get('text_yes') : $this->language->get('text_no'),
				'status'     => $result['status'],
				'comment'    => nl2br($result['comment']),
				'date_added' => date($this->language->get('date_format_short'), strtotime($result['date_added']))
				);
			}
			
			$history_total = $this->model_extension_purpletree_multivendor_product_returns->getTotalReturnHistories($this->request->get['return_id']);
			$pagination = new Pagination();
			$pagination->total = $history_total;
			$pagination->page = $page;
			$pagination->limit = 10;
			$pagination->url = $this->url->link('extension/account/purpletree_multivendor/product_returns/history', '&return_id=' . $this->request->get['return_id'] . '&page={page}', true);
			$data['pagination'] = $pagination->render();
			$data['results'] = sprintf($this->language->get('text_pagination'), ($history_total) ? (($page - 1) * 10) + 1 : 0, ((($page - 1) * 10) > ($history_total - 10)) ? $history_total : ((($page - 1) * 10) + 10), $history_total, ceil($history_total / 10));
			$this->response->setOutput($this->load->view('account/purpletree_multivendor/product_return_history', $data));
		}
		
		public function addHistory() {
			$this->load->language('purpletree_multivendor/product_returns');
			$json = array();		
			$this->load->model('extension/purpletree_multivendor/product_returns');
			$this->load->model('extension/purpletree_multivendor/dashboard');
			$this->model_extension_purpletree_multivendor_dashboard->checkSellerApproval();
			$this->model_extension_purpletree_multivendor_product_returns->addReturnHistory($this->request->get['return_id'], $this->request->post['return_status_id'], $this->request->post['comment'], $this->request->post['notify']);
			$json['success'] = $this->language->get('text_success');
			$this->response->addHeader('Content-Type: application/json');
			$this->response->setOutput(json_encode($json));
		}
		public function autocomplete() {
			$json = array();
			
			if (isset($this->request->get['filter_name'])) {
				
				$this->load->model('extension/purpletree_multivendor/product_returns');
				
				if (isset($this->request->get['filter_name'])) {
					$filter_name = $this->request->get['filter_name'];
					} else {
					$filter_name = '';
				}
				if (isset($this->request->get['limit'])) {
					$limit = $this->request->get['limit'];
					} else {
					$limit = 5;
				}
				
				$seller_id = $this->customer->getId();
				
				$filter_data = array(
				'filter_name'  => $filter_name,			
				'start'        => 0,
				'limit'        => $limit,
				'seller_id' => $seller_id
				);
				
				$results = $this->model_extension_purpletree_multivendor_product_returns->getProducts($filter_data);
				
				foreach ($results as $result) {
					$json[] = array(
					'product_id' => $result['product_id'],
					'name'       => strip_tags(html_entity_decode($result['name'], ENT_QUOTES, 'UTF-8')),
					'model'      => $result['model'],					
					'price'      => $result['price']
					);
				}
			}
			
			$this->response->addHeader('Content-Type: application/json');
			$this->response->setOutput(json_encode($json));
		}	
	}
?>