<?php
class ControllerExtensionPurpletreeMultivendorSellercommission extends Controller{
		private $error = array();
		public function index(){
			if (!$this->customer->validateSeller()) {
				$this->load->language('purpletree_multivendor/ptsmultivendor');
				$this->session->data['error_warning'] = $this->language->get('error_license');
				
			}
			$this->load->language('purpletree_multivendor/sellercommission');
			$this->document->addStyle('view/javascript/purpletreecss/commonstylesheet.css');
			$this->load->model('extension/purpletree_multivendor/sellercommission');
			
			if (isset($this->request->get['filter_date_from'])) {
				$filter_date_from = $this->request->get['filter_date_from'];
				} else {
				$end_date = date('Y-m-d', strtotime("-30 days"));
				$filter_date_from = $end_date;
			}
			
			if (isset($this->request->get['filter_date_to'])) {
				$filter_date_to = $this->request->get['filter_date_to'];
				} else {
				$end_date = date('Y-m-d');
				$filter_date_to = $end_date;
			}
			
			if (isset($this->request->get['page'])) {
				$page = $this->request->get['page'];
				} else {
				$page = 1;
			}
			
			if (isset($this->request->get['seller_id'])) {
				$seller_id = $this->request->get['seller_id'];
				} else {
				$seller_id = 0;
			}
			
			$url = '';
			
			if (isset($this->request->get['filter_date_from'])) {
				$url .= '&filter_date_from=' . $this->request->get['filter_date_from'];
			}
			
			if (isset($this->request->get['filter_date_to'])) {
				$url .= '&filter_date_to=' . $this->request->get['filter_date_to'];
			}
			
			if (isset($this->request->get['seller_id'])) {
				$url .= '&seller_id=' . $this->request->get['seller_id'];
			}
			
			$data['breadcrumbs'] = array();
			
			$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
			);
			
