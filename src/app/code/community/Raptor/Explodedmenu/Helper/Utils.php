<?php

/**
 * Raptor Commerce
 *
 * @category   Raptor
 * @package    Raptor_Custommenu
 * @copyright  Copyright (c) 2009 Raptor Commerce (http://www.raptorcommerce.com)
 */
class Raptor_Explodedmenu_Helper_Utils extends Mage_Core_Helper_Abstract
{
	/**
	 * Returns a config value
	 *
	 * @param String $namespace e.g. 'custommenu' or 'supermenu'
	 * @param String $parentKey the parent key e.g. 'exploded_menu'
	 * @param String $key e.g. 'show_third_level;
	 * @return mixed
	 * @throws Exception if key not set
	 */
	public function getConfigData($namespace, $parentKey, $key) {
		$config = Mage::getStoreConfig($namespace);
		if (isset($config[$parentKey]) && isset($config[$parentKey][$key]) && strlen($config[$parentKey][$key]) > 0) {
			$value = $config[$parentKey][$key];
			return $value;
		} else {
			throw new Exception('Value not set');
		}
	}
}