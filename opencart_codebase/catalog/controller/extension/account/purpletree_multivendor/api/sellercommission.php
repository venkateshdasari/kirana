<?php
class ControllerExtensionAccountPurpletreeMultivendorApiSellercommission extends Controller{
		public function index(){
			$this->checkPlugin();
			$this->load->language('purpletree_multivendor/api');
			$json['status'] = 'error';
			$json['message'] = $this->language->get('no_data');
			if (!$this->customer->isMobileApiCall()) { 
				$json['status'] = 'error';
				$json['message'] = $this->language->get('error_permission');
				$this->response->addHeader('Content-Type: application/json');
				return $this->response->setOutput(json_encode($json));
			}
			if (!$this->customer->isLogged()) {
				$json['status'] = 'error';
				$json['message'] = $this->language->get('seller_not_logged');
				$this->response->addHeader('Content-Type: application/json');
				return $this->response->setOutput(json_encode($json));
			}
			
			$store_detail = $this->customer->isSeller();
			if(!isset($store_detail['store_status'])){
				$json['status'] = 'error';
				$json['message'] = $this->language->get('seller_not_approved');
				$this->response->addHeader('Content-Type: application/json');
				return $this->response->setOutput(json_encode($json)); 
			}
			if(!$this->customer->validateSeller()) {		
				$json['status'] = 'error';
				$json['message'] = $this->language->get('error_license');
				$this->response->addHeader('Content-Type: application/json');
				return $this->response->setOutput(json_encode($json));
			}
			$this->load->language('purpletree_multivendor/sellercommission');
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
			
			$seller_id = $this->customer->getId();
			$filter_data = array(
			'filter_date_from'    => $filter_date_from,
			'order_status' => $this->config->get('module_purpletree_multivendor_commission_status'),
			'filter_date_to' => $filter_date_to,
			'start'                => ($page - 1) * 4,
			'limit'                => 4,
			'seller_id'				=> $seller_id
			);
			
			
			$total_sale = $this->model_extension_purpletree_multivendor_sellercommission->getTotalsale($filter_data);
			
			$seller_commissions = $this->model_extension_purpletree_multivendor_sellercommission->getCommissions($filter_data);
			
			$total_commissions = $this->model_extension_purpletree_multivendor_sellercommission->getTotalCommissions($filter_data);
			
			$this->load->model('extension/purpletree_multivendor/sellerpayment');
			$curency = $this->config->get('config_currency');
			
			$currency_detail = $this->model_extension_purpletree_multivendor_sellerpayment->getCurrencySymbol($curency);
			$json['data']['seller_commissions'] = array();
			if(!empty($seller_commissions)){
				foreach($seller_commissions as $seller_commission){
					$json['data']['seller_commissions'][] = array(
					'order_id' => $seller_commission['order_id'],
					'product_name' => $seller_commission['name'],
					'price' => $this->currency->format($seller_commission['total_price'], $currency_detail['code'], $currency_detail['value']),
					'commission' => $this->currency->format($seller_commission['commission'], $currency_detail['code'], $currency_detail['value']),
					'created_at' => date($this->language->get('date_format_short'), strtotime($seller_commission['created_at']))
					);
				}
				$json['status'] = 'success';
				} else {
				$json['message'] = $this->language->get('no_data');
				$json['status'] = 'success';
			}
			//	$json['data']['pagination']['total'] = $total_commissions;
			//$json['data']['pagination']['page'] = $page;
			//$json['data']['pagination']['limit'] = $this->config->get('config_limit_admin');
			//$json['data']['results'] = sprintf($this->language->get('text_pagination'), ($total_commissions) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($total_commissions - $this->config->get('config_limit_admin'))) ? $total_commissions : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $total_commissions, ceil($total_commissions / $this->config->get('config_limit_admin')));
			
			$json['data']['filter_date_from'] = $filter_date_from;
			$json['data']['filter_date_to'] = $filter_date_to;
			$this->response->addHeader('Content-Type: application/json');
			return $this->response->setOutput(json_encode($json));
			
		}
		private function checkPlugin() {
			header('Access-Control-Allow-Origin:*');
			header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
			header('Access-Control-Max-Age: 286400');
			header('Access-Control-Allow-Credentials: true');
			header('Access-Control-Allow-Headers: languageid,LANGUAGEID,Languageid,purpletreemultivendor,Purpletreemultivendor,PURPLETREEMULTIVENDOR,xocmerchantid,XOCMERCHANTID,Xocmerchantid,XOCSESSION,xocsession,Xocsession,content-type,CONTENT-TYPE');
		}
}