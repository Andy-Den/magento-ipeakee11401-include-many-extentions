<?php
class AHT_Tradeprice_Model_Observer extends Varien_Event_Observer
{
	public function modifyPrice(Varien_Event_Observer $observer)
	{
		$item = $observer->getQuoteItem();
		$item = ( $item->getParentItem() ? $item->getParentItem() : $item );
		$productId = $item->getProductId();
		$_product = Mage::getModel('catalog/product')->load($productId);
		if (Mage::helper('tradegroup')->_isCustomerGroupTrade()) 
		{
			if($item->getProduct()->getPrice_trade()>0)
			{
				$price =  $item->getProduct()->getPrice_trade();			
				$item->setCustomPrice($price);
				$item->setOriginalCustomPrice($price);
				$item->getProduct()->setIsSuperMode(true);
			}
		}
	}
    public function saveCmsPageObserve($observer)
	{
		$event = $observer->getEvent();
		$product = $event->getProduct();   
		// process percentage discounts only for simple products     
		if (Mage::helper('tradegroup')->_isCustomerGroupTrade()) 
		{
			if($product->getPrice_trade()>0)
			{
				$price =  $product->getPrice_trade();
				$finalPriceNow = $product->getData('final_price');
				$product->setFinalPrice($price); // set the product final price
			}
			else
			{
				$price =  $product->getPrice();
				$finalPriceNow = $product->getData('final_price');
				$product->setFinalPrice($price); // set the product final price
			}
		}   
		return $this;
    }
}
?>