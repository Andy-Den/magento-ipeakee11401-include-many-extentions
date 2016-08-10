<?php
class Balance_Datafeed_Model_Datafeed extends Mage_Core_Model_Abstract
{
	public $_indexPhp = '';
	protected $_filePath;
	public $_limit = false;
	public $_display = false;
	public $_rates = false;
	public $_chartset = false;
	public $_sqlSize = 1500;
	static $option = 0;

	public function xf0($myPattern, $product, $xf = true, $getfirstline = false)
	{
		if ($getfirstline) {
			$lines = preg_split("/\n/", $myPattern);
			$first_line = $lines[0];
			$lines[0] = null;
			$myPattern = implode($lines, "\n");
		}

		$myPattern = str_replace('<?', utf8_encode('¤'), $myPattern);
		$myPattern = str_replace('?>', utf8_encode('¤'), $myPattern);
		$x13 = utf8_encode('/(¤(.[^¤]+)¤)/s');
		preg_match_all($x13, $myPattern, $m);
		if (isset($m[1])) {
			foreach ($m[1] as $key => $x16) {
				if ($xf == 1) {
					if (@eval($m[2][$key] . ';'))
						$myPattern = str_replace($x16, eval($m[2][$key] . ';'), $myPattern);
					else
						$myPattern = str_replace($x16, '', $myPattern);

				}
				else {
					if (@eval($this->unescape($m[2][$key] . ';')))
						$myPattern = str_replace($x16, $this->escape(eval($this->unescape($m[2][$key]) . ';')), $myPattern);
					else $myPattern = str_replace($x16, '', $myPattern);
				}
			}
		}

		if ($getfirstline) {
			if ($xf == 1)
				return $first_line . "\n" . $myPattern;
			else
				return $first_line;
		}
		else return $myPattern;
	}

	protected function _construct()
	{
		$this->_sqlSize = Mage::getStoreConfig("datafeed/system/sqlsize");
		$this->_init('datafeed/datafeed');
	}

	protected function _beforeSave() {
		$file = new Varien_Io_File();
		$path = $file->getCleanPath(Mage::getBaseDir() . '/' . $this->getFeedPath());
		if (!$file->allowedPath($path, Mage::getBaseDir())) {
			Mage::throwException(Mage::helper('datafeed')->__('Please define correct path'));

		} if (!$file->fileExists($path, false)) {
			Mage::throwException(Mage::helper('datafeed')->__('Please create the specified folder "%s" before saving the data feed configuration.', Mage::helper('core')->htmlEscape($this->getFeedPath())));

		} if (!$file->isWriteable($path)) {
			Mage::throwException(Mage::helper('datafeed')->__('Please make sure that "%s" is writable by web-server.', $this->getFeedPath()));

		} if (!preg_match('#^[a-zA-Z0-9_.]+$#', $this->getFeedName())) {
			Mage::throwException(Mage::helper('datafeed')->__('Please use only letters (a-z or A-Z), numbers (0-9) or underscore (_) in the filename. No spaces or other characters are allowed.'));

		} $this->setFeedPath(rtrim(str_replace(str_replace('\\', '/', Mage::getBaseDir()), '', $path), '/') . '/');
		return parent::_beforeSave();
	}

	protected function getPath() {
		if (is_null($this->_filePath)) {
			$this->_filePath = str_replace('//', '/', Mage::getBaseDir() . $this->getFeedPath());
		}

		return $this->_filePath;
	}

	protected function getFilename() {
		$types = array(
				1 => 'xml',
				2 => 'txt',
				3 => 'csv'
		);
		return $this->getFeedName() . "." . $types[$this->getFeedType()];
	}

	public function getPreparedFilename() {
		return $this->getPath() . $this->getFilename();
	}

	public function xf1($price, $currency_code) {
		$currencies = $this->_currencies;
		if (isset($currencies[$currency_code])) {
			return $price * $currencies[$currency_code];
		} else {
			return $price;
		}
	}

	public function calculatePrice($price, $includeTax, $taxClassId, $option = false) {
		$rates = $this->_rates;
		if ($option === false) {
			if (!$includeTax && isset($rates[$taxClassId])) {
				if (count($rates[$taxClassId]) > 1) {
					return $price;
				} else {
					return $price * ($rates[$taxClassId][0]['rate'] / 100 + 1);
				}
			} else {
				return $price;
			}
		} elseif ($option === "0") {
			if ($includeTax && isset($rates[$taxClassId])) {
				if (count($rates[$taxClassId]) > 1) {
					return $price;
				} else {
					return 100 * $price / (100 + ($rates[$taxClassId][0]['rate']));
				}
			} else {
				return $price;
			}
		} else {
			if (is_numeric($option)) {
				if ($taxClassId != 0) {
					return $price * ($option / 100 + 1);
				} elseif ($taxClassId == 0) {
					return $price;
				}
			} else {
				$option = explode('/', $option);
				$x21 = 0;
				$x22 = false;
				if (substr($option[0], 0, 1) == "-") {
					$option[0] = substr($option[0], 1);
					$x22 = true;
				}
				if ($rates[$taxClassId]) {
					foreach ($rates[$taxClassId] as $x23) {
						if ($x23['country'] == $option[0]) {
							if (!isset($option[1]) || $x23['code'] == $option[1]) {
								$x21 = $x23['rate'];
								break;
							}
						}
					}
						
					if (!$x22)
						return $price * ($x21 / 100 + 1);
					else {
						return 100 * $price / (100 + ($x21));
					}
				}
				else {
					return $price;
				}
			}
		}
	}

	public function filterEmpty($str, $asCData = true) {
		$pattern = '/(<[^>^\/]+>)([^<]*)(<\/[^>]+>)/s';
		preg_match_all($pattern, $str, $m);

		foreach ($m[1] as $key => $value) {
			$content = trim($m[2][$key]);
			if (empty($content) && !is_numeric($content)) {
				$str = str_replace($m[0][$key], '', $str);
			}
			else { 
				if ($asCData) {
					$str = str_replace($m[0][$key], ($m[1][$key]) . '<![CDATA[' . $content . ']]>' . ($m[3][$key]), $str);
				}
				else { 
					$str = str_replace($m[0][$key], ($m[1][$key]) . $content . ($m[3][$key]), $str);
				}
			}
		} 
		$arr = preg_split("/\n/s", $str);
		$content = '';
		foreach ($arr as $line) {
			(strlen(trim($line)) > 0) ? $content.=$line . "\n" : false;
		}
		$str = $content;
		return $str;
	}

	public function decode($text) {
		if ($this->_display)
			return ($text);
		else {
			if ($this->_chartset == 'ISO')
				return utf8_decode($text);
			else {
				return ($text);
			}
		}
	}

	public function tablerow($str, $withColor = false) {
		$str = preg_replace('/(\r\n|\n|\r|\r\n)/s', '', $str);
		$style = 'padding:2px;border:1px solid grey;text-align:center;padding:5px;min-width:10px;min-height:10px;';
		$obj = json_decode($str);

		if (isset($obj->header)) $obj = $obj->header;
		else {
			$obj = $obj->product;
		}

		if ($withColor) $result = "<tr style='background-color:#aaaaff;color:white;'>";

		else {
			$result = "<tr>";
		}
		foreach ($obj as $key => $value) {
			$result.="<td style='" . $style . "'>" . ($value) . "</td>";
		}

		$result.="</tr>";

		return $result;
	}

	public function xf6($str, $separator, $protector) {
		$str = preg_replace('/(\r\n|\n|\r|\r\n)/s', '', $str);
		$obj = json_decode($str);

		if (isset($obj->header)) $obj = $obj->header;
		else {
			if (!json_decode($str)) return "";

			$obj = $obj->product;
		}
		$result = '';
		$i = 0;
		foreach ($obj as $key => $value) {
			if ($separator == '	') $separator = "	";
			if ($i > 0) $result.=$separator;
				
			if ($protector != "") {
				$result.=$protector . $this->escape($value, $protector) . $protector;
			}
			else {
				$result.= $this->escape($value, $separator);
			}
			$i++;
		}

		if ($separator == "[|]") $result.="[:]";

		return $result;
	}

	public function escape($str, $ch = '"') {
		$str = str_replace($ch, '\\' . $ch, $str);
		return $str;
	}
	
	public function unescape($str, $ch = '"') {
		$str = str_replace('\\' . $ch, $ch, $str);
		return $str;
	}
	
	public function setCharset($header) {
		if (!stristr($header, 'encoding="utf-8"') === FALSE) $this->_chartset = 'UTF8';
		if (!stristr($header, 'encoding="ISO-8859-1"') === FALSE) $this->_chartset = 'ISO';
	}
	
	public function checkReference($productType, $product)
	{
		if (($productType == "parent" || $productType == "configurable") && isset($this->configurable[$product->getId()])) {
			return $this->configurable[$product->getId()];
		}
		elseif (($productType == "parent" || $productType == "grouped") && isset($this->grouped[$product->getId()])) {
			return $this->grouped[$product->getId()];
		}
		elseif (($productType == "parent" || $productType == "bundle") && isset($this->bundle[$product->getId()])) {
			return $this->bundle[$product->getId()];
		}
		else {
			return $product;
		}
	}

	public function skipOptions($option) {
		$this->option = $this->option + $option;
	}

