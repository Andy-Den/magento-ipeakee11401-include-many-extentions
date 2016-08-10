<?php
class Criteo_OneTag_Model_Page_Observer extends Mage_Catalog_Block_Product_List {
	/*
	 * * Internal variables
	 * */
	protected $_ctoUserId           = null;
	protected $_ctoSearchQuery      = null;
	protected $_ctoTransactionId    = null;
	protected $_ctoProductSku       = null;
	protected $_ctoProductId        = null;
	protected $_ctoProductList      = null;
	protected $_ctoBasketList       = null;
	protected $_ctoCheckoutList     = null;
	protected $_ctoConfirmationList = null;
	protected $_ctoListingList      = null;
	
	/*
	 * * Shortcuts to Controllers
	 * */
	protected function _ctoGetRequest() {
		return Mage::app()->getFrontController()->getRequest();
	}
	protected function _ctoGetControllerName() {
		return $this->_ctoGetRequest()->getControllerName();
	}
	protected function _ctoGetActionName() {
		return $this->_ctoGetRequest()->getActionName();
	}
	protected function _ctoGetModuleName() {
		return $this->_ctoGetRequest()->getModuleName();
	}
	protected function _ctoGetCustomer() {
		return Mage::helper('customer')->getCustomer();
	}
	protected function _ctoGetCurrentProduct() {
		return Mage::registry('current_product');
	}
	protected function _ctoGetProduct($productId) {
		return Mage::getModel('catalog/product')->load($productId);
	}
	protected function _ctoGetCheckoutCart() {
		return Mage::getSingleton('checkout/cart');
	}
	protected function _ctoGetCheckoutSession() {
		return Mage::getSingleton('checkout/session');
	}
	protected function _ctoGetSalesOrder() {
		return Mage::getModel('sales/order');
	}
	
	protected function _ctoGetPartnerID() {
		return Mage::helper('Criteo_OneTag')->get_partner_id();
	}
	protected function _ctoGetCrossDevice() {
		return Mage::helper('Criteo_OneTag')->get_cross_device();
	}
	protected function _ctoGetProductID() {
		return Mage::helper('Criteo_OneTag')->get_product_id();
	}

	/*
	 * * Create Block 
	 * */
	protected function _ctoCreateBlock() {
		$layout = Mage::app()->getLayout();
		$block = $layout->createBlock('Criteo_OneTag_Block_TagBlock');
	}

	
	/*
	 * * Determine page type 
	 * */
	public function ctoIsHomePage() {
		if (Mage::app()->getRequest()->getRequestString() == "/") {
			return true;
		} else {
			return false;
		}
	}
	public function ctoIsCategoryPage() {
		if ($this->_ctoGetControllerName() == 'category') {
			return true;
		} else {
			return false;
		}
	}
	public function ctoIsSearchPage() {
		if ($this->_ctoGetModuleName() == 'catalogsearch') {
			return true;
		} else {
			return false;
		}
	}
	public function ctoIsProductPage() {
		if(Mage::registry('current_product')) {
			return true;
		} else {
			return false;
		}
	}
	public function ctoIsBasketPage() {
		$request = $this->_ctoGetRequest();
		$module = $request->getModuleName();
		$controller = $request->getControllerName();
		$action = $request->getActionName();
		$ctoIsConfirmationPage = $this->ctoIsConfirmationPage();
		if (!$ctoIsConfirmationPage && $module == 'checkout' && $controller == 'cart' && $action == 'index'){
			return true;
		} else {
			return false;
		}
	}
	public function ctoIsCheckoutPage() {
		$ctoIsBasketPage = $this->ctoIsBasketPage();
		$ctoIsConfirmationPage = $this->ctoIsConfirmationPage();
		if (!$ctoIsBasketPage && !$ctoIsConfirmationPage && strpos($this->_ctoGetModuleName(), 'checkout') !== false && $this->_ctoGetActionName() != 'success') {
			return true;
		} else {
			return false;
		}
	}
	public function ctoIsConfirmationPage() {
		if (
			(
				strpos($this->_ctoGetModuleName(), 'checkout') !== false 
				&& $this->_ctoGetActionName() == "success" 
			) || (
			       	strpos($_SERVER['REQUEST_URI'], 'checkout') !== false 
				&& strpos($_SERVER['REQUEST_URI'], 'success') !== false 
			)
		) {
			return true;
		} else {
			return false;
		}
	}
	
	public function ctoPartnerID() {
		return $this->_ctoGetPartnerID();
	}
	public function ctoCrossDevice() {
		return $this->_ctoGetCrossDevice();
	}
	public function ctoProductID() {
		return $this->_ctoGetProductID();
	}

	
	
	/*
	 * * Compute and give access to variables
	 * */
	protected function _ctoSetUserId() {
		$user = $this->_ctoGetCustomer();
		if($user->getEmail() == "") {
			$userId = "";
		} else {
			$userId = md5(mb_convert_encoding(trim(strtolower(str_replace('"',"",$user->getEmail()))), "UTF-8", "ISO-8859-1"));
		}
		$this->_ctoUserId = $userId;
	}
	public function ctoGetUserId() {
		return $this->_ctoUserId;
	}

	
	protected function _ctoSetSearchQuery() {
		if (isset($_GET['q'])) {
			$this->_ctoSearchQuery = $_GET['q'];
		}
	}
	public function ctoGetSearchQuery() {
		return $this->_ctoSearchQuery;
	}

	
	protected function _ctoSetTransactionId() {
		$orderId = $this->_ctoGetCheckoutSession()->getLastOrderId();
		if ($orderId) {
			$order = $this->_ctoGetSalesOrder()->load($orderId);
			$this->_ctoTransactionId = $order->getIncrementId();
		}
	}
	public function ctoGetTransactionId() {
		return $this->_ctoTransactionId;
	}

	
	protected function _ctoSetProductSku() {
		$currentProduct = $this->_ctoGetCurrentProduct();
		if (!$currentProduct) return false;
		$productSku = $currentProduct->getSku();
		$this->_ctoProductSku = $productSku;
	}
	public function ctoGetProductSku() {
		return $this->_ctoProductSku;
	}

	
	protected function _ctoSetProductId() {
		$currentProduct = $this->_ctoGetCurrentProduct();
		if (!$currentProduct) return false;
		$productId = $currentProduct->getId();
		$this->_ctoProductId = $productId;
	}
	public function ctoGetProductId() {
		return $this->_ctoProductId;
	}


