<?php

/*
 * @copyright  Copyright (c) 2011 by  ESS-UA.
 */

abstract class Ess_M2ePro_Model_Connector_Xfabric_Requester extends Ess_M2ePro_Model_Connector_Xfabric_Protocol
{
    protected $params = array();

    // ########################################

    public function __construct(array $params = array())
    {
        $this->params = $params;
        parent::__construct();
    }

    // ########################################

    public function process()
    {
        /** @var $xfabricRequest Ess_M2ePro_Model_Xfabric_Request */
        $xfabricRequest = $this->sendRequest();
        $this->setLocks($xfabricRequest->getMessageHash());
    }

    //-----------------------------------------

    abstract protected function setLocks($messageHash);

    // ########################################

    protected function encodeEmbeddedMessage($data)
    {
        if ($this->contentType == Ess_M2ePro_Model_Connector_Xfabric_Protocol::CONTENT_TYPE_JSON) {
            return $data['payload'];
        }

        /** @var $xfabricEndpoint Ess_M2ePro_Model_Connector_Xfabric_Endpoint */
        $xfabricEndpoint = Mage::getModel('M2ePro/Connector_Xfabric_Endpoint');

        $schemaUri = isset($data['schemaUri']) ? $data['schemaUri'] : NULL;
        $schemaVersion = isset($data['schemaVersion']) ? $data['schemaVersion'] : NULL;

        $payload = $xfabricEndpoint->encodeAsAvro($data['payload'],$schemaUri,$schemaVersion);
        $payload = utf8_encode($payload);

        return trim(json_encode($payload),'"');
    }

    // ########################################
}