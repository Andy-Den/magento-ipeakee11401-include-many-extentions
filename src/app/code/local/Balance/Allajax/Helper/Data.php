<?php
class Balance_Allajax_Helper_Data extends Mage_Core_Helper_Abstract
{

    const XML_SEARCH_ENABLED_PATH = 'allajax/settings/enabled';

     /**
      * This method that outputs the html content for the given layout
      * @param array $handle name of the layout handle to output
      * @return string html output
      */
     public function getHandleHtmlOutput($handle) {                        
        $layout = Mage::app()->getLayout();
        $update = $layout->getUpdate();
        //reset old previous updates
        $update->resetHandles();
        $update->resetUpdates();
        foreach(array_keys($layout->getAllBlocks()) as $name){
            if ($name != 'messages') {
                $layout->removeOutputBlock($name);
                $layout->unsetBlock($name);
            }   
        }
        $update->setCacheId(false);
        $update->load($handle);
        $layout->generateXml();
        $layout->generateBlocks();
        $output = $layout->getOutput();                
        return $output;
    }

    /**
     * This method takes input array of handles and returns the output of those handles
     * 
     * @param array $handles key-value pairs of html element id and the layout handle to output
     * @param array $result  an array holding the output blocks
     * @return array an array returned after appending the resultant output blocks
     */
    public function getHandleBlockResult($handles,$result = array()){
        
        foreach ( $handles as $name => $handle){            
            $result['blocks'][] = array('name' => $name,
                                        'html'=> $this->getHandleHtmlOutput($handle));
        }
        
        return $result;
    }
    
    /**
     * This method get messages from the session object and sets it to the 
     * message block for rendering 
     * 
     * @param Mage_Core_Model_Session $session
     */
    public function setMessages($session){     
        $layout = Mage::app()->getLayout();
        $layout->getMessagesBlock()->addMessages($session->getMessages(true));                
    }
    
    /**
     * Checks whether the request originated from checkout cart page
     * This is used where we need to decide whether to render cart page or not for
     * ajax requests
     * 
     * @return int
     */
    public function isRefererCartPage(){
        $referer = Mage::app()->getRequest()->getServer('HTTP_REFERER');
        if ($referer && preg_match('/(checkout\/cart)/', $referer )){
            return 1;
        }
        return 0;
    }
    
    /**
     * Used to add additional request paramters for the product url
     * 
     * @param type $block a Magento Block class
     * @param Mage_Catalog_Model_Product $product 
     * @return type
     */
    public function getSubmitUrl($block,$product){
        if ($this->isRefererCartPage()){
            return $block->getSubmitUrl($product,array('cart_handle'=>true));
        }
        return $block->getSubmitUrl($product);
    }
    
     /**
     * Checks whether the request originated from my wish list page
     * This is used where we need to decide whether to render cart page or not for
     * ajax requests
     * 
     * @return int
     */
    public function isRefererWishlistPage(){
        $referer = Mage::app()->getRequest()->getServer('HTTP_REFERER');
        if ($referer && preg_match('/(wishlist)/', $referer )){
            return 1;
        }
        return 0;
    }

    /**
     * Should we attempt to do a default search
     *
     * @return bool
     */
    public function isEnabled()
    {
        return (Mage::getStoreConfig(self::XML_SEARCH_ENABLED_PATH));
    }
}
	 