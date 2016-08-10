<?php

/*
 * @copyright  Copyright (c) 2011 by  ESS-UA.
 */

abstract class Ess_M2ePro_Model_Connector_Xfabric_Responser
{
    const ERRORS_KEY = 'errors';

    const ERROR_CODE_KEY = 'code';
    const ERROR_TEXT_KEY = 'message';
    const ERROR_SENDER_KEY = 'sender';

    const ERROR_SENDER_SYSTEM = 'system';
    const ERROR_SENDER_COMPONENT = 'component';

    // ########################################

    /** @var Ess_M2ePro_Model_Xfabric_Request */
    protected $xfabricRequest = NULL;

    protected $params = array();
    protected $messageHash = NULL;

    protected $topicPath = array();
    protected $requestData = array();

    protected $errors = array();
    protected $contentType = Ess_M2ePro_Model_Connector_Xfabric_Protocol::CONTENT_TYPE_JSON;

    // ########################################

    public function __construct(Ess_M2ePro_Model_Xfabric_Request $xfabricRequest)
    {
        $this->xfabricRequest = $xfabricRequest;

        $this->params = $this->xfabricRequest->getDecodedResponserParams();
        $this->messageHash = $this->xfabricRequest->getMessageHash();

        $this->topicPath = $this->xfabricRequest->getDecodedTopicPath();
        $this->requestData = $this->xfabricRequest->getDecodedRequestBody();
    }

    // ########################################

    public function process(array $responseBody = array(), $isFailed = false)
    {
        $this->contentType = $responseBody['xfabric']['content_type'];

        try {

            $isFailed && $this->processResponseErrors($responseBody);

            if ($isFailed) {
                $tempResult = $this->validateFailedResponseData($responseBody);
            } else {
                $tempResult = $this->validateSucceededResponseData($responseBody);
            }

            if (!$tempResult) {
                throw new Exception('Validation Failed. The server response data is not valid.');
            }

            if ($isFailed) {
                $this->processFailedResponseData($responseBody);
            } else {
                $this->processSucceededResponseData($responseBody);
            }

        } catch (Exception $exception) {
            $this->unsetLocks(true,$exception->getMessage());
            $this->xfabricRequest->deleteInstance();
            return;
        }

        $this->unsetLocks();
        $this->xfabricRequest->deleteInstance();
    }

    public function processMessageFailed($message)
    {
        try {
            $this->unsetLocks(true,$message);
        } catch (Exception $exception) {}

        $this->xfabricRequest->deleteInstance();
    }

    // ########################################

    abstract protected function unsetLocks($isFailed = false, $message = NULL);

    //-----------------------------------------

    abstract protected function validateSucceededResponseData($response);

    abstract protected function validateFailedResponseData($response);

    //-----------------------------------------

    abstract protected function processSucceededResponseData($response);

    abstract protected function processFailedResponseData($response);

    // ########################################

    protected function processResponseErrors($responseData)
    {
        $tempErrors = $this->processDataErrors($responseData);
        is_array($tempErrors) && $this->errors = $tempErrors;

        $errorHasSystem = '';

        foreach ($this->errors as $error) {
            if (!isset($error[self::ERROR_SENDER_KEY])) {
                continue;
            }
            if ($error[self::ERROR_SENDER_KEY] == self::ERROR_SENDER_SYSTEM) {
                $errorHasSystem != '' && $errorHasSystem .= ', ';
                $errorHasSystem .= $error[self::ERROR_TEXT_KEY];
            }
        }

        if ($errorHasSystem != '') {
            throw new Exception("Internal server error(s) [{$errorHasSystem}]");
        }
    }

    protected function processDataErrors($data)
    {
        if (!isset($data[self::ERRORS_KEY])) {
            return false;
        }

        $errors = array();

        foreach ($data[self::ERRORS_KEY] as $error) {

            if (isset($error[self::ERRORS_KEY]) ||
                empty($error[self::ERROR_CODE_KEY]) ||
                empty($error[self::ERROR_TEXT_KEY])) {
                continue;
            }

            $error[self::ERROR_SENDER_KEY] = self::ERROR_SENDER_SYSTEM;

            if (strpos($error[self::ERROR_CODE_KEY],'-'.self::ERROR_SENDER_COMPONENT) !== false) {
                $error[self::ERROR_SENDER_KEY] = self::ERROR_SENDER_COMPONENT;
            }

            $error[self::ERROR_CODE_KEY] = (int)$error[self::ERROR_CODE_KEY];

            $errors[] = $error;
        }

        if (count($errors) <= 0) {
            return false;
        }

        return $errors;
    }

    // ########################################

    protected function decodeEmbeddedMessage($data)
    {
        if ($this->contentType == Ess_M2ePro_Model_Connector_Xfabric_Protocol::CONTENT_TYPE_JSON) {
            return $data['payload'];
        }

        /** @var $xfabricEndpoint Ess_M2ePro_Model_Connector_Xfabric_Endpoint */
        $xfabricEndpoint = Mage::getModel('M2ePro/Connector_Xfabric_Endpoint');

        $schemaUri = isset($data['schemaUri']) ? $data['schemaUri'] : NULL;
        $schemaVersion = isset($data['schemaVersion']) ? $data['schemaVersion'] : NULL;

        $payload = json_decode('"'.$data['payload'].'"');
        $payload = utf8_decode($payload);

        return $xfabricEndpoint->decodeAsAvro($payload,$schemaUri,$schemaVersion);
    }

    // ########################################
}