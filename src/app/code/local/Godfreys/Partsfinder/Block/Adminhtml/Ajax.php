<?php
class Godfreys_Partsfinder_Block_Adminhtml_Ajax extends Mage_Adminhtml_Block_Widget
{
	public function __construct()
	{
		parent::__construct();
		$this->setTemplate('partsfinder/ajax.phtml');
	}
	
}