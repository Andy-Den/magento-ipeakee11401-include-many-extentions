<?php
class Criteo_OneTag_Helper_Feed extends Mage_Core_Helper_Abstract {
    protected $_list = null;
 
    public function __construct() {
        $collection = Mage::getModel('catalog/product')->getCollection()->addAttributeToSelect('*');
        $this->setList($collection);
    }
 
    public function setList($collection){
        $this->_list = $collection;
    }
 
    public function generateFeed() {
        if (!is_null($this->_list)) {
			//Get settings
			$ctoObserver = 'criteo_onetag_parameters/observer';
			$ctoMage = Mage::getSingleton($ctoObserver);
			$ctoIdType = $ctoMage->ctoProductID();
			$ctoFeedPassword = Mage::helper('Criteo_OneTag')->get_feed_password();
			$ctoURLTracking = Mage::helper('Criteo_OneTag')->get_feed_url_tracking();
            
			//Password check
			if($_GET['password'] == $ctoFeedPassword) {
				//Get items in our shop
				$items = $this->_list->getItems();
				$total_items = count($items);
				
				//Display the header
				echo "id|name|producturl|smallimage|bigimage|description|price|retailprice|instock|categoryid1|categoryid2|categoryid3\n";
				
				$items_count = 0;
				foreach($items as $i) {
					//print_r($i);
					$items_count++;

					//ID - selects the id based on whether they use SKU or not
					if($ctoIdType == 0) {
						echo $i->getEntityId()."|";
					} else {
						echo $i->getSku()."|";
					}
					
					//Name
					echo htmlentities($i->getName(), ENT_QUOTES)."|";
					
					//Product url
					echo Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB).$i->getUrlPath()."".($ctoURLTracking == "" ? "" : "?".$ctoURLTracking)."|";
					
					//Small image
					$smallimage = $i->getSmallImage();
					if($smallimage == "no_selection") {
						//We don't want to add no_selection in our feed
						echo "|";
					} else {
						echo Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA)."catalog/product".$smallimage."|";
					}
					
					//Big image
					$bigimage = $i->getImage();
					if($bigimage == "no_selection") {
						//We don't want to add no_selection in our feed
						echo "|";
					} else {
						echo Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA)."catalog/product".$bigimage."|";
					}
					
					//Description
					echo htmlentities($i->getDescription(), ENT_QUOTES)."|";
					
					//Prices
					$price = $i->getPrice();
					$specialprice = $i->getSpecialPrice();
					if($specialprice != "") {
						//We have a retail price
						echo $specialprice."|".$price."|";
					} else {
						//No retail price
						echo $price."||";
					}
					
					//Instock - isAvailable is used to mark if something is in stock or not
					echo $i->isAvailable()."|";
					
					//Categories - goes as deep as possible, and then back up to the next root category
					$categories = $i->getCategoryIds();
					$categories_counter = 0;
					foreach($categories as $c) {
						$category = Mage::getModel('catalog/category')->load($c);
						if($categories_counter < 3 && $category->getIsActive()) {
							echo ($categories_counter == 0 ? "" : "|").$category->getName();
						}
						$categories_counter++;
					}
					while($categories_counter < 3) {
						echo "|";
						$categories_counter++;
					}
					
					//Blank line
					if($items_count < $total_items) {
						echo "\n";
					}
				}
			}
        }
    }
}
?>