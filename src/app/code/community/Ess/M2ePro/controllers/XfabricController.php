<?php

/*
 * @copyright  Copyright (c) 2011 by  ESS-UA.
 */

class Ess_M2ePro_XfabricController extends Mage_Core_Controller_Front_Action
{
    private $headers = array();
    private $topic = '';
    private $body = '';

    //#############################################

    public function indexAction()
    {
        $this->headers = @getallheaders();
        $this->topic = $this->getRequest()->getParam('topic');
        $this->body = @file_get_contents('php://input');

        // check input data
        //--------------------
        if (empty($this->headers) || empty($this->topic) || empty($this->body)) {
            $this->sendHttpResponse(false,'Failed: Empty input data');
        }
        //--------------------

        if (Mage::helper('M2ePro/Server')->isDeveloper()) {
            Mage::log(print_r($this->headers,1),NULL,'xfabric_stream.log');
            Mage::log(print_r($this->topic,1),NULL,'xfabric_stream.log');
            Mage::log(print_r($this->body,1),NULL,'xfabric_stream.log');
        }

        // check authentification
        //--------------------
        try {
            $this->xfabricAuthorization();
        } catch (Exception $e) {
            $tempBaseMessage = 'Failed: authorization -> ';
            $this->sendHttpResponse(false,$tempBaseMessage.$e->getMessage());
        }
        //--------------------

        // decode response body
        //--------------------
        try {
            $this->decodeResponseBody();
            if (Mage::helper('M2ePro/Server')->isDeveloper()) {
                Mage::log(print_r($this->body,1),NULL,'xfabric_stream.log');
            }
        } catch (Exception $e) {
            $tempBaseMessage = 'Failed: decode body -> ';
            $this->sendHttpResponse(false,$tempBaseMessage.$e->getMessage());
        }
        //--------------------

        // message failed topic
        //--------------------
        if (strtolower($this->topic) == 'message/failed') {

            try {
                $xfabricRequest = $this->getMessageFailedRequest();
            } catch (Exception $e) {
                $tempBaseMessage = 'Failed: message failed topic -> ';
                $this->sendHttpResponse(false,$tempBaseMessage.$e->getMessage());
            }

            /** @var $xfabricRequest Ess_M2ePro_Model_Xfabric_Request */
            $modelName = $xfabricRequest->getResponserModel();
            $className = Mage::getConfig()->getModelClassName($modelName);

            /** @var $responserObject Ess_M2ePro_Model_Connector_Xfabric_Responser */
            $responserObject = new $className($xfabricRequest);

            $tempMessage = 'Xfabric sending message is failed. Please try again later.';
            !empty($this->body['errorCause']) && $tempMessage = $this->body['errorCause'];

            $responserObject->processMessageFailed($tempMessage);

            if (Mage::helper('M2ePro/Server')->isDeveloper()) {
                Mage::log(print_r($this->body,1),NULL,'xfabric_failed.log');
            }

            $this->sendHttpResponse();
        }
        //--------------------

        // correlation message
        //--------------------
        try {
            $xfabricRequest = $this->getCorrelationRequest();
        } catch (Exception $e) {
            $tempBaseMessage = 'Failed: correlation message -> ';
            $this->sendHttpResponse(false,$tempBaseMessage.$e->getMessage());
        }

        /** @var $endpointObject Ess_M2ePro_Model_Connector_Xfabric_Endpoint */
        $endpointObject = Mage::getModel('M2ePro/Connector_Xfabric_Endpoint');
        $endpointObject->process($xfabricRequest,$this->headers,$this->topic,$this->body);
        //--------------------

        $this->sendHttpResponse();
    }

    //#############################################

    private function xfabricAuthorization()
    {
        if (empty($this->headers['Authorization'])) {
            throw new Exception('Message does not have "Xfabric Token" header');
        }

        $xfabricToken = Mage::helper('M2ePro/Module')->getXfabricFabricToken();

        if ($this->headers['Authorization'] !== 'Bearer '.$xfabricToken) {
            throw new Exception('"Xfabric Token" header is wrong');
        }
    }

