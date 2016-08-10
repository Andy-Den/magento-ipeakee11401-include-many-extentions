<?php
ini_set('memory_limit','1024M');
set_time_limit(7200);
ini_set('max_execution_time',7200);
ini_set('display_errors', 1);
ini_set('output_buffering', 0);

/**
 * Celebros Qwiser - Magento Extension
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish correct extension functionality.
 * If you wish to customize it, please contact Celebros.
 *
 * @category    Celebros
 * @package     Celebros_Salesperson
 * @author		Omniscience Co. - Dan Aharon-Shalom (email: dan@omniscience.co.il)
 *
 */
 
//include_once("createZip.php");
class Celebros_Salesperson_Model_ObserverLarge extends Celebros_Salesperson_Model_Observer
{
	protected static $_profilingResults;
	protected $bExportProductLink = true;
	protected $_product_entity_type_id = null;
	protected $_category_entity_type_id = null;
	protected $prod_file_name="source_products";
	protected $_categoriesForStore = false;

	function __construct() {
		$this->_read=Mage::getSingleton('core/resource')->getConnection('core_read');
		$this->_product_entity_type_id = $this->get_product_entity_type_id();
		$this->_category_entity_type_id = $this->get_category_entity_type_id();
	}


        public function export_celebros($webAdmin) {
		//self::startProfiling(__FUNCTION__);
		 $this->isWebRun=$webAdmin;

		foreach (Mage::app()->getStores() as $store) {
			if ($store->getConfig('salesperson/export_settings/export_enabled')) {
				$this->_fStore_id = $store->getId();
				$this->export_config($this->_fStore_id);

				$this->_fileNameZip=Mage::getStoreConfig('salesperson/export_settings/zipname',$this->_fStore_id);

				$this->comments_style('section', "Store code: {$this->_fStore_id}, name: {$store->getName()}", 'STORE');

				$this->comments_style('section', "Zip file name: {$this->_fileNameZip}" , 'STORE');

				$this->_categoriesForStore = false;
                $this->_categoriesForStore = implode(',', $this->_getAllCategoriesForStore());

				$this->logProfiler('===============');
				$this->logProfiler('Starting Export');
				$this->logProfiler('===============',true);

				$this->logProfiler("Store code: {$this->_fStore_id}, name: {$store->getName()}");
				$this->logProfiler("Zip file name: {$this->_fileNameZip}");

				$this->logProfiler('Mem usage: '.memory_get_usage(true),true);


            $this->comments_style('icon', "Memory usage: ".memory_get_usage(true) , 'icon');

            $this->comments_style('icon', 'Exporting tables' , 'icon');
            $this->comments_style('info', "Memory usage: ".memory_get_usage(true) , 'info');

				$this->logProfiler('Exporting tables');
				$this->logProfiler('----------------',true);

				$this->export_tables();

            $this->comments_style('info', "Memory usage: ".memory_get_usage(true) , 'info');

            $this->comments_style('icon', 'Exporting products' , 'icon');
            $this->comments_style('info', "Memory usage: ".memory_get_usage(true) , 'info');

				$this->logProfiler('Writing products file');
				$this->logProfiler('---------------------',true);

				$this->export_store_products();

				$this->comments_style('icon', 'Exporting category-less products' , 'icon');
            $this->comments_style('info', "Memory usage: ".memory_get_usage(true) , 'info');

				$this->logProfiler('Writing category-less products file');
				$this->logProfiler('-----------------------------------',true);

				// Running over the products that aren't assigned to a category separately.
				$this->export_categoryless_products();

            $this->comments_style('icon', 'Creating ZIP file' , 'icon');
            $this->comments_style('info', "Memory usage: ".memory_get_usage(true) , 'info');

				$this->logProfiler('Creating ZIP file');
				$this->logProfiler('-----------------',true);

				$zipFilePath = $this->zipLargeFiles();

            $this->comments_style('icon', 'Checking FTP upload' , 'icon');
            $this->comments_style('info', "Memory usage: ".memory_get_usage(true) , 'info');

				if($this->_fType==="ftp" && $this->_bUpload)
				{

                $this->comments_style('info', 'Uploading export file' , 'info');

					$ftpRes = $this->ftpfile($zipFilePath);
					if(!$ftpRes)
					{
                    $this->comments_style('info', "Could not upload " . $zipFilePath . ' to ftp' , 'info');
						$this->logProfiler('FTP upload ERROR',true);
					}
					else
						$this->logProfiler('FTP upload success',true);
				}
				else
				{
                $this->comments_style('info', 'No need to upload export file' , 'info');
					$this->logProfiler('No need to upload export file',true);
				}

            $this->comments_style('icon', 'Finished' , 'icon');

            $this->comments_style('info', "Memory usage: ".memory_get_usage(true) , 'info');
            $this->comments_style('info', "Memory peek usage: ".memory_get_peak_usage(true) , 'info');

            $this->comments_style('icon', date('Y/m/d H:i:s') , 'icon');

				$this->logProfiler('Mem usage: '.memory_get_usage(true),true);
				$this->logProfiler('Mem peek usage: '.memory_get_peak_usage(true),true);

				//self::stopProfiling(__FUNCTION__);

				//$html = self::getProfilingResultsString();
				//$this->log_profiling_results($html);
				//echo $html;
			}
		}
	}
	/*
	 * Wrapper function around export_products(), that defines a specific sql query and file name for use when exporting store
	 * specific products.
	 */
	protected function deprecated_export_store_products() {
		$table = $this->getTableName("catalog_product_entity");
		$categoryProductsTable = $this->getTableName("catalog_category_product");
		$sql = "SELECT DISTINCT(entity_id), type_id, sku
				FROM {$table}
				LEFT JOIN (`{$categoryProductsTable}`)
					ON (`{$categoryProductsTable}`.`category_id` IN ({$this->_getCategoriesForStore()}))
				WHERE {$table}.entity_type_id ={$this->_product_entity_type_id}
					AND {$table}.`entity_id` = `{$categoryProductsTable}`.`product_id`";
		$this->export_products($sql, $this->prod_file_name);
	}
    
