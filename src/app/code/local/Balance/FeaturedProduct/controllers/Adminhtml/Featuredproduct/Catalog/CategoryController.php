<?php
require_once ("Mage/Adminhtml/controllers/Catalog/CategoryController.php");
class Balance_FeaturedProduct_Adminhtml_Featuredproduct_Catalog_CategoryController extends Mage_Adminhtml_Catalog_CategoryController
{	
  
      /**
     * Grid Action
     * Display list of products related to current category
     *
     * @return void
     */
    public function featuredproductgridAction()
    {
        if (!$category = $this->_initCategory(true)) {
            return;
        }
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('featuredproduct/adminhtml_category_tab_featuredproduct', 'featuredproduct.grid')
                ->toHtml()
        );
    }
}