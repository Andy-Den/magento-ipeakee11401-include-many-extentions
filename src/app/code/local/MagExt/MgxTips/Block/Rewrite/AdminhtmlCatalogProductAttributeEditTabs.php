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
 
class MagExt_MgxTips_Block_Rewrite_AdminhtmlCatalogProductAttributeEditTabs extends CJM_ColorSelectorPlus_Block_Adminhtml_Catalog_Product_Attribute_Edit_Tabs
{

    protected function _beforeToHtml()
    {       
        
        $this->addTab('description', array(
            'label'     => Mage::helper('catalog')->__('Manage Description'),
            'title'     => Mage::helper('catalog')->__('Manage Description'),
            'content'   => $this->getLayout()->createBlock('mgxtips/adminhtml_catalog_product_attribute_edit_tab_options')->toHtml(),
            'after' => 'labels',
        ));

        return parent::_beforeToHtml();
    }

}
