<?php

class Balanceinternet_Shoppingdotcomfeed_IndexController extends Mage_Core_Controller_Front_Action {

    public function indexAction() {
        $id = $this->getRequest()->getParam('id');
        Mage::register('id', $id);


        $this->loadLayout();
        $this->renderLayout();
    }

}