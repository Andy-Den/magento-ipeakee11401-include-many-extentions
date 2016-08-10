<?php
class Criteo_OneTag_IndexController extends Mage_Core_Controller_Front_Action
{
    public function indexAction() {
        Mage::helper('Criteo_OneTag/Feed')->generateFeed();
    }
}
?>