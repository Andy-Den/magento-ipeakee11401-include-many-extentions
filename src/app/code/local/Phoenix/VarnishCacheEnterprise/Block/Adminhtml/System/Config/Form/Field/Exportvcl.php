<?php
/**
 * PageCache powered by Varnish
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the PageCache powered by Varnish License
 * that is bundled with this package in the file LICENSE_VARNISH_CACHE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.phoenix-media.eu/license/license_varnish_cache.txt
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to support@phoenix-media.eu so we can send you a copy immediately.
 *
 * @category   Phoenix
 * @package    Phoenix_VarnishCacheEnterprise
 * @copyright  Copyright (c) 2011 PHOENIX MEDIA GmbH & Co. KG (http://www.phoenix-media.eu)
 * @license    http://www.phoenix-media.eu/license/license_varnish_cache.txt
 */

class Phoenix_VarnishCacheEnterprise_Block_Adminhtml_System_Config_Form_Field_Exportvcl
    extends Mage_Adminhtml_Block_System_Config_Form_Field
{
    /**
     * Returns html of vcl export button
     *
     * @param Varien_Data_Form_Element_Abstract $element
     * @return string
     */
    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        $this->setElement($element);
        $buttonBlock = $this->getLayout()->createBlock('adminhtml/widget_button');
        $params = array(
            'website' => $buttonBlock->getRequest()->getParam('website')
        );
        $data = array(
            'label'     => Mage::helper('varnishcacheenterprise')->__('Export'),
            'onclick'   => "setLocation('".Mage::helper('adminhtml')->getUrl('*/varnishCache/exportVcl', $params)."')",
            'class'     => '',
        );
        $html = $buttonBlock->setData($data)->toHtml();
        return '<div id="system_varnishcache_export">' . $html . '</div>';
    }
}

