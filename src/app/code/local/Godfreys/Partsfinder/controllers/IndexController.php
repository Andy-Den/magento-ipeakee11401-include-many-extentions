<?php
class Godfreys_Partsfinder_IndexController extends Mage_Core_Controller_Front_Action
{
	protected $_productCollection = null;
	
	protected function _getSession() {
		return Mage::getSingleton('core/session');
	}
	
	protected function _goBack()
	{
		$returnUrl = $this->getRequest()->getParam('return_url');
		if ($returnUrl) {
			// clear layout messages in case of external url redirect
			if ($this->_isUrlInternal($returnUrl)) {
				$this->_getSession()->getMessages(true);
			}
			$this->getResponse()->setRedirect($returnUrl);
		} elseif ($backUrl = $this->_getRefererUrl()) {
			$this->getResponse()->setRedirect($backUrl);
		} else {
			$this->_redirect('vacuum-cleaner-parts/accessories');
		}
		return $this;
	}
	
	protected function getProductCollection() {
		$storeId = Mage::app()->getStore()->getStoreId();
		
		$brandId = $this->getRequest()->getParam('filter_brand');
		$modelId = $this->getRequest()->getParam('filter_model');
		//$categoryId = $this->getRequest()->getParam('filter_cate');
		$categoryId = null;
		
		if (empty($brandId)) {
			Mage::log('empty brandid');
			return false;
		}
		
		$category = empty($categoryId) ? null : Mage::getModel('catalog/category')->load($categoryId);

		$accessories = Mage::getModel('partsfinder/accessory')->loadAccessory(array('filter_brand' => $brandId, 'filter_model'=> $modelId));

		if (is_null($this->_productCollection)) {

			$this->_productCollection = Mage::getModel('catalog/product')->setStoreId($storeId)->getResourceCollection()
			->addAttributeToSelect(Mage::getSingleton('catalog/config')->getProductAttributes())
			->addIdFilter($accessories)
			->addAttributeToSort($this->getCurrentOrder(), $this->getCurrentDirection())
//			->setPageSize($this->getProductsLimit())
			->setCurPage($this->getCurrentPage())
			;
			if (!empty($category) && $category->getId() > 0) {
				$this->_productCollection->addCategoryFilter($category);
			}
		}
		
		return $this->_productCollection;
	}
	
	public function getProductsLimit() {
		return 999;
	}
	
	public function getCurrentOrder() {
		return $this->getRequest()->getParam('order', 'price');
	}
	
	public function getCurrentDirection() {
		return $this->getRequest()->getParam('dir', 'asc');
	}
	
	public function getCurrentPage() {
		return $this->getRequest()->getParam('p', 1);
	}
	
	public function addAction() {
		$brandId = $this->getRequest()->getParam('filter_brand');
		$modelId = $this->getRequest()->getParam('filter_model');
		
		if (empty($brandId)) {
			$message = 'Brand not specified. Please select one brand';
			Mage::getSingleton('core/session')->addError($message);
			$this->_goBack();
			return;
		}
		
		Mage::app()->getCookie()->set('brand_id', $brandId);
		Mage::app()->getCookie()->set('model_id', $modelId);
		
		$this->_goBack();
	}
	
	public function indexAction() {
		$brandId = $this->getRequest()->getParam('filter_brand');
		$modelId = $this->getRequest()->getParam('filter_model');
		
		if (empty($brandId)) {
Mage::log('empty here');			
			$message = 'Brand not specified. Please select one brand';
			Mage::getSingleton('core/session')->addError($message);
			$this->_goBack();
			return;
		}
		
		//Mage::app()->getCookie()->set('brand_id', $brandId);
		//Mage::app()->getCookie()->set('model_id', $modelId);
		
		$this->loadLayout();

		if ($this->getProductCollection() ) {
			
			$this->getLayout()->getBlock('parts.list')
				->setCollection($this->_productCollection);
		
			$this->getLayout()->getBlock('toolbar')->setCollection($this->_productCollection);
		
			$this->getLayout()->getBlock('product_list_toolbar_pager')->setCollection($this->_productCollection);
		}
		
		$this->renderLayout();
	}
	
}