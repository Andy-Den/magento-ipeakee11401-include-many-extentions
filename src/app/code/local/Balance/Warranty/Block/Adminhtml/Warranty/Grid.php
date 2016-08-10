<?php

/**
 * Adminhtml warranty grid block
 *
 * @category   Balance
 * @package    Balance_Warranty
 * @author     Carey Sizer <carey@balanceinternet.com.au>
 */
class Balance_Warranty_Block_Adminhtml_Warranty_Grid extends Mage_Adminhtml_Block_Widget_Grid
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('warrantyGrid');
        $this->setUseAjax(true);
        $this->setDefaultSort('id');
        $this->setSaveParametersInSession(true);
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getResourceModel('warranty/warranty_collection');
        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('id', array(
            'header'    => Mage::helper('warranty')->__('ID'),
            'width'     => '50',
            'index'     => 'id',
            'type'  => 'number',
        ));
        
        $this->addColumn('customer_id', array(
            'header'    => Mage::helper('warranty')->__('Customer ID'),
            'type'      => 'number',
            'align'     => 'left',
            'width'     =>  '50',
            'index'     => 'customer_id'
        ));
        
        $this->addColumn('date_of_purchase', array(
            'header'    => Mage::helper('warranty')->__('Date of Purchase'),
            'type'      => 'datetime',
            'align'     => 'center',
            'index' => 'date_of_purchase',
            'type' => 'datetime',
        ));
       
        $this->addColumn('make', array(
            'header'    => Mage::helper('warranty')->__('Make'),
            'index'     => 'make'
        ));
        
        $this->addColumn('model', array(
            'header'    => Mage::helper('warranty')->__('Model'),
            'index'     => 'model'
        ));
        
        $this->addColumn('term', array(
            'header'    => Mage::helper('warranty')->__('Term (Years)'),
            'index'     => 'term'
        ));
        
        $this->addColumn('serial', array(
            'header'    => Mage::helper('warranty')->__('Serial'),
            'index'     => 'serial'
        ));
        
        $this->addColumn('price', array(
            'header'    => Mage::helper('warranty')->__('Price'),
            'index'     => 'price',
            'type'      => 'number'
        ));
        
        $this->addColumn('store_of_purchase', array(
            'header'    => Mage::helper('warranty')->__('Store of Purchase'),
            'index'     => 'store_of_purchase'
        ));
        
        $this->addColumn('purchase_reason_price', array(
            'header'    => Mage::helper('warranty')->__('Purchase for Price?'),
            'index'     => 'purchase_reason_price'
        ));
        
        $this->addColumn('purchase_reason_features', array(
            'header'    => Mage::helper('warranty')->__('Purchase for Features?'),
            'index'     => 'purchase_reason_featuress'
        ));
        
        $this->addColumn('purchase_reason_brand', array(
            'header'    => Mage::helper('warranty')->__('Purchase for Brand?'),
            'index'     => 'purchase_reason_brand'
        ));
                
        $this->addColumn('purchase_reason_other', array(
            'header'    => Mage::helper('warranty')->__('Purchase for other reason?'),
            'width'     =>  '275',
            'index'     => 'purchase_reason_other'
        ));

        $this->addExportType('*/*/exportCsv', Mage::helper('warranty')->__('CSV'));
        $this->addExportType('*/*/exportXml', Mage::helper('warranty')->__('Excel XML'));
        return parent::_prepareColumns();
    }

    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('id');
        $this->getMassactionBlock()->setFormFieldName('warrantyGrid');

        return $this;
    }

    public function getGridUrl()
    {
        return $this->getUrl('*/*/grid', array('_current'=> true));
    }

    public function getRowUrl($row)
    {
        return null;
    }
    
   
}