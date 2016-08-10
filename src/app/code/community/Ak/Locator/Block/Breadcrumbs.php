<?php
class Ak_Locator_Block_Breadcrumbs extends Mage_Core_Block_Template
{


    /**
     * Preparing layout
     *
     * @return Ak_Locator_Block_Breadcrumbs
     */
    protected function _prepareLayout()
    {

        if ($breadcrumbsBlock = $this->getLayout()->getBlock('breadcrumbs')) {
            $breadcrumbsBlock->addCrumb('home', array(
                'label'=>Mage::helper('ak_locator')->__('Home'),
                'title'=>Mage::helper('ak_locator')->__('Go to Home Page'),
                'link'=>Mage::getBaseUrl()
            ));

            $breadcrumbsBlock->addCrumb('storeLocator', array(
                'label'=>Mage::helper('ak_locator')->__('Find a Store'),
                'title'=>Mage::helper('ak_locator')->__('Find a Store'),
                'link' => ''
            ));
            $path  = Mage::helper('ak_locator')->getBreadcrumbPath();
            foreach ($path as $name => $breadcrumb) {
                    $breadcrumbsBlock->addCrumb($name, $breadcrumb);
            }
        }
        return parent::_prepareLayout();
    }
}
