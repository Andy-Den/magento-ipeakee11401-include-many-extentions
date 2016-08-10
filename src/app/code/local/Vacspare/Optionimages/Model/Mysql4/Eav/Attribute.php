<?php
/**
 * Vacspare_Optionimages Extension
 *
 * @category    Local
 * @package     Vacspare_Optionimages
 * @author      dungnv (dungnv@arrowhitech.com)
 * @copyright   Copyright(c) 2011 Arrowhitech Inc. (http://www.arrowhitech.com)
 *
 */

/**
 *
 * @category   Local
 * @package    Vacspare_Optionimages
 * @author     dungnv <dungnv@arrowhitech.com>
 */
class Vacspare_Optionimages_Model_Mysql4_Eav_Attribute extends MagExt_MgxTips_Model_Rewrite_CatalogResourceEavMysql4Attribute
{
	
	/**
     * Save store labels
     *
     * @param Mage_Core_Model_Abstract $object
     * @return Mage_Eav_Model_Mysql4_Entity_Attribute
     */
    protected function _saveStoreLabels(Mage_Core_Model_Abstract $object)
    {
		
        $storeLabels = $object->getStoreLabels();
		$attributeCode = $object->getAttributeCode();
		
        if (is_array($storeLabels)) {
            if ($object->getId()) {
                $condition = $this->_getWriteAdapter()->quoteInto('attribute_id = ?', $object->getId());
                $this->_getWriteAdapter()->delete($this->getTable('eav/attribute_label'), $condition);
            }
            foreach ($storeLabels as $storeId => $label) {
				//Mage::getResourceModel('vacspareproduct/vacspareproduct')->updateLabelForCustomOptionAttribute($attributeCode, $label, $storeId);
                if ($storeId == 0 || !strlen($label)) {
                    continue;
                }
                $this->_getWriteAdapter()->insert(
                    $this->getTable('eav/attribute_label'),
                    array(
                        'attribute_id' => $object->getId(),
                        'store_id' => $storeId,
                        'value' => $label
                    )
                );
            }
        }
        return $this;
    }

    /**
     * Perform actions before object save
     *
     * @param Mage_Core_Model_Abstract $object
     * @return Vacspare_Optionimages_Model_Mysql4_Eav_Xattribute
     */
    protected function _saveOption(Mage_Core_Model_Abstract $object)
    {
        $option = $object->getOption();
        if (is_array($option)) {
            $write = $this->_getWriteAdapter();
            $optionTable        = $this->getTable('attribute_option');
            $optionValueTable   = $this->getTable('attribute_option_value');
            $vacspareOptionValueTable = $this->getTable('optionimages/vacspare_attribute_option_value');
            $stores = Mage::getModel('core/store')
                ->getResourceCollection()
                ->setLoadDefault(true)
                ->load();
			
            if (isset($option['value'])) {
                $attributeDefaultValue = array();
                if (!is_array($object->getDefault())) {
                    $object->setDefault(array());
                }

                foreach ($option['value'] as $optionId => $values) {
                    $intOptionId = (int) $optionId;
                    if (!empty($option['delete'][$optionId])) {
                        if ($intOptionId) {
                            $condition = $write->quoteInto('option_id=?', $intOptionId);
                            $write->delete($optionTable, $condition);
                        }

                        continue;
                    }

                    if (!$intOptionId) {
                        $data = array(
                           'attribute_id'  => $object->getId(),
                           'sort_order'    => isset($option['order'][$optionId]) ? $option['order'][$optionId] : 0,
                        );
                        $write->insert($optionTable, $data);
                        $intOptionId = $write->lastInsertId();
                    }
                    else {
                        $data = array(
                           'sort_order'    => isset($option['order'][$optionId]) ? $option['order'][$optionId] : 0,
                        );
                        $write->update($optionTable, $data, $write->quoteInto('option_id=?', $intOptionId));
                    }

                    if (in_array($optionId, $object->getDefault())) {
                        if ($object->getFrontendInput() == 'multiselect') {
                            $attributeDefaultValue[] = $intOptionId;
                        } else if ($object->getFrontendInput() == 'select') {
                            $attributeDefaultValue = array($intOptionId);
                        }
                    }


                    // Default value
                    if (!isset($values[0])) {
                        Mage::throwException(Mage::helper('eav')->__('Default option value is not defined.'));
                    }
                    if (isset($_FILES['filename_'.$optionId]['name']) && $_FILES['filename_'.$optionId]['name'] != '') {
                        try {
                            $uploader = new Varien_File_Uploader('filename_'.$optionId);
                            $uploader->setAllowedExtensions(array('jpg','jpeg','gif','png'));
                            $uploader->setAllowRenameFiles(false);
                            $uploader->setFilesDispersion(false);
                            //$path = Mage::getBaseDir('media') . DS;
                            $path = Mage::getBaseDir('media') . DS . 'image_color' . DS;
                            $fileName = $intOptionId.'_'.str_replace(" ", "_", trim($_FILES['filename_'.$optionId]['name']));
                            $uploader->save($path, $fileName);

                            $write->delete($vacspareOptionValueTable, $write->quoteInto('option_id=?', $intOptionId));
                            //$fileName = $_FILES['filename']['name'][$optionId];
							
                            $data2 = array(
                                    'option_id' => $intOptionId,
                                    'filename'  => $fileName,
                                );
								
							
                            $write->insert($vacspareOptionValueTable, $data2);

                        } catch (Exception $e) {

                        }
                    }
                        
                        
                    $write->delete($optionValueTable, $write->quoteInto('option_id=?', $intOptionId));
                    foreach ($stores as $store) {
                        if (isset($values[$store->getId()]) && (!empty($values[$store->getId()]) || $values[$store->getId()] == "0")) {
                            $data = array(
                                'option_id' => $intOptionId,
                                'store_id'  => $store->getId(),
                                'value'     => $values[$store->getId()],
                            );
                            $write->insert($optionValueTable, $data);
                        }
                    }
                }
                $write->update($this->getMainTable(), array(
                    'default_value' => implode(',', $attributeDefaultValue)
                ), $write->quoteInto($this->getIdFieldName() . '=?', $object->getId()));
            }
        }
        return $this;
    }
}
