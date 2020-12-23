<?php
class ModelExtensionPurpletreeMultivendorMails extends Model{
		
			public function getEmailTemplate($email_code) {
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
		
	   public function replaceVariables($variables=array(),$templatestring){
				$find=array();
				$replace=array();
				$find=array_keys($variables);
				$replace=array_values($variables);
				$templatefrom = str_replace($find,$replace,$templatestring);
				return $templatefrom;
		}

		public function sendMail($reciver,$subject,$message){
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
}