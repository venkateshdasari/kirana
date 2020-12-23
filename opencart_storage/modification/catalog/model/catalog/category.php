<?php
class ModelCatalogCategory extends Model {
	public function getCategory($category_id) {
		$query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "category c LEFT JOIN " . DB_PREFIX . "category_description cd ON (c.category_id = cd.category_id) LEFT JOIN " . DB_PREFIX . "category_to_store c2s ON (c.category_id = c2s.category_id) WHERE c.category_id = '" . (int)$category_id . "' AND cd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND c2s.store_id = '" . (int)$this->config->get('config_store_id') . "' AND c.status = '1'");

		return $query->row;
	}

	public function getCategories($parent_id = 0) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "category c LEFT JOIN " . DB_PREFIX . "category_description cd ON (c.category_id = cd.category_id) LEFT JOIN " . DB_PREFIX . "category_to_store c2s ON (c.category_id = c2s.category_id) WHERE c.parent_id = '" . (int)$parent_id . "' AND cd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND c2s.store_id = '" . (int)$this->config->get('config_store_id') . "'  AND c.status = '1' ORDER BY c.sort_order, LCASE(cd.name)");

		return $query->rows;
	}

	public function getCategoryFilters($category_id) {
		$implode = array();

		$query = $this->db->query("SELECT filter_id FROM " . DB_PREFIX . "category_filter WHERE category_id = '" . (int)$category_id . "'");

		foreach ($query->rows as $result) {
			$implode[] = (int)$result['filter_id'];
		}

		$filter_group_data = array();

		if ($implode) {
			$filter_group_query = $this->db->query("SELECT DISTINCT f.filter_group_id, fgd.name, fg.sort_order FROM " . DB_PREFIX . "filter f LEFT JOIN " . DB_PREFIX . "filter_group fg ON (f.filter_group_id = fg.filter_group_id) LEFT JOIN " . DB_PREFIX . "filter_group_description fgd ON (fg.filter_group_id = fgd.filter_group_id) WHERE f.filter_id IN (" . implode(',', $implode) . ") AND fgd.language_id = '" . (int)$this->config->get('config_language_id') . "' GROUP BY f.filter_group_id ORDER BY fg.sort_order, LCASE(fgd.name)");

			foreach ($filter_group_query->rows as $filter_group) {
				$filter_data = array();

				$filter_query = $this->db->query("SELECT DISTINCT f.filter_id, fd.name FROM " . DB_PREFIX . "filter f LEFT JOIN " . DB_PREFIX . "filter_description fd ON (f.filter_id = fd.filter_id) WHERE f.filter_id IN (" . implode(',', $implode) . ") AND f.filter_group_id = '" . (int)$filter_group['filter_group_id'] . "' AND fd.language_id = '" . (int)$this->config->get('config_language_id') . "' ORDER BY f.sort_order, LCASE(fd.name)");

				foreach ($filter_query->rows as $filter) {
					$filter_data[] = array(
						'filter_id' => $filter['filter_id'],
						'name'      => $filter['name']
					);
				}

				if ($filter_data) {
					$filter_group_data[] = array(
						'filter_group_id' => $filter_group['filter_group_id'],
						'name'            => $filter_group['name'],
						'filter'          => $filter_data
					);
				}
			}
		}

		return $filter_group_data;
	}

	public function getCategoryLayoutId($category_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "category_to_layout WHERE category_id = '" . (int)$category_id . "' AND store_id = '" . (int)$this->config->get('config_store_id') . "'");

		if ($query->num_rows) {
			return (int)$query->row['layout_id'];
		} else {
			return 0;
		}
	}


	public function getCategoriesBySellerStore($path_id) {
		$query = $this->db->query("SELECT co.name AS country_name,ptc.*,cp.*, c2s.*,pvp.*,pvs.*,co.*,cd.*,c.* FROM " . DB_PREFIX . "product_to_category ptc LEFT JOIN " . DB_PREFIX . "category_path cp ON (ptc.category_id = cp.category_id) LEFT JOIN " . DB_PREFIX . "category c ON (c.category_id = ptc.category_id) LEFT JOIN " . DB_PREFIX . "category_description cd ON (c.category_id = cd.category_id) LEFT JOIN " . DB_PREFIX . "category_to_store c2s ON (c.category_id = c2s.category_id) LEFT JOIN " . DB_PREFIX . "purpletree_vendor_products pvp ON (pvp.product_id = ptc.product_id) Inner JOIN " . DB_PREFIX . "purpletree_vendor_stores pvs ON (pvp.seller_id = pvs.seller_id) LEFT JOIN " . DB_PREFIX . "country co ON (co.country_id = pvs.store_country) WHERE c.parent_id = '" . (int)"0" . "' AND cd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND c2s.store_id = '" . (int)$this->config->get('config_store_id') . "' AND cp.path_id = '" . (int)$path_id . "'  AND c.status = '1' GROUP BY pvs.id ORDER BY c.sort_order, LCASE(cd.name)");

		return $query->rows;
	}
	public function getCategoriesBySellerStoreFromTemplatePro($path_id) {
		$query = $this->db->query("SELECT co.name AS country_name,ptc.*,cp.*, c2s.*,pvt.*,pvs.*,co.*,cd.*,c.* FROM " . DB_PREFIX . "product_to_category ptc LEFT JOIN " . DB_PREFIX . "category_path cp ON (ptc.category_id = cp.category_id) LEFT JOIN " . DB_PREFIX . "category c ON (c.category_id = ptc.category_id) LEFT JOIN " . DB_PREFIX . "category_description cd ON (c.category_id = cd.category_id) LEFT JOIN " . DB_PREFIX . "category_to_store c2s ON (c.category_id = c2s.category_id) LEFT JOIN " . DB_PREFIX . "purpletree_vendor_template pvt ON (pvt.product_id = ptc.product_id) LEFT JOIN " . DB_PREFIX . "purpletree_vendor_template_products pvtp ON (pvt.id = pvtp.template_id) Inner JOIN " . DB_PREFIX . "purpletree_vendor_stores pvs ON (pvtp.seller_id = pvs.seller_id) LEFT JOIN " . DB_PREFIX . "country co ON (co.country_id = pvs.store_country) WHERE c.parent_id = '" . (int)"0" . "' AND cd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND c2s.store_id = '" . (int)$this->config->get('config_store_id') . "' AND cp.path_id = '" . (int)$path_id . "'  AND c.status = '1' GROUP BY pvs.id ORDER BY c.sort_order, LCASE(cd.name)");

		return $query->rows;
	}
	public function getAssinCategoriesSeller($category_id, $data=array()) {
		$sql = "SELECT *,co.name AS country_name FROM " . DB_PREFIX . "purpletree_vendor_allowed_category pvac INNER JOIN " . DB_PREFIX . "purpletree_vendor_stores pvs ON (pvs.seller_id = pvac.seller_id) LEFT JOIN " . DB_PREFIX . "country co ON (co.country_id = pvs.store_country) WHERE category_id = '" . (int)$category_id . "'";
		  
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
	}
	public function getTotalStores($category_id) {

		$sql = "SELECT COUNT(DISTINCT pvs.seller_id) AS total FROM " . DB_PREFIX . "purpletree_vendor_allowed_category pvac INNER JOIN " . DB_PREFIX . "purpletree_vendor_stores pvs ON (pvs.seller_id = pvac.seller_id) LEFT JOIN " . DB_PREFIX . "country co ON (co.country_id = pvs.store_country) LEFT JOIN " . DB_PREFIX . "category_to_store c2s ON (c2s.category_id = pvac.category_id) WHERE pvac.category_id = '" . (int)$category_id . "' AND c2s.store_id = '" . (int)$this->config->get('config_store_id') . "'";
		
		$query = $this->db->query($sql);

		return $query->row['total'];
	}
	public function checkAssignCategory($category_id) {
		$query = $this->db->query("SELECT seller_id FROM " . DB_PREFIX . "purpletree_vendor_allowed_category  WHERE category_id = '" . (int)$category_id . "'");
		return $query->rows;
	}	
			
	public function getTotalCategoriesByCategoryId($parent_id = 0) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "category c LEFT JOIN " . DB_PREFIX . "category_to_store c2s ON (c.category_id = c2s.category_id) WHERE c.parent_id = '" . (int)$parent_id . "' AND c2s.store_id = '" . (int)$this->config->get('config_store_id') . "' AND c.status = '1'");

		return $query->row['total'];
	}
}