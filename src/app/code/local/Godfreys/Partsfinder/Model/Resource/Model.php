<?php
class Godfreys_Partsfinder_Model_Resource_Model extends Mage_Core_Model_Resource_Db_Abstract
{
	protected function _construct()
	{
		$this->_init('partsfinder/model', 'model_id');
	}
	
}