			$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('extension/purpletree_multivendor/sellercommission', 'user_token=' . $this->session->data['user_token'] . $url, true)
			);
			$this->document->setTitle($this->language->get('heading_title'));
			
			$data['heading_title'] = $this->language->get('heading_title');
			$data['text_list'] = $this->language->get('text_list');
			$data['text_total_sale'] = $this->language->get('text_total_sale');
			$data['text_total_commission'] = $this->language->get('text_total_commission');
			$data['text_recvd_amt'] = $this->language->get('text_recvd_amt');
			$data['text_pending_amt'] = $this->language->get('text_pending_amt');
			$data['text_order_id'] = $this->language->get('text_order_id');
			$data['text_product_id'] = $this->language->get('text_product_id');
			$data['text_status'] = $this->language->get('text_status');
			$data['text_created_at'] = $this->language->get('text_created_at');
			/////commission invoice////
			$data['text_commission_percent'] = $this->language->get('text_commission_percent');
			$data['generate_invoice'] = $this->language->get('generate_invoice');
			$data['text_commission_fixed'] = $this->language->get('text_commission_fixed');
			$data['text_commission_shipping'] = $this->language->get('text_commission_shipping');	
			/////End commission invoice////
			$data['text_commission'] = $this->language->get('text_commission');		
			$data['text_product_price'] = $this->language->get('text_product_price');
			$data['text_no_results'] = $this->language->get('text_empty');
			$data['help_Invoice'] = $this->language->get('help_Invoice');
			$data['help_store'] = $this->language->get('help_store');
			
			$data['entry_date_from'] = $this->language->get('entry_date_from');
			$data['entry_date_to'] = $this->language->get('entry_date_to');
			
			$data['button_filter'] = $this->language->get('button_filter');
			$data['text_store_name'] = $this->language->get('text_store_name');
			
			//$url = '';
			
			if (isset($this->request->get['seller_id'])) {
				$url .= '&seller_id=' . $this->request->get['seller_id'];
			}
			
			$data['seller_commissions'] = array();
			$data['seller_id'] = (isset($this->request->get['seller_id'])?$this->request->get['seller_id']:'');
			$filter_data = array(
			'filter_date_from'    => $filter_date_from,
			'filter_date_to' => $filter_date_to,
			'start'                => ($page - 1) * $this->config->get('config_limit_admin'),
			'limit'                => $this->config->get('config_limit_admin'),
			'seller_id'				=> $seller_id,
			'order_status' => $this->config->get('module_purpletree_multivendor_commission_status'),
			);
			
			$total_sale = $this->model_extension_purpletree_multivendor_sellercommission->getTotalsale($filter_data);
			
			$seller_commissions1 = $this->model_extension_purpletree_multivendor_sellercommission->getCommissions($filter_data);
			$seller_commissions111 = $this->model_extension_purpletree_multivendor_sellercommission->getCommissions111($filter_data);
			$seller_commissions = array_merge($seller_commissions1,$seller_commissions111);
			$total_commissions = count($seller_commissions);
			$this->load->model('extension/purpletree_multivendor/sellerpayment');
			$curency = $this->config->get('config_currency');
			
			$currency_detail = $this->model_extension_purpletree_multivendor_sellerpayment->getCurrencySymbol($curency);
			$data['seller_commissions'] = array();		
			if($seller_commissions){
				foreach($seller_commissions as $seller_commission){
					//$seller_commissions2 = $this->model_extension_purpletree_multivendor_sellercommission->getCommissions11($seller_commission1['order_id']);
					//foreach($seller_commissions2 as $seller_commission){
						$data['seller_commissions'][] = array(
						'id' => $seller_commission['id'],
						'order_id' => $seller_commission['order_id'],
						'product_name' => $seller_commission['order_product_name'],					
						'invoice_status' => $seller_commission['invoice_status'],
						'store_name' => $seller_commission['store_name'],
						'store_url' => $this->url->link('extension/purpletree_multivendor/stores/edit&store_id='.$seller_commission['store_id'], 'user_token=' . $this->session->data['user_token'], true),
						'commission_fixed' =>$this->currency->format($seller_commission['commission_fixed'], $currency_detail['code'], $currency_detail['value']),
						'commission_percent' => $this->currency->format((($seller_commission['commission_percent']/100)*$seller_commission['total_price']), $currency_detail['code'], $currency_detail['value']),
						'commission_shipping' => $this->currency->format($seller_commission['commission_shipping'], $currency_detail['code'], $currency_detail['value']),
						'price' => $this->currency->format($seller_commission['total_price'], $currency_detail['code'], $currency_detail['value']),
						'commission' => $this->currency->format($seller_commission['commission'], $currency_detail['code'], $currency_detail['value']),
						'created_at' => date($this->language->get('date_format_short'), strtotime($seller_commission['created_at']))
						);
					//}
				}
			}	
			
			$data['user_token'] = $this->session->data['user_token'];
			
			if (isset($this->error['warning'])) {
				$data['error_warning'] = $this->error['warning'];
				}elseif (isset($this->session->data['error_warning'])) {
				$data['error_warning'] = $this->session->data['error_warning'];
				
				unset($this->session->data['error_warning']);
				} else {
				$data['error_warning'] = '';
			}
			
			if (isset($this->session->data['success'])) {
				$data['success'] = $this->session->data['success'];
				
				unset($this->session->data['success']);
				} else {
				$data['success'] = '';
			}
			
			$pagination = new Pagination();
			$pagination->total = $total_commissions;
			$pagination->page = $page;
			$pagination->limit = $this->config->get('config_limit_admin');
			$pagination->url = $this->url->link('extension/purpletree_multivendor/sellercommission', 'user_token=' . $this->session->data['user_token'] . $url . '&page={page}', true);
			
			$data['pagination'] = '';
			
			$data['results'] = '';
			
			$data['seller_stores'] = $this->model_extension_purpletree_multivendor_sellercommission->getSellerstore();
			
			$data['filter_date_from'] = $filter_date_from;
			$data['filter_date_to'] = $filter_date_to;
			$data['ver']=VERSION;
			$data['header'] = $this->load->controller('common/header');
			$data['column_left'] = $this->load->controller('common/column_left');
			$data['footer'] = $this->load->controller('common/footer');
			$data['commission_invoice'] = $this->url->link('extension/purpletree_multivendor/sellercommission/generate', 'user_token=' . $this->session->data['user_token'] . $url, true);
			$this->response->setOutput($this->load->view('extension/purpletree_multivendor/commission_list', $data));
		}
		public function generate(){
			if (!$this->customer->validateSeller()) {
				$this->load->language('purpletree_multivendor/ptsmultivendor');
				$this->session->data['error_warning'] = $this->language->get('error_license');
				$this->response->redirect($this->url->link('extension/purpletree_multivendor/sellercommission', 'user_token=' . $this->session->data['user_token'], true));
			}
			$this->load->language('purpletree_multivendor/sellercommission');
			
			$this->load->model('extension/purpletree_multivendor/sellercommission');
			$this->load->model('extension/purpletree_multivendor/commissioninvoice');
			if (isset($this->request->post['selected'])) {
				$commisionss = $this->request->post['selected'];
				
				try {
					$commisioninvoiceids = array();
					$so_id = array();
					$uniqueSoId=array();
					$total_price =array();
					$total_commission=array();
					if(!empty($commisionss)) {
						foreach ($commisionss as $commisionid => $order_id) {
							$commisionssss = $this->model_extension_purpletree_multivendor_sellercommission->getCommissionsforinvoide($commisionid);
							$so_id[] = array('seller_id'=> $commisionssss['seller_id'],
							'order_id'=> $commisionssss['order_id']
							);
							$total_commission[$commisionssss['seller_id']][]=$commisionssss['commission'];
						}
						$uniqueSoId=array_unique($so_id,SORT_REGULAR);
						foreach($uniqueSoId as $vvvv){
							$total_price[$vvvv['seller_id']][]= $this->model_extension_purpletree_multivendor_commissioninvoice->getCommissionTotal($vvvv['order_id'],$vvvv['seller_id']);
							
							$coupon_amount[$vvvv['seller_id']][]= $this->model_extension_purpletree_multivendor_commissioninvoice->getCouponAmount($vvvv['order_id'],$vvvv['seller_id']);
						}
						
						$t_comm=array();
						if(!empty($total_commission)){
							foreach($total_commission as $vk=>$vv){
								$t_commission=0;
								foreach($vv as $vkk=>$vvv){
									$t_commission+=$vvv;	
								}
								$t_comm[$vk]=$t_commission;	
							}
						}
						
						$t_total=array();
						if(!empty($total_price)){
							foreach($total_price as $vk1=>$vv1){
								$t_tot=0;
								foreach($vv1 as $vkk2=>$vvv2){
									$t_tot+=$vvv2;	
								}
								$t_total[$vk1]=$t_tot;	
							}
							
						}
						$coupon_total=array();
						if(!empty($coupon_amount)){
							foreach($coupon_amount as $vk1=>$vv1){
								$t_tot=0;
								foreach($vv1 as $vkk2=>$vvv2){
									$t_tot+=$vvv2;	
								}
								$coupon_total[$vk1]=$t_tot;	
							}
							
						}
						if(!empty($t_comm)){
							foreach($t_comm as $seller_idd=>$seller_commm){
								$total_price=$t_total[$seller_idd];
								$total_pay_amount=$total_price-$seller_commm;
								$coupon_amt=0;
								if(!empty($coupon_total[$seller_idd])){
								$coupon_amt=$coupon_total[$seller_idd];
								}
								$linkid[$seller_idd] = $this->model_extension_purpletree_multivendor_commissioninvoice->savelinkid($total_price,$seller_commm,$total_pay_amount,$coupon_amt);
							}
						}
						foreach ($commisionss as $commisionid => $order_id) {
							$commisionsss = $this->model_extension_purpletree_multivendor_sellercommission->getCommissionsforinvoide($commisionid);
							
							if(!empty($commisionsss)) {
								if($commisionsss['order_id'] == $order_id && $commisionsss['invoice_status'] == 0) {
									$linkidd=$linkid[$commisionsss['seller_id']];
									$this->model_extension_purpletree_multivendor_commissioninvoice->saveCommisionInvoice($commisionsss,$linkidd);
									
									$this->session->data['success'] = "#".$linkid[$commisionsss['seller_id']]." ".$this->language->get('success_message'); 
								}
							} 
						}
					}
					} catch (Exception $e) {
					$this->error['warning'] = $e->getMessage();
				}
				if(!empty($linkid)){
					$this->response->redirect($this->url->link('extension/purpletree_multivendor/commissioninvoice/commissionInvoice', 'user_token=' . $this->session->data['user_token'], true));
				}
			}
			$seller_id ='';
			if (isset($this->request->get['seller_id'])) {
				$seller_id = '&seller_id='.$this->request->get['seller_id'];
			}
			$this->response->redirect($this->url->link('extension/purpletree_multivendor/sellercommission', 'user_token=' . $this->session->data['user_token'].$seller_id , true));
			
		}
}
?>