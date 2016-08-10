<?php

class Balance_Allajax_Model_Observer {
    /**
     * Updating blocks after add to cart event
     * 
     * @param type $observer
     */
    public function addToCartEvent($observer) {


        if (Mage::helper('allajax')->isEnabled()) {
            $handles = $observer->getEvent()->getHandles();

            $request = Mage::app()->getFrontController()->getRequest();

            if (!$request->getParam('in_cart') && !$request->getParam('is_checkout')) {

                Mage::getSingleton('checkout/session')->setNoCartRedirect(true);

                $_response = Mage::getModel('allajax/response')
                    ->setProductName($observer->getProduct()->getName())
                    ->setMessage(Mage::helper('checkout')->__('%s was added into your cart.', $observer->getProduct()->getName()));

                //append updated blocks
                $_response->addUpdatedBlocks($_response,$handles);

                $_response->send();
            }
            if ($request->getParam('is_checkout')) {

                Mage::getSingleton('checkout/session')->setNoCartRedirect(true);

                $_response = Mage::getModel('allajax/response')
                    ->setProductName($observer->getProduct()->getName())
                    ->setMessage(Mage::helper('checkout')->__('%s was added into your cart.', $observer->getProduct()->getName()));
                $_response->send();
            }
        }
    }
    /**
     * Updating blocks after cart item edit
     * 
     * @param type $observer
     */
    public function updateItemEvent($observer) {

        if (Mage::helper('allajax')->isEnabled()) {
            $request = Mage::app()->getFrontController()->getRequest();
            $handles = $observer->getEvent()->getHandles();
            if (!$request->getParam('in_cart') && !$request->getParam('is_checkout')) {

                Mage::getSingleton('checkout/session')->setNoCartRedirect(true);

                $_response = Mage::getModel('allajax/response')
                        ->setMessage(Mage::helper('checkout')->__('Item was updated.'));

                //append updated blocks
                $_response->addUpdatedBlocks($_response,$handles);

                $_response->send();
            }
            if ($request->getParam('is_checkout')) {

                Mage::getSingleton('checkout/session')->setNoCartRedirect(true);

                $_response = Mage::getModel('allajax/response')
                        ->setMessage(Mage::helper('checkout')->__('Item was updated.'));
                $_response->send();
            }
        }
    }

    public function getConfigurableOptions($observer) {
        if (Mage::helper('allajax')->isEnabled()) {
            $is_ajax = Mage::app()->getFrontController()->getRequest()->getParam('ajax');

            if($is_ajax) {
                $_response = Mage::getModel('allajax/response');

                $product = Mage::registry('current_product');
                if (!$product->isConfigurable() && !$product->getTypeId() == 'bundle'){return false;exit;}

                //append configurable options block
                $_response->addConfigurableOptionsBlock($_response);
                $_response->send();
            }
            return;
        }
    }

    public function getGroupProductOptions() {

        if (Mage::helper('allajax')->isEnabled()) {
            $id = Mage::app()->getFrontController()->getRequest()->getParam('product');
            $options = Mage::app()->getFrontController()->getRequest()->getParam('super_group');

            if($id) {
                $product = Mage::getModel('catalog/product')->load($id);
                if($product->getData()) {
                    if($product->getTypeId() == 'grouped' && !$options) {
                        $_response = Mage::getModel('allajax/response');
                        Mage::register('product', $product);
                        Mage::register('current_product', $product);

                        //add group product's items block
                        $_response->addGroupProductItemsBlock($_response);
                        $_response->send();
                    }
                }
            }
        }

    }
    
    /**
     * set html element id to the top wish list link, so that it can be
     * updated by ajax calls
     * 
     * @param type $observer
     * @return \Balance_Allajax_Model_Observer
     */
    public function renderWishlistLink($observer) 
    {
        if (Mage::helper('allajax')->isEnabled()) {
            $block = $observer->getEvent()->getBlock();
            if ($block instanceof Mage_Wishlist_Block_Links){
                $data  = 'id = "allajax-wishlist-links"';
                $aParams = $block->getAParams()? $block->getAParams().' '.$data  : $data;
                $block->setAParams($aParams);
            }
            return $this;
        }
    }
}