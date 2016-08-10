<?php
class Tal_Tabs_Block_Tabblock extends  Mage_Core_Block_Template {

	protected function _toHtml(){
		return '';
	}
	
	public function getHtml()
    {
        $blockId = $this->getBlockId();
        $html = false;
        if ($blockId) {
            $block = Mage::getModel('cms/block')
                ->setStoreId(Mage::app()->getStore()->getId())
                ->load($blockId);
            if ($block->getIsActive()) {
                /* @var $helper Mage_Cms_Helper_Data */
                $helper = Mage::helper('cms');
                $processor = $helper->getBlockTemplateProcessor();
                $html['title'] = $block->getTitle(); 
                $html['content'] = $processor->filter($block->getContent());
            }
        }
        return $html;
    }
}