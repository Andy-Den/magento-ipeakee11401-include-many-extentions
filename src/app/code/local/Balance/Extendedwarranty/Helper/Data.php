<?php



class Balance_Extendedwarranty_Helper_Data extends Mage_Core_Helper_Abstract
{

  const XML_PATH_ENABLED     = 'extendedwarranty/settings/enabled';


	
	public function isEnabled()
    {
        return Mage::getStoreConfig( self::XML_PATH_ENABLED );
    }


}

