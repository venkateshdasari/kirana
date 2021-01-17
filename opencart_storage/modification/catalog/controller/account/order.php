<?php
class ControllerAccountOrder extends Controller {
	public function index() {
		if (!$this->customer->isLogged()) {
			$this->session->data['redirect'] = $this->url->link('account/order', '', true);

			$this->response->redirect($this->url->link('account/login', '', true));
		}

		$this->load->language('account/order');

		$this->document->setTitle($this->language->get('heading_title'));

		$url = '';

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/home')
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_account'),
			'href' => $this->url->link('account/account', '', true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('account/order', $url, true)
		);

		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}

		$data['orders'] = array();

		$this->load->model('account/order');

		$order_total = $this->model_account_order->getTotalOrders();

		$results = $this->model_account_order->getOrders(($page - 1) * 10, 10);

		foreach ($results as $result) {
			$product_total = $this->model_account_order->getTotalOrderProductsByOrderId($result['order_id']);
			$voucher_total = $this->model_account_order->getTotalOrderVouchersByOrderId($result['order_id']);

			$data['orders'][] = array(
				'order_id'   => $result['order_id'],

			'name'       => $result['firstname'] . ' ' . $result['lastname'],
			'status'     => $this->model_account_order->getUniqueSeller($result['order_id']),

				'date_added' => date($this->language->get('date_format_short'), strtotime($result['date_added'])),
				'products'   => ($product_total + $voucher_total),
				'total'      => $this->currency->format($result['total'], $result['currency_code'], $result['currency_value']),
				'view'       => $this->url->link('account/order/info', 'order_id=' . $result['order_id'], true),
			);
		}

