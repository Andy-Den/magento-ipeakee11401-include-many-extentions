<?php
require_once Mage::getModuleDir('controllers', 'Mage_Checkout') . DS . 'CartController.php';
class Balance_Allajax_Checkout_CartController extends Mage_Checkout_CartController
{
    /**
     * main cart update layout handle
     * 
     * @var string  
     */ 
    private $_cartUpdateHandle = 'CART_UPDATE_AJAX';
    

    /**
     *       
     * @param boolean $validate
     */
    protected function _initCart($validate = false)
    {
        $cart = $this->_getCart();
        if ($cart->getQuote()->getItemsCount()) {
            $cart->init();
            $cart->save();

            if ($validate && !$this->_getQuote()->validateMinimumAmount()) {
                $minimumAmount = Mage::app()->getLocale()->currency(Mage::app()->getStore()->getCurrentCurrencyCode())
                    ->toCurrency(Mage::getStoreConfig('sales/minimum_order/amount'));

                $warning = Mage::getStoreConfig('sales/minimum_order/description')
                    ? Mage::getStoreConfig('sales/minimum_order/description')
                    : Mage::helper('checkout')->__('Minimum order amount is %s', $minimumAmount);

                $cart->getCheckoutSession()->addNotice($warning);
            }
        }

        // Compose array of messages to add
        $messages = array();
        foreach ($cart->getQuote()->getMessages() as $message) {
            if ($message) {
                // Escape HTML entities in quote message to prevent XSS
                $message->setCode(Mage::helper('core')->escapeHtml($message->getCode()));
                $messages[] = $message;
            }
        }
        $cart->getCheckoutSession()->addUniqueMessages($messages);

        /**
         * if customer enteres shopping cart we should mark quote
         * as modified bc he can has checkout page in another window.
         */
        $this->_getSession()->setCartWasUpdated(true);       
    }

    /**
     * Delete shoping cart item action
     */
    public function deleteAction()
    {

        //if the extension isn't enabled for this store then just use the parent action
        if (!Mage::helper('allajax')->isEnabled()) {
            return parent::deleteAction();
        }

        $result = array();                               
        $handle = $this->getRequest()->getPost('handle',false);
        if($handle && !Mage::helper('allajax')->isRefererCartPage()){
            $handles = array($handle => 'CART_MINICART_AJAX');
        }
        else{
            $handles = array('allajax-cart' => $this->_cartUpdateHandle,
                              'mini-cart' => 'CART_MINICART_AJAX');
        }        
        $this->_deleteItem();
        $this->_initCart();
        Mage::helper('allajax')->setMessages($this->_getSession());
        $result = Mage::helper('allajax')->getHandleBlockResult($handles,$result);        
        $this->getResponse()->setBody(Zend_Json::encode($result));
    }
    
    
    protected function _deleteItem()    
    {            
        $id = (int) $this->getRequest()->getParam('id');
        if ($id) {
            try {                
                $this->_getCart()->removeItem($id)
                  ->save();
                $this->_getSession()->setCartWasUpdated(true);
            } catch (Exception $e) {                
                $this->_getSession()->addError($this->__('Cannot remove the item.'));
                Mage::logException($e);
            }
        }        
    }
    
    
    /**
     * Update shopping cart data action
     */
    public function updatePostAction()
    {


        //if the extension isn't enabled for this store then just use the parent action
        if (!Mage::helper('allajax')->isEnabled()) {
            return parent::updatePostAction();
        }

        $result = array();        
        $handles = array('allajax-cart' => $this->_cartUpdateHandle,
                          'mini-cart' => 'CART_MINICART_AJAX'  
            );
        
        $updateAction = (string)$this->getRequest()->getPost('update_cart_action');                
        switch ($updateAction) {
            case 'empty_cart':
                $this->_emptyShoppingCart();
                break;
            case 'update_qty':
                $this->_updateShoppingCart();
                break;
            default:
                $this->_updateShoppingCart();
        }
        $this->_initCart(true);
        $this->_getSession()->setCartWasUpdated(true);                
        Mage::helper('allajax')->setMessages($this->_getSession());
        $result = Mage::helper('allajax')
                    ->getHandleBlockResult($handles,$result);                
        $this->getResponse()->setBody(Zend_Json::encode($result));
    }

    
    /**
     * Initialize shipping information
     */
    public function estimatePostAction()
    {

        //if the extension isn't enabled for this store then just use the parent action
        if (!Mage::helper('allajax')->isEnabled()) {
            return parent::estimatePostAction();
        }

        $result = array();        
        $handles = array('allajax-cart-shipping-methods' =>   'CART_SHIPPING_METHODS_AJAX');
        
        $country    = (string) $this->getRequest()->getPost('country_id');
        $postcode   = (string) $this->getRequest()->getPost('estimate_postcode');
        $city       = (string) $this->getRequest()->getPost('estimate_city');
        $regionId   = (string) $this->getRequest()->getPost('region_id');
        $region     = (string) $this->getRequest()->getPost('region');

        $this->_getQuote()->getShippingAddress()
            ->setCountryId($country)
            ->setCity($city)
            ->setPostcode($postcode)
            ->setRegionId($regionId)
            ->setRegion($region)
            ->setCollectShippingRates(true);
        $this->_getQuote()->save(); 
        $this->_initCart();
        $result = Mage::helper('allajax')
                    ->getHandleBlockResult($handles,$result);                
        $this->getResponse()->setBody(Zend_Json::encode($result));
    }
    
