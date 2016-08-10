<?php

/*
 * @copyright  Copyright (c) 2011 by  ESS-UA.
 */

class Ess_M2ePro_Model_Connector_Xfabric_Endpoint
{
    const TOPIC_FAILED_SUFFIX = 'Failed';
    const SCHEMA_BASEURL = 'https://api.x.com/ocl/';

    static private $avroSchemaCacheData = array();

    // ########################################

    public function process($xfabricRequest, $headers, $topic, $body)
    {
        $isFailed = false;

        if (strlen($topic) > strlen(self::TOPIC_FAILED_SUFFIX)) {
            $suffix = substr($topic,strlen($topic)-strlen(self::TOPIC_FAILED_SUFFIX));
            if (strtolower($suffix) == strtolower(self::TOPIC_FAILED_SUFFIX)) {
                $isFailed = true;
            }
        }

        /** @var $xfabricRequest Ess_M2ePro_Model_Xfabric_Request */
        $modelName = $xfabricRequest->getResponserModel();
        $className = Mage::getConfig()->getModelClassName($modelName);

        /** @var $responserObject Ess_M2ePro_Model_Connector_Xfabric_Responser */
        $responserObject = new $className($xfabricRequest);
        $responserObject->process($body,$isFailed);
    }

    public function isSystemTopic($topic)
    {
        $systemMessages = array(
            'xfabric/capability/endpoint'
        );
        return in_array(trim($topic,'/'),$systemMessages);
    }

    // ########################################

    public function encodeAsJson(array $data)
    {
        return (string)@json_encode($data);
    }

    public function encodeAsAvro(array $data, $schemaUri, $schemaVersion, $isSystemMessage = false)
    {
        require_once('lib/ApacheAvro/avro.php');

        $schemaObj = $this->getSchemaObject($schemaUri,$schemaVersion);

        $write_io = new AvroStringIO();
        $datum_writer = new AvroIODatumWriter($schemaObj);

        if ($isSystemMessage && Mage::helper('M2ePro/Server')->isDeveloper()) {
            $data_writer = new AvroDataIOWriter($write_io, $datum_writer, $schemaObj);
            $data_writer->append($data);
            $data_writer->close();
        } else {
            $encoder = new AvroIOBinaryEncoder($write_io);
            $datum_writer->write($data, $encoder);
        }

        return $write_io->string();
    }

    //---------------------------------------------

    public function decodeAsJson($data)
    {
        return (array)@json_decode($data,true);
    }

    public function decodeAsAvro($data, $schemaUri = NULL, $schemaVersion = NULL)
    {
        require_once('lib/ApacheAvro/avro.php');

        if (is_null($schemaUri)) {
            return $this->decodeAsAvroWithoutSchema($data);
        }

        try {
            $schemaObj = $this->getSchemaObject($schemaUri,$schemaVersion);
        } catch (Exception $e) {
            return $this->decodeAsAvroWithoutSchema($data);
        }

        $datum_reader = new AvroIODatumReader($schemaObj);
        $read_io = new AvroStringIO($data);
        $decoder = new AvroIOBinaryDecoder($read_io);

        return $datum_reader->read($decoder);
    }

    // ########################################

    private function getSchemaModel($file, $version = NULL)
    {
        if (isset(self::$avroSchemaCacheData['model_'.$file.(string)$version])) {
            return self::$avroSchemaCacheData['model_'.$file.(string)$version];
        }

        /** @var $xfabricSchema Ess_M2ePro_Model_Xfabric_Schema */
        $xfabricSchema = Mage::getModel('M2ePro/Xfabric_Schema');

        /** @var $xfabricSchemaCollection Mage_Core_Model_Mysql4_Collection_Abstract */
        $xfabricSchemaCollection = $xfabricSchema->getCollection();

        $xfabricSchemaCollection->addFieldToFilter('file',$file);
        $xfabricSchemaCollection->addFieldToFilter('version',$version);

        $items = $xfabricSchemaCollection->getItems();

        if (count($items) <= 0) {

            $dataForAdd = array(
                'file' => $file,
                'version' => $version
            );

            /** @var $xfabricSchema Ess_M2ePro_Model_Xfabric_Schema */
            $xfabricSchema = Mage::getModel('M2ePro/Xfabric_Schema');
            $xfabricSchema->addData($dataForAdd)->save();

        } else {
            $xfabricSchema = array_shift($items);
        }

        self::$avroSchemaCacheData['model_'.$file.(string)$version] = $xfabricSchema;

        return $xfabricSchema;
    }

    private function getSchemaData($file, $version = NULL)
    {
        if (isset(self::$avroSchemaCacheData['raw_'.$file.(string)$version])) {
            return self::$avroSchemaCacheData['raw_'.$file.(string)$version];
        }

        $xfabricSchema = $this->getSchemaModel($file,$version);
        $schemaData = $xfabricSchema->getBody();

        if (empty($schemaData)) {

            $schemaData = @file_get_contents($file);
            if (empty($schemaData)) {
                throw new Exception('Xfabric get avro schema is failed. Please try again later.');
            }
            $xfabricSchema->addData(array('body' => $schemaData))->save();
        }

        self::$avroSchemaCacheData['raw_'.$file.(string)$version] = $schemaData;

        return $schemaData;
    }

    private function getSchemaObject($file, $version = NULL)
    {
        if (isset(self::$avroSchemaCacheData['object_'.$file.(string)$version])) {
            return self::$avroSchemaCacheData['object_'.$file.(string)$version];
        }

        $xfabricSchema = $this->getSchemaModel($file,$version);
        $schema = $xfabricSchema->getObject();

        if (empty($schema)) {

            $schemaData = $this->getSchemaData($file,$version);
            $schema = AvroSchema::parse($schemaData);
            $xfabricSchema->addData(array('object' => @serialize($schema)))->save();

        } else {
            $schema = @unserialize($schema);
        }

        self::$avroSchemaCacheData['object_'.$file.(string)$version] = $schema;

        return $schema;
    }

    //----------------------------------------

    private function decodeAsAvroWithoutSchema($data)
    {
        $read_io = new AvroStringIO($data);
        $data_reader = new AvroDataIOReader($read_io, new AvroIODatumReader());
        $data = $data_reader->data();
        return array_shift($data);
    }

    // ########################################
}