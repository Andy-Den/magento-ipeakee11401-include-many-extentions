<?php

/*
 * @copyright  Copyright (c) 2011 by  ESS-UA.
 */

class Ess_M2ePro_Model_Connector_Xfabric_Amazon_Orders_Update_Items extends Ess_M2ePro_Model_Connector_Xfabric_Amazon_Requester
{
    // ########################################

    protected function getTopicPath()
    {
        return array('order','update');
    }

    // ########################################

    protected function getResponserModel()
    {
        return 'Amazon_Orders_Update_ItemsResponser';
    }

    protected function getResponserParams()
    {
        return array(
            'order_id' => $this->params['order']->getId(),
            'tracking_details' => isset($this->params['tracking_details']) ? $this->params['tracking_details'] : array()
        );
    }

    // ########################################

    protected function setLocks($messageHash)
    {
        /** @var $order Ess_M2ePro_Model_Order */
        $order = $this->params['order'];
        $order->addObjectLock('update_shipping_status', $messageHash);
    }

    // ########################################

    protected function getRequestData()
    {
        $update = array(
            'marketOrderId' => $this->params['amazon_order_id'],
            'processed' => null,
            'orderShipmentUpdate' => null
        );

        if (!empty($this->params['tracking_details'])) {
            $itemTrackingDetails = $this->params['tracking_details'];

            $trackingDetails = array(
                'trackingNumber' => $itemTrackingDetails['tracking_number'],
                'carrier' => $itemTrackingDetails['carrier']
            );

            $trackingDetails['service'] = null;
            if (!empty($itemTrackingDetails['shipping_method'])) {
                $trackingDetails['service'] = $itemTrackingDetails['shipping_method'];
            }

            $update['orderShipmentUpdate']['shipmentId'] = '1'; // fake data
            $update['orderShipmentUpdate']['trackingDetails'] = array($trackingDetails);
        }

        return array(
            'updates' => array($update)
        );
    }

    protected function getAvroSchemaVersion()
    {
        return '1.0.0';
    }

    // ########################################

    protected function getAccountIdKey()
    {
        return 'xProfileId';
    }

    // ########################################

}