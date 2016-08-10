<?php
class Balance_Ajax_IndexController
	extends Mage_Core_Controller_Front_Action
{
	protected $_eventObject = 'balance_ajax';
	
	protected function _construct() {
		$this->setFlag('fetchview', self::FLAG_NO_PRE_DISPATCH, true);
		$this->setFlag('fetchview', self::FLAG_NO_POST_DISPATCH, true);
	}
	 
	public function fetchviewAction()
	{
		//Do nothing for wrong method request
		if (! $this->getRequest()->isPost()) {
			return;
		}
		
		$response = $this->getResponse();
		$transportResponse = new Balance_Ajax_Controller_Request_Http();
		
		$ref_url = $this->_getRefererUrl();
		
		if ($this->_isUrlInternal($ref_url)) {
			$host = parse_url($ref_url, PHP_URL_HOST);
			$start = stripos($ref_url, $host) + strlen($host);
			$ref_url = substr($ref_url, $start);
			$ref_url = (substr($ref_url, 0, 1) == '/' ? '' : '/') . $ref_url;
			$_SERVER['REQUEST_URI'] = $ref_url;
		}else{
			$_SERVER['REQUEST_URI'] = '/';
		}

		//added handle so we can use 'ajax="true"' within these handles
		$p = $this->getRequest()->getParams();
		$p = is_array($p) ? array_shift($p) : array('module'=>'','controller'=>'','action'=>'');
		$key = $p['module'] .'_' . $p['controller'] . '_' . $p['action'];
		
		//save last visited category id for varnish
		if ('catalog_category_view' == $key && isset($p['params']['id']) ) {
			$categoryId = intval($p['params']['id']);
			if ($categoryId != 0 ) {
				Mage::getSingleton('catalog/session')->setLastVisitedCategoryId($categoryId);
			}
		}
		
		//using ajax block on product detail page
		if (in_array($key, array('catalog_product_view'))) {
			$update = $this->getLayout()->getUpdate();
			$update->addHandle($key);
		}

		$this->loadLayout();
		
		Mage::dispatchEvent($this->_eventObject.'_dispatch_before', array('request' => $this->getRequest(), 'response'=>$transportResponse));
		//to json 
		$transportResponse->transport($response);		
		Mage::dispatchEvent($this->_eventObject.'_dispatch_after', array('request' => $this->getRequest(), 'response'=>$response));

	}
}
