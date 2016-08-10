<?php
/*
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * It is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @category   Balance
 * @package    Feefocache
 * @copyright  Copyright (c) 2011 Balance
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

class Balance_Feefocache_Model_Cron
{
	private $_fileName = 'product_review.xml';
	
    //cron job for import review from Feefo
    public function importReviews(Mage_Cron_Model_Schedule $schedule)
    {
        //set_time_limit(0); //for non-safe mode
        //gett the all product collection
        $collection = Mage::getModel('catalog/product')->getCollection();
        $collection->addStoreFilter(2);

        $import_dir = Mage::getBaseDir('var') . DS . 'import';
        //file name for reviews save
        $product_review = $import_dir . DS . $this->_fileName;

        if(!is_dir($import_dir)){
             mkdir($import_dir, 0777);
        }

        foreach ($collection as $product)
        {
            //get the path of the Feefo
            $xml_path = $this->getProductXMLLink($product);
            //get the sku of the product
            //$sku = $product['sku'];
            $getFeed = $xml_path;
            $xml[] = $getFeed;
        }
        //echo "<pre>"; print_r($xml); die();
        $count = count($xml);
        try {
            foreach ($xml as $feed){
                $need = simplexml_load_file($feed);
                if($count > 1000) {
                    sleep(1);
                }
                foreach( $need->SUMMARY as $summary ) {
                    $content [] = $summary;
                }
            }

            $productCount = count($content);

            $xmlContent = new SimpleXMLElement('<ROOT/>');

            for ($i = 0; $i < $productCount; ++$i) {
                $track = $xmlContent->addChild('FEEDBACKLIST');
                if($content[$i]->COUNT > 0) {
                    $count = $content[$i]->SERVICEBAD + $content[$i]->PRODUCTBAD + $content[$i]->SERVICEPOOR + $content[$i]->PRODUCTPOOR + $content[$i]->SERVICEGOOD + $content[$i]->PRODUCTGOOD + $content[$i]->SERVICEEXCELLENT + $content[$i]->PRODUCTEXCELLENT;
                    $bad = ($content[$i]->SERVICEBAD + $content[$i]->PRODUCTBAD)*2;
                    $poor = ($content[$i]->SERVICEPOOR + $content[$i]->PRODUCTPOOR)*3;
                    $good = ($content[$i]->SERVICEGOOD + $content[$i]->PRODUCTGOOD)*4;
                    $excellent = ($content[$i]->SERVICEEXCELLENT + $content[$i]->PRODUCTEXCELLENT)*5;
                    $total = $bad + $poor + $good + $excellent;
                    $average = (round(($total/$count)*2, 0, PHP_ROUND_HALF_UP)/2)*20;
                } else {
                    $average = $content[$i]->AVERAGE;
                }
                $track->addChild('AVERAGE', $average);
                $track->addChild('COUNT', $content[$i]->COUNT);
                $track->addChild('VENDORREF', $content[$i]->VENDORREF);
            }

            //write the xml content to file and save it
            $fileOpen = fopen($product_review, 'w+') or die("Can`t open file");
            fwrite($fileOpen, $xmlContent->asXML());
            fclose($fileOpen);

             Mage::log("Hello, Product Review import successfully");
            //send the email if the cron finished
            if(filesize($product_review) > 0) {
                $adminEmail = Mage::getStoreConfig('trans_email/ident_general/email');
                $to = $adminEmail;
                $subject = "FeeFo Review Imported";
                $body = "Hello, \n\n Products Reviews has been imported";
                $body .= date('l jS \of F Y h:i:s A');

                if(mail($to, $subject, $body)){
                     Mage::log("Mail successfully send to admin");
                } else {
                     Mage::log("Message Email Delivery Failed");
                }
            }

        } catch (Exception $e) {
             Mage::log("GET product XML path is not working");
        }
    }

    //function for update crons
    public function updateReviews(Mage_Cron_Model_Schedule $schedule)
    {
        //file name for reviews save
        $product_review = Mage::getBaseDir('var') . DS . 'import' . DS . $this->_fileName;
        //get store id
        //$store_id = Mage::app()->getStore()->getStoreId();

        if(file_exists($product_review))
        {
            //load up local xml file for processing
            $feed_xml = simplexml_load_file($product_review);
            //$filterData = array('type'=>'simple');

            //get all products collection from our db
            $collection = Mage::getModel('catalog/product')->getCollection();
            $collection->addStoreFilter(2);

            foreach($collection as $product) {
                $sku = $product['sku'];
                //search in xml
                $search_xml = "//FEEDBACKLIST[VENDORREF='$sku']";
                $summary = $feed_xml->xpath($search_xml);

                 if(!empty($summary))
                 {
                     foreach ($summary as $review)
                     {
                        $count =  $review->COUNT;
                        $average = $review->AVERAGE;
                        try {
                            $get_item = Mage::getModel('catalog/product')->loadByAttribute('sku', $sku);
                            $entityID = $get_item->getEntityId();
                            $product = Mage::getModel('catalog/product')->load($entityID);
                            $product->setData('feefo_reviews_average', $average)->getResource()->saveAttribute($product, 'feefo_reviews_average');
                            $product->setData('feefo_reviews_count', $count)->getResource()->saveAttribute($product, 'feefo_reviews_count');

                            Mage::log("Review update - successful");

                        } catch (Exception $e) {
                            Mage::log("Review update - Cannot retrieve products from Magento: " . $e->getMessage());
                            return;
                        }
//                         //Get the product Ids
//                         $getProductID = Mage::getResourceModel('catalog/product_collection')
//                                            ->addAttributeToFilter('sku', $sku)
//                                            ->getAllIds();
//
//                         $_product = Mage::getModel('catalog/product')->load($getProductID);
//                         $storeIds = $_product->getStoreIds();
//                     //    var_dump($storeIds);
//                         //array of attribute value for update
//                         $attributeData = array(
//                                                'feefo_reviews_average' => $average,
//                                                'feefo_reviews_count'=> $count
//                                         );
//
//                                     //    var_dump($attributeData);
//                         //update the attribute for given (sku) product
//                         Mage::getSingleton('catalog/product_action')
//                               ->updateAttributes($getProductID, $attributeData, $storeIds);
                     }
                      Mage::log("Reviews updated");

                 } else {
                     Mage::log("No any Reviews found");
                 }
            }

            //notify by email
            $adminEmail = Mage::getStoreConfig('trans_email/ident_general/email');
            $to = $adminEmail;
            $subject = "Feed Processed";
            $body = "Reviews Updated";
            if(mail($to, $subject, $body))
            {
                 Mage::log("Email was send, update");
            } else {
                 Mage::log("Email failed");
            }
            
            rename($product_review, $product_review . '.' . date('YmdHi', time()) );

        } else {
            Mage::log("Product Review file is not Exists");
        }

    }

    //override the getXML link
     public function getProductXMLLink($product){
        $link = 'http://www.feefo.com/feefo/xmlfeed.jsp?';

        if(Mage::getStoreConfig('flint_feefo/general/logon')){
            $link .= '&logon='.Mage::getStoreConfig('flint_feefo/general/logon');
        }
        if(Mage::getStoreConfig('flint_feefo/product/mode')){
            $link .= '&mode='.Mage::getStoreConfig('flint_feefo/product/mode');
        }
        if($product->getSku()){
            $link .= '&vendorref='.$product->getSku();
        }

        return $link;
    }


}
