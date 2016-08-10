<?php
class Balance_Googletrustedstore_Helper_Data extends Mage_Core_Helper_Abstract
{
	const XML_PATH_ACTIVE  = 'google/trusted_store/active';
	const XML_PATH_ACCOUNT = 'google/trusted_store/account';
	const XML_PATH_FEED_ACCOUNT = 'google/feed/account';
    const XML_PATH_PRODUCTATTR = 'google/trusted_store/productattr';
	
	public function isGoogleTrustedStoreAvailable() 
	{
		$accountId = Mage::getStoreConfig(self::XML_PATH_ACCOUNT);
		return $accountId && Mage::getStoreConfigFlag(self::XML_PATH_ACTIVE);
	}

	public function getOfferId() {
        if($_product = Mage::registry('current_product')) {
            $attribute = strtolower(Mage::getStoreConfig(self::XML_PATH_PRODUCTATTR));
            $product = Mage::getModel('catalog/product')->load($_product->getId());
            $value = $product->getData($attribute);

            return $value;
        } else {
            return false;
        }
	}

    public function getShoppingId($productId) {
        $attribute = strtolower(Mage::getStoreConfig(self::XML_PATH_PRODUCTATTR));
        $product = Mage::getModel('catalog/product')->load($productId)->getData($attribute);

        return $product;
    }
	
	public function getFeedAccountId() {
		return Mage::getStoreConfig(self::XML_PATH_FEED_ACCOUNT);
	}
	
	public function getLastOrderId() {
		return Mage::getSingleton('checkout/session')->getLastOrderId();
	}
	
	public function getLastOrder() {
		$_orderId = $this->getLastOrderId();
		
		if (empty($_orderId)) return false;
		
		return Mage::getModel('sales/order')->load($_orderId);
	}
	
	public function getOrderItems($order) 
	{
		if (empty($order) || !$order->getId()) return array();

		return $order->getAllItems();
	}
	
	public function getDomain()
	{
		$domain = Mage::app()->getStore()->getBaseUrl();
		
		$domain = str_replace('http://', '', $domain);
		$domain = str_replace('https://', '', $domain);
		list($domain) = explode('/', $domain);
		return $domain;
	}
	
	public function getCustomerCountry($order)
	{
		$address = $order->getBillingAddress();
		$address = empty($address) ? $order->getShippingAddress() : $address;
		$country = empty($address) ? false : $address->getCountryId();
		
		return empty($country) ? Mage::getStoreConfig('general/country/default') : $country;
	}
	
	public function getShipDate($items)
	{
		$result = '';
		$time = strtotime('+100 year');
		//get earliest dispatch date
		foreach($items as $item) 
		{
			$dispatch = $item->getDispatchDate();
			if (empty($dispatch)) continue;
			
			$dispatch = strtotime($dispatch);
			
			if ($dispatch < $time) {
				$time = $dispatch;
				$result = $item->getDispatchDate();
			}
		}
		
		return empty($result) ? date('Y-m-d') : date('Y-m-d', $time);
	}

	public function getDeliveryDate($items, $date='')
	{
		$date = empty($date) ? $this->getShipDate($items) : $date;
		return date('Y-m-d', strtotime($date) + 86400 * 10);
	}
    /**
     * getShipDateNew
     * @param $items
     * @return bool|string
     * MID-280
     */
    public function getShipDateNew($items)
    {
        $date = $this->getShipDate($items);
        $result = $this->dateFromBusinessDays(2, strtotime($date));
        return $result;
    }

    /**
     * getDeliveryDateNew
     * @param $date
     * @return bool|string
     * MID-280
     */

    public function getDeliveryDateNew($date)
    {
        $result = $this->dateFromBusinessDays(10, strtotime($date));
        return $result;
    }

    /**
     * dateFromBusinessDays
     * @param $days
     * @param null $dateTime
     * @return bool|string
     * MID-280
     */
    function dateFromBusinessDays($days, $dateTime=null) {
        $dateTime = is_null($dateTime) ? time() : $dateTime;
        $_day = 0;
        $_direction = $days == 0 ? 0 : intval($days/abs($days));
        $_day_value = (60 * 60 * 24);

        while($_day !== $days) {
            $dateTime += $_direction * $_day_value;

            $_day_w = date("w", $dateTime);
            if ($_day_w > 0 && $_day_w < 6) {
                $_day += $_direction * 1;
            }
        }

        return date('Y-m-d', $dateTime);
    }
	
	public function getBackorderFlag($items)
	{
		/*$productIds = array();
		foreach($items as $item) {
			$productIds = $item->getProductId();
		}
		
		$collection = Mage::getModel('catalog/product')->getResourceCollection()
		->addAttributeToSelect(array('custom_stock_status', 'preorder_calender'))
		->addIdFilter($productIds);
		
		$flagday = Mage::app()->getLocale()->date()->addDay(45);
		
		foreach($collection as $product) {
			$txt = $product->getAttributeText('custom_stock_status');

			if (empty($txt)) continue;
			
			if (stripos($txt, 'expected shipment date') !==false) {
				$preorderDate = $product->getPreorderCalender();
				
				if (empty($preorderDate)) continue;
				
				if ($flagday->compare(strtotime($preorderDate)) < 0) return 'Y';
			}
			elseif (stripos($txt, 'in stock') !== false) {
				continue;
			}
			elseif (stripos($txt, 'sold out') !== false || stripos($txt, 'out of stock') !== false) {
				continue;
			}
		}*/
		
		return 'N';
	}
	
	public function getDigitalFlag($items)
	{
		return 'N';
	}
}