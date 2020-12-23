<?php
class ControllerExtensionAccountPurpletreeMultivendorVacationcron extends Controller {
		private $error = array();
		public function index() {
			$this->load->model('extension/purpletree_multivendor/vendor');
			//Store Enable
			if($this->config->get('module_purpletree_multivendor_status')==1){
			date_default_timezone_set('Asia/Calcutta');
			$current_day_name = date("l");
			$current_date = date('Y-m-d');
			$current_time = date('H:i:s');
			$store_id = $this->model_extension_purpletree_multivendor_vendor->getStoreId($this->customer->getId());
                if($current_day_name == 'Sunday'){
			       $day_id = 1;
				}elseif($current_day_name == 'Monday'){
				   $day_id = 2;
				}elseif($current_day_name == 'Tuesday'){
				   $day_id = 3;
				}elseif($current_day_name == 'Wednesday'){
				   $day_id = 4;
				}elseif($current_day_name == 'Thursday'){
				   $day_id = 5;
				}elseif($current_day_name == 'Friday'){
				   $day_id = 6;
				}elseif($current_day_name == 'Saturday'){
				   $day_id = 7;
				}else{
				   $day_id = '';
				}
				$store_holiday = array();
				$store_holiday = $this->model_extension_purpletree_multivendor_vendor->getStoreHoliday($store_id);
				$productstts = array();
			if(!empty($store_holiday)){
			foreach ($store_holiday as $key => $value){
			   if($value['date'] == $current_date){
			   $status = 1;
			   $this->model_extension_purpletree_multivendor_vendor->updateVacation($store_id,$status);
			        $productstts = $this->model_extension_purpletree_multivendor_vendor->getSellerProduct($this->customer->getId());
				if($productstts){
				foreach ($productstts as $productstt) {
					$this->model_extension_purpletree_multivendor_vendor->updateVacationProduct($productstt['product_id'],$productstt['status'],$this->customer->getId());
					
					$this->model_extension_purpletree_multivendor_vendor->updateProductAccrVacation($productstt['product_id']);
				}
				}
				   }else{
				   $status = 0;
			       $this->model_extension_purpletree_multivendor_vendor->updateVacation($store_id,$status);
				    $productsttss = $this->model_extension_purpletree_multivendor_vendor->getSellerProductBystatus($this->customer->getId());
				if($productsttss){
				foreach ($productsttss as $productstts) {
					$this->model_extension_purpletree_multivendor_vendor->updateProductAccrVacationn($productstts['product_id']);
				}
				}
				$this->model_extension_purpletree_multivendor_vendor->updateVacationProductByOff($this->customer->getId()); 
				   }
			
			}
			}else{
			  $store_time = $this->model_extension_purpletree_multivendor_vendor->getStoreTimeByDay($store_id,$day_id); 
			$store_time  = array();
			$store_time = $this->model_extension_purpletree_multivendor_vendor->getStoreTimeByDay($store_id,$day_id); 
			if(!empty($store_time)){
			foreach ($store_time as $key => $value) {
			  if(($value['open_time'] < $current_time)&&($value['close_time'] > $current_time)){
			    $status = 1;
			   $this->model_extension_purpletree_multivendor_vendor->updateVacation($store_id,$status);
			    $productstts = $this->model_extension_purpletree_multivendor_vendor->getSellerProduct($this->customer->getId());
				if($productstts){
				foreach ($productstts as $productstt) {
					$this->model_extension_purpletree_multivendor_vendor->updateVacationProduct($productstt['product_id'],$productstt['status'],$this->customer->getId());
					
					$this->model_extension_purpletree_multivendor_vendor->updateProductAccrVacation($productstt['product_id']);
				}
				}
			}else{
			   $status = 0;
			   $this->model_extension_purpletree_multivendor_vendor->updateVacation($store_id,$status);
			  $productsttss = $this->model_extension_purpletree_multivendor_vendor->getSellerProductBystatus($this->customer->getId());
				if($productsttss){
				foreach ($productsttss as $productstts) {
					$this->model_extension_purpletree_multivendor_vendor->updateProductAccrVacationn($productstts['product_id']);
				}
				}
				$this->model_extension_purpletree_multivendor_vendor->updateVacationProductByOff($this->customer->getId());   
			}
			}
			 
			}
			}		
			
			}
		$logger = new Log('error.log'); 
        $logger->write("Vacation cron check on ". date('Y-m-d'));	
		}
		
}
?>