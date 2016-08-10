<?php

class Balanceinternet_Shoppingdotcomfeed_Model_Observer extends Mage_CatalogRule_Model_Observer {

    private $xml;

    public function __construct() {
        
    }

    public function processCronDaily() {
        $this->exportFeed(1); // Daily - per sdc_frequency id
    }

    public function processCronWeekly() {
        $this->exportFeed(2); // Daily - per sdc_frequency id
    }

    /**
     * Export cron, loop through sdc_feedproducts and 
     * export each respective cron for each country store added
     *
     * @param int $frequency
     * @return object $this
     */
    public function exportFeed($frequency) {

        $sdcFeedResults = Mage::getModel('shoppingdotcomfeed/feedproducts')->getResource()->getAllExportFeedsContent($frequency);

        // Loop through Daily Feeds        
        foreach ($sdcFeedResults as $sdcFeedResult) {
            $path = Mage::app()->getConfig()->getTempVarDir() . '/export/';
            $productIds = unserialize($sdcFeedResult['product_ids']);
            $products = Mage::getModel('catalog/product')->getCollection()
                    ->addAttributeToFilter('entity_id', array('in' => $productIds))
                    ->addAttributeToSelect(array('name', 'sku', 'short_description', 'description', 'price', 'image', 'status', 'manufacturer', 'url_path', 'url_key'));


            $sdcFeed = Mage::getModel('shoppingdotcomfeed/feed')->getResource()->getExportFeedContent($sdcFeedResult['id_feed'], 1);
            $filename = $sdcFeed[0]['filename'];
            $result = array();
            foreach ($products as $product) {
                //if (Mage::getModel('cataloginventory/stock_item')->loadByProduct($product)->getIsInStock()
                $result['products']['product'][] = array(
                    'Merchant_SKU' => $product->getSku(),
                    'Product_Name' => $product->getName(),
                    'Current_Price' => number_format($product->getFinalPrice(), 2, '.', ''), //$product->getPrice(), 
                    'Image_URL' => Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA) . 'catalog/product' . $product->getImage(),
                    'Product_URL' => Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB) . $product->getUrlPath(),
                    'Condition' => 'New',
                    'Shipping_Rate' => '',
                    'Availability' => (Mage::getModel('cataloginventory/stock_item')->loadByProduct($product)->getIsInStock()) ? "YES" : "NO"
                );
            }

            $this->xml = new SimpleXMLElement("<Products></Products>");
            $this->iterate($result, $this->xml);
            $content = $this->xml->asXML();
            file_put_contents($path . $filename, $content);

            // Update date .xml file exported
            $id_feed = $sdcFeedResult['id_feed'];
            Mage::getModel('shoppingdotcomfeed/feed')->getResource()->updateFeedExportTime($id_feed);

            /* Copy file to location */
            $this->ftpFeed($filename, $path, $sdcFeed);
        }
    }

    /**
     * Convert array to an xml with SimpleXMLElement
     *
     * @param $filename filename
     * @param $sdcFeed contains username, password,  filename.
     * @return 
     */
    public function ftpFeed($filename, $path, $sdcFeed) {

        $server = $sdcFeed[0]['ftp'];
        $username = $sdcFeed[0]['username'];
        $password = $sdcFeed[0]['password'];
        $errors = array();
        if ($server == "localhost")
            $remote_file = '/Users/stephengoudie/ftp/' . $filename;
        else
            $remote_file = $filename;
        $local_file = $path . $filename;

        try {
            $con = ftp_connect($server);
            if (false === $con) {
                throw new Exception('Unable to connect');
            }

            $loggedIn = ftp_login($con, $username, $password);
            if (true === $loggedIn) {
                // Upload file to FTP                //Mage::log('Connected, establishing ftp_put...');
                $upload = ftp_put($con, $remote_file, $local_file, FTP_BINARY);
                if (!$upload) {
                    throw new Exeception('Unable to copy the file to the FTP Server');
                    $error = 'FTP Failedd: ' . $e->getMessage();
                    array_push($errors, $error);
                    Mage::getModel('shoppingdotcomfeed/feed')->getResource()->setProductFeedSuccess(0, $sdcFeed[0]['id'], $errors);                    
                } else {
                    Mage::getModel('shoppingdotcomfeed/feed')->getResource()->updateSuccessfulUpload($sdcFeed[0]['id']);
                    Mage::getModel('shoppingdotcomfeed/feed')->getResource()->setProductFeedSuccess(1, $sdcFeed[0]['id'], $errors);                    
                }
            }
            ftp_close($con);
        } catch (Exception $e) {
            $error = 'FTP Failed: ' . $e->getMessage();
            array_push($errors, $error);
            Mage::log($error, null, 'shoppingdotcom.log');
            Mage::getModel('shoppingdotcomfeed/feed')->getResource()->setProductFeedSuccess(0, $sdcFeed[0]['id'], $errors); // Fail
        }
    }

    /**
     * Iterate through array and create XML Object
     *
     * @param array $arr
     * @param SimpleXMLElement $xml
     * @return SimpleXMLElement $xml
     */
    public function iterate($element, $xml) {
        foreach ($element as $name => $value) {
            if (is_string($value) || is_numeric($value)) {
                $xml->$name = $value;
            } else {
                $xml->$name = null;
                $this->iterate($value, $xml->$name);
            }
        }
    }

}
