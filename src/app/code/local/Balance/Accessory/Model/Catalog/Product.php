<?php

/**
 * Catalog product model overwrite for Balance Accessory support
 *
 * @method Mage_Catalog_Model_Resource_Product getResource()
 * @method Mage_Catalog_Model_Resource_Product _getResource()
 *
 * @category   Balance
 * @package    Balance_Accessory
 * @author     Carey Sizer <carey@balanceinternet.com.au>
 */
class Balance_Accessory_Model_Catalog_Product extends Mage_Catalog_Model_Product
{
    
    public function _construct()
    {
        parent::_construct();
    }
    
    /**
     * Retrieve array of accessories
     *
     * @return array
     */
    public function getAccessoryProducts()
    {
        if (!$this->hasAccessoryProducts()) {
            $products = array();
            $collection = $this->getAccessoryProductCollection();
            foreach ($collection as $product) {
                $products[] = $product;
            }
            $this->setAccessoryProducts($products);
        }
        return $this->getData('accessory_products');
    }

    /**
     * Retrieve accessory identifiers
     *
     * @return array
     */
    public function getAccessoryProductIds()
    {
        if (!$this->hasAccessoryProductIds()) {
            $ids = array();
            foreach ($this->getAccessoryProducts() as $product) {
                $ids[] = $product->getId();
            }
            $this->setAccessoryProductIds($ids);
        }
        return $this->getData('accessory_product_ids');
    }
    
    /**
     * Retrieve collection related product
     */
    public function getAccessoryProductCollection()
    {
        $collection = $this->getLinkInstance()->useAccessoryLinks()
            ->getProductCollection()
            ->setIsStrongMode();
        $collection->setProduct($this);
        return $collection;
    }
    
    /**
     * Retrieve collection related link
     */
    public function getAccessoryLinkCollection()
    {
        $collection = $this->getLinkInstance()->useAccessoryLinks()
            ->getLinkCollection();
        $collection->setProduct($this);
        $collection->addLinkTypeIdFilter();
        $collection->addProductIdFilter();
        $collection->joinAttributes();
        return $collection;
    }
    
    
    /**
     * Create duplicate
     *
     * @return Mage_Catalog_Model_Product
     */
    public function duplicate()
    {
        $newProduct = parent::duplicate();
        
        /* Prepare Accessories*/
        $data = array();
        $this->getLinkInstance()->useAccessoryLinks();
        $attributes = array();
        foreach ($this->getLinkInstance()->getAttributes() as $_attribute) {
            if (isset($_attribute['code'])) {
                $attributes[]=$_attribute['code'];
            }
        }
        foreach ($this->getAccessoryLinkCollection() as $_link) {
            $data[$_link->getLinkedProductId()] = $_link->toArray($attributes);
        }
        $newProduct->setAccessoryLinkData($data);

        $newProduct->save();

        $this->getOptionInstance()->duplicate($this->getId(), $newProduct->getId());
        $this->getResource()->duplicate($this->getId(), $newProduct->getId());

        return $newProduct;
    }
    
}