    protected function export_store_products() {
        $table = $this->getTableName("catalog_product_entity");
        $categoryProductsTable = $this->getTableName("catalog_category_product");
        $catalogProductWebsite=$this->getTableName("catalog_product_website");
        $rootCategoryId = $this->_fStore->getRootCategoryId();
        $sql = "SELECT DISTINCT(entity_id), type_id, sku
            FROM {$table}
            LEFT JOIN (`{$categoryProductsTable}`)
                ON (`{$categoryProductsTable}`.`category_id` IN ({$this->_categoriesForStore}))
            LEFT JOIN (`{$catalogProductWebsite}`)
                    ON ({$table}.`entity_id` = `{$catalogProductWebsite}`.`product_id`)
            WHERE {$table}.entity_type_id ={$this->_product_entity_type_id}
                AND {$table}.`entity_id` = `{$categoryProductsTable}`.`product_id`
                AND `{$categoryProductsTable}`.`category_id` != {$rootCategoryId}
                AND `{$catalogProductWebsite}`.`website_id` =".Mage::app()->getWebsite()->getId();
        
        $this->export_products($sql, $this->prod_file_name);
    }
    
	/*
	 * Wrapper function around export_products(), that defines a specific sql query and file name for use when exporting products
	 * that aren't assigned to any category (thus, not appearing under any store either).
	 */
	protected function deprecated_export_categoryless_products() {
		$table = $this->getTableName("catalog_product_entity");
		$categoryProductsTable = $this->getTableName("catalog_category_product");
		$sql = "SELECT DISTINCT(entity_id), type_id, sku
				FROM {$table}
				LEFT JOIN (`{$categoryProductsTable}`)
					ON ({$table}.`entity_id` = `{$categoryProductsTable}`.`product_id`)
				WHERE {$table}.entity_type_id ={$this->_product_entity_type_id}
					AND `{$categoryProductsTable}`.`product_id` IS NULL";
		$this->export_products($sql, 'categoryless_products');
	}
    
    protected function export_categoryless_products() {
        $table = $this->getTableName("catalog_product_entity");
        $categoryProductsTable = $this->getTableName("catalog_category_product");
        $catalogProductWebsite=$this->getTableName("catalog_product_website");
        $rootCategoryId = $this->_fStore->getRootCategoryId();
        $sql = "SELECT DISTINCT(entity_id), type_id, sku
                FROM {$table}
                LEFT JOIN (`{$categoryProductsTable}`)
                    ON ({$table}.`entity_id` = `{$categoryProductsTable}`.`product_id`)
                LEFT JOIN (`{$catalogProductWebsite}`)
                    ON ({$table}.`entity_id` = `{$catalogProductWebsite}`.`product_id`)
                WHERE (`{$categoryProductsTable}`.`product_id` IS NULL 
                    OR `{$categoryProductsTable}`.`category_id` = {$rootCategoryId} )
                    AND {$table}.entity_type_id = {$this->_product_entity_type_id}
                    AND `{$catalogProductWebsite}`.`website_id` = " . Mage::app()->getWebsite()->getId();
                    
        $this->export_products($sql, 'categoryless_products');
    }

	protected function log_profiling_results($html) {
		$fh = $this->create_file("profiling_results.log", "html");
		$this->write_to_file($html, $fh);
	}

	protected function get_status_attribute_id() {
		$table = $this->getTableName("eav_attribute");
		$sql = "SELECT attribute_id
		FROM {$table}
		WHERE entity_type_id ={$this->_product_entity_type_id} AND attribute_code='status'";
		return $this->_read->fetchOne($sql);
	}

	protected function get_product_entity_type_id() {
		$table = $this->getTableName("eav_entity_type");
		$sql = "SELECT entity_type_id
		FROM {$table}
		WHERE entity_type_code='catalog_product'";
		return $this->_read->fetchOne($sql);
	}

	protected function get_category_entity_type_id() {
		$table = $this->getTableName("eav_entity_type");
		$sql = "SELECT entity_type_id
		FROM {$table}
		WHERE entity_type_code='catalog_category'";
		return $this->_read->fetchOne($sql);
	}

	protected function get_visibility_attribute_id() {
		$table = $this->getTableName("eav_attribute");
		$sql = "SELECT attribute_id
		FROM {$table}
		WHERE entity_type_id ={$this->_product_entity_type_id} AND attribute_code='visibility'";
		return $this->_read->fetchOne($sql);
	}

	protected function get_category_name_attribute_id() {
		$table = $this->getTableName("eav_attribute");
		$sql = "SELECT attribute_id
		FROM {$table}
		WHERE entity_type_id ={$this->_category_entity_type_id} AND attribute_code='name'";
		return $this->_read->fetchOne($sql);
	}

	protected function get_category_is_active_attribute_id() {
		$table = $this->getTableName("eav_attribute");
		$sql = "SELECT attribute_id
		FROM {$table}
		WHERE entity_type_id ={$this->_category_entity_type_id} AND attribute_code='is_active'";
		return $this->_read->fetchOne($sql);
	}

