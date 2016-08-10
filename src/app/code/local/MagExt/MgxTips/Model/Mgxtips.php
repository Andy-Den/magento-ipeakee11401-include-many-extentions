<?php
/**
 * Webtex
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.webtexsoftware.com/LICENSE.txt
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to info@webtexsoftware.com and we will send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade the extension to newer
 * versions in the future. If you wish to customize the extension for your
 * needs please refer to http://www.webtexsoftware.com for more information, 
 * or contact us through this email: info@webtexsoftware.com.
 *
 * @category   MagExt
 * @package    MagExt_Tips
 * @copyright  Copyright (c) 2011 Webtex Solutions, LLC (http://www.webtexsoftware.com/)
 * @license    http://www.webtexsoftware.com/LICENSE.txt End-User License Agreement
 */
 
class MagExt_MgxTips_Model_MgxTips extends Mage_Core_Model_Abstract
{
    private $aPositions = array(
        'nw' => 'topLeft',
        'n' => 'topMiddle',
        'ne' => 'topRight',
        'w' => 'leftMiddle',
        'e' => 'rightMiddle',
        'sw' => 'bottomLeft',
        's' => 'bottomMiddle',
        'se' => 'bottomRight'
    );
    
    public function _construct()
    {
        parent::_construct();
        $this->_init('mgxtips/mgxtips');
    }
   
    public function getLabels()
    {
        $this->_getResource();
        return $this;                
            
    }
   
   
    
    public function getImageSrc()
    {
        $sSrc = Mage::getStoreConfig('magext_mgxtips/mgxtips/upload');
        $sBaseUrl = Mage::getBaseUrl('media') . 'magext/mgxtips/';
        
        if("" != $sSrc)
        {
            return $sBaseUrl . $sSrc;
        }
        else
        {
            return $sBaseUrl . 'default/default.png';
        }
    }
    
    
    public function getPositionTips()
    {
        $sPosition = Mage::getStoreConfig('magext_mgxtips/mgxtips/position');
        return "'" . $this->_getConvertedPosition($sPosition) . "'";
    }
    
    public function getFageEffect()
    {
        $bFadeFlag = Mage::getStoreConfig('magext_mgxtips/mgxtips/fade');
        
        if($bFadeFlag) 
        {
            return '400';
        }
        else
        {
            return '0';
        }
    }
    
    public function getOpacity()
    {
        $bFadeFlag = Mage::getStoreConfig('magext_mgxtips/mgxtips/opacity');
        
        if($bFadeFlag) 
        {
            return '0.8';
        }
        else
        {
            return '1';
        }
    }
    
    private function _getConvertedPosition($sPosition) {
    	return ($sPosition !== '') && isset($this->aPositions[$sPosition]) ? $this->aPositions[$sPosition] : 'center'; 
    }
}