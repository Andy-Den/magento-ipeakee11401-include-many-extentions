<?php
/**
 * BelVG LLC.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://store.belvg.com/BelVG-LICENSE-COMMUNITY.txt
 *
 /***************************************
 *         MAGENTO EDITION USAGE NOTICE *
 *****************************************/
 /* This package designed for Magento COMMUNITY edition
 * BelVG does not guarantee correct work of this extension
 * on any other Magento edition except Magento COMMUNITY edition.
 * BelVG does not provide extension support in case of
 * incorrect edition usage.
 /***************************************
 *         DISCLAIMER   *
 *****************************************/
 /* Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future.
 *****************************************************
 * @category   Belvg
 * @package    Belvg_Twitterconnect
 * @copyright  Copyright (c) 2010 - 2011 BelVG LLC. (http://www.belvg.com)
 * @license    http://store.belvg.com/BelVG-LICENSE-COMMUNITY.txt
 */?>
<?php

class Belvg_Twitter_Block_Adminhtml_Twitter_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

  public function __construct()
  {
      parent::__construct();
      $this->setId('twitter_tabs');
      $this->setDestElementId('edit_form');
      $this->setTitle(Mage::helper('twitter')->__('Item Information'));
  }

  protected function _beforeToHtml()
  {
      $this->addTab('form_section', array(
          'label'     => Mage::helper('twitter')->__('Item Information'),
          'title'     => Mage::helper('twitter')->__('Item Information'),
          'content'   => $this->getLayout()->createBlock('twitter/adminhtml_twitter_edit_tab_form')->toHtml(),
      ));
      $this->addTab('settings_section', array(
          'label'     => Mage::helper('twitter')->__('Item Settings'),
          'title'     => Mage::helper('twitter')->__('Item InforSettingsmation'),
          'content'   => $this->getLayout()->createBlock('twitter/adminhtml_twitter_edit_tab_settings')->toHtml(),
      ));
     
      return parent::_beforeToHtml();
  }
}