<?php

class Godfreys_Locator_Model_Import extends Varien_Object
{
    protected $headers = array();
    protected $_attributeModels = array();
    protected $_attributeOptions = array();

    protected $_skipped = array();

    public function __construct(){
        $this->mapper = array(
            'title' => 'store_title',
            'store_phone' => 'store_phone',
            'store_description' => 'store_description',
            'store_serviced_suburbs' => 'store_serviced_suburbs',
            'address' => 'store_address',
            'locality' => 'store_city',
            'administrative_area' => 'store_state',
            'latitude' => 'latitude',
            'longitude' => 'longitude',
            'country' => 'country',
            'store_hours_monday' => 'store_hours_monday',
            'store_hours_tuesday' => 'store_hours_tuesday',
            'store_hours_wednesday' => 'store_hours_wednesday',
            'store_hours_thursday' => 'store_hours_thursday',
            'store_hours_friday' => 'store_hours_friday',
            'store_hours_saturday' => 'store_hours_saturday',
            'store_hours_sunday' => 'store_hours_sunday',
            'meta_title' => 'meta_title',
            'meta_description' => 'meta_description',
            'meta_keywords' => 'meta_keywords',
            'url_key' => 'store_url',
            'postal_code' => 'store_postcode',
            'administrative_area' => 'store_state',
            'locality' => 'store_city',
            'store_fax'=>'store_fax',
            'website_based_stores'=>'store_id'
        );
    }

    /**
     *
     */
    public function run()
    {
        $filepath =  realpath(dirname(__FILE__)).'/../data/stores.csv';
        $i = 0;
        $storeImported = false;
        if(($handle = fopen("$filepath", "r")) !== false) {
            while(($data = fgetcsv($handle, 1000, ",")) !== false){
                if($i==0){
                    $this->setHeaders($data);
                }else{
                    $this->saveFromCsv($this->parseCsv($data));
                }
                $i++;
            }
            $storeImported = true;
            if(count($this->_skipped)){
                $this->log(count($this->_skipped).' stores were skipped');
            }
            fclose($handle);
        }
        else{
            Mage::getSingleton('adminhtml/session')->addError("There is some Error");
        }
        if($storeImported) {
            rename($filepath,realpath(dirname(__FILE__)).'/../data/stores_imported.csv');
        }
    }

    public function saveFromCsv($data)
    {
        $loc = Mage::getModel('ak_locator/location');

        if(count($loc->getCollection()->addAttributeToSelect('*')->addAttributeToFilter('title',$data['store_title']))){
            $this->log('updating existing store '.trim($data['store_title']));
            $loc = $loc->getCollection()->addAttributeToSelect('*')->addAttributeToFilter('title', $data['store_title'])->getFirstItem();
        }else{
            $this->log('importing new store '.trim($data['store_title']));
        }
        if(!isset($data['meta_title'])){
            $data['meta_title'] = '';
        }
        if(!isset($data['meta_description'])){
            $data['meta_description'] = '';
        }
        if(!isset($data['meta_keywords'])){
            $data['meta_keywords'] = '';
        }
        if(!isset($data['enabled'])){
            $data['enabled'] = '';
        }
        if(!isset($data['stockist'])){
            $data['stockist'] = '';
        }
        if(!isset($data['country']) || empty($data['country']) || $data['country']==''){
            $data['country'] = "Australia";
        }
        if(!isset($data['store_id']) || empty($data['store_id']) || $data['store_id']==''){
            $data['store_id'] = 0;
        }
        //preprocess data to manipulate values where required
        if (!isset($data['store_url'])) {
            $data['store_url'] = str_replace("'", '', strtolower(str_replace(' ', '-', $data['store_title'])));
        }
        foreach($this->mapper as $att => $col){
            if($att == "website_based_stores"){
                $loc->setData($att, $data[$col]);
            }
            else {
                switch ($this->getAttributeModel($att)->getFrontendInput()) {
                    case 'select':
                        $loc->setData($att, $this->getSelectValue($att, $data[$col]));
                        break;
                    case 'multiselect':
                        $loc->setData($att, $this->getMultiSelectValue($att, $data[$col]));
                        break;
                    default:
                        $loc->setData($att, $data[$col]);
                        break;
                }
            }
        }

        $data = $this->preprocess($data);

        $loc->setData('is_enabled', 1);
        $loc->setData('is_stockist', 0);


        $loc->setUrlKey(str_replace("'", '', strtolower(str_replace(' ', '-', $data['store_title']))));

        /*if(!$loc->getGeocoded() && $geodata = $this->geocodeData($data)){
            $this->log('geocoding address');

            $addressComponents = array(
                'sub_premise',
                'premise',
                'thoroughfare',
                'postalcode',
                'locality',
                'dependent_locality',
                'administrative_area',
                'sub_administrative_area',
                'country',
                'latitude',
                'longitude'
            );

            //clear out existing address data
            foreach($addressComponents as $component){
                $loc->setData($component,'');
            }

            $loc->setLatitude($geodata->geometry->location->lat);
            $loc->setLongitude($geodata->geometry->location->lng);

            foreach($geodata->address_components as $component){

                switch ($component->types[0]) {
                    case 'country':
                        $loc->setCountry($component->long_name);
                        break;
                    case 'administrative_area_level_1':
                        $loc->setAdministrativeArea($component->long_name);
                        break;
                    case 'locality':
                        $loc->setLocality($component->long_name);
                        break;
                    case 'postal_code':

                        //only set postcode if it wasn't already defined
                        if($loc->getPostalCode() == ''){
                            //echo 'setting postcode to '.$component->long_name;
                            $loc->setPostalCode($component->long_name);
                        }
                        break;
                    case 'route':
                        $loc->setThoroughfare($component->long_name);
                        break;
                    case 'street_number':
                        $loc->setPremise($component->long_name);
                        break;
                    case 'subpremise':
                        $loc->setSubPremise($component->long_name);
                        break;
                }

            }
            $loc->setGeocoded(1);
        }else{
            $loc->setGeocoded(0);
        }
*/
        $loc->save();
        $this->log(trim($data['store_title']).' saved');
    }

