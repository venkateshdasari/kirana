<?php
class ControllerExtensionModulePurpletreeSellerdetail extends Controller {
		public function index() { 		
			if($this->config->get('module_purpletree_multivendor_status') && $this->config->get('module_purpletree_sellerdetail_status')){			
				if(isset($this->request->get['product_id']))
				{					
					$this->load->language('purpletree_multivendor/storeview');
				    $this->load->model('extension/purpletree_multivendor/vendor');
					$this->load->model('tool/image');
					$data['text_sellercontact'] = $this->language->get('text_sellercontact');				
					$data['text_returnpolicy'] = $this->language->get('text_returnpolicy');
					$data['text_shippingpolicy'] = $this->language->get('text_shippingpolicy');
					$data['text_aboutstore'] = $this->language->get('text_aboutstore');
					$data['module_purpletree_multivendor_seller_name'] = $this->config->get('module_purpletree_multivendor_seller_name');
					$seller_detail = $this->model_extension_purpletree_multivendor_sellerproduct->getSellername($this->request->get['product_id']);
					$seller_prices = array();
					if($this->config->get('module_purpletree_multivendor_seller_product_template')){
					$this->load->model('extension/module/purpletree_sellerprice');
					$seller_prices = $this->model_extension_module_purpletree_sellerprice->getTemplatePrice($this->request->get['product_id']);
					}
					if(empty($seller_prices)) {
					if(!empty($seller_detail)) {
					$seller_store_id = '';
					$seller_store_id = $seller_detail['id'];
					
					$store_detail = $this->model_extension_purpletree_multivendor_vendor->getStore($seller_store_id);
					$seller_info_social = $this->model_extension_purpletree_multivendor_vendor->getStoreSocial($seller_store_id);
				if (!empty($seller_info_social) && isset($seller_info_social['facebook_link'])) {
					$data['facebook_link'] = $seller_info_social['facebook_link'];
				} else {
					$data['facebook_link'] = '';
				}	
				if (!empty($seller_info_social) && isset($seller_info_social['google_link'])) {
					$data['google_link'] = $seller_info_social['google_link'];
				} else {
					$data['google_link'] = '';
				}	
				if (!empty($seller_info_social) && isset($seller_info_social['twitter_link'])) {
					$data['twitter_link'] = $seller_info_social['twitter_link'];
				} else {
					$data['twitter_link'] = '';
				}		
				if (!empty($seller_info_social) && isset($seller_info_social['instagram_link'])) {
					$data['instagram_link'] = $seller_info_social['instagram_link'];
				} else {
					$data['instagram_link'] = '';
				}		
				if (!empty($seller_info_social) && isset($seller_info_social['pinterest_link'])) {
					$data['pinterest_link'] = $seller_info_social['pinterest_link'];
				} else {
					$data['pinterest_link'] = '';
				}		
				if (!empty($seller_info_social) && isset($seller_info_social['wesbsite_link'])) {
					$data['wesbsite_link'] = $seller_info_social['wesbsite_link'];
				} else {
					$data['wesbsite_link'] = '';
				}		
							
					if (!empty($seller_info_social) && isset($seller_info_social['whatsapp_link'])) {
					$whatsapp_no = $seller_info_social['whatsapp_link'];
				} else {
					$whatsapp_no = '';
				}	
				
				// Check mobile/desktop
				
				$tablet_browser = 0;
				$mobile_browser = 0;
				$data['whatsapp_link']='';
				   if (isset($_SERVER['HTTP_USER_AGENT'])) {
				if (preg_match('/(tablet|ipad|playbook)|(android(?!.*(mobi|opera mini)))/i', strtolower($_SERVER['HTTP_USER_AGENT']))) {
					$tablet_browser++;
				}
				 
				if (preg_match('/(up.browser|up.link|mmp|symbian|smartphone|midp|wap|phone|android|iemobile)/i', strtolower($_SERVER['HTTP_USER_AGENT']))) {
					$mobile_browser++;
				}
				}
				if (isset($_SERVER['HTTP_ACCEPT'])) {
				if ((strpos(strtolower($_SERVER['HTTP_ACCEPT']),'application/vnd.wap.xhtml+xml') > 0) or ((isset($_SERVER['HTTP_X_WAP_PROFILE']) or isset($_SERVER['HTTP_PROFILE'])))) {
					$mobile_browser++;
				}
				}
				 if (isset($_SERVER['HTTP_USER_AGENT'])) { 
				$mobile_ua = strtolower(substr($_SERVER['HTTP_USER_AGENT'], 0, 4));
				$mobile_agents = array(
					'w3c ','acs-','alav','alca','amoi','audi','avan','benq','bird','blac',
					'blaz','brew','cell','cldc','cmd-','dang','doco','eric','hipt','inno',
					'ipaq','java','jigs','kddi','keji','leno','lg-c','lg-d','lg-g','lge-',
					'maui','maxo','midp','mits','mmef','mobi','mot-','moto','mwbp','nec-',
					'newt','noki','palm','pana','pant','phil','play','port','prox',
					'qwap','sage','sams','sany','sch-','sec-','send','seri','sgh-','shar',
					'sie-','siem','smal','smar','sony','sph-','symb','t-mo','teli','tim-',
					'tosh','tsm-','upg1','upsi','vk-v','voda','wap-','wapa','wapi','wapp',
					'wapr','webc','winw','winw','xda ','xda-');
				 
				if (in_array($mobile_ua,$mobile_agents)) {
					$mobile_browser++;
				}
				 
				if (strpos(strtolower($_SERVER['HTTP_USER_AGENT']),'opera mini') > 0) {
					$mobile_browser++;
					//Check for tablets on opera mini alternative headers
					$stock_ua = strtolower(isset($_SERVER['HTTP_X_OPERAMINI_PHONE_UA'])?$_SERVER['HTTP_X_OPERAMINI_PHONE_UA']:(isset($_SERVER['HTTP_DEVICE_STOCK_UA'])?$_SERVER['HTTP_DEVICE_STOCK_UA']:''));
					if (preg_match('/(tablet|ipad|playbook)|(android(?!.*mobile))/i', $stock_ua)) {
					  $tablet_browser++;
					}
				}
				}
				 
				if ($tablet_browser > 0) {
				   // do something for tablet devices
				   if($whatsapp_no!=''){
				   $data['whatsapp_link']='https://api.whatsapp.com/send?phone='.$whatsapp_no;
				   }
				}
				else if ($mobile_browser > 0) {
				   // do something for mobile devices
				   if($whatsapp_no!=''){
				   $data['whatsapp_link']='https://api.whatsapp.com/send?phone='.$whatsapp_no;
				   }
				}
				else {
				   // do something for everything else
					   if($whatsapp_no!=''){
							$data['whatsapp_link']='https://web.whatsapp.com/send?phone='.$whatsapp_no;
					   }
				}
						$data['seller_name'] = $seller_detail['seller_name'];if (!empty($store_detail)) {
					$data['store_address'] = $store_detail['store_address'];
					$data['store_addresslen'] = strlen($data['store_address']);
				} else {
					$data['store_address'] = '';
				}		
						
				if (isset($this->request->post['store_address'])) {
					$data['store_address'] = $this->request->post['store_address'];
					$data['store_addresslen'] = strlen($data['store_address']);
				} elseif (!empty($store_detail)) {
					$data['store_address'] = $store_detail['store_address'];
					$data['store_addresslen'] = strlen($data['store_address']);
				} else {
					$data['store_address'] = '';
				}
			
				
				$data['store_rating'] = $this->model_extension_purpletree_multivendor_vendor->getStoreRating($store_detail['seller_id']);
				$this->load->model('extension/purpletree_multivendor/vendor');
				$cus_seller_email  = $this->model_extension_purpletree_multivendor_vendor->getCustomerEmailId($store_detail['seller_id']);
				
				$data['module_purpletree_multivendor_store_email'] = $this->config->get('module_purpletree_multivendor_store_email');
				$data['module_purpletree_multivendor_store_phone'] = $this->config->get('module_purpletree_multivendor_store_phone');
				$data['module_purpletree_multivendor_store_address'] = $this->config->get('module_purpletree_multivendor_store_address');
				 $data['module_purpletree_multivendor_store_social_link'] = $this->config->get('module_purpletree_multivendor_store_social_link');///Social links
				$data['seller_name'] = $store_detail['seller_name'];
				$data['store_email'] = $cus_seller_email;
				$data['store_phone'] = $store_detail['store_phone'];
				$data['store_tin'] = $store_detail['store_tin'];
				$data['store_zipcode'] = $store_detail['store_zipcode'];
				$data['store_description'] = html_entity_decode($store_detail['store_description'], ENT_QUOTES, 'UTF-8');
				$data['store_timings'] = html_entity_decode($store_detail['store_timings'], ENT_QUOTES, 'UTF-8');
				$data['store_address'] = html_entity_decode($store_detail['store_address'], ENT_QUOTES, 'UTF-8');
				$data['store_city'] = html_entity_decode($store_detail['store_city'], ENT_QUOTES, 'UTF-8').',';
				$data['store_state'] = $this->model_extension_purpletree_multivendor_vendor->getStateName($store_detail['store_state'],$store_detail['store_country']);
				$data['store_zipcode'] = $store_detail['store_zipcode'];
				$data['store_country'] = $this->model_extension_purpletree_multivendor_vendor->getCountryName($store_detail['store_country']).',';
				$data['seller_review_status'] = $this->config->get('module_purpletree_multivendor_seller_review');
				$data['store_review'] = $this->url->link('extension/account/purpletree_multivendor/sellerstore/sellerreview','seller_id=' . $store_detail['seller_id'], true);
				
				$data['store_shipping_policy'] = $this->url->link('extension/account/purpletree_multivendor/sellerstore/storedesc','path=shippingpolicy'. '&seller_store_id=' . $store_detail['id'], true);
				
				$data['store_return_policy'] = $this->url->link('extension/account/purpletree_multivendor/sellerstore/storedesc','path=returnpolicy'. '&seller_store_id=' . $store_detail['id'], true);
				
				$data['store_about'] = $this->url->link('extension/account/purpletree_multivendor/sellerstore/storedesc','path=aboutstore'. '&seller_store_id=' . $store_detail['id'], true);
				
				$data['seller_contact'] = $this->url->link('extension/account/purpletree_multivendor/sellercontact/customerReply','seller_id=' . $store_detail['seller_id'], true);	
				if (is_file(DIR_IMAGE . $store_detail['store_logo'])) {
					$data['store_logo'] = $this->model_tool_image->resize($store_detail['store_logo'], 150, 150);
				} else {
					$data['store_logo'] = $this->model_tool_image->resize('no_image.png', 150, 150);
				}
				
				$data['currenttheme'] = $this->config->get('theme_default_directory');
				$direction = $this->language->get('direction');
			 if ($direction=='rtl'){
				$this->document->addStyle('catalog/view/javascript/purpletree/bootstrap/css/bootstrap.min-a.css');
				$this->document->addStyle('catalog/view/theme/default/stylesheet/purpletree/custom-a.css'); 
				}else{
				$this->document->addStyle('catalog/view/javascript/purpletree/bootstrap/css/bootstrap.min.css'); 
				$this->document->addStyle('catalog/view/theme/default/stylesheet/purpletree/custom.css'); 
				}
				$this->document->addStyle('catalog/view/javascript/purpletree/css/stylesheet/commonstylesheet.css');
				return $this->load->view('extension/module/purpletree_sellerdetail', $data);			
							
					}
			    }
				}
			}
		}
}