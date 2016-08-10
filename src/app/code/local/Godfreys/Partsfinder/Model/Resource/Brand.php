<?php
class Godfreys_Partsfinder_Model_Resource_Brand extends Mage_Core_Model_Resource_Db_Abstract
{
	protected function _construct()
	{
		$this->_init('partsfinder/brand', 'brand_id');
	}

}