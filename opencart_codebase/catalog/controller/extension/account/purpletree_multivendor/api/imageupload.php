<?php
class ControllerExtensionAccountPurpletreeMultivendorApiImageupload extends Controller{
		private $error = array(); 
		public $json = array(); 
		
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
			$seller_id 	= $this->customer->getId();
			$seller_folder = "Seller_".$seller_id;
			if (($this->request->server['REQUEST_METHOD'] == 'POST')) {
				//$path = 'admin/ptsseller/';
				$directory = DIR_IMAGE . 'catalog';
				$file = "";
				if (!is_dir($directory . '/' . $seller_folder)) {
					mkdir($directory . '/' . $seller_folder, 0777);
					chmod($directory . '/' . $seller_folder, 0777);
					@touch($directory . '/' . $seller_folder . '/' . 'index.html');
				}
				$directory = DIR_IMAGE . 'catalog'.'/'.$seller_folder;
				if(is_dir($directory)){
					if(isset($_FILES['upload_file'])) {
                        $allowed_file=array('gif','png','jpg','pdf','doc','docx','zip');
                        $filename = basename(preg_replace('/[^a-zA-Z0-9\.\-\s+]/', '', html_entity_decode($_FILES['upload_file']['name'], ENT_QUOTES, 'UTF-8')));
						$extension = pathinfo($filename, PATHINFO_EXTENSION);
						if($filename != '') {
							if(in_array($extension,$allowed_file) ) {
								$file = md5(mt_rand()).'-'.$filename;
								move_uploaded_file($_FILES['upload_file']['tmp_name'], $directory.'/'.$file);
								$json['status'] = 'success';
								$json['message'] = $this->language->get('text_success');
								$json['file'] = 'catalog'.'/'.$seller_folder.'/'.$file;
							}     
						}    
					}						
				}
			} 
			$this->load->model('tool/image');
			if($file != '') {
				$json['banner_thumb'] = $this->model_tool_image->resize('catalog'.'/'.$seller_folder.'/'.$file, 100, 100);
				} else {
				$json['banner_thumb'] = $this->model_tool_image->resize('catalog/purpletree_banner.jpg', 100, 100);
			}
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