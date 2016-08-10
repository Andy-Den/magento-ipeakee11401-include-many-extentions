<?php
/**
 * @category    Fishpig
 * @package     Fishpig_Wordpress
 * @license     http://fishpig.co.uk/license.txt
 * @author      Ben Tideswell <help@fishpig.co.uk>
 */

class Fishpig_Wordpress_Addon_Disqus_Block_Comments extends Fishpig_Wordpress_Block_Post_View_Comment_Wrapper
{
	/**
	 * Change the comments template
	 *
	 */
	protected function _beforeToHtml()
	{
		parent::_beforeToHtml();

		if ($this->isEnabled() && trim($this->getDisqusShortname()) !== '' && !$this->isPreview() && $this->isCommentsEnabled()) {
			$this->setTemplate('wordpress-addons/disqus/comments.phtml');
		}
	}
	
	/**
	 * Determine whether preview mode is enabled
	 *
	 * @return bool
	 */
	public function isPreview()
	{
		return $this->getRequest()->getParam('preview') === 'true';
	}

	/**
	 * Display nothing if comments are disabled
	 *
	 * @return null|string
	 */
	protected function _toHtml()
	{
		if ($this->isCommentsEnabled()) {
			return parent::_toHtml();
		}
		
		return null;
	}
	
	/**
	 * Determine whether Disqus is enabled
	 *
	 * @return bool
	 */
	public function isEnabled()
	{
		return Mage::getStoreConfigFlag('wordpress/extend/disqus') && $this->helper('wordpress')->isPluginEnabled('disqus');
	}
	
	/**
	 * Retrieve the Disqus ID for the post
	 *
	 * @return string
	 */
	public function getDisqusIdentifier(Fishpig_Wordpress_Model_Post_Abstract $object)
	{
		return $object->getId() . ' ' . $object->getGuid();
	}
	
	/**
	 * Retrieve the shortname for the Disqus account
	 *
	 * @return string
	 */
	public function getDisqusShortname()
	{
		return $this->helper('wordpress')->getWpOption('disqus_forum_url');
	}
	
	/**
	 * Retrieve the Disqus title
	 *
	 * @param
	 * @return string
	 */
	public function getDisqusTitle(Fishpig_Wordpress_Model_Post_Abstract $object)
	{
		return addslashes($this->escapeHtml($object->getPostTitle()));
	}
	
	/**
	 * Retrieve the Disqus Single-Sign On information
	 * This feature is not currently supported
	 *
	 * @return array
	 */
	public function getDisqusSso()
	{
		$helper = Mage::helper('wordpress');
		
		if ($key = $helper->getWpOption('disqus_partner_key')) {
			$new = false;	
		}
		else if (($key = $helper->getWpOption('disqus_secret_key')) && ($public = $helper->getWpOption('disqus_public_key'))) {
			$new = true;
		}
		else {
			return array();
		}
		
		$userData = new Varien_Object();
		
		Mage::dispatchEvent('wordpress_disqus_before_sso', array('user_data' => $userData, 'block' => $this));
		
		$userData = base64_encode(json_encode(
			$userData->getDetails() ? $userData->getDetails() : array()
		));
		
		$time = time();
		$hmac = $this->_hmacSha1($userData.' ' . $time, $key);
		
		$payload = $userData . ' ' . $hmac . ' ' . $time;
		
		return $new
			? array('remote_auth_s3' => $payload, 'api_key' => $public)
			: array('remote_auth_s2'=>$payload);
	}
	
	/**
	 * Determine whether to use SSO
	 * This can be disabled for debugging
	 *
	 * @return bool
	 */
	public function canSso()
	{
		return true;
	}
	
	/**
	 * Retrieve the SSO Login data
	 *
	 * @return string
	 */
	public function getDisqusSsoLogin()
	{
		$helper = Mage::helper('wordpress');
		
		$data = array(
			'name' => $helper->getWpOption('blogname'),
			'button' => $helper->getWpOption('disqus_sso_button'),
			'icon' => $helper->getWpOption('disqus_sso_icon'),
			'url' => $helper->getBaseUrl('wp-login.php'),
			'logout' => $helper->getBaseUrl('wp-login.php?action=logout'),
			'width' => 800,
			'height' => 700,
		);
		
		foreach($data as $k => $v) {
			$data[$k] = sprintf('%s: "%s", ', $k, $v);
		}
		
		return sprintf('this.sso = {%s}', rtrim(implode("", $data), ','));
	}
	
	/**
	 * Hash the payload!
	 *
	 * @param string $data
	 * @param string $key
	 * @return string
	 */
	protected function _hmacSha1($data, $key)
	{
		$blocksize=64;
		$hashfunc='sha1';

		if (strlen($key)>$blocksize) {
			$key=pack('H*', $hashfunc($key));
		}
		
		$key=str_pad($key,$blocksize,chr(0x00));
		$ipad=str_repeat(chr(0x36),$blocksize);
		$opad=str_repeat(chr(0x5c),$blocksize);
		$hmac = pack('H*', $hashfunc(($key^$opad).pack('H*',$hashfunc(($key^$ipad).$data))));
		
		return bin2hex($hmac);
	}
}
