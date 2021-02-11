<?php
class ControllerExtensionAccountPurpletreeMultivendorApiRegister extends Controller {
		
		private $error = array();
		
		private $json = array();
		
		public function index() {
			$this->checkPlugin();
			$this->load->language('purpletree_multivendor/api');
			$json['success'] = false;
			$json['error'] = $this->language->get('no_data');
			if (!$this->customer->isMobileApiCall()) { 
				$json['success'] = false;
				$json['error'] = $this->language->get('error_permission');
				$this->response->addHeader('Content-Type: application/json');
				return $this->response->setOutput(json_encode($json));
			}
			if ($this->customer->isLogged()) {
				$json['success'] = false;
				$json['error'] = $this->language->get('already_logged');
				$this->response->addHeader('Content-Type: application/json');
				return $this->response->setOutput(json_encode($json));
			}
			$this->load->language('account/register');
			$this->load->language('account/ptsregister');
			$this->load->model('account/customer');
			if (($this->request->server['REQUEST_METHOD'] == 'POST')) {
				$requestjson = file_get_contents('php://input');
				$requestjson = json_decode($requestjson, true);
				if($this->validate($requestjson)) {
					if(!isset($requestjson['telephone'])){
						$requestjson['telephone'] = '';
					}
					$customer_id = $this->model_account_customer->addCustomer($requestjson);
					$json['customer_id'] = $customer_id;
					$json['success'] = true;
					$json['message'] = $this->language->get('register_success');
					//if ($this->config->get('module_purpletree_multivendor_become_seller')) {
					if($requestjson['become_seller']=="1"){
						$store_name = $requestjson['seller_storename'];
						$this->load->model('extension/purpletree_multivendor/vendor');
						$file = '';
						$seller_id = $this->model_extension_purpletree_multivendor_vendor->addSeller($customer_id,$store_name ,$file);
					}
					if(isset($seller_id)){
						$json['success']  = true;
						$json['message'] = $this->language->get('seller_register_succ');
						
					}
					//}
					
					// Clear any previous login attempts for unregistered accounts.
					$this->model_account_customer->deleteLoginAttempts($requestjson['email']);
					
					$this->customer->login($requestjson['email'], $requestjson['password']);
					
					unset($this->session->data['guest']);
					
					$this->response->addHeader('Content-Type: application/json');
					return $this->response->setOutput(json_encode($json));
				}
			}
			
			if (isset($this->error['warning'])) {
				$json['success'] = false;
				$json['error'] = $this->error['warning'];
				$this->response->addHeader('Content-Type: application/json');
				return $this->response->setOutput(json_encode($json));
			}
			
			if (isset($this->error['firstname'])) {
				$json['success'] = false;
				$json['error'] = $this->error['firstname'];
				$this->response->addHeader('Content-Type: application/json');
				return $this->response->setOutput(json_encode($json));
			}
			
			if (isset($this->error['lastname'])) {
				$json['success'] = false;
				$json['error'] = $this->error['lastname'];
				$this->response->addHeader('Content-Type: application/json');
				return $this->response->setOutput(json_encode($json));
			}
			
			if (isset($this->error['email'])) {
				$json['success'] = false;
				$json['error'] = $this->error['email'];
				$this->response->addHeader('Content-Type: application/json');
				return $this->response->setOutput(json_encode($json));
			}
			
			if (isset($this->error['telephone'])) {
				$json['success'] = false;
				$json['error'] = $this->error['telephone'];
				$this->response->addHeader('Content-Type: application/json');
				return $this->response->setOutput(json_encode($json));
			}
			
			if (isset($this->error['custom_field'])) {
				$json['success'] = false;
				$json['error'] = $this->error['custom_field'];
				$this->response->addHeader('Content-Type: application/json');
				return $this->response->setOutput(json_encode($json));
			}
			
			if (isset($this->error['password'])) {
				$json['success'] = false;
				$json['error'] = $this->error['password'];
				$this->response->addHeader('Content-Type: application/json');
				return $this->response->setOutput(json_encode($json));
			}
			
			if (isset($this->error['seller_store'])) {
				$json['success'] = false;
				$json['error'] = $this->error['seller_store'];
				$this->response->addHeader('Content-Type: application/json');
				return $this->response->setOutput(json_encode($json));
			}
			
			
			if (isset($this->error['confirm'])) {
				$json['success'] = false;
				$json['error'] = $this->error['confirm'];
				$this->response->addHeader('Content-Type: application/json');
				return $this->response->setOutput(json_encode($json));
			}
			$this->response->addHeader('Content-Type: application/json');
			$this->response->setOutput(json_encode($json));
		}
		
