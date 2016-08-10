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
 
class MagExt_MgxTips_Block_Adminhtml_Catalog_Product_Attribute_Edit_Tab_Options extends Mage_Adminhtml_Block_Widget
{

    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('mgxtips/options.phtml');
    }


    public function getStores()
    {
        $stores = $this->getData('stores');
        if (is_null($stores)) {
            $stores = Mage::getModel('core/store')
                ->getResourceCollection()
                ->setLoadDefault(true)
                ->load();
            $this->setData('stores', $stores);
        }
        return $stores;
    }


    public function getDescValues()
    {
        $values = array();
        $values[0] = $this->getAttributeObject()->getData('mgxtips');
        
        $labels = Mage::getModel('mgxtips/mgxtips')->getCollection()
        ->addFieldToFilter('attribute_id', $this->getAttributeObject()
        ->getAttributeId())
        ->load()
        ->toArray();
        
        foreach($labels['items'] as $item)
        {
            $values[$item['store_id']] = $item['value'];
        }

        return $values;
    }


    public function getAttributeObject()
    {
        return Mage::registry('entity_attribute');
    }

}
