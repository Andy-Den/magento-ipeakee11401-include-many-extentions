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

class Belvg_Twitter_Block_Adminhtml_Settings_Tab_General extends Mage_Adminhtml_Block_Widget_Form
{
  protected function _prepareForm()
  {
      $form		= new Varien_Data_Form();
        $news_id	= $this->getRequest()->getParam('id');
        $fieldset	= $form->addFieldset('general', array('legend'=>Mage::helper('core')->__('General Settings')));
        $wysiwygConfig	= Mage::getSingleton('cms/wysiwyg_config')->getConfig(
            array('tab_id' => 'page_tabs')
        );
       
            $fieldset->addField('user_name', 'text', array(
                'label'    => Mage::helper('core')->__('User Name'),
                'title'    => Mage::helper('core')->__('User Name'),
                'name'     => 'user_name',	
                'width'    => '50px',                
                'required' => true,
            ));
	    $fieldset->addField('title', 'text', array(
                'label'    => Mage::helper('core')->__('Title'),
                'title'    => Mage::helper('core')->__('Title'),
                'name'     => 'title',		
                'width'    => '50px',                
                'required' => true,
            ));
	    $fieldset->addField('subject', 'text', array(
                'label'    => Mage::helper('core')->__('Subject'),
                'title'    => Mage::helper('core')->__('Subject'),
                'name'     => 'subject',		
                'width'    => '50px',                
                'required' => true,
            ));
	    $fieldset->addField('width', 'text', array(
                'label'    => Mage::helper('core')->__('Width'),
                'title'    => Mage::helper('core')->__('Width'),
                'name'     => 'width',		
                'width'    => '50px',                
                'required' => true,
            ));
	    $fieldset->addField('height', 'text', array(
                'label'    => Mage::helper('core')->__('Height'),
                'title'    => Mage::helper('core')->__('Height'),
                'name'     => 'height',		
                'width'    => '50px',                
                'required' => true,
            ));
	     $fieldset->addField('shell_bg', 'text', array(
                'label'    => Mage::helper('core')->__('Shell Background'),
                'title'    => Mage::helper('core')->__('Shell Background'),
                'name'     => 'shell_bg',		
                'width'    => '50px',                
                'required' => true,
            ));
	     $fieldset->addField('shell_color', 'text', array(
                'label'    => Mage::helper('core')->__('Shell Color'),
                'title'    => Mage::helper('core')->__('Shell Color'),
                'name'     => 'shell_color',		
                'width'    => '50px',                
                'required' => true,
            ));
	     $fieldset->addField('tweets_bg', 'text', array(
                'label'    => Mage::helper('core')->__('Tweets Background'),
                'title'    => Mage::helper('core')->__('Tweets Background'),
                'name'     => 'tweets_bg',		
                'width'    => '50px',                
                'required' => true,
            ));
	     $fieldset->addField('tweets_color', 'text', array(
                'label'    => Mage::helper('core')->__('Tweets Color'),
                'title'    => Mage::helper('core')->__('Tweets Color'),
                'name'     => 'tweets_color',		
                'width'    => '50px',                
                'required' => true,
            ));
	    $fieldset->addField('tweets_link', 'text', array(
                'label'    => Mage::helper('core')->__('Tweets Link'),
                'title'    => Mage::helper('core')->__('Tweets Link'),
                'name'     => 'tweets_link',		
                'width'    => '50px',                
                'required' => true,
            ));

	   $fieldset->addField('pages', 'multiselect', array(
		  'name'      => 'pages[]',
		  'label'     => Mage::helper('core')->__('Pages'),
		  'title'     => Mage::helper('core')->__('Pages'),
		  'required'  => true,
		  'values'    => $this->getCmsPages()
	   ));
	    $fieldset->addField('position', 'select', array(
	      'label'     => Mage::helper('twitter')->__('Position'),
	      'name'      => 'position',
	      'values'    => array(
		  array(
		      'value'     => 0,
		      'label'     => Mage::helper('twitter')->__('None'),
		  ),

		  array(
		      'value'     => 1,
		      'label'     => Mage::helper('twitter')->__('Left'),
		  ),

		  array(
		      'value'     => 2,
		      'label'     => Mage::helper('twitter')->__('Right'),
		  ),
	      ),
	  ));
	   $fieldset->addField('status', 'select', array(
	      'label'     => Mage::helper('twitter')->__('Status'),
	      'name'      => 'status',
	      'values'    => array(
		  array(
		      'value'     => 0,
		      'label'     => Mage::helper('twitter')->__('Enabled'),
		  ),

		  array(
		      'value'     => 1,
		      'label'     => Mage::helper('twitter')->__('Disabled'),
		  )		
	      ),
	  ));
                   
	$formData = Mage::getModel('twitter/twitter')->getSettings();	
        $form->addValues($formData);
        $form->setFieldNameSuffix('design');
        $this->setForm($form);
  }

  protected function getCmsPages(){
	$pages = array();
	$collection = Mage::getModel('cms/page')->getCollection();
	foreach ($collection as $_page){	    
	    $pages[] = array('value'=>$_page->getId(),'label'=>$_page->getTitle());
	}
	$pages[] = array('value'=>'777','label'=> 'Categories');
	$pages[] = array('value'=>'555','label'=> 'Products');
	return $pages;
  }
}