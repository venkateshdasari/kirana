<?php
class ControllerCheckoutSuccess extends Controller {
	public function index() {
		$this->load->language('checkout/success');

		if (isset($this->session->data['order_id'])) {
/// quick order ////
		$data['seller_info_on_order_sucess'] = $this->config->get('module_purpletree_multivendor_seller_info_on_order_sucess');
			$this->load->language('account/ptsregister');
			 $this->load->model('account/order');
			 $this->load->model('catalog/product');
			$products = $this->model_account_order->getOrderProducts($this->session->data['order_id']);
            if ($this->customer->isLogged()) {
			$data['pts_text_message'] = sprintf($this->language->get('pts_text_customer'), $this->url->link('account/account', '', true), $this->url->link('account/order', '', true), $this->url->link('account/download', '', true));
		} else {
			$data['pts_text_message'] = $this->language->get('pts_text_guest');
		}
		
		$data['pts_text_thanks'] = $this->language->get('pts_text_thanks');
		$data['pts_text_store_owner'] = sprintf($this->language->get('pts_text_store_owner'),$this->url->link('information/contact'));
			foreach ($products as $product) {			
               
				$product_info = $this->model_catalog_product->getProduct($product['product_id']);
                $orderd_pro_seller_id = "";
				//$seller_datile = "";
                $orderd_pro_seller_id = $this->model_account_order->getOrderedProductsellerid($this->session->data['order_id'],$product['product_id']);
				$this->load->model('extension/purpletree_multivendor/sellerorder');
				$seller_datile = $this->model_account_order->getsellerInfofororder($orderd_pro_seller_id);
				$this->load->model('extension/purpletree_multivendor/vendor');
				$seller_detailss = $this->model_extension_purpletree_multivendor_vendor->getStoreDetail($orderd_pro_seller_id);
				$cuntry_name = '';
				if(isset($seller_detailss['store_country'])) {
				$cuntry_name = $this->model_extension_purpletree_multivendor_vendor->getCountryName($seller_detailss['store_country']);
				}
				$state_name = '';
				if(isset($seller_detailss['store_state']) && isset($seller_detailss['store_country'])) {
				$state_name = $this->model_extension_purpletree_multivendor_vendor->getStateName($seller_detailss['store_state'],$seller_detailss['store_country']);
				}
				$store_address1 = '';
				if(isset($seller_detailss['store_address'])) {
					$store_address1 = $seller_detailss['store_address'];
				}
				$store_city = '';
				if(isset($seller_detailss['store_city'])) {
					$store_city = $seller_detailss['store_city'];
				}
				$store_address = $store_address1.','.$store_city.','.$state_name.','.$cuntry_name;
				$seller_link  = '';
				if(isset($seller_detailss['id'])) {
				$seller_link  = $this->url->link('extension/account/purpletree_multivendor/sellerstore/storeview', 'seller_store_id=' . $seller_detailss['id']);
				}
				$google_map = '';
				if(isset($seller_detailss['google_map_link'])) {
				$google_map = $seller_detailss['google_map_link'];
				}
				if(empty($seller_datile)){
					$seller_datile['store_name'] = '';
					$seller_datile['store_id'] = '';
					$store_address = '';
					$google_map = '';
				}			
				$data['products'][] = array(
                    'seller_store_name'    => $seller_datile['store_name'],
					'google_map'  => $google_map,
                    'seller_id'    => $orderd_pro_seller_id,
					'seller_store_id'    => $seller_datile['store_id'],					
					'name'     => $product['name'],	
					'store_address'     => $store_address,
					'seller_link'     => $seller_link				
				);
			}
			/// End quick order /////

			$this->load->model('checkout/order');
			$logger = new Log('error.log'); 
			$logger->write($_GET);
			$logger->write($this->session->data['order_id']);
			if(isset($this->request->get['payment']) && $this->request->get['payment']=="paypalpayout"){
				$logger->write($this->request->get['payment']);
				$logger->write($_POST);
				$this->model_checkout_order->addOrderHistory($this->session->data['order_id'], $this->config->get('payment_pp_adaptive_pending_status_id'));
				$this->load->model('extension/payment/pp_adaptive');
				$this->model_extension_payment_pp_adaptive->authorizeAndCapture($_POST);	
			}
			
			$this->cart->clear();

			unset($this->session->data['shipping_method']);
			unset($this->session->data['shipping_methods']);
			unset($this->session->data['payment_method']);
			unset($this->session->data['payment_methods']);
			unset($this->session->data['guest']);
			unset($this->session->data['comment']);
			unset($this->session->data['order_id']);
			unset($this->session->data['coupon']);
			unset($this->session->data['reward']);
			unset($this->session->data['voucher']);
			unset($this->session->data['vouchers']);
			unset($this->session->data['totals']);
		}

		$this->document->setTitle($this->language->get('heading_title'));

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/home')
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_basket'),
			'href' => $this->url->link('checkout/cart')
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_checkout'),
			'href' => $this->url->link('checkout/checkout', '', true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_success'),
			'href' => $this->url->link('checkout/success')
		);

		if ($this->customer->isLogged()) {
			$data['text_message'] = sprintf($this->language->get('text_customer'), $this->url->link('account/account', '', true), $this->url->link('account/order', '', true), $this->url->link('account/download', '', true), $this->url->link('information/contact'));
		} else {
			$data['text_message'] = sprintf($this->language->get('text_guest'), $this->url->link('information/contact'));
		}

		$data['continue'] = $this->url->link('common/home');

		$data['column_left'] = $this->load->controller('common/column_left');
		$data['column_right'] = $this->load->controller('common/column_right');
		$data['content_top'] = $this->load->controller('common/content_top');
		$data['content_bottom'] = $this->load->controller('common/content_bottom');
		$data['footer'] = $this->load->controller('common/footer');
		$data['header'] = $this->load->controller('common/header');

		$this->response->setOutput($this->load->view('common/success', $data));
	}
}