    /**
     * update selected shipping option
     */
    public function estimateUpdatePostAction()
    {

        //if the extension isn't enabled for this store then just use the parent action
        if (!Mage::helper('allajax')->isEnabled()) {
            return parent::estimateUpdatePostAction();
        }
                
        $result = array();        
        $handles = array('allajax-cart-totals' => 'CART_TOTALS_AJAX');
       
        $code = (string) $this->getRequest()->getPost('estimate_method');
        if (!empty($code)) {
            $this->_getQuote()->getShippingAddress()->setShippingMethod($code)/*collectTotals()*/->save();
        }
        $this->_initCart();
        Mage::getSingleton('checkout/session')->resetCheckout();
        $result = Mage::helper('allajax')
                    ->getHandleBlockResult($handles,$result);                
        $this->getResponse()->setBody(Zend_Json::encode($result));
        
    }
    
    //discount coupon
     /**
     * Initialize coupon
     */
    public function couponPostAction()
    {

        //if the extension isn't enabled for this store then just use the parent action
        if (!Mage::helper('allajax')->isEnabled()) {
            return parent::couponPostAction();
        }

        $result = array();
         $handles = array('allajax-cart' => $this->_cartUpdateHandle);
        //$handles = array('allajax-cart-totals'=> 'CART_TOTALS_AJAX','allajax-cart-couponcode'=>'CART_COUPONCODE_AJAX');     
        /**
         * No reason continue with empty shopping cart
         */
        if (!$this->_getCart()->getQuote()->getItemsCount()) {
            $this->getResponse()->setBody(Zend_Json::encode($result));
            return;
        }

        $couponCode = (string) $this->getRequest()->getPost('coupon_code');
        if ($this->getRequest()->getParam('remove') == 1) {
            $couponCode = '';
        }
        $oldCouponCode = $this->_getQuote()->getCouponCode();

        if (!strlen($couponCode) && !strlen($oldCouponCode)) {
            $this->getResponse()->setBody(Zend_Json::encode($result));
            return;            
        }

        try {
            $this->_getQuote()->getShippingAddress()->setCollectShippingRates(true);
            $this->_getQuote()->setCouponCode(strlen($couponCode) ? $couponCode : '')
                ->collectTotals()
                ->save();

            if (strlen($couponCode)) {
                if ($couponCode == $this->_getQuote()->getCouponCode()) {
                    $this->_getSession()->addSuccess(
                        $this->__('Coupon code "%s" was applied.', Mage::helper('core')->htmlEscape($couponCode))
                    );
                }
                else {
                    $this->_getSession()->addError(
                        $this->__('Coupon code "%s" is not valid.', Mage::helper('core')->htmlEscape($couponCode))
                    );
                }
            } else {
                $this->_getSession()->addSuccess($this->__('Coupon code was canceled.'));
            }            
        } catch (Mage_Core_Exception $e) {
            $this->_getSession()->addError($e->getMessage());
        } catch (Exception $e) {
            $this->_getSession()->addError($this->__('Cannot apply the coupon code.'));
            Mage::logException($e);
        }
        
        Mage::helper('allajax')->setMessages($this->_getSession());
        $result = Mage::helper('allajax')
                    ->getHandleBlockResult($handles,$result);        
        $this->getResponse()->setBody(Zend_Json::encode($result));
        
    }
    
    
    /**
     * Add Gift Card to current quote
     *
     */
    public function addGiftcardAction()
    {

        //if the extension isn't enabled for this store then just use the parent action
        if (!Mage::helper('allajax')->isEnabled()) {
            return parent::addGiftcardAction();
        }

        $result = array();
         $handles = array('allajax-cart' => $this->_cartUpdateHandle);
        //$handles = array('allajax-cart-totals' => 'CART_TOTALS_AJAX');
                              
        $data = $this->getRequest()->getPost();
        if (isset($data['giftcard_code'])) {
            $code = $data['giftcard_code'];
            try {
                Mage::getModel('enterprise_giftcardaccount/giftcardaccount')
                    ->loadByCode($code)
                    ->addToCart();
                $this->_getQuote()->collectTotals()->save();
                Mage::getSingleton('checkout/session')->addSuccess(
                    $this->__('Gift Card "%s" was added.', Mage::helper('core')->htmlEscape($code))
                );
            } catch (Mage_Core_Exception $e) {
                Mage::dispatchEvent('enterprise_giftcardaccount_add', array('status' => 'fail', 'code' => $code));
                Mage::getSingleton('checkout/session')->addError(
                    $e->getMessage()
                );
            } catch (Exception $e) {
                Mage::getSingleton('checkout/session')->addException($e, $this->__('Cannot apply gift card.'));
            }
        }
        Mage::helper('allajax')->setMessages(Mage::getSingleton('checkout/session'));
        $result = Mage::helper('allajax')
                    ->getHandleBlockResult($handles,$result);        
        $this->getResponse()->setBody(Zend_Json::encode($result));
    }

            
    public function removeGiftcardAction()
    {

        //if the extension isn't enabled for this store then just use the parent action
        if (!Mage::helper('allajax')->isEnabled()) {
            return parent::removeGiftcardAction();
        }

        $result = array();
         $handles = array('allajax-cart' => $this->_cartUpdateHandle);
        //$handles = array('allajax-cart-totals' => 'CART_TOTALS_AJAX');
        
        if ($code = $this->getRequest()->getParam('code')) {
            try {
                Mage::getModel('enterprise_giftcardaccount/giftcardaccount')
                    ->loadByCode($code)
                    ->removeFromCart();
                Mage::getSingleton('checkout/session')->addSuccess(
                    $this->__('Gift Card "%s" was removed.', Mage::helper('core')->htmlEscape($code))
                );
            } catch (Mage_Core_Exception $e) {
                Mage::getSingleton('checkout/session')->addError(
                    $e->getMessage()
                );
            } catch (Exception $e) {
                Mage::getSingleton('checkout/session')->addException($e, $this->__('Cannot remove gift card.'));
            }            
        } 
         Mage::helper('allajax')->setMessages(Mage::getSingleton('checkout/session'));
        $result = Mage::helper('allajax')
                    ->getHandleBlockResult($handles,$result);        
        $this->getResponse()->setBody(Zend_Json::encode($result));
    }


