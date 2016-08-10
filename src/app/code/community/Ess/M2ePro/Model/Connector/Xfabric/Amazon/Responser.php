<?php

/*
 * @copyright  Copyright (c) 2011 by  ESS-UA.
 */

abstract class Ess_M2ePro_Model_Connector_Xfabric_Amazon_Responser extends Ess_M2ePro_Model_Connector_Xfabric_Responser
{
    private $cachedParamsObjects = array();

    // ########################################

    protected function getObjectByParam($model, $idKey)
    {
        if (isset($this->cachedParamsObjects[$idKey])) {
            return $this->cachedParamsObjects[$idKey];
        }

        if (!isset($this->params[$idKey])) {
            return NULL;
        }

        $this->cachedParamsObjects[$idKey] = Mage::helper('M2ePro/Component_Amazon')
                    ->getObject($model,$this->params[$idKey]);

        return $this->cachedParamsObjects[$idKey];
    }

    protected function validateErrorData($data)
    {
        if (empty($data[parent::ERRORS_KEY])) {
            return false;
        }

        foreach ($data[parent::ERRORS_KEY] as $error) {

            if (isset($error[parent::ERRORS_KEY]) ||
                empty($error[parent::ERROR_CODE_KEY]) ||
                empty($error[parent::ERROR_TEXT_KEY])) {
                return false;
            }
        }

        return true;
    }

    // ########################################
}