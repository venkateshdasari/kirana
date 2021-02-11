<?php
class ControllerStartupSeoUrl extends Controller {
	public function index() {
		// Add rewrite to url class
		if ($this->config->get('config_seo_url')) {
			$this->url->addRewrite($this);
		}

		// Decode URL

			//ocmultivendor SEO
			if ($this->config->get('config_seo_url')) {
				if(isset($this->request->get['route']) && $this->request->get['route'] != 'extension/account/purpletree_multivendor/sellerstore/storeview') {
				if($this->request->get['route'] == 'extension/account/purpletree_multivendor/blog_post' && isset($this->request->get['blog_post_id'])) {
					} else {
		$routeee = (explode("extension/account/purpletree_multivendor/",$this->request->get['route']));
				 if (array_key_exists("1",$routeee)) {
					 if(!empty($this->request->get) && $this->request->server['REQUEST_METHOD'] != 'POST') {
							unset($this->request->get['route']);
							$urlappend = '';
							$ccc = 0;
						foreach($this->request->get as $keyy => $valuee) {
							if($ccc == 0) {
								$urlappend .= '?';
							} else {
								$urlappend .= '&';
							}
							$urlappend .= $keyy.'='.$valuee;
							$ccc++;
						}
						if ($this->config->get('config_store_id')) {
							$store_url = $this->config->get('config_url');
						} else {
							if ($this->request->server['HTTPS']) {
								$store_url = HTTPS_SERVER;
							} else {
								$store_url = HTTP_SERVER;
							}
						}
							header('Location: '.$store_url.'ocmultivendor/'.$routeee[1].$urlappend, true, 301);
							exit;
						}
				 }
			}
		} 
		}
		//ocmultivendor SEO
		
		if (isset($this->request->get['_route_'])) {
			$parts = explode('/', $this->request->get['_route_']);

			// remove any empty arrays from trailing
			if (utf8_strlen(end($parts)) == 0) {
				array_pop($parts);
			}

			foreach ($parts as $part) {
				$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "seo_url WHERE keyword = '" . $this->db->escape($part) . "' AND store_id = '" . (int)$this->config->get('config_store_id') . "'");

				if ($query->num_rows) {
					$url = explode('=', $query->row['query']);

					if ($url[0] == 'product_id') {
						$this->request->get['product_id'] = $url[1];
					}

if ($url[0] == 'seller_store_id') {
						$this->request->get['seller_store_id'] = $url[1];
					}
					if ($url[0] == 'blog_post_id') {
						$this->request->get['blog_post_id'] = $url[1];
					}
			
					if ($url[0] == 'category_id') {
						if (!isset($this->request->get['path'])) {
							$this->request->get['path'] = $url[1];
						} else {
							$this->request->get['path'] .= '_' . $url[1];
						}
					}

					if ($url[0] == 'manufacturer_id') {
						$this->request->get['manufacturer_id'] = $url[1];
					}

					if ($url[0] == 'information_id') {
						$this->request->get['information_id'] = $url[1];
					}

					if ($query->row['query'] && $url[0] != 'information_id' && $url[0] != 'manufacturer_id' && $url[0] != 'category_id' && $url[0] != 'product_id' && $url[0] != 'seller_store_id' && $url[0] != 'blog_post_id') {
						$this->request->get['route'] = $query->row['query'];
					}
				} else {
					$this->request->get['route'] = 'error/not_found';

					break;
				}
			}


			//ocmultivendor SEO
			$routeee = (explode("ocmultivendor/",$this->request->get['_route_']));
				 if (array_key_exists("1",$routeee)) {
					 $this->request->get['route'] = 'extension/account/purpletree_multivendor/'.$routeee[1];
				 }
				 //ocmultivendor SEO
			
			if (!isset($this->request->get['route'])) {
				if (isset($this->request->get['product_id'])) {
					$this->request->get['route'] = 'product/product';
				} elseif (isset($this->request->get['path'])) {
					$this->request->get['route'] = 'product/category';
				} elseif (isset($this->request->get['manufacturer_id'])) {
					$this->request->get['route'] = 'product/manufacturer/info';

				}elseif (isset($this->request->get['seller_store_id'])) {
					$this->request->get['route'] = 'extension/account/purpletree_multivendor/sellerstore/storeview';
				} elseif (isset($this->request->get['blog_post_id'])) {
					$this->request->get['route'] = 'extension/account/purpletree_multivendor/blog_post';
	
				} elseif (isset($this->request->get['information_id'])) {
					$this->request->get['route'] = 'information/information';
				}
			}
		}
	}

	public function rewrite($link) {
		$url_info = parse_url(str_replace('&amp;', '&', $link));

		$url = '';

		$data = array();

		parse_str($url_info['query'], $data);

		foreach ($data as $key => $value) {
			if (isset($data['route'])) {

			//ocmultivendor SEO
			$route11 = '';
				 $routeee = (explode("extension/account/purpletree_multivendor",$data['route']));
				 if (array_key_exists("1",$routeee)) {
					  $route11 = $routeee[1]; 
				 }
				 //ocmultivendor SEO
			
				if (($data['route'] == 'product/product' && $key == 'product_id') || (($data['route'] == 'product/manufacturer/info' || $data['route'] == 'product/product') && $key == 'manufacturer_id') || ($data['route'] == 'information/information' && $key == 'information_id')) {
					$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "seo_url WHERE `query` = '" . $this->db->escape($key . '=' . (int)$value) . "' AND store_id = '" . (int)$this->config->get('config_store_id') . "' AND language_id = '" . (int)$this->config->get('config_language_id') . "'");

					if ($query->num_rows && $query->row['keyword']) {
						$url .= '/' . $query->row['keyword'];

						unset($data[$key]);
					}
} elseif ($data['route'] == 'extension/account/purpletree_multivendor/sellerstore/storeview' && $key == 'seller_store_id' || ($data['route'] == 'extension/account/purpletree_multivendor/blog_post' && $key == 'blog_post_id')) {
                    $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "seo_url WHERE `query` = '" . $this->db->escape($key . '=' . (int)$value) . "' AND store_id = '" . (int)$this->config->get('config_store_id') . "'");

                    if ( $query->num_rows && $query->row['keyword'] ) {
                        $url .=  '/' . $query->row['keyword'];
 
                        unset( $data[$key] );
                    }
					//ocmultivendor SEO
					} elseif ($route11 != '' && $data['route'] != 'extension/account/purpletree_multivendor/sellerstore/storeview' && $data['route'] != 'extension/account/purpletree_multivendor/blog_post') {
						$url .=  '/ocmultivendor' . $route11;
 
                        unset( $data[$key] );
						//ocmultivendor SEO
                
				} elseif ($key == 'path') {
					$categories = explode('_', $value);

					foreach ($categories as $category) {
						$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "seo_url WHERE `query` = 'category_id=" . (int)$category . "' AND store_id = '" . (int)$this->config->get('config_store_id') . "' AND language_id = '" . (int)$this->config->get('config_language_id') . "'");

						if ($query->num_rows && $query->row['keyword']) {
							$url .= '/' . $query->row['keyword'];
						} else {
							$url = '';

							break;
						}
					}

					unset($data[$key]);
				}
			}
		}

		if ($url) {
			unset($data['route']);

			$query = '';

			if ($data) {
				foreach ($data as $key => $value) {
					$query .= '&' . rawurlencode((string)$key) . '=' . rawurlencode((is_array($value) ? http_build_query($value) : (string)$value));
				}

				if ($query) {
					$query = '?' . str_replace('&', '&amp;', trim($query, '&'));
				}
			}

			return $url_info['scheme'] . '://' . $url_info['host'] . (isset($url_info['port']) ? ':' . $url_info['port'] : '') . str_replace('/index.php', '', $url_info['path']) . $url . $query;
		} else {
			return $link;
		}
	}
}
