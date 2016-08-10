<?php
class Godfreys_Partsfinder_Block_Form extends Mage_Core_Block_Template
{
	public function  __construct()
	{
		parent::__construct();
		$this->filter_brand =  $this->getRequest()->getParam('filter_brand');
		$this->filter_model = $this->getRequest()->getParam('filter_model');
	}
	
}