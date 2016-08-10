<?php

/*
 * @copyright  Copyright (c) 2011 by  ESS-UA.
 */

class Ess_M2ePro_Model_Connector_Xfabric_System_Dispatcher
{
    //####################################

    /**
     * @throws Exception
     * @param string $entity
     * @param string $type
     * @param string $name
     * @param array $params
     * @param null|string $ormPrefixToConnector
     * @return Ess_M2ePro_Model_Connector_Xfabric_System_Requester
     */
    public function getConnector($entity, $type, $name,
                                 array $params = array(),
                                 $ormPrefixToConnector = NULL)
    {
        $entity = uc_words(trim($entity));
        $type = uc_words(trim($type));
        $name = uc_words(trim($name));

        $className = 'Ess_M2ePro_Model_Connector_Xfabric_System';
        !empty($ormPrefixToConnector) && $className = $ormPrefixToConnector;

        $entity != '' && $className .= '_'.$entity;
        $type != '' && $className .= '_'.$type;
        $name != '' && $className .= '_'.$name;

        $object = new $className($params);

        return $object;
    }

    //####################################
    
    /**
     * @param string $entity
     * @param string $type
     * @param string $name
     * @param array $params
     * @param null|string $ormPrefixToConnector
     * @return mixed
     */
    public function processConnector($entity, $type, $name,
                                     array $params = array(),
                                     $ormPrefixToConnector = NULL)
    {
        $object = $this->getConnector($entity, $type, $name, $params,$ormPrefixToConnector);

        return $object->process();
    }

    /**
     * @param array $topicPath
     * @param string $responserModel
     * @param array $responserParams
     * @param array $requestData
     * @param null|string $ormPrefixToConnector
     * @return mixed
     */
    public function processVirtual(array $topicPath,
                                   $responserModel = 'System_VirtualResponser',
                                   array $responserParams = array(),
                                   array $requestData = array(),
                                   $ormPrefixToConnector = NULL)
    {
        $params = array();
        $params['__topic_path__'] = $topicPath;
        $params['__responser_model__'] = $responserModel;
        $params['__responser_params__'] = $responserParams;
        $params['__request_data__'] = $requestData;
        return $this->processConnector('virtualRequester','','',$params,$ormPrefixToConnector);
    }

    //####################################
}