   /**
     * Add product to shopping cart action
     */
    public function addAction() {

        //if the extension isn't enabled for this store then just use the parent action
        if (!Mage::helper('allajax')->isEnabled()) {
            return parent::addAction();
        }

        //mini-cart
        $handles = array( 'mini-cart' => 'CART_MINICART_AJAX');
        $referer = $this->_getRefererUrl();
        if (($referer && preg_match('/(checkout\/cart)/', $referer )) || $this->getRequest()->getParam('cart_handle',false)){
            $handles = array_merge($handles,array('allajax-cart' => $this->_cartUpdateHandle));
        }

        $cart = $this->_getCart();
        $params = $this->getRequest()->getParams();
        try {
            if (isset($params['qty'])) {
                $filter = new Zend_Filter_LocalizedToNormalized(
                                array('locale' => Mage::app()->getLocale()->getLocaleCode())
                );
                $params['qty'] = $filter->filter($params['qty']);
            }

            $product = $this->_initProduct();
            $related = $this->getRequest()->getParam('related_product');

            /**
             * Check product availability
             */
            if (!$product) {
                $this->_goBack();
                return;
            }

            $cart->addProduct($product, $params);
            if (!empty($related)) {
                $cart->addProductsByIds(explode(',', $related));
            }

            $cart->save();

            $this->_getSession()->setCartWasUpdated(true);

            /**
             * @todo remove wishlist observer processAddToCart
             */
            $this->getLayout()->getUpdate()->addHandle('ajaxcart');
            $this->loadLayout();

            Mage::dispatchEvent('controller_action_postdispatch_checkout_cart_add', array());
            
            Mage::dispatchEvent('checkout_cart_add_product_complete', array('product' => $product, 'request' => $this->getRequest(), 'response' => $this->getResponse(),'handles'=> $handles)
            );

            if (!$this->_getSession()->getNoCartRedirect(true)) {
                if (!$cart->getQuote()->getHasError()) {
                    $message = $this->__('%s was added to your shopping cart.', Mage::helper('core')->escapeHtml($product->getName()));
                    $this->_getSession()->addSuccess($message);
                }
                $this->_goBack();
            }
        } catch (Mage_Core_Exception $e) {
            $_response = Mage::getModel('allajax/response');
            $_response->setError(true);

            $messages = array_unique(explode("\n", $e->getMessage()));
            $json_messages = array();
            foreach ($messages as $message) {
                $json_messages[] = Mage::helper('core')->escapeHtml($message);
            }

            $_response->setMessages($json_messages);
            $_response->setMessage($e->getMessage());
            $url = $this->_getSession()->getRedirectUrl(true);

            $_response->send();
        } catch (Exception $e) {
            $this->_getSession()->addException($e, $this->__('Cannot add the item to shopping cart.'));
            Mage::logException($e);

            $_response = Mage::getModel('allajax/response');
            $_response->setError(true);
            $_response->setMessage($this->__('Cannot add the item to shopping cart.'));
            $_response->send();
        }

        $this->_clearMessages();
    }

