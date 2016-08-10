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

class Godfreys_Locator_Block_Adminhtml_Location_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('location_grid');
        $this->setDefaultSort('id');
        $this->setDefaultDir('desc');
        $this->setSaveParametersInSession(true);
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getModel('ak_locator/location')->getCollection()
            ->addAttributeToSelect('title')
            ->addAttributeToSelect('geocoded')
            ->addAttributeToSelect('address')
            ->addAttributeToSelect('postal_code')
            ->addAttributeToSelect('is_enabled')
            ->addAttributeToSelect('website_based_stores')
            ->addAttributeToSelect('country');

        $this->setCollection($collection);
        return  parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('id', array(
            'header'    => Mage::helper('ak_locator')->__('ID'),
            'align'     =>'right',
            'width'     => '50px',
            'index'     => 'entity_id',
        ));

        $this->addColumn('title', array(
            'header'    => Mage::helper('ak_locator')->__('Title'),
            'align'     =>'left',
            'index'     => 'title',
        ));

        $this->addColumn('address', array(
            'header'    => Mage::helper('ak_locator')->__('Address'),
            'align'     =>'left',
            'index'     => 'address',
        ));

        $this->addColumn('postal_code', array(
            'header'    => Mage::helper('ak_locator')->__('Postalcode'),
            'align'     => 'left',
            'index'     => 'postal_code',
        ));

        $this->addColumn('country', array(
            'header'    => Mage::helper('ak_locator')->__('Country'),
            'align'     => 'left',
            'index'     => 'country',
        ));
        if (!Mage::app()->isSingleStoreMode()) {
            $this->addColumn('website_based_stores', array(
                'header'        => Mage::helper('cms')->__('Store View'),
                'index'         => 'website_based_stores',
                'type'          => 'store',
                'store_all'     => true,
                'store_view'    => true,
                'sortable'      => false,
                'renderer' => 'godfreys_locator/adminhtml_location_renderer_stores',
                'filter_condition_callback' => array($this, '_filterStoreCondition'),
            ));
        }

        $this->addColumn('is_enabled', array(
            'header'    => Mage::helper('ak_locator')->__('Enabled'),
            'align'     => 'left',
            'index'     => 'is_enabled',
            'type'  => 'options',
            'options' => array(1=>'Yes',0=>'No'),
        ));

        return parent::_prepareColumns();
    }

    protected function _filterStoreCondition($collection, $column)
    {
        if (!$value = $column->getFilter()->getValue()) {
            return;
        }
        $this->getCollection()->addStoreFilter($value);
    }

    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('id' => $row->getId()));
    }
}
