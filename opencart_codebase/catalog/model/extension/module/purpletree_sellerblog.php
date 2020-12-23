<?php
class ModelExtensionModulePurpletreeSellerblog extends Model {
		public function getPurpletreeBlog($limit){		
			if($this->config->get('module_purpletree_multivendor_seller_blog_order')){
				$query = $this->db->query("SELECT pbp.*, pbpd.title, pbpd.description FROM " . DB_PREFIX . "purpletree_vendor_blog_post pbp LEFT JOIN " . DB_PREFIX . "purpletree_vendor_blog_post_description pbpd ON (pbp.blog_post_id = pbpd.blog_post_id) LEFT JOIN " . DB_PREFIX . "purpletree_vendor_stores pvs ON (pvs.seller_id = pbp.seller_id) WHERE pbp.status = '1'  AND pbpd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND pvs.vacation = 0 ORDER BY pbp.created_at DESC LIMIT " . (int)$limit);
				}else{
				$query = $this->db->query("SELECT pbp.*, pbpd.title, pbpd.description FROM " . DB_PREFIX . "purpletree_vendor_blog_post pbp LEFT JOIN " . DB_PREFIX . "purpletree_vendor_blog_post_description pbpd ON (pbp.blog_post_id = pbpd.blog_post_id) LEFT JOIN " . DB_PREFIX . "purpletree_vendor_stores pvs ON (pvs.seller_id = pbp.seller_id) WHERE pbp.status = '1' AND pvs.vacation = 0 AND pbpd.language_id = '" . (int)$this->config->get('config_language_id') . "' ORDER BY pbp.sort_order ASC LIMIT " . (int)$limit);
			}
			return $query->rows;
			
		}
		public function checkSellerVacation($blog_post_id){		
			$query = $this->db->query("SELECT count(*) AS num_row FROM " . DB_PREFIX . "purpletree_vendor_blog_post pbp JOIN " . DB_PREFIX . "purpletree_vendor_stores pvs ON (pvs.seller_id = pbp.seller_id) WHERE pbp.blog_post_id='".(int)$blog_post_id ."' AND pvs.vacation =1");
			if ($query->num_rows > 0) {
				return $query->row['num_row'];
				} else {	
				return NULL;
			}
		}
}