    /**
     * Update product configuration for a cart item
     */
    public function updateItemOptionsAction() {

        //if the extension isn't enabled for this store then just use the parent action
        if (!Mage::helper('allajax')->isEnabled()) {
            return parent::updateItemOptionsAction();
        }

        $handles = array( 'mini-cart' => 'CART_MINICART_AJAX');
        $cart = $this->_getCart();
        $id = (int) $this->getRequest()->getParam('id');
        $params = $this->getRequest()->getParams();

        if (!isset($params['options'])) {
            $params['options'] = array();
        }
        try {
            if (isset($params['qty'])) {
                $filter = new Zend_Filter_LocalizedToNormalized(
                                array('locale' => Mage::app()->getLocale()->getLocaleCode())
                );
                $params['qty'] = $filter->filter($params['qty']);
            }

            $quoteItem = $cart->getQuote()->getItemById($id);
            if (!$quoteItem) {
                Mage::throwException($this->__('Quote item is not found.'));
            }

            $item = $cart->updateItem($id, new Varien_Object($params));
            if (is_string($item)) {
                Mage::throwException($item);
            }
            if ($item->getHasError()) {
                Mage::throwException($item->getMessage());
            }

            $related = $this->getRequest()->getParam('related_product');
            if (!empty($related)) {
                $cart->addProductsByIds(explode(',', $related));
            }

            $cart->save();

            $this->_getSession()->setCartWasUpdated(true);

            $this->getLayout()->getUpdate()->addHandle('ajaxcart');
            $this->loadLayout();

            Mage::dispatchEvent('checkout_cart_update_item_complete', array('item' => $item, 'request' => $this->getRequest(), 'response' => $this->getResponse(),'handles'=> $handles)
            );
            if (!$this->_getSession()->getNoCartRedirect(true)) {
                if (!$cart->getQuote()->getHasError()) {
                    $message = $this->__('%s was updated in your shopping cart.', Mage::helper('core')->htmlEscape($item->getProduct()->getName()));
                    $this->_getSession()->addSuccess($message);
                }
                $this->_goBack();
            }
        } catch (Mage_Core_Exception $e) {
            $_response = Mage::getModel('allajax/response');
            $_response->setError(true);

            $messages = array_unique(explode("\n", $e->getMessage()));
            $json_messages = array();
            foreach ($messages as $message) {
                $json_messages[] = Mage::helper('core')->escapeHtml($message);
            }

            $_response->setMessages($json_messages);
            $_response->setMessage($e->getMessage());
            $url = $this->_getSession()->getRedirectUrl(true);

            $_response->send();
        } catch (Exception $e) {
            $this->_getSession()->addException($e, $this->__('Cannot update the item.'));
            Mage::logException($e);

            $_response = Mage::getModel('allajax/response');
            $_response->setError(true);
            $_response->setMessage($this->__('Cannot update the item.'));
            $_response->send();
        }
        
        $this->_clearMessages();
    }
    
    protected function _clearMessages(){
        $this->_getSession()->getMessages(true);
    }
}