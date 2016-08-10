<?php

/*
 * @copyright  Copyright (c) 2011 by  ESS-UA.
 */

abstract class Ess_M2ePro_Model_Connector_Xfabric_Protocol extends Ess_M2ePro_Model_Connector_Protocol
{
    const CONTENT_TYPE_AVRO = 1;
    const CONTENT_TYPE_JSON = 2;

    protected $contentType = self::CONTENT_TYPE_AVRO;

    // ########################################

    protected $endpoint = '';

    protected $fabricToken = '';
    protected $capabilityToken = '';

    protected $tenantId = '';
    protected $tenantToken = '';

    protected $destinationId = '';

    // ########################################

    public function __construct()
    {
        $this->endpoint = Mage::helper('M2ePro/Ess')->getXfabricEndpoint();
        $this->endpoint = rtrim($this->endpoint,'/').'/';

        $this->fabricToken = Mage::helper('M2ePro/Module')->getXfabricFabricToken();
        $this->capabilityToken = Mage::helper('M2ePro/Module')->getXfabricCapabilityToken();

        $this->tenantId = Mage::helper('M2ePro/Module')->getXfabricTenantId();
        $this->tenantToken = Mage::helper('M2ePro/Module')->getXfabricTenantToken();

        $this->destinationId = $this->getDestinationId();
    }

    // ########################################

    protected function sendRequest()
    {
        // Get topic path
        //------------------
        $topicPath = $this->getTopicPath();
        if (!is_array($topicPath) || !isset($topicPath[0]) || !isset($topicPath[1])) {
            throw new Exception('Requested topic has invalid format.');
        }
        //------------------

        // Create request body
        //------------------
        $requestData = $this->getRequestData();

        !is_array($requestData) && $requestData = array();
        $requestData = array_merge_recursive($requestData,$this->requestExtraData);
        //------------------

        // Send Message
        //------------------
        $messageHash = $this->createMessageHash();
        $messageGuid = $this->sendMessageToFabric($messageHash,$topicPath,$requestData);
        //------------------

        // Create xfabric request
        //------------------
        $dataForAdd = array(
            'message_hash' => $messageHash,
            'message_guid' => $messageGuid,
            'topic_path' => json_encode($topicPath),
            'request_body' => json_encode($requestData),
            'responser_model' => $this->makeResponserModel(),
            'responser_params' => json_encode((array)$this->getResponserParams())
        );

        return Mage::getModel('M2ePro/Xfabric_Request')->setData($dataForAdd)->save();
        //------------------
    }

    protected function makeResponserModel()
    {
        return 'M2ePro/Connector_Xfabric_'.(string)$this->getResponserModel();
    }

    // ----------------------------------------

    private function sendMessageToFabric($messageHash, array $topic, $body)
    {
        // Prepare Topic
        //------------------
        $topic = implode('/',$topic);
        //------------------

        // Prepare Headers
        //------------------
        $headers = array();

        if ($this->contentType == self::CONTENT_TYPE_AVRO) {
            $headers[] = 'Content-Type: avro/binary';
            $headers[] = 'X-XC-SCHEMA-VERSION: '.$this->getAvroSchemaVersion();
            $headers[] = 'X-XC-SCHEMA-URI: '.$this->getAvroSchemaUri($topic);
        } else if ($this->contentType == self::CONTENT_TYPE_JSON) {
            $headers[] = 'Content-Type: application/json';
        }

        $headers[] = 'Authorization: Bearer '.$this->getAuthorizationBearer();

        if (!empty($this->destinationId)) {
            $headers[] = 'X-XC-DESTINATION-ID: '.$this->destinationId;
        }

        $headers[] = 'X-XC-RESULT-CORRELATION-ID: '.$messageHash;
        //------------------

        // Prepare Body
        //------------------
        $body = $this->encodeBody($topic,$body);
        //------------------

        // Make http POST request
        $responseData = $this->makeHttpPostRequest($this->endpoint.$topic,$headers,$body);

        // Process http code
        //------------------
        if ($responseData['http_code'] != '200') {
            throw new Exception('Xfabric catching message is failed. Please try again later.');
        }
        //------------------

        return $responseData['headers']['X-XC-MESSAGE-GUID'];
    }

