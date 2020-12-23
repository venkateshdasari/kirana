<?php
class ControllerExtensionPaymentPPAdaptive extends Controller {
	public function index() {
	$this->log->write('init Purpletre pp Payout');
		$this->load->language('extension/payment/pp_adaptive');

		$data['text_testmode'] = $this->language->get('text_testmode');
		$data['button_confirm'] = $this->language->get('button_confirm');

		$data['testmode'] = $this->config->get('payment_pp_adaptive_test');

		if (!$this->config->get('payment_pp_adaptive_test')) {
			$data['action'] = 'https://www.paypal.com/cgi-bin/webscr&pal=V4T754QB63XXL';
		} else {
			$data['action'] = 'https://www.sandbox.paypal.com/cgi-bin/webscr&pal=V4T754QB63XXL';
		}

		$this->load->model('checkout/order');

		$order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);

		if ($order_info) {
			$data['business'] = $this->config->get('payment_pp_adaptive_email');
			$data['item_name'] = html_entity_decode($this->config->get('config_name'), ENT_QUOTES, 'UTF-8');

			$data['products'] = array();

			foreach ($this->cart->getProducts() as $product) {
				$option_data = array();

				foreach ($product['option'] as $option) {
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

				$data['products'][] = array(
					'name'     => htmlspecialchars($product['name']),
					'model'    => htmlspecialchars($product['model']),
					'price'    => $this->currency->format($product['price'], $order_info['currency_code'], false, false),
					'quantity' => $product['quantity'],
					'option'   => $option_data,
					'weight'   => $product['weight']
				);
			}

			$data['discount_amount_cart'] = 0;

			$total = $this->currency->format($order_info['total'] - $this->cart->getSubTotal(), $order_info['currency_code'], false, false);

			if ($total > 0) {
				$data['products'][] = array(
					'name'     => $this->language->get('text_total'),
					'model'    => '',
					'price'    => $total,
					'quantity' => 1,
					'option'   => array(),
					'weight'   => 0
				);
			} else {
				$data['discount_amount_cart'] -= $total;
			}

			$data['currency_code'] = $order_info['currency_code'];
			$data['first_name'] = $order_info['payment_firstname'];
			$data['last_name'] = $order_info['payment_lastname'];
			$data['address1'] = $order_info['payment_address_1'];
			$data['address2'] = $order_info['payment_address_2'];
			$data['city'] = $order_info['payment_city'];
			$data['zip'] = $order_info['payment_postcode'];
			$data['country'] = $order_info['payment_iso_code_2'];
			$data['email'] = $order_info['email'];
			$data['invoice'] = $this->session->data['order_id'] . ' - ' . $order_info['payment_firstname'] . ' ' . $order_info['payment_lastname'];
			$data['lc'] = $this->session->data['language'];
			$data['return'] = $this->url->link('checkout/success','payment=paypalpayout',true);
			$data['notify_url'] = $this->url->link('extension/payment/pp_adaptive/callback', '', true);
			$data['cancel_return'] = $this->url->link('checkout/checkout', '', true);

			if (!$this->config->get('payment_pp_adaptive_transaction')) {
				$data['paymentaction'] = 'authorization';
			} else {
				$data['paymentaction'] = 'sale';
			}

			$data['custom'] = $this->session->data['order_id'];

			return $this->load->view('extension/payment/pp_adaptive', $data);
		}
	}

