<?php
/**
 * Category edit block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Balance_FeaturedProduct_Block_Adminhtml_Category_Edit_Form extends Mage_Adminhtml_Block_Catalog_Category_Edit_Form
{    
    public function __construct()
    {        
        parent::__construct();
        $this->setTemplate('balance/featuredproduct/catalog/category/edit/form.phtml');
    }
    
    public function getFeaturedProductsJson()
    {
        $products =$this->getCategory()
                        ->getFeaturedProductsPosition();       
        if (!empty($products)) {
            return Mage::helper('core')->jsonEncode($products);
        }
        return '{}';
    }
}
