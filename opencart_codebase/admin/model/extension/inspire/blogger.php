<?php
class ModelExtensionInspireBlogger extends Model {
	public function createBlogs() {
		$create_blogger = "CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "blogger` (`blogger_id` int(11) NOT NULL auto_increment, `module_id` int(11) NOT NULL, `status` int(1) NOT NULL default '0', `image` varchar(255) default NULL, `date_added` datetime NOT NULL default '0000-00-00 00:00:00', `date_modified` datetime NOT NULL default '0000-00-00 00:00:00', PRIMARY KEY (`blogger_id`)) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;";
		$this->db->query($create_blogger);

		$create_blogger_description = "CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "blogger_description` (`blogger_id` int(11) NOT NULL default '0', `language_id` int(11) NOT NULL default '0', `title` varchar(64) NOT NULL default '', `description` text NOT NULL, PRIMARY KEY (`blogger_id`,`language_id`)) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;";
		$this->db->query($create_blogger_description);

		$create_blogger_comment = "CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "blogger_comment` (`blogger_comment_id` int(11) NOT NULL auto_increment, `blogger_id` int(11) NOT NULL, `approved` int(1) NOT NULL default '0', `author` varchar(64) NOT NULL default '', `email` varchar(96) NOT NULL, `date_added` datetime NOT NULL default '0000-00-00 00:00:00', PRIMARY KEY (`blogger_comment_id`, `blogger_id`)) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;";
		$this->db->query($create_blogger_comment);

		$create_blogger_comment_description = "CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "blogger_comment_description` (`blogger_comment_id` int(11) NOT NULL default '0', `language_id` int(11) NOT NULL default '0', `comment` text NOT NULL, PRIMARY KEY (`blogger_comment_id`,`language_id`)) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;";
		$this->db->query($create_blogger_comment_description);
	}

	public function dropBlogs() {
		$drop_blogger = "DROP TABLE IF EXISTS `" . DB_PREFIX . "blogger`;";
		$this->db->query($drop_blogger);

		$drop_blogger_description = "DROP TABLE IF EXISTS `" . DB_PREFIX . "blogger_description`;";
		$this->db->query($drop_blogger_description);

		$drop_blogger_comment = "DROP TABLE IF EXISTS `" . DB_PREFIX . "blogger_comment`;";
		$this->db->query($drop_blogger_comment);

		$drop_blogger_comment_description = "DROP TABLE IF EXISTS `" . DB_PREFIX . "blogger_comment_description`;";
		$this->db->query($drop_blogger_comment_description);
	}

	public function addModule($code, $data) {
		$this->db->query("INSERT INTO `" . DB_PREFIX . "module` SET `name` = '" . $this->db->escape($data['name']) . "', `code` = '" . $this->db->escape($code) . "', `setting` = '" . $this->db->escape(json_encode($data)) . "'");

		$module_id = $this->db->getLastId();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "module WHERE module_id = '" . (int)$module_id . "'");

		$settings = json_decode($query->row['setting'], true);

		$settings['module_id'] = $module_id;

		$this->db->query("UPDATE " . DB_PREFIX . "module SET setting = '" . $this->db->escape(json_encode($settings)) . "' WHERE module_id = '" . (int)$module_id . "'");

