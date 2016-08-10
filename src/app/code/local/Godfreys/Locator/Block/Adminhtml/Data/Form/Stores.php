<?php
class Godfreys_Locator_Block_Adminhtml_Data_Form_Stores
    extends Varien_Data_Form_Element_Multiselect
{
    /**
     * Set the default value to USE_NONE. This is needed if the extension is is installed
     * after products already where created.
     *
     * @return int
     */
    public function getValue()
    {
        $value = $this->getData('value');
        if (!is_null($value) && !is_array($value)) {
            $value = explode(',', (string)$value);
        }
        return $value;
    }
}
