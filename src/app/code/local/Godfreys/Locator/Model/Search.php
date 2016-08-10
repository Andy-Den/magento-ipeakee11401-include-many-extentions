<?php
/**
 * Location extension for Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @copyright 2013 Andrew Kett. (http://www.andrewkett.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link      http://andrewkett.github.io/Ak_Locator/
 */

/**
 * @category   Ak
 * @package    Ak_Locator
 * @author     Andrew Kett
 */
class Godfreys_Locator_Model_Search extends Ak_Locator_Model_Search
{

    public function setCollection(Mage_Eav_Model_Entity_Collection_Abstract $collection)
    {
        $tbl_item = Mage::getSingleton('core/resource')->getTableName('locator_location_entity_text');
        $storeId=Mage::app()->getStore()->getStoreId();
        $attribute_id=Mage::getSingleton("eav/config")->getAttribute('ak_locator_location',"website_based_stores")->getAttributeId();
        $collection->getSelect() ->join(array('t2' => $tbl_item),'e.entity_id = t2.entity_id','t2.value')
            ->where("t2.value like '".$storeId.",%' or t2.value = '%,".$storeId.",%'or t2.value like '%,".$storeId."' or t2.value = '".$storeId."' or t2.value = '0' or t2.value = null")
            ->where("t2.attribute_id = ".$attribute_id);

        $this->_collection = $collection;

        return $this;
    }

}
