<?php
class ModelExtensionPurpletreeMultivendorVendor extends Model{
		///seller area /////
	public function getSellerAreass($data = array()) {			
			
			$sql="SELECT pva.*,pvad.area_name AS name FROM ". DB_PREFIX ."purpletree_vendor_area pva LEFT JOIN ". DB_PREFIX ."purpletree_vendor_area_discaription pvad ON (pva.area_id=pvad.area_id) WHERE pvad.language_id='".(int)$this->config->get('config_language_id') ."' AND pva.status = 1";
			
		if (!empty($data['filter_name'])) {
			$sql .= " AND pvad.area_name LIKE '%" . $this->db->escape($data['filter_name']) . "%'";
		}

		$sql .= " GROUP BY pva.area_id";

		$sort_data = array(
			'name',
			'sort_order'
		);

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];
		} else {
			$sql .= " ORDER BY sort_order";
		}

		if (isset($data['order']) && ($data['order'] == 'DESC')) {
			$sql .= " DESC";
		} else {
			$sql .= " ASC";
		}

		if (isset($data['start']) || isset($data['limit'])) {
			if ($data['start'] < 0) {
				$data['start'] = 0;
			}

			if ($data['limit'] < 1) {
				$data['limit'] = 20;
			}

			$sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
		}
			
			$query = $this->db->query($sql);
			
			return $query->rows;
		}
	public function getSellerAreaByID($area_id) {			
			
			$sql="SELECT pva.*,pvad.area_name AS name FROM ". DB_PREFIX ."purpletree_vendor_area pva LEFT JOIN ". DB_PREFIX ."purpletree_vendor_area_discaription pvad ON (pva.area_id=pvad.area_id) WHERE pvad.language_id='".(int)$this->config->get('config_language_id') ."' AND pva.status = 1 AND pva.area_id = '".(int)$area_id ."'";
			
		
			
			$query = $this->db->query($sql);
			
			return $query->row;
		}
	public function getSellerAreasName($area_id) {			
			
			$sql="SELECT pva.*,pvad.area_name FROM ". DB_PREFIX ."purpletree_vendor_area pva LEFT JOIN ". DB_PREFIX ."purpletree_vendor_area_discaription pvad ON (pva.area_id=pvad.area_id) WHERE pvad.language_id='".(int)$this->config->get('config_language_id') ."' AND pva.area_id='".(int)$area_id."'";			
			$query = $this->db->query($sql);
			if ($query->num_rows) {
				return $query->row['area_name'];
			}			
		}
	 public function getSellerAreas() {			
			
			$sql="SELECT pva.*,pvad.area_name FROM ". DB_PREFIX ."purpletree_vendor_area pva LEFT JOIN ". DB_PREFIX ."purpletree_vendor_area_discaription pvad ON (pva.area_id=pvad.area_id) WHERE pvad.language_id='".(int)$this->config->get('config_language_id') ."'  AND pva.status=1";
			
			
			$query = $this->db->query($sql);
			
			return $query->rows;
		}
	public function getAllSellerInfo() {
			$query = $this->db->query("SELECT pvs.*,c.firstname,c.lastname FROM " . DB_PREFIX . "purpletree_vendor_stores pvs JOIN " . DB_PREFIX . "customer c ON(c.customer_id = pvs.seller_id) where store_status = 1");
			if ($query->num_rows) {
				return $query->rows;
			}
	}	
	/// End seller area ///
	///  vacation
	public function getsellerInfo() {
			$query = $this->db->query("SELECT pvs.*,c.firstname,c.lastname FROM " . DB_PREFIX . "purpletree_vendor_stores pvs JOIN " . DB_PREFIX . "customer c ON(c.customer_id = pvs.seller_id) where seller_id='".$this->customer->getId()."' AND store_status = 1");
			if ($query->num_rows) {
				return $query->row;
			}
	}
	public function updateVacation($store_id,$status) {
		   $query = $this->db->query("UPDATE " . DB_PREFIX . "purpletree_vendor_stores SET vacation = '" . (int)$status . "' WHERE id = '" . (int)$store_id . "'");
			}
		public function getStoreHoliday($store_id) {
		   $query = $this->db->query("SELECT *  FROM " . DB_PREFIX . "purpletree_vendor_holiday WHERE store_id = '" . (int)$store_id . "'");
			if ($query->num_rows > 0) {			 
				return $query->rows;
				} else {	
				return NULL;
			}
			}
		public function getStoreTimeByDay($store_id,$day_id) {
			$query = $this->db->query("SELECT *  FROM " . DB_PREFIX . "purpletree_vendor_store_time WHERE store_id = '" . (int)$store_id . "' AND day_id = '" . (int)$day_id . "'");
			if ($query->num_rows > 0) {			 
				return $query->rows;
				} else {	
				return NULL;
			}
			}	
		public function getStoreTime($store_id) {
			$query = $this->db->query("SELECT *  FROM " . DB_PREFIX . "purpletree_vendor_store_time WHERE store_id = '" . (int)$store_id . "'");
			if ($query->num_rows > 0) {			 
				return $query->rows;
				} else {	
				return NULL;
			}
			}
		public function storeTime($store_id,$data = array()) {
			$query = $this->db->query("SELECT count(id) AS total FROM " . DB_PREFIX . "purpletree_vendor_store_time WHERE store_id = '" . (int)$store_id . "'");
			if ($query->row['total']  > 0) {
			//update
			if(isset($data['store_timing'])) {
			   foreach ($data['store_timing'] as $day_id => $value) {
			   if($day_id == 1){
			       $day_name = 'Sunday';
				}elseif($day_id == 2){
				   $day_name = 'Monday';
				}elseif($day_id == 3){
				   $day_name = 'Tuesday';
				}elseif($day_id == 4){
				   $day_name = 'Wednesday';
				}elseif($day_id == 5){
				   $day_name = 'Thursday';
				}elseif($day_id == 6){
				   $day_name = 'Friday';
				}elseif($day_id == 7){
				   $day_name = 'Saturday';
				}else{
				   $day_name = '';
				}			   
			   $this->db->query("UPDATE " . DB_PREFIX . "purpletree_vendor_store_time SET day_name = '" . $this->db->escape($day_name) . "',open_time = '" . $value['open'] . "', close_time = '" . $value['close'] . "' WHERE day_id = '" . (int)$day_id . "' AND store_id = '" . (int)$store_id . "'");
				   }
			}
				
				} else {	
				// insert
				if(isset($data['store_timing'])) {
				foreach ($data['store_timing'] as $day_id => $value) {
				    if($day_id == 1){
			       $day_name = 'Sunday';
				}elseif($day_id == 2){
				   $day_name = 'Monday';
				}elseif($day_id == 3){
				   $day_name = 'Tuesday';
				}elseif($day_id == 4){
				   $day_name = 'Wednesday';
				}elseif($day_id == 5){
				   $day_name = 'Thursday';
				}elseif($day_id == 6){
				   $day_name = 'Friday';
				}elseif($day_id == 7){
				   $day_name = 'Saturday';
				}else{
				   $day_name = '';
				}			   
				    $this->db->query("INSERT INTO " . DB_PREFIX . "purpletree_vendor_store_time SET day_id = '" . (int)$day_id . "',store_id = '" . (int)$store_id . "', day_name = '" . $this->db->escape($day_name) . "', open_time = '" . $value['open'] . "', close_time	 = '" . $value['close'] . "'");
				   }
				}
			}
			}
		public function addHoliday($store_id,$data = array()) {
		    // delete
		    $this->db->query("DELETE FROM " . DB_PREFIX . "purpletree_vendor_holiday WHERE store_id = '" . (int)$store_id . "'");
			if(isset($data['input-date-holiday'])) {
		    foreach ($data['input-date-holiday'] as $key => $value) {
			   
			// Insert
			   $this->db->query("INSERT INTO " . DB_PREFIX . "purpletree_vendor_holiday SET store_id = '" . (int)$store_id . "',date = '" . $value . "'");
		     }	
			}			 
			}
		/// vacation
	////quick order////
	public function getCountryName($country_id) {
		$query = $this->db->query("SELECT name FROM ". DB_PREFIX . "country WHERE country_id ='". (int)$country_id ."'");
			if($query->num_rows) {			
				 return $query->row['name'];
			 }else{
				 return null;
			 }
		}
	public function getStateName($state_id,$country_id) {
		$query = $this->db->query("SELECT name FROM ". DB_PREFIX . "zone WHERE zone_id ='". (int)$state_id ."'AND country_id ='". (int)$country_id ."'");
			if($query->num_rows) {			
				 return $query->row['name'];
			 }else{
				 return null;
			 }
		}
	////end quick order ////
		public function isSeller($customer_id){
			if ($this->config->get('module_purpletree_multivendor_status')) {
				$query = $this->db->query("SELECT id, multi_store_id,store_status, is_removed FROM " . DB_PREFIX . "purpletree_vendor_stores where seller_id='".$customer_id."'");
				if($query->num_rows) {
					return $query->row;
				}
			}
			}
		
		public function addSeller($customer_id,$store_name,$filename = ''){
			$this->db->query("INSERT into " . DB_PREFIX . "purpletree_vendor_stores SET seller_id ='".(int)$customer_id."', store_name='".$this->db->escape(trim($store_name))."', multi_store_id='".(int)($this->config->get('config_store_id'))."',store_status='".(int)(!$this->config->get('module_purpletree_multivendor_seller_approval'))."', store_created_at= NOW(), store_updated_at= NOW()");
			return $this->db->getLastId();
		}
		public function getStoreId($sellerid){
			$query = $this->db->query("SELECT id FROM " . DB_PREFIX . "purpletree_vendor_stores WHERE seller_id='". (int)$sellerid."'");
			if ($query->num_rows > 0) {
				return $query->row['id'];
			}	
			return '';
		}
		public function becomeSeller($customer_id,$store_name,$filename = ''){
			if($store_name['become_seller']){
				$this->db->query("INSERT into " . DB_PREFIX . "purpletree_vendor_stores SET seller_id ='".(int)$customer_id."', store_name='".$this->db->escape(trim($store_name['seller_storename']))."', store_status='".(int)(!$this->config->get('module_purpletree_multivendor_seller_approval'))."', store_created_at= NOW(), store_updated_at= NOW()");
				$store_id = $this->db->getLastId();
			}
			else {
				$store_id = 0;
			}
			return $store_id;
			
		}
		
		public function reseller($customer_id,$store_name){
			if($store_name['become_seller']){	
				$this->db->query("UPDATE " . DB_PREFIX . "purpletree_vendor_stores SET store_status='".(int)(!$this->config->get('module_purpletree_multivendor_seller_approval'))."', is_removed=0 WHERE seller_id='".(int)$customer_id."'");
				$store_id = 1;
			}
			else {
				$store_id = 0;
			}
			return $store_id;
			
		}
		
		public function getSellerStorename($store_name){
			$query = $this->db->query("SELECT id FROM " . DB_PREFIX . "purpletree_vendor_stores where store_name='".$this->db->escape($store_name)."'");
			return $query->num_rows;
		}
		
		public function getStoreRating($seller_id){
			$query = $this->db->query("SELECT AVG(rating) as rating,count(*) as count FROM " . DB_PREFIX . "purpletree_vendor_reviews where seller_id='".(int)$seller_id."' AND status=1");
			return $query->row;
		}
		
		public function getStore($store_id){
			$query = $this->db->query("SELECT pvs.*,CONCAT(c.firstname, ' ', c.lastname) AS seller_name, (SELECT keyword FROM " . DB_PREFIX . "seo_url WHERE query = 'seller_store_id=" . (int)$store_id . "') AS store_seo FROM " . DB_PREFIX . "purpletree_vendor_stores pvs JOIN " . DB_PREFIX . "customer c ON(c.customer_id = pvs.seller_id) where pvs.id='".(int)$store_id."'");
			return $query->row;
		} 
		
		public function getStoreDetail($customer_id){
			$query = $this->db->query("SELECT pvs.* FROM " . DB_PREFIX . "purpletree_vendor_stores pvs where pvs.seller_id='".(int)$customer_id."'");
			return $query->row;
		}
		
		public function editStoreImage($store_id,$store_logo = '',$store_banner = ''){	
			$this->db->query("UPDATE " . DB_PREFIX. "purpletree_vendor_stores SET  store_logo='".$this->db->escape($store_logo)."', store_banner='".$this->db->escape($store_banner)."',store_updated_at=NOW() where id='".(int)$store_id."'");
		}
		public function editStore($store_id,$data,$file = ''){
	       $dcument = "";
			if($file != '') {
				$dcument = ",document='".$file."'";
			}
			$store_live_chat_enable = "";
			$store_live_chat_code = "";
			if(isset($data['store_live_chat_enable'])) {
				$store_live_chat_enable = ", store_live_chat_enable=". $data['store_live_chat_enable'];
			}
			if(isset($data['store_live_chat_code'])) {
				$store_live_chat_code = ', store_live_chat_code="'. $data['store_live_chat_code'].'"';
			}
			if(!isset($data['store_name'])) {
				$data['store_name'] = '';
				}if(!isset($data['store_logo'])) {
				$data['store_logo'] = '';
				}if(!isset($data['store_email'])) {
				$data['store_email'] = '';
				}if(!isset($data['store_phone'])) {
				$data['store_phone'] = '';
				}if(!isset($data['store_banner'])) {
				$data['store_banner'] = '';
				}if(!isset($data['store_address'])) {
				$data['store_address'] = '';
				}if(!isset($data['store_city'])) {
				$data['store_city'] = '';
				}if(!isset($data['store_country'])) {
				$data['store_country'] = '';
				}if(!isset($data['store_state'])) {
				$data['store_state'] = '';
				}if(!isset($data['store_meta_keywords'])) {
				$data['store_meta_keywords'] = '';
				}if(!isset($data['store_meta_description'])) {
				$data['store_meta_description'] = '';
				}if(!isset($data['store_bank_details'])) {
				$data['store_bank_details'] = '';
				}if(!isset($data['store_shipping_type'])) {
				$data['store_shipping_type'] = '';
				}if(!isset($data['store_shipping_order_type'])) {
				$data['store_shipping_order_type'] = '';
				}if(!isset($data['store_shipping_charge'])) {
				$data['store_shipping_charge'] = '';
			}
			if($data['store_shipping_charge'] == '') {
				$store_shipping_charge ='NULL';
				} else {
				$store_shipping_charge = $this->db->escape($data['store_shipping_charge']);
			}
			if(!isset($data['store_description'])) {
				$data['store_description'] = '';
			}
			if(!isset($data['facebook_link'])) {
				$data['facebook_link'] = '';
			}
			if(!isset($data['google_link'])) {
				$data['google_link'] = '';
			}
			if(!isset($data['instagram_link'])) {
				$data['instagram_link'] = '';
			}
			if(!isset($data['twitter_link'])) {
				$data['twitter_link'] = '';
			}
			if(!isset($data['pinterest_link'])) {
				$data['pinterest_link'] = '';
			}		
			if(!isset($data['wesbsite_link'])) {
				$data['wesbsite_link'] = '';
			} 	
			if(!isset($data['whatsapp_link'])) {
				$data['whatsapp_link'] = '';
			} 		
			if(!isset($data['store_video'])) {
				$data['store_video'] = '';
			}
			if(!isset($data['store_image'])) {
				$data['store_image'] = '';
			}
			if(!isset($data['store_timings'])) {
				$data['store_timings'] = '';
			}
			if(!isset($data['google_map'])) {
				$data['google_map'] = '';
			}
			if(!isset($data['google_map_link'])) {
				$data['google_map_link'] = '';
			}
			if(!isset($data['vacation'])) {
				$data['vacation'] = 0;
			}
			if(!isset($data['store_shipping_policy'])) {
				$data['store_shipping_policy'] = '';
			}
			if(!isset($data['store_tin'])) {
				$data['store_tin'] = '';
			}
			if(!isset($data['seller_paypal_id'])) {
				$data['seller_paypal_id'] = '';
			}
			if(!isset($data['store_return_policy'])) {
				$data['store_return_policy'] = '';
			}
			if(!isset($data['store_zipcode'])) {
				$data['store_zipcode'] = '';
			}	
			if(empty($data['seller_area'])){
				$data['seller_area'] = "";
			}
			$this->db->query("UPDATE " . DB_PREFIX. "purpletree_vendor_stores SET store_name='".$this->db->escape(trim($data['store_name']))."', store_logo='".$this->db->escape($data['store_logo'])."', store_email='".$this->db->escape($data['store_email'])."', store_phone='".$this->db->escape($data['store_phone'])."', store_image='".$this->db->escape($data['store_image'])."', store_banner='".$this->db->escape($data['store_banner'])."', store_description='".$this->db->escape($data['store_description'])."'".$dcument.$store_live_chat_enable.$store_live_chat_code." , store_address='".$this->db->escape($data['store_address'])."',store_timings='".$this->db->escape($data['store_timings'])."',google_map='".$this->db->escape($data['google_map'])."',google_map_link='".$this->db->escape($data['google_map_link'])."',store_video='".$this->db->escape($data['store_video'])."', store_city='".$this->db->escape($data['store_city'])."',store_country='".(int)$data['store_country']."', store_state='".(int)$data['store_state']."',vacation='".(int)$data['vacation']."',store_state='".(int)$data['store_state']."', store_zipcode='".$this->db->escape($data['store_zipcode'])."',store_area='".$this->db->escape($data['seller_area'])."', store_shipping_policy='".$this->db->escape($data['store_shipping_policy'])."', store_return_policy='".$this->db->escape($data['store_return_policy'])."', store_meta_keywords='".$this->db->escape($data['store_meta_keywords'])."', store_meta_descriptions='".$this->db->escape($data['store_meta_description'])."', store_bank_details='".$this->db->escape($data['store_bank_details'])."', store_tin='".$this->db->escape($data['store_tin'])."', store_shipping_type ='".$this->db->escape($data['store_shipping_type'])."',store_shipping_order_type ='".$this->db->escape($data['store_shipping_order_type'])."',store_shipping_charge =".$store_shipping_charge.",seller_paypal_id ='".$this->db->escape($data['seller_paypal_id'])."',store_updated_at=NOW() where id='".(int)$store_id."'");
		
			$seller_id = $this->customer->getId();
			$this->db->query("UPDATE " . DB_PREFIX . "customer SET custom_field = '" . $this->db->escape(isset($data['custom_field']) ? json_encode($data['custom_field']) : json_encode(array())) . "' WHERE customer_id = '" . (int)$seller_id . "'");
			
			$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "purpletree_vendor_social_links     WHERE store_id = " . (int)$store_id . "");
			if($query->num_rows > 0){
				$this->db->query("UPDATE " . DB_PREFIX . "purpletree_vendor_social_links SET store_id = '" . (int)$store_id ."', facebook_link ='".$this->db->escape($data['facebook_link']). "', google_link ='".$this->db->escape($data['google_link']). "',instagram_link ='".$this->db->escape($data['instagram_link'])."', twitter_link ='".$this->db->escape($data['twitter_link'])."', pinterest_link ='".$this->db->escape($data['pinterest_link'])."', wesbsite_link ='".$this->db->escape($data['wesbsite_link']). "',  whatsapp_link ='".$this->db->escape($data['whatsapp_link']). "' where store_id ='".(int)$store_id."'");
				}else{ 
				$this->db->query("INSERT INTO " . DB_PREFIX . "purpletree_vendor_social_links SET store_id = '" . (int)$store_id ."', facebook_link ='".$this->db->escape($data['facebook_link']). "', google_link ='".$this->db->escape($data['google_link']). "',instagram_link ='".$this->db->escape($data['instagram_link'])."', twitter_link ='".$this->db->escape($data['twitter_link'])."', pinterest_link ='".$this->db->escape($data['pinterest_link'])."', wesbsite_link ='".$this->db->escape($data['wesbsite_link']). "',  whatsapp_link ='".$this->db->escape($data['whatsapp_link']). "'");
				
			}
			
			if ($data['store_seo']) {
				$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "seo_url WHERE query = 'seller_store_id=" . (int)$store_id . "'");
				if($query->num_rows > 0){
					$row = $query->row;
					$this->db->query("UPDATE " . DB_PREFIX . "seo_url SET query = 'seller_store_id=" . (int)$store_id . "', language_id = '0', keyword = '".$this->db->escape($data['store_seo']) . "' WHERE seo_url_id=".$row['seo_url_id']);
					} else{
					if(VERSION=='3.1.0.0_b'){
						$push='route=extension/account/purpletree_multivendor/sellerstore/storeview&seller_store_id='.(int)$store_id;	
						$this->db->query("INSERT INTO " . DB_PREFIX . "seo_url SET query = 'seller_store_id=" . (int)$store_id . "', language_id = '1', keyword = '" . $this->db->escape($data['store_seo']) . "', push='".$push."'");
						}else {
						$this->db->query("INSERT INTO " . DB_PREFIX . "seo_url SET query = 'seller_store_id=" . (int)$store_id . "', language_id = '0', keyword = '" . $this->db->escape($data['store_seo']) . "'");
					}
				}
				}else {
				$this->db->query("DELETE FROM " . DB_PREFIX . "seo_url WHERE query = 'seller_store_id=" . (int)$store_id ."'");
			}
		}
		public function getStoreByEmail($email) {
			$query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "purpletree_vendor_stores WHERE LCASE(store_email) = '" . $this->db->escape(utf8_strtolower($email)) . "'");
			
			return $query->row;
			
		}
		
		public function getStoreSeo($seo_url) {
			
			$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "seo_url WHERE keyword = '".$this->db->escape($seo_url) . "'");
			return $query->row;
		}
		
		public function removeSeller($seller_id){
			$this->db->query("UPDATE " . DB_PREFIX . "purpletree_vendor_products pvp JOIN " . DB_PREFIX . "product p ON(p.product_id=pvp.product_id) SET p.status=0 WHERE pvp.seller_id='".(int)$seller_id."'");
			
			$this->db->query("UPDATE " . DB_PREFIX . "purpletree_vendor_stores SET store_status=0, is_removed=1 WHERE seller_id='".(int)$seller_id."'");
		}
		public function getStoreNameByStoreName($store_name2){
			$sql = "SELECT pvs.id ,pvs.seller_id ,pvs.store_name,c.status FROM " . DB_PREFIX . "purpletree_vendor_stores pvs LEFT JOIN ". DB_PREFIX ."customer c ON(pvs.seller_id = c.customer_id) WHERE pvs.store_name = '" . $this->db->escape(trim($store_name2)) . "' AND c.status=1";
			$query = $this->db->query($sql);    
			return $query->row;	
		}
		public function getStoreSocial($store_id){
			$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "purpletree_vendor_social_links pvsl where pvsl.store_id='".(int)$store_id."'");
			if($query->num_rows) {
				return $query->row;
			} 
		}
		public function getStoreByIdd($sellerid,$email_id){
			$query = $this->db->query("SELECT count(*) AS num_row FROM " . DB_PREFIX . "purpletree_vendor_stores WHERE seller_id !='". (int)$sellerid."' AND store_email='".$email_id."'");
			if ($query->num_rows > 0) {
				return $query->row['num_row'];
				} else {	
				return NULL;
			}
		}
		public function getStoreById($sellerid){
			$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "purpletree_vendor_stores WHERE seller_id='". (int)$sellerid."'");
			if ($query->num_rows > 0) {
				return $query->row;
			}	
			return '';
		}
		public function getCustomerEmailId($seller_id) {
			
			$query = $this->db->query("SELECT email  FROM " . DB_PREFIX . "customer WHERE customer_id = '" . (int)$seller_id . "'");
			if($query->num_rows>0){
				return $query->row['email'];
				}else {
				return NULL;
			}
		}		
		public function getSellerProduct($seller_id) {
			$query = $this->db->query("SELECT pvp.product_id,p.status FROM " . DB_PREFIX . "product p LEFT JOIN ". DB_PREFIX ."purpletree_vendor_products pvp ON(p.product_id = pvp.product_id) WHERE pvp.seller_id = '" . (int)$seller_id . "'");
			if($query->num_rows>0){
				return $query->rows;
			}
		}	
		public function getSellerProductBystatus($seller_id) {
			$query = $this->db->query("SELECT pvp.product_id,p.status FROM " . DB_PREFIX . "product p LEFT JOIN ". DB_PREFIX ."purpletree_vendor_products pvp ON(p.product_id = pvp.product_id) WHERE pvp.seller_id = '" . (int)$seller_id . "' AND pvp.vacation=1 ");
			if($query->num_rows>0){
				return $query->rows;
			}
		}
		public function updateVacationProduct($product_id,$status,$seller_id){
			if($status==1){	
				$this->db->query("UPDATE " . DB_PREFIX . "purpletree_vendor_products SET vacation=1 WHERE seller_id='".(int)$seller_id."' AND product_id='".(int)$product_id."'");
				}elseif($status==0){
				$this->db->query("UPDATE " . DB_PREFIX . "purpletree_vendor_products SET vacation=2 WHERE seller_id='".(int)$seller_id."' AND product_id='".(int)$product_id."'");
				}else{
				return NULL;
			}
		}	
		public function updateVacationProductByOff($seller_id){
			$this->db->query("UPDATE " . DB_PREFIX . "purpletree_vendor_products SET vacation=0 WHERE seller_id='".(int)$seller_id."'");
		}	
		public function updateProductAccrVacation($product_id){
			$this->db->query("UPDATE " . DB_PREFIX . "product SET status=0 WHERE product_id='".(int)$product_id."'");
		}	
		public function updateProductAccrVacationn($product_id){
			$this->db->query("UPDATE " . DB_PREFIX . "product SET status=1 WHERE product_id='".(int)$product_id."'");
		}	
		public function checkSellerVacation($store_id){
			$query = $this->db->query("SELECT count(id) AS num_row FROM " . DB_PREFIX . "purpletree_vendor_stores WHERE id = '" . (int)$store_id . "' AND vacation = 1");
			if ($query->num_rows > 0) {
				return $query->row['num_row'];
				} else {	
				return NULL;
			}
		}
		public function getAssingedCategories() {
			$seller_id = $this->customer->getId();
			$sql = "SELECT * FROM " . DB_PREFIX . "category_description cd LEFT JOIN " . DB_PREFIX . "purpletree_vendor_allowed_category pvac ON (pvac.category_id = cd.category_id) WHERE cd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND pvac.seller_id = '" . (int)$seller_id . "' ORDER BY	cd.name";
			
			$query = $this->db->query($sql);
			
			if($query->num_rows > 0){
				return $query->rows;
			}
		}
		public function getSelleRegisterEmailTemplate($email_code) {
			
			$sql1="SELECT lu.code FROM ". DB_PREFIX ."language lu WHERE lu.language_id='".(int)$this->config->get('config_language_id') ."'";
			
			$query2 = $this->db->query($sql1);
			$language_code = $query2->row['code'];	
			
			$query3 = $this->db->query("SELECT count(*) AS total FROM ". DB_PREFIX ."purpletree_vendor_email pve WHERE pve.language_code='".  $this->db->escape($language_code) ."' AND pve.email_code = '".$this->db->escape($email_code)."'");
			if ($query3->row['total'] > 0) {
				   $language_code = $query2->row['code'];	
				} else {	
				  $language_code = 'en-gb';
			}
			$sql="SELECT pve.id,pve.title,pve.new_subject, pve.new_message FROM ". DB_PREFIX ."purpletree_vendor_email pve WHERE pve.language_code='".  $this->db->escape($language_code) ."' AND pve.email_code = '".$this->db->escape($email_code)."'";			
			
			$query = $this->db->query($sql);			
			return $query->row;
		}
		public function getmsgfromarray($replacevar = array(),$templatefromdb){		
			foreach($replacevar as $key => $val) {
			
			$templatefromdb = str_replace($key,$val,$templatefromdb);
			}
			
			return $templatefromdb;
		}
		public function ptsSendMail($reciver,$subject,$message){
			$mail = new Mail($this->config->get('config_mail_engine'));
			$mail->parameter = $this->config->get('config_mail_parameter');
			$mail->smtp_hostname = $this->config->get('config_mail_smtp_hostname');
			$mail->smtp_username = $this->config->get('config_mail_smtp_username');
			$mail->smtp_password = html_entity_decode($this->config->get('config_mail_smtp_password'), ENT_QUOTES, 'UTF-8');
			$mail->smtp_port = $this->config->get('config_mail_smtp_port');
			$mail->smtp_timeout = $this->config->get('config_mail_smtp_timeout');
			$mail->setTo($reciver);
			$mail->setFrom($this->config->get('config_email'));
			$mail->setSender(html_entity_decode($this->config->get('config_name'), ENT_QUOTES, 'UTF-8'));
			$mail->setSubject(html_entity_decode($subject));
			$mail->setHtml(html_entity_decode($message));
			$mail->send();
		}
	public function getCustomFieldValues($custom_field_id) {
		$custom_field_value_data = array();

		$custom_field_value_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "custom_field_value cfv LEFT JOIN " . DB_PREFIX . "custom_field_value_description cfvd ON (cfv.custom_field_value_id = cfvd.custom_field_value_id) WHERE cfv.custom_field_id = '" . (int)$custom_field_id . "' AND cfvd.language_id = '" . (int)$this->config->get('config_language_id') . "' ORDER BY cfv.sort_order ASC");

		foreach ($custom_field_value_query->rows as $custom_field_value) {
			$custom_field_value_data[$custom_field_value['custom_field_value_id']] = array(
				'custom_field_value_id' => $custom_field_value['custom_field_value_id'],
				'name'                  => $custom_field_value['name']
			);
		}

		return $custom_field_value_data;
	}
	public function getCustomer($customer_id) {
		$query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "customer WHERE customer_id = '" . (int)$customer_id . "'");

		return $query->row;
	}
	public function getCustomFieldsForSeller($data = array()) {
		if (empty($data['filter_customer_group_id'])) {
			$sql = "SELECT * FROM `" . DB_PREFIX . "custom_field` cf INNER JOIN " . DB_PREFIX . "purpletree_vendor_customfield pvc ON (pvc.custom_field_id = cf.custom_field_id) LEFT JOIN " . DB_PREFIX . "custom_field_description cfd ON (cf.custom_field_id = cfd.custom_field_id) WHERE cfd.language_id = '" . (int)$this->config->get('config_language_id') . "'";
		} else {
			$sql = "SELECT * FROM " . DB_PREFIX . "custom_field_customer_group cfcg INNER JOIN " . DB_PREFIX . "purpletree_vendor_customfield pvc ON (pvc.custom_field_id = cf.custom_field_id) LEFT JOIN `" . DB_PREFIX . "custom_field` cf ON (cfcg.custom_field_id = cf.custom_field_id) LEFT JOIN " . DB_PREFIX . "custom_field_description cfd ON (cf.custom_field_id = cfd.custom_field_id) WHERE cfd.language_id = '" . (int)$this->config->get('config_language_id') . "'";
		}

		if (!empty($data['filter_name'])) {
			$sql .= " AND cfd.name LIKE '" . $this->db->escape($data['filter_name']) . "%'";
		}

		if (!empty($data['filter_customer_group_id'])) {
			$sql .= " AND cfcg.customer_group_id = '" . (int)$data['filter_customer_group_id'] . "'";
		}

		$sort_data = array(
			'cfd.name',
			'cf.type',
			'cf.location',
			'cf.status',
			'cf.sort_order'
		);

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];
		} else {
			$sql .= " ORDER BY cfd.name";
		}

		if (isset($data['order']) && ($data['order'] == 'DESC')) {
			$sql .= " DESC";
		} else {
			$sql .= " ASC";
		}

		if (isset($data['start']) || isset($data['limit'])) {
			if ($data['start'] < 0) {
				$data['start'] = 0;
			}

			if ($data['limit'] < 1) {
				$data['limit'] = 20;
			}

			$sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
		}

		$query = $this->db->query($sql);

		return $query->rows;
	}
	public function getCustomField($custom_field_id) {
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "custom_field` cf LEFT JOIN `" . DB_PREFIX . "custom_field_description` cfd ON (cf.custom_field_id = cfd.custom_field_id) INNER JOIN `" . DB_PREFIX . "purpletree_vendor_customfield` pvc ON (pvc.custom_field_id = cf.custom_field_id) WHERE cf.status = '1' AND cf.custom_field_id = '" . (int)$custom_field_id . "' AND cfd.language_id = '" . (int)$this->config->get('config_language_id') . "'");

		return $query->row;
	}

	public function getCustomFields($customer_group_id = 1) {
		$custom_field_data = array();

		if (!$customer_group_id) {
			$custom_field_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "custom_field` cf LEFT JOIN `" . DB_PREFIX . "custom_field_description` cfd ON (cf.custom_field_id = cfd.custom_field_id) INNER JOIN `" . DB_PREFIX . "purpletree_vendor_customfield` pvc ON (cf.custom_field_id = pvc.custom_field_id) WHERE cf.status = '1' AND cfd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND cf.status = '1' ORDER BY cf.sort_order ASC");
		} else {
			$custom_field_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "custom_field_customer_group` cfcg LEFT JOIN `" . DB_PREFIX . "custom_field` cf ON (cfcg.custom_field_id = cf.custom_field_id) INNER JOIN `" . DB_PREFIX . "purpletree_vendor_customfield` pvc ON (cf.custom_field_id = pvc.custom_field_id) LEFT JOIN `" . DB_PREFIX . "custom_field_description` cfd ON (cf.custom_field_id = cfd.custom_field_id) WHERE cf.status = '1' AND cfd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND cfcg.customer_group_id = '" . (int)$customer_group_id . "' ORDER BY cf.sort_order ASC");
		}

		foreach ($custom_field_query->rows as $custom_field) {
			$custom_field_value_data = array();

			if ($custom_field['type'] == 'select' || $custom_field['type'] == 'radio' || $custom_field['type'] == 'checkbox') {
				$custom_field_value_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "custom_field_value cfv LEFT JOIN " . DB_PREFIX . "custom_field_value_description cfvd ON (cfv.custom_field_value_id = cfvd.custom_field_value_id) INNER JOIN " . DB_PREFIX . "purpletree_vendor_customfield pvc ON (pvc.custom_field_id = cfvd.custom_field_id) WHERE cfv.custom_field_id = '" . (int)$custom_field['custom_field_id'] . "' AND cfvd.language_id = '" . (int)$this->config->get('config_language_id') . "' ORDER BY cfv.sort_order ASC");

				foreach ($custom_field_value_query->rows as $custom_field_value) {
					$custom_field_value_data[] = array(
						'custom_field_value_id' => $custom_field_value['custom_field_value_id'],
						'name'                  => $custom_field_value['name']
					);
				}
			}

			$custom_field_data[] = array(
				'custom_field_id'    => $custom_field['custom_field_id'],
				'custom_field_value' => $custom_field_value_data,
				'name'               => $custom_field['name'],
				'type'               => $custom_field['type'],
				'value'              => $custom_field['value'],
				'validation'         => $custom_field['validation'],
				'location'           => $custom_field['location'],
				'step_location'      => $custom_field['step_location'],
				'required'           => empty($custom_field['required']) || $custom_field['required'] == 0 ? false : true,
				'sort_order'         => $custom_field['sort_order']
			);
		}

		return $custom_field_data;
	}
	public function getcustomsellerfiled($custom_field_id) {
		$query = $this->db->query("SELECT id FROM `" . DB_PREFIX . "purpletree_vendor_customfield` WHERE `custom_field_id`=".$custom_field_id);

			return $query->num_rows;
		}
}
?>