<?php

class Vacspare_Tradegroup_Helper_Data extends Mage_Core_Helper_Abstract
{
	protected $_model ='customer/group';
        
        
        protected $_cartController = 'cart';
        
        
        protected $_onpageController = 'onepage';


        /**
	 *@get Check setting customer goup is anable or not
	 *@return boolean
	 */
	public function _isModuleActive(){
		$config = Mage::getStoreConfig('tradegroups/general/enabled');
		if($config !=0) return true;
		return false;
	}
	
	/**
	 *@get Model customer/group
	 *@return Mage_Customer_Model_Group
	 *
	 */
	public function getModel(){
		return Mage::getModel($this->_model);
	}
	/**
	 *@get current customer group
	 *@return customer group id
	 *
	 */
	public function getCustomerGroupId(){
		//Check if User is Logged In
		$session = Mage::getSingleton('customer/session');
		$login = $session->isLoggedIn(); 
		if($login){
			$groupId = $session->getCustomerGroupId(); 
		}else{
			$groupId = 0;
		}
		return $groupId;
	}
	
	/**
	 * @get  customer trade by group setting
	 * @return boolean
	 */
	public function _isCustomerGroupTrade(){
		$groupId = $this->getCustomerGroupId();
		
		$config = Mage::getStoreConfig('tradegroups/setting/trade');
		$config = explode(',', $config);
		$config = empty($config) ? array() : $config;
		
		return in_array($groupId, $config); 
	} 
}
