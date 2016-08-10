<?php
/**
 * Webtex
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.webtexsoftware.com/LICENSE.txt
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to info@webtexsoftware.com and we will send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade the extension to newer
 * versions in the future. If you wish to customize the extension for your
 * needs please refer to http://www.webtexsoftware.com for more information, 
 * or contact us through this email: info@webtexsoftware.com.
 *
 * @category   MagExt
 * @package    MagExt_Tips
 * @copyright  Copyright (c) 2011 Webtex Solutions, LLC (http://www.webtexsoftware.com/)
 * @license    http://www.webtexsoftware.com/LICENSE.txt End-User License Agreement
 */
 
class MagExt_MgxTips_Model_Rewrite_CatalogResourceEavMysql4Attribute extends Mage_Catalog_Model_Resource_Eav_Mysql4_Attribute
{
    protected function _construct()
    {
        parent::_construct();
    }
 
    public function _beforeSave(Mage_Core_Model_Abstract $object)
    { 
            $aDesc = $object->getData('mgxtips');    
            

            if(is_array($aDesc))
            {
                if(trim($aDesc[0]) == '<br>') $aDesc[0] = '';
                
                $object->setData('mgxtips', $aDesc[0]);
                                                
                $model = Mage::getModel('mgxtips/mgxtips');
                
                unset($aDesc[0]);
                                                
                foreach($aDesc as $key => $val)
                {
                    if(trim($val) == '<br>') $val = '';
                
                        $labels = Mage::getModel('mgxtips/mgxtips')->getCollection()
                        ->addFieldToFilter('attribute_id', $object->getAttributeId())
                        ->addFieldToFilter('store_id', $key)
                        ->load()
                        ->toArray();
                        
                        $data['value'] = $val;
                        $data['store_id'] = $key;
                        $data['attribute_id'] = $object->getAttributeId();
                        
                        if($labels['totalRecords'])
                        {
                            $model->setData($data)->setId($labels['items'][0]['mgxtips_label_id']);
                            
                            try 
                            {
   	                            if($val) $model->save(); else $model->delete();
                                   
           	                }
                            catch(Exception $e)
                            {
                                Mage::getSingleton('adminhtml/session')->addError(Mage::helper('mgxtips')->__('Descriptions was not saved'));
                            }
                        }
                        else
                        {  
                            if($val)
                            {
                                $model->setData($data);
                                
                                try 
                                {
           	                        $model->save();
           	                    }
                                catch(Exception $e)
                                {
                                    Mage::getSingleton('adminhtml/session')->addError(Mage::helper('mgxtips')->__('Descriptions was not saved'));
                                }
                            }
                        }
                }
            } 

        return parent::_beforeSave($object);
    }

}