	protected function _ctoGetProductInfo($product) {
		$productInfo = array();
		$productInfo['id'] = $product->getId();
		$productInfo['sku'] = $product->getSku();
		$productInfo['unit_original_price'] = (float) $product->getPrice();
		$productInfo['unit_final_price'] = (float) $product->getFinalPrice();
		return $productInfo;
	}
	
	
	protected function _ctoSetListingList() {
		$listingList = array();
		$origProductCollection = $this->getLoadedProductCollection();
		$productCollection = clone $origProductCollection;
		$productCollection->setPageSize(3,1); //Only fetch the first 3 products of page 1
		if(!$productCollection->count()) {
		       	// There are no products matching the selection.
			$listingList = null;
		} else {
			$itemNumber = 0;
			foreach ($productCollection as $_product) {
				$itemInfo = array();
				$itemInfo['sku'] = $_product->getSku();
				$itemInfo['id'] = $_product->getId();
			       	array_push($listingList, $itemInfo);
				$itemNumber++;
				if ( $itemNumber >= 3 ) {
					break;
				}
			}
		}
		$this->_ctoListingList = $listingList;
	}

	
	protected function _ctoSetBasketList() {
		$basketList = array();
		$cart = $this->_ctoGetCheckoutCart();
		$quote = $cart->getQuote();
		//Old: $items = $quote->getAllItems();
		$items = $quote->getAllVisibleItems();
		foreach($items as $item) {
			$productId = $item->getProductId();
			$product = $this->_ctoGetProduct($productId);
			$itemInfo = array();
			$itemInfo['product'] = $this->_ctoGetProductInfo($product);
			$itemInfo['quantity'] = (float) $item->getQty();
			array_push($basketList, $itemInfo);
		}
		$this->_ctoBasketList = $basketList;
	}

	
	protected function _ctoSetCheckoutList() {
		$checkoutList = array();
		$cart = $this->_ctoGetCheckoutSession();
		$quote = $cart->getQuote();
		//Old: $items = $quote->getAllItems();
		$items = $quote->getAllVisibleItems();
		foreach($items as $item) {
			$productId = $item->getProductId();
			$product = $this->_ctoGetProduct($productId);
			$itemInfo = array();
			$itemInfo['product'] = $this->_ctoGetProductInfo($product);
			$itemInfo['quantity'] = (float) $item->getQty();
			array_push($checkoutList, $itemInfo);
		}
		$this->_ctoCheckoutList = $checkoutList;
	}

	
	protected function _ctoSetConfirmationList() {
		$orderId = $this->_ctoGetCheckoutSession()->getLastOrderId();
		if ($orderId) {
			$confirmationList = array();
			$order = $this->_ctoGetSalesOrder()->load($orderId);
			//Old: $items = $order->getAllItems();
			$items = $order->getAllVisibleItems();
			foreach($items as $item) {
				$productId = $item->getProductId();
				$product = $this->_ctoGetProduct($productId);
				$itemInfo = array();
				$itemInfo['product'] = $this->_ctoGetProductInfo($product);
				$itemInfo['quantity'] = (float) $item->getQtyOrdered();
				array_push($confirmationList, $itemInfo);
			}
			$this->_ctoConfirmationList = $confirmationList;
		}
	}

	
	protected function _ctoSetProductList() {
		if ($this->ctoIsBasketPage()) {
			$this->_ctoSetBasketList();
			$productsFullList = $this->_ctoBasketList;
		} elseif ($this->ctoIsCheckoutPage()) {
			$this->_ctoSetCheckoutList();
			$productsFullList = $this->_ctoCheckoutList;
		} elseif ($this->ctoIsCategoryPage() || $this->ctoIsSearchPage()) {
			$this->_ctoSetListingList();
			$productsFullList = $this->_ctoListingList;
		} elseif ($this->ctoIsConfirmationPage()) {
			$this->_ctoSetConfirmationList();
			$productsFullList = $this->_ctoConfirmationList;
		}
		$this->_ctoProductList = $productsFullList;
	}
	public function ctoGetProductList() {
		return $this->_ctoProductList;
	}
	

	/*
	 * * Initialization
	 * */
	public function setCriteoParameters() {
		$this->_ctoSetUserId();
		$this->_ctoSetSearchQuery();
		
		if ($this->ctoIsCategoryPage() || $this->ctoIsBasketPage() || $this->ctoIsCheckoutPage() ) {
			$this->_ctoSetProductList();
		}
		
		if ($this->ctoIsProductPage()) {
			$this->_ctoSetProductSku();
			$this->_ctoSetProductId();
		}
		
		if ($this->ctoIsConfirmationPage()) {
			$this->_ctoSetProductList();
			$this->_ctoSetTransactionId();
		}
		
		$this->_ctoCreateBlock();
		return $this;
	}
}
?>
