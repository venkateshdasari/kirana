<?php
class ModelExtensionPurpletreeMultivendorQuickOrder extends Model {
	public function getProductCategory($productid){
		
		$sql = "SELECT category_id FROM " . DB_PREFIX . "product_to_category where 	product_id = '".(int)$productid."'"; 
		
		  $query = $this->db->query($sql);
		  
		  return $query->rows;  
		}
		public function getTemplateId($product_id) {
			$query = $this->db->query("SELECT pvt.id as id FROM " . DB_PREFIX . "purpletree_vendor_template pvt  WHERE pvt.product_id ='". (int)$product_id ."'");
			 if($query->num_rows){		
				return $query->row['id'];
			 }else{
				 return null;
			 }
		
	}
			public function getVendorOrderProducts($order_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "purpletree_vendor_orders WHERE order_id = '" . (int)$order_id . "'");
		
		return $query->rows;
	}
		public function getMatrixShippingCharge($address,$totalweight,$seller_id){
			if(!$this->config->get('module_purpletree_multivendor_shippingtype')){
				$sql = "SELECT * FROM " . DB_PREFIX . "purpletree_vendor_shipping pvss WHERE pvss.seller_id =".$seller_id." AND pvss.shipping_country = '".$address['shipping_country_id']."'";
						 if(!is_numeric($address['shipping_postcode'])) {
						 $sql .= " AND pvss.zipcode_from = '".$address['shipping_postcode']."' AND pvss.zipcode_to = '".$address['shipping_postcode']."'";
						 }
						 $shippingqery = $this->db->query($sql)->rows;
						if(!empty($shippingqery)) {
							$shipprice = array();
							foreach($shippingqery as $shipp) {
								if($totalweight >= $shipp['weight_from'] && $totalweight <= $shipp['weight_to']) {
									 if(is_numeric($address['shipping_postcode'])) {
										 if($address['shipping_postcode'] >= $shipp['zipcode_from'] && $address['shipping_postcode'] <= $shipp['zipcode_to']) {
											$shipprice[] = $shipp['shipping_price'];
										 }
									 } else {
										$shipprice[] = $shipp['shipping_price'];
									 }
								}
							}

							if(!empty($shipprice)) {
								return max($shipprice);
							}
								
						}
			}else{
				$sql = "SELECT * FROM  " . DB_PREFIX . "zone_to_geo_zone ztgz INNER JOIN " . DB_PREFIX . "purpletree_vendor_geozone pvz ON (ztgz.geo_zone_id = pvz.geo_zone_id) WHERE pvz.seller_id =".$seller_id." AND ztgz.country_id = '" . (int)$address['shipping_country_id'] . "' AND (ztgz.zone_id = '" . (int)$address['shipping_zone_id'] . "' OR ztgz.zone_id = '0')";

					$shippingqery = $this->db->query($sql)->rows;
					if(!empty($shippingqery)) {
						$shipprice = array();
						foreach($shippingqery as $shipp) {
							$shipprice[] = $shipp['price'];
						}
						if(!empty($shipprice)) {
							return max($shipprice);
						}
					}
					return '0';
			}
	}
	public function getoptionsweight($product){
		$productsql = "SELECT weight,weight_class_id FROM ".DB_PREFIX."product WHERE product_id =".$product['product_id']."";
				$productquery = $this->db->query($productsql)->row;
				$totweight = $productquery['weight'];
			if(!empty($product['option'])) {
				foreach($product['option'] as $productsoptin) {
					//echo "c";
					$productsql1 = "SELECT pov.weight,pov.weight_prefix,p.weight_class_id FROM ".DB_PREFIX."product p JOIN ". DB_PREFIX ."product_option_value pov ON(pov.product_id = p.product_id) WHERE pov.product_option_value_id = '".$productsoptin['product_option_value_id']."' AND pov.product_option_id = '".$productsoptin['product_option_id']."' AND pov.product_id = '".$product['product_id']."' AND pov.option_id = '".$productsoptin['option_id']."' AND pov.option_value_id = '".$productsoptin['option_value_id']."'";
					$productquery1 = $this->db->query($productsql1)->row;
					if(!empty($productquery1)){
						if ($productquery1['weight_prefix'] == '+') {
							$totweight += $totweight+($productquery1['weight'] * $product['quantity']);	
						} elseif ($product_option_value_info['weight_prefix'] == '-') {
							$totweight -= $totweight-($productquery1['weight'] * $product['quantity']);
						}
					}
				}
			} else {
					$totweight = $totweight * $product['quantity'];
			}
		$totalweight = $this->weight->convert($totweight, $productquery['weight_class_id'], $this->config->get('config_weight_class_id'));
		return $totalweight;
	}			
	public function getsellershipping($seller_shipping,$product,$address) {

		if($seller_shipping['seller_id'] == '0'){
	
				$shipping_purpletree_shipping_type = (null !== $this->config->get('shipping_purpletree_shipping_type'))? $this->config->get('shipping_purpletree_shipping_type') : 'pts_flat_rate_shipping';
			$shipping_purpletree_shipping_order_type = (null !== $this->config->get('shipping_purpletree_shipping_order_type'))? $this->config->get('shipping_purpletree_shipping_order_type') : 'pts_product_wise';
			$shipping_purpletree_shipping_charge = (null !== $this->config->get('shipping_purpletree_shipping_charge'))? $this->config->get('shipping_purpletree_shipping_charge') : '0';

		} else {
		$shipping_purpletree_shipping_type 			= $seller_shipping['store_shipping_type'] != '' ? $seller_shipping['store_shipping_type'] : 'pts_flat_rate_shipping';
		$shipping_purpletree_shipping_order_type 	= $seller_shipping['store_shipping_order_type'] != '' ? $seller_shipping['store_shipping_order_type'] : 'pts_product_wise';
		$shipping_purpletree_shipping_charge 		= $seller_shipping['store_shipping_charge'] != '' ? $seller_shipping['store_shipping_charge'] : '0';
		}
		$total = 0;
		$totalweight = $this->getoptionsweight($product);
		$getMatrixShippingCharge = $this->getMatrixShippingCharge($address,$totalweight,$seller_shipping['seller_id']);
		// if Matric shipping
		
		if($shipping_purpletree_shipping_type == 'pts_matrix_shipping'){
			if(!$this->config->get('module_purpletree_multivendor_shippingtype')){
				if($address['shipping_postcode'] != '') {
					if($shipping_purpletree_shipping_order_type == 'pts_product_wise'){
						if($getMatrixShippingCharge) {
							$total = $getMatrixShippingCharge;
						}
					} 
				}	
			} else{
				if($shipping_purpletree_shipping_order_type == 'pts_product_wise'){
					if($getMatrixShippingCharge) {
						$total = $getMatrixShippingCharge;
					}
				} 
			}			
		} // if Matric shipping
		// if Flexible shipping
		elseif($shipping_purpletree_shipping_type  == 'pts_flexible_shipping'){
		if(!$this->config->get('module_purpletree_multivendor_shippingtype')){
			if($address['shipping_postcode'] != '') {
				if($shipping_purpletree_shipping_order_type == 'pts_product_wise'){
					if($getMatrixShippingCharge) {
						 $total = $getMatrixShippingCharge;
					} else {
						 $total = $shipping_purpletree_shipping_charge;
					}
				}
			} else {
				if($shipping_purpletree_shipping_order_type == 'pts_product_wise'){
					 $total = $shipping_purpletree_shipping_charge;
				}
			}
		} else {
			if($shipping_purpletree_shipping_order_type == 'pts_product_wise'){
				if($getMatrixShippingCharge) {
					 $total = $getMatrixShippingCharge;
				} else {
					$total = $shipping_purpletree_shipping_charge;
				}
			}
		}
	} // if Flexible shipping
		// if Flat Rate shipping
			else {
			if($shipping_purpletree_shipping_order_type == 'pts_product_wise'){
				 $total = $shipping_purpletree_shipping_charge;
			}
		}
		
		// if Flat Rate shipping
		return $total;	
	}
	public function getsellershipping1($seller_shipping,$product,$address) {
		if($seller_shipping['seller_id'] == '0'){
			$shipping_purpletree_shipping_type = (null !== $this->config->get('shipping_purpletree_shipping_type'))? $this->config->get('shipping_purpletree_shipping_type') : 'pts_flat_rate_shipping';
			$shipping_purpletree_shipping_order_type = (null !== $this->config->get('shipping_purpletree_shipping_order_type'))? $this->config->get('shipping_purpletree_shipping_order_type') : 'pts_product_wise';
			$shipping_purpletree_shipping_charge = (null !== $this->config->get('shipping_purpletree_shipping_charge'))? $this->config->get('shipping_purpletree_shipping_charge') : '0';

		} else {
		$shipping_purpletree_shipping_type 			= $seller_shipping['store_shipping_type'] != '' ? $seller_shipping['store_shipping_type'] : 'pts_flat_rate_shipping';
		$shipping_purpletree_shipping_order_type 	= $seller_shipping['store_shipping_order_type'] != '' ? $seller_shipping['store_shipping_order_type'] : 'pts_product_wise';
		$shipping_purpletree_shipping_charge 		= $seller_shipping['store_shipping_charge'] != '' ? $seller_shipping['store_shipping_charge'] : '0';
		}
		$weightt = 0;
		// if Matric shipping
		if($shipping_purpletree_shipping_type == 'pts_matrix_shipping'){
			if($address['shipping_postcode'] != '') {
				if($shipping_purpletree_shipping_order_type == 'pts_order_wise'){
					 $weightt = $this->getoptionsweight($product);;
				}
			}					
		}// if Matric shipping
		// if Flexible shipping
		elseif($shipping_purpletree_shipping_type  == 'pts_flexible_shipping'){
			if($address['shipping_postcode'] != '') {
				if($shipping_purpletree_shipping_order_type == 'pts_order_wise'){
					  $weightt = $this->getoptionsweight($product);;
				}
			} else {
				if($shipping_purpletree_shipping_order_type == 'pts_order_wise'){
				 $weightt = $this->getoptionsweight($product);;
				}
			}
		} // if Flexible shipping
		// if Flat Rate shipping
			else {
			if($shipping_purpletree_shipping_order_type == 'pts_order_wise'){
				 
				  $weightt = $this->getoptionsweight($product);
			}
		}
		
		// if Flat Rate shipping
		return $weightt;	
	}
	public function getsellerInfofororder($sellerid) { 	
		    $query = $this->db->query("SELECT pvs.store_name, pvs.id AS store_id FROM " . DB_PREFIX . "purpletree_vendor_stores pvs  WHERE pvs.seller_id = '" . (int)$sellerid . "'");    
		     return $query->row;
		 
	}
	public function getOrderedProductsellerid($order_id,$product_id) {
		$query = $this->db->query("SELECT seller_id FROM `" . DB_PREFIX . "purpletree_vendor_orders` WHERE order_id = '" . (int)$order_id . "' AND product_id = '" . (int)$product_id . "' ");
		if(!empty($query->row['seller_id'])){
			return $query->row['seller_id'];
		}else{
			return null;
		}
	}
	public function getQucikOrderStatus($product_id){
		   
			 $query = $this->db->query("SELECT seller_id FROM " . DB_PREFIX . "purpletree_vendor_quick_order_product WHERE product_id='".(int)$product_id."'");
			
			if($query->num_rows){
				return $query->row['seller_id'];
				} else {
				return NULL;	
			}
		}
	public function getTaxes($product) {
		$tax_data = array();
			if ($product['tax_class_id']) {
				$tax_rates = $this->tax->getRates($product['price'], $product['tax_class_id']);

				foreach ($tax_rates as $tax_rate) {
					if (!isset($tax_data[$tax_rate['tax_rate_id']])) {
						$tax_data[$tax_rate['tax_rate_id']] = ($tax_rate['amount'] * $product['quantity']);
					} else {
						$tax_data[$tax_rate['tax_rate_id']] += ($tax_rate['amount'] * $product['quantity']);
					}
				}
			}
		
		return $tax_data;
	}
	public function getSubTotal($product) {
		$total = 0;
			$total += $product['total'];
		return $total;
	}
	public function getTotal($product) {
		$total = 0;
			$total += $this->tax->calculate($product['price'], $product['tax_class_id'], $this->config->get('config_tax')) * $product['quantity'];
		return $total;
	}
	   public function getProducts($product_id) {
	       $stock = true;
	       $product_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_to_store p2s LEFT JOIN " . DB_PREFIX . "product p ON (p2s.product_id = p.product_id) LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) WHERE p2s.store_id = '" . (int)$this->config->get('config_store_id') . "' AND p2s.product_id = '" . (int)$product_id . "' AND pd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND p.date_available <= NOW() AND p.status = '1'");
           $quantity = 1;
			if ($product_query->num_rows && ($quantity > 0)) {
				$option_price = 0;
				$option_points = 0;
				$option_weight = 0;

				$option_data = array();
                $option = '';
				if($option!=''){
				foreach (json_decode($option) as $product_option_id => $value) {
					$option_query = $this->db->query("SELECT po.product_option_id, po.option_id, od.name, o.type FROM " . DB_PREFIX . "product_option po LEFT JOIN `" . DB_PREFIX . "option` o ON (po.option_id = o.option_id) LEFT JOIN " . DB_PREFIX . "option_description od ON (o.option_id = od.option_id) WHERE po.product_option_id = '" . (int)$product_option_id . "' AND po.product_id = '" . (int)$product_id . "' AND od.language_id = '" . (int)$this->config->get('config_language_id') . "'");

					if ($option_query->num_rows) {
						if ($option_query->row['type'] == 'select' || $option_query->row['type'] == 'radio') {
							$option_value_query = $this->db->query("SELECT pov.option_value_id, ovd.name, pov.quantity, pov.subtract, pov.price, pov.price_prefix, pov.points, pov.points_prefix, pov.weight, pov.weight_prefix FROM " . DB_PREFIX . "product_option_value pov LEFT JOIN " . DB_PREFIX . "option_value ov ON (pov.option_value_id = ov.option_value_id) LEFT JOIN " . DB_PREFIX . "option_value_description ovd ON (ov.option_value_id = ovd.option_value_id) WHERE pov.product_option_value_id = '" . (int)$value . "' AND pov.product_option_id = '" . (int)$product_option_id . "' AND ovd.language_id = '" . (int)$this->config->get('config_language_id') . "'");

							if ($option_value_query->num_rows) {
								if ($option_value_query->row['price_prefix'] == '+') {
									$option_price += $option_value_query->row['price'];
								} elseif ($option_value_query->row['price_prefix'] == '-') {
									$option_price -= $option_value_query->row['price'];
								}

								if ($option_value_query->row['points_prefix'] == '+') {
									$option_points += $option_value_query->row['points'];
								} elseif ($option_value_query->row['points_prefix'] == '-') {
									$option_points -= $option_value_query->row['points'];
								}

								if ($option_value_query->row['weight_prefix'] == '+') {
									$option_weight += $option_value_query->row['weight'];
								} elseif ($option_value_query->row['weight_prefix'] == '-') {
									$option_weight -= $option_value_query->row['weight'];
								}

								if ($option_value_query->row['subtract'] && (!$option_value_query->row['quantity'] || ($option_value_query->row['quantity'] < $quantity))) {
									$stock = false;
								}

								$option_data[] = array(
									'product_option_id'       => $product_option_id,
									'product_option_value_id' => $value,
									'option_id'               => $option_query->row['option_id'],
									'option_value_id'         => $option_value_query->row['option_value_id'],
									'name'                    => $option_query->row['name'],
									'value'                   => $option_value_query->row['name'],
									'type'                    => $option_query->row['type'],
									'quantity'                => $option_value_query->row['quantity'],
									'subtract'                => $option_value_query->row['subtract'],
									'price'                   => $option_value_query->row['price'],
									'price_prefix'            => $option_value_query->row['price_prefix'],
									'points'                  => $option_value_query->row['points'],
									'points_prefix'           => $option_value_query->row['points_prefix'],
									'weight'                  => $option_value_query->row['weight'],
									'weight_prefix'           => $option_value_query->row['weight_prefix']
								);
							}
						} elseif ($option_query->row['type'] == 'checkbox' && is_array($value)) {
							foreach ($value as $product_option_value_id) {
								$option_value_query = $this->db->query("SELECT pov.option_value_id, pov.quantity, pov.subtract, pov.price, pov.price_prefix, pov.points, pov.points_prefix, pov.weight, pov.weight_prefix, ovd.name FROM " . DB_PREFIX . "product_option_value pov LEFT JOIN " . DB_PREFIX . "option_value_description ovd ON (pov.option_value_id = ovd.option_value_id) WHERE pov.product_option_value_id = '" . (int)$product_option_value_id . "' AND pov.product_option_id = '" . (int)$product_option_id . "' AND ovd.language_id = '" . (int)$this->config->get('config_language_id') . "'");

								if ($option_value_query->num_rows) {
									if ($option_value_query->row['price_prefix'] == '+') {
										$option_price += $option_value_query->row['price'];
									} elseif ($option_value_query->row['price_prefix'] == '-') {
										$option_price -= $option_value_query->row['price'];
									}

									if ($option_value_query->row['points_prefix'] == '+') {
										$option_points += $option_value_query->row['points'];
									} elseif ($option_value_query->row['points_prefix'] == '-') {
										$option_points -= $option_value_query->row['points'];
									}

									if ($option_value_query->row['weight_prefix'] == '+') {
										$option_weight += $option_value_query->row['weight'];
									} elseif ($option_value_query->row['weight_prefix'] == '-') {
										$option_weight -= $option_value_query->row['weight'];
									}

									if ($option_value_query->row['subtract'] && (!$option_value_query->row['quantity'] || ($option_value_query->row['quantity'] < $quantity))) {
										$stock = false;
									}

									$option_data[] = array(
										'product_option_id'       => $product_option_id,
										'product_option_value_id' => $product_option_value_id,
										'option_id'               => $option_query->row['option_id'],
										'option_value_id'         => $option_value_query->row['option_value_id'],
										'name'                    => $option_query->row['name'],
										'value'                   => $option_value_query->row['name'],
										'type'                    => $option_query->row['type'],
										'quantity'                => $option_value_query->row['quantity'],
										'subtract'                => $option_value_query->row['subtract'],
										'price'                   => $option_value_query->row['price'],
										'price_prefix'            => $option_value_query->row['price_prefix'],
										'points'                  => $option_value_query->row['points'],
										'points_prefix'           => $option_value_query->row['points_prefix'],
										'weight'                  => $option_value_query->row['weight'],
										'weight_prefix'           => $option_value_query->row['weight_prefix']
									);
								}
							}
						} elseif ($option_query->row['type'] == 'text' || $option_query->row['type'] == 'textarea' || $option_query->row['type'] == 'file' || $option_query->row['type'] == 'date' || $option_query->row['type'] == 'datetime' || $option_query->row['type'] == 'time') {
							$option_data[] = array(
								'product_option_id'       => $product_option_id,
								'product_option_value_id' => '',
								'option_id'               => $option_query->row['option_id'],
								'option_value_id'         => '',
								'name'                    => $option_query->row['name'],
								'value'                   => $value,
								'type'                    => $option_query->row['type'],
								'quantity'                => '',
								'subtract'                => '',
								'price'                   => '',
								'price_prefix'            => '',
								'points'                  => '',
								'points_prefix'           => '',
								'weight'                  => '',
								'weight_prefix'           => ''
							);
						}
					}
				}
			}
				$price = $product_query->row['price'];

				// Product Discounts
				$discount_quantity = 0;
				$product_discount_query = $this->db->query("SELECT price FROM " . DB_PREFIX . "product_discount WHERE product_id = '" . (int)$product_id . "' AND customer_group_id = '" . (int)$this->config->get('config_customer_group_id') . "' AND quantity <= '" . (int)$discount_quantity . "' AND ((date_start = '0000-00-00' OR date_start < NOW()) AND (date_end = '0000-00-00' OR date_end > NOW())) ORDER BY quantity DESC, priority ASC, price ASC LIMIT 1");

				if ($product_discount_query->num_rows) {
					$price = $product_discount_query->row['price'];
				}

				// Product Specials
				$product_special_query = $this->db->query("SELECT price FROM " . DB_PREFIX . "product_special WHERE product_id = '" . (int)$product_id . "' AND customer_group_id = '" . (int)$this->config->get('config_customer_group_id') . "' AND ((date_start = '0000-00-00' OR date_start < NOW()) AND (date_end = '0000-00-00' OR date_end > NOW())) ORDER BY priority ASC, price ASC LIMIT 1");

				if ($product_special_query->num_rows) {
					$price = $product_special_query->row['price'];
				}

				// Reward Points
				$product_reward_query = $this->db->query("SELECT points FROM " . DB_PREFIX . "product_reward WHERE product_id = '" . (int)$product_id . "' AND customer_group_id = '" . (int)$this->config->get('config_customer_group_id') . "'");

				if ($product_reward_query->num_rows) {
					$reward = $product_reward_query->row['points'];
				} else {
					$reward = 0;
				}

				// Downloads
				$download_data = array();

				$download_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_to_download p2d LEFT JOIN " . DB_PREFIX . "download d ON (p2d.download_id = d.download_id) LEFT JOIN " . DB_PREFIX . "download_description dd ON (d.download_id = dd.download_id) WHERE p2d.product_id = '" . (int)$product_id . "' AND dd.language_id = '" . (int)$this->config->get('config_language_id') . "'");

				foreach ($download_query->rows as $download) {
					$download_data[] = array(
						'download_id' => $download['download_id'],
						'name'        => $download['name'],
						'filename'    => $download['filename'],
						'mask'        => $download['mask']
					);
				}

				// Stock
				if (!$product_query->row['quantity'] || ($product_query->row['quantity'] < $quantity)) {
					$stock = false;
				}
                $cart['recurring_id'] ='';
				$recurring_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "recurring r LEFT JOIN " . DB_PREFIX . "product_recurring pr ON (r.recurring_id = pr.recurring_id) LEFT JOIN " . DB_PREFIX . "recurring_description rd ON (r.recurring_id = rd.recurring_id) WHERE r.recurring_id = '" . (int)$cart['recurring_id'] . "' AND pr.product_id = '" . (int)$product_id . "' AND rd.language_id = " . (int)$this->config->get('config_language_id') . " AND r.status = 1 AND pr.customer_group_id = '" . (int)$this->config->get('config_customer_group_id') . "'");

				if ($recurring_query->num_rows) {
					$recurring = array(
						'recurring_id'    => $cart['recurring_id'],
						'name'            => $recurring_query->row['name'],
						'frequency'       => $recurring_query->row['frequency'],
						'price'           => $recurring_query->row['price'],
						'cycle'           => $recurring_query->row['cycle'],
						'duration'        => $recurring_query->row['duration'],
						'trial'           => $recurring_query->row['trial_status'],
						'trial_frequency' => $recurring_query->row['trial_frequency'],
						'trial_price'     => $recurring_query->row['trial_price'],
						'trial_cycle'     => $recurring_query->row['trial_cycle'],
						'trial_duration'  => $recurring_query->row['trial_duration']
					);
				} else {
					$recurring = false;
				}

				
			
				$product_data = array(
					'product_id'      => $product_query->row['product_id'],
					'name'            => $product_query->row['name'],
					'model'           => $product_query->row['model'],
					'shipping'        => $product_query->row['shipping'],
					'image'           => $product_query->row['image'],
					'option'          => $option_data,
					'download'        => $download_data,
					'quantity'        => $product_query->row['quantity'],
					'minimum'         => $product_query->row['minimum'],
					'subtract'        => $product_query->row['subtract'],
					'stock'           => $stock,
					'price'           => ($price + $option_price),
					'total'           => ($price + $option_price) * $quantity,
					'reward'          => $reward * $quantity,
					'points'          => ($product_query->row['points'] ? ($product_query->row['points'] + $option_points) * $quantity : 0),
					'tax_class_id'    => $product_query->row['tax_class_id'],
					'weight'          => ($product_query->row['weight'] + $option_weight) * $quantity,
					'weight_class_id' => $product_query->row['weight_class_id'],
					'length'          => $product_query->row['length'],
					'width'           => $product_query->row['width'],
					'height'          => $product_query->row['height'],
					'length_class_id' => $product_query->row['length_class_id'],
					'recurring'       => $recurring
				);
			}
			return $product_data;
		   }
		public function addQuickOrder($data) {
		    $total = 0;	
			$seller_sub_total = array();
			$seller_final_total = array();
			$seller_tax_data = array();
			$seller_total_tax = array();
			$tax_data = array();
			$seller = array();
			$seller_shipping = array();
		$this->db->query("INSERT INTO `" . DB_PREFIX . "order` SET invoice_prefix = '" . $this->db->escape($data['invoice_prefix']) . "', store_id = '" . (int)$data['store_id'] . "', store_name = '" . $this->db->escape($data['store_name']) . "', store_url = '" . $this->db->escape($data['store_url']) . "', customer_id = '" . (int)$data['customer_id'] . "', customer_group_id = '" . (int)$data['customer_group_id'] . "', firstname = '" . $this->db->escape($data['firstname']) . "', lastname = '" . $this->db->escape($data['lastname']) . "', email = '" . $this->db->escape($data['email']) . "', telephone = '" . $this->db->escape($data['telephone']) . "', custom_field = '" . $this->db->escape(isset($data['custom_field']) ? json_encode($data['custom_field']) : '') . "', payment_firstname = '" . $this->db->escape($data['payment_firstname']) . "', payment_lastname = '" . $this->db->escape($data['payment_lastname']) . "', payment_company = '" . $this->db->escape($data['payment_company']) . "', payment_address_1 = '" . $this->db->escape($data['payment_address_1']) . "', payment_address_2 = '" . $this->db->escape($data['payment_address_2']) . "', payment_city = '" . $this->db->escape($data['payment_city']) . "', payment_postcode = '" . $this->db->escape($data['payment_postcode']) . "', payment_country = '" . $this->db->escape($data['payment_country']) . "', payment_country_id = '" . (int)$data['payment_country_id'] . "', payment_zone = '" . $this->db->escape($data['payment_zone']) . "', payment_zone_id = '" . (int)$data['payment_zone_id'] . "', payment_address_format = '" . $this->db->escape($data['payment_address_format']) . "', payment_custom_field = '" . $this->db->escape(isset($data['payment_custom_field']) ? json_encode($data['payment_custom_field']) : '') . "', payment_method = '" . $this->db->escape($data['payment_method']) . "', payment_code = '" . $this->db->escape($data['payment_code']) . "', shipping_firstname = '" . $this->db->escape($data['shipping_firstname']) . "', shipping_lastname = '" . $this->db->escape($data['shipping_lastname']) . "', shipping_company = '" . $this->db->escape($data['shipping_company']) . "', shipping_address_1 = '" . $this->db->escape($data['shipping_address_1']) . "', shipping_address_2 = '" . $this->db->escape($data['shipping_address_2']) . "', shipping_city = '" . $this->db->escape($data['shipping_city']) . "', shipping_postcode = '" . $this->db->escape($data['shipping_postcode']) . "', shipping_country = '" . $this->db->escape($data['shipping_country']) . "', shipping_country_id = '" . (int)$data['shipping_country_id'] . "', shipping_zone = '" . $this->db->escape($data['shipping_zone']) . "', shipping_zone_id = '" . (int)$data['shipping_zone_id'] . "', shipping_address_format = '" . $this->db->escape($data['shipping_address_format']) . "', shipping_custom_field = '" . $this->db->escape(isset($data['shipping_custom_field']) ? json_encode($data['shipping_custom_field']) : '') . "', shipping_method = '" . $this->db->escape($data['shipping_method']) . "', shipping_code = '" . $this->db->escape($data['shipping_code']) . "', comment = '" . $this->db->escape($data['comment']) . "', total = '" . (float)$data['total'] . "', affiliate_id = '" . (int)$data['affiliate_id'] . "', commission = '" . (float)$data['commission'] . "', marketing_id = '" . (int)$data['marketing_id'] . "', tracking = '" . $this->db->escape($data['tracking']) . "', language_id = '" . (int)$data['language_id'] . "', currency_id = '" . (int)$data['currency_id'] . "', currency_code = '" . $this->db->escape($data['currency_code']) . "', currency_value = '" . (float)$data['currency_value'] . "', ip = '" . $this->db->escape($data['ip']) . "', forwarded_ip = '" .  $this->db->escape($data['forwarded_ip']) . "', user_agent = '" . $this->db->escape($data['user_agent']) . "', accept_language = '" . $this->db->escape($data['accept_language']) . "', date_added = NOW(), date_modified = NOW()");

		$order_id = $this->db->getLastId();

		// Products
		if (isset($data['products'])) {
		$store_shipping_type = array();
			foreach ($data['products'] as $product) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "order_product SET order_id = '" . (int)$order_id . "', product_id = '" . (int)$product['product_id'] . "', name = '" . $this->db->escape($product['name']) . "', model = '" . $this->db->escape($product['model']) . "', quantity = '" . (int)$product['quantity'] . "', price = '" . (float)$product['price'] . "', total = '" . (float)$product['total'] . "', tax = '" . (float)$product['tax'] . "', reward = '" . (int)$product['reward'] . "'");

				$order_product_id = $this->db->getLastId();
/*** insert into seller orders ****/
			if ($this->config->get('module_purpletree_multivendor_status')) {	
					
					$seller_id = $this->db->query("SELECT pvp.seller_id, pvs.store_shipping_charge,pvs.store_shipping_order_type,pvs.store_shipping_type,pvs.store_commission, p.tax_class_id FROM " . DB_PREFIX . "purpletree_vendor_products pvp JOIN " . DB_PREFIX . "purpletree_vendor_stores pvs ON(pvs.seller_id=pvp.seller_id) JOIN " . DB_PREFIX . "product p ON(p.product_id=pvp.product_id) WHERE pvp.product_id='".(int)$product['product_id']."' AND pvp.is_approved=1")->row;
					if($this->config->get('module_purpletree_multivendor_seller_product_template')){
					if(empty($seller_id['seller_id'])) {
						$sseller_id = $product['seller_id'];
						$seller_id = $this->db->query("SELECT pvs.seller_id, pvs.store_shipping_charge,pvs.store_shipping_order_type,pvs.store_shipping_type,pvs.store_commission, p.tax_class_id FROM " . DB_PREFIX . "purpletree_vendor_template_products pvtp JOIN " . DB_PREFIX . "purpletree_vendor_stores pvs ON(pvs.seller_id=pvtp.seller_id) JOIN " . DB_PREFIX . "purpletree_vendor_template pvt ON(pvt.id=pvtp.template_id) JOIN " . DB_PREFIX . "product p ON(p.product_id=pvt.product_id) WHERE pvt.product_id='".(int)$product['product_id']."' AND pvs.seller_id='".$sseller_id."'")->row;
					}
					}
					if(!empty($seller_id['seller_id'])) {
						
						$this->db->query("INSERT INTO " . DB_PREFIX . "purpletree_vendor_orders SET order_id ='".(int)$order_id."', seller_id = '".(int)$seller_id['seller_id']."', product_id ='".(int)$product['product_id']."', shipping = '".(float)$seller_id['store_shipping_charge']."', quantity = '" . (int)$product['quantity'] . "', unit_price = '" . (float)$product['price'] . "', total_price = '" . (float)$product['total'] . "', created_at =NOW(), updated_at = NOW()");
						
						
						$this->db->query("INSERT INTO " . DB_PREFIX . "purpletree_vendor_commissions SET order_id = '" . (int)$order_id . "', product_id ='".(int)$product['product_id']."', seller_id = '" . (int)$seller_id['seller_id'] . "', commission_shipping = '0', commission_fixed = '0', commission_percent = '0', commission = '0', status = 'Pending', created_at = NOW(), updated_at = NOW()");
						
						if(!isset($seller_sub_total[$seller_id['seller_id']])){
						$seller_sub_total[$seller_id['seller_id']] = $product['total'];
						} else {
							$seller_sub_total[$seller_id['seller_id']] += $product['total'];
						}
						
						if(!isset($seller_final_total[$seller_id['seller_id']])){
							$seller_final_total[$seller_id['seller_id']] = $this->tax->calculate($product['price'], $seller_id['tax_class_id'], $this->config->get('config_tax')) * $product['quantity'];
						} else {
							$seller_final_total[$seller_id['seller_id']] += $this->tax->calculate($product['price'], $seller_id['tax_class_id'], $this->config->get('config_tax')) * $product['quantity'];
						}
						
						$tax_rates = $this->tax->getRates($product['price'], $seller_id['tax_class_id']);
			
						foreach ($tax_rates as $tax_rate) {
							if (!isset($seller_tax_data[$seller_id['seller_id']][$tax_rate['tax_rate_id']])) {
								$seller_tax_data[$seller_id['seller_id']][$tax_rate['tax_rate_id']] = ($tax_rate['amount'] * $product['quantity']);
							} else {
								$seller_tax_data[$seller_id['seller_id']][$tax_rate['tax_rate_id']] += ($tax_rate['amount'] * $product['quantity']);
							}
						}
				$shipping_purpletree_shipping_order_type 			= $seller_id['store_shipping_order_type'] != '' ? $seller_id['store_shipping_order_type']:'pts_product_wise' ;
				$shipping_purpletree_shipping_type 			= $seller_id['store_shipping_type'] != '' ? $seller_id['store_shipping_type']:'pts_flat_rate_shipping' ;
				$shipping_purpletree_shipping_charge 		= $seller_id['store_shipping_charge'] != '' ? $seller_id['store_shipping_charge'] : '0';
						$getsellershipping = $this->getsellershipping($seller_id,$product,$data);
						$getsellershipping1 = $this->getsellershipping1($seller_id,$product,$data);
						if(!isset($seller_shipping[$seller_id['seller_id']])){
							$seller_shipping[$seller_id['seller_id']] = $getsellershipping;
							$seller_shipping1[$seller_id['seller_id']] = $getsellershipping1;
						} else {
							$seller_shipping[$seller_id['seller_id']] += $getsellershipping;
							$seller_shipping1[$seller_id['seller_id']] += $getsellershipping1;
						}
					} else {
						$seller_id = array();
						$seller_id['seller_id'] = 0;
						$getsellershipping = $this->getsellershipping($seller_id,$product,$data);
						$getsellershipping1 = $this->getsellershipping1($seller_id,$product,$data);
						if(!isset($seller_shipping[$seller_id['seller_id']])){
							$seller_shipping[$seller_id['seller_id']] = $getsellershipping;
							$seller_shipping1[$seller_id['seller_id']] = $getsellershipping1;
						} else {
							$seller_shipping[$seller_id['seller_id']] += $getsellershipping;
							$seller_shipping1[$seller_id['seller_id']] += $getsellershipping1;
						}
				$shipping_purpletree_shipping_order_type = (null !== $this->config->get('shipping_purpletree_shipping_order_type'))? $this->config->get('shipping_purpletree_shipping_order_type') : 'pts_product_wise';
				$shipping_purpletree_shipping_type = (null !== $this->config->get('shipping_purpletree_shipping_type'))? $this->config->get('shipping_purpletree_shipping_type') : 'pts_flat_rate_shipping';
				$shipping_purpletree_shipping_charge = (null !== $this->config->get('shipping_purpletree_shipping_charge'))? $this->config->get('shipping_purpletree_shipping_charge') : '0';
					} 
				$store_shipping_type[$seller_id['seller_id']] = $shipping_purpletree_shipping_type;
				$store_shipping_charge[$seller_id['seller_id']] = $shipping_purpletree_shipping_charge;
				$store_shipping_order_type[$seller_id['seller_id']] = $shipping_purpletree_shipping_order_type;
				}
				foreach ($product['option'] as $option) {
					$this->db->query("INSERT INTO " . DB_PREFIX . "order_option SET order_id = '" . (int)$order_id . "', order_product_id = '" . (int)$order_product_id . "', product_option_id = '" . (int)$option['product_option_id'] . "', product_option_value_id = '" . (int)$option['product_option_value_id'] . "', name = '" . $this->db->escape($option['name']) . "', `value` = '" . $this->db->escape($option['value']) . "', `type` = '" . $this->db->escape($option['type']) . "'");
				}
			}
		}

		// Gift Voucher
		$this->load->model('extension/total/voucher');
        if(!empty($seller_shipping1)) {
			foreach($seller_shipping1 as $sellerid => $totalweight) {
				if($store_shipping_order_type[$sellerid] == 'pts_order_wise')  {
					$getMatrixShippingCharge1 = $this->getMatrixShippingCharge($data,$totalweight,$sellerid);
					if($store_shipping_type[$sellerid] == 'pts_matrix_shipping') {
						if(!$this->config->get('module_purpletree_multivendor_shippingtype')){
							if($data['shipping_postcode'] != '') {
								if($getMatrixShippingCharge1) {
									$seller_shipping[$sellerid] += $getMatrixShippingCharge1;
								}
							} 
						}else{
							if($getMatrixShippingCharge1) {
								$seller_shipping[$sellerid] += $getMatrixShippingCharge1;
							}
						}
					} elseif($store_shipping_type[$sellerid] == 'pts_flexible_shipping') {
						if($getMatrixShippingCharge1) {
							$seller_shipping[$sellerid] += $getMatrixShippingCharge1;
						} else {
							$seller_shipping[$sellerid] += $store_shipping_charge[$sellerid];
						}
					} elseif($store_shipping_type[$sellerid] == 'pts_flat_rate_shipping') {
							$seller_shipping[$sellerid] += $store_shipping_charge[$sellerid];
					}
				}
			}
		}
					$this->load->language('extension/total/total');
			/**************************************** For seller tax*******************************/
		if(! empty($seller_tax_data))
		{
			foreach($seller_tax_data as $key=>$value){
				foreach ($value as $key1 => $value1) {
					if ($value1 > 0) {
						$tax_detail[$key][] = array(
							'code'       => 'tax',
							'title'      => $this->tax->getRateName($key1),
							'value'      => $value1,
							'sort_order' => $this->config->get('total_tax_sort_order')
						);
						if(!isset($seller_total_tax[$key])){
							$seller_total_tax[$key] = $value1;
						} else {
							$seller_total_tax[$key] +=$value1 ;
						}
					}
				}
			} 
			}
			/**************************************** For seller shipping*******************************/
			$this->load->language('account/ptsregister');
			if($this->config->get('shipping_purpletree_shipping_status')){
			if($data['shipping_code'] == 'purpletree_shipping.purpletree_shipping') {
				foreach($seller_shipping as $key=>$value) {
					if ($value > 0) {
						$shippingtitle = $this->language->get('text_seller_shipping_total');
						if($key == 0) {
						$shippingtitle = $this->language->get('text_admin_shipping_total');
						}
						$tax_detail[$key][] = array(
							'code'       => 'seller_shipping',
							'title'      => $shippingtitle,
							'value'      => $value,
							'sort_order' => '2'
						);
					}
				}	
			}
			}
		
			/**************************************** For seller total*******************************/
			
			foreach($seller_final_total as $key=>$value) {
				if(!isset($seller_total_tax[$key])){
					$seller_total_tax[$key]=0;
				}
				if(!$this->config->get('shipping_purpletree_shipping_status')){
						$seller_shipping[$key]=0;
				}
				//echo $data['shipping_code'];
				if($data['shipping_code'] != 'purpletree_shipping.purpletree_shipping') {
						$seller_shipping[$key]=0;
				}
				if ($value > 0) { 
					$tax_detail[$key][] = array(
						'code'       => 'total',
						'title'      => $this->language->get('text_total'),
						'value'      => max(0, ($seller_sub_total[$key]+$seller_total_tax[$key]+$seller_shipping[$key])),
						'sort_order' => $this->config->get('total_total_sort_order')
					);
				}
			}
				
			/**************************************** For seller sub-total*******************************/
			foreach($seller_sub_total as $key=>$value) {
				if ($value > 0) {
					$tax_detail[$key][] = array(
						'code'       => 'sub_total',
						'title'      => $this->language->get('text_sub_total'),
						'value'      => $value,
						'sort_order' => $this->config->get('sub_total_sort_order')
					);
				}
			}
		if (isset($tax_detail)) {
			foreach ($tax_detail as $key=>$value) {
				foreach($value as $data_1){
					$this->db->query("INSERT INTO " . DB_PREFIX . "purpletree_order_total SET order_id = '" . (int)$order_id . "', seller_id = '".(int)$key."', code = '" . $this->db->escape($data_1['code']) . "', title = '" . $this->db->escape($data_1['title']) . "', `value` = '" . (float)$data_1['value'] . "', sort_order = '" . (int)$data_1['sort_order'] . "'");
				}
			}
		}
		// Vouchers
		if (isset($data['vouchers'])) {
			foreach ($data['vouchers'] as $voucher) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "order_voucher SET order_id = '" . (int)$order_id . "', description = '" . $this->db->escape($voucher['description']) . "', code = '" . $this->db->escape($voucher['code']) . "', from_name = '" . $this->db->escape($voucher['from_name']) . "', from_email = '" . $this->db->escape($voucher['from_email']) . "', to_name = '" . $this->db->escape($voucher['to_name']) . "', to_email = '" . $this->db->escape($voucher['to_email']) . "', voucher_theme_id = '" . (int)$voucher['voucher_theme_id'] . "', message = '" . $this->db->escape($voucher['message']) . "', amount = '" . (float)$voucher['amount'] . "'");

				$order_voucher_id = $this->db->getLastId();

				$voucher_id = $this->model_extension_total_voucher->addVoucher($order_id, $voucher);

				$this->db->query("UPDATE " . DB_PREFIX . "order_voucher SET voucher_id = '" . (int)$voucher_id . "' WHERE order_voucher_id = '" . (int)$order_voucher_id . "'");
			}
		}

		// Totals
		if (isset($data['totals'])) {
			foreach ($data['totals'] as $total) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "order_total SET order_id = '" . (int)$order_id . "', code = '" . $this->db->escape($total['code']) . "', title = '" . $this->db->escape($total['title']) . "', `value` = '" . (float)$total['value'] . "', sort_order = '" . (int)$total['sort_order'] . "'");
			}
		}
        if (isset($data['totals'])) {
				foreach ($data['totals'] as $total) {
					if($total['code']=='coupon'){
						$couponn = substr($total['title'],8,-1);				
						$query6 = $this->db->query("SELECT pvc.seller_id,co.discount FROM `" . DB_PREFIX . "coupon` co INNER JOIN " . DB_PREFIX . "purpletree_vendor_coupons pvc ON(co.coupon_id=pvc.coupon_id) WHERE co.code = '".$couponn."'AND pvc.seller_id!=0");
					if($query6->num_rows){
						$seller_id = $query6->row['seller_id'];
						
						$this->db->query("INSERT INTO " . DB_PREFIX . "purpletree_order_total SET order_id = '" . (int)$order_id . "', code = '" . $this->db->escape($total['code']) . "', title = '" . $this->db->escape($total['title']) . "', `value` = '" . (float)$total['value'] . "', sort_order = '" . (int)$total['sort_order'] . "', seller_id = '" .(int)$seller_id."'");
						
						$query7 = $this->db->query("SELECT order_total_id,value FROM `" . DB_PREFIX . "purpletree_order_total` WHERE order_id = '" . (int)$order_id . "' AND seller_id = '".(int)$seller_id."' AND code ='total'");
						if($query7->num_rows){
							$order_total_id = $query7->row['order_total_id'];
							$total_value = $query7->row['value'];
						}
						$coupon_amount = $total['value'];
						if($total_value && $coupon_amount){
							$total_after_apply_coupon = $total_value + ($coupon_amount);
							$this->db->query("UPDATE `" . DB_PREFIX . "purpletree_order_total` SET value = '" . (int)$total_after_apply_coupon . "' WHERE order_total_id='". (int)$order_total_id ."'");
						}
					}
				}
			}
		}
		return $order_id;
	}
		
}
?>