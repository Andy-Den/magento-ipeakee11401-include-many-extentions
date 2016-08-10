<?php
class Tal_Tabs_Block_Tabs  extends Mage_Core_Block_Template implements Mage_Widget_Block_Interface
{
    
    /**
     * Initialization
     */
    protected function _construct()
    {
        parent::_construct();
    }

    /**
     * Produce tabs as html
     *
     * @return string
     */
    protected function _toHtml()
    {
        
    	//$blockids = $this->getData('block_ids');
		$blockids = $this->getData('block_ids');
    	$tabs = array();
    	if (!empty($blockids))
    	{
    		$blockids = explode(',', $blockids);
    		if (!empty($blockids)){
    			foreach ($blockids as $blockid){
    				$tabs[]= $this->getLayout()->createBlock('tal_tabs/tabblock')->setBlockId($blockid); 
    			}
    		}
    	} 
    	
        $this->assign('tabs',$tabs);
              
        return parent::_toHtml();
    }

    
}