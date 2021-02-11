<?php
class ControllerExtensionModulePurpletreeSellerprice extends Controller {
		public function index() {
			if($this->config->get('module_purpletree_multivendor_seller_product_template')){
				if (strpos($this->config->get('config_template'), 'journal2') === 0){
					$data['journal2'] = 1;
				}	
				if(isset($this->request->get['product_id']))
				{
					$this->load->language('extension/module/purpletree_sellerprice');
					$this->load->model('extension/module/purpletree_sellerprice');
					$this->load->model('tool/image');
					$data['template_prices'] = array(); 
					$seller_prices = $this->model_extension_module_purpletree_sellerprice->getTemplatePrice($this->request->get['product_id']);			
					$data['text_seller_price'] = $this->language->get('text_seller_price');
					$data['button_add_cart'] = $this->language->get('button_add_cart');
					$data['module_purpletree_multivendor_seller_product_template'] = $this->config->get('module_purpletree_multivendor_seller_product_template');
					if(!empty($seller_prices)) {         	
						foreach($seller_prices as $seller_price){
							if ($seller_price['store_logo']) {
								$image = $this->model_tool_image->resize($seller_price['store_logo'], '200' , '200');
								}else {
								$image = $this->model_tool_image->resize('placeholder.png', '200' , '200');
							}
							
							if ($this->customer->isLogged() || !$this->config->get('config_customer_price')) {
								$price = $this->currency->format($this->tax->calculate($seller_price['price'] , $seller_price['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
								} else {
								$price = false;
							}
							if($seller_price['vacation'] !=1){
								$rating = $this->model_extension_module_purpletree_sellerprice->getStoreRating($seller_price['seller_id']);
								
								$data['template_prices'][] = array(
								'product_id'  => $this->request->get['product_id'],
								'href'        => $this->url->link('extension/account/purpletree_multivendor/sellerstore/storeview', 'seller_store_id=' . $seller_price['storeidd'],true),
								'template_id'  => $seller_price['template_id'],
								'store_logo'       => $image,						
								'store_name'        => $seller_price['store_name'],
								'seller_id'        => $seller_price['seller_id'],						
								'price'       => $price,
								'status'      =>$seller_price['status'],
								'rating'      =>$rating,
								'minimum'      =>$seller_price['minimum']					 
								);
							}
							
						}
						
						if( $data['template_prices'][0]['template_id']){
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
							return $this->load->view('extension/module/purpletree_sellerprice', $data);
						}
						
					}
				}
			}
		}
}