<?php

class Balance_ContentSetup_Helper_Data
    extends Mage_Core_Helper_Abstract
{


    protected $_pageDefaults = array(
        'root_template' => 'two_columns_left',
        'is_active'     => 1,
        'stores'        => 0,
    );

    protected $_staticBlockDefaults = array(
        'is_active' => 1,
        'stores'    => 0,
    );


    public function createPage ($data) {
        // Check if page already exists
        if (Mage::getModel('cms/page')->checkIdentifier($data['identifier'], $data['stores'])) return;

        $data = array_merge($this->_pageDefaults, $data);

        Mage::getModel('cms/page')->setData($data)->save();
    }


    public function createStaticBlock ($data) {
        // Check if block already exists
        if (Mage::getModel('cms/block')->load($data['identifier'])->getId()) return;

        $data = array_merge($this->_staticBlockDefaults, $data);

        Mage::getModel('cms/block')->setData($data)->save();
    }


}