    private function decodeResponseBody()
    {
        if (empty($this->body)) {
            throw new Exception('Received body is empty');
        }

        if (empty($this->headers['Content-Type'])) {
            throw new Exception('Content type is not defined');
        }

        /** @var $xfabricEndpoint Ess_M2ePro_Model_Connector_Xfabric_Endpoint */
        $xfabricEndpoint = Mage::getModel('M2ePro/Connector_Xfabric_Endpoint');

        if ($this->headers['Content-Type'] == 'application/json') {

            $this->body = $xfabricEndpoint->decodeAsJson($this->body);

            $this->body['xfabric'] = array();
            $this->body['xfabric']['content_type'] = Ess_M2ePro_Model_Connector_Xfabric_Protocol::CONTENT_TYPE_JSON;

        } elseif ($this->headers['Content-Type'] == 'avro/binary') {

            $schemaUri = isset($this->headers['X-XC-SCHEMA-URI']) ? $this->headers['X-XC-SCHEMA-URI'] : NULL;
            $schemaVersion = isset($this->headers['X-XC-SCHEMA-VERSION']) ? $this->headers['X-XC-SCHEMA-VERSION'] : NULL;

            // may be or an url or an fs path
            if (!Mage::helper('M2ePro/Server')->isDeveloper()) {
                is_null($schemaVersion) && $schemaVersion = '1.0.0';
                is_null($schemaUri) && $schemaUri = Ess_M2ePro_Model_Connector_Xfabric_Endpoint::SCHEMA_BASEURL.
                    $this->topic.'/'.$schemaVersion;
            }

            $this->body = $xfabricEndpoint->decodeAsAvro($this->body,$schemaUri,$schemaVersion);

            $this->body['xfabric'] = array();
            $this->body['xfabric']['content_type'] = Ess_M2ePro_Model_Connector_Xfabric_Protocol::CONTENT_TYPE_AVRO;

        } else {
            throw new Exception('Content type has wrong value');
        }
    }

    //---------------------------------------------

    private function getMessageFailedRequest()
    {
        if (empty($this->body['messageGuid']) && empty($this->headers['X-XC-RESULT-CORRELATION-ID'])) {
            throw new Exception('Message guid nad correlation id is not defined');
        }

        /** @var $xfabricRequest Ess_M2ePro_Model_Xfabric_Request */
        $xfabricRequest = Mage::getModel('M2ePro/Xfabric_Request');

        if (!empty($this->headers['X-XC-RESULT-CORRELATION-ID'])) {
            $xfabricRequest->loadInstance($this->headers['X-XC-RESULT-CORRELATION-ID'],'message_hash');
        } else {
            $xfabricRequest->loadInstance($this->body['messageGuid'],'message_guid');
        }

        if (is_null($xfabricRequest->getId())) {
             throw new Exception('This message not found id DB');
        }

        return $xfabricRequest;
    }

    private function getCorrelationRequest()
    {
        if (empty($this->headers['X-XC-RESULT-CORRELATION-ID'])) {
            throw new Exception('Correlation id is not defined');
        }

        /** @var $xfabricRequest Ess_M2ePro_Model_Xfabric_Request */
        $xfabricRequest = Mage::getModel('M2ePro/Xfabric_Request');
        $xfabricRequest->loadInstance($this->headers['X-XC-RESULT-CORRELATION-ID'],'message_hash');

        if (is_null($xfabricRequest->getId())) {
             throw new Exception('This message not found id DB');
        }

        return $xfabricRequest;
    }

    //---------------------------------------------

    private function sendHttpResponse($success = true, $message = 'OK')
    {
        header('HTTP/1.0 200 OK');
        header('Connection: close');

        if (!$success && Mage::helper('M2ePro/Server')->isDeveloper()) {
            Mage::log($message,NULL,'xfabric_failed.log');
        }

        exit($message);
    }

    //#############################################
}