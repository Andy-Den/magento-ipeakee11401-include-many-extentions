<?php

class Balance_Datafeed_DatafeedController extends Mage_Core_Controller_Front_Action {

    public function generateAction() {
        // http://www.example.com/index.php/datafeed/datafeed/generate/id/{data_feed_id}/ak/{YOUR_ACTIVATION_KEY}
        
        $id = $this->getRequest()->getParam('id');
        $ak=$this->getRequest()->getParam('ak');
        
        $activation_key=Mage::getStoreConfig("datafeed/license/activation_key");
        
        if($activation_key==$ak) {
            $datafeed = Mage::getModel('datafeed/datafeed');
            $datafeed->setId($id);
            if ($datafeed->load($id)) {
                try {
                    $datafeed->generateFile();
                    die(Mage::helper('datafeed')->__('The data feed "%s" has been generated.', $datafeed->getFeedName()));
                } catch (Mage_Core_Exception $e) {
                    die($e->getMessage());
                } catch (Exception $e) {
                    die($e->getMessage());
                }
            } else {
                die(Mage::helper('datafeed')->__('Unable to find a data feed to generate.'));
            }
        } 
        else die('Invalid activation key');
    }

}

