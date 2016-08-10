<?php

class Balance_Sitemap_Model_Observer extends Mage_Sitemap_Model_Observer
{
    
    private $_error;
    private $_helper;
    /**
     * Generate sitemaps
     *
     * @param Mage_Cron_Model_Schedule $schedule
     */
    public function scheduledGenerateSitemaps($schedule)
    {
        
        // check if scheduled generation enabled
        if (!Mage::getStoreConfigFlag(self::XML_PATH_GENERATION_ENABLED)) {
            return;
        }            
        
        Mage::getSingleton('balance_sitemap/sitemap_handler')->initSitemaps();
        
        parent::scheduledGenerateSitemaps($schedule);
    }
       
}
