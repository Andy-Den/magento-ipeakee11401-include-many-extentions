<?php

class Balanceinternet_Shoppingdotcomfeed_Model_Feedproducts extends Mage_Core_Model_Abstract {

    public function _construct() {
        $this->_init('shoppingdotcomfeed/feedproducts');
    }

    /**
     * Delete Products from feed
     *
     * @param int $id_feed
     */
    public function deleteProductsFromFeed($id_feed) {
        Mage::getModel('shoppingdotcomfeed/feedproducts')
                ->setId($id_feed)
                ->delete();
    }

}
