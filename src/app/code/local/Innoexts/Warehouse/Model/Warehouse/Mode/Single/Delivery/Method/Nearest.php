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
 * Nearest single mode delivery method
 * 
 * @category   Innoexts
 * @package    Innoexts_Warehouse
 * @author     Innoexts Team <developers@innoexts.com>
 */
class Innoexts_Warehouse_Model_Warehouse_Mode_Single_Delivery_Method_Nearest 
    extends Innoexts_Warehouse_Model_Warehouse_Mode_Single_Delivery_Method_Abstract 
{
    /**
     * Get stock identifier
     * 
     * @return int
     */
    protected function _getStockId()
    {
        $stockId = null;
        $address = $this->getCustomerShippingAddress();
        if ($address) {
            $coordinates = $this->getWarehouseHelper()->getGeoCoderHelper()->getCoordinates($address);
            if ($coordinates->getLatitude() && $coordinates->getLongitude()) {
                $warehouse = $this->getWarehouse();
                $resource = $warehouse->getResource();
                $adapter = $resource->getReadConnection();
                $latitude = $coordinates->getLatitude();
                $longitude = $coordinates->getLongitude();
                $select = $adapter->select()
                    ->from($resource->getMainTable())
                    ->limit(1);
                $select->order('IF ((origin_latitude IS NOT NULL) AND (origin_longitude IS NOT NULL), 
                69.09 * DEGREES(ACOS(
                    SIN(RADIANS(origin_latitude)) * SIN(RADIANS('.$adapter->quoteInto('?', $latitude).')) +
                    COS(RADIANS(origin_latitude)) * COS(RADIANS('.$adapter->quoteInto('?', $latitude).')) * 
                        COS(RADIANS(origin_longitude - '.$adapter->quoteInto('?', $longitude).'))
                )), 12500)');
                $data = $adapter->fetchRow($select);
                $warehouse->setData($data);
                if ($warehouse->getId()) {
                    $stockId = $warehouse->getStockId();
                }
            }
        }
        return $stockId;
    }
}