		private function validate($requestjson) {
			
			$this->load->model('extension/purpletree_multivendor/vendor');
			if(!$this->customer->validateSeller()) {
				$this->error['warning1'] = $this->language->get('error_license');
			}
			//if($this->config->get('module_purpletree_multivendor_become_seller')) { 
			if(isset($requestjson['become_seller'])){
				if($requestjson['become_seller']){
					$json['data']['become_seller'] = $requestjson['become_seller'];	
					if(!isset($requestjson['seller_storename']) || (utf8_strlen($requestjson['seller_storename']) < 5) || (utf8_strlen(trim($requestjson['seller_storename'])) > 50)) {
						$this->error['seller_store'] = $this->language->get('error_storename');
					}
				}
			}
			//}
			if(isset($requestjson['firstname'])){
				if ((utf8_strlen(trim($requestjson['firstname'])) < 1) || (utf8_strlen(trim($requestjson['firstname'])) > 32)) {
					$this->error['firstname'] = $this->language->get('error_firstname');
				}
				} else {
				$this->error['firstname'] = $this->language->get('error_firstname');
			}
			if(isset($requestjson['lastname'])){
				if ((utf8_strlen(trim($requestjson['lastname'])) < 1) || (utf8_strlen(trim($requestjson['lastname'])) > 32)) {
					$this->error['lastname'] = $this->language->get('error_lastname');
				}
				} else {
				
				$this->error['lastname'] = $this->language->get('error_lastname');	
			}
			
			if(isset($requestjson['email'])){
				if ((utf8_strlen($requestjson['email']) > 96) || !filter_var($requestjson['email'], FILTER_VALIDATE_EMAIL)) {
					$this->error['email'] = $this->language->get('error_email');
				}
				} else {
				$this->error['email'] = $this->language->get('error_email');	
			}
			if(isset($requestjson['email'])){
				if ($this->model_account_customer->getTotalCustomersByEmail($requestjson['email'])) {
					$this->error['warning'] = $this->language->get('error_exists');
				}
				} else {
				$this->error['warning'] = $this->language->get('error_exists');	
			}
			if(isset($requestjson['telephone'])){
				if ((utf8_strlen($requestjson['telephone']) < 3) || (utf8_strlen($requestjson['telephone']) > 32)) {
					$this->error['telephone'] = $this->language->get('error_telephone');
				}
			}
			
			// Customer Group
			
			if (isset($requestjson['customer_group_id']) && is_array($this->config->get('config_customer_group_display')) && in_array($requestjson['customer_group_id'], $this->config->get('config_customer_group_display'))) {
				$customer_group_id = $requestjson['customer_group_id'];
			} 
			
			// Custom field validation
			$this->load->model('account/custom_field');
			/* $custom_fields = $this->model_account_custom_field->getCustomFields($customer_group_id);
				if(!empty($custom_fields)){
				foreach ($custom_fields as $custom_field) {
				if ($custom_field['location'] == 'account') {
				if ($custom_field['required'] && empty($requestjson['custom_field'][$custom_field['location']][$custom_field['custom_field_id']])) {
				$this->error['custom_field'] = sprintf($this->language->get('error_custom_field'), $custom_field['name']);
				} elseif (($custom_field['type'] == 'text') && !empty($custom_field['validation']) && !filter_var($requestjson['custom_field'][$custom_field['location']][$custom_field['custom_field_id']], FILTER_VALIDATE_REGEXP, array('options' => array('regexp' => $custom_field['validation'])))) {
				$this->error['custom_field'] = sprintf($this->language->get('error_custom_field'), $custom_field['name']);
				}
				}
				}
			} */
			if(isset($requestjson['password'])){
				if ((utf8_strlen(html_entity_decode($requestjson['password'], ENT_QUOTES, 'UTF-8')) < 4) || (utf8_strlen(html_entity_decode($requestjson['password'], ENT_QUOTES, 'UTF-8')) > 40)) {
					$this->error['password'] = $this->language->get('error_password');
				}
				} else {
				$this->error['password'] = $this->language->get('error_password');		
			}
			if(isset($requestjson['confirm'])){
				if ($requestjson['confirm'] != $requestjson['password']) {
					$this->error['confirm'] = $this->language->get('error_confirm');
				}
				} else {
				$this->error['confirm'] = $this->language->get('error_confirm');	
			}
			
			// Captcha
			/* if ($this->config->get('captcha_' . $this->config->get('config_captcha') . '_status') && in_array('register', (array)$this->config->get('config_captcha_page'))) {
				$captcha = $this->load->controller('extension/captcha/' . $this->config->get('config_captcha') . '/validate');
				
				if ($captcha) {
				$this->error['captcha'] = $captcha;
				}
			} */
			
			// Agree to terms
			if ($this->config->get('config_account_id')) {
				$this->load->model('catalog/information');
				
				$information_info = $this->model_catalog_information->getInformation($this->config->get('config_account_id'));
				if(isset($requestjson['agree'])){
					if ($information_info && !isset($requestjson['agree'])) {
						$this->error['warning'] = sprintf($this->language->get('error_agree'), $information_info['title']);
					}
					} else {
					$this->error['warning'] = sprintf($this->language->get('error_agree'), $information_info['title']);
					
				}
			}
			
			return !$this->error;
		}
		
		public function customfield() {
			$this->checkPlugin();
			$this->load->language('purpletree_multivendor/api');
			$json['success'] = false;
			$json['error'] = $this->language->get('no_data');
			if (!$this->customer->isMobileApiCall()) { 
				$json['success'] = false;
				$json['error'] = $this->language->get('error_permission');
				$this->response->addHeader('Content-Type: application/json');
				return $this->response->setOutput(json_encode($json));
			}
			$this->load->model('account/custom_field');
			// Customer Group
			if (isset($this->request->get['customer_group_id']) && is_array($this->config->get('config_customer_group_display')) && in_array($this->request->get['customer_group_id'], $this->config->get('config_customer_group_display'))) {
				$customer_group_id = $this->request->get['customer_group_id'];
				} else {
				$customer_group_id = $this->config->get('config_customer_group_id');
			}
			$custom_fields = $this->model_account_custom_field->getCustomFields($customer_group_id);
			
			foreach ($custom_fields as $custom_field) {
				$json['data'][] = array(
				'custom_field_id' => $custom_field['custom_field_id'],
				'required'        => $custom_field['required']
				);
				$json['success'] = 'success';
				$json['error'] = '';
			} 
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