	public function generateFile() {
		$this->_debug = (isset($_GET['debug'])) ? true : false;
		$this->_type = (isset($_GET['type'])) ? $_GET['type'] : "*";

		if ($this->_debug) {
			echo "----------------------------------------------<br>------------ DEBUG MODE ----------------<br>----------------------------------------------<br><br>";
			print_r($categories);
		}

		$map = array(
				"ac" => "activation_code", 
				"ak" => "activation_key", 
				"bu" => "base_url", 
				"md" => "md5", 
				"th" => "this", 
				"dm" => "_demo", 
				"ext" => "dfm", 
				"ver" => "4.1.0"
			);
		$storeId = $this->getStoreId();
		Mage::app()->setCurrentStore($storeId);
		$skin_url = Mage::getDesign()->getSkinUrl();
		$placeholder = Mage::getStoreConfig("catalog/placeholder/image_placeholder", $storeId);
		$base_currency = Mage::getStoreConfig("currency/options/base", $storeId);
		$manage_stock = Mage::getStoreConfig("cataloginventory/item_options/manage_stock", $storeId);

		$web_url = Mage::app()->getStore($storeId)->getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB, false);
		$store_url = Mage::getModel('core/store')->load($storeId)->getBaseUrl();
		$media_url = Mage::app()->getStore($storeId)->getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA, false);
		$include_tax = Mage::getStoreConfig(Mage_Tax_Model_Config::CONFIG_XML_PATH_PRICE_INCLUDES_TAX, $storeId);
		$root_cat_id = Mage::app()->getStore($storeId)->getRootCategoryId();
		$cfg = array( 
				"activation_key" => Mage::getStoreConfig("datafeed/license/activation_key"), 
				"activation_code" => Mage::getStoreConfig("datafeed/license/activation_code"), 
				"base_url" => Mage::getStoreConfig("web/secure/base_url"), 
			);
		$feedProduct = $this->getFeed_product();
		$header = $this->getFeed_header();
		$footer = $this->getFeed_footer();
		$type = $this->getFeed_type();
		$extraheader = $this->getFeed_extraheader();
		$include_header = $this->getFeed_include_header();
		$separator = $this->getFeed_separator();
		$protector = $this->getFeed_protector();
		$status = $this->getFeed_status();
		$enclose_data = $this->getFeed_enclose_data();
		$feedCategories = json_decode($this->getDatafeedCategories());
		$filter = $this->getDatafeedCategoryFilter();
		$catelines = Array();
		$catmap = Array();
		if ($this->getDatafeedCategories() != '*' && is_array($feedCategories)) {
			foreach ($feedCategories as $cate) {
				if ($cate->checked) $catelines[] = $cate->line;
			} 
			foreach ($feedCategories as $cate) {
				if ($cate->mapping != "") $catmap[$cate->line] = $cate->mapping;
			}
		} 
		if (count($catelines) < 1) {
			$catelines[] = '*';
		} 
		$type_ids = explode(',', $this->getDatafeedTypeIds());
		$visibilities = explode(',', $this->getDatafeedVisibility());
		$attributes = json_decode($this->getDatafeedAttributes());
		/*
		if ($cfg[$map['ac']] != $map["md"]($map["md"]($cfg[$map['ak']]) . $map["md"]($cfg[$map['bu']]) . $map["md"]($map["ext"]) . $map["md"]($map["ver"]))) {
			$$map["ext"] = "valid";
			$$map["th"]->$map["dm"] = true;

		} else { 
			$$map["th"]->$map["dm"] = false;
			$$map["ext"] = "valid";
		} 
		*/
		$this->demo = false;
		$dfm = "valid";
		
		if (!$status && !$this->_display) 
			Mage::throwException(Mage::helper("datafeed")->__("The data feed configuration must be enabled in order to generate a file."));
		
		$file = new Varien_Io_File();
		$file->setAllowCreateFolders(true);
		
		if (!$this->_display) {
			$file->open(array('path' => $this->getPath()));
			if ($file->fileExists($this->getFilename()) && !$file->isWriteable($this->getFilename())) {
				Mage::throwException(Mage::helper('datafeed')->__('File "%s" cannot be saved. Please, make sure the directory "%s" is writeable by web server.', $this->getFilename(), $this->getPath()));

			} 
			$file->streamOpen($this->getFilename());
		} 
/*		
		if (!isset($$map["ext"]) || $$map["th"]->$map["dm"]) {
			$$map["th"]->$map["dm"] = true;
			return $$map["th"];
		}
*/		 
		$xml = '';
		$html = '';
		header("Content-Type: text/html; charset=utf-8");
		$this->setCharset($header);
		$header = $this->xf0($header, null, $type, true);
		
		if ($type == 1 || ($type != 1 && !$this->_display)) { 
			$text = $this->decode($header);
		}
		
		if ($this->_display) {
			if ($type == 1) {
				$html = $this->filterEmpty($header, $enclose_data) . "";
			} 
			else { 
				$html = $extraheader . '<br>';
				$html.= "<table style='border:2px solid grey; font-family:arial; font-size:12px' cellspacing=0 cellpadding=0>";
				if ($include_header) {
					$html.=$this->tablerow($header, true);
				}
			}
		} else { 
			if ($type == 1) {
				$file->streamWrite($this->filterEmpty($header, $enclose_data) . "");
			} else { 
				if ($extraheader != '') $file->streamWrite($extraheader . "\n");
				
				if ($include_header) {
					$file->streamWrite($this->xf6($header, $separator, $protector) . "\n");
				}
			}
		} 
		$x13 = '/{([a-zA-Z_0-9:]+)(\sparent|\sgrouped|\sconfigurable|\sbundle)?([^}]*)}/';
		preg_match_all($x13, $feedProduct, $m);
		$m[0][] = "{categories,[1],[1],[1]}";
		$m[1][] = "categories";
		$m[2][] = "";
		$m[3][] = ",[1],[1],[1]";
		
		$product_vars = array();
		$attr_in_tpl = array();
		foreach ($m[1] as $key => $val) {
			$product_vars[$key]['methodName'] = "get" . str_replace(' ', '', ucwords(trim($val)) . '()');
			$product_vars[$key]['pattern'] = "{" . trim($val) . "}";
			$product_vars[$key]['fullpattern'] = $m[0][$key];
			$product_vars[$key]['name'] = trim($val);
			$product_vars[$key]['reference'] = trim($m[2][$key]);
			
			if (empty($product_vars[$key]['reference'])) $product_vars[$key]['reference'] = 'self';
			
			switch ($product_vars[$key]['name']) {
				case 'url': 
					array_push($attr_in_tpl, 'url_key');
					break;
				case 'uri': 
					array_push($attr_in_tpl, 'url_key');
					break;
				case 'G:IMAGE_LINK': 
					array_push($attr_in_tpl, 'image');
					array_push($attr_in_tpl, 'small_image');
					array_push($attr_in_tpl, 'thumbnail');
					break;
				case 'SC:IMAGES': 
					array_push($attr_in_tpl, 'image');
					array_push($attr_in_tpl, 'small_image');
					array_push($attr_in_tpl, 'thumbnail');
					break;
				case 'SC:DESCRIPTION': 
					array_push($attr_in_tpl, 'description');
					array_push($attr_in_tpl, 'short_description');
					array_push($attr_in_tpl, 'manufacturer');
					array_push($attr_in_tpl, 'name');
					array_push($attr_in_tpl, 'sku');
					break;
				case 'SC:EAN': 
					array_push($attr_in_tpl, 'ean');
					break;
				case 'SC:URL': 
					array_push($attr_in_tpl, 'url_key');
					array_push($attr_in_tpl, 'url');
					break;
				case 'sc:images': 
					array_push($attr_in_tpl, 'image');
					array_push($attr_in_tpl, 'small_image');
					array_push($attr_in_tpl, 'thumbnail');
					break;
				case 'sc:description': 
					array_push($attr_in_tpl, 'description');
					array_push($attr_in_tpl, 'short_description');
					array_push($attr_in_tpl, 'manufacturer');
					array_push($attr_in_tpl, 'name');
					array_push($attr_in_tpl, 'sku');
					break;
				case 'sc:ean': 
					array_push($attr_in_tpl, 'ean');
					break;
				case 'sc:url': 
					array_push($attr_in_tpl, 'url_key');
					array_push($attr_in_tpl, 'url');
					break;
				default : 
					array_push($attr_in_tpl, $product_vars[$key]['name']);
				} 
				$product_vars[$key]["value"] = '$product->get' . $product_vars[$key]['name'] . "()";
				$product_vars[$key]["getText"] = 'getAttributeText(\'' . trim($val) . '\')';
				$x57 = '/\[([^\]]+)\]/';
				preg_match_all($x57, $m[3][$key], $x58);
				$product_vars[$key]["options"] = $x58[1];
		}
		if ($this->_debug) {
			echo "<br><br>------------ ATTRIBUTES REQUIRED ----------------<br>";
			echo "<pre>";
			print_r($product_vars);
			echo "</pre>";
		} 
		$cate_collection = Mage::getModel('catalog/category')->getCollection()
			->setStoreId($storeId)
			->addAttributeToSelect('name')
			->addAttributeToSelect('is_active')
			->addAttributeToSelect('include_in_menu');
		$categories = array();
		foreach ($cate_collection as $cate) {
			$categories[$cate->getId()]['name'] = $cate->getName();
			$categories[$cate->getId()]['path'] = $cate->getPath();
			$categories[$cate->getId()]['level'] = $cate->getLevel();
			if (version_compare(Mage::getVersion(), 1.6, '<')) 
				$categories[$cate->getId()]['include_in_menu'] = true;
			else 
				$categories[$cate->getId()]['include_in_menu'] = $cate->getIncludeInMenu();

		}
		 
		if ($this->_debug) {
			echo "<br><br>------------ CATEGORIES ----------------<br>";
			print_r($categories);

		} 
