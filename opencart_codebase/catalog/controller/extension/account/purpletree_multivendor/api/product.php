<?php
class ControllerExtensionAccountPurpletreeMultivendorApiProduct extends Controller {
		private $error = array();
		
		public function index() {
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
			if (isset($this->request->get['id']) && ctype_digit($this->request->get['id'])) {
                $this->getSeller($this->request->get['id']);
			}
		}
		
		public function getSeller($id) {
			$json = array('success' => false);
			/******* get seller details to show on product page ******/
			if($this->config->get('module_purpletree_multivendor_status')){
				$this->load->model('extension/purpletree_multivendor/sellerproduct');
				$this->load->model('extension/purpletree_multivendor/vendor');
				$seller_detail = $this->model_extension_purpletree_multivendor_sellerproduct->getSellername($id);
				if($seller_detail){
					$json['data']['seller_review_status'] = $this->config->get('module_purpletree_multivendor_seller_review');
					$seller_detailss = $this->model_extension_purpletree_multivendor_vendor->getStoreDetail($seller_detail['seller_id']);
					$seller_rating = $this->model_extension_purpletree_multivendor_vendor->getStoreRating($seller_detail['seller_id']);
					$json['success'] = true;
					$json['data']['seller_detail'] = array(
					'seller_name' => $seller_detail['store_name'],
					'store_id' => $seller_detail['id'],
					'seller_rating' => (isset($seller_rating['rating'])?$seller_rating['rating']:'0'),
					'seller_count' => (isset($seller_rating['count'])?$seller_rating['count']:'0'),
					'seller_id' => $seller_detail['seller_id']
					);
				}
			} 
			/******* get seller details to show on product page ******/
			
			$this->sendResponse($json);
		}
		
		private function sendResponse($json)
		{
			if ($this->debugIt) {
				echo '<pre>';
				print_r($json);
				echo '</pre>';
				} else {
				$this->response->setOutput(json_encode($json));
			}
		}
		private function checkPlugin() {
			header('Access-Control-Allow-Origin:*');
			header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
			header('Access-Control-Max-Age: 286400');
			header('Access-Control-Allow-Credentials: true');
			header('Access-Control-Allow-Headers: languageid,LANGUAGEID,Languageid,purpletreemultivendor,Purpletreemultivendor,PURPLETREEMULTIVENDOR,xocmerchantid,XOCMERCHANTID,Xocmerchantid,XOCSESSION,xocsession,Xocsession,content-type,CONTENT-TYPE,Content-Type');
		}
}