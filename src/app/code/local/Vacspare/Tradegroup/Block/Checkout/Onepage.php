<?php
/**
 * Magento Enterprise Edition
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magento Enterprise Edition License
 * that is bundled with this package in the file LICENSE_EE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.magentocommerce.com/license/enterprise-edition
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Checkout
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/enterprise-edition
 */

/**
 * Onepage checkout block
 *
 * @category   Mage
 * @package    Mage_Checkout
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Vacspare_Tradegroup_Block_Checkout_Onepage extends Mage_Checkout_Block_Onepage
{
   
    
    public function getActiveStep()
    {
        if((Mage::app()->getStore()->getCode() != 'vacspare_au_trade'
        		&&  Mage::app()->getStore()->getCode() != 'vacspare_nz_trade')
        		|| !Mage::helper('tradegroup')->_isCustomerGroupTrade())
        {
            return parent::getActiveStep();
        }
        $model = Mage::getModel('tradegroup/checkout_purchase');
        $model->saveInfo();
        
        return 'review';
    }
 }
