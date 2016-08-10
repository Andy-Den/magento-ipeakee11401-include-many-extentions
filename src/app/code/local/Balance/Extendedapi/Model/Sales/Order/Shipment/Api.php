<?php

class Balance_Extendedapi_Model_Sales_Order_Shipment_Api extends Mage_Sales_Model_Order_Shipment_Api
{

    /**
     * Create new shipment for order (Extended)
     *
     * @param string $orderIncrementId
     * @param array $itemsQty
     * @param string $comment
     * @param booleam $email
     * @param boolean $includeComment
	 * @param string $carrier
     * @param string $title
     * @param string $trackNumber
     * @return string
     */
    public function create($orderIncrementId, $itemsQty = array(), $comment = null, $email = false, $includeComment = false,$carrier = "",$title="",$trackNumber="")
    {
		Mage::log("Params - OrderID:" . $orderIncrementId . ", Item Array:" . print_r($itemsQty,true) . ", Comment:" . $comment . ", Send Email:" . $email . ", Incl. Comment:" . $includeComment . ", Carrier:" . $carrier . ", Custom Title:" . $title . ", Tracking No::" . $trackNumber , null , "Balance_ExtendedApi.log");
		
        $order = Mage::getModel('sales/order')->loadByIncrementId($orderIncrementId);

        /**
          * Check order existing
          */
        if (!$order->getId()) {
             $this->_fault('order_not_exists');
        }

        /**
         * Check shipment create availability
         */
        if (!$order->canShip()) {
             $this->_fault('data_invalid', Mage::helper('sales')->__('Cannot do shipment for order.'));
        }

         /* @var $shipment Mage_Sales_Model_Order_Shipment */
        $shipment = $order->prepareShipment($itemsQty);
        if ($shipment) {
            $shipment->register();
            $shipment->addComment($comment, $email && $includeComment);
            
            if ($email) {
                $shipment->setEmailSent(true);
            }
            $shipment->getOrder()->setIsInProcess(true);
            try {
                $transactionSave = Mage::getModel('core/resource_transaction')
                    ->addObject($shipment)
                    ->addObject($shipment->getOrder())
                    ->save();
                if ($trackNumber!="")
                {
                    $this->addTrack($shipment->getIncrementId(),$carrier,$title,$trackNumber);
                }
                $shipment->sendEmail($email, ($includeComment ? $comment : ''));
            } catch (Mage_Core_Exception $e) {
                $this->_fault('data_invalid', $e->getMessage());
            }
            return $shipment->getIncrementId();
        }
        return null;
    }    
} 