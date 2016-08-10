<?php
class Celebros_Salesperson_Block_TargetRule_Checkout_Cart_Crosssell extends Enterprise_TargetRule_Block_Checkout_Cart_Crosssell
{
	/**
     * Items quantity will be capped to this value
     *
     * @var int
     */
    protected $_maxItemCount = 4;
	
	protected $_items;
	protected $_itemCollection;
	
	/**
	 * Get crosssell items
	 *
	 * @return array
	 */
	public function getItemCollection()
	{
		if (!Mage::getStoreConfigFlag('salesperson/general_settings/search_enabled')
			|| !Mage::getStoreConfigFlag('salesperson/crosssell_settings/crosssell_enabled')) {
			
			return parent::getItemCollection();
		}
		
		if (is_null($this->_items)) {
			$lastAdded = null;
		
			//This code path covers the 2 cases - product page and shoping cart
			if($this->getProduct()!=null){
				$lastAdded = $this->getProduct()->getId();
			}
			else{
				$cartProductIds = $this->_getCartProductIds();
				$lastAdded = null;
				for($i=count($cartProductIds) -1; $i >=0 ; $i--){
					$id =  $cartProductIds[$i];
					$parentIds = Mage::getModel('catalog/product_type_configurable')->getParentIdsByChild($id);
					if(empty($parentIds)){
						$lastAdded = $id;
						break;
					}
				}
			}

			$crossSellIds = Mage::helper('salesperson')->getSalespersonCrossSellApi()->getRecommendationsIds($lastAdded);
			
			$this->_maxItemCount = Mage::getStoreConfig('salesperson/crosssell_settings/crosssell_limit');

			$this->_itemCollection = $this->_getCollection($crossSellIds);
		}
		
		$this->_items = $this->_itemCollection->getItems();
		
		return $this->_items;
	}

	/**
	 * Get crosssell products collection
	*/
	protected function _getCollection($ids)
	{
		$collection = Mage::getModel('catalog/product')
		->getCollection()
		->setStoreId(Mage::app()->getStore()->getId())
		->addStoreFilter()
		->addAttributeToFilter('entity_id', array('in' => $ids))
		->setPageSize($this->_maxItemCount);

		$this->_addProductAttributesAndPrices($collection);

		$numOfItems=count($collection); // dummy line. for some reason without this Magento crashes
		
		Mage::getSingleton('catalog/product_status')->addSaleableFilterToCollection($collection);
		Mage::getSingleton('catalog/product_visibility')->addVisibleInCatalogFilterToCollection($collection);
		Mage::getSingleton('cataloginventory/stock')->addInStockFilterToCollection($collection);

		return $collection;
	}

}
