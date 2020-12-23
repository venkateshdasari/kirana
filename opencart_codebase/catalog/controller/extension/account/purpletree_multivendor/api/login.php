<?php
class ControllerExtensionAccountPurpletreeMultivendorApiLogin extends Controller{
		private $error = array();  
		
		public function index(){
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
				$json['seller'] = '3';
				$json['message'] = $this->language->get('seller_not_logged');
				$this->response->addHeader('Content-Type: application/json');
				return $this->response->setOutput(json_encode($json));
			}
			
			if ($this->config->get('module_purpletree_multivendor_status')) { } else {
				$json['status'] = 'error';
				$json['message'] = 'Purpletree Multivendor Disabled';
				$this->response->addHeader('Content-Type: application/json');
				return $this->response->setOutput(json_encode($json)); 
				
			}
			if(!$this->customer->validateSeller()) {		
				$json['status'] = 'error';
				$json['message'] = $this->language->get('error_license');
				$this->response->addHeader('Content-Type: application/json');
				return $this->response->setOutput(json_encode($json));
			} 
			$this->load->model('extension/purpletree_multivendor/vendor');
			$store_id = $this->model_extension_purpletree_multivendor_vendor->getStoreId($this->customer->getId());
			$store_detail = $this->customer->isSeller();
			if(!empty($store_detail)){
				if(($store_id === $store_detail['id']) && $store_detail['store_status'] == '1'){
					
					$json['message'] = $this->language->get('showsellermenu');
					$json['seller'] = '1';		
					} else {
					$json['seller'] = '2';		
					$json['message'] = $this->language->get('waitingselerapprova');
				}	
				} else {
				$json['seller'] = '0';		
				$json['message'] = $this->language->get('become_seller_lan');
			}
			$json['status'] = 'success';
			$this->response->addHeader('Content-Type: application/json');
			return $this->response->setOutput(json_encode($json));
		}
		private function checkPlugin() {
			header('Access-Control-Allow-Origin:*');
			header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
			header('Access-Control-Max-Age: 286400');
			header('Access-Control-Allow-Credentials: true');
			header('Access-Control-Allow-Headers: languageid,LANGUAGEID,Languageid,purpletreemultivendor,Purpletreemultivendor,PURPLETREEMULTIVENDOR,xocmerchantid,XOCMERCHANTID,Xocmerchantid,XOCSESSION,xocsession,Xocsession,content-type,CONTENT-TYPE,Content-Type');
		}
}
?>