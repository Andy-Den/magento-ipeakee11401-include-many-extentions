<?php

class Balanceinternet_Shoppingdotcomfeed_Block_Adminhtml_Shoppingdotcomfeed_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

  public function __construct()
  {
      parent::__construct();
      $this->setId('shoppingdotcomfeed_tabs');
      $this->setDestElementId('edit_form');
      $this->setTitle(Mage::helper('shoppingdotcomfeed')->__('Feed Information'));
      
  }

  protected function _beforeToHtml()
  {
      $this->addTab('form_section', array(
          'label'     => Mage::helper('shoppingdotcomfeed')->__('Feed Information'),
          'title'     => Mage::helper('shoppingdotcomfeed')->__('Feed Information'),
          'content'   => $this->getLayout()->createBlock('shoppingdotcomfeed/adminhtml_shoppingdotcomfeed_edit_tab_form')->toHtml(),          
      ));
      return parent::_beforeToHtml();
  }
 

  
}