<?php

/*
 * @copyright  Copyright (c) 2011 by  ESS-UA.
 */

class Ess_M2ePro_Model_Amazon_Template_General extends Ess_M2ePro_Model_Component_Child_Amazon_Abstract
{
    const SKU_MODE_NONE             = 0;
    const SKU_MODE_DEFAULT          = 1;
    const SKU_MODE_CUSTOM_ATTRIBUTE = 2;

    const GENERAL_ID_MODE_NONE             = 0;
    const GENERAL_ID_MODE_CUSTOM_ATTRIBUTE = 1;

    // ########################################

    public function _construct()
    {
        parent::_construct();
        $this->_init('M2ePro/Amazon_Template_General');
    }

    // ########################################

    /**
     * @return Ess_M2ePro_Model_Account
     */
    public function getAccount()
    {
        return $this->getParentObject()->getAccount();
    }

    /**
     * @return Ess_M2ePro_Model_Marketplace
     */
    public function getMarketplace()
    {
        return $this->getParentObject()->getMarketplace();
    }

    // ########################################

    public function getListings($asObjects = false, array $filters = array())
    {
        return $this->getParentObject->getListings($asObjects,$filters);
    }

    // ########################################

    public function getSkuMode()
    {
        return (int)$this->getData('sku_mode');
    }

    public function isSkuNoneMode()
    {
        return $this->getSkuMode() == self::SKU_MODE_NONE;
    }

    public function isSkuDefaultMode()
    {
        return $this->getSkuMode() == self::SKU_MODE_DEFAULT;
    }

    public function isSkuAttributeMode()
    {
        return $this->getSkuMode() == self::SKU_MODE_CUSTOM_ATTRIBUTE;
    }

    public function getSkuSource()
    {
        return array(
            'mode'      => $this->getSkuMode(),
            'attribute' => $this->getData('sku_custom_attribute')
        );
    }

    //-------------------------

    public function getGeneralIdMode()
    {
        return (int)$this->getData('general_id_mode');
    }

    public function isGeneralIdNoneMode()
    {
        return $this->getGeneralIdMode() == self::GENERAL_ID_MODE_NONE;
    }

    public function isGeneralIdAttributeMode()
    {
        return $this->getGeneralIdMode() == self::GENERAL_ID_MODE_CUSTOM_ATTRIBUTE;
    }

    public function getGeneralIdSource()
    {
        return array(
            'mode'      => $this->getGeneralIdMode(),
            'attribute' => $this->getData('general_id_custom_attribute')
        );
    }

    // ########################################

    public function getUsedAttributes()
    {
        return array();
    }

    // ########################################
}