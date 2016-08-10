<?php
/**
 *  extension for Magento
 *
 * Long description of this file (if any...)
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade
 * the Godfreys Ajax module to newer versions in the future.
 * If you wish to customize the Godfreys Ajax module for your needs
 * please refer to http://www.magentocommerce.com for more information.
 *
 * @category   Godfreys
 * @package    Godfreys_Ajax
 * @copyright  Copyright (C) 2012 Balance Internet (http://www.balanceinternet.com.au)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Short description of the class
 *
 * Long description of the class (if any...)
 *
 * @category   Godfreys
 * @package    Godfreys_Ajax
 * @subpackage Model
 * @author     Richard Cai <richard@balancenet.com.au>
 */
 class Godfreys_Ajax_Model_Observer
 {
 	/**
 	 * ajax shopping cart content
 	 * @param Varien_Event_Observer $observer
 	 */
 	public function ajaxShoppingCart(Varien_Event_Observer $observer)
 	{
 		$request = $observer->getRequest();
 		$response = $observer->getResponse();
 		$type = $request->getParam('cart_sidebar');
 		if($type)
 		{
 			$cart = Mage::app()->getLayout()->createBlock('checkout/cart_sidebar');
 			$cart->setTemplate('checkout/cart/mini.phtml');
 			$cart->addItemRender('simple','checkout/cart_item_renderer','checkout/cart/sidebar/default.phtml');
 			$cart->addItemRender('grouped','checkout/cart_item_renderer_grouped','checkout/cart/sidebar/default.phtml');
 			$cart->addItemRender('configurable','checkout/cart_item_renderer_configurable','checkout/cart/sidebar/default.phtml');
 			
 			$response->setData($type['name'], $cart->toHtml());
 		}
 	}
 	
 	/**
 	 * ajax global message
 	 * @param Varien_Event_Observer $observer
 	 */
 	public function ajaxGlobalMessage(Varien_Event_Observer $observer)
 	{
 		$request = $observer->getRequest();
 		$response = $observer->getResponse();
 		$type = $request->getParam('ajax_global_messages');
 		if($type)
 		{
 			$msg_block = Mage::app()->getLayout()->getMessagesBlock();

 			$msg_block->addMessages(Mage::getSingleton('core/session')->getMessages(true));
 			$msg_block->addMessages(Mage::getSingleton('checkout/session')->getMessages(true));
 			$msg_block->addMessages(Mage::getSingleton('catalog/session')->getMessages(true));
 			$msg_block->addMessages(Mage::getSingleton('customer/session')->getMessages(true));
 			$msg_block->addMessages(Mage::getSingleton('wishlist/session')->getMessages(true));
 			
 			$html = $msg_block->toHtml();
 			$html = strlen($html) > 0 ? '<div class="messages-container">' . $html . '</div>' : '';

			//get global message
 			$response->setData($type['name'], $html);
 		}
 	}
 	
 	/**
 	 * ajax global message
 	 * @param Varien_Event_Observer $observer
 	 */
 	public function ajaxName(Varien_Event_Observer $observer)
 	{
 		$request = $observer->getRequest();
 		$response = $observer->getResponse();
 		//use block's alias name as parameter's name
 		$type = $request->getParam('welcome');
 		if($type)
 		{
 			//get customer name
 			$welcome = Mage::app()->getLayout()->createBlock('page/html_header')->getWelcome();
 			$welcome = strlen($welcome) > 0 ? '<p>' . $welcome . '</p>' : '';

 			$response->setData($type['name'], $welcome);
 		}
 	}
 	
 	public function ajaxAccountLinks(Varien_Event_Observer $observer)
 	{
 		$request = $observer->getRequest();
 		$response = $observer->getResponse();
 		
 		$type = $request->getParam('account.links');
 		if ($type)
 		{
 			$block = Mage::app()->getLayout()->createBlock('page/template_links');
 			$block->setTemplate('page/template/links.phtml');
 			$response->setData($type['name'], $block->toHtml());
 		}
 	}
 	
 	public function ajaxCompareSidebar(Varien_Event_Observer $observer)
 	{
 	 	$request = $observer->getRequest();
 		$response = $observer->getResponse();

 		$sidebar_blocks = array('catalog.compare.sidebar','wishlist_sidebar','left.reports.product.viewed', 'utility.links');
 		
 		foreach($sidebar_blocks as $block_name) {
 			$type = $request->getParam($block_name);
 			if ($type)
 			{
	 			$block = Mage::app()->getLayout()->createBlock($type['type']);
 				$block->setTemplate($type['template']);
 				$response->setData($type['name'], $block->toHtml());
 			}
 		}
 	}
        
        public function ajaxTopBar(Varien_Event_Observer $observer) {
            $request = $observer->getRequest();
 		$response = $observer->getResponse();

 		$sidebar_blocks = array('smhshop.avatar', 'smhshop.topbar.links');
 		
 		foreach($sidebar_blocks as $block_name) {
 			$type = $request->getParam($block_name);
 			if ($type)
 			{
	 			$block = Mage::app()->getLayout()->createBlock($type['type']);
 				$block->setTemplate($type['template']);
 				$response->setData($type['name'], $block->toHtml());
 			}
 		}
        }
 	
 }