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
 
class MagExt_MgxTips_Model_Adminhtml_Source_Position
{

    /**
     * Get available options
     *
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            array('value' => 'se',                          'label' => Mage::helper('mgxtips')->__('North-West')),
            array('value' => 's',                           'label' => Mage::helper('mgxtips')->__('North')),
            array('value' => 'sw',                          'label' => Mage::helper('mgxtips')->__('North-East')),
            array('value' => 'e',                           'label' => Mage::helper('mgxtips')->__('West')),
            array('value' => 'w',                           'label' => Mage::helper('mgxtips')->__('East')),
            array('value' => 'ne',                          'label' => Mage::helper('mgxtips')->__('South-West')),
            array('value' => 'n',                           'label' => Mage::helper('mgxtips')->__('South')),
            array('value' => 'nw',                          'label' => Mage::helper('mgxtips')->__('South-East')),
        );
    }

}