	protected function export_extra_tables()
	{
		$this->comments_style('icon', "Exporting extra tables", 'icon');
		$read = Mage::getModel('core/resource')->getConnection('core_read');
		$extraTablesData=Mage::getStoreConfig('salesperson/export_settings/extra_tables',0);
		$extraTables=explode("\n",$extraTablesData);
		foreach($extraTables as $table) {
			if (trim($table)=='')
				continue;
			try {
				$tableName=$this->getTableName(trim($table));
			}
			catch (Exception $ex) {
				$this->comments_style('error', "Table '{$table}' does not exist", 'error');
				continue;
			}
			$tableExists=$read->isTableExists($tableName);
			if ($tableExists) {
				$this->comments_style('info', "Exporting table '{$tableName}'", 'info');
				$query = $read->select()
				  ->from($tableName,
					array('*'));
				$this->export_table($query, $tableName);
			}
			else
				$this->comments_style('error', "Table '{$table}'='{$tableName}' does not exist", 'error');
		}
	}
	protected function export_tables() {
		//self::startProfiling(__FUNCTION__);
		$read = Mage::getModel('core/resource')->getConnection('core_read');

		$table = $this->getTableName("catalog_eav_attribute");
		$query = $read->select()
			->from($table,
					array('attribute_id', 'is_searchable', 'is_filterable', 'is_comparable'));
		$this->export_table($query, "catalog_eav_attribute");

		$table = $this->getTableName("eav_attribute");
		$this->logProfiler("START {$table}");
		$this->logProfiler('Mem usage: '.memory_get_usage(true));
		$query = $read->select()
			->from($table,
				array('attribute_id', 'attribute_code', 'backend_type', 'frontend_input'))
			->where('entity_type_id = ?', $this->_product_entity_type_id);
		$this->export_attributes_table($query, "attributes_lookup");
		$this->logProfiler("FINISH {$table}");
		$this->logProfiler('Mem usage: '.memory_get_usage(true),true);


		$table = $this->getTableName("catalog_product_entity");
		$this->logProfiler("START {$table}");
		$this->logProfiler('Mem usage: '.memory_get_usage(true));
		$categories = implode(',', $this->_getAllCategoriesForStore());
		$categoryProductsTable = $this->getTableName("catalog_category_product");

		$query = $read->select()
			->from($table,
				array('entity_id', 'type_id', 'sku'))
			->where("`{$table}`.`entity_type_id` = ?", $this->_product_entity_type_id)
			->joinLeft($categoryProductsTable, 
						"`{$table}`.`entity_id` = `{$categoryProductsTable}`.`product_id`",
						array())
			->where("`{$categoryProductsTable}`.`category_id` IN ({$categories})")
			->group('entity_id');
		$this->export_table($query, "catalog_product_entity");
		$this->logProfiler("FINISH {$table}");
		$this->logProfiler('Mem usage: '.memory_get_usage(true),true);


		$table = $this->getTableName("catalog_product_entity_int");
		$this->logProfiler("START {$table}");
		$this->logProfiler('Mem usage: '.memory_get_usage(true));
		$status_attribute_id = $this->get_status_attribute_id();

		$sql = $read->select()

			->from($table, 
				array('entity_id'))
			->where('entity_type_id = ?', $this->_product_entity_type_id)
            ->where('store_id = ?', $this->_fStore_id)
			->where('attribute_id = ?', $status_attribute_id)
			->where('value = ?', '2');

		$this->export_product_att_table($sql, "disabled_products");

		$this->logProfiler("FINISH {$table}");
		$this->logProfiler('Mem usage: '.memory_get_usage(true),true);


		$table = $this->getTableName("catalog_product_entity_int");
		$this->logProfiler("START {$table}");
		$this->logProfiler('Mem usage: '.memory_get_usage(true));
		$visibility_attribute_id = $this->get_visibility_attribute_id();

		$sql = $read->select()
			->distinct()
			->from($table, 
				array('entity_id'))
			->where('entity_type_id = ?', $this->_product_entity_type_id)
            //->where('store_id = ?', $this->_fStore_id)
			->where('attribute_id = ?', $visibility_attribute_id)
			->where('value = ?', '1');

		$this->export_product_att_table($sql, "not_visible_individually_products");		

		$this->logProfiler("FINISH {$table}");
		$this->logProfiler('Mem usage: '.memory_get_usage(true),true);


		$table = $this->getTableName("catalog_product_entity_varchar");		
		$this->logProfiler("START {$table}");
		$this->logProfiler('Mem usage: '.memory_get_usage(true));

		$sql = $read->select()
			->from($table, 
				array('entity_id', 'value', 'attribute_id'))
			->where('entity_type_id = ?', $this->_product_entity_type_id);

		$this->export_product_att_table($sql, "catalog_product_entity_varchar");
		$this->logProfiler("FINISH {$table}");
		$this->logProfiler('Mem usage: '.memory_get_usage(true),true);


		$table = $this->getTableName("catalog_product_entity_int");
		$this->logProfiler("START {$table}");
		$this->logProfiler('Mem usage: '.memory_get_usage(true));
		$query = $read->select()
			->from($table,
					array('entity_id', 'value', 'attribute_id'))
            //->where('store_id = ?', $this->_fStore_id)
			->where('entity_type_id = ?', $this->_product_entity_type_id);

		$this->export_product_att_table($query, "catalog_product_entity_int");

		$this->logProfiler("FINISH {$table}");
		$this->logProfiler('Mem usage: '.memory_get_usage(true),true);


		$table = $this->getTableName("catalog_product_entity_text");		
		$this->logProfiler("START {$table}");
		$this->logProfiler('Mem usage: '.memory_get_usage(true));
		$query = $read->select()
			->from($table,
					array('entity_id', 'value', 'attribute_id'))
			->where('entity_type_id = ?', $this->_product_entity_type_id);


		$this->export_product_att_table($query, "catalog_product_entity_text");
		$this->logProfiler("FINISH {$table}");
		$this->logProfiler('Mem usage: '.memory_get_usage(true),true);


		$table = $this->getTableName("catalog_product_entity_decimal");		
		$this->logProfiler("START {$table}");
		$this->logProfiler('Mem usage: '.memory_get_usage(true));
		$query = $read->select()
			->from($table,
					array('entity_id', 'value', 'attribute_id'))
			->where('entity_type_id = ?', $this->_product_entity_type_id);

		$this->export_product_att_table($query, "catalog_product_entity_decimal");
		$this->logProfiler("FINISH {$table}");
		$this->logProfiler('Mem usage: '.memory_get_usage(true),true);


		$table = $this->getTableName("catalog_product_entity_datetime");
		$this->logProfiler("START {$table}");
		$this->logProfiler('Mem usage: '.memory_get_usage(true));
		$query = $read->select()
			->from($table,
					array('entity_id', 'value', 'attribute_id'))
			->where('entity_type_id = ?', $this->_product_entity_type_id);

		$this->export_product_att_table($query, "catalog_product_entity_datetime");
		$this->logProfiler("FINISH {$table}");
		$this->logProfiler('Mem usage: '.memory_get_usage(true),true);


		$table = $this->getTableName("eav_attribute_option_value");
		$this->logProfiler("START {$table}");
		$this->logProfiler('Mem usage: '.memory_get_usage(true));
		$query = $read->select()
			->from($table,
					array('option_id', 'value'));

		$this->export_table($query, "eav_attribute_option_value", array('option_id'));
		$this->logProfiler("FINISH {$table}");
		$this->logProfiler('Mem usage: '.memory_get_usage(true),true);


		$table = $this->getTableName("eav_attribute_option");		
		$this->logProfiler("START {$table}");
		$this->logProfiler('Mem usage: '.memory_get_usage(true));
		$query = $read->select()
			->from($table,
				array('option_id', 'attribute_id'));

		$this->export_table($query, "eav_attribute_option");
		$this->logProfiler("FINISH {$table}");
		$this->logProfiler('Mem usage: '.memory_get_usage(true),true);


		$table = $this->getTableName("catalog_category_product");		
		$this->logProfiler("START {$table}");
		$this->logProfiler('Mem usage: '.memory_get_usage(true));
		$categories = implode(',', $this->_getAllCategoriesForStore());
		$query = $read->select()
			->from($table,
					array('category_id', 'product_id'))
			->where("`category_id` IN ({$categories})");
		$this->export_table($query, "catalog_category_product");
		$this->logProfiler("FINISH {$table}");
		$this->logProfiler('Mem usage: '.memory_get_usage(true),true);


		$table = $this->getTableName("catalog_category_entity");		
		$this->logProfiler("START {$table}");
		$this->logProfiler('Mem usage: '.memory_get_usage(true));
		$categories = implode(',', $this->_getAllCategoriesForStore());
		$query = $read->select()
			->from($table,
					array('entity_id', 'parent_id', 'path'))
			->where("`entity_id` IN ({$categories})");
		$this->export_table($query, "catalog_category_entity");
		$this->logProfiler("FINISH {$table}");
		$this->logProfiler('Mem usage: '.memory_get_usage(true),true);


		$table = $this->getTableName("catalog_category_entity_varchar");
		$this->logProfiler("START {$table}");
		$this->logProfiler('Mem usage: '.memory_get_usage(true));
		$name_attribute_id = $this->get_category_name_attribute_id();
		$categories = implode(',', $this->_getAllCategoriesForStore());
		$query = $read->select()
			->from($table,
					array('entity_id', 'value'))
			->where('attribute_id = ?', $name_attribute_id)
			->where("`entity_id` IN ({$categories})");

		$this->export_table($query, "category_lookup", array('entity_id'));
		$this->logProfiler("FINISH {$table}");
		$this->logProfiler('Mem usage: '.memory_get_usage(true),true);


		$table = $this->getTableName("catalog_category_entity_int");
		$this->logProfiler("START {$table}");
		$this->logProfiler('Mem usage: '.memory_get_usage(true));
		$is_active_attribute_id = $this->get_category_is_active_attribute_id();
		$categories = implode(',', $this->_getAllCategoriesForStore());
		$query = $read->select()
			->from($table,
					array('entity_id'))
			->where('attribute_id = ?', $is_active_attribute_id)
			->where('value = 0')
			->where('entity_type_id = ?', $this->_category_entity_type_id)
			->where("`entity_id` IN ({$categories})");

		$this->export_table($query, "disabled_categories", array('entity_id'));
		$this->logProfiler("FINISH {$table}");
		$this->logProfiler('Mem usage: '.memory_get_usage(true),true);


		$table = $this->getTableName("catalog_product_super_link");		
		$this->logProfiler("START {$table}");
		$this->logProfiler('Mem usage: '.memory_get_usage(true));
		$query = $read->select()
			->from($table,
					array('product_id', 'parent_id'));

		$this->export_table($query, "catalog_product_super_link");
		$this->logProfiler("FINISH {$table}");
		$this->logProfiler('Mem usage: '.memory_get_usage(true),true);


		$table = $this->getTableName("catalog_product_super_attribute");
		$this->logProfiler("START {$table}");
		$this->logProfiler('Mem usage: '.memory_get_usage(true));
		$query = $read->select()
			->from($table,
					array('product_id', 'attribute_id'));

		$this->export_table($query, "catalog_product_super_attribute");
		$this->logProfiler("FINISH {$table}");
		$this->logProfiler('Mem usage: '.memory_get_usage(true),true);	

		$table = $this->getTableName("celebrosfieldsmapping");
		$this->logProfiler("START {$table}");
		$this->logProfiler('Mem usage: '.memory_get_usage(true));
		$query = $read->select()
			->from($table,
					array('xml_field', 'code_field'));
		$this->export_table($query, "salesperson_mapping");
		$this->logProfiler("FINISH {$table}");
		$this->logProfiler('Mem usage: '.memory_get_usage(true),true);		

		$table = $this->getTableName("review_entity");
		$product_entity_id = $read->select()
									->from($table, array('entity_id'))
									->where("`entity_code` = 'product'")
									->query()->fetch();

		$table = $this->getTableName("review_entity_summary");
		$this->logProfiler("START {$table}");
		$this->logProfiler('Mem usage: '.memory_get_usage(true));
		$query = $read->select()
			->from($table,
					array('entity_pk_value', 'reviews_count', 'rating_summary'))
			->where("`entity_type` = '{$product_entity_id['entity_id']}'");
		$this->export_table($query, "review_entity", array('entity_pk_value'));
		$this->logProfiler("FINISH {$table}");
		$this->logProfiler('Mem usage: '.memory_get_usage(true),true);		

        $table = $this->getTableName("catalog_product_relation");
        $this->logProfiler("START {$table}");
        $this->logProfiler('Mem usage: '.memory_get_usage(true));
        $query = $read->select()
            ->from($table,
                    array('parent_id', 'child_id'));
        $this->export_table($query, "catalog_product_relation");
        $this->logProfiler("FINISH {$table}");
        $this->logProfiler('Mem usage: '.memory_get_usage(true),true);
		$this->export_extra_tables();
		//self::stopProfiling(__FUNCTION__);
	}

