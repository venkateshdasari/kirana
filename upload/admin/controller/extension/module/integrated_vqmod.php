<?php
class ControllerExtensionModuleIntegratedVQmod extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('extension/module/integrated_vqmod');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('setting/setting');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('module_integrated_vqmod', $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$this->response->redirect($this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true));
		}

		$data['heading_title'] = $this->language->get('heading_title');

		$data['text_edit'] = $this->language->get('text_edit');
		$data['text_enabled'] = $this->language->get('text_enabled');
		$data['text_disabled'] = $this->language->get('text_disabled');

		$data['entry_status'] = $this->language->get('entry_status');

		$data['button_save'] = $this->language->get('button_save');
		$data['button_cancel'] = $this->language->get('button_cancel');

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
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

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('extension/module/integrated_vqmod', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['action'] = $this->url->link('extension/module/integrated_vqmod', 'user_token=' . $this->session->data['user_token'], true);

		$data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true);

		if (isset($this->request->post['module_integrated_vqmod_status'])) {
			$data['module_integrated_vqmod_status'] = $this->request->post['module_integrated_vqmod_status'];
		} else {
			$data['module_integrated_vqmod_status'] = $this->config->get('module_integrated_vqmod_status');
		}

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/module/integrated_vqmod', $data));
	}


	protected function validate() {
		if (!$this->user->hasPermission('modify', 'extension/module/integrated_vqmod')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}


	public function install() {
		// add event handlers
		$this->load->model('setting/event');
		$code = 'module_integrated_vqmod';
		$app = 'admin/';
		$trigger = 'controller/marketplace/modification/refresh/before';
		$route = 'extension/module/integrated_vqmod/ControllerMarketplaceModificationRefreshBefore';
		$this->model_setting_event->addEvent( $code, $app.$trigger, $route );
		$trigger = 'view/marketplace/modification/before';
		$route = 'extension/module/integrated_vqmod/ViewMarketplaceModificationBefore';
		$this->model_setting_event->addEvent( $code, $app.$trigger, $route );
		$trigger = 'controller/marketplace/installer/upload/before';
		$route = 'extension/module/integrated_vqmod/ControllerMarketplaceInstallerUploadBefore';
		$this->model_setting_event->addEvent( $code, $app.$trigger, $route );
		$trigger = 'controller/marketplace/install/xml/before';
		$route = 'extension/module/integrated_vqmod/ControllerMarketplaceInstallXmlBefore';
		$this->model_setting_event->addEvent( $code, $app.$trigger, $route );
		$trigger = 'view/design/layout_form/before';
		$route = 'extension/module/integrated_vqmod/eventViewDesignLayoutFormBefore';
		$this->model_setting_event->addEvent( $code, $app.$trigger, $route );
	}


	public function uninstall() {
		// remove event handlers
		$this->load->model('setting/event');
		$code = 'module_integrated_vqmod';
		$this->model_setting_event->deleteEventByCode( $code );
	}


	protected function vqmodAnother() {
		if (!empty($GLOBALS['vqmod'])) {
			return "Another VQmod 2.3.2 or earlier is already installed.";
		}
		if (class_exists('VQMod',false)) {
			if (VQMod::$directorySeparator) {
				return "Another VQmod 2.4.0 or later is already installed.";
			}
		}
		return '';
	}
	
	protected function vqmodGetIndexes( $search_node_index ) {
		if ($search_node_index) {
			$tmp = explode(',', $search_node_index);
			foreach ($tmp as $k => $v) {
				if (!is_int($v)) {
					unset($k);
				}
			}
			$tmp = array_unique($tmp);
			return empty($tmp) ? false : $tmp;
		} else {
			return false;
		}
	}

	protected function vqmodGetFileKey( $file ) {
		// Get the key to be used for the modification cache filename.
		$key = '';
		if (substr($file, 0, strlen(DIR_CATALOG)) == DIR_CATALOG) {
			$key = 'catalog/' . substr($file, strlen(DIR_CATALOG));
		}
		if (substr($file, 0, strlen(DIR_APPLICATION)) == DIR_APPLICATION) {
			$key = 'admin/' . substr($file, strlen(DIR_APPLICATION));
		}
		if (substr($file, 0, strlen(DIR_SYSTEM)) == DIR_SYSTEM) {
			$key = 'system/' . substr($file, strlen(DIR_SYSTEM));
		}
		return $key;
	}

	protected function vqmodWrite( DOMDocument $dom, &$modification, &$original, &$log ) {
		$modification_node = $dom->getElementsByTagName('modification')->item(0);
		$file_nodes = $modification_node->getElementsByTagName('file');
		$modification_id = $modification_node->getElementsByTagName('id')->item(0)->nodeValue;

		$log[] = "VQmod - Processing '". $modification_id ."'";

		$version = '2.6.1';
		$vqmver_node = $modification_node->getElementsByTagName('vqmver')->item(0);
		if ($vqmver_node) {
			$vqmver_node_required = $vqmver_node->getAttribute('required');
			$vqmver_node_value = $vqmver_node->nodeValue;
			if (strtolower($vqmver_node_required) == 'true') {
				if (version_compare($version, $vqmver_node_value, '<')) {
					$log[] = "VQmod - VQMOD VERSION '" . $vqmver_node_value . "' OR ABOVE REQUIRED, XML FILE HAS BEEN SKIPPED";
					$log[] = "  vqmver = '$vqmver_node_value'";
					$log[] = '----------------------------------------------------------------';
					return;
				}
			}
		}

		foreach ($file_nodes as $file_node) {

			$file_node_path = $file_node->getAttribute('path');
			$file_node_name = $file_node->getAttribute('name');
			$file_node_error = $file_node->getAttribute('error');

			// find all files to be modified
			$files = array();
			$file_names = explode( ',', $file_node_name );
			if ($file_names===false) {
				 $file_names = array();
			}
			foreach ($file_names as $file_name) {
				$path = '';
				if (substr($file_node_path.$file_name, 0, 7) == 'catalog') {
					$path = DIR_CATALOG . substr($file_node_path.$file_name, 8);
				} else if (substr($file_node_path.$file_name, 0, 5) == 'admin') {
					$path = DIR_APPLICATION . substr($file_node_path.$file_name, 6);
				} else if (substr($file_node_path.$file_name, 0, 6) == 'system') {
					$path = DIR_SYSTEM . substr($file_node_path.$file_name, 7);
				}
				$paths = glob($path);
				if (($paths===false) || is_array($paths) && (count($paths)==0)) {
					switch ($file_node_error) {
						case 'skip':
							break;
						case 'abort':
							$log[] = "VQmod - UNABLE TO FIND FILE(S), XML PARSING ABORTED:";
							$log[] = "  file = '$path'";
							$log[] = '----------------------------------------------------------------';
							return;
						case 'log':
						default:
							$log[] = "VQmod - UNABLE TO FIND FILE(S), IGNORED:";
							$log[] = "  file = '$path'";
							break;
					}
				} else {
					foreach ($paths as $file) {
						if (is_file($file)) {
							$files[] = $file;
						} else {
							switch ($file_node_error) {
								case 'skip':
									break;
								case 'abort':
									$log[] = "VQmod - NOT A FILE, XML PARSING ABORTED:";
									$log[] = "  file = '$file'";
									$log[] = '----------------------------------------------------------------';
									return;
								case 'log':
								default:
									$log[] = "VQmod - NOT A FILE, IGNORED:";
									$log[] = "  file = '$file'";
									break;
							}
						}
					}
				}
			}

			$operation_nodes = $file_node->getElementsByTagName('operation');
			
			foreach ($files as $file) {
				$key = $this->vqmodGetFileKey( $file );
				if ($key=='') {
					$log[] = "VQmod - UNABLE TO GENERATE FILE KEY:";
					$log[] = "  file name = '$file'";
					continue;
				}
				if (!isset($modification[$key])) {
					$modification[$key] = preg_replace('~\r?\n~', "\n", file_get_contents($file));
					$original[$key] = $modification[$key];
				}

				foreach ($operation_nodes as $operation_node) {
					$continue_with_next_operation_node = false;
					$operation_node_error = $operation_node->getAttribute('error');
					if (($operation_node_error != 'skip') && ($operation_node_error != 'log')) {
						$operation_node_error = 'abort';
					}

					$ignoreif_node = $operation_node->getElementsByTagName('ignoreif')->item(0);
					if ($ignoreif_node) {
						$ignoreif_node_regex = $ignoreif_node->getAttribute('regex');
						$ignoreif_node_value = trim( $ignoreif_node->nodeValue );
						if ($ignoreif_node_regex == 'true') {
							if (preg_match($ignoreif_node_value, $modification[$key])) {
								continue;
							}
						} else {
							if (strpos($modification[$key], $ignoreif_node_value) !== false) {
								continue;
							}
						}
					}

					$search_node_list = $operation_node->getElementsByTagName('search');
					$search_node = ($search_node_list->length > 0) ? $search_node_list->item(0) : null;
					if ($search_node == null) {
						if ($operation_node_error == 'log') {
							$log[] = "VQmod - Missing <search> tag (SKIPPED):";
							$log[] = "  file name = '$file'";
						} else if ($operation_node_error == 'abort') {
							$log[] = "VQmod - Missing <search> tag (ABORTING MOD):";
							$log[] = "  file name = '$file'";
						}
						if ($operation_node_error == 'abort') {
							$log[] = '----------------------------------------------------------------';
							return; // skip this XML file
						}
						continue; // continue with next <operation> node
					}

					$search_node_position = ($search_node->getAttribute('position')) ? $search_node->getAttribute('position') : 'replace';
					$search_node_indexes = $this->vqmodGetIndexes( $search_node->getAttribute('index') );
					$search_node_offset = ($search_node->getAttribute('offset')) ? $search_node->getAttribute('offset') : '0';
					$search_node_regex = ($search_node->getAttribute('regex')) ? $search_node->getAttribute('regex') : 'false';
					$search_node_trim = ($search_node->getAttribute('trim')=='false') ? 'false' : 'true';
					$search_node_value = ($search_node_trim=='true') ? trim($search_node->nodeValue) : $search_node->nodeValue;

					$add_node_list = $operation_node->getElementsByTagName('add');
					$add_node = ($add_node_list->length > 0) ? $add_node_list->item(0) : null;
					if ($add_node == null) {
						if ($operation_node_error == 'log') {
							$log[] = "VQmod - Missing <add> tag (SKIPPED):";
							$log[] = "  file name = '$file'";
						} else if ($operation_node_error == 'abort') {
							$log[] = "VQmod - Missing <add> tag (ABORTING MOD):";
							$log[] = "  file name = '$file'";
						}
						if ($operation_node_error == 'abort') {
							$log[] = '----------------------------------------------------------------';
							return; // skip this XML file
						}
						continue; // continue with next <operation> node
					}

					$add_node_trim = ($add_node->getAttribute('trim')=='true') ? 'true' : 'false';
					$add_node_value = ($add_node_trim=='true') ? trim($add_node->nodeValue) : $add_node->nodeValue;
					
					if ($add_node->getAttribute('position')) {
						$search_node_position = $add_node->getAttribute('position');
					}
					if ($add_node->getAttribute('index')) {
						$search_node_indexes = $this->vqmodGetIndexes( $add_node->getAttribute('index') );
					}
					if ($add_node->getAttribute('offset')) {
						$search_node_offset = $add_node->getAttribute('offset');
					}
					if ($add_node->getAttribute('regex')) {
						$search_node_regex = $add_node->getAttribute('regex');
					}

					$index_count = 0;
					$tmp = explode("\n",$modification[$key]);
					$line_max = count($tmp)-1;

					// apply the next search and add operation to the file content
					switch ($search_node_position) {
						case 'top':
							$tmp[(int)$search_node_offset] = $add_node_value . $tmp[(int)$search_node_offset];
							break;
						case 'bottom':
							$offset = $line_max - (int)$search_node_offset;
							if ($offset < 0) {
								$tmp[-1] = $add_node_value;
							} else {
								$tmp[$offset] .= $add_node_value;;
							}
							break;
						default:
							$changed = false;
							foreach ($tmp as $line_num => $line) {
								if (strlen($search_node_value) == 0) {
									if ($operation_node_error == 'log' || $operation_node_error == 'abort') {
										$log[] = "VQmod - EMPTY SEARCH CONTENT ERROR:";
										$log[] = "  file name = '$file'";
									}
									break;
								}
								
								if ($search_node_regex == 'true') {
									$pos = @preg_match($search_node_value, $line);
									if ($pos === false) {
										if ($operation_node_error == 'log' || $operation_node_error == 'abort') {
											$log[] = "VQmod - INVALID REGEX ERROR:";
											$log[] = "  file name = '$file'";
											$log[] = "  search = '$search_node_value'";
										}
										$continue_with_next_operation_node = true;
										break;
//										continue 2; // continue with next operation_node
									} elseif ($pos == 0) {
										$pos = false;
									}
								} else {
									$pos = strpos($line, $search_node_value);
								}

								if ($pos !== false) {
									$index_count++;
									$changed = true;

									if (!$search_node_indexes || ($search_node_indexes && in_array($index_count, $search_node_indexes))) {
										switch ($search_node_position) {
											case 'before':
												$offset = ($line_num - $search_node_offset < 0) ? -1 : $line_num - $search_node_offset;
												$tmp[$offset] = empty($tmp[$offset]) ? $add_node_value : $add_node_value . "\n" . $tmp[$offset];
												break;
											case 'after':
												$offset = ($line_num + $search_node_offset > $line_max) ? $line_max : $line_num + $search_node_offset;
												$tmp[$offset] = $tmp[$offset] . "\n" . $add_node_value;
												break;
											case 'ibefore':
												$tmp[$line_num] = str_replace($search_node_value, $add_node_value . $search_node_value, $line);
												break;
											case 'iafter':
												$tmp[$line_num] = str_replace($search_node_value, $search_node_value . $add_node_value, $line);
												break;
											default:
												if (!empty($search_node_offset)) {
													for ($i = 1; $i <= $search_node_offset; $i++) {
														if (isset($tmp[$line_num + $i])) {
															$tmp[$line_num + $i] = '';
														}
													}
												}
												if ($search_node_regex == 'true') {
													$tmp[$line_num] = preg_replace( $search_node_value, $add_node_value, $line);
												} else {
													$tmp[$line_num] = str_replace( $search_node_value, $add_node_value, $line);
												}
												break;
										}
									}
								}
							}

							if (!$changed) {
								if ($operation_node_error == 'log') {
									$log[] = "VQmod - SEARCH NOT FOUND (SKIPPED):";
									$log[] = "  file name = '$file'";
									$log[] = "  search = '$search_node_value'";
								} else if ($operation_node_error == 'abort') {
									$log[] = "VQmod - SEARCH NOT FOUND (ABORTING MOD):";
									$log[] = "  file name = '$file'";
									$log[] = "  search = '$search_node_value'";
								}
								if ($operation_node_error == 'abort') {
									$log[] = '----------------------------------------------------------------';
									return; // skip this XML file
								}
							}
							break;
					}

					if ($continue_with_next_operation_node) {
						continue;
					}

					ksort($tmp);

					$modification[$key] = implode("\n", $tmp);

				} // $operation_nodes
			} // $files
		} // $file_nodes

		$log[] = "VQmod - Done '". $modification_id ."'";
		$log[] = '----------------------------------------------------------------';

	}


	protected function isVqmod( DOMDocument $dom ) {
		$modification_node = $dom->getElementsByTagName('modification')->item(0);
		if ($modification_node) {
			$vqmver_node = $modification_node->getElementsByTagName('vqmver')->item(0);
			if ($vqmver_node) {
				return true;
			}
		}
		return false;
	}


	// event handler for trigger admin/controller/marketplace/modification/refresh/before
	public function ControllerMarketplaceModificationRefreshBefore( &$route, &$data ) {
		if (!$this->config->get('module_integrated_vqmod_status')) {
			return null;
		}

		$this->load->language('marketplace/modification');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('setting/modification');

		if ($this->validate()) {
			// Just before files are deleted, if config settings say maintenance mode is off then turn it on
			$maintenance = $this->config->get('config_maintenance');

			$this->load->model('setting/setting');

			$this->model_setting_setting->editSettingValue('config', 'config_maintenance', true);

			//Log
			$log = array();

			// Clear all modification files
			$files = array();

			// Make path into an array
			$path = array(DIR_MODIFICATION . '*');

			// While the path array is still populated keep looping through
			while (count($path) != 0) {
				$next = array_shift($path);

				foreach (glob($next) as $file) {
					// If directory add to path array
					if (is_dir($file)) {
						$path[] = $file . '/*';
					}

					// Add the file to the files to be deleted array
					$files[] = $file;
				}
			}

			// Reverse sort the file array
			rsort($files);

			// Clear all modification files
			foreach ($files as $file) {
				if ($file != DIR_MODIFICATION . 'index.html') {
					// If file just delete
					if (is_file($file)) {
						unlink($file);

					// If directory use the remove directory function
					} elseif (is_dir($file)) {
						rmdir($file);
					}
				}
			}

			// Begin
			$xml = array();

			// Load the default modification XML
			$xml[] = file_get_contents(DIR_SYSTEM . 'modification.xml');

			// This is purly for developers so they can run mods directly and have them run without upload after each change.
			$files = glob(DIR_SYSTEM.'*.{ocmod,vqmod}.xml',GLOB_BRACE);

			if ($files) {
				foreach ($files as $file) {
					$xml[] = file_get_contents($file);
				}
			}

			// For traditional VQmod files loaded from 'vqmod/xml' folder, to have them run without upload after each change.
			$another = $this->vqmodAnother();
			if (!$another) {
				$files = is_dir(DIR_SYSTEM.'../vqmod/xml') ? glob(DIR_SYSTEM.'../vqmod/xml/*.xml') : array();
			} else {
				$files = array();
				$log[] = $another;
				$log[] = 'The files from vqmod/xml/*.xml are processed by the other VQmod!';
				$log[] = '----------------------------------------------------------------';
			}
			if ($files) {
				foreach ($files as $file) {
					if (basename($file)=='vqmod_opencart.xml') {
						continue;
					}
					$xml[] = file_get_contents($file);
				}
			}

			// Get the default modification file
			$results = $this->model_setting_modification->getModifications();

			foreach ($results as $result) {
				if ($result['status']) {
					$xml[] = $result['xml'];
				}
			}

			$modification = array();
			$original = array();

			foreach ($xml as $xml) {
				if(empty($xml)){
					continue;
				}
				$dom = new DOMDocument('1.0', 'UTF-8');
				$dom->preserveWhiteSpace = false;
				$dom->loadXml($xml);

				if ($this->isVqmod( $dom )) {
					$this->vqmodWrite( $dom, $modification, $original, $log );
					continue;
				}

				// Log
				$log[] = 'MOD: ' . $dom->getElementsByTagName('name')->item(0)->textContent;

				// Wipe the past modification store in the backup array
				$recovery = array();

				// Set the a recovery of the modification code in case we need to use it if an abort attribute is used.
				if (isset($modification)) {
					$recovery = $modification;
				}

				$files = $dom->getElementsByTagName('modification')->item(0)->getElementsByTagName('file');

				foreach ($files as $file) {
					$operations = $file->getElementsByTagName('operation');

					$files = explode('|', $file->getAttribute('path'));

					foreach ($files as $file) {
						$path = '';

						// Get the full path of the files that are going to be used for modification
						if (substr($file, 0, 7) == 'catalog') {
							$path = DIR_CATALOG . substr($file, 8);
						}

						if (substr($file, 0, 5) == 'admin') {
							$path = DIR_APPLICATION . substr($file, 6);
						}

						if (substr($file, 0, 6) == 'system') {
							$path = DIR_SYSTEM . substr($file, 7);
						}

						if ($path) {
							$files = glob($path, GLOB_BRACE);

							if ($files) {
								foreach ($files as $file) {
									// Get the key to be used for the modification cache filename.
									if (substr($file, 0, strlen(DIR_CATALOG)) == DIR_CATALOG) {
										$key = 'catalog/' . substr($file, strlen(DIR_CATALOG));
									}

									if (substr($file, 0, strlen(DIR_APPLICATION)) == DIR_APPLICATION) {
										$key = 'admin/' . substr($file, strlen(DIR_APPLICATION));
									}

									if (substr($file, 0, strlen(DIR_SYSTEM)) == DIR_SYSTEM) {
										$key = 'system/' . substr($file, strlen(DIR_SYSTEM));
									}

									// If file contents is not already in the modification array we need to load it.
									if (!isset($modification[$key])) {
										$content = file_get_contents($file);

										$modification[$key] = preg_replace('~\r?\n~', "\n", $content);
										$original[$key] = preg_replace('~\r?\n~', "\n", $content);

										// Log
										$log[] = PHP_EOL . 'FILE: ' . $key;
									}

									foreach ($operations as $operation) {
										$error = $operation->getAttribute('error');

										// Ignoreif
										$ignoreif = $operation->getElementsByTagName('ignoreif')->item(0);

										if ($ignoreif) {
											if ($ignoreif->getAttribute('regex') != 'true') {
												if (strpos($modification[$key], $ignoreif->textContent) !== false) {
													continue;
												}
											} else {
												if (preg_match($ignoreif->textContent, $modification[$key])) {
													continue;
												}
											}
										}

										$status = false;

										// Search and replace
										if ($operation->getElementsByTagName('search')->item(0)->getAttribute('regex') != 'true') {
											// Search
											$search = $operation->getElementsByTagName('search')->item(0)->textContent;
											$trim = $operation->getElementsByTagName('search')->item(0)->getAttribute('trim');
											$index = $operation->getElementsByTagName('search')->item(0)->getAttribute('index');

											// Trim line if no trim attribute is set or is set to true.
											if (!$trim || $trim == 'true') {
												$search = trim($search);
											}

											// Add
											$add = $operation->getElementsByTagName('add')->item(0)->textContent;
											$trim = $operation->getElementsByTagName('add')->item(0)->getAttribute('trim');
											$position = $operation->getElementsByTagName('add')->item(0)->getAttribute('position');
											$offset = $operation->getElementsByTagName('add')->item(0)->getAttribute('offset');

											if ($offset == '') {
												$offset = 0;
											}

											// Trim line if is set to true.
											if ($trim == 'true') {
												$add = trim($add);
											}

											// Log
											$log[] = 'CODE: ' . $search;

											// Check if using indexes
											if ($index !== '') {
												$indexes = explode(',', $index);
											} else {
												$indexes = array();
											}

											// Get all the matches
											$i = 0;

											$lines = explode("\n", $modification[$key]);

											for ($line_id = 0; $line_id < count($lines); $line_id++) {
												$line = $lines[$line_id];

												// Status
												$match = false;

												// Check to see if the line matches the search code.
												if (stripos($line, $search) !== false) {
													// If indexes are not used then just set the found status to true.
													if (!$indexes) {
														$match = true;
													} elseif (in_array($i, $indexes)) {
														$match = true;
													}

													$i++;
												}

												// Now for replacing or adding to the matched elements
												if ($match) {
													switch ($position) {
														default:
														case 'replace':
															$new_lines = explode("\n", $add);

															if ($offset < 0) {
																array_splice($lines, $line_id + $offset, abs($offset) + 1, array(str_replace($search, $add, $line)));

																$line_id -= $offset;
															} else {
																array_splice($lines, $line_id, $offset + 1, array(str_replace($search, $add, $line)));
															}

															break;
														case 'before':
															$new_lines = explode("\n", $add);

															array_splice($lines, $line_id - $offset, 0, $new_lines);

															$line_id += count($new_lines);
															break;
														case 'after':
															$new_lines = explode("\n", $add);

															array_splice($lines, ($line_id + 1) + $offset, 0, $new_lines);

															$line_id += count($new_lines);
															break;
													}

													// Log
													$log[] = 'LINE: ' . $line_id;

													$status = true;
												}
											}

											$modification[$key] = implode("\n", $lines);
										} else {
											$search = trim($operation->getElementsByTagName('search')->item(0)->textContent);
											$limit = $operation->getElementsByTagName('search')->item(0)->getAttribute('limit');
											$replace = trim($operation->getElementsByTagName('add')->item(0)->textContent);

											// Limit
											if (!$limit) {
												$limit = -1;
											}

											// Log
											$match = array();

											preg_match_all($search, $modification[$key], $match, PREG_OFFSET_CAPTURE);

											// Remove part of the the result if a limit is set.
											if ($limit > 0) {
												$match[0] = array_slice($match[0], 0, $limit);
											}

											if ($match[0]) {
												$log[] = 'REGEX: ' . $search;

												for ($i = 0; $i < count($match[0]); $i++) {
													$log[] = 'LINE: ' . (substr_count(substr($modification[$key], 0, $match[0][$i][1]), "\n") + 1);
												}

												$status = true;
											}

											// Make the modification
											$modification[$key] = preg_replace($search, $replace, $modification[$key], $limit);
										}

										if (!$status) {
											// Abort applying this modification completely.
											if ($error == 'abort') {
												$modification = $recovery;
												// Log
												$log[] = 'NOT FOUND - ABORTING!';
												break 5;
											}
											// Skip current operation or break
											elseif ($error == 'skip') {
												// Log
												$log[] = 'NOT FOUND - OPERATION SKIPPED!';
												continue;
											}
											// Break current operations
											else {
												// Log
												$log[] = 'NOT FOUND - OPERATIONS ABORTED!';
											 	break;
											}
										}
									}
								}
							}
						}
					}
				}

				// Log
				$log[] = '----------------------------------------------------------------';
			}

			// Log
			$ocmod = new Log('ocmod.log');
			$ocmod->write(implode("\n", $log));

			// Write all modification files
			foreach ($modification as $key => $value) {
				// Only create a file if there are changes
				if ($original[$key] != $value) {
					$path = '';

					$directories = explode('/', dirname($key));

					foreach ($directories as $directory) {
						$path = ($path=='') ? $directory : $path . '/' . $directory;

						if (!is_dir(DIR_MODIFICATION . $path)) {
							@mkdir(DIR_MODIFICATION . $path, 0777);
						}
					}

					$handle = fopen(DIR_MODIFICATION . $key, 'w');

					fwrite($handle, $value);

					fclose($handle);
				}
			}

			// Maintance mode back to original settings
			$this->model_setting_setting->editSettingValue('config', 'config_maintenance', $maintenance);

			// Do not return success message if refresh() was called with $data
			if (!empty($data['redirect'])) {
				$this->session->data['success'] = $this->language->get('text_success');
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

			$this->response->redirect($this->url->link(!empty($data['redirect']) ? $data['redirect'] : 'marketplace/modification', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		return null;
	}


	protected function getOtherModifications() {
		$xml_files = array();
		$files = glob(DIR_SYSTEM.'*.{ocmod,vqmod}.xml',GLOB_BRACE);

		if ($files) {
			foreach ($files as $file) {
				$xml_files[] = array( 'content'=>file_get_contents($file), 'file'=>$file );
			}
		}

		$files = is_dir(DIR_SYSTEM.'../vqmod/xml') ? glob(DIR_SYSTEM.'../vqmod/xml/*.{xml}',GLOB_BRACE) : array();

		if ($files) {
			foreach ($files as $file) {
				if (basename($file) == 'vqmod_opencart.xml') {
					continue;
				}
				$xml_files[] = array( 'content'=>file_get_contents($file), 'file'=>$file );
			}
		}

		$modifications = array();
		foreach ($xml_files as $xml_file) {
			$xml = $xml_file['content'];
			$file = $xml_file['file'];
			if (empty($xml)) {
				continue;
			}
			$dom = new DOMDocument('1.0', 'UTF-8');
			$dom->preserveWhiteSpace = false;
			$dom->loadXml($xml);

			$name_node = $dom->getElementsByTagName('name')->item(0);
			if ($name_node) {
				$name = $name_node->nodeValue;
			} else {
				$id_node = $dom->getElementsByTagName('id')->item(0);
				if ($id_node) {
					$name = $id_node->nodeValue;
				} else {
					$name = '';
				}
			}
			if ($name) {
				$name .= "\n";
			}

			$i = strpos( $file, '/vqmod/xml/', 0 );
			if ($i === false) {
				$name .= '(' . substr( $file, strlen( realpath(DIR_SYSTEM.'../').'/' ) ) . ')';
			} else {
				$name .= '(' . substr( $file, $i+1 ) . ')';
			}

			$author_node = $dom->getElementsByTagName('author')->item(0);
			if ($author_node) {
				$author = $author_node->nodeValue;
			} else {
				$author = '';
			}

			$version_node = $dom->getElementsByTagName('version')->item(0);
			if ($version_node) {
				$version = $version_node->nodeValue;
			} else {
				$version = '';
			}

			$link_node = $dom->getElementsByTagName('link')->item(0);
			if ($link_node) {
				$link = $link_node->nodeValue;
			} else {
				$link = '';
			}

			if ((strlen($file)>4) && (substr($file,strlen($file)-4)=='.xml')) {
				$status = $this->language->get('text_enabled');
				$enabled = true;
			} else {
				$status = $this->language->get('text_disabled');
				$enabled = false;
			}

			$date_added = date( $this->language->get('date_format_short'), filemtime($file) );

			$modifications[] = array(
				'modification_id' => 0,
				'name'            => $name,
				'author'          => $author,
				'version'         => $version,
				'status'          => $status,
				'date_added'      => $date_added,
				'link'            => $link,
				'enable'          => $this->url->link('marketplace/modification/enable', 'user_token=' . $this->session->data['user_token'] . '&modification_id=0', true),
				'disable'         => $this->url->link('marketplace/modification/disable', 'user_token=' . $this->session->data['user_token'] . '&modification_id=0', true),
				'enabled'         => $enabled,
				'date'            => date( "Y-m-d H:i:s", filemtime($file) )
			);
		
		}

		return $modifications;
	}


	protected function getTotalOtherModifications() {
		$xml_files = array();
		$files = glob(DIR_SYSTEM.'*.{ocmod,vqmod}.xml',GLOB_BRACE);

		if ($files) {
			foreach ($files as $file) {
				$xml_files[] = array( 'content'=>file_get_contents($file), 'file'=>$file );
			}
		}

		$files = is_dir(DIR_SYSTEM.'../vqmod/xml') ? glob(DIR_SYSTEM.'../vqmod/xml/*.{xml}',GLOB_BRACE) : array();

		if ($files) {
			foreach ($files as $file) {
				if (basename($file)=='vqmod_opencart.xml') {
					continue;
				}
				$xml_files[] = array( 'content'=>file_get_contents($file), 'file'=>$file );
			}
		}

		$modifications = array();
		foreach ($xml_files as $xml_file) {
			$xml = $xml_file['content'];
			$file = $xml_file['file'];
			if (empty($xml)) {
				continue;
			}
			$modifications[] = $xml_file;
		}

		return count($modifications);
	}


	protected function compareModificationsByNameASC( $modification1, $modification2 ) {
		return strcmp( $modification1['name'], $modification2['name'] );
	}


	protected function compareModificationsByNameDESC( $modification1, $modification2 ) {
		return -strcmp( $modification1['name'], $modification2['name'] );
	}


	protected function compareModificationsByAuthorASC( $modification1, $modification2 ) {
		return strcmp( $modification1['author'], $modification2['author'] );
	}


	protected function compareModificationsByAuthorDESC( $modification1, $modification2 ) {
		return -strcmp( $modification1['author'], $modification2['author'] );
	}


	protected function compareModificationsByVersionASC( $modification1, $modification2 ) {
		return version_compare( $modification1['version'], $modification2['version'] );
	}


	protected function compareModificationsByVersionDESC( $modification1, $modification2 ) {
		return -version_compare( $modification1['version'], $modification2['version'] );
	}


	protected function compareModificationsByStatusASC( $modification1, $modification2 ) {
		return strcmp( $modification1['status'], $modification2['status'] );
	}


	protected function compareModificationsByStatusDESC( $modification1, $modification2 ) {
		return -strcmp( $modification1['status'], $modification2['status'] );
	}


	protected function compareModificationsByDateASC( $modification1, $modification2 ) {
		return strcmp( $modification1['date'], $modification2['date'] );
	}


	protected function compareModificationsByDateDESC( $modification1, $modification2 ) {
		return -strcmp( $modification1['date'], $modification2['date'] );
	}


	protected function getModifications($filter_data) {
		if (isset($filter_data['start'])) {
			$start = $filter_data['start'];
			if ($filter_data['start'] < 0) {
				$start = 0;
			}
			unset($filter_data['start']);
		}

		if (isset($filter_data['limit'])) {
			$limit = $filter_data['limit'];
			if ($filter_data['limit'] < 1) {
				$limit = 20;
			}
			unset($filter_data['limit']);
		}

		$results = $this->model_setting_modification->getModifications($filter_data);

		$modifications = array();
		foreach ($results as $result) {
			$modifications[] = array(
				'modification_id' => $result['modification_id'],
				'name'            => $result['name'],
				'author'          => $result['author'],
				'version'         => $result['version'],
				'status'          => $result['status'] ? $this->language->get('text_enabled') : $this->language->get('text_disabled'),
				'date_added'      => date($this->language->get('date_format_short'), strtotime($result['date_added'])),
				'link'            => $result['link'],
				'enable'          => $this->url->link('marketplace/modification/enable', 'user_token=' . $this->session->data['user_token'] . '&modification_id=' . $result['modification_id'], true),
				'disable'         => $this->url->link('marketplace/modification/disable', 'user_token=' . $this->session->data['user_token'] . '&modification_id=' . $result['modification_id'], true),
				'enabled'         => $result['status'],
				'date'            => $result['date_added']
			);
		}

		$modifications = array_merge( $modifications, $this->getOtherModifications() );

		$order = 'ASC';
		if (isset($filter_data['order'])) {
			if ($filter_data['order'] == 'DESC') {
				$order = 'DESC';
			}
		}
		
		$sort = 'Name';
		if (isset($filter_data['sort'])) {
			switch ($filter_data['sort']) {
				case 'name':
					$sort = 'Name';
					break;
				case 'author':
					$sort = 'Author';
					break;
				case 'version':
					$sort = 'Version';
					break;
				case 'status':
					$sort = 'Status';
					break;
				case 'date_added':
					$sort = 'Date';
					break;
				default:
					$sort = 'Name';
					break;
			}
		}

		usort( $modifications, array( $this, "compareModificationsBy$sort$order" ) );
		foreach ($modifications as $key=>$modification) {
			$modifications[$key]['name'] = str_replace( "\n", "<br />", htmlspecialchars( $modification['name'] ) );
		}
		
		$results = array();
		$i = 0;
		foreach ($modifications as $modification) {
			if (($i >= $start) && ($i < ($start+$limit))) {
				$results[] = $modification;
			}
			$i += 1;
		}

		return $results;
	}


	protected function getTotalModifications() {
		$total = $this->model_setting_modification->getTotalModifications();
		$total += $this->getTotalOtherModifications();
		return $total;
	}


	// event handler for trigger admin/view/marketplace/modification/before
	public function ViewMarketplaceModificationBefore( &$route, &$data ) {
		if (!$this->config->get('module_integrated_vqmod_status')) {
			return null;
		}

		if (isset($this->request->get['sort'])) {
			$sort = $this->request->get['sort'];
		} else {
			$sort = 'name';
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

		// update modifications, they now also include VQmod XML files
		$data['modifications'] = array();
		$filter_data = array(
			'sort'  => $sort,
			'order' => $order,
			'start' => ($page - 1) * $this->config->get('config_limit_admin'),
			'limit' => $this->config->get('config_limit_admin')
		);
		$modification_total = $this->getTotalModifications();
		$data['modifications'] = $this->getModifications($filter_data);

		$url = '';
		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}
		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		// update pagination, they now also include VQmod XML files
		$pagination = new Pagination();
		$pagination->total = $modification_total;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_limit_admin');
		$pagination->url = $this->url->link('marketplace/modification', 'user_token=' . $this->session->data['user_token'] . $url . '&page={page}', true);
		$data['pagination'] = $pagination->render();
		$data['results'] = sprintf($this->language->get('text_pagination'), ($modification_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($modification_total - $this->config->get('config_limit_admin'))) ? $modification_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $modification_total, ceil($modification_total / $this->config->get('config_limit_admin')));

		// we use our own modified template
		$route = 'extension/module/integrated_vqmod_marketplace_modification';

		return null;
	}


	// event handler for trigger admin/controller/marketplace/installer/upload/before
	public function ControllerMarketplaceInstallerUploadBefore( &$route, &$data ) {
		if (!$this->config->get('module_integrated_vqmod_status')) {
			return null;
		}

		$this->load->language('marketplace/installer');

		$json = array();

		// Check user has permission
		if (!$this->user->hasPermission('modify', 'marketplace/installer')) {
			$json['error'] = $this->language->get('error_permission');
		}

		// Check if there is a install zip already there
		$files = glob(DIR_UPLOAD . '*.tmp');

		foreach ($files as $file) {
			if (is_file($file) && (filectime($file) < (time() - 5))) {
				unlink($file);
			}
			
			if (is_file($file)) {
				$json['error'] = $this->language->get('error_install');
				
				break;
			}
		}

		// Check for any install directories
		$directories = glob(DIR_UPLOAD . 'tmp-*');
		
		foreach ($directories as $directory) {
			if (is_dir($directory) && (filectime($directory) < (time() - 5))) {
				// Get a list of files ready to upload
				$files = array();
	
				$path = array($directory);
	
				while (count($path) != 0) {
					$next = array_shift($path);
	
					// We have to use scandir function because glob will not pick up dot files.
					foreach (array_diff(scandir($next), array('.', '..')) as $file) {
						$file = $next . '/' . $file;
	
						if (is_dir($file)) {
							$path[] = $file;
						}
	
						$files[] = $file;
					}
				}
	
				rsort($files);
	
				foreach ($files as $file) {
					if (is_file($file)) {
						unlink($file);
					} elseif (is_dir($file)) {
						rmdir($file);
					}
				}
	
				rmdir($directory);
			}
			
			if (is_dir($directory)) {
				$json['error'] = $this->language->get('error_install');
				
				break;
			}		
		}
		
		if (isset($this->request->files['file']['name'])) {
			if (substr($this->request->files['file']['name'], -10) != '.ocmod.zip' && substr($this->request->files['file']['name'], -10) != '.vqmod.zip') {
				$json['error'] = $this->language->get('error_filetype');
			}

			if ($this->request->files['file']['error'] != UPLOAD_ERR_OK) {
				$json['error'] = $this->language->get('error_upload_' . $this->request->files['file']['error']);
			}
		} else {
			$json['error'] = $this->language->get('error_upload');
		}

		if (!$json) {
			$this->session->data['install'] = token(10);
			
			$file = DIR_UPLOAD . $this->session->data['install'] . '.tmp';
			
			move_uploaded_file($this->request->files['file']['tmp_name'], $file);

			if (is_file($file)) {
				$this->load->model('setting/extension');
				
				$extension_install_id = $this->model_setting_extension->addExtensionInstall($this->request->files['file']['name']);
				
				$json['text'] = $this->language->get('text_install');

				$json['next'] = str_replace('&amp;', '&', $this->url->link('marketplace/install/install', 'user_token=' . $this->session->data['user_token'] . '&extension_install_id=' . $extension_install_id, true));		
			} else {
				$json['error'] = $this->language->get('error_file');
			}
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));

		return '';
	}


	// event handler for trigger admin/controller/marketplace/install/xml/before
	public function ControllerMarketplaceInstallXmlBefore( &$route, &$data ) {
		if (!$this->config->get('module_integrated_vqmod_status')) {
			return null;
		}

		$this->load->language('marketplace/install');

		$json = array();
		
		if (isset($this->request->get['extension_install_id'])) {
			$extension_install_id = $this->request->get['extension_install_id'];
		} else {
			$extension_install_id = 0;
		}

		if (!$this->user->hasPermission('modify', 'marketplace/install')) {
			$json['error'] = $this->language->get('error_permission');
		}

		if (!isset($this->session->data['install'])) {
			$json['error'] = $this->language->get('error_directory');
		} elseif (!is_dir(DIR_UPLOAD . 'tmp-' . $this->session->data['install'] . '/')) {
			$json['error'] = $this->language->get('error_directory');
		}

		if (!$json) {
			$file = DIR_UPLOAD . 'tmp-' . $this->session->data['install'] . '/install.xml';

			if (is_file($file)) {
				// If xml file just put it straight into the DB
				$xml = file_get_contents($file);
	
				if ($xml) {
					try {
						$dom = new DOMDocument('1.0', 'UTF-8');
						$dom->loadXml($xml);
	
						$name = $dom->getElementsByTagName('name')->item(0);
	
						if ($name) {
							$name = $name->nodeValue;
						} else {
							$name = $dom->getElementsByTagName('id')->item(0);
							if ($name) {
								$name = $name->nodeValue;
							} else {
								$name = '';
							}
						}
	
						$code = $dom->getElementsByTagName('code')->item(0);
						if (!$code) {
							$code = $dom->getElementsByTagName('id')->item(0);
						}
	
						if ($code) {
							$code = $code->nodeValue;
	
							// Check to see if the modification is already installed or not.
							$this->load->model('setting/modification');
							$modification_info = $this->model_setting_modification->getModificationByCode($code);
	
							if ($modification_info) {
								$json['error'] = sprintf($this->language->get('error_xml'), $modification_info['name']);
							}
						} else {
							$json['error'] = $this->language->get('error_code');
						}
	
						$author = $dom->getElementsByTagName('author')->item(0);
	
						if ($author) {
							$author = $author->nodeValue;
						} else {
							$author = '';
						}
	
						$version = $dom->getElementsByTagName('version')->item(0);
	
						if ($version) {
							$version = $version->nodeValue;
						} else {
							$version = '';
						}
	
						$link = $dom->getElementsByTagName('link')->item(0);
	
						if ($link) {
							$link = $link->nodeValue;
						} else {
							$link = '';
						}
	
						if (!$json) {
							$modification_data = array(
								'extension_install_id' => $extension_install_id,
								'name'                 => $name,
								'code'                 => $code,
								'author'               => $author,
								'version'              => $version,
								'link'                 => $link,
								'xml'                  => $xml,
								'status'               => 1
							);
	
							$this->load->model('setting/modification');
	
							$this->model_setting_modification->addModification($modification_data);
						}
					} catch(Exception $exception) {
						$json['error'] = sprintf($this->language->get('error_exception'), $exception->getCode(), $exception->getMessage(), $exception->getFile(), $exception->getLine());
					}
				}
			}
		}

		if (!$json) {
			if (version_compare(VERSION,'3.1.0.0','<')) {
				$json['text'] = $this->language->get('text_remove');
				$json['next'] = str_replace('&amp;', '&', $this->url->link('marketplace/install/remove', 'user_token=' . $this->session->data['user_token'], true));
			} else {
				$json['text'] = $this->language->get('text_clear');
				$json['next'] = str_replace('&amp;', '&', $this->url->link('marketplace/install/clear', 'user_token=' . $this->session->data['user_token'], true));
			}
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));

		return '';
	}


	// event handler for admin/view/design/layout_form/before
	public function eventViewDesignLayoutFormBefore( &$route, &$data, &$template ) {
		foreach ($data['extensions'] as $key=>$extension) {
			if ($extension['code'] == 'integrated_vqmod') {
				unset($data['extensions'][$key]);
			}
		}
		return null;
	}
}