<?php
class AHT_Tradeprice_IndexController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {
    	
    	/*
    	 * Load an object by id 
    	 * Request looking like:
    	 * http://site.com/tradeprice?id=15 
    	 *  or
    	 * http://site.com/tradeprice/id/15 	
    	 */
    	/* 
		$tradeprice_id = $this->getRequest()->getParam('id');

  		if($tradeprice_id != null && $tradeprice_id != '')	{
			$tradeprice = Mage::getModel('tradeprice/tradeprice')->load($tradeprice_id)->getData();
		} else {
			$tradeprice = null;
		}	
		*/
		
		 /*
    	 * If no param we load a the last created item
    	 */ 
    	/*
    	if($tradeprice == null) {
			$resource = Mage::getSingleton('core/resource');
			$read= $resource->getConnection('core_read');
			$tradepriceTable = $resource->getTableName('tradeprice');
			
			$select = $read->select()
			   ->from($tradepriceTable,array('tradeprice_id','title','content','status'))
			   ->where('status',1)
			   ->order('created_time DESC') ;
			   
			$tradeprice = $read->fetchRow($select);
		}
		Mage::register('tradeprice', $tradeprice);
		*/

			
		$this->loadLayout();     
		$this->renderLayout();
    }
}