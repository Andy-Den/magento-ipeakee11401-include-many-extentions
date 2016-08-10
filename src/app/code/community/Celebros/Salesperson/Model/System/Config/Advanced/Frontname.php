<?php

class Celebros_Salesperson_Model_System_Config_Advanced_Frontname extends Mage_Core_Model_Config_Data
    {

        protected function _beforeSave()
        {
            $newFrontname=$this->value=str_replace(' ','',$this->getValue());

            if (!ctype_alnum($newFrontname))
                Mage::throwException("Frontname must contain only alphanumeric
                character(s)");

            switch ($newFrontname)
            {
                case 'catalogsearch':
                    Mage::throwException("catalogsearch is reserved for Magento's
                    search");
                    break;
                case '':
                    Mage::throwException("Frontname cannot be empty");
                    break;
            }

            $config_file=Mage::getModuleDir('etc',"Celebros_Salesperson")
                .DIRECTORY_SEPARATOR
                .'config.xml';

            //$layout_file=Mage::getBaseDir('design').DIRECTORY_SEPARATOR.'frontend'.DIRECTORY_SEPARATOR.'base'.DIRECTORY_SEPARATOR.'default'.DIRECTORY_SEPARATOR.'layout'.DIRECTORY_SEPARATOR.'salesperson.xml';


            $isConfigWritable=is_writable($config_file);
            //$isLayoutWritable=is_writable($layout_file);

            if ($isConfigWritable) //&& $isLayoutWritable)
            {
                // salesperson config.xml file
                // ***************************

                $config_xml=simplexml_load_file($config_file);

                $config_xml->frontend->routers->{'salesperson'}->args->frontName=$newFrontname;

                $configWrite=$config_xml->asXML($config_file);

                if (!$configWrite)
                    Mage::throwException("Error while saving new frontname to config
                    .xml");

                // salesperson.xml layout file
                // ***************************
/*
                $currentFrontname=$this->getOldValue();

                $layout_content=file_get_contents($layout_file);

                $layout_content=str_replace($currentFrontname.'_result_index', $newFrontname.'_result_index',$layout_content);

                $layout_content=str_replace($currentFrontname.'_result_change', $newFrontname.'_result_change',$layout_content);

                $layout_content=str_replace($currentFrontname.'_giftfinder_index', $newFrontname.'_giftfinder_index',$layout_content);

                $layout_content=str_replace($currentFrontname.'_giftfinder_change', $newFrontname.'_giftfinder_change',$layout_content);
*/

                //$layout_file_new=Mage::getBaseDir('design').DIRECTORY_SEPARATOR.'frontend'.DIRECTORY_SEPARATOR.'base'.DIRECTORY_SEPARATOR.'default'.DIRECTORY_SEPARATOR.'layout'.DIRECTORY_SEPARATOR.'salesperson_new.xml';
//                file_put_contents($layout_file,$layout_content, LOCK_EX );


                $resource = new Mage_Core_Model_Config();

                $resource->saveConfig('salesperson/advanced_settings/frontname_name', $newFrontname, 'default', 0);

                $resource->saveConfig('salesperson/advanced_settings/frontname_enabled', false, 'default', 0);
            }
            else
                Mage::throwException("config.xml must be
                writable!");
        }
    }
