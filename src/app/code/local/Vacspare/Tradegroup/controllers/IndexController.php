<?php
class Vacspare_Tradegroup_IndexController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {
    	
    	/*
    	 * Load an object by id 
    	 * Request looking like:
    	 * http://site.com/tradegroup?id=15 
    	 *  or
    	 * http://site.com/tradegroup/id/15 	
    	 */
    	/* 
		$tradegroup_id = $this->getRequest()->getParam('id');

  		if($tradegroup_id != null && $tradegroup_id != '')	{
			$tradegroup = Mage::getModel('tradegroup/tradegroup')->load($tradegroup_id)->getData();
		} else {
			$tradegroup = null;
		}	
		*/
		
		 /*
    	 * If no param we load a the last created item
    	 */ 
    	/*
    	if($tradegroup == null) {
			$resource = Mage::getSingleton('core/resource');
			$read= $resource->getConnection('core_read');
			$tradegroupTable = $resource->getTableName('tradegroup');
			
			$select = $read->select()
			   ->from($tradegroupTable,array('tradegroup_id','title','content','status'))
			   ->where('status',1)
			   ->order('created_time DESC') ;
			   
			$tradegroup = $read->fetchRow($select);
		}
		Mage::register('tradegroup', $tradegroup);
		*/

			
		$this->loadLayout();     
		$this->renderLayout();
    }
}