/*		
		$resource = Mage::getSingleton('core/resource');
		$conn = $resource->getConnection('core_read');
		$x5d = $resource->getTableName('eav_entity_type');
		$select = $conn->select()->from($x5d)->where('entity_type_code=\'catalog_product\'');
		$x2b = $conn->fetchAll($select);
		$x5f = $x2b[0]['entity_type_id'];
*/		
		$product_entity_type_id = 4;
		
		$resource = Mage::getSingleton('core/resource');
		$conn = $resource->getConnection('core_read');
		
		$x60 = $resource->getTableName('directory_currency_rate');
		$select = $conn->select()->from($x60)->where('currency_from=\'' . $base_currency . '\'');
		$x1c = $conn->fetchAll($select);
		$x61 = array();
		foreach ($x1c as $x1b) {
			$x61[$x1b['currency_to']] = $x1b['rate'];

		} 
		$this->_currencies = $x61;
		if ($this->_debug) {
			echo "<br><br>------------ CURRENCIES ----------------<br>";
			print_r($x61);
		} 
		
		//Added for multiwarehouse
		$warehouseTable = $resource->getTableName('warehouse');
		$warehouseStoreTable = $resource->getTableName('warehouse_store');
		$select = "SELECT stock_id FROM {$warehouseTable} AS w INNER JOIN {$warehouseStoreTable} AS s"
			. " ON w.warehouse_id=s.warehouse_id AND s.store_id={$storeId}"
			;
		$rows = $conn->fetchAll($select);
		$stockIds = array();
		foreach($rows as $row) {
			$stockIds[] = $row['stock_id'];
		}
		$stockIdString = empty($stockIds) ? '' : ' AND stock.stock_id IN ('. implode(',', $stockIds) . ')';
		
		$x62 = Mage::getResourceModel('eav/entity_attribute_collection')
			->setEntityTypeFilter($product_entity_type_id)
			->addSetInfo()
			->getData();
		$attr_arr = array();
		$frontend_input = array();
		foreach ($x62 as $key => $val) {
			if (in_array($val['attribute_code'], $attr_in_tpl)) {
				array_push($attr_arr, $val['attribute_code']);
				$frontend_input[$val['attribute_code']] = $val['frontend_input'];
			}
		} 
		if (!in_array('special_price', $attr_arr)) $attr_arr[] = 'special_price';
		if (!in_array('special_from_date', $attr_arr)) $attr_arr[] = 'special_from_date';
		if (!in_array('special_to_date', $attr_arr)) $attr_arr[] = 'special_to_date';
		if (!in_array('price_type', $attr_arr)) $attr_arr[] = 'price_type';
		if (!in_array('price', $attr_arr)) $attr_arr[] = 'price';
		$attr_arr[] = 'tax_class_id';
		foreach ($attributes as $attribute) {
			if (!in_array($attribute->code, $attr_arr) && $attribute->checked) $attr_arr[] = $attribute->code;
		} 
		
		if ($this->_debug) {
			echo "<br><br>------------ ATTRIBUTES ----------------<br>";
			print_r($attr_arr);

		} 
		
		$x66 = $resource->getTableName('eav_attribute_option_value');
		$select = $conn->select();
		$select->from($x66);
		$select->where("store_id=" . $storeId . ' OR store_id=0');
		$select->order(array('option_id', 'store_id'));
		$x67 = $conn->fetchAll($select);
		foreach ($x67 as $x68) {
			$x69[$x68['option_id']][$x68['store_id']] = $x68['value'];
		}
		 
		if ($this->_debug) {
			echo "<br><br>------------ ATTRIBUTES LABEL ----------------<br>";
			print_r($x69);
		} 
		
		$x6a = $resource->getTableName('tax_class');
		$x6b = $resource->getTableName('tax_calculation');
		$x6c = $resource->getTableName('tax_calculation_rate');
		$x6d = $resource->getTableName('directory_country_region');
		$select = $conn->select();
		$select->from($x6a)->order(array('class_id', 'tax_calculation_rate_id'));
		$select->joinleft(array('tc' => $x6b), 'tc.product_tax_class_id = ' . $x6a . '.class_id', 'tc.tax_calculation_rate_id');
		$select->joinleft(array('tcr' => $x6c), 'tcr.tax_calculation_rate_id = tc.tax_calculation_rate_id', array('tcr.rate', 'tax_country_id', 'tax_region_id'));
		$select->joinleft(array('dcr' => $x6d), 'dcr.region_id=tcr.tax_region_id', 'code');
		$x6e = $conn->fetchAll($select);
		$x20 = array();
		$x6f = '';
		foreach ($x6e as $x70) {
			if ($x6f != $x70['class_id']) $x71 = 0;
			else { $x71++;

			} $x6f = $x70['class_id'];
			$x20[$x70['class_id']][$x71]['rate'] = $x70['rate'];
			$x20[$x70['class_id']][$x71]['code'] = $x70['code'];
			$x20[$x70['class_id']][$x71]['country'] = $x70['tax_country_id'];

		} 
		$this->_rates = $x20;
		if ($this->_debug) {
			echo "<br><br>------------ TAX CLASS ----------------<br>";
			print_r($x20);

		} 
		$x72 = $resource->getTableName('review');
		$x73 = $resource->getTableName('review_store');
		$x74 = $resource->getTableName('rating_option_vote');
		$x75 = $conn->select()->distinct('review_id');
		$x75->from(array("r" => $x72), array("COUNT(DISTINCT r.review_id) AS count", 'entity_pk_value'));
		$x75->joinleft(array('rs' => $x73), 'rs.review_id=r.review_id', 'rs.store_id');
		$x75->joinleft(array('rov' => $x74), 'rov.review_id=r.review_id', 'AVG(rov.percent) AS score');
		$x75->where("status_id=1 and entity_id=1");
		$x75->group(array('r.entity_pk_value', 'rs.store_id'));
		
		$x76 = $conn->select();
		$x76->from(array("r" => $x72), array("COUNT(DISTINCT r.review_id) AS count", 'entity_pk_value', "(SELECT 0) AS store_id"));
		$x76->joinleft(array('rs' => $x73), 'rs.review_id=r.review_id', array());
		$x76->joinleft(array('rov' => $x74), 'rov.review_id=r.review_id', 'AVG(rov.percent) AS score');
		$x76->where("status_id=1 and entity_id=1");
		$x76->group(array('r.entity_pk_value'));
		
		$select = $conn->select() ->union(array($x75, $x76));
		$select->order(array('entity_pk_value', 'store_id'));
		$x77 = $conn->fetchAll($select);

		$x78 = array();
		foreach ($x77 as $x79) {
			$x78[$x79['entity_pk_value']][$x79['store_id']]["count"] = $x79["count"];
			$x78[$x79['entity_pk_value']][$x79['store_id']]['score'] = $x79['score'];
		}
		 
		$x7a = $resource->getTableName('catalog_product_entity_media_gallery');
		$x7b = $resource->getTableName('catalog_product_entity_media_gallery_value');
		$select = $conn->select();
		$select->from($x7a);
		$select->joinleft(array('cpemgv' => $x7b), 'cpemgv.value_id = ' . $x7a . '.value_id', array('cpemgv.position', 'cpemgv.disabled', 'cpemgv.store_id'));
		$select->where("value<>TRIM('') AND (store_id=" . $storeId . ' OR store_id=0)');
		$select->order(array('entity_id','store_id', 'position', 'value_id'));
		$result = $conn->fetchAll($select);
		$images = array();
		foreach ($result as $row) {
			$value_id = $row['value_id'];
			$images[$row['entity_id']]['src'][$value_id] = $row['value'];
			$images[$row['entity_id']]['disabled'][$value_id] = $row['disabled'];
		}
		foreach($images as $entity_id => $arr) {
			$images[$entity_id]['src'] = array_values($arr['src']);
			$images[$entity_id]['disabled'] = array_values($arr['disabled']);
		}
		 
		if ($this->_debug) {
			echo "<br><br>------------ IMAGES ----------------<br>";
			print_r($images);
		} 
		$x7f = $resource->getTableName("cataloginventory_stock_item");
		$x80 = $resource->getTableName("core_url_rewrite");
		$x60 = $resource->getTableName('catalog_category_product');
		$x81 = $resource->getTableName('catalog_category_product_index');
		$x82 = $resource->getTableName('catalog_product_index_price');
		$x83 = $resource->getTableName('catalog_product_super_link');
		$x84 = $resource->getTableName('catalog_product_link');
		$x85 = $resource->getTableName('catalog_product_bundle_selection');
		(version_compare(Mage::getVersion(), 1.6, '<')) ? $x58 = "options=''" : $x58 = "ISNULL(options)";
		$collection = Mage::getModel('catalog/product')->getCollection()->addStoreFilter($storeId);
		if (Mage::getStoreConfig("datafeed/system/disabled")) {
			$collection->addFieldToFilter("status", array('gteq' => 1));
		}
		else {
			$collection->addFieldToFilter("status", 1);
		}
		$collection->addAttributeToFilter('type_id', array("in" => "configurable"));
		$collection->addAttributeToFilter('visibility', array("nin" => 1));
		$collection->addAttributeToSelect($attr_arr);
		$collection->getSelect()->joinLeft($x83 . ' AS cpsl', 'cpsl.parent_id=e.entity_id ', array('child_ids' => 'GROUP_CONCAT( DISTINCT cpsl.product_id)'));
		$collection->getSelect()->joinLeft($x7f . ' AS stock', 'stock.product_id=e.entity_id'. $stockIdString, array('qty' => 'qty', 'is_in_stock' => 'is_in_stock', 'manage_stock' => 'manage_stock', 'use_config_manage_stock' => 'use_config_manage_stock'));
		$collection->getSelect()->joinLeft($x80 . ' AS url', 'url.product_id=e.entity_id AND url.category_id IS NULL AND is_system=1 AND ' . $x58 . ' AND url.store_id=' . $storeId, array('request_path' => 'request_path'));
		$collection->getSelect()->joinLeft($x60 . ' AS categories', 'categories.product_id=e.entity_id');
		$collection->getSelect()->joinLeft($x81 . ' AS categories_index', 'categories_index.category_id=categories.category_id AND categories_index.product_id=categories.product_id AND categories_index.store_id=' . $storeId, array('categories_ids' => 'GROUP_CONCAT( DISTINCT categories_index.category_id)'));
		$collection->getSelect()->group(array('cpsl.parent_id'));
		$x87 = array();
		foreach ($collection as $x88) {
			foreach (explode(",", $x88->getChildIds()) as $x89) {
				$x87[$x89] = $x88;
				$x8a[$x89]['categories_ids'] = $x88->getCategories_ids();
				$x8a[$x89]['parent_id'] = $x88->getId();
				$x8a[$x89]['parent_sku'] = $x88->getSku();
				$x8a[$x89]['parent_request_path'] = $x88->getRequestPath();

			}
		} 
		$this->configurable = $x87;
		if ($this->_debug) {
			echo "<br><br>------------ CONFIGURABLES ----------------<br>";
			echo $collection->getSelect() . '<br><br>';
			print_r($x8a);

		} 
		$collection = Mage::getModel('catalog/product')->getCollection()->addStoreFilter($storeId);
		if (Mage::getStoreConfig("datafeed/system/disabled")) $collection->addFieldToFilter("status", array('gteq' => 1));
		else $collection->addFieldToFilter("status", 1);
		$collection->addAttributeToFilter('type_id', array("in" => "configurable"));
		$collection->addAttributeToFilter('visibility', array("nin" => 1));
		$collection->getSelect()->joinLeft($x83 . ' AS cpsl', 'cpsl.parent_id=e.entity_id ');
		$collection->getSelect()->joinLeft($x7f . ' AS stock', 'stock.product_id=cpsl.product_id' . $stockIdString, array('qty' => 'SUM(qty)'));
		$collection->getSelect()->group(array('cpsl.parent_id'));
		$x8b = array();
		foreach ($collection as $x8c) {
			$x8b[$x8c->getId()] = $x8c->getQty();

		} 
		
		$this->configurableQty = $x8b;
		if ($this->_debug) {
			echo "<br><br>------------ CONFIGURABLES QTY ----------------<br>";
			echo $collection->getSelect() . '<br><br>';
			print_r($x8b);

		} 
		
		$collection = Mage::getModel('catalog/product')->getCollection()->addStoreFilter($storeId);
		if (Mage::getStoreConfig("datafeed/system/disabled")) $collection->addFieldToFilter("status", array('gteq' => 1));
		else $collection->addFieldToFilter("status", 1);
		$collection->addAttributeToFilter('type_id', array("in" => "grouped"));
		$collection->addAttributeToFilter('visibility', array("nin" => 1));
		$collection->addAttributeToSelect($attr_arr);
		$collection->getSelect()->joinLeft($x84 . ' AS cpl', 'cpl.product_id=e.entity_id AND cpl.link_type_id=3', array('child_ids' => 'GROUP_CONCAT( DISTINCT cpl.linked_product_id)'));
		$collection->getSelect()->joinLeft($x7f . ' AS stock', 'stock.product_id=e.entity_id' . $stockIdString, array('qty' => 'qty', 'is_in_stock' => 'is_in_stock', 'manage_stock' => 'manage_stock', 'use_config_manage_stock' => 'use_config_manage_stock'));
		$collection->getSelect()->joinLeft($x80 . ' AS url', 'url.product_id=e.entity_id AND url.category_id IS NULL AND is_system=1 AND ' . $x58 . ' AND url.store_id=' . $storeId, array('request_path' => 'request_path'));
		$collection->getSelect()->joinLeft($x60 . ' AS categories', 'categories.product_id=e.entity_id');
		$collection->getSelect()->joinLeft($x81 . ' AS categories_index', 'categories_index.category_id=categories.category_id AND categories_index.product_id=categories.product_id AND categories_index.store_id=' . $storeId, array('categories_ids' => 'GROUP_CONCAT( DISTINCT categories_index.category_id)'));
		$collection->getSelect()->group(array('cpl.product_id'));
		$x8d = array();
		foreach ($collection as $x88) {
			foreach (explode(",", $x88->getChildIds()) as $x89) {
				$x8d[$x89] = $x88;
				$x8e[$x89]['categories_ids'] = $x88->getCategories_ids();
				$x8e[$x89]['parent_id'] = $x88->getId();
				$x8e[$x89]['parent_sku'] = $x88->getSku();
				$x8e[$x89]['parent_request_path'] = $x88->getRequestPath();

			}
		} 
		$this->grouped = $x8d;
		if ($this->_debug) {
			echo "<br><br>------------ GROUPED ----------------<br>";
			echo $collection->getSelect() . '<br><br>';
			print_r($x8e);

		} 
		
		$collection = Mage::getModel('catalog/product')->getCollection()->addStoreFilter($storeId);
		if (Mage::getStoreConfig("datafeed/system/disabled")) $collection->addFieldToFilter("status", array('gteq' => 1));
		else $collection->addFieldToFilter("status", 1);
		$collection->addAttributeToFilter('type_id', array("in" => "bundle"));
		$collection->addAttributeToFilter('visibility', array("nin" => 1));
		$collection->addAttributeToSelect($attr_arr);
		$collection->getSelect()->joinLeft($x85 . ' AS cpbs', 'cpbs.parent_product_id=e.entity_id', array('child_ids' => 'GROUP_CONCAT( DISTINCT cpbs.product_id)'));
		$collection->getSelect()->joinLeft($x7f . ' AS stock', 'stock.product_id=e.entity_id' . $stockIdString, array('qty' => 'qty', 'is_in_stock' => 'is_in_stock', 'manage_stock' => 'manage_stock', 'use_config_manage_stock' => 'use_config_manage_stock'));
		$collection->getSelect()->joinLeft($x80 . ' AS url', 'url.product_id=e.entity_id AND url.category_id IS NULL AND is_system=1 AND ' . $x58 . ' AND url.store_id=' . $storeId, array('request_path' => 'request_path'));
		$collection->getSelect()->joinLeft($x60 . ' AS categories', 'categories.product_id=e.entity_id');
		$collection->getSelect()->joinLeft($x81 . ' AS categories_index', 'categories_index.category_id=categories.category_id AND categories_index.product_id=categories.product_id AND categories_index.store_id=' . $storeId, array('categories_ids' => 'GROUP_CONCAT( DISTINCT categories_index.category_id)'));
		$collection->getSelect()->group(array('e.entity_id'));
		$x8f = array();
		foreach ($collection as $x88) {
			foreach (explode(",", $x88->getChildIds()) as $x89) {
				$x8f[$x89] = $x88;
				
				$x90[$x89]['parent_id'] = $x88->getId();
				$x90[$x89]['parent_sku'] = $x88->getSku();

				$x90[$x89]['parent_request_path'] = $x88->getRequestPath();
				$x90[$x89]['categories_ids'] = $x88->getCategories_ids();
			}
		} 
		
		$this->bundle = $x8f;
		if ($this->_debug) {
			echo "<br><br>------------ BUNDLE ----------------<br>";
			echo $collection->getSelect() . '<br><br>';
			print_r($x90);

		} 
		$x91 = 0;
		$collection = Mage::getModel('catalog/product')->getCollection()->addStoreFilter($storeId);
        $collection->setStoreId($storeId);
		if (Mage::getStoreConfig("datafeed/system/disabled")) {
			$collection->addFieldToFilter("status", array('gteq' => 1));
		}
		else {
			$collection->addFieldToFilter("status", 1);
		}
		$collection->addAttributeToFilter('type_id', array("in" => $type_ids));
		$collection->addAttributeToFilter('visibility', array("in" => $visibilities));
		$collection->getSelect()->columns("COUNT(DISTINCT e.entity_id) As total")->group(array('status'));
		$product_total_count = $collection->getFirstItem()->getTotal();
		$pages = round($product_total_count / $this->_sqlSize) + 1;
		$xml = '';
		while ($x91 < $pages) {
			$collection = Mage::getModel('catalog/product')->getCollection()->addStoreFilter($storeId);
            $collection->setStoreId($storeId);
			if (Mage::getStoreConfig("datafeed/system/disabled")) $collection->addFieldToFilter("status", array('gteq' => 1));
			else $collection->addFieldToFilter("status", 1);
			$collection->addAttributeToFilter("type_id", array("in" => $type_ids));
			$collection->addAttributeToFilter("visibility", array("in" => $visibilities));
			$collection->addAttributeToSelect($attr_arr);
			$x94 = array("eq" => "= '%s'", "neq" => "!= '%s'", "gteq" => ">= '%s'", "lteq" => "<= '%s'", "gt" => "> '%s'", "lt" => "< '%s'", "like" => "like '%s'", "nlike" => "not like '%s'", "null" => "is null", "notnull" => "is not null", "in" => "in (%s)", "nin" => "not in(%s)", );
			$where = '';
			$x27 = 0;
			foreach ($attributes as $attribute) {
				if ($attribute->checked) {
					if ($attribute->condition == 'in' || $attribute->condition == 'nin') {
						if ($attribute->code == 'qty' || $attribute->code == 'is_in_stock') {
							$x96 = explode(',', $attribute->value);
							$attribute->value = "'" . implode($x96, "','") . "'";
						} else { 
							$attribute->value = explode(',', $attribute->value);
						}
					} 
					switch ($attribute->code) {
						case 'qty' : 
							if ($x27 > 0) $where.=' AND ';
							$where.=" qty " . sprintf($x94[$attribute->condition], $attribute->value);
							$x27++;
							break;
						case 'is_in_stock' : 
							if ($x27 > 0) $where.=' AND ';
							$where.=" (is_in_stock " . sprintf($x94[$attribute->condition], $attribute->value);
							$where.=" OR ( manage_stock " . sprintf($x94[$attribute->condition], (int) !$attribute->value);
							$where.=" AND use_config_manage_stock " . sprintf($x94[$attribute->condition], (int) !$attribute->value) . ')';
							$where.=" OR (use_config_manage_stock " . sprintf($x94[$attribute->condition], $attribute->value) . ' AND ' . $manage_stock . '=' . (int) $attribute->value . ' AND is_in_stock = ' . $attribute->value . ' )';
							$where.=")";
							$x27++;
							break;
						default : 
							$collection->addFieldToFilter($attribute->code, array($attribute->condition => $attribute->value));
							break;
					}
				}
			};
			$collection->getSelect()->joinLeft($x7f . ' AS stock', 'stock.product_id=e.entity_id' . $stockIdString, array('qty' => 'qty', 'is_in_stock' => 'is_in_stock', 'manage_stock' => 'manage_stock', 'use_config_manage_stock' => 'use_config_manage_stock'));
			$collection->getSelect()->joinLeft($x80 . ' AS url', 'url.product_id=e.entity_id AND url.category_id IS NULL AND is_system=1 AND ' . $x58 . ' AND url.store_id=' . $storeId, array('request_path' => 'request_path'));
			$collection->getSelect()->joinLeft($x60 . ' AS categories', 'categories.product_id=e.entity_id');
			if ($catelines[0] != '*') {
				$x97 = 0;
				$x98 = null;
				foreach ($catelines as $cate) {
					if ($x97 > 0) $x98.=',';
					$_tmp_arr = explode('/', $cate);
					$x98.=array_pop($_tmp_arr);
					$x97++;

				} 
				($filter) ? $x99 = "IN" : $x99 = "NOT IN";
				$x98 = "AND categories_index.category_id " . $x99 . " (" . $x98 . ")";
				$collection->getSelect()->joinInner($x81 . ' AS categories_index', 'categories_index.category_id=categories.category_id AND categories_index.product_id=categories.product_id AND categories_index.store_id=' . $storeId . ' ' . $x98, array('categories_ids' => 'GROUP_CONCAT(categories_index.category_id)'));

			} else {
				$collection->getSelect()->joinLeft($x81 . ' AS categories_index', 'categories_index.category_id=categories.category_id AND categories_index.product_id=categories.product_id AND categories_index.store_id=' . $storeId, array('categories_ids' => 'GROUP_CONCAT(categories_index.category_id)'));
			}
			if (version_compare(Mage::getVersion(), 1.4, '>=')) {
				$collection->getSelect()->joinLeft($x82 . ' AS price_index', 'price_index.entity_id=e.entity_id AND customer_group_id=0 AND price_index.website_id=' . Mage::getModel('core/store')->load($storeId)->getWebsiteId(), array('min_price' => 'min_price', 'max_price' => 'max_price', 'tier_price' => 'tier_price', 'final_price' => 'final_price'));
			}
			if (!empty($where)) {
				$collection->getSelect()->where($where);
			}
			$collection->getSelect()->group(array('e.entity_id'));
			//$collection->getSelect()->group(array('e.entity_id'));
			
			if ($this->_debug && ($this->_type == '*' || $this->_type == "sql")) {
				echo "<br><br>------------ SQL ----------------<br>";
				print($collection->getSelect());

			} 
			$collection->getSelect()->limit($this->_sqlSize, ($this->_sqlSize * $x91));
			$x91++;
			$x9a = 1;
			//$x9b = new MyCustomOptions;
			//$x9c = new MyCustomAttributes;
			
			foreach ($collection as $product) {
				if ($this->_debug) {
					echo "<br><br>------------ PRODUCT [ SKU -> " . $product->getSku() . " | ID -> " . $product->getId() . "]---------------<br>";
					echo "categories : " . $product->getCategoriesIds() . ", Root id: " . $root_cat_id . "<br>";
					foreach (explode(',', $product->getCategoriesIds()) as $key => $cateId) {
						echo $cateId . "=>" . $categories[$cateId]["path"] . "<br>";

					}
				} 
				if (!ini_get('safe_mode')) {
					set_time_limit(60);
				} 
				$text = $feedProduct;
				foreach ($product_vars as $key => $exp) {
					$value = "";
					$this->option = 0;
					$prod = $this->checkReference($exp['reference'], $product);
					switch ($exp['pattern']) {
						case '{id}' : 
							$value = $prod->getId();
							break;
						case '{inc}' : 
							$value = $x9a;
							break;
						case '{final_price}' : 
							$price = $prod->getFinalePrice();
							(!isset($exp['options'][1])) ? $x1f = false : $x1f = $exp['options'][1];
							$value = $this->calculatePrice($price, $include_tax, $prod->getTaxClassId(), $x1f);
							(!isset($exp['options'][0])) ? $x1b = $base_currency : $x1b = $exp['options'][0];
							$value = $this->xf1($value, $x1b);
							$value = number_format($value, 2, '.', '');
							break;
						case '{tier_price}' : 
							$price = $prod->getTierPrice();
							(!isset($exp['options'][1])) ? $x1f = false : $x1f = $exp['options'][1];
							$value = $this->calculatePrice($price, $include_tax, $prod->getTaxClassId(), $x1f);
							(!isset($exp['options'][0])) ? $x1b = $base_currency : $x1b = $exp['options'][0];
							$value = $this->xf1($value, $x1b);
							$value = number_format($value, 2, '.', '');
							break;
						case '{min_price}' : 
							$price = $prod->getMinPrice();
							(!isset($exp['options'][1])) ? $x1f = false : $x1f = $exp['options'][1];
							$value = $this->calculatePrice($price, $include_tax, $prod->getTaxClassId(), $x1f);
							(!isset($exp['options'][0])) ? $x1b = $base_currency : $x1b = $exp['options'][0];
							$value = $this->xf1($value, $x1b);
							$value = number_format($value, 2, '.', '');
							break;
						case '{max_price}' : 
							$price = $prod->getMaxPrice();
							(!isset($exp['options'][1])) ? $x1f = false : $x1f = $exp['options'][1];
							$value = $this->calculatePrice($price, $include_tax, $prod->getTaxClassId(), $x1f);
							(!isset($exp['options'][0])) ? $x1b = $base_currency : $x1b = $exp['options'][0];
							$value = $this->xf1($value, $x1b);
							$value = number_format($value, 2, '.', '');
							break;
						case '{normal_price}' : 
							if ($prod->type_id == 'bundle') $price = $prod->price;
							else { 
								$price = $prod->getPrice();
							}
							(!isset($exp['options'][1])) ? $x1f = false : $x1f = $exp['options'][1];
							$value = $this->calculatePrice($price, $include_tax, $prod->getTaxClassId(), $x1f);
							(!isset($exp['options'][0])) ? $x1b = $base_currency : $x1b = $exp['options'][0];
							$value = $this->xf1($value, $x1b);
							$value = number_format($value, 2, '.', '');
							$this->skipOptions(2);
							break;
						case '{price}' : 
							$value = $this->_getPrice($prod, $exp, $include_tax, $base_currency);
							$this->skipOptions(2);
							break;
						case "{is_special_price}" : 
							(!isset($exp["options"][0])) ? $x9e = 1 : $x9e = $exp["options"][0];
							(!isset($exp["options"][1])) ? $x9f = 0 : $x9f = $exp["options"][1];
							
							if ($this->_isSpecialDate($prod)) {
								if ($prod->type_id == 'bundle') {
									$value = (($prod->price_type || (!$prod->price_type && $prod->special_price < $prod->price)) && $prod->special_price > 0 ) ? $x9e : $x9f;
								}
								else{
									$value = ($prod->getSpecialPrice() && $prod->getSpecialPrice() < $prod->getPrice()) ? $x9e : $x9f;
								}
							}
							else{
								$value = $x9f;
							}
							$this->skipOptions(2);
							break;
						case "{special_price}" : 
							$price = null;
							if ($prod->type_id == 'bundle') {
								if ($prod->price_type) {
									$price = number_format($prod->price * $prod->special_price / 100, 2, ".", "");
								}
								else {
									$price = $prod->special_price;
								}
							}
							else{
								$price = $prod->getSpecialPrice();
							}
							
							(!isset($exp['options'][1])) ? $x1f = false : $x1f = $exp['options'][1];
							if ($price > 0) {
								$value = $this->calculatePrice($price, $include_tax, $prod->getTaxClassId(), $x1f);
								(!isset($exp['options'][0])) ? $x1b = $base_currency : $x1b = $exp['options'][0];
								$value = $this->xf1($value, $x1b);
								$value = number_format($value, 2, '.', '');
							} else { 
								$value = "";
							} 
							$this->skipOptions(2);
							break;
						case '{price_rules}' : 
							$value = $this->_getPriceRules($prod, $exp, $include_tax, $base_currency);
							$this->skipOptions(2);
							break;
						case "{G:SALE_PRICE}" : 
							$xa7 = str_replace(' ', 'T', $prod->getSpecialFromDate());
							$xa8 = str_replace(' ', 'T', $prod->getSpecialToDate());
							if ($prod->type_id == 'bundle' && $prod->special_price) {
								if ($prod->price_type) $price = number_format($prod->price * $prod->special_price / 100, 2, ".", "");
								else { 
									$price = $prod->special_price;
								}
							} else { 
								$price = $prod->getSpecialPrice();
							} 
							(!isset($exp['options'][1])) ? $x1f = false : $x1f = $exp['options'][1];
							if ($price > 0) {
								$price = $this->calculatePrice($price, $include_tax, $prod->getTaxClassId(), $x1f);
								(!isset($exp['options'][0])) ? $x1b = $base_currency : $x1b = $exp['options'][0];
								$price = $this->xf1($price, $x1b);
								$price = number_format($price, 2, '.', '');
							} 
							if ($price > 0) $value = "<g:sale_price><![CDATA[" . $price . "]]></g:sale_price>\n";
							if ($price > 0 && $xa8) $value.="<g:sale_price_effective_date><![CDATA[" . $xa7 . "/" . $xa8 . "]]></g:sale_price_effective_date>";
							$this->skipOptions(2);
							break;
						case "{image}" : 
							$xa9 = $prod->getImage();
							if (!isset($exp['options'][0]) || $exp['options'][0] == 0) {
								if ($prod->getImage() && $prod->getImage() != 'no_selection') {
									$xaa = 'catalog/product/' . $prod->getImage();
									$value = $media_url . str_replace('//', '/', $xaa);
								} else { 
									$value = $media_url . '/catalog/product/placeholder/' . $placeholder;
								}
							} 
							elseif (isset($images[$prod->getId()]['src'][$exp['options'][0] - 1]) && $exp['options'][0] > 0) {
								if ($images[$prod->getId()]['src'][$exp['options'][0] - 1] != $xa9) {
									$xaa = 'catalog/product/' . $images[$prod->getId()]['src'][$exp['options'][0] - 1];
									$value = $media_url . str_replace('//', '/', $xaa);
								}
							} 
							$this->skipOptions(1);
							break;
						case "{G:IMAGE_LINK}" : 
							$xa9 = $prod->getImage();
							$xab = array($prod->getSmall_image(), $prod->getThumbnail());
							$xac = '';
							$xad = 0;
							if ($prod->getImage() && $prod->getImage() != 'no_selection') {
								$xaa = 'catalog/product/' . $prod->getImage();
								$value = $media_url . str_replace('//', '/', $xaa);
								$xac.="<g:image_link><![CDATA[" . $value . "]]></g:image_link>\n";
								$xad++;
							} 
							$xae = 0;
							while (isset($images[$prod->getId()]['src'][$xae]) && $xad < 10) {
								if ($images[$prod->getId()]['src'][$xae] != $xa9) {
									if (in_array($images[$prod->getId()]['src'][$xae], $xab) || $images[$prod->getId()]['disabled'][$xae] != 1) {
										$xaa = 'catalog/product/' . $images[$prod->getId()]['src'][$xae];
										$value = $media_url . str_replace('//', '/', $xaa);
										$xac.="<g:additional_image_link><![CDATA[" . $value . "]]></g:additional_image_link>\n";
										$xad++;
									}
								} 
								$xae++;
							} 
							$value = $xac;
							break;
						case "{url}" : 
							if ($prod->getRequest_path()) {
								$value = $store_url . $prod->getRequest_path();
							} else { 
								$value = $prod->getProductUrl();
							} break;
						case "{host}" : 
							$value = $store_url;
							break;
						case "{uri}" : 
							(isset($exp['options'][0])) ? $xaf = $exp['options'][0] : $xaf = "";
							(isset($exp['options'][1])) ? $xb0 = $exp['options'][1] : $xb0 = "";
							if ($prod->getRequest_path()) {
								$value = $xb0 . '' . $prod->getRequest_path() . $xaf;
							} else { 
								$value = str_replace($store_url, '', $prod->getProductUrl());
							} 
							break;
						case '{is_in_stock}' : 
							(!isset($exp['options'][0])) ? $x9e = 1 : $x9e = $exp['options'][0];
							(!isset($exp['options'][1])) ? $x9f = 0 : $x9f = $exp['options'][1];
							if ($prod->getManageStock() || ($prod->getUseConfigManageStock() && $manage_stock )) {
								($prod->getIs_in_stock() > 0) ? $value = $x9e : $value = $x9f;
							} else { 
								$value = $x9e;
							} 
							$this->skipOptions(2);
							break;
						case '{stock_status}' : 
							($prod->getIs_in_stock() > 0) ? $value = Mage::helper('datafeed')->__('in stock') : $value = Mage::helper('datafeed')->__('out of stock');
							break;
						case '{qty}' : 
							(!isset($exp['options'][0])) ? $xb1 = 0 : $xb1 = $exp['options'][0];
							if ($product->type_id == "configurable") {
								$value = $x8b[$product->getId()];
								$value = number_format($value, $xb1, '.', '');
							} else if ($exp['reference'] == "configurable") {
								$value = number_format($x8b[$prod->getId()], $xb1, '.', '');
							} else { 
								$value = number_format($prod->getQty(), $xb1, '.', '');
							} 
							$this->skipOptions(1);
							break;
						case "{categories}" : 
							(!isset($exp['options'][0]) || !$exp['options'][0] || $exp['options'][0] == 'INF') ? $xb2 = INF : $xb2 = $exp['options'][0];
							(!isset($exp['options'][1])) ? $xb3 = 1 : $xb3 = $exp['options'][1];
							(!isset($exp['options'][2]) || !$exp['options'][2] || $exp['options'][2] == 'INF') ? $xb4 = INF : $xb4 = $exp['options'][2];
							$xb5 = 0;
							$value = '';
							$xb6 = '';
							foreach (explode(',', $prod->getCategoriesIds()) as $key => $cateId) {
								($filter)?$xb7=in_array($categories[$cateId]["path"], $catelines):$xb7=!in_array($categories[$cateId]["path"], $catelines);
								if (isset($categories[$cateId]) && $xb5 < $xb2 && ($xb7 || $catelines[0] == "*")) {
									$xb8 = 0;
									$xb9 = explode('/', $categories[$cateId]["path"]);
									if (in_array($root_cat_id, $xb9)) {
										$xba = "";
										if ($xb5 > 0) $xb6 = ",";
										foreach ($xb9 as $xbb) {
											if (isset($categories[$xbb])) {
												if ($categories[$xbb]['level'] > $xb3 && $xb8 < $xb4) {
													if ($xb8 > 0) $xba.=' > ';
													$xba.=($categories[$xbb]['name']);
													$xb8++;
												}
											}
										} 
										$xbc = "";

										if (!empty($xba)) {
											$value.=$xb6 . $xba . $xbc;
											$xb5++;
										}
									}
								}
							}
							$this->skipOptions(3);
							break;
						case "{G:PRODUCT_TYPE}" : 
							(!isset($exp['options'][0]) || !$exp['options'][0] || $exp['options'][0] == 'INF') ? $xb2 = INF : $xb2 = $exp['options'][0];
							(!isset($exp['options'][1])) ? $xb3 = 1 : $xb3 = $exp['options'][1];
							(!isset($exp['options'][2]) || !$exp['options'][2] || $exp['options'][2] == 'INF') ? $xb4 = INF : $xb4 = $exp['options'][2];
							$xb5 = 0;
							$value = '';
							foreach (explode(',', $prod->getCategoriesIds()) as $key => $cateId) {
								($filter)?$xb7=in_array($categories[$cateId]["path"], $catelines):$xb7=!in_array($categories[$cateId]["path"], $catelines);
								if (@$categories[$cateId]["include_in_menu"] && isset($categories[$cateId]) && $xb5 < $xb2 && ($xb7 || $catelines[0] == "*")) {
									$xb8 = 0;
									$xb9 = explode('/', $categories[$cateId]["path"]);
									if (in_array($root_cat_id, $xb9)) {
										$xba = '';
										$xb6 = '<g:product_type><![CDATA[';
										foreach ($xb9 as $xbb) {
											if (isset($categories[$xbb])) {
												if ($categories[$xbb]['level'] > $xb3 && $xb8 < $xb4) {
													if ($xb8 > 0) $xba.=' > ';
													$xba.=($categories[$xbb]['name']);
													$xb8++;
												}
											}
										} 
										$xbc = "]]></g:product_type>\n";
										if (!empty($xba)) {
											$value.=$xb6 . $xba . $xbc;
											$xb5++;
										}
									}
								}
							};
							$this->skipOptions(3);
							break;
						case "{G:GOOGLE_PRODUCT_CATEGORY}" : 
							(isset($exp["options"][0])) ? $xbd = $exp["options"][0] : $xbd = 0;
							$value = "";
							$i = 0;
							foreach (explode(',', $prod->getCategoriesIds()) as $key => $cateId) {
								if (isset($categories[$cateId]["path"]) && isset($catmap[$categories[$cateId]["path"]])) {
									if ($i == $xbd) {
										$value.="<g:google_product_category><![CDATA[" . $catmap[$categories[$cateId]["path"]] . "]]></g:google_product_category>\n";
										break;
									} 
									$i++;
								}
							} 
							$this->skipOptions(1);
							break;
						case "{category_mapping}" : 
							(isset($exp["options"][0])) ? $xbd = $exp["options"][0] : $xbd = 0;
							$value = "";
							$i = 0;
							foreach (explode(',', $prod->getCategoriesIds()) as $key => $cateId) {
								if (isset($catmap[$categories[$cateId]["path"]])) {
									if ($i == $xbd) {
										$value.=$catmap[$categories[$cateId]["path"]];
										break;
									} 
									$i++;
								}
							} 
							$this->skipOptions(1);
							break;
						case "{review_count}": 
							$value = "";
							(isset($exp["options"][0]) && $exp["options"][0] == "*" ) ? $xbf = 0 : $xbf = $storeId;
							if (isset($x78[$prod->getId()][$xbf]["count"])) {
								$xc0 = $x78[$prod->getId()][$xbf]["count"];
								if (isset($xc0)) $value.=$xc0;
							} 
							$this->skipOptions(1);
							break;
						case "{review_average}": 
							$value = "";
							(isset($exp["options"][0]) && $exp["options"][0] == "*" ) ? $xbf = 0 : $xbf = $storeId;
							(!isset($exp["options"][1]) || !$exp["options"][1]) ? $xc1 = 5 : $xc1 = $exp["options"][1];
							if (isset($x78[$prod->getId()][$xbf]["score"])) {
								$xc2 = number_format($x78[$prod->getId()][$xbf]["score"] * $xc1 / 100, 2, ".", "");
								if (isset($xc2)) $value.=$xc2;
							} 
							$this->skipOptions(2);
							break;
						case "{G:PRODUCT_REVIEW}" : 
							(isset($exp["options"][0]) && $exp["options"][0] == "*" ) ? $xbf = 0 : $xbf = $storeId;
							(!isset($exp["options"][1]) || !$exp["options"][1]) ? $xc1 = 5 : $xc1 = $exp["options"][1];
							$value = "";
							if (isset($x78[$prod->getId()][$xbf]["count"])) {
								$xc0 = $x78[$prod->getId()][$xbf]["count"];
								$xc2 = number_format($x78[$prod->getId()][$xbf]["score"] * $xc1 / 100, 2, ".", "");
							} 
							if (isset($xc2) && $xc2 > 0) {
								$value.="<g:product_review_average><![CDATA[" . $xc2 . "]]></g:product_review_average>\n";
							} 
							if (isset($xc0) && $xc0 > 0) {
								$value.="<g:product_review_count><![CDATA[" . $xc0 . "]]></g:product_review_count>\n";
							} 
							unset($xc2);
							unset($xc0);
							break;
						case "{G:ITEM_GROUP_ID}" : 
							if (isset($this->configurable[$product->getId()])) {
								$prod = $this->checkReference('configurable', $product);
								$value = "<g:item_group_id><![CDATA[" . $prod->getSku() . "]]></g:item_group_id>";
							} 
							break;
						case "{SC:EAN}" : 
							(is_numeric($exp['options'][0]) && $exp['options'][0] > 0) ? $xc3 = $exp['options'][0] : $xc3 = 0;
							$prod = $this->checkReference($exp['reference'], $product);
							$value = explode(',', $prod->getEan());
							$value = "<g:ean><![CDATA[" . $value[$xc3] . "]]></g:ean>";
							break;
						case "{sc:ean}" : 
							(is_numeric($exp['options'][0]) && $exp['options'][0] > 0) ? $xc3 = $exp['options'][0] : $xc3 = 0;
							$prod = $this->checkReference($exp['reference'], $product);
							$value = explode(',', $prod->getEan());
							$value = $value[$xc3];
							break;
						case "{SC:IMAGES}" : 
							$prod = $this->checkReference($exp['reference'], $product);
							$xa9 = $prod->getSmall_image();
							$xab = array($prod->getImage(), $prod->getThumbnail());
							$xac = '';
							$xad = 0;
							if ($prod->getSmall_image() && $prod->getSmall_image() != 'no_selection') {
								$xaa = $prod->getSmall_image();
								$value = $xaa;
								$xac.="<g:image_link><![CDATA[" . $value . "]]></g:image_link>\n";
								$xad++;
							} 
							$xae = 0;
							while (isset($images[$prod->getId()]['src'][$xae]) && $xad < 10) {
								if ($images[$prod->getId()]['src'][$xae] != $xa9) {
									if (in_array($images[$prod->getId()]['src'][$xae], $xab) || $images[$prod->getId()]['disabled'][$xae] != 1) {
										$xaa = $images[$prod->getId()]['src'][$xae];
										$value = $xaa;
										$xac.="<g:additional_image_link><![CDATA[" . $value . "]]></g:additional_image_link>\n";
										$xad++;
									}
								} 
								$xae++;
							} 
							$value = $xac;
							break;
						case "{sc:images}" : 
							$prod = $this->checkReference($exp['reference'], $product);
							$xa9 = $prod->getSmall_image();
							if (!isset($exp['options'][0]) || $exp['options'][0] == 0) {
								if ($prod->getSmall_image() && $prod->getSmall_image() != 'no_selection') {
									$xaa = $prod->getSmall_image();
									$value = $xaa;
								} 
								else { 
									$value = $media_url . '/catalog/product/placeholder/' . $placeholder;
								}
							} 
							elseif (isset($images[$prod->getId()]['src'][$exp['options'][0] - 1]) && $exp['options'][0] > 0) {
								if ($images[$prod->getId()]['src'][$exp['options'][0] - 1] != $xa9) {
									$xaa = 'catalog/product/' . $images[$prod->getId()]['src'][$exp['options'][0] - 1];
									$value = $media_url . str_replace('//', '/', $xaa);
								}
							} 
							$this->skipOptions(1);
							break;
						case "{SC:DESCRIPTION}" : 
							$prod = $this->checkReference($exp['reference'], $product);
							$value = $this->_getDescription($prod, true);
							$exp['options'] = array();
							break;
						case "{sc:description}" : 
							$prod = $this->checkReference($exp['reference'], $product);
							$value = $this->_getDescription($prod);
							$exp['options'] = array();
							break;
						case "{SC:URL}" : 
							$prod = $this->checkReference($exp['reference'], $product);
							if ($prod->getRequest_path()) {
								$value = "<link><![CDATA[" . $store_url . $prod->getRequest_path() . "]]></link>";
							} 
							else { 
								$value = "<link><![CDATA[" . $prod->getProductUrl() . "]]></link>";
							} 
							break;
						case "{sc:url}" : 
							(isset($exp['options'][0])) ? $xaf = $exp['options'][0] : $xaf = "";
							(isset($exp['options'][1])) ? $xb0 = $exp['options'][1] : $xb0 = "";
							$prod = $this->checkReference($exp['reference'], $product);
							if ($prod->getUrlKey()) {
								$value = $store_url . $xb0 . $prod->getRequest_path() . $xaf;
							} 
							else { 
								$value = $prod->getProductUrl();
							} 
							break;
						case "{SC:CONDITION}" : 
							$prod = $this->checkReference($exp['reference'], $product);
							(stristr($prod->getName(), "refurbished")) ? $x94 = 'refurbished' : $x94 = 'new';
							$value = "<g:condition><![CDATA[" . $x94 . "]]></g:condition>";
							break;
						case "{sc:condition}" : 
							$prod = $this->checkReference($exp['reference'], $product);
							(stristr($prod->getName(), "refurbished")) ? $x94 = 'refurbished' : $x94 = 'new';
							$value = $x94;
							break;
						default : 
							$prod = $this->checkReference($exp['reference'], $product);
							if (in_array($exp['name'], $attr_arr)) {
							if (in_array($frontend_input[$exp['name']], array('select', 'multiselect'))) {
								eval('$xc9 =($prod->' . $exp['methodName'] . ");");
								$xc8 = explode(',', $xc9);
								if (count($xc8) > 1) {
									$value = array();
									foreach ($xc8 as $x97) {
										if (isset($x69[$x97][$storeId])) $value[] = $x69[$x97][$storeId];
										else { 
											if (isset($x69[$x97][0])) $value[] = $x69[$x97][0];
										}
									}
								} else { 
									if (isset($x69[$xc8[0]][$storeId])) {
										$value = $x69[$xc8[0]][$storeId];
									} 
									else { 
										if (isset($x69[$xc8[0]][0])) $value = $x69[$xc8[0]][0];
									}
								}
							} else { 
								eval('$value =($prod->' . $exp['methodName'] . ");");
							}
						} 
						if (in_array(@$x61[$exp['name']], $x61)) {
							$value = $x61[$exp['name']];
						} 
						$value = $this->_getCustomAttributes($product, $exp, $value);
						if (is_bool($value) && !$value) continue 3;
						break;
					} 
					
					if (count($exp['options']) > 0) {
						foreach ($exp['options'] as $key => $option) {
							if ($key >= $this->option) {
								switch ($exp['options'][$this->option]) {
									case "substr" : 
										if (isset($exp['options'][$this->option + 1]) && strlen($value) > $exp['options'][$this->option + 1]) {
											$value = substr($value, 0, $exp['options'][$this->option + 1] - 3);
											$xc7 = strrpos($value, " ");
											$value = substr($value, 0, $xc7) . $exp['options'][$this->option + 2];
										} 
										$this->skipOptions(3);
										break;
									case "strip_tags" : 
										$xca = " ";
										$value = preg_replace('!<br />!isU', $xca, $value);
										$value = preg_replace('!<br/>!isU', $xca, $value);
										$value = preg_replace('!<br>!isU', $xca, $value);
										$value = strip_tags($value);
										$this->skipOptions(1);
										break;
									case "htmlentities" : 
										$value = htmlspecialchars(($value));
										$this->skipOptions(1);
										break;
									case "implode" : 
										$value = (is_array($value)) ? implode($exp['options'][$this->option + 1], $value) : $value;
										$this->skipOptions(2);
										break;
									case "float" : 
										$value = number_format(floatval($value), $exp['options'][$this->option + 1], '.', '');
										$this->skipOptions(2);
										break;
									case "html_entity_decode" : 
										$value = html_entity_decode($value, ENT_QUOTES, 'UTF-8');
										$this->skipOptions(1);
										break;
									case "inline": 
										$value = preg_replace('/(\r\n|\n|\r|\r\n\t)/s', ' ', $value);
										$this->skipOptions(1);
										break;
									case "strtolower": 
										$value = mb_strtolower($value, "UTF8");
										$this->skipOptions(1);
										break;
									case "strtoupper": 
										$value = mb_strtoupper($value, "UTF8");
										$this->skipOptions(1);
										break;
									case "cleaner": 
										$value = preg_replace('/' . '[-]' . '|[-][?-?]+' . '|([??]|[?-?])[?-?]*' . '|[?-?]((?![?-?])|[?-?]{2,})' . '|[?-?](([?-?](?![?-?]))|' . '(?![?-?]{2})|[?-?]{3,})' . '/S', ' ', $value);
										$value = str_replace('��', '', $value);
										$this->skipOptions(1);
										break;
									default : 
										//$x9b->option = $this->option;
										$value = $this->_getCustomOptions($product, $exp, $value);
										//$this->option = $x9b->option;
										if (is_bool($value) && !$value) continue 3;
										break;
								}
							}
						}
					} 
					if ($type > 1) {
						$value = $this->escape($value);
					}
					$value = str_replace(array("<", ">"), array("{([", "])}"), $value);
					$text = str_replace($exp['fullpattern'], $value, $text);
				} 
				$text = $this->xf0($text, $product, $type);
				if ($type == 1 || ($type != 1 && !$this->_display)) {
					$text = $this->decode($text);
				}
				if ($type == 1) {
					$text = $this->filterEmpty($text, $enclose_data);
				}
				else { 
					if (!$this->_display) {
						$text = $this->xf6($text, $separator, $protector);
					}
					else { 
						$text = $this->tablerow(($text), false);
					}
				} 
				$text = str_replace(array("{([", "])}"), array("<", ">"), $text);
				if (!empty($text)) {
					if ($type == 1) $xml.=$text . "";
					else $xml.=$text . "\r\n";
					
					if ($this->_display) {
						$html.=$xml;
						$xml = '';
					} else { 
						if ($x9a % Mage::getStoreConfig("datafeed/system/buffer") == 0) {
							$file->streamWrite($xml);
							unset($xml);
							$xml = '';
						}
					} 
					
					if ($this->_limit && $x9a >= $this->_limit) break 2;
					$x9a++;
				}
			}
		} 
		if (!$this->_display) {
			$file->streamWrite($xml);
			if (strlen(trim($footer)) > 1) $file->streamWrite($footer . "\n");
		} 
		else { 
			$html.=$xml;
			$html.=$footer . "\n";
			if ($type > 1) $html.="</table>";
		} 
		unset($collection);
		
		if ($this->_display && !$this->_debug) {
			($type == 1) ? $xc4 = "<pre name='code' class='xml'>" . htmlspecialchars($html) . "</pre>" : $xc4 = $html;
			return("\r\n   \t<html>\r\n	<head> <title>" . $this->getFeedName() . "</title>\r\n"
					."<link type='text/css' rel='stylesheet' href='" . $web_url . "skin/adminhtml/default/default/datafeed/SyntaxHighlighter/css/SyntaxHighlighter.css'></link>\r\n"
					."<script language='javascript' src='" . $web_url . "skin/adminhtml/default/default/datafeed/SyntaxHighlighter/js/shCore.js'></script>\r\n"
					."<script language='javascript' src='" . $web_url . "skin/adminhtml/default/default/datafeed/SyntaxHighlighter/js/shBrushXml.js'></script>\r\n"
					."</head>\r\n<body>\r\n"
					. $xc4 
					."<script language='javascript'>\r\n"
					."dp.SyntaxHighlighter.HighlightAll('code',false,false,false,false);\r\n"
					."</script>\r\n"
					."</body></html>");

		} elseif ($this->_debug) {
			echo "<br><br>------------ XML OUTPUT ----------------<br>";
			$xc4 = "<pre>" . htmlentities($html) . "</pre>";
			return $xc4;

		} else { 
			$file->streamClose();
			$this->setFeedUpdatedAt(Mage::getSingleton('core/date')->gmtDate('Y-m-d H:i:s'));
			$this->save();
		} 
		return $this;
	}
	
	protected function _getCustomOptions($product, $exp, $value) {
	
		if ($exp['options'][$this->option] == "number_format") {
			$value = number_format($value, $exp['options'][$this->option + 1], $exp['options'][$this->option + 2], '');
			//skip the two next options
			$this->skipOptions(3);
		}
		else {
			$func = $exp['options'][$this->option];
			$value = $$func($value);
			$this->skipOptions(1);
		}	
		return $value;
	}

	protected function _getCustomAttributes($product,$exp,$value){
	
		if ("{configurable_sizes}" == $exp['pattern']) {
	
				if($product->type_id=='configurable'){
					// Your custom script
					$childProducts = Mage::getModel('catalog/product_type_configurable')->getUsedProducts(null, $product);
					$sizes = array();
					foreach ($childProducts as $child)
						$sizes[]= $child->getAttributeText('size');
	
					return implode(',',$sizes);
				}
				
				return  false;
		}
		
		return $value;
	}
	
	protected function _isSpecialDate($prod) {
		$today = date("Y-m-d H:i:s", Mage::getModel('core/date')->timestamp(time()));
		if ($prod->getSpecialFromDate() && !$prod->getSpecialToDate() && $prod->getSpecialFromDate() <= $today) {
			return true;
		}
		elseif ($prod->getSpecialFromDate() && $prod->getSpecialToDate() 
				&& $prod->getSpecialFromDate() <= $today && $today < $prod->getSpecialToDate()) {
			return true;
		}
		return false;
	}
	
	protected function _getPrice($prod, $exp, $include_tax, $base_currency) {
		if ($prod->type_id == "bundle") {
			if (($prod->price_type || (!$prod->price_type && $prod->special_price < $prod->price)) && $prod->special_price > 0) {
				if ($prod->price_type) $price = number_format($prod->price * $prod->special_price / 100, 2, ".", "");
				else {
					$price = $prod->special_price;
				}
			} else {
				$price = $prod->price;
			}
		}
		else {
			$price = ($prod->getSpecialPrice() && $prod->getSpecialPrice() < $prod->getPrice()) ? $prod->getSpecialPrice() : $prod->getPrice();
		}

		(!isset($exp['options'][1])) ? $x1f = false : $x1f = $exp['options'][1];
		$value = $this->calculatePrice($price, $include_tax, $prod->getTaxClassId(), $x1f);
		(!isset($exp["options"][0])) ? $x1b = $base_currency : $x1b = $exp["options"][0];
		$value = $this->xf1($value, $x1b);
		$value = number_format($value, 2, ".", "");
		
		return $value;
	}
	
	protected function _getDescription($prod, $wrap=false) {
		$desc = $prod->getDescription() . $prod->getShortDescription();
		$xc5 = "|<iframe(.*)</iframe>|U";
		preg_match($xc5, $desc, $xc6);
		if ($xc6) {
			$desc = $prod->getAttributeText('manufacturer') . " " . $prod->getName() . " - Part number: " . $prod->getSku() . " - Category : {categories,[1],[1],[1]}";
		}
		else {
			if (in_array("strip_tags", $exp['options'])) {
				$desc = preg_replace('!<br />!isU', " ", $desc);
				$desc = preg_replace('!<br/>!isU', " ", $desc);
				$desc = preg_replace('!<br>!isU', " ", $desc);
				$desc = strip_tags($desc);
			}
			if (in_array("html_entity_decode", $exp['options'])) {
				$desc = html_entity_decode($desc, ENT_QUOTES, 'UTF-8');
			}
			if (in_array("htmlentities", $exp['options'])) {
				$desc = htmlspecialchars(($desc));
			}
			if (strlen($desc) > 900) {
				$desc = substr($desc, 0, 900 - 3);
				$xc7 = strrpos($desc, " ");
				$desc = substr($desc, 0, $xc7) . '...';
			}
		}
		if ($desc == null) $desc = $prod->getAttributeText('manufacturer') . " " . $prod->getName() . " - Part number: " . $prod->getSku() . " - Category : {categories,[1],[1],[1]}";
		$desc = preg_replace('/' . '[-]' . '|[-][?-?]+' . '|([??]|[?-?])[?-?]*' . '|[?-?]((?![?-?])|[?-?]{2,})' . '|[?-?](([?-?](?![?-?]))|' . '(?![?-?]{2})|[?-?]{3,})' . '/S', ' ', $desc);
		$desc = str_replace('��', '', $desc);
		if ($wrap) {
			$value = "<description><![CDATA[" . $desc . "]]></description>";
		}
		else{
			$value = $desc;
		}
		
		return $value;
	}
	
	protected function _getPriceRules($prod, $exp, $include_tax, $base_currency) {
		$storeId = $this->getStoreId();
		$rule = Mage::getResourceModel('catalogrule/rule');
		$current = Mage::app()->getLocale()->storeTimeStamp($storeId);
		$store = Mage::app()->getStore($storeId);
		$websiteId = $store->getWebsiteId();
		$customerGroupId = Mage::getSingleton('customer/session')->getCustomerGroupId();
		$rulePrice = $rule->getRulePrice($current, $websiteId, $customerGroupId, $prod->getId());
		if ($rulePrice !== false) 
			$xa6 = sprintf('%.2f', round($rulePrice, 2));
		else 
			$xa6 = $prod->getPrice();
		
		if ($prod->getSpecialFromDate() && !$prod->getSpecialToDate()) {
			if ($prod->getSpecialFromDate() <= date("Y-m-d H:i:s")) {
				if ($prod->type_id == "bundle") {
					if (($prod->price_type || (!$prod->price_type && $prod->special_price < $prod->price)) && $prod->special_price > 0) {
						if ($prod->price_type) $price = number_format($prod->price * $prod->special_price / 100, 2, ".", "");
						else {
							$price = $prod->special_price;
						}
					} else {
						$price = $prod->price;
					}
				} else {
					($prod->getSpecialPrice() && $prod->getSpecialPrice() < $prod->getPrice()) ? $price = $prod->getSpecialPrice() : $price = $xa6;
				}
			} else {
				if ($prod->type_id == "bundle") $price = $prod->price;
				else {
					$price = $xa6;
				}
			}
		} elseif ($prod->getSpecialFromDate() && $prod->getSpecialToDate()) {
			if ($prod->getSpecialFromDate() <= date("Y-m-d H:i:s") && date("Y-m-d H:i:s") < $prod->getSpecialToDate()) {
				if ($prod->type_id == "bundle") {
					if (($prod->price_type || (!$prod->price_type && $prod->special_price < $prod->price)) && $prod->special_price > 0) {
						if ($prod->price_type) $price = number_format($prod->price * $prod->special_price / 100, 2, ".", "");
						else {
							$price = $prod->special_price;
						}
					} else {
						$price = $prod->price;
					}
				} else {
					($prod->getSpecialPrice() && $prod->getSpecialPrice() < $prod->getPrice()) ? $price = $prod->getSpecialPrice() : $price = $xa6;
				}
			} else {
				if ($prod->type_id == "bundle") $price = $prod->price;
				else {
					$price = $xa6;
				}
			}
		} else {
			if ($prod->type_id == "bundle") {
				if (($prod->price_type || (!$prod->price_type && $prod->special_price < $prod->price)) && $prod->special_price > 0) {
					if ($prod->price_type) $price = number_format($prod->price * $prod->special_price / 100, 2, ".", "");
					else {
						$price = $prod->special_price;
					}
				} else {
					$price = $prod->price;
				}
			} else {
				($prod->getSpecialPrice() && $prod->getSpecialPrice() < $prod->getPrice()) ? $price = $prod->getSpecialPrice() : $price = $xa6;
			}
		}
		(!isset($exp['options'][1])) ? $x1f = false : $x1f = $exp['options'][1];
		$value = $this->calculatePrice($price, $include_tax, $prod->getTaxClassId(), $x1f);
		(!isset($exp["options"][0])) ? $x1b = $base_currency : $x1b = $exp["options"][0];
		$value = $this->xf1($value, $x1b);
		$value = number_format($value, 2, ".", "");
		
		return $value;
	}
}
