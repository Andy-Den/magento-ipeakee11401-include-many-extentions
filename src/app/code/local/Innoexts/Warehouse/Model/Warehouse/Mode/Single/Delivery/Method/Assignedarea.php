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
 * Assigned areas single mode delivery method
 * 
 * @category   Innoexts
 * @package    Innoexts_Warehouse
 * @author     Innoexts Team <developers@innoexts.com>
 */
class Innoexts_Warehouse_Model_Warehouse_Mode_Single_Delivery_Method_Assignedarea 
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
            $warehouse = $this->getWarehouse();
            $resource = $warehouse->getResource();
            $adapter = $resource->getReadConnection();
            $bind = array(
                ':country_id' => $address->getCountryId(), 
                ':region_id'  => intval($address->getRegionId()), 
                ':postcode'   => $address->getPostcode(), 
            );
            $select  = $adapter->select()
                ->from(array('w' => $resource->getMainTable()))
                ->joinLeft(array('wa' => $resource->getTable('warehouse/warehouse_area')), 'w.warehouse_id = wa.warehouse_id')
                ->order(array('wa.country_id DESC', 'wa.region_id DESC', 'wa.zip DESC'))
                ->limit(1);
            $orWhere = '('.implode(') OR (', array(
                'wa.country_id = :country_id AND wa.region_id = :region_id AND wa.zip = :postcode',
                'wa.country_id = :country_id AND wa.region_id = :region_id AND wa.zip = \'\'',
                'wa.country_id = :country_id AND wa.region_id = 0 AND wa.zip = :postcode', 
                'wa.country_id = :country_id AND wa.region_id = 0 AND wa.zip = \'\'',
                'wa.country_id = \'0\' AND wa.region_id = 0 AND wa.zip = \'\'', 
            )).')';
            $select->where($orWhere);
            $data = $adapter->fetchRow($select, $bind);
            $warehouse->setData($data);
            if ($warehouse->getId()) {
                $stockId = $warehouse->getStockId();
            }
        }
        return $stockId;
    }
}