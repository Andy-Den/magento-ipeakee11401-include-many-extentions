<?php

class Balance_Allajax_WishlistController extends Mage_Wishlist_Controller_Abstract {
    
    /**
     * redirect if not ajax
     * 
     * @return \Balance_Allajax_WishlistController
     */
    protected function _ajaxRedirectResponse()
    {
        $this->getResponse()
            ->setHeader('HTTP/1.1', '403 Session Expired')            
            ->sendResponse();
        die;        
    }
    
    /*public function preDispatch()
    {
        
        parent::preDispatch();
        
        if ($this->getRequest()->isXmlHttpRequest()) {
             if (!Mage::getStoreConfigFlag('wishlist/general/active')) {
                 $this->_ajaxRedirectResponse();                 
             }
             return ;
        }
        
        if (!Mage::getSingleton('customer/session')->authenticate($this)) {
            $this->setFlag('', 'no-dispatch', true);
            if (!Mage::getSingleton('customer/session')->getBeforeWishlistUrl()) {
                Mage::getSingleton('customer/session')->setBeforeWishlistUrl($this->_getRefererUrl());
            }
            Mage::getSingleton('customer/session')->setBeforeWishlistRequest($this->getRequest()->getParams());
        }
        
        if (!Mage::getStoreConfigFlag('wishlist/general/active')) {
            $this->norouteAction();
            return;
        }
    }*/
	//Get Wishlist Data
    protected function _getWishlist($wishlistId=null) {
        $wishlist = Mage::registry('wishlist');
        if ($wishlist) {
            return $wishlist;
        }

        try {
            if (!$wishlistId) {
                $wishlistId = $this->getRequest()->getParam('wishlist_id');
            }
            $customerId = Mage::getSingleton('customer/session')->getCustomerId();
            $wishlist = Mage::getModel('wishlist/wishlist');
            if ($wishlistId) {
                $wishlist->load($wishlistId);
            } else {
                $wishlist->loadByCustomer($customerId, true);
            }
            Mage::register('wishlist', $wishlist);
        } catch (Mage_Core_Exception $e) {
            Mage::getSingleton('wishlist/session')->addError($e->getMessage());
        } catch (Exception $e) {
            Mage::getSingleton('wishlist/session')->addException($e, Mage::helper('wishlist')->__('Cannot create wishlist.')
            );
            return false;
        }

        return $wishlist;
    }
	// Add Item to the wishlist
    public function addAction() {
       
        $_response = Mage::getModel('allajax/response');

        if (!Mage::getStoreConfigFlag('wishlist/general/active')) {
            $_response->setError(true);
            $_response->setMessage($this->__('Wishlist Has Been Disabled By Admin.'));
        }
        if (!Mage::getSingleton('customer/session')->isLoggedIn()) {
            $_response->setError(true);
            $_response->setMessage($this->__('Please Login First.'));
        } else {

            if (empty($response)) {
   				$name = $this->getRequest()->getParam('name');
                $session = Mage::getSingleton('customer/session');
                                
                if ($name !== null) {
                    $wishlistid = $this->_createWishlist($name);
                    $wishlist = $this->_getWishlist($wishlistid);
                }else{
                    $wishlist = $this->_getWishlist();   
                }
            
                if (!$wishlist) {
                    $_response->setError(true);
                    $_response->setMessage($this->__('Unable to Create Wishlist.'));
                } else {

                    $productId = (int) $this->getRequest()->getParam('product');
                    if (!$productId) {
                        $_response->setError(true);
                        $_response->setMessage($this->__('Product Not Found.'));
                    } else {

                        $product = Mage::getModel('catalog/product')->load($productId);
                        if (!$product->getId() || !$product->isVisibleInCatalog()) {
                            $_response->setError(true);
                            $_response->setMessage($this->__('Cannot specify product.'));
                        } else {

                            try {
                                $requestParams = $this->getRequest()->getParams();
                                $buyRequest = new Varien_Object($requestParams);

                                $result = $wishlist->addNewItem($product, $buyRequest);
                                if (is_string($result)) {
                                    Mage::throwException($result);
                                }
                                $wishlist->save();


            
                                Mage::dispatchEvent(
                                    'wishlist_add_product', array(
                                    'wishlist' => $wishlist,
                                    'product' => $product,
                                    'item' => $result
                                        )
                                );
                                
                                

                                Mage::helper('wishlist')->calculate();

                                $message = $this->__('%1$s has been added to your wishlist.', $product->getName(), '');

                                $message = $this->__($message);
                                $_response->setMessage($message);
                                $this->_getSession()->addSuccess($message);
                                //$toplink = $this->getLayout()->getBlock('top.links')->toHtml();    
                                Mage::unregister('wishlist');
                                
                                $this->getLayout()->getUpdate()->addHandle('ajaxcart');
                                $this->loadLayout();
                                $handles = array( 'ajaxall-wishlist' => 'ajaxwishlist_wishlist_sidebar',
                                                  //'toplinks'=> 'toplinks_allajax'); 
                                                  'allajax-wishlist-links'=> 'WISHLIST_LINKS_AJAX');
                                $_response->addUpdatedBlocks($_response,$handles);
                              

                            } catch (Mage_Core_Exception $e) {
                                $_response->setError(true);
                                $_response->setMessage($this->__('An error occurred while adding item to wishlist: %s', $e->getMessage()));
                            } catch (Exception $e) {
                                mage::log($e->getMessage());
                                $_response->setError(true);
                                $_response->setMessage($this->__('An error occurred while adding item to wishlist: %s', $e->getMessage()));
                            }
                        }
                    }
                }
            }
        }
        $this->_clearMessages();
        $_response->send();
        return;
    }
	// Create Wishlist
    protected function _createWishlist($name){
        
        $customerId =  Mage::getSingleton('customer/session')->getCustomerId();
        //Mage::log('dsds'.$customerId);
        $visibility = ($this->getRequest()->getParam('visibility', 0) === 'on' ? 1 : 0);
        if ($name !== null) {
            try {
                $wishlist = $this->_editWishlist($customerId, $name, $visibility);
                $this->getRequest()->setParam('wishlist_id', $wishlist->getId());
                return $wishlist->getId();
            } catch (Mage_Core_Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            } catch (Exception $e) {
                $this->_getSession()->addException(
                    $e,
                    Mage::helper('enterprise_wishlist')->__('Error happened during wishlist creation')
                );
            }
        }
        
    }
	// Get Session Data
    protected function _getSession() {
        return Mage::getSingleton('checkout/session');
    }

