<?php
class ControllerExtensionAccountPurpletreeMultivendorCommonHeader extends Controller {
	public function index() {
		$data = array();
				// Analytics
		$this->load->model('setting/extension');

		$data['analytics'] = array();

		$analytics = $this->model_setting_extension->getExtensions('analytics');

		foreach ($analytics as $analytic) {
			if ($this->config->get('analytics_' . $analytic['code'] . '_status')) {
				$data['analytics'][] = $this->load->controller('extension/analytics/' . $analytic['code'], $this->config->get('analytics_' . $analytic['code'] . '_status'));
			}
		}

		if ($this->request->server['HTTPS']) {
			$server = $this->config->get('config_ssl');
		} else {
			$server = $this->config->get('config_url');
		}

		if (is_file(DIR_IMAGE . $this->config->get('config_icon'))) {
			$this->document->addLink($server . 'image/' . $this->config->get('config_icon'), 'icon');
		}

		$data['base'] = $server;
		$data['description'] = $this->document->getDescription();
		$data['keywords'] = $this->document->getKeywords();
		$data['links'] = $this->document->getLinks();
		$data['heading_title1'] = $this->document->getTitle();
		$data['seller_logo'] = '/admin/view/image/logo.png';
		
		$this->load->language('extension/module/purpletree_sellerpanel');  
		$this->load->language('purpletree_multivendor/header');  
		$this->load->language('account/ptsregister');  
	
		if($this->customer->isLogged() && $seller_store = $this->customer->isSeller()) {
			$this->load->model('extension/purpletree_multivendor/vendor');
			$data['logged'] = 1;
			$seller = $this->model_extension_purpletree_multivendor_vendor->getsellerInfo();
				$data['firstname'] = '';
					$data['lastname'] = '';
			if($seller) {
				if(isset($seller['store_logo']) && $seller['store_logo'] != '') {
					//$data['seller_logo'] = 'image/'.$seller['store_logo'];
				}
				if(isset($seller['firstname'])) {
					$data['firstname'] = $seller['firstname'];
				} 
			
				if(isset($seller['lastname'])) {
					$data['lastname']  = $seller['lastname'];
				}
				}
				$data['profile'] 			= $this->url->link('extension/account/purpletree_multivendor/sellerstore', '', true);
				$data['storename'] 				= '';
			if(isset($seller['store_name'])) {
				$data['storename'] 				= $seller['store_name'];
			}
			$data['storeurl'] 				= 	'';
			if(isset($seller["id"])) {
				$data['storeurl'] 				= $this->url->link('extension/account/purpletree_multivendor/sellerstore/storeview&seller_store_id='.$seller["id"], '', true);
			}
				$data['currency'] = $this->load->controller('extension/account/purpletree_multivendor/common/currency');
			}
			$data['dashboardpageurl'] 			= $this->url->link('extension/account/purpletree_multivendor/dashboardicons', '', true);
			$data['logout'] 				= $this->url->link('account/logout', '', true);
			
			$this->load->model('tool/image');
			$data['image'] = $this->model_tool_image->resize('catalog/no_image_seller.png', 40, 40);
			$data['sellerprofile'] 			= $this->url->link('account/edit', '', true);
			$data['direction'] = $this->language->get('direction');
		
			$data['lang'] = $this->language->get('code');
			$data['language'] = $this->load->controller('extension/account/purpletree_multivendor/common/language');
			
			$data['stylespts'] = $this->document->getStylespts();
			$data['scriptspts'] = $this->document->getScriptspts('header');
			
	
		return $this->load->view('account/purpletree_multivendor/header', $data);
	}
}