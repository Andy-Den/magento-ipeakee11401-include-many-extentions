<?php
class Balance_Allajax_Block_Checkout_Cart_Item_Configure extends Mage_Core_Block_Template
{
        protected function _prepareLayout()
    {
        // Set custom submit url route for form - to submit updated options to cart
        $block = $this->getLayout()->getBlock('product.info');
        if ($block) {
             $block->setSubmitRouteData(array(
                'route' => 'checkout/cart/updateItemOptions',
                'params' => array('id' => $this->getRequest()->getParam('id'))
             ));
        }


        // Set custom template with 'Update Cart' button
        $block = $this->getLayout()->getBlock('product.info.addtocart');
        if ($block) {
            $block->setTemplate('allajax/checkout/cart/item/configure/updatecart.phtml');
        }

        return parent::_prepareLayout();
    }
    
}
			