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

class Belvg_Twitter_Block_Adminhtml_Twitter_Edit_Tab_Settings extends Mage_Adminhtml_Block_Widget_Form
{
  protected function _prepareForm()
  {
      $form = new Varien_Data_Form();
      $this->setForm($form);
      $fieldset = $form->addFieldset('twitter_form', array('legend'=>Mage::helper('twitter')->__('Item information')));
      $_store = 0;
      if ($this->getRequest()->getParam('store')) $_store = $this->getRequest()->getParam('store');
            
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
            $fieldset->addField('interval', 'text', array(
                'label'    => Mage::helper('core')->__('Interval'),
                'title'    => Mage::helper('core')->__('Interval'),
                'name'     => 'interval',
                'width'    => '50px',
                'required' => true,
            ));
            $fieldset->addField('timestamp', 'select', array(
                'label'    => Mage::helper('core')->__('Show timestamp'),
                'title'    => Mage::helper('core')->__('Show timestamp'),
                'name'     => 'timestamp',
                'values'    => array(
		  array(
		      'value'     => 'true',
		      'label'     => Mage::helper('twitter')->__('Yes'),
		  ),

		  array(
		      'value'     => 'false',
		      'label'     => Mage::helper('twitter')->__('No'),
		  )
                ),
                'required' => true,
            ));
            $fieldset->addField('avatars', 'select', array(
                'label'    => Mage::helper('core')->__('Show avatars'),
                'title'    => Mage::helper('core')->__('Show avatars'),
                'name'     => 'avatars',
                'values'    => array(
		  array(
		      'value'     => 'true',
		      'label'     => Mage::helper('twitter')->__('Yes'),
		  ),

		  array(
		      'value'     => 'false',
		      'label'     => Mage::helper('twitter')->__('No'),
		  )
                ),
                'required' => true,
            ));
             $fieldset->addField('hashtags', 'select', array(
                'label'    => Mage::helper('core')->__('Show hashtags'),
                'title'    => Mage::helper('core')->__('Show hashtags'),
                'name'     => 'hashtags',
                'values'    => array(
		  array(
		      'value'     => 'true',
		      'label'     => Mage::helper('twitter')->__('Yes'),
		  ),

		  array(
		      'value'     => 'false',
		      'label'     => Mage::helper('twitter')->__('No'),
		  )
                ),
                'required' => true,
            ));
              $fieldset->addField('scrollbar', 'select', array(
                'label'    => Mage::helper('core')->__('Show scrollbar'),
                'title'    => Mage::helper('core')->__('Show scrollbar'),
                'name'     => 'scrollbar',
                'values'    => array(
		  array(
		      'value'     => 'true',
		      'label'     => Mage::helper('twitter')->__('Yes'),
		  ),

		  array(
		      'value'     => 'false',
		      'label'     => Mage::helper('twitter')->__('No'),
		  )
                ),
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