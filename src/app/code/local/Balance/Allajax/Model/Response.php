<?php

class Balance_Allajax_Model_Response extends Mage_Catalog_Block_Product_Abstract {

    public function send() {
        Zend_Json::$useBuiltinEncoderDecoder = true;
        if ($this->getError())
            $this->setR('error');
        else
            $this->setR('success');
        Mage::app()->getFrontController()->getResponse()->setHeader('Content-Type', 'text/plain')->setBody(Zend_Json::encode($this->getData()));
        Mage::app()->getFrontController()->getResponse()->sendResponse();
        die;
    }
    
    /**
     * genenerate outputs from pre-defined blocks or generate outputs from
     * input handles
     * 
     * @param type $_response
     * @param type $handles
     */
    public function addUpdatedBlocks(&$_response , $handles = null) {
        $updated_blocks = unserialize(Mage::getStoreConfig('allajax/general/update_blocks'));

        if ($updated_blocks) {
            $layout = Mage::getSingleton('core/layout');
            $res = array();

            foreach ($updated_blocks['id'] as $index => $block) {
                $value = $layout->getBlock($updated_blocks['xml'][$index]);

                if ($value) {
                    $tmp['key'] = $block;
                    $tmp['value'] = $value->toHtml();
                    $res[] = $tmp;
                }
            }
            if (!empty($res)) {
                $_response->setUpdateBlocks($res);
            }
        }
        //add handles if there are.
        $this->addUpdatedHandles($_response, $handles);
    }
    
    public function addConfigurableOptionsBlock(&$_response) {
        $layout = Mage::getSingleton('core/layout');
        $res = '';
        $_product = Mage::registry('current_product');

        $layout->getUpdate()->addHandle('ajaxcart_configurable_options');
        
        if ($_product->getTypeId() == 'bundle')
        $layout->getUpdate()->addHandle('ajaxcart_bundle_options');   
        
        if ($_product->getTypeId() == 'downloadable')
        $layout->getUpdate()->addHandle('ajaxcart_downloadable_options'); 

        // set unique cache ID to bypass caching
        $cacheId = 'LAYOUT_'.Mage::app()->getStore()->getId().md5(join('__', $layout->getUpdate()->getHandles()));
        $layout->getUpdate()->setCacheId($cacheId);

        $layout->getUpdate()->load();
        $layout->generateXml();
        $layout->generateBlocks();
        
       
        $value = $layout->getBlock('ajaxcart.configurable.options');        
        
        if ($value) {
            $res .= $value->toHtml();
        }
        
        if ($_product->getTypeId() == 'bundle') {
            $res ='';
            
            $value = $layout->getBlock('ajaxcart.bundled.options');        
        
            if ($value) {
                $res .= $value->toHtml();
            }
        
        }
        
        if ($_product->getTypeId() == 'downloadable') {
            $res ='';
            
            $value = $layout->getBlock('ajaxcart.downloadable.options');        
        
            if ($value) {
                $res .= $value->toHtml();
            }
        
        }
        

        
        if (!empty($res)) {
            $_response->setConfigurableOptionsBlock($res);
        }
    }

    public function addGroupProductItemsBlock(&$_response) {
        $layout = Mage::getSingleton('core/layout');
        $res = '';

        $layout->getUpdate()->addHandle('ajaxcart_grouped_options');

        // set unique cache ID to bypass caching
        $cacheId = 'LAYOUT_'.Mage::app()->getStore()->getId().md5(join('__', $layout->getUpdate()->getHandles()));
        $layout->getUpdate()->setCacheId($cacheId);

        $layout->getUpdate()->load();
        $layout->generateXml();
        $layout->generateBlocks();

        $value = $layout->getBlock('ajaxcart.grouped.options');

        if ($value) {
            $res .= $value->toHtml();
        }

        if (!empty($res)) {
            $_response->setConfigurableOptionsBlock($res);
        }
    }
    
    /**
     * Generate output for input handles
     * 
     * @param type $_response
     * @param type $handles
     */
    public function addUpdatedHandles(&$_response,$handles = null) 
    {                           
          if ($handles) {  
            $res = $_response->getUpdateBlocks();
            if (empty($res)) {
                $res = array();
            }                        
            $result = Mage::helper('allajax')->getHandleBlockResult($handles);
            if (isset($result['blocks'])){
                $res = array_merge($res,$result['blocks']);
            }                
            if (!empty($res)) {
                $_response->setUpdateBlocks($res);
            }
          }
    }
}