    protected function getAuthorizationBearer()
    {
        return $this->tenantToken;
    }

    private function makeHttpPostRequest($url, $headers, $body)
    {
        $curlHandler = curl_init();

        curl_setopt($curlHandler, CURLOPT_URL, $url);
        curl_setopt($curlHandler, CURLOPT_HEADER, true);
        curl_setopt($curlHandler, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curlHandler, CURLOPT_TIMEOUT, 10);
        curl_setopt($curlHandler, CURLOPT_POST, true);
        curl_setopt($curlHandler, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curlHandler, CURLOPT_POSTFIELDS, $body);

        $response = curl_exec($curlHandler);

        if ($response === false) {
            throw new Exception('Xfabric connection is failed. Please try again later.');
        }

        $header_size = curl_getinfo($curlHandler,CURLINFO_HEADER_SIZE);
        $header_block = substr($response, 0, $header_size);
        $allheaders = explode("\r\n", $header_block); // split the header text into lines
        array_shift($allheaders); // drop the first line since that's the HTTP status line

        $headers = array();
        foreach ($allheaders as $header) {
            $splitheader = explode(": ", $header, 2);
            if (count($splitheader) == 2) {
                $headers[$splitheader[0]] = $splitheader[1];
            }
        }

        $result = array();

        $result['headers'] = $headers;
        $result['http_code'] = curl_getinfo($curlHandler,CURLINFO_HTTP_CODE);
        $result['body'] = trim(strip_tags(substr( $response, $header_size )));

        return $result;
    }

    // ########################################

    /**
     * @abstract
     * @return array
     */
    abstract protected function getTopicPath();

    /**
     * @abstract
     * @return string
     */
    abstract protected function getDestinationId();

    //----------------------------------------

    /**
     * @abstract
     * @return string
     */
    abstract protected function getResponserModel();

    /**
     * @abstract
     * @return array
     */
    abstract protected function getResponserParams();

    //----------------------------------------

    protected function getAvroSchemaVersion()
    {
        return '1.0.0';
    }

    protected function getAvroSchemaUri($topic)
    {
        return Ess_M2ePro_Model_Connector_Xfabric_Endpoint::SCHEMA_BASEURL.
               $topic.'/'.$this->getAvroSchemaVersion();
    }

    // ########################################

    private function encodeBody($topic, $body)
    {
        /** @var $xfabricEndpoint Ess_M2ePro_Model_Connector_Xfabric_Endpoint */
        $xfabricEndpoint = Mage::getModel('M2ePro/Connector_Xfabric_Endpoint');

        if ($this->contentType == self::CONTENT_TYPE_JSON) {

            $body = $xfabricEndpoint->encodeAsJson($body);

        } elseif ($this->contentType == self::CONTENT_TYPE_AVRO) {

            try {

                // may be or an url or an fs path
                $schemaVersion = $this->getAvroSchemaVersion();
                $schemaUri = $this->getAvroSchemaUri($topic);

                $isSystemMessage = $xfabricEndpoint->isSystemTopic($topic);
                $body = $xfabricEndpoint->encodeAsAvro($body,$schemaUri,$schemaVersion,$isSystemMessage);

            } catch (Exception $exception) {
                throw new Exception('Xfabric encode to avro format is wrong.');
            }

        } else {
            throw new Exception('Xfabric content type has wrong value.');
        }

        return $body;
    }

    private function createMessageHash()
    {
        $domain = Mage::helper('M2ePro/Server')->getDomain();
        return sha1($domain.time().rand(0,100000000));
    }

    // ########################################
}