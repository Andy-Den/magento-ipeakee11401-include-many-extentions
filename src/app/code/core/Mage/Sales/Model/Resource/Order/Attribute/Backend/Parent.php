<?php
/**
 * Magento Enterprise Edition
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magento Enterprise Edition License
 * that is bundled with this package in the file LICENSE_EE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.magentocommerce.com/license/enterprise-edition
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Sales
 * @copyright   Copyright (c) 2014 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/enterprise-edition
 */


/**
 * Invoice backend model for parent attribute
 *
 * @category    Mage
 * @package     Mage_Sales
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Sales_Model_Resource_Order_Attribute_Backend_Parent extends Mage_Eav_Model_Entity_Attribute_Backend_Abstract
{
    /**
     * Perform operation after save
     *
     * @param Varien_Object $object
     * @return Mage_Sales_Model_Resource_Order_Attribute_Backend_Parent
     */
    public function afterSave($object)
    {
        parent::afterSave($object);

        foreach ($object->getAddressesCollection() as $item) {
            $item->save();
        }
        foreach ($object->getItemsCollection() as $item) {
            $item->save();
        }
        foreach ($object->getPaymentsCollection() as $item) {
            $item->save();
        }
        foreach ($object->getStatusHistoryCollection() as $item) {
            $item->save();
        }
        foreach ($object->getRelatedObjects() as $object) {
            $object->save();
        }
        return $this;
    }
}