    /**
     * Add wishlist item to shopping cart and remove from wishlist
     *
     * If Product has required options - item removed from wishlist and redirect
     * to product view page with message about needed defined required options
     */
   public function cartAction() {
       
             
        $_response = Mage::getModel('allajax/response');
        $itemId = (int) $this->getRequest()->getParam('item');

        /* @var $item Mage_Wishlist_Model_Item */
        $item = Mage::getModel('wishlist/item')->load($itemId);
        //  Mage::log($itemId.'--'.$item);
        if (!$item->getId()) {

            $_response->setError(true);
            $_response->setMessage($this->__('Product Not Found.'));

            //return $this->_redirect('*/*');
        }
        $wishlist = $this->_getWishlist($item->getWishlistId());
        if (!$wishlist) {
            $_response->setError(true);
            $_response->setMessage($this->__('Product Not Found.'));
            //return $this->_redirect('*/*');
        }

        if (empty($response)) {
            $qty = $this->getRequest()->getParam('qty');
            if (is_array($qty)) {
                if (isset($qty[$itemId])) {
                    $qty = $qty[$itemId];
                } else {
                    $qty = 1;
                }
            } else {
                if($qty!=""){
                $qty = $qty;
                }else{
                $qty = 1;    
                }
            }
            $qty = $this->_processLocalizedQty($qty);
            if ($qty) {
                $item->setQty($qty);
               // $this->getRequest()->getParams();
                
            }
            
            //$item->setQty(1);

            $session = Mage::getSingleton('wishlist/session');
            $cart = Mage::getSingleton('checkout/cart');

            $redirectUrl = Mage::getUrl('*/*');

            try {

                $options = Mage::getModel('wishlist/item_option')->getCollection()
                        ->addItemFilter(array($itemId));
                $item->setOptions($options->getOptionsByItem($itemId));
                //$item->setOptions($this->getRequest()->getParams());

                $buyRequest = Mage::helper('catalog/product')->addParamsToBuyRequest(
                        $this->getRequest()->getParams(), array('current_config' => $item->getBuyRequest())
                );

                $item->mergeBuyRequest($buyRequest);
                $item->addToCart($cart, true);
                $cart->save()->getQuote()->collectTotals();
                $wishlist->save();

                Mage::helper('wishlist')->calculate();

                if (Mage::helper('checkout/cart')->getShouldRedirectToCart()) {
                    $redirectUrl = Mage::helper('checkout/cart')->getCartUrl();
                } else if ($this->_getRefererUrl()) {
                    $redirectUrl = $this->_getRefererUrl();
                }
                Mage::helper('wishlist')->calculate();

                $message = $this->__('Product has been added to cart');
                $_response->setMessage($message);
                $this->_getSession()->addSuccess($message);
                        
                 $handles = array( 'ajaxall-wishlist' => 'ajaxwishlist_wishlist_sidebar',
                                                  'allajax-wishlist-links'=> 'WISHLIST_LINKS_AJAX',
                     'my-wishlist' =>   'WISHLIST_UPDATE_AJAX',
                     'mini-cart' => 'CART_MINICART_AJAX'); 
                  $_response->addUpdatedBlocks($_response,$handles);
                                
            } catch (Mage_Core_Exception $e) {
                if ($e->getCode() == Mage_Wishlist_Model_Item::EXCEPTION_CODE_NOT_SALABLE) {
                    $_response->setError(true);
                    $_response->setMessage($this->__('This product(s) is currently out of stock.'));
                    //$session->addError(Mage::helper('wishlist')->__('This product(s) is currently out of stock'));
                } else if ($e->getCode() == Mage_Wishlist_Model_Item::EXCEPTION_CODE_HAS_REQUIRED_OPTIONS) {
                    $_response->setError(true);
                    $_response->setMessage($this->__($e->getMessage()));
                    $response['redirectUrl'] = Mage::getUrl('*/*/configure/', array('id' => $item->getId()));

                    //Mage::getSingleton('catalog/session')->addNotice($e->getMessage());
                    //$redirectUrl = Mage::getUrl('*/*/configure/', array('id' => $item->getId()));
                } else {
                    $_response->setError(true);
                    $_response->setMessage($this->__($e->getMessage()));
                    $response['redirectUrl'] = Mage::getUrl('*/*/configure/', array('id' => $item->getId()));

                    //Mage::getSingleton('catalog/session')->addNotice($e->getMessage());
                    //$redirectUrl = Mage::getUrl('*/*/configure/', array('id' => $item->getId()));
                }
            } catch (Exception $e) {
                //$session->addException($e, Mage::helper('wishlist')->__('Cannot add item to shopping cart'));
                $_response->setError(true);
                $_response->setMessage($this->__('Cannot add item to shopping cart'));
            }

            Mage::helper('wishlist')->calculate();

            //return $this->_redirectUrl($redirectUrl);
        }
       $this->_clearMessages(); 
       $_response->send();
       return;

        /* @var $session Mage_Wishlist_Model_Session */
    }
    
