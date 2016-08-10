<?php

/**
 * admin product edit tabs - rewrite to include accessory
 *
 * @category   Balance
 * @package    Balance_Accessory
 * @author     Carey Sizer <carey@balanceinternet.com.au>
 */
class Balance_Accessory_Block_Adminhtml_Catalog_Product_Edit_Tabs extends Mage_Adminhtml_Block_Catalog_Product_Edit_Tabs
{

    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        $product = $this->getProduct();

        if (!($setId = $product->getAttributeSetId())) {
            $setId = $this->getRequest()->getParam('set', null);
        }

        if ($setId) {

            $this->addTab('accessory', array(
                'label'     => Mage::helper('catalog')->__('Accessories'),
                'url'       => $this->getUrl('*/*/accessory', array('_current' => true)),
                'class'     => 'ajax',
            ));
        }
        else {
            $this->addTab('set', array(
                'label'     => Mage::helper('catalog')->__('Settings'),
                'content'   => $this->_translateHtml($this->getLayout()
                    ->createBlock('adminhtml/catalog_product_edit_tab_settings')->toHtml()),
                'active'    => true
            ));
        }
        return parent::_prepareLayout();
    }

}