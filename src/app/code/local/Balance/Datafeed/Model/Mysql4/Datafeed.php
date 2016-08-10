<?php
class Balance_Datafeed_Model_Mysql4_Datafeed extends Mage_Core_Model_Mysql4_Abstract
{
	public function _construct()
	{
		// Note that the datafeed_id refers to the key field in your database table.
		$this->_init('datafeed/datafeed', 'feed_id');
	}
}
