<?php
class AHT_Aproduct_IndexController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {
        $this->loadLayout();
		$this->_initLayoutMessages('customer/session');
		$this->_initLayoutMessages('catalog/session');
        $this->renderLayout();
    }
    public function featuredAction()
    {
        $this->loadLayout();
		$this->_initLayoutMessages('customer/session');
		$this->_initLayoutMessages('catalog/session');
        $this->renderLayout();
    }
    public function specialAction()
    {
        $this->loadLayout();
		$this->_initLayoutMessages('customer/session');
		$this->_initLayoutMessages('catalog/session');
        $this->renderLayout();
    }
    public function productallAction()
    {
        $this->loadLayout();
		$this->_initLayoutMessages('customer/session');
		$this->_initLayoutMessages('catalog/session');
        $this->renderLayout();
    }
    public function ordertrackingAction()
    {
        $this->loadLayout();
		$this->_initLayoutMessages('customer/session');
		$this->_initLayoutMessages('catalog/session');
        $this->renderLayout();
    }
    
}