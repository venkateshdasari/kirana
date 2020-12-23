<?php
class ModelExtensionPurpletreeMultivendorGeozone extends Model {
		public function addSellerGeoZone($data) {
			$this->db->query("INSERT INTO " . DB_PREFIX . "geo_zone SET name = '" . $this->db->escape($data['name']) . "', description = '" . $this->db->escape($data['description']) . "', date_added = NOW()");
			
			$geo_zone_id = $this->db->getLastId();
			
			$this->db->query("INSERT INTO " . DB_PREFIX . "purpletree_vendor_geozone SET geo_zone_id = '" . (int)$geo_zone_id . "', seller_id = '" . (int)$data['seller_id'] . "', weight_from = '" . (float)$data['weight_from'] . "', weight_to = '" . (float)$data['weight_to'] . "', price = '" . (float)$data['price'] . "'");
			
			if (isset($data['zone_to_geo_zone'])) {
				foreach ($data['zone_to_geo_zone'] as $value) {
					if($value['country_id']!=='a'){
					$this->db->query("DELETE FROM " . DB_PREFIX . "zone_to_geo_zone WHERE geo_zone_id = '" . (int)$geo_zone_id . "' AND country_id = '" . (int)$value['country_id'] . "' AND zone_id = '" . (int)$value['zone_id'] . "'");				
					
					$this->db->query("INSERT INTO " . DB_PREFIX . "zone_to_geo_zone SET country_id = '" . (int)$value['country_id'] . "', zone_id = '" . (int)$value['zone_id'] . "', geo_zone_id = '" . (int)$geo_zone_id . "', date_added = NOW()");
					}
					
				}
			}
			
			$this->cache->delete('geo_zone');
			
			return $geo_zone_id;
		}
		
		public function editSellerGeoZone($geo_zone_id, $data) {
			$this->db->query("UPDATE " . DB_PREFIX . "geo_zone SET name = '" . $this->db->escape($data['name']) . "', description = '" . $this->db->escape($data['description']) . "', date_modified = NOW() WHERE geo_zone_id = '" . (int)$geo_zone_id . "'");

			$this->db->query("UPDATE " . DB_PREFIX . "purpletree_vendor_geozone SET seller_id = '" . (int)$data['seller_id'] . "', weight_from = '" . (float)$data['weight_from'] . "', weight_to = '" . (float)$data['weight_to'] . "', price = '" . (float)$data['price'] . "' WHERE geo_zone_id = '" . (int)$geo_zone_id . "'");
			
			$this->db->query("DELETE FROM " . DB_PREFIX . "zone_to_geo_zone WHERE geo_zone_id = '" . (int)$geo_zone_id . "'");
			
			if (isset($data['zone_to_geo_zone'])) {
				foreach ($data['zone_to_geo_zone'] as $value) {
					if($value['country_id']!=='a'){
					$this->db->query("DELETE FROM " . DB_PREFIX . "zone_to_geo_zone WHERE geo_zone_id = '" . (int)$geo_zone_id . "' AND country_id = '" . (int)$value['country_id'] . "' AND zone_id = '" . (int)$value['zone_id'] . "'");				
					
					$this->db->query("INSERT INTO " . DB_PREFIX . "zone_to_geo_zone SET country_id = '" . (int)$value['country_id'] . "', zone_id = '" . (int)$value['zone_id'] . "', geo_zone_id = '" . (int)$geo_zone_id . "', date_added = NOW()");
					}
				}
			}
			
			$this->cache->delete('geo_zone');
		}
		public function getSellerstore($store_name1) {
			$sql = "SELECT pvs.seller_id,pvs.store_name FROM " . DB_PREFIX . "purpletree_vendor_stores pvs LEFT JOIN " . DB_PREFIX . "customer c ON (c.customer_id = pvs.seller_id) WHERE pvs.store_name  LIKE '%" . $this->db->escape($store_name1) . "%' AND c.status=1 AND pvs.store_status=1";
			
			$query = $this->db->query($sql);
			return $query->rows;
			
		}
		
