<?php
class ModelExtensionPurpletreeMultivendorSelleremail extends Model {
		
		public function editSelleremail($id,$data) {
		
			foreach ($data['email'] as $language_id => $value) {
				$this->db->query("UPDATE " . DB_PREFIX . "purpletree_vendor_email SET new_subject = '" . $this->db->escape($value['new_subject']) . "', new_message = '" . $this->db->escape($value['new_message']) . "' WHERE language_code='".  $this->db->escape($value['language_code']) ."' AND id ='".  (int)($value['id']) ."'");
			}
		}
		
		public function resetSelleremail($id) {
		    $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "purpletree_vendor_email WHERE id = '" . (int)$id . "'");
			
		    $query1 = $this->db->query("SELECT * FROM " . DB_PREFIX . "purpletree_vendor_email pve  WHERE pve.email_code = '" . $this->db->escape($query->row['email_code']). "'"); 
			$resust = $query1->rows;
			
			foreach ($resust as $key => $value) {
			  $query3 = $this->db->query("SELECT * FROM " . DB_PREFIX . "purpletree_vendor_email pve  WHERE pve.id = '" . (int)($value['id']). "'");
			 
			   $orginal_subject = $query3->row['subject'];
			  
			   $orginal_message = $query3->row['message'];
			 
				$this->db->query("UPDATE " . DB_PREFIX . "purpletree_vendor_email SET new_subject = '" . $this->db->escape($orginal_subject) . "', new_message = '" . $this->db->escape($orginal_message) . "' WHERE language_code='".  $this->db->escape($value['language_code']) ."' AND id ='".  (int)($value['id']) ."'");
			}
			
		}
		
		public function getSelleremail($data = array()) {
			
			$sql1="SELECT lu.code FROM ". DB_PREFIX ."language lu WHERE lu.language_id='".(int)$this->config->get('config_language_id') ."'";
			
			$query2 = $this->db->query($sql1);
			$language_code = $query2->row['code'];			
			
			$sql="SELECT pve.id,pve.title,pve.new_subject,pve.type FROM ". DB_PREFIX ."purpletree_vendor_email pve WHERE pve.language_code='".  $this->db->escape($language_code) ."'";	
			
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
			//echo"<pre>"; print_r($query->rows); die;
			return $query->rows;
		}
		public function getTotalSelleremail($data = array()) {
			
			$sql1="SELECT lu.code FROM ". DB_PREFIX ."language lu WHERE lu.language_id='".(int)$this->config->get('config_language_id') ."'";
			
			$query2 = $this->db->query($sql1);
			$language_code = $query2->row['code'];			
			
			$sql="SELECT COUNT(*) AS total FROM ". DB_PREFIX ."purpletree_vendor_email pve WHERE pve.language_code='".  $this->db->escape($language_code) ."'";					
			
			$query = $this->db->query($sql);
			//echo"<pre>"; print_r($query->row['total']); die;
			return $query->row['total'];
		}
		
		
		public function getemail($id) {
			$email_description_data = array();
			$email1 = array();
			$email2 = array();
			$lang_id = '';
			$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "purpletree_vendor_email WHERE id = '" . (int)$id . "'");
			
			$query1 = $this->db->query("SELECT * FROM " . DB_PREFIX . "purpletree_vendor_email pve  WHERE pve.email_code = '" . $this->db->escape($query->row['email_code']). "'");
			$query2 = $this->db->query("SELECT * FROM ". DB_PREFIX ."language lu");
			$email1 = $query1->rows;
			$email2 = $query2->rows;
			foreach ($email1 as $key => $result) {
			  foreach ($email2 as $result2) {
			    if($result2['code'] == $result['language_code']){
				   $lang_id=$result2['language_id'];
			     }
			  }
			  if($lang_id){
				   $note=str_replace(',',', ',$result['note']);
			  $email_description_data[$lang_id] = array(
						'id'             => $result['id'],
						'language_code' => $result['language_code'],
						'email_code' => $result['email_code'],
						'title' => $result['title'],
						'subject' => $result['subject'],
						'new_subject' => $result['new_subject'],
						'message' => $result['message'],
						'new_message' => $result['new_message'],
						'note' =>$note,
						'note_subject'     => $result['note_subject'], 
						'language_id'     => $lang_id 
				   );
				   $lang_id='';
			  }
				
			}
			//echo"<pre>"; print_r($email1); 
			//echo"<pre>"; print_r($email2); 
			//echo"<pre>"; print_r($email_description_data); 
			//die;
			return $email_description_data;
		}		
}