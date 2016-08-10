<?php

class Itech_Changetheme_Model_Observer
{
    public function addCustomHandles($observer) {
		if($theme = Mage::getSingleton('core/session')->getCustomizeTheme()){
			Mage::getDesign()->setArea('frontend')->setTheme($theme);
		}
	}
}