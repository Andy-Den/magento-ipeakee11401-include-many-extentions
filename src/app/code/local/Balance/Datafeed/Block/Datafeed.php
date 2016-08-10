<?php
class Balance_Datafeed_Block_Datafeed extends Mage_Core_Block_Template
{
	public function _prepareLayout()
	{
		return parent::_prepareLayout();
	}

	public function getDatafeed()
	{
		if (!$this->hasData('datafeed')) {
			$this->setData('datafeed', Mage::registry('datafeed'));
		}
		return $this->getData('datafeed');

	}
}