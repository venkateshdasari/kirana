<?php
class ControllerExtensionPurpletreeMultivendorGeozone extends Controller {
		private $error = array();
		
		public function index() {
			$this->load->language('purpletree_multivendor/geo_zone');
			
			$this->document->setTitle($this->language->get('heading_title'));
			
			$this->load->model('extension/purpletree_multivendor/geozone');
			
			$this->getList();
		}
		
		public function add() {
			$this->load->language('purpletree_multivendor/geo_zone');
			
			$this->document->setTitle($this->language->get('heading_title'));
			
			$this->load->model('extension/purpletree_multivendor/geozone');
			
			if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
				
				$this->model_extension_purpletree_multivendor_geozone->addSellerGeoZone($this->request->post);
				
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
			
			$this->response->redirect($this->url->link('extension/purpletree_multivendor/geozone', 'user_token=' . $this->session->data['user_token'] . $url, true));
			}
			
			$this->getForm();
			}
			
			public function edit() {
			
			$this->load->language('purpletree_multivendor/geo_zone');
			
			$this->document->setTitle($this->language->get('heading_title'));
			
			$this->load->model('extension/purpletree_multivendor/geozone');
			
			if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_extension_purpletree_multivendor_geozone->editSellerGeoZone($this->request->get['geo_zone_id'], $this->request->post);
			
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
			
			$this->response->redirect($this->url->link('extension/purpletree_multivendor/geozone', 'user_token=' . $this->session->data['user_token'] . $url, true));
			}
			
			$this->getForm();
			}
			
			public function delete() {
			$this->load->language('purpletree_multivendor/geo_zone');
			
			$this->document->setTitle($this->language->get('heading_title'));
			
			$this->load->model('extension/purpletree_multivendor/geozone');
			
			if (isset($this->request->post['selected']) && $this->validateDelete()) {
			foreach ($this->request->post['selected'] as $geo_zone_id) {
			$this->model_extension_purpletree_multivendor_geozone->deleteGeoZone($geo_zone_id);
			}
			
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
			
			$this->response->redirect($this->url->link('extension/purpletree_multivendor/geozone', 'user_token=' . $this->session->data['user_token'] . $url, true));
			}
			
			$this->getList();
			}
			
			protected function getList() {
				$this->document->addStyle('view/javascript/purpletreecss/commonstylesheet.css');
			$this->load->language('purpletree_multivendor/shipping');
			
			if (isset($this->request->get['sort'])) {
			$sort = $this->request->get['sort'];
			} else {
			$sort = 'name';
			}		
			if (isset($this->request->get['price'])) {
			$price = $this->request->get['price'];
			} else {
			$price = 'price';
			}
			
			if (isset($this->request->get['order'])) {
			$order = $this->request->get['order'];
			} else {
			$order = 'ASC';
			}
			
			if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
			} else {
			$page = 1;
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
			'href' => $this->url->link('extension/purpletree_multivendor/geozone', 'user_token=' . $this->session->data['user_token'] . $url, true)
			);
			
			$data['add'] = $this->url->link('extension/purpletree_multivendor/geozone/add', 'user_token=' . $this->session->data['user_token'] . $url, true);
			$data['delete'] = $this->url->link('extension/purpletree_multivendor/geozone/delete', 'user_token=' . $this->session->data['user_token'] . $url, true);
			
			$data['geo_zones'] = array();
			
			$filter_data = array(
			'sort'  => $sort,
			'order' => $order,
			'price' => $price,
			'start' => ($page - 1) * $this->config->get('config_limit_admin'),
			'limit' => $this->config->get('config_limit_admin')
			);
			$data['manageshippingoptionUrl'] = $this->url->link('extension/shipping/purpletree_shipping', 'user_token=' . $this->session->data['user_token'], true);
			$geo_zone_total = $this->model_extension_purpletree_multivendor_geozone->getTotalGeoZones();
			
			$results = $this->model_extension_purpletree_multivendor_geozone->getGeoZones($filter_data);
			//echo"<pre>"; print_r($results); die;
			foreach ($results as $result) {
			$data['geo_zones'][] = array(
			'geo_zone_id' => $result['geo_zone_id'],
			'seller_name' => $result['seller_name'],
			'name'        => $result['name'],
			'price'        => $result['price'],
			'description' => $result['description'],
			'edit'        => $this->url->link('extension/purpletree_multivendor/geozone/edit', 'user_token=' . $this->session->data['user_token'] . '&geo_zone_id=' . $result['geo_zone_id'] . $url, true)
			);
			}
			
			if (isset($this->error['warning'])) {
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
			
			if ($order == 'ASC') {
			$url .= '&order=DESC';
			} else {
			$url .= '&order=ASC';
			}
			
			if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
			}
			
			$data['sort_name'] = $this->url->link('extension/purpletree_multivendor/geozone', 'user_token=' . $this->session->data['user_token'] . '&sort=name' . $url, true);
			$data['sort_description'] = $this->url->link('extension/purpletree_multivendor/geozone', 'user_token=' . $this->session->data['user_token'] . '&sort=description' . $url, true);
			
			$data['sort_description'] = $this->url->link('extension/purpletree_multivendor/geozone', 'user_token=' . $this->session->data['user_token'] . '&sort=price' . $url, true);
			
			$url = '';
			
			if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
			}
			
			if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
			}
			
			$pagination = new Pagination();
			$pagination->total = $geo_zone_total;
			$pagination->page = $page;
			$pagination->limit = $this->config->get('config_limit_admin');
			$pagination->url = $this->url->link('extension/purpletree_multivendor/geozone', 'user_token=' . $this->session->data['user_token'] . $url . '&page={page}', true);
			
			$data['pagination'] = $pagination->render();
			
			$data['results'] = sprintf($this->language->get('text_pagination'), ($geo_zone_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($geo_zone_total - $this->config->get('config_limit_admin'))) ? $geo_zone_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $geo_zone_total, ceil($geo_zone_total / $this->config->get('config_limit_admin')));
			
			$data['sort'] = $sort;
			$data['order'] = $order;
			
			$data['header'] = $this->load->controller('common/header');
			$data['column_left'] = $this->load->controller('common/column_left');
			$data['footer'] = $this->load->controller('common/footer');
			
			$this->response->setOutput($this->load->view('extension/purpletree_multivendor/geo_zone_list', $data));	
			}
			
			protected function getForm() {
			$this->document->addStyle('view/javascript/purpletreecss/commonstylesheet.css');	
			$data['text_form'] = !isset($this->request->get['geo_zone_id']) ? $this->language->get('text_add') : $this->language->get('text_edit');
			
			if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
			} else {
			$data['error_warning'] = '';
			}
			
			if (isset($this->error['name'])) {
			$data['error_name'] = $this->error['name'];
			} else {
			$data['error_name'] = '';
			}
			
			if (isset($this->error['description'])) {
			$data['error_description'] = $this->error['description'];
			} else {
			$data['error_description'] = '';
			}		
			
			if (isset($this->error['store_name'])) {
			$data['error_store_name'] = $this->error['store_name'];
			} else {
			$data['error_store_name'] = '';
			}
			if (isset($this->error['weight_from'])) {
			$data['error_weight_from'] = $this->error['weight_from'];
			} else {
			$data['error_weight_from'] = '';
			}
			if (isset($this->error['weight_to'])) {
			$data['error_weight_to'] = $this->error['weight_to'];
			} else {
			$data['error_weight_to'] = '';
			}
			
			if (isset($this->error['price'])) {
			$data['error_price'] = $this->error['price'];
			} else {
			$data['error_price'] = '';
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
			'href' => $this->url->link('extension/purpletree_multivendor/geozone', 'user_token=' . $this->session->data['user_token'] . $url, true)
			);
			
			if (!isset($this->request->get['geo_zone_id'])) {
			$data['action'] = $this->url->link('extension/purpletree_multivendor/geozone/add', 'user_token=' . $this->session->data['user_token'] . $url, true);
			} else {
			$data['action'] = $this->url->link('extension/purpletree_multivendor/geozone/edit', 'user_token=' . $this->session->data['user_token'] . '&geo_zone_id=' . $this->request->get['geo_zone_id'] . $url, true);
			}
			
			$data['cancel'] = $this->url->link('extension/purpletree_multivendor/geozone', 'user_token=' . $this->session->data['user_token'] . $url, true);
			
			if (isset($this->request->get['geo_zone_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
			$geo_zone_info = $this->model_extension_purpletree_multivendor_geozone->getGeoZone($this->request->get['geo_zone_id']);
			}
			$data['user_token'] = $this->session->data['user_token'];
			
			if (isset($this->request->post['seller_name'])) {
			$data['store_name'] = $this->request->post['seller_name'];
			} elseif (!empty($geo_zone_info)) {
			$data['store_name'] = $geo_zone_info['seller_name'];
			if($geo_zone_info['seller_name']==''){
				$data['store_name']='N/A';	
				}
			} else {
			$data['store_name'] = '';
			}		
			if (isset($this->request->post['weight_from'])) {
			$data['weight_from'] = $this->request->post['weight_from'];
			} elseif (!empty($geo_zone_info)) {
			$data['weight_from'] = $geo_zone_info['weight_from'];
			} else {
			$data['weight_from'] = '';
			}	
			if (isset($this->request->post['weight_to'])) {
			$data['weight_to'] = $this->request->post['weight_to'];
			} elseif (!empty($geo_zone_info)) {
			$data['weight_to'] = $geo_zone_info['weight_to'];
			} else {
			$data['weight_to'] = '';
			}	
			
			if (isset($this->request->post['price'])) {
			$data['price'] = $this->request->post['price'];
			} elseif (!empty($geo_zone_info)) {
			$data['price'] = $geo_zone_info['price'];
			} else {
			$data['price'] = '';
			}		
			if (isset($this->request->post['seller_id'])) {
			$data['vendor_id'] = $this->request->post['seller_id'];
			} elseif (!empty($geo_zone_info)) {
			$data['vendor_id'] = $geo_zone_info['seller_id'];
			} else {
			$data['vendor_id'] = '';
			}		
			if (isset($this->request->post['name'])) {
			$data['name'] = $this->request->post['name'];
			} elseif (!empty($geo_zone_info)) {
			$data['name'] = $geo_zone_info['name'];
			} else {
			$data['name'] = '';
			}
			
			if (isset($this->request->post['description'])) {
			$data['description'] = $this->request->post['description'];
			} elseif (!empty($geo_zone_info)) {
			$data['description'] = $geo_zone_info['description'];
			} else {
			$data['description'] = '';
			}
			
			$this->load->model('localisation/country');
			
			$data['countries'] = $this->model_localisation_country->getCountries();
			
			if (isset($this->request->post['zone_to_geo_zone'])) {
			$data['zone_to_geo_zones'] = $this->request->post['zone_to_geo_zone'];
			} elseif (isset($this->request->get['geo_zone_id'])) {
			$data['zone_to_geo_zones'] = $this->model_extension_purpletree_multivendor_geozone->getZoneToGeoZones($this->request->get['geo_zone_id']);
			} else {
			$data['zone_to_geo_zones'] = array();
			}
			
			$data['text_select_country'] = $this->language->get('text_select_country');
			$data['header'] = $this->load->controller('common/header');
			$data['column_left'] = $this->load->controller('common/column_left');
			$data['footer'] = $this->load->controller('common/footer');
			
			$this->response->setOutput($this->load->view('extension/purpletree_multivendor/geo_zone_form', $data));
			}
			
			protected function validateForm() {
			if (!$this->user->hasPermission('modify', 'extension/purpletree_multivendor/geozone')) {
			$this->error['warning'] = $this->language->get('error_permission');
			}
			
			if ((utf8_strlen($this->request->post['name']) < 3) || (utf8_strlen($this->request->post['name']) > 32)) {
			$this->error['name'] = $this->language->get('error_name');
			}
			
			if ((utf8_strlen($this->request->post['description']) < 3) || (utf8_strlen($this->request->post['description']) > 255)) {
			$this->error['description'] = $this->language->get('error_description');
			}			
			if ((utf8_strlen($this->request->post['store_name']) < 3) || (utf8_strlen($this->request->post['store_name']) > 255)) {
			$this->error['store_name'] = $this->language->get('error_store_name');
			}	

			if ((utf8_strlen($this->request->post['weight_from']) < 1 ) || (utf8_strlen($this->request->post['weight_from']) > 255)) {
			$this->error['weight_from'] = $this->language->get('error_weight_from');
			} elseif (!is_numeric($this->request->post['weight_from'])) {
			$this->error['weight_from'] = $this->language->get('error_weight_from_numeric');
			}
			
			if ((utf8_strlen($this->request->post['weight_to']) < 1 ) || (utf8_strlen($this->request->post['weight_to']) > 255)) {
			$this->error['weight_to'] = $this->language->get('error_weight_to');
			} elseif (!is_numeric($this->request->post['weight_to'])) {
			$this->error['weight_to'] = $this->language->get('error_weight_to_numeric');
			}
						
			if ((utf8_strlen($this->request->post['price']) < 1 ) || (utf8_strlen($this->request->post['price']) > 255)) {
			$this->error['price'] = $this->language->get('error_price');
			}elseif (!is_numeric($this->request->post['price'])) {
			$this->error['price'] = $this->language->get('error_price_numeric');
			}
			
			return !$this->error;
			}
			
			protected function validateDelete() {
			if (!$this->user->hasPermission('modify', 'extension/purpletree_multivendor/geozone')) {
			$this->error['warning'] = $this->language->get('error_permission');
			}
			
			$this->load->model('localisation/tax_rate');
			
			foreach ($this->request->post['selected'] as $geo_zone_id) {
			$tax_rate_total = $this->model_localisation_tax_rate->getTotalTaxRatesByGeoZoneId($geo_zone_id);
			
			if ($tax_rate_total) {
			$this->error['warning'] = sprintf($this->language->get('error_tax_rate'), $tax_rate_total);
			}
			}
			
			return !$this->error;
			}
}
?>