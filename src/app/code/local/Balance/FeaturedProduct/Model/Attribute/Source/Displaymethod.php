<?php
class Balance_FeaturedProduct_Model_Attribute_Source_Displaymethod extends  Mage_Eav_Model_Entity_Attribute_Source_Abstract {
    
    const ASCENDING   = 1;
    const DESCENDING   = 2;
    const RANDOM = 3;
       
    /**
     * Retrieve class array
     *
     * @return array
     */
    public function getClassArray()
    {
       return   array (   
                self::ASCENDING  => 'Ascending',
                self::DESCENDING => 'Descending', 
                self::RANDOM     => 'Random'                        
               );                         
    }
    /**
     * Retrieve option array
     *
     * @return array
     */
    public function getOptionArray()
    {
        return array(
            self::ASCENDING   =>  Mage::helper('featuredproduct')->__('Ascending'),
            self::DESCENDING  =>  Mage::helper('featuredproduct')->__('Descending'),
            self::RANDOM      =>  Mage::helper('featuredproduct')->__('Random'),            
        );
    }

    /**
     * Retrieve option array with empty value
     *
     * @return array
     */
    public function getAllOption()
    {
        $options = self::getOptionArray();
        return $options;
    }

    /**
     * Retrieve option array with empty value
     *
     * @return array
     */
    public function getAllOptions()
    {
       
        foreach (self::getOptionArray() as $index => $value) {
            $res[] = array(
               'value' => $index,
               'label' => $value
            );
        }
        return $res;
    }

    /**
     * Retrieve option text by option value
     *
     * @param string $optionId
     * @return string
     */
    public function getOptionText($optionId)
    {
        $options = self::getOptionArray();
        return isset($options[$optionId]) ? $options[$optionId] : null;
    }
    

    public function getClass($optionId)
    {
        $classes = self::getClassArray();
        return isset($classes[$optionId]) ? $classes[$optionId] : $classes[self::ASCENDING];
    }

}