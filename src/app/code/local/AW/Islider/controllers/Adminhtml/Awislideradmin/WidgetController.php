<?php
/**
 * aheadWorks Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://ecommerce.aheadworks.com/AW-LICENSE-ENTERPRISE.txt
 * 
 * =================================================================
 *                 MAGENTO EDITION USAGE NOTICE
 * =================================================================
 * This package designed for Magento ENTERPRISE edition
 * aheadWorks does not guarantee correct work of this extension
 * on any other Magento edition except Magento ENTERPRISE edition.
 * aheadWorks does not provide extension support in case of
 * incorrect edition usage.
 * =================================================================
 *
 * @category   AW
 * @package    AW_Islider
 * @copyright  Copyright (c) 2010-2011 aheadWorks Co. (http://www.aheadworks.com)
 * @license    http://ecommerce.aheadworks.com/AW-LICENSE-ENTERPRISE.txt
 */
class AW_Islider_Adminhtml_Awislideradmin_WidgetController extends Mage_Adminhtml_Controller_Action {
    public function blockchooserAction() {
        $uniqId = $this->getRequest()->getParam('uniq_id');
        $blocksGrid = $this->getLayout()->createBlock('awislider/adminhtml_widget_blockchooser', '', array(
            'id' => $uniqId,
        ));
        $_blockCSS = $this->getLayout()->createBlock('adminhtml/template');
        $_blockCSS->setTemplate('aw_islider/widget/sliders.phtml')
            ->setGridId($uniqId);
        $this->getResponse()->setBody($blocksGrid->toHtml().$_blockCSS->toHtml());
    }
}
