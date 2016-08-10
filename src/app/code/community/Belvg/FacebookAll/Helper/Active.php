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

class Belvg_FacebookAll_Helper_Active extends Mage_Core_Helper_Abstract
{
    public function getAppId()
    {
        return Mage::getStoreConfig('facebookall/settings/appid');
    }

    public function getSecretKey()
    {
        return Mage::getStoreConfig('facebookall/settings/secret');
    }

    public function isActiveLike()
    {
        return Mage::getStoreConfig('facebookall/like/enabled');
    }
    
    public function isActiveComments()
    {
        return Mage::getStoreConfig('facebookall/comments/enabled');
    }    
    
    public function isActiveActivity()
    {
        return Mage::getStoreConfig('facebookall/activity/enabled');
    }        
    
    public function getActivityWidth()
    {
        return Mage::getStoreConfig('facebookall/activity/width');
    }    
    
    public function getActivityHeight()
    {
        return Mage::getStoreConfig('facebookall/activity/height');
    }        
    
    public function getActivityHeader()
    {
        return Mage::getStoreConfig('facebookall/activity/header')?'true':'false';
    }            
    
    public function getActivityColor()
    {
        return Mage::getStoreConfig('facebookall/activity/color');
    }       

    public function getActivityFont()
    {
        return Mage::getStoreConfig('facebookall/activity/font');
    }       
    
    public function getActivityRecommendations()
    {
        return Mage::getStoreConfig('facebookall/activity/recommendations')?'true':'false';
    }    
    
    public function getActivityMaxage()
    {
        return Mage::getStoreConfig('facebookall/activity/maxage');
    }           
    
    public function isFacesLikeActive()
    {
        return Mage::getStoreConfig('facebookall/like/faces')?'true':'false';
    }

    public function getLikeWidth()
    {
        return Mage::getStoreConfig('facebookall/like/width');
    }

    public function getLikeColor()
    {
        return Mage::getStoreConfig('facebookall/like/color');
    }

    public function getLikeLayout()
    {
        return Mage::getStoreConfig('facebookall/like/layout');
    }

    public function getProducts($order)
    {
	$db_read = Mage::getSingleton('core/resource')->getConnection('facebookall_read');
        $tablePrefix = (string)Mage::getConfig()->getTablePrefix();

	$sql = 'SELECT `product_id` FROM `'.$tablePrefix.'sales_flat_order_item` as i
                LEFT JOIN `'.$tablePrefix.'sales_flat_order` as o ON o.`increment_id` = "'.$order.'"
                WHERE i.`order_id` = o.`entity_id` AND i.`parent_item_id` IS NULL';
        $data = $db_read->fetchAll($sql);
        return $data;
    }

	public function getLoginImg()
	{
		$img = Mage::getStoreConfig('facebookall/settings/imglogin');
		if (empty($img)) {
			$img = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_SKIN).
						'frontend/default/default/images/belvg/fb.gif';
		} else {
			$img = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA).
						'facebookall/'.$img;
		}
		return $img;
	}
        
    public function isActiveShare()
    {
        return Mage::getStoreConfig('facebookall/share/enabled');
    }        

    public function getShareColor()
    {
        return Mage::getStoreConfig('facebookall/share/color');
    }    

    public function getShareFont()
    {
        return Mage::getStoreConfig('facebookall/share/font');
    }         
}
