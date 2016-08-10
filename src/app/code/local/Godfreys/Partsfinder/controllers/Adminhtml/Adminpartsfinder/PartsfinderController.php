<?php
require_once("Mage/Adminhtml/controllers/Catalog/ProductController.php");

class Godfreys_Partsfinder_Adminhtml_Adminpartsfinder_PartsfinderController extends Mage_Adminhtml_Catalog_ProductController
{
	public function indexAction()
	{
		$this->_initProduct();
		$this->loadLayout();
		$this->getLayout()->getBlock('catalog.product.edit.tab.parts');
		$this->renderLayout();
	}


	public function ajaxAction()
    {
		$brand_id = $this->getRequest()->getParam('brand');
		$model_id = $this->getRequest()->getParam('model');
		if (empty($brand_id)) {
			$result = array('error' => true, 'message' => "No brand id specified.");
				
			$this->getResponse()->setHeader('Content-type', 'application/json');
			$this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
			return;
				
		}

		if (empty($model_id)) {
			$result = Mage::getModel('partsfinder/model')->getModelsByBrandId($brand_id, false);

			$this->getResponse()->setHeader('Content-type', 'application/json');
			$this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
			return;
		}
		else{
			$model = Mage::getModel('partsfinder/model')->load($model_id);

			$modelName = $model->getModelName();
				
			$product = Mage::getModel('partsfinder/accessory')->loadProduct($brand_id, $modelName);

			$result = array('id'=> 0, 'product_name' => '', 'sku'=> '');
			if (!empty($product)) {
				$result = array('id'=> $product->getId(), 'product_name' => $product->getName(), 'sku' => $product->getSku());
			}
				
			$this->getResponse()->setHeader('Content-type', 'application/json');
			$this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
			return;
		}
	}

	public function saverelationAction()
	{
		$productId = $this->getRequest()->getParam('id');
		$params = $this->getRequest()->getParam('option', array());
		$accessory = Mage::getSingleton('partsfinder/accessory');
		try{
			foreach($params as $p)
			{
				if(!empty($p['delete'])) {
					$accessory->removeRelation($productId, $p['brand_id'], $p['model_id']);
				}
				else{
					$product = null;
					if (!empty($p['product_id']) && !empty($p['sku'])) {
						$product = new Varien_Object();
						$product->setId($p['product_id']);
						$product->setSku($p['sku']);
					}
					$accessory->addRelation($productId, $p['brand_id'], $p['model_id'], $product, $p['product_name']);
				}
			}

			$result = array('success'=>true, 'message'=> $this->__('Parts relation has been saved.'));
		}
		catch(Exception $e)
		{
			Mage::logException($e);
			$result = array('error'=>true, 'message' => $e->getMessage());
		}
		
		$this->getResponse()->setHeader('Content-type', 'application/json');
		$this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
	}
	
	public function customAction()
	{
		$this->_initProduct();
		$this->loadLayout();
		$this->getLayout()->getBlock('catalog.product.edit.tab.custom')
		->setProductsCustom($this->getRequest()->getPost('products_custom', null));
		$this->renderLayout();
	}
	
	public function customGridAction()
	{
		$this->_initProduct();
		$this->loadLayout();
		$this->getLayout()->getBlock('catalog.product.edit.tab.custom')
		->setProductsCustom($this->getRequest()->getPost('products_custom', null));
		$this->renderLayout();
	}
}