	protected function export_table_rows($sql, $fields, $fh)
	{
		$str = "";

		$query = $sql->query();
		$rowCount=0;
		$processedRows = array();

		while ($row = $query->fetch()) {

			//$this->logProfiler("Block read start ({$this->_limit} products");
			//$this->logProfiler('Mem usage: '.memory_get_usage(true));

			//remember all the rows we're processing now, so we won't go over them again when we iterate over the default store.
			if (isset($fields)) {
				$concatenatedRow = '';
				foreach ($fields as $field) {
					$concatenatedRow .= $row[$field] . '-';
				}
				$processedRows[] = substr($concatenatedRow, 0, -1);
			}

			$str.= "^" . implode("^	^",$row) . "^" . "\r\n";
			$rowCount++;

			if (($rowCount%1000)==0)
			{
				//$this->logProfiler("Write block start");
				$this->write_to_file($str , $fh);
				//$this->logProfiler("Write block end");
				$str="";
			}
		}

		if (($rowCount%1000)!=0)
		{
			//$this->logProfiler("Write last block start");
			$this->write_to_file($str , $fh);
			//$this->logProfiler("Write last block end");
		}

		$this->logProfiler("Total rows: {$rowCount}");

		return $processedRows;
	}

	protected function write_headers($sql, $fh)
	{	
		$header = "^";
		$columns = $sql->getPart('columns');
		$fields=array();
		foreach ($columns as $column) {
			if ($column[1] != '*') {
				$fields[] = $column[1];
			}
			else {
				$read = Mage::getModel('core/resource')->getConnection('core_read');
				$fields=array_merge($fields,array_keys($read->describeTable($this->getTableName($columns[0][0]))));
			}
		}
		$header .= implode("^	^", $fields);
		$header .= "^" . "\r\n";
		$this->write_to_file($header, $fh);

		return $columns;
	}