	public function callback() {
	//$this->log->write('callback pp adaptive');
		if ($this->config->get('payment_pp_adaptive_debug')) {
	    $this->log->write('callback pp adaptive');
	     $this->log->write($this->request->post);
	    	}
		if (isset($this->request->post['custom'])) {
			$order_id = $this->request->post['custom'];
		} else {
			$order_id = 0;
		}

		$this->load->model('checkout/order');

		$order_info = $this->model_checkout_order->getOrder($order_id);
		//$this->log->write('callback order_info');
		if ($order_info) {
		//$this->log->write('callback if order_info');
			$request = 'cmd=_notify-validate';

			foreach ($this->request->post as $key => $value) {
				$request .= '&' . $key . '=' . urlencode(html_entity_decode($value, ENT_QUOTES, 'UTF-8'));
			}
			//$this->log->write('callback reqiest'.$request);
			if (!$this->config->get('payment_pp_adaptive_test')) {
				$curl = curl_init('https://www.paypal.com/cgi-bin/webscr');
			} else {
				$curl = curl_init('https://www.sandbox.paypal.com/cgi-bin/webscr');
			}

			curl_setopt($curl, CURLOPT_POST, true);
			curl_setopt($curl, CURLOPT_POSTFIELDS, $request);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($curl, CURLOPT_HEADER, false);
			curl_setopt($curl, CURLOPT_TIMEOUT, 30);
			curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

			$response = curl_exec($curl);
				//$this->log->write('callback response'.$response);
				//$this->log->write('callback response'.$this->request->post['payment_status']);
			if (!$response) {
				$this->log->write('PP_PAYOUT :: CURL failed ' . curl_error($curl) . '(' . curl_errno($curl) . ')');
			}

			curl_close($curl);
			if ($this->config->get('payment_pp_adaptive_debug')) {
				$this->log->write('PP_PAYOUT :: IPN REQUEST: ' . $request);
				$this->log->write('PP_PAYOUT :: IPN RESPONSE: ' . $response);
			}

			if ((strcmp($response, 'VERIFIED') == 0 || strcmp($response, 'UNVERIFIED') == 0) && isset($this->request->post['payment_status'])) {
				$order_status_id = $this->config->get('config_order_status_id');

				switch($this->request->post['payment_status']) {
					case 'Canceled_Reversal':
						$order_status_id = $this->config->get('payment_pp_adaptive_canceled_reversal_status_id');
						break;
					case 'Completed':
						$receiver_match = (strtolower($this->request->post['receiver_email']) == strtolower($this->config->get('payment_pp_adaptive_email')));

						$total_paid_match = ((float)$this->request->post['mc_gross'] == $this->currency->format($order_info['total'], $order_info['currency_code'], $order_info['currency_value'], false));

						if ($receiver_match && $total_paid_match) {
							$order_status_id = $this->config->get('payment_pp_adaptive_completed_status_id');
						}
						
						if (!$receiver_match) {
							$this->log->write('PP_PAYOUT :: RECEIVER EMAIL MISMATCH! ' . strtolower($this->request->post['receiver_email']));
						}
						
						if (!$total_paid_match) {
							$this->log->write('PP_PAYOUT :: TOTAL PAID MISMATCH! ' . $this->request->post['mc_gross']);
						}
						break;
					case 'Denied':
						$order_status_id = $this->config->get('payment_pp_adaptive_denied_status_id');
						break;
					case 'Expired':
						$order_status_id = $this->config->get('payment_pp_adaptive_expired_status_id');
						break;
					case 'Failed':
						$order_status_id = $this->config->get('payment_pp_adaptive_failed_status_id');
						break;
					case 'Pending':
						$order_status_id = $this->config->get('payment_pp_adaptive_pending_status_id');
						break;
					case 'Processed':
						$order_status_id = $this->config->get('payment_pp_adaptive_processed_status_id');
						break;
					case 'Refunded':
						$order_status_id = $this->config->get('payment_pp_adaptive_refunded_status_id');
						break;
					case 'Reversed':
						$order_status_id = $this->config->get('payment_pp_adaptive_reversed_status_id');
						break;
					case 'Voided':
						$order_status_id = $this->config->get('payment_pp_adaptive_voided_status_id');
						break;
				}
				if($order_status_id == $this->config->get('payment_pp_adaptive_completed_status_id')) {
				$this->model_checkout_order->addOrderHistory($order_id, $order_status_id);
					$this->load->model('extension/purpletree_multivendor/sellerorder');
				$sellersss = $this->model_extension_purpletree_multivendor_sellerorder->getOrderSeller($order_id);
				if(!empty($sellersss)) {
					if ($this->config->get('payment_pp_adaptive_debug')) {
				$this->log->write('sellersss');
				$this->log->write($sellersss);
					}
					foreach($sellersss as $sellerr_id1) {
						if ($this->config->get('payment_pp_adaptive_debug')) {
					$this->log->write('a');
					$this->log->write($sellerr_id1['seller_id']);
						}
						$comment = '';
						$this->model_extension_purpletree_multivendor_sellerorder->addOrderHistory($order_id,$sellerr_id1['seller_id'], $order_status_id, $comment,false,false);
							if ($this->config->get('payment_pp_adaptive_debug')) {
					$this->log->write('b');
							}
					//$this->log->write($sellerr_id1);
					}
					$this->model_checkout_order->addOrderHistory($order_id, $order_status_id);
				}
					$this->savetoDataBase($order_status_id,$order_id);
				}
			} else {
				$this->model_checkout_order->addOrderHistory($order_id, $this->config->get('config_order_status_id'));
			}

			
		}
	}
	public function getAccestoken() {
		 $tokenreturn = "";
		 $client_id   = $this->config->get('payment_pp_adaptive_client_id');
		 $secret      = $this->config->get('payment_pp_adaptive_admin_secret');
	 	if (!$this->config->get('payment_pp_adaptive_test')) {
				$curl = 'https://api.paypal.com/v1/oauth2/token';
			} else {
				$curl = 'https://api.sandbox.paypal.com/v1/oauth2/token';
			}
				$ch = curl_init();
				curl_setopt($ch, CURLOPT_URL, $curl);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
				curl_setopt($ch, CURLOPT_POST, 1);
				curl_setopt($ch, CURLOPT_POSTFIELDS, "grant_type=client_credentials");
				curl_setopt($ch, CURLOPT_USERPWD, $client_id . ':' . $secret);

				$headers = array();
				$headers[] = 'Accept: application/json';
				$headers[] = 'Accept-Language: en_US';
				$headers[] = 'Content-Type: application/x-www-form-urlencoded';
				curl_setopt($ch, CURLOPT_HTTPHEADER, $headers); 

				$result = curl_exec($ch);
				if (curl_errno($ch)) {
					if ($this->config->get('payment_pp_adaptive_debug')) {
						$this->log->write('Error on get Access Token: ' . curl_error($ch));
						//echo 'Error:' . curl_error($ch);
					}
				curl_close($ch);
				} else {
				$response = json_decode($result, true);
				curl_close($ch);
					if ($this->config->get('payment_pp_adaptive_debug')) {
						$this->log->write('Response with Access Token: ' . $result);
					}
					if(isset($response['access_token'])) {
						$tokenreturn = $response['access_token'];
					}
				}
				return $tokenreturn;
	}
	public function savetoDataBase($order_status_id ='',$order_id= "") {

				$access_token1 = $this->getAccestoken();
				$access_token = $access_token1;
				if($access_token != '') {
				$data_items = $this->getpayoutitemsdeatils($order_id);
					$baseurll = $this->config->get('config_url');
					$encodedurl = substr(base64_encode($baseurll),0,30);
					$ch1 = curl_init();
					$datttt = array( 
								"sender_batch_header" => array(
									"sender_batch_id" => $order_id."00".$encodedurl,
									"email_subject" => "You have a payout!",
									"email_message" => "You have received a payout! Thanks for using our service!"
									),
									"items" => $data_items				
								);
				$inputs = json_encode($datttt,true);
				if ($this->config->get('payment_pp_adaptive_debug')) {
							$this->log->write('sender batch data');
							$this->log->write($datttt);
						}
				if (!$this->config->get('payment_pp_adaptive_test')) {
					$curl1 = 'https://api.paypal.com/v1/payments/payouts';
				} else {
					$curl1 = 'https://api.sandbox.paypal.com/v1/payments/payouts';
				}
				curl_setopt($ch1, CURLOPT_URL, $curl1);
				curl_setopt($ch1, CURLOPT_RETURNTRANSFER, 1);
				curl_setopt($ch1, CURLOPT_POST, 1);
				curl_setopt($ch1, CURLOPT_POSTFIELDS,$inputs);

				$headers = array();
				$headers[] = 'Content-Type: application/json';
				$headers[] = 'Authorization: Bearer '.$access_token;
				curl_setopt($ch1, CURLOPT_HTTPHEADER, $headers);

				$result1 = curl_exec($ch1);
					if (curl_errno($ch1)) {
						echo 'Error:' . curl_error($ch1);
						if ($this->config->get('payment_pp_adaptive_debug')) {
							$this->log->write('Error on Payouts POST: ' . curl_error($ch1));
						}
					curl_close($ch1);
					} else {
					$response1 = json_decode($result1, true);
					curl_close($ch1);
					if ($this->config->get('payment_pp_adaptive_debug')) {
						$this->log->write('Response from Payouts POST: ' . $result1);
					}
					
					if(isset($response1['batch_header']['payout_batch_id']) &&  $response1['batch_header']['payout_batch_id'] != '') {
					$payout_batch_id = $response1['batch_header']['payout_batch_id'];
					$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "purpletree_vendor_adaptive_paykey where order_id = '".(int)$order_id."'"); 
						if($query->num_rows>0){
							$this->db->query("UPDATE " . DB_PREFIX . "purpletree_vendor_adaptive_paykey SET payment_key='".$payout_batch_id."' where order_id = '".(int)$order_id."'");
							} else {
							$this->db->query("INSERT INTO `" . DB_PREFIX . "purpletree_vendor_adaptive_paykey` SET order_id='".(int)$order_id."',payment_key='".$payout_batch_id."'");
						}
						$getpayoutss = $this->getpayoutInformation($payout_batch_id,$order_id,$access_token);
						if(!empty($getpayoutss)) {
						if($this->config->get('payment_pp_adaptive_debug')){
										$this->log->write('getpayoutss');
										$this->log->write($getpayoutss);
									}
						$invoicedata = array();
								$this->load->model('extension/purpletree_multivendor/sellerorder');
								foreach($getpayoutss as $keys=>$valuess){
									if($valuess['transaction_status'] != 'SUCCESS'){
										$status = 'Pending';
										$order_status_id1 = $this->config->get('payment_pp_adaptive_pending_status_id');
											$status_idd = 1;
									  //$order_status_id1 = $this->config->get('config_order_status_id');
									} else {
										$status = 'Complete';
										$order_status_id1 = $this->config->get('payment_pp_adaptive_completed_status_id');
											$status_idd = 2;
									}
									$output = implode(', ', array_map(
												function ($v, $k) {
													if(is_array($v)){
														return $k.'[]='.implode('&'.$k.'[]=', $v);
													}else{
														return $k.'='.$v;
													}
												}, 
												$valuess, 
												array_keys($valuess)
											));
								$messageseller = "Seller Payout Transaction ".$status." status: ".$output;	
									$total_commission_data = $this->model_extension_payment_pp_adaptive->totalCommission($valuess['seller_id'],$order_id);
									$total_price_data= $this->model_extension_payment_pp_adaptive->totalPrice($valuess['seller_id'],$order_id);
									
									$invoice_id = $this->model_extension_payment_pp_adaptive->savelinkid($total_price_data['total_price'],$total_commission_data['commission'],$valuess['amount']);
									if($invoice_id){
									$stats = 'Pending';
								if($valuess['transaction_status'] == 'SUCCESS') {
									$stats = 'Complete';
								}
										$invoicedata[$valuess['seller_id']]=$invoice_id;
										$transData=array(
										'invoice_id'		=> $invoice_id,
										'seller_id'			=> $valuess['seller_id'],
										'transaction_id'	=> ($valuess['transaction_id'] != '')?$valuess['transaction_id']:$valuess['transaction_status'] ,
										'amount'			=> $valuess['amount'],
										'payment_mode'		=> 'Online',
										'status'			=> $stats,
										'status_id'			=> $status_idd,
										'comment'			=> $messageseller,
										);
										$this->model_extension_payment_pp_adaptive->saveTranDetail($transData);
										$this->model_extension_payment_pp_adaptive->saveTranHistory($transData); 
									}
								//}
								//$this->model_extension_purpletree_multivendor_sellerorder->addOrderHistory($order_id,$valuess['seller_id'], $order_status_id1, $messageseller, false,false);
							}
							$commisionsss=array();
							$commisionsss= $this->model_extension_payment_pp_adaptive->getCommissionData($order_id);
								if ($this->config->get('payment_pp_adaptive_debug')) {
										$this->log->write('commisionsss');
										$this->log->write($commisionsss);
									}
							if(!empty($commisionsss)) {
								foreach($commisionsss as $keyes=>$commisionss){
									if((!isset($commisionss['invoice_status']) || $commisionss['invoice_status'] == 0) && isset($commisionss['seller_id'])) {
									if(isset($invoicedata[$commisionss['seller_id']])) {
										$linkid = $invoicedata[$commisionss['seller_id']];	
										$this->model_extension_payment_pp_adaptive->saveCommisionInvoice($commisionss,$linkid);
									}
									}
								}
							}
							
						}
					}
					}
				}
		
	}
	public function getpayoutInformation($payout_batch_id = "",$order_id = "",$access_token = "") {
	$ch2 = curl_init();
		if (!$this->config->get('payment_pp_adaptive_test')) {
					$curl1 = 'https://api.paypal.com/v1/payments/payouts/';
				} else {
					$curl1 = 'https://api.sandbox.paypal.com/v1/payments/payouts/';
				}
				curl_setopt($ch2, CURLOPT_URL, $curl1.$payout_batch_id);
				curl_setopt($ch2, CURLOPT_RETURNTRANSFER, 1);
				$headers = array();
				$headers[] = 'Content-Type: application/json';
				$headers[] = 'Authorization: Bearer '.$access_token;
				curl_setopt($ch2, CURLOPT_HTTPHEADER, $headers);
				$result1 = curl_exec($ch2);
				$responseData = array();
					if (curl_errno($ch2)) {
						//echo 'Error:' . curl_error($ch2);
						if ($this->config->get('payment_pp_adaptive_debug')) {
							$this->log->write('Error on GET Payouts: ' . curl_error($ch2));
						}
					curl_close($ch2);
					} else {
				$response1 = json_decode($result1, true);
					curl_close($ch2);
						if ($this->config->get('payment_pp_adaptive_debug')) {
							$this->log->write('Response check payout with id: ' . $result1);
						}
						if(isset($response1['batch_header']) && isset($response1['batch_header']['batch_status'])) {
							if(isset($response1['items'])) {
								foreach($response1['items'] as $item) {
									if(isset($item['payout_item']['recipient_type'])) {
										if($item['payout_item']['recipient_type'] == 'EMAIL') {
											if(isset($item['payout_item']['receiver'])) {
												 $recemail = $item['payout_item']['receiver'];
												// opencartdata
								$transaction_id='';
								$transaction_status='';
								if(isset($item['transaction_id'])){
									$transaction_id = $item['transaction_id'];
									//$item['payout_item_id'];
								}
								if(isset($item['transaction_status'])){
									$transaction_status=$item['transaction_status'];
								}
							if(isset($item['payout_item']['sender_item_id'])) {
								$sender_item_id = $item['payout_item']['sender_item_id'];
								$seller_id = explode('_',$sender_item_id);
								$responseData[] = array(
								'seller_id'					=> $seller_id[0],
								'transaction_id'			=> $transaction_id,
								'payout_item_id'			=> isset($item['payout_item_id'])?$item['payout_item_id']:'',
								'activity_id'				=> isset($item['activity_id'])?$item['activity_id']:'',
								'payout_batch_id'			=> isset($item['payout_batch_id'])?$item['payout_batch_id']:'',
								'transaction_status' 		=> $transaction_status,
								'email'						=> $recemail,
								'currency'					=> $item['payout_item']['amount']['currency'],
								'amount'					=> $item['payout_item']['amount']['value'],
								'sender_item_id'			=> $item['payout_item']['sender_item_id'],
								'recipient_wallet'			=> $item['payout_item']['recipient_wallet'],
								);
							}
										}
									}	
								}
							}
						}
					}
				}
					return $responseData;
	}
	public function getpayoutitemsdeatils($order_id = "") {
		$data_items = array();
	//if ( $this->config->get('module_purpletree_multivendor_commission_status')!= null) {
					$sellerorders = $this->db->query("SELECT * FROM `" . DB_PREFIX . "purpletree_vendor_orders` WHERE order_id = '" . (int)$order_id . "'");
					
					$shipcommsvirtial = '0';
					$dsdsds = array();
					$pay_admin_commission=0;
					$pay_seller_commission=array();
					$seller_payment	= 0;
					if(!empty($sellerorders->rows)) {
						foreach($sellerorders->rows as $sellerorder) {
							$sql1111 = "SELECT `store_commission` FROM `" . DB_PREFIX . "purpletree_vendor_stores` WHERE seller_id = '" . (int)$sellerorder['seller_id'] . "'";
							$totalshipingorder = '0';
							$getShippingOrderTotal = $this->db->query("SELECT `value` FROM `" . DB_PREFIX . "purpletree_order_total` WHERE order_id = '" . (int)$order_id . "' AND seller_id = '" . (int)$sellerorder['seller_id'] . "' AND code ='seller_shipping'");
							if($getShippingOrderTotal->num_rows){
								$totalshipingorder = $getShippingOrderTotal->row['value'];
							}
							
							$query = $this->db->query($sql1111);
						
							$seller_commission = $query->row;
							$productid = $sellerorder['product_id'];	
							$catids =$this->getProductCategory($productid );
							$commission_cat = array();
							$catttt = array();
							$shippingcommision = 0;
							
							if($totalshipingorder != 0) {
								if (null !== $this->config->get('module_purpletree_multivendor_shipping_commission')) {
									if(!array_key_exists($sellerorder['seller_id'],$dsdsds)) {
										$shippingcommision = (($this->config->get('module_purpletree_multivendor_shipping_commission')*$totalshipingorder)/100);
										$dsdsds[$sellerorder['seller_id']] = $shippingcommision;
									}
								}
							}
								
							if(!empty($catids)){
								foreach($catids as $cat) {
									$sql = "SELECT * FROM " . DB_PREFIX . "purpletree_vendor_categories_commission where category_id = '".(int)$cat['category_id']."'";
									$query = $this->db->query($sql);
								
									$commission_cat[] = $query->rows;
								}
								
							}
							$commission = -1;
							$commission1 = -1;
							$comipercen = 0;
							$comifixs = 0;
							
							if(!empty($commission_cat)) {
								foreach($commission_cat as $catts) {
									foreach($catts as $catt) {
										$comifix = 0;
										if(isset($catt['commison_fixed']) && $catt['commison_fixed'] != '') {
											$comifix = $catt['commison_fixed'];
										}
										$comiper = 0;
										if(isset($catt['commission']) && $catt['commission'] != '') {
											$comiper = $catt['commission'];
										}
										
										if (null !== $this->config->get('module_purpletree_multivendor_seller_group') && $this->config->get('module_purpletree_multivendor_seller_group') == 1) {
											$sqlgrop = "Select `customer_group_id` from `" . DB_PREFIX . "customer` where customer_id= ".$sellerorder['seller_id']." ";
											$querygrop = $this->db->query($sqlgrop);
											$sellergrp = $querygrop->row;
											if($catt['seller_group'] == $sellergrp['customer_group_id']) {
												$commipercent = (($comiper*$sellerorder['total_price'])/100);
												$commission1 = $comifix + $commipercent + $shippingcommision;
												if($commission1 > $commission) {
													$comipercen 		= $comiper;
													$comifixs 			= $comifix;
													$shippingcommision 	= $shippingcommision;
													$commission 		= $commission1;
												}
											}
											} else {
											$commipercent = (($comiper*$sellerorder['total_price'])/100);
											$commission1 = $comifix + $commipercent + $shippingcommision;
											if($commission1 > $commission) {
												$comipercen 		= $comiper;
												$comifixs 			= $comifix;
												$shippingcommision 	= $shippingcommision;
												$commission 		= $commission1;
											} 
										}
									}
								}
							}
							if($commission != -1) {
								$commission = $commission;
							}
							//category_commission
							elseif(isset($seller_commission['store_commission']) && ($seller_commission['store_commission'] != NULL || $seller_commission['store_commission'] != '')){
								$comipercen = $seller_commission['store_commission'];
								$commission = (($sellerorder['total_price']*$seller_commission['store_commission'])/100)+$shippingcommision;
							
								} else {
								$comipercen = $this->config->get('module_purpletree_multivendor_commission');
								$commission = (($sellerorder['total_price']*$this->config->get('module_purpletree_multivendor_commission'))/100)+$shippingcommision;
									
							}
								
							$seller_payment = $sellerorder['total_price']+$totalshipingorder-$commission;
							if(!isset($pay_seller_commission[$sellerorder['seller_id']])){
								$pay_seller_commission[$sellerorder['seller_id']] = $seller_payment ;
								} else {
								$pay_seller_commission[$sellerorder['seller_id']] += $seller_payment;
							} 
							
						}
					}
				
				$this->load->model('extension/payment/pp_adaptive');
				$this->load->model('account/order');
				$receiverList	= array();
				$seller_detail	= array();
				if(!empty($pay_seller_commission)) {
				foreach($pay_seller_commission as $pts_seller_id=>$pts_amount){
					$seller_detail=$this->model_extension_payment_pp_adaptive->getSellerDetail($pts_seller_id);
				//pts adaptive
				$baseurll = $this->config->get('config_url');
				$getCurrency = $this->model_extension_payment_pp_adaptive->getorderCurrency($order_id);
				$encodedurl = substr(base64_encode($baseurll),0,30);
				$data_items[] = array(
									  "recipient_type" => "EMAIL",
									  "amount" => array(
										"value" => $pts_amount,
										"currency" => isset($getCurrency) ? $getCurrency : "USD"
									  ),
									  "note" => "Thanks for your patronage!",
									  "sender_item_id" => $pts_seller_id."_".$order_id."_".$encodedurl,
									  "receiver" => $seller_detail['seller_paypal_id']
									);
				}
				}
				return $data_items;
	}

		public function getProductCategory($productid){
			
			$sql = "SELECT category_id FROM " . DB_PREFIX . "product_to_category where 	product_id = '".(int)$productid."'"; 
			
			$query = $this->db->query($sql);
			
			return $query->rows;  
		}
}
?>