<?php
class ControllerExtensionAccountPurpletreeMultivendorSellerstore extends Controller{
	private $error = array();
	
	public function index(){
		if (!$this->customer->isLogged()) {
			$this->session->data['redirect'] = $this->url->link('extension/account/purpletree_multivendor/sellerstore', '', true);
			$this->response->redirect($this->url->link('account/login', '', true));
		}
		
		$store_detail = $this->customer->isSeller();
		if(empty($store_detail)){
			$this->response->redirect($this->url->link('account/account', '', true));
		}else{
			        if($store_detail['multi_store_id'] != $this->config->get('config_store_id')){	
						$this->response->redirect($this->url->link('account/account','', true));
				    }
		        }
		if(!$this->customer->validateSeller()) {
			$this->load->language('purpletree_multivendor/ptsmultivendor');
			$this->session->data['error_warning'] = $this->language->get('error_license');
			$this->response->redirect($this->url->link('account/account', '', true));
		}
				
		
		$this->load->language('purpletree_multivendor/sellerstore');
		
		$this->document->setTitle($this->language->get('heading_title'));

		if(VERSION=='3.1.0.0_b'){
			$this->document->addScriptpts('catalog/view/javascript/purpletree_style_bs4.js');	
		}else{
			$this->document->addScriptpts('catalog/view/javascript/purpletree_style.js');
		}
		
		$this->load->model('extension/purpletree_multivendor/vendor');
		
		$store_detail = $this->customer->isSeller();
		
		$store_id = (isset($store_detail['id'])?$store_detail['id']:'');		
		  
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
		    //seller area //			
			if((isset($this->request->post['seller_area_selection_type'])) && ($this->request->post['seller_area_selection_type'] == 1)){
			     if(!empty($this->request->post['seller_area'])){
			     $this->request->post['seller_area']   = serialize($this->request->post['seller_area']);
			     }
				}else{
				 if(isset($this->request->post['seller_area_selection_type'])){
				 $this->request->post['seller_area'] = 0;
				 }
				}
				
		    //seller area //
			$path = 'admin/ptsseller/';
			$file = "";
					if (!is_dir($path)) {
						@mkdir($path, 0777);
					}
					if(is_dir($path)){
                        
                        $allowed_file=array('gif','png','jpg','pdf','doc','docx','zip');
                        $filename = basename(preg_replace('/[^a-zA-Z0-9\.\-\s+]/', '', html_entity_decode($_FILES['upload_file']['name'], ENT_QUOTES, 'UTF-8')));
                    $extension = pathinfo($filename, PATHINFO_EXTENSION);
                    if($filename != '') {
                        if(in_array($extension,$allowed_file) ) {
                            $file = md5(mt_rand()).'-'.$filename;
                            $directory  = $path;
                        
                            move_uploaded_file($_FILES['upload_file']['tmp_name'], $directory.'/'.$file);
                        }     
                    }
                   
                                
                    }
			if(isset($this->request->post['store_video'])){	
				$video_url = $this->generateVideoEmbedUrl($this->request->post['store_video']);			
				$this->request->post['store_video'] = $video_url;
			}
			if($this->request->post['vacation']==1){		
				$productstts = $this->model_extension_purpletree_multivendor_vendor->getSellerProduct($this->customer->getId());
				if($productstts){
				foreach ($productstts as $productstt) {
					$this->model_extension_purpletree_multivendor_vendor->updateVacationProduct($productstt['product_id'],$productstt['status'],$this->customer->getId());
					
					$this->model_extension_purpletree_multivendor_vendor->updateProductAccrVacation($productstt['product_id']);
				}
				}
			}else{
				$productsttss = $this->model_extension_purpletree_multivendor_vendor->getSellerProductBystatus($this->customer->getId());
				if($productsttss){
				foreach ($productsttss as $productstts) {
					$this->model_extension_purpletree_multivendor_vendor->updateProductAccrVacationn($productstts['product_id']);
				}
				}
				$this->model_extension_purpletree_multivendor_vendor->updateVacationProductByOff($this->customer->getId());
			}
			$this->model_extension_purpletree_multivendor_vendor->editStore($store_id, $this->request->post,$file);
            ///vacation
				$this->model_extension_purpletree_multivendor_vendor->storeTime($store_id, $this->request->post);
				$this->model_extension_purpletree_multivendor_vendor->addHoliday($store_id, $this->request->post);
			///vacation
			$this->session->data['success'] = $this->language->get('text_success');
			
			$this->response->redirect($this->url->link('extension/account/purpletree_multivendor/sellerstore','',true));
		}
		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/home','',true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_store'),
			'href' => $this->url->link('extension/account/purpletree_multivendor/sellerstore', '', true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_edit'),
			'href' => $this->url->link('extension/account/purpletree_multivendor/sellerstore', '', true)
		);

		$data['heading_title'] = $this->language->get('heading_title');
		$data['module_purpletree_multivendor_allow_live_chat'] = 0;
		if(NULL !== $this->config->get('module_purpletree_multivendor_allow_live_chat')) {
			$data['module_purpletree_multivendor_allow_live_chat'] = $this->config->get('module_purpletree_multivendor_allow_live_chat');
		}
		/// vacation
		$data['text_store_opening'] = $this->language->get('text_store_opening');
		$data['text_open'] = $this->language->get('text_open');
		$data['text_close'] = $this->language->get('text_close');
		$data['text_sunday'] = $this->language->get('text_sunday');
		$data['text_monday'] = $this->language->get('text_monday');
		$data['text_tuesday'] = $this->language->get('text_tuesday');
		$data['text_wednesday'] = $this->language->get('text_wednesday');
		$data['text_thursday'] = $this->language->get('text_thursday');
		$data['text_friday'] = $this->language->get('text_friday');
		$data['text_saturday'] = $this->language->get('text_saturday');
		$data['text_holiday'] = $this->language->get('text_holiday');
		$data['text_date'] = $this->language->get('text_date');
		$data['text_action'] = $this->language->get('text_action');
		/// vacation
		$data['entry_allow_live_chat'] = $this->language->get('entry_allow_live_chat');
		$data['entry_live_chat_code'] = $this->language->get('entry_live_chat_code');
		$data['text_list'] = $this->language->get('text_list');
		$data['text_select'] = $this->language->get('text_select');
		$data['text_none'] = $this->language->get('text_none');
		$data['text_enabled'] = $this->language->get('text_enabled');
		$data['text_disabled'] = $this->language->get('text_disabled');
		$data['text_yes'] = $this->language->get('text_yes');
		$data['text_no'] = $this->language->get('text_no');
		$data['text_default'] = $this->language->get('text_default');
		$data['text_no_results'] = $this->language->get('text_no_results');
		$data['text_confirm'] = $this->language->get('text_confirm');
		$data['text_no_file'] = $this->language->get('text_no_file');

		$data['entry_storename'] = $this->language->get('entry_storename');
		$data['entry_storeemail'] = $this->language->get('entry_storeemail');
		$data['entry_storephone'] = $this->language->get('entry_storephone');
		$data['entry_storelogo'] = $this->language->get('entry_storelogo');
		$data['entry_storebanner'] = $this->language->get('entry_storebanner');
		if($this->config->get('module_purpletree_multivendor_storepage_layout')){
			$data['entry_storebanner_desc'] = $this->language->get('entry_storebanner_desc2');
		}else{
			$data['entry_storebanner_desc'] = $this->language->get('entry_storebanner_desc');
		}
		$data['entry_storestatus'] = $this->language->get('entry_storestatus');
		$data['entry_storeaddress'] = $this->language->get('entry_storeaddress');
		$data['entry_storecity'] = $this->language->get('entry_storecity');
		$data['entry_storepostcode'] = $this->language->get('entry_storepostcode');
		$data['entry_storecountry'] = $this->language->get('entry_storecountry');
		$data['entry_storezone'] = $this->language->get('entry_storezone');
		$data['entry_storedescription'] = $this->language->get('entry_storedescription');
		$data['entry_storeshippingpolicy'] = $this->language->get('entry_storeshippingpolicy');
		$data['entry_storereturn'] = $this->language->get('entry_storereturn');
		$data['entry_storemetakeyword'] = $this->language->get('entry_storemetakeyword');
		$data['entry_storemetadescription'] = $this->language->get('entry_storemetadescription');
		$data['entry_storebankdetail'] = $this->language->get('entry_storebankdetail');
		$data['entry_storetin'] = $this->language->get('entry_storetin');
		$data['entry_name'] = $this->language->get('entry_name');
		$data['entry_storestatus'] = $this->language->get('entry_storestatus');
		$data['entry_storeshipping'] = $this->language->get('entry_storeshipping');
		$data['entry_storeshipping_charge'] = $this->language->get('entry_storeshipping_charge');
		$data['entry_storeshipping_type'] = $this->language->get('entry_storeshipping_type');
        $data['entry_order_wise'] = $this->language->get('entry_order_wise');
		$data['entry_product_wise'] = $this->language->get('entry_product_wise');
		$data['entry_seller_paypal_id'] = $this->language->get('entry_seller_paypal_id');//paypal
		$data['help_paypal'] = $this->language->get('help_paypal');
		
		$data['entry_storeseo'] = $this->language->get('entry_storeseo');
		
		$data['button_continue'] = $this->language->get('button_save');
		$data['google_map_link'] = $this->language->get('google_map_link');
		$data['button_back'] = $this->language->get('button_back');
		$data['storepage_layout'] = $this->config->get('module_purpletree_multivendor_storepage_layout');

		if (isset($store_id)) {
			$data['store_id'] = $store_id;
		} else {
			$data['store_id'] = 0;
		}
		
		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}
		
		if (isset($this->session->data['success'])) {
			$data['success'] = $this->session->data['success'];

			unset($this->session->data['success']);
		} else {
			$data['success'] = '';
		}
		
		if (isset($this->error['store_name'])) {
			$data['error_storename'] = $this->error['store_name'];
		} else {
			$data['error_storename'] = '';
		}
		
		if (isset($this->error['store_seo'])) {
			$data['error_storeseo'] = $this->error['store_seo'];
		} else {
			$data['error_storeseo'] = '';
		}
		if (isset($this->error['error_file_upload'])) {
			$data['error_file_upload'] = $this->error['error_file_upload'];
		} else {
			$data['error_file_upload'] = '';
		}
		
		/* if (isset($this->error['store_email'])) {
			$data['error_storeemail'] = $this->error['store_email'];
		} else {
			$data['error_storeemail'] = '';
		} */
		
		if (isset($this->error['seller_paypal_id'])) {
			$data['error_seller_paypal_id'] = $this->error['seller_paypal_id'];
		} else {
			$data['error_seller_paypal_id'] = '';
		}
		
		if (isset($this->error['store_phone'])) {
			$data['error_storephone'] = $this->error['store_phone'];
		} else {
			$data['error_storephone'] = '';
		}
				
		if (isset($this->error['store_address'])) {
			$data['error_storeaddress'] = $this->error['store_address'];
		} else {
			$data['error_storeaddress'] = '';
		}
		
		if (isset($this->error['store_city'])) {
			$data['error_storecity'] = $this->error['store_city'];
		} else {
			$data['error_storecity'] = '';
		}
		
		if (isset($this->error['store_country'])) {
			$data['error_storecountry'] = $this->error['store_country'];
		} else {
			$data['error_storecountry'] = '';
		}
		
		if (isset($this->error['error_storezone'])) {
			$data['error_storezone'] = $this->error['error_storezone'];
		} else {
			$data['error_storezone'] = '';
		}
		
		if (isset($this->error['store_zipcode'])) {
			$data['error_storezipcode'] = $this->error['store_zipcode'];
		} else {
			$data['error_storezipcode'] = '';
		}
		
		if (isset($this->error['store_shipping'])) {
			$data['error_storeshipping'] = $this->error['store_shipping'];
		} else {
			$data['error_storeshipping'] = '';
		}
		
		if (isset($this->error['store_return'])) {
			$data['error_storereturn'] = $this->error['store_return'];
		} else {
			$data['error_storereturn'] = '';
		}
		
		if (isset($this->error['store_meta_keywords'])) {
			$data['error_storemetakeyword'] = $this->error['store_meta_keywords'];
		} else {
			$data['error_storemetakeyword'] = '';
		}
		
		if (isset($this->error['store_meta_description'])) {
			$data['error_storemetadescription'] = $this->error['store_meta_description'];
		} else {
			$data['error_storemetadescription'] = '';
		}
		
		if (isset($this->error['store_bank_details'])) {
			$data['error_storebankdetail'] = $this->error['store_bank_details'];
		} else {
			$data['error_storebankdetail'] = '';
		}
		
		if (isset($this->error['store_tin'])) {
			$data['error_storetin'] = $this->error['store_tin'];
		} else {
			$data['error_storetin'] = '';
		}
		
		if (isset($this->error['store_shipping_charge'])) {
			$data['error_storecharge'] = $this->error['store_shipping_charge'];
		} else {
			$data['error_storecharge'] = '';
		}

		$data['action'] = $this->url->link('extension/account/purpletree_multivendor/sellerstore', '', true);

		if (isset($store_id) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
			$seller_info = $this->model_extension_purpletree_multivendor_vendor->getStore($store_id);
			$seller_info_social = $this->model_extension_purpletree_multivendor_vendor->getStoreSocial($store_id);
			/// vacation
			$store_time = array();
			$store_holiday = array();
			 $store_time = $this->model_extension_purpletree_multivendor_vendor->getStoreTime($store_id);
			 $store_holiday = $this->model_extension_purpletree_multivendor_vendor->getStoreHoliday($store_id);
			/// vacation
		}
		/// vacation
			$data['store_holiday'] = array();
			if(!empty($store_holiday)){
			foreach ($store_holiday as $key => $value){
			$data['store_holiday'][] = array(
						'id' => $value['id'],
						'store_id' => $value['store_id'],
						'date' => $value['date']				
						);
			}
			}
			if(!empty($store_time)){
			foreach ($store_time as $key => $value) {
			if($value['day_id'] == 1){
			     $data['sunday_open'] = $value['open_time'];
				 $data['sunday_close'] = $value['close_time'];
				}
			if($value['day_id'] == 2){
				 $data['monday_open'] = $value['open_time'];
				 $data['monday_close'] = $value['close_time'];
				}
			if($value['day_id'] == 3){
				   $data['tuesday_open'] = $value['open_time'];
				   $data['tuesday_close'] = $value['close_time'];
				}
			if($value['day_id'] == 4){
				   $data['wednesday_open'] = $value['open_time'];
				   $data['wednesday_close'] = $value['close_time'];
				}
			if($value['day_id'] == 5){
				   $data['thursday_open'] = $value['open_time'];
				   $data['thursday_close'] = $value['close_time'];
				}
			if($value['day_id'] == 6){
				   $data['friday_open'] = $value['open_time'];
				   $data['friday_close'] = $value['close_time'];
				}
			if($value['day_id'] == 7){
				   $data['saturday_open'] = $value['open_time'];
				   $data['saturday_close'] = $value['close_time'];
				 }
				}
			}
			 if(isset($this->request->post['store_timing'][1]['open'])){
			     $data['sunday_open'] = $this->request->post['store_timing'][1]['open'];
			}elseif(!empty($store_time)){
			     $data['sunday_open'] = $data['sunday_open'];
			}else{
			     $data['sunday_open'] = '';
			}
			if(isset($this->request->post['store_timing'][1]['close'])){
			     $data['sunday_close'] = $this->request->post['store_timing'][1]['close'];
			}elseif(!empty($store_time)){
			     $data['sunday_close'] = $data['sunday_close'];
			}else{
			     $data['sunday_close'] = '';
			}	 
			
			if(isset($this->request->post['store_timing'][2]['open'])){
			     $data['monday_open'] = $this->request->post['store_timing'][2]['open'];
			}elseif(!empty($store_time)){
			      $data['monday_open'] =  $data['monday_open'];
			}else{
			      $data['monday_open'] = '';
			}
			if(isset($this->request->post['store_timing'][2]['close'])){
			     $data['monday_close'] = $this->request->post['store_timing'][2]['close'];
			}elseif(!empty($store_time)){
			     $data['monday_close'] = $data['monday_close'];
			}else{
			     $data['monday_close'] = '';
			}	
				   
			if(isset($this->request->post['store_timing'][3]['open'])){
			     $data['tuesday_open'] = $this->request->post['store_timing'][3]['open'];
			}elseif(!empty($store_time)){
			      $data['tuesday_open'] =  $data['tuesday_open'];
			}else{
			      $data['tuesday_open'] = '';
			}
			if(isset($this->request->post['store_timing'][3]['close'])){
			     $data['tuesday_close'] = $this->request->post['store_timing'][3]['close'];
			}elseif(!empty($store_time)){
			     $data['tuesday_close'] = $data['tuesday_close'];
			}else{
			     $data['tuesday_close'] = '';
			}	
			
			if(isset($this->request->post['store_timing'][4]['open'])){
			     $data['wednesday_open'] = $this->request->post['store_timing'][4]['open'];
			}elseif(!empty($store_time)){
			      $data['wednesday_open'] =  $data['wednesday_open'];
			}else{
			      $data['wednesday_open'] = '';
			}
			if(isset($this->request->post['store_timing'][4]['close'])){
			     $data['wednesday_close'] = $this->request->post['store_timing'][4]['close'];
			}elseif(!empty($store_time)){
			     $data['wednesday_close'] = $data['wednesday_close'];
			}else{
			     $data['wednesday_close'] = '';
			}	
				   
			if(isset($this->request->post['store_timing'][5]['open'])){
			     $data['thursday_open'] = $this->request->post['store_timing'][5]['open'];
			}elseif(!empty($store_time)){
			      $data['thursday_open'] =  $data['thursday_open'];
			}else{
			      $data['thursday_open'] = '';
			}
			if(isset($this->request->post['store_timing'][5]['close'])){
			     $data['thursday_close'] = $this->request->post['store_timing'][5]['close'];
			}elseif(!empty($store_time)){
			     $data['thursday_close'] = $data['thursday_close'];
			}else{
			     $data['thursday_close'] = '';
			}	
			
			if(isset($this->request->post['store_timing'][6]['open'])){
			     $data['friday_open'] = $this->request->post['store_timing'][6]['open'];
			}elseif(!empty($store_time)){
			      $data['friday_open'] =  $data['friday_open'];
			}else{
			      $data['friday_open'] = '';
			}
			if(isset($this->request->post['store_timing'][6]['close'])){
			     $data['friday_close'] = $this->request->post['store_timing'][6]['close'];
			}elseif(!empty($store_time)){
			     $data['friday_close'] = $data['friday_close'];
			}else{
			     $data['friday_close'] = '';
			}	
				 
			if(isset($this->request->post['store_timing'][7]['open'])){
			     $data['saturday_open'] = $this->request->post['store_timing'][7]['open'];
			}elseif(!empty($store_time)){
			      $data['saturday_open'] =  $data['saturday_open'];
			}else{
			      $data['saturday_open'] = '';
			}
			if(isset($this->request->post['store_timing'][7]['close'])){
			     $data['saturday_close'] = $this->request->post['store_timing'][7]['close'];
			}elseif(!empty($store_time)){
			     $data['saturday_close'] = $data['saturday_close'];
			}else{
			     $data['saturday_close'] = '';
			}	
			
			/// vacation
		
		if (!empty($seller_info)) {
			$data['seller_id'] = $seller_info['seller_id'];
		} else {
			$data['seller_id'] = $this->customer->getId();
		}
		
		if (isset($this->request->post['store_video'])) { 
			$data['store_video'] = $this->request->post['store_video'];
		} elseif (!empty($seller_info)) { 
			$data['store_video'] = $seller_info['store_video'];
		} else { 
			$data['store_video'] = '';
		}		
		if (isset($this->request->post['seller_name'])) { 
			$data['seller_name'] = $this->request->post['seller_name'];
		} elseif (!empty($seller_info)) { 
			$data['seller_name'] = $seller_info['seller_name'];
		} else { 
			$data['seller_name'] = '';
		}
				if (isset($this->request->post['store_live_chat_enable'])) { 
			$data['store_live_chat_enable'] = $this->request->post['store_live_chat_enable'];
		} elseif (!empty($seller_info)) { 
			$data['store_live_chat_enable'] = $seller_info['store_live_chat_enable'];
		} else { 
			$data['store_live_chat_enable'] = 0;
		}
		if (isset($this->request->post['store_live_chat_code'])) { 
			$data['store_live_chat_code'] = $this->request->post['store_live_chat_code'];
		} elseif (!empty($seller_info)) { 
			$data['store_live_chat_code'] = $seller_info['store_live_chat_code'];	
		} else { 
			$data['store_live_chat_code'] = '';
		}
		if (isset($this->request->post['store_seo'])) { 
			$data['store_seo'] = $this->request->post['store_seo'];
		} elseif (!empty($seller_info) && isset($seller_info['store_seo'])) { 
			$data['store_seo'] = $seller_info['store_seo'];
		} else { 
			$data['store_seo'] = '';
		}
		
		if (isset($this->request->post['store_name'])) {
			$data['store_name'] = $this->request->post['store_name'];
		} elseif (!empty($seller_info)) {
			$data['store_name'] = $seller_info['store_name'];
		} else {
			$data['store_name'] = '';
		}
		

		/* if (isset($this->request->post['store_email'])) {
			$data['store_email'] = $this->request->post['store_email'];
		} elseif (!empty($seller_info)) {
			$data['store_email'] = $seller_info['store_email'];
		} else {
			$data['store_email'] = '';
		} */
		
		if (isset($this->request->post['store_phone'])) {
			$data['store_phone'] = $this->request->post['store_phone'];
		} elseif (!empty($seller_info)) {
			$data['store_phone'] = $seller_info['store_phone'];
		} else {
			$data['store_phone'] = '';
		}
		
		if (isset($this->request->post['store_description'])) {
			$data['store_description'] = $this->request->post['store_description'];
		} elseif (!empty($seller_info)) {
			$data['store_description'] = $seller_info['store_description'];
		} else {
			$data['store_description'] = '';
		}		
		if (isset($this->request->post['store_timings'])) {
			$data['store_timings'] = $this->request->post['store_timings'];
		} elseif (!empty($seller_info)) {
			$data['store_timings'] = $seller_info['store_timings'];
		} else {
			$data['store_timings'] = '';
		}		
		if (isset($this->request->post['google_map'])) {
			$data['google_map'] = $this->request->post['google_map'];
		} elseif (!empty($seller_info)) {
			$data['google_map'] = $seller_info['google_map'];
		} else {
			$data['google_map'] = '';
		}
		if (isset($this->request->post['google_map_link'])) {
			$data['google_map_link'] = $this->request->post['google_map_link'];
		} elseif (!empty($seller_info)) {
			$data['google_map_link'] = $seller_info['google_map_link'];
		} else {
			$data['google_map_link'] = '';
		}
		
		if (isset($this->request->post['store_address'])) {
			$data['store_address'] = $this->request->post['store_address'];
		} elseif (!empty($seller_info)) {
			$data['store_address'] = $seller_info['store_address'];
		} else {
			$data['store_address'] = '';
		}
		
		
		if (isset($this->request->post['store_country'])) {
			$data['store_country'] = $this->request->post['store_country'];
		} elseif (!empty($seller_info)) {
			$data['store_country'] = $seller_info['store_country'];
		} else {
			$data['store_country'] = '';
		}
		
		if (isset($this->request->post['store_state'])) {
			$data['store_state'] = $this->request->post['store_state'];
		} elseif (!empty($seller_info)) {
			$data['store_state'] = $seller_info['store_state'];
		} else {
			$data['store_state'] = '';
		}
		
		if (isset($this->request->post['store_city'])) {
			$data['store_city'] = $this->request->post['store_city'];
		} elseif (!empty($seller_info)) {
			$data['store_city'] = $seller_info['store_city'];
		} else {
			$data['store_city'] = '';
		}
		
		if (isset($this->request->post['store_zipcode'])) {
			$data['store_zipcode'] = $this->request->post['store_zipcode'];
		} elseif (!empty($seller_info)) {
			$data['store_zipcode'] = $seller_info['store_zipcode'];
		} else {
			$data['store_zipcode'] = '';
		}
				// seller area
			if (isset($this->request->post['seller_area'])) {			    
			    if(isset($this->request->post['seller_area_selection_type']) &&($this->request->post['seller_area_selection_type'] == 1)){
				$data['seller_area_selection_type'] = 1;
				$sellerareas = Unserialize($this->request->post['seller_area']);
				}else{
				 $sellerareas = array();
				$data['seller_area_selection_type'] = 0;
				}
				} elseif (!empty($seller_info)) {
				if(!empty($seller_info['store_area'])){				
				$sellerareas = Unserialize($seller_info['store_area']);
				$data['seller_area_selection_type'] = 1;
				}else{				
				 $sellerareas = array();
				 $data['seller_area_selection_type'] = 0;
				}
				} else {
				$sellerareas = array();
				$data['seller_area_selection_type'] = 0;
			}			
			$data['sellerareas'] = array();
			if(!empty($sellerareas)) {
			foreach ($sellerareas as $area_id) {
			$area_info = $this->model_extension_purpletree_multivendor_vendor->getSellerAreaByID($area_id);

			if ($area_info) {
				$data['sellerareas'][] = array(
					'area_id' => $area_info['area_id'],
					'name'        => $area_info['name']
				);
			}
		}
		}
	    // seller area	
		if (isset($this->request->post['store_shipping_policy'])) {
			$data['store_shipping_policy'] = $this->request->post['store_shipping_policy'];
		} elseif (!empty($seller_info)) {
			$data['store_shipping_policy'] = $seller_info['store_shipping_policy'];
		} else {
			$data['store_shipping_policy'] = '';
		}
		
		if (isset($this->request->post['store_return_policy'])) {
			$data['store_return_policy'] = $this->request->post['store_return_policy'];
		} elseif (!empty($seller_info)) {
			$data['store_return_policy'] = $seller_info['store_return_policy'];
		} else {
			$data['store_return_policy'] = '';
		}
		
		if (isset($this->request->post['store_meta_keywords'])) {
			$data['store_meta_keywords'] = $this->request->post['store_meta_keywords'];
		} elseif (!empty($seller_info)) {
			$data['store_meta_keywords'] = $seller_info['store_meta_keywords'];
		} else {
			$data['store_meta_keywords'] = '';
		}
		
		if (isset($this->request->post['store_meta_description'])) {
			$data['store_meta_description'] = $this->request->post['store_meta_description'];
		} elseif (!empty($seller_info)) {
			$data['store_meta_description'] = $seller_info['store_meta_descriptions'];
		} else {
			$data['store_meta_description'] = '';
		}
		
		if (isset($this->request->post['store_bank_details'])) {
			$data['store_bank_details'] = $this->request->post['store_bank_details'];
		} elseif (!empty($seller_info)) {
			$data['store_bank_details'] = $seller_info['store_bank_details'];
		} else {
			$data['store_bank_details'] = '';
		}
		
		if (isset($this->request->post['store_tin'])) {
			$data['store_tin'] = $this->request->post['store_tin'];
		} elseif (!empty($seller_info)) {
			$data['store_tin'] = $seller_info['store_tin'];
		} else {
			$data['store_tin'] = '';
		}
		
		if (isset($this->request->post['store_shipping_type'])) {
			$data['store_shipping_type'] = $this->request->post['store_shipping_type'];
		} elseif (!empty($seller_info) && isset($seller_info['store_shipping_type'])) {
			$data['store_shipping_type'] = $seller_info['store_shipping_type'];
		} else {
			$data['store_shipping_type'] = 'pts_flat_rate_shipping';
		}	
       if (isset($this->request->post['store_shipping_order_type'])) {
			$data['store_shipping_order_type'] = $this->request->post['store_shipping_order_type'];
		} elseif (!empty($seller_info) && isset($seller_info['store_shipping_order_type'])) {
			$data['store_shipping_order_type'] = $seller_info['store_shipping_order_type'];
		} else {
			$data['store_shipping_order_type'] = 'pts_product_wise';
		}				
		
		if (isset($this->request->post['store_shipping_charge'])) {
			$data['store_shipping_charge'] = $this->request->post['store_shipping_charge'];
		} elseif (!empty($seller_info) && isset($seller_info['store_shipping_charge'])) {
			$data['store_shipping_charge'] = $seller_info['store_shipping_charge'];
		} else {
			$data['store_shipping_charge'] = '';
		}
		
		if (isset($this->request->post['store_status'])) {
			$data['store_status'] = $this->request->post['store_status'];
		} elseif (!empty($seller_info)) {
			$data['store_status'] = $seller_info['store_status'];
		} else {
			$data['store_status'] = '';
		}
			//paypal
		if (isset($this->request->post['seller_paypal_id'])) {
			$data['seller_paypal_id'] = $this->request->post['seller_paypal_id'];
		} elseif (!empty($seller_info)&& isset($seller_info['seller_paypal_id'])) { 
			$data['seller_paypal_id'] = $seller_info['seller_paypal_id'];
		} else {
			$data['seller_paypal_id'] = '';
		}			
		//vacation
		if (isset($this->request->post['vacation'])) {
			$data['vacation'] = $this->request->post['vacation'];
		} elseif (isset($seller_info['vacation'])) { 
			$data['vacation'] = $seller_info['vacation'];
		} else {
			$data['vacation'] = '';
		}		
		//paypal
				
		if (isset($this->request->post['store_logo'])) {
			$data['store_logo'] = $this->request->post['store_logo'];
		} elseif (!empty($seller_info)) {
			$data['store_logo'] = $seller_info['store_logo'];
		} else {
			$data['store_logo'] = '';
		}
		
		//social Links
		if (isset($this->request->post['facebook_link'])) {
			$data['facebook_link'] = $this->request->post['facebook_link'];
		} elseif (!empty($seller_info_social) && isset($seller_info_social['facebook_link'])) {
			$data['facebook_link'] = $seller_info_social['facebook_link'];
		} else {
			$data['facebook_link'] = '';
		}		
		if (isset($this->request->post['google_link'])) {
			$data['google_link'] = $this->request->post['google_link'];
		} elseif (!empty($seller_info_social) && isset($seller_info_social['google_link'])) {
			$data['google_link'] = $seller_info_social['google_link'];
		} else {
			$data['google_link'] = '';
		}		
		if (isset($this->request->post['twitter_link'])) {
			$data['twitter_link'] = $this->request->post['twitter_link'];
		} elseif (!empty($seller_info_social) && isset($seller_info_social['twitter_link'])) {
			$data['twitter_link'] = $seller_info_social['twitter_link'];
		} else {
			$data['twitter_link'] = '';
		}		
		if (isset($this->request->post['instagram_link'])) {
			$data['instagram_link'] = $this->request->post['instagram_link'];
		} elseif (!empty($seller_info_social) && isset($seller_info_social['instagram_link'])) {
			$data['instagram_link'] = $seller_info_social['instagram_link'];
		} else {
			$data['instagram_link'] = '';
		}
		if (isset($this->request->post['pinterest_link'])) {
			$data['pinterest_link'] = $this->request->post['pinterest_link'];
		} elseif (!empty($seller_info_social) && isset($seller_info_social['pinterest_link'])) {
			$data['pinterest_link'] = $seller_info_social['pinterest_link'];
		} else {
			$data['pinterest_link'] = '';
		}		
		if (isset($this->request->post['wesbsite_link'])) {
			$data['wesbsite_link'] = $this->request->post['wesbsite_link'];
		} elseif (!empty($seller_info_social) && isset($seller_info_social['wesbsite_link'])) {
			$data['wesbsite_link'] = $seller_info_social['wesbsite_link'];
		} else {
			$data['wesbsite_link'] = '';
		}
		if (isset($this->request->post['whatsapp_link'])) {
			$data['whatsapp_link'] = $this->request->post['whatsapp_link'];
		} elseif (!empty($seller_info_social) && isset($seller_info_social['whatsapp_link'])) {
			$data['whatsapp_link'] = $seller_info_social['whatsapp_link'];
		} else {
			$data['whatsapp_link'] = '';
		}
		$results = $this->model_extension_purpletree_multivendor_vendor->getAssingedCategories();

		if(!empty($results)){
			foreach ($results as $result) {
				$data['allow_category'][strip_tags(html_entity_decode($result['name'], ENT_QUOTES, 'UTF-8'))] = $result['category_id'];
			}
		}
		if($this->config->get('module_purpletree_multivendor_allow_selleron_category') === 'service_mode'){	
			$data['check_category_bar'] = '1';
		}
		//End Social links


		$this->load->model('tool/image');

		if (isset($this->request->post['store_logo']) && is_file(DIR_IMAGE . $this->request->post['store_logo'])) {
			$data['thumb'] = $this->model_tool_image->resize($this->request->post['store_logo'], 100, 100);
		} elseif (!empty($seller_info) && is_file(DIR_IMAGE . $seller_info['store_logo'])) {
			$data['thumb'] = $this->model_tool_image->resize($seller_info['store_logo'], 100, 100);
		} else {
			$data['thumb'] = $this->model_tool_image->resize('no_image.png', 100, 100);
		}
		
		if (isset($this->request->post['store_banner'])) {
			$data['store_banner'] = $this->request->post['store_banner'];
		} elseif (!empty($seller_info)) {
			$data['store_banner'] = $seller_info['store_banner'];
		} else {
			$data['store_banner'] = '';
		}		
		if (isset($this->request->post['store_image'])) {
			$data['store_image'] = $this->request->post['store_image'];
		} elseif (!empty($seller_info)) {
			$data['store_image'] = $seller_info['store_image'];
		} else {
			$data['store_image'] = '';
		}

		$this->load->model('tool/image');

		if (isset($this->request->post['store_banner']) && is_file(DIR_IMAGE . $this->request->post['store_banner'])) {
			$data['banner_thumb'] = $this->model_tool_image->resize($this->request->post['store_banner'], 100, 100);
		} elseif (!empty($seller_info) && is_file(DIR_IMAGE . $seller_info['store_banner'])) {
			$data['banner_thumb'] = $this->model_tool_image->resize($seller_info['store_banner'], 100, 100);
		} else {
			$data['banner_thumb'] = $this->model_tool_image->resize('catalog/purpletree_banner.jpg', 100, 100);
		}		
		if (isset($this->request->post['store_image']) && is_file(DIR_IMAGE . $this->request->post['store_image'])) {
			$data['image_thumb'] = $this->model_tool_image->resize($this->request->post['store_image'], 100, 100);
		} elseif (!empty($seller_info) && is_file(DIR_IMAGE . $seller_info['store_image'])) {
			$data['image_thumb'] = $this->model_tool_image->resize($seller_info['store_image'], 100, 100);
		} else {
			$data['image_thumb'] = $this->model_tool_image->resize('catalog/purpletree_banner.jpg', 100, 100);
		}
		
		$data['placeholder'] = $this->model_tool_image->resize('no_image.png', 100, 100);
		
		$this->load->model('localisation/country');

		$data['countries'] = $this->model_localisation_country->getCountries();
		
		$data['back'] = $this->url->link('account/account', '', true);

		// Start download document file of store
						//$data['upload_file'] = $seller_info['document'];
		/**Start Document upload of became seller **/ 
		if(!empty($seller_info['document'])){
				$data['upload_file_existing'] = $seller_info['document'];
				$data['upload_file_existing_href'] = "admin/ptsseller/".$seller_info['document'];
			}      
                    
		//
			
			/* if(! empty($seller_info['document'])){
				$data['download']=array(
				'name'=> $this->language->get('text_downloads'),
				'href'=> 'admin/ptsseller/'. $seller_info['document']
			);
			} else {
				$data['download']=array(
				'name'=> $this->language->get('text_no_file'),
				'href'=> "#"
			);  
			} */
			
		// End download document file of store       
        $data['ver']=VERSION;
		if($data['ver']=='3.1.0.0_b'){
			 $this->document->addScriptpts('admin/view/javascript/ckeditor/ckeditor.js');
		     $this->document->addScriptpts('admin/view/javascript/ckeditor/adapters/jquery.js');
		}
 $this->document->addStylepts('catalog/view/javascript/purpletree/codemirror/lib/codemirror.css'); 
 $this->document->addStylepts('catalog/view/javascript/purpletree/codemirror/theme/monokai.csss'); 
 $this->document->addScriptpts('catalog/view/javascript/purpletree/codemirror/lib/codemirror.js'); 
 $this->document->addScriptpts('catalog/view/javascript/purpletree/codemirror/lib/xml.js'); 
 $this->document->addScriptpts('catalog/view/javascript/purpletree/codemirror/lib/formatting.js'); 
   if($data['ver'] == '3.1.0.0_b') { } else {

 $this->document->addScriptpts('catalog/view/javascript/purpletree/summernote/summernote.js'); 
 $this->document->addStylepts('catalog/view/javascript/purpletree/summernote/summernote.css'); 
 $this->document->addScriptpts('catalog/view/javascript/purpletree/summernote/summernote-image-attributes.js'); 
 $this->document->addScriptpts('catalog/view/javascript/purpletree/summernote/opencart.js'); 
   }
		// Custom Fields

		$data['custom_fields'] = array();

		$filter_data = array(
			'sort'  => 'cf.sort_order',
			'order' => 'ASC'
		);

		$custom_fields = $this->model_extension_purpletree_multivendor_vendor->getCustomFieldsForSeller($filter_data);

		foreach ($custom_fields as $custom_field) {
			$data['custom_fields'][] = array(
				'custom_field_id'    => $custom_field['custom_field_id'],
				'custom_field_value' => $this->model_extension_purpletree_multivendor_vendor->getCustomFieldValues($custom_field['custom_field_id']),
				'name'               => $custom_field['name'],
				'value'              => $custom_field['value'],
				'type'               => $custom_field['type'],
				'location'           => $custom_field['location'],
				'sort_order'         => $custom_field['sort_order']
			);			
		}
		$customer_info = $this->model_extension_purpletree_multivendor_vendor->getCustomer($this->customer->getId());
		if (isset($this->request->post['custom_field'])) {
			$data['account_custom_field'] = $this->request->post['custom_field'];
		} elseif (!empty($customer_info)) {
			$data['account_custom_field'] = json_decode($customer_info['custom_field'], true);
		} else {
			$data['account_custom_field'] = array();
		}
		$this->load->model('tool/upload');
		if(!empty($data['custom_fields'])) {
			foreach ($data['custom_fields'] as $custom_field) {
			if(isset($custom_field['type']) && isset($custom_field['custom_field_id']) &&$custom_field['type'] == 'file' && isset($data['account_custom_field'][$custom_field['custom_field_id']])){ 
			$code = $data['account_custom_field'][$custom_field['custom_field_id']];
				$file =  $this->model_tool_upload->getUploadByCode($code);
				if(!empty($file) && isset($file['name']) && isset($file['filename'])) {
		if ($this->request->server['HTTPS']) {
			$baseurl = $this->config->get('config_ssl') . 'upload/';
		} else {
			$baseurl = $this->config->get('config_url') . 'upload/';
		}
		$data['account_custom_field'][$custom_field['custom_field_id']] = array(
			'file' => $file['name'],
			'value' => $data['account_custom_field'][$custom_field['custom_field_id']],
			'url' => $this->getrealpath($file['filename'],$file['name'])
		);
				}
			}
			}
		}

		$this->document->addScriptpts('catalog/view/javascript/purpletree/jquery/datetimepicker/moment/moment.min.js'); 
		$this->document->addScriptpts('catalog/view/javascript/purpletree/jquery/datetimepicker/moment/moment-with-locales.min.js'); 
		$this->document->addScriptpts('catalog/view/javascript/purpletree/jquery/datetimepicker/bootstrap-datetimepicker.min.js'); 
		$this->document->addStylepts('catalog/view/javascript/purpletree/jquery/datetimepicker/bootstrap-datetimepicker.min.css'); 
		$data['column_left'] = $this->load->controller('extension/account/purpletree_multivendor/common/column_left');
		$data['footer'] = $this->load->controller('extension/account/purpletree_multivendor/common/footer');
		$data['header'] = $this->load->controller('extension/account/purpletree_multivendor/common/header');
 			
		
		$this->response->setOutput($this->load->view('account/purpletree_multivendor/seller_store', $data));
	}

	public function downloadAttachment()
	{
		
		$file="ptsseller/".$this->request->get["document"]; //file location 
		
        if(file_exists($file)) {

        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename='.basename($file));
        header('Content-Transfer-Encoding: binary');
        header('Expires: 0');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Pragma: public');
		
        header('Content-Length: ' . filesize($file));
        ob_clean();
        flush();
        readfile($file);
        exit();
	}
	}	
	
	public function becomeseller(){
		if (!$this->customer->isLogged()) {
			$this->session->data['redirect'] = $this->url->link('account/purpletree_multivendor/becomeseller', '', true);

			$this->response->redirect($this->url->link('account/login', '', true));
		}		
		$this->load->language('purpletree_multivendor/sellerstore');
		
		$this->document->setTitle($this->language->get('heading_become_title'));
		
		$this->load->model('extension/purpletree_multivendor/vendor');
		
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateSeller()) {

			$file ='';
			$store_id = $this->model_extension_purpletree_multivendor_vendor->becomeSeller($this->customer->getId(), $this->request->post,$file);
			////// Start register mail for seller////////////
		
			$this->load->language('mail/register');
		    $this->load->language('account/ptsregister');
			$data['text_welcome'] = sprintf($this->language->get('text_welcome'), html_entity_decode($this->config->get('config_name'), ENT_QUOTES, 'UTF-8'));
			$data['text_login'] = $this->language->get('text_login');
			$data['text_approval'] = $this->language->get('text_approval');
			$data['text_service'] = $this->language->get('text_service');
			$data['text_thanks'] = $this->language->get('text_thanks');
			  $this->load->model('account/customer'); 
               $this->load->model('account/customer_group');
				$datacust = $this->model_account_customer->getCustomer($this->customer->getId());
			if (isset($datacust['customer_group_id'])) {
				$customer_group_id = $datacust['customer_group_id'];
			} else {
				$customer_group_id = $this->config->get('config_customer_group_id');
			}
			$data['text_admin'] ="";
			if($this->config->get('module_purpletree_multivendor_seller_approval') == 1){
				$data['text_admin'] = $this->language->get('text_admin');
			}
						
			$customer_group_info = $this->model_account_customer_group->getCustomerGroup($customer_group_id);
			
			if ($customer_group_info) {
				$data['approval'] = $customer_group_info['approval'];
			} else {
				$data['approval'] = '';
			}
				
			$data['login'] = $this->url->link('account/login', '', true);		
			$data['store'] = html_entity_decode($this->config->get('config_name'), ENT_QUOTES, 'UTF-8');

			$mail = new Mail($this->config->get('config_mail_engine'));
			$mail->parameter = $this->config->get('config_mail_parameter');
			$mail->smtp_hostname = $this->config->get('config_mail_smtp_hostname');
			$mail->smtp_username = $this->config->get('config_mail_smtp_username');
			$mail->smtp_password = html_entity_decode($this->config->get('config_mail_smtp_password'), ENT_QUOTES, 'UTF-8');
			$mail->smtp_port = $this->config->get('config_mail_smtp_port');
			$mail->smtp_timeout = $this->config->get('config_mail_smtp_timeout');

			$mail->setTo($datacust['email']);
			$mail->setFrom($this->config->get('config_email'));
			$mail->setSender(html_entity_decode($this->config->get('config_name'), ENT_QUOTES, 'UTF-8'));
			$mail->setSubject(sprintf($this->language->get('text_subject_seller'), html_entity_decode($this->config->get('config_name'), ENT_QUOTES, 'UTF-8')));
			$mail->setText($this->load->view('account/purpletree_multivendor/register_mail', $data));
			$mail->send();
		
		//////End register mail for seller////////////
		// Send to main admin email if new account email is enabled
		if (in_array('account', (array)$this->config->get('config_mail_alert'))) {

			$this->load->language('mail/register');
		    /////// Start alert mail for admin///////////
			
			$this->load->language('account/ptsregister');
			
			$data['text_signup_seller'] = $this->language->get('text_signup_seller');
			$data['text_firstname'] = $this->language->get('text_firstname');
			$data['text_lastname'] = $this->language->get('text_lastname');
			$data['text_customer_group'] = $this->language->get('text_customer_group');
			$data['text_email'] = $this->language->get('text_email');
			$data['text_telephone'] = $this->language->get('text_telephone');
			
			$data['firstname'] = $datacust['firstname'];
			$data['lastname'] = $datacust['lastname'];
			
			$this->load->model('account/customer_group');
			
			if (isset($datacust['customer_group_id'])) {
				$customer_group_id = $datacust['customer_group_id'];
			} else {
				$customer_group_id = $this->config->get('config_customer_group_id');
			}
			
			$customer_group_info = $this->model_account_customer_group->getCustomerGroup($customer_group_id);
			
			if ($customer_group_info) {
				$data['customer_group'] = $customer_group_info['name'];
			} else {
				$data['customer_group'] = '';
			}
			
			$data['email'] = $datacust['email'];
			$data['telephone'] = $datacust['telephone'];

			$mail = new Mail($this->config->get('config_mail_engine'));
			$mail->parameter = $this->config->get('config_mail_parameter');
			$mail->smtp_hostname = $this->config->get('config_mail_smtp_hostname');
			$mail->smtp_username = $this->config->get('config_mail_smtp_username');
			$mail->smtp_password = html_entity_decode($this->config->get('config_mail_smtp_password'), ENT_QUOTES, 'UTF-8');
			$mail->smtp_port = $this->config->get('config_mail_smtp_port');
			$mail->smtp_timeout = $this->config->get('config_mail_smtp_timeout');

			$mail->setTo($this->config->get('config_email'));
			$mail->setFrom($this->config->get('config_email'));
			$mail->setSender(html_entity_decode($this->config->get('config_name'), ENT_QUOTES, 'UTF-8'));
			$mail->setSubject(html_entity_decode($this->language->get('text_new_Seller'), ENT_QUOTES, 'UTF-8'));
			$mail->setText($this->load->view('account/purpletree_multivendor/register_alertmail', $data));
			$mail->send();

			// Send to additional alert emails if new account email is enabled
			$emails1 = explode(',', $this->config->get('config_mail_alert_email'));

			foreach ($emails1 as $email1) {
				if (utf8_strlen($email1) > 0 && filter_var($email1, FILTER_VALIDATE_EMAIL)) {
					$mail->setTo($email1);
					$mail->send();
				}
			}
			}
		  /////// End alert mail for admin///////////
			if($store_id){
				if($this->config->get('module_purpletree_multivendor_seller_approval')){
					$this->session->data['success'] = $this->language->get('text_approval');
					$this->response->redirect($this->url->link('account/account','',true));
				} else {
					$this->session->data['success'] = $this->language->get('text_seller_success');
					$this->response->redirect($this->url->link('account/account','',true));
				}
			} else {
				$this->response->redirect($this->url->link('account/account','',true));
			}
		}
		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/home','',true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_store'),
			'href' => $this->url->link('extension/account/purpletree_multivendor/sellerstore', '', true)
		);
		$data['text_supported'] = $this->language->get('text_supported');
		$data['text_attachment'] = $this->language->get('text_attachment');
		$data['heading_title'] = $this->language->get('heading_title');
		$data['text_seller'] = $this->language->get('text_seller');
		$data['text_seller_heading'] = $this->language->get('text_seller_heading');
		$data['text_store_name'] = $this->language->get('text_store_name');
		$data['text_yes'] = $this->language->get('text_yes');
		$data['text_no'] = $this->language->get('text_no');
		$data['text_remove_msg'] = $this->language->get('text_remove_msg');
		
		$data['button_continue'] = $this->language->get('button_continue');
		$data['button_back'] = $this->language->get('button_back');
		
		if (isset($this->request->post['become_seller'])) {
			$data['become_seller'] = $this->request->post['become_seller'];
		} 
		 else {
			$data['become_seller'] = '';
		}
		
		if (isset($this->request->post['seller_storename'])) {
			$data['seller_storename'] = $this->request->post['seller_storename'];
		} 
		 else {
			$data['seller_storename'] = '';
		}
		if (isset($this->error['seller_store'])) {
			$data['error_sellerstore'] = $this->error['seller_store'];
		} else {
			$data['error_sellerstore'] = '';
		}
		
		if (isset($this->error['error_warning'])) {
			$data['error_warning'] = $this->error['error_warning'];
		} else {
			$data['error_warning'] = '';
		}
		
		if(isset($this->error['warning1']))
		{
			
				$data['warning1'] = $this->error['warning1'];
		}
		else{
			
			$data['warning1']= '';
		}
		$isSeller = $this->customer->isSeller();
				$data['text_approval'] = $this->language->get('text_approval');
		if($isSeller){
				$data['isSeller'] = $isSeller;
			if($isSeller['is_removed']){
				$data['action'] = $this->url->link('extension/account/purpletree_multivendor/sellerstore/reseller', '', true);
				$data['is_removed'] = 1;
			} elseif($isSeller['store_status'] == 1) {
						$this->response->redirect($this->url->link('extension/account/purpletree_multivendor/dashboardicons','',true));
			}
		} else {
			//$data['action'] = $this->url->link('extension/account/purpletree_multivendor/sellerstore/becomeseller', '', true);
			//$data['is_removed'] = 0;
			$this->response->redirect($this->url->link('extension/account/purpletree_multivendor/sellerregister','',true));
		}
		
		if (isset($this->request->post['become_seller'])) {
			$data['become_seller'] = $this->request->post['become_seller'];
		} else {
			$data['become_seller'] = '';
		}
		
		if (isset($this->request->post['store_name'])) {
			$data['store_name'] = $this->request->post['store_name'];
		} else {
			$data['store_name'] = '';
		}

		$direction = $this->language->get('direction');
		 if ($direction=='rtl'){
			$this->document->addStyle('catalog/view/javascript/purpletree/bootstrap/css/bootstrap.min-a.css');
			$this->document->addStyle('catalog/view/theme/default/stylesheet/purpletree/custom-a.css'); 
			}else{
			$this->document->addStyle('catalog/view/javascript/purpletree/bootstrap/css/bootstrap.min.css'); 
			$this->document->addStyle('catalog/view/theme/default/stylesheet/purpletree/custom.css'); 
			}
			$data['column_left'] = $this->load->controller('extension/account/purpletree_multivendor/common/column_left');
		$data['footer'] = $this->load->controller('extension/account/purpletree_multivendor/common/footer');
		$data['header'] = $this->load->controller('extension/account/purpletree_multivendor/common/header');

		$this->response->setOutput($this->load->view('account/purpletree_multivendor/seller_form', $data));
	}
	
	public function reseller(){
		
		$this->load->language('purpletree_multivendor/sellerstore');
		
		$this->load->model('extension/purpletree_multivendor/vendor');
		
		if (($this->request->server['REQUEST_METHOD'] == 'POST')) {
			
			$store_id = $this->model_extension_purpletree_multivendor_vendor->reseller($this->customer->getId(), $this->request->post);
			if($store_id){
				if($this->config->get('module_purpletree_multivendor_seller_approval')){
					$this->session->data['success'] = $this->language->get('text_approval');
					$this->response->redirect($this->url->link('account/account','',true));		
				} else {
					$this->session->data['success'] = $this->language->get('text_seller_success');
					$this->response->redirect($this->url->link('extension/account/purpletree_multivendor/sellerstore','',true));
				}
			} else {
				$this->response->redirect($this->url->link('account/account','',true));
			}
		}
		
	}
	public function storeview(){
		/* $store_detail = $this->customer->isSeller();
		if(!isset($store_detail['store_status'])){
			$this->response->redirect($this->url->link('account/account', '', true));
		}else{
			        if(isset($store_detail['store_status']) && $store_detail[  'multi_store_id'] != $this->config->get('config_store_id')){	
						$this->response->redirect($this->url->link('account/account','', true));
				    }
		        } */
				$this->load->language('purpletree_multivendor/sellerstore');
		$labelss ='';
	$this->load->model('setting/extension');
    $installed_modules = $this->model_setting_extension->getExtensions('module');

			if(isset($this->session->data['seller_sto_page'])) {
				unset($this->session->data['seller_sto_page']);
			}
			$data['error_warning'] = '';
		$this->load->model('extension/purpletree_multivendor/vendor');
			$seller_Store = $this->model_extension_purpletree_multivendor_vendor->getStore($this->request->get['seller_store_id']);
			// hyper local 
			$current_area = '';
			$seller_area_find = 0;
			$assign_area = array();
			if($this->config->get('module_purpletree_multivendor_hyperlocal_status')){
			if((isset($this->session->data['seller_area'])&& ($this->session->data['seller_area'] > 0))){
			$current_area = $this->session->data['seller_area'];
			if($seller_Store['store_area'] != ''){
			$assign_area = unserialize($seller_Store['store_area']);
			if(in_array($current_area,$assign_area)){
			     $seller_area_find = 1;
				}
			}
			}
			}
			// end hyper local 
		if(isset($seller_Store['seller_id'])){
			 if(isset($seller_Store['store_status']) && $seller_Store[  'multi_store_id'] != $this->config->get('config_store_id')){	
						$this->response->redirect($this->url->link('common/home','', true));
			 }
		}
		if($this->config->get('module_purpletree_multivendor_subscription_plans')){
		if(isset($seller_Store['seller_id'])){
		if(!$this->subscription($seller_Store['seller_id'])){
			//$data['error_warning'] = "Seller is not Subscribed to any Plan or Subscription Expired";
			}
		}
		}
		$this->load->language('purpletree_multivendor/storeview');
		
		$this->document->setTitle($this->language->get('heading_title'));
		$this->load->model('extension/purpletree_multivendor/vendor');
		
		$this->load->model('extension/module/purpletree_sellerprice');
		
		$this->load->model('extension/purpletree_multivendor/sellerproduct');
			if(array_search('journal2', array_column($installed_modules, 'code')) !== False) {
		
		 $this->load->model('journal2/product');
	}
		if (isset($this->request->get['filter'])) {
			$filter = $this->request->get['filter'];
		} else {
			$filter = '';
		}

		if (isset($this->request->get['sort'])) {
			$sort = $this->request->get['sort'];
		} else {
			$sort = 'p.sort_order';
		}

		if (isset($this->request->get['order'])) {
			$order = $this->request->get['order'];
		} else {
			$order = 'ASC';
		}

		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}
		
		if (isset($this->request->get['limit'])) {
			$limit = (int)$this->request->get['limit'];
		} else {
			$limit = $this->config->get('theme_' . $this->config->get('config_theme') . '_product_limit');
		}
		
		$category_id=0;
		if(!empty($this->request->get['category']))
		{
			$category_id=$this->request->get['category'];
		}
		
		$data['seller_products'] = array();
		
		$data['toatl_seller_products'] = array();
		
		$data['text_compare'] = sprintf($this->language->get('text_compare'), (isset($this->session->data['compare']) ? count($this->session->data['compare']) : 0));
		$data['text_sort'] = $this->language->get('text_sort');
		$data['text_limit'] = $this->language->get('text_limit');
		
		$data['heading_title'] = $this->language->get('heading_title');
		
		$data['text_returnpolicy'] = $this->language->get('text_returnpolicy');
		$data['text_shippingpolicy'] = $this->language->get('text_shippingpolicy');
		$data['button_cart'] = $this->language->get('button_cart');
		$data['button_wishlist'] = $this->language->get('button_wishlist');
		$data['button_compare'] = $this->language->get('button_compare');
		$data['button_continue'] = $this->language->get('button_continue');
		$data['button_list'] = $this->language->get('button_list');
		$data['button_grid'] = $this->language->get('button_grid');
		$data['text_aboutstore'] = $this->language->get('text_aboutstore');
		$data['text_sellerreview'] = $this->language->get('text_sellerreview');
		$data['text_no_results'] = $this->language->get('text_empty');
		if($this->config->get('module_purpletree_multivendor_allow_selleron_category') === 'service_mode'){	
			$data['text_no_results'] = '';
			$data['check_category_bar'] = '1';
		}
		$data['text_sellercontact'] = $this->language->get('text_sellercontact');
		$data['text_all'] = $this->language->get('text_all');
		$data['module_purpletree_multivendor_seller_name'] = $this->config->get('module_purpletree_multivendor_seller_name');
		
		$data['button_cart'] = $this->language->get('button_cart');
		$data['button_wishlist'] = $this->language->get('button_wishlist');
		$data['button_compare'] = $this->language->get('button_compare');
		$sellerstore='';
		if(isset($this->request->get['seller_store_id'])){
			$sellerstore = $this->request->get['seller_store_id'];
		} else if ($this->customer->isSeller()) {
			$sellerstore_d = $this->customer->isSeller();
			$sellerstore = $sellerstore_d['id'];
		}
		$seller_info_social = $this->model_extension_purpletree_multivendor_vendor->getStoreSocial($sellerstore);
		if (!empty($seller_info_social) && isset($seller_info_social['facebook_link'])) {
			$data['facebook_link'] = $seller_info_social['facebook_link'];
		} else {
			$data['facebook_link'] = '';
		}	
		if (!empty($seller_info_social) && isset($seller_info_social['google_link'])) {
			$data['google_link'] = $seller_info_social['google_link'];
		} else {
			$data['google_link'] = '';
		}	
		if (!empty($seller_info_social) && isset($seller_info_social['twitter_link'])) {
			$data['twitter_link'] = $seller_info_social['twitter_link'];
		} else {
			$data['twitter_link'] = '';
		}		
		if (!empty($seller_info_social) && isset($seller_info_social['instagram_link'])) {
			$data['instagram_link'] = $seller_info_social['instagram_link'];
		} else {
			$data['instagram_link'] = '';
		}		
		if (!empty($seller_info_social) && isset($seller_info_social['pinterest_link'])) {
			$data['pinterest_link'] = $seller_info_social['pinterest_link'];
		} else {
			$data['pinterest_link'] = '';
		}		
		if (!empty($seller_info_social) && isset($seller_info_social['wesbsite_link'])) {
			$data['wesbsite_link'] = $seller_info_social['wesbsite_link'];
		} else {
			$data['wesbsite_link'] = '';
		}		
					
			if (!empty($seller_info_social) && isset($seller_info_social['whatsapp_link'])) {
			$whatsapp_no = $seller_info_social['whatsapp_link'];
		} else {
			$whatsapp_no = '';
		}	
		
		// Check mobile/desktop
		
		$tablet_browser = 0;
		$mobile_browser = 0;
		$data['whatsapp_link']='';
		   if (isset($_SERVER['HTTP_USER_AGENT'])) {
		if (preg_match('/(tablet|ipad|playbook)|(android(?!.*(mobi|opera mini)))/i', strtolower($_SERVER['HTTP_USER_AGENT']))) {
			$tablet_browser++;
		}
		 
		if (preg_match('/(up.browser|up.link|mmp|symbian|smartphone|midp|wap|phone|android|iemobile)/i', strtolower($_SERVER['HTTP_USER_AGENT']))) {
			$mobile_browser++;
		}
		}
		if (isset($_SERVER['HTTP_ACCEPT'])) {
		if ((strpos(strtolower($_SERVER['HTTP_ACCEPT']),'application/vnd.wap.xhtml+xml') > 0) or ((isset($_SERVER['HTTP_X_WAP_PROFILE']) or isset($_SERVER['HTTP_PROFILE'])))) {
			$mobile_browser++;
		}
		}
		 if (isset($_SERVER['HTTP_USER_AGENT'])) { 
		$mobile_ua = strtolower(substr($_SERVER['HTTP_USER_AGENT'], 0, 4));
		$mobile_agents = array(
			'w3c ','acs-','alav','alca','amoi','audi','avan','benq','bird','blac',
			'blaz','brew','cell','cldc','cmd-','dang','doco','eric','hipt','inno',
			'ipaq','java','jigs','kddi','keji','leno','lg-c','lg-d','lg-g','lge-',
			'maui','maxo','midp','mits','mmef','mobi','mot-','moto','mwbp','nec-',
			'newt','noki','palm','pana','pant','phil','play','port','prox',
			'qwap','sage','sams','sany','sch-','sec-','send','seri','sgh-','shar',
			'sie-','siem','smal','smar','sony','sph-','symb','t-mo','teli','tim-',
			'tosh','tsm-','upg1','upsi','vk-v','voda','wap-','wapa','wapi','wapp',
			'wapr','webc','winw','winw','xda ','xda-');
		 
		if (in_array($mobile_ua,$mobile_agents)) {
			$mobile_browser++;
		}
		 
		if (strpos(strtolower($_SERVER['HTTP_USER_AGENT']),'opera mini') > 0) {
			$mobile_browser++;
			//Check for tablets on opera mini alternative headers
			$stock_ua = strtolower(isset($_SERVER['HTTP_X_OPERAMINI_PHONE_UA'])?$_SERVER['HTTP_X_OPERAMINI_PHONE_UA']:(isset($_SERVER['HTTP_DEVICE_STOCK_UA'])?$_SERVER['HTTP_DEVICE_STOCK_UA']:''));
			if (preg_match('/(tablet|ipad|playbook)|(android(?!.*mobile))/i', $stock_ua)) {
			  $tablet_browser++;
			}
		}
		}
		 
		if ($tablet_browser > 0) {
		   // do something for tablet devices
		   if($whatsapp_no!=''){
		   $data['whatsapp_link']='https://api.whatsapp.com/send?phone='.$whatsapp_no;
		   }
		}
		else if ($mobile_browser > 0) {
		   // do something for mobile devices
		   if($whatsapp_no!=''){
		   $data['whatsapp_link']='https://api.whatsapp.com/send?phone='.$whatsapp_no;
		   }
		}
		else {
		   // do something for everything else
			   if($whatsapp_no!=''){
					$data['whatsapp_link']='https://web.whatsapp.com/send?phone='.$whatsapp_no;
			   }
		}   
		
	
		//
		
		
		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/home','',true)
		);
		if(isset($this->request->get['p_url'])){
		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_storeview'),
			'href' => $this->url->link('extension/account/purpletree_multivendor/sellerstore/storeview&seller_store_id='.$sellerstore.'&p_url='.$this->request->get['p_url'], '', true)
		);
		}else{
		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_storeview'),
			'href' => $this->url->link('extension/account/purpletree_multivendor/sellerstore/storeview&seller_store_id='.$sellerstore, '', true)
		);
	  }
		$data['storepage_layout'] = $this->config->get('module_purpletree_multivendor_storepage_layout');
		
		$store_detail = $this->model_extension_purpletree_multivendor_vendor->getStore($sellerstore);

		if (!empty($store_detail)) {
			$data['store_address'] = $store_detail['store_address'];
			$data['store_addresslen'] = strlen($data['store_address']);
		} else {
			$data['store_address'] = '';
		}		
		if (!empty($store_detail['store_timings'])) {
			$data['store_timings'] = $store_detail['store_timings'];
		} else {
			$data['store_timings'] = '';
		}		
		if (isset($this->request->post['store_address'])) {
			$data['store_address'] = $this->request->post['store_address'];
			$data['store_addresslen'] = strlen($data['store_address']);
		} elseif (!empty($store_detail)) {
			$data['store_address'] = $store_detail['store_address'];
			$data['store_addresslen'] = strlen($data['store_address']);
		} else {
			$data['store_address'] = '';
		}
		if(!empty($store_detail['store_video'])){
			if(strpos($store_detail['store_video'], 'facebook.com/') !== false) {
				$data['store_video'] = 'https://www.facebook.com/plugins/video.php?href='.rawurlencode($store_detail['store_video']).'&show_text=1&width=200';
			}else{
				$data['store_video'] = $store_detail['store_video'];
			}
		}else{
			$data['store_video'] = '';
		}		
		if(!empty($store_detail['google_map'])){
			$data['google_map'] = $store_detail['google_map'];
		}else{
			$data['google_map'] = '';
		}
					$data['module_purpletree_multivendor_allow_live_chat'] = 0;
				if(NULL !== $this->config->get('module_purpletree_multivendor_allow_live_chat')) {
					$data['module_purpletree_multivendor_allow_live_chat'] = $this->config->get('module_purpletree_multivendor_allow_live_chat');
				}
				$data['store_live_chat_enable'] =0;
				$data['store_live_chat_code'] ='';
		if($store_detail  and ($store_detail['store_status']==1)){
			$seller_detailss = $this->model_extension_purpletree_multivendor_vendor->getStoreDetail($store_detail['seller_id']);
				
				$data['store_live_chat_enable'] = isset($seller_detailss['store_live_chat_enable'])?$seller_detailss['store_live_chat_enable']:0;
				$data['store_live_chat_code'] ='';
				if(isset($seller_detailss['store_live_chat_code'])) {
					$data['store_live_chat_code'] = $seller_detailss['store_live_chat_code'];					
					if($seller_detailss['store_live_chat_code'] != '') {
						$this->session->data['seller_sto_page'] = $seller_detailss['id'];
					}
				}
				$currentpage = $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
		$this->session->data['ptsmv_current_page'] = $currentpage;
		$this->session->data['ptsmv_current_page_name'] = $store_detail['store_name'];
			$this->document->setTitle($store_detail['store_name']);
			$this->document->setDescription($store_detail['store_meta_descriptions']);
			$this->document->setKeywords($store_detail['store_meta_keywords']);
			
			$data['store_rating'] = $this->model_extension_purpletree_multivendor_vendor->getStoreRating($store_detail['seller_id']);
			$this->load->model('extension/purpletree_multivendor/vendor');
	        $cus_seller_email  = $this->model_extension_purpletree_multivendor_vendor->getCustomerEmailId($store_detail['seller_id']);
			
			$data['module_purpletree_multivendor_store_email'] = $this->config->get('module_purpletree_multivendor_store_email');
			$data['module_purpletree_multivendor_store_phone'] = $this->config->get('module_purpletree_multivendor_store_phone');
			$data['module_purpletree_multivendor_store_address'] = $this->config->get('module_purpletree_multivendor_store_address');
		     $data['module_purpletree_multivendor_store_social_link'] = $this->config->get('module_purpletree_multivendor_store_social_link');///Social links
			$data['store_name'] = $store_detail['store_name'];
			$data['seller_name'] = $store_detail['seller_name'];
			$data['store_email'] = $cus_seller_email;
			$data['store_phone'] = $store_detail['store_phone'];
			$data['store_tin'] = $store_detail['store_tin'];
			$data['store_zipcode'] = $store_detail['store_zipcode'];
			$data['store_description'] = html_entity_decode($store_detail['store_description'], ENT_QUOTES, 'UTF-8');
			$data['store_timings'] = html_entity_decode($store_detail['store_timings'], ENT_QUOTES, 'UTF-8');
			$data['store_address'] = html_entity_decode($store_detail['store_address'], ENT_QUOTES, 'UTF-8');
			$data['store_city'] = html_entity_decode($store_detail['store_city'], ENT_QUOTES, 'UTF-8').',';
			$data['store_state'] = $this->model_extension_purpletree_multivendor_vendor->getStateName($store_detail['store_state'],$store_detail['store_country']);
			$data['store_zipcode'] = $store_detail['store_zipcode'];
			$data['store_country'] = $this->model_extension_purpletree_multivendor_vendor->getCountryName($store_detail['store_country']).',';
			$data['seller_review_status'] = $this->config->get('module_purpletree_multivendor_seller_review');
			$data['store_review'] = $this->url->link('extension/account/purpletree_multivendor/sellerstore/sellerreview','seller_id=' . $store_detail['seller_id'], true);
			
			$data['store_shipping_policy'] = $this->url->link('extension/account/purpletree_multivendor/sellerstore/storedesc','path=shippingpolicy'. '&seller_store_id=' . $store_detail['id'], true);
			
			$data['store_return_policy'] = $this->url->link('extension/account/purpletree_multivendor/sellerstore/storedesc','path=returnpolicy'. '&seller_store_id=' . $store_detail['id'], true);
			
			$data['store_about'] = $this->url->link('extension/account/purpletree_multivendor/sellerstore/storedesc','path=aboutstore'. '&seller_store_id=' . $store_detail['id'], true);
			
			$data['seller_contact'] = $this->url->link('extension/account/purpletree_multivendor/sellercontact/customerReply','seller_id=' . $store_detail['seller_id'], true);
			
			$data['compare'] = $this->url->link('product/compare', true);
			
			$this->load->model('tool/image');
			
			if (is_file(DIR_IMAGE . $store_detail['store_logo'])) {
				$data['store_logo'] = $this->model_tool_image->resize($store_detail['store_logo'], 150, 150);
			} else {
				$data['store_logo'] = $this->model_tool_image->resize('no_image.png', 150, 150);
			}
			if ($this->request->server['HTTPS']) {
			$fullurl =  $this->config->get('config_ssl') . 'image/';
			} else {
			$fullurl =  $this->config->get('config_url') . 'image/';
			}
			if (is_file(DIR_IMAGE . $store_detail['store_banner'])) {
				if($this->config->get('module_purpletree_multivendor_storepage_layout')){
					$data['store_banner'] = $fullurl.$store_detail['store_banner'];
				} else {	
					$data['store_banner'] = $this->model_tool_image->resize($store_detail['store_banner'], 900,300);
				}
			} else {
				if($this->config->get('module_purpletree_multivendor_storepage_layout')){
					$data['store_banner'] = $fullurl.'catalog/purpletree_banner.jpg';
				} else {
					$data['store_banner'] = $this->model_tool_image->resize('catalog/purpletree_banner.jpg', 900, 300);
				}
			}			
			
			if (is_file(DIR_IMAGE . $store_detail['store_image'])) {
				$data['store_image'] = $fullurl.$store_detail['store_image'];
			} else {
				$data['store_image'] = $this->model_tool_image->resize('catalog/purpletree_banner.jpg', 555, 329);
			}

		$url = '';

			if (isset($this->request->get['filter'])) {
				$url .= '&filter=' . $this->request->get['filter'];
			}

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['limit'])) {
				$url .= '&limit=' . $this->request->get['limit'];
			}
			
			if (isset($this->request->get['p_url'])) {
				$url .= '&p_url=' . $this->request->get['p_url'];
			}
			$p_url='all';	
			if (isset($this->request->get['p_url'])) {
				$p_url=$this->request->get['p_url'];
			}
			
