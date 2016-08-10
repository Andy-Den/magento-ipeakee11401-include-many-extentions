<?php

class Raptor_Explodedmenu_Model_Chunktype extends Varien_Object
{
    const VERTICAL	= 1;
    const FIXED	= 2;

    static public function toOptionArray()
    {
        return array(
            self::VERTICAL	=> Mage::helper('explodedmenu')->__('Top to bottom'),
            self::FIXED		=> Mage::helper('explodedmenu')->__('Left to right')
        );
    }
}