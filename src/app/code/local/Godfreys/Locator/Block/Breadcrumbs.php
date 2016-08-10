<?php
class Godfreys_Locator_Block_Breadcrumbs extends Ak_Locator_Block_Breadcrumbs
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
            if(Mage::app()->getStore()->getCode() == "hoover_au") {
                $breadcrumbsBlock->addCrumb('storeLocator', array(
                    'label'=>Mage::helper('ak_locator')->__('Service your hoover'),
                    'title'=>Mage::helper('ak_locator')->__('Service your hoover'),
                    'link' => ''
                ));
                if($this->getRequest()->getModuleName() == "locator" && $this->getRequest()->getControllerName() == "location" && $this->getRequest()->getActionName()=="index") {
                    $locationList =  Mage::registry('locator_locations');
                    $locationCurrent =$locationList->getFirstItem();
                    if(count($locationCurrent->getData())) {
                        $breadcrumbsBlock->addCrumb('locatordetail', array(
                            'label'=>$locationCurrent->getTitle(),
                            'title'=>$locationCurrent->getTitle(),
                            'link' => ''
                        ));
                    }
                }
            }
            else {
                $breadcrumbsBlock->addCrumb('storeLocator', array(
                    'label'=>Mage::helper('ak_locator')->__('Find a Store'),
                    'title'=>Mage::helper('ak_locator')->__('Find a Store'),
                    'link' => ''
                ));
            }

            $path  = Mage::helper('ak_locator')->getBreadcrumbPath();
            foreach ($path as $name => $breadcrumb) {
                $breadcrumbsBlock->addCrumb($name, $breadcrumb);
            }
        }
        return $this;
    }
}
