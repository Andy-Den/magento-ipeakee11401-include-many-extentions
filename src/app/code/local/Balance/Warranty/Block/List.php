<?php
/**
 * The list of products under the customer's account menu
 * @category Balance
 * @package Balance_Warranty
 * @author Carey Sizer <carey@balanceinternet.com.au>
 */
class Balance_Warranty_Block_List extends Balance_Warranty_Block_Registration_Abstract
{
    /**
     * Get the list of warranties for the customer
     * @return Varien_Data_Collection 
     */
    public function getWarranties()
    {
        return Mage::getModel('warranty/warranty')->getCollection()
                ->addFieldToFilter('customer_id', array(
                    'eq' => 
                    $this->_getSession()->getCustomerId()
                ));
    }
    
    /**
     * Retrieve customer session model object
     *
     * @return Mage_Customer_Model_Session
     */
    protected function _getSession()
    {
        return Mage::getSingleton('customer/session');
    }
    
    /**
     * Get the image for a warranty
     * @param Balance_Warranty_Model_Warranty $warranty 
     */
    public function getWarrantyImage(Balance_Warranty_Model_Warranty $warranty)
    {
            $collection = Mage::getModel('catalog/product')
                ->getCollection()
                ->addAttributeToFilter('model', array('eq' => $warranty->getModel()));
        
        
        $chosenProduct = null;
        foreach($collection as $product){
            if($product->getSmallImageUrl()){
                $chosenProduct = $product;
                break; // just take the first one that's got an image
                // its text-based attribute so I don't trust it
            }
        }
        if(is_null($chosenProduct)){
            return $this->getSkinUrl(Mage::helper('catalog/image')
                    ->init(Mage::getModel('catalog/product'),'small_image')
                    ->getPlaceholder());
        }
        return $chosenProduct->getSmallImageUrl();
    }
}