<?php

/*
 * @copyright  Copyright (c) 2011 by  ESS-UA.
 */

class Ess_M2ePro_Model_Connector_Xfabric_Amazon_Orders_Update_ItemsResponser extends Ess_M2ePro_Model_Connector_Xfabric_Amazon_Responser
{
    // Parser hack -> Mage::helper('M2ePro')->__('Amazon Order status was not updated. Reason: %msg%');
    // Parser hack -> Mage::helper('M2ePro')->__('Amazon Order status was updated to Shipped.');
    // Parser hack -> Mage::helper('M2ePro')->__('Tracking number "%num%" for "%code%" has been sent to Amazon.');
    const LOG_SHIPPING_STATUS_NOT_UPDATED = 'Amazon Order status was not updated. Reason: %msg%';
    const LOG_SHIPPING_STATUS_UPDATED     = 'Amazon Order status was updated to Shipped.';
    const LOG_TRACK_SUBMITTED             = 'Tracking number "%num%" for "%code%" has been sent to Amazon.';

    // ########################################

    /**
     * @return Ess_M2ePro_Model_Order
     */
    private function getOrder()
    {
        return $this->getObjectByParam('Order', 'order_id');
    }

    // ########################################

    protected function unsetLocks($fail = false, $message = NULL)
    {
        $this->getOrder()->deleteObjectLocks('update_shipping_status', $this->messageHash);
    }

    // ########################################

    protected function validateSucceededResponseData($response)
    {
        if (!isset($response['updates'])) {
            return false;
        }

        return true;
    }

    protected function validateFailedResponseData($response)
    {
        if (empty($response[parent::ERRORS_KEY])) {
            return false;
        }

        foreach ($response[parent::ERRORS_KEY] as $error) {
            if (!isset($error['orderUpdate'])) {
                return false;
            }

            if (!$this->validateErrorData($error)) {
                return false;
            }
        }

        return true;
    }

    //-----------------------------------------

    protected function processSucceededResponseData($response)
    {
        $this->getOrder()->setData('status', Ess_M2ePro_Model_Amazon_Order::STATUS_SHIPPED)->save();
        $this->getOrder()->addSuccessLog(self::LOG_SHIPPING_STATUS_UPDATED);

        if (!empty($this->params['tracking_details'])) {
            $log = $this->getOrder()->makeLog(self::LOG_TRACK_SUBMITTED, array(
                '!num' => $this->params['tracking_details']['tracking_number'],
                'code' => $this->params['tracking_details']['carrier']
            ));
            $this->getOrder()->addSuccessLog($log);
        }
    }

    protected function processFailedResponseData($response)
    {
        foreach ($response['errors'] as $error) {
            if (!isset($error['errors']) || !is_array($error['errors'])) {
                continue;
            }

            foreach ($error['errors'] as $singleError) {
                $message = $this->getOrder()->makeLog(self::LOG_SHIPPING_STATUS_NOT_UPDATED, array(
                    'msg' => $singleError
                ));

                $this->getOrder()->addErrorLog($message);
            }
        }
    }
}