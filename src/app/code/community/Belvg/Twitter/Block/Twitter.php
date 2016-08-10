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
class Belvg_Twitter_Block_Twitter extends Mage_Core_Block_Template
{
	public function _prepareLayout()
    {
		return parent::_prepareLayout();
    }
    
     public function getTwitter()     
     { 
        if (!$this->hasData('twitter')) {
            $this->setData('twitter', Mage::registry('twitter'));
        }
        return $this->getData('twitter');
        
    }
    
    public function getSettings(){
	$result = array();
	$page_id = $this->checkType(Mage::app()->getRequest());	
	$_data = Mage::getModel('twitter/twitter')->getSettings();	
	
	foreach ($_data as $dt){
	    if (in_array($page_id, $dt['pages']) and $this->isEnabled($dt) and $this->inStore($dt))
	      $result[] = $dt;	     
	}
	return $result;
	
    }
    protected function checkType($request){
	$type = 1;
	if ($request->getModuleName() == 'catalog'){	
	    switch ($request->getControllerName()){
	      case 'category': $type = 777; break;
	      case 'product': $type = 555; break;
	      default: break;
	    }
	}elseif($request->getModuleName() == 'cms'){
	   
	    if ($request->getParams('page_id'))
		$type = $request->getParam('page_id');	    
	    if ($request->getControllerName() == 'index' && $request->getActionName() == 'index')
		$type = 2;
	}
	return $type;
    }
    protected function isEnabled($data){
	if ($data['status'] == 0)	
	    return true;
	else
	    return false;	
    }

    protected function inStore($data){
	if ($data['store'] == Mage::app()->getStore()->getId() or $data['store'] == 0) return true;
	return false;
    }
}