	/* This is a separate function because of two changes from export_table(): 
	 * 1. We're adding another column header at the start for the frontend_label (which isn't selected in the first run)
	 * 2. On the first run, we've added a join statement to get the labels from eav_attribute_label. The second run covers all
	 * cases where eav_attribute_label didn't have a value for a specific attribute.
	 */
	protected function export_attributes_table($sql, $filename)
	{
		$fh = $this->create_file($filename);

		//Adding another column header before the call to write_headers().
		$columns = $sql->getPart('columns');
		$sql->columns('frontend_label');
		$this->write_headers($sql, $fh);
		$sql->setPart('columns', $columns);

		$sql->limit(100000000, 0);

		//Preparing the select object for the second query.
		$secondSql = clone($sql);

		//Adding a join statement to the first run alone, to get labels from eav_attribute_label.
		$table = $sql->getPart('from');
		$table = array_shift($table);
		$labelTable = $this->getTableName("eav_attribute_label");
		$sql->joinLeft($labelTable, 
				"{$table['tableName']}.`attribute_id` = `{$labelTable}`.`attribute_id`
				AND `{$labelTable}`.`store_id` = {$this->_fStore_id}",
				array('value'))
			->where("`{$labelTable}`.`value` IS NOT NULL")
			->group('attribute_id');

		//Process the rows that are covered by eav_attribute_label.
		$processedRows = $this->export_table_rows($sql, array('attribute_id'), $fh);

		//run a second time with only ids that are not in the list from the first run.
		$secondSql->columns('frontend_label');
		if (count($processedRows)) {
			$secondSql->where("`attribute_id` NOT IN (?)", $processedRows);
		}			
		$this->export_table_rows($secondSql, null, $fh);

		fclose($fh);
		//self::stopProfiling(__FUNCTION__. "({$filename})");
	}

