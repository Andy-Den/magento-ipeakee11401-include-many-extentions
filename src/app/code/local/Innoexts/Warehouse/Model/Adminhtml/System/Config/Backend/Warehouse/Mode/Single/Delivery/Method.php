<?php
/**
 * Innoexts
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the InnoExts Commercial License
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://innoexts.com/commercial-license-agreement
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@innoexts.com so we can send you a copy immediately.
 * 
 * @category    Innoexts
 * @package     Innoexts_Warehouse
 * @copyright   Copyright (c) 2011 Innoexts (http://www.innoexts.com)
 * @license     http://innoexts.com/commercial-license-agreement  InnoExts Commercial License
 */

/**
 * Warehouse single mode delivery method backend
 * 
 * @category   Innoexts
 * @package    Innoexts_Warehouse
 * @author     Innoexts Team <developers@innoexts.com>
 */
class Innoexts_Warehouse_Model_Adminhtml_System_Config_Backend_Warehouse_Mode_Single_Delivery_Method extends Mage_Core_Model_Config_Data 
{
    /**
     * Get warehouse helper
     * 
     * @return Innoexts_Warehouse_Helper_Data
     */
    protected function getWarehouseHelper()
    {
        return Mage::helper('warehouse');
    }
    /**
     * After save handler
     *
     * @return Innoexts_Warehouse_Model_Adminhtml_System_Config_Backend_Warehouse_Mode_Single_Delivery_Method
     */
    protected function _afterSave()
    {
        $newValue = $this->getValue();
        $oldValue = $this->getWarehouseHelper()->getConfig()->getSingleModeDeliveryMethodCode();
        if (($newValue != $oldValue) && ($newValue == 'nearest')) {
            foreach ($this->getWarehouseHelper()->getWarehouses() as $warehouse) {
                $coordinates = $this->getWarehouseHelper()->getGeoCoderHelper()->getCoordinates($warehouse->getOrigin());
                if ($coordinates->getLatitude() && $coordinates->getLongitude()) {
                    $warehouse->setOriginLatitude($coordinates->getLatitude());
                    $warehouse->setOriginLongitude($coordinates->getLongitude());
                }
                $warehouse->save();
                sleep(1);
            }
        }
        return $this;
    }
}