///// Start Menu 
		$seller_store_id='';
		if(isset($this->request->get['seller_store_id'])){
				$seller_store_id=$this->request->get['seller_store_id'];
			}

		$data['allCategory']=$this->url->link('extension/account/purpletree_multivendor/sellerstore/storeview', '&seller_store_id='.$this->request->get['seller_store_id'].'&p_url=all', true);
		
		$categoryMenu =$this->model_extension_purpletree_multivendor_sellerproduct->categoryMenu($this->request->get['seller_store_id']);
		
		$categoryMenuProduct =$this->model_extension_purpletree_multivendor_sellerproduct->categoryMenuProduct($this->request->get['seller_store_id'],$p_url);
		
		$storeMenu=array();
		if(!empty($categoryMenu)){
					foreach($categoryMenu as $category_key=>$category_value){
						if($category_value['category_id']){
						$data['storeMenu'][]=array(
						'url'=>$this->url->link('extension/account/purpletree_multivendor/sellerstore/storeview', '&seller_store_id='.$this->request->get['seller_store_id'].'&p_url='.$category_value['category_id'], true),
						'name'=>$category_value['name']
						
					);		
					}					
				}
			}

		
		$categoryProduct=array();
		$templateproductss=array();			
		$prod='';
		$prodimp='';
			if(isset($categoryMenuProduct)){
				foreach($categoryMenuProduct as $ki=>$valuei){
					$categoryProduct[]=$valuei['product_id'];
				}
				$prod=implode(',',$categoryProduct);
				}
				
		$templateproducts =$this->model_extension_module_purpletree_sellerprice->getTemplateProduct($seller_store_id,$p_url);

		$countttt = 0;
		if(isset($templateproducts)){
			foreach($templateproducts as $templateproduct){
				$proodid = $templateproduct;
				 $this->load->model('catalog/product');
				$image2 ='';
                $additional_images = $this->model_catalog_product->getProductImages($templateproduct['product_id']);
                 if (count($additional_images) > 0) {
                    $image2 = $this->model_tool_image->resize($additional_images[0]['image'], $this->config->get('theme_' . $this->config->get('config_theme') . '_image_product_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_product_height'));                 
                } 
				$price = $templateproduct['t_price'];
				$product_specials = $this->model_extension_purpletree_multivendor_sellerproduct->getProductSpecials($templateproduct['product_id']);
				$special = false;
				foreach ($product_specials  as $product_special) {
					if (($product_special['date_start'] == '0000-00-00' || strtotime($product_special['date_start']) < time()) && ($product_special['date_end'] == '0000-00-00' || strtotime($product_special['date_end']) > time())) {
						$special = $this->currency->format($this->tax->calculate($product_special['price'], $templateproduct['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
						break;
					}
				}
				 $image2 = false;
				 $date_end = false;	if(array_search('journal2', array_column($installed_modules, 'code')) !== False) {
				
				if (strpos($this->config->get('config_template'), 'journal2') === 0){
				 $this->load->model('journal2/product');
				 $labelss = (array_search('journal2', array_column($installed_modules, 'code')) !== False)?$this->model_journal2_product->getLabels($templateproduct['product_id']):'';
				}
	
				if (strpos($this->config->get('config_template'), 'journal2') === 0 && $special && $this->journal2->settings->get('show_countdown', 'never') !== 'never') {
                    //$this->load->model('journal2/product');
                    $date_end = $this->model_journal2_product->getSpecialCountdown($templateproduct['product_id']);
                    if ($date_end === '0000-00-00') {
                        $date_end = false;
						}
					} 
				}
				$this->load->model('catalog/product');
				
                $additional_images = $this->model_catalog_product->getProductImages($templateproduct['product_id']);


                  if (count($additional_images) > 0) {
                    $image2 = $this->model_tool_image->resize($additional_images[0]['image'], $this->config->get('theme_' . $this->config->get('config_theme') . '_image_product_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_product_height'));                 
                } 
				$conditu ="";
				if($this->config->get('module_purpletree_multivendor_seller_product_template')){
					if(!empty($seller_prices)) {
						$conditu = '&seller_id='.$seller_product['seller_id'];
					}
				}
				$templateproductss[] =  array(
					'href'  => $this->url->link('product/product', 'product_id=' . $templateproduct['product_id'].$conditu,true),
					'thumb'       => $templateproduct['image'],
					'image' 		=> $templateproduct['image'],
					'thumb2'       => $image2,
					'product_id' => $templateproduct['product_id'],
					'language_id' => $templateproduct['language_id'],
					'name' => $templateproduct['name'],
					'description' => utf8_substr(strip_tags(html_entity_decode($templateproduct['t_description'], ENT_QUOTES, 'UTF-8')), 0, $this->config->get('theme_' . $this->config->get('config_theme') . '_product_description_length'))  . '..',
                    'tag'       	 => $templateproduct['tag'] ,
                    'meta_title'     => $templateproduct['meta_title'] ,
                    'meta_description'=> $templateproduct['meta_description'],
                    'meta_keyword'   => $templateproduct['meta_keyword'],
					'rating'         => $templateproduct['rating'],
					'seller_id'      => $templateproduct['seller_id'],
					'shipping'       => $templateproduct['shipping'],
					'date_available' => $templateproduct['date_available'],
					'weight'      	 => $templateproduct['weight'],
					'length'    	 => $templateproduct['length'],
					'width'     	 => $templateproduct['width'],
					'height'     	 => $templateproduct['height'],
					'length_class_id'=> $templateproduct['length_class_id'],
					'subtract'       => $templateproduct['subtract'],
					'tax_class_id'   => $templateproduct['tax_class_id'],
                    'labels'         => $labelss ,
					'price'			 => $price,
					'countttt' 	     => $countttt,
					'date_end'       => $date_end,
					'special'    	 => $special,
					'temp_set'    	 => 1,
					'minimum'        => $templateproduct['minimum'] > 0 ? $templateproduct['minimum'] : 1
					);
				}
			}
			$template_product=0;
		if(!empty($templateproductss)){
			$template_product=1;
		}
		$store_detail = array(
			'seller_id' => $store_detail['seller_id'],
			'category_id' => $category_id,
			'filter_filter'      => $filter,
			'sort'               => $sort,
			'order'              => $order,
			'start'              => ($page - 1) * $limit,
			'limit'              => $limit,
			'status'              => 1,
			'is_approved'              => 1,
			'p_url'=>$p_url,
			'product'=>$prod,
			'producttemp'=>$prodimp,
			'producttemp'=>$prodimp,
			'template_product'=>$template_product,
		);
			
			
		$store_detail['status'] = 1;
		$store_detail['is_approved'] = 1;
		$seller_products = $this->model_extension_purpletree_multivendor_sellerproduct->getSellerProducts($store_detail);
		if(!$this->config->get('module_purpletree_multivendor_seller_product_template')){
			$templateproductss = array();
		}
		
		$seller_products =	array_merge($seller_products,$templateproductss);
		//$toatl_seller_products = count($seller_products);
		$toatl_seller_products = $this->model_extension_purpletree_multivendor_sellerproduct->getTotalSellerProducts($store_detail);
		if($seller_products){
			$countttt = 0;
			foreach($seller_products as $seller_product){
				
				/* if (is_file(DIR_IMAGE . $seller_product['image'])) {
				$image = $this->model_tool_image->resize($seller_product['image'], 150, 150);
				} else {
					$image = $this->model_tool_image->resize('no_image.png', 150, 150);
				} */
				if ($seller_product['image'] && is_file(DIR_IMAGE . $seller_product['image'])) {
					$image = $this->model_tool_image->resize($seller_product['image'], $this->config->get('theme_' . $this->config->get('config_theme') . '_image_product_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_product_height'));
				} else {
					$image = $this->model_tool_image->resize('placeholder.png', $this->config->get('theme_' . $this->config->get('config_theme') . '_image_product_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_product_height'));
				}
				
				$price = $this->currency->format($this->tax->calculate($seller_product['price'], $seller_product['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
				
				$product_specials = $this->model_extension_purpletree_multivendor_sellerproduct->getProductSpecials($seller_product['product_id']);
				
				$special = false;
				
				foreach ($product_specials  as $product_special) {
					if (($product_special['date_start'] == '0000-00-00' || strtotime($product_special['date_start']) < time()) && ($product_special['date_end'] == '0000-00-00' || strtotime($product_special['date_end']) > time())) {
						$special = $this->currency->format($this->tax->calculate($product_special['price'], $seller_product['tax_class_id'], $this->config->get('config_tax')), $this->session->data['currency']);
						break;
					}
				}
                $image2 = false;
				 $date_end = false;	if(array_search('journal2', array_column($installed_modules, 'code')) !== False) {
				
				if (strpos($this->config->get('config_template'), 'journal2') === 0){
				 $this->load->model('journal2/product');
				 $labelss = (array_search('journal2', array_column($installed_modules, 'code')) !== False)?$this->model_journal2_product->getLabels($seller_product['product_id']):'';
				}
	
				if (strpos($this->config->get('config_template'), 'journal2') === 0 && $special && $this->journal2->settings->get('show_countdown', 'never') !== 'never') {
                    //$this->load->model('journal2/product');
                    $date_end = $this->model_journal2_product->getSpecialCountdown($seller_product['product_id']);
                    if ($date_end === '0000-00-00') {
                        $date_end = false;
                    }
                }
				  
                    $this->load->model('catalog/product');
				
                $additional_images = $this->model_catalog_product->getProductImages($seller_product['product_id']);


                  if (count($additional_images) > 0) {
                    $image2 = $this->model_tool_image->resize($additional_images[0]['image'], $this->config->get('theme_' . $this->config->get('config_theme') . '_image_product_width'), $this->config->get('theme_' . $this->config->get('config_theme') . '_image_product_height'));                 
                } 
			}
		$this->load->model('extension/module/purpletree_sellerprice');

      
        $seller_prices = $this->model_extension_module_purpletree_sellerprice->getTemplateProductfromproandseller($seller_product['product_id'],$seller_product['seller_id']);	
				$conditu ="";
				if($this->config->get('module_purpletree_multivendor_seller_product_template')){
					if(!empty($seller_prices)) {
						$conditu = '&seller_id='.$seller_product['seller_id'];
					}
				}
				if(empty($seller_product['temp_set'])) {
						$seller_product['temp_set'] = 0;
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
					$seller_detail = $this->model_extension_purpletree_multivendor_sellerproduct->getSellername($seller_product['product_id']);
					$pts_quick_status = $this->model_extension_purpletree_multivendor_sellerproduct->getQucikOrderStatus($seller_product['product_id']);
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
				       $data['seller_products'][] = array('seller_name'  => isset($seller_detail['seller_name'])?$seller_detail['seller_name']:'',
						'store_address'  => $store_address,
						'google_map'  => $google_map,
						'seller_link'  => $seller_link,
						'pts_quick_status'  => $pts_quick_status,
				        'quick_order'       => $this->url->link('extension/account/purpletree_multivendor/quick_order', '&product_id='.$seller_product['product_id'].$url, true),
					    'href'  => $this->url->link('product/product', 'product_id=' . $seller_product['product_id'].$conditu,true),
						'thumb'       => $image,
					   'thumb2'       => $image2,
					   'quantity'       => isset($seller_product['quantity'])?$seller_product['quantity']:'',
					   'price_value'       => $price,  
                      'labels'        => $labelss ,
					'product_id' => $seller_product['product_id'],
					'name' => $seller_product['name'],
					'price' => $price,
					'countttt' => $countttt,
					'image' => $image,
					 'date_end'       => $date_end,
					'special'    => $special,
					'temp_set'    => $seller_product['temp_set'],
					'rating'      => $seller_product['rating'],
					'minimum'     => $seller_product['minimum'] > 0 ? $seller_product['minimum'] : 1,
					'description' => utf8_substr(strip_tags(html_entity_decode($seller_product['description'], ENT_QUOTES, 'UTF-8')), 0, $this->config->get('theme_' . $this->config->get('config_theme') . '_product_description_length'))  . '..'
				);
				if($this->config->get('module_purpletree_multivendor_allow_selleron_category') === 'service_mode'){
					$data['seller_products'] =array();
				}
				$countttt++;
			}
		}
		// Hyper local
		if($this->config->get('module_purpletree_multivendor_hyperlocal_status')){
			if((isset($this->session->data['seller_area'])&& ($this->session->data['seller_area'] > 0))){		
			if($seller_area_find != 1){
			    $data['seller_products'] = array();
				$toatl_seller_products = 0;
				}
			}
			}
		// End Hyper local 
		if(isset($this->request->get['seller_store_id'])){
			$seller_storessst = $this->model_extension_purpletree_multivendor_vendor->checkSellerVacation($this->request->get['seller_store_id']);
			if($seller_storessst>=1){	
				$data['seller_products'] ='';
				$data['error_warning'] = $this->language->get('text_warning_vacation');
			}
		}
		$url = '';

		if (isset($this->request->get['filter'])) {
			$url .= '&filter=' . $this->request->get['filter'];
		}

		if (isset($this->request->get['limit'])) {
			$url .= '&limit=' . $this->request->get['limit'];
		}
		if (isset($this->request->get['p_url'])) {
			$url .= '&p_url=' . $this->request->get['p_url'];
		}
			
		$data['sorts'] = array();

		$data['sorts'][] = array(
			'text'  => $this->language->get('text_default'),
			'value' => 'p.sort_order-ASC',
			'href'  => $this->url->link('extension/account/purpletree_multivendor/sellerstore/storeview','&sort=p.sort_order&order=ASC' . $url.'&seller_store_id='.$sellerstore,true)
		);

		$data['sorts'][] = array(
			'text'  => $this->language->get('text_name_asc'),
			'value' => 'pd.name-ASC',
			'href'  => $this->url->link('extension/account/purpletree_multivendor/sellerstore/storeview', '&sort=pd.name&order=ASC' . $url.'&seller_store_id='.$sellerstore,true)
		);

		$data['sorts'][] = array(
			'text'  => $this->language->get('text_name_desc'),
			'value' => 'pd.name-DESC',
			'href'  => $this->url->link('extension/account/purpletree_multivendor/sellerstore/storeview', '&sort=pd.name&order=DESC' . $url.'&seller_store_id='.$sellerstore,true)
		);

		$data['sorts'][] = array(
			'text'  => $this->language->get('text_price_asc'),
			'value' => 'p.price-ASC',
			'href'  => $this->url->link('extension/account/purpletree_multivendor/sellerstore/storeview',  '&sort=p.price&order=ASC' . $url.'&seller_store_id='.$sellerstore,true)
		);

		$data['sorts'][] = array(
			'text'  => $this->language->get('text_price_desc'),
			'value' => 'p.price-DESC',
			'href'  => $this->url->link('extension/account/purpletree_multivendor/sellerstore/storeview', '&sort=p.price&order=DESC' . $url.'&seller_store_id='.$sellerstore,true)
		);
		
			$data['sorts'][] = array(
				'text'  => $this->language->get('text_model_asc'),
				'value' => 'p.model-ASC',
				'href'  => $this->url->link('extension/account/purpletree_multivendor/sellerstore/storeview', '&sort=p.model&order=ASC' . $url.'&seller_store_id='.$sellerstore,true)
			);

			$data['sorts'][] = array(
				'text'  => $this->language->get('text_model_desc'),
				'value' => 'p.model-DESC',
				'href'  => $this->url->link('extension/account/purpletree_multivendor/sellerstore/storeview', '&sort=p.model&order=DESC' . $url.'&seller_store_id='.$sellerstore,true)
			);
			
			$url = '';

			if (isset($this->request->get['filter'])) {
				$url .= '&filter=' . $this->request->get['filter'];
			}

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}
			
			if (isset($this->request->get['p_url'])) {
				$url .= '&p_url=' . $this->request->get['p_url'];
			}

		
			$data['limits'] = array();

			$limits = array_unique(array($this->config->get('theme_' . $this->config->get('config_theme') . '_product_limit'), 25, 50, 75, 100));

			sort($limits);

			foreach($limits as $value) {
				$data['limits'][] = array(
					'text'  => $value,
					'value' => $value,
					'href'  => $this->url->link('extension/account/purpletree_multivendor/sellerstore/storeview', $url . '&limit=' . $value.'&seller_store_id='.$sellerstore,true)
				);
			}

			$url = '';

			if (isset($this->request->get['filter'])) {
				$url .= '&filter=' . $this->request->get['filter'];
			}
			if(! empty($category_id))
			{
				$url.= '&category=' .$category_id;
			}
			
			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['limit'])) {
				$url .= '&limit=' . $this->request->get['limit'];
			}
			if (isset($this->request->get['p_url'])) {
				$url .= '&p_url=' . $this->request->get['p_url'];
			}
			//$toatl_seller_products = $countttt;
			$url .= '&seller_store_id='.$sellerstore;
			$pagination = new Pagination();
			$pagination->total = $toatl_seller_products;
			$pagination->page = $page;
			$pagination->limit = $limit;
			$pagination->url = $this->url->link('extension/account/purpletree_multivendor/sellerstore/storeview', $url . '&page={page}',true);

			$data['pagination'] = $pagination->render();

			$data['results'] = sprintf($this->language->get('text_pagination'), ($toatl_seller_products) ? (($page - 1) * $limit) + 1 : 0, ((($page - 1) * $limit) > ($toatl_seller_products - $limit)) ? $toatl_seller_products : ((($page - 1) * $limit) + $limit), $toatl_seller_products, ceil($toatl_seller_products / $limit));

			// http://googlewebmastercentral.blogspot.com/2011/09/pagination-with-relnext-and-relprev.html
			if ($page == 1) {
			    $this->document->addLink($this->url->link('extension/account/purpletree_multivendor/sellerstore/storeview', '&seller_store_id='.$sellerstore, true), 'canonical');
			} elseif ($page == 2) {
			    $this->document->addLink($this->url->link('extension/account/purpletree_multivendor/sellerstore/storeview', '&seller_store_id='.$sellerstore, true), 'prev');
			} else {
			    $this->document->addLink($this->url->link('extension/account/purpletree_multivendor/sellerstore/storeview', '&seller_store_id='.$sellerstore.'&page='. ($page - 1), true), 'prev');
			}

			if ($limit && ceil($toatl_seller_products / $limit) > $page) {
			    $this->document->addLink($this->url->link('extension/account/purpletree_multivendor/sellerstore/storeview', '&seller_store_id='.$sellerstore.'&page='. ($page + 1), true), 'next');
			}

			$data['sort'] = $sort;
			$data['order'] = $order;
			$data['limit'] = $limit;
		$direction = $this->language->get('direction');
		 if ($direction=='rtl'){
			$this->document->addStyle('catalog/view/javascript/purpletree/bootstrap/css/bootstrap.min-a.css');
			$this->document->addStyle('catalog/view/theme/default/stylesheet/purpletree/custom-a.css'); 
			}else{
			$this->document->addStyle('catalog/view/javascript/purpletree/bootstrap/css/bootstrap.min.css'); 
			$this->document->addStyle('catalog/view/theme/default/stylesheet/purpletree/custom.css'); 
			}
		$data['column_right'] = $this->load->controller('common/column_right');		
		$data['content_top'] = $this->load->controller('common/content_top');
		$data['content_bottom'] = $this->load->controller('common/content_bottom');
		$data['footer'] = $this->load->controller('common/footer');
		$data['header'] = $this->load->controller('common/header');
		$this->response->setOutput($this->load->view('account/purpletree_multivendor/storeview', $data));
		} else {

			$data['breadcrumbs'][] = array(
				'text' => $this->language->get('text_error'),
				'href' => $this->url->link('extension/account/purpletree_multivendor/sellerstore','',true)
			);

			$this->document->setTitle($this->language->get('text_error'));

			$data['heading_title'] = $this->language->get('text_error');

			$data['text_error'] = $this->language->get('text_error');

			$data['button_continue'] = $this->language->get('button_continue');

			$data['continue'] = $this->url->link('common/home','',true);

			$this->response->addHeader($this->request->server['SERVER_PROTOCOL'] . ' 404 Not Found');
	$direction = $this->language->get('direction');
		 if ($direction=='rtl'){
			$this->document->addStyle('catalog/view/javascript/purpletree/bootstrap/css/bootstrap.min-a.css');
			$this->document->addStyle('catalog/view/theme/default/stylesheet/purpletree/custom-a.css'); 
			}else{
			$this->document->addStyle('catalog/view/javascript/purpletree/bootstrap/css/bootstrap.min.css'); 
			$this->document->addStyle('catalog/view/theme/default/stylesheet/purpletree/custom.css'); 
			}
			$data['column_left'] = $this->load->controller('common/column_left');
			$data['column_right'] = $this->load->controller('common/column_right');
			$data['content_top'] = $this->load->controller('common/content_top');
			$data['content_bottom'] = $this->load->controller('common/content_bottom');
			$data['footer'] = $this->load->controller('common/footer');
			$data['header'] = $this->load->controller('common/header');

			$this->response->setOutput($this->load->view('error/not_found', $data));
		}
	}

	public function storedesc() { 
		$this->load->language('purpletree_multivendor/storeview');
		
		$this->load->model('extension/purpletree_multivendor/vendor');
		
		$this->load->model('extension/purpletree_multivendor/dashboard');
			
		$this->model_extension_purpletree_multivendor_dashboard->checkSellerApproval();
		if (isset($this->request->get['seller_store_id'])) {
			$store_id = (int)$this->request->get['seller_store_id'];
		} else {
			$store_id = 0;
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/home','',true)
		);
		$store_info = $this->model_extension_purpletree_multivendor_vendor->getStore($store_id);

		if ($store_info) {
			if($this->request->get['path']=="shippingpolicy"){
				$data['breadcrumbs'][] = array(
					'text' => $this->language->get('text_shippingpolicy'),
					'href' => $this->url->link('extension/account/purpletree_multivendor/sellerstore/storedesc&path='.$this->request->get['path'].'&seller_store_id='.$store_id, '', true)
				);
		
				$this->document->setTitle($this->language->get('text_shippingpolicy'));
				$data['text_policy'] = $this->language->get('text_shippingpolicy');
				$data['store_policy'] = html_entity_decode($store_info['store_shipping_policy'], ENT_QUOTES, 'UTF-8') . "\n";
			} elseif($this->request->get['path']=="returnpolicy"){
				$data['breadcrumbs'][] = array(
					'text' => $this->language->get('text_returnpolicy'),
					'href' => $this->url->link('extension/account/purpletree_multivendor/sellerstore/storedesc&path='.$this->request->get['path'].'&seller_store_id='.$store_id, '', true)
				);
				$this->document->setTitle($this->language->get('text_returnpolicy'));
				$data['text_policy'] = $this->language->get('text_returnpolicy');
				$data['store_policy'] = html_entity_decode($store_info['store_return_policy'], ENT_QUOTES, 'UTF-8') . "\n";
			} elseif($this->request->get['path']=="aboutstore"){
				$data['breadcrumbs'][] = array(
					'text' => $this->language->get('text_aboutstore'),
					'href' => $this->url->link('extension/account/purpletree_multivendor/sellerstore/storedesc&path='.$this->request->get['path'].'&seller_store_id='.$store_id, '', true)
				);
				$this->document->setTitle($this->language->get('text_aboutstore'));
				$data['text_policy'] = $this->language->get('text_aboutstore');
				$data['store_policy'] = html_entity_decode($store_info['store_description'], ENT_QUOTES, 'UTF-8') . "\n";
			}
		}
	$direction = $this->language->get('direction');
		 if ($direction=='rtl'){
			$this->document->addStyle('catalog/view/javascript/purpletree/bootstrap/css/bootstrap.min-a.css');
			$this->document->addStyle('catalog/view/theme/default/stylesheet/purpletree/custom-a.css'); 
			}else{
			$this->document->addStyle('catalog/view/javascript/purpletree/bootstrap/css/bootstrap.min.css'); 
			$this->document->addStyle('catalog/view/theme/default/stylesheet/purpletree/custom.css'); 
			}
		$data['content_top'] = $this->load->controller('common/content_top');
		$data['content_bottom'] = $this->load->controller('common/content_bottom');
		$data['footer'] = $this->load->controller('common/footer');
		$data['header'] = $this->load->controller('common/header');

		$this->response->setOutput($this->load->view('account/purpletree_multivendor/policy', $data));
	}
	
	private function validateSeller(){		
		$this->load->model('extension/purpletree_multivendor/vendor');
		if(!$this->customer->validateSeller()) {
            $this->load->language('purpletree_multivendor/ptsmultivendor');			
			$this->error['error_warning'] = $this->language->get('error_license');
		}		
		if($this->request->post['become_seller']){ 		
		if ((utf8_strlen(trim($this->request->post['seller_storename'])) < 5) || (utf8_strlen(trim($this->request->post['seller_storename'])) > 50)) {			
			$this->error['seller_store'] = $this->language->get('error_storename');		
		}
		
		$store_info1 = $this->model_extension_purpletree_multivendor_vendor->getStoreNameByStoreName($this->request->post['seller_storename']);	
		
        if ($store_info1 && (strtoupper(trim($this->request->post['seller_storename']))==strtoupper($store_info1['store_name']))) {
			
				$this->error['seller_store'] = $this->language->get('error_exist_storename');
				$this->error['warning'] = $this->language->get('error_warning');
		}
		
			
			if(!empty($this->request->files['upload_file']['name']))
			{
				$allowed_file=array('gif','png','jpg','pdf','doc','docx','zip');
				$filename = $this->request->files['upload_file']['name'];
				
				$extension = pathinfo($filename,PATHINFO_EXTENSION);
				
				if(!in_array($extension ,$allowed_file)) {
					
					$this->error['warning1'] = $this->language->get('error_supported_file');
				}
			}
		
		}
		return !$this->error;
	}
	
	private function validateForm(){
		
		$seller_seo = $this->model_extension_purpletree_multivendor_vendor->getStoreSeo($this->request->post['store_seo']);
		
		$store_info = $this->model_extension_purpletree_multivendor_vendor->getStoreById($this->customer->getId());

		$pattern = '/[\'\/~`\!@#\$%\^&\*\(\)\+=\{\}\[\]\|;:"\<\>,\.\?\\\ ]/';
		if (preg_match($pattern, $this->request->post['store_seo'])==true) {
			$this->error['store_seo'] = $this->language->get('error_store_seo');
		}elseif(isset($store_info['id'])){
			$seller_seot = "seller_store_id=".$store_info['id'];
			if(isset($seller_seo['query'])){
				if($seller_seo['query']!=$seller_seot){
					$this->error['store_seo'] = $this->language->get('error_storeseo');
				}
			}
		}
		if(!empty($_FILES['upload_file']['name'])) {
		 $allowed_file=array('gif','png','jpg','pdf','doc','docx','zip');
                        $filename = basename(preg_replace('/[^a-zA-Z0-9\.\-\s+]/', '', html_entity_decode($_FILES['upload_file']['name'], ENT_QUOTES, 'UTF-8')));
                    $extension = pathinfo($filename, PATHINFO_EXTENSION);
					 if(!in_array($extension,$allowed_file) ) {
						$this->error['error_file_upload'] = $this->language->get('error_supported_file');
					 }
		}
		if ((utf8_strlen(trim($this->request->post['store_name'])) < 5) || (utf8_strlen(trim($this->request->post['store_name'])) > 50)) {
			$this->error['store_name'] = $this->language->get('error_storename');
		}
		$store_info1 = $this->model_extension_purpletree_multivendor_vendor->getStoreNameByStoreName($this->request->post['store_name']);

		$store_detail = $this->customer->isSeller();
		
		if (isset($store_detail['id'])) {
			if ($store_info1 && ($store_detail['id'] != $store_info1['id'] && strtoupper(trim($this->request->post['store_name']))==strtoupper($store_info1['store_name']))) {
				$this->error['store_name'] = $this->language->get('error_exist_storename');
				$this->error['warning'] = $this->language->get('error_warning');
		}
		}
		/* $EMAIL_REGEX='/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,4})$/';
		
		if (preg_match($EMAIL_REGEX, $this->request->post['store_email'])==false)	
		{
			$this->error['store_email'] = $this->language->get('error_storeemail');
		}
		$store_detail = $this->customer->isSeller();
		
		$store_info1 = $this->model_extension_purpletree_multivendor_vendor->getStoreByIdd($this->customer->getId(),$this->request->post['store_email']);
		if($store_info1){
			$this->error['warning'] = $this->language->get('error_exists');
		} */
		if(!empty($this->request->post['store_phone'])){	
		if(trim($this->request->post['store_phone']) < 1){
			if ((utf8_strlen(trim($this->request->post['store_phone'])) < 3) || (utf8_strlen(trim($this->request->post['store_phone'])) > 32)) {
					$this->error['store_phone'] = $this->language->get('error_storephone');
			}
		}
		}
		if ((utf8_strlen(trim($this->request->post['store_address'])) < 5) || (utf8_strlen(trim($this->request->post['store_address'])) > 201)) {
			$this->error['store_address'] = $this->language->get('error_storeaddress');
		}
		
		if ((utf8_strlen(trim($this->request->post['store_city'])) < 3) || (utf8_strlen(trim($this->request->post['store_city'])) > 50)) {
			$this->error['store_city'] = $this->language->get('error_storecity');
		}
		
		if (empty($this->request->post['store_country'])) {
			$this->error['store_country'] = $this->language->get('error_storecountry');
		}
		
		if ($this->request->post['store_state'] == '') {
			$this->error['error_storezone'] = $this->language->get('error_storezone');
		}
		
		if(trim($this->request->post['store_zipcode']) >= 1){
			if ((utf8_strlen(trim($this->request->post['store_zipcode'])) < 3) || (utf8_strlen(trim($this->request->post['store_zipcode'])) > 12)) {
				$this->error['store_zipcode'] = $this->language->get('error_storepostcode');
			}
		}
		
		/* if ((utf8_strlen(trim($this->request->post['store_meta_keywords'])) =='') ) {
			$this->error['store_meta_keywords'] = $this->language->get('error_storemetakeywords');
		} */
		
		/* if ((utf8_strlen(trim($this->request->post['store_meta_description']))=='') ) {
			$this->error['store_meta_description'] = $this->language->get('error_storemetadescription');
		} */
		
		/* if ((utf8_strlen(trim($this->request->post['store_bank_details'])) =='') ) {
			$this->error['store_bank_details'] = $this->language->get('error_storebankdetail');
		} */
		if($this->request->post['seller_paypal_id'] != ''){
		$EMAIL_REGEX='/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,4})$/';
		
			if (preg_match($EMAIL_REGEX, $this->request->post['seller_paypal_id'])==false)	
			{
				$this->error['seller_paypal_id'] = $this->language->get('error_storeemail');
			}
		}
		
		if(!empty($this->request->post['store_shipping_charge'])){
			if(trim($this->request->post['store_shipping_charge']) < 0){
				$this->error['store_shipping_charge'] = $this->language->get('error_storeshippingcharge');
			}
		}
		if ($this->error && !isset($this->error['warning'])) {
			$this->error['warning'] = $this->language->get('error_warning');
		}
		return !$this->error;
	}
	
	public function removeseller(){
		
		if (!$this->customer->isLogged()) {
			$this->session->data['redirect'] = $this->url->link('account/purpletree_multivendor/sellerstore/removeseller', '', true);

			$this->response->redirect($this->url->link('account/login', '', true));
		}
				if (!$this->customer->isLogged()) {
			$this->session->data['redirect'] = $this->url->link('extension/account/purpletree_multivendor/removeseller', '', true);
			$this->response->redirect($this->url->link('account/login', '', true));
		}
		$store_detail = $this->customer->isSeller();
		if(!isset($store_detail['store_status'])){
			$this->response->redirect($this->url->link('account/account', '', true));
		}
		if(!$this->customer->validateSeller()) {
			$this->load->language('purpletree_multivendor/ptsmultivendor');
			$this->session->data['error_warning'] = $this->language->get('error_license');
			$this->response->redirect($this->url->link('account/account', '', true));
		}
		$this->load->language('purpletree_multivendor/storeview');
		
		$seller_id = $this->customer->getId();
		
		$this->load->model('extension/purpletree_multivendor/vendor');
		
		$result = $this->model_extension_purpletree_multivendor_vendor->removeSeller($seller_id);
		
		$this->session->data['success'] = $this->language->get('text_remove_account_success');
		
		$this->response->redirect($this->url->link('account/account', '', true));
		
	}
	

	public function sellerreview() { 

		$data['customer_id'] = $this->customer->getId();
		
		$this->load->language('purpletree_multivendor/sellerreview');
		
		$this->load->model('extension/purpletree_multivendor/sellerreview');

		if ($this->request->server['REQUEST_METHOD'] == 'POST' && $this->validateReview()) {
			
			$this->model_extension_purpletree_multivendor_sellerreview->addReview($this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$this->response->redirect($this->url->link('extension/account/purpletree_multivendor/sellerstore/sellerreview&seller_id='.$this->request->post['seller_id'],'',true));
		}
		
		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}
		
		if (isset($this->request->get['limit'])) {
			$limit = (int)$this->request->get['limit'];
		} else {
			$limit = $this->config->get('config_limit_admin');
		}
		
		if (isset($this->request->get['seller_id'])) {
			$seller_id = (int)$this->request->get['seller_id'];
		} else {
			$seller_id = 0;
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/home','',true)
		);	
		
		$this->document->setTitle($this->language->get('text_storereview'));
		
		$data['text_storereview'] = $this->language->get('text_storereview');
		$data['text_title'] = $this->language->get('text_title');
		$data['text_description'] = $this->language->get('text_description');
		$data['text_rating'] = $this->language->get('text_rating');
		$data['text_empty_result'] = $this->language->get('text_empty_result');
		$data['text_heading'] = $this->language->get('text_heading');
		$data['text_note'] = $this->language->get('text_note');
		$data['entry_bad'] = $this->language->get('entry_bad');
		$data['entry_good'] = $this->language->get('entry_good');
		$data['text_login'] = $this->language->get('text_login');
		$data['button_continue'] = $this->language->get('button_continue');
		
		if (isset($this->session->data['success'])) {
			$data['success'] = $this->session->data['success'];

			unset($this->session->data['success']);
		} else {
			$data['success'] = '';
		}
		
		if (isset($this->error['review_title'])) {
			$data['error_title'] = $this->error['review_title'];
		} else {
			$data['error_title'] = '';
		}
		
		if (isset($this->error['rating'])) {
			$data['error_rating'] = $this->error['rating'];
		} else {
			$data['error_rating'] = '';
		}
		
		if (isset($this->error['review_description'])) {
			$data['error_description'] = $this->error['review_description'];
		} else {
			$data['error_description'] = '';
		}
		if (isset($this->error['no_can_review'])) {
			$data['warning'] = $this->error['no_can_review'];
		} else {
			$data['warning'] = '';
		}
		
		if(isset($this->request->get['seller_id'])){
			$data['breadcrumbs'][] = array(
				'text' => $this->language->get('text_storereview'),
				'href' => $this->url->link('extension/account/purpletree_multivendor/sellerstore/sellerreview&seller_id='.$this->request->get['seller_id'], '', true)
			);
			$data['seller_id'] = $seller_id;
			$this->load->model('extension/purpletree_multivendor/sellerreview');
		if(!$this->model_extension_purpletree_multivendor_sellerreview->canReview($datasend = array('seller_id' =>$seller_id,'customer_id' =>$data['customer_id']))) {
				$data['warning'] = $this->language->get('no_can_review');
		}
			
			$data['action'] = $this->url->link('extension/account/purpletree_multivendor/sellerstore/sellerreview&seller_id='.$data['seller_id'],'',true);
			
			$filter_data = array(
				'start'              => ($page - 1) * $limit,
				'limit'              => $limit,
				'seller_id' 		=> $seller_id,
				'customer_id'		=> $data['customer_id']
			);
				
			$review_total = $this->model_extension_purpletree_multivendor_sellerreview->getTotalSellerReview($filter_data);
			
			if (isset($this->request->post['review_title'])) { 
				$data['review_title'] = $this->request->post['review_title'];
			} else { 
				$data['review_title'] = '';
			}
			
			if (isset($this->request->post['review_description'])) { 
				$data['review_description'] = $this->request->post['review_description'];
			} else { 
				$data['review_description'] = '';
			}
			
			if (isset($this->request->post['seller_id'])) { 
				$data['seller_id'] = $this->request->post['seller_id'];
			} else { 
				$data['seller_id'] = (isset($this->request->get['seller_id'])?$this->request->get['seller_id']:'');
			}
			
			$results = $this->model_extension_purpletree_multivendor_sellerreview->getSellerReview($filter_data);
			
			$data['result_check'] = $this->model_extension_purpletree_multivendor_sellerreview->checkReview($filter_data);
			
			$data['reviews'] = array();
			if ($results) {
				foreach($results as $result){
					$data['reviews'][] = array(
						'customer_name'     => $result['customer_name'],
						'seller_id'     => $result['seller_id'],
						'review_title'     => $result['review_title'],
						'review_description'       => nl2br($result['review_description']),
						'rating'     => (int)$result['rating'],
						'date_added' => date($this->language->get('date_format_short'), strtotime($result['created_at']))
					);
				}
			}
			
			$pagination = new Pagination();
			$pagination->total = $review_total;
			$pagination->page = $page;
			$pagination->limit = $limit;
			$pagination->url = $this->url->link('extension/account/purpletree_multivendor/sellerstore/sellerreview', 'seller_id=' . $data['seller_id'] . '&page={page}',true);

			$data['pagination'] = $pagination->render();

			$data['results'] = sprintf($this->language->get('text_pagination'), ($review_total) ? (($page - 1) * $limit) + 1 : 0, ((($page - 1) * $limit) > ($review_total - $limit)) ? $review_total : ((($page - 1) * $limit) + $limit), $review_total, ceil($review_total / $limit));
					$direction = $this->language->get('direction');
		 if ($direction=='rtl'){
			$this->document->addStyle('catalog/view/javascript/purpletree/bootstrap/css/bootstrap.min-a.css');
			$this->document->addStyle('catalog/view/theme/default/stylesheet/purpletree/custom-a.css'); 
			}else{
			$this->document->addStyle('catalog/view/javascript/purpletree/bootstrap/css/bootstrap.min.css'); 
			$this->document->addStyle('catalog/view/theme/default/stylesheet/purpletree/custom.css'); 
			}
			$data['column_left'] = $this->load->controller('common/column_left');
			$data['column_right'] = $this->load->controller('common/column_right');
			$data['content_top'] = $this->load->controller('common/content_top');
			$data['content_bottom'] = $this->load->controller('common/content_bottom');
			$data['footer'] = $this->load->controller('common/footer');
			$data['header'] = $this->load->controller('common/header');

			$this->response->setOutput($this->load->view('account/purpletree_multivendor/review', $data));
		} else{
			if($this->customer->isSeller()){
				
			$seller_id = $this->customer->getId();
			
			$data['breadcrumbs'][] = array(
				'text' => $this->language->get('text_storereview'),
				'href' => $this->url->link('extension/account/purpletree_multivendor/sellerstore/sellerreview', '', true)
			);
			$filter_data = array(
				'start'              => ($page - 1) * $limit,
				'limit'              => $limit,
				'seller_id' 		=> $seller_id,
				'shown'				=> '1'
			);
				
			$review_total = $this->model_extension_purpletree_multivendor_sellerreview->getTotalSellerReview($filter_data);

			$results = $this->model_extension_purpletree_multivendor_sellerreview->getSellerReview($filter_data);
			
			$data['reviews'] = array();
			
			if ($results) {
				foreach($results as $result){
					$data['reviews'][] = array(
						'customer_name'     => $result['customer_name'],
						'review_title'     => $result['review_title'],
						'review_description'       => nl2br($result['review_description']),
						'rating'     => (int)$result['rating'],
						'status'     => (($result['status'])?$this->language->get('text_approved'):$this->language->get('text_notapproved')),
						'date_added' => date($this->language->get('date_format_short'), strtotime($result['created_at']))
					);
				}
			}
			
			$pagination = new Pagination();
			$pagination->total = $review_total;
			$pagination->page = $page;
			$pagination->limit = $limit;
			$pagination->url = $this->url->link('extension/account/purpletree_multivendor/sellerstore/sellerreview', 'page={page}',true);

			$data['pagination'] = $pagination->render();

			$data['results'] = sprintf($this->language->get('text_pagination'), ($review_total) ? (($page - 1) * $limit) + 1 : 0, ((($page - 1) * $limit) > ($review_total - $limit)) ? $review_total : ((($page - 1) * $limit) + $limit), $review_total, ceil($review_total / $limit));
				
			$data['column_left'] = $this->load->controller('extension/account/purpletree_multivendor/common/column_left');
			$data['footer'] = $this->load->controller('extension/account/purpletree_multivendor/common/footer');
			$data['header'] = $this->load->controller('extension/account/purpletree_multivendor/common/header');

			$this->response->setOutput($this->load->view('account/purpletree_multivendor/reviewlist', $data));
			}
		}
	}
	
	private function validateReview(){
		
		if ((utf8_strlen($this->request->post['review_title']) < 3) ) {
			$this->error['review_title'] = $this->language->get('error_title');
		}
		
		if ((empty($this->request->post['rating'])) ) {
			$this->error['rating'] = $this->language->get('error_rating');
		}
		
		if ((utf8_strlen($this->request->post['review_description']) < 5) ) {
			$this->error['review_description'] = $this->language->get('error_description_length');
		} elseif(empty($this->request->post['review_description'])){
			$this->error['review_description'] = $this->language->get('error_description');
		}
		
		$this->load->model('extension/purpletree_multivendor/sellerreview');
		
		if(!$this->model_extension_purpletree_multivendor_sellerreview->canReview($this->request->post)) {
				$this->error['no_can_review'] = $this->language->get('no_can_review');
		}
		
		return !$this->error;
	}
	
		protected function subscription($seller){
			$this->load->model('extension/purpletree_multivendor/sellercontact');
			   $invoiceStatus=array();
			if($this->config->get('module_purpletree_multivendor_multiple_subscription_plan_active')){
			   $getSsellerplanStatus=array();
		$getSsellerplanStatus = $this->model_extension_purpletree_multivendor_sellercontact->getSsellerplanStatusMultiplePlan($seller);
		$invoiceStatus = $this->model_extension_purpletree_multivendor_sellercontact->getInvoiceStatusMultiplePlan($seller);
		
		$this->load->model('extension/purpletree_multivendor/dashboard');
			
		$this->model_extension_purpletree_multivendor_dashboard->checkSellerApproval();		

		    $data['subscription_status']=1;
            $invo_sts='0';
			if(!empty($invoiceStatus)){
				foreach($invoiceStatus as $key => $invoice_sts){
					if($invoice_sts['invoice_status']==2){
						$invo_sts=$invoice_sts['invoice_status'];
					}
				}
			}
		    	if(!$getSsellerplanStatus || ($invoice_sts['invoice_status']==NULL || $invo_sts!=2)) {
					$data['subscription_status']=0;			

				}   
			} else {
			$getSsellerplanStatus = $this->model_extension_purpletree_multivendor_sellercontact->getSsellerplanStatus($seller);
		    $invoiceStatus = $this->model_extension_purpletree_multivendor_sellercontact->getInvoiceStatus($seller);   
		    $data['subscription_status']=1;
				if(!$getSsellerplanStatus && ($invoiceStatus==NULL || $invoiceStatus!=2)) {
					$data['subscription_status']=0;			
				}
			}
			
				
			return $data['subscription_status'];			
		}
	public function generateVideoEmbedUrl($url){
		$finalUrl = '';
		if(strpos($url, 'facebook.com/') !== false) {
			$finalUrl.=$url;
		}else if(strpos($url, 'vimeo.com/') !== false) {
			$videoId = explode("vimeo.com/",$url);
			if(strpos($videoId, '&') !== false){
				$videoId = explode("&",$videoId)[0];
			}
			$finalUrl.='https://player.vimeo.com/embed/'.$videoId;
		}else if(strpos($url, 'https://player.vimeo.com/embed/') !== false) {
			return $this->request->post['store_video'];
		}else if(strpos($url, 'https://www.youtube.com/embed/') !== false) {
			return $this->request->post['store_video'];
		} else if(strpos($url, 'youtube.com/') !== false) {
			$videoId = explode("v=",$url)[1];
			if(strpos($videoId, '&') !== false){
				$videoId = explode("&",$videoId)[0];
			}
			$finalUrl.='https://www.youtube.com/embed/'.$videoId;
		}else if(strpos($url, 'youtu.be/') !== false){
			$videoId = explode("youtu.be/",$url)[1];
			if(strpos($videoId, '&') !== false){
				$videoId = explode("&",$videoId)[0];
			}
			$finalUrl.='https://www.youtube.com/embed/'.$videoId;
		}else if(strpos($url, 'dailymotion.com/embed/') !== false) {
			return $this->request->post['store_video'];
		} else if(strpos($url, 'dailymotion.com/') !== false) {
			$videoId = explode("dailymotion.com/",$url)[1];
			if(strpos($videoId, '&') !== false){
				$videoId = explode("&",$videoId)[0];
			}
			$finalUrl.='https://www.dailymotion.com/embed/'.$videoId;
		}else if(strpos($url, 'youku.com/') !== false) {
			$videoId = explode("youku.com/",$url)[1];
			if(strpos($videoId, '&') !== false){
				$videoId = explode("&",$videoId)[0];
			}
			$finalUrl.='https://v.youku.com/v_show/'.$videoId;
		} 
		return $finalUrl;
	}
		// seller area
		public function sellerarea() {
		
		$json = array();

		if (isset($this->request->get['filter_name'])) {
			$this->load->model('extension/purpletree_multivendor/vendor');

			$filter_data = array(
				'filter_name' => $this->request->get['filter_name'],
				'sort'        => 'name',
				'order'       => 'ASC',
				'start'       => 0,
				'limit'       => 5
			);
            $results = $this->model_extension_purpletree_multivendor_vendor->getSellerAreass($filter_data);
			foreach ($results as $result) {
				$json[] = array(
					'area_id' => $result['area_id'],
					'name'        => strip_tags(html_entity_decode($result['name'], ENT_QUOTES, 'UTF-8'))
				);
			}
		}

		$sort_order = array();

		foreach ($json as $key => $value) {
			$sort_order[$key] = $value['name'];
		}

		array_multisort($sort_order, SORT_ASC, $json);

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
	// end seller area
	public function getrealpath($filename,$realfile) {
		if (!is_file(DIR_UPLOAD . $filename) || substr(str_replace('\\', '/', realpath(DIR_UPLOAD . $filename)), 0, strlen(DIR_UPLOAD)) != str_replace('\\', '/', DIR_UPLOAD)) {
			return;
		}

		 $extension = pathinfo($realfile, PATHINFO_EXTENSION);
		 $image_old = $realfile;
		 $image_new = 'cache/' . utf8_substr($filename, 0, utf8_strrpos($filename, '.'));

		if (!is_file(DIR_IMAGE . $image_new)) {
			
			$path = '';

			$directories = explode('/', dirname($image_new));

			foreach ($directories as $directory) {
				$path = $path . '/' . $directory;

				if (!is_dir(DIR_UPLOAD . $path)) {
					@mkdir(DIR_UPLOAD . $path, 0777);
				}
			}
				copy(DIR_UPLOAD . $filename, DIR_IMAGE . $image_new);
		}
		
		$image_new = str_replace(' ', '%20', $image_new);  // fix bug when attach image on email (gmail.com). it is automatic changing space " " to +
		if ($this->request->server['HTTPS']) {
			return $this->config->get('config_ssl') . 'image/' . $image_new;
		} else {
			return $this->config->get('config_url') . 'image/' . $image_new;
		}
	}
}
if (! function_exists('array_column')) {
    function array_column(array $input, $columnKey, $indexKey = null) {
        $array = array();
        foreach ($input as $value) {
            if ( !array_key_exists($columnKey, $value)) {
                trigger_error("Key \"$columnKey\" does not exist in array");
                return false;
            }
            if (is_null($indexKey)) {
                $array[] = $value[$columnKey];
            }
            else {
                if ( !array_key_exists($indexKey, $value)) {
                    trigger_error("Key \"$indexKey\" does not exist in array");
                    return false;
                }
                if ( ! is_scalar($value[$indexKey])) {
                    trigger_error("Key \"$indexKey\" does not contain scalar value");
                    return false;
                }
                $array[$value[$indexKey]] = $value[$columnKey];
            }
        }
        return $array;
    }
}
?>