	protected function export_table($sql, $filename, $main_fields = null)
	{
		$fh = $this->create_file($filename);

		$this->write_headers($sql, $fh);

		$sql->limit(100000000, 0);

		//This part is only for tables that should be run twice - once with the store view, and again with the default.
		if (isset($main_fields)) {
			//preparing the query for the second run on the default store view.
			$secondSql = clone($sql);

			//On the first run, we'll only get the current store view.
			$sql->where('store_id = ?', $this->_fStore_id);
		}

		//Run the actual process of getting the rows and inserting them to the file,
		// and output the list of rows you covered to $processedRows.
		$processedRows = $this->export_table_rows($sql, $main_fields, $fh);

		//This part is only for tables that should be run twice - once with the store view, and again with the default.
		if (isset($main_fields)) {
			//Specifying the default store view.
			$secondSql->where('store_id = 0');

			//Only add the where statement in case items were found in the first run.
			if (count($processedRows)) {
				$concat_fields = implode('-', $main_fields);
				$secondSql->where("CONCAT({$concat_fields}) NOT IN (?)", $processedRows);
			}

			//Run the actual process of getting each row again, this time selecting rows with the default store view.
			$this->export_table_rows($secondSql, null, $fh);
		}

		fclose($fh);
		//self::stopProfiling(__FUNCTION__. "({$filename})");
	}