		return $module_id;
	}

	public function addBlog($data) {
		$this->db->query("INSERT INTO " . DB_PREFIX . "blogger SET module_id = '" . (int)$data['module_id'] . "', status = '" . (int)$data['status'] . "', date_added = now(), date_modified = now()");

		$blogger_id = $this->db->getLastId();

		if (isset($data['image'])) {
			$this->db->query("UPDATE " . DB_PREFIX . "blogger SET image = '" . $this->db->escape($data['image']) . "' WHERE blogger_id = '" . (int)$blogger_id . "'");
		}

		foreach ($data['blogger_description'] as $language_id => $value) {
			$this->db->query("INSERT INTO " . DB_PREFIX . "blogger_description SET blogger_id = '" . (int)$blogger_id . "', language_id = '" . (int)$language_id . "', title = '" . $this->db->escape($value['title']) . "', description = '" . $this->db->escape($value['description']) . "'");
		}
	}

	public function editBlog($blogger_id, $data) {
		$this->db->query("UPDATE " . DB_PREFIX . "blogger SET module_id = '" . (int)$data['module_id'] . "', status = '" . (int)$data['status'] . "', date_modified = now() WHERE blogger_id = '" . (int)$blogger_id . "'");

		if (isset($data['image'])) {
			$this->db->query("UPDATE " . DB_PREFIX . "blogger SET image = '" . $this->db->escape($data['image']) . "' WHERE blogger_id = '" . (int)$blogger_id . "'");
		}

		$this->db->query("DELETE FROM " . DB_PREFIX . "blogger_description WHERE blogger_id = '" . (int)$blogger_id . "'");

		foreach ($data['blogger_description'] as $language_id => $value) {
			$this->db->query("INSERT INTO " . DB_PREFIX . "blogger_description SET blogger_id = '" . (int)$blogger_id . "', language_id = '" . (int)$language_id . "', title = '" . $this->db->escape($value['title']) . "', description = '" . $this->db->escape($value['description']) . "'");
		}
	}

	public function deleteBlog($blogger_id) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "blogger WHERE blogger_id = '" . (int)$blogger_id . "'");
		echo "DELETE FROM " . DB_PREFIX . "blogger WHERE blogger_id = '" . (int)$blogger_id . "'";
		$this->db->query("DELETE FROM " . DB_PREFIX . "blogger_description WHERE blogger_id = '" . (int)$blogger_id . "'");
	}	

	public function getBlog($blogger_id) {
		$query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "blogger WHERE blogger_id = '" . (int)$blogger_id . "'");

		return $query->row;
	}

	public function getBlogTitle($blogger_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "blogger_description WHERE language_id = '" . (int)$this->config->get('config_language_id') . "' AND blogger_id = '" . (int)$blogger_id . "'");

		if ($query->row) {
			return $query->row['title'];
		} else {
			return false;
		}
	}

	public function getBlogDescriptions($blogger_id) {
		$blogger_description_data = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "blogger_description WHERE blogger_id = '" . (int)$blogger_id . "'");

		foreach ($query->rows as $result) {
			$blogger_description_data[$result['language_id']] = array(
				'title'       => $result['title'],
				'description' => $result['description']
			);
		}

		return $blogger_description_data;
	}

	public function deleteBlogComment($blogger_comment_id) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "blogger_comment WHERE blogger_comment_id = '" . (int)$blogger_comment_id . "'");

		$this->db->query("DELETE FROM " . DB_PREFIX . "blogger_comment_description WHERE blogger_comment_id = '" . (int)$blogger_comment_id . "'");
	}

	public function approveComment($blogger_comment_id) {
		$this->db->query("UPDATE " . DB_PREFIX . "blogger_comment SET approved = '1' WHERE blogger_comment_id = '" . (int)$blogger_comment_id . "'");
	}

	public function disapproveComment($blogger_comment_id) {
		$this->db->query("UPDATE " . DB_PREFIX . "blogger_comment SET approved = '0' WHERE blogger_comment_id = '" . (int)$blogger_comment_id . "'");
	}

	public function getTotalBlogComments($blogger_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "blogger_comment WHERE blogger_id = '" . (int)$blogger_id . "'");

		if ($query->row) {
			return $query->row['total'];
		} else {
			return false;
		}
	}

	public function getBlogComments($blogger_id, $data = array()) {
		if ($data) {
			$sql = "SELECT * FROM " . DB_PREFIX . "blogger_comment WHERE blogger_id = '" . (int)$blogger_id . "'";

			$sort_data = array(
				'author',
				'date_added'
			);

			if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
				$sql .= " ORDER BY " . $data['sort'];
			} else {
				$sql .= " ORDER BY date_added";
			}

			if (isset($data['order']) && ($data['order'] == 'DESC')) {
				$sql .= " DESC";
			} else {
				$sql .= " ASC";
			}

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
			$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "blogger_comment WHERE blogger_id = '" . (int)$blogger_id . "'");

			return $query->rows;
		}
	}

	public function getBlogComment($blogger_comment_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "blogger_comment WHERE blogger_comment_id = '" . (int)$blogger_comment_id . "'");

		return $query->row;
	}

	public function getBlogCommentDescriptions($blogger_comment_id) {
		$blogger_comment_data = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "blogger_comment_description WHERE blogger_comment_id = '" . (int)$blogger_comment_id . "'");

		foreach ($query->rows as $result) {
			$blogger_comment_data[$result['language_id']] = array(
				'comment' => $result['comment']
			);
		}

		return $blogger_comment_data;
	}

	public function getBlogsByModule($module_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "blogger b LEFT JOIN " . DB_PREFIX . "blogger_description bd ON (b.blogger_id = bd.blogger_id) WHERE bd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND b.module_id = '" . (int)$module_id . "' ORDER BY b.date_added");

		return $query->rows;
	}

	public function getBlogs($data = array()) {
		if ($data) {
			$sql = "SELECT * FROM " . DB_PREFIX . "blogger b LEFT JOIN " . DB_PREFIX . "blogger_description bd ON (b.blogger_id = bd.blogger_id) WHERE bd.language_id = '" . (int)$this->config->get('config_language_id') . "'";

			$sort_data = array(
				'bd.title',
				'b.module_id',
				'b.date_added'
			);

			if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
				$sql .= " ORDER BY " . $data['sort'];
			} else {
				$sql .= " ORDER BY b.module_id, b.date_added";
			}

			if (isset($data['order']) && ($data['order'] == 'DESC')) {
				$sql .= " DESC";
			} else {
				$sql .= " ASC";
			}

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
			$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "blogger b LEFT JOIN " . DB_PREFIX . "blogger_description bd ON (b.blogger_id = bd.blogger_id) WHERE bd.language_id = '" . (int)$this->config->get('config_language_id') . "' ORDER BY b.date_added ASC");

			return $query->rows;
		}
	}

	public function getTotalBlogs() {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "blogger");

		return $query->row['total'];
	}

	public function getModule($module_id) {
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "module` WHERE `module_id` = '" . (int)$module_id . "'");

		return $query->row;
	}
}