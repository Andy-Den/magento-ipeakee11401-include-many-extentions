<?php
class Criteo_OneTag_Helper_Data extends Mage_Core_Helper_Abstract
{
    const SETTINGS_PARTNER_ID = 'Criteo_OneTag/settings/partner_id';
    const SETTINGS_CROSS_DEVICE = 'Criteo_OneTag/settings/cross_device';
    const SETTINGS_PRODUCT_ID = 'Criteo_OneTag/settings/product_id';
	const FEED_SETTINGS_PASSWORD = 'Criteo_OneTag/feed_settings/password';
	const FEED_SETTINGS_URL_TRACKING = 'Criteo_OneTag/feed_settings/url_tracking';

    public function get_partner_id($store = null)
    {
        return Mage::getStoreConfig(self::SETTINGS_PARTNER_ID, $store);
    }

    public function get_cross_device($store = null)
    {
        return Mage::getStoreConfig(self::SETTINGS_CROSS_DEVICE, $store);
    }

    public function get_product_id($store = null)
    {
        return Mage::getStoreConfig(self::SETTINGS_PRODUCT_ID, $store);
    }
	
	public function get_feed_password($store = null)
    {
        return Mage::getStoreConfig(self::FEED_SETTINGS_PASSWORD, $store);
    }
	
	public function get_feed_url_tracking($store = null)
    {
        return Mage::getStoreConfig(self::FEED_SETTINGS_URL_TRACKING, $store);
    }
}
?>