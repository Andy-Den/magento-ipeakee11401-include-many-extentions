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
class Belvg_Twitter_Block_Twitterbtn extends Mage_Core_Block_Template
{
    public function getEnabled(){
        if (Mage::getStoreConfig('twitter/tweetbtn/enabled') == 0)
            return false;
        return true;

    }

    public function getDataCount(){
        return Mage::getStoreConfig('twitter/tweetbtn/type');
    }


    public function getDataVia(){
        if (Mage::getStoreConfig('twitter/tweetbtn/recommend') != '')
            return ' data-via="'.Mage::getStoreConfig('twitter/tweetbtn/recommend').'"';
        return '';
    }

    public function getDataText(){
        if (Mage::getStoreConfig('twitter/tweetbtn/ttext') != '')
            return ' data-text="'.Mage::getStoreConfig('twitter/tweetbtn/ttext').'"';
        return '';
    }

    public function getDataLang(){
        return Mage::getStoreConfig('twitter/tweetbtn/lang');
    }
}