    // Add Poroduct To Cart form the wishlist 	
    protected function _addProductToCart($_response, $_this) {
        //mini-cart
        $handles = array( 'mini-cart' => 'CART_MINICART_AJAX');                
        $cart = Mage::getSingleton('checkout/cart');
        $params = $_this->getRequest()->getParams();
        try {
            if (isset($params['qty'])) {
                $filter = new Zend_Filter_LocalizedToNormalized(
                                array('locale' => Mage::app()->getLocale()->getLocaleCode())
                );
                $params['qty'] = $filter->filter($params['qty']);
            }

            $product = $_this->_initProduct();
            $related = $_this->getRequest()->getParam('related_product');

            /**
             * Check product availability
             */
            if (!$product) {
                $_response->setError(true);
                $_response->setMessage($this->__('Product is not Available.'));
            }

            $cart->addProduct($product, $params);
            if (!empty($related)) {
                $cart->addProductsByIds(explode(',', $related));
            }

            $cart->save();

            $_this->_getSession()->setCartWasUpdated(true);

            /**
             * @todo remove wishlist observer processAddToCart
             */
            $_this->getLayout()->getUpdate()->addHandle('ajaxcart');
            $_this->loadLayout();

            Mage::dispatchEvent('checkout_cart_add_product_complete', array('product' => $product, 'request' => $_this->getRequest(), 'response' => $_this->getResponse(),'handles'=> $handles)
            );

            if (!$_this->_getSession()->getNoCartRedirect(true)) {
                if (!$cart->getQuote()->getHasError()) {
                    $message = $_this->__('%s was added to your shopping cart.', Mage::helper('core')->escapeHtml($product->getName()));
                    $_this->_getSession()->addSuccess($message);
                    $_response->setMessage($message);
                }
                $_this->_goBack();
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

            $url = $_this->_getSession()->getRedirectUrl(true);

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
        return $_response;
    }
    
 	  // Get Product Data
      protected function _initProduct()
    {
        $productId = (int) $this->getRequest()->getParam('product');
        if ($productId) {
            $product = Mage::getModel('catalog/product')
                ->setStoreId(Mage::app()->getStore()->getId())
                ->load($productId);
            if ($product->getId()) {
                return $product;
            }
        }
        return false;
    }
    
    // Remove Item From Wishlist

    protected function _removeWishlistForaddtocart($_response,$_this,$item) {
        //$_response = Mage::getModel('allajax/response');
       // $response = array();
        if (!Mage::getStoreConfigFlag('wishlist/general/active')) {
            $_response->setError(true);
            $_response->setMessage($_this->__('Wishlist Has Been Disabled By Admin'));
        }
        if (!Mage::getSingleton('customer/session')->isLoggedIn()) {
            $_response->setError(true);
            $_response->setMessage($_this->__('Please Login First'));
        }

        if (empty($response)) {
            $session = Mage::getSingleton('customer/session');
            $wishlist = $this->_getWishlist();
            if (!$wishlist) {
                $_response->setError(true);
                $_response->setMessage($_this->__('Unable to Delete Wishlist'));
            } else {

                $itemId = (int) $_this->getRequest()->getParam('item');

                if (!$itemId) {
                    $_response->setError(true);
                    $_response->setMessage($this->__('Product Not Found'));
                } else {
                    try {

                        $id = (int) $_this->getRequest()->getParam('item');
                        $productId = $item->getProductId();
                        if (!$item->getId()) {
                            return $_this->norouteAction();
                        }

                        $wishlist = $_this->_getWishlist($item->getWishlistId());
                        if (!$wishlist) {
                            return $_this->norouteAction();
                        }

                        $item->delete();
                        $wishlist->save();


                        Mage::helper('wishlist')->calculate();

                                       
                        Mage::unregister('wishlist');

                        $_this->loadLayout();
                        $wishlistCollection = Mage::getModel('wishlist/item')->getCollection();
                        foreach ($wishlistCollection as $key => $wishlistModel) {
                            if ($wishlistModel->getProductId() == $productId) {
                                $response['flag'] = "true";
                                $response['previous'] = $id;
                                $response['current'] = (int) $wishlistModel->getId();
                                break;
                            }
                        }

                      
                        
                    } catch (Mage_Core_Exception $e) {
                        $_response->setError(true);
                        $_response->setMessage($_this->__('An error occurred while deleting the item from wishlist: %s', $e->getMessage()));
                    } catch (Exception $e) {
                        mage::log($e->getMessage());
                        $_response->setError(true);
                        $_response->setMessage($_this->__('An error occurred while deleting the item from wishlist'));
                    }
                }
            }
        }
        $this->_clearMessages();
        return $_response;
    }
    
    
     // Remove Item From Wishlist
    public function removewishlistAction() {
        $_response = Mage::getModel('allajax/response');
        $response = array();
        if (!Mage::getStoreConfigFlag('wishlist/general/active')) {
            $_response->setError(true);
            $_response->setMessage($this->__('Wishlist Has Been Disabled By Admin'));
        }
        if (!Mage::getSingleton('customer/session')->isLoggedIn()) {
            $_response->setError(true);
            $_response->setMessage($this->__('Please Login First'));
        }

        if (empty($response)) {
            $session = Mage::getSingleton('customer/session');
            $wishlist = $this->_getWishlist();
            if (!$wishlist) {
                $_response->setError(true);
                $_response->setMessage($this->__('Unable to Delete Wishlist'));
            } else {

                $itemId = (int) $this->getRequest()->getParam('item');

                if (!$itemId) {
                    $_response->setError(true);
                    $_response->setMessage($this->__('Product Not Found'));
                } else {
                    try {

                        $id = (int) $this->getRequest()->getParam('item');
                        $item = Mage::getModel('wishlist/item')->load($id);
                        $productId = $item->getProductId();
                        if (!$item->getId()) {
                            return $this->norouteAction();
                        }

                        $wishlist = $this->_getWishlist($item->getWishlistId());
                        if (!$wishlist) {
                            return $this->norouteAction();
                        }

                        $item->delete();
                        $wishlist->save();


                        Mage::helper('wishlist')->calculate();

                        $message = $this->__('Item has been deleted from your wishlist.');

                        $message = $this->__($message);
                        $_response->setMessage($message);
                        $this->_getSession()->addSuccess($message);
               
                        Mage::unregister('wishlist');

                        $this->loadLayout();
                        $wishlistCollection = Mage::getModel('wishlist/item')->getCollection();
                        foreach ($wishlistCollection as $key => $wishlistModel) {
                            if ($wishlistModel->getProductId() == $productId) {
                                $response['flag'] = "true";
                                $response['previous'] = $id;
                                $response['current'] = (int) $wishlistModel->getId();
                                break;
                            }
                        }
                        
                        $handles = array(
                                          'ajaxall-wishlist' => 'ajaxwishlist_wishlist_sidebar',
                                          //'toplinks' => 'toplinks_allajax',
                                          'allajax-wishlist-links'=> 'WISHLIST_LINKS_AJAX'
                        );
                        if (Mage::helper('allajax')->isRefererWishlistPage()){
                            $handles = array_merge($handles, array('my-wishlist' =>   'WISHLIST_UPDATE_AJAX'));                                    
                        }    
                        
                        $_response->addUpdatedBlocks($_response, $handles);
                    } catch (Mage_Core_Exception $e) {
                        $_response->setError(true);
                        $_response->setMessage($this->__('An error occurred while deleting the item from wishlist: %s', $e->getMessage()));
                    } catch (Exception $e) {
                        mage::log($e->getMessage());
                        $_response->setError(true);
                        $_response->setMessage($this->__('An error occurred while deleting the item from wishlist'));
                    }
                }
            }
        }
        $this->_clearMessages();
        $_response->send();
        return;
    }

    // Add Product To Compare List
        public function compareAction()
    {
        
        $_response = Mage::getModel('allajax/response');
       
        if ($productId = (int) $this->getRequest()->getParam('product'))
        {
            $product = Mage::getModel('catalog/product')
                    ->setStoreId(Mage::app()->getStore()->getId())
                    ->load($productId);

            if ($product->getId()/* && !$product->isSuper() */)
            {
                Mage::getSingleton('catalog/product_compare_list')->addProduct($product);
                $message = $this->__('The product %s has been added to comparison list.', Mage::helper('core')->escapeHtml($product->getName()));
                $_response->setMessage($message);
                $this->_getSession()->addSuccess($message);
                
                Mage::register('referrer_url', $this->_getRefererUrl());
                Mage::helper('catalog/product_compare')->calculate();
                Mage::dispatchEvent('catalog_product_compare_add_product', array('product' => $product));
                $this->loadLayout();
                
                
                                                
                //$handles = array( 'ajaxwishlist_compare' => 'ajaxwishlist_compare');
               // $_response->addUpdatedBlocks($_response,$handles);
                $tmp['name'] = 'ajaxwishlist_compare_custom';
                $tmp['html'] = '';
                if ($this->getLayout()->createBlock('cms/block')->setBlockId('block_header_top_right')) {
                    $tmp['html'] = $this->getLayout()->createBlock('cms/block')->setBlockId('block_header_top_right')->toHtml();
                }
                $result[] = $tmp;
                $_response->setUpdateBlocks($result);
 
            }
        }
        $this->_clearMessages();
        $_response->send();
        return;
    }
    
    
        /**
     * Remove item from compare list
     */
    public function removecompareAction()
    {
        $_response = Mage::getModel('allajax/response');
        if ($productId = (int) $this->getRequest()->getParam('product')) {
            $product = Mage::getModel('catalog/product')
                ->setStoreId(Mage::app()->getStore()->getId())
                ->load($productId);

            if($product->getId()) {
                /** @var $item Mage_Catalog_Model_Product_Compare_Item */
                $item = Mage::getModel('catalog/product_compare_item');
                if(Mage::getSingleton('customer/session')->isLoggedIn()) {
                    $item->addCustomerData(Mage::getSingleton('customer/session')->getCustomer());
                } elseif ($this->_customerId) {
                    $item->addCustomerData(
                        Mage::getModel('customer/customer')->load($this->_customerId)
                    );
                } else {
                    $item->addVisitorId(Mage::getSingleton('log/visitor')->getId());
                }

                $item->loadByProduct($product);

                if($item->getId()) {
                    $item->delete();
                                    
                    $message = $this->__('The product %s has been removed from comparison list.', $product->getName());
                    $_response->setMessage($message);
                    $this->_getSession()->addSuccess($message);
                
                    Mage::dispatchEvent('catalog_product_compare_remove_product', array('product'=>$item));
                    Mage::helper('catalog/product_compare')->calculate();
                    
                    $this->loadLayout();
                    $handles = array( 'ajaxwishlist_compare' => 'ajaxwishlist_compare'); 
                    $_response->addUpdatedBlocks($_response,$handles);
                
                }
            }
        }
        $this->_clearMessages();    
        $_response->send();
        return;
    }

    /**
     * Remove all items from comparison list
     */
    public function clearcompareAction()
    {
        $_response = Mage::getModel('allajax/response');
        $items = Mage::getResourceModel('catalog/product_compare_item_collection');

        if (Mage::getSingleton('customer/session')->isLoggedIn()) {
            $items->setCustomerId(Mage::getSingleton('customer/session')->getCustomerId());
        } elseif ($this->_customerId) {
            $items->setCustomerId($this->_customerId);
        } else {
            $items->setVisitorId(Mage::getSingleton('log/visitor')->getId());
        }

        /** @var $session Mage_Catalog_Model_Session */
        $session = Mage::getSingleton('catalog/session');

        try {
            $items->clear();
            $message = $this->__('The comparison list was cleared.');
            $_response->setMessage($message);
            $this->_getSession()->addSuccess($message);
            Mage::helper('catalog/product_compare')->calculate();
            $this->loadLayout();
            $handles = array( 'ajaxwishlist_compare' => 'ajaxwishlist_compare'); 
            $_response->addUpdatedBlocks($_response,$handles);
        } catch (Mage_Core_Exception $e) {
            $session->addError($e->getMessage());
        } catch (Exception $e) {
            $session->addException($e, $this->__('An error occurred while clearing comparison list.'));
        }
        $this->_clearMessages();
        $_response->send();
        return;
    }
    
    // Check Quantity for add product to cart
     protected function _processLocalizedQty($qty)
    {
        if (!$this->_localFilter)
        {
            $this->_localFilter = new Zend_Filter_LocalizedToNormalized(
                            array('locale' => Mage::app()->getLocale()->getLocaleCode())
            );
        }
        $qty = $this->_localFilter->filter((float) $qty);
        if ($qty < 0)
        {
            $qty = null;
        }
        return $qty;
    }

	/**
     * Remove item
     */
    public function removeAction()
    {
        $result = array();        
        $handles = array('my-wishlist' =>   'WISHLIST_UPDATE_AJAX',
                        'allajax-wishlist-links'=> 'WISHLIST_LINKS_AJAX',
                        'ajaxall-wishlist' => 'ajaxwishlist_wishlist_sidebar');
               
        $id = (int) $this->getRequest()->getParam('item');
        $item = Mage::getModel('wishlist/item')->load($id);
        if (!$item->getId()) {
            return $this->_ajaxRedirectResponse();
        }
        $wishlist = $this->_getWishlist($item->getWishlistId());
        if (!$wishlist) {
            return $this->_ajaxRedirectResponse();
        }
        try {
            $item->delete();
            $wishlist->save();
        } catch (Mage_Core_Exception $e) {
            Mage::getSingleton('customer/session')->addError(
                $this->__('An error occurred while deleting the item from wishlist: %s', $e->getMessage())
            );
        } catch(Exception $e) {
            Mage::getSingleton('customer/session')->addError(
                $this->__('An error occurred while deleting the item from wishlist.')
            );
        }

        Mage::helper('wishlist')->calculate();
        
        Mage::helper('allajax')->setMessages(Mage::getSingleton('customer/session'));
        $result = Mage::helper('allajax')
                    ->getHandleBlockResult($handles,$result);                
        $this->getResponse()->setBody(Zend_Json::encode($result));
        
    }
    
    //check product has options 
    public function checkoptionsAction() {
        $_response = Mage::getModel('allajax/response');
        $item_id = (int) $this->getRequest()->getParam('item');

        $customer = Mage::getSingleton('customer/session')->getCustomer();
        if ($customer->getId()) {
            $wishlist = Mage::getModel('wishlist/item')->load($item_id);
            //$wishlist = Mage::getModel('wishlist/wishlist')->loadByCustomer($customer, true);
            //$wishListItemCollection = $wishlist->getItemById($item_id);;
           // foreach ($wishListItemCollection as $item) {
                //$item_current = $item->getId();
                //if ($item_current == $item_id) {
                    $productId = $wishlist->getProductId();
              //  }
            //}

            $product = Mage::getModel('catalog/product');
            $product->load($productId);
            if( $product->getTypeId() == 'grouped' ){
                    $_response->setMessage('grouped'); 
                    $_response->setUrl($this->_getAddToCartUrl($product));
            }else{
                if ($product->getTypeId() != 'simple') {
                    $_response->setMessage('configurable'); 
                    $_response->setUrl($this->_getAddToCartUrl($product));
                } else {
                    $_response->setMessage('simple'); 
                    $_response->setUrl($this->_getAddToCartUrl($product));
                }
            }
        }
           $_response->send();
        return;
    }
    // Get Add to Cart Url
    private function _getAddToCartUrl($product, $additional = array())
    {
        if ($product->getTypeInstance(true)->hasRequiredOptions($product)) {
            if (!isset($additional['_escape'])) {
                $additional['_escape'] = true;
            }
            if (!isset($additional['_query'])) {
                $additional['_query'] = array();
            }
            $additional['_query']['options'] = 'cart';

            return $this->_getProductUrl($product, $additional);
        }
        return Mage::helper('checkout/cart')->getAddUrl($product, $additional);
    }
    // Get Product Url
    private function _getProductUrl($product, $additional = array())
    {
        if ($this->hasProductUrl($product)) {
            if (!isset($additional['_escape'])) {
                $additional['_escape'] = true;
            }
            return $product->getUrlModel()->getUrl($product, $additional);
        }

        return '#';
    }
    // Check if productis visible
    public function hasProductUrl($product)
    {
        if ($product->getVisibleInSiteVisibilities()) {
            return true;
        }
        if ($product->hasUrlDataObject()) {
            if (in_array($product->hasUrlDataObject()->getVisibility(), $product->getVisibleInSiteVisibilities())) {
                return true;
            }
        }

        return false;
    }
    

	  /**
     * Update wishlist item comments
     */
    public function updateAction()
    {
        $result = array();        
        $handles = array(
                'my-wishlist' =>   'WISHLIST_UPDATE_AJAX',
                'allajax-wishlist-links'=> 'WISHLIST_LINKS_AJAX',
                'ajaxall-wishlist' => 'ajaxwishlist_wishlist_sidebar'
         );
        
        if (!$this->_validateFormKey()) {
            $this->_ajaxRedirectResponse();
        }
        $wishlist = $this->_getWishlist();
        if (!$wishlist) {
            $this->_ajaxRedirectResponse();
        }

        $post = $this->getRequest()->getPost();
        if($post && isset($post['description']) && is_array($post['description'])) {
            $updatedItems = 0;

          if(isset($post['do'])){
            foreach ($post['description'] as $itemId => $description) {
                $item = Mage::getModel('wishlist/item')->load($itemId);
                if ($item->getWishlistId() != $wishlist->getId()) {
                    continue;
                }

                // Extract new values
                $description = (string) $description;
                if (!strlen($description)) {
                    $description = $item->getDescription();
                }

                $qty = null;
                if (isset($post['qty'][$itemId])) {
                    $qty = $this->_processLocalizedQty($post['qty'][$itemId]);
                }
                if (is_null($qty)) {
                    $qty = $item->getQty();
                    if (!$qty) {
                        $qty = 1;
                    }
                } elseif (0 == $qty) {
                    try {
                        $item->delete();
                    } catch (Exception $e) {
                        Mage::logException($e);
                        Mage::getSingleton('customer/session')->addError(
                            $this->__('Can\'t delete item from wishlist')
                        );
                    }
                }

                // Check that we need to save
                if (($item->getDescription() == $description) && ($item->getQty() == $qty)) {
                    continue;
                }
                try {
                    $item->setDescription($description)
                        ->setQty($qty)
                        ->save();
                    $updatedItems++;
                } catch (Exception $e) {
                    Mage::getSingleton('customer/session')->addError(
                        $this->__('Can\'t save description %s', Mage::helper('core')->htmlEscape($description))
                    );
                }
            }
        }else{
                $itemId = $post['dosave'];
                $item = Mage::getModel('wishlist/item')->load($itemId);
                if ($item->getWishlistId() != $wishlist->getId()) {
                    Mage::getSingleton('customer/session')->addError(
                            $this->__('Unble to specify Wishlist')
                        );
                }

                // Extract new values
                $description = $post['description'][$itemId];
                if (!strlen($description)) {
                    $description = $item->getDescription();
                }

                $qty = null;
                
                if (is_null($qty)) {
                    $qty = $item->getQty();
                    if (!$qty) {
                        $qty = 1;
                    }
                } elseif (0 == $qty) {
                    try {
                        $item->delete();
                    } catch (Exception $e) {
                        Mage::logException($e);
                        Mage::getSingleton('customer/session')->addError(
                            $this->__('Can\'t delete item from wishlist')
                        );
                    }
                }

                // Check that we need to save
                if (($item->getDescription() != $description)) {
                     try {
                        $item->setDescription($description)
                            ->setQty($qty)
                            ->save();
                        $updatedItems++;
                    } catch (Exception $e) {
                        Mage::getSingleton('customer/session')->addError(
                            $this->__('Can\'t save description %s', Mage::helper('core')->htmlEscape($description))
                        );
                    }
                }
               
            
            
        }

            // save wishlist model for setting date of last update
            if ($updatedItems) {
                try {
                    $wishlist->save();
                    Mage::helper('wishlist')->calculate();
                }
                catch (Exception $e) {
                    Mage::getSingleton('customer/session')->addError($this->__('Can\'t update wishlist'));
                }
            }                        
            
        }
        Mage::helper('allajax')->setMessages(Mage::getSingleton('customer/session'));
        $result = Mage::helper('allajax')
                        ->getHandleBlockResult($handles,$result);                
        $this->getResponse()->setBody(Zend_Json::encode($result));
    }
    
    protected function _clearMessages(){
        $this->_getSession()->getMessages(true);        
    }
     /**
     * Create new customer wishlist
     */
    public function createwishlistAction()
    {
        $this->_forward('editwishlist');
    }
    
    
    /**
     * Edit wishlist
     *
     * @param int $customerId
     * @param string $wishlistName
     * @param bool $visibility
     * @param int $wishlistId
     * @return Mage_Wishlist_Model_Wishlist
     */
    protected function _editWishlist($customerId, $wishlistName, $visibility = false, $wishlistId = null)
    {
        $wishlist = Mage::getModel('wishlist/wishlist');

        if (!$customerId) {
            Mage::throwException(Mage::helper('enterprise_wishlist')->__('Log in to edit wishlists.'));
        }
        if (!strlen($wishlistName)) {
            Mage::throwException(Mage::helper('enterprise_wishlist')->__('Provide wishlist name'));
        }
        if ($wishlistId){
            $wishlist->load($wishlistId);
            if ($wishlist->getCustomerId() !== $this->_getSession()->getCustomerId()) {
                Mage::throwException(
                    Mage::helper('enterprise_wishlist')->__('The wishlist is not assigned to your account and cannot be edited ')
                );
            }
        } else {
            $wishlistCollection = Mage::getModel('wishlist/wishlist')->getCollection()
                ->filterByCustomerId($customerId);
            $limit = Mage::helper('enterprise_wishlist')->getWishlistLimit();
            if (Mage::helper('enterprise_wishlist')->isWishlistLimitReached($wishlistCollection)) {
                Mage::throwException(
                    Mage::helper('enterprise_wishlist')->__('Only %d wishlists can be created.', $limit)
                );
            }
            $wishlist->setCustomerId($customerId);
        }
        $wishlist->setName($wishlistName)
            ->setVisibility($visibility)
            ->generateSharingCode()
            ->save();
        return $wishlist;
    }
    
}
