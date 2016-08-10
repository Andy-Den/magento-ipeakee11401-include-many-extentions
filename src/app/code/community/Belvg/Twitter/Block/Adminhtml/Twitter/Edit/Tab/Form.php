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

class Belvg_Twitter_Block_Adminhtml_Twitter_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
  protected function _prepareForm()
  {
      $form = new Varien_Data_Form();
      $this->setForm($form);
      $fieldset = $form->addFieldset('twitter_form', array('legend'=>Mage::helper('twitter')->__('Item information')));
      $_store = 0;
      if ($this->getRequest()->getParam('store')) $_store = $this->getRequest()->getParam('store');
            $fieldset->addField('type', 'select', array(
                'label'    => Mage::helper('core')->__('Type'),
                'title'    => Mage::helper('core')->__('Type'),
                'name'     => 'type',
                'values'    => array(
		  array(
		      'value'     => 'faves',
		      'label'     => Mage::helper('twitter')->__('Favorites'),
		  ),

		  array(
		      'value'     => 'profile',
		      'label'     => Mage::helper('twitter')->__('Profile'),
		  )
                ),
                'required' => true,
            ));
            $fieldset->addField('user_name', 'text', array(
                'label'    => Mage::helper('core')->__('User Name'),
                'title'    => Mage::helper('core')->__('User Name'),
                'name'     => 'user_name',	
                'width'    => '50px',                
                'required' => true,
            ));
 	    $fieldset->addField('store', 'hidden', array(
                'label'    => Mage::helper('core')->__('Store'),
                'title'    => Mage::helper('core')->__('Store'),		
                'name'     => 'store',			
                'width'    => '50px',                                
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
                   array(
		      'value'     => 3,
		      'label'     => Mage::helper('twitter')->__('Content'),
		  ),
                  
                   array(
		      'value'     => 5,
		      'label'     => Mage::helper('twitter')->__('Footer'),
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
	
      $form->setFieldNameSuffix('design');       
      if ( Mage::getSingleton('adminhtml/session')->getTwitterData() )
      {	            
	  $_data = Mage::getSingleton('adminhtml/session')->getTwitterData();
	  $_data['store'] = $_store;
          $form->setValues($_data);
          Mage::getSingleton('adminhtml/session')->setTwitterData(null);
      } elseif ( Mage::registry('twitter_data') ) {
	  $_data = Mage::registry('twitter_data')->getData();
	  $_data['store'] = $_store;
          $form->setValues($_data);	  
      }
      return parent::_prepareForm();
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