<?php

/*
 * @copyright  Copyright (c) 2011 by  ESS-UA.
 */

class Ess_M2ePro_Model_Connector_Xfabric_Amazon_Orders_Get_ItemsResponser extends Ess_M2ePro_Model_Connector_Xfabric_Amazon_Responser
{
    // ########################################

    protected function unsetLocks($fail = false, $message = NULL)
    {

    }

    // ########################################

    protected function validateSucceededResponseData($response)
    {
        if(!isset($response['orders'])) {
            return false;
        }
        return true;
    }

    protected function validateFailedResponseData($response)
    {
        if (!$this->validateErrorData($response)) {
            return false;
        }

        return true;
    }

    protected function processSucceededResponseData($response)
    {
        $response = $response['orders'];

        $orders = array();

        foreach ($response as $orderData) {
            $orderEmbeddedMessage = $this->decodeEmbeddedMessage($orderData['embeddedMessage']);

            $order = array();

            $order['amazon_order_id'] = trim($orderData['marketOrderId']);
            $order['status'] = trim($orderData['status']);

            $order['marketplace_id'] = trim($orderEmbeddedMessage['marketplaceId']);
            $order['is_afn_channel'] = $orderEmbeddedMessage['fulfillmentChannel'] == 'AFN' ? 1 : 0;

            $order['purchase_create_date'] = date('Y-m-d H:i:s', $orderData['orderTime']);
            $order['purchase_update_date'] = date('Y-m-d H:i:s', $orderEmbeddedMessage['updateDate']);

            $order['buyer_name'] = trim($orderData['customer']['name']);
            $order['buyer_email'] = trim($orderData['customer']['email']);

            $order['qty_shipped'] = (int)$orderEmbeddedMessage['quantity']['shipped'];
            $order['qty_unshipped'] = (int)$orderEmbeddedMessage['quantity']['unshipped'];

            $shipment = reset($orderData['shipments']);

            $order['shipping_service'] = trim($shipment['shippingServiceOption']['serviceName']);
            $order['shipping_price'] = (float)$shipment['shippingServiceOption']['cost']['amount'];

            $order['shipping_address'] = array(
                'recipient_name' => trim($shipment['recipientName']),
                'county'       => trim($shipment['address']['county']),
                'country_code' => trim($shipment['address']['country']),
                'state'        => trim($shipment['address']['stateOrProvince']),
                'city'         => trim($shipment['address']['city']),
                'postal_code'  => trim($shipment['address']['postalCode']),
                'phone'        => trim($orderData['customer']['phone']),
                'street'       => array(
                    trim($shipment['address']['street1']),
                    trim($shipment['address']['street2'])
                )
            );

            $order['currency'] = trim($orderData['totalAmount']['code']);
            $order['paid_amount'] = (float)$orderData['totalAmount']['amount'];
            $order['tax_amount'] = (float)$orderData['totalTaxAmount']['amount'];
            $order['discount_amount'] = (float)$orderData['totalDiscountAmount']['amount'];

            $order['items'] = array();

            foreach ($orderData['items'] as $item) {
                $itemEmbeddedMessage = $this->decodeEmbeddedMessage($item['embeddedMessage']);

                $order['items'][] = array(
                    'amazon_order_item_id' => trim($item['shipmentId']),
                    'sku'                  => trim($item['sku']),
                    'general_id'           => trim($item['marketListingId']),
                    'is_isbn_general_id'   => $itemEmbeddedMessage['identifierType'] == 'ISBN' ? 1 : 0,
                    'title'                => trim($item['listingTitle']),
                    'price'                => (float)$item['cost']['amount'],
                    'currency'             => trim($item['cost']['code']),
                    'tax_amount'           => (float)$item['taxAmount']['amount'],
                    'discount_amount'      => (float)$item['discountAmount']['amount'],
                    'qty_purchased'        => (int)$itemEmbeddedMessage['quantity']['ordered'],
                    'qty_shipped'          => (int)$itemEmbeddedMessage['quantity']['shipped']
                );
            }

            $orders[] = $order;
        }

        return $orders;
    }

    protected function processFailedResponseData($response)
    {
        return false;
    }
}