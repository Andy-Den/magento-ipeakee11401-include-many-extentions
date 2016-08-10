<?php
class Balance_Datafeed_Block_Adminhtml_Datafeed_Renderer_Action extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Action
{
	public function render(Varien_Object $row)
	{
		$this->getColumn()->setActions(
			array(
				array(
					'url'     => $this->getUrl('*/datafeed_datafeed/edit', array('id' => $row->getFeed_id())),
					'caption' => Mage::helper('datafeed')->__('Edit'),
				),
				array(
					'url'     => $this->getUrl('*/datafeed_datafeed/delete', array('id' => $row->getFeed_id())),
					'confirm'   =>  Mage::helper('datafeed')->__('Are you sure you want to delete this feed ?'),
					'caption' => Mage::helper('datafeed')->__('Delete'),
				),
				array(
					'url'     => $this->getUrl('*/datafeed_datafeed/sample', array('feed_id' => $row->getFeed_id(), 'limit'=>10)),
					'caption' => Mage::helper('datafeed')->__('Preview'). " (10 ".Mage::helper('datafeed')->__('products').")" ,
					'popup'     =>  true
				),
				array(
					'url'     => $this->getUrl('*/datafeed_datafeed/generate', array('feed_id' => $row->getFeed_id())),
					'confirm'   =>  Mage::helper('datafeed')->__('Generate a data feed can take a while. Are you sure you want to generate it now ?'),
					'caption' => Mage::helper('datafeed')->__('Generate'),
				),
			)
		);
		return parent::render($row);
	}
}
