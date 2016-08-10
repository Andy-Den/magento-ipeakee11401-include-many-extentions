<?php
/**
 * BelVG LLC.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://store.belvg.com/BelVG-LICENSE-COMMUNITY.txt
 *
 /***************************************
 *         MAGENTO EDITION USAGE NOTICE *
 *****************************************/
 /* This package designed for Magento COMMUNITY edition
 * BelVG does not guarantee correct work of this extension
 * on any other Magento edition except Magento COMMUNITY edition.
 * BelVG does not provide extension support in case of
 * incorrect edition usage.
 /***************************************
 *         DISCLAIMER   *
 *****************************************/
 /* Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future.
 *****************************************************
 * @category   Belvg
 * @package    Belvg_FacebookAll
 * @copyright  Copyright (c) 2010 - 2011 BelVG LLC. (http://www.belvg.com)
 * @license    http://store.belvg.com/BelVG-LICENSE-COMMUNITY.txt
 */

include_once("Mage/Wishlist/controllers/IndexController.php");

class Belvg_FacebookAll_Wishlist_IndexController extends Mage_Wishlist_IndexController
{
    /**
     * Adding new item
     */
 
    public function addAction()
    {
        $session = Mage::getSingleton('customer/session');
        $wishlist = $this->_getWishlist();
        if (!$wishlist) {
            $this->_redirect('*/');
            return;
        }

        $productId = (int) $this->getRequest()->getParam('product');
        if (!$productId) {
            $this->_redirect('*/');
            return;
        }

        $product = Mage::getModel('catalog/product')->load($productId);
        if (!$product->getId() || !$product->isVisibleInCatalog()) {
            $session->addError($this->__('Cannot specify product.'));
            $this->_redirect('*/');
            return;
        }

        try {
            $buyRequest = new Varien_Object($this->getRequest()->getParams());

            $result = $wishlist->addNewItem($product, $buyRequest);
            if (is_string($result)) {
                Mage::throwException($result);
            }
            $wishlist->save();

            Mage::dispatchEvent(
                'wishlist_add_product',
                array(
                    'wishlist'  => $wishlist,
                    'product'   => $product,
                    'item'      => $result
                )
            );

            $referer = $session->getBeforeWishlistUrl();
            if ($referer) {
                $session->setBeforeWishlistUrl(null);
            } else {
                $referer = $this->_getRefererUrl();
            }

            /**
             *  Set referer to avoid referring to the compare popup window
             */
            $session->setAddActionReferer($referer);

            Mage::helper('wishlist')->calculate();

            $message = $this->__('%1$s has been added to your wishlist. Click <a href="%2$s">here</a> to continue shopping', $product->getName(), $referer);
            $session->addSuccess($message);
            
	/***/
            if (Mage::getStoreConfig('facebookall/wishlist/enabled')) {
                    $facebook = new Facebook_Api(array(
                      'appId'  => Mage::getStoreConfig('facebookall/settings/appid'),
                      'secret' => Mage::getStoreConfig('facebookall/settings/secret'),
                      'cookie' => true,
                    ));
                    //$fb_session = $facebook->getSession();
					$cookie = $this->get_facebook_cookie(Mage::getStoreConfig('facebookall/settings/appid'), Mage::getStoreConfig('facebookall/settings/secret'));

                    $me = null;
                    if ($cookie) {
                      try {
                        $uid = $facebook->getUser();
			    $message = str_replace('{product}', $product->getName(), Mage::getStoreConfig('facebookall/wishlist/note'));
                            $feed_data = array('message'=>$message,
                                                               'link'=>$product->getProductUrl(),
                                                               'picture'=>	$product->getImageUrl(),
                                                               'name'=>$product->getName(),
                                                               'caption'=>'',
                                                               'description'=>$product->getShortDescription(),
                                                               'access_token'=>$cookie['access_token']);

                        $me = $facebook->api('/me/feed/','post', $feed_data);
                      } catch (Facebook_Exception $e) {
                        error_log($e);
                      }
                    }
            }
	/****/            
        }
        catch (Mage_Core_Exception $e) {
            $session->addError($this->__('An error occurred while adding item to wishlist: %s', $e->getMessage()));
        }
        catch (Exception $e) {
            mage::log($e->getMessage());
            $session->addError($this->__('An error occurred while adding item to wishlist.'));
        }

        $this->_redirect('*');
    }
    	
		
    private function get_facebook_cookie($app_id, $app_secret)
    {
        if ($_COOKIE['fbsr_' . $app_id] != '') {
            return $this->get_new_facebook_cookie($app_id, $app_secret);
        } else {
            return $this->get_old_facebook_cookie($app_id, $app_secret);
        }
    }

    private function get_old_facebook_cookie($app_id, $app_secret)
    {
        $args = array();
        parse_str(trim($_COOKIE['fbs_' . $app_id], '\\"'), $args);
        ksort($args);
        $payload = '';
        foreach ($args as $key => $value) {
            if ($key != 'sig') {
                $payload .= $key . '=' . $value;
            }
        }
        if (md5($payload . $app_secret) != $args['sig']) {
            return array();
        }
        return $args;
    }

    private function get_new_facebook_cookie($app_id, $app_secret)
    {
        $signed_request = $this->parse_signed_request($_COOKIE['fbsr_' . $app_id], $app_secret);
        // $signed_request should now have most of the old elements
        $signed_request['uid'] = $signed_request['user_id']; // for compatibility 
        if (!is_null($signed_request)) {
            // the cookie is valid/signed correctly
            // lets change "code" into an "access_token"
            $access_token_response = $this->getFbData("https://graph.facebook.com/oauth/access_token?client_id=$app_id&redirect_uri=&client_secret=$app_secret&code=$signed_request[code]");
			parse_str($access_token_response);
			$signed_request['access_token'] = $access_token;
			$signed_request['expires'] = time() + $expires;
        }
        return $signed_request;
    }

    private function parse_signed_request($signed_request, $secret)
    {
        list($encoded_sig, $payload) = explode('.', $signed_request, 2);

        // decode the data
        $sig = $this->base64_url_decode($encoded_sig);
        $data = json_decode($this->base64_url_decode($payload), true);

        if (strtoupper($data['algorithm']) !== 'HMAC-SHA256') {
            error_log('Unknown algorithm. Expected HMAC-SHA256');
            return null;
        }

        // check sig
        $expected_sig = hash_hmac('sha256', $payload, $secret, $raw = true);
        if ($sig !== $expected_sig) {
            error_log('Bad Signed JSON signature!');
            return null;
        }

        return $data;
    }

    private function base64_url_decode($input)
    {
        return base64_decode(strtr($input, '-_', '+/'));
    }
    	
	private function getFbData($url)
	{
		$data = null;

		if (ini_get('allow_url_fopen') && function_exists('file_get_contents')) {
			$data = file_get_contents($url);
		} else {
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			$data = curl_exec($ch);
		}
		return $data;
	}

}