		$pagination = new Pagination();
		$pagination->total = $order_total;
		$pagination->page = $page;
		$pagination->limit = 10;
		$pagination->url = $this->url->link('account/order', 'page={page}', true);

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($order_total) ? (($page - 1) * 10) + 1 : 0, ((($page - 1) * 10) > ($order_total - 10)) ? $order_total : ((($page - 1) * 10) + 10), $order_total, ceil($order_total / 10));

		$data['continue'] = $this->url->link('account/account', '', true);

		$data['column_left'] = $this->load->controller('common/column_left');
		$data['column_right'] = $this->load->controller('common/column_right');
		$data['content_top'] = $this->load->controller('common/content_top');
		$data['content_bottom'] = $this->load->controller('common/content_bottom');
		$data['footer'] = $this->load->controller('common/footer');
		$data['header'] = $this->load->controller('common/header');

		$this->response->setOutput($this->load->view('account/order_list', $data));
	}

	public function info() {

				$storesstatus = array();
			$this->load->language('account/ptsregister');
		    $data['text_seller_label'] = $this->language->get('text_seller_label');
		    $data['text_seller_label_status'] = $this->language->get('text_seller_label_status');
			$direction = $this->language->get('direction');
		 if ($direction=='rtl'){
			$this->document->addStyle('catalog/view/javascript/purpletree/bootstrap/css/bootstrap.min-a.css');
			$this->document->addStyle('catalog/view/theme/default/stylesheet/purpletree/custom-a.css');
			}else{
			$this->document->addStyle('catalog/view/javascript/purpletree/bootstrap/css/bootstrap.min.css');
			$this->document->addStyle('catalog/view/theme/default/stylesheet/purpletree/custom.css');
			}

		$this->load->language('account/order');

		if (isset($this->request->get['order_id'])) {
			$order_id = $this->request->get['order_id'];
		} else {
			$order_id = 0;
		}

		if (!$this->customer->isLogged()) {
			$this->session->data['redirect'] = $this->url->link('account/order/info', 'order_id=' . $order_id, true);

			$this->response->redirect($this->url->link('account/login', '', true));
		}

		$this->load->model('account/order');

		$order_info = $this->model_account_order->getOrder($order_id);

		if ($order_info) {
			$this->document->setTitle($this->language->get('text_order'));

			$url = '';

			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}

			$data['breadcrumbs'] = array();

			$data['breadcrumbs'][] = array(
				'text' => $this->language->get('text_home'),
				'href' => $this->url->link('common/home')
			);

			$data['breadcrumbs'][] = array(
				'text' => $this->language->get('text_account'),
				'href' => $this->url->link('account/account', '', true)
			);

			$data['breadcrumbs'][] = array(
				'text' => $this->language->get('heading_title'),
				'href' => $this->url->link('account/order', $url, true)
			);

			$data['breadcrumbs'][] = array(
				'text' => $this->language->get('text_order'),
				'href' => $this->url->link('account/order/info', 'order_id=' . $this->request->get['order_id'] . $url, true)
			);

			if (isset($this->session->data['error'])) {
				$data['error_warning'] = $this->session->data['error'];

				unset($this->session->data['error']);
			} else {
				$data['error_warning'] = '';
			}

			if (isset($this->session->data['success'])) {
				$data['success'] = $this->session->data['success'];

				unset($this->session->data['success']);
			} else {
				$data['success'] = '';
			}

			if ($order_info['invoice_no']) {
				$data['invoice_no'] = $order_info['invoice_prefix'] . $order_info['invoice_no'];
			} else {
				$data['invoice_no'] = '';
			}

			$data['order_id'] = $this->request->get['order_id'];
			$data['date_added'] = date($this->language->get('date_format_short'), strtotime($order_info['date_added']));

			if ($order_info['payment_address_format']) {
				$format = $order_info['payment_address_format'];
			} else {
				$format = '{firstname} {lastname}' . "\n" . '{company}' . "\n" . '{address_1}' . "\n" . '{address_2}' . "\n" . '{city} {postcode}' . "\n" . '{zone}' . "\n" . '{country}';
			}

			$find = array(
				'{firstname}',
				'{lastname}',
				'{company}',
				'{address_1}',
				'{address_2}',
				'{city}',
				'{postcode}',
				'{zone}',
				'{zone_code}',
				'{country}'
			);

			$replace = array(
				'firstname' => $order_info['payment_firstname'],
				'lastname'  => $order_info['payment_lastname'],
				'company'   => $order_info['payment_company'],
				'address_1' => $order_info['payment_address_1'],
				'address_2' => $order_info['payment_address_2'],
				'city'      => $order_info['payment_city'],
				'postcode'  => $order_info['payment_postcode'],
				'zone'      => $order_info['payment_zone'],
				'zone_code' => $order_info['payment_zone_code'],
				'country'   => $order_info['payment_country']
			);

			$data['payment_address'] = str_replace(array("\r\n", "\r", "\n"), '<br />', preg_replace(array("/\s\s+/", "/\r\r+/", "/\n\n+/"), '<br />', trim(str_replace($find, $replace, $format))));

			$data['payment_method'] = $order_info['payment_method'];

			if ($order_info['shipping_address_format']) {
				$format = $order_info['shipping_address_format'];
			} else {
				$format = '{firstname} {lastname}' . "\n" . '{company}' . "\n" . '{address_1}' . "\n" . '{address_2}' . "\n" . '{city} {postcode}' . "\n" . '{zone}' . "\n" . '{country}';
			}

			$find = array(
				'{firstname}',
				'{lastname}',
				'{company}',
				'{address_1}',
				'{address_2}',
				'{city}',
				'{postcode}',
				'{zone}',
				'{zone_code}',
				'{country}'
			);

			$replace = array(
				'firstname' => $order_info['shipping_firstname'],
				'lastname'  => $order_info['shipping_lastname'],
				'company'   => $order_info['shipping_company'],
				'address_1' => $order_info['shipping_address_1'],
				'address_2' => $order_info['shipping_address_2'],
				'city'      => $order_info['shipping_city'],
				'postcode'  => $order_info['shipping_postcode'],
				'zone'      => $order_info['shipping_zone'],
				'zone_code' => $order_info['shipping_zone_code'],
				'country'   => $order_info['shipping_country']
			);

			$data['shipping_address'] = str_replace(array("\r\n", "\r", "\n"), '<br />', preg_replace(array("/\s\s+/", "/\r\r+/", "/\n\n+/"), '<br />', trim(str_replace($find, $replace, $format))));

			$data['shipping_method'] = $order_info['shipping_method'];

			$this->load->model('catalog/product');
			$this->load->model('tool/upload');

			// Products
			$data['products'] = array();

			$products = $this->model_account_order->getOrderProducts($this->request->get['order_id']);

			foreach ($products as $product) {
				$option_data = array();

				$options = $this->model_account_order->getOrderOptions($this->request->get['order_id'], $product['order_product_id']);

				foreach ($options as $option) {
					if ($option['type'] != 'file') {
						$value = $option['value'];
					} else {
						$upload_info = $this->model_tool_upload->getUploadByCode($option['value']);

						if ($upload_info) {
							$value = $upload_info['name'];
						} else {
							$value = '';
						}
					}

					$option_data[] = array(
						'name'  => $option['name'],
						'value' => (utf8_strlen($value) > 20 ? utf8_substr($value, 0, 20) . '..' : $value)
					);
				}

				$product_info = $this->model_catalog_product->getProduct($product['product_id']);
$orderd_pro_seller_id = "";
				$seller_datile = "";
                $orderd_pro_seller_id = $this->model_account_order->getOrderedProductsellerid($this->request->get['order_id'],$product['product_id']);
				////quick order ////
				$this->load->model('extension/purpletree_multivendor/sellerorder');
			    $pts_order_info = $this->model_extension_purpletree_multivendor_sellerorder->getOrder($order_id,$orderd_pro_seller_id);
				//// end quick order ////
				$seller_datile = $this->model_account_order->getsellerInfofororder($orderd_pro_seller_id);
				if(empty($seller_datile)){
					$seller_datile['store_name'] = '';
					$seller_datile['store_id'] = '';
				}
				$delivery_address ='';
				$deliveraddresslon ='';
				$deliveraddresslat ='';
				if (defined('QUICK_ORDER') && QUICK_ORDER == 1 ){
			     $data['quick_order_check'] = QUICK_ORDER;
				}
				if (defined('QUICK_ORDER') && QUICK_ORDER == 1 ){
				$delivery_address1 = $this->model_extension_purpletree_multivendor_sellerorder->getDeliveryAddress($product['product_id']);
				if(isset($delivery_address1['delivery_address'])) {
					$delivery_address = $delivery_address1['delivery_address'];
				}
				if(isset($delivery_address1['deliveraddresslon'])) {
					$deliveraddresslon = $delivery_address1['deliveraddresslon'];
				}
				if(isset($delivery_address1['deliveraddresslat'])) {
					$deliveraddresslat = $delivery_address1['deliveraddresslat'];
				}
				}
				$storesstatus[$orderd_pro_seller_id] = array(
														 'seller_id'    => $orderd_pro_seller_id,
														 'seller_store_name'    => $seller_datile['store_name'],
														 'admin_order_status_id'    => $pts_order_info['admin_order_status_id'],
				'seller_order_status' => (!empty($seller_datile['store_name'])?$this->model_account_order->getLatestsellerstatus($this->request->get['order_id'], $orderd_pro_seller_id):'')
															);


				if ($product_info) {
					$reorder = $this->url->link('account/order/reorder', 'order_id=' . $order_id . '&order_product_id=' . $product['order_product_id'], true);
				} else {
					$reorder = '';
				}

				$data['products'][] = array(
'delivery_address'    => $delivery_address,
			'deliveraddresslon'    => $deliveraddresslon,
			'deliveraddresslat'    => $deliveraddresslat,
			'admin_order_status_id'    => $pts_order_info['admin_order_status_id'],
                    'seller_store_name'    => $seller_datile['store_name'],
                    'seller_id'    => $orderd_pro_seller_id,
					'seller_store_id'    => $seller_datile['store_id'],
					'seller_order_status' => (!empty($seller_datile['store_name'])?$this->model_account_order->getLatestsellerstatus($this->request->get['order_id'], $orderd_pro_seller_id):''),
					'name'     => $product['name'],
					'model'    => $product['model'],
					'option'   => $option_data,
					'quantity' => $product['quantity'],
					'price'    => $this->currency->format($product['price'] + ($this->config->get('config_tax') ? $product['tax'] : 0), $order_info['currency_code'], $order_info['currency_value']),
					'total'    => $this->currency->format($product['total'] + ($this->config->get('config_tax') ? ($product['tax'] * $product['quantity']) : 0), $order_info['currency_code'], $order_info['currency_value']),
					'reorder'  => $reorder,
					'return'   => $this->url->link('account/return/add', 'order_id=' . $order_info['order_id'] . '&product_id=' . $product['product_id'], true)
				);
			}

			// Voucher
			$data['vouchers'] = array();

			$vouchers = $this->model_account_order->getOrderVouchers($this->request->get['order_id']);

			foreach ($vouchers as $voucher) {
				$data['vouchers'][] = array(
					'description' => $voucher['description'],
					'amount'      => $this->currency->format($voucher['amount'], $order_info['currency_code'], $order_info['currency_value'])
				);
			}

			// Totals
			$data['totals'] = array();

			$totals = $this->model_account_order->getOrderTotals($this->request->get['order_id']);

			foreach ($totals as $total) {
if($total['title'] == 'Refunded'){
					$ids = array();
					$return_seller_store_name = "";
					$ids = explode('_',$total['code']);
					$seller_idd = $ids[1];
					$sellerdd = $this->model_account_order->getsellerInfofororder($seller_idd);
						$return_seller_store_name  = $sellerdd['store_name'];
					$final_title = $total['title']." "."(".($return_seller_store_name).")";
				}else{
					$final_title = $total['title'];
				}
				$data['totals'][] = array(
					'title' => $final_title,
					'text'  => $this->currency->format($total['value'], $order_info['currency_code'], $order_info['currency_value']),
				);
			}

			$data['comment'] = nl2br($order_info['comment']);

			// History
			$data['histories'] = array();

			$results = $this->model_account_order->getOrderHistories($this->request->get['order_id']);

			$data['storesstatus'] = $storesstatus;
				$data['column_updated_by'] = "Updated By";
			$resultssellers =
			$this->model_account_order->getSellerOrderHistories($this->request->get['order_id']);
			foreach ($resultssellers as $result1) {
			$data['histories'][] = array(
					'date_added' => date($this->language->get('date_format_short'), strtotime($result1['created_at'])),
					'product_name' => $this->model_account_order->getSellerOrderProducts($result1['order_id'],$result1['seller_id']),
					'status'     => $result1['status'],
					'comment'    => $result1['notify'] ? nl2br($result1['comment']) : '',
					'updated_by' => $this->model_account_order->getStoreName($result1['seller_id'])
				);
			}


			foreach ($results as $result) {

			$product22[0]['product_name'] = "All";
			$data['histories'][] = array(
					'date_added' => date($this->language->get('date_format_short'), strtotime($result['date_added'])),
					'product_name' => $product22,
					'updated_by' => "Admin",

					'status'     => $result['status'],
					'comment'    => $result['notify'] ? nl2br($result['comment']) : ''
				);
			}


			 usort($data['histories'], function($a, $b) {
			  return ($a['date_added'] < $b['date_added']) ? -1 : 1;
			});

			$data['continue'] = $this->url->link('account/order', '', true);
/////quick order ////////
			$data['customer_manage_order'] = $this->config->get('module_purpletree_multivendor_customer_manage_order');
			$orderstatus = 0;
			if(null !== $this->config->get('module_purpletree_multivendor_commission_status')) {
				$orderstatus = $this->config->get('module_purpletree_multivendor_commission_status');
				} else {
				$data['error_warning'] = $this->language->get('module_purpletree_multivendor_commission_status_warning');
			}
			$data['module_purpletree_multivendor_commission_status'] = $orderstatus;
			$this->load->model('extension/localisation/ptsorder_status');
				$data['order_statuses1'] = $this->model_extension_localisation_ptsorder_status->getOrderStatuses();
				$data['order_status_id'] = $order_info['order_status_id'];
				if(NULL !== $this->config->get('module_purpletree_multivendor_allow_order_status')){
				$data['allow_order_statuse'] = Unserialize($this->config->get('module_purpletree_multivendor_allow_order_status'));
				}
			if(!empty($data['allow_order_statuse'])){
			foreach($data['allow_order_statuse'] as $key=>$value){
				$allow_order_statuse1[$value]='selected';
			}
			}

			foreach($data['order_statuses1'] as $key => $value){
			    $allow_order_statuse4 ='';
				if(isset($allow_order_statuse1[$value['order_status_id']])){
				   $allow_order_statuse4= 'selected';
					}
				if($allow_order_statuse4 == 'selected'){
			    $data['order_statuses'][] = array(
				'order_status_id' => $value['order_status_id'],
				'name' => $value['name']
				);

				}
			}
			$data['button_change_status'] = $this->language->get('button_change_status');
			$data['text_change_order_status'] = $this->language->get('text_change_order_status');

			//// end quick order ////

			$data['column_left'] = $this->load->controller('common/column_left');
			$data['column_right'] = $this->load->controller('common/column_right');
			$data['content_top'] = $this->load->controller('common/content_top');
			$data['content_bottom'] = $this->load->controller('common/content_bottom');
			$data['footer'] = $this->load->controller('common/footer');
			$data['header'] = $this->load->controller('common/header');

			$this->response->setOutput($this->load->view('account/order_info', $data));
		} else {
			return new Action('error/not_found');
		}
	}

	///quick order///
    public function changeStatus() {
			$this->load->language('purpletree_multivendor/sellerorder');

			$this->load->model('extension/purpletree_multivendor/dashboard');

			$this->model_extension_purpletree_multivendor_dashboard->checkSellerApproval();

			$json = array();

			/* if (!isset($this->session->data['api_id'])) {
				$json['error'] = $this->language->get('error_permission');
			} else { */
			// Add keys for missing post vars
			$keys = array(
			'order_status_id',
			'notify',
			'override',
			'comment'
			);

			foreach ($keys as $key) {
				if (!isset($this->request->post[$key])) {
					$this->request->post[$key] = '';
				}
			}

			$this->load->model('extension/purpletree_multivendor/sellerorder');

			if (isset($this->request->get['order_id'])) {
				$order_id = $this->request->get['order_id'];
				} else {
				$order_id = 0;
			}
			if (isset($this->request->post['seller_id'])) {
				$seller_id = $this->request->post['seller_id'];
				}
			$order_info = $this->model_extension_purpletree_multivendor_sellerorder->getOrder($order_id,$seller_id);

			if ($order_info) {
				$this->model_extension_purpletree_multivendor_sellerorder->addOrderHistory($order_id,$seller_id, $this->request->post['order_status_id'], $this->request->post['comment'], $this->request->post['notify'], $this->request->post['override']);

				$json['success'] = $this->language->get('text_success');
				} else {
				$json['error'] = $this->language->get('error_not_found');
			}
			//}

			if (isset($this->request->server['HTTP_ORIGIN'])) {
				$this->response->addHeader('Access-Control-Allow-Origin: ' . $this->request->server['HTTP_ORIGIN']);
				$this->response->addHeader('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
				$this->response->addHeader('Access-Control-Max-Age: 1000');
				$this->response->addHeader('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
			}

			$this->response->addHeader('Content-Type: application/json');
			$this->response->setOutput(json_encode($json));
		}
		///quick order///
	public function reorder() {
		$this->load->language('account/order');

		if (isset($this->request->get['order_id'])) {
			$order_id = $this->request->get['order_id'];
		} else {
			$order_id = 0;
		}

		$this->load->model('account/order');

		$order_info = $this->model_account_order->getOrder($order_id);

		if ($order_info) {
			if (isset($this->request->get['order_product_id'])) {
				$order_product_id = $this->request->get['order_product_id'];
			} else {
				$order_product_id = 0;
			}

			$order_product_info = $this->model_account_order->getOrderProduct($order_id, $order_product_id);

			if ($order_product_info) {
				$this->load->model('catalog/product');

				$product_info = $this->model_catalog_product->getProduct($order_product_info['product_id']);

				if ($product_info) {
					$option_data = array();

					$order_options = $this->model_account_order->getOrderOptions($order_product_info['order_id'], $order_product_id);

					foreach ($order_options as $order_option) {
						if ($order_option['type'] == 'select' || $order_option['type'] == 'radio' || $order_option['type'] == 'image') {
							$option_data[$order_option['product_option_id']] = $order_option['product_option_value_id'];
						} elseif ($order_option['type'] == 'checkbox') {
							$option_data[$order_option['product_option_id']][] = $order_option['product_option_value_id'];
						} elseif ($order_option['type'] == 'text' || $order_option['type'] == 'textarea' || $order_option['type'] == 'date' || $order_option['type'] == 'datetime' || $order_option['type'] == 'time') {
							$option_data[$order_option['product_option_id']] = $order_option['value'];
						} elseif ($order_option['type'] == 'file') {
							$option_data[$order_option['product_option_id']] = $this->encryption->encrypt($this->config->get('config_encryption'), $order_option['value']);
						}
					}

					$this->cart->add($order_product_info['product_id'], $order_product_info['quantity'], $option_data);

					$this->session->data['success'] = sprintf($this->language->get('text_success'), $this->url->link('product/product', 'product_id=' . $product_info['product_id']), $product_info['name'], $this->url->link('checkout/cart'));

					unset($this->session->data['shipping_method']);
					unset($this->session->data['shipping_methods']);
					unset($this->session->data['payment_method']);
					unset($this->session->data['payment_methods']);
				} else {
					$this->session->data['error'] = sprintf($this->language->get('error_reorder'), $order_product_info['name']);
				}
			}
		}

		$this->response->redirect($this->url->link('account/order/info', 'order_id=' . $order_id));
	}
}
