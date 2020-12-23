<?php
class ControllerExtensionModuleSpecial extends Controller {
	public function index($setting) {
		$this->load->language('extension/module/special');

		$this->load->model('catalog/product');

		$this->load->model('tool/image');

		$data['products'] = array();

		$filter_data = array(
			'sort'  => 'pd.name',
			'order' => 'ASC',
			'start' => 0,
			'limit' => $setting['limit']
		);

		$results = $this->model_catalog_product->getProductSpecials($filter_data);

		if ($results) {
			foreach ($results as $result) {
				if ($result['image']) {
					$image = $this->model_tool_image->resize($result['image'], $setting['width'], $setting['height']);
				} else {
					$image = $this->model_tool_image->resize('placeholder.png', $setting['width'], $setting['height']);
				}

				if ($this->customer->isLogged() || !$this->config->get('config_customer_price')) {
					$price = $this->currency->format($this->tax->calculate($result['price'], $result['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
				} else {
					$price = false;
				}

				if ((float)$result['special']) {
					$special = $this->currency->format($this->tax->calculate($result['special'], $result['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
				} else {
					$special = false;
				}

				if ($this->config->get('config_tax')) {
					$tax = $this->currency->format((float)$result['special'] ? $result['special'] : $result['price'], $this->session->data['currency']);
				} else {
					$tax = false;
				}

				if ($this->config->get('config_review_status')) {
					$rating = $result['rating'];
				} else {
					$rating = false;
				}

$this->load->model('extension/purpletree_multivendor/sellerproduct');
			$this->load->language('account/ptsregister');
            $data['text_seller_label'] =$this->language->get('text_seller_label');
			$data['button_deliver'] =$this->language->get('button_deliver');
             $data['show_seller_name'] = $this->config->get('module_purpletree_multivendor_show_seller_name');
             $data['show_seller_address'] = $this->config->get('module_purpletree_multivendor_show_seller_address');
			 $data['multivendor_status'] = $this->config->get('module_purpletree_multivendor_status');
			 $this->load->model('extension/purpletree_multivendor/vendor');
					$seller_detail = array();
					$pts_quick_status = '';
					$seller_detail = $this->model_extension_purpletree_multivendor_sellerproduct->getSellername($result['product_id']);
					$pts_quick_status = $this->model_extension_purpletree_multivendor_sellerproduct->getQucikOrderStatus($result['product_id']);
					$seller_detailss = array();
					if($seller_detail){
				      $seller_detailss = $this->model_extension_purpletree_multivendor_vendor->getStoreDetail($seller_detail['seller_id']);
					}
					if(!empty($seller_detailss)){
					   $cuntry_name = $this->model_extension_purpletree_multivendor_vendor->getStoreDetail($seller_detail['seller_id']);
					   $cuntry_name = $this->model_extension_purpletree_multivendor_vendor->getCountryName($seller_detailss['store_country']);
					   $state_name = $this->model_extension_purpletree_multivendor_vendor->getStateName($seller_detailss['store_state'],$seller_detailss['store_country']);
					   $store_address = $seller_detailss['store_address'].','.$seller_detailss['store_city'].','.$state_name.','.$cuntry_name;
					   $seller_link  = $this->url->link('extension/account/purpletree_multivendor/sellerstore/storeview', 'seller_store_id=' . $seller_detailss['id']);
					    $google_map = $seller_detailss['google_map_link'];
					}else{
					$seller_link ='';
					$store_address = '';
					$google_map = '';
					}
				$data['products'][] = array(
'seller_name'  => isset($seller_detail['seller_name'])?$seller_detail['seller_name']:'',
						'store_address'  => $store_address,
						'google_map'  => $google_map,
						'seller_link'  => $seller_link,
						'pts_quick_status'  => $pts_quick_status,
						'quick_order'       => $this->url->link('extension/account/purpletree_multivendor/quick_order', '&product_id='.$result['product_id'], true),
					'product_id'  => $result['product_id'],
					'thumb'       => $image,
					'name'        => $result['name'],
					'description' => utf8_substr(trim(strip_tags(html_entity_decode($result['description'], ENT_QUOTES, 'UTF-8'))), 0, $this->config->get('theme_' . $this->config->get('config_theme') . '_product_description_length')) . '..',
					'price'       => $price,
					'special'     => $special,
					'tax'         => $tax,
					'rating'      => $rating,
					'href'        => $this->url->link('product/product', 'product_id=' . $result['product_id'])
				);
			}

			return $this->load->view('extension/module/special', $data);
		}
	}
}