<?php
class ControllerInformationBlogger extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('information/blogger');
		$this->load->model('extension/inspire/blogger');
		$this->load->model('tool/image');

		if (!isset($this->request->get['blogger_id'])) {
			$this->response->redirect($this->url->link('information/blogger/blogs'));
		}

		$language = $this->model_extension_inspire_blogger->getLanguageByCode($this->session->data['language']);

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_extension_inspire_blogger->addComment($this->request->get['blogger_id'], $this->request->post, $language['language_id']);

			if ($this->request->post['auto_approve']) {
				$this->session->data['success'] = $this->language->get('text_success');
			} else {
				$this->session->data['success'] = $this->language->get('text_approval');
			}
		}
		
		

		$blog_info = $this->model_extension_inspire_blogger->getBlog($this->request->get['blogger_id']);
		
		if ($blog_info) {
		
		$url = '';

		$this->document->setTitle($blog_info['title']);

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/home', '', true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('information/blogger', '', true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $blog_info['title'],
			'href' => $this->url->link('information/blogger', 'blogger_id=' . $this->request->get['blogger_id'], true)
		);

		$data['heading_title'] = $blog_info['title'];
		
		$module = $this->model_extension_inspire_blogger->getModule($blog_info['module_id']);

		$setting = json_decode($module['setting'],true);
		
		$data['blogs'] = array(
			'image'  => $this->model_tool_image->resize($blog_info['image'], $setting['width'], $setting['height'])
		);
			
		$data['description'] = html_entity_decode($blog_info['description'], ENT_QUOTES, 'UTF-8');

		$data['text_date_added'] = $this->language->get('text_date_added');
		$data['text_login_required'] = sprintf($this->language->get('text_login_required'), $this->url->link('account/login', '', true), $this->url->link('account/register', '', true));
		$data['text_your_comments'] = $this->language->get('text_your_comments');

		$data['entry_author'] = $this->language->get('entry_author');
		$data['entry_email'] = $this->language->get('entry_email');
		$data['entry_comment'] = $this->language->get('entry_comment');

		$data['button_comment_add'] = $this->language->get('button_comment_add');
		$data['button_submit'] = $this->language->get('button_submit');
		$data['button_list_blogs'] = $this->language->get('button_list_blogs');

		if ($this->request->server['REQUEST_METHOD'] == 'POST') {
			if ($this->error) {
				$data['div_display'] = 'block';
			} else {
				$data['div_display'] = 'none';
			}
		} else {
			$data['div_display'] = 'none';
		}

		if (isset($this->error['author'])) {
			$data['error_author'] = $this->error['author'];
		} else {
			$data['error_author'] = '';
		}

		if (isset($this->error['email'])) {
			$data['error_email'] = $this->error['email'];
		} else {
			$data['error_email'] = '';
		}

		if (isset($this->error['comment'])) {
			$data['error_comment'] = $this->error['comment'];
		} else {
			$data['error_comment'] = '';
		}

		if (isset($this->session->data['success'])) {
			$data['success'] = $this->session->data['success'];

			unset($this->session->data['success']);
		} else {
			$data['success'] = '';
		}

		$module = $this->model_extension_inspire_blogger->getModule($blog_info['module_id']);

		$setting = json_decode($module['setting'],true);

		$data['allow_comments'] = $setting['comments'];
		$data['login_required'] = $setting['login'];
		$data['auto_approve'] = $setting['auto_approve'];

		if ($this->customer->isLogged()) {
			$data['is_logged'] = true;
		} else {
			$data['is_logged'] = false;

		}

		$data['blog_comments'] = array();

		$blog_comments = $this->model_extension_inspire_blogger->getBlogComments($this->request->get['blogger_id'], $language['language_id']);

		if ($blog_comments) {
			foreach ($blog_comments as $comment) {
				$data['blog_comments'][] = array(
					'author'     => $comment['author'],
					'comment'    => html_entity_decode($comment['comment'], ENT_QUOTES, 'UTF-8'),
					'date_added' => date($this->language->get('date_format_long'), strtotime($comment['date_added']))
				);
			}
		}

		$data['action'] = $this->url->link('information/blogger', 'blogger_id=' . $this->request->get['blogger_id'], true);

		if (isset($this->request->post['author'])) {
			if ($this->error) {
				$data['author'] = $this->request->post['author'];
			} else {
				$data['author'] = '';
			}
		} elseif ($this->customer->isLogged()) {
			$data['author'] = $this->customer->getFirstName();
		} else {
			$data['author'] = '';
		}

		if (isset($this->request->post['email'])) {
			if ($this->error) {
				$data['email'] = $this->request->post['email'];
			} else {
				$data['email'] = '';
			}
		} elseif ($this->customer->isLogged()) {
			$data['email'] = $this->customer->getEmail();
		} else {
			$data['email'] = '';
		}

		if (isset($this->request->post['comment'])) {
			if ($this->error) {
				$data['comment'] = $this->request->post['comment'];
			} else {
				$data['comment'] = '';
			}
		} else {
			$data['comment'] = '';
		}

		// Captcha
		if ($this->config->get($this->config->get('config_captcha') . '_status')) {
			$data['captcha'] = $this->load->controller('captcha/' . $this->config->get('config_captcha'), $this->error);
		} else {
			$data['captcha'] = '';
		}

		if (!$this->customer->isLogged()) {
			$this->session->data['redirect'] = $this->url->link('information/blogger', 'blogger_id=' . $this->request->get['blogger_id'], true);
		}

		$data['column_left'] = $this->load->controller('common/column_left');
		$data['column_right'] = $this->load->controller('common/column_right');
		$data['content_top'] = $this->load->controller('common/content_top');
		$data['content_bottom'] = $this->load->controller('common/content_bottom');
		$data['footer'] = $this->load->controller('common/footer');
		$data['header'] = $this->load->controller('common/header');



		$this->response->setOutput($this->load->view('information/blogger', $data));

		}else {
			$url = '';

			$data['breadcrumbs'][] = array(
				'text' => $this->language->get('text_error'),
				'href' => $this->url->link('information/blogger', 'blogger_id=' . $this->request->get['blogger_id'], true)
			);

			$this->document->setTitle($this->language->get('text_error'));

			$data['heading_title'] = $this->language->get('text_error');

			$data['text_error'] = $this->language->get('text_error');

			$data['button_continue'] = $this->language->get('button_continue');

			$data['continue'] = $this->url->link('common/home');

			$this->response->addHeader($this->request->server['SERVER_PROTOCOL'] . ' 404 Not Found');

			$data['column_left'] = $this->load->controller('common/column_left');
			$data['column_right'] = $this->load->controller('common/column_right');
			$data['content_top'] = $this->load->controller('common/content_top');
			$data['content_bottom'] = $this->load->controller('common/content_bottom');
			$data['footer'] = $this->load->controller('common/footer');
			$data['header'] = $this->load->controller('common/header');

			$this->response->setOutput($this->load->view('error/not_found', $data));
		}
	}

	private function validate() {
		if ((utf8_strlen(trim($this->request->post['author'])) < 1) || (utf8_strlen(trim($this->request->post['author'])) > 32)) {
			$this->error['author'] = $this->language->get('error_author');
		}

		if (!preg_match('/^[^\@]+@.*.[a-z]{2,15}$/i', $this->request->post['email'])) {
			$this->error['email'] = $this->language->get('error_email');
		}

		if ((utf8_strlen($this->request->post['comment']) < 10) || (utf8_strlen($this->request->post['comment']) > 3000)) {
			$this->error['comment'] = $this->language->get('error_comment');
		}

		// Captcha
		if ($this->config->get($this->config->get('config_captcha') . '_status')) {
			$captcha = $this->load->controller('captcha/' . $this->config->get('config_captcha') . '/validate');

			if ($captcha) {
				$this->error['captcha'] = $captcha;
			}
		}

		return !$this->error;
	}

	public function blogs() {
		$this->load->language('information/blogger');
		$this->load->model('extension/inspire/blogger');
		$this->load->model('tool/image');

		$this->document->setTitle($this->language->get('heading_title'));

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/home', '', true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('information/blogger', '', true)
		);

		$data['heading_title'] = $this->language->get('heading_title');

		$data['text_blogs'] = $this->language->get('text_blogs');
		$data['text_comment'] = $this->language->get('text_comment');
		$data['text_no_blogs'] = $this->language->get('text_no_blogs');
		$data['text_read_more'] = $this->language->get('text_read_more');
		$data['text_date_added'] = $this->language->get('text_date_added');
		
		$data['entry_comment'] = $this->language->get('entry_comment');

		$data['column_title'] = $this->language->get('column_title');

		$data['button_view'] = $this->language->get('button_view');

		$data['blogs'] = array();	
		
		$blogs = $this->model_extension_inspire_blogger->getBlogs();

		foreach ($blogs as $blog) {
			$module_blog_data = array();

			$module = $this->model_extension_inspire_blogger->getModule($blog['module_id']);

			$setting = json_decode($module['setting'],true);

			if (!empty($setting['status']) && $setting['status']) {
				$module_blogs = $this->model_extension_inspire_blogger->getBlogsByModule($blog['module_id']);
			
			
				foreach ($module_blogs as $module_blog) {
				
				$total_comments = $this->model_extension_inspire_blogger->getTotalBlogComments($module_blog['blogger_id']);
				
					$module_blog_data[] = array(
						'title'       => $module_blog['title'],
						'image' 	  => $this->model_tool_image->resize($module_blog['image'], $setting['width'], $setting['height']),
						'description' => $module_blog['description'],
						'date_added'  => date($this->language->get('date_format_short'), strtotime($module_blog['date_added'])),
						'total_comments' => number_format($total_comments),
						'href'        => $this->url->link('information/blogger', 'blogger_id=' . $module_blog['blogger_id'], true)
					);
				}

				$data['blogs'][] = array(
					'name'      => $module['name'],
					'blog_data' => $module_blog_data
				);
			}
		}

		$data['column_left'] = $this->load->controller('common/column_left');
		$data['column_right'] = $this->load->controller('common/column_right');
		$data['content_top'] = $this->load->controller('common/content_top');
		$data['content_bottom'] = $this->load->controller('common/content_bottom');
		$data['footer'] = $this->load->controller('common/footer');
		$data['header'] = $this->load->controller('common/header');


		$this->response->setOutput($this->load->view('information/blogger_blogs', $data));
	}
}