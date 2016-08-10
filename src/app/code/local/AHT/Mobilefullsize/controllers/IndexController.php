<?php

class AHT_Mobilefullsize_IndexController extends Mage_Core_Controller_Front_Action {

    public function indexAction() {
        if($this->getRequest()->getParam('enabled',0)==1){
            Mage::getSingleton('core/session')->setMobileFullSize(true);
        }else{
            Mage::getSingleton('core/session')->setMobileFullSize(false);
        }
        $this->_forward('index', 'index', 'cms');
        $this->_redirectReferer();
    }
}