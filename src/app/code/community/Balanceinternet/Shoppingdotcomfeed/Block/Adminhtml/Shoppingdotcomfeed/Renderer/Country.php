<?php

class Balanceinternet_Shoppingdotcomfeed_Block_Adminhtml_Shoppingdotcomfeed_Renderer_Country extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {

    public function render(Varien_Object $row) {
        return Mage::getModel('shoppingdotcomfeed/feedportal')->getResource()->getCountriesForManageFeedGrid($row->getData($this->getColumn()->getIndex()));   
    }

    /* @TODO - remove - test if it's not anywhere else. */
    public function getCountries($idFeedportal) {
        $conn = Mage::getSingleton('core/resource')->getConnection('core_read');
        $results = $conn->fetchAll("SELECT id, country_code, country FROM sdc_feedportal where id=");

        foreach ($results as $result) {
            $countries[$results[$count]['id']] = $result['country'] . ' - ' . $result['country_code'];
            $count++;
        }
    }

}

