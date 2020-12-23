<?php
class ControllerExtensionAccountPurpletreeMultivendorApiSellerproduct extends Controller{
		private $error = array();
		private $json = array();
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
			
			$this->load->language('purpletree_multivendor/sellerproduct');
			$this->load->language('purpletree_multivendor/metals_spot_price');
			
			$this->load->model('extension/purpletree_multivendor/sellerproduct');
			
			$json = $this->getList();
			$this->response->addHeader('Content-Type: application/json');
			return $this->response->setOutput(json_encode($json));
		}	
		public function productimage(){
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
			$file = "";
			//echo "a";
			if (($this->request->server['REQUEST_METHOD'] == 'POST') && isset($_FILES['upload_file']['name'])) {
				//echo "b";
				$directory = DIR_IMAGE . 'catalog';
				//echo "c";
				if (!is_dir($directory . '/' . $seller_folder)) {
					mkdir($directory . '/' . $seller_folder, 0777);
					chmod($directory . '/' . $seller_folder, 0777);
					@touch($directory . '/' . $seller_folder . '/' . 'index.html');
				}
				$directory = DIR_IMAGE . 'catalog'.'/'.$seller_folder;
				if(is_dir($directory)){
					$allowed_file=array('gif','png','jpg','GIF','PNG','JPG');
					$filename = basename(preg_replace('/[^a-zA-Z0-9\.\-\s+]/', '', html_entity_decode($_FILES['upload_file']['name'], ENT_QUOTES, 'UTF-8')));
					$extension = pathinfo($filename, PATHINFO_EXTENSION);
					//echo "d";
					if($filename != '') {
						//echo "e";
						if(in_array($extension,$allowed_file) ) {
							//echo "f";
							$file = md5(mt_rand()).'-'.$filename;
							move_uploaded_file($_FILES['upload_file']['tmp_name'], $directory.'/'.$file);
							$json['status'] = 'success';
							$json['message'] = $this->language->get('Image uploaded successfully');
							$json['file'] = 'catalog'.'/'.$seller_folder.'/'.$file;
						}     
					}                                
				}
			} 
			
			$this->load->model('tool/image');
			if($file != '') {
				$json['product_thumb'] = $this->model_tool_image->resize('catalog'.'/'.$seller_folder.'/'.$file, 100, 100);;
				} else {
				$json['product_thumb'] = $this->model_tool_image->resize('image/cache/no_image.jpg', 100, 100);
			}
			$this->response->addHeader('Content-Type: application/json');
			return $this->response->setOutput(json_encode($json));
		}
		public function delete() {
			$this->checkPlugin();
			$this->load->language('purpletree_multivendor/api');
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
			$this->load->language('purpletree_multivendor/sellerproduct');
			$this->load->model('extension/purpletree_multivendor/sellerproduct');
			
			if (isset($this->request->get['product_id']) ) {
				$this->model_extension_purpletree_multivendor_sellerproduct->deleteProduct($this->request->get['product_id']);
				//$this->session->data['success'] = $this->language->get('text_success_delete');
				$json['status'] = 'success';
				$json['message'] =  $this->language->get('text_success_delete');
			}
			$this->index();
			$this->response->addHeader('Content-Type: application/json');
			$this->response->setOutput(json_encode($json));
		}
		
		public function add() {
			$this->checkPlugin();
			$this->load->language('purpletree_multivendor/api');
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
			
			$this->load->language('purpletree_multivendor/sellerproduct');
			$this->load->model('extension/purpletree_multivendor/sellerproduct');
			$this->load->model('setting/store');
			$this->load->model('catalog/product');
			$this->load->model('extension/catalog/option');
			if ($this->request->server['REQUEST_METHOD'] == 'POST') {
				$requestjson2 = file_get_contents('php://input');
				$requestjson1 = json_decode($requestjson2, true); 
				//echo"<pre>"; print_r($requestjson1); die;
			    $requestjson1['seller_id'] = $this->customer->getId();
				foreach($requestjson1['all_categories'] as $producyttc => $val) {
					$requestjson1['product_category'][] = $producyttc;
				}
				if(isset($requestjson1['selected_stores'])) {
					foreach($requestjson1['selected_stores'] as $storesss => $val1) {
						$requestjson1['product_store'][] = $storesss;
					}
				}
				if(isset($requestjson1['selected_downloads'])) {
					foreach($requestjson1['selected_downloads'] as $storesss1 => $val2) {
						$requestjson1['product_download'][] = $storesss1;
					}
				}
				if(isset($requestjson1['selected_filters'])) {
					foreach($requestjson1['selected_filters'] as $storesss2 => $val3) {
						$requestjson1['product_filter'][] = $storesss2;
					}
				}
				if(isset($requestjson1['selected_related'])) {
					foreach($requestjson1['selected_related'] as $storesss3 => $val4) {
						$requestjson1['product_related'][] = $storesss3;
					}
				}
				$requestjson1['status'] = isset($requestjson1['status'])?$requestjson1['status']:0;
				$requestjson1['model'] = isset($requestjson1['model'])?$requestjson1['model']:0;
				$requestjson1['quantity'] = isset($requestjson1['quantity'])?$requestjson1['quantity']:0;
				$requestjson1['minimum'] = isset($requestjson1['minimum'])?$requestjson1['minimum']:0;
				$requestjson1['subtract'] = isset($requestjson1['subtract'])?$requestjson1['subtract']:0;
				$requestjson1['stock_status_id'] = isset($requestjson1['stock_status_id'])?$requestjson1['stock_status_id']:7;
				$requestjson1['date_available'] = isset($requestjson1['date_available'])?$requestjson1['date_available']:'';
				$requestjson1['shipping'] = isset($requestjson1['shipping '])?$requestjson1['shipping']:0;
				$requestjson1['price'] = isset($requestjson1['price'])?$requestjson1['price']:0;
				$requestjson1['weight'] = isset($requestjson1['weight'])?$requestjson1['weight']:'';
				$requestjson1['length'] = isset($requestjson1['length'])?$requestjson1['length']:'';
				$requestjson1['width'] = isset($requestjson1['width'])?$requestjson1['width']:'';
				$requestjson1['height'] = isset($requestjson1['height'])?$requestjson1['height']:'';
				$requestjson1['tax_class_id'] = isset($requestjson1['tax_class_id'])?$requestjson1['tax_class_id']:'';
				$this->load->model('localisation/language');
				$languagesall1 = $this->model_localisation_language->getLanguages();
				$languagesall  = array();
				foreach($languagesall1 as $languagg) {
					$languagesall[] = $languagg['language_id'];
				}
				foreach($languagesall as $langkey) {
					$requestjson1['product_description'][$langkey]['product_name'] = $requestjson1['name'];
					$requestjson1['product_description'][$langkey]['name'] = $requestjson1['name'];
					$requestjson1['product_description'][$langkey]['description'] = isset($requestjson1['description'])?$requestjson1['description']:'';
					$requestjson1['product_description'][$langkey]['tag'] = isset($requestjson1['tag'])?$requestjson1['tag']:'';
					$requestjson1['product_description'][$langkey]['meta_title'] =  isset($requestjson1['meta_title'])?$requestjson1['meta_title']:'';
					$requestjson1['product_description'][$langkey]['meta_description'] = isset($requestjson1['meta_description'])?$requestjson1['meta_description']:'';
					$requestjson1['product_description'][$langkey]['meta_keyword'] = isset($requestjson1['meta_keyword'])?$requestjson1['meta_keyword']:'';
				}
				//	$requestjson1['product_seo_url'][1]['product_seo_url'] = $requestjson1['product_seo_url'];
				//discount
				if(isset($requestjson1['product_discount'])) {
					$requestjson1['product_discount'][0]['customer_group_id'] = isset($requestjson1['customer_group_id'])?$requestjson1['customer_group_id']:'';
					$requestjson1['product_discount'][0]['quantity'] = isset($requestjson1['discount_quantity'])?$requestjson1['discount_quantity']:'';
					$requestjson1['product_discount'][0]['priority'] = isset($requestjson1['discount_priority'])?$requestjson1['discount_priority']:'';
					$requestjson1['product_discount'][0]['price'] = isset($requestjson1['discount_price'])?$requestjson1['discount_price']:'';
					$requestjson1['product_discount'][0]['date_start'] = isset($requestjson1['discount_date_start'])?$requestjson1['discount_date_start']:'';
					$requestjson1['product_discount'][0]['date_end'] = isset($requestjson1['discount_date_end'])?$requestjson1['discount_date_end']:'';
				}
				//special
				if(isset($requestjson1['product_special'])) {
					$requestjson1['product_special'][0]['price'] = isset($requestjson1['special_price'])?$requestjson1['special_price']:'';
					$requestjson1['product_special'][0]['priority'] = isset($requestjson1['special_priority'])?$requestjson1['special_priority']:'';
					$requestjson1['product_special'][0]['customer_group_id'] = isset($requestjson1['customer_group_idspecial'])?$requestjson1['customer_group_idspecial']:'';
					$requestjson1['product_special'][0]['date_start'] = isset($requestjson1['special_date_start'])?$requestjson1['special_date_start']:'';
					$requestjson1['product_special'][0]['date_end'] = isset($requestjson1['special_date_end'])?$requestjson1['special_date_end']:'';
				}
				//rewards points
				$requestjson1['product_reward']['points'] = isset($requestjson1['rewardpoints'])?$requestjson1['rewardpoints']:array();
				$requestjson1['product_reward'][1]['points'] = isset($requestjson1['points'])?$requestjson1['points']:array();
				//SEO
				//$requestjson1['product_seo_url'][0][1] = $requestjson1['keyword'];
				$this->model_extension_purpletree_multivendor_sellerproduct->addProduct($requestjson1);
				//$this->session->data['success'] = $this->language->get('text_success_add');
				$json['status'] = 'success';
				$json['message'] =  $this->language->get('text_success_add');
			}
			$filter_data = array();
			$json['data']['image'] = 'no_image.png';
			$json['data']['product_name'] = '';
			if (defined('QUICK_ORDER') && QUICK_ORDER == 1 ){
			$product_name = 'Delivery'.date('d-m-y');
			$json['data']['product_name'] = str_replace("-","",$product_name);
			}
			$this->load->model('tool/image');
			$json['data']['thumb'] = $this->model_tool_image->resize('no_image.jpg', 100, 100);
			$json['data']['all_categories'] = array();
			if ($this->request->server['REQUEST_METHOD'] != 'POST') {
				$json['data']['product_images'] = array();
				$results = $this->model_extension_purpletree_multivendor_sellerproduct->getCategories($filter_data);
				foreach ($results as $result) {
					$json['data']['all_categories'][] = array(
					'category_id' => $result['category_id'],
					'name'        => strip_tags(html_entity_decode($result['name'], ENT_QUOTES, 'UTF-8'))
					);
				}
				$json['data']['stores'][] = array(
				'store_id' => 0,
				'name'     => $this->language->get('text_default')
				);
				$stores = $this->model_setting_store->getStores();
				foreach ($stores as $store) {
					$json['data']['stores'][] = array(
					'store_id' => $store['store_id'],
					'name'     => $store['name']
					);
				}
				$json['data']['manufacturer'] = array();
				$results1 = $this->model_extension_purpletree_multivendor_sellerproduct->getManufacturers($filter_data);
				foreach ($results1 as $result2) {
					$json['data']['manufacturer'][] = array(
					'manufacturer_id' => $result2['manufacturer_id'],
					'name'            => strip_tags(html_entity_decode($result2['name'], ENT_QUOTES, 'UTF-8'))
					);
				}
				$json['data']['filters'] = array();
				$filters = $this->model_extension_purpletree_multivendor_sellerproduct->getFilters($filter_data);
				foreach ($filters as $filter) {
					$json['data']['filters'][] = array(
					'filter_id' => $filter['filter_id'],
					'name'      => strip_tags(html_entity_decode($filter['group'] . ' &gt; ' . $filter['name'], ENT_QUOTES, 'UTF-8'))
					);
				}
				$json['data']['downloads'] = array();
				$results = $this->model_extension_purpletree_multivendor_sellerproduct->getDownloads($filter_data);
				foreach ($results as $result) {
					$json['data']['downloads'][] = array(
					'download_id' => $result['download_id'],
					'name'        => strip_tags(html_entity_decode($result['name'], ENT_QUOTES, 'UTF-8'))
					);
				}
				
				$results = $this->model_extension_purpletree_multivendor_sellerproduct->getProducts($filter_data);
				
				foreach ($results as $result) {
					$option_data = array();
					$product_options = $this->model_catalog_product->getProductOptions($result['product_id']);
					foreach ($product_options as $product_option) {
						$option_info = $this->model_extension_catalog_option->getOption($product_option['option_id']);
						if ($option_info) {
							$product_option_value_data = array();
							foreach ($product_option['product_option_value'] as $product_option_value) {
								$option_value_info = $this->model_extension_catalog_option->getOptionValue($product_option_value['option_value_id']);
								if ($option_value_info) {
									$product_option_value_data[] = array(
									'product_option_value_id' => $product_option_value['product_option_value_id'],
									'option_value_id'         => $product_option_value['option_value_id'],
									'name'                    => $option_value_info['name'],
									'price'                   => (float)$product_option_value['price'] ? $this->currency->format($product_option_value['price'], $this->session->data['currency']) : false,
									'price_prefix'            => $product_option_value['price_prefix']
									);
								}
							}
							$option_data[] = array(
							'product_option_id'    => $product_option['product_option_id'],
							'product_option_value' => $product_option_value_data,
							'option_id'            => $product_option['option_id'],
							'name'                 => $option_info['name'],
							'type'                 => $option_info['type'],
							'value'                => $product_option['value'],
							'required'             => $product_option['required']
							);
						}
					}
					
					
					$json['data']['related_products'][] = array(
					'product_id' => $result['product_id'],
					'name'       => strip_tags(html_entity_decode($result['name'], ENT_QUOTES, 'UTF-8')),
					'model'      => $result['model'],
					'option'     => $option_data,
					'price'      => $result['price']
					);
				}
				
			}
			//$this->index();
			$this->load->model('extension/localisation/stock_status');
			$json['data']['stock_status'] = $this->model_extension_localisation_stock_status->getStockStatuses();
			$this->load->model('extension/purpletree_multivendor/customer_group');
			$json['data']['customer_groups'] = $this->model_extension_purpletree_multivendor_customer_group->getCustomerGroups();
			$this->response->addHeader('Content-Type: application/json');
			return $this->response->setOutput(json_encode($json));
		}
		public function edit() {
			$this->checkPlugin();
			$this->load->language('purpletree_multivendor/api');
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
			
			$this->load->language('purpletree_multivendor/sellerproduct');
			$this->load->model('extension/purpletree_multivendor/sellerproduct');
			if ($this->request->server['REQUEST_METHOD'] == 'POST') {
				$requestjson2 = file_get_contents('php://input');
				$requestjson1 = json_decode($requestjson2, true);
				$requestjson1 = $requestjson1['data'];
		        $requestjson1['image'] = $requestjson1['image'];
				$requestjson1['product_category'] = array();
				foreach($requestjson1['all_categories'] as $producyttc) {
					if((gettype($producyttc['category_id']) == 'string' && $producyttc['checkedvalue'] == 1) || (gettype($producyttc['category_id']) == 'integer')) {	
						$requestjson1['product_category'][] = $producyttc['category_id'];
					}
				}
				$requestjson1['product_image'] = $requestjson1['product_images'];
				if(isset($requestjson1['related_products'])) {
					$requestjson1['product_related'] = array();
					foreach($requestjson1['related_products'] as $relatedpross) {
						if((gettype($relatedpross['product_id']) == 'string' && $relatedpross['checkedrelated'] == 1) || (gettype($relatedpross['product_id']) == 'integer')) {
							$requestjson1['product_related'][] = $relatedpross['product_id'];
						}
					}
				}
				if(isset($requestjson1['stores'])) {
					$requestjson1['product_store'] = array();
					foreach($requestjson1['stores'] as $storesss) {
						if((gettype($storesss['store_id']) == 'string' && $storesss['checkedstore'] == 1) || (gettype($storesss['store_id']) == 'integer')) {
							$requestjson1['product_store'][] = $storesss['store_id'];
						}
					}
				}
				if(isset($requestjson1['downloads'])) {
					$requestjson1['product_download'] = array();
					foreach($requestjson1['downloads'] as $downloadsss) {
						if((gettype($downloadsss['download_id']) == 'string' && $downloadsss['checkedDownload'] == 1) || (gettype($downloadsss['download_id']) == 'integer')) {
							$requestjson1['product_download'][] = $downloadsss['download_id'];
						}
					}
				}
				if(isset($requestjson1['filters'])) {
					$requestjson1['product_filter'] = array();
					foreach($requestjson1['filters'] as $filterssss) {
						if((gettype($filterssss['filter_id']) == 'string' && $filterssss['checkedFilter'] == 1) || (gettype($filterssss['filter_id']) == 'integer')) {
							$requestjson1['product_filter'][] = $filterssss['filter_id'];
						}
					}
				}
				$languagesall1 = $this->model_localisation_language->getLanguages();
				$languagesall  = array();
				foreach($languagesall1 as $languagg) {
					$languagesall[] = $languagg['language_id'];
				}
				foreach($languagesall as $langkey) {
					$requestjson1['product_description'][$langkey]['product_name'] = $requestjson1['name'];
					$requestjson1['product_description'][$langkey]['name'] = $requestjson1['name'];
					$requestjson1['product_description'][$langkey]['description'] = $requestjson1['description'];
					$requestjson1['product_description'][$langkey]['tag'] = $requestjson1['tag'];
					$requestjson1['product_description'][$langkey]['meta_title'] =  $requestjson1['meta_title'];
					$requestjson1['product_description'][$langkey]['meta_description'] = $requestjson1['meta_description'];
					$requestjson1['product_description'][$langkey]['meta_keyword'] = $requestjson1['meta_keyword'];
				}
				if(isset($requestjson1['product_seo_url'])) {
					$requestjson1['product_seo_url'] = $requestjson1['product_seo_url'];
				}
				if(isset($requestjson1['product_special'])) {
					$requestjson1['product_special'] = $requestjson1['product_special'];
				}
				if(isset($requestjson1['product_discount'])) {
					$requestjson1['product_discount'] = $requestjson1['product_discount'];
				}
				
				//$logger = new Log('error.log'); 
				//foreach($requestjson1['product_option'] as $ddd) {
				//foreach($ddd as $ddd1) {
				//$logger->write("Product ID ".$ddd1);/*
				//}
				//}
				$requestjson1['product_seo_url'][0][1] = $requestjson1['keyword'];
				if(isset($requestjson1['product_option']) && !empty($requestjson1['product_option'])) {
					$requestjson1['product_option'] = $requestjson1['product_option'];
					} else { 
					unset($requestjson1['product_option']);
				}
				$requestjson1['product_reward'] = $requestjson1['product_reward'];
				$this->model_extension_purpletree_multivendor_sellerproduct->editProduct($requestjson1['product_id'],$requestjson1);
				$json['status'] = 'success';
				$json['message'] =  $this->language->get('text_success_add');
				$this->response->addHeader('Content-Type: application/json');
				return $this->response->setOutput(json_encode($json));
			}
			$this->Productform();
		}
		public function Productform(){
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
			$this->load->language('purpletree_multivendor/sellerstore');
			$store_id = (isset($store_detail['id'])?$store_detail['id']:'');
			$this->load->model('extension/purpletree_multivendor/vendor');
			
			$store_detail = $this->customer->isSeller();
			
			if (isset($store_id)) {
				$json['data']['store_id'] = $store_id;
				} else {
				$json['data']['store_id'] = 0;
			}
			
			if (isset($this->session->data['success'])) {
				$json['message'] = $this->session->data['success'];
				
				unset($this->session->data['success']);
				} else {
				$json['message'] = '';
			}
			
			if (isset($this->error['store_name'])) {
				$json['messages']['store_name'] = $this->error['store_name'];
				$json['status'] = 'error';
			}
			
			if (isset($this->error['store_seo'])) {
				$json['messages']['store_seo'] = $this->error['store_seo'];
				$json['status'] = 'error';
			} 
			if (isset($this->error['error_file_upload'])) {
				$json['messages']['error_file_upload'] = $this->error['error_file_upload'];
				$json['status'] = 'error';
			}
			
			if (isset($this->error['store_email'])) {
				$json['messages']['store_email'] = $this->error['store_email'];
				$json['status'] = 'error';
			}
			
			if (isset($this->error['store_phone'])) {
				$json['messages']['store_phone'] = $this->error['store_phone'];
				$json['status'] = 'error';
			}
			
			if (isset($this->error['store_address'])) {
				$json['messages']['store_address'] = $this->error['store_address'];
				$json['status'] = 'error';
			}
			
			if (isset($this->error['store_city'])) {
				$json['messages']['store_city'] = $this->error['store_city'];
				$json['status'] = 'error';
			}
			
			if (isset($this->error['store_country'])) {
				$json['messages']['store_country'] = $this->error['store_country'];
				$json['status'] = 'error';
			}
			
			if (isset($this->error['error_storezone'])) {
				$json['messages']['error_storezone'] = $this->error['error_storezone'];
				$json['status'] = 'error';
			}
			
			if (isset($this->error['store_zipcode'])) {
				$json['messages']['store_zipcode'] = $this->error['store_zipcode'];
				$json['status'] = 'error';
			}
			
			if (isset($this->error['store_shipping'])) {
				$json['messages']['store_shipping'] = $this->error['store_shipping'];
				$json['status'] = 'error';
			}
			
			if (isset($this->error['store_return'])) {
				$json['messages']['store_return'] = $this->error['store_return'];
				$json['status'] = 'error';
			}
			
			if (isset($this->error['store_meta_keywords'])) {
				$json['messages']['store_meta_keywords'] = $this->error['store_meta_keywords'];
				$json['status'] = 'error';
			}
			
			if (isset($this->error['store_meta_description'])) {
				$json['messages']['store_meta_description'] = $this->error['store_meta_description'];
				$json['status'] = 'error';
			}
			
			if (isset($this->error['store_bank_details'])) {
				$json['messages']['store_bank_details'] = $this->error['store_bank_details'];
				$json['status'] = 'error';
			}
			
			if (isset($this->error['store_tin'])) {
				$json['messages']['store_tin'] = $this->error['store_tin'];
				$json['status'] = 'error';
			}
			
			if (isset($this->error['store_shipping_charge'])) {
				$json['messages']['store_shipping_charge'] = $this->error['store_shipping_charge'];
				$json['status'] = 'error';
			}
			if (isset($this->error['warning'])) {
				$json['message'] = $this->error['warning'];
				$json['status'] = 'error';
				$this->response->addHeader('Content-Type: application/json');
				return $this->response->setOutput(json_encode($json));
			}
			$this->load->model('tool/image');
			$this->load->model('extension/localisation/stock_status');
			$this->load->model('extension/localisation/length_class');
			$this->load->model('extension/purpletree_multivendor/sellerproduct');
			$this->load->model('extension/purpletree_multivendor/customer_group');
			$this->load->model('extension/localisation/weight_class');
			$this->load->model('extension/catalog/option');
			$this->load->model('setting/store');
			$this->load->model('extension/catalog/option');
			$this->load->model('catalog/product');
			$seller_id = $this->customer->getId();
			$filter_data['seller_id'] = $seller_id;
			
			if(isset($this->request->get['product_id'])){	
				$filter_data['current_product_id'] = $this->request->get['product_id'];
			}
			//echo"<pre>"; print_r($this->request->server['REQUEST_METHOD']); die;
			if (isset($seller_id) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
				if(isset($this->request->get['product_id'])){	
					$product_infoq = $this->model_extension_purpletree_multivendor_sellerproduct->getProduct($this->request->get['product_id'],$seller_id);
				
					$json['data']['product_reward'] = $this->model_extension_purpletree_multivendor_sellerproduct->getProductRewards($this->request->get['product_id']);
				}
				$json['data']['stock_status'] = $this->model_extension_localisation_stock_status->getStockStatuses();
				$json['data']['product_attributes'] = $this->model_extension_purpletree_multivendor_sellerproduct->getAttributes();
				$json['data']['product_lengthclasses'] = $this->model_extension_localisation_length_class->getLengthClasses();
				$json['data']['weight_classes'] = $this->model_extension_localisation_weight_class->getWeightClasses();
				$json['data']['customer_groups'] = $this->model_extension_purpletree_multivendor_customer_group->getCustomerGroups();
				$json['data']['product_option'] = $this->model_extension_catalog_option->getOptions($filter_data);
				// Categories
				//$this->load->model('catalog/category');
				
				if (isset($this->request->post['product_category'])) {
					$categories = $this->request->post['product_category'];
					} elseif (isset($this->request->get['product_id'])) {
					$categories = $this->model_extension_purpletree_multivendor_sellerproduct->getProductCategories($this->request->get['product_id']);
					} else {
					$categories = array();
				}
				
				$product_categories = array();
				
				foreach ($categories as $category_id) {
					$category_info = $this->model_extension_purpletree_multivendor_sellerproduct->getCategory($category_id);
					
					if ($category_info) {
						$product_categories[] = $category_info['category_id'];
					}
				}
				
			}
			if (isset($this->request->post['product_related'])) {
				$relatedpproducts = $this->request->post['product_related'];
				} elseif (isset($this->request->get['product_id'])) {
				$relatedpproducts = $this->model_extension_purpletree_multivendor_sellerproduct->getProductRelated($this->request->get['product_id']);
				} else {
				$relatedpproducts = array();
			}
			$results = $this->model_extension_purpletree_multivendor_sellerproduct->getProducts($filter_data);
			foreach ($results as $result) {
				$checkedrelated = false;
				if(!empty($relatedpproducts) && in_array($result['product_id'],$relatedpproducts)) {
					$checkedrelated = true;
				}
				$option_data = array();
				$product_options = $this->model_catalog_product->getProductOptions($result['product_id']);
				foreach ($product_options as $product_option) {
					$option_info = $this->model_extension_catalog_option->getOption($product_option['option_id']);
					if ($option_info) {
						$product_option_value_data = array();
						foreach ($product_option['product_option_value'] as $product_option_value) {
							$option_value_info = $this->model_extension_catalog_option->getOptionValue($product_option_value['option_value_id']);
							if ($option_value_info) {
								$product_option_value_data[] = array(
								'product_option_value_id' => $product_option_value['product_option_value_id'],
								'option_value_id'         => $product_option_value['option_value_id'],
								'name'                    => $option_value_info['name'],
								'price'                   => (float)$product_option_value['price'] ? $this->currency->format($product_option_value['price'], $this->session->data['currency']) : false,
								'price_prefix'            => $product_option_value['price_prefix']
								);
							}
						}
						$option_data[] = array(
						'product_option_id'    => $product_option['product_option_id'],
						'product_option_value' => $product_option_value_data,
						'option_id'            => $product_option['option_id'],
						'name'                 => $option_info['name'],
						'type'                 => $option_info['type'],
						'value'                => $product_option['value'],
						'required'             => $product_option['required']
						
						);
					}
				}
				$json['data']['related_products'][] = array(
				'product_id' => $result['product_id'],
				'name'       => strip_tags(html_entity_decode($result['name'], ENT_QUOTES, 'UTF-8')),
				'model'      => $result['model'],
				'option'     => $option_data,
				'price'      => $result['price'],
				'checkedrelated'	   => $checkedrelated
				);
			}
			if (isset($this->request->post['product_download'])) {
				$product_downloads = $this->request->post['product_download'];
				} elseif (isset($this->request->get['product_id'])) {
				$product_downloads = $this->model_extension_purpletree_multivendor_sellerproduct->getProductDownloads($this->request->get['product_id']);
				} else {
				$product_downloads = array();
			}
			$json['data']['downloads'] = array();
			$results = $this->model_extension_purpletree_multivendor_sellerproduct->getDownloads($filter_data);
			foreach ($results as $result) {
				$checkedDownload = false;
				if(!empty($product_downloads) && in_array($result['download_id'],$product_downloads)) {
					$checkedDownload = true;
				}
				$json['data']['downloads'][] = array(
				'download_id' => $result['download_id'],
				'name'        => strip_tags(html_entity_decode($result['name'], ENT_QUOTES, 'UTF-8')),
				'checkedDownload' => $checkedDownload
				);
			}
			
			if (isset($this->request->post['product_store'])) {
				$product_store = $this->request->post['product_store'];
				} elseif (isset($this->request->get['product_id'])) {
				$product_store = $this->model_extension_purpletree_multivendor_sellerproduct->getProductStores($this->request->get['product_id']);
				} else {
				$product_store = array(0);
			}
			$checkedstore = false;
			if(in_array(0,$product_store)) {
				$checkedstore = true;
			}
			$json['data']['stores'][] = array(
			'store_id' => 0,
			'name'     => $this->language->get('text_default'),
			'checkedstore' => $checkedstore
			);
			$stores = $this->model_setting_store->getStores();
			foreach ($stores as $store) {
				$checkedstore = false;
				if(in_array($store['store_id'],$product_store)) {
					$checkedstore = true;
				}
				$json['data']['stores'][] = array(
				'store_id' => $store['store_id'],
				'name'     => $store['name'],
				'checkedstore' => $checkedstore
				);
			}
			
			$results1 = $this->model_extension_purpletree_multivendor_sellerproduct->getManufacturers($filter_data);
			
			foreach ($results1 as $result1) {
				$json['data']['manufacturer'][] = array(
				'manufacturer_id' => $result1['manufacturer_id'],
				'name'            => strip_tags(html_entity_decode($result1['name'], ENT_QUOTES, 'UTF-8'))
				);
			}
			$results = $this->model_extension_purpletree_multivendor_sellerproduct->getCategories($filter_data);
			foreach ($results as $result) {
				$checkedvalue = false;
				if(in_array($result['category_id'],$product_categories)) {
					$checkedvalue = true;
				}
				$json['data']['all_categories'][] = array(
				'category_id' => $result['category_id'],
				'name'        => strip_tags(html_entity_decode($result['name'], ENT_QUOTES, 'UTF-8')),
				'checkedvalue' => $checkedvalue
				);
			}
			
			if (isset($this->request->post['product_filter'])) {
				$productfilters = $this->request->post['product_filter'];
				} elseif (isset($this->request->get['product_id'])) {
				$productfilters = $this->model_extension_purpletree_multivendor_sellerproduct->getProductFilters($this->request->get['product_id']);
				} else {
				$productfilters = array();
			}
			$filters = $this->model_extension_purpletree_multivendor_sellerproduct->getFilters($filter_data);
			foreach ($filters as $filter) {
				$checkedFilter = false;
				if(!empty($productfilters) && in_array($filter['filter_id'],$productfilters)) {
					$checkedFilter = true;
				}
				$json['data']['filters'][] = array(
				'filter_id' => $filter['filter_id'],
				'name'      => strip_tags(html_entity_decode($filter['group'] . ' &gt; ' . $filter['name'], ENT_QUOTES, 'UTF-8')),
				'checkedFilter' => $checkedFilter
				);
			}
			if (isset($this->request->post['product_option'])) {
				$product_options = $this->request->post['product_option'];
				} elseif (isset($this->request->get['product_id'])) {
				$product_options = $this->model_extension_purpletree_multivendor_sellerproduct->getProductOptions($this->request->get['product_id']);
				} else {
				$product_options = array();
			}
			foreach ($product_options as $product_option) {
				$product_option_value_data = array();
				
				if (isset($product_option['product_option_value'])) {
					foreach ($product_option['product_option_value'] as $product_option_value) {
						$product_option_value_data = array(
						'product_option_value_id' => $product_option_value['product_option_value_id'],
						'option_value_id'         => $product_option_value['option_value_id'],
						'quantity'                => $product_option_value['quantity'],
						'subtract'                => $product_option_value['subtract'],
						'price'                   => $product_option_value['price'],
						'price_prefix'            => $product_option_value['price_prefix'],
						'points'                  => $product_option_value['points'],
						'points_prefix'           => $product_option_value['points_prefix'],
						'weight'                  => $product_option_value['weight'],
						'weight_prefix'           => $product_option_value['weight_prefix']
						);
					}
				}
				
				$json['data']['product_options_multiple'][] = array(
				'product_option_id'    => $product_option['product_option_id'],
				'product_option_value' => $product_option_value_data,
				'option_id'            => $product_option['option_id'],
				'name'                 => $product_option['name'],
				'type'                 => $product_option['type'],
				'value'                => isset($product_option['value']) ? $product_option['value'] : '',
				'required'             => $product_option['required']
				);
			}
			$json['data']['option_values'] = array();
			if(isset($json['data']['product_options_multiple'])) {
				foreach($json['data']['product_options_multiple'] as $product_option) {
					if ($product_option['type'] == 'select' || $product_option['type'] == 'radio' || $product_option['type'] == 'checkbox' || $product_option['type'] == 'image') {
						if (!isset($data['option_values'][$product_option['option_id']])) {
							$json['data']['option_values'] = $this->model_extension_catalog_option->getOptionValues($product_option['option_id']);
						}
					}
				}
			}
			if (isset($this->request->post['product_special'])) {
				$product_specials = $this->request->post['product_special'];
				} elseif (isset($this->request->get['product_id'])) {
				$product_specials = $this->model_extension_purpletree_multivendor_sellerproduct->getProductSpecials($this->request->get['product_id']);
				} else {
				$product_specials = array();
			}
			$json['data']['product_specials'] = array();
			
			foreach ($product_specials as $product_special) {
				$json['data']['product_specials'][] = array(
				'customer_group_id' => $product_special['customer_group_id'],
				'priority'          => $product_special['priority'],
				'price'             => $product_special['price'],
				'date_start'        => ($product_special['date_start'] != '0000-00-00') ? $product_special['date_start'] : '',
				'date_end'          => ($product_special['date_end'] != '0000-00-00') ? $product_special['date_end'] :  ''
				);
			}
			if (isset($this->request->post['product_discount'])) {
				$product_discounts = $this->request->post['product_discount'];
				} elseif (isset($this->request->get['product_id'])) {
				$product_discounts = $this->model_extension_purpletree_multivendor_sellerproduct->getProductDiscounts($this->request->get['product_id']);
				} else {
				$product_discounts = array();
			}
			
			$json['data']['product_discounts'] = array();
			
			foreach ($product_discounts as $product_discount) {
				$json['data']['product_discounts'][] = array(
				'customer_group_id' => $product_discount['customer_group_id'],
				'quantity'          => $product_discount['quantity'],
				'priority'          => $product_discount['priority'],
				'price'             => $product_discount['price'],
				'date_start'        => ($product_discount['date_start'] != '0000-00-00') ? $product_discount['date_start'] : '',
				'date_end'          => ($product_discount['date_end'] != '0000-00-00') ? $product_discount['date_end'] : ''
				);
			}
			
			if (isset($this->request->post['image']) && is_file(DIR_IMAGE . $this->request->post['image'])) {
				$json['data']['thumb'] = $this->model_tool_image->resize($this->request->post['image'], 50, 50);
				} elseif (!empty($product_infoq) && is_file(DIR_IMAGE . $product_infoq['image'])) {
				$json['data']['thumb'] = $this->model_tool_image->resize($product_infoq['image'], 50, 50);
				} else {
				$json['data']['thumb'] = $this->model_tool_image->resize('no_image.png', 50, 50);
			}
			
			if (isset($this->request->post['product_image'])) {
				$product_images = $this->request->post['product_image'];
				} elseif (isset($this->request->get['product_id'])) {
				$product_images = $this->model_extension_purpletree_multivendor_sellerproduct->getProductImages($this->request->get['product_id']);
				} else {
				$product_images = array();
			}
			
			$json['data']['product_images'] = array();
			
			foreach ($product_images as $product_image) {
				if (is_file(DIR_IMAGE . $product_image['image'])) {
					$image = $product_image['image'];
					$thumb = $product_image['image'];
					} else {
					$image = '';
					$thumb = 'no_image.png';
				}
				
				$json['data']['product_images'][] = array(
				'image'      => $image,
				'thumb'      => $this->model_tool_image->resize($thumb, 100, 100),
				'sort_order' => $product_image['sort_order']
				);
			}
			
			if (isset($this->request->post['product_id'])) {
				$json['data']['product_id'] = $this->request->post['product_id'];
				} elseif (!empty($product_infoq)) {
				$json['data']['product_id'] = $product_infoq['product_id'];
				} else {
				$json['data']['product_id'] = '';
			}		
			if (isset($this->request->post['keyword'])) {
				$json['data']['keyword'] = $this->request->post['keyword'];
				} elseif (!empty($product_infoq)) {
				$json['data']['keyword'] = $product_infoq['keyword'];
				} else {
				$json['data']['keyword'] = '';
			}
			if (isset($this->request->post['model'])) {
				$json['data']['model'] = $this->request->post['model'];
				} elseif (!empty($product_infoq)) {
				$json['data']['model'] = $product_infoq['model'];
				} else {
				$json['data']['model'] = '';
			}
			
			
			if (isset($this->request->post['name'])) {
				$json['data']['name'] = $this->request->post['name'];
				} elseif(!empty($product_infoq)) {
				$json['data']['name'] = $product_infoq['name'];
				} else {
				$json['data']['name'] = '';
			}
			$quickorderproduct = '';
			if (defined('QUICK_ORDER') && QUICK_ORDER == 1 ){
				if (isset($this->request->get['product_id'])) {
				$quickorderproduct = $this->model_extension_purpletree_multivendor_sellerproduct->getQucikOrderStatus($this->request->get['product_id']);
				}
			if (isset($this->request->get['product_id']) && $quickorderproduct == 1) {
				$json['data']['quick_order'] = $quickorderproduct;
				$json['data']['quick_orderr'] = $quickorderproduct;
				} elseif(isset($this->request->post['quickorderproduct'])) {
				$json['data']['quick_order'] = $this->request->post['quickorderproduct'];
				$json['data']['quick_orderr'] = $this->request->post['quickorderproduct'];
				} else {
				$json['data']['quick_order'] = '';
				$json['data']['quick_orderr'] = '';
				}
			if (isset($this->request->post['name'])) {
				$json['data']['name'] = $this->request->post['name'];
				} elseif(!empty($product_infoq)) {
				$json['data']['name'] = $product_infoq['name'];
				} else {
					if($quickorderproduct) {
				$product_name = 'Delivery'.date('d-m-y');
				$json['data']['name'] = str_replace("-","",$product_name);
					}
			}
			if (isset($this->request->post['delivery_address'])) {
				$json['data']['delivery_address'] = $this->request->post['delivery_address'];
				} elseif (!empty($product_infoq)) {
				$json['data']['delivery_address'] = $product_infoq['delivery_address'];
				} else {
				$json['data']['delivery_address'] = '';
			}
			if (isset($this->request->post['deliveraddresslat'])) {
				$json['data']['deliveraddresslat'] = $this->request->post['deliveraddresslat'];
				} elseif (!empty($product_infoq)) {
				$json['data']['deliveraddresslat'] = $product_infoq['deliveraddresslat'];
				} else {
				$json['data']['deliveraddresslat'] = '';
			}
			if (isset($this->request->post['deliveraddresslon'])) {
				$json['data']['deliveraddresslon'] = $this->request->post['deliveraddresslon'];
				} elseif (!empty($product_infoq)) {
				$json['data']['deliveraddresslon'] = $product_infoq['deliveraddresslon'];
				} else {
				$json['data']['deliveraddresslon'] = '';
			}
				
				$json['data']['quickorderproduct'] = 1;
				$json['quick_order_tab_position'] = $this->config->get('module_purpletree_multivendor_quick_order_tab_position');
			}
			if (!empty($product_infoq)) {
				$json['data']['sku'] = $product_infoq['sku'];
				} elseif(isset($this->request->post['sku'])) {
				$json['data']['sku'] = $this->request->post['sku'];
				} else {
				$json['data']['sku'] = '';
			}
			if (!empty($product_infoq)) {
				$json['data']['ean'] = $product_infoq['ean'];
				} elseif(isset($this->request->post['ean'])) {
				$json['data']['ean'] = $this->request->post['ean'];
				} else {
				$json['data']['ean'] = '';
			}
			if (!empty($product_infoq)) {
				$json['data']['upc'] = $product_infoq['upc'];
				} elseif(isset($this->request->post['upc'])) {
				$json['data']['upc'] = $this->request->post['upc'];
				} else {
				$json['data']['upc'] = '';
			}
			if (!empty($product_infoq)) {
				$json['data']['jan'] = $product_infoq['jan'];
				} elseif(isset($this->request->post['jan'])) {
				$json['data']['jan'] = $this->request->post['jan'];
				} else {
				$json['data']['jan'] = '';
			}
			if (!empty($product_infoq)) {
				$json['data']['isbn'] = $product_infoq['isbn'];
				} elseif(isset($this->request->post['isbn'])) {
				$json['data']['isbn'] = $this->request->post['isbn'];
				} else {
				$json['data']['isbn'] = '';
			}
			if (!empty($product_infoq)) {
				$json['data']['mpn'] = $product_infoq['mpn'];
				} elseif(isset($this->request->post['mpn'])) {
				$json['data']['mpn'] = $this->request->post['mpn'];
				} else {
				$json['data']['mpn'] = '';
			}
			if (!empty($product_infoq)) {
				$json['data']['location'] = $product_infoq['location'];
				} elseif(isset($this->request->post['location'])) {
				$json['data']['location'] = $this->request->post['location'];
				} else {
				$json['data']['location'] = '';
			}
			if (!empty($product_infoq)) {
				$json['data']['quantity'] = $product_infoq['quantity'];
				} elseif(isset($this->request->post['quantity'])) {
				$json['data']['quantity'] = $this->request->post['quantity'];
				} else {
				$json['data']['quantity'] = '';
			}
			if (!empty($product_infoq)) {
				$json['data']['stock_status_id'] = $product_infoq['stock_status_id'];
				} elseif(isset($this->request->post['stock_status_id'])) {
				$json['data']['stock_status_id'] = $this->request->post['stock_status_id'];
				} else {
				$json['data']['stock_status_id'] = '';
			}
			if (!empty($product_infoq)) {
				$json['data']['image'] = $product_infoq['image'];
				} elseif(isset($this->request->post['image'])) {
				$json['data']['image'] = $this->request->post['image'];
				} else {
				$json['data']['image'] = '';
			}
			if (!empty($product_infoq)) {
				$json['data']['manufacturer_id'] = $product_infoq['manufacturer_id'];
				} elseif(isset($this->request->post['manufacturer_id'])) {
				$json['data']['manufacturer_id'] = $this->request->post['manufacturer_id'];
				} else {
				$json['data']['manufacturer_id'] = '';
			}
			if (!empty($product_infoq)) {
				$json['data']['shipping'] = $product_infoq['shipping'];
				} elseif(isset($this->request->post['shipping'])) {
				$json['data']['shipping'] = $this->request->post['shipping'];
				} else {
				$json['data']['shipping'] = '';
			}
			if (!empty($product_infoq)) {
				$json['data']['price'] = $product_infoq['price'];
				} elseif(isset($this->request->post['price'])) {
				$json['data']['price'] = $this->request->post['price'];
				} else {
				$json['data']['price'] = '';
			}
			if (!empty($product_infoq)) {
				$json['data']['price_extra_type'] = $product_infoq['price_extra_type'];
				} elseif(isset($this->request->post['price_extra_type'])) {
				$json['data']['price_extra_type'] = $this->request->post['price_extra_type'];
				} else {
				$json['data']['price_extra_type'] = '';
			}
			if (!empty($product_infoq)) {
				$json['data']['price_extra'] = $product_infoq['price_extra'];
				} elseif(isset($this->request->post['price_extra'])) {
				$json['data']['price_extra'] = $this->request->post['price_extra'];
				} else {
				$json['data']['price_extra'] = '';
			}
			if (!empty($product_infoq)) {
				$json['data']['points'] = $product_infoq['points'];
				} elseif(isset($this->request->post['points'])) {
				$json['data']['points'] = $this->request->post['points'];
				} else {
				$json['data']['points'] = '';
			}
			if (!empty($product_infoq)) {
				$json['data']['tax_class_id'] = $product_infoq['tax_class_id'];
				} elseif(isset($this->request->post['tax_class_id'])) {
				$json['data']['tax_class_id'] = $this->request->post['tax_class_id'];
				} else {
				$json['data']['tax_class_id'] = '';
			}
			if (!empty($product_infoq)) {
				$json['data']['weight'] = $product_infoq['weight'];
				} elseif(isset($this->request->post['weight'])) {
				$json['data']['weight'] = $this->request->post['weight'];
				} else {
				$json['data']['weight'] = '';
			}
			if (!empty($product_infoq)) {
				$json['data']['date_available'] = $product_infoq['date_available'];
				} elseif(isset($this->request->post['date_available'])) {
				$json['data']['date_available'] = $this->request->post['date_available'];
				} else {
				$json['data']['date_available'] = '';
			}
			if (!empty($product_infoq)) {
				$json['data']['weight_class_id'] = $product_infoq['weight_class_id'];
				} elseif(isset($this->request->post['weight_class_id'])) {
				$json['data']['weight_class_id'] = $this->request->post['weight_class_id'];
				} else {
				$json['data']['weight_class_id'] = '';
			}
			if (!empty($product_infoq)) {
				$json['data']['length'] = $product_infoq['length'];
				} elseif(isset($this->request->post['length'])) {
				$json['data']['length'] = $this->request->post['length'];
				} else {
				$json['data']['length'] = '';
			}
			if (!empty($product_infoq)) {
				$json['data']['width'] = $product_infoq['width'];
				} elseif(isset($this->request->post['width'])) {
				$json['data']['width'] = $this->request->post['width'];
				} else {
				$json['data']['width'] = '';
			}
			if (!empty($product_infoq)) {
				$json['data']['height'] = $product_infoq['height'];
				} elseif(isset($this->request->post['height'])) {
				$json['data']['height'] = $this->request->post['height'];
				} else {
				$json['data']['height'] = '';
			}
			if (!empty($product_infoq)) {
				$json['data']['length_class_id'] = $product_infoq['length_class_id'];
				} elseif(isset($this->request->post['length_class_id'])) {
				$json['data']['length_class_id'] = $this->request->post['length_class_id'];
				} else {
				$json['data']['length_class_id'] = '';
			}
			if (!empty($product_infoq)) {
				$json['data']['subtract'] = $product_infoq['subtract'];
				} elseif(isset($this->request->post['subtract'])) {
				$json['data']['subtract'] = $this->request->post['subtract'];
				} else {
				$json['data']['subtract'] = '';
			}
			if (!empty($product_infoq)) {
				$json['data']['minimum'] = $product_infoq['minimum'];
				} elseif(isset($this->request->post['minimum'])) {
				$json['data']['minimum'] = $this->request->post['minimum'];
				} else {
				$json['data']['minimum'] = '';
			}
			if (!empty($product_infoq)) {
				$json['data']['sort_order'] = $product_infoq['sort_order'];
				} elseif(isset($this->request->post['sort_order'])) {
				$json['data']['sort_order'] = $this->request->post['sort_order'];
				} else {
				$json['data']['sort_order'] = '';
			}
			if (!empty($product_infoq)) {
				$json['data']['status'] = $product_infoq['status'];
				} elseif(isset($this->request->post['status'])) {
				$json['data']['status'] = $this->request->post['status'];
				} else {
				$json['data']['status'] = '';
			}
			if (!empty($product_infoq)) {
				$json['data']['viewed'] = $product_infoq['viewed'];
				} elseif(isset($this->request->post['viewed'])) {
				$json['data']['viewed'] = $this->request->post['viewed'];
				} else {
				$json['data']['viewed'] = '';
			}
			if (!empty($product_infoq)) {
				$json['data']['date_added'] = $product_infoq['date_added'];
				} elseif(isset($this->request->post['seller_id'])) {
				$json['data']['date_added'] = $this->request->post['date_added'];
				} else {
				$json['data']['date_added'] = '';
			}
			if (!empty($product_infoq)) {
				$json['data']['date_modified'] = $product_infoq['date_modified'];
				} elseif(isset($this->request->post['date_modified'])) {
				$json['data']['date_modified'] = $this->request->post['date_modified'];
				} else {
				$json['data']['date_modified'] = '';
			}
			if (!empty($product_infoq)) {
				$json['data']['language_id'] = $product_infoq['language_id'];
				} elseif(isset($this->request->post['language_id'])) {
				$json['data']['language_id'] = $this->request->post['language_id'];
				} else {
				$json['data']['language_id'] = '';
			}
			if (!empty($product_infoq)) {
				$json['data']['description'] = $product_infoq['description'];
				} elseif(isset($this->request->post['description'])) {
				$json['data']['description'] = $this->request->post['description'];
				} else {
				$json['data']['description'] = '';
			}
			if (!empty($product_infoq)) {
				$json['data']['tag'] = $product_infoq['tag'];
				} elseif(isset($this->request->post['tag'])) {
				$json['data']['tag'] = $this->request->post['tag'];
				} else {
				$json['data']['tag'] = '';
			}
			if (!empty($product_infoq)) {
				$json['data']['meta_title'] = $product_infoq['meta_title'];
				} elseif(isset($this->request->post['meta_title'])) {
				$json['data']['meta_title'] = $this->request->post['meta_title'];
				} else {
				$json['data']['meta_title'] = '';
			}
			if (!empty($product_infoq)) {
				$json['data']['meta_description'] = $product_infoq['meta_description'];
				} elseif(isset($this->request->post['meta_description'])) {
				$json['data']['meta_description'] = $this->request->post['meta_description'];
				} else {
				$json['data']['meta_description'] = '';
			}
			if (!empty($product_infoq)) {
				$json['data']['meta_keyword'] = $product_infoq['meta_keyword'];
				} elseif(isset($this->request->post['meta_keyword'])) {
				$json['data']['meta_keyword'] = $this->request->post['meta_keyword'];
				} else {
				$json['data']['meta_keyword'] = '';
			}
			if (!empty($product_infoq)) {
				$json['data']['id'] = $product_infoq['id'];
				} elseif(isset($this->request->post['id'])) {
				$json['data']['id'] = $this->request->post['id'];
				} else {
				$json['data']['id'] = '';
			}
			if (!empty($product_infoq)) {
				$json['data']['seller_id'] = $product_infoq['seller_id'];
				} elseif(isset($this->request->post['seller_id'])) {
				$json['data']['seller_id'] = $this->request->post['seller_id'];
				} else {
				$json['data']['seller_id'] = '';
			}
			if (!empty($product_infoq)) {
				$json['data']['is_featured'] = $product_infoq['is_featured'];
				} elseif(isset($this->request->post['is_featured'])) {
				$json['data']['is_featured'] = $this->request->post['is_featured'];
				} else {
				$json['data']['is_featured'] = '';
			}
			if (!empty($product_infoq)) {
				$json['data']['is_category_featured'] = $product_infoq['is_category_featured'];
				} elseif(isset($this->request->post['is_category_featured'])) {
				$json['data']['is_category_featured'] = $this->request->post['is_category_featured'];
				} else {
				$json['data']['is_category_featured'] = '';
			}
			if (!empty($product_infoq)) {
				$json['data']['is_approved'] = $product_infoq['is_approved'];
				} elseif(isset($this->request->post['is_approved'])) {
				$json['data']['is_approved'] = $this->request->post['is_approved'];
				} else {
				$json['data']['is_approved'] = '';
			}
			if (!empty($product_infoq)) {
				$json['data']['created_at'] = $product_infoq['created_at'];
				} elseif(isset($this->request->post['created_at'])) {
				$json['data']['created_at'] = $this->request->post['created_at'];
				} else {
				$json['data']['created_at'] = '';
			}
			if (!empty($product_infoq)) {
				$json['data']['updated_at'] = $product_infoq['updated_at'];
				} elseif(isset($this->request->post['updated_at'])) {
				$json['data']['updated_at'] = $this->request->post['updated_at'];
				} else {
				$json['data']['updated_at'] = '';
			}
			//echo"sssss"; die;
			// End download document file of store
			$json['status'] = 'success';
			
			$this->response->addHeader('Content-Type: application/json');
			
			return $this->response->setOutput(json_encode($json));
		}
		protected function getForm() {
			$this->checkPlugin();
			$this->load->model('extension/localisation/stock_status');
			$this->load->model('extension/purpletree_multivendor/sellerproduct');
			$this->load->model('extension/purpletree_multivendor/customer_group');
			$json['data']['stock_status'] = $this->model_extension_localisation_stock_status->getStockStatuses();
			$json['data']['product_attributes'] = $this->model_extension_purpletree_multivendor_sellerproduct->getAttributes();
			$json['data']['customer_groups'] = $this->model_extension_purpletree_multivendor_customer_group->getCustomerGroups();
			
			$json['data']['text_form'] = $this->language->get('text_add') ;
			$data['seller_id'] = $this->customer->getId();
			
			
			
			if (isset($this->error['warning'])) {
				$json['status'] = 'error';
				$json['message'] = $this->error['warning'];
				return $json;
				} else {
				$json['message'] = '';
			}
			
			if (isset($this->error['name'])) {
				$json['status'] = 'error';
				$json['message'] = $this->error['name']; 
				return $json;
				} else {
				$json['message'] = '';
			}
			
			$json['status'] = 'success';
			return $json;
		}
		protected function getList(){
			if (isset($this->request->get['filter_name'])) {
				$filter_name = $this->request->get['filter_name'];
				} else {
				$filter_name = null;
			}
			if (isset($this->request->get['page'])) {
				$page = $this->request->get['page'];
				} else {
				$page = 1;
			} 
			
			if (isset($this->error['warning'])) {
				$json['status'] = 'error';
				$json['message'] = $this->error['warning'];
				return $json;
			}
			
			if (isset($this->session->data['success'])) {
				$json['status'] = 'success';
				$json['message'] = $this->session->data['success'];
				
				unset($this->session->data['success']);
				return $json;
			}
			
			if (isset($this->session->data['error_warning'])) {
				$json['status'] = 'error';
				$json['message'] = $this->error['warning'];
				
				unset($this->session->data['error_warning']);
				return $json;
			}
			$json['data']['products'] = array();
			
			$filter_data = array(
			'filter_name'	  => $filter_name,
			//'filter_model'	  => $filter_model,
			//'filter_price'	  => $filter_price,
			//'filter_quantity' => $filter_quantity,
			//'filter_status'   => $filter_status,
			//'sort'            => $sort,
			//'order'           => $order,
			'start'           => ($page - 1) * 8,
			'limit'           => 8,
			'seller_id'		  => $this->customer->getId()	
			);
			
			$product_total = $this->model_extension_purpletree_multivendor_sellerproduct->getTotalSellerProducts($filter_data);
			$seller_id = $this->customer->getId();	
			$results = $this->model_extension_purpletree_multivendor_sellerproduct->getSellerProducts($filter_data);
			
			$this->load->model('tool/image');
			
			if(!empty($results)) {
				foreach ($results as $result) {
					if (is_file(DIR_IMAGE . $result['image'])) {
						$image = $this->model_tool_image->resize($result['image'], 200, 200);
						} else {
						$image = $this->model_tool_image->resize('no_image.png', 200, 200);
					}
					
					$special = false;
					
					$product_specials = $this->model_extension_purpletree_multivendor_sellerproduct->getProductSpecials($result['product_id']);
					
					foreach ($product_specials  as $product_special) {
						if (($product_special['date_start'] == '0000-00-00' || strtotime($product_special['date_start']) < time()) && ($product_special['date_end'] == '0000-00-00' || strtotime($product_special['date_end']) > time())) {
							$special = $product_special['price'];
							
							break;
						}
					}
					$price_extra = 0;
					if($result['price_extra_type'] == 1){$price_extra = $result['price_extra'];}
					elseif($result['price_extra_type'] == 2){$price_extra = $result['price'] * $result['price_extra']/100;}
					elseif(!$result['price_extra_type']){
						if($result['metal'] == 1 && $metals_extra_price_default[0] > 0){ // Gold
							$price_extra = $result['price'] * $metals_extra_price_default[0]/100;
						}
						if($result['metal'] == 2 && $metals_extra_price_default[1] > 0){ // Silver
							$price_extra = $result['price'] * $metals_extra_price_default[1]/100;
						}
						if($result['metal'] == 3 && $metals_extra_price_default[2] > 0){ // Platinum
							$price_extra = $result['price'] * $metals_extra_price_default[2]/100;
						}
						if($result['metal'] == 4 && $metals_extra_price_default[3] > 0){ // Palladium
							$price_extra = $result['price'] * $metals_extra_price_default[3]/100;
						}
						if($result['metal'] == 5 && $metals_extra_price_default[4] > 0){ // Copper
							$price_extra = $result['price'] * $metals_extra_price_default[4]/100;
						}
						if($result['metal'] == 6 && $metals_extra_price_default[5] > 0){ // Rhodium
							$price_extra = $result['price'] * $metals_extra_price_default[5]/100;
						}
					}
					$price = $this->currency->format($result['price'] + $price_extra,  $this->config->get('config_currency'), '', false);
					
					$json['data']['products'][] = array(
					'product_id' => $result['product_id'],
					'image'      => $image,
					'name'       => $result['name'],
					'model'      => $result['model'],
					//'price'      => $result['price'],
					'price'      => $price,
					'special'    => $special,
					'quantity'   => $result['quantity'],
					'status'     => $result['status'] ? $this->language->get('text_enabled') : $this->language->get('text_disabled'),
					'is_approved'     => $result['is_approved'] ? $this->language->get('text_yes') : $this->language->get('text_no'),
					);
				}
				$json['status'] = 'success';
				} else {
				$json['message'] = $this->language->get('no_data');
				$json['status'] = 'success';
			}
			$results1 = $this->model_extension_purpletree_multivendor_sellerproduct->getManufacturers($filter_data);
			foreach ($results1 as $result2) {
				$json['data']['manufacturer'][] = array(
				'manufacturer_id' => $result2['manufacturer_id'],
				'name'            => strip_tags(html_entity_decode($result2['name'], ENT_QUOTES, 'UTF-8'))
				);
			}
			/* if (isset($this->request->post['selected'])) {
				$json['data']['selected'] = (array)$this->request->post['selected'];
				} else {
				$json['data']['selected'] = array();
			} */
			//$json['data']['pagination']['total'] = $product_total;
			//$json['data']['pagination']['page'] = $page;
			//$json['data']['pagination']['limit'] = $this->config->get('config_limit_admin');
			
			//$json['data']['results'] = sprintf($this->language->get('text_pagination'), ($product_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($product_total - $this->config->get('config_limit_admin'))) ? $product_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $product_total, ceil($product_total / $this->config->get('config_limit_admin')));
			
			$json['data']['filter_name'] = $filter_name;
			//$json['data']['filter_model'] = $filter_model;
			//$json['data']['filter_price'] = $filter_price;
			//$json['data']['filter_quantity'] = $filter_quantity;
			//$json['data']['filter_status'] = $filter_status;
			
			//$json['data']['sort'] = $sort;
			//$json['data']['order'] = $order;
			
			
			return $json;
		}
		private function checkPlugin() {
			header('Access-Control-Allow-Origin:*');
			header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
			header('Access-Control-Max-Age: 286400');
			header('Access-Control-Allow-Credentials: true');
			header('Access-Control-Allow-Headers: languageid,LANGUAGEID,Languageid,purpletreemultivendor,Purpletreemultivendor,PURPLETREEMULTIVENDOR,xocmerchantid,XOCMERCHANTID,Xocmerchantid,XOCSESSION,xocsession,Xocsession,content-type,CONTENT-TYPE');
		}
}
?>