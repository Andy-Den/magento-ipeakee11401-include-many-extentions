<?php
/**
 * Innoexts
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the InnoExts Commercial License
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://innoexts.com/commercial-license-agreement
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@innoexts.com so we can send you a copy immediately.
 * 
 * @category    Innoexts
 * @package     Innoexts_Warehouse
 * @copyright   Copyright (c) 2011 Innoexts (http://www.innoexts.com)
 * @license     http://innoexts.com/commercial-license-agreement  InnoExts Commercial License
 */

/**
 * Product multiple inventory tab
 *
 * @category   Innoexts
 * @package    Innoexts_Warehouse
 * @author     Innoexts Team <developers@innoexts.com>
 */
class Innoexts_Warehouse_Block_Adminhtml_Catalog_Product_Edit_Tab_Multipleinventory extends Mage_Adminhtml_Block_Widget_Form
{
	/**
     * Get warehouse helper
     *
     * @return Innoexts_Warehouse_Helper_Data
     */
    protected function getWarehouseHelper()
    {
        return Mage::helper('warehouse');
    }
    /**
     * Retrieve registered product model
     *
     * @return Mage_Catalog_Model_Product
     */
    protected function getProduct()
    {
        return Mage::registry('product');
    }
    /**
     * Prepare form before rendering HTML
     *
     * @return Innoexts_Warehouse_Block_Adminhtml_Catalog_Product_Edit_Tab_Multipleinventory
     */
    protected function _prepareForm() {
        $helper = $this->getWarehouseHelper();
        $product = $this->getProduct();
        $form = new Varien_Data_Form();
        $form->setHtmlIdPrefix('product_');
        $form->setFieldNameSuffix('product');
        $fieldset = $form->addFieldset('multipleinventory', array('legend' => $helper->__('Inventory'), ));
        $fieldset->addField('stocks_data', 'text', array(
            'name' => 'stocks_data', 
            'label' => $helper->__('Inventory'), 
            'title' => $helper->__('Inventory'), 
            'required' => true, 
            'class' => 'requried-entry', 
            'value' => $product->getStocksItems(), 
        ));
        $form->getElement('stocks_data')->setRenderer(
            $this->getLayout()->createBlock('warehouse/adminhtml_catalog_product_edit_tab_multipleinventory_renderer')
        );
        $this->setForm($form);
        return parent::_prepareForm();
    }
}