<?php

class AHT_Backupcms_Block_Adminhtml_Backupcms_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
  public function __construct()
  {
      parent::__construct();
      $this->setId('backupcmsGrid');
      //$this->setDefaultSort('page_id');
      $this->setDefaultDir('ASC');
      $this->setSaveParametersInSession(true);
  }

  protected function _prepareCollection()
  {
		if(Mage::app()->getRequest()->getActionName()!='static'){
			$collection = Mage::getModel('cms/page')->getCollection();
			$collection->setFirstStoreFlag(true);
		}
		else{
			$collection = Mage::getModel('cms/block')->getCollection();
			
		}
        $this->setCollection($collection);
        return parent::_prepareCollection();
  }

  protected function _prepareColumns()
  {
		if(Mage::app()->getRequest()->getActionName()!='static'){
			$this->addColumn('page_id', array(
				  'header'    => Mage::helper('backupcms')->__('ID'),
				  'align'     =>'right',
				  'width'     => '50px',
				  'index'     => 'page_id',
			  ));
		}
		else{
			$this->addColumn('block_id', array(
				  'header'    => Mage::helper('backupcms')->__('ID'),
				  'align'     =>'right',
				  'width'     => '50px',
				  'index'     => 'block_id',
			  ));
		}
		
	  
      $this->addColumn('title', array(
            'header'    => Mage::helper('cms')->__('Title'),
            'align'     => 'left',
            'index'     => 'title',
        ));

        $this->addColumn('identifier', array(
            'header'    => Mage::helper('cms')->__('Key'),
            'align'     => 'left',
            'index'     => 'identifier'
        ));


		if(Mage::app()->getRequest()->getActionName()!='static'){
			$this->addColumn('root_template', array(
				'header'    => Mage::helper('cms')->__('Layout'),
				'index'     => 'root_template',
				'type'      => 'options',
				'options'   => Mage::getSingleton('page/source_layout')->getOptions(),
			));
		}
		
		if (!Mage::app()->isSingleStoreMode()) {
            $this->addColumn('store_id', array(
                'header'        => Mage::helper('cms')->__('Store View'),
                'index'         => 'store_id',
                'type'          => 'store',
                'store_all'     => true,
                'store_view'    => true,
                'sortable'      => false,
                'filter_condition_callback'
                                => array($this, '_filterStoreCondition'),
            ));
        }
		
        $this->addColumn('creation_time', array(
            'header'    => Mage::helper('cms')->__('Date Created'),
            'index'     => 'creation_time',
            'type'      => 'datetime',
        ));

        $this->addColumn('update_time', array(
            'header'    => Mage::helper('cms')->__('Last Modified'),
            'index'     => 'update_time',
            'type'      => 'datetime',
        ));
	  
      return parent::_prepareColumns();
  }

    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('backupcms_id');
        $this->getMassactionBlock()->setFormFieldName('backupcms');

		$url = Mage::helper('adminhtml')->getUrl('adminhtml/backupcms_backup/index');
		if(Mage::app()->getRequest()->getActionName()=='static'){
			$url = Mage::helper('adminhtml')->getUrl('adminhtml/backupcms_backup/static');
		}
        $this->getMassactionBlock()->addItem('backup', array(
             'label'    => Mage::helper('backupcms')->__('Backup'),
             'url'      => $url,
        ));
        return $this;
    }
	
	protected function _filterStoreCondition($collection, $column){
		if (!$value = $column->getFilter()->getValue()) {
			return;
		}
		$this->getCollection()->addStoreFilter($value);
	}

  public function getRowUrl($row)
  {
      //return $this->getUrl('*/*/edit', array('id' => $row->getId()));
  }

}