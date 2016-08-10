<?php

/*
 * @copyright  Copyright (c) 2011 by  ESS-UA.
 */

class Ess_M2ePro_Block_Adminhtml_Menu extends Mage_Adminhtml_Block_Page_Menu
{
    public function getMenuArray()
    {
        $menuArray = parent::getMenuArray();

        try {

            if (!Mage::getSingleton('admin/session')->isAllowed('m2epro')) {
                return $menuArray;
            }

            $menuArray['m2epro']['label'] = $this->getRootNodeLabel();

            // Add wizard menu item
            //---------------------------------
            if (!Mage::getSingleton('M2ePro/Wizard')->isInstallationFinished()) {
                $menuArray['m2epro']['children'] = array();
                $menuArray['m2epro']['children']['wizard'] = array(
                    'label' => Mage::helper('M2ePro')->__('Configuration Wizard'),
                    'sort_order' => 1,
                    'url' => $this->getUrl('adminhtml/m2epro_wizard/index'),
                    'active' => false,
                    'level' => 1,
                    'last' => true
                );
                return $menuArray;
            }
            //---------------------------------

            if (!Mage::helper('M2ePro/Component_Ebay')->isEnabled()) {
                unset($menuArray['m2epro']['children']['templates']['children']['general']);
                unset($menuArray['m2epro']['children']['communication']);
            }

            // Set documentation redirect url
            //---------------------------------
            $menuArray['m2epro']['children']['help']['children']['doc']['click'] =
                    "window.open(this.href, 'M2ePro Documentation ' + this.href); return false;";
            $menuArray['m2epro']['children']['help']['children']['doc']['url'] =
                    Mage::helper('M2ePro/Module')->getConfig()->getGroupValue('/documentation/', 'baseurl');
            //---------------------------------

        } catch (Exception $exception) {}


        return $menuArray;
    }

    private function getRootNodeLabel()
    {
        $componentsLabels = array();
        Mage::helper('M2ePro/Component_Ebay')->isEnabled()   && $componentsLabels[] = Mage::helper('M2ePro')->__(Ess_M2ePro_Helper_Component_Ebay::TITLE);
        Mage::helper('M2ePro/Component_Amazon')->isEnabled() && $componentsLabels[] = Mage::helper('M2ePro')->__(Ess_M2ePro_Helper_Component_Amazon::TITLE .' (Beta)');
        return implode(' / ', $componentsLabels);
    }
}