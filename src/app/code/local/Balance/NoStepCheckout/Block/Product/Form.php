<?php

class Balance_NoStepCheckout_Block_Product_Form extends Mage_Catalog_Block_Product_View {
    protected function _toHtml() {
        $helper = Mage::helper('nostepcheckout');
        if($helper->isEnabled()) {
            return parent::_toHtml();
        }
        return '';
               
    }
    public function getSubmitUrl($product, $additional = array()) {      

        $addUrlKey = Mage_Core_Controller_Front_Action::PARAM_NAME_URL_ENCODED;
        $addUrlValue = Mage::getUrl('*/*/*', array('_use_rewrite' => true, '_current' => true));
        $additional[$addUrlKey] = Mage::helper('core')->urlEncode($addUrlValue);
        
        $routeParams = array(
            Mage_Core_Controller_Front_Action::PARAM_NAME_URL_ENCODED => Mage::helper('core')->urlEncode($this->getCurrentUrl()),
            'product' => $product->getEntityId(),
            Mage_Core_Model_Url::FORM_KEY => $this->_getSingletonModel('core/session')->getFormKey()
        );

        if (!empty($additional)) {
            $routeParams = array_merge($routeParams, $additional);
        }

        if ($product->hasUrlDataObject()) {
            $routeParams['_store'] = $product->getUrlDataObject()->getStoreId();
            $routeParams['_store_to_url'] = true;
        }

        

        return Mage::helper('nostepcheckout')->formatSecureUrl(Mage::getUrl('nostepcheckout/checkout/index', $routeParams));
    }
    
    public function shareOptionSelector() {
        return (bool)(Mage::helper('nostepcheckout')->getConfigData('order_from_product/share_option_selector') > 0);
    }
            
}