<?php
class ControllerExtensionAccountPurpletreeMultivendorQuickOrder extends Controller{
	private $error = array();	
	public function index(){
		if (!$this->customer->isLogged()) {
			$this->session->data['quick_redirect'] = $this->url->link('extension/account/purpletree_multivendor/quick_order', '&product_id='.$this->request->get['product_id'].$url, true);
			$this->response->redirect($this->url->link('account/login', '', true));
		}
		$order_data = array();
		$this->load->language('checkout/cart');
		$this->load->model('extension/purpletree_multivendor/quick_order');
		if ($this->request->server['REQUEST_METHOD'] == 'GET') {
		
		$products = $this->model_extension_purpletree_multivendor_quick_order->getProducts($this->request->get['product_id']);
		$seller_id = '';
		$seller_id = $this->model_extension_purpletree_multivendor_quick_order->getQucikOrderStatus($this->request->get['product_id']);
		
		$this->load->language('checkout/cart');
		if ($products['minimum'] > $products['quantity']) {
		        $this->session->data['error_warning'] = sprintf($this->language->get('error_minimum'), $products['name'], $products['minimum']);
				$this->response->redirect($this->url->link('account/account', '', true));
			}
		$pro_tax = $this->model_extension_purpletree_multivendor_quick_order->getTaxes($products);
		$pro_total = $this->model_extension_purpletree_multivendor_quick_order->getTotal($products);
		    $order_data = array();
			$totals = array();
			$taxes = $pro_tax;
			$total = $pro_total;	
			// Because __call can not keep var references so we put them into an array.
			$total_data = array(
				'totals' => &$totals,
				'taxes'  => &$taxes,
				'total'  => &$total
			);

			$this->load->model('setting/extension');

			$sort_order = array();

			$results = $this->model_setting_extension->getExtensions('total');

			foreach ($results as $key => $value) {
				$sort_order[$key] = $this->config->get('total_' . $value['code'] . '_sort_order');
			}

			array_multisort($sort_order, SORT_ASC, $results);

			foreach ($results as $result) {
				if ($this->config->get('total_' . $result['code'] . '_status')) {
					$this->load->model('extension/total/' . $result['code']);

					// We have to put the totals in an array so that they pass by reference.
					$this->{'model_extension_total_' . $result['code']}->getTotal($total_data);
					
				}
			}
           
		   $pro_sub_total = $this->model_extension_purpletree_multivendor_quick_order->getSubTotal($products);
		   $totals[0]['value'] = $pro_sub_total;
 
			$sort_order = array();

			foreach ($totals as $key => $value) {
				$sort_order[$key] = $value['sort_order'];
			}

			array_multisort($sort_order, SORT_ASC, $totals);

			$order_data['totals'] = $totals;
			
			$this->load->language('checkout/checkout');

			$order_data['invoice_prefix'] = $this->config->get('config_invoice_prefix');
			$order_data['store_id'] = $this->config->get('config_store_id');
			$order_data['store_name'] = $this->config->get('config_name');

			if ($order_data['store_id']) {
				$order_data['store_url'] = $this->config->get('config_url');
			} else {
				if ($this->request->server['HTTPS']) {
					$order_data['store_url'] = HTTPS_SERVER;
				} else {
					$order_data['store_url'] = HTTP_SERVER;
				}
			}
			
			$this->load->model('account/customer');

			if ($this->customer->isLogged()) {
				$customer_info = $this->model_account_customer->getCustomer($this->customer->getId());

				$order_data['customer_id'] = $this->customer->getId();
				$order_data['customer_group_id'] = $customer_info['customer_group_id'];
				$order_data['firstname'] = $customer_info['firstname'];
				$order_data['lastname'] = $customer_info['lastname'];
				$order_data['email'] = $customer_info['email'];
				$order_data['telephone'] = $customer_info['telephone'];
				$order_data['custom_field'] = json_decode($customer_info['custom_field'], true);
			}
			$order_data['payment_firstname'] = '';
			$order_data['payment_lastname'] = '';
			$order_data['payment_company'] = '';
			$order_data['payment_address_1'] = '';
			$order_data['payment_address_2'] = '';
			$order_data['payment_city'] = '';
			$order_data['payment_postcode'] = '';
			$order_data['payment_zone'] = '';
			$order_data['payment_zone_id'] = '';
			$order_data['payment_country'] = '';
			$order_data['payment_country_id'] = '';
			$order_data['payment_address_format'] = '';
			$order_data['payment_custom_field'] = '';
			$order_data['payment_method'] = '';
			$order_data['payment_code'] = '';
			$order_data['shipping_firstname'] = '';
				$order_data['shipping_lastname'] = '';
				$order_data['shipping_company'] = '';
				$order_data['shipping_address_1'] = '';
				$order_data['shipping_address_2'] = '';
				$order_data['shipping_city'] = '';
				$order_data['shipping_postcode'] = '';
				$order_data['shipping_zone'] = '';
				$order_data['shipping_zone_id'] = '';
				$order_data['shipping_country'] = '';
				$order_data['shipping_country_id'] = '';
				$order_data['shipping_address_format'] = '';
				$order_data['shipping_custom_field'] = array();
				$order_data['shipping_method'] = '';
				$order_data['shipping_code'] = '';
				$order_data['products'] = array();
				$option_data = array();
				$order_data['products'][] = array(
				     'seller_id' => $seller_id,
					'product_id' => $products['product_id'],
					'name'       => $products['name'],
					'model'      => $products['model'],
					'option'     => $option_data,
					'download'   => $products['download'],
					'quantity'   => $products['minimum'],
					'subtract'   => $products['subtract'],
					'price'      => $products['price'],
					'total'      => $products['total'],
					'tax'        => $this->tax->getTax($products['price'], $products['tax_class_id']),
					'reward'     => $products['reward']
				);
				$order_data['vouchers'] = array();
				$order_data['comment'] = '';
				$order_data['total'] = $total_data['total'];
				if (isset($this->request->cookie['tracking'])) {
				$order_data['tracking'] = $this->request->cookie['tracking'];
				$subtotal = $products['total'];
				// Affiliate
				$affiliate_info = $this->model_account_customer->getAffiliateByTracking($this->request->cookie['tracking']);

				if ($affiliate_info) {
					$order_data['affiliate_id'] = $affiliate_info['customer_id'];
					$order_data['commission'] = ($subtotal / 100) * $affiliate_info['commission'];
				} else {
					$order_data['affiliate_id'] = 0;
					$order_data['commission'] = 0;
				}
				// Marketing
				$this->load->model('checkout/marketing');

				$marketing_info = $this->model_checkout_marketing->getMarketingByCode($this->request->cookie['tracking']);

				if ($marketing_info) {
					$order_data['marketing_id'] = $marketing_info['marketing_id'];
				} else {
					$order_data['marketing_id'] = 0;
				}
			} else {
				$order_data['affiliate_id'] = 0;
				$order_data['commission'] = 0;
				$order_data['marketing_id'] = 0;
				$order_data['tracking'] = '';
			}
			$order_data['language_id'] = $this->config->get('config_language_id');
			$order_data['currency_id'] = $this->currency->getId($this->session->data['currency']);
			$order_data['currency_code'] = $this->session->data['currency'];
			$order_data['currency_value'] = $this->currency->getValue($this->session->data['currency']);
			$order_data['ip'] = $this->request->server['REMOTE_ADDR'];

			if (!empty($this->request->server['HTTP_X_FORWARDED_FOR'])) {
				$order_data['forwarded_ip'] = $this->request->server['HTTP_X_FORWARDED_FOR'];
			} elseif (!empty($this->request->server['HTTP_CLIENT_IP'])) {
				$order_data['forwarded_ip'] = $this->request->server['HTTP_CLIENT_IP'];
			} else {
				$order_data['forwarded_ip'] = '';
			}

			if (isset($this->request->server['HTTP_USER_AGENT'])) {
				$order_data['user_agent'] = $this->request->server['HTTP_USER_AGENT'];
			} else {
				$order_data['user_agent'] = '';
			}

			if (isset($this->request->server['HTTP_ACCEPT_LANGUAGE'])) {
				$order_data['accept_language'] = $this->request->server['HTTP_ACCEPT_LANGUAGE'];
			} else {
				$order_data['accept_language'] = '';
			}

			$this->load->model('checkout/order');

			$this->session->data['order_id'] = $this->model_extension_purpletree_multivendor_quick_order->addQuickOrder($order_data);
			$this->load->model('checkout/order');
			
			$this->model_checkout_order->addOrderHistory($this->session->data['order_id'],$this->config->get('config_order_status_id'));
			unset($this->session->data['quick_redirect']);
		}
		
		$this->response->redirect($this->url->link('checkout/success', '', true));
	}
}
?>