	/*
	 * This version of the export_table function is meant for entity attribute tables, that have store view specific values.
	 * Differences:
	 * 1. We check whether the current store view has any categories assigned, and return nothing if it does not.
	 * 2. We've added a join statement to only get rows that correspond to products that are assigned to categories that are 
	 * under the current store view.
	 * 3. Before running export_table_rows() for the first time, we execute the query, and withdraw a list of rows that will
	 * be covered once the first run is complete. We then use that list in to exclude those rows from the second run. This is
	 * essential because we have to include some columns (entity_id, attribute_id) that might not be in the select statement.
	 */
	protected function export_product_att_table($sql, $filename) {

		$fh = $this->create_file($filename);

		$columns = $this->write_headers($sql, $fh);

		$sql->limit(100000000, 0);							

		//Get Relevant Categories for the query.
		$categoriesForStore = implode(',', $this->_getAllCategoriesForStore());

		//Don't run the query at all if no categories were found to match the current store view.
		if (!$categoriesForStore || !count($categoriesForStore)) {
			$this->logProfiler("Total rows: 0");

			fclose($fh);
			return;
		}

		//Fetch list of categoryless products.
		$read = Mage::getModel('core/resource')->getConnection('core_read');
		$table = $this->getTableName("catalog_product_entity");
		$categoryProductsTable = $this->getTableName("catalog_category_product");
		$categoryless_products = $read->fetchCol("SELECT DISTINCT(entity_id)
				FROM {$table}
				LEFT JOIN (`{$categoryProductsTable}`)
					ON ({$table}.`entity_id` = `{$categoryProductsTable}`.`product_id`)
				WHERE {$table}.entity_type_id ={$this->_product_entity_type_id}
					AND `{$categoryProductsTable}`.`product_id` IS NULL");
		//Fetch the list of products in the current store view.
		$products_from_store = $read->fetchCol("SELECT DISTINCT(product_id)
				FROM {$categoryProductsTable}
				WHERE `{$categoryProductsTable}`.`category_id` IN ({$categoriesForStore})");
		$relevant_products = implode(',', array_merge($categoryless_products, $products_from_store));
		$table = $sql->getPart('from');
		$table = array_shift($table);
		$sql->where("{$table['tableName']}.`entity_id` IN ({$relevant_products})");

		$secondSql = clone($sql);

		$sql->where("{$table['tableName']} . `store_id` = ?", $this->_fStore_id);

		//Get list of rows with this specific store view, to exclude when running on the default store view.
		$sql->columns('entity_id');
		$sql->columns('attribute_id');
		$query = $sql->query();
        
		$processedRows = array();
		while ($row = $query->fetch()) {
			$processedRows[] = $row['attribute_id'] . '-' . $row['entity_id'];
           
                Mage::log($row, NULL, $filename . '_store' . $this->_fStore_id . '.log', TRUE);
		}
		$sql->setPart('columns', $columns);

		//Run the query on each row and save results to the file.
		$this->export_table_rows($sql, null, $fh);

		//Prepare the second query.
		$secondSql->where("{$table['tableName']} . `store_id` = 0");
		if (count($processedRows)) {
			$secondSql->where("CONCAT(`attribute_id`, '-', `entity_id`) NOT IN (?)", $processedRows);
            
            $query2 = $secondSql->query();
            while ($row = $query2->fetch()) {
                Mage::log($row, NULL, $filename . '_global' . $this->_fStore_id . '.log', TRUE);
            }
		}

		//Run for the second time, now with the default store view.
		$this->export_table_rows($secondSql, null, $fh);

		fclose($fh);
		//self::stopProfiling(__FUNCTION__. "({$filename})");
	}

	protected function create_file($name, $ext = "txt") {
		//self::startProfiling(__FUNCTION__);
		if (!is_dir($this->_fPath)) $dir=@mkdir($this->_fPath,0777,true);
		$filePath = $this->_fPath . DIRECTORY_SEPARATOR . $name . "." . $ext;

		//if (file_exists($filePath)) unlink($filePath);
		$fh = fopen($filePath, 'wb');
		//self::stopProfiling(__FUNCTION__);
		return $fh;
	}

	protected function write_to_file($str, $fh){
		//self::startProfiling(__FUNCTION__);
		fwrite($fh, $str);

		//self::stopProfiling(__FUNCTION__);
	}

	public function zipLargeFiles() {
		//self::startProfiling(__FUNCTION__);

		$out = false;
		$zipPath = $this->_fPath . DIRECTORY_SEPARATOR . $this->_fileNameZip;

		$dh=opendir($this->_fPath);
		$filesToZip = array();
		while(($item=readdir($dh)) !== false && !is_null($item)){
			$filePath = $this->_fPath . DIRECTORY_SEPARATOR . $item;
			$ext = pathinfo($filePath, PATHINFO_EXTENSION);
			if(is_file($filePath) && ($ext == "txt" || $ext == "log")) {
				$filesToZip[] = $filePath;
			}
		}

		if (file_exists($zipPath)) {
			unlink($zipPath);
		}

		for($i=0; $i < count($filesToZip); $i++) {
			$filePath = $filesToZip[$i];
			$out = $this->zipLargeFile($filePath, $zipPath);
		}

		//self::stopProfiling(__FUNCTION__);
		return $out ? $zipPath : false;
	}

	public function zipLargeFile($filePath, $zipPath)
	{
		//self::startProfiling(__FUNCTION__);

		$out = false;

		$zip = new ZipArchive();
		if ($zip->open($zipPath, ZipArchive::CREATE) == true) {
			$fileName = basename($filePath);
			$out = $zip->addFile($filePath, basename($filePath));
			if(!$out) throw new  Exception("Could not add file '{$fileName}' to_zip_file");
			$zip->close();
			$ext = pathinfo($fileName, PATHINFO_EXTENSION);
			if($ext != "log") unlink($filePath);
		}
		else
		{
			throw new  Exception("Could not create zip file");
		}

		//self::stopProfiling(__FUNCTION__);
		return $out;
	}

	protected function _getCategoriesForStore()
	{
		if (!$this->_categoriesForStore) {
			$rootCategoryId = $this->_fStore->getRootCategoryId();
			$rootCategory = Mage::getModel('catalog/category')->load($rootCategoryId);
			$rootResource = $rootCategory->getResource();
			$this->_categoriesForStore = implode(',', $rootResource->getChildren($rootCategory));
		}
		return $this->_categoriesForStore;
	}

	/*
	 * This function gets root categories too, as well as disabled categories.
	 * We've left these in so as not to create holes in the tables export.
	 */
	protected function _getAllCategoriesForStore()
	{
		$read = Mage::getModel('core/resource')->getConnection('core_read');
		$table = $this->getTableName("catalog_category_entity");
		$sql2 = $read->select()

			->from($table,
					array('entity_id', 'path'));

		$results = $read->fetchPairs($sql2);
		$rootCategoryId = $this->_fStore->getRootCategoryId();
		$categories = array();
		foreach ($results as $entity_id => $path) {
			$path = explode('/', $path);
			if (count($path) > 1) {
				if ($path[1] == $rootCategoryId) {
					$categories[] = $entity_id;
				}
			} else {
				$categories[] = $entity_id;
			}
		}

		return $categories;
	}


	protected function export_products($sql, $fileName)
	{
        $this->comments_style('info', "Begining products export", 'info');
        $this->comments_style('info', "Memory usage: ".memory_get_usage(true), 'info');
        
        $this->logProfiler("START export products");
        $this->logProfiler('Mem usage: '.memory_get_usage(true));

		$fh = $this->create_file($fileName);

		$fields = array("id", "price", "image_link", "thumbnail_label", "type_id", "sku");

		$attributes = array("price", "image", "thumbnail", "type");

		if ($this->bExportProductLink) $fields[] = "link";

		$fields[] = "rating_summary";
		$fields[] = "reviews_count";
		$fields[] = "is_saleable";
		$fields[] = "is_in_stock";
		$fields[] = "qty";
		$fields[] = "min_qty";

		foreach ($fields as $key => $field) {
			$fields[$key] = Mage::helper('salesperson/mapping')->getMapping($field);
		}

		$header = "^" . implode("^	^",$fields) . "^" . "\r\n";
		$this->write_to_file($header, $fh);

		// *********************************

		if (!$this->_getCategoriesForStore() || !count($this->_getCategoriesForStore())) {			
			fclose($fh);
			return;
		}

		$table = $this->getTableName("catalog_product_entity");
		$categoryProductsTable = $this->getTableName("catalog_category_product");


		$stm = $this->_read->query($sql . " LIMIT 0, 100000000");

		$this->logProfiler('SQL QUERY: Number of rows: '.$stm->rowCount());
		$productNum=0;
		$rowCount=0;
		$str='';

		$product=Mage::getSingleton('catalog/product');

		$hasData=($row=$stm->fetch());

		while($hasData)
		{
			do
			{
				$product->load(($row["entity_id"]));

				$values["id"] = $product->getentity_id();
				$values["price"] = Mage::helper('core')->currency($this->getCalculatedPrice($product), false, false);

				$isException=false;
				$msgStr='ERROR';
				try {
					// Get original image 
					//$values["image_link"] = $product->getMediaConfig()->getMediaUrl($product->getData("image"));

 					// Get image from cache
					$values["image_link"] = $product->getImageUrl();
				}
				catch (Exception $e) {  // We get here in case that there is no product image and no placeholder image is set
					$isException=true;
					$msgStr='EXCEPTION';
				}
				if (($isException) || (stripos($values["image_link"],'no_selection')!=false) || (substr($values["image_link"],-1)==DIRECTORY_SEPARATOR)) {
                    if (!isset($values["image_link"])) {
                        $values["image_link"] = '';
                    }
                    
					$this->comments_style('warning','IMAGE '.$msgStr.': Product ID '.$values["id"].', image url: '.$values["image_link"],'warning');
					$values["image_link"] = '';
				}

				$isException=false;
				$msgStr='ERROR';
				try {
					// Get original image
					//$values["thumbnail"] =$product->getMediaConfig()->getMediaUrl($product->getData("thumbnail"));

					// Get image from cahce
					$values["thumbnail"] = Mage::helper('catalog/image')->init($product, 'thumbnail')->resize(66);
				}
				catch (Exception $e) {  // We get here in case that there is no thumbnail image and no placeholder image is set
					$isException=true;
					$msgStr='EXCEPTION';
				}
				if (($isException) || (stripos($values["thumbnail"],'no_selection')!=false) || (substr($values["thumbnail"],-1)==DIRECTORY_SEPARATOR)) {
                    if (!isset($values["thumbnail"])) {
                        $values["thumbnail"] = '';
                    }
                    
					$this->comments_style('warning','THUMBNAIL '.$msgStr.': Product ID '.$values["id"].', thumbnail url: '.$values["thumbnail"],'warning');
					$values["thumbnail"] = '';
				}

				$values["type_id"] = $product->gettype_id();
				$values["product_sku"] = $product->getSku();

				if($this->bExportProductLink)
				{
					$values["link"] = $product->getProductUrl();
				}

				$values["rating_summary"] = $this->getRatingSummary($product);
				$values["reviews_count"] = $this->getReviewsCount($product);

				$values["is_saleable"] = $product->isSaleable();

				$stockItem = $product->getStockItem();
				$values["is_in_stock"] = $stockItem->getIsInStock() ? "1" : "0";
				$values["qty"] = ((int) $stockItem->getQty());
				$values["min_qty"] = ((int) $stockItem->getMinQty());


				$str.= "^" . implode("^	^",$values) . "^" . "\r\n";

				$productNum++;

				if (($productNum%2000)==0)
				{
					$this->logProfiler("Product num: {$productNum}");
					//$this->logProfiler('Mem usage: '.memory_get_usage(true));
					//$this->logProfiler("Write block start");
					$this->write_to_file($str , $fh);
					//$this->logProfiler("Write block end",true);
					$str='';
				}

				//$product->cleanCache();
				$product->clearInstance();
				$product->reset();

				$rowCount++;

				/*					if (($rowCount%1000)==0)
									{
										$this->logProfiler("Number of processed rows: {$rowCount}");
										$this->logProfiler("Fetch start");
										$hasData=($row=$stm->fetch());
										$this->logProfiler("Fetch end", true);
									}
									else
				*/
				$hasData=($row=$stm->fetch());
			} while($hasData);

			if (($productNum%2000)!=0)
			{
				//$this->logProfiler("Product num: {$productNum}");
				//$this->logProfiler("Write last block start");
				$this->write_to_file($str , $fh);
				//$this->logProfiler("Write last block end",true);
			}

		}

		//$this->logProfiler("Finished outer while",true);

		fclose($fh);

		$this->logProfiler('Mem usage: '.memory_get_usage(true));
		$this->logProfiler("Total number of products: {$productNum}");
		$this->logProfiler("FINISH export products",true);
	}

































	protected static function startProfiling($key) {
		if(!isset(self::$_profilingResults[$key])) {
			$profile = new stdClass();
			$profile->average =0 ;
			$profile->count = 0;
			$profile->max = 0;
			self::$_profilingResults[$key] = $profile;
		}
		$profile = self::$_profilingResults[$key];
		if(isset($profile->start) && $profile->start > $profile->end) throw new Exception("The start of profiling timer '{$key}' is called before the stop of it was called");
		$profile->start = (float) array_sum(explode(' ',microtime()));
	}

	protected static function stopProfiling($key) {
		if(!isset(self::$_profilingResults[$key])) throw new Exception("The stop of profiling timer '{$key}' was called while the start was never declared");

		$profile = self::$_profilingResults[$key];
		if($profile->start == -1) throw new Exception("The start time of '{$key}' profiling is -1");

		$profile->end = (float) array_sum(explode(' ',microtime()));
		$duration = $profile->end - $profile->start;
		if($profile->max < $duration) $profile->max = $duration;

		$profile->average = ($profile->average * $profile->count + $duration)/($profile->count +1);
		$profile->count++;
	}

	protected static function getProfilingResultsString() {
		$html = "";
		if(count(self::$_profilingResults)) {
			$html.= "In sec:";
			$html.=  '<table border="1">';
			$html.=  "<tr><th>Timer</th><th>Total</th><th>Average</th><th>Count</th><th>Peak</th></tr>";
			foreach(self::$_profilingResults as $key =>$profile) {
				$total = $profile->average * $profile->count;
				$html.=  "<tr><td>{$key}</td><td>{$total}</td><td>{$profile->average}</td><td>{$profile->count}</td><td>{$profile->max}</td></tr>";
			}
			$html.=  "</table>";
		}

		$html.= 'PHP Memory peak usage: ' . memory_get_peak_usage();

		return $html;
	}

}