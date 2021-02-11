<?php
class ControllerExtensionModulePurpletreeSellerfeatured extends Controller {
		private $error = array();
		
		public function index() {
			$this->load->language('extension/module/purpletree_sellerfeatured');
			
			$this->document->setTitle($this->language->get('heading_title'));
			
			$this->load->model('setting/setting'); 
			
			if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
				
				$this->model_setting_setting->editSetting('module_purpletree_sellerfeatured', $this->request->post);
				
				
				$this->session->data['success'] = $this->language->get('text_success');
				
				$this->response->redirect($this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true));
			}
			
			$data['heading_title'] = $this->language->get('heading_title');
			
			$data['text_edit'] = $this->language->get('text_edit');
			$data['text_enabled'] = $this->language->get('text_enabled');
			$data['text_disabled'] = $this->language->get('text_disabled');
			$data['entry_status'] = $this->language->get('entry_status');
			$data['entry_limit'] = $this->language->get('entry_limit');
			$data['entry_width'] = $this->language->get('entry_width');
			$data['entry_height'] = $this->language->get('entry_height');
			$data['button_save'] = $this->language->get('button_save');
			$data['button_cancel'] = $this->language->get('button_cancel');
			
			if (isset($this->error['warning'])) {
				$data['error_warning'] = $this->error['warning'];
				} else {
				$data['error_warning'] = '';
			}
			if (isset($this->error['width'])) {
			$data['error_width'] = $this->error['width'];
			} else {
				$data['error_width'] = '';
			}

			if (isset($this->error['height'])) {
				$data['error_height'] = $this->error['height'];
			} else {
				$data['error_height'] = '';
			}
			
			if (isset($this->error['limit'])) {
				$data['error_limit'] = $this->error['limit'];
			} else {
				$data['error_limit'] = '';
			}
			
			$data['breadcrumbs'] = array();
			
			$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
			);
			
			$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_extension'),
			'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true)
			);
			
			if (!isset($this->request->get['module_id'])) {
				$data['breadcrumbs'][] = array(
				'text' => $this->language->get('heading_title'),
				'href' => $this->url->link('extension/module/purpletree_sellerfeatured', 'user_token=' . $this->session->data['user_token'], true)
				);
				} else {
				$data['breadcrumbs'][] = array(
				'text' => $this->language->get('heading_title'),
				'href' => $this->url->link('extension/module/purpletree_sellerfeatured', 'user_token=' . $this->session->data['user_token'] . '&module_id=' . $this->request->get['module_id'], true)
				);
			}
			
			$data['action'] = $this->url->link('extension/module/purpletree_sellerfeatured', 'user_token=' . $this->session->data['user_token'], true);
			
			
			$data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true);
			
			
			$data['user_token'] = $this->session->data['user_token'];
			
			if (isset($this->request->post['module_purpletree_sellerfeatured_status'])) {
				$data['module_purpletree_sellerfeatured_status'] = $this->request->post['module_purpletree_sellerfeatured_status'];
				} else {
				$data['module_purpletree_sellerfeatured_status'] = $this->config->get('module_purpletree_sellerfeatured_status');
			}
		if (isset($this->request->post['module_purpletree_sellerfeatured_limit'])) {
			$data['limit'] = $this->request->post['module_purpletree_sellerfeatured_limit'];
		} elseif ($this->config->get('module_purpletree_sellerfeatured_limit')) {
			$data['limit'] = $this->config->get('module_purpletree_sellerfeatured_limit');
		} else {
			$data['limit'] = 5;
		}

		if (isset($this->request->post['module_purpletree_sellerfeatured_width'])) {
			$data['width'] = $this->request->post['module_purpletree_sellerfeatured_width'];
		} elseif ($this->config->get('module_purpletree_sellerfeatured_width')) {
			$data['width'] = $this->config->get('module_purpletree_sellerfeatured_width');
		} else {
			$data['width'] = 200;
		}

		if (isset($this->request->post['module_purpletree_sellerfeatured_height'])) {
			$data['height'] = $this->request->post['module_purpletree_sellerfeatured_height'];
		} elseif ($this->config->get('module_purpletree_sellerfeatured_height')){
			$data['height'] = $this->config->get('module_purpletree_sellerfeatured_height');
		} else {
			$data['height'] = 200;
		}
			$data['header'] = $this->load->controller('common/header');
			$data['column_left'] = $this->load->controller('common/column_left');
			$data['footer'] = $this->load->controller('common/footer');
			
			$this->response->setOutput($this->load->view('extension/module/purpletree_sellerfeatured', $data));
		}
		protected function validate() {
			if (!$this->user->hasPermission('modify', 'extension/module/purpletree_sellerfeatured')) {
				$this->error['warning'] = $this->language->get('error_permission');
			}
			if (!$this->request->post['module_purpletree_sellerfeatured_width']) {
			$this->error['width'] = $this->language->get('error_width');
			}

			if (!$this->request->post['module_purpletree_sellerfeatured_height']) {
				$this->error['height'] = $this->language->get('error_height');
			}
			
			if (!$this->request->post['module_purpletree_sellerfeatured_limit']) {
				$this->error['limit'] = $this->language->get('error_limit');
			}
			return !$this->error;
		}
		
	}