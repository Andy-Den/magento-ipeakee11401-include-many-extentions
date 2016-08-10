<?php

class Balanceinternet_Shoppingdotcomfeed_Block_Adminhtml_Shoppingdotcomfeed_Grid extends Mage_Adminhtml_Block_Widget_Grid {

    public function __construct() {
        parent::__construct();
        $this->setId('shoppingdotcomfeedGrid');
        $this->setDefaultSort('id');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
    }

    protected function _prepareCollection() {
        $collection = Mage::getModel('shoppingdotcomfeed/shoppingdotcomfeed')->getCollection();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns() {

        $this->addColumn('id_feedportal', array(
            'header' => Mage::helper('shoppingdotcomfeed')->__('Country'),
            'align' => 'left',
            'width' => '100px',
            'index' => 'id_feedportal',
            'renderer' => 'shoppingdotcomfeed/adminhtml_shoppingdotcomfeed_renderer_country'
        ));

        $this->addColumn('id_store', array(
            'header' => Mage::helper('shoppingdotcomfeed')->__('Site'),
            'align' => 'left',
            'width' => '200px',
            'index' => 'id_store',
            'renderer' => 'shoppingdotcomfeed/adminhtml_shoppingdotcomfeed_renderer_store'
        ));

        $this->addColumn('status', array(
            'header' => Mage::helper('shoppingdotcomfeed')->__('Status'),
            'align' => 'left',
            'width' => '100x',
            'index' => 'status',
            'renderer' => 'shoppingdotcomfeed/adminhtml_shoppingdotcomfeed_renderer_status'
        ));

        $this->addColumn('filename', array(
            'header' => Mage::helper('shoppingdotcomfeed')->__('Filename'),
            'align' => 'right',
            'width' => '50px',
            'index' => 'filename',
        ));

        $this->addColumn('id_frequency', array(
            'header' => Mage::helper('shoppingdotcomfeed')->__('Frequency'),
            'align' => 'right',
            'width' => '100px',
            'index' => 'id_frequency',
            'renderer' => 'shoppingdotcomfeed/adminhtml_shoppingdotcomfeed_renderer_frequency'
        ));

        $this->addColumn('successful_export', array(
            'header' => Mage::helper('shoppingdotcomfeed')->__('Last Successful Export'),
            'align' => 'right',
            'width' => '50px',
            'index' => 'successful_export',
        ));

        $this->addColumn('successful_upload', array(
            'header' => Mage::helper('shoppingdotcomfeed')->__('Last Successful Upload'),
            'align' => 'right',
            'width' => '50px',
            'index' => 'successful_upload',
        ));

        $this->addColumn('error', array(
            'header' => Mage::helper('shoppingdotcomfeed')->__('Error'),
            'align' => 'right',
            'width' => '100px',
            'index' => 'error',
        ));

        $this->addColumn('product_list', array(
            'header' => Mage::helper('shoppingdotcomfeed')->__('Product List'),
            'width' => '100',
            'type' => 'action',
            'getter' => 'getId',
            'actions' => array(
                array(
                    'caption' => Mage::helper('shoppingdotcomfeed')->__('Manage Products'),
                    //'url' => array('base' => '*/*/manage'),
                    'url' => array(
                        'base' => "*/adminhtml_catalog/index",
                        array(
                            'id_feed' => $this->getId(),
                            'store' => Mage::getModel('shoppingdotcomfeed/feed')->getResource()->getStoreIdForFeed($this->getId())
                        )
                    ),
                    'field' => 'id_feed'
                )
            ),
            'filter' => false,
            'sortable' => false,
            'index' => 'stores',
            'is_system' => true,
        ));

        $this->addColumn('action_edit', array(
            'header' => Mage::helper('shoppingdotcomfeed')->__(''),
            'width' => '100',
            'type' => 'action',
            'getter' => 'getId',
            'actions' => array(
                array(
                    'caption' => Mage::helper('shoppingdotcomfeed')->__('Edit'),
                    'url' => array('base' => '*/*/edit'),
                    'field' => 'id'
                )
            ),
            'filter' => false,
            'sortable' => false,
            'index' => 'stores',
            'is_system' => true,
        ));
        return parent::_prepareColumns();
    }

    public function getRowUrl($row) {
        return $this->getUrl('*/*/edit', array('id' => $row->getId()));
    }

}