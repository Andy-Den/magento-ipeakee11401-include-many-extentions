<?php
class Balance_Datafeed_Model_Mysql4_Datafeed_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
	public function _construct()
	{
		parent::_construct();
		$this->_init('datafeed/datafeed');
	}
}
