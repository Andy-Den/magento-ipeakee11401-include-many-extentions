<?php

class Balance_Sitemap_Block_Adminhtml_Sitemap_Grid_Renderer_Link extends Mage_Adminhtml_Block_Sitemap_Grid_Renderer_Link
{

    /**
     * Prepare link to display in grid
     *
     * @param Varien_Object $row
     * @return string
     */
    public function render(Varien_Object $row)
    {
        $fileName = preg_replace('/^\//', '', $row->getSitemapPath() . $row->getSitemapFilename());                
        $url = $this->htmlEscape(Mage::app()->getStore($row->getStoreId())->getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB) . 'sitemap.xml');        
        if (file_exists($fileName)) {
            return sprintf('<a href="%1$s">%1$s</a>', $url);
        }
        return $url;
    }

}
