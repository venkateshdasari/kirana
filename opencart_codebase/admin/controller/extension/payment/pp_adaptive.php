<?php
class ControllerExtensionPaymentPPAdaptive extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('extension/payment/pp_adaptive');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('setting/setting');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('payment_pp_adaptive', $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$this->response->redirect($this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=payment', true));
		}

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->error['email'])) {
			$data['error_email'] = $this->error['email'];
		} else {
			$data['error_email'] = '';
		}
		
		if (isset($this->error['error_e_secret_key'])) {
			$data['error_secret_key'] = $this->error['error_e_secret_key'];
		} else {
			$data['error_secret_key'] = '';
		}
		if (isset($this->error['error_e_client_id'])) {
			$data['error_client_id'] = $this->error['error_e_client_id'];
		} else {
			$data['error_client_id'] = '';
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_extension'),
			'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=payment', true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('extension/payment/pp_adaptive', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['action'] = $this->url->link('extension/payment/pp_adaptive', 'user_token=' . $this->session->data['user_token'], true);

		$data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=payment', true);

		if (isset($this->request->post['payment_pp_adaptive_email'])) {
			$data['payment_pp_adaptive_email'] = $this->request->post['payment_pp_adaptive_email'];
		} else {
			$data['payment_pp_adaptive_email'] = $this->config->get('payment_pp_adaptive_email');
		}
		if (isset($this->request->post['payment_pp_adaptive_client_id'])) {
			$data['payment_pp_adaptive_client_id'] = $this->request->post['payment_pp_adaptive_client_id'];
		} else {
			$data['payment_pp_adaptive_client_id'] = $this->config->get('payment_pp_adaptive_client_id');
		}
		if (isset($this->request->post['payment_pp_adaptive_admin_secret'])) {
			$data['payment_pp_adaptive_admin_secret'] = $this->request->post['payment_pp_adaptive_admin_secret'];
		} else {
			$data['payment_pp_adaptive_admin_secret'] = $this->config->get('payment_pp_adaptive_admin_secret');
		}

		if (isset($this->request->post['payment_pp_adaptive_test'])) {
			$data['payment_pp_adaptive_test'] = $this->request->post['payment_pp_adaptive_test'];
		} else {
			$data['payment_pp_adaptive_test'] = $this->config->get('payment_pp_adaptive_test');
		}

		if (isset($this->request->post['payment_pp_adaptive_transaction'])) {
			$data['payment_pp_adaptive_transaction'] = $this->request->post['payment_pp_adaptive_transaction'];
		} else {
			$data['payment_pp_adaptive_transaction'] = $this->config->get('payment_pp_adaptive_transaction');
		}

		if (isset($this->request->post['payment_pp_adaptive_debug'])) {
			$data['payment_pp_adaptive_debug'] = $this->request->post['payment_pp_adaptive_debug'];
		} else {
			$data['payment_pp_adaptive_debug'] = $this->config->get('payment_pp_adaptive_debug');
		}

		if (isset($this->request->post['payment_pp_adaptive_total'])) {
			$data['payment_pp_adaptive_total'] = $this->request->post['payment_pp_adaptive_total'];
		} else {
			$data['payment_pp_adaptive_total'] = $this->config->get('payment_pp_adaptive_total');
		}

		if (isset($this->request->post['payment_pp_adaptive_canceled_reversal_status_id'])) {
			$data['payment_pp_adaptive_canceled_reversal_status_id'] = $this->request->post['payment_pp_adaptive_canceled_reversal_status_id'];
		} else {
			$data['payment_pp_adaptive_canceled_reversal_status_id'] = $this->config->get('payment_pp_adaptive_canceled_reversal_status_id');
		}

		if (isset($this->request->post['payment_pp_adaptive_completed_status_id'])) {
			$data['payment_pp_adaptive_completed_status_id'] = $this->request->post['payment_pp_adaptive_completed_status_id'];
		} else {
			$data['payment_pp_adaptive_completed_status_id'] = $this->config->get('payment_pp_adaptive_completed_status_id');
		}

		if (isset($this->request->post['payment_pp_adaptive_denied_status_id'])) {
			$data['payment_pp_adaptive_denied_status_id'] = $this->request->post['payment_pp_adaptive_denied_status_id'];
		} else {
			$data['payment_pp_adaptive_denied_status_id'] = $this->config->get('payment_pp_adaptive_denied_status_id');
		}

		if (isset($this->request->post['payment_pp_adaptive_expired_status_id'])) {
			$data['payment_pp_adaptive_expired_status_id'] = $this->request->post['payment_pp_adaptive_expired_status_id'];
		} else {
			$data['payment_pp_adaptive_expired_status_id'] = $this->config->get('payment_pp_adaptive_expired_status_id');
		}

		if (isset($this->request->post['payment_pp_adaptive_failed_status_id'])) {
			$data['payment_pp_adaptive_failed_status_id'] = $this->request->post['payment_pp_adaptive_failed_status_id'];
		} else {
			$data['payment_pp_adaptive_failed_status_id'] = $this->config->get('payment_pp_adaptive_failed_status_id');
		}

		if (isset($this->request->post['payment_pp_adaptive_pending_status_id'])) {
			$data['payment_pp_adaptive_pending_status_id'] = $this->request->post['payment_pp_adaptive_pending_status_id'];
		} else {
			$data['payment_pp_adaptive_pending_status_id'] = $this->config->get('payment_pp_adaptive_pending_status_id');
		}

		if (isset($this->request->post['payment_pp_adaptive_processed_status_id'])) {
			$data['payment_pp_adaptive_processed_status_id'] = $this->request->post['payment_pp_adaptive_processed_status_id'];
		} else {
			$data['payment_pp_adaptive_processed_status_id'] = $this->config->get('payment_pp_adaptive_processed_status_id');
		}

		if (isset($this->request->post['payment_pp_adaptive_refunded_status_id'])) {
			$data['payment_pp_adaptive_refunded_status_id'] = $this->request->post['payment_pp_adaptive_refunded_status_id'];
		} else {
			$data['payment_pp_adaptive_refunded_status_id'] = $this->config->get('payment_pp_adaptive_refunded_status_id');
		}

		if (isset($this->request->post['payment_pp_adaptive_reversed_status_id'])) {
			$data['payment_pp_adaptive_reversed_status_id'] = $this->request->post['payment_pp_adaptive_reversed_status_id'];
		} else {
			$data['payment_pp_adaptive_reversed_status_id'] = $this->config->get('payment_pp_adaptive_reversed_status_id');
		}

		if (isset($this->request->post['payment_pp_adaptive_voided_status_id'])) {
			$data['payment_pp_adaptive_voided_status_id'] = $this->request->post['payment_pp_adaptive_voided_status_id'];
		} else {
			$data['payment_pp_adaptive_voided_status_id'] = $this->config->get('payment_pp_adaptive_voided_status_id');
		}

		$this->load->model('localisation/order_status');

		$data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();

		if (isset($this->request->post['payment_pp_adaptive_geo_zone_id'])) {
			$data['payment_pp_adaptive_geo_zone_id'] = $this->request->post['payment_pp_adaptive_geo_zone_id'];
		} else {
			$data['payment_pp_adaptive_geo_zone_id'] = $this->config->get('payment_pp_adaptive_geo_zone_id');
		}

		$this->load->model('localisation/geo_zone');

		$data['geo_zones'] = $this->model_localisation_geo_zone->getGeoZones();

		if (isset($this->request->post['payment_pp_adaptive_status'])) {
			$data['payment_pp_adaptive_status'] = $this->request->post['payment_pp_adaptive_status'];
		} else {
			$data['payment_pp_adaptive_status'] = $this->config->get('payment_pp_adaptive_status');
		}

		if (isset($this->request->post['payment_pp_adaptive_sort_order'])) {
			$data['payment_pp_adaptive_sort_order'] = $this->request->post['payment_pp_adaptive_sort_order'];
		} else {
			$data['payment_pp_adaptive_sort_order'] = $this->config->get('payment_pp_adaptive_sort_order');
		}

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/payment/pp_adaptive', $data));
	}

	private function validate() {
		if (!$this->user->hasPermission('modify', 'extension/payment/pp_adaptive')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		if (!$this->request->post['payment_pp_adaptive_email']) {
			$this->error['email'] = $this->language->get('error_email');
		}
		if (!$this->request->post['payment_pp_adaptive_client_id']) {
			$this->error['error_e_client_id'] =  $this->language->get('error_error_client_id');
		}
		if (!$this->request->post['payment_pp_adaptive_admin_secret']) {
			$this->error['error_e_secret_key'] = $this->language->get('error_error_secret_key');
		}

		return !$this->error;
	}
}
?>