    private function setHeaders($data)
    {
        foreach($data as $col){
            $this->headers[] = str_replace(' ', '_', strtolower($col));
        }

    }

    protected function getSelectValue($attribute_code, $label)
    {
        foreach($this->getAttributeOptions($attribute_code) as $option){
            if($option['label'] == $label){
                return $option['value'];
            }
        }
    }

    protected function getMultiSelectValue($attribute_code, $label)
    {
        $values = array();

        if(strstr($label, ' , ')){
            $labels = explode(' , ', $label);
        }else if(strstr($label, ' or ')){
            //specific to trackside data as sometimes is has " or " in place of commas
            $labels = explode(' or ', $label);
        }else{
            $labels[] = trim($label);
        }

        foreach($labels as $label){
            foreach($this->getAttributeOptions($attribute_code) as $option){
                if($option['label'] == trim($label)){
                    $values[] = $option['value'];
                }
            }
        }

        return implode(',', $values);
    }


    protected function getAttributeModel($attribute_code)
    {
        if(!isset($this->_attributeModels[$attribute_code])){
            $attribute_model = Mage::getModel('eav/entity_attribute');
            $id = $attribute_model->getIdByCode(Ak_Locator_Model_Location::ENTITY, $attribute_code);
            $this->_attributeModels[$attribute_code] = $attribute_model->load($id);
        }

        return $this->_attributeModels[$attribute_code];
    }

    protected function getAttributeOptions($attribute_code)
    {

        if(!$this->_attributeOptions[$attribute_code]){
            $this->_attributeOptions[$attribute_code] = $this->getAttributeModel($attribute_code)->getSource()->getAllOptions(false);
        }
        return $this->_attributeOptions[$attribute_code];
    }

    /**
     *  Get address in display format from csv data
     */
    public function getAddress($data)
    {
        $parts = array();

        if($data['address']){
            $parts[] = $data['address'];
        }
        if($data['address_2']){
            $parts[] = $data['address_2'];
        }
        if($data['suburb']){
            $parts[] = $data['suburb'];
        }

        $parts[] = 'australia';

        return implode(', ', $parts);
    }

    /**
     * parse csv row to array with column header as key
     */
    private function parseCsv($data)
    {
        $storeData = array();

        $col = 0;
        foreach($data as $value){
            $storeData[$this->headers[$col]] = trim($value);
            $col++;
        }

        return $storeData;
    }

    /**
     * attempt to generate a lat long value from address data givin
     */
    private function geocodeData($data)
    {
        //return false;
        include_once(Mage::getBaseDir('lib').'/geoPHP/geoPHP.inc');
        $key = Mage::getStoreConfig('locator_settings/google_maps/api_key');
        $geocoder = new GoogleGeocode($key);
        $query = $this->getAddress($data);

        try{
            $result = $geocoder->read($query,'raw');
        }catch (Exception $e){
            $_skipped[] = $data['store_name'];
            $this->log('skipping address as it could not be geocoded. '.$query);
            return false;
        }


        if($result){
            return $result;
        }else{
            return false;
        }
    }


    /**
     * Preprocess the row to manipulate any data
     *
     * @param $data
     * @return mixed
     */
    protected function preprocess($data)
    {
        if ($data['enabled'] == '') {
            $data['is_enabled'] = '1';
        }

        if ($data['stockist'] == '') {
            $data['is_stockist'] = '0';
        }

        foreach($data as $key => $val){
            if($val == 'NULL'){
                $data[$key] = '';
            }
        }

        return $data;
    }


    protected function log($msg)
    {
        Mage::log($msg,Zend_Log::DEBUG, 'store_import.log');
    }

}