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
 * @category    Enterprise
 * @package     Enterprise_Cms
 * @copyright   Copyright (c) 2014 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/enterprise-edition
 */


/**
 * Cms Hierarchy Context Menu
 *
 * @category   Enterprise
 * @package    Enterprise_Cms
 */
class Godfreys_Cms_Block_Hierarchy_Menu extends Enterprise_Cms_Block_Hierarchy_Menu
{

    /**
     * Recursive draw menu
     *
     * @param array $tree
     * @param int $parentNodeId
     * @return string
     */
    public function drawMenu(array $tree, $parentNodeId = 0)
    {

        if (!isset($tree[$parentNodeId])) {
            return '';
        }

        $addStyles = ($parentNodeId == 0);
        $html = $this->_getListTagBegin($addStyles);

        $count = 0;
        foreach ($tree[$parentNodeId] as $nodeId => $node) {
            /* @var $node Enterprise_Cms_Model_Hierarchy_Node */

            //get identifier of group
            if($count == 0) {
                $data = Mage::getModel('enterprise_cms/hierarchy_node')->getCollection()
                        ->addFieldToFilter('node_id', array('eq' => $nodeId));
                $dataFirstGroup = $data->getFirstItem();
                $id_group = $dataFirstGroup->identifier;
            }

            $nested = $this->drawMenu($tree, $nodeId);

            /*begin config add link*/
            if(Mage::getSingleton('core/design_package')->getPackageName() == 'ultimo' && isset($id_group) && $id_group == 'support') {
                $nested = str_replace ('Asthma and Allergies Sufferers', 'Asthma & Allergy Sufferers', $nested);
                $nested = str_replace ('Pet Owners Cleaning Information', 'Pet Owners', $nested);
                $nested = str_replace ('support/locator', 'locator', $nested);
                $nested = str_replace ('support/warranty/registration', 'warranty/registration', $nested);
            }
            /*end config add link*/
            $hasChilds = ($nested != '');
            $html .= $this->_getItemTagBegin($node, $hasChilds) . $this->_getNodeLabel($node);
            $html .= $nested;
            $html .= $this->_getItemTagEnd();

            $this->_totalMenuNodes++;
            $count++;
        }

        $html .= $this->_getListTagEnd();
        return $html;
    }

}
