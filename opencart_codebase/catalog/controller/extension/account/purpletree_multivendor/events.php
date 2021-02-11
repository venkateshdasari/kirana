<?php
class ControllerExtensionAccountPurpletreeMultivendorEvents extends Controller {
	
		// model/catalog/product/getProducts/after
		public function getFilterProducts(&$route, &$args, &$output) {		
		if($this->config->get('module_purpletree_multivendor_status')){		
		if($this->config->get('module_purpletree_multivendor_hyperlocal_status')){
		if((isset($this->session->data['seller_area'])&& ($this->session->data['seller_area'] > 0))){
		$current_area = '';	
		$assign_area = array();
		     $filter_products = array();
			$this->load->model('extension/purpletree_multivendor/sellerproduct');
			$this->load->model('extension/purpletree_multivendor/vendor');
			if(!empty($output)){
			foreach($output as $key => $value){
			    $seller_detail = $this->model_extension_purpletree_multivendor_sellerproduct->getSellername($value['product_id']);
				if($seller_detail){
				$seller_detailss = $this->model_extension_purpletree_multivendor_vendor->getStoreDetail($seller_detail['seller_id']);
				if($seller_detailss['store_area'] != ''){
				    $current_area = $this->session->data['seller_area'];	
					$assign_area = unserialize($seller_detailss['store_area']);
					if(!in_array($current_area,$assign_area)){
						 unset($output[$key]);
						}
				  }
				   }
			   }
			 }
		   }
		   }
			return $output;
			}
		}	
		
		// model/catalog/product/getTotalProducts/after
		public function getFilterTotalProducts(&$route, &$args, &$output) {
		  if($this->config->get('module_purpletree_multivendor_status')){		
			if($this->config->get('module_purpletree_multivendor_hyperlocal_status')){
				if((isset($this->session->data['seller_area'])&& ($this->session->data['seller_area'] > 0))){
				 /// add code here 
				}
			}
		  }
		}
		
		// model/catalog/product/getProduct/after
		public function getFilterProduct(&$route, &$args, &$output) {		
		   if($this->config->get('module_purpletree_multivendor_status')){		if($this->config->get('module_purpletree_multivendor_hyperlocal_status')){
		if((isset($this->session->data['seller_area'])&& ($this->session->data['seller_area'] > 0))){	
		$current_area = '';	
		$assign_area = array();
		     $filter_products = array();
			$this->load->model('extension/purpletree_multivendor/sellerproduct');
			$this->load->model('extension/purpletree_multivendor/vendor');
			if(!empty($output)){
			   $seller_detail = $this->model_extension_purpletree_multivendor_sellerproduct->getSellername($output['product_id']);
				if($seller_detail){
				$seller_detailss = $this->model_extension_purpletree_multivendor_vendor->getStoreDetail($seller_detail['seller_id']);
				    $current_area = $this->session->data['seller_area'];
					if($seller_detailss['store_area'] != ''){
					$assign_area = unserialize($seller_detailss['store_area']);
					if(!in_array($current_area,$assign_area)){
						 $output = '';
						}
					}
				   }
			}
		}
		}
	  }
	}
		
	public function beforeheader(&$route, &$data, &$output) {
		  if($this->config->get('module_purpletree_multivendor_status')){	
		$data= array();
		$this->load->language('account/ptsregister');
		$this->load->language('purpletree_multivendor/sellerregister');
		   //unset($this->session->data['seller_area']);
           
            if(!isset($this->session->data['seller_area'])) {		
						
               $this->session->data['seller_area']= '';			   
			   $data['check_seller_area'] = $this->session->data['seller_area'];
	         }else{
			  $data['check_seller_area'] = $this->session->data['seller_area'];
			 }
			 if($this->config->get('module_purpletree_multivendor_area_status')){
		            $data['seller_area_namea'] = "...";
				  }else{
					$data['seller_area_namea'] = $this->language->get('text_all_sellerarea');
				  }
			 $this->load->model('extension/purpletree_multivendor/vendor');
			 $data['seller_area'] = '';
			if((isset($this->session->data['seller_area'])) && ($this->session->data['seller_area'] != '')) {
			if($this->session->data['seller_area'] > 0){
		        $seller_area = $this->model_extension_purpletree_multivendor_vendor->getSellerAreasName($this->session->data['seller_area']); 
	            if(isset($seller_area)){
				   $data['seller_area_namea'] = $seller_area;
				}else{
				   unset($this->session->data['seller_area']);
				   $data['seller_area_namea'] = "...";
					}
			   }else{
			     if($this->session->data['seller_area'] == 0){
				    $data['seller_area_namea'] = $this->language->get('text_all_sellerarea');
				  }else{
					 $data['seller_area_namea'] = "...";
				  }
			   }
			    if(isset( $this->session->data['seller_area'])){
				$data['seller_area'] = $this->session->data['seller_area'];	
				}else{
				 $data['seller_area'] = '';
				}
	         }
             // start hyper local  section//
                $hyper_local=$this->config->get('module_purpletree_multivendor_hyperlocal_status');
                $data['hyper_status']= $hyper_local;               
		
		    // End  hyper local section//
                

       // start pop up heading 
	      if($this->config->get('module_purpletree_multivendor_hp_heading')){
            $popupheading=$this->config->get('module_purpletree_multivendor_hp_heading');			
            $data['hp_heading']=$popupheading;
		  }else{		    
			 $data['hp_heading']="";
		  }
		  if($this->config->get('module_purpletree_multivendor_area_status')){
		      $data['area_status_check'] = $this->config->get('module_purpletree_multivendor_area_status');
			  }
		  if($this->config->get('config_url')){
		   $data['hyperlocal_loder'] = $this->config->get('config_url')."image/catalog/spinner.gif"; 
		  }
		  if($this->config->get('config_url')){
		   $data['base_url'] = $this->config->get('config_url'); 
		  }
		  // end pop up heading       

            $data['sellerareas'] = array();			
			$data['sellerareas'] = array();
		    $all_seller_store = array();
		    $assign_area = array();
		    $all_seller_store = array();
			$results = $this->model_extension_purpletree_multivendor_vendor->getSellerAreas();
			$sellerareas = array();
			if(!empty($results)){
			foreach ($results as $result) {	
			/* $all_seller_store = $this->model_extension_purpletree_multivendor_vendor->getAllSellerInfo();
			if(!empty($all_seller_store)){
			     foreach ($all_seller_store as $seller_store) {
				       if($seller_store['store_area'] != ''){
					       $assign_area = unserialize($seller_store['store_area']);
					       if(in_array($result['area_id'],$assign_area)){ */
						       $sellerareas[] = array(
								'area_id'=> $result['area_id'],
								'area_name'=> $result['area_name']);
						        
							   /* }
						   }
					 }
				} */
				
			} 
			} 			
			$all_area[] = array(
			    'area_id' => 0,
                'area_name' => $this->language->get('text_all_sellerarea')
				);
		   $data['sellerareas'] = array_merge($all_area,$sellerareas);		   
		$newheader = $this->load->view('extension/module/beforeheader', $data);
		 $output = str_replace('</head>', '</head>'.$newheader, $output);

	}

	}
}
?>