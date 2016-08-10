<?php
/**
 * Catalog product controller
 *
 * @category   Balance
 * @package    Balance_Accessory
 * @author     Carey Sizer <carey@balanceinternet.com>
 */
require_once("Mage/Adminhtml/controllers/Catalog/ProductController.php");
class Balance_Accessory_Adminhtml_Catalog_ProductController extends Mage_Adminhtml_Catalog_ProductController
{
    protected function _construct()
    {
        // Define module dependent translate
        // should drop back to Mage_Catalog where necesary
        $this->setUsedModuleName('Balance_Accessory');
    }

    /**
     * Get accessory products grid and serializer block
     */
    public function accessoryAction()
    {
        $this->_initProduct();
        $this->loadLayout();
        $this->getLayout()->getBlock('catalog.product.edit.tab.accessory')
            ->setProductsAccessory($this->getRequest()->getPost('products_accessory', null));
        $this->renderLayout();
    }

    /**
     * Get accessory products grid
     */
    public function accessoryGridAction()
    {
        $this->_initProduct();
        $this->loadLayout();
        $this->getLayout()->getBlock('catalog.product.edit.tab.accessory')
            ->setProductsAccessory($this->getRequest()->getPost('products_accessory', null));
        $this->renderLayout();
    }


    /**
     * Initialize product before saving - save out the accessory
     */
    protected function _initProductSave()
    {
        parent::_initProductSave();
        $product = Mage::registry('product');
        /**
         * Init product links data (accessory)
         */
        $links = $this->getRequest()->getPost('links');
        if (isset($links['accessory']) && !$product->getAccessoryReadonly()) {
            $product->setAccessoryLinkData(Mage::helper('adminhtml/js')->decodeGridSerializedInput($links['accessory']));
        }

        return $product;
    }
}