<?php

class Balance_Sitemap_Block_Adminhtml_Robots_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('balance_sitemapRobotsGrid');
        $this->setDefaultSort('robots_id');
        $this->setDefaultDir('ASC');
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getModel('balance_sitemap/robots')->getCollection();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        
        $baseUrl = $this->getUrl();

        $this->addColumn('title', array(
            'header'    => Mage::helper('balance_sitemap')->__('Title'),
            'align'     => 'left',
            'index'     => 'title',
        ));

        if (!Mage::app()->isSingleStoreMode()) {
            $this->addColumn('store_id', array(
                'header'        => Mage::helper('balance_sitemap')->__('Store View'),
                'index'         => 'store_id',
                'type'          => 'store',
                'store_all'     => true,
                'store_view'    => true,
                'sortable'      => false,
                'renderer'  => 'balance_sitemap/adminhtml_robots_grid_renderer_store', 
                
            ));
        }
        
        $this->addColumn('is_active', array(
            'header'    => Mage::helper('balance_sitemap')->__('Status'),
            'index'     => 'is_active',
            'type'      => 'options',
            'options'   => array(
                0 => Mage::helper('balance_sitemap')->__('Disabled'),
                1 => Mage::helper('balance_sitemap')->__('Enabled')
            ),
        ));

        $this->addColumn('creation_time', array(
            'header'    => Mage::helper('balance_sitemap')->__('Date Created'),
            'index'     => 'creation_time',
            'type'      => 'datetime',
        ));

        $this->addColumn('update_time', array(
            'header'    => Mage::helper('balance_sitemap')->__('Last Modified'),
            'index'     => 'update_time',
            'type'      => 'datetime',
        ));
        
        return parent::_prepareColumns();               
    }

    
    /**
     * Row click url
     *
     * @return string
     */
    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('robots_id' => $row->getId()));
    }

}
