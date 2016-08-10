<?php
class AHT_Backupcms_IndexController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {
    	
    	/*
    	 * Load an object by id 
    	 * Request looking like:
    	 * http://site.com/backupcms?id=15 
    	 *  or
    	 * http://site.com/backupcms/id/15 	
    	 */
    	/* 
		$backupcms_id = $this->getRequest()->getParam('id');

  		if($backupcms_id != null && $backupcms_id != '')	{
			$backupcms = Mage::getModel('backupcms/backupcms')->load($backupcms_id)->getData();
		} else {
			$backupcms = null;
		}	
		*/
		
		 /*
    	 * If no param we load a the last created item
    	 */ 
    	/*
    	if($backupcms == null) {
			$resource = Mage::getSingleton('core/resource');
			$read= $resource->getConnection('core_read');
			$backupcmsTable = $resource->getTableName('backupcms');
			
			$select = $read->select()
			   ->from($backupcmsTable,array('backupcms_id','title','content','status'))
			   ->where('status',1)
			   ->order('created_time DESC') ;
			   
			$backupcms = $read->fetchRow($select);
		}
		Mage::register('backupcms', $backupcms);
		*/

			
		$this->loadLayout();     
		$this->renderLayout();
    }
}