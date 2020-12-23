<?php
class ControllerExtensionModulePurpletreeFeaturedstore extends Controller {
		public function index($setting) {
			$this->load->language('extension/module/purpletree_featuredstore');			
			
			$this->load->model('catalog/product');
			
			$this->load->model('tool/image');
			
			$data['products'] = array();
			
			$this->load->model('extension/module/storefeatured');
			$data = array();	
			$data['store']=array();	
			$purpletree_multivendor_subscription_plans = $this->config->get('module_purpletree_multivendor_subscription_plans');
			if($purpletree_multivendor_subscription_plans==1){
				$stores = $this->model_extension_module_storefeatured->getLatest();
			}
			if(!empty($stores)){
				$i=0;
				$storearray = array();				
				$count= 0;
				foreach($stores as $store){
					if($count < 8) {
						$count++;
						if(!in_array($store['id'],$storearray)) {
							$storearray[] = $store['id'];		
							if($stores[$i]['store_logo']) {
								$store_logo = $this->model_tool_image->resize($stores[$i]['store_logo'], '200' , '200');
								} else {
								$store_logo = $this->model_tool_image->resize('placeholder.png', '200', '200');
								
							}
							$i++;
							$data['store'][]=array(
							'store_name'=>$store['store_name'],
							'store_logo'=>$store_logo,
							'href'    => $this->url->link('extension/account/purpletree_multivendor/sellerstore/storeview','seller_store_id=' . $store['id'])
							);
						}
					}
				}
				//owl carousel condition
			$themePrevent=array(
			'zeexo',
			'fastor',
			'wokiee',
			);
			if(!in_array($this->config->get('theme_default_directory'),$themePrevent) and $this->config->get('theme_default_status')==1){
				$this->document->addStyle('catalog/view/javascript/purpletree/jquery/owl-carousel/owl.carousel.css');
			$this->document->addScript('catalog/view/javascript/purpletree/jquery/owl-carousel/owl.carousel.min.js');
			}
			$direction = $this->language->get('direction');
		 if ($direction=='rtl'){
			$this->document->addStyle('catalog/view/javascript/purpletree/bootstrap/css/bootstrap.min-a.css');
			$this->document->addStyle('catalog/view/theme/default/stylesheet/purpletree/custom-a.css'); 
			}else{
			$this->document->addStyle('catalog/view/javascript/purpletree/bootstrap/css/bootstrap.min.css'); 
			$this->document->addStyle('catalog/view/theme/default/stylesheet/purpletree/custom.css'); 
			}
			}
			$data['heading_title'] = $this->language->get('heading_title');	 	
			
			return $this->load->view('extension/module/purpletree_featuredstore', $data);
		}
	}
?>