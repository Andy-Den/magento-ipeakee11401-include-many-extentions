<?php
class Itoris_Producttabsslider_Block_View extends Mage_Catalog_Block_Product_View
{
    public function __construct()
    {
        parent::__construct();
        Mage::helper('itoris_producttabsslider')->checkConfiguration();
    }
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        $this->settings = Mage::getModel('itoris_producttabsslider/settings')->getCurrent();
        $csss           = $this->processCss();
        $html           = '';
        $skinUrl        = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_SKIN);
        foreach ($csss as $css) {
            $html .= '<link rel="stylesheet" type="text/css" href="' . $skinUrl . 'frontend/base/default/css/itoris/producttabsslider' . $css . '.css" media="all" /> ';
        }
        $css = $this->getLayout()->createBlock('core/text');
        $css->setText($html);
        $block = $this->getLayout()->getBlock('head');
        if($block) {
            $block->setChild('cssadds', $css);
        }
    }
    protected function _beforeToHtml()
    {
        parent::_beforeToHtml();
        if ($this->settings['enabled'] != '1') {
            return;
        }
        if (!Mage::helper('itoris_producttabsslider')->isRegisteredAutonomous()) {
            return;
        }
        Mage::helper('itoris_producttabsslider')->activate();
        foreach ($this->_children as $key => $block) {
            if ($key == 'info_tabs' && $block instanceof Mage_Catalog_Block_Product_View_Tabs) {
                foreach ($block->getChild() as $k => $b) {
                    if (Mage::helper('itoris_producttabsslider')->isTab($k)) {
                        if (!in_array($k, $this->tabAliasesToHtmlFromInfoBlock)) {
                            $this->tabAliasesToHtmlFromInfoBlock[] = $k;
                        }
                        Mage::helper('itoris_producttabsslider')->incTabsCount();
                    }
                }
                continue;
            }
            if (Mage::helper('itoris_producttabsslider')->isTab($key)) {
                if (!in_array($key, $this->tabAliasesToHtml)) {
                    $this->tabAliasesToHtml[] = $key;
                }
                Mage::helper('itoris_producttabsslider')->incTabsCount();
            }
        }
        $this->preloadTabs();
    }
    public function preloadTabs()
    {
        $rootBlock = $this->getChild('info_tabs');
        if ($rootBlock) {
            foreach ($this->tabAliasesToHtmlFromInfoBlock as $name) {
                $block = $rootBlock->getChild($name);
                Mage::helper('itoris_producttabsslider')->addTab($block, $block->toHtml(), $name);
            }
        }
        foreach ($this->tabAliasesToHtml as $name) {
            Mage::helper('itoris_producttabsslider')->addTab($this->getChild($name), parent::getChildHtml($name), $name);
        }
    }
    public function getChildHtml($name = '', $useCache = true, $sorted = false)
    {
        if ($name == 'info_tabs' && $this->getChild($name) instanceof Mage_Catalog_Block_Product_View_Tabs) {
            if (Mage::helper('itoris_producttabsslider')->isActive()) {
                if (count($this->tabAliasesToHtmlFromInfoBlock) > 0) {
                    return Mage::helper('itoris_producttabsslider')->getTabOutput();
                } else {
                    return '';
                }
            } else {
                parent::getChildHtml($name, $useCache, $sorted);
            }
        }
        if (Mage::helper('itoris_producttabsslider')->isActive() && in_array($name, $this->tabAliasesToHtml)) {
            return Mage::helper('itoris_producttabsslider')->getTabOutput();
        }
        if (in_array($name, $this->tabAliasesToHtml)) {
            return '';
        }
        $html = parent::getChildHtml($name, $useCache, $sorted);
        return $html;
    }
    private function processCss()
    {
        $css        = array();
        $theme      = $this->settings['theme'];
        $themeParts = explode('_', $theme);
        $cssPath    = '';
        foreach ($themeParts as $part) {
            $cssPath .= '/' . $part;
            $css[] = $cssPath;
        }
        return $css;
    }
    protected $settings = null;
    protected $tabAliasesToHtml = array();
    protected $tabAliasesToHtmlFromInfoBlock = array();
}
?>