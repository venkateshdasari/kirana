<?php
class ControllerExtensionModuleFeatured extends Controller {
	public function index($setting) {
		$this->load->language('extension/module/featured');

		$this->load->model('catalog/product');

		$this->load->model('tool/image');

		$this->document->addStyle('catalog/view/javascript/jquery/swiper/css/owl.carousel.css');
		$this->document->addStyle('catalog/view/javascript/jquery/swiper/css/owl.theme.css');
		$this->document->addScript('catalog/view/javascript/jquery/swiper/js/owl.carousel.min.js');

		$data['products'] = array();

		if (!$setting['limit']) {
			$setting['limit'] = 4;
		}

		if (!empty($setting['product'])) {
			$products = array_slice($setting['product'], 0, (int)$setting['limit']);

			foreach ($products as $product_id) {
				$product_info = $this->model_catalog_product->getProduct($product_id);

				if ($product_info) {
					if ($product_info['image']) {
						$image = $this->model_tool_image->resize($product_info['image'], $setting['width'], $setting['height']);
					} else {
						$image = $this->model_tool_image->resize('placeholder.png', $setting['width'], $setting['height']);
					}

					if ($this->customer->isLogged() || !$this->config->get('config_customer_price')) {
						$price = $this->currency->format($this->tax->calculate($product_info['price'], $product_info['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
					} else {
						$price = false;
					}

					if ((float)$product_info['special']) {
						$special = $this->currency->format($this->tax->calculate($product_info['special'], $product_info['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
					} else {
						$special = false;
					}

					if ($this->config->get('config_tax')) {
						$tax = $this->currency->format((float)$product_info['special'] ? $product_info['special'] : $product_info['price'], $this->session->data['currency']);
					} else {
						$tax = false;
					}

					if ($this->config->get('config_review_status')) {
						$rating = $product_info['rating'];
					} else {
						$rating = false;
					}

					/*Additional images start*/
                              
                            $more_images['images'] = array();
                            
                            $results = $this->model_catalog_product->getProductImages($product_info['product_id']);
                            
                            foreach ($results as $result){
                                    $more_images['images'][]=array(
                                        'popup_more' => $this->model_tool_image->resize($result['image'],$setting['width'], $setting['height'])
                                    );
                                    //print_r($more_images);
                            }
                            $more_images['product_id_images']=$product_info['product_id'];
                            
                    /*Additional images end*/

                    if($product_info['special'] > 0 AND $product_info['special'] != NULL ){
					$tag_per = ($product_info['special']*100)/$product_info['price'];
					$tag_per = round($tag_per);
					if($tag_per == 0){
					$tag_per = 1;
					}else{
					$tag_per = 100-$tag_per;
					}
					$tag = $product_info['price'] - $product_info['special'];
					}else{
					$tag = 0;
					$tag_per = 0;
					}

$this->load->model('extension/purpletree_multivendor/sellerproduct');
                $this->load->language('account/ptsregister');
                $data['text_seller_label'] =$this->language->get('text_seller_label'); 
			$data['button_deliver'] =$this->language->get('button_deliver');
			$this->load->model('extension/purpletree_multivendor/vendor');
				$data['show_seller_name'] = $this->config->get('module_purpletree_multivendor_show_seller_name');
				$data['multivendor_status'] = $this->config->get('module_purpletree_multivendor_status');
                $data['show_seller_address'] = $this->config->get('module_purpletree_multivendor_show_seller_address');
					$seller_detail = array();
					$pts_quick_status = '';
					$seller_detail = $this->model_extension_purpletree_multivendor_sellerproduct->getSellername($product_info['product_id']);
					$pts_quick_status = $this->model_extension_purpletree_multivendor_sellerproduct->getQucikOrderStatus($product_info['product_id']);
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
						'quick_order'       => $this->url->link('extension/account/purpletree_multivendor/quick_order', '&product_id='.$product_info['product_id'], true),
						'product_id'  => $product_info['product_id'],
						'thumb'       => $image,
						'tag_per'     => $tag_per,
						'name'        => $product_info['name'],
						'description' => utf8_substr(strip_tags(html_entity_decode($product_info['description'], ENT_QUOTES, 'UTF-8')), 0, $this->config->get('theme_' . $this->config->get('config_theme') . '_product_description_length')) . '..',
						'price'       => $price,
						'special'     => $special,
						'tax'         => $tax,
						'rating'      => $rating,
						  // Add images Data 
                                'more_images' => $more_images, //Additional images
                           //End
						'href'        => $this->url->link('product/product', 'product_id=' . $product_info['product_id'])
					);
				}
			}
		}

		if ($data['products']) {
			return $this->load->view('extension/module/featured', $data);
		}
	}
}