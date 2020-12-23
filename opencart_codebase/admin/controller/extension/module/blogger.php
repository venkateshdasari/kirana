<?php
class ControllerExtensionModuleBlogger extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('extension/module/blogger');
		$this->load->model('extension/inspire/blogger');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('setting/module');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			if (!isset($this->request->get['module_id'])) {
				$this->model_setting_module->addModule('blogger', $this->request->post);
			} else {
				$this->model_setting_module->editModule($this->request->get['module_id'], $this->request->post);
			}

			$this->session->data['success'] = $this->language->get('text_success');

			$this->response->redirect($this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true));
		}

		$data['heading_title'] = $this->language->get('heading_title');

		$data['text_edit'] = $this->language->get('text_edit');
		$data['text_enabled'] = $this->language->get('text_enabled');
		$data['text_disabled'] = $this->language->get('text_disabled');
		$data['text_yes'] = $this->language->get('text_yes');
		$data['text_no'] = $this->language->get('text_no');
		$data['text_width'] = $this->language->get('text_width');
		$data['text_height'] = $this->language->get('text_height');
		$data['text_save_module'] = $this->language->get('text_save_module');
		$data['text_no_results'] = $this->language->get('text_no_results');
		$data['text_confirm'] = $this->language->get('text_confirm');

		if (!isset($this->request->get['module_id'])) {
			$data['text_view_list'] = sprintf($this->language->get('text_view_list'), $this->url->link('module/blogger/view_list', 'user_token=' . $this->session->data['user_token'], true));
		} else {
			$data['text_view_list'] = sprintf($this->language->get('text_view_list'), $this->url->link('module/blogger/view_list', 'user_token=' . $this->session->data['user_token'] . '&module_id=' . $this->request->get['module_id'], true));
		}

		$data['entry_name'] = $this->language->get('entry_name');
		$data['entry_status'] = $this->language->get('entry_status');
		$data['entry_image_size'] = $this->language->get('entry_image_size');
		$data['entry_width'] = $this->language->get('entry_width');
		$data['entry_height'] = $this->language->get('entry_height');
		$data['entry_allow_comments'] = $this->language->get('entry_allow_comments');
		$data['entry_login_required'] = $this->language->get('entry_login_required');
		$data['entry_limit'] = $this->language->get('entry_limit');
		$data['entry_char_limit'] = $this->language->get('entry_char_limit');
		$data['entry_auto_approve'] = $this->language->get('entry_auto_approve');

		$data['help_image'] = $this->language->get('help_image');
		$data['help_limit'] = $this->language->get('help_limit');
		$data['help_char_limit'] = $this->language->get('help_char_limit');
		$data['help_allow_comments'] = $this->language->get('help_allow_comments');
		$data['help_login_required'] = $this->language->get('help_login_required');
		$data['help_auto_approve'] = $this->language->get('help_auto_approve');

		$data['column_title'] = $this->language->get('column_title');
		$data['column_status'] = $this->language->get('column_status');
		$data['column_comments'] = $this->language->get('column_comments');
		$data['column_date_added'] = $this->language->get('column_date_added');
		$data['column_action'] = $this->language->get('column_action');

		$data['button_save'] = $this->language->get('button_save');
		$data['button_cancel'] = $this->language->get('button_cancel');
		$data['button_edit'] = $this->language->get('button_edit');
		$data['button_delete'] = $this->language->get('button_delete');
		$data['button_comments'] = $this->language->get('button_comments');
		$data['button_add_blog'] = $this->language->get('button_add_blog');

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->error['name'])) {
			$data['error_name'] = $this->error['name'];
		} else {
			$data['error_name'] = '';
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

		if (isset($this->session->data['success'])) {
			$data['success'] = $this->session->data['success'];

			unset($this->session->data['success']);
		} else {
			$data['success'] = '';
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_extension'),
			'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'], true)
		);

		if (!isset($this->request->get['module_id'])) {
			$data['breadcrumbs'][] = array(
				'text' => $this->language->get('heading_title'),
				'href' => $this->url->link('extension/module/blogger', 'user_token=' . $this->session->data['user_token'], true)
			);
		} else {
			$data['breadcrumbs'][] = array(
				'text' => $this->language->get('heading_title'),
				'href' => $this->url->link('extension/module/blogger', 'user_token=' . $this->session->data['user_token'] . '&module_id=' . $this->request->get['module_id'], true)
			);
		}

		if (!isset($this->request->get['module_id'])) {
			$data['action'] = $this->url->link('extension/module/blogger', 'user_token=' . $this->session->data['user_token'], true);
		} else {
			$data['action'] = $this->url->link('extension/module/blogger', 'user_token=' . $this->session->data['user_token'] . '&module_id=' . $this->request->get['module_id'], true);
		}

		$data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'], true);

		if (!isset($this->request->get['module_id'])) {
			$data['add_blog'] = '';
		} elseif (!$this->request->get['module_id']) {
			$data['add_blog'] = '';
		} else {
			$data['add_blog'] = $this->url->link('extension/module/blogger/add', 'user_token=' . $this->session->data['user_token'] . '&module_id=' . $this->request->get['module_id'], true);
		}

		if (isset($this->request->get['module_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
			$module_info = $this->model_setting_module->getModule($this->request->get['module_id']);
		}

		if (isset($this->request->post['module_id'])) {
			$data['module_id'] = $this->request->post['module_id'];
		} elseif (isset($this->request->get['module_id'])) {
			$data['module_id'] = $this->request->get['module_id'];
		} elseif (!empty($module_info)) {
			$data['module_id'] = $module_info['module_id'];
		} else {
			$data['module_id'] = '';
		}

		if (isset($this->request->post['name'])) {
			$data['name'] = $this->request->post['name'];
		} elseif (!empty($module_info)) {
			$data['name'] = $module_info['name'];
		} else {
			$data['name'] = '';
		}

		if (isset($this->request->post['status'])) {
			$data['status'] = $this->request->post['status'];
		} elseif (!empty($module_info)) {
			$data['status'] = $module_info['status'];
		} else {
			$data['status'] = '';
		}

		if (isset($this->request->post['width'])) {
			$data['width'] = $this->request->post['width'];
		} elseif (!empty($module_info)) {
			$data['width'] = $module_info['width'];
		} else {
			$data['width'] = 80;
		}

		if (isset($this->request->post['height'])) {
			$data['height'] = $this->request->post['height'];
		} elseif (!empty($module_info)) {
			$data['height'] = $module_info['height'];
		} else {
			$data['height'] = 80;
		}

		if (isset($this->request->post['limit'])) {
			$data['limit'] = $this->request->post['limit'];
		} elseif (!empty($module_info)) {
			$data['limit'] = $module_info['limit'];
		} else {
			$data['limit'] = 5;
		}

		if (isset($this->request->post['char_limit'])) {
			$data['char_limit'] = $this->request->post['char_limit'];
		} elseif (!empty($module_info)) {
			$data['char_limit'] = $module_info['char_limit'];
		} else {
			$data['char_limit'] = 50;
		}

		if (isset($this->request->post['comments'])) {
			$data['comments'] = $this->request->post['comments'];
		} elseif (!empty($module_info)) {
			$data['comments'] = $module_info['comments'];
		} else {
			$data['comments'] = '';
		}

		if (isset($this->request->post['login'])) {
			$data['login'] = $this->request->post['login'];
		} elseif (!empty($module_info)) {
			$data['login'] = $module_info['login'];
		} else {
			$data['login'] = '';
		}

		if (isset($this->request->post['auto_approve'])) {
			$data['auto_approve'] = $this->request->post['auto_approve'];
		} elseif (!empty($module_info)) {
			$data['auto_approve'] = $module_info['auto_approve'];
		} else {
			$data['auto_approve'] = '';
		}

		$data['blogger_entries'] = array();

		if (!isset($this->request->get['module_id'])) {
			$results = array();
		} else {
			$results = $this->model_extension_inspire_blogger->getBlogsByModule($this->request->get['module_id']);
		}

		if ($results) {
			foreach ($results as $result) {
				$total_comments = $this->model_extension_inspire_blogger->getTotalBlogComments($result['blogger_id']);

				$data['blogger_entries'][] = array(
					'blogger_id'     => $result['blogger_id'],
					'title'          => $result['title'],
					'module_id'      => $result['module_id'],
					'total_comments' => number_format($total_comments),
					'status'         => ($result['status'] ? $this->language->get('text_enabled') : $this->language->get('text_disabled')),
					'date_added'     => date($this->language->get('date_format_short'), strtotime($result['date_added'])),
					'comments'       => $this->url->link('extension/module/blogger/comments', 'user_token=' . $this->session->data['user_token'] . '&blogger_id=' . $result['blogger_id'] . '&module_id=' . $this->request->get['module_id'], true),
					'edit'           => $this->url->link('extension/module/blogger/edit', 'user_token=' . $this->session->data['user_token'] . '&blogger_id=' . $result['blogger_id'] . '&module_id=' . $this->request->get['module_id'], true),
					'delete'         => $this->url->link('extension/module/blogger/delete', 'user_token=' . $this->session->data['user_token'] . '&blogger_id=' . $result['blogger_id'] . '&module_id=' . $this->request->get['module_id'], true)
				);
			}
		}

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/module/blogger', $data));
	}

	protected function validate() {
		if (!$this->user->hasPermission('modify', 'extension/module/blogger')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		if ((utf8_strlen($this->request->post['name']) < 3) || (utf8_strlen($this->request->post['name']) > 64)) {
			$this->error['name'] = $this->language->get('error_name');
		}

		if (!$this->request->post['width']) {
			$this->error['width'] = $this->language->get('error_width');
		}

		if (!$this->request->post['height']) {
			$this->error['height'] = $this->language->get('error_height');
		}

		return !$this->error;
	}

	public function install() {
		$this->load->model('extension/inspire/blogger');

		$this->model_extension_inspire_blogger->createBlogs();
	}

	public function uninstall() {
		$this->load->model('extension/inspire/blogger');

		$this->model_extension_inspire_blogger->dropBlogs();
	}

	public function add() {
		$this->load->language('extension/module/blogger');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('extension/inspire/blogger');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_extension_inspire_blogger->addBlog($this->request->post, $this->request->post['module_id']);

			$this->session->data['success'] = $this->language->get('text_success');

			$this->response->redirect($this->url->link('extension/module/blogger', 'user_token=' . $this->session->data['user_token'] . '&module_id=' . $this->request->post['module_id'], true));
		}

		$this->getForm();
	}

	public function edit() {
		$this->load->language('extension/module/blogger');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('extension/inspire/blogger');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_extension_inspire_blogger->editBlog($this->request->get['blogger_id'], $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$this->response->redirect($this->url->link('extension/module/blogger', 'user_token=' . $this->session->data['user_token'] . '&module_id=' . $this->request->post['module_id'], true));
		}

		$this->getForm();
	}

	public function delete() {
		$this->load->language('extension/module/blogger');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('extension/inspire/blogger');

		if (isset($this->request->post['selected']) && $this->validateDelete()) {
			foreach ($this->request->post['selected'] as $blogger_id) {
				$this->model_setting_inspire_blogger->deleteBlog($blogger_id);
			}

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}

			$this->response->redirect($this->url->link('extension/module/blogger/view_list', 'user_token=' . $this->session->data['user_token'] . $url, true));
		} elseif ((isset($this->request->get['blogger_id']) && isset($this->request->get['delete'])) && $this->validateDelete()) {
			$this->model_extension_inspire_blogger->deleteBlog($this->request->get['blogger_id']);

			$this->session->data['success'] = $this->language->get('text_success');

		$this->response->redirect($this->url->link('extension/module/blogger', 'user_token=' . $this->session->data['user_token'] . '&module_id=' . $this->request->get['module_id'], true));
		}else{
		$this->model_extension_inspire_blogger->deleteBlog($this->request->get['blogger_id']);
		$this->session->data['success'] = $this->language->get('text_success');
		}

		if (!isset($this->request->get['module_id'])) {
			$this->response->redirect($this->url->link('extension/module/blogger/view_list', 'user_token=' . $this->session->data['user_token'], true));
		} else {
			$this->response->redirect($this->url->link('extension/module/blogger', 'user_token=' . $this->session->data['user_token'] . '&module_id=' . $this->request->get['module_id'], true));
		}
	}

	protected function getForm() {
		$data['heading_title'] = $this->language->get('heading_title');
		
		$data['text_form'] = !isset($this->request->get['blogger_id']) ? $this->language->get('text_add') : $this->language->get('text_form');
		$data['text_default'] = $this->language->get('text_default');
		$data['text_enabled'] = $this->language->get('text_enabled');
		$data['text_disabled'] = $this->language->get('text_disabled');
		$data['text_unallocated'] = $this->language->get('text_unallocated');

		$data['entry_title'] = $this->language->get('entry_title');
		$data['entry_description'] = $this->language->get('entry_description');
		$data['entry_status'] = $this->language->get('entry_status');
		$data['entry_image'] = $this->language->get('entry_image');
		$data['entry_module'] = $this->language->get('entry_module');

		$data['button_save'] = $this->language->get('button_save');
		$data['button_cancel'] = $this->language->get('button_cancel');

		$data['tab_general'] = $this->language->get('tab_general');
		$data['tab_data'] = $this->language->get('tab_data');

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->error['title'])) {
			$data['error_title'] = $this->error['title'];
		} else {
			$data['error_title'] = array();
		}

		if (isset($this->error['description'])) {
			$data['error_description'] = $this->error['description'];
		} else {
			$data['error_description'] = array();
		}

		if (isset($this->error['module'])) {
			$data['error_module'] = $this->error['module'];
		} else {
			$data['error_module'] = '';
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_module'),
			'href' => $this->url->link('extension/extension', 'user_token=' . $this->session->data['user_token'], true)
		);

		if (!isset($this->request->get['module_id'])) {
			$data['breadcrumbs'][] = array(
				'text' => $this->language->get('heading_title'),
				'href' => $this->url->link('extension/module/blogger/view_list', 'user_token=' . $this->session->data['user_token'], true)
			);
		} else {
			$data['breadcrumbs'][] = array(
				'text' => $this->language->get('heading_title'),
				'href' => $this->url->link('extension/module/blogger', 'user_token=' . $this->session->data['user_token'] . '&module_id=' . $this->request->get['module_id'], true)
			);
		}
		
		if (!isset($this->request->get['blogger_id'])) {
			$data['action'] = $this->url->link('extension/module/blogger/add', 'user_token=' . $this->session->data['user_token'], true);
		} else {
			$data['action'] = $this->url->link('extension/module/blogger/edit', 'user_token=' . $this->session->data['user_token'] . '&blogger_id=' . $this->request->get['blogger_id'], true);
		}

		if (isset($this->request->get['blogger_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
			$blogger_info = $this->model_extension_inspire_blogger->getBlog($this->request->get['blogger_id']);
		}

		if (isset($this->request->post['module_id'])) {
			$data['cancel'] = $this->url->link('extension/module/blogger', 'user_token=' . $this->session->data['user_token'] . '&module_id=' . $this->request->post['module_id'], true);
		} elseif (isset($this->request->get['module_id'])) {
			$data['cancel'] = $this->url->link('extension/module/blogger', 'user_token=' . $this->session->data['user_token'] . '&module_id=' . $this->request->get['module_id'], true);
		} elseif (!empty($blogger_info)) {
			$data['cancel'] = $this->url->link('extension/module/blogger', 'user_token=' . $this->session->data['user_token'] . '&module_id=' . $blogger_info['module_id'], true);
		} else {
			$data['cancel'] = $this->url->link('extension/module/blogger/view_list', 'user_token=' . $this->session->data['user_token'], true);
		}

		$data['user_token'] = $this->session->data['user_token'];

		$this->load->model('localisation/language');

		$data['languages'] = $this->model_localisation_language->getLanguages();

		if (isset($this->request->post['blogger_description'])) {
			$data['blogger_description'] = $this->request->post['blogger_description'];
		} elseif (isset($this->request->get['blogger_id'])) {
			$data['blogger_description'] = $this->model_extension_inspire_blogger->getBlogDescriptions($this->request->get['blogger_id']);
		} else {
			$data['blogger_description'] = array();
		}

		if (isset($this->request->post['status'])) {
			$data['status'] = $this->request->post['status'];
		} elseif (!empty($blogger_info)) {
			$data['status'] = $blogger_info['status'];
		} else {
			$data['status'] = 1;
		}

		if (isset($this->request->post['module_id'])) {
			$data['module_id'] = $this->request->post['module_id'];
		} elseif (isset($this->request->get['blogger_id']) && !empty($blogger_info)) {
			$data['module_id'] = $blogger_info['module_id'];
		} elseif (isset($this->request->get['module_id'])) {
			$data['module_id'] = $this->request->get['module_id'];
		} else {
			$data['module_id'] = '';
		}

		if (isset($this->request->post['image'])) {
			$data['image'] = $this->request->post['image'];
		} elseif (!empty($blogger_info)) {
			$data['image'] = $blogger_info['image'];
		} else {
			$data['image'] = '';
		}

		$this->load->model('tool/image');

		if (isset($this->request->post['image']) && is_file(DIR_IMAGE . $this->request->post['image'])) {
			$data['thumb'] = $this->model_tool_image->resize($this->request->post['image'], 100, 100);
		} elseif (!empty($blogger_info) && is_file(DIR_IMAGE . $blogger_info['image'])) {
			$data['thumb'] = $this->model_tool_image->resize($blogger_info['image'], 100, 100);
		} else {
			$data['thumb'] = $this->model_tool_image->resize('no_image.png', 100, 100);
		}

		$data['placeholder'] = $this->model_tool_image->resize('no_image.png', 100, 100);

		$this->load->model('setting/module');

		$data['blogger_modules'] = array();

		$modules = $this->model_setting_module->getModulesByCode('blogger');

		foreach ($modules as $module) {
			if (!isset($data['blogger_modules'][$module['code']])) {
				$data['blogger_modules'][$module['code']]['name'] = $this->language->get('heading_title');
			}

			$data['blogger_modules'][$module['code']]['module'][] = array(
				'module_id' => $module['module_id'],
				'name'      => $module['name']
			);
		}

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/module/blogger/form', $data));
	}

	protected function validateForm() {
		if (!$this->user->hasPermission('modify', 'extension/module/blogger')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		foreach ($this->request->post['blogger_description'] as $language_id => $value) {
			if ((utf8_strlen($value['title']) < 3) || (utf8_strlen($value['title']) > 64)) {
				$this->error['title'][$language_id] = $this->language->get('error_title');
			}

			if (utf8_strlen($value['description']) < 3) {
				$this->error['description'][$language_id] = $this->language->get('error_description');
			}
		}

		if (!$this->request->post['module_id']) {
			$this->error['module'] = $this->language->get('error_module');
		}

		if ($this->error && !isset($this->error['warning'])) {
			$this->error['warning'] = $this->language->get('error_warning');
		}

		return !$this->error;
	}

	protected function validateDelete() {
		if (!$this->user->hasPermission('modify', 'extension/module/blogger')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}

	public function view_list() {
		$this->load->language('extension/module/blogger');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('extension/inspire/blogger');

		$this->getlist();
	}

	protected function getList() {
		if (isset($this->request->get['sort'])) {
			$sort = $this->request->get['sort'];
		} else {
			$sort = 'b.module_id, b.date_added';
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

		$url = '';

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_module'),
			'href' => $this->url->link('extension/extension', 'user_token=' . $this->session->data['user_token'], true)
		);

		if (!isset($this->request->get['module_id'])) {
			$data['breadcrumbs'][] = array(
				'text' => $this->language->get('heading_title'),
				'href' => $this->url->link('extension/module/blogger/view_list', 'user_token=' . $this->session->data['user_token'], true)
			);
		} else {
			$data['breadcrumbs'][] = array(
				'text' => $this->language->get('heading_title'),
				'href' => $this->url->link('extension/module/blogger/view_list', 'user_token=' . $this->session->data['user_token'] . '&module_id=' . $this->request->get['module_id'], true)
			);
		}

		if (!isset($this->request->get['module_id'])) {
			$data['return'] = $this->url->link('extension/module/blogger', 'user_token=' . $this->session->data['user_token'], true);
			$data['add'] = $this->url->link('extension/module/blogger/add', 'user_token=' . $this->session->data['user_token'], true);
			$data['delete'] = $this->url->link('extension/module/blogger/delete', 'user_token=' . $this->session->data['user_token'] . $url, true);
		} else {
			$data['return'] = $this->url->link('extension/module/blogger', 'user_token=' . $this->session->data['user_token'] . '&module_id=' . $this->request->get['module_id'], true);
			$data['add'] = $this->url->link('extension/module/blogger/add', 'user_token=' . $this->session->data['user_token'] . '&module_id=' . $this->request->get['module_id'], true);
			$data['delete'] = $this->url->link('extension/module/blogger/delete', 'user_token=' . $this->session->data['user_token'] . '&module_id=' . $this->request->get['module_id'] . $url, true);
		}

		$data['bloggers'] = array();

		$filter_data = array(
			'sort'  => $sort,
			'order' => $order,
			'start' => ($page - 1) * $this->config->get('config_limit_admin'),
			'limit' => $this->config->get('config_limit_admin')
		);

		$blogger_total = $this->model_extension_inspire_blogger->getTotalBlogs();

		$results = $this->model_extension_inspire_blogger->getBlogs($filter_data);

		if (isset($this->request->get['module_id'])) {
			$url .= '&module_id=' . $this->request->get['module_id'];
		}

		foreach ($results as $result) {
			$module_info = $this->model_extension_inspire_blogger->getModule($result['module_id']);

			if ($module_info) {
				$module = $module_info['name'];
			} else {
				$module = $this->language->get('text_unallocated');
			}

			$total_comments = $this->model_extension_inspire_blogger->getTotalBlogComments($result['blogger_id']);

			$data['bloggers'][] = array(
				'blogger_id'     => $result['blogger_id'],
				'title'          => $result['title'],
				'module'         => $module,
				'total_comments' => number_format($total_comments),
				'status'         => ($result['status'] ? $this->language->get('text_enabled') : $this->language->get('text_disabled')),
				'date_added'     => date($this->language->get('date_format_short'), strtotime($result['date_added'])),
				'comments'       => $this->url->link('extension/module/blogger/comments', 'user_token=' . $this->session->data['user_token'] . '&blogger_id=' . $result['blogger_id'] . $url, true),
				'edit'           => $this->url->link('extension/module/blogger/edit', 'user_token=' . $this->session->data['user_token'] . '&blogger_id=' . $result['blogger_id'], true)
			);
		}

		$data['heading_title'] = $this->language->get('heading_title');

		$data['text_list'] = $this->language->get('text_list');
		$data['text_no_results'] = $this->language->get('text_no_results');
		$data['text_confirm'] = $this->language->get('text_confirm');

		$data['column_title'] = $this->language->get('column_title');
		$data['column_module'] = $this->language->get('column_module');
		$data['column_status'] = $this->language->get('column_status');
		$data['column_comments'] = $this->language->get('column_comments');
		$data['column_date_added'] = $this->language->get('column_date_added');
		$data['column_action'] = $this->language->get('column_action');

		$data['button_add'] = $this->language->get('button_add');
		$data['button_edit'] = $this->language->get('button_edit');
		$data['button_delete'] = $this->language->get('button_delete');
		$data['button_return'] = $this->language->get('button_return');
		$data['button_comments'] = $this->language->get('button_comments');

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

		if (isset($this->request->post['selected'])) {
			$data['selected'] = (array)$this->request->post['selected'];
		} else {
			$data['selected'] = array();
		}

		$url = '';

		if ($order == 'ASC') {
			$url .= '&order=DESC';
		} else {
			$url .= '&order=ASC';
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		if (!isset($this->request->get['module_id'])) {
			$data['sort_title'] = $this->url->link('extension/module/blogger/view_list', 'user_token=' . $this->session->data['user_token'] . '&sort=bd.title' . $url, true);
			$data['sort_module'] = $this->url->link('extension/module/blogger/view_list', 'user_token=' . $this->session->data['user_token'] . '&sort=b.module_id' . $url, true);
			$data['sort_date_added'] = $this->url->link('extension/module/blogger/view_list', 'user_token=' . $this->session->data['user_token'] . '&sort=b.date_added' . $url, true);
		} else {
			$data['sort_title'] = $this->url->link('extension/module/blogger/view_list', 'user_token=' . $this->session->data['user_token'] . '&sort=bd.title' . '&module_id=' . $this->request->get['module_id'] . $url, true);
			$data['sort_module'] = $this->url->link('extension/module/blogger/view_list', 'user_token=' . $this->session->data['user_token'] . '&sort=b.module_id' . '&module_id=' . $this->request->get['module_id'] . $url, true);
			$data['sort_date_added'] = $this->url->link('extension/module/blogger/view_list', 'user_token=' . $this->session->data['user_token'] . '&sort=b.date_added' . '&module_id=' . $this->request->get['module_id'] . $url, true);
		}

		$url = '';

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		$pagination = new Pagination();
		$pagination->total = $blogger_total;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_limit_admin');

		if (!isset($this->request->get['module_id'])) {
			$pagination->url = $this->url->link('extension/module/blogger/view_list', 'user_token=' . $this->session->data['user_token'] . $url . '&page={page}', true);
		} else {
			$pagination->url = $this->url->link('extension/module/blogger/view_list', 'user_token=' . $this->session->data['user_token'] . '&module_id=' . $this->request->get['module_id'] . $url . '&page={page}', true);
		}

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($blogger_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($blogger_total - $this->config->get('config_limit_admin'))) ? $blogger_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $blogger_total, ceil($blogger_total / $this->config->get('config_limit_admin')));

		$data['sort'] = $sort;
		$data['order'] = $order;

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/module/blogger/list', $data));
	}

	public function comments() {
		$this->load->language('extension/module/blogger');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('extension/inspire/blogger');

		$this->getComments();
	}

	protected function getComments() {
		if (!isset($this->request->get['blogger_id'])) {
			$this->response->redirect($this->url->link('extension/module/blogger/view_list', 'user_token=' . $this->session->data['user_token'], true));
		}

		$blog_info = $this->model_extension_inspire_blogger->getBlog($this->request->get['blogger_id']);

		$blog_title = $this->model_extension_inspire_blogger->getBlogTitle($this->request->get['blogger_id']);

		if (isset($this->request->get['sort'])) {
			$sort = $this->request->get['sort'];
		} else {
			$sort = 'date_added';
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

		$url = '';

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_module'),
			'href' => $this->url->link('extension/extension', 'user_token=' . $this->session->data['user_token'], true)
		);

		if (!isset($this->request->get['module_id'])){
			$data['breadcrumbs'][] = array(
				'text' => $this->language->get('heading_title'),
				'href' => $this->url->link('extension/module/blogger/view_list', 'user_token=' . $this->session->data['user_token'], true)
			);

			$data['breadcrumbs'][] = array(
				'text' => $blog_title,
				'href' => $this->url->link('extension/module/blogger/comments', 'user_token=' . $this->session->data['user_token'] . '&blogger_id=' . $this->request->get['blogger_id'], true)
			);
		} else {
			$data['breadcrumbs'][] = array(
				'text' => $this->language->get('heading_title'),
				'href' => $this->url->link('extension/module/blogger/view_list', 'user_token=' . $this->session->data['user_token'] . '&module_id=' . $this->request->get['module_id'], true)
			);

			$data['breadcrumbs'][] = array(
				'text' => $blog_title,
				'href' => $this->url->link('extension/module/blogger/comments', 'user_token=' . $this->session->data['user_token'] . '&module_id=' . $this->request->get['module_id'] . '&blogger_id=' . $this->request->get['blogger_id'], true)
			);
		}

		if (!isset($this->request->get['module_id'])){
			$data['return'] = $this->url->link('extension/module/blogger/view_list', 'user_token=' . $this->session->data['user_token'], true);
			$data['delete'] = $this->url->link('extension/module/blogger/delete_comment', 'user_token=' . $this->session->data['user_token'] . '&blogger_id=' . $this->request->get['blogger_id'] . $url, true);
		} else {
			$data['return'] = $this->url->link('extension/module/blogger', 'user_token=' . $this->session->data['user_token'] . '&module_id=' . $this->request->get['module_id'], true);
			$data['delete'] = $this->url->link('extension/module/blogger/delete_comment', 'user_token=' . $this->session->data['user_token'] . '&module_id=' . $this->request->get['module_id'] . '&blogger_id=' . $this->request->get['blogger_id'] . $url, true);
		}

		$data['blog_comments'] = array();

		$filter_data = array(
			'sort'  => $sort,
			'order' => $order,
			'start' => ($page - 1) * $this->config->get('config_limit_admin'),
			'limit' => $this->config->get('config_limit_admin')
		);

		$blog_comment_total = $this->model_extension_inspire_blogger->getTotalBlogComments($this->request->get['blogger_id']);

		$results = $this->model_extension_inspire_blogger->getBlogComments($this->request->get['blogger_id'], $filter_data);

		if (isset($this->request->get['module_id'])) {
			$url .= '&module_id=' . $this->request->get['module_id'];
		}

		foreach ($results as $result) {
			$data['blog_comments'][] = array(
				'blogger_comment_id' => $result['blogger_comment_id'],
				'author'             => $result['author'],
				'email'              => $result['email'],
				'approved'           => ($result['approved'] ? $this->language->get('text_approved') : $this->language->get('text_unapproved')),
				'date_added'         => date($this->language->get('date_format_short'), strtotime($result['date_added'])),
				'is_approved'        => $result['approved'],
				'approve'            => $this->url->link('extension/module/blogger/approve_comment', 'user_token=' . $this->session->data['user_token'] . '&blogger_id=' . $this->request->get['blogger_id'] . '&blogger_comment_id=' . $result['blogger_comment_id'] . $url, true),
				'disapprove'         => $this->url->link('extension/module/blogger/disapprove_comment', 'user_token=' . $this->session->data['user_token'] . '&blogger_id=' . $this->request->get['blogger_id'] . '&blogger_comment_id=' . $result['blogger_comment_id'] . $url, true),
				'view'               => $this->url->link('extension/module/blogger/view_comment', 'user_token=' . $this->session->data['user_token'] . '&blogger_id=' . $this->request->get['blogger_id'] . '&blogger_comment_id=' . $result['blogger_comment_id'] . $url, true)
			);
		}

		$data['heading_title'] = $this->language->get('heading_title');

		$data['text_list'] = $this->language->get('text_list');
		$data['text_no_results'] = $this->language->get('text_no_results');
		$data['text_confirm'] = $this->language->get('text_confirm');

		$data['column_author'] = $this->language->get('column_author');
		$data['column_email'] = $this->language->get('column_email');
		$data['column_status'] = $this->language->get('column_status');
		$data['column_date_added'] = $this->language->get('column_date_added');
		$data['column_action'] = $this->language->get('column_action');

		$data['button_list'] = $this->language->get('button_list');
		$data['button_delete'] = $this->language->get('button_delete');
		$data['button_approve'] = $this->language->get('button_approve');
		$data['button_disapprove'] = $this->language->get('button_disapprove');
		$data['button_view'] = $this->language->get('button_view');
		if (!isset($this->request->get['module_id'])) {
			$data['button_return'] = $this->language->get('button_list');
		} else {
			$data['button_return'] = $this->language->get('button_return');
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

		if (isset($this->request->post['selected'])) {
			$data['selected'] = (array)$this->request->post['selected'];
		} else {
			$data['selected'] = array();
		}

		$url = '';

		if ($order == 'ASC') {
			$url .= '&order=DESC';
		} else {
			$url .= '&order=ASC';
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		if (!isset($this->request->get['module_id'])) {
			$data['sort_author'] = $this->url->link('extension/module/blogger/comments', 'user_token=' . $this->session->data['user_token'] . '&sort=author' . $url . '&blogger_id=' . $this->request->get['blogger_id'], true);
			$data['sort_date_added'] = $this->url->link('extension/module/blogger/comments', 'user_token=' . $this->session->data['user_token'] . '&sort=date_added' . $url . '&blogger_id=' . $this->request->get['blogger_id'], true);
		} else {
			$data['sort_author'] = $this->url->link('extension/module/blogger/comments', 'user_token=' . $this->session->data['user_token'] . '&module_id=' . $this->request->get['module_id'] . '&sort=author' . $url . '&blogger_id=' . $this->request->get['blogger_id'], true);
			$data['sort_date_added'] = $this->url->link('extension/module/blogger/comments', 'user_token=' . $this->session->data['user_token'] . '&module_id=' . $this->request->get['module_id'] . '&sort=date_added' . $url . '&blogger_id=' . $this->request->get['blogger_id'], true);
		}

		$url = '';

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		$pagination = new Pagination();
		$pagination->total = $blog_comment_total;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_limit_admin');

		if (!isset($this->request->get['module_id'])) {
			$pagination->url = $this->url->link('extension/module/blogger/comments', 'user_token=' . $this->session->data['user_token'] . $url . '&blogger_id=' . $this->request->get['blogger_id'] . '&page={page}', true);
		} else {
			$pagination->url = $this->url->link('extension/module/blogger/comments', 'user_token=' . $this->session->data['user_token'] . '&module_id=' . $this->request->get['module_id'] . $url . '&blogger_id=' . $this->request->get['blogger_id'] . '&page={page}', true);
		}

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($blog_comment_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($blog_comment_total - $this->config->get('config_limit_admin'))) ? $blog_comment_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $blog_comment_total, ceil($blog_comment_total / $this->config->get('config_limit_admin')));

		$data['sort'] = $sort;
		$data['order'] = $order;

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/module/blogger/comments', $data));
	}

	public function approve_comment() {
		$this->load->language('extension/module/blogger');
		$this->load->model('extension/inspire/blogger');

		$this->model_extension_inspire_blogger->approveComment($this->request->get['blogger_comment_id']);

		$url = '';

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		if (!isset($this->request->get['module_id'])) {
			$this->response->redirect($this->url->link('extension/module/blogger/comments', 'user_token=' . $this->session->data['user_token'] . '&blogger_id=' . $this->request->get['blogger_id'] . $url, true));
		} else {
			$this->response->redirect($this->url->link('extension/module/blogger/comments', 'user_token=' . $this->session->data['user_token'] . '&module_id=' . $this->request->get['module_id'] . '&blogger_id=' . $this->request->get['blogger_id'] . $url, true));
		}
	}

	public function disapprove_comment() {
		$this->load->language('extension/module/blogger');
		$this->load->model('extension/inspire/blogger');

		$this->model_extension_inspire_blogger->disapproveComment($this->request->get['blogger_comment_id']);

		$url = '';

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		if (!isset($this->request->get['module_id'])) {
			$this->response->redirect($this->url->link('extension/module/blogger/comments', 'user_token=' . $this->session->data['user_token'] . '&blogger_id=' . $this->request->get['blogger_id'] . $url, true));
		} else {
			$this->response->redirect($this->url->link('extension/module/blogger/comments', 'user_token=' . $this->session->data['user_token'] . '&module_id=' . $this->request->get['module_id'] . '&blogger_id=' . $this->request->get['blogger_id'] . $url, true));
		}
	}

	public function delete_comment() {
		$this->load->language('extension/module/blogger');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('extension/inspire/blogger');

		if (isset($this->request->post['selected']) && $this->validateDelete()) {
			foreach ($this->request->post['selected'] as $blogger_comment_id) {
				$this->model_setting_inspire_blogger->deleteBlogComment($blogger_comment_id);
			}

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}

			if (!isset($this->request->get['module_id'])) {
				$this->response->redirect($this->url->link('extension/module/blogger/comments', 'user_token=' . $this->session->data['user_token'] . '&blogger_id=' . $this->request->get['blogger_id'] . $url, true));
			} else {
				$this->response->redirect($this->url->link('extension/module/blogger/comments', 'user_token=' . $this->session->data['user_token'] . '&module_id=' . $this->request->get['module_id'] . '&blogger_id=' . $this->request->get['blogger_id'] . $url, true));
			}
		}

		$this->getComments();
	}

	public function view_comment() {
		$this->load->language('extension/module/blogger');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('extension/inspire/blogger');

		$this->viewComment();
	}

	protected function viewComment() {
		if (!isset($this->request->get['blogger_id'])) {
			$this->response->redirect($this->url->link('extension/module/blogger/view_list', 'user_token=' . $this->session->data['user_token'], true));
		}

		$blog_title = $this->model_extension_inspire_blogger->getBlogTitle($this->request->get['blogger_id']);

		$data['heading_title'] = $this->language->get('heading_title');

		$data['text_author'] = $this->language->get('text_author');
		$data['text_email'] = $this->language->get('text_email');
		$data['text_date_added'] = $this->language->get('text_date_added');
		$data['text_comment'] = $this->language->get('text_comment');

		$data['button_back'] = $this->language->get('button_back');

		$url = '';

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_module'),
			'href' => $this->url->link('extension/extension', 'user_token=' . $this->session->data['user_token'], true)
		);

		if (!isset($this->request->get['module_id'])){
			$data['breadcrumbs'][] = array(
				'text' => $this->language->get('heading_title'),
				'href' => $this->url->link('extension/module/blogger/view_list', 'user_token=' . $this->session->data['user_token'], true)
			);

			$data['breadcrumbs'][] = array(
				'text' => $blog_title,
				'href' => $this->url->link('extension/module/blogger/comments', 'user_token=' . $this->session->data['user_token'] . '&blogger_id=' . $this->request->get['blogger_id'] . $url, true)
			);
		} else {
			$data['breadcrumbs'][] = array(
				'text' => $this->language->get('heading_title'),
				'href' => $this->url->link('extension/module/blogger/view_list', 'user_token=' . $this->session->data['user_token'] . '&module_id=' . $this->request->get['module_id'], true)
			);

			$data['breadcrumbs'][] = array(
				'text' => $blog_title,
				'href' => $this->url->link('extension/module/blogger/comments', 'user_token=' . $this->session->data['user_token'] . '&module_id=' . $this->request->get['module_id'] . '&blogger_id=' . $this->request->get['blogger_id'] . $url, true)
			);
		}

		if (!isset($this->request->get['module_id'])){
			$data['return'] = $this->url->link('extension/module/blogger/comments', 'user_token=' . $this->session->data['user_token'] . '&blogger_id=' . $this->request->get['blogger_id'], true);
		} else {
			$data['return'] = $this->url->link('extension/module/blogger/comments', 'user_token=' . $this->session->data['user_token'] . '&module_id=' . $this->request->get['module_id'] . '&blogger_id=' . $this->request->get['blogger_id'], true);
		}

		$comment_info = $this->model_extension_inspire_blogger->getBlogComment($this->request->get['blogger_comment_id']);

		$comment_descriptions = $this->model_extension_inspire_blogger->getBlogCommentDescriptions($this->request->get['blogger_comment_id']);

		$data['author'] = $comment_info['author'];
		$data['email'] = $comment_info['email'];
		$data['date_added'] = date($this->language->get('date_format_short'), strtotime($comment_info['date_added']));
		$data['comment_descriptions'] = $comment_descriptions;

		$this->load->model('localisation/language');

		$data['languages'] = $this->model_localisation_language->getLanguages();

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/module/blogger/view_comment', $data));
	}
}