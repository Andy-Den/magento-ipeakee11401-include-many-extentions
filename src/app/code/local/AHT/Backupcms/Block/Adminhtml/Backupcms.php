<?php
class AHT_Backupcms_Block_Adminhtml_Backupcms extends Mage_Adminhtml_Block_Widget_Grid_Container
{
	public function __construct()
	{
		$this->_controller = 'adminhtml_backupcms';
		$this->_blockGroup = 'backupcms';
		if(Mage::app()->getRequest()->getActionName()!='static'){
			$this->_headerText = Mage::helper('backupcms')->__('Backup Pages');
		}
		else{
			$this->_headerText = Mage::helper('backupcms')->__('Backup Static Blocks');
		}
		parent::__construct();
		$this->_removeButton('add');
	}
	
	public function getStores()
	{
		$stores = Mage::getSingleton('adminhtml/system_store')->getStoreValuesForForm(false, true);
		$html = '';
		$html .= '<select style="width:270px" id="block_store_id" class=" required-entry select multiselect" multiple="multiple" size="10" title="Store View" name="stores[]">';
		foreach($stores as $_stores){
			if(!is_array($_stores['value']))
			{
				$html.='<option value="'.$_stores['value'].'">'.$_stores['label'].'</option>';
			}
			else{
				$html.='<optgroup label="'.$_stores['label'].'">';
					if(count($_stores['value'])>0){
						foreach($_stores['value'] as $_store){
							$html.='<option value="'.$_store['value'].'">'.$_store['label'].'</option>';
						}
					}
				$html.='</optgroup>';
			}
		}
		$html .= '</select>';
		return $html;	
	}
}