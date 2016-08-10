<?php

class AHT_Backupcms_Adminhtml_Backupcms_BackupController extends Mage_Adminhtml_Controller_action
{
	public function indexAction() {
		$backupcmsIds = $this->getRequest()->getParam('backupcms');
		//print_r($backupcmsIds);die();
		$FileName = 'CmsPages-Backup-'.date("d-m-Y") . '.csv';
		$data = "";

		# Titlte of the CSV
		$row[0] = array();
		$row[0][] = 'Page Title';
		$row[0][] = 'Root Template';
		$row[0][] = 'Meta Keywords';
		$row[0][] = 'Meta Description';
		$row[0][] = 'Identifier';
		$row[0][] = 'Content Heading';
		$row[0][] = 'Content';
		$row[0][] = 'Layout Update Xml';
		$row[0][] = 'Status';
		
		# fill data in the CSV
		$i=1;
		foreach($backupcmsIds as $cmsId)
		{
			$_page = Mage::getModel('cms/page')->load($cmsId);
			$row[$i][] = $_page->getTitle();
			$row[$i][] = $_page->getRootTemplate();
			$row[$i][] = $_page->getMetaKeywords();
			$row[$i][] = $_page->getMetaDescription();
			$row[$i][] = $_page->getIdentifier();
			$row[$i][] = $_page->getContentHeading();
			$row[$i][] = $_page->getContent();
			$row[$i][] = $_page->getLayoutUpdateXml();
			$row[$i][] = $_page->getIsActive();
			$i++;
		}
		
		$csv_folder     =  Mage::getBaseDir('media') . DS . 'csv';
		
		if( is_dir($csv_folder) === false )
		{
			mkdir($csv_folder, 0777);
		}
		
		$CSVFileName    = $csv_folder.'/'.$FileName;
		$FileHandle     = fopen($CSVFileName, 'w') or die("can't open file");
		fclose($FileHandle);
		$fp = fopen($CSVFileName, 'w');
		
		foreach ($row as $fields) {
			fputcsv($fp, $fields);
		}
		fclose($fp);

		header('Content-Type: application/csv'); 
		header("Content-length: " . filesize($CSVFileName)); 
		header('Content-Disposition: attachment; filename="' . $FileName . '"'); 
		header("Content-Transfer-Encoding: binary");
		header('Pragma: no-cache');
		header('Expires: 0');
		set_time_limit(0);
		readfile($CSVFileName);  
		exit();
	}
	
	
	public function staticAction() {
		$backupcmsIds = $this->getRequest()->getParam('backupcms');
		//print_r($backupcmsIds);die();
		$FileName = 'CmsStaticBlocks-Backup-'.date("d-m-Y") . '.csv';
		$data = "";

		# Titlte of the CSV
		$row[0] = array();
		$row[0][] = 'Block Title';
		$row[0][] = 'Identifier';
		$row[0][] = 'Content';
		$row[0][] = 'Status';
		
		# fill data in the CSV
		$i=1;
		foreach($backupcmsIds as $cmsId)
		{
			$_page = Mage::getModel('cms/block')->load($cmsId);
			$row[$i][] = $_page->getTitle();
			$row[$i][] = $_page->getIdentifier();
			$row[$i][] = $_page->getContent();
			$row[$i][] = $_page->getIsActive();
			$i++;
		}
		
		$csv_folder     =  Mage::getBaseDir('media') . DS . 'csv';
		
		if( is_dir($csv_folder) === false )
		{
			mkdir($csv_folder, 0777);
		}
		
		$CSVFileName    = $csv_folder.'/'.$FileName;
		$FileHandle     = fopen($CSVFileName, 'w') or die("can't open file");
		fclose($FileHandle);
		$fp = fopen($CSVFileName, 'w');
		
		foreach ($row as $fields) {
			fputcsv($fp, $fields);
		}
		fclose($fp);

		header('Content-Type: application/csv'); 
		header("Content-length: " . filesize($CSVFileName)); 
		header('Content-Disposition: attachment; filename="' . $FileName . '"'); 
		header("Content-Transfer-Encoding: binary");
		header('Pragma: no-cache');
		header('Expires: 0');
		set_time_limit(0);
		readfile($CSVFileName);  
		exit();
	}
	public function importcmspageAction() {
		$model = Mage::getModel('cms/page');
		$session = Mage::getSingleton('core/session');
		if ($datap = $this->getRequest()->getPost()){
			if(isset($_FILES['csvimport']['name']) && $_FILES['csvimport']['name'] != '') {
				$handle = fopen($_FILES['csvimport']['tmp_name'], "r");
				$data = fgetcsv($handle, 1000, ","); //Remove if CSV file does not have column headings
				while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
					$title = $data[0];
					$root_template = $data[1];
					$meta_keywords = $data[2];
					$meta_description = $data[3];
					$identifier = $data[4];
					$content_heading = $data[5];
					$content  = $data[6];
					$layout_update_xml  = $data[7];
					$status  = $data[8];
					//Set data import
					$dataimport['title'] = $title;
					$dataimport['root_template'] = $root_template;
					$dataimport['meta_keywords'] = $root_template;
					$dataimport['meta_description'] = $meta_keywords;
					$dataimport['identifier'] = $identifier;
					$dataimport['content_heading'] = $content_heading;
					$dataimport['content'] = $content;
					$dataimport['layout_update_xml'] = $layout_update_xml;
					$dataimport['stores'] = $datap['stores'];
					$dataimport['created_time'] = now();	
					$dataimport['is_active'] = $status;
					$model->setData($dataimport);
					//Try import Data
					$ss = 0;
					$er = 0;
					try{
						$model->save();
						$model->getId();		
						$ss++;
					}
					catch(Exception $e){
						$er++;
					}
				}
				if($er==0 && $ss>0){
					$session->addSuccess('CMS Page import successfully');
					$url = Mage::helper('adminhtml')->getUrl('adminhtml/backupcms_backupcms/index/');
					$this->_redirectUrl($url);	
				}
				elseif($er>0 && $ss>0){
					$session->addSuccess('%s CMS Page import successfully, %s CMS Page import unsuccessful',$ss,$er);
					$url = Mage::helper('adminhtml')->getUrl('adminhtml/backupcms_backupcms/index/');
					$this->_redirectUrl($url);
				}
				elseif($er>0 && $ss==0){
					$session->addError('A page URL key for specified store already exists.');
					$url = Mage::helper('adminhtml')->getUrl('adminhtml/backupcms_backupcms/index/');
					$this->_redirectUrl($url);
				}
			}
		}
	}
	
	public function importcmsblockAction() {
		$model = Mage::getModel('cms/block');
		$session = Mage::getSingleton('core/session');
		if ($datap = $this->getRequest()->getPost()){
			if(isset($_FILES['csvimport']['name']) && $_FILES['csvimport']['name'] != '') {
				$handle = fopen($_FILES['csvimport']['tmp_name'], "r");
				$data = fgetcsv($handle, 1000, ","); //Remove if CSV file does not have column headings
				while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
					$title = $data[0];
					$identifier = $data[1];
					$content  = $data[2];
					$status  = $data[3];
					//Set data import
					$dataimport['title'] = $title;
					$dataimport['identifier'] = $identifier;
					$dataimport['content'] = $content;
					$dataimport['created_time'] = now();	
					$dataimport['is_active'] = $status;	
					$dataimport['stores'] = $datap['stores'];	
					//var_dump($dataimport);die();
					$model->setData($dataimport);
					//Try import Data
					$ss = 0;
					$er = 0;
					try{
						$model->save();
						$model->getId();		
						$ss++;
					}
					catch(Exception $e){
						$er++;
					}
				}
				if($er==0 && $ss>0){
					$session->addSuccess('CMS Block import successfully');
					$url = Mage::helper('adminhtml')->getUrl('adminhtml/backupcms_backupcms/static/');
					$this->_redirectUrl($url);	
				}
				elseif($er>0 && $ss>0){
					$session->addSuccess('%s CMS Block import successfully, %s CMS Block import unsuccessful',$ss,$er);
					$url = Mage::helper('adminhtml')->getUrl('adminhtml/backupcms_backupcms/static/');
					$this->_redirectUrl($url);
				}
				elseif($er>0 && $ss==0){
					$session->addError('A block identifier with the same properties already exists in the selected store.');
					$url = Mage::helper('adminhtml')->getUrl('adminhtml/backupcms_backupcms/static/');
					$this->_redirectUrl($url);
				}
			}
		}
	}
}