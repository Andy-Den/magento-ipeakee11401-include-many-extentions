<?php
class Celebros_Salesperson_Block_TargetRule_Catalog_Product_List_Upsell extends Enterprise_TargetRule_Block_Catalog_Product_List_Upsell
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
			|| !Mage::getStoreConfigFlag('salesperson/crosssell_settings/upsell_enabled')) {
			
			return parent::getItemCollection();
		}
		
		$items = $this->_items;
		if (is_null($items)) {
		
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

			$this->_maxItemCount = Mage::getStoreConfig('salesperson/crosssell_settings/upsell_limit');
			
			$this->_itemCollection = $this->_getCollection($crossSellIds);
		}

		$this->_items = $this->_itemCollection->getItems();
		return $this->_itemCollection;
	}
	
	public function hasItems()
	{
		return (count($this->getItemCollection()));
	}

	/**
	 * Get crosssell products collection
	 */
	protected function _getCollection($ids)
	{
		$collection = Mage::getModel('catalog/product')
		->getCollection()
		->addAttributeToFilter('entity_id', array('in' => $ids))
		->setStoreId(Mage::app()->getStore()->getId())
		->addStoreFilter()
		->setPageSize($this->_maxItemCount);
		
		$this->_addProductAttributesAndPrices($collection);
	
		$numOfItems=count($collection); // dummy line. for some reason without this Magento crashes

		Mage::getSingleton('catalog/product_status')->addSaleableFilterToCollection($collection);
		Mage::getSingleton('catalog/product_visibility')->addVisibleInCatalogFilterToCollection($collection);
		Mage::getSingleton('cataloginventory/stock')->addInStockFilterToCollection($collection);
		
		return $collection;
	}
}