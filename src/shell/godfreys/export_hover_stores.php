<?php
require_once '../abstract.php';
set_time_limit(0);
ini_set("memory_limit","2000M");


class Mage_Shell_Godfrey_Exporthhoverstores extends Mage_Shell_Abstract
{
    function run()
        {
            if ($this->getArg('run')) { //DIRECTORY_SEPARATOR
                Mage::app('admin');
                $header = array(
                    "store_title",
                    "latitude",
                    "longitude",
                    "meta_description",
                    "meta_keyword",
                    "meta_title",
                    "store_address",
                    "store_city",
                    "store_state",
                    "store_description",
                    "store_hours_friday","store_hours_monday","store_hours_saturday","store_hours_sunday","store_hours_thursday","store_hours_tuesday","store_hours_wednesday"
                ,"store_phone","store_postcode","store_serviced_suburbs","store_email","store_fax","store_id","country"
                );

                //country, store_news, sotre_url are not exist in old store
                $currentStoreArray = array();
                $fileName = 'hover_store_locator.csv';
                $fileNamePath = Mage::getBaseDir().DS.'var'.DS.$fileName;
                @unlink($fileNamePath);
                $fp = fopen($fileNamePath, 'w');
                fputcsv($fp,$header);
                $hoverSotes = $this->getHoverStores();
                echo "Start export Hover store locations\n";
                if($hoverSotes->getSize()){
                    $i =0;
                    $arrayPostCode = array();
                    $arrayPostCodeDu = array();
                    foreach ($hoverSotes as $hStore) {
                        $i++;
                        $data = array();
                        $productDetail = Mage::getModel('catalog/product')->load($hStore->getId());
                        if (!in_array($productDetail->getData('store_postcode'),$arrayPostCode)) {
                            foreach ($header as $storeField) {
                                $data[$storeField] = $productDetail->getData($storeField);
                            }
                        }
                        else {
                            $arrayPostCodeDu = $productDetail->getData('store_postcode').':'.$productDetail->getId();
                        }
                        if (in_array(9,$productDetail->getStoreIds()) || in_array(10,$productDetail->getStoreIds()) ){
                            $productStores = $productDetail->getStoreIds();
                            //set value for country
                            $country = "Australia";
                            if (in_array(3,$productStores)) {
                                $country = "New Zealand";
                                for ($i=0; $i<count($productStores);$i++) {
                                    if ($productStores[$i] == 9) {
                                        $data['store_id'] = 10;
                                    }
                                }
                            }
                            else {
                                for ($i=0; $i<count($productStores);$i++) {
                                    if ($productStores[$i] == 10) {
                                        $data['store_id'] = 9;
                                    }
                                }
                            }


                            $data['country'] = $country;
                            fputcsv($fp,$data);
                        }


                    }
                    Mage::log($arrayPostCodeDu,null,"exportHoverStore.csv");
                }
                fclose($fp);
                echo "Export hover store location completely\n";
                echo "Please check result file ".$fileNamePath;
                exit;
            }
            else {
                echo $this->usageHelp();
            }
        }

        function getHoverStores()
        {
            $collection = Mage::getModel('catalog/product')->getCollection();
            $collection
                ->joinField('qty',
                    'cataloginventory/stock_item',
                    'qty',
                    'product_id=entity_id',
                    '{{table}}.stock_id=1',
                    'left');
            $collection->addAttributeToSelect('sku');
            $collection->addAttributeToSelect('price');
            $collection->addAttributeToSelect('status');
            $collection->addAttributeToSelect('visibility');

            $Attribute = new Mage_Eav_Model_Entity_Setup('core_setup');
            $IDS = $Attribute->getAllAttributeSetIds();
            foreach($IDS as $Id)
            {
                $attribute_set = Mage::getModel('eav/entity_attribute_set')->load($Id);
                if($attribute_set)
                {
                    $attribute_set_Name = $attribute_set->getAttributeSetName();
                    if($attribute_set_Name == 'Stockist')
                    {
                        $AttributeSetId = $Id;
                    }
                }
            }
            $collection->addFieldToFilter(array(
                array('attribute'=>'attribute_set_id','eq'=>$AttributeSetId),
            ));

            return $collection;
        }





    /**
     * Retrieve Usage Help Message
     *
     */
    public function usageHelp()
    {
        return <<<USAGE
Usage:  php export_hover_stores.php -- [options]
    --run run export script

USAGE;
    }
}

$shell = new Mage_Shell_Godfrey_Exporthhoverstores();
$shell->run();