		public function deleteGeoZone($geo_zone_id) {
			
			$this->db->query("DELETE FROM " . DB_PREFIX . "geo_zone WHERE geo_zone_id = '" . (int)$geo_zone_id . "'");
			$this->db->query("DELETE FROM " . DB_PREFIX . "zone_to_geo_zone WHERE geo_zone_id = '" . (int)$geo_zone_id . "'");
			$this->db->query("DELETE FROM " . DB_PREFIX . "purpletree_vendor_geozone WHERE geo_zone_id = '" . (int)$geo_zone_id . "'");
			
			$this->cache->delete('geo_zone');
		}
		
		public function getGeoZone($geo_zone_id) {
			$query = $this->db->query("SELECT pvss.store_name AS seller_name, gz.*,pvg.* FROM " . DB_PREFIX . "geo_zone gz LEFT JOIN " . DB_PREFIX . "purpletree_vendor_geozone pvg ON (gz.geo_zone_id = pvg.geo_zone_id) LEFT JOIN " .DB_PREFIX. "purpletree_vendor_stores pvss ON(pvss.seller_id=pvg.seller_id) WHERE gz.geo_zone_id = '" . (int)$geo_zone_id . "'");
			
			return $query->row;
		}
		
		public function getGeoZones($data = array()) {
			if ($data) {
				$sql = "SELECT pvss.store_name AS seller_name, pvg.*, gz.* FROM " . DB_PREFIX . "geo_zone gz INNER JOIN " . DB_PREFIX . "purpletree_vendor_geozone pvg ON (gz.geo_zone_id = pvg.geo_zone_id) LEFt JOIN " .DB_PREFIX. "purpletree_vendor_stores pvss ON(pvss.seller_id=pvg.seller_id) AND pvg.seller_id !=0";
				
				$sort_data = array(
				'name',
				'description'
				);
				
				if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
					$sql .= " ORDER BY " . $data['sort'];
					} else {
					$sql .= " ORDER BY name";
				}
				
				if (isset($data['order']) && ($data['order'] == 'DESC')) {
					$sql .= " DESC";
					} else {
					$sql .= " ASC";
				}		
				/* 			if (isset($data['price']) && ($data['price'] == 'DESC')) {
					$sql .= " DESC";
					} else {
					$sql .= " ASC";
				} */
				
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
				
				return $query->rows;
				} else {
				$geo_zone_data = $this->cache->get('geo_zone');
				
				if (!$geo_zone_data) {
					$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "geo_zone ORDER BY name ASC");
					
					$geo_zone_data = $query->rows;
					
					$this->cache->set('geo_zone', $geo_zone_data);
				}
				
				return $geo_zone_data;
			}
		}
		
		public function getTotalGeoZones() {
			$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "purpletree_vendor_geozone");
			
			return $query->row['total'];
		}
		
		public function getZoneToGeoZones($geo_zone_id) {
			$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "zone_to_geo_zone WHERE geo_zone_id = '" . (int)$geo_zone_id . "'");
			
			return $query->rows;
		}
		
		public function getTotalZoneToGeoZoneByGeoZoneId($geo_zone_id) {
			$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "zone_to_geo_zone WHERE geo_zone_id = '" . (int)$geo_zone_id . "'");
			
			return $query->row['total'];
		}
		
		public function getTotalZoneToGeoZoneByCountryId($country_id) {
			$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "zone_to_geo_zone WHERE country_id = '" . (int)$country_id . "'");
			
			return $query->row['total'];
		}
		
		public function getTotalZoneToGeoZoneByZoneId($zone_id) {
			$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "zone_to_geo_zone WHERE zone_id = '" . (int)$zone_id . "'");
			
			return $query->row['total'];
		}
}
?>