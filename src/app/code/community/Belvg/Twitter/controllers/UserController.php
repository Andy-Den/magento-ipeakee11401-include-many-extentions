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
 * @package    Belvg_Twitterconnect
 * @copyright  Copyright (c) 2010 - 2011 BelVG LLC. (http://www.belvg.com)
 * @license    http://store.belvg.com/BelVG-LICENSE-COMMUNITY.txt
 */?>
<?php




class Belvg_Twitter_UserController extends Mage_Core_Controller_Front_Action
{

    private $consumer_key;
    private $consumer_secret;
    private $oauth_callback;

    public function _init(){
        $this->consumer_secret = Mage::getStoreConfig('twitter/userauth/conssecret');
	$this->consumer_key  = Mage::getStoreConfig('twitter/userauth/conskey');
	$this->oauth_callback = Mage::getStoreConfig('twitter/userauth/oauthcallback');
        require_once(getcwd().'/lib/twitteroauth/twitteroauth.php');    
    }

    public function indexAction(){
        $this->loadLayout();
        $this->renderLayout();
    }

    public function loginAction(){
        $this->_init();     
        if (empty($_SESSION['access_token']) || empty($_SESSION['access_token']['oauth_token']) || empty($_SESSION['access_token']['oauth_token_secret'])) {
            $this->_redirect('*/*/clearsession/');
        }
        /* Get user access tokens out of the session. */
        $access_token = $_SESSION['access_token'];

        /* Create a TwitterOauth object with consumer/user tokens. */
        $connection = new TwitterOAuth($this->consumer_key, $this->consumer_secret, $access_token['oauth_token'], $access_token['oauth_token_secret']);

        /* If method is set change API call made. Test is called by default. */
        $content = $connection->get('account/verify_credentials');       
        if ($content->id){
            $this->userLog($content);
            echo '<script>window.opener.location.href=window.opener.location .href;
                window.opener.focus();
                window.close();</script>';
        }
         
    }

    public function clearsessionAction(){
        //unset($_SESSION['access_token']);
        $this->_redirect('*/*/connect/');
    }

    public function connectAction(){
        $this->_init();
        if ($this->consumer_key === '' || $this->consumer_secret === '') {
          echo 'You need a consumer key and secret to test the sample code. Get one from <a href="https://twitter.com/apps">https://twitter.com/apps</a>';
          exit;
        }
        
        $connection = new TwitterOAuth($this->consumer_key, $this->consumer_secret);

        /* Get temporary credentials. */
        $request_token = $connection->getRequestToken();        
        /* Save temporary credentials to session. */
        $_SESSION['oauth_token'] = $token = $request_token['oauth_token'];
        $_SESSION['oauth_token_secret'] = $request_token['oauth_token_secret'];

        /* If last connection failed don't display authorization link. */
        switch ($connection->http_code) {
          case 200:
            /* Build authorize URL and redirect user to Twitter. */
            $url = $connection->getAuthorizeURL($token);
            $this->_redirectUrl($url);
            break;
          default:
            /* Show notification if something went wrong. */
            echo 'Could not connect to Twitter. Refresh the page or try again later.';
        }
    }

    public function callbackAction(){
        $this->_init();
        /* If the oauth_token is old redirect to the connect page. */
        if (isset($_REQUEST['oauth_token']) && $_SESSION['oauth_token'] !== $_REQUEST['oauth_token']) {
          $_SESSION['oauth_status'] = 'oldtoken';
          $this->_redirect('*/*/clearsession/');
        }

        /* Create TwitteroAuth object with app key/secret and token key/secret from default phase */
        $connection = new TwitterOAuth($this->consumer_key, $this->consumer_secret, $_SESSION['oauth_token'], $_SESSION['oauth_token_secret']);

        /* Request access tokens from twitter */
        $access_token = $connection->getAccessToken($_REQUEST['oauth_verifier']);

        /* Save the access tokens. Normally these would be saved in a database for future use. */
        $_SESSION['access_token'] = $access_token;

        /* Remove no longer needed request tokens */
        unset($_SESSION['oauth_token']);
        unset($_SESSION['oauth_token_secret']);

        /* If HTTP response is 200 continue otherwise send to connect page to retry */
        if (200 == $connection->http_code) {
          /* The user has been verified and the access tokens can be saved for future use */
          $_SESSION['status'] = 'verified';
          $this->_redirect('*/*/login/');
        } else {
          /* Save HTTP status for error dialog on connnect page.*/
          $this->_redirect('*/*/clearsession/');
        }
    }

    private function userLog($_data){
        $result = Mage::getModel('twitter/main')->checkExist($_data->id);
        if (!$result)
            $this->createTwitterUser($_data);
        else
            $this->loginTwitterUser($_data);       

    }

    private function createTwitterUser($_data){        
        $customer = Mage::getModel('customer/customer');
        $password = '';
        $email = $_data->screen_name;

        $customer->setWebsiteId(Mage::app()->getWebsite()->getId());        
        $customer->setEmail($email);
        $customer->setFirstname($_data->name);
        $customer->setLastname('');
        $customer->setPassword($password);
        try {
            $customer->save();
            $customer->setConfirmation(null);
            $customer->save();
            Mage::getModel('twitter/main')->addTwitterAccount($customer->getId(),$_data->id);
            Mage::getSingleton('customer/session')->loginById($customer->getId());
        }
        catch (Exception $ex) {
            echo $ex->getMessage();die;
        }
    }

    private function loginTwitterUser($_data){
        $rel = Mage::getModel('twitter/main')->getTwitterRel($_data->id);
        if ($rel['user_id'])
            Mage::getSingleton('customer/session')->loginById($rel['user_id']);
        else
            $this->createTwitterUser ($_data);
    }

}