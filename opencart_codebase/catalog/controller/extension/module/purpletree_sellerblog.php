<?php
class ControllerExtensionModulePurpletreeSellerblog extends Controller {
		public function index() { 
			$this->load->language('extension/module/purpletree_sellerblog');
			
			$data['heading_title'] = $this->language->get('heading_title');
			
			$data['text_readmore'] = $this->language->get('text_readmore');
			
			$this->load->model('extension/module/purpletree_sellerblog');
			
			$this->load->model('tool/image');
			
			$data['pts_blogs'] = array();
			$data['view_all'] = $this->language->get('view_all');
			$data['all_blog'] = $this->url->link('extension/account/purpletree_multivendor/blog_post/all_blog','', true);
			
			$results = $this->model_extension_module_purpletree_sellerblog->getPurpletreeBlog($this->config->get('module_purpletree_sellerblog_limit'));
			
			foreach ($results as $result) {
				if ($result['image']) {
					$image = $this->model_tool_image->resize($result['image'], 150, 150);
					} else {
					$image = $this->model_tool_image->resize('placeholder.png',150, 150);
				}
				
				$shortdescription = utf8_substr(strip_tags(html_entity_decode($result['description'], ENT_QUOTES, 'UTF-8')), 0, $this->config->get($this->config->get('config_theme') . '_product_description_length')) . '..';
				
				
				if(strlen($shortdescription) > 25){
					$shortdescription =  substr($shortdescription, 0, 25).'...';
				}      
				$data['pts_blogs'][] = array(
				'blog_post_id'  => $result['blog_post_id'],
				'thumb'         => $image,
				'title'         => $result['title'],
				'description'   => $shortdescription,
				'date'          => date('d M', strtotime($result['created_at'])),
				'href'          => $this->url->link('extension/account/purpletree_multivendor/blog_post', 'blog_post_id=' . $result['blog_post_id'])						
				);
			}
			if ($results) {
			//owl carousel condition
			$themePrevent=array(
			'zeexo',
			'fastor',
			'wokiee',
			);
			if(!in_array($this->config->get('theme_default_directory'),$themePrevent) and $this->config->get('theme_default_status')==1){
				$this->document->addStyle('catalog/view/javascript/purpletree/jquery/owl-carousel/owl.carousel.css');
			$this->document->addScript('catalog/view/javascript/purpletree/jquery/owl-carousel/owl.carousel.min.js');
			}
			$direction = $this->language->get('direction');
		 if ($direction=='rtl'){
			$this->document->addStyle('catalog/view/javascript/purpletree/bootstrap/css/bootstrap.min-a.css');
			$this->document->addStyle('catalog/view/theme/default/stylesheet/purpletree/custom-a.css'); 
			}else{
			$this->document->addStyle('catalog/view/javascript/purpletree/bootstrap/css/bootstrap.min.css'); 
			$this->document->addStyle('catalog/view/theme/default/stylesheet/purpletree/custom.css'); 
			}
				return $this->load->view('extension/module/purpletree_